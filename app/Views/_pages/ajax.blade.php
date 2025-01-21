@extends('Layouts.Main')

@section('content')
<div class="min-vh-100">
    <form action="{{BASEURL}}/ajax" method="POST" data-ajaxify data-before="validate">
        <input type="text" name="first_name" id="first_name">
        <button type="submit" name="submit">Submit</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{BASEURL}}/public/nthajax.js"></script>
<script src="{{BASEURL}}/public/refid.js"></script>

<script>
    function validate() {
        if (first_name.value.length < 3) {
            alert('Minimum 3 Chars')
            return false;
        }
    }
    
    first_name.addEventListener('input', function() {
        console.log(first_name.value)
    })
</script>
@endsection