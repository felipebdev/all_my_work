<template>
  <div>
    <BarChart :chartData="data" :options="options" />
  </div>
</template>

<script>
import { defineComponent } from "vue";
import Chart from "chart.js/auto";
import { BarChart } from "vue-chart-3";

export default defineComponent({
  name: "phase-chart-component",
  components: { BarChart },

  setup() {
    const data = {
      labels: [
        "Fase 1",
        "Fase 2",
        "Fase 3",
        "Fase 4",
        "Fase 5",
        "Fase 6",
        "Fase 7",
        "Fase 8",
        "Fase 9",
        "Fase 10",
      ],
      datasets: [
        {
          data: [1700, 1000, 1600, 1700, 2300, 500, 1600, 2500, 1700, 1300],
          backgroundColor: [
            "#A9D6E5",
            "#89C2D9",
            "#61A5C2",
            "#468FAF",
            "#2C7DA0",
            "#2A6F97",
            "#014F86",
            "#01406C",
            "#003459",
            "#012A4A",
          ],
        },
      ],
    };

    const options = {
      plugins: {
        legend: {
          display: false,
        }
      },
    };

    return { data, options };
  },
});
</script>

<style lang="scss" scoped>
</style>