@extends('layouts.app')
@section('content')
    @if(!$load)
    <form method="post" action="">
        @csrf
        <div class="col-lg-12 mt-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Копирование карточек конкурентов</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">Конкурент</div>
                        <div class="col-md-6">
                            <select required class="mr-sm-2 mb-3 default-select form-control-sm" name="competitor">
                                @foreach($competitors as $competitor)
                                    <option value="{{ $competitor->url }}">{{ $competitor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">Количество товаров</div>
                        <div class="col-md-6 input-group-sm">
                            <input type="number" name="count" class="form-control w-25">
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
    @else
        <div class="welcome-text text-center">
            <h4>Процесс копирования карточек уже запущен.</h4>
            <div class="sk-three-bounce" style="background-color:transparent; height: 100px">
                <div class="sk-child sk-bounce1"></div>
                <div class="sk-child sk-bounce2"></div>
                <div class="sk-child sk-bounce3"></div>
            </div>
        </div>
    @endif
@endsection
@section('js')
@endsection
