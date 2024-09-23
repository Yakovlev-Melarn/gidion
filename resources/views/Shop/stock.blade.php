@extends('layouts.app');
@section('content')
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Остатки на складе {{ $warehouseName }}</h4>
                    @if(!$toSalle)
                        <p><a href="/shop/stocks/{{ $warehouseName }}/1">Не участвуют в распродаже</a></p>
                    @else
                        <p><a href="/shop/stocks/{{ $warehouseName }}">Показать все товары на складе</a></p>
                    @endif
                </div>
            </div>
            <div class="col-sm-6 p-md-0 text-right">
                <a href="/shop/stocks">&laquo; Назад</a>
            </div>
        </div>
    </div>
    @if($loaded)
        <div class="row">
            @foreach($stocks as $stock)
                @if(!empty($stock->card))
                    <div class="col-xl-3 col-lg-6 col-sm-6">
                        <div class="card" @if(!$saled[$stock->id]) style="background-color: #302d3d" @endif>
                            <div class="card-body">
                                <div class="new-arrival-product">
                                <span class="lightgallery">
                                    <a href="{{ $stock->card->photos[0]->big }}"
                                       data-exthumbimage="{{ $stock->card->photos[0]->big }}"
                                       data-src="{{ $stock->card->photos[0]->big }}" class="col-lg-3 col-md-6 mb-4">
                                        <img src="{{ $stock->card->photos[0]->big }}" alt="" style="width:100%;">
                                    </a>
                                </span>
                                    <div class="new-arrival-content text-center mt-3">
                                        <h4><a href="/card/{{ $stock->card->id }}">{{ $stock->card->title }}</a></h4>
                                        <a href="/card/{{ $stock->card->id }}"><span class="price">{{ $stock->quantity }} шт.</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
            @else
                <div class="welcome-text text-center">
                    <h4>Данные в процессе загрузки. Автоматическое обновление страницы через: <span
                            class="timer">60</span> сек.</h4>
                    <div class="sk-three-bounce" style="background-color:transparent; height: 100px">
                        <div class="sk-child sk-bounce1"></div>
                        <div class="sk-child sk-bounce2"></div>
                        <div class="sk-child sk-bounce3"></div>
                    </div>
                </div>
            @endif
        </div>
@endsection
@section('js')
    @if(!$loaded)
        <script src="{{ url('/js/loading.js') }}"></script>
    @endif
@endsection
