{{-- LAYOUT WITH TABLE --}}
<html style="width: 100%">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<body style="width:100%!important;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;margin:0;padding:0;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#2a2e39" style="border-radius:5px;">
        <tbody>
            <tr style="border-top-right-radius:5px;border-top-left-radius:5px;">
                <td style="padding:10px;border-top-right-radius:5px;border-top-left-radius:5px;">
                    <img src="{{ asset('xgrow-vendor/assets/img/logo/dark.svg') }}" alt=""
                        style="width:120px;height:auto;padding:10px" />
                </td>
                <td align="right" style="padding:10px"></td>
            </tr>
            <tr style="background-color:#ffffff;border-bottom-right-radius:10px;border-bottom-left-radius:10px;">
                <td colspan="2" style="border-bottom-right-radius:10px;border-bottom-left-radius:10px;padding:15px;">
                    <p style="padding:0;margin:0">Olá {{ $data['name'] }}!</p>
                    <br />
                    <p style="padding:0;margin:0">Você foi incluído como coprodutor na plataforma
                        {{ $data['platform_name'] }}. Seguem abaixo os dados de acesso:</p>
                    <br />
                    <p style="padding:0;margin:20px 0 0 0;">Email: {{ $data['email'] }}</p>
                    <br />
                    <p style="padding:0;margin:20px 0 0 0;">Senha: {{ $data['password'] }}</p>
                    <br />
                    <a href="{{ url('/login') }}" style="padding:10px 20px;background-color:#93bc1e;border-radius:5px;
                                            color:#ffffff;font-weight:bold;text-decoration:none;">
                        Acessar
                    </a>
                </td>
            </tr>
            <tr style="border-bottom-right-radius:5px;border-bottom-left-radius:5px;">
                <td colspan="2" style="padding:10px;border-bottom-right-radius:5px;border-bottom-left-radius:5px;">
                    <p style="color:#ffffff;text-align:center;padding:0;margin:0">
                        <img src="{{asset('xgrow-vendor/assets/img/logo/symbol.svg')}}" alt="XGrow"
                            style="height:40px;padding:5px"/>
                    </p>
                    {{-- <p style="color:#ffffff;text-align:center;padding:0;margin:0">
                            Precisa de ajuda? <a href="#" style="font-weight:bold;color:#ffffff;">Fale com nosso suporte</a>
                        </p> --}}
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
