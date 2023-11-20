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
                 style="width:120px;height:auto;"/>
        </td>
        <td align="right" style="padding:10px">
            <p style="margin:0 10px;padding:0;color:#ffffff;font-weight:bold;text-align:right;">
                {{ config('app.name') }}
            </p>
        </td>
    </tr>
    <tr style="background-color:#ffffff;border-bottom-right-radius:10px;border-bottom-left-radius:10px;">
        <td colspan="2" style="border-bottom-right-radius:10px;border-bottom-left-radius:10px;padding:15px;">
            <p style="padding:0;margin:0">Olá, {{ $name }} tudo bem?</p>
            <br/>
            <p style="padding:0;margin:0">Verificamos que você ainda não acessou seu produto {{$plan}}.</p>
            <br/>
            <p style="padding:0;margin:0">Você teve algum problema para acessar?</p>
            <br/>
            <div style="display: flex">
                <a href="{{ $url }}?email={{$email}}&rp=true" style="padding:10px 20px;background-color:#93bc1e;border-radius:5px;
                                            color:#ffffff;font-weight:bold;text-decoration:none;margin-right: 1rem;">
                    Sim
                </a>
                <a href="{{ $url }}?email={{$email}}&rp=false" style="padding:10px 20px;background-color:#adadad;border-radius:5px;
                                            color:#ffffff;font-weight:bold;text-decoration:none;">
                    Não
                </a>
            </div>
            <br/>
        </td>
    </tr>
    <tr style="border-bottom-right-radius:5px;border-bottom-left-radius:5px;">
        <td colspan="2" style="padding:10px;border-bottom-right-radius:5px;border-bottom-left-radius:5px;">
            <p style="color:#ffffff;text-align:center;padding:0;margin:0">
                Suporte XGROW
            </p>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>
