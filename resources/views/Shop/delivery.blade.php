@extends('layouts.app');
@section('content')
    <input type="hidden" name="seller" id="sellerId" value="{{session()->get('sellerId')}}">
    <div class="container-fluid">
        <div class="row page-titles mx-0" style="margin-bottom: 0;padding: 0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Отгрузки</h4>
                    <p class="mb-0"></p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0">

            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <input type="text" class="scanner form-control">
            <button type="button" disabled class="btn btn-primary btn-sm addDelivery">+ Создать поставку</button>
        </div>
        <div class="card-body">
            <div class="issetDelivery"></div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ url('/js/shop/delivery.js') }}"></script>
@endsection
