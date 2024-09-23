@extends('layouts.app')
@section('content')
    <div id="addSeller" class="m-5">
        <h3 class="text-center">Добавить новый магазин</h3>
        <form method="post" action="">
            @csrf
            <div class="form-group row">
                <label class="col-sm-2 col-form-label col-form-label-lg">Юридическое лицо/ИП:</label>
                <div class="col-sm-10">
                    <input required type="text" name="name" class="form-control form-control-lg"
                           placeholder="ИП Иванов Иван Иванович">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label col-form-label-lg">ID склада продавца:</label>
                <div class="col-sm-10">
                    <input required type="text" name="whID" class="form-control form-control-lg" placeholder="12345">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label col-form-label-lg">WB токен:</label>
                <div class="col-sm-10">
                    <input required type="text" name="token" class="form-control form-control-lg" placeholder="*******">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-10"></div>
                <div class="col-sm-2 text-right">
                    <button type="submit" class="btn btn-primary">Добавить</button>
                </div>
            </div>
        </form>
    </div>
@endsection
