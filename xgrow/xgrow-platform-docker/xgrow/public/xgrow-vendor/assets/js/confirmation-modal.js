const confirmModalOptions = {
    backdrop: true
}

function openConfirmationModal(params) {
    // Add forceReload when you need reload the page. Default is null for not change functions existents
    const { title, description, btnSave, btnCancel, url, method, body, success, error, datatables, forceReload = false } = JSON.parse(window.atob(params));
    const modalHTMLElement = document.getElementById('confirmationModal');
    const modalTitle = document.getElementById('confirmationModalTitle');
    const modalBody = document.getElementById('confirmationModalBody');
    const modalBtnSave = document.getElementById('confirmationModalSave');
    const modalBtnCancel = document.getElementById('confirmationModalCancel');
    modalTitle.innerText = title;
    modalBody.innerText = description;
    modalBtnCancel.innerText = btnCancel;
    modalBtnSave.innerText = btnSave;
    modalBtnSave.onclick = function () {
        $.ajax({
            type: method,
            url: url,
            dataType: 'json',
            data: body,
            success: function(data) {
                successToast('Sucesso', success);
                modalElement.hide();
                if (datatables) {
                    $(datatables).DataTable().ajax.reload();
                    if (forceReload){
                      document.location.reload(true);
                    }
                }
                $(`#card_${body.id}`).remove()
            },
            error: function(data) {
                errorToast('Erro', error + data.responseJSON.message);
                modalElement.hide();
            },
        });
    }

    const modalElement = new bootstrap.Modal(modalHTMLElement, confirmModalOptions);
    modalElement.show();
}
