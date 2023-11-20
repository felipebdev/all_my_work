<template>
  <Line :chart-options="chartOptions" :chart-data="chartData" :chart-id="chartId" :width="width"
    :height="height" />
</template>

<script>
import { Line } from "vue-chartjs";
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Filler,
  Plugin,
} from "chart.js";

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  Filler,
  LinearScale
);

export default {
  name: "LineChart",
  components: {
    Line,
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
      default: "line-chart",
    },
    width: {
      type: Number,
      default: 400,
    },
    height: {
      type: Number,
      default: 220,
    },
    dataLabels: {
      type: Array,
      default: () => [ "01/12", "02/12", "03/12", "04/12", "05/12", "06/12", "07/12" ],
      required: true,
    },
    dataSet: {
      type: Array,
      default: () => [0, 500, 1000, 1500, 2000, 2500, 3000],
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
            suggestedMax: 2000,
            grid: { color: "#2A2E39", borderColor: "transparent", },
            gridLines: { zeroLineColor: "#E7E7E7", },
            ticks: { color: "#E7E7E7", scaleFontSize: 12, stepSize: 500, fontSize: 60, },
          },
          x: {
            grid: { display: false, },
            ticks: { color: "#E7E7E7", maxRotation: 90, minRotation: 0, scaleFontSize: 5, beginAtZero: true, },
          },
        },
      },
      required: false,
    },
  },
};
</script>
