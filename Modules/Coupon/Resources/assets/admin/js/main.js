import tinyMCE from "@admin/js/wysiwyg";
import flatpickr from "flatpickr";

document.addEventListener('DOMContentLoaded', function (e) {
    $('.datetime-picker').flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
});


