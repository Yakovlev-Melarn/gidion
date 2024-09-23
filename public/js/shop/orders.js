$('.delivery').focus();
$(".printAll").click(function () {
    let _this = $(this);
    _this.prop('disabled', true);
    let shipment = $(this).data('shipment');
    $.post('/shop/printAll', {
        shipment: shipment
    }).done(function () {
        _this.prop('disabled', false);
    });
});
$(".print").click(function () {
    let _this = $(this);
    _this.prop('disabled', true);
    let orderId = _this.data('orderid');
    $.post('/shop/print', {
        orderId: orderId
    }).done(function () {
        _this.prop('disabled', false);
    });
});
$(".delivery").click(function () {
    let _this = $(this);
    let orderId = _this.data('orderid');
    if (_this.parent().parent().parent().find('.w').length) {
        let width = _this.parent().parent().parent().find('.w');
        let height = _this.parent().parent().parent().find('.h');
        let length = _this.parent().parent().parent().find('.l');
        if (width.val() == 0 || height.val() == 0 || length.val() == 0) {
            _this.parent().parent().parent().find('.whl').css('border', 'solid 1px red');
        } else {
            _this.prop('disabled', true);
            $.post('/shop/updateWhl', {
                cardId: _this.data('cardid'),
                cardDimensionsWidth: width.val(),
                cardDimensionsHeight: height.val(),
                cardDimensionsLength: length.val()
            }).done(function () {
                $.post('/shop/orderComplete', {
                    orderId: orderId
                }).done(function () {
                    _this.parent().parent().parent().parent().parent().parent().parent().parent().remove();
                    $('.srch').focus();
                });
            });
        }
    } else {
        $.post('/shop/orderComplete', {
            orderId: orderId
        }).done(function () {
            _this.parent().parent().parent().parent().parent().parent().parent().parent().remove();
            $('.srch').focus();
        });
    }
});

