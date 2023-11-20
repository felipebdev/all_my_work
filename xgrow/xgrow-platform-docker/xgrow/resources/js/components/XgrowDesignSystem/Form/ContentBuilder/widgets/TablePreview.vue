<template>
    <div class="table-widget">
        <div class="table-widget__icon">
            <img class="btn-widget-drag" src="/xgrow-vendor/assets/img/widgets/svg/apps.svg"/>
        </div>


        <div class="d-flex flex-column gap-2 w-100">
            <table class="table-widget__table">
                <thead>
                    <th
                        :key="i"
                        :style="{ background: table_data.backgroundTitle }"
                        class="table-widget__header"
                        v-for="(header, i) in bodyColumns"
                    >
                        {{header}}
                    </th>
                </thead>
                <tbody v-if="bodyRows.length">
                    <tr v-for="(row, i) in bodyRows" :key="i + 'row'">
                        <template v-for="data in previewRow(row)">
                            <td
                                :style="{ background: table_data.backgroundBody }"
                                class="table-widget__data"
                            >
                                {{data}}
                            </td>
                        </template>
                    </tr>
                </tbody>
            </table>

            <p
                v-if="table_data.rows.length >= 5"
                class="text-center"
            >
                ...
                <br/>
                Pré visualização da tabela
            </p>
        </div>


        <div class="table-widget__actions">
            <span @click="$emit('edit')">
                <i class="fas fa-pen edit" />
            </span>
            <div class="pipe"></div>
            <span @click="$emit('delete')">
                <i class="fas fa-trash delete" />
            </span>
        </div>
    </div>
</template>

<script>
export default {
    name: "table-widget",
    props: {
        table_data: { type: Object, required: true },
    },
    computed: {
        bodyRows(){
            return this.table_data.rows.filter((_, i) => i !== 0 && i < 5);
        },
        bodyColumns() {
            return this.table_data.rows[0].filter((_, i) => i < 5);
        },
    },
    methods: {
        previewRow(row) {
            return row.filter((_, i) => i < 5);
        }
    }
};
</script>

<style lang="scss" scoped>
.table-widget {
    align-items: center;
    background: #2A2E39;
    border-radius: 4px;
    color: #E7E7E7;
    display: flex;
    font-size: 14px;
    gap: 8px;
    line-height: 1.6;
    padding: 4px 8px;
    position: relative;
    width: 100%;

    &:hover {
        .table-widget__actions, .table-widget__icon { display: flex; }
    }

    &__icon {
        display: none;
        height: 24px;
        width: 24px;

        &:hover {
            cursor: grab;

            &:active { cursor: grabbing; }
        }

    }

    &__table { width: 100%; }

    &__header {
        border-left: 1px solid #646D85;
        border-right: 1px solid #646D85;
        font-size: 14px;
        font-weight: 700;
        padding: 4px 8px;
        text-align: center;
        height: 30px;

        &:first-child {
            border-top-left-radius: 4px;
            border-left: none;
        }

        &:last-child {
            border-top-right-radius: 4px;
            border-right: none;
        }
    }

    &__data {
        border: 1px solid #646D85;
        text-align: center;
        height: 30px;
    }

    &__actions {
        align-items: center;
        background: linear-gradient(269.89deg, rgba(#333844, .85) 22.33%, rgba(#333844, 0) 90.13%);
        border-radius: inherit;
        display: none;
        gap: 8px;
        height: 100%;
        justify-content: flex-end;
        padding: 8px;
        position: absolute;
        right: 0;
        top: 0;
        width: calc(100% - 32px);

        .pipe {
            background:#222429;
            height: 14px;
            width: 1px;
        }

        .edit, .delete { cursor: pointer; }

        .edit { color: #ADDF45; }

        .delete { color: #F96C6C; }
    }
}
</style>
