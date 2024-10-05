@extends('layouts.app');
@section('content')
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Результаты поиска</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @if(isset($cards))
            @foreach($cards as $card)
                <div class="col-xl-3 col-lg-6 col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="new-arrival-product">
                                <a href="/card/{{ $card->id }}">
                                    @if($card->photos->count())
                                        <img src="{{ $card->photos[0]->big }}" alt="" style="width:100%;">
                                    @endif
                                </a>
                                <div class="new-arrival-content text-center mt-3">
                                    <h4><a href="/card/{{ $card->id }}">{{ $card->title }}</a></h4>
                                    @if($card->prices)
                                        <a href="/card/{{ $card->id }}">
                                            <span class="price">{{ $card->prices->price }} ₽</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
        @if(isset($orders))
            @foreach($orders as $order)
                <div class="col-lg-12 col-xl-6">
                    <div class="card">
                        <div class="card-header"><a
                                href="/shop/orders/{{ $order->shipmentId }}">Поставка {{ $order->shipmentId }}</a></div>
                        <div class="card-body">
                            <div class="row m-b-30">
                                <div class="col-md-5 col-xxl-12">
                                    <div class="new-arrival-product mb-4 mb-xxl-4 mb-md-0">
                                        <div class="new-arrivals-img-contnent">
                                            @if($order->card->photos->count())
                                                <img class="img-fluid" src="{{ $order->card->photos[0]->big }}" alt="">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7 col-xxl-12">
                                    <div class="new-arrival-content position-relative">
                                        <h4><a href="/card/{{ $order->card->id }}"
                                               target="_blank">{{ $order->card->title }}</a></h4>
                                        <div class="comment-review star-rating">
                                        <span style="color: wheat">{{ $order->partA }} <span
                                                style="font-size: 36px">{{ $order->partB }}</span></span>
                                            <p class="price">{{ $order->convertedPrice/100 }} ₽</p>
                                        </div>
                                        @if($order->card->slstock)
                                            @if($order->card->slstock->is_local)
                                                <p>Наличие: <span class="item"> На складе {{ $order->card->slstock->amount }} <i
                                                            class="fa fa-check-circle text-success"></i></span></p>
                                                <p>Ячейка: <span
                                                        class="item">{{ $order->card->slstock->address }}</span>
                                                </p>
                                            @else
                                                <p>Наличие: <span class="item"> У поставщика {{ $order->card->slstock->amount }} <i
                                                            class="fa fa-check-circle text-warning"></i></span></p>
                                                @php
                                                    use App\Http\Libs\Helper;if(isset($order)){
if($order->card->dimensions->width == 10 && $order->card->dimensions->height == 10 && $order->card->dimensions->length==10){
                                                            if($order->card->cardcatalog){
                                                                $order->card->dimensions->width = ceil(Helper::arrSearch(json_decode($order->card->cardcatalog->package_size,1),'type','width'));
                                                                $order->card->dimensions->height = ceil(Helper::arrSearch(json_decode($order->card->cardcatalog->package_size,1),'type','height'));;
                                                                $order->card->dimensions->length = ceil(Helper::arrSearch(json_decode($order->card->cardcatalog->package_size,1),'type','depth'));;
                                                            } else {
                                                                $order->card->dimensions->width = 0;
                                                                $order->card->dimensions->height = 0;
                                                                $order->card->dimensions->length = 0;
                                                            }
                                                    }
}
                                                @endphp
                                                Габариты:
                                                    <div class="item">
                                                        <div class="input-group input-group-sm pt-2 whl">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Длина</span>
                                                            </div>
                                                            <label>
                                                                <input type="number" class="form-control w"
                                                                       value="{{ $order->card->dimensions->width }}">
                                                            </label>
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Ширина</span>
                                                            </div>
                                                            <label>
                                                                <input type="number" class="form-control h"
                                                                       value="{{ $order->card->dimensions->height }}">
                                                            </label>
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Высота</span>
                                                            </div>
                                                            <label>
                                                                <input type="number" class="form-control l"
                                                                       value="{{ $order->card->dimensions->length }}">
                                                            </label>
                                                        </div>
                                                    </div>
                                            @endif
                                        @endif
                                        <p>Артикул: <span class="item">{{ $order->article }}</span></p>
                                        <p>ШК: <span class="item">{{ $order->skus }}</span></p>
                                        @if($order->card->sizes)
                                            @foreach($order->card->sizes as $size)
                                                @if($size->skus == $order->skus && $size->techSize > 0)
                                                    <p>Размер: <span class="item">{{ $size->techSize }}</span></p>
                                                @endif
                                            @endforeach
                                        @endif
                                        <p>Заказано: <span
                                                class="item">{{ date("d.m.Y H:i:s",strtotime($order->createdAt)) }}</span>
                                        </p>
                                        <p>Отмена: <span
                                                class="item">{{ date("d.m.Y H:i:s",strtotime($order->createdAt)+604800) }}</span>
                                        </p>
                                        <p>Штраф за отмену: <span
                                                class="item text-danger">{{ ceil($order->convertedPrice/100*0.35)<100?100:ceil($order->convertedPrice/100*0.35) }} ₽</span>
                                        </p>
                                        <p>Статус заказа: @if($order->actual)<span
                                                class="item text-success">Актуален</span>@else<span
                                                class="item text-danger">Отменен</span>@endif
                                        </p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <button class="btn btn-info print" data-orderid="{{ $order->orderId }}">
                                                    Распечатать ШК
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button class="btn btn-info delivery"
                                                        data-cardid="{{ $order->card->id }}"
                                                        data-orderid="{{ $order->orderId }}">Отгрузить
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
@section('js')
    @if(isset($orders))
        <script src="{{ url('/js/shop/orders.js') }}"></script>
    @endif
@endsection
