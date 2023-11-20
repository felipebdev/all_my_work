@push('after-scripts')
    <script>
        function modalBlockList(blockList) {
            blockList = JSON.parse(atob(blockList)) || [];

            let html = ``;

            if (blockList.length > 0) {
                blockList.forEach(element => {
                    html += `
                        <div class="d-flex flex-wrap align-items-center justify-content-start w-100 mb-3">
                            <p><b>Data bloqueio:</b> ${formatDateTimePTBR(element.createdAt)}</p>
                            <p>&nbsp;&nbsp;<b>IP:</b> ${element.userIp}</p>
                            <p>&nbsp;&nbsp;<b>Localização:</b> ${element.userLocation || '-'}</p>
                        </div>
                    `;
                });
            } else {
                html = `
                    <div class="d-flex flex-wrap align-items-center justify-content-center w-100 mb-3">
                        <p>Não há registros para mostrar</p>
                    </div>
                `;
            }

            $("#ip-list").html(html);
            $("#modal-blocked").modal('show');
        }
    </script>
@endpush

<div class="modal-sections modal fade" id="modal-blocked" tabindex="-1" aria-labelledby="modal-blockedLabel"
        aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="max-width: 100%">
            <div class="modal-header">
                <button type="button" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="modal-header">
                <p class="modal-title" id="modal-blockedLabel">
                    Lista de bloqueios
                </p>
            </div>
            <div class="modal-body d-flex flex-wrap align-items-start w-100" id="ip-list">
            </div>
            <div class="modal-footer justify-content-center">
                <button
                    onclick="$('#modal-blocked').modal('hide')"
                    class="xgrow-button mt-3"
                    style="height:40px; width:128px">
                    Ok
                </button>
            </div>
        </div>
    </div>
</div>
