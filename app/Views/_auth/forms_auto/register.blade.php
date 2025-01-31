@extends('_auth.AuthLayout')
@section('page_title', 'Register')
@section('heading', 'Register')
@section('description')
Create a new {{APPNAME}} account
@endsection

@section('content')
<form id="register" action="{{BASEURL}}/register" method="POST"
    data-ajaxify
    data-before="preValidate"
    data-success="onSuccess">
    @csrf
    <div class="form-floating mb-1">
        <input class="form-control" id="email" required type="email" name="email" placeholder="Email" />
        <label for="email"> Email </label>
    </div>

    <div class="form-floating mb-1">
        <input class="form-control" id="username" required type="text" name="username" placeholder="Username" />
        <label for="username"> Username </label>
    </div>

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


    <div class="w-100 my-3">
        <button class="btn btn-light" type="submit" name="submit" value="Register">Register</button>
    </div>

    <div class="mt-3">
        Already have an account? <a href="{{BASEURL}}/login"> Login </a>
    </div>
</form>

<script>
    function preValidate() {
        if (password.value !== confirm_password.value) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: 'Passwords do not match!.',
            })
            return false;
        }
    }

    function onSuccess(form, json) {
        window.location.href = '{{BASEURL}}/login'
    }
</script>

@endsection