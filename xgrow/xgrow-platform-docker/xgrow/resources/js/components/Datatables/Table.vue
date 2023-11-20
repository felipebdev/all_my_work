<template>
    <div>
        <div class="d-flex align-items-center justify-content-sm-center justify-content-md-between flex-wrap">
            <slot name="title"></slot>
        </div>
        <slot name='filter'></slot>
        <slot name="collapse"></slot>
        <div class="table-responsive" :class="{ 'min-vh-50': minHeight }">
            <table :id="id" class="table custom-table w-100">
                <thead>
                    <tr class="xgrow-table-header">
                        <template v-if="sortable">
                            <template v-for="item in tableHeader" :key="item.col">
                                <th v-if="item.col" class="has-header" @click="sortTable(item.col)">
                                    <p>{{ item.title }} <span v-html="sortIconTable(item.col)"></span></p>
                                </th>
                                <th v-else>
                                    <p>{{ item.title }}</p>
                                </th>
                            </template>
                        </template>
                        <template v-else>
                            <slot name="header"></slot>
                        </template>
                    </tr>
                </thead>
                <tbody>
                    <slot name="body"></slot>
                </tbody>
            </table>
        </div>
        <slot name="footer"></slot>
    </div>
</template>

<script>
export default {
    name: "XgrowTable",
    props: {
        id: { type: String },
        minHeight: { type: Boolean, default: false },
        sortable: { type: Boolean, default: false },
        tableHeader: { type: Array, default: [] },
        order: { type: Array, default: [] },
    },
    data() {
        return {
            sortOrder: []
        }
    },
    emits: ['sortTable'],
    methods: {
        /** Sort Datatables */
        sortTable: async function (header) {
            const index = this.$props.order.findIndex(item => item.col == header)
            if (index === -1) {
                this.sortOrder.push({ col: header, order: 'asc' });

            } else if (index > -1 && this.sortOrder[index].order === 'asc') {
                this.sortOrder[index].order = 'desc';
            } else if (index > -1 && this.sortOrder[index].order === 'desc') {
                this.sortOrder = this.sortOrder.filter(item => item.col != header)
            }
            this.$emit('sortTable', this.sortOrder)
        },
        /** Change sort icon */
        sortIconTable: function (header) {
            const index = this.$props.order.findIndex(item => item.col == header)
            if (index === -1) {
                return "";
            } else if (index > -1 && this.$props.order[index].order === 'asc') {
                return '<i class="fa-solid fa-caret-up"></i>';
            } else if (index > -1 && this.$props.order[index].order === 'desc') {
                return '<i class="fa-solid fa-caret-down"></i>';
            }
        }
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

tbody {
    :slotted(tr) {
        background: #333844 !important;
        border-bottom: 6px solid #2a2d39 !important;
        font-size: 0.875rem;
        vertical-align: middle !important;

        td {
            border: none !important;
            background: transparent;
        }
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

.has-header {
    cursor: pointer;

    p {
        display: flex;
        justify-content: space-between;
    }
}
</style>
