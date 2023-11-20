<template>
    <Doughnut
        :chart-options="chartOptions"
        :chart-data="chartData"
        :chart-id="chartId"
        :width="width"
        :height="height"
    />
</template>

<script>
import { Doughnut } from "vue-chartjs";
import DoughnutInnerText from "chartjs-plugin-doughnut-innertext";

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    ArcElement,
    Plugin,
} from "chart.js";

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    ArcElement,
    DoughnutInnerText
);

export default {
    name: "PieChart",
    components: {
        Doughnut,
    },
    data: function () {
        return {
            chartData: {
                labels: this.dataLabels,
                datasets: this.dataSet,
            },
        };
    },
    props: {
        chartId: {
            type: String,
            default: "pie-chart",
        },
        width: {
            type: Number,
            default: 310,
        },
        dataSet: {
            type: Array,
            default: () => [],
            required: true,
        },
        dataLabels: {
            type: Array,
            default: () => [],
            required: true,
        },
        height: {
            type: Number,
            default: 158,
        },
        chartOptions: {
            type: Object,
            default: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "85%",
                layout: {
                    padding: {
                        top: 4,
                        bottom: 4,
                    },
                },
                plugins: {
                    legend: {
                        display: true,
                        position: "right",
                        labels: {
                            color: "#E7E7E7",
                            usePointStyle: true,
                            pointStyle: "circle",
                            boxWidth: 8,
                        },
                        onClick: () => null
                    },
                    tooltip: {
                        enabled: false,
                    }
                },
                centerText: {
                    color: "#E7E7E7",
                    value: `Example`,
                    fontSizeAdjust: -0.2,
                }
            },
            required: false
        }
    },
};
</script>
