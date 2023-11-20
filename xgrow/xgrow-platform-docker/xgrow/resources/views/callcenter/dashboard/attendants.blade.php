@push('after-scripts')
    <script>
        async function getAttendantsList(filter) {
            $('.list-attendant').html(`<p class="text-center my-auto" id="attendantsInfo">Carregando...</p>`);

            let res = await axios.get(`/callcenter/reports/dashboard/get-attendants/${filter}`);

            if (res.data.length < 1) {
                $('.list-attendant').html(`<p class="text-center my-auto">Nenhum atendente foi registrado</p>`);
                return;
            }
            
            let html = '';
            res.data.forEach(attendant => {
                const route = @json(route('callcenter.reports.attendant', ':id'));
                const url_report = route.replace(/:id/g, attendant.id);
                html += `
                    <div class="item-attendant border-bottom pt-2 pb-3 mb-3">
                        <div class="left">
                            <a href="${url_report}">
                                <p>${attendant.name}</p>
                                <p>${attendant.email}</p>
                            </a>
                        </div>
                        <div class="right">
                            <div class="dropdown x-dropdown">
                                <button class="xgrow-button table-action-button m-1" type="button" id="dropdownMenuButton${attendant.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu table-menu" aria-labelledby="dropdownMenuButton${attendant.id}">
                                    <li><a class="dropdown-item table-menu-item" href="${url_report}">Ver relatórios</a></li>
                                    <li><a class="dropdown-item table-menu-item" href="/callcenter/attendant/${attendant.id}/edit">Editar</a></li>
                                    <li><a class="dropdown-item table-menu-item" href="javascript:sendLinkCallcenter(${attendant.id})">Enviar link do Call Center</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            $('.list-attendant').html(html);
        }

        async function sendLinkCallcenter(attendant_id) {
            try {
                await axios.get(`/callcenter/attendant/send-mail-link-callcenter/${attendant_id}`);
                successToast('Sucesso','Email enviado para o atendente');
            } catch (error) {
               errorToast('Erro', "Erro ao enviar email");
            }
        }
    </script>
@endpush

<div class="xgrow-card card-dark mb-2 mt-2">
    <div class="xgrow-card-header">
        <p class="xgrow-card-title">Listagem de atendentes</p>
    </div>
    <div class="xgrow-card-body">
        <div class="list-attendant">
            <p class="text-center my-auto" id="attendantsInfo">Selecione um público para mostrar as informações</p>
        </div>
    </div>
    <div class="xgrow-card-footer">
        <button class="xgrow-button-secondary" onclick="window.location.href='/callcenter/attendant/create'">
            <i class="fa fa-plus" aria-hidden="true"></i>
            Novo atendente
        </button>
        <button class="xgrow-button mx-3" onclick="window.location.href='{{ route('callcenter.reports') }}'">Ver relatórios</button>
        <button class="xgrow-button" onclick="window.location.href='/callcenter/attendant'">Ver atendentes</button>
    </div>
</div>