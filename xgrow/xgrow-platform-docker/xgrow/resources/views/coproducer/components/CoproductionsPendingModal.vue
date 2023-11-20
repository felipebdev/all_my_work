<template>
    <xgrow-modal-component
        :isOpen="modal.isOpen"
        @close="closeModal">
        <template v-slot:title>
            <!-- WHEN USER IS VERIFIED AND WILL "ACCEPT" THE REQUEST -->
            <div v-if="modal.isVerified && modal.isAccept">Confirmação de dados bancários</div>

            <!-- WHEN USER IS VERIFIED AND WILL "REJECT" THE REQUEST -->
            <div v-if="modal.isVerified && !modal.isAccept">Recusar coprodução</div>
        </template>

        <template v-slot:content>
            <!-- WHEN USER IS VERIFIED AND WILL "ACCEPT" THE REQUEST -->
            <div v-if="modal.isVerified && modal.isAccept && accountIsValid">
                <p class="text-center">
                    Verificamos que você já possui um cadastro como cliente em nossa plataforma. Você deseja
                    permanecer com os mesmos dados bancários cadastrados anteriormente ou deseja cadastrar novos?
                </p>
                <xgrow-table-component>
                    <template v-slot:header>
                        <th>Dados bancários</th>
                        <th></th>
                    </template>

                    <template v-slot:body>
                        <tr>
                            <td>Instituição</td>
                            <td>{{ bankData.client_bank + "-" + bankData.bank }}</td>
                        </tr>
                        <tr>
                            <td>Agência</td>
                            <td v-if="bankData.branch_check_digit">{{ bankData.branch + "-" + bankData.branch_check_digit }}</td>
                            <td v-else>{{ bankData.branch}}</td>
                        </tr>
                        <tr>
                            <td>Conta</td>
                            <td>{{ bankData.account + "-" + bankData.account_check_digit }}</td>
                        </tr>
                        <tr>
                            <td>CPF/CNPJ</td>
                            <td>{{ documentFormated ?? "" }}</td>
                        </tr>
                    </template>
                </xgrow-table-component>
            </div>

            <!-- WHEN USER IS VERIFIED AND WILL "ACCEPT" THE REQUEST, BUT ACCOUNT IS INVALID -->
            <div v-else-if="modal.isVerified && modal.isAccept && !accountIsValid">
                <p class="text-center">
                    Verificamos que você já possui um cadastro como cliente em nossa plataforma. Mas ainda precisa
                    cadastrar seus dados bancários, clique no botão abaixo para continuar
                </p>
            </div>

            <!-- WHEN USER IS VERIFIED AND WILL "REJECT" THE REQUEST -->
            <div v-else-if="modal.isVerified && !modal.isAccept" class="d-flex flex-column align-items-center w-100">
                <i class="fas fa-exclamation-circle mb-3 xgrow-modal-icon"></i>
                <h3 class="text-center">Tem certeza dessa ação?</h3>
                <p class="text-center">Não terá como desfazer essa ação no futuro</p>
            </div>

            <!-- WHEN USER IS NOT VERIFIED -->
            <div v-else class="d-flex flex-column align-items-center">
                <i class="fas fa-exclamation-circle mb-3 xgrow-modal-icon"></i>
                <h3 class="text-center">Verifique a sua identidade</h3>
                <p class="text-center">Antes de aceitar o convite, você precisa verificar a sua identidade.</p>
            </div>
        </template>

        <template v-slot:footer>
            <!-- WHEN USER IS VERIFIED AND WILL "ACCEPT" THE REQUEST -->
            <div class="d-flex align-items-center justify-content-center w-100"
                 v-if="modal.isVerified && modal.isAccept && accountIsValid">
                <button type="button" class="btn btn-outline-success me-2"
                        @click.prevent="updateCoproduction('active')">
                    <small>Permanecer com a mesma conta</small>
                </button>
            </div>

            <!-- WHEN USER IS VERIFIED AND WILL "ACCEPT" THE REQUEST, BUT ACCOUNT IS INVALID -->
            <div class="d-flex align-items-center justify-content-center w-100"
                 v-else-if="modal.isVerified && modal.isAccept && !accountIsValid">
                <button type="button" class="btn btn-success"
                        @click.prevent="changePage('coproducer.flow');closeModal();">
                    <small>Cadastrar nova conta</small>
                </button>
            </div>

            <!-- WHEN USER IS VERIFIED AND WILL "REJECT" THE REQUEST -->
            <div v-else-if="modal.isVerified && !modal.isAccept"
                 class="d-flex align-items-center justify-content-center w-100">
                <button type="button" class="btn btn-outline-success me-2"
                        @click.prevent="closeModal">
                    <small>Cancelar</small>
                </button>
                <button type="button" class="btn btn-success"
                        @click.prevent="updateCoproduction('canceled')">
                    <small>Sim, recusar</small>
                </button>
            </div>

            <!-- WHEN USER IS NOT VERIFIED -->
            <div v-else class="d-flex align-items-center justify-content-center w-100">
                <button type="button" class="btn btn-outline-success me-2"
                        @click.prevent="closeModal">
                    <small>Cancelar</small>
                </button>
                <button type="button" class="btn btn-success btn-not-red"
                        @click.prevent="closeModal">
                    <small>Verificar identidade</small>
                </button>
            </div>
        </template>
    </xgrow-modal-component>
