<?php

namespace Modules\Setting\Entities;

use Modules\Support\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Modules\Setting\Events\SettingSaved;
use Modules\Support\Eloquent\Translatable;
use Illuminate\Database\Eloquent\Collection;

class Setting extends Model
{
    use Translatable;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['translations'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['key', 'is_translatable', 'plain_value'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_translatable' => 'boolean',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => SettingSaved::class,
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    protected $translatedAttributes = ['value'];


    /**
     * Get all settings with cache support.
     *
     * @return Collection
     */
    public static function allCached()
    {
        return Cache::rememberForever(md5('settings.all:' . locale()), function () {
            return self::all()->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->value];
            });
        });
    }


    /**
     * Determine if the given setting key exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public static function has($key)
    {
        return static::where('key', $key)->exists();
    }


    /**
     * Get setting for the given key.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return string|array
     */
    public static function get($key, $default = null)
    {
        return static::where('key', $key)->first()->value ?? $default;
    }


    /**
     * Set the given settings.
     *
     * @param array $settings
     *
     * @return void
     */
    public static function setMany($settings)
    {
        foreach ($settings as $key => $value) {
            self::set($key, $value);
        }
    }


    /**
     * Set the given setting.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public static function set($key, $value)
    {
        if ($key === 'translatable') {
            return static::setTranslatableSettings($value);
        }

        static::updateOrCreate(['key' => $key], ['plain_value' => $value]);
    }


    /**
     * Set a translatable settings.
     *
     * @param array $settings
     *
     * @return void
     */
    public static function setTranslatableSettings($settings = [])
    {
        foreach ($settings as $key => $values) {
            if (!is_array($values) && !is_iterable($values)) {
                continue;
            }

            $setting = static::firstOrCreate(['key' => $key]);

            if (!$setting->is_translatable) {
                $setting->is_translatable = true;
                $setting->save();
            }

            foreach ($values as $locale => $value) {
                if ($locale === 'is_translatable') {
                    continue;
                }
                $cleanLocale = trim($locale);
                \DB::table('setting_translations')->updateOrInsert(
                    [
                        'setting_id' => $setting->id,
                        'locale'     => $cleanLocale,
                    ],
                    [
                        'value'      => serialize($value),
                    ]
                );
            }
        }
    }


    /**
     * Get the value of the setting.
     *
     * @return mixed
     */
    public function getValueAttribute()
    {
        $inAdminPanel = false;
        if (app()->bound('inAdminPanel')) {
            $inAdminPanel = app('inAdminPanel');
        } elseif (request()->segment(1) === 'admin' || request()->segment(2) === 'admin') {
            $inAdminPanel = true;
        }


        if ($this->is_translatable) {
            if(!$inAdminPanel){
//            if(in_array($this->key, ['store_name'])){
                return $this->translateOrDefault(locale())->value ?? null;
            } else {
                return $this->getTranslationsArray() ?? null;
            }
        }

        return unserialize($this->plain_value);
    }


    /**
     * Set the value of the setting.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function setPlainValueAttribute($value)
    {
        $this->attributes['plain_value'] = serialize($value);
    }
}
