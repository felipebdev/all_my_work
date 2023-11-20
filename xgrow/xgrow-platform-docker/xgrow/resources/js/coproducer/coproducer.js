import StatusModalComponent from "../components/StatusModalComponent";
import Table from "../components/Datatables/Table";
import CoproductionsOwner from "../../views/coproducer/components/CoproductionsOwner";
import TransactionTransactionComponent from "../../views/coproducer/components/TransactionTransactionComponent";
import SalesWithdrawnComponent from "../../views/coproducer/components/SalesWithdrawnComponent";
import CoproductionsPending from "../../views/coproducer/components/CoproductionsPending";
import TransactionNoLimitComponent from "../../views/coproducer/components/TransactionNoLimitComponent";
import CoproducerFlow from "../../views/coproducer/components/CoproducerFlow";
import moment from "moment";
import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from '../config/elk-apm';

const vue = require("vue");

const app = vue.createApp({
    delimiters: ["[[", "]]"],
    data() {
        return {
            method: "create",
            loading: false,
            status: "loading",
            activeScreen: "coproducer.my",
            platformId: null,
            dataFlow: {
                platformId: null,
                producerId: null,
                producerProductsId: null
            }
        };
    },
    methods: {
        /** Change screen by value */
        changePage: function (screen, closeMenu = false) {
            this.activeScreen = screen.toString();

            if (closeMenu) {
                document.querySelector('[data-bs-target="#navbarNav"]').click()
            }
        },
        /** Hack for load button */
        goToProducerArea: function () {
            const menus = document.querySelectorAll(".list-group-item.list-group-item-action.active");
            menus.forEach(item => item.classList.remove("active"));
            this.menuController("hide");
            document.getElementById("coProducerButton").classList.add("active");
            this.activeScreen = "coproducer.my";
        },
        /** Hack for load button */
        loadTabButtons: function () {
            this.activeClick("linkWithdraw", "sales.withdraw");
            this.activeClick("linkTransaction", "transaction.transaction");
        },
        /** Hack for click button */
        activeClick: function (dataId, page) {
            const links = document.querySelectorAll(`[data-id="${dataId}"]`)

            Array.from(links).forEach((element) => {
                element.onclick = () => {
                    const menus = document.querySelectorAll(".list-group-item.list-group-item-action.active");
                    menus.forEach(item => item.classList.remove("active"));
                    this.changePage(page, true);
                    element.classList.add('active');
                }
            })
        },
        /** Get Id */
        chargeDataFlow: function (item) {
            this.dataFlow.platformId = item.platform_id;
            this.dataFlow.producerId = item.producer_id;
            this.dataFlow.producerProductsId = item.producer_products_id;
        },
        /** Get Id */
        getId: async function (id) {
            this.platformId = id;
            const menus = document.querySelectorAll(".list-group-item.list-group-item-action.active");
            menus.forEach(item => item.classList.remove("active"));
            document.getElementById("linkWithdraw").classList.add("active");
            document.getElementById("salesCoproducer").classList.add("show");
            this.menuController("show");
            this.changePage("sales.withdraw");
            await this.$refs.salesWithdraw.getWithdrawList(id);
            await this.$refs.transactionTransaction.getTransactionList(id);
        },
        /** Trigger reload in coproduction owner */
        reloadCoproductionsOwner: async function () {
            // await this.reloadCoproductionsOwner();
            await this.$refs.coproductionsOwner.getCoproducers();
        },
        /** Trigger reload in coproduction pending */
        reloadCoproductionsPending: async function () {
            // await this.reloadCoproductionsOwner();
            await this.$refs.coproductionsPending.getCoproducers();
        },
        /** HiddenMenu */
        menuController: function (status) {
            const menus = document.querySelectorAll(".cop-menu");
            const startButtons = document.querySelectorAll(".start-button");
            if (status === "hide") {
                startButtons.forEach(item => item.classList.remove("hidden"));
                menus.forEach(item => item.classList.add("d-none"));
            } else {
                menus.forEach(item => item.classList.remove("d-none"));
                startButtons.forEach(item => item.classList.add("hidden"));
            }
        }
    },

    /** Created lifecycle */
    async created() {
        this.menuController("hide");
        this.loadTabButtons();
    }
});

