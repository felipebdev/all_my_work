@push('after-styles')
    <style>
        .term-link {
            color: #92bc1d;
        }

        .term-link:hover {
            color: #ffffff;
        }

    </style>
@endpush

{!! Form::open(['route' => 'accept.platform.terms', 'method' => 'POST']) !!}
{{ csrf_field() }}
<div class="xgrow-card card-dark p-0 mt-4 col-lg-6 offset-lg-3">
    <div class="xgrow-card-body p-3">
        <h5 class="xgrow-card-title mt-3" style="font-size: 1.5rem; line-height: inherit">
            Termos de Uso
        </h5>
        <small>É necessário você ler e aceitar os termos de uso para prossegir.</small>
        <div class="row mt-3">
            <div class="col-lg-12 col-md-12 col-sm-12 my-1">
                <div class="xgrow-check">
                    <input type="checkbox" name="politicsAD" id="politicsAD" required>
                    <label for="politicsAD" style="display: initial">
                        Eu aceito a
                        <a href="{{ url('docs/platform/policy-for-meeting-the-rights-of-xgrow-holders.pdf') }}"
                            class="term-link" target="_blank">
                            Política de atendimento aos direitos dos titulares XGROW
                        </a>.
                    </label>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 my-1">
                <div class="xgrow-check">
                    <input type="checkbox" name="politicsCX" id="politicsCX" required>
                    <label for="politicsCX" style="display: initial">
                        Eu aceito a <a href="{{ url('docs/platform/xgrow-cookie-policy.pdf') }}" class="term-link"
                            target="_blank">
                            Política de cookies XGROW
                        </a>.
                    </label>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 my-1">
                <div class="xgrow-check">
                    <input type="checkbox" name="politicsPU" id="politicsPU" required>
                    <label for="politicsPU" style="display: initial">
                        Eu aceito a <a href="{{ url('docs/platform/xgrow-user-privacy-policy.pdf') }}"
                            class="term-link" target="_blank">
                            Política de privacidade do usuário XGROW
                        </a>.
                    </label>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 my-1">
                <div class="xgrow-check">
                    <input type="checkbox" name="termCPR" id="termCPR" required>
                    <label for="termCPR" style="display: initial">
                        Eu aceito o <a
                            href="{{ url('docs/platform/term-of-consent-of-parents-or-guardians-of-the-minor-user-xgrow.pdf') }}"
                            class="term-link" target="_blank">
                            Termo de consentimento dos pais ou responsável pelo usuário menor XGROW
                        </a>.
                    </label>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 my-1">
                <div class="xgrow-check">
                    <input type="checkbox" name="termUU" id="termUU" required>
                    <label for="termUU" style="display: initial">
                        Eu aceito os <a href="{{ url('docs/platform/xgrow-user-terms-of-use.pdf') }}"
                            class="term-link" target="_blank">
                            Termos de uso do usuário XGROW
                        </a>.
                    </label>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 my-1">
                <div class="xgrow-check">
                    <input type="checkbox" name="politicsPP" id="politicsPP" required>
                    <label for="politicsPP" style="display: initial">
                        Eu aceito a <a href="{{ url('docs/platform/xgrow-producer-privacy-policy.pdf') }}"
                            class="term-link" target="_blank">
                            Política privacidade do produtor XGROW
                        </a>.
                    </label>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 my-1">
                <div class="xgrow-check">
                    <input type="checkbox" name="termUP" id="termUP" required>
                    <label for="termUP" style="display: initial">
                        Eu aceito os <a href="{{ url('docs/platform/xgrow-producer-terms-of-use.pdf') }}"
                            class="term-link" target="_blank">
                            Termos de uso do produtor XGROW
                        </a>.
                    </label>
                </div>
            </div>
            {{-- <div class="col-lg-12 col-md-12 col-sm-12 my-1">
                <div class="xgrow-check">
                    <input type="checkbox" name="termCG" id="termCG" required>
                    <label for="termCG" style="display: initial">
                        Eu aceito os <a href="{{ url('docs/platform/xgrow-terms-conditions.pdf') }}"
                            class="term-link" target="_blank">Termos e Condições Gerais de Uso da Plataforma e
                            Seus Serviços - XGROW</a>.
                    </label>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 my-1">
                <div class="xgrow-check">
                    <input type="checkbox" name="termCP" id="termCP" required>
                    <label for="termCP" style="display: initial">
                        Eu aceito os <a href="{{ url('docs/platform/xgrow-terms-buy.pdf') }}" class="term-link"
                            target="_blank">Termos de compra</a>.
                    </label>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 my-1">
                <div class="xgrow-check">
                    <input type="checkbox" name="termUS" id="termUS" required>
                    <label for="termUS" style="display: initial">
                        Eu aceito os <a href="{{ url('docs/platform/xgrow-terms-use.pdf') }}" class="term-link"
                            target="_blank">Termos de Uso</a>.
                    </label>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 my-1">
                <div class="xgrow-check">
                    <input type="checkbox" name="termUR" id="termUR" required>
                    <label for="termUR" style="display: initial">
                        Eu aceito os <a href="{{ url('docs/platform/xgrow-terms-responsibilities.pdf') }}"
                            class="term-link" target="_blank">Termos de Uso Responsável</a>.
                    </label>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 my-1">
                <div class="xgrow-check">
                    <input type="checkbox" name="termPP" id="termPP" required>
                    <label for="termPP" style="display: initial">
                        Eu aceito a <a href="{{ url('docs/platform/xgrow-terms-privacy.pdf') }}"
                            class="term-link" target="_blank">Política de Privacidade de Dados: LGPD</a>.
                    </label>
                </div>
            </div> --}}
        </div>
    </div>
    <div class="xgrow-card-footer p-3 border-top mt-4 justify-content-center">
        <input class="xgrow-button" type="submit" value="Aceitar">
    </div>
</div>
{!! Form::close() !!}
