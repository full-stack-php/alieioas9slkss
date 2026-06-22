import tinymce from "tinymce";

export default function (options = {}) {
    tinymce.baseURL = `${window.location.origin}/backoffice/assets/tinymce`;

    let themeAttribute = document.documentElement.getAttribute('data-bs-theme');
    let themeSkin = 'oxide';
    let contentStyle = 'body { font-size: 14px; color: #555555; }';
    let contentCss = '';

    if (themeAttribute === 'dark') {
        themeSkin = 'oxide-dark';
        contentStyle = 'body { font-size: 14px; color: #dddddd; background-color: #272c33; }';
        contentCss = 'dark';
    }

    tinymce.init({
        selector: ".wysiwyg",
        theme: 'silver',
        skin: themeSkin,
        height: 350,
        menubar: false,
        branding: false,
        image_advtab: true,
        automatic_uploads: true,
        media_alt_source: false,
        media_poster: false,
        relative_urls: false,
        toolbar_mode: "sliding",
        images_file_types: 'jpg,svg,webp,png,gif',
        directionality: Korf.rtl ? "rtl" : "ltr",
        cache_suffix: `?v=${Korf.version}`,
        content_css: contentCss,
        content_style: contentStyle,
        plugins:
            "lists, link, table, image, media, paste, autosave, autolink,quickbars, wordcount, code, fullscreen",
        toolbar:
            "styleselect | bold italic underline strikethrough blockquote | bullist numlist | alignleft aligncenter alignright alignjustify | outdent indent | forecolor removeformat | table | image media link | code fullscreen",
        quickbars_selection_toolbar:
            "bold italic | quicklink h2 h3 blockquote quickimage quicktable",
        extended_valid_elements: "img[class|src|alt|title|width|loading=lazy]",

        images_upload_handler(blobInfo, success, failure) {
            let formData = new FormData();
            formData.append("file", blobInfo.blob(), blobInfo.filename());
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            $.ajax({
                url: `${Korf.baseUrl}/admin/media`,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    success(response.path);
                },
                error: function (xhr) {
                    let message = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : "Upload failed";
                    failure(message);
                }
            });
        },
        ...options,
    });

    return tinymce;
}
