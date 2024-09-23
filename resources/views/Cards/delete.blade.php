@extends('layouts.app')
@section('content')
    <form method="post" action="">
        @csrf
        <div class="col-lg-12 mt-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Удаление карточек</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="pt-3">Артикул WB каждый с новой строки</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <textarea rows="25" name="nmIds" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-danger">Отправить в корзину</button>
                        </div>
                    </div>
                    <hr/>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('js')
@endsection
