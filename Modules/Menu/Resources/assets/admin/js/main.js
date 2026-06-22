import "nestable2";

window.admin.removeSubmitButtonOffsetOn("#image");

$("#type").on("change", (e) => {
    $(".link-field").addClass("d-none");
    $(`.${e.currentTarget.value}-field`).removeClass("d-none");
});

$(".dd").nestable({ maxDepth: 15 });

$(".dd").on("change", () => {
    const orderData = $(".dd").nestable("serialize")[0];

    $.ajax({
        url: `${Korf.baseUrl}/admin/menus/items/order`,
        type: "PUT",
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(orderData),
        success: () => {
            success(trans("menu::messages.menu_items_order_updated"));
        },
        error: (xhr) => {
            const errorMessage = xhr.responseJSON && xhr.responseJSON.message
                ? xhr.responseJSON.message
                : "Произошла ошибка при обновлении порядка";

            error(errorMessage);
        }
    });
});

let id;
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$(".delete-menu-item").on("click", (e) => {
    e.preventDefault();
    id = $(e.currentTarget).closest(".dd-item").data("id");

    $.ajax({
        url: `${Korf.baseUrl}/admin/menus/items/${id}`,
        type: 'DELETE',
        success: () => {
            $(`.dd-item[data-id="${id}"]`).fadeOut();
        },
        error: (xhr) => {
            const errorMessage = xhr.responseJSON && xhr.responseJSON.message
                ? xhr.responseJSON.message
                : "Произошла ошибка при удалении";

            error(errorMessage);
        }
    });
});
