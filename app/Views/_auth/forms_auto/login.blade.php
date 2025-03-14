@extends('_auth.AuthLayout')
@section('page_title', 'Login')
@section('heading', 'Login')
@section('description')
Login Into Your {{APPNAME}} account.
@endsection

@section('content')
<form id="loginForm" action="@baseurl(login)" method="POST" data-ajaxify data-success="onSuccess">
    @csrf
    <input type="hidden" name="next" value="@baseurl(($_GET['next'] ?? ''))">
    <div class="form-floating mb-1">
        <input required class="form-control" id="email" type="email" name="email" placeholder="Email" />
        <label for="email"> Email </label>
    </div>

    <div class="input-group mb-1">
        <div class="form-floating">
            <input required class="form-control" id="password" type="password" name="password" placeholder="Password" />
            <label for="password"> Password </label>
        </div>
    </div>

    <div class="mb-2 mt-3 form-check">
        <input type="checkbox" class="form-check-input" id="rem" name="remember" />
        <label for="rem" class="form-check-label"> Remember me </label>
    </div>

    <div class="w-100 my-2 mt-3">
        <button class="btn btn-light" type="submit" name="submit" value="Login">Login</button>
    </div>

    <div class="my-3">
        Don't have an account? <a class="" href="{{BASEURL}}/register"> Register </a>
    </div>

    <div class="my-3 text-underline">
        Having Trouble logging in?
        <a class="" href="{{BASEURL}}/recover"> Recover Password </a> |
        <a class="" href="{{BASEURL}}/reconfirm"> Request Reconfirmation </a>
    </div>
</form>

<script>

    function onSuccess(form, json) {
        if (json.success) {
            window.location.href = json.next ?? "@baseurl()";
        }
    }
 
</script>

@endsection