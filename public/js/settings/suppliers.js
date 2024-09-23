$(".deletesupplier").click(function (e) {
    e.preventDefault();
    let _this = $(this);
    $.post('/settings/deleteSupplier', {
        id: _this.data('id')
    }).done(function (data) {
        if (data.status === 1) {
            _this.parent().parent().parent().remove();
        }
    })
});
