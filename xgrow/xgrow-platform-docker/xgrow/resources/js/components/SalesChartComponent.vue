<template>
  <card-container-component>
    <div class="row flex-wrap">
      <div class="col-sm-7">
        <p class="card-title mb-2">Gráfico de Vendas</p>
        <p class="card-subtitle mb-3">
          Acompanhe os detalhes suas vendas por produtos
        </p>
      </div>
      <div class="col-sm-5">
        <multiselect
          v-model="product"
          :options="products"
          @select="getData"
          @clear="clearProduct"
          placeholder="Todos os produtos"
          style="min-width: 190px"
        />
      </div>
    </div>
    <div class="card-content row flex-wrap mt-2">
      <LineChart :chartData="chartData" :options="options" />
    </div>
  </card-container-component>
</template>

<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import Chart from "chart.js/auto";
import { LineChart } from "vue-chart-3";
import CardContainerComponent from "../components/CardContainerComponent";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

export default defineComponent({
  name: "Home",
  components: { LineChart, CardContainerComponent, Multiselect },
  emits: ["saleChartByProduct"],
  props: {
    products: {
      required: true,
      type: [Object, null],
      default: null,
    },
    chartData: {
      required: true,
      type: [Object, null],
      default: [],
    },
  },
  setup(props, { emit }) {
    const COLORS = ["rgba(173, 255, 47, .2)"];

    function color(index) {
      return COLORS[index % COLORS.length];
    }

    /** Product Filter */
    const product = ref(null);

    const data = null;

    const options = {
      tension: 0.4,
      fill: true,
      borderColor: "rgba(173, 255, 47, .9)",
      backgroundColor: "rgba(173, 255, 47, .2)",
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    var label = context.dataset.label || '';

                    if (label) {
                        label += ': ';
                    }
                    if (context.parsed.y !== null) {
                        label += new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(context.parsed.y);
                    }
                    return label;
                }
            }
        },
      },
      interaction: {
        mode: 'index',
        intersect: false,
      },
    };

    /** Clear product data */
    function clearProduct() {
      emit("saleChartByProduct", 0);
    }

    /** Get all data */
    function getData() {
      emit("saleChartByProduct", product.value);
    }

    return { data, options, product, clearProduct, getData };
  },
});
</script>
<style lang="scss" scoped>
select {
  background: #21242d;
  border: 1px solid #ffffff;
  box-sizing: border-box;
  border-radius: 8px;
  color: #fff;
  height: 40px;
}

.card-title {
  font-weight: bold;
  font-size: 1rem;
}

.card-subtitle {
  opacity: 0.8;
  font-weight: lighter;
  font-size: 0.8rem;
}
</style>
