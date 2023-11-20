<template>
    <Bar :chart-options="chartOptions" :chart-data="chartData" :chart-id="chartId" :plugins="plugins"
        :css-classes="cssClasses" :styles="styles" :width="width" :height="height" />
</template>

<script>
import { Bar } from "vue-chartjs";

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    Plugin,
} from "chart.js";

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
);

export default {
    name: "BarChart",
    components: {
        Bar,
    },
    data: function () {
        return {
            chartData: {
                labels: this.dataLabels,
                datasets: this.dataSet
            },
        };
    },
    props: {
        chartId: {
            type: String,
            default: "bar-chart",
        },
        width: {
            type: Number,
            default: 400,
        },
        height: {
            type: Number,
            default: 158,
        },
        cssClasses: {
            default: "",
            type: String,
        },
        styles: {
            type: Object,
            default: () => { },
        },
        plugins: {
            type: Array,
            default: () => [],
        },
        dataLabels: {
            type: Array,
            default: () => [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December",
            ],
            required: true
        },
        dataSet: {
            type: Array,
            default: () => [
                {
                    borderRadius: { topLeft: 5, topRight: 5 },
                    backgroundColor: "#6677A5",
                    data: [2000, 4000, 12, 39, 10, 40, 39, 80, 40, 20, 12, 11]
                },
            ],
            required: true,
        },
        chartOptions: {
            type: Object,
            default: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        min: 0,
                        grid: {
                            color: '#2A2E39',
                            borderColor: 'transparent',
                        },
                        ticks: {
                            color: '#E7E7E7',
                            scaleFontSize: 12,
                            stepSize: 1,
                            fontSize: 60,
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#E7E7E7',
                            maxRotation: 90,
                            minRotation: 0,
                            scaleFontSize: 5,
                        }
                    }
                },
                required: false,
            }
        },
    }
};
</script>
