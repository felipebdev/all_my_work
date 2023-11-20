@php
    use Carbon\Carbon;
    use App\Subscription;
@endphp

@push('after-styles')
@endpush

@push('after-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
    <script src=" https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <!--
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    -->
    <script>
        $(function () {
            $('.btn-modal-subscription-cancel').on('click', function () {
                const subscription = $(this).data('id');
                const payment_plan_id = $(this).data('payment_plan_id');
                const plan = $(this).data('plan');
                const subscriber = $(this).data('subscriber');
                $('#modal-subscription-cancel form').attr('action', `/subscriptions/${subscription}/status`);
                $('#modal-subscription-cancel .spn-cancel-product').text(plan);
                $('#modal-subscription-cancel .spn-cancel-subscriber').text(subscriber);
                $('#modal-subscription-cancel').modal('show', true);
            });

            $('.btn-modal-subscription-cancel-sub').on('click', function () {
                const subscription = $(this).data('id');
                const plan = $(this).data('plan');
                const subscriber = $(this).data('subscriber');
                const lastPaymentDate = $(this).data('last-payment');
                $('#modal-subscription-cancel-sub form').attr('action', `/subscriptions/${subscription}/status`);
                $('#modal-subscription-cancel-sub .spn-cancel-product').text(plan);
                $('#modal-subscription-cancel-sub .spn-cancel-subscriber').text(subscriber);
                $('#modal-subscription-cancel-sub input[type=text]').val(lastPaymentDate);
                $('#modal-subscription-cancel-sub').modal('show', true);
            });
        });

        let newProductsAssignments = [];
        let planOptions = '';

        async function start() {
            let plansRequest = await axios.get('/api/plans/get-all');
            const plans = plansRequest.data.plans;
            let xController = 0;

            let assignedPlansNodeList = document.querySelectorAll('#payment-table > tbody > tr > td:nth-child(2)');
            let assignedPlans = [];

            assignedPlansNodeList.forEach(node => assignedPlans.push(node.innerText.trim()));


            plans.forEach((plan) => {
                const isAssigned = assignedPlans.indexOf(plan.name) > -1;

                newProductsAssignments.push({
                    id: plan.id,
                    name: plan.name,
                    isAssigned
                });

            });

            updateOptionsList();

            $('#btnAddPlan').click(function () {
                    if (document.body.contains(document.getElementById('notPlanRegistered'))) {
                        const tr = document.getElementById('notPlanRegistered');
                        tr.classList.add('d-none');
                    }
                    const html =
                        `<tr id="plan-${xController}">
                            <td>
                            </td>
                            <td>
                                <div class="xgrow-form-control mui-textfield mui-textfield--float-label mb-0">
                                    <select class="xgrow-select plan_field" onChange="assignOption(${xController})">
                                        ${planOptions}
                                    </select>
                                    <label>Produto</label>
                                </div>
                            </td>
                            <td style="vertical-align: middle;">
                                Ativo
                            </td>
                            <td style="vertical-align: middle;">
                                ${moment().format('DD/MM/YYYY')}
                            </td>
                            <td>
                            </td>
                            <td style="vertical-align: middle;">
                                <button class="text-white xgrow-button table-action-button" style="background-color: #dc3545" onclick="deletePlanRow(${xController})" type="button">
                                    <i class="fa fa-minus" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>`;

                    const table = $('#payment-table > tbody');
                    table.append(html);
                    assignOption(xController);
                    xController++;
                }
            );
        }

        $('#sendPlans').click(function () {
            const plans = document.querySelectorAll('.plan_field');

            if (plans.length === 0) {
                errorToast('Algum erro aconteceu!', 'Para salvar a alteração, adicione ao menos 1 produto ao aluno.');
                return false;
            }

            const plansArray = [];
            for (let i = 0; i < plans.length; i++) {
                plansArray.push(plans[i].value);
            }


            axios.post('/api/subscriptions/add', {
                plans: plansArray,
                subscriber_id: document.getElementById('subscriberInput').value,
            }).then(async res => {
                if (res.status === 200) {
                    successToast('Produto(s) adicionado(s)!', res.data.data.toString());
                    await sleep(2000);
                    location = self.location.href.split('?')[0] + '?tab=history'
                }
            }).catch(error => {
                errorToast('Algum erro aconteceu!', 'Não foi possível cadastrar os produtos para o aluno solicitado.');
            });

        });

        function deletePlanRow(data) {
            const tr = document.getElementById('plan-' + data);
            unassignOption(data);
            tr.remove();
        }

        function sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        function assignOption(controller) {
            const planField = document.querySelector(`#plan-${controller} .plan_field`);
            const optionSelected = parseInt(planField.value);

            newProductsAssignments.forEach((plan) => {
                if (plan.id === optionSelected) {
                    plan.isAssigned = true;
                }
            });

            updateOptionsList();
        }

        function unassignOption(controller) {
            const planField = document.querySelector(`#plan-${controller} .plan_field`);
            const optionSelected = parseInt(planField.value);

            newProductsAssignments.forEach((plan) => {
                if (plan.id === optionSelected) {
                    plan.isAssigned = false;
                }
            });

            updateOptionsList();
        }

        function updateOptionsList() {
            let newOptionsList = '';
            newProductsAssignments.forEach((plan) => {
                if (!plan.isAssigned) {
                    newOptionsList += `<option value="${plan.id}">${plan.name}</option>`;
                }
            });

            planOptions = newOptionsList;
        }

        start();
    </script>
@endpush

<input type="hidden" value="{{$subscriber->id}}" id="subscriberInput" readonly>
<div class="table-responsive" style="min-height: 512px">
    <table id="payment-table"
        class="mt-3 xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
        style="width:100%">
        <thead>
        <tr class="card-black" style="border: 4px solid var(--black-card-color)">
            <th>Pedido</th>
            <th>Produto</th>
            <th>Status</th>
            <th>Data de cadastro</th>
            <th>Data de cancelamento</th>
            <th class="no-export"></th>
        </tr>
        </thead>
        <tbody>
        @forelse ($subscriptions as $item)
            <tr>
                <td>{{ $item->order_number ?? '-' }}</td>
                <td>
                        <a href="/products/{{ $item->plan->product_id ?? 'javascript:void(0)' }}/plans"
                        style="color: inherit">
                            {{ $item->plan->name ?? '-' }}
                        </a><br>
                </td>
                <td id="status-{{$item->id}}">
                    @if( strlen($item->status) > 0 )
                        {{Subscription::listStatus()[$item->status]}}
                    @else
                        Status não encontrado
                    @endif

                    @if ($item->plan->deleted_at)
                        (produto excluído)
                    @endif

                    @if ($item->status == 'canceled' && $item->cancellation_reason)
                        <span class="d-inline-block" tabindex="0" data-toggle="tooltip" data-placement="top" html="true" title="{{$item->cancellation_reason}}">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    @endif
                    <?php
                    /*
                    @if (!empty($item->transaction))
                        @if (!empty($item->canceled_at))
                            Cancelado
                        @elseif ($item->transaction->type === 'P' || $item->transaction->type === 'R')
                            @if ($item->payments->count() > 0)
                                {{ getSubscriptionStatusByPayment($item->payments->last()->status) ?? '-' }}
                            @else
                                {{ getSubscriptionStatusByPayment($item->transaction->status) ?? '-' }}
                            @endif
                        @elseif ($item->transaction->type === 'U')
                            @if ($item->payments->count() > 0)
                                @php $last = $item->payments->filter(function ($item) { return $item->payment_date <= date('Y-m-d'); })->last(); @endphp
                                {{ getSubscriptionStatusByPayment($last->status ?? '') ?? '-' }}
                            @else
                                {{ getSubscriptionStatusByPayment($item->transaction->status) ?? '-' }}
                            @endif
                        @else
                            '-'
                        @endif
                    @endif
                    */
                    ?>
                </td>
                <td>{{ dateBr($item->created_at) }}</td>
                <td>
                    <span id="cancelDate{{ $item->id }}">
                        @if (!empty($item->canceled_at))
                            {{ dateBr($item->canceled_at) }}
                        @elseif (
                            !empty($item->transaction) &&
                            $item->transaction->status === 'canceled'
                        )
                            {{ dateBr($item->transaction->updated_at) }}
                        @else
                            -
                        @endif
                    </span>
                </td>
                <td>
                    <div class="dropdown">
                        <button class="xgrow-button table-action-button m-1" type="button"
                                id="dropdownMenuButton_subscripion_{{ $item->id }}"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu table-menu"
                            aria-labelledby="dropdownMenuButton_subscripion_{{ $item->id }}">
                            <li>
                                <a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                data-bs-toggle="modal" data-bs-target="#modal-change-status" id="btnStatus-{{ $item->id }}"
                                data-bs-id="{{ $item->id }}" data-bs-status="{{ $item->status }}">
                                    Alterar Status
                                </a>
                            </li>
                            @php
                            if(!empty($item->transaction)){
                                $paymentPlan = \App\PaymentPlan::getPaymentPlantData($item->transaction->id, $item->plan->id);
                            }
                            @endphp
                            @if (
                                !empty($item->transaction) &&
                                empty($item->canceled_at) &&
                                ($item->transaction->type === 'P' || $item->transaction->type === 'U') &&
                                $paymentPlan->status === 'paid'
                            )
                                <a class="dropdown-item table-menu-item btn-modal-subscription-cancel"
                                href="javascript:void(0)"
                                data-id="{{ $item->id }}"
                                data-payment_plan_id="{{ $paymentPlan->id }}"
                                data-subscriber="{{ $item->subscriber->name }}"
                                data-plan="{{ $item->plan->name }}">
                                    Cancelar
                                </a>
                                @if (!empty($item->order_number))
                                    <a class="dropdown-item table-menu-item btn-modal-subscription-cancel-refund"
                                        href="javascript:void(0)"
                                        onclick="callModalCancelAndRefund(
                                            '{{$item->transaction->type_payment}}',
                                            '{{$paymentPlan->id}}',
                                            '{{$item->plan->name}}',
                                            '{{$item->subscriber->name}}'
                                        )">
                                        Cancelar e estornar
                                    </a>
                                @endif
                            @endif

                            @if (!empty($item->transaction) &&
                                $item->transaction->type === 'R' &&
                                empty($item->canceled_at)
                            )
                                <a class="dropdown-item table-menu-item btn-modal-subscription-cancel-sub"
                                href="javascript:void(0)"
                                data-id="{{ $item->id }}"
                                data-subscriber="{{ $item->subscriber->name }}"
                                data-plan="{{ $item->plan->name }}"
                                data-recurrence="{{ $item->plan->recurrence }}"
                                data-last-payment="{{ ($item->payments->count() > 0) ? dateBr(Carbon::parse(end($item->payments)[0]->payment_date)->add($item->plan->recurrence, 'days')) : '' }}">
                                    Cancelar
                                </a>
                            @endif
                            @if (!empty($item->transaction) && $paymentPlan->status === 'paid')
                                <a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                data-bs-toggle="modal"
                                data-bs-target="#modal-send-purchase-proof"
                                data-id="{{ $item->transaction->id }}">
                                    Reenviar comprovante de compra
                                </a>
                            @endif
                            @if (!empty($item->transaction) && $item->transaction->type_payment === 'boleto')
                                <li>
                                    <a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                    data-bs-toggle="modal" data-bs-target="#modal-send-bank-slip"
                                    data-id="{{ $item->transaction->id }}">
                                        Reenviar boleto
                                    </a>
                                </li>
                            @endif
                            @if (!empty($item->transaction) &&
                                $item->transaction->type_payment === 'credit_card' &&
                                $paymentPlan->status === 'canceled'
                            )
                                <li>
                                    <a class="dropdown-item table-menu-item btn-"
                                    href="javascript:void(0)"
                                    data-bs-toggle="modal" data-bs-target="#modal-send-refund"
                                    data-id="{{ $paymentPlan->id }}">
                                        Reenviar comprovante de estorno
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item table-menu-item btn-"
                                    href="javascript:void(0)"
                                    onclick="downloadRefundProof('{{ $paymentPlan->id }}')">
                                        Baixar comprovante de estorno
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </td>
            </tr>
        @empty
            <tr id="notPlanRegistered">
                <td class="text-center" colspan="5">Não há assinaturas cadastradas!</td>
            </tr>
        @endforelse
        </tbody>
        <tfoot>
        <tr>
            <td colspan="6">
                <div class="d-flex justify-content-end">
                    <button class="xgrow-button" type="button" id="btnAddPlan" style="width: fit-content; padding: 0 20px;">
                        <i class="fa fa-plus"></i> Adicionar produto
                    </button>
                </div>
            </td>
        </tr>
        </tfoot>
    </table>
