<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />

    @yield('checkout')

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    {{--<link rel="stylesheet" href="./assets/css/platform-style.css" />--}}
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="/css/steper-getnet.css">
    <link href="/css/toastr/toastr.css" rel="stylesheet"/>
    <title>{{ $platform->name ?? '' }}</title>
    <style>
        body{
            padding: 0;
            margin: 0;
        }

        #div_form_login{
            display: none
        }

        .fill {
            min-height: 100%!important;
            height: 100%;
        }
    </style>
    @include('mundipagg.checkout.facebook-pixel')
    @include('mundipagg.checkout.google-tag-manager')
</head>
<body>
@include('mundipagg.checkout.google-tag-manager-no-script')
@yield('content')

@yield('js-personal-data')
@yield('js-payment')

<script src="https://code.jquery.com/jquery-3.5.1.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
<script src="/js/toastr/toastr.js"></script>

<script type="text/javascript">
    function limpa_formulário_cep() {
        // Limpa valores do formulário de cep.
        $("#address_street").val("");
        $("#address_district").val("");
        $("#address_city").val("");
        $("#address_state").val("");
    }

    function searchAddress() {
        let cep_ = $("#address_zipcode").val();
        const cep = cep_.replace('-', '');

        $("#address").show();
        if (cep == '') {
            $("#address").hide();
        }

        if (cep != "") {
            let validacep = /^[0-9]{8}$/;

            if(validacep.test(cep)) {
                $("#address_street").val("...");
                $("#address_district").val("...");
                $("#address_city").val("...");
                $("#address_state").val("...");

                $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {
                    if (!("erro" in dados)) {
                        $("#address_street").val(dados.logradouro);
                        $("#address_district").val(dados.bairro);
                        $("#address_city").val(dados.localidade);
                        $("#address_state").val(dados.uf);
                        $("#address_number").focus();

                        if( dados.logradouro == "" ) {
                            $("#address_street").removeAttr('readonly');
                        }
                        if( dados.bairro == "" ) {
                            $("#address_district").removeAttr('readonly');
                        }
                    }
                    else {
                        limpa_formulário_cep();
                        toastr["warning"]("CEP não encontrado");

                    }
                });
            } else {
                limpa_formulário_cep();
                toastr["warning"]("Formato de CEP inválido");
                $("#address_zipcode").val('');
            }
        } else {
            limpa_formulário_cep();
        }
    }

    function validarCPF(cpf) {
        cpf = cpf.replace(/[^\d]+/g,'');

        if(cpf == '') return false;
        // Elimina CPFs invalidos conhecidos
        if (cpf.length != 11 ||
            cpf == "00000000000" ||
            cpf == "11111111111" ||
            cpf == "22222222222" ||
            cpf == "33333333333" ||
            cpf == "44444444444" ||
            cpf == "55555555555" ||
            cpf == "66666666666" ||
            cpf == "77777777777" ||
            cpf == "88888888888" ||
            cpf == "99999999999")
            return false;
        // Valida 1o digito
        add = 0;
        for (i=0; i < 9; i ++)
            add += parseInt(cpf.charAt(i)) * (10 - i);
        rev = 11 - (add % 11);
        if (rev == 10 || rev == 11)
            rev = 0;
        if (rev != parseInt(cpf.charAt(9)))
            return false;
        // Valida 2o digito
        add = 0;
        for (i = 0; i < 10; i ++)
            add += parseInt(cpf.charAt(i)) * (11 - i);
        rev = 11 - (add % 11);
        if (rev == 10 || rev == 11)
            rev = 0;
        if (rev != parseInt(cpf.charAt(10)))
            return false;
        return true;
    }

    $(function() {

        var inputs = $('#address_zipcode').keyup(function(e){
            if( $("#radioEstrangeiro").is(':checked') == false ) {
                if (inputs.val().length === 9 ) {
                    searchAddress();

                } else {
                    $("#address_zipcode").blur(function () {
                        searchAddress();

                    });
                }
            }
        });

        $("#address").hide();
        if ($("#address_zipcode").val() != '') {
            $("#address").show();
        }

        $("#cpf_number").blur(function () {
            const cpf = $(this).val();

            if (cpf === '') {
                return false;
            }
            if (!validarCPF(cpf)) {
                toastr["warning"]("Digite um CPF válido por favor!");
                $("#cpf_number").val('');
                $("#cpf_number").focus();
                return false;
            }
        });

        // script for tab steps
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

            var href = $(e.target).attr('href');
            var $curr = $(".process-model  a[href='" + href + "']").parent();

            $('.process-model li').removeClass();

            $curr.addClass("active");
            $curr.prevAll().addClass("visited");
        });
        // end  script for tab steps


        $("#address_zipcode").blur(function() {
            $(this).val().replace(/\D/g, '');
            if ($("#address_zipcode").val() == '' && $("#radioEstrangeiro").is(':checked') == false ) {
                $("#address").hide();
            }
            // searchAddress();
        });

        $("#emailConfirm").blur(function() {
            if ($("#email").val() != $("#emailConfirm").val()) {
                toastr["warning"]("Os e-mails devem ser iguais! \n Redigite por favor!");
                $("#emailConfirm").val('');
                return false;
            }
        });
    });

</script>

</body>

</html>

