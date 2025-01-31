@extends('_auth.AuthLayout')
@section('page_title', 'Update Password')
@section('heading', 'Update Password')
@section('description')

<p> Update password for your {{APPNAME}} account</p>
@endsection

@section('content')
@if (!$success)
<div class="text-center display-5">
    Error:
    {{ $message }}
</div>
@else

<form id="reset" action="{{BASEURL}}/reset" method="POST">
    @csrf

    <input type="hidden" name="selector" value="{{ $selector }}">
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="input-group mb-1">
        <div class="form-floating">
            <input required class="form-control" id="password" type="password" name="password" placeholder="Password" />
            <label for="password"> Password </label>
        </div>
    </div>

    <div class="input-group mb-1">
        <div class="form-floating">
            <input required class="form-control" id="confirm_password" type="password" name="confirm_password" placeholder="Password" />
            <label for="confirm_password"> Confirm Password </label>
        </div>
    </div>


    <div class="w-100 my-2">
        <button class="btn btn-light" type="submit" name="submit" value="Update">Update</button>
    </div>

    <div class="mt-3">
        Want to login instead? <a href="{{BASEURL}}/login"> Login </a>
    </div>
</form>
</form>

<script>
    ajaxifyForm(document.querySelector('#reset'), function() {
        window.location.href = '{{BASEURL}}/login'
    });
</script>
@endif

@endsection