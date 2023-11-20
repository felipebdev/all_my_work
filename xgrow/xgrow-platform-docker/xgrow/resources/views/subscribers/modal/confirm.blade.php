@push('after-scripts')
    <script>
        function modalFreeAccess(userName, userId) {
            $("#modal-confirmbLabel").text("Liberar acesso");
            $("#confirmationModalSave").text("Sim, liberar");
            $("#confirmb-message").text(`Deseja realmente liberar o acesso de ${userName}`);
            $("#confirmationbModal").attr('onclick', `updateSub(${userId}, 0)`);
            $("#confirmationbModal").text("Sim, liberar");
            $("#modal-confirmb").modal('show');
        }

        function modalBanAccess(userName, userId) {
            $("#modal-confirmbLabel").text("Banir acesso");
            $("#confirmationModalSave").text("Sim, banir");
            $("#confirmb-message").text(`Deseja realmente banir o acesso de ${userName}`);
            $("#confirmationbModal").attr('onclick', `updateSub(${userId}, 1)`);
            $("#confirmationbModal").text("Sim, banir");
            $("#modal-confirmb").modal('show');
        }

        function updateSub(userId, action) {
            $.ajax({
                type: 'POST',
                url: '{{ route("subscribers.blocked.user.update") }}',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    '_token': "{{ csrf_token() }}",
                    'userId': userId,
                    'action': action
                },
                success: function (data) {
                    successToast('Sucesso!', `Usuário ${action == 0 ? 'liberado' : 'banido'} com exito`);
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                },
                error: function (data) {
                    errorToast('Algum erro aconteceu!', `${data.responseJSON.message}`);
                },
            });
        }
    </script>
@endpush

<div class="modal-sections modal fade" id="modal-confirmb" tabindex="-1" aria-labelledby="modal-confirmbLabel"
        aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="modal-header">
                <p class="modal-title" id="modal-confirmbLabel">
                    Liberar/Banir acesso
                </p>
            </div>
            <div class="modal-body">
                <p id="confirmb-message">Deseja realmente liberar/banir o acesso de Silvia Lima</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal" id="confirmationbModal">Sim, liberar/banir</button>
                <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal" id="cancelbModal">Não</button>
            </div>
        </div>
    </div>
</div>
