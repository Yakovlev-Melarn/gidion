$(".fSupplier").click(function () {
    let supplier = $(this).data('supplierid');
    $.post('/cards/changeFilter', {supplier: supplier}).done(function () {
        location.reload();
    })
});
$(".fAmount").click(function () {
    let amount = $(this).data('amountid');
    $.post('/cards/changeFilter', {amount: amount}).done(function () {
        location.reload();
    })
});
