@extends('layouts.app')
@section('content')
    <div id="addSeller" class="m-5">
        <h3 class="text-center">Добавить поставщика</h3>
        <form method="post" action="">
            @csrf
            <div class="form-group row">
                <label class="col-sm-2 col-form-label col-form-label-lg">Наименование:</label>
                <div class="col-sm-10">
                    <input required type="text" name="name" class="form-control form-control-lg"
                           placeholder="ИП Иванов Иван Иванович">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label col-form-label-lg">ID:</label>
                <div class="col-sm-10">
                    <input required type="text" name="supplierId" class="form-control form-control-lg">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label col-form-label-lg">URL:</label>
                <div class="col-sm-10">
                    <input type="text" name="url" class="form-control form-control-lg">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label col-form-label-lg">Префикс:</label>
                <div class="col-sm-10">
                    <input type="text" name="prefix" class="form-control form-control-lg">
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
