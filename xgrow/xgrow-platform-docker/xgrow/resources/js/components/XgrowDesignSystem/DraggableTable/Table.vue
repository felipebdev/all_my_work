<template>
    <div>
        <slot name='filter'></slot>
        <slot name="collapse"></slot>
        <div class="table-responsive" :class="{ 'min-vh-50': minHeight }">
            <table :id="id" class="table custom-table w-100">
                <thead>
                    <tr class="xgrow-table-header">
                        <slot name="thead"></slot>
                    </tr>
                </thead>
                <slot name="tbody"></slot>
            </table>
        </div>
        <slot name="footer"></slot>
    </div>
</template>

<script>
export default {
    name: "DraggableTable",
    props: {
        id: { type: String },
        minHeight: { type: Boolean, default: false }
    }
};
</script>

<style lang="scss" scoped>
.custom-table {
    color: #ffffff !important;
}

.xgrow-table-header {
    background-color: transparent;
    border: none;
    color: #7A7F8D;
    font-size: 0.875rem;
    font-weight: 600;
    line-height: 1.375rem;

    :slotted(th) {
        padding: 12px 8px;
        border-bottom: none;
    }
}

:slotted(tbody) {
    tr {
        background: #333844 !important;
        border-bottom: 6px solid #2a2d39 !important;
        font-size: 0.875rem !important;
        vertical-align: middle !important;

        td {
            border: none !important;
            background: transparent !important;
        }
    }

    .ghost {
        cursor: grab !important;
        background: #222329 !important;
        border: 2px dashed #93BC1E !important;
        border-radius: 6px !important;
    }
}

:slotted(.filter-container) .title-filter {
    font-weight: 700;

    svg {
        color: #93bc1e;
        margin-right: 5px;
    }
}

.min-vh-50 {
    min-height: 50vh;
}
</style>
