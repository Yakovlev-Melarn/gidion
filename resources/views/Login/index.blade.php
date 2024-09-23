<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Gidion - Seller Portal </title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('/images/favicon.png') }}">
    <link href="{{ url('/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ url('/css/style.css') }}" rel="stylesheet">
</head>
<body class="vh-100">
<div class="authincation h-100">
    <div class="container h-100">
        <div class="row justify-content-center h-100 align-items-center">
            <div class="col-md-6">
                <div class="authincation-content">
                    <div class="row no-gutters">
                        <div class="col-xl-12">
                            <div class="auth-form">
                                <div class="text-center mb-3">
                                    <h1>Gidion</h1>
                                </div>
                                <h4 class="text-center mb-4">Войдите в ваш аккаунт</h4>
                                <form action="/login/auth" method="post">
                                    @csrf
                                    @if(session('loginStatus') == 'error')
                                        <div class="text-center"><span
                                                class="badge badge-danger">Неверные логин или пароль!</span>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label class="mb-1"><strong>Логин</strong></label>
                                        <input type="text" name="login" class="form-control" value="">
                                    </div>
                                    <div class="form-group">
                                        <label class="mb-1"><strong>Пароль</strong></label>
                                        <input type="password" name="password" class="form-control" value="">
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary btn-block">Войти</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ url('/vendor/global/global.min.js') }}"></script>
<script src="{{ url('/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{ url('/js/custom.min.js') }}"></script>
<script src="{{ url('/js/deznav-init.js') }}"></script>
</body>
</html>
