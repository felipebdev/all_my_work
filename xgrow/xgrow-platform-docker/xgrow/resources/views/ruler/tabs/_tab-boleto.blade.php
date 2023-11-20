@push('after-scripts')
@endpush

<div class="tab-pane fade show active" id="nav-boleto" role="tabpanel" aria-labelledby="nav-boleto-tab">

    <div class="flex-wrap-reverse py-2 d-flex justify-content-between card-dark"
         style="background: transparent;box-shadow: none;">
    </div>

    <div class="xgrow-card card-dark p-0">
        <form method="POST" action="{{ route('ruler.save') }}">
            <fieldset disabled>
                {{ csrf_field() }}
                <input type="hidden" id="boleto-type" name="type" value="boleto">
                <div class="xgrow-card-body p-3 py-4">
                    <h5 class="align-self-center">Vendas via boleto</h5>
                    <div class="mb-3">
                        Defina o intervalo de tempo para envio de cobrança de boletos. O aluno receberá o e-mail com o
                        boleto conforme os intervalos ativados abaixo. O não pagamento irá cancelar automaticamente o
                        acesso.
                    </div>

                    <div class="container">
                        <div class="row rounded-3" style="background-color: #242832">
                            <div class="col-md-4">Mensagens</div>
                            <div class="col-md-4">Intervalo</div>
                            <div class="col-md-4">Template de e-mail</div>
                        </div>

                        @foreach (range(1, 3) as $i)
                            <input type="hidden" id="boleto-lines" name="lines[]" value="{{$i}}">
                            <div class="row p-2 my-2 rounded-3" style="background-color: #383D49">
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="boleto-checkbox-{{$i}}"
                                            value="{{$i}}" name="checkbox[{{$i}}]"
                                            {{ optional($boletos->firstWhere('position', $i))->active == true ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="boleto-checkbox-{{$i}}"></label>
                                        <label for="boleto-checkbox-{{$i}}">{{$i}}ª Mensagem</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <select class="xgrow-select" id="boleto-interval-{{$i}}" name="interval[{{$i}}]">
                                        @foreach (range($i, 3) as $j)
                                            <option value="{{$j}}"
                                                {{ optional($boletos->firstWhere('position', $i))->interval == $j ? 'selected' : '' }}
                                            >{{$j}} {{str_plural('dia', $j)}} após a compra
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="xgrow-select" id="boleto-email_id-{{$i}}" name="email_id[{{$i}}]">
                                        <option value="">Selecione uma opção</option>
                                        @foreach ($emails->whereIn('id', [
                                            \App\Email::CONSTANT_EMAIL_BOLETO,
                                            \App\Email::CONSTANT_EMAIL_BANK_SLIP_EXPIRATION
                                        ]) as $email)
                                            <option value="{{$email->id}}"
                                                {{ optional($boletos->firstWhere('position', $i))->email_id == $email->id ? 'selected' : '' }}
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

