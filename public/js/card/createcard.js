let seller = 16;
let name = $('.productName').html();
let $cardContent = $('.cardContent');
let $objectName = $(".objectName");
let $subjectId = $(".subjectId");
let sku = $(".skuLink").html();
let pack = $(".pack");
$('.buttons').hide();
pack.on('change', function () {
    if ($('.packInfo').length) {
        $('.packInfo').html($(this).val());
        calcPrice();
    }
});
$.get('/cards/getObjectsAll?seller=' + seller + '&name=' + name).done(function (data) {
    for (let i in data) {
        $('.helpObjects').append('(' + data[i].subjectRootName + ')<a href="#" style="border-bottom: 1px dashed;color:#ed740b" class="changeHelpObject" data-id="' + data[i].subjectID + '">' + data[i].subjectName + '</a> <span style="color:#ed740b">↑</span> ');
    }
});
$('.sortable-img').sortable();
$('body').on('click', '.changeHelpObject', function () {
    $objectName.val($(this).html());
    $subjectId.val($(this).data('id'));
    $objectName.change();
});
$(".saveRule").on('click', function () {
    let rules = $("#changeRule input:checkbox:checked").map(function () {
        return $(this).val();
    }).get();
    $.post('/cards/saveRule', {
        subjectId: $('.subjectId').val(),
        facet: $('.productFieldName').html(),
        rules: rules
    }).done(function () {
        $('#changeRule').modal('toggle');
    });
});
$(".changeRules").on('click', function () {
    $('.productFieldName').html($(this).data('fieldname'));
    $('.rule').html('');
    $('.charcName').each(function (i) {
        $('.rule').append('<input type="checkbox" id="i' + i + '" value="' + $(this).html() + '" />&nbsp;&nbsp;&nbsp;<label for="i' + i + '">' + $(this).html() + '</label><br />');
    });
});
$objectName.on('change', function () {
    $cardContent.html('');
    $.get('/cards/getCharc/' + $subjectId.val() + '?seller=' + seller).done(function (data) {
        $cardContent.append('<div class="row"><div class="col-6">Артикул</div><div class="col-6">OB-' + sku + '-<span class="packInfo">' + pack.val() + '</span></div></div>');
        $cardContent.append('<div class="row"><div class="col-6">Наименование</div><div class="col-6"><input type="text" class="form form-control title" maxlength="60" value="' + $('.productName').html() + '"></div></div>');
        for (let i in data.data) {
            let info = data.data[i];
            if (info.name == 'Вес товара с упаковкой (г)' ||
                info.name == 'ИКПУ' ||
                info.name == 'Год выпуска' ||
                info.name == 'Форма упаковки' ||
                info.name == 'Дата окончания действия сертификата/декларации' ||
                info.name == 'Дата регистрации сертификата/декларации' ||
                info.name == 'Номер декларации соответствия' ||
                info.name == 'Ставка НДС' ||
                info.name == 'Номер сертификата соответствия' ||
                info.name == 'Код упаковки') {
                continue;
            }
            $cardContent.append('<div class="row"><div class="col-6 charcName">' + info.name + '</div><div class="col-6"><select multiple data-id="' + info.charcID + '" class="s2' + info.charcID + ' form form-control-sm"></select> </div></div>');
            maximumSelectionLength = 10;
            if (info.charcType == 4) {
                maximumSelectionLength = 1;
            }
            if (info.maxCount > 0) {
                maximumSelectionLength = info.maxCount;
            }
            $('.s2' + info.charcID).select2({
                tags: true,
                allowClear: true,
                maximumSelectionLength: maximumSelectionLength
            });
        }
        calcPrice();
        $('.buttons').show();
    });
});
$('.sendCard').click(function () {
    let btn = $(this);
    btn.prop('disable', true);
    let charList = [];
    $cardContent.find('select').each(function () {
        if ($(this).val().length > 0) {
            if (
                $(this).data('id') == 63260 || // Объем (л)
                $(this).data('id') == 14975 || // Количество отделений
                $(this).data('id') == 90658 || // Количество листов
                $(this).data('id') == 90632 || // Количество предметов в наборе
                $(this).data('id') == 15679 || // Количество в упаковке
                $(this).data('id') == 90703 || // Плотность бумаги
                $(this).data('id') == 90602) { // Диаметр предмета
                charList.push({
                    id: $(this).data('id'),
                    value: parseInt($(this).val())
                });
            } else {
                charList.push({
                    id: $(this).data('id'),
                    value: $(this).val()
                });
            }
        }
    });
    let card = {
        subjectID: Number($('.subjectId').val()),
        variants: [{
            vendorCode: 'DZ-N-' + sku + '-' + $('.packInfo').html(),
            title: $('.title').val(),
            description: $('.description').val(),
            brand: $('.brand').val(),
            dimensions: {
                height: Number($('.height').val()),
                length: Number($('.length').val()),
                width: Number($('.width').val()),
            },
            characteristics: charList,
            sizes: [{
                price: Number($('.sellPrice').val())
            }]
        }]
    };
    let photos = [];
    $('.lightgallery img').each(function () {
        photos.push($(this).attr('src'));
    });
    $.post('/cards/uploadCard', {
        sku: sku,
        vendorCode: 'DZ-N-' + sku + '-' + $('.packInfo').html(),
        seller: seller,
        cardData: {
            card: JSON.stringify(card),
            photos: photos
        }
    }).done(function () {
        btn.prop('disable', false);
    })
});
$('.getRules').click(function () {
    $cardContent.find('select').find('option').remove();
    $.get('/cards/getRules?subjectId=' + $('.subjectId').val()).done(function (data) {
        for (let i in data) {
            let rule = data[i];
            $('.charcName').each(function () {
                let select = $(this).parent().find('select');
                if (rule.filed == $(this).html()) {
                    $('.fname').each(function () {
                        if ($(this).html() == rule.facet) {
                            let value = $(this).parent().find('.fval');
                            let $newOption = $("<option selected='selected'></option>").val(value.html()).text(value.html());
                            select.append($newOption).trigger('change');
                        }
                    });
                }
            });
        }
    });
});

function calcPrice() {
    let basePriceDelivery = 63;
    let costPerLiter = 7;
    let h = $('.height').val();
    let l = $('.length').val();
    let w = $('.width').val();
    let liters = Math.ceil(h * l * w / 1000);
    let deliveryCost = basePriceDelivery;
    if (liters > 1) {
        deliveryCost = basePriceDelivery + ((liters - 1) * costPerLiter);
    }
    $('.deliveryCost').html(deliveryCost);
    $.get('/cards/comission?subject=' + $objectName.val()).done(function (data) {
        let comssion = Number(data) + 6;
        $('.comission').html(comssion);
        let sPrice = Number($('.sPrice').html())* $('.pack').val();
        if (sPrice < 50) {
            sPrice = 50;
        }
        let price = Math.ceil(((deliveryCost + (sPrice * 2)) / (100 - comssion) * 100));
        $('.sellPrice').val(price);
    });
}

$('.removeImage').click(function () {
    $(this).parent().parent().parent().remove();
});
$(".getObject").select2({
    width: "100%",
    ajax: {
        url: "/cards/getObjectsAll",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                name: params.term,
                seller: 13
            };
        },
        processResults: function (data) {
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.subjectName,
                        id: item.subjectID
                    }
                })
            };
        },
        cache: true
    },
    minimumInputLength: 3
});

