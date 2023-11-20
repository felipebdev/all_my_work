<template>

    <status-modal-component :is-open="loading" :status="status"></status-modal-component>

    <div class="xgrow-tabs nav nav-tabs mb-3" id="nav-tab">
        <a class="xgrow-tab-item nav-item nav-link" id="nav-bank-data-tab"
           :class="{'active': activeScreen.toString() === 'bankDataScreen'}">
            Dados bancários
        </a>

        <a class="xgrow-tab-item nav-item nav-link" id="nav-upload-docs-tab"
           :class="{'active': activeScreen.toString() === 'uploadDocsScreen'}">
            Upload de documento
        </a>
    </div>

    <div v-if="activeScreen.toString() === 'bankDataScreen'" id="bankDataScreen">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <h3 class="my-0">Dados bancários</h3>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-12 py-3">
                <p class="xgrow-panel-subtitle">Esta conta bancária será utilizada para depositar seus lucros.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 py-3">
                <p class="xgrow-panel-subtitle"><b>Conta bancária</b></p>
            </div>

            <div class="col-sm-12 col-md-6">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input id="name" autocomplete="off" spellcheck="false" type="text"
                           class="mui--is-touched mui--is-dirty mui--is-not-empty" v-model="bank.name">
                    <label for="name">Nome do titular da conta</label>
                </div>
            </div>

            <div class="col-sm-12 col-md-3">
                <div class="xgrow-form-control mb-2">
                    <multiselect-component
                        id="userType" v-model="clientType" :options="userTypes"
                        @select="null" :canClear="false" placeholder="Selecione uma opção"/>
                </div>
            </div>

            <div class="col-sm-12 col-md-3">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input id="clientIdentity" autocomplete="off" spellcheck="false" type="text"
                           class="mui--is-touched mui--is-dirty mui--is-not-empty" v-model="bank.clientIdentity"
                           v-maska="clientType === 'F' ? '###.###.###-##' : '##.###.###/####-##'">
                    <label for="clientIdentity">{{ clientType === "F" ? "CPF" : "CNPJ" }}</label>
                </div>
            </div>

            <div class="col-sm-12 col-md-6">
                <div class="xgrow-form-control mb-2">
                    <multiselect-component
                        id="accountType" v-model="bank.accountType" :options="accountTypes"
                        @select="null" :canClear="false" placeholder="Selecione uma opção"/>
                </div>
            </div>

            <div class="col-sm-12 col-md-6">
                <div class="xgrow-form-control mb-2">
                    <multiselect-component
                        id="bank" style="background: transparent;color: #FFFFFF;"
                        v-model="bank.bank" :options="banks" @select="null" :canClear="false"
                        :searchable="true" placeholder="Selecione uma opção"/>
                </div>
            </div>

            <div class="col-sm-12 col-md-4 mt-2">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input id="agency" autocomplete="off" spellcheck="false" type="text" v-maska="'####'"
                           class="mui--is-touched mui--is-dirty mui--is-not-empty" v-model="bank.agency">
                    <label for="agency">Agência</label>
                </div>
            </div>

            <div class="col-sm-12 col-md-2 mt-2">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input id="agencyDigit" autocomplete="off" spellcheck="false" type="text" v-maska="'#'"
                           class="mui--is-touched mui--is-dirty mui--is-not-empty" v-model="bank.agencyDigit">
                    <label for="agencyDigit">Dígito</label>
                </div>
                <small>Use 0 (zero) para dígito em branco ou letra</small>
            </div>

            <div class="col-sm-12 col-md-4 mt-2">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input id="account" autocomplete="off" spellcheck="false" type="text" v-maska="'############'"
                           class="mui--is-touched mui--is-dirty mui--is-not-empty" v-model="bank.account">
                    <label for="account">Conta</label>
                </div>
            </div>

            <div class="col-sm-12 col-md-2 mt-2">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input id="accountDigit" autocomplete="off" spellcheck="false" type="text" v-maska="'#'"
                           class="mui--is-touched mui--is-dirty mui--is-not-empty" v-model="bank.accountDigit">
                    <label for="accountDigit">Dígito</label>
                </div>
                <small>Use 0 (zero) para dígito em branco ou letra</small>
            </div>

            <div class="border-top border-secondary mt-5">
                <div class="d-flex py-4 px-0 justify-content-between flex-wrap gap-3">
                    <button class="btn xgrow-button-secondary button-cancel" @click="changePage('coproducer.pending')">
                        Voltar
                    </button>
                    <button class="xgrow-button xgrow-button-custom " @click="saveBankData">
                        Salvar e prosseguir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div v-if="activeScreen.toString() === 'uploadDocsScreen'" id="uploadDocsScreen">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <h3 class="my-0">Upload de documento (com foto)</h3>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-12 py-3">
                <p class="xgrow-panel-subtitle">
                    Este documento será utilizado par confirmar a veracidade de seus dados informados.
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 py-3">
                <p class="xgrow-panel-subtitle"><b>Escolha o documento a ser enviado</b></p>
            </div>

            <div class="col-sm-12 col-md-12">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <select id="documentTypes" class="xgrow-select" v-model="document.type">
                        <option v-for="documentType in documentTypes" :value="documentType.id" :key="documentType.id">
                            {{ documentType.name }}
                        </option>
                    </select>
                    <label for="accountType">Selecione o tipo de documento</label>
                    <span class="caret"></span>
                </div>
            </div>

            <div class="col-sm-12 col-md-6">
                <upload-dropzone-image
                    ref="front" refer="front" title="Imagem do lado do documento que contenha o CPF"
                    side="" btn-title="Buscar arquivo" @send-image="receiveImage" @clear="document.front=null">
                </upload-dropzone-image>
            </div>

            <div class="border-top border-secondary mt-5">
                <div class="d-flex py-4 px-0 justify-content-between flex-wrap gap-3">
                    <button class="btn xgrow-button-secondary button-cancel" @click="previousStep('bankDataScreen')">
                        Voltar
                    </button>
                    <button class="xgrow-button xgrow-button-custom" @click="saveDocData">
                        Finalizar
                    </button>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import UploadDropzoneImage from "../../../js/components/UploadDropzoneImage";
import StatusModalComponent from "../../../js/components/StatusModalComponent";

import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

import {maska} from "maska";
import axios from "axios";
import {cnpj, cpf} from "cpf-cnpj-validator";

export default {
    name: "CoproducerFlow",
    components: {
        "multiselect-component": Multiselect,
        "upload-dropzone-image": UploadDropzoneImage,
        "status-modal-component": StatusModalComponent
    },
    props: {
        coproducerData: {required: false}
    },
    emits: ["returnCoproducer", "updatePendingList"],
    directives: {maska},
    data() {
        return {
            activeScreen: "bankDataScreen",
            step: 1,
            loading: false,
            status: "loading",

            /** Selects */
            userTypes: [
                {value: "F", label: "Pessoa Física"},
                {value: "J", label: "Pessoa Jurídica"}
            ],
            accountTypes: [
                {value: "checking", label: "Conta Corrente"},
                {value: "savings", label: "Conta Poupança"}
            ],
            banks: [],

            /** Bank data */

            clientType: "F",
            bank: {
                name: "",
                clientIdentity: "",
                accountType: "checking",
                bank: "001",
                agency: "",
                agencyDigit: "",
                account: "",
                accountDigit: 0
            },

            /** Document Upload */
            documentTypes: [
                {id: 1, name: "CNH"},
                {id: 2, name: "RG"},
                {id: 3, name: "NOVO RG"},
                {id: 4, name: "CPF"}
            ],
            document: {type: 1, front: null}
        };
    },
    methods: {
        /** Change screen by value */
        changePage: function (screen) {
            this.$emit("returnCoproducer", screen);
        },
        /** Next Step */
        nextStep: async function (screenName) {
            if (await this.verifyFields()) return true;
            this.activeScreen = screenName;
            this.step++;
        },
        /** Previous step */
        previousStep: function (screenName) {
            this.activeScreen = screenName;
            this.step--;
        },
        /** Get Bank List */
        getBanks: async function () {
            const res = await axios.get(banksURL);
            this.banks = res.data.map(item => {
                return {value: item.code, label: item.bank};
            });
        },
        /** Get user info */
        getUserInfo: async function () {
            try {
                const res = await axios.post(clientURL);
                this.bank.name = res.data.response.name;
            } catch (e) {
                console.log(e);
            }

        },
        /** Save bank data */
        saveBankData: async function () {
            if (await this.verifyFields()) return true;
            this.loading = true;
            this.status = "saving";

            try {
                const formData = new FormData();
                formData.append("holder_name", this.bank.name);
                formData.append("document", this.bank.clientIdentity.replaceAll(".", "").replaceAll("-", "").replaceAll("/", ""));
                formData.append("account_type", this.bank.accountType);
                formData.append("document_type", this.clientType);
                formData.append("bank", this.bank.bank);
                formData.append("branch", this.bank.agency);
                formData.append("branch_check_digit", this.bank.agencyDigit);
                formData.append("account", this.bank.account);
                formData.append("account_check_digit", this.bank.accountDigit);

                let updateBankProducerUrl = updateBankProducerURL.replace(/:platformId/g, this.coproducerData.platformId);
                await axios.post(updateBankProducerUrl, formData);

                this.loading = false;
                this.status = "loading";
                successToast("Ação realizada com sucesso", "Dados bancários atualizados com sucesso!");
                await this.nextStep("uploadDocsScreen");
            } catch (e) {
                this.loading = false;
                this.status = "loading";
                errorToast("Falha ao realizar ação", e.response.data.message.toString());
                if (e.response.status >= 500) {
                    errorToast("Falha ao realizar ação", e.response.statusText);
                }
                if (e.response.status === 422) {
                    const error = e.response.data.errors;
                    this.isKeyExists(error, "holder_name");
                    this.isKeyExists(error, "document");
                    this.isKeyExists(error, "client_identity");
                    this.isKeyExists(error, "account_type");
                    this.isKeyExists(error, "bank");
                    this.isKeyExists(error, "branch");
                    this.isKeyExists(error, "branch_check_digit");
                    this.isKeyExists(error, "account");
                    this.isKeyExists(error, "account_check_digit");
                    this.isKeyExists(error, "status");
                }
            }
        },
        /** Save document data */
        saveDocData: async function () {
            if (await this.verifyFields()) return true;
            this.loading = true;
            this.status = "saving";

            try {
                if (this.document.front) {
                    const headers = {headers: {"Content-Type": "multipart/form-data"}};
                    const validateDocumentUrl = validateDocumentURL.replace(/:platformId/g, this.coproducerData.platformId);
                    const formData = new FormData();
                    formData.append("file", this.document.front);
                    await axios.post(validateDocumentUrl, formData, headers);

                    /** Update producer status */
                    let updateProducerUrl = updateProducerURL.replace(/:idProducerProducts/g, this.coproducerData.producerProductsId);
                    updateProducerUrl = updateProducerUrl.replace(/:producerId/g, this.coproducerData.producerId);
                    const formProducerData = new FormData();
                    formProducerData.append("status", "active");
                    await axios.post(updateProducerUrl, formProducerData);
                }

                this.loading = false;
                this.status = "loading";
                this.clearFields();
                this.$emit("updatePendingList");
                await this.changePage("coproducer.my");
                return true;
            } catch (e) {
                this.loading = false;
                this.status = "loading";
                errorToast("Falha ao realizar ação", e.response.data.message.toString());
                if (e.response.status >= 500) {
                    errorToast("Falha ao realizar ação", e.response.statusText);
                }
            }
        },
        clearFields: function () {
            this.step = this.document.type = 1;
            this.clientType = "F";
            this.bank.accountType = "checking";
            this.bank.bank = "001";
            this.bank.clientIdentity = this.bank.agency = this.bank.account = "";
            this.bank.accountDigit = 0;
            this.document.front = null;
        },
        /** Verify error on backend */
        isKeyExists: function (obj, key) {
            if (obj.hasOwnProperty(key)) {
                errorToast("Falha ao realizar ação", obj[key][0].toString());
            }
            return false;
        },
        /** Verify if has error */
        verifyFields: function () {
            if (this.step === 1) {
                if (this.bank.name.trim() === "") {
                    errorToast("Algum erro aconteceu!", "O nome do titular não pode ficar em branco.");
                    return true;
                }
                if (this.clientType !== "F" && this.clientType !== "J") {
                    errorToast("Algum erro aconteceu!", "O tipo da conta selecionada é inválido.");
                    return true;
                }
                if (this.bank.clientIdentity.trim() === "") {
                    errorToast("Algum erro aconteceu!", "O numero do documento não pode ficar em branco.");
                    return true;
                }
                if (this.clientType === "F" && !cpf.isValid(this.bank.clientIdentity)) {
                    errorToast("Algum erro aconteceu!", `O CPF informado é inválido.`);
                    return true;
                }
                if (this.clientType === "J" && !cnpj.isValid(this.bank.clientIdentity)) {
                    errorToast("Algum erro aconteceu!", `O CNPJ informado é inválido.`);
                    return true;
                }
                if (this.bank.accountType !== "checking" && this.bank.accountType !== "savings") {
                    errorToast("Algum erro aconteceu!", "O tipo da conta selecionada é inválido.");
                    return true;
                }
                if (this.bank.bank.trim() === "") {
                    errorToast("Algum erro aconteceu!", "O banco selecionado é inválido.");
                    return true;
                }
                if (this.bank.agency.length === 0) {
                    errorToast("Algum erro aconteceu!", "A agência não pode ficar em branco.");
                    return true;
                }
                if (this.bank.account.length === 0) {
                    errorToast("Algum erro aconteceu!", "A conta não pode ficar em branco.");
                    return true;
                }
                if (this.bank.accountDigit.length === 0) {
                    errorToast("Algum erro aconteceu!", "O dígito da conta não pode ficar em branco. Caso não haja ou é x, utilizar 0.");
                    return true;
                }
            }
            if (this.step === 2) {
                if (this.document.type < 1) {
                    errorToast("Algum erro aconteceu!", "O tipo de documento selecionado é inválido.");
                    return true;
                }
                if (this.document.front === null) {
                    errorToast("Algum erro aconteceu!", "O envio do documento válido é obrigatório.");
                    return true;
                }
            }
        },
        /** Receive front document for UploadDropzoneImage Plugin */
        receiveImage: function (obj) {
            this.document.front = obj.file.files[0];
        }
    },
    /** Created lifecycle */
    async created() {
        this.statusLoading = true;
        await this.getBanks();
        await this.getUserInfo();
        this.statusLoading = false;
    }
};
</script>

<style>
.xgrow-floating-input input {
    min-width: auto;
}

.xgrow-select {
    height: 65px !important;
}

.xgrow-select + label {
    color: var(--contrast-green3) !important;
    top: 0 !important;
    left: -4px !important;
    -webkit-transform: none !important;
    transform: none !important;
}

.xgrow-select {
    color: white !important;
    padding-top: 30px !important;
}

.xgrow-select + label + span.caret::before {
    content: '' !important;
    background: url('/xgrow-vendor/assets/img/caret.svg') no-repeat !important;
    top: 12px !important;
    right: 30px !important;
}

.multiselect-search {
    background: transparent !important;
    color: #FFFFFF !important;
}
</style>
