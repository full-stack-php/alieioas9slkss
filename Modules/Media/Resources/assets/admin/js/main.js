import ImagePicker from './ImagePicker';
import FilePicker from './FilePicker';
import MediaPicker from './MediaPicker';
import Uploader from './Uploader';

window.MediaPicker = MediaPicker;

if ($('.image-picker').length !== 0) {
    new ImagePicker();
}

if ($('.file-picker').length !== 0) {
    new FilePicker();
}

if ($('.dropzone').length !== 0) {
    new Uploader();
}
