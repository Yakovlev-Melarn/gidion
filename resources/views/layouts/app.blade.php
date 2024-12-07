<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="token" content="{{ session()->get('token') }}">
    <meta name="csrf" content="{{ csrf_token() }}">
    <title>Gidion - Seller Portal </title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('/images/favicon.png') }}">
    <link href="{{ url('/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ url('/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ url('/vendor/lightgallery/css/lightgallery.min.css') }}" rel="stylesheet">
    <link href="{{ url('/vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ url('/vendor/jquery-ui/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ url('/css/style.css') }}" rel="stylesheet">
    @viteReactRefresh
    @vite('resources/js/app.jsx')
    @yield('css')
</head>
<body>
<div id="preloader">
    <div class="sk-three-bounce">
        <div class="sk-child sk-bounce1"></div>
        <div class="sk-child sk-bounce2"></div>
        <div class="sk-child sk-bounce3"></div>
    </div>
</div>
<div id="main-wrapper">
    <div class="nav-header">
        <a href="/" class="brand-logo" style="justify-content:center">
            <h4>Gidion</h4>
        </a>
        <div class="nav-control">
            <div class="hamburger">
                <span class="line"></span><span class="line"></span><span class="line"></span>
            </div>
        </div>
    </div>
    <div class="header">
        <div class="header-content">
            <nav class="navbar navbar-expand">
                <div class="collapse navbar-collapse justify-content-between">
                    <div class="header-left">
                        <form method="post" action="{{ url('search') }}">
                            @csrf
                            <label for="searchField"></label>
                            <label for="searchTypeField"></label>
                            <div class="input-group search-area right d-lg-inline-flex d-none" style="width: 730px">
                                <input type="text" id="searchField" name="search" required class="form-control srch"
                                       placeholder="Поиск...">
                                <div class="input-group-append">
                                    <span class="input-group-text"><button class="btn btn-sm" type="submit"
                                                                           href="javascript:void(0)"><i
                                                class="flaticon-381-search-2"></i></button></span>
                                </div>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <select name="type" id="searchTypeField" class="form-control-sm"
                                                style="background: #2C254A !important; border-color: transparent; height: 50px; width: 130px; color: #fff">
                                            <option value="card"
                                                    @if(isset($searchType)) @if($searchType == 'card') selected @endif @endif>
                                                Товар
                                            </option>
                                            <option value="order"
                                                    @if(isset($searchType)) @if($searchType == 'order') selected @endif @endif>
                                                Заказ
                                            </option>
                                        </select></div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <x-sellers></x-sellers>
                </div>
            </nav>
        </div>
    </div>
    <div class="deznav">
        <div style="min-height: 110px"></div>
        <div class="deznav-scroll">
            <ul class="metismenu" id="menu">
                <li><a class="has-arrow ai-icon" href="#" aria-expanded="false">
                        <i class="flaticon-091-shopping-cart"></i>
                        <span class="nav-text">Магазин</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{url('/shop/orders')}}">Заказы</a></li>
                        <li><a href="{{url('/shop/stocks')}}">Остатки</a></li>
                        <li><a href="{{url('/shop/delivery')}}">Отгрузки</a></li>
                    </ul>
                </li>
                <li><a class="has-arrow ai-icon" href="#" aria-expanded="false">
                        <i class="flaticon-077-menu-1"></i>
                        <span class="nav-text">Товары</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{url('/cards/list/1')}}">Все товары</a></li>
                        <li><a href="{{url('/cards/copyCard')}}">Копирование карточки</a></li>
                        <li><a href="{{url('/cards/copy')}}">Товары конкурентов</a></li>
                        <li><a href="{{url('/cards/catalog')}}">Каталоги поставщиков</a></li>
                        <li><a href="{{url('/cards/delete')}}">Удалить товары</a></li>
                    </ul>
                </li>
                {{--<li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="flaticon-061-puzzle"></i>
                        <span class="nav-text">Charts</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="./chart-flot.html">Flot</a></li>
                        <li><a href="./chart-morris.html">Morris</a></li>
                        <li><a href="./chart-chartjs.html">Chartjs</a></li>
                        <li><a href="./chart-chartist.html">Chartist</a></li>
                        <li><a href="./chart-sparkline.html">Sparkline</a></li>
                        <li><a href="./chart-peity.html">Peity</a></li>
                    </ul>
                </li>
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="flaticon-003-diamond"></i>
                        <span class="nav-text">Bootstrap</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="./ui-accordion.html">Accordion</a></li>
                        <li><a href="./ui-alert.html">Alert</a></li>
                        <li><a href="./ui-badge.html">Badge</a></li>
                        <li><a href="./ui-button.html">Button</a></li>
                        <li><a href="./ui-modal.html">Modal</a></li>
                        <li><a href="./ui-button-group.html">Button Group</a></li>
                        <li><a href="./ui-list-group.html">List Group</a></li>
                        <li><a href="./ui-media-object.html">Media Object</a></li>
                        <li><a href="./ui-card.html">Cards</a></li>
                        <li><a href="./ui-carousel.html">Carousel</a></li>
                        <li><a href="./ui-dropdown.html">Dropdown</a></li>
                        <li><a href="./ui-popover.html">Popover</a></li>
                        <li><a href="./ui-progressbar.html">Progressbar</a></li>
                        <li><a href="./ui-tab.html">Tab</a></li>
                        <li><a href="./ui-typography.html">Typography</a></li>
                        <li><a href="./ui-pagination.html">Pagination</a></li>
                        <li><a href="./ui-grid.html">Grid</a></li>

                    </ul>
                </li>
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="flaticon-053-heart"></i>
                        <span class="nav-text">Plugins</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="./uc-select2.html">Select 2</a></li>
                        <li><a href="./uc-nestable.html">Nestedable</a></li>
                        <li><a href="./uc-noui-slider.html">Noui Slider</a></li>
                        <li><a href="./uc-sweetalert.html">Sweet Alert</a></li>
                        <li><a href="./uc-toastr.html">Toastr</a></li>
                        <li><a href="./map-jqvmap.html">Jqv Map</a></li>
                        <li><a href="./uc-lightgallery.html">Light Gallery</a></li>
                    </ul>
                </li>
                <li><a href="widget-basic.html" class="ai-icon" aria-expanded="false">
                        <i class="flaticon-381-settings-2"></i>
                        <span class="nav-text">Widget</span>
                    </a>
                </li>
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="flaticon-044-file"></i>
                        <span class="nav-text">Forms</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="./form-element.html">Form Elements</a></li>
                        <li><a href="./form-wizard.html">Wizard</a></li>
                        <li><a href="./form-editor-summernote.html">Summernote</a></li>
                        <li><a href="form-pickers.html">Pickers</a></li>
                        <li><a href="form-validation-jquery.html">Jquery Validate</a></li>
                    </ul>
                </li>--}}
                <li><a class="has-arrow ai-icon" href="#" aria-expanded="false">
                        <i class="flaticon-381-network"></i>
                        <span class="nav-text">Утилиты</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{url('/tools/calendar')}}">Календари</a></li>
                    </ul>
                </li>
                <li><a class="has-arrow ai-icon" href="#" aria-expanded="false">
                        <i class="flaticon-073-settings"></i>
                        <span class="nav-text">Настройки</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{url('/settings/sellers')}}">Магазины</a></li>
                        <li><a href="{{url('/settings/suppliers')}}">Поставщики</a></li>
                        <li><a href="{{url('/settings/competitors')}}">Конкуренты</a></li>
                        <li><a href="{{url('/settings/process')}}">Фоновые процессы</a></li>
                    </ul>
                </li>
            </ul>
            <div class="copyright">
                <p><strong>Gidion Seller Portal</strong> © 2024 All Rights Reserved</p>
            </div>
        </div>
    </div>
    <div class="content-body" style="min-height:895px">
        <div class="container-fluid" style="padding: 0 10px 0 10px" id="bodySection">
            @yield('content')
        </div>
    </div>
</div>
<script src="{{ url('/vendor/global/global.min.js') }}"></script>
<script src="{{ url('/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{ url('/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('/js/plugins-init/datatables.init.js') }}"></script>
<script src="{{ url('/vendor/lightgallery/js/lightgallery-all.min.js') }}"></script>
<script src="{{ url('/vendor/jquery-ui/jquery-ui.js') }}"></script>
<script src="{{ url('/js/custom.js') }}"></script>
<script src="{{ url('/js/deznav-init.js') }}"></script>
<script src="{{ url('/js/layout/app.js') }}"></script>
@yield('js')
</body>
</html>
