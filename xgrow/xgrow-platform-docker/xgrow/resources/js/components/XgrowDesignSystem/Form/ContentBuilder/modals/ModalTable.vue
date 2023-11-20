<template>
    <confirm-modal :isOpen="isOpen" modalSize="lg" class="modal">
        <template v-slot:header>
            <div class="modal__header">
                <h1 class="modal__title">
                    {{ isEdit ? "Editar" : "Adicionar" }} conteúdo:
                    <span class="modal__title modal__title--semi-bold">
                        Tabela
                    </span>
                </h1>
            </div>
        </template>
        <div class="modal__body">
            <p class="modal__text">
                Selecione abaixo o tamanho da tabela e insira em seu conteúdo.
            </p>


            <div class="d-flex gap-4">
                <div class="d-flex gap-2">
                    <select-table-items-vue
                        id="table"
                        :value="quantityColumns"
                        @select="(value) => quantityColumns = value"
                    />

                    <color-picker-vue v-model="backgroundTitle" />
                </div>

                <div class="d-flex gap-2">
                    <select-table-items-vue
                        id="table_row"
                        type="row"
                        :value="quantityRows"
                        @select="(value) => quantityRows = value"
                    />

                    <color-picker-vue v-model="backgroundBody" />
                </div>
            </div>


            <div class="modal__table-wrapper">
                <span class="modal__table__title">Conteúdo da tabela</span>


                <table class="modal__table">
                    <thead>
                        <th
                            :key="i"
                            :style="{ background: backgroundTitle }"
                            class="modal__table__header"
                            v-for="(header, i) in rows[0]"
                        >
                            <input
                                type="text"
                                class="modal__hiden-input modal__hiden-input--title"
                                v-model="rows[0][i]"
                            />
                        </th>
                    </thead>
                    <tbody v-if="bodyRows.length">
                        <tr v-for="(row, i) in bodyRows" :key="i + 'row'">
                            <td
                                :key="j + 'data'"
                                :style="{ background: backgroundBody }"
                                class="modal__table__data"
                                v-for="(data, j) in row"
                            >
                                <input
                                    type="text"
                                    class="modal__hiden-input"
                                    v-model="row[j]"
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="modal__actions">
                <default-button
                    :onClick="close"
                    :outline="true"
                    text="Voltar"
                />
                <default-button
                    :onClick="save"
                    icon="fas fa-save"
                    status="success"
                    text="Salvar"
                />
            </div>
        </div>
    </confirm-modal>
</template>

<script>
import DefaultButton from '../../../Buttons/DefaultButton.vue';
import ConfirmModal from '../../../Modals/ConfirmModal.vue';
import ColorPickerVue from '../components/ColorPicker.vue';
import SelectTableItemsVue from '../components/SelectTableItems.vue';


export default {
    name: 'table-modal',
    components: {
        ConfirmModal,
        DefaultButton,
        SelectTableItemsVue,
        ColorPickerVue
    },
    data() {
        return {
            quantityColumns: undefined,
            quantityRows: undefined,
            rows: [],
            backgroundTitle: "#324066",
            backgroundBody: "#252932",
        }
    },
    methods: {
        close() { this.$emit('close'); },
        save() {
            if (this.isEdit) {
                successToast("Ação realizada", "Widget atualizado com sucesso");

                this.$emit('update', {
                    position: this.data.position,
                    type: 'table',
                    table_data: {
                        backgroundTitle: this.backgroundTitle,
                        backgroundBody: this.backgroundBody,
                        rows: this.rows,
                    }
                });
            }
            else {
                successToast("Ação realizada", "Widget adicionado com sucesso");

                this.$emit('save', {
                    type: 'table',
                    table_data: {
                        backgroundTitle: this.backgroundTitle,
                        backgroundBody: this.backgroundBody,
                        rows: this.rows,
                    }
                });
            }

        },
    },
    props: {
        data: {
            type: Object,
            default: {
                table_data: {
                    rows: [['1', '2']],
                    backgroundTitle: "#324066",
                    backgroundBody: "#252932",
                }
            }
        },
        isOpen: { type: Boolean, required: true },
        isEdit: { type: Boolean, required: true },
    },
    watch: {
        data(newState) {
            this.quantityColumns = newState.table_data.rows[0].length;
            this.quantityRows = newState.table_data.rows.length;
            this.rows = newState.table_data.rows;
            this.backgroundTitle = newState.table_data.backgroundTitle;
            this.backgroundBody = newState.table_data.backgroundBody;
        },
        quantityRows(newValue, oldValue) {
            console.log(newValue, oldValue);

            if (oldValue < newValue) {
                for(let i = oldValue; i < newValue; i++) {
                    let newRow = [];

                    this.rows[0].forEach(() => newRow.push(''));

                    this.rows.push(newRow);
                }
            } else {
                this.rows.splice(newValue);
            }
        },
        quantityColumns(newValue, oldValue) {
            if (oldValue < newValue) {

                for(let i = oldValue; i < newValue; i++)
                    this.rows.forEach(row => row.push(''));

            } else {
                this.rows.forEach(row => row.splice(newValue));
            }
        }
    },
    computed: {
        bodyRows(){
            return this.rows.filter((_, i) => i !== 0)
        }
    },
    mounted() {
        console.log(this.data.table_data, 'table_data');

        this.quantityColumns = this.data.table_data.rows[0].length;

        this.quantityRows =  this.data.table_data.rows.length;

        this.backgroundBody = this.data.table_data.backgroundBody;
        this.rows = this.data.table_data.rows;
        this.backgroundTitle = this.data.table_data.backgroundTitle;
    }
}
</script>

<style lang="scss" scoped>
    .modal {
        &__header {
            border-bottom: 1px solid rgba(#C4C4C4, .15);
            padding: 20px 0;
            margin-left: 12px;
            width: 100%;
        }

        &__title {
            font-size: 20px;
            font-weight: 700;
            line-height: 1.6;

            &--semi-bold { font-weight: 600; }
        }

        &__body {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 16px;
            width: 100%;
        }

        &__table-wrapper {
            background: #252932;
            border-bottom: 1px solid #93BC1E;
            display: flex;
            flex-direction: column;
            max-height: 365px;
            overflow: auto;
            padding: 12px;
        }

        &__table {
            width: 100%;

            &__title {
                color: #93BC1E;
                display: block;
                font-size: 14px;
                font-weight: 700;
                line-height: 1.6;
                margin-bottom: 6px;
                text-align: start;
            }

            &__header {
                border-left: 1px solid #646D85;
                border-right: 1px solid #646D85;
                font-size: 14px;
                font-weight: 700;
                padding: 4px 8px;
                text-align: center;

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
            }
        }


        &__hiden-input {
            background: transparent;
            border: none;
            color: #FFFFFF;
            font-size: 14px;
            line-height: 1.6;
            outline: none;
            width: 100%;
            text-align: center;

            &--title {
                font-weight: 700;
                font-size: 16px;
            }

            &:focus { outline: none; }
        }


        &__text {
            font-size: 16px;
            font-weight: 600;
            line-height: 1.6;
            text-align: left;
        }

        &__actions {
            border-top: 1px solid rgba(#C4C4C4, .15);
            display: flex;
            gap: 20px;
            justify-content: flex-end;
            padding-top: 40px;
        }
    }
</style>
