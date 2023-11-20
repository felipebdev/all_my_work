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
                            style="width:120px;height:auto;" />
                    </td>
                    <td align="right" style="padding:10px">
                        <p style="margin:0 10px;padding:0;color:#ffffff;font-weight:bold;text-align:right;">
                            {{ config('app.name') }}
                        </p>
                    </td>
                </tr>
                <tr style="background-color:#ffffff;border-bottom-right-radius:10px;border-bottom-left-radius:10px;">
                    <td colspan="2" style="border-bottom-right-radius:10px;border-bottom-left-radius:10px;padding:15px;">
                        <p style="padding:0;margin:0"><b>Motivo:</b> {{ $emailData['reason'] }}</p>
                        <br/>
                        <p style="padding:0;margin:0"><b>Assunto:</b> {{ $emailData['subject'] }}</p>
                        <br/>
                        <p style="padding:0;margin:0;white-space:pre-warp;">{{ $emailData['message'] }}</p>
                        <br/>
                    </td>
                </tr>
                <tr style="border-bottom-right-radius:5px;border-bottom-left-radius:5px;">
                    <td colspan="2" style="padding:10px;border-bottom-right-radius:5px;border-bottom-left-radius:5px;">
                        <p style="color:#ffffff;text-align:center;padding:0;margin:0">
                            <img src="{{asset('images/xgrow-icon.png')}}" alt="XGrow"/>
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

{{-- LAYOUT WITH DISPLAY FLEX --}}
{{-- <body style="width:100%;display:flex;flex-direction:column;background-color:#2a2e39">
    <header style="width:100%;display:flex;flex-direction:row;align-items:center;justify-content:space-between;
                    padding:10px">
        <img src="{{ asset('xgrow-vendor/assets/img/logo_wide_darkmode.svg') }}" alt=""
            style="width:120px;height:auto;" />
        <p style="margin:0 10px;padding:0;color:#ffffff;font-weight:bold;text-align:center;">
            {{ config('app.name') }}
        </p>
    </header>
    <main style="width:100%;height:100vh;background-color:#ffffff;border-radius:10px;padding:15px;
                    overflow-x:auto;">
        <h1 style="padding:0;margin:0;font-size:30px;width:100%;text-align:center">{{ config('app.name') }}</h1><br/>
        <p style="padding:0;margin:0"><b>Motivo:</b> {{ $emailData['reason'] }}</p><br/>
        <p style="padding:0;margin:0"><b>Assunto:</b> {{ $emailData['subject'] }}</p><br/>
        <p style="padding:0;margin:0;white-space:pre-warp;">{{ $emailData['message'] }}</p><br/>
    </main>
    <footer style="width:100%;min-height:80px;display:flex;flex-direction:row;align-items:center;justify-content:center;
                    padding:10px;">
        <p style="color:#ffffff;text-align:center;padding:0;margin:0">
            Suporte Xgrow
        </p>
    </footer>
</body> --}}
