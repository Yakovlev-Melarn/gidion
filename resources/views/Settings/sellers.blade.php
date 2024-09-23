@extends('layouts.app')
@section('content')
    <div class="col-lg-12 mt-3">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Магазины</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md">
                        <thead>
                        <tr>
                            <th><strong>ID</strong></th>
                            <th><strong>Наименование</strong></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($sellers as $seller)
                            <tr>
                                <td><strong>{{ $seller->id }}</strong></td>
                                <td>{{ $seller->name }}</td>
                                <td class="hf edit-id-{{ $seller->id }}">
                                    <div class="mb-3 input-primary-o">
                                        <div class="input-group-prepend">
                                            <input type="text" name="apiKey" value=""
                                                   placeholder="WB API ключ"
                                                   class="form-control apiKey">
                                        </div>
                                        <div class="input-group-append">
                                            <input type="text" name="whID" value="{{ $seller->whID }}"
                                                   placeholder="ID склада продавца"
                                                   class="form-control whID">
                                            <span class="input-group-text">
                                                <a href="#"
                                                   class="submitsellerapikey"
                                                   data-id="{{ $seller->id }}">Сохранить</a>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <a href="#" class="btn btn-primary shadow btn-xs sharp mr-1 editsellerapikey"
                                           data-id="{{ $seller->id }}"><i class="fa fa-pencil"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="text-right">
                        <a class="btn btn-primary mt-5" href="/settings/addSeller">Добавить</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ url('/js/settings/sellers.js') }}"></script>
@endsection
