<template>
  <div>
    <Table id="plans-content-table">
      <template v-slot:title>
        <div
          class="d-flex align-items-center gap-1 flex-wrap justify-content-center justify-content-md-between w-100"
        >
          <div>
            <p class="xgrow-card-title mb-2">Plano de vendas</p>
            <span
              ><i class="fa-solid fa-star"></i> Por padrão, utilizamos o último plano
              criado como o seu plano favorito.</span
            >
          </div>
        </div>
      </template>
      <template v-slot:header>
        <th>Nome</th>
        <th>Tipo de pagamento</th>
        <th>Valor</th>
        <th v-if="false">Troca de plano</th>
        <th>Status</th>
        <th style="width: 40px"></th>
      </template>
      <template v-slot:body>
        <tr :key="plan.id" v-for="plan in plans">
          <td>
            <i
              :id="`favorite-${plan.id}`"
              v-show="plan.id === parseInt(favoritePlan)"
              class="fa-solid fa-star"
            ></i>
            {{ plan.name }}
          </td>
          <td>{{ formatPaymentType(plan.type_plan) }}</td>
          <td>{{ formatBRLCurrency(plan.price) }}</td>
          <td v-if="false">
            <div
              class="form-check form-switch"
              :title="
                !allowEditingPlans
                  ? 'É necessário ter ao menos 2 planos para habilitar a edição.'
                  : ''
              "
            >
              <input
                class="form-check-input"
                :id="`switch-edit-${plan.id}`"
                type="checkbox"
                :disabled="!allowEditingPlans"
                :checked="allowToEdits.includes(plan.id)"
                @change="changePermissionToEdit(plan.id)"
              />
              <label class="form-check-label" :for="`switch-edit-${plan.id}`"></label>
            </div>
          </td>
          <td>
            <div class="form-check form-switch">
              <input
                class="form-check-input"
                :id="`switch-status-${plan.id}`"
                type="checkbox"
                :checked="Boolean(parseInt(plan.status))"
                @click="changeStatus(plan.id)"
              />
              <label class="form-check-label" :for="`switch-status-${plan.id}`"></label>
            </div>
          </td>
          <td class="text-end">
            <div class="dropdown">
              <button
                class="xgrow-button table-action-button m-1"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                <i class="fas fa-ellipsis-v"></i>
              </button>
              <ul class="dropdown-menu table-menu xgrow-dropdown-menu">
                <li v-if="plan.id !== parseInt(favoritePlan)">
                  <a
                    class="dropdown-item table-menu-item"
                    role="button"
                    @click="setFavorite(plan.id)"
                  >
                    Tornar Favorito
                  </a>
                </li>
                <li>
                  <a
                    class="dropdown-item table-menu-item"
                    :href="`${editPlanRoute}/${plan.id}`"
                  >
                    Editar
                  </a>
                </li>
                <li v-if="plan.id !== parseInt(favoritePlan)">
                  <a
                    class="dropdown-item table-menu-item"
                    role="button"
                    @click="confirmDelete(plan.id)"
                  >
                    Excluir
                  </a>
                </li>
              </ul>
            </div>
          </td>
        </tr>
      </template>
    </Table>

    <div class="d-flex justify-content-end mt-2 mb-3">
      <a :href="newPlanRoute" class="xgrow-upload-btn-lg xgrow-outline-btn btn"
        >Criar novo Plano</a
      >
    </div>

    <StatusModalComponent :is-open="loading" status="loading"></StatusModalComponent>
    <div id="modal-hide" style="display: none">
      <Modal :is-open="modal.isOpen">
        <div>
          <h3>Excluir plano</h3>
          <hr />
          <p class="mt-5">Deseja realmente excluir este plano?</p>
        </div>
        <div class="d-flex justify-content-end gap-3">
          <button class="xgrow-button" @click="handleDelete">Sim, excluir</button>
          <button
            class="xgrow-button"
            style="background: 0; border: 1px solid"
            @click="modal.isOpen = false"
          >
            Não, manter
          </button>
        </div>
      </Modal>
    </div>
  </div>
</template>

<script>
import axios from "axios";

import Modal from "../../../../js/components/XgrowDesignSystem/Modals/Modal";
import formatBRLCurrency from "../../../../js/components/XgrowDesignSystem/Mixins/formatBRLCurrency";
import StatusModalComponent from "../../../../js/components/StatusModalComponent";
import Table from "../../../../js/components/Datatables/Table";

