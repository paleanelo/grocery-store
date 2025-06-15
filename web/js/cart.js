function renderQuantityControls(productId, quantity) {
    return `
        <div class="d-flex justify-content-between align-items-center w-100 quantity-controls">
            <button class="btn btn-sm btn-outline-danger decrease-qty" data-id="${productId}">-</button>
            <span class="mx-2">${quantity}</span>
            <button class="btn btn-sm btn-outline-success increase-qty" data-id="${productId}">+</button>
        </div>
    `;
}

$(document).on('click', '.add-to-cart', function () {
    var productId = $(this).data('id');
    var wrapper = $(this).closest('.cart-action-wrapper');

    $.ajax({
        url: '/cart/add?id=' + productId,
        type: 'POST',
        headers: {
            'X-CSRF-Token': yii.getCsrfToken()
        },
        success: function (res) {
            if (res.success) {
                wrapper.html(renderQuantityControls(productId, res.quantity));
            } else {
                alert(res.message || 'Ошибка');
            }
        },
        error: function () {
            alert('Ошибка при добавлении в корзину');
        }
    });
});

$(document).on('click', '.increase-qty', function () {
    var productId = $(this).data('id');
    var container = $(this).closest('.quantity-controls');

    $.ajax({
        url: '/cart/add?id=' + productId,
        type: 'POST',
        headers: {
            'X-CSRF-Token': yii.getCsrfToken()
        },
        success: function (res) {
            if (res.success) {
                container.find('span').text(res.quantity);
            }
        }
    });
});

$(document).on('click', '.decrease-qty', function () {
    var productId = $(this).data('id');
    var container = $(this).closest('.quantity-controls');

    $.ajax({
        url: '/web/cart/decrease?id=' + productId,
        type: 'POST',
        headers: {
            'X-CSRF-Token': yii.getCsrfToken()
        },
        success: function (res) {
            if (res.success) {
                if (res.quantity === 0) {
                    container.closest('.cart-action-wrapper').html(`
                        <button class="btn btn-warning w-100 add-to-cart" data-id="${productId}">Добавить в корзину</button>
                    `);
                } else {
                    container.find('span').text(res.quantity);
                }
            }
        }
    });
});
