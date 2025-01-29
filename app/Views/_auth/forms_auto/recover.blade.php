@extends('Layouts.Auth')
@section('page_title', 'Revover Password')
@section('heading', 'Revover Password')
@section('description')
Recover or Change Password for your {{APPNAME}} account
@endsection

@section('content')

<form id="recover" action="{{BASEURL}}/recover" method="POST">
    @csrf
    <div class="form-floating mb-1">
        <input class="form-control" id="email" required type="email" name="email" placeholder="Email" />
        <label for="email"> Email </label>
    </div>

    <div class="w-100 my-2">
        <button class="btn btn-light" type="submit" name="submit" value="Recover">Recover</button>
    </div>

    <div class="mt-3">
        Want to login instead? <a href="{{BASEURL}}/login"> Login </a>
    </div>
</form>
</form>

<script>
    ajaxifyForm(document.querySelector('#recover'), function() {
        window.location.href = '{{BASEURL}}/login'
    });
</script>

@endsection