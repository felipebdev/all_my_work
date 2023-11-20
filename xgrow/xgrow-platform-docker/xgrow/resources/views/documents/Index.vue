<template>
  <div>
    <div
      class="tab-pane fade"
      :class="$store.state.tab === 'validate' ? 'show active' : 'd-none'"
    >
      <Validate @payload="getValidatePayload" />
    </div>
    <div
      class="tab-pane fade"
      :class="$store.state.tab === 'bank-data' ? 'show active' : 'd-none'"
    >
      <Bank :payload="form" />
    </div>
    <div
      class="tab-pane fade"
      :class="$store.state.tab === 'address' ? 'show active' : 'd-none'"
    >
      <Address />
    </div>
    <div
      class="tab-pane fade"
      :class="$store.state.tab === 'rates-and-terms' ? 'show active' : 'd-none'"
    >
      <Fees />
    </div>
  </div>
</template>

<script>

import Validate from "./tabs/DocumentsValidate";
import Address from "./tabs/DocumentsAddress";
import Fees from "./tabs/DocumentsFees";
import Bank from "./tabs/DocumentsBankData";

export default {
  name: "Index",
  components: {
    Validate,
    Address,
    Fees,
    Bank,
  },
  data() {
    return {
      form: {},
      errors: false,
    };
  },
  async mounted() {},
  methods: {
    getValidatePayload(payload) {
      this.form = { ...this.form, ...payload };
    },
  },
};
</script>
