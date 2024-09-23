@extends('layouts.app');
@section('content')
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4 class="">Остатки на складах</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 text-right">
                <a href="/shop/stocks/товаров, которые не участвуют в распродаже">Не участвуют в распродаже</a>
            </div>
        </div>
        <div class="card" style="height: auto;">
            <div class="card-body">
                @foreach($result as $item)
                    <h6><a href="/shop/stocks/{{ $item['name'] }}">{{ $item['name'] }}</a>
                        <span class="pull-right">{{ $item['percent'] }}% / {{ $item['amount'] }}</span>
                    </h6>
                    <div class="progress ">
                        <div class="progress-bar progress-animated"
                             style="width: {{ $item['percent'] }}%; height:6px; background-color: {{ \App\Http\Libs\Helper::randColor() }}"
                             role="progressbar">
                            <span class="sr-only">{{ $item['percent'] }}% / {{ $item['amount'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection
