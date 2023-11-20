<template>
  <div>
    <PieChart :chartData="data" :width="300" :height="300" :options="options" />
  </div>
</template>

<script>
import { defineComponent } from "vue";
import Chart from "chart.js/auto";
import { PieChart } from "vue-chart-3";

export default defineComponent({
  name: "challenge-component",
  components: { PieChart },
  props: {
    labels: {
      required: true,
      type: Array,
    },
    values: {
      required: true,
      type: Array,
    },
    colors: {
      required: true,
      type: Array,
    },
  },

  setup(props) {
    const data = {
      labels: props.labels,
      datasets: [{ data: props.values, backgroundColor: props.colors }],
    };

    const options = {
      elements: {
        arc: {
          borderWidth: 0,
        },
      },
      plugins: {
        legend: {
          labels: {
            color: "#ffffff",
            boxWidth: 12
          },
        },
      },
    };

    return { data, options };
  },
});
</script>

<style lang="scss" scoped>
</style>