@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>{{ $title }} товаров - всего {{ $all }} шт.</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @foreach($cards as $orderId => $card)
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card">
                    <div class="card-header" style="padding: 3px">
                        <div class="col-md-6"><small>Артикул: {{ $card->nmID }}</small></div>
                        <div class="col-md-6 text-right"><small>Поставщик: {{ $card->origSku }}</small></div>
                    </div>
                    <div class="card-body">
                        <div class="new-arrival-product">
                            <a href="/card/{{ $card->id }}">
                                @if($card->photos->count())
                                    <img src="{{ $card->photos[0]->big }}" alt="" style="width:100%;">
                                @endif
                            </a>
                            <div class="new-arrival-content text-center mt-3">
                                <h4><a href="/card/{{ $card->id }}">{{ $card->title }}</a></h4>
                                <a href="/card/{{ $card->id }}">
                                    <span class="price">{{ $prices[$orderId] }} ₽</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer" style="padding: 12px">
                        <div class="row">
                            <div class="col-md-6"><small>
                                    @if($mp[$orderId] == 1)
                                        <span class="text-warning">Маркетплейс</span>
                                    @else
                                        @if($mp[$orderId] == 0)
                                        <span class="text-success">Склад</span>
                                            @else
                                            <span class="text-danger">{{ $mp[$orderId] }}</span>
                                        @endif
                                    @endif
                                </small></div>
                            <div class="col-md-6 text-right"><small>Создано: {{ date("d.m.Y",strtotime($card->createdAt)) }}</small></div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
@section('js')
@endsection
