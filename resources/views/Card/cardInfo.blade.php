@extends('layouts.app');
@section('content')
    @if($loaded)
        <form method="post" action="/card/{{ $card->id }}">
            @csrf
            <input type="hidden" value="{{ $card->id }}" class="card_id">
            <input type="hidden" value="{{ $card->nmID }}" class="card_nmid">
            <input type="hidden" value="{{ $card->sizes[0]->skus }}" class="cardBarcode">
            <input type="hidden" value="update" name="action">
            <div class="container-fluid">
                <div class="row page-titles mx-0">
                    <div class="col-sm-6 p-md-0">
                        <div class="welcome-text">
                            <h4 class="cardTitle">{{ $card->title }}</h4>
                            <p class="mb-0">nmID:
                                <a href="https://www.wildberries.ru/catalog/{{ $card->nmID }}/detail.aspx?targetUrl=GP"
                                   target="_blank">{{ $card->nmID }}</a> |
                                SKU: @if(!empty($card->cardSupplier->url))<a
                                    href="{{ $card->cardSupplier->url }}{{ $card->supplierSku }}@if($card->cardSupplier->name == 'Wildberries') /detail.aspx @endif"
                                    target="_blank"><span class="cardSku">{{ $card->vendorCode }}</span></a> @else <span
                                    class="cardSku">{{ $card->vendorCode }}</span> @endif
                                |
                                Поставщик: <select class="mr-sm-2 mb-3 default-select form-control-sm"
                                                   id="suppliers">
                                    @if($card->supplier === 0)
                                        <option class="chose" selected>Выберите...</option>
                                    @endif
                                    @foreach($suppliers as $supplier)
                                        <option @if($supplier->supplierId == $card->supplier) selected
                                                @endif value="{{ $supplier->supplierId }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select> |
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-6 p-md-0 text-right">
                        <a href="/{{ $backUrl }}">&laquo; Назад</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-4  col-md-4 col-xxl-4 pr-4"
                                         style="border-right: dashed 2px #2c254a">
                                        <div class="lightgallery">
                                            @foreach($card->photos as $key => $photo)
                                                <a href="{{ $photo->big }}"
                                                   data-exthumbimage="{{ $photo->big }}"
                                                   data-src="{{ $photo->big }}"
                                                   class="col-lg-3 col-md-6 mb-4 @if($key > 0) hf @endif">
                                                    <img src="{{ $photo->big }}" alt="" style="width:100%;">
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4  col-md-4 col-xxl-4 col-sm-12 pr-4"
                                         style="border-right: dashed 2px #2c254a">
                                        <div class="product-detail-content">
                                            <div class="new-arrival-content pr">
                                                <h4>Весогабаритные характеристики</h4>
                                                <div class="row">
                                                    <div class="col-md-5 col-sm-12">Длина:</div>
                                                    <div class="col-md-7 col-sm-12">
                                                <span class="item">
                                                    <input type="number" class="fc-mini cardDimensionsWidth inputCheck"
                                                           name="cardDimensionsWidth"
                                                           value="{{ $card->dimensions->width }}">
                                                </span>
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-md-5 col-sm-12">Ширина:</div>
                                                    <div class="col-md-7 col-sm-12">
                                                <span class="item">
                                                    <input type="number" class="fc-mini cardDimensionsHeight inputCheck"
                                                           name="cardDimensionsHeight"
                                                           value="{{ $card->dimensions->height }}">
                                                </span>
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-md-5 col-sm-12">Высота:</div>
                                                    <div class="col-md-7 col-sm-12">
                                                <span class="item">
                                                    <input type="number" class="fc-mini cardDimensionsLength inputCheck"
                                                           name="cardDimensionsLength"
                                                           value="{{ $card->dimensions->length }}">
                                                </span>
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-md-5 col-sm-12">Объемный вес:</div>
                                                    <div class="col-md-7 col-sm-12">
                                                <span class="item volumetricWeight inputCheck"
                                                      style="margin-left: 11px">{{ $volumetricWeight }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="new-arrival-content pr mt-4">
                                                <div class="row">
                                                    <div class="col-md-6"><h4>Цены и скидки</h4></div>
                                                    <div class="col-md-6 text-right">
                                                        <a href="#" class="sellStock">
                                                            <small>Распродать в 0</small>
                                                        </a>
                                                        <a href="#" class="sellSPrice">
                                                            <small>Распродать в -</small>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-5">Закупочная цена:</div>
                                                    <div class="col-md-7">
                                                <span class="item">
                                                    <input type="number"
                                                           class="fc-mini supplierPrice inputCheck recalcPrice"
                                                           name="supplierPrice" value="{{ $card->prices->s_price }}">
                                                </span>
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-md-5">Цена продажи:</div>
                                                    <div class="col-md-7">
                                                <span class="item">
                                                    <input type="number"
                                                           class="fc-mini sellPrice inputCheck recalcPrice"
                                                           name="sellPrice"
                                                           value="{{ $card->prices->price }}">
                                                </span>
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-md-5">Скидка:</div>
                                                    <div class="col-md-7">
                                                <span class="item">
                                                    <input type="number" class="fc-mini discount recalcPrice"
                                                           name="discount" value="{{ $card->prices->discount }}">
                                                </span>
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-md-5">СПП:</div>
                                                    <div class="col-md-7">
                                                <span class="item">
                                                    <input type="number" class="fc-mini spp recalcPrice"
                                                           name="spp" value="{{ $spp }}">
                                                </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="new-arrival-content pr mt-4">
                                                <h4>Расчет дохода</h4>
                                                <div class="row">
                                                    <div class="col-md-5">Стоимость логистики:</div>
                                                    <div class="col-md-7">
                                                        <span class="item costOfLogistics">{{ $costOfLogistics }}</span>
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-md-5">Комиссия с продажи:</div>
                                                    <div class="col-md-7">
                                                        <span class="item comission">{{ $comission }}</span><span
                                                            class="item"> / </span><span
                                                            class="item percent">{{ $comissionPercent }}</span><span
                                                            class="item">%</span>
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-md-5">Чистая прибыль:</div>
                                                    <div class="col-md-7"><span
                                                            class="item profit">{{ $realPrice-$comission-$costOfLogistics-$card->prices->s_price }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            @if(!empty($seller->whID))
                                                <hr/>
                                                <div class="new-arrival-content pr mt-4">
                                                    <input type="checkbox" value="1" name="removeStock" id="removeStock"
                                                           @if($localStock['amount']==0) checked @endif > <label
                                                        for="removeStock">Снять с остатков</label>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4  col-md-4 col-xxl-4 col-sm-12 pr-4">
                                        <div class="product-detail-content">
                                            <div class="new-arrival-content pr">
                                                <h4>Печать ШК</h4>
                                                <div class="row">
                                                    @foreach($card->sizes as $size)
                                                        <div class="col-md-12 col-sm-12">
                                                            <div class="input-group input-group-sm pt-2">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">@if($size->techSize)
                                                                            <span
                                                                                class="cardSize">р-р {{ $size->techSize }}</span>
                                                                            &nbsp;&nbsp;|&nbsp;&nbsp; @else <span
                                                                                class="cardSize"></span> @endif <span
                                                                            class="bc">{{ $size->skus }}</span>&nbsp;&nbsp;| Количество:</span>
                                                                </div>
                                                                <input type="number" class="form-control countBarcode"
                                                                       value="1">
                                                                <div class="input-group-prepend">
                                                                    <a href="#" class="btn btn-sm print">
                                                                        <i class="fa fa-print"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="text-right errorPrint"><small></small></div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <hr/>
                                            <div class="new-arrival-content pr mt-4">
                                                <h4>Остатки на сладах</h4>
                                                @if ($stocks->count() > 0)
                                                    <div class="row mt-1">
                                                        <div class="col-md-6 col-sm-12"></div>
                                                        <div class="col-md-6 col-sm-12">
                                                            <div class="row bootstrap-popover-wrapper">
                                                                <div class="col-md-3 bootstrap-popover mr-0">
                                                                    <i class="fa fa-cube" data-container="body"
                                                                       data-toggle="popover" data-placement="top"
                                                                       data-content="Всего на складе"
                                                                       title="Обозначение"></i>
                                                                </div>
                                                                <div class="col-md-3 bootstrap-popover mr-0">
                                                                    <i class="fa fa-cube text-success"
                                                                       data-container="body"
                                                                       data-toggle="popover" data-placement="top"
                                                                       data-content="В пути к клиенту"
                                                                       title="Обозначение"></i>
                                                                </div>
                                                                <div class="col-md-3 bootstrap-popover mr-0">
                                                                    <i class="fa fa-cube text-danger"
                                                                       data-container="body"
                                                                       data-toggle="popover" data-placement="top"
                                                                       data-content="В пути от клиента"
                                                                       title="Обозначение"></i>
                                                                </div>
                                                                <div class="col-md-3 bootstrap-popover mr-0">
                                                                    <i class="fa fa-cube text-info"
                                                                       data-container="body"
                                                                       data-toggle="popover" data-placement="top"
                                                                       data-content="Свободный остаток"
                                                                       title="Обозначение"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @foreach($stocks as $stock)
                                                        <div class="row mt-1">
                                                            <div class="col-md-6 col-sm-12">
                                                                {{ $stock->warehouseName }}:
                                                            </div>
                                                            <div class="col-md-6 col-sm-12">
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                    <span
                                                                        class="item"> {{$stock->quantityFull}}</span>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                    <span
                                                                        class="item text-success"> {{$stock->inWayToClient}}</span>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                    <span
                                                                        class="item text-danger"> {{$stock->inWayFromClient}}</span>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                    <span
                                                                        class="item text-info"> {{$stock->quantity}}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                <hr/>
                                                @if($card->supplier == 10)
                                                    <div class="row mt-1">
                                                        <div class="col-md-6 col-sm-12">
                                                            На складе поставщика:
                                                        </div>
                                                        <div class="col-md-6 col-sm-12">
                                                            {{ $card->slstock->amount }}
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="row mt-1">
                                                    <div class="col-md-12 col-sm-12">
                                                        <h5>На собственном складе:</h5>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="input-group input-group-sm pt-2">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">Количество</span>
                                                                    </div>
                                                                    <input type="number"
                                                                           class="form-control localAmount"
                                                                           name="localAmount"
                                                                           value="{{ $localStock['amount'] }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="input-group input-group-sm pt-2">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">Адрес</span>
                                                                    </div>
                                                                    <input type="text" required
                                                                           class="form-control address"
                                                                           name="address"
                                                                           value="{{ $localStock['address'] }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr/>
                                            <div class="new-arrival-content pr">
                                                <h4>Статистика продаж</h4>
                                                <div class="row">
                                                    <div class="col-md-6 col-sm-12">Заказано:</div>
                                                    <div class="col-md-6 col-sm-12"><span class="item">123</span></div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-md-6 col-sm-12">Продано:</div>
                                                    <div class="col-md-6 col-sm-12"><span class="item">123</span></div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-md-6 col-sm-12">Возвращено:</div>
                                                    <div class="col-md-6 col-sm-12"><span class="item">123</span></div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-md-6 col-sm-12">Количество дней на сайте:</div>
                                                    <div class="col-md-6 col-sm-12"><span class="item">123 дн.</span>
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-md-6 col-sm-12">Последний заказ:</div>
                                                    <div class="col-md-6 col-sm-12"><span class="item">123 дн.</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-4 col-lg-4  col-md-4 col-xxl-4 col-sm-12 pr-4 text-right"></div>
                                    <div class="col-xl-4 col-lg-4  col-md-4 col-xxl-4 col-sm-12 pr-4 text-right">
                                        <button type="submit" class="btn btn-warning updateInfo">Обновить
                                        </button>
                                    </div>
                                    <div class="col-xl-4 col-lg-4  col-md-4 col-xxl-4 col-sm-12 pr-4 text-right">
                                        <button class="btn btn-danger trash">Перенсти в корзину</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @else
        <div class="welcome-text text-center">
            <h4>Данные в процессе загрузки. Автоматическое обновление страницы через: <span
                    class="timer">5</span> сек.</h4>
            <div class="sk-three-bounce" style="background-color:transparent; height: 100px">
                <div class="sk-child sk-bounce1"></div>
                <div class="sk-child sk-bounce2"></div>
                <div class="sk-child sk-bounce3"></div>
            </div>
        </div>
    @endif
@endsection
@section('js')
    @if(!$loaded)
        <script src="{{ url('/js/loading.js') }}"></script>
    @endif
    <script src="{{ url('/js/card/cardinfo.js') }}"></script>
@endsection
