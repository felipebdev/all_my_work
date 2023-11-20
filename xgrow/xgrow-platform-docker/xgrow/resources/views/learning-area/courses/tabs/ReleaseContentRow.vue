<template>
    <div class="d-flex w-100 align-items-baseline gap-3">
        <div
            class="p-3 p-md-2 d-flex flex-column flex-md-row w-100 align-items-baseline gap-3 content-child justify-content-between">
            <ProfileRow class="w-100 align-self-center"
                :profile="{ img: item.horizontal_image ?? 'https://las.xgrow.com/background-default.png', title: `Aula ${index + 1} | ${item.title}`, subtitle: item.subtitle }" />
            <div class="w-100 align-self-center">
                <DeliveryPeriod class="align-items-end" :id="`deliveryPeriod-${item.id}`"
                    v-model:formDelivery="item.form_delivery" v-model:frequency="item.frequency"
                    v-model:deliveryModel="item.delivery_model" v-model:deliveryOption="item.delivery_option"
                    v-model:deliveredAt="item.delivered_at" />
            </div>
        </div>
    </div>
</template>

<script>
import ButtonDetail from "../../../../js/components/Datatables/ButtonDetail";
import Select from "../../../../js/components/XgrowDesignSystem/Form/Select";
import ProfileRow from "../../../../js/components/Datatables/ProfileRow";
import DeliveryPeriod from "../../components/DeliveryPeriod.vue";

export default {
    name: "ReleaseContentRow",
    components: { DeliveryPeriod, ProfileRow, ButtonDetail, Select },
    props: {
        item: { type: Object },
        index: { type: Number }
    },
    data() {
        return {
            delivery: {
                enable: false,
                releaseOption: this.$props.item.form_delivery,
                deliveryOption: this.$props.item.delivery_option,
                frequency: this.$props.item.frequency,
                startedAt: this.$props.item.started_at,
                deliveryModel: this.$props.item.delivery_model,
                deliveredAt: this.$props.item.delivered_at,
            }
        }
    },
    methods: {
        /** Get content type */
        getTypeContent: function (type) {
            if (type === 'archive') return '<i class="fas fa-paperclip me-2"></i> Arquivo'
            if (type === 'content') return '<i class="fa fa-file-alt me-2"></i> Conteúdo'
            if (type === 'link') return '<i class="fas fa-link me-2"></i> Link'
            if (type === 'text') return '<i class="fas fa-align-left me-2"></i> Texto'
            if (type === 'video') return '<i class="fa fa-photo-video me-2"></i> Vídeo'
            if (type === 'audio') return '<i class="fas headphones me-2"></i> Áudio'
            return '<i class="fa fa-file-alt me-2"></i> Conteúdo'
        }
    },
}
</script>

<style lang="scss" scoped>
tr {
    vertical-align: middle;
    border: 2px solid #2a2f39;
}

.content-child {
    background: #2f333f;
    margin-top: 10px;
    padding: 10px 5px;
    box-shadow: 0 4px 4px rgb(0 0 0 / 25%);
    border-radius: 6px;
}
</style>
