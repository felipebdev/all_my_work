<template>
    <button
        type="button"
        data-bs-toggle="collapse"
        :data-bs-target="`#${target}`"
        aria-bs-expanded="false"
        :aria-bs-controls="target"
        class="xgrow-button-filter xgrow-button export-button custom"
        aria-expanded="true">
        <span>Filtros avançados <i class="fa fa-chevron-down" aria-hidden="true"></i></span>
    </button>
</template>

<script>
export default {
    name: "FilterButton",
    props: {
        target: {
            type: String,
            required: true
        }
    }
}
</script>

<style lang="scss" scoped>
.custom {
    min-width: 190px;
    width: fit-content !important;
    padding: 0 1rem;

    span {
        font-size: 0.875rem;
    }

    &:focus {
        border: none;
        filter: brightness(.9);
    }
}

i.fa {
    margin-left: 0.625rem;
}
</style>
