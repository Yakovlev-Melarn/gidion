@extends('layouts.app')
@section('content')
    <form method="post" action="">
        @csrf
        <div class="col-lg-12 mt-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Создание карточки товара</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"><h5>Карточка</h5></div>
                        <div class="col-md-6"><h5>Информация</h5>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6" style="border-right: 1px solid #473F72">
                            Предмет <input type="text" value="" class="form-control-sm form objectName">
                            <input type="hidden" class="subjectId" value="">
                            <div class="helpObjects"><i>Подсказка: </i></div>
                            <hr>
                            Количество в упаковке <input type="number" value="1" class="form-control-sm form pack">
                            <hr>
                            Описание <textarea rows="5" class="form form-control description">{{ $product->description }}</textarea>
                            <hr>
                            Бренд <input type="text" value="{{ $product->brand }}" class="form-control-sm form brand">
                            <hr>
                            Себестоимость <span class="sPrice">{{ $product->obPrice }}</span><br>
                            Логистика <span class="deliveryCost"></span><br>
                            Комиссия <span class="comission"></span><br>
                            Цена <input type="number" value="" class="form-control-sm form sellPrice"><br>
                            <hr>
                            Весогабаритные характеристики<br>
                            ширина: <input type="number" style="width: 60px"
                                           value="{{ ceil(\App\Http\Libs\Helper::arrSearch(json_decode($product->package_size,1),'type','height')) }}"
                                           class="form-control-sm form height">
                            глубина: <input type="number" style="width: 60px"
                                            value="{{ ceil(\App\Http\Libs\Helper::arrSearch(json_decode($product->package_size,1),'type','depth')) }}"
                                            class="form-control-sm form length">
                            высота: <input type="number" style="width: 60px"
                                           value="{{ ceil(\App\Http\Libs\Helper::arrSearch(json_decode($product->package_size,1),'type','width')) }}"
                                           class="form-control-sm form width">
                            <hr>
                            <div class="cardContent">

                            </div>
                            <div class="buttons">
                                <hr>
                                <button type="button" class="btn btn-info getRules">Заполнить характеристики</button>
                                <button type="button" class="btn btn-primary sendCard">Отправить карточку</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <span class="productSku"><a href="https://office-burg.ru/search/?q={{ $product->sku }}"
                                                        target="_blank" class="skuLink">{{ $product->sku }}</a></span>
                            <span class="productName">{{ $product->name }}</span>
                            <div class="gallery">
                                <div class="row sortable-img">
                                    @foreach($photos as $photo)
                                        <div class="col-4 text-center">
                                            <div class="lgElement">
                                                <div class="text-right pr-3"><a href="#" class="removeImage"
                                                                                style="font-size: 12px">❌</a></div>
                                                <div class="lightgallery">
                                                    <a href="{{ $photo }}"
                                                       data-exthumbimage="{{ $photo }}"
                                                       data-src="{{ $photo }}">
                                                        <img width="150" src="{{ $photo }}"></a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="productDescription">{{ $product->description }}</div>
                            <hr>
                            <div class="productAttributes">
                                <div class="row">
                                    <div class="col-1"><a href="#" data-fieldname="Страна" class="changeRules"
                                                          data-toggle="modal" data-target="#changeRule"><i
                                                class="fa fa-cogs"></i></a></div>
                                    <div class="col-5 fname">Страна</div>
                                    <div class="col-6 fval">{{ $product->manufacturer }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-1"><a href="#" data-fieldname="SKU" class="changeRules"
                                                          data-toggle="modal" data-target="#changeRule"><i
                                                class="fa fa-cogs"></i></a></div>
                                    <div class="col-5 fname">SKU</div>
                                    <div class="col-6 fval">{{ $product->sku }}</div>
                                </div>
                                @foreach($facets as $facet)
                                    <div class="row">
                                        <div class="col-1"><a href="#" data-fieldname="{{ $facet['name'] }}"
                                                              class="changeRules"
                                                              data-toggle="modal" data-target="#changeRule"><i
                                                    class="fa fa-cogs"></i></a>
                                        </div>
                                        <div class="col-5 fname">{{ $facet['name'] }}</div>
                                        <div class="col-6 fval">{{ $facet['value'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="modal fade" id="changeRule" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ruleHead">Правило для характеристики <span
                            class="productFieldName"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Сопоставить аттрибут с полем:
                    <div class="rule"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary saveRule">Сохранить изменения</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ url('/vendor/draggable/draggable.js') }}"></script>
    <script src="{{ url('/vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ url('/js/card/createcard.js') }}"></script>
@endsection
