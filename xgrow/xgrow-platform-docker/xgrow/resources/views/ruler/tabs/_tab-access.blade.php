@push('after-scripts')
@endpush

<div class="tab-pane fade show" id="nav-access" role="tabpanel" aria-labelledby="nav-boleto-tab">

    <div class="flex-wrap-reverse py-2 d-flex justify-content-between card-dark"
         style="background: transparent;box-shadow: none;">
    </div>

    <div class="xgrow-card card-dark p-0">
        <form method="POST" action="{{ route('ruler.save') }}">
            <fieldset disabled>
                {{ csrf_field() }}
                <input type="hidden" id="access-type" name="type" value="access">
                <div class="xgrow-card-body p-3 py-4">
                    <h5 class="align-self-center">Acesso</h5>
                    <div class="mb-3">
                        Defina as configurações do envio de seus e-mails para os alunos que nunca acessaram a área de aprendizado.
                    </div>

                    <div class="container">
                        <div class="row rounded-3" style="background-color: #242832">
                            <div class="col-md-4">Mensagens</div>
                            <div class="col-md-4">Intervalo</div>
                            <div class="col-md-4">Template de e-mail</div>
                        </div>

                        @foreach (range(1, 4) as $i)
                            <input type="hidden" id="access-lines" name="lines[]" value="{{$i}}">
                            <div class="row p-2 my-2 rounded-3" style="background-color: #383D49">
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="access-checkbox-{{$i}}"
                                            value="{{$i}}" name="checkbox[{{$i}}]"
                                            {{ optional($accesses->firstWhere('position', $i))->active == true ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="access-checkbox-{{$i}}"></label>
                                        <label for="access-checkbox-{{$i}}">{{$i}}ª Mensagem</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <select class="xgrow-select" id="access-interval-{{$i}}" name="interval[{{$i}}]">
                                        @foreach (range($i, 31) as $j)
                                            <option value="{{$j}}"
                                                {{ optional($accesses->firstWhere('position', $i))->interval == $j ? 'selected' : '' }}
                                            >{{$j}} {{str_plural('dia', $j)}} após a compra
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="xgrow-select" id="access-email_id-{{$i}}" name="email_id[{{$i}}]">
                                        <option value="-1">Aviso de produto não acessado</option>
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

