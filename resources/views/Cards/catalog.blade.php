@extends('layouts.app')
@section('content')
    <form method="post" action="">
        @csrf
        <div class="col-lg-12 mt-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Каталог карточек поставщиков</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">Поставщик</div>
                        <div class="col-md-4">
                            <select required class="mr-sm-2 mb-3 default-select form-control-sm" name="competitor">
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 text-right">
                            <button type="submit" class="btn btn-primary">Обновить</button>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <table class="table table-sm">
                        <thead>
                        <tr class="table-primary">
                            <th>Sku</th>
                            <th>Нименование</th>
                            <th>Цена (Офисмаг)</th>
                            <th>Цена (WB)</th>
                            <th>Объемный вес</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        @foreach($rows as $row)
                            <tr @if($row['wbPrice'] > 0) class="table-success" @endif>
                                <td>{{ $row['sku'] }}</td>
                                <td>{{ $row['name'] }}</td>
                                <td>{{ $row['omPrice'] }}</td>
                                <td>{{ $row['wbPrice'] }}</td>
                                <td>{{ $row['volume'] }}</td>
                                <td>@if($row['wbPrice'] > 0)
                                        <a href="/card/{{ $row['cardId'] }}" target="_blank"><i
                                                class="fa fa-gear"></i></a>
                                    @else
                                        <a href="/cards/createcard/{{ $row['id'] }}" target="_blank"><i
                                                class="fa fa-gear"></i></a>
                                    @endif
                                    <a href="#" data-id="{{ $row['id'] }}" class="blockcatalogitem"><i
                                            class="fa fa-ban"></i></a>
                                    <a href="#" class="complete"><i class="fa fa-check"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('js')
    <script src="{{ url('/js/catalog/index.js') }}"></script>
@endsection