app.component("status-modal-component", StatusModalComponent);
app.component("xgrow-table-component", Table);
app.component("coproductions-owner-component", CoproductionsOwner);
app.component("transaction-transaction-component", TransactionTransactionComponent);
app.component("sales-withdrawn-component", SalesWithdrawnComponent);
app.component("coproductions-pending-component", CoproductionsPending);
app.component("transaction-no-limit-component", TransactionNoLimitComponent);
app.component("coproducer-flow", CoproducerFlow);

/** Filter user for datetime format */
app.config.globalProperties.$filters = {
    formatDateTimeBR(value) {
        return moment(value).format("DD/MM/YYYY HH:mm:ss");
    },
    formatDateBR(value) {
        return moment(value).format("DD/MM/YYYY");
    },
    modifyStatus(value) {
        if (value === "success") return "<span class=\"xgrow-ds-badge xgrow-ds-badge-success\">realizada</span>";
        if (value === "fail") return "<span class=\"xgrow-ds-badge xgrow-ds-badge-danger\">falho</span>";
        if (value === "recipient_failed") return "<span class=\"xgrow-ds-badge xgrow-ds-badge-danger\">falha</span>";
        if (value === "pending") return "<span class=\"xgrow-ds-badge xgrow-ds-badge-warning\">pendente</span>";
    },
    modifyPaymentStatus(value) {
        if (value === "paid") return "<span class=\"xgrow-ds-badge xgrow-ds-badge-success\">Pago</span>";
        if (value === "fail") return "<span class=\"xgrow-ds-badge xgrow-ds-badge-danger\">Falho</span>";
        if (value === "failed") return "<span class=\"xgrow-ds-badge xgrow-ds-badge-danger\">Falha no pagamento</span>";
        if (value === "canceled") return "<span class=\"xgrow-ds-badge xgrow-ds-badge-danger\">Cancelado</span>";
        if (value === "expired") return "<span class=\"xgrow-ds-badge xgrow-ds-badge-danger\">Expirado</span>";
        if (value === "pending") return "<span class=\"xgrow-ds-badge xgrow-ds-badge-warning\">Pendente</span>";
        if (value === "chargeback") return "<span class=\"xgrow-ds-badge xgrow-ds-badge-warning\">Chargeback</span>";
        if (value === "refunded") return "<span class=\"xgrow-ds-badge xgrow-ds-badge-danger\">Estornado</span>";
    },
    modifyWithdrawStatus(value) {
        if (value === "transferred") return "<span class=\"xgrow-ds-badge xgrow-ds-badge-success\">Realizado</span>";
        if (value === "canceled") return "<span class=\"xgrow-ds-badge xgrow-ds-badge-danger\">Cancelado</span>";
        if (value === "failed") return "<span class=\"xgrow-ds-badge xgrow-ds-badge-danger\">Falha</span>";
        if (value === "pending") return "<span class=\"xgrow-ds-badge xgrow-ds-badge-warning\">Pendente</span>";
        if (value === "processing") return "<span class=\"xgrow-ds-badge xgrow-ds-badge-warning\">Processando</span>";
    },
    modifyPaymentMethod(value) {
        if (value === "billet" || value === "boleto") return "<span>Boleto</span>";
        if (value === "pix") return "<span>Pix</span>";
        if (value === "credit_card") return "<span>Cartão de Crédito</span>";
    },
    formatCurrency: function (value) {
        return value.toLocaleString("pt-br", { style: "currency", currency: "BRL" });
    },
    formatBRLCurrency: function (val) {
        return new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(val);
    }
};

app.use(ApmVuePlugin, apmConfig);

app.mount("#coproducer");
