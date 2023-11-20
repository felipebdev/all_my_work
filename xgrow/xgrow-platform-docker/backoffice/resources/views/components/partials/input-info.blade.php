<div class="info-container client-info">

    @if(@isset($info))
        <ul class="validation-rules-list">
            @foreach($info as $message)

                <li class="info-message client-info-message">
                    <i class="fas fa-info"></i> {{ vsprintf($message['message'], $message['params']) }}
                </li>

            @endforeach
        </ul>
    @endif

</div>

<div class="info-container server-info">

    @if(@isset($serverInfo))
        <ul class="validation-rules-list">
            @foreach($serverInfo as $message)

                <li class="info-message server-info-message">
                    <i class="fas fa-info"></i> {{ vsprintf($message['message'], $message['params']) }}
                </li>

            @endforeach
        </ul>
    @endif

</div>
