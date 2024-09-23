@extends('layouts.app');
@section('content')
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Карточки товаров - всего {{ $all }} шт.</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <nav class="col-md-6">
            @if($all > 0)
                <ul class="pagination pagination-gutter pagination-primary no-bg">
                    @if($previousPage > 0)
                        <li class="page-item page-indicator">
                            <a class="page-link" href="{{ url("cards/list/{$previousPage}") }}">
                                <i class="la la-angle-left"></i></a>
                        </li>
                    @endif
                    @php
                        if($page<6){
                            $it = 0;
                            $max = 10;
                        } else {
                            $it = $page-5;
                            $max = 5+$page;
                            if($max > $totalPages){
                                $max = $totalPages;
                            }
                        }
                    @endphp
                    @for($i = $it;$i<$max;$i++)
                        @php
                            $p = $i+1;
                        @endphp
                        <li class="page-item @if($page == $p) active @endif"><a class="page-link"
                                                                                href="{{ url("cards/list/{$p}") }}">{{ $p }}</a>
                        </li>
                    @endfor
                    @if($nextPage > 0)
                        <li class="page-item page-indicator">
                            <a class="page-link" href="{{ url("cards/list/{$nextPage}") }}">
                                <i class="la la-angle-right"></i></a>
                        </li>
                    @endif
                </ul>
            @endif
        </nav>
        <div class="col-md-6">
            <div class="dropdown custom-dropdown">
                <div data-toggle="dropdown" aria-expanded="false">{{ $supplierName }}
                    <i class="fa fa-angle-down ml-3"></i>
                </div>
                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end"
                     style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(118px, 24px, 0px);">
                    <a class="dropdown-item fSupplier" href="#"
                       data-supplierid="0">Все поставщики</a>
                    @foreach($suppliers as $supplier)
                        <a class="dropdown-item fSupplier" href="#"
                           data-supplierid="{{ $supplier->supplierId }}">{{ $supplier->name }}</a>
                    @endforeach
                </div>
            </div>
            <div class="dropdown custom-dropdown">
                <div data-toggle="dropdown" aria-expanded="false">{{ $amountName }}
                    <i class="fa fa-angle-down ml-3"></i>
                </div>
                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end"
                     style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(118px, 24px, 0px);">
                    <a class="dropdown-item fAmount" href="#"
                       data-amountid="0">С любым остатком</a>
                    @foreach($amountNames as $key => $name)
                        <a class="dropdown-item fAmount" href="#"
                           data-amountid="{{ $key }}">{{ $name }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="row">
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
    </div>
    <nav>
        @if($all > 0)
            <ul class="pagination pagination-gutter pagination-primary no-bg">
                @if($previousPage > 0)
                    <li class="page-item page-indicator">
                        <a class="page-link" href="{{ url("cards/list/{$previousPage}") }}">
                            <i class="la la-angle-left"></i></a>
                    </li>
                @endif
                @php
                    if($page<6){
                        $it = 0;
                        $max = 10;
                    } else {
                        $it = $page-5;
                        $max = 5+$page;
                        if($max > $totalPages){
                            $max = $totalPages;
                        }
                    }
                @endphp
                @for($i = $it;$i<$max;$i++)
                    @php
                        $p = $i+1;
                    @endphp
                    <li class="page-item @if($page == $p) active @endif"><a class="page-link"
                                                                            href="{{ url("cards/list/{$p}") }}">{{ $p }}</a>
                    </li>
                @endfor
                @if($nextPage > 0)
                    <li class="page-item page-indicator">
                        <a class="page-link" href="{{ url("cards/list/{$nextPage}") }}">
                            <i class="la la-angle-right"></i></a>
                    </li>
                @endif
            </ul>
        @endif
    </nav>
@endsection
@section('js')
    <script src="{{ url('/js/card/list.js') }}"></script>
@endsection
