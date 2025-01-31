@extends('_auth.AuthLayout')
@section('page_title', 'Request Reconfirmation Email')

@section('heading', 'Resend Account Confirmation')
@section('description')
Request to resend activation email for your {{APPNAME}} account.
@endsection


@section('content')

<form id="reconfirm" action="{{BASEURL}}/reconfirm" method="POST">
    @csrf
    <div class="input-group mb-1">
        <div class="form-floating">
            <input required class="form-control" id="email" type="email" name="email" placeholder="email" />
            <label for="email"> Email </label>
        </div>
    </div>


    <div class="w-100 my-2">
        <button class="btn btn-light" type="submit" name="submit" value="Update">Submit Request</button>
    </div>

    <div class="mt-3">
        Want to login instead? <a href="{{BASEURL}}/login"> Login </a>
    </div>
</form>
</form>

<script>
    ajaxifyForm(document.querySelector('#reconfirm'), function() {
        window.location.href = '{{BASEURL}}/login'
    });
</script>


@endsection