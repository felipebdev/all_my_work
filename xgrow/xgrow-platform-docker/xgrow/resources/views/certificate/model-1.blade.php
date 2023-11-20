<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Certificado padrão</title>
        <style type="text/css">

           @font-face {
              font-family: 'Photograph Signature';
              src: url("{{ storage_path('fonts/photograph-signature.ttf') }}") format("truetype");
              font-weight: 400;
              font-style: normal;
          }

            body, html{
                top: 0;
                left: 0;
                margin: 0px;
                padding: 0px;
                font-size: 30px;
            }
            #watermark {
                position: fixed; bottom: 0px; right: 0px; width: 100%; height: 800px; top: 0px;
                z-index: 1;
            }
            #watermark > img{
                width: 100%;
                height: 100%;
            }

            #logo{
                position: absolute;
                top: 85px;
                left: 80px;
                z-index: 2;
            }

            #logo > img{
                max-width: 240px;
            }

            #subscriber_name{
                position: absolute;
                z-index: 2;
                width: 720px;
                top: 400px;
                left: 200px;
                text-align: center;
                border-bottom: solid 3px #1854bc;
            }

            #title{
               position: absolute;
               top: 330px;
               left: 373px;
               width: 385px;
               z-index: 2;
               text-align: center;
            }

            #details{
               position: absolute;
               top: 480px;
               left: 160px;
               width: 820px;
               z-index: 2;
            }

            #sign{
               position: absolute;
               top: 580px;
               left: 630px;
               width: 320px;
               z-index: 2;
               text-align: center;
               margin: 0px;
               padding: 0px;
            }

            hr{
                height: 1px;
                color: black;
                background-color: black;
                padding: 0px;
                margin: 0px;
            }

            .sign_author{
              font-family: "Photograph Signature";
              font-size: 40px;
            }



        </style>
    </head>
    <body>
        <div id="watermark">
           <img src="{{ public_path('images/certificate-model-1.png') }}">
        </div>
        <div id="logo">
            <img src="{{ $certificate->logo }}" >
        </div>
        <div id="title">
            Certificamos que
        </div>
        <div id="subscriber_name">
            {{ $subscriber->name }}
        </div>
        <div id="details">
            @if($subscriber->document_number)
              Inscrito(a) no {{ $subscriber->document_type }} sob o nº <u>{{ $subscriber->document_number }}</u>, concluiu
            @else
              Concluiu
            @endif
              com sucesso o curso de <u>{{ $course->name }}</u>, ministrado por <u>{{ $course->author->name_author }}</u> concluído em <u>{{ dateBr($certificate->certificated_at) }}</u>, com carga horária de <u>{{ $course->total_hours }}</u> horas.
        </div>
        <div id="sign">
            <span class="sign_author">{{ $course->author->name_author }}</span><hr />
            Instrutor
        </div>
    </body>
</html>
