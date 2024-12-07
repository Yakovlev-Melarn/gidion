$('.scanner').focus();
let ordersPack = [];
const time = 5000;
const step = 1;
const sellerId = $("#sellerId").val();
const issetDelivery = $('.issetDelivery');

function outNum(num, elem) {
    let l = $('.' + elem)
    n = 0;
    let t = Math.round(time / (num / step));
    console.log(t);
    let interval = setInterval(() => {
        n = n + step;
        if (n === num) {
            clearInterval(interval);
        }
        l.html(n);
    }, t);
}

function findOrder(find) {
    for (let i = 0; i < ordersPack.length; i++) {
        if (ordersPack[i][find]) {
            console.log(i);
        }
    }
}

function deliveryRow(i) {
    issetDelivery.append('<div class="row">\n' +
        '                <div class="col-1"><input type="radio" name="deliveryId" checked class="' + i.id + '"></div>\n' +
        '                <div class="col-4">' + i.id + '</div>\n' +
        '                <div class="col-4">' + i.name +
        ' <span class="badge badge-danger text-white badge-sm float-right ' + i.id + '"></span> </div>\n' +
        '                <div class="col-3">\n' +
        '                    <button type="button" disabled class="btn btn-primary btn-sm sendDelivery">\n' +
        '                        Отгрузить\n' +
        '                    </button>\n' +
        '                </div>\n' +
        '            </div>\n' +
        '            <hr />');
}

function getOrders(d) {
    $.post('/shop/getOrders', {
        seller: sellerId,
        delivery: d
    }).done(function (data) {
        data.forEach(function (i) {
            ordersPack.push(i);
        });
        outNum(data.length, d);
        $('.sendDelivery').prop('disabled', false);
        $('.addDelivery').prop('disabled', false);
    });
}

function getDeliveries() {
    ordersPack = [];
    $.post('/shop/getDeliveries', {
        seller: sellerId
    }).done(function (data) {
        data.forEach(function (i) {
            deliveryRow(i);
            getOrders(i.id);
        });
    });
}

getDeliveries();
$('.addDelivery').on('click', function () {
    $.post('/shop/addDelivery', {
        seller: sellerId
    }).done(function () {
        issetDelivery.html('');
        getDeliveries();
    });
});


