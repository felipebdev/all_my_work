@extends('templates.xgrow.main')

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
            <li class="breadcrumb-item active mx-2"><span>Suporte</span></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark">
        <div class="xgrow-card-header">
            <h3>Suporte</h3>
        </div>
        <div class="xgrow-square-buttons-container row flex-nowrap nav nav-tabs" id="nav-tab" role="tablist">
            <a id="nav-melhoria-tab" class="xgrow-square-button mx-3 mb-5 active" data-bs-toggle="tab" href="#melhoria-tab"
                role="tab" aria-controls="melhoria-tab" aria-selected="true">
                <i class="fas fa-thumbs-up"></i>
                <p>Sugerir melhoria</p>
            </a>

            <a id="nav-faq-tab" class="xgrow-square-button mx-3" data-bs-toggle="tab" href="#faq-tab"
                role="tab" aria-controls="faq-tab" aria-selected="false">
                <i class="fas fa-question-circle"></i>
                <p>FAQ</p>
                <p>Dúvidas comuns</p>
            </a>

            <a id="nav-ticket-tab" class="xgrow-square-button mx-3" data-bs-toggle="tab" href="#ticket-tab"
                role="tab" aria-controls="ticket-tab" aria-selected="false">
                <i class="fas fa-comments"></i>
                <p>Chamados</p>
            </a>
        </div>

        <div class="tab-content" id="support-content">
            <div class="mt-3 tab-pane fade show active" id="melhoria-tab" role="tabpanel" aria-labelledby="nav-melhoria-tab">
                @include('platforms-users.support._tab-melhoria')
            </div>
            <div class="mt-3 tab-pane fade" id="faq-tab" role="tabpanel" aria-labelledby="nav-faq-tab">
                @include('platforms-users.support._tab-faq')
            </div>
            <div class="mt-3 tab-pane fade" id="ticket-tab" role="tabpanel" aria-labelledby="nav-ticket-tab">
                @include('platforms-users.support._tab-ticket')
            </div>
        </div>
    </div>
@endsection