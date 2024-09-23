@extends('layouts.app')
@section('content')
    <div class="col-lg-12 mt-3">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Поставщики</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md">
                        <thead>
                        <tr>
                            <th><strong>ID</strong></th>
                            <th><strong>Наименование</strong></th>
                            <th><strong>Url</strong></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($suppliers as $supplier)
                            <tr>
                                <td><strong>{{ $supplier->supplierId }}</strong></td>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->url }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="#" class="btn btn-primary shadow btn-xs sharp mr-1 deletesupplier"
                                           data-id="{{ $supplier->id }}"><i class="fa fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="text-right">
                        <a class="btn btn-primary mt-5" href="/settings/addSupplier">Добавить</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ url('/js/settings/suppliers.js') }}"></script>
@endsection
