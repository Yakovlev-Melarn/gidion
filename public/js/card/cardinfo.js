let $cardDimensionsWidth = $(".cardDimensionsWidth");
let $cardDimensionsHeight = $(".cardDimensionsHeight");
let $cardDimensionsLength = $(".cardDimensionsLength");
let $volumetricWeight = $(".volumetricWeight");
let $costOfLogistics = $(".costOfLogistics");
let $print = $(".print");
let $cardDimensionsFields = $(".cardDimensionsLength,.cardDimensionsHeight,.cardDimensionsWidth");
let costOfLogistics = 63;
let volumetricWeight = ($cardDimensionsWidth.val() * $cardDimensionsHeight.val() * $cardDimensionsLength.val()) / 1000;
if (volumetricWeight > 1) {
    costOfLogistics = 63 + ((volumetricWeight - 1) * 12);
}
$costOfLogistics.html(Math.ceil(costOfLogistics));
$cardDimensionsFields.on('keyup change', function () {
    volumetricWeight = ($cardDimensionsWidth.val() * $cardDimensionsHeight.val() * $cardDimensionsLength.val()) / 1000;
    let costOfLogistics = 63;
    if (volumetricWeight > 1) {
        costOfLogistics = 63 + ((volumetricWeight - 1) * 12);
    }
    $volumetricWeight.html(Math.ceil(volumetricWeight));
    $costOfLogistics.html(Math.ceil(costOfLogistics));
    recalcPrice();
});
$('.trash').click(function () {
    let _this = $(this);
    _this.prop('disabled', true);
    $.post('/card/trash', {nmId: $('.card_nmid').val()}).done(function () {
        _this.prop('disabled', false);
    });
});

function recalcPrice() {
    let sellPrice = $(".sellPrice");
    let supplierPrice = $(".supplierPrice");
    let discount = $(".discount");
    let comission = $(".comission");
    let comissionPercent = $(".percent");
    let realPrice = Math.ceil(sellPrice.val() - (sellPrice.val() * (discount.val() / 100)));
    let profit = $(".profit");
    comission.html(Math.ceil(sellPrice.val() * (comissionPercent.html() / 100)));
    profit.html(realPrice - supplierPrice.val() - $costOfLogistics.html() - comission.html());
}

$print.click(function (e) {
    let _this = $(this);
    _this.parent().parent().find('input').prop('disabled', true);
    e.preventDefault();
    _this.parent().parent().parent().find('small').html('занят');
    $.post("/card/print", {
        sku: $('.cardSku').html(),
        barcode: _this.parent().parent().find('.bc').html(),
        name: $('.cardTitle').html() + ' ' + _this.parent().parent().find('.cardSize').html(),
        amount: $('.countBarcode').val()
    }).done(function () {
        _this.parent().parent().find('input').prop('disabled', false);
        _this.parent().parent().parent().find('small').html('Добавлено в очередь печати');
    }).fail(function () {
        _this.parent().parent().find('input').prop('disabled', false);
        _this.parent().parent().parent().find('small').html('Ошибка');
    });
});
$(".sellStock").on('click', function () {
    $.get('/card/getSellStockPrice/' + $('.card_id').val()).done(function (data) {
        if (data.status === 1) {
            $(".discount").val(data.discount);
            $(".sellPrice").val(data.price).keyup();
        }
    });
});
$(".sellSPrice").on('click', function () {
    let spp = $(".spp").val();
    let sPrice = $(".supplierPrice").val();
    let percent = 100 - spp;
    let price = sPrice / percent * 100;
    let sellPrice = Math.floor(price);
    if (sellPrice < 300) {
        sellPrice = sPrice;
    }
    $('.sellPrice').val(Math.floor(sellPrice));
});
$(".recalcPrice").on('keyup change', function () {
    recalcPrice();
});
$(".inputCheck,.discount").on('keyup change', function () {
    let inputsValues = 0;
    $(".inputCheck").each(function () {
        if ($(this).val() > 0) {
            inputsValues++;
        }
    });
    if (inputsValues === 5) {
        $('.updateInfo').prop('disabled', false);
    }
});
$("#suppliers").on('change', function () {
    $.post('/card/' + $('.card_id').val(), {
        supplier: $(this).val(),
        action: 'updateSupplier'
    }).done(function (data) {
        if (data.success === 1) {
            location.reload();
        }
    });
})
