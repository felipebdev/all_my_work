<h1 class="header h1"
    style="margin: 20px 0; color: #E8E8E8; font-family: 'Open Sans','sans-serif'; font-size: 24px; font-weight: 700; font-style: normal; line-height: 100%; text-align: center;">
    {{$title}}
</h1>
<h2 class="header h2"
    style="margin: 20px 0; color: #E8E8E8; font-family: 'Open Sans','sans-serif'; font-style: normal; line-height: 100%; text-align: center; font-size: 14px; font-weight: 600;">
    {{$subtitle}}
</h2>

<div>
    <center>
        Para abrir este e-mail em seu navegador,
        @include('emails.partials.link', [
            'href' => 'https://example.com',
            'text' => 'clique aqui'
        ])
        .
    </center>
</div>
