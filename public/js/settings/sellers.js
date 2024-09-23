$(".editsellerapikey").click(function (e) {
    e.preventDefault();
    let id = $(this).data('id');
    $(".edit-id-" + id).show();
});
$(".submitsellerapikey").click(function (e) {
    e.preventDefault();
    let _this = $(this);
    $.post('/settings/updateSeller', {
        id: _this.data('id'),
        key: $(".apiKey").val(),
        whID: $(".whID").val()
    }).done(function (data) {
        if (data.status === 1) {
            $(".edit-id-" + _this.data('id')).hide();
        }
    })
});
