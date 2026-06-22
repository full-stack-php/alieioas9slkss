import tinyMCE from "@admin/js/wysiwyg";
import flatpickr from "flatpickr";
import ProductForm from './ProductForm';
import ProductPackagingManager from './ProductPackagingManager';
import ProductGiftManager from './ProductGiftManager';
import BundleManager from './BundleManager';

document.addEventListener('DOMContentLoaded', function (e) {
    $('.datetime-picker').flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
});




new ProductForm();
new ProductPackagingManager();
new ProductGiftManager();
new BundleManager();

tinyMCE();

