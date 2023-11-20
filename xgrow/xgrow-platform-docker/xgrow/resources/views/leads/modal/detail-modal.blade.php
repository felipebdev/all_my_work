@push('after-scripts')
    <script>
        /** Open detail modal and create the TR on table */
        const showDetailModal = async (uid) => {
            let url = @json(route('get.lead.fail.detail', ':id'));
            url = url.replace(':id', uid);
            const res = await axios.get(url);

            let html = '';
            if(res.data.response.details.length > 0){
                res.data.response.details.forEach((item) => {
                    html += `
                        <tr>
                            <td>${moment(item.created_at).format('DD/MM/YYYY HH:mm:ss')}</td>
                            <td>${item.transaction_message}</td>
                            <td>${getStatus(item.status)}</td>
                            <td></td>
                        </tr>
                    `;
                });
            } else {
                html += `<tr class="table-line"><td colspan="5">Não há dados para exibir</td></tr>`;
            }
            $('#leadDetailTableBody').html(html);
            $("#modalDetail").modal('show');
        }

        function getStatus(status){
            if(status === 'paid') return 'Pago';
            if(status === 'pending') return 'Pendente';
            if(status === 'canceled') return 'Cancelado';
            if(status === 'failed') return 'Falha no pagamento';
            if(status === 'chargeback') return 'Chargeback';
            if(status === 'expired') return 'Expirado';
            if(status === 'refunded') return 'Estornado';
            if(status === 'pending_refund') return 'Estorno pendente';
            return 'Desconhecido';
        }
    </script>
@endpush


<div class="modal-sections modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel"
        aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="max-width: 100% !important">
            <div class="modal-header">
                <button type="button" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="modal-header">
                <p class="modal-title" id="modalDetailLabel">Detalhes da(s) Transação(ões)</p>
            </div>
            <div class="modal-body">
                <div class="table-responsive m-t-30 w-100">
                    <table id="leadDetailTable"
                        class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                        style="width:100%">
                        <thead>
                            <tr class="card-black" style="border: 4px solid var(--black-card-color)">
                                <th>Data</th>
                                <th>Mensagem</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="leadDetailTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal" id="confirmationbModal">Fechar</button>
            </div>
        </div>
    </div>
</div>
{{-- Produto, data-hota, motivo da recusa, status --}}
