<?php
    $crisp_website = env("CRISP_WEBSITE_ID", null);
    $crisp_secret = env("CRISP_HMAC_SECRET", null);
    $crisp_url = env("CRISP_URL", null);

    if ($crisp_secret !== null && $crisp_website !== null && $crisp_url !== null) {
        $crisp_url .= $crisp_website;
        $crisp_url .= "?email=" . $user->email;
        $crisp_url .= "&hmac=" . hash_hmac('sha256', $user->email, $crisp_secret, false);
    }
?>

<div class="xgrow-card-body">
    <div class="row">
        @if ($crisp_secret !== null && $crisp_website !== null && $crisp_url !== null)
            <iframe
                title="Ticket Center"
                src="{{ $crisp_url }}"
                referrerpolicy="origin"
                sandbox="allow-forms allow-popups allow-scripts allow-same-origin"
                width="100%"
                height="600px"
                frameborder="0">
            </iframe>
        @else
            <h5 class="my-5">Esta plataforma ainda não suporta o sistema de chamados</h5>
        @endif
    </div>
</div>