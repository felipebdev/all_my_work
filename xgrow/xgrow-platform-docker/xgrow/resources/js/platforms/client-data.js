import UploadDropzoneImage from "../components/UploadDropzoneImage";
import StatusModalComponent from "../components/StatusModalComponent";

import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

import Maska from "maska";
import axios from "axios";
import {cpf, cnpj} from "cpf-cnpj-validator";
import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from '../config/elk-apm';

/** Upload component */

const vue = require("vue");

const app = vue.createApp({
    delimiters: ["[[", "]]"],
    data() {
        return {
            method: "create",
            statusLoading: false,
            status: "loading",
            activeScreen: "bankDataScreen",
            step: 1,
            /** Bank data */
            banks: [
                {id: 1, name: "Banco do Brasil"},
                {id: 2, name: "Banco Xgrow"}
            ],
            accountTypes: [
                {id: "checking", name: "Conta Corrente"},
                {id: "savings", name: "Conta Poupança"}
            ],
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
            document: {
                type: 1,
                front: null,
                back: null
            },
            /** Address Data */
            address: {
                zipcode: "",
                state: "",
                city: "",
                address: "",
                number: "",
                neighbourhood: "",
                complement: ""
            }
        };
    },
    methods: {
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
        /** Save bank data */
        saveBankData: async function () {
            if (await this.verifyFields()) return true;
            this.statusLoading = true;
            this.status = "saving";

            try {
                const headers = {headers: {"Content-Type": "multipart/form-data"}};
                const formData = new FormData();
                formData.append("holder_name", this.bank.name);
                formData.append("client_identity", this.bank.clientIdentity);
                formData.append("account_type", this.bank.accountType);
                formData.append("bank", this.bank.bank);
                formData.append("branch", this.bank.agency);
                formData.append("branch_check_digit", this.bank.agencyDigit);
                formData.append("account", this.bank.account);
                formData.append("account_check_digit", this.bank.accountDigit);

                await axios.post(postClientURL, formData, headers);
                this.statusLoading = false;
                this.status = "loading";
                successToast("Ação realizada com sucesso", "Dados bancários atualizados com sucesso!");
                await this.nextStep("addressScreen");
                return true;
            } catch (e) {
                this.statusLoading = false;
                this.status = "loading";
                errorToast("Falha ao realizar ação", e.response.data.message.toString());
                if (e.response.status >= 500) {
                    errorToast("Falha ao realizar ação", e.response.statusText);
                } else {
                    const error = e.response.data.errors;
                    this.isKeyExists(error, "holder_name");
                    this.isKeyExists(error, "client_identity");
                    this.isKeyExists(error, "account_type");
                    this.isKeyExists(error, "bank");
                    this.isKeyExists(error, "branch");
                    this.isKeyExists(error, "branch_check_digit");
                    this.isKeyExists(error, "account");
                    this.isKeyExists(error, "account_check_digit");
                }
            }
        },
        /** Save address data */
        saveAddressData: async function () {
            if (await this.verifyFields()) return true;
            this.statusLoading = true;
            this.status = "saving";

            try {
                const headers = {headers: {"Content-Type": "multipart/form-data"}};
                const formData = new FormData();
                formData.append("zipcode", this.address.zipcode);
                formData.append("address", this.address.address);
                formData.append("number", this.address.number);
                formData.append("district", this.address.neighbourhood);
                formData.append("city", this.address.city);
                formData.append("state", this.address.state);
                formData.append("complement", this.address.complement);

                await axios.post(updateAddressURL, formData, headers);
                this.statusLoading = false;
                this.status = "loading";
                successToast("Ação realizada com sucesso", "Dados de endereço atualizados com sucesso!");
                await this.nextStep("uploadDocsScreen");
                return true;
            } catch (e) {
                this.statusLoading = false;
                this.status = "loading";
                errorToast("Falha ao realizar ação", e.response.data.message.toString());
                if (e.response.status >= 500) {
                    errorToast("Falha ao realizar ação", e.response.statusText);
                } else {
                    const error = e.response.data.errors;
                    this.isKeyExists(error, "zipcode");
                    this.isKeyExists(error, "address");
                    this.isKeyExists(error, "number");
                    this.isKeyExists(error, "district");
                    this.isKeyExists(error, "city");
                    this.isKeyExists(error, "state");
                }
            }
        },
        /** Upload documents */
        saveUploadDocuments: async function () {
            if (await this.verifyFields()) return true;
            this.statusLoading = true;
            this.status = "saving";

            try {
                const headers = {headers: {"Content-Type": "multipart/form-data"}};
                const formData = new FormData();
                formData.append("cpf", this.bank.clientIdentity);

                if (this.document.front) {
                    const uploadDocumentURL = validateDocumentURL.replace(/:side/g, "front");
                    formData.append("file", this.document.front);
                    await axios.post(uploadDocumentURL, formData, headers);
                }
                // if (this.document.back) {
                //     const uploadDocumentURL = validateDocumentURL.replace(/:sideAs/g, "back");
                //     formData.append("file", this.document.back);
                //     await axios.post(uploadDocumentURL, formData, headers);
                // }

                this.statusLoading = false;
                this.status = "loading";
                successToast("Ação realizada com sucesso", "Documentos verificados com sucesso!");
                this.previousStep("addressScreen");
                this.returnToDashboard();
                return true;
            } catch (e) {
                this.statusLoading = false;
                this.status = "loading";
                errorToast("Falha ao realizar ação", e.response.data.message.toString());
                if (e.response.status >= 500) {
                    errorToast("Falha ao realizar ação", e.response.statusText);
                } else {
                    const error = e.response.data.errors;
                    this.isKeyExists(error, "front");
                    this.isKeyExists(error, "back");
                }
            }
        },
        /** Verify if has error */
        verifyFields: async function () {
            if (this.step === 1) {
                if (this.bank.name.trim() === "") {
                    errorToast("Algum erro aconteceu!", "O nome do titular não pode ficar em branco.");
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
                if (this.bank.agency.trim() === "") {
                    errorToast("Algum erro aconteceu!", "A agência não pode ficar em branco.");
                    return true;
                }
                if (this.bank.account.trim() === "") {
                    errorToast("Algum erro aconteceu!", "A conta não pode ficar em branco.");
                    return true;
                }
                if (this.bank.accountDigit.length === 0) {
                    errorToast("Algum erro aconteceu!", "O dígito da conta não pode ficar em branco. Caso não haja ou é x, utilizar 0.");
                    return true;
                }
            }
            if (this.step === 2) {
                if (this.address.zipcode.trim() === "") {
                    errorToast("Algum erro aconteceu!", "O CEP não pode ficar em branco.");
                    return true;
                }
                if (this.address.state.trim() === "") {
                    errorToast("Algum erro aconteceu!", "O estado não pode ficar em branco.");
                    return true;
                }
                if (this.address.city.trim() === "") {
                    errorToast("Algum erro aconteceu!", "A cidade não pode ficar em branco.");
                    return true;
                }
                if (this.address.address.trim() === "") {
                    errorToast("Algum erro aconteceu!", "O logradouro não pode ficar em branco.");
                    return true;
                }
                if (this.address.neighbourhood.trim() === "") {
                    errorToast("Algum erro aconteceu!", "O bairro não pode ficar em branco.");
                    return true;
                }
            }
            if (this.step === 3) {
                if (this.document.type < 1) {
                    errorToast("Algum erro aconteceu!", "O tipo de documento selecionado é inválido.");
                    return true;
                }
                if (this.document.front === null && this.document.back === null) {
                    errorToast("Algum erro aconteceu!", "Você deve enviar ao menos um documento.");
                    return true;
                }
                if (this.document.front === null) {
                    errorToast("Algum erro aconteceu!", "A frente do documento é obrigatória.");
                    return true;
                }
            }
        },
        /** Verify error on backend */
        isKeyExists: function (obj, key) {
            if (obj.hasOwnProperty(key)) {
                errorToast("Falha ao realizar ação", obj[key][0].toString());
            }
            return false;
        },
        /** Receive front document for UploadDropzoneImage Plugin */
        receiveFrontImage: function (obj) {
            this.document.front = obj.file.files[0];
        },
        /** Receive back document for UploadDropzoneImage Plugin */
        receiveBackImage: function (obj) {
            this.document.back = obj.file.files[0];
        },
        /** Search CEP on ViaCEP WebService */
        getCEP: async function () {
            const zipcode = this.address.zipcode.trim().replace(/[^0-9]/g, "");
            let viaCepURL = "https://viacep.com.br/ws/" + this.address.zipcode + "/json/";

            if (!zipcode) return errorToast("Falha ao realizar ação", "O CEP não foi informado");
            try {
                this.statusLoading = true;
                let res = await axios.get(viaCepURL);
                this.statusLoading = false;
                if (res.data.erro) return errorToast("Falha ao realizar ação", "O CEP informado é inválido");
                this.address.state = res.data.uf;
                this.address.city = res.data.localidade;
                this.address.address = res.data.logradouro;
                this.address.neighbourhood = res.data.bairro;
                this.address.complement = res.data.complemento;
            } catch (e) {
                this.statusLoading = false;
                errorToast("Falha ao realizar ação", e.toString());
            }
        },
        /** Get Bank List */
        getBanks: async function () {
            const res = await axios.get(banksURL);
            this.banks = res.data.map(item => {
                return {value: item.code, label: `${item.code} - ${item.bank}`};
            });
        },
        /** Get user info */
        getUserInfo: async function () {
            const res = await axios.post(clientURL);
            if (res.status === 200) {
                this.bank.clientIdentity = res.data.response.clientIdentity;
                this.bank.name = res.data.response.name;
                this.clientType = res.data.response.type;
            } else {
                errorToast("Falha ao realizar ação", "Erro ao carregar dados do usuário.");
            }
        },
        /** Redirect to dashboard */
        returnToDashboard: function () {
            window.location.href = "/";
        },
    },
    /** Created lifecycle */
    async created() {
        this.statusLoading = true;
        await this.getUserInfo();
        await this.getBanks();
        await new Promise(resolve => setTimeout(resolve, 500));
        this.statusLoading = false;
    }
});

app.component("upload-dropzone-image", UploadDropzoneImage);
app.component("status-modal-component", StatusModalComponent);
app.component("multiselect-component", Multiselect);

app.use(Maska);
app.use(ApmVuePlugin, apmConfig);
app.mount("#clientDataFlow");
