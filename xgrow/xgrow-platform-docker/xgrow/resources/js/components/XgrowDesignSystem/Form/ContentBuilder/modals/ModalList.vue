<template>
    <confirm-modal :isOpen="isOpen" modalSize="lg" class="modal">
        <template v-slot:header>
            <div class="modal__header">
                <h1 class="modal__title">
                    {{ isEdit ? "Editar" : "Adicionar" }} conteúdo:
                    <span class="modal__title modal__title--semi-bold">
                        Listagem
                    </span>
                </h1>
            </div>
        </template>
        <div class="modal__body">
            <p class="modal__text">Selecione abaixo o estilo da listagem e insira em seu conteúdo.</p>

            <toggle-list @change="toggleListType" :type="type" />

            <div class="modal__list-wrapper">
                <span class="modal__list__title">Conteúdo da listagem</span>

                <component :is="type" class="modal__list" >
                    <li class="modal__item" v-for="(item, i) in items" :key="i">
                        <input
                            type="text"
                            class="modal__hiden-input"
                            v-model="items[i]"
                            @keydown.enter="nextListItem(i)"
                            @keydown.delete="previousListItem(i)"
                        />
                    </li>
                </component>
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
import ToggleList from '../components/ToggleListButton.vue';

export default {
    name: 'list-modal',
    components: {
        ConfirmModal,
        DefaultButton,
        ToggleList
    },
    data() {
        return {
            items: [],
            type: "ul"
        }
    },
    methods: {
        close() { this.$emit('close'); },
        save() {
            try {
                this.validation();

                if (this.isEdit) {
                    successToast("Ação realizada", "Widget atualizado com sucesso");

                    this.$emit('update', {
                        position: this.elemData.position,
                        html: this.encodeList(this.items),
                        type: this.elemData.type
                    });
                }
                else {
                    successToast("Ação realizada", "Widget adicionado com sucesso");

                    this.$emit('save', {
                        html: this.encodeList(this.items),
                        type: this.elemData.type
                    });
                }
            }
            catch(message) {
                errorToast("Não foi possível realizar a ação", message)
            }
        },
        nextListItem(index) {
            if (
                this.items[index] == ""
                || index != document.querySelectorAll('.modal__hiden-input').length - 1
            ) return;

            this.items.push('');
            setTimeout(() => {
                document.querySelectorAll('.modal__hiden-input')[index + 1].focus();
            }, 100);
        },
        previousListItem(index) {
            if (this.items[index] != "" || index == 0) return;

            setTimeout(() => {
                document.querySelectorAll('.modal__hiden-input')[index + -1].focus();
                this.items.splice(index, 1);
            }, 100);
        },
        toggleListType(type) {
            this.type = type;
        },
        decodeList(html) {
            // check list type
            this.type = html.includes('ul') ? 'ul' : 'ol';
            // remove ul and li tags
            let parsedArr = html.replace(/((<ul>|<ol>)|<li>(.*)<\/li>|(<\/ul>|<\/ol>))/g, '$3');
            // split string with breaking line and remove empty items
            this.items = parsedArr.split('\n').filter(el => !!el)
        },
        encodeList(arr) {
            //create list items and convert in string
            arr = arr.map(item => `<li>${item}</li>\n`).join('');
            const formatArrayType = (str, type) => `<${type}>\n${str}</${type}>`;
            return formatArrayType(arr, this.type);
        },
        validation() {
            if(this.items.filter(el => !!el).length == 0)
                throw new Error("A lista precisa ter ao menos 1 item");
        }
    },
    props: {
        isOpen: { type: Boolean, required: true },
        isEdit: { type: Boolean, required: true },
        elemData: { type: Object ,  default: { html: '<ul>\n<li>item de exemplo</li>\n</ul>' }}
    },
    watch: {
        elemData() {
            this.decodeList(this.elemData.html);
        }
    },
    mounted() {
        this.decodeList(this.elemData.html);
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

        &__list-wrapper {
            background: #252932;
            border-bottom: 1px solid #93BC1E;
            display: flex;
            flex-direction: column;
            max-height: 365px;
            overflow-y: auto;
            padding: 12px;
        }

        &__list {
            text-align: start;

            &__title {
                color: #93BC1E;
                display: block;
                font-size: 14px;
                font-weight: 700;
                line-height: 1.6;
                margin-bottom: 6px;
                text-align: start;
            }
        }

        ul > li { list-style-type: disc; }

        ol > li { list-style-type: decimal; }

        &__item {
            color: #C1C5CF;
            list-style: inherit;
        }

        &__hiden-input {
            background: transparent;
            border: none;
            color: #C1C5CF;
            font-size: 14px;
            line-height: 1.6;
            outline: none;
            width: 100%;

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
