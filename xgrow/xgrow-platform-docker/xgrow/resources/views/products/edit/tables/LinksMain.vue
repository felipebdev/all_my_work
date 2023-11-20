<template>
  <div
    class="tab-pane fade active show"
    id="nav-links-main"
    role="tabpanel"
    aria-labelledby="nav-links-main-tab"
  >
    <div class="xgrow-card card-dark p-0">
      <div class="mt-4">
        <div>
          <p class="xgrow-card-title mb-2">Links</p>
          <div class="table-responsive">
            <table
              id="link-table"
              class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
              style="width: 100%"
            >
              <thead>
                <tr class="card-black" style="border: 2px solid var(--black-card-color)">
                  <th>Nome do Plano</th>
                  <th>Checkout URL</th>
                  <th>Preço</th>
                </tr>
              </thead>
              <tbody>
                <tr :key="plan.id" v-for="plan in linkPlans">
                  <td>{{ plan.name }}</td>
                  <td>
                    <input
                      type="text"
                      :id="`url${plan.id}}`"
                      class="url-input"
                      :value="plan.checkout_url"
                      readonly
                      @click="copyLink(plan.checkout_url)"
                    />
                  </td>
                  <td>{{ formatBRLCurrency(plan.price) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import formatBRLCurrency from "../../../../js/components/XgrowDesignSystem/Mixins/formatBRLCurrency";
export default {
  mixins: [formatBRLCurrency],
  data() {
    return {
      linkPlans
    }
  },
  methods: {
    copyLink(link) {
      navigator.clipboard.writeText(link);
      successToast("Sucesso", "Link copiado com sucesso!");
    },
  }
};
</script>

<style></style>
