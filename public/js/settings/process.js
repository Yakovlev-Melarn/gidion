$(".startProcessSyncCards").click(function () {
    $(this).prop('disabled', true);
    $.post('/settings/process/run', {process: 'syncCards'});
});
$(".startProcessDeleteCards").click(function () {
    $(this).prop('disabled', true);
    $.post('/settings/process/run', {process: 'deleteCards'});
});
$(".startProcessUpdatePrice").click(function () {
    $(this).prop('disabled', true);
    $.post('/settings/process/run', {
        process: 'updatePrice',
        percent: $('.percent').val(),
        wbpercent: $('.wbpercent').prop('checked') ? 1 : 0
    });
});
$(".startProcessRemoveDiscount").click(function () {
    $(this).prop('disabled', true);
    $.post('/settings/process/run', {process: 'removeDiscount'});
});
$(".startUploadPhotos").click(function () {
    $(this).prop('disabled', true);
    $.post('/settings/process/run', {process: 'uploadPhotos'});
});
$(".startProcessUpdateStock").click(function () {
    $(this).prop('disabled', true);
    $.post('/settings/process/run', {process: 'updateStock'});
});
$(".startTrash").click(function () {
    $(this).prop('disabled', true);
    $.post('/settings/process/run', {process: 'trash'});
});
