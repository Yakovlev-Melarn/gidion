@extends('layouts.app')
@section('content')
    <form method="post" action="">
        @csrf
        <div class="col-lg-12 mt-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Копирование карточки товара</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"><label for="nmIDField">Артикул существующей карточки товара (WB nmID)</label></div>
                        <div class="col-md-6 input-group-sm">
                            <input type="number" name="nmID" id="nmIDField" required class="form-control w-25">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><label for="prefixField">Префикс</label></div>
                        <div class="col-md-6 input-group-sm">
                            <input type="text" name="prefix" id="prefixField" value="RS-X" class="form-control w-25">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><label for="packField">Упаковка</label></div>
                        <div class="col-md-6 input-group-sm">
                            <input type="number" id="packField" name="pack" value="1" class="form-control w-25">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6"></div>
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-primary">Начать копирование</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('js')
@endsection
