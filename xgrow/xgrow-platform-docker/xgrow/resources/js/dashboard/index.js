import {createApp} from "vue";

import axios from "axios";
import CardComponent from "../components/CardComponent";
import SecondCardComponent from "../components/SecondCardComponent";
import RevenueComponent from "../components/RevenueComponent";
import CarouselComponent from "../components/CarouselComponent";
import SalesChartComponent from "../components/SalesChartComponent";
import SubscriberComponent from "./components/SubscriberComponent";
import DashboardFilter from "../components/DashboardFilter";
import MyStudentsComponent from "../components/MyStudentsComponent";
import VerifyDocument from "../../js/components/XgrowDesignSystem/Alert/VerifyDocument"
import DatePicker from "vue-datepicker-next";
import "vue-datepicker-next/index.css";
import "vue-datepicker-next/locale/pt-br";
import moment from "moment";
import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from '../config/elk-apm';

const app = createApp({
    delimiters: ["[[", "]]"],
    data() {
        return {
            verifyDocument,
            recipientStatusMessage,
            /** DateRange Picker */
            dateRange: null,
            dateRangeFormat: null,
            subscribers: {
                activeSubscribers: 0,
                activeSubscribersPercentage: 0,
                newSubscribers: 0,
                cancelledSubscribers: 0,
                automaticRegisteredPayingStudents: 0
            },
            sales: {
                paid: 0,
                refunds: 0,
                refundPercentage: 0
            },
            myStudents: {
                online: [],
                news: []
            },
            conversion: {
                credit_card: {
                    percentage: 0,
                    paid: 0,
                    generated: 0
                },
                pix: {
                    percentage: 0,
                    paid: 0,
                    generated: 0
                },
                boleto: {
                    percentage: 0,
                    paid: 0,
                    generated: 0
                }
            },
            carouselItems: [
                {
                    backgroundImg: "https://site-xgrow.vercel.app/assets/img/banner_1_.jpg",
                    title: "Título da novidade",
                    subtitle: "Descrição da novidade.",
                    link: "#"
                }
            ],
            products: null,
            chartData: null
        };
    },
    methods: {
        getSubscribersSummary: function () {
            const url = "/api/subscribers-bar-summary";
            const params = {period: this.dateRangeFormat, allDate: 0};
            axios.get(url, {params})
                .then(({ data }) => {
                    const {
                        activeSubscribers,
                        newSubscribers,
                        cancelledSubscribers,
                        activeSubscribersPercentage,
                        automaticRegisteredPayingStudents
                    } = data.response;

                    this.subscribers.activeSubscribers = activeSubscribers;
                    this.subscribers.activeSubscribersPercentage = activeSubscribersPercentage;
                    this.subscribers.newSubscribers = newSubscribers;
                    this.subscribers.cancelledSubscribers = cancelledSubscribers;
                    this.subscribers.automaticRegisteredPayingStudents = automaticRegisteredPayingStudents;
                })
                .catch(error => {
                    console.log(error);
                });
        },
        getTotalSales: function () {
            const url = "/api/reports/financial/total-transactions";
            const params = {period: this.dateRangeFormat, allDate: 0};
            let transactions = {
                paid: 0,
                pending: 0,
                canceled: 0,
                chargeback: 0,
                expired: 0
            };

            axios.get(url, {params})
                .then(response => {
                    response.data.data.forEach(transaction => {
                        transactions[transaction.status] = transaction.count;
                    });

                    this.sales.paid = transactions.paid;
                    this.sales.refunds = transactions.chargeback;
                    if (this.sales.refunds > 0 && this.sales.paid > 0) {
                        this.sales.refundPercentage = this.roundNumber((this.sales.refunds * 100) / this.sales.paid);
                    } else {
                        this.sales.refundPercentage = 0;
                    }
                })
                .catch(error => {
                    console.log(error);
                });
        },
        getConversion: function () {
            const url = "/api/reports/financial/generated-paid-transactions";
            const params = {period: this.dateRangeFormat, allDate: 0};
            const types = ["pix", "boleto", "credit_card"];

            types.forEach(type => {
                axios.get(url, {params: {...params, type_payment: type}})
                    .then(response => {
                        this.conversion[type].generated = response.data.data.generated;
                        this.conversion[type].paid = response.data.data.paid;

                        if (this.conversion[type].generated > 0)
                            this.conversion[type].percentage = this.roundNumber((this.conversion[type].paid * 100) / this.conversion[type].generated);
                        else
                            this.conversion[type].percentage = 0;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            });
        },
        roundNumber: function (number) {
            return (number % 1) !== 0 ? number.toFixed(2) : number;
        },
        startReports: async function () {
            await this.getTotalSales();
            await this.getConversion();
            await this.getSubscribersSummary();
            // await this.getProductSaleByPeriod(0);
            await this.getOnlineUsers();
            // await this.getNewUsers();
        },
        /** Start date on create **/
        startDate: function () {
            const endDate = new Date();
            const startDate = new Date(new Date().setDate(endDate.getDate() - 6));
            this.dateRange = [startDate, endDate];
            this.dateRangeFormat = moment(startDate).format("DD/MM/YYYY") + " - " + moment(endDate).format("DD/MM/YYYY");
        },
        convertDateTime: async function () {
            this.dateRangeFormat = moment(this.dateRange[0]).format("DD/MM/YYYY") + " - " + moment(this.dateRange[1]).format("DD/MM/YYYY");
            await this.startReports();
        },
        /** back date in one day  **/
        dateFilterBack: function () {
            this.navPeriod(-1);
        },
        /** back date in one day  **/
        dateFilterNext: function () {
            this.navPeriod(1);
        },

        /** convert date PT-BR to EN-US **/
        changeDate: function (datePT, days) {
            const dates = datePT.split("/");
            const dateEN = new Date(dates[2], dates[1] - 1, dates[0]);
            const date = new Date(dateEN);
            date.setDate(date.getDate() + days);
            return date;
        },

        /** navigation period **/
        navPeriod: function (day) {
            const period = this.dateRangeFormat.split(" - ");
            const startDate = this.changeDate(period[0], day);
            const endDate = this.changeDate(period[1], day);
            this.dateRange = [startDate, endDate];
            this.dateRangeFormat = moment(startDate).format("DD/MM/YYYY") + " - " + moment(endDate).format("DD/MM/YYYY");
            this.startReports();
        },

        /** Get products by platform */
        getProducts: async function () {
            const res = await axios.get(getAPIProducts);
            this.products = res.data.response.data;
        },

        /** Get Product Sale Report */
        getProductSaleByPeriod: async function (product_id) {
            const period = this.dateRangeFormat;
            const res = await axios.get("api/get-product-sale-by-period", {params: {product_id, period}});
            const data = res.data.response.data;
            this.chartData = {
                labels: data.label,
                datasets: data.datasets
            };
        },
        /** Get Sale Chart By Product */
        saleChartByProduct: function (product_id) {
            this.getProductSaleByPeriod(product_id);
        },
        /* GET ONLINE SUBSCRIBERS */
        getOnlineUsers: async function () {
            let {
                data: {
                    data
                }
            } = await axios.get("/api/get-online-users/");
            this.myStudents.online = data;
        },

        // /* GET NEW SUBSCRIBERS */
        // getNewUsers: async function () {
        //     const period = this.dateRangeFormat;
        //     let {
        //         data: {
        //             data
        //         }
        //     } = await axios.get("/api/subscribers-last-created-by-period/", {params: {period}});
        //     this.myStudents.news = data;
        // },
    },
    mounted() {
        window.app = this;
    },
    async created() {
        this.startDate();
        await this.getProducts();
        await this.startReports();
    }
});

// app.component("component-vue", ComponentVue);
app.component("card-component", CardComponent);
app.component("second-card-component", SecondCardComponent);
app.component("revenue-component", RevenueComponent);
app.component("carousel-component", CarouselComponent);
app.component("sales-chart-component", SalesChartComponent);
app.component("subscriber-component", SubscriberComponent);
app.component("dashboard-filter", DashboardFilter);
app.component("my-students-component", MyStudentsComponent);
app.component("xgrow-daterange-component", DatePicker);
app.component("verify-document", VerifyDocument);

/** Filter user for datetime format */
app.config.globalProperties.$filters = {
    formateDateBR(value) {
        return moment(value).format("DD/MM/YYYY HH:mm:ss");
    }
};

app.use(ApmVuePlugin, apmConfig);

app.mount("#dashboard");
