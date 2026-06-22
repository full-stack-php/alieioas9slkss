<?php

namespace Modules\Media\Entities;

use Modules\Media\IconResolver;
use Modules\User\Entities\User;
use Illuminate\Http\JsonResponse;
use Modules\Media\Admin\MediaTable;
use Modules\Support\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be visible in serialization.
     *
     * @var array
     */
    protected $visible = ['id', 'filename', 'path'];


    /**
     * Perform any actions required after the model boots.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($file) {
            Storage::disk($file->disk)->delete($file->getRawOriginal('path'));
        });
    }


    /**
     * Get the user that uploaded the file.
     *
     * @return void
     */
    public function uploader()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Get the file's path.
     *
     * @param string $path
     *
     * @return string|null
     */
    public function getPathAttribute($path)
    {
        if (!is_null($path)) {
            return Storage::disk($this->disk)->url($path);
        }
    }


    /**
     * Get file's real path.
     *
     * @return void
     */
    public function realPath()
    {
        if (!is_null($this->attributes['path'])) {
            return Storage::disk($this->disk)->path($this->attributes['path']);
        }
    }


    /**
     * Determine if the file type is image.
     *
     * @return bool
     */
    public function isImage()
    {
        return strtok($this->mime, '/') === 'image';
    }


    /**
     * Get the file's icon.
     *
     * @return string
     */
    public function icon()
    {
        return IconResolver::resolve($this->mime);
    }


    /**
     * Get table data for the resource
     *
     * @return JsonResponse
     */
    public function table($request)
    {
        $query = $this->newQuery()
            ->when(!is_null($request->type) && $request->type !== 'null', function ($query) use ($request) {
                $query->where('mime', 'LIKE', "{$request->type}/%");
            });

        return new MediaTable($query);
    }

    public function resizeAndCrop($targetWidth = 600, $targetHeight = 393, $type = 'thumb', $quality=90)
    {
        if (is_null($this->path) && $type == 'big_image') {
            $this->path = 'media/header_placeholder.png';
        }




        if (is_null($this->path) && $type == 'thumb') {
            $this->path = 'build/assets/image-placeholder.png';
        }

        $fullPathParts = explode('/', $this->path);
        $fileBaseName = pathinfo(end($fullPathParts), PATHINFO_FILENAME);
        $fileExt = pathinfo($this->path, PATHINFO_EXTENSION);

        $originalImagePath = "media/{$fileBaseName}.{$fileExt}";

        $useWebp = false;
        if (strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false) {
            $useWebp = true;
            $fileExt = 'webp';
        }
        $newImagePath = "cache/{$fileBaseName}_{$targetWidth}_{$targetHeight}.{$fileExt}";


        $imagePath = $this->fetchImageFromUrl("/images/cache/{$fileBaseName}_{$targetWidth}_{$targetHeight}.{$fileExt}");

        if ($imagePath) {
            return $imagePath;
        }

        if (!is_null($this->path) && !Storage::exists($originalImagePath) && $type == 'thumb') {
            $this->path = 'build/assets/image-placeholder.png';
            if($useWebp){
                $newImagePath = "cache/image-placeholder_{$targetWidth}_{$targetHeight}.{$fileExt}";
            } else {
                $newImagePath = "cache/image-placeholder_{$targetWidth}_{$targetHeight}.png";
            }

            $originalImagePath = "build/assets/image-placeholder.png";

        }

        if (!is_null($this->path) && !Storage::exists($originalImagePath) && $type == 'big_image') {
            $this->path = "build/assets/image-placeholder.png";
            if($useWebp){
                $newImagePath = "cache/big_image-placeholder_{$targetWidth}_{$targetHeight}.{$fileExt}";
            } else {
                $newImagePath = "cache/big_image-placeholder_{$targetWidth}_{$targetHeight}.png";
            }

            $originalImagePath = "build/assets/image-placeholder.png";
        }

        if (!Storage::exists($newImagePath)) {
            if (!Storage::exists($originalImagePath)) {
                \Log::error("Original image not found: " . $originalImagePath);
                return null;
            }

            $imageInfo = getimagesize(Storage::path($originalImagePath));
            if (!$imageInfo || !in_array($imageInfo[2], [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF])) {
                \Log::error("Unsupported image type: " . $originalImagePath);
                return null;
            }

            if (!Storage::exists(dirname($newImagePath))) {
                Storage::makeDirectory(dirname($newImagePath));
            }

            if ($imageInfo[0] != $targetWidth || $imageInfo[1] != $targetHeight) {

                $filePath = Storage::path($originalImagePath);
                $mime = $imageInfo['mime'];
                $width = $imageInfo[0];
                $height = $imageInfo[1];

                // Загружаем изображение в ЛОКАЛЬНУЮ переменную
                if ($mime == 'image/gif') {
                    $image = imagecreatefromgif($filePath);
                } elseif ($mime == 'image/png') {
                    $image = imagecreatefrompng($filePath);
                } elseif ($mime == 'image/jpeg') {
                    $image = imagecreatefromjpeg($filePath);
                } else {
                    return null;
                }

                // Передаем переменную в методы вместо использования $this->image
                $croppedImage = $this->smartCropImage($image, $width, $height, $targetWidth, $targetHeight, $mime);
                $this->saveCacheImage($croppedImage, Storage::path($newImagePath), $quality);
            } else {
                Storage::copy($originalImagePath, $newImagePath);
            }
        }

        return Storage::url($newImagePath);
    }

    private function smartCropImage($sourceImage, $sourceWidth, $sourceHeight, $targetWidth, $targetHeight, $mime)
    {
        $originalAspect = $sourceWidth / $sourceHeight;
        $targetAspect = $targetWidth / $targetHeight;

        if ($originalAspect >= $targetAspect) {
            $newHeight = $targetHeight;
            $newWidth = (int)($newHeight * $originalAspect);
        } else {
            $newWidth = $targetWidth;
            $newHeight = (int)($newWidth / $originalAspect);
        }

        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        if ($mime == 'image/png') {
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            $background = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
            imagecolortransparent($resizedImage, $background);
        } else {
            $background = imagecolorallocate($resizedImage, 255, 255, 255);
        }

        imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $background);
        imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);

        $croppedImage = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($mime == 'image/png') {
            imagealphablending($croppedImage, false);
            imagesavealpha($croppedImage, true);
            $background = imagecolorallocatealpha($croppedImage, 255, 255, 255, 127);
            imagecolortransparent($croppedImage, $background);
        } else {
            $background = imagecolorallocate($croppedImage, 255, 255, 255);
        }

        imagefilledrectangle($croppedImage, 0, 0, $targetWidth, $targetHeight, $background);

        $xOffset = ($newWidth - $targetWidth) / 2;
        $yOffset = ($newHeight - $targetHeight) / 2;

        imagecopyresampled($croppedImage, $resizedImage, 0, 0, $xOffset, $yOffset, $targetWidth, $targetHeight, $targetWidth, $targetHeight);

        // Чистим временные ресурсы
        imagedestroy($sourceImage);
        imagedestroy($resizedImage);

        return $croppedImage; // Возвращаем результат
    }

    private function saveCacheImage($imageResource, $path, $quality = 90)
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($extension == 'jpeg' || $extension == 'jpg') {
            imagejpeg($imageResource, $path, $quality);
        } elseif ($extension == 'png') {
            imagepng($imageResource, $path);
        } elseif ($extension == 'gif') {
            imagegif($imageResource, $path);
        } elseif ($extension == 'webp') {
            imagewebp($imageResource, $path, $quality);
        }

        imagedestroy($imageResource);
    }

    /**
     * Fetch image from URL if the given key is avatar or cover.
     *
     * @param string $key
     * @param string $url
     * @return string|null
     */
    private function fetchImageFromUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }
        return null;
    }
}
