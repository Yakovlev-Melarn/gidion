@extends('layouts.app');
@section('content')
    @if(!session()->has('sellerId'))
        <h3 class="text-center">Не добавлено ни одного магазина.</h3>
        <div class="text-center"><a class="btn btn-primary" href="/settings/addSeller">Добавить</a></div>
    @else
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-sm-12" style="height: 450px">
                <div class="card">
                    <div class="card-header">
                        <input type="hidden" class="selectedDate" value="{{ $selectedDate }}">
                        <div class="col-md-9">
                            <h4 class="">Сводка на {{ date("d.m.Y", strtotime($selectedDate)) }}</h4>
                        </div>
                        <div class="col-md-3">
                            <a
                                href="/{{ date("Y-m-d H:i:s", strtotime($selectedDate)-86400) }}">&laquo; Предыдущий
                                день</a>
                            | <a href="/{{ date("Y-m-d H:i:s", strtotime($selectedDate)+86400) }}">
                                Следующий день &raquo;</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="lineChart_3"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-3 col-xxl-4 col-lg-6 col-sm-6">
                <div class="widget-stat card">
                    <div class="card-body p-4">
                        <div class="media">
									<span class="mr-3">
										<i class="flaticon-091-shopping-cart"></i>
									</span>
                            <div class="media-body text-white text-right">
                                <p class="mb-1">Заказано</p>
                                <h3 class="text-white"><a href="/shop/ordered/{{ $selectedDate }}"
                                                          class="tOrders">{{ number_format($ordersSum,0,'.',' ') }}
                                        ₽</a></h3>
                                <p class="mb-0 fs-13">
                                    <span class="text-warning mr-1">{{ $ordersCount }}</span> шт.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-xxl-4 col-lg-6 col-sm-6">
                <div class="widget-stat card">
                    <div class="card-body p-4">
                        <div class="media">
									<span class="mr-3">
										<i class="flaticon-008-credit-card"></i>
									</span>
                            <div class="media-body text-white text-right">
                                <p class="mb-1">Выкуплено</p>
                                <h3 class="text-white"><a href="/shop/saled/{{ $selectedDate }}"
                                                          class="tOrders">{{ number_format($salesSum,0,'.',' ') }} ₽</a>
                                </h3>
                                <p class="mb-0 fs-13">
                                    <span class="text-success mr-1">{{ $salesCount }}</span> шт.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-xxl-4 col-lg-6 col-sm-6">
                <div class="widget-stat card">
                    <div class="card-body p-4">
                        <div class="media">
									<span class="mr-3">
										<i class="flaticon-055-cube"></i>
									</span>
                            <div class="media-body text-white text-right">
                                <p class="mb-1">Остатки на складах</p>
                                <h3 class="text-white"><a href="/shop/stocks">{{ $productOnWh }} шт.</a></h3>
                                <p class="mb-0 fs-13">
                                    <span class="text mr-1">{{ number_format($stockPrice,0,'.',' ') }}</span> ₽
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-xxl-4 col-lg-6 col-sm-6">
                <div class="widget-stat card">
                    <div class="card-body p-4">
                        <div class="media">
									<span class="mr-3">
										<i class="flaticon-023-move"></i>
									</span>
                            <div class="media-body text-white text-right">
                                <p class="mb-1">В пути к клиенту</p>
                                <h3 class="text-white"><a href="#">{{ $inWayToClient }} шт.</a></h3>
                                <p class="mb-0 fs-13">
                                    <span class="text mr-1">{{ number_format($inWayToClientPrice,0,'.',' ') }}</span> ₽
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-xxl-4 col-lg-6 col-sm-6">
                <div class="widget-stat card">
                    <div class="card-body p-4">
                        <div class="media">
									<span class="mr-3">
										<i class="flaticon-023-move"></i>
									</span>
                            <div class="media-body text-white text-right">
                                <p class="mb-1">В пути от клиента</p>
                                <h3 class="text-white"><a href="#">{{ $inWayFromClient }} шт.</a></h3>
                                <p class="mb-0 fs-13">
                                    <span class="text-danger mr-1">{{ number_format($inWayFromClientPrice,0,'.',' ') }}</span> ₽
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@section('js')
    <script src="{{ url('/vendor/chart.js/Chart.bundle.min.js') }}"></script>
    <script src="{{ url('/js/plugins-init/chartjs-init.js') }}"></script>
    <script src="{{ url('/js/index/index.js') }}"></script>
@endsection
