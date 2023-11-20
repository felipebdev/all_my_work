<template>
    <ConfirmModal :is-open="isOpen">
        <i class="fas fa-times-circle fa-7x text-danger"></i>
        <div class="modal-body__content">
            <h1>Excluir seção?</h1>
            <p>Ao remover esta seção, a mesma não poderá ser recuperada!</p>
        </div>
        <div class="modal-body__footer">
            <hr>
            <DefaultButton text="Cancelar" outline @click="$emit('close')" />
            <DefaultButton text="Confirmar" status="success" @click="$emit('confirm', id)" />
        </div>
    </ConfirmModal>
</template>

<script>
import ConfirmModal from "../../../../js/components/XgrowDesignSystem/Modals/ConfirmModal";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";


export default {
    name: 'modal-delete-section',
    components: {
        ConfirmModal,
        DefaultButton
    },
    props: {
        isOpen: { type: Boolean, required: true },
        id: { type: String, required: true }
    }

}
</script>
