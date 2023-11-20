@push('after-scripts')
@endpush

<div class="tab-pane fade show" id="nav-nolimit" role="tabpanel" aria-labelledby="nav-nolimit-tab">

    <div class="flex-wrap-reverse py-2 d-flex justify-content-between card-dark"
         style="background: transparent;box-shadow: none;">
    </div>

    <div class="xgrow-card card-dark p-0">
        <form method="POST" action="{{ route('ruler.save') }}">
            <fieldset disabled>
                {{ csrf_field() }}
                <input type="hidden" id="nolimit-type" name="type" value="nolimit">
                <div class="xgrow-card-body p-3 py-4">
                    <h5 class="align-self-center">Vendas via Sem Limite</h5>
                    <div class="mb-3">
                        Defina abaixo o intervalo de cobrança das cobranças sem limite não pagas que o aluno irá receber.
                    </div>

                    <div class="container">
                        <div class="row rounded-3" style="background-color: #242832">
                            <div class="col-md-4">Mensagens</div>
                            <div class="col-md-4">Intervalo</div>
                            <div class="col-md-4">Template de e-mail</div>
                        </div>

                        @foreach (range(1, 3) as $i)
                            <input type="hidden" id="nolimit-lines" name="lines[]" value="{{$i}}">
                            <div class="row p-2 my-2 rounded-3" style="background-color: #383D49">
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="nolimit-checkbox-{{$i}}"
                                            value="{{$i}}" name="checkbox[{{$i}}]"
                                            {{ optional($nolimits->firstWhere('position', $i))->active == true ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="nolimit-checkbox-{{$i}}"></label>
                                        <label for="nolimit-checkbox-{{$i}}">{{$i}}ª Mensagem</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <select class="xgrow-select" id="nolimit-interval-{{$i}}" name="interval[{{$i}}]">
                                        @foreach (range($i, 31) as $j)
                                            <option value="{{$j}}"
                                                {{ optional($nolimits->firstWhere('position', $i))->interval == $j ? 'selected' : '' }}
                                            >{{$j}} {{str_plural('dia', $j)}} após a falha
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="xgrow-select" id="nolimit-email_id-{{$i}}" name="email_id[{{$i}}]">
                                        <option value="">Selecione uma opção</option>
                                        @foreach ($emails->whereIn('id', [
                                                    \App\Email::CONSTANT_EMAIL_RECURRENCE_PAYMENT_RETRY_FAILED,
                                                    \App\Email::CONSTANT_EMAIL_RECURRENCE_PAYMENT_FAILED_SUBSCRIPTION_CANCEL
                                                ]) as $email)
                                            <option value="{{$email->id}}"
                                                {{ optional($nolimits->firstWhere('position', $i))->email_id == $email->id ? 'selected' : '' }}
                                            >{{$email->subject}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endforeach

                        <div class="xgrow-card-footer p-3 border-top mt-4">
                            <input class="xgrow-button" type="submit" value="Salvar">
                        </div>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>

