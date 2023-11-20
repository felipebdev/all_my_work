@extends('templates.monster.main')

@section('jquery')
    <script>
        $(document).ready(function (){
            $("input[name=type_plan]").change(function() {
                if ($('input[name=type_plan]:checked').val() === "P") {
                    $("#installment").show();
                    $("#installment").attr('required', true);
                    $("#div_installment").removeClass('d-none');
                    $("#installment").focus();

                } else {
                    $("#installment").val('');
                    $("#installment").hide();
                    $("#installment").removeAttr('required');
                    $("#div_installment").addClass('d-none');
                }
            });
        });
    </script>
@endsection

@push('before-scripts')
<script src="{{ mix('/js/home-one.js') }}"></script>
<script>
    function copyUrlCheckout() {
        var textCopy = document.getElementById("url_checkout");
        textCopy.select();
        document.execCommand("Copy");
        toastr["success"]("Link copiado!");
    }
</script>
@endpush

@section('content')

<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Planos</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="/plans">Planos</a></li>
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <h4 class="card-title">Editar plataforma</h4>

        <form class="mt-4" method="POST" action="{{ url("/plans/{$plan->id}") }}">
            @include('plans.form')
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            <button type="submit" class="btn btn-success">Alterar</button>
        </form>

    </div>
</div>

@endsection