</template>

<script>
import ModalComponent from "../../../js/components/ModalComponent";
import axios from "axios";

export default {
    name: "CoproductionsPendingModal",
    components: {
        "xgrow-modal-component": ModalComponent
    },
    props: {
        modal: {
            type: Object,
            required: true
        },
        changePage: {
            type: Function,
            required: true
        },
        closeModal: {
            type: Function,
            required: true
        }
    },
    emits: ["changePage", "reloadCoproduction"],
    data() {
        return {
            bankData: {
                client_bank: "",
                bank: "",
                branch: "",
                branch_check_digit: "",
                account: "",
                account_check_digit: ""
            }
        };
    },
    computed: {
        documentFormated: function () {
            if (this.bankData.document) {
                if (this.bankData.document.length == 11) {
                    // Apply CPF mask
                    return this.bankData.document
                        .replace(/(\d{3})(\d)/, "$1.$2")
                        .replace(/(\d{3})(\d)/, "$1.$2")
                        .replace(/(\d{3})(\d{1,2})$/, "$1-$2");
                } else {
                    // Apply CNPJ mask
                    return this.bankData.document
                        .replace(/^(\d{2})(\d)/, "$1.$2")
                        .replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3")
                        .replace(/\.(\d{3})(\d)/, ".$1/$2")
                        .replace(/(\d{4})(\d)/, "$1-$2");
                }
            }
        },
        accountIsValid: function () {
            return (this.bankData.account !== "");
            // return this.bankData.account !== "";
            // if (
            //     (this.bankData.bank != "" && this.bankData.bank != null) &&
            //     (this.bankData.branch != "" && this.bankData.branch != null)
            // ) {
            //     return true;
            // }
            //
            // return false;
        }
    },
    methods: {
        getCoproducerBankInformation: async function (platformId) {
            const url = coproducerBankInformation.replace(/:platformId/g, platformId);
            const res = await axios.get(url);
            this.bankData = res.data.response.data;
        },
        async updateCoproduction(status) {
            const coproduction = this.modal.item;
            const url = updateInviteURL.replace(/:idProducerProducts/g, coproduction.producer_products_id)
                .replace(/:producerId/g, coproduction.producer_id);

                try {
                    const res = await axios.post(url,
                        {status}
                    );

                    if (!res.data.error) {
                        successToast("Ação realizada com sucesso", "Convite recusado com sucesso");
                    }
                } catch (error) {
                    errorToast("Algo aconteceu", error.response.data.message);
                }

            this.closeModal();
            this.$emit("reloadCoproduction");
        }
    },
    watch: {
        modal: {
            deep: true,
            handler(_, newVal) {
                const modal = newVal;
                if (modal.isOpen && modal.item != null) {
                    this.getCoproducerBankInformation(modal.item.platform_id);
                }
            }
        }
    }
};
</script>

<style lang="scss">
.xgrow-modal-icon {
    color: #E28A22;
    font-size: 5rem;
}

.btn-not-red:hover {
    background-color: #638110 !important;
}
</style>