</div>

<div class="row">
    <div class="col-sm-12" style="text-align: right">
        <button class="xgrow-button mx-2" type="button" id="sendPlans">
            Salvar alterações
        </button>
    </div>
</div>


{{-- MODAL STATUS --}}
<div class="modal-sections modal fade" tabindex="-1" id="modal-change-status" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="d-flex w-100 justify-content-end p-3 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-header">
                <p class="modal-title">Alteração de status do produto</p>
            </div>
            <div class="modal-body" style="display:unset;padding:30px;text-align:left">
                <div
                    class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                    <select name="lbl-mb-status" id="lbl-mb-status" class="xgrow-select">
                        <option value="{{\App\Subscription::STATUS_ACTIVE}}">Ativo</option>
                        <option value="{{\App\Subscription::STATUS_CANCELED}}">Cancelado</option>
                        <option value="{{\App\Subscription::STATUS_PENDING}}">Pendente</option>
                    </select>
                    {!! Form::label('lbl-mb-status', 'Status:') !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" id="btn-submit" data-bs-dismiss="modal">Salvar</button>
                <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script>
    let exampleModal = document.getElementById('modal-change-status');
    exampleModal.addEventListener('show.bs.modal', function (event) {
        let button = event.relatedTarget;
        let subId = button.getAttribute('data-bs-id');
        let subStatus = button.getAttribute('data-bs-status');
        let slcStatus = exampleModal.querySelector('#modal-change-status .modal-body select');
        slcStatus.value = subStatus;
        document.getElementById('btn-submit').setAttribute('onclick', `changeStatus(${subId})`);
    })

    function changeStatus(id) {
        const status = document.getElementById("lbl-mb-status")
        axios.post('{{route('subscription.change.product')}}', {sub_id: id, sub_status: status.value})
            .then(res => {
                successToast('Status alterado com sucesso.', res.data.message.toString());
                let txt = document.getElementById('status-'+id);
                txt.textContent = convertStatus(status.value);
                document.getElementById(`btnStatus-${id}`).setAttribute('data-bs-status', status.value);
            })
            .catch(err => {
                errorToast('Algum erro aconteceu!', 'Não foi status do produto solicitado.');
                console.log(err);
            });
    }
</script>