export default {
  components: {
    Modal,
    StatusModalComponent,
    Table,
  },
  mixins: [formatBRLCurrency],
  data() {
    return {
      newPlanRoute,
      editPlanRoute,
      loading: false,
      modal: {
        isOpen: false,
      },
      plans: [],
      favoritePlan: 0,
      deleteUrl: "",
      allowEditingPlans: false,
      allowToEdits: [],
    };
  },
  async mounted() {
    this.favoritePlan = favoritePlanDefault;

    await this.getPlans();

    if (this.plans.length > 0) {
      this.allowToEdits = this.plans
        .filter((item) => Boolean(parseInt(item.allow_change)))
        .map((item) => item.id);

    }

    if (this.plans.length > 1) {
      this.allowEditingPlans = true;
    }
  },
  methods: {
    async getPlans() {
      this.loading = true;

      const result = await axios.get(listPlansUrl);
      this.plans = result.data.data;

      this.loading = false;
    },
    formatPaymentType(payment) {
      const types = {
        P: "Venda única",
        R: "Assinatura",
      };

      return types[payment];
    },
    async confirmDelete(id) {
      this.deleteUrl = deleteRoute.replace(/:id/g, id);
      this.modal.isOpen = true;
    },
    async handleDelete() {
      this.loading = true;

      try {
        await axios.delete(this.deleteUrl);
        await this.getPlans();
        successToast("Sucesso!", "Plano removido com sucesso!");
      } catch (error) {
        errorToast(
          "Algum erro aconteceu!",
          `Houve um erro ao alterar o registro: ${error.response.data.message}`
        );
      }
      this.modal.isOpen = false;
      this.loading = false;
    },
    async setFavorite(planId) {
      this.loading = true;

      try {
        const res = await axios.post(`${favoritePlanRoute}`, { plan: planId, product });

        document.getElementById(`favorite-${this.favoritePlan}`).style.display = "none";
        document.getElementById(`favorite-${planId}`).style.display = "";
        this.favoritePlan = planId;
        await this.getPlans();
        successToast("Sucesso!", res.data.message.toString());
      } catch (error) {
        errorToast(
          "Erro ao atualizar item",
          "Ocorreu um erro ao atualizar este item, por favor tente mais tarde."
        );
      }
      this.loading = false;
    },
    changeStatus(id) {
      this.loading = true;

      const url = changeStatusUrl.replace(/:id/g, id);
      axios
        .put(url)
        .then(function (response) {
          successToast("Sucesso!", "Status atualizado com sucesso!");
        })
        .catch(function (error) {
          errorToast(
            "Algum erro aconteceu!",
            `Houve um erro ao atualizado o status do plano`
          );
        });

      this.loading = false;
    },
    async changePermissionToEdit(planId) {

      //Disable plans
      if (this.allowToEdits.includes(planId)) {

        //Disable last two edit enabled plans
        if (this.allowToEdits.length === 2) {
          this.allowToEdits.map(async (id) => await this.changeEditPlanStatus(id));
          return this.allowToEdits = [];
        }

        //Disable one plan
        await this.changeEditPlanStatus(planId)
        return (this.allowToEdits = this.allowToEdits.filter((id) => id !== planId));
      }

      //Enable all plans to edit in first time
      if (this.allowToEdits.length === 0) {
        return this.plans.map(async (item) => {
          this.allowToEdits.push(item.id);
          await this.changeEditPlanStatus(item.id);
        });
      }

      // Enable one plan
      await this.changeEditPlanStatus(planId)
      return this.allowToEdits.push(planId);
    },
    async changeEditPlanStatus(planId) {
      try {
        const url = allowEditPlanUrl.replace(":id", planId);
        const res = await axios.patch(url);
        successToast("Sucesso!", res.data.message.toString());

      } catch (error) {
        errorToast(
          "Alguem erro aconteceu!",
          error.response.data.message.toString()
        );
      }
    },
  },
};
</script>

<style>
.table-responsive {
  min-height: 200px !important;
}
.modal__content .modal__close {
  display: none;
}
.modal__content {
  padding: 25px;
  text-align: center;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
</style>
