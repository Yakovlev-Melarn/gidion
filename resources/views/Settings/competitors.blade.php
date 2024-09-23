@extends('layouts.app')
@section('content')
    <div class="col-lg-12 mt-3">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Конкуренты</h4>
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
                        @foreach($competitors as $competitor)
                            <tr>
                                <td><strong>{{ $competitor->id }}</strong></td>
                                <td>{{ $competitor->name }}</td>
                                <td>{{ $competitor->url }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="#" class="btn btn-primary shadow btn-xs sharp mr-1 deletecompetitor"
                                           data-id="{{ $competitor->id }}"><i class="fa fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="text-right">
                        <a class="btn btn-primary mt-5" href="/settings/addCompetitor">Добавить</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ url('/js/settings/competitors.js') }}"></script>
@endsection
