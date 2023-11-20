import StatusModalComponent from '../components/StatusModalComponent.vue';
import PillComponent from '../components/PillComponent.vue';
import CardComponent from '../components/CardComponent.vue';
import Podium from '../../views/gamification/components/Podium.vue';
import Leaderboard from '../../views/gamification/components/Leaderboard.vue';
import PhaseChartComponent from '../../views/gamification/components/PhaseChartComponent.vue';
import ChallengeComponent from '../../views/gamification/components/ChallengeComponent.vue';

import XGrowTour from '../functions/tour';

import { ApmVuePlugin } from '@elastic/apm-rum-vue';
import apmConfig from '../config/elk-apm';

import axios from "axios";
const vue = require('vue');

const app = vue.createApp({
    delimiters: ["[[", "]]"],
    data() {
        return {
            /** Loading */
            statusLoading: false,
            status: 'loading',

            /** Page controller */
            activeScreen: 'dashboard',
            activeContentScreen: '',

            method: "create", /** create, update */

            /** Gamification Status */
            coinsEarned: 0,
            coinsAverage: 0,
            statusEngagement: 0,

            /** Subscribers Leaderboard */
            winners: [],
            leaderboard: [],
            noengagement: [],

            /** Charts Data */
            charts: {
                mostCompleted: {
                    labels: [],
                    values: [],
                    colors: [
                        '#18A99A', '#1CBFAF', '#2FC6A5', '#41CD9D', '#55D498',
                        '#5CEDBA', '#6DF0B2', '#7DF3AD', '#8EF6AB', '#92F2E8'
                    ]
                },
                leastCompleted: {
                    labels: [],
                    values: [],
                    colors: [
                        '#F25C54', '#F4845F', '#FB9461', '#FFA464', '#FFB568',
                        '#FFC66D', '#FFD772', '#FFE779', '#FFF780', '#FFFF90'
                    ]
                },
            }
        };
    },
    watch: {
    },
    computed: {
        engagementFormated() {
            return `${this.statusEngagement}%`;
        }
    },
    methods: {
        // Change for choose screen
        changePage(screen, contentScreen) {
            this.activeScreen = screen.toString();
            this.activeContentScreen = contentScreen.toString();
        },

        async getApiData(url) {
            return await axios.get(`/gamification/dashboard/get/${url}`)
            .then(response => {
                return response.data.response;
            })
            .catch(error => {
                console.log(error);
                return {};
            });
        }
    },
    async created() {
        this.statusLoading = true;

        const status = await this.getApiData('status');
        this.coinsEarned = status.coinsEarned || 0;
        this.coinsAverage = status.coinsAverage || 0;
        this.statusEngagement = status.engagement || 0;

        const outstanding = await this.getApiData('outstanding');
        this.winners = [...outstanding.winners];
        this.leaderboard = [...outstanding.leaderboard];

        const noengagement = await this.getApiData('noengagement');
        this.noengagement = [...noengagement];

        const mostCompleted = await this.getApiData('challenges/most');
        this.charts.mostCompleted.labels = [...mostCompleted.data.labels];
        this.charts.mostCompleted.values = [...mostCompleted.data.values];

        const leastCompleted = await this.getApiData('challenges/least');
        this.charts.leastCompleted.labels = [...leastCompleted.data.labels];
        this.charts.leastCompleted.values = [...leastCompleted.data.values];

        this.statusLoading = false;

        XGrowTour.initialize(
            "gamificationTour",
            [
                {
                    elementId: '#gamification-status',
                    title: 'Cards de informações gerais',
                    customClasses: [],
                    description: `
                        <p>Nesta área, você pode ter uma visão geral de:</p>
                        <p>
                            <img src="/xgrow-vendor/assets/img/gamification/coin.svg">
                            Quantidade de Xcoins conquistados por todos os seus alunos;
                        </p>
                        <p>
                            <img src="/xgrow-vendor/assets/img/gamification/user-coin.svg">
                            Média de Xcoins cocnquistados por aluno;
                        </p>
                        <p><i class="fas fa-chart-bar yellow"></i> Porcentagem de seus alunos que estão engajados na gamificação da sua área de membros.</p>
                    `
                },
                {
                    elementId: '#leaderboard-1',
                    title: 'Ranking - alunos em destaque',
                    description: 'Nesta área, você pode ver o ranking dos seus 10 melhores alunos da área de membros, os 3 primeiros acima e os demais abaixo.',
                    position: 'right'
                },
                {
                    elementId: '#leaderboard-2',
                    title: 'Ranking - alunos sem engajamento',
                    description: 'Nesta área, você pode ver o ranking dos seus 10 alunos com pouco ou nenhum engajamento na área de membros.',
                    position: 'left'
                },
                {
                    elementId: '#phase-chart',
                    title: 'Alunos por fase',
                    description: 'Neste indicador, você pode ver em detalhes a quantidade de seus alunos por fase.',
                    position: 'top'
                },
                {
                    elementId: '#challenge-chart-1',
                    title: 'Desafios mais completados',
                    description: 'Neste indicador, você pode ver em detalhes quais desafios da sua área de membros foram os mais completados.',
                    position: 'right'
                },
                {
                    elementId: '#challenge-chart-2',
                    title: 'Desafios menos completados',
                    description: 'Neste indicador, você pode ver em detalhes quais desafios da sua área de membros foram os menos completados.',
                    position: 'left'
                }
            ]
        );
    }
});

app.component("status-modal-component", StatusModalComponent);
app.component("pill-component", PillComponent);
app.component("card-component", CardComponent);
app.component("podium", Podium);
app.component("leaderboard", Leaderboard);
app.component("phase-chart-component", PhaseChartComponent);
app.component("challenge-component", ChallengeComponent);

/** Filter user for datetime format */
app.config.globalProperties.$filters = {
    formateDateBR(value) {
        return moment(value).format('DD/MM/YYYY HH:mm:ss')
    }
}

console.log('hey')

app.use(ApmVuePlugin, apmConfig);

app.mount("#dashboard");
