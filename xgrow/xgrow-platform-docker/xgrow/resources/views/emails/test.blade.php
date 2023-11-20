@component('mail::message')

    {!! $emailData['message'] !!}

    Obrigado,
    {{ config('app.name') }}
@endcomponent
