<div id="payments" class="table-responsive" style="min-height: 512px">
    <table id="plan-table" class="mt-3 xgrow-table table text-light dataTable no-footer" style="width:100%">
        <thead>
        <tr class="card-black" style="border: 4px solid var(--black-card-color)">
            <th>Pedido</th>
            <th>Transação</th>
            <th>Dt. cobrança</th>
            <th>Produto</th>
            <th>Status</th>
            <th>Método</th>
            <th>Origem</th>
            <th>Valor</th>
            <th>Valor líq.</th>
            <th class="no-export"></th>
        </tr>
        </thead>
        <tbody>
        @forelse ($payments as $item)
            <tr id="payment_{{ $item->id }}">
                <td>{{ $item->order_number ?? '-' }}</td>
                <td>{{ $item->charge_code ?? '-' }}</td>
                <td>
                    {{ isset($item->payment_date) ? date('d/m/Y', strtotime($item->payment_date)) : '-' }}
                    @if (
                        $item->confirmed_at !== null &&
                        $item->payment_date !== date('Y-m-d',strtotime($item->confirmed_at))
                    )
                    <i style="color: rgb(173, 255, 47);cursor:pointer" class='fas fa-circle-info'
                        title="O pagamento foi efetuado em {{ date('d/m/Y', strtotime($item->confirmed_at)) }}"></i>
                    @endif
                </td>
<?php
    /*
                <td>
                    @isset($item->plans)
                        @foreach ($item->plans as $plan)
                            <a href="/products/{{ $plan->product_id ?? 'javascript:void(0)' }}/plans"
                               style="color: inherit">
                                {{ $plan->name ?? '-' }}@if (!$loop->last), @endif
                            </a><br>
                        @endforeach
                        {{ count($item->plans) === 0 ? 'Produto excluído' : '' }}
                    @endisset
                </td>
    */
?>
                <td>
                            <a href="/products/{{ $item->product_id }}/plans"
                               style="color: inherit">
                                {{ $item->plan_name ?? '-' }}
                            </a><br>
                </td>
                <td name="status">
                    {{ \App\Payment::listStatus()[$item->status] }}
                </td>
                <td>
                    {{ getPaymentType($item->type_payment) }}
                    @if ($item->type === 'P')
                        @php $totalPaymentsSameTransaction = $payments->where('charge_code', $item->charge_code)->count(); @endphp
                        @if ($totalPaymentsSameTransaction > 1)
                            <i class="fa fa-info-circle" data-bs-container="body" data-bs-toggle="popover"
                               data-bs-placement="bottom" data-bs-content="Múltiplos cartões">
                            </i>
                        @endif
                    @endif
                </td>
                <td>{{ \App\Payment::listPaymentSources()[$item->payment_source] ?? '-' }}
                </td>
                <td>{{ formatCoin($item->plan_value) ?? '-' }}</td>
                <td>{{ formatCoin($item->customer_value) ?? '-' }}</td>
                <td>
                    @if ($item->status !== 'pending')
                        <div class="dropdown">
                            <button class="xgrow-button table-action-button m-1" type="button"
                                    id="dropdownMenuButton_payment_{{ $item->id }}" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu table-menu"
                                aria-labelledby="dropdownMenuButton_payment_{{ $item->id }}">
                                @if ($item->gateway == 'mundipagg')
                                    @if ((in_array($item->type_payment, ['credit_card', 'pix', 'boleto'])) && (in_array($item->status, ['paid'])))
                                        <li>
                                            @if( $item->type == 'U')
                                                <a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                                   onclick="callModalRefund('{{ $item->type_payment }}', '{{ $item->payment_plan_id }}', '{{ $item->plan_value }}', true )"
                                                   id="btnReverse{{ $item->id }}">Estornar apenas este pagamento</a>
                                            @else
                                                <a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                                   onclick="callModalRefund('{{ $item->type_payment }}', '{{ $item->payment_plan_id }}', '{{ $item->plan_value }}')"
                                                   id="btnReverse{{ $item->id }}">Estornar pagamento</a>
                                            @endif

                                        </li>

                                        @if( $item->type == 'U' && $item->status == 'paid' )
                                        <li>
                                            <a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                               onclick="callModalRefund('{{ $item->type_payment }}', '{{$item->payment_plan_id}}', '{{ $item->plan_value }}', false)"
                                               id="btnReverse{{ $item->id }}">
                                                Estornar todos pagamentos da recorrência
                                            </a>
                                        </li>
                                        @endif
                                    @endif

                                    @if ((in_array($item->type_payment, ['credit_card', 'pix', 'boleto'])) && (in_array($item->status, ['refunded'])))
                                        <li>
                                            <a class="dropdown-item table-menu-item btn-" href="javascript:void(0)"
                                               data-bs-toggle="modal" data-bs-target="#modal-send-refund"
                                               data-id="{{ $item->payment_plan_id }}">Reenviar comprovante de estorno</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item table-menu-item btn-" href="javascript:void(0)"
                                               onclick="downloadRefundProof('{{ $item->payment_plan_id }}')">
                                                Baixar comprovante de estorno
                                            </a>
                                        </li>
                                    @endif

                                    @if( in_array($item->status, ['paid']) )
                                    <li>
                                        <a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                           data-bs-toggle="modal" data-bs-target="#modal-send-purchase-proof"
                                           data-id="{{ $item->id }}">Reenviar comprovante de compra</a>
                                    </li>
                                    @endif
                                @endif

                                @if ($item->type_payment === 'boleto' && in_array($item->status, ['pending']))
                                    <li>
                                        <a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                           data-bs-toggle="modal" data-bs-target="#modal-send-bank-slip"
                                           data-id="{{ $item->id }}">Reenviar boleto</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td class="text-center" colspan="8">Não foram encontrados pagamentos!</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
