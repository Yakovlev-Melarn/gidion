@extends('layouts.app')
@section('content')
    <div class="col-lg-12 mt-3">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Фоновые процессы</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="pt-3">Синхронизировать карточки товаров</h6>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-primary btn-sm startProcessSyncCards"
                                @if($processSyncCards) disabled @endif>Начать
                        </button>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="pt-3">Очистить устаревшие карточки ({{ $oldCardsCount }})</h6>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-primary btn-sm startProcessDeleteCards"
                                @if(!$oldCardsCount) disabled @endif>Начать
                        </button>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="pt-3">Удалить все скидки</h6>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-primary btn-sm startProcessRemoveDiscount">Начать</button>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="pt-3">Установить наценку на товары</h6>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm pt-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text">% наценки</span>
                            </div>
                            <label for="percent"></label>
                            <input type="number" id="percent" class="form-control percent"
                                   value="{{ $percentageOfMargin }}">
                            <div class="input-group-append">
                                <span class="input-group-text">% наценки = комиссии ВБ</span>
                            </div>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <label for="wbpercent"></label>
                                    <input type="checkbox" id="wbpercent" class="wbpercent mb-1" value="1">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                        <button class="btn btn-primary btn-sm startProcessUpdatePrice"
                                @if($processUpdatePrice) disabled @endif>Начать
                        </button>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="pt-3">Обновить остатки</h6>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-primary btn-sm startProcessUpdateStock"
                                @if($processUpdateStock) disabled @endif>Начать
                        </button>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="pt-3">Перенести в корзину товары без остатка ({{ $emptyStockCount }})</h6>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-primary btn-sm startTrash">Начать</button>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="pt-3">Загрузить фото для товаров без фотографий @if($noProductPhotoCount > 99)
                                > {{$noProductPhotoCount}} @else
                                {{$noProductPhotoCount}} @endif</h6>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-primary btn-sm startUploadPhotos"
                                @if($processUploadPhotos) disabled @endif>Начать
                        </button>
                    </div>
                </div>
                <hr/>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ url('/js/settings/process.js') }}"></script>
@endsection
