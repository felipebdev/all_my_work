<template>
    <ConfirmModal :is-open="isOpen">
        <i class="fas fa-times-circle fa-7x text-danger"></i>

        <div class="modal-body__content">
            <h1>Excluir Notificação?</h1>
            <p>Ao remover esta notificação, a mesma não poderá ser recuperada!</p>
        </div>

        <div class="modal-body__footer">
            <hr>
            <DefaultButton text="Cancelar" outline @click="$emit('close')" />
            <DefaultButton text="Excluir mesmo assim" :disabled="false" status="success" @click="$emit('confirm')" />
        </div>
    </ConfirmModal>
</template>

<script>
import ConfirmModal from '../../../../js/components/XgrowDesignSystem/Modals/ConfirmModal.vue'
import DefaultButton from '../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue'

export default {
    name: 'delete-Notification',
    components: {
        ConfirmModal,
        DefaultButton,
    },
    props: {
        isOpen: { type: Boolean, required: true },
    },
}
</script>

<style lang="scss" scoped>
.modal-body__content {
    border-radius: 8px;
    padding: 1rem;
    margin-top: 0 !important;
}
</style>