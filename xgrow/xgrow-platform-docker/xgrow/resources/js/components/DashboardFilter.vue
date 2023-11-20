<template>
    <card-container-component>

        <div class="row align-items-center">
            <div class="col-xxl-6 col-xl-6 col-lg-3 col-md-12 col-sm-12 mt-3 mb-2">
                <div class="dashboard-title">
                    <h5>Resumo</h5>
                </div>
            </div>
            <div class="col-xxl-6 col-xl-6 col-lg-9 col-md-12 col-sm-12 d-flex flex-wrap align-items-center gap-2 justify-content-sm-start justify-content-md-end">
                <p class="p-0 m-0" style="font-size: 14px;">
                    Filtrar por período:
                </p>
                <div class="d-flex date-filter">
                    <slot></slot>
                </div>
            </div>
        </div>

    </card-container-component>

</template>

<script>
import CardContainerComponent from "../components/CardContainerComponent";

export default {
    name: "subscriber-component",
    components: {CardContainerComponent}
};
</script>

<style>

/* =============== vue-datepicker-next =============== */
.mx-icon-calendar {
    display: none !important;
}

.mx-input {
    border-bottom: none !important;
}

@media (max-width: 768px) {
    .date-filter {
        width: inherit !important;
    }

    .mx-input-wrapper {
        width: auto !important;
    }
}

</style>
