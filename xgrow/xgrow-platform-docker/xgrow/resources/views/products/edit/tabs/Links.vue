<template>
  <div>
    <div class="xgrow-tabs nav nav-tabs mb-3" id="nav-tab" role="tablist">
        <a class="xgrow-tab-item nav-item nav-link active" id="nav-links-main-tab" data-bs-toggle="tab"
           href="#nav-links-main" role="tab" aria-controls="nav-links-tables" aria-selected="true">
            Links principais
        </a>

        <a class="xgrow-tab-item nav-item nav-link" id="nav-links-additional-tab" data-bs-toggle="tab" href="#nav-links-additional" role="tab"
           aria-controls="nav-links-tables" aria-selected="false">
            Links adicionais
        </a>
    </div>

    <div class="tab-content" id="nav-tabContent">
        <Main />
        <Additional />
    </div>
  </div>
</template>

<script>
import axios from "axios";

import Modal from "../../../../js/components/XgrowDesignSystem/Modals/Modal";
import formatBRLCurrency from "../../../../js/components/XgrowDesignSystem/Mixins/formatBRLCurrency";
import StatusModalComponent from "../../../../js/components/StatusModalComponent";
import Table from "../../../../js/components/Datatables/Table";
import Main from "../tables/LinksMain"
import Additional from "../tables/LinksAdditional"

export default {
  components: {
    Modal,
    StatusModalComponent,
    Table,
    Main,
    Additional
  },
  mixins: [formatBRLCurrency],
  data() {
    return {

    };
  },
  async mounted() {

  },
  methods: {
  },
};
</script>

<style>

</style>
