<template>
  <div class="tab-pane fade" id="nav-links-additional" role="tabpanel" aria-labelledby="nav-links-additional-tab">
    <div id="product-links"  class="xgrow-card card-dark card-dark--no-shadow p-0">
        <div class="mt-4">
            <div>
                <div class="links__header">
                    <h3>Links adicionais: {{linkstCount}}</h3>
                    <button @click="createLinkModal()" class="links__add xgrow-button xgrow-button-auto" type="button" ><i class="fal fa-plus"></i><span >Novo link adicional</span></button>
                </div>
                <p class="subtitle-links pb-2"> links adicionais são links de qualquer página da internet que podem ajudar os afiliados a venderem este produto.</p>
                <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>
                            O script de marcação precisa estar instalado corretamente no link para que o afiliado receba a comissão sobre a venda. OBS: Cada link tem um script de marcação único.
                        </span>
                    </div>
                </div>
                <hr>
            </div>
            <div class="table-responsive">
                <table id="" class="table custom-table w-100" v-if="linkstCount != 0" >
                    <thead>
                    <tr class="xgrow-table-header">
                        <th>Nome</th>
                        <th>Plano</th>
                        <th>URL</th>
                        <th>Ações</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr :key="link.id" v-for="link in links">
                        <td>{{link.link_name}}</td>
                        <td>{{link.plan.name}}</td>
                        <td><a @click="copyLink(link.url)" >{{link.url}}</a></td>
                        <td>
                            <button @click="copyLink(link.url)" class="links__copy xgrow-button xgrow-button-auto" type="button" ><i class="far fa-copy" aria-hidden="true" ></i><span >Copiar link</span></button>
                        </td>
                        <td class="text-end">
                            <div class="dropdown x-dropdown">
                                <button class="xgrow-button table-action-button m-1" type="button"
                                        :id="`dropdownMenuButton${link.id}`" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu table-menu xgrow-dropdown-menu"
                                    :aria-labelledby="`dropdownMenuButton${link.id}`">
                                    <li>
                                        <a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                           @click="editLinkModal(link)">
                                            Editar Link
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                           @click="showScript(link)">
                                            Script de marcação
                                        </a>
                                    </li>
                                    <li class="">
                                        <a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                           @click="deleteConfirm(link.id)">
                                            Excluir Link
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <p v-else >Não existe link adicional para este produto!</p>
            </div>

        </div>


        <!-- Modal Script -->
        <Modal :is-open="openModalScript">
            <div class="modal-script">
                <div class="modal-header">
                    <button @click="closeModal()" type="button" class="modal_close" ><i class="fa fa-times" aria-hidden="true"></i></button>
                </div>
                <h3>Script de marcação</h3>
                <div class="box-script">
                    <label>Código do script</label>
                    <textarea id="text-script" class="input-script" readonly="readonly"></textarea>
                    <div class="btn-right">
                        <button @click="copyScript()" class="links__copy xgrow-button" type="button" ><i class="far fa-copy" aria-hidden="true" ></i><span >Copiar código</span></button>
                    </div>

                </div>
                <div class="footer-modal">
                    <button @click="closeModal()" class="links__voltar btn-back" type="button" ><span >Voltar</span></button>
                </div>
            </div>
        </Modal>

        <!-- Modal confirm delete -->
        <Modal :is-open="openModalConfirm">
            <div class="modal-content">
                <div class="modal-header">
                    <button @click="closeModal()" type="button" class="modal_close" ><i class="fa fa-times" aria-hidden="true"></i></button>
                </div>
                <div class="modal-header">
                    <p class="modal-title" id="confirmationModalTitle">Excluir Link</p>
                </div>
                <div class="modal-body">
                    <p id="confirmationModalBody">Você tem certeza que deseja excluir o link?</p>
                </div>
                <div class="modal-footer">
                    <button @click="deleteLink()" type="button" class="btn btn-success" id="confirmationModalSave">Sim, excluir</button>
                    <button @click="closeModal()" type="button" class="btn btn-outline-success" data-bs-dismiss="modal" id="confirmationModalCancel">Não, manter</button>
                </div>
            </div>
        </Modal>

        <!-- Modal Action delete and create -->
        <Modal :is-open="openModalAction">
            <div class="modal-content modal-action">
                <form @submit.prevent="saveLink()">
                    <div class="modal-header">
                        <button @click="closeModal()" type="button" class="modal_close" ><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <p class="modal-title">
                              <div v-if="action=='edit'">
                                <i class="fa fa-edit"></i>
                                Editar Link adicional
                              </div>
                              <div v-else>
                                <i class="fa fa-plus-circle"></i>
                                Novo Link adicional
                              </div>
                            </p>
                            <hr>
                            <p>Insira um link externo que vai ajudar os afiliados a venderem seu produto.</p>
                        </div>

                        <Input v-model="model.link_name" id="link_name" class="w-100" label="Nome do Link" placeholder="Insira um nome para reconhecer o link criado..." required="required" ></Input>
                        <Input v-model="model.url" id="url" class="w-100 mb-2" label="Url do Link" placeholder="https://" required="required" ></Input>
                        <Multiselect
                            v-model="model.plan_id"
                            :options="plans"
                            placeholder="Selecione o plano:"
                        />
                    </div>
                    <div class="footer-modal">
                        <button @click="closeModal()" class="links__voltar btn-back" type="button" ><span> Cancelar</span></button>
                        <button class="links__copy xgrow-button" type="submit" ><i class="fa fa-check" aria-hidden="true" ></i><span> Salvar</span></button>
                    </div>
                </form>
            </div>
        </Modal>
    </div>
</div>

</template>

<script>
import axios from "axios";
import Modal from "../../../../js/components/XgrowDesignSystem/Modals/Modal";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input";
import formatBRLCurrency from "../../../../js/components/XgrowDesignSystem/Mixins/formatBRLCurrency";
import Multiselect from "@vueform/multiselect";

import "@vueform/multiselect/themes/default.css";
export default {
  components: {
    Modal,
    Input,
    Multiselect
  },
  mixins: [formatBRLCurrency],
  data() {
    return {
      openModalConfirm: false,
      openModalScript: false,
      openModalAction: false,
      isLoading: false,
      status: "loading",
      error: false,
      links: [],
      linkstCount: 0,
      deleteModelID: null,
      action: "",
      model: { link_name: "", url: "", product_id: productID, plan_id: "" },
      updateModelID: null,
      plans: null,
    };
  },
  methods: {
    list() {
      this.isLoading = true;
      //const json = JSON.stringify({ affiliation_settings_id: invite_link });
      axios
        .get(listProductLinksURL, {
          headers: {
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          this.error = response.data.error;
          this.links = response.data.data;
          this.linkstCount = this.links.length;
          this.isLoading = false;
        })
        .catch((error) => {
          this.error = true;
          this.isLoading = false;
        });
    },
    listPlans() {
      axios
        .get(listPlansProductLinksURL, {
          headers: {
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          this.error = response.data.error;
          this.plans = response.data.data.map(function (plan) {
            return { value: plan.id, label: plan.name };
          });
        })
        .catch((error) => {
          this.error = true;
        });
    },
    loading() {
      this.isLoading = true;
    },
    resetForm() {
      this.model = { link_name: "", url: "", product_id: productID, plan_id: "" };
    },
    copyLink(link) {
      navigator.clipboard.writeText(link);
      successToast("Sucesso", "Link copiado com sucesso!");
    },
    copyScript() {
      let text = document.getElementById("text-script").value;
      navigator.clipboard.writeText(text);
      successToast("Sucesso", "Script copiado com sucesso!");
    },
    showScript(link) {
      document.getElementById("text-script").value = link.script_code;
      this.openModalScript = true;
    },
    deleteConfirm(link_id) {
      this.openModalConfirm = true;
      this.deleteModelID = link_id;
    },
    deleteLink() {
      this.isLoading = true;
      let url = deleteProductLinksURL.replace("0", "") + this.deleteModelID;
      axios
        .delete(url, {
          headers: {
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          this.error = response.data.error;
          this.isLoading = false;
          this.deleteModelID = null;
          this.list();
          this.closeModal();
          successToast("Sucesso", "Link excluído com sucesso!");
        })
        .catch((error) => {
          this.error = true;
          this.isLoading = false;
          console.log(error)
          errorToast("Falha ao realizar ação", "Aconteceu algo errado!");
        });
    },
    createLinkModal() {
      this.action = "create";
      this.openModalAction = true;
    },
    editLinkModal(link) {
      this.action = "edit";
      this.openModalAction = true;
      this.model = {
        link_name: link.link_name,
        url: link.url,
        product_id: productID,
        plan_id: link.plan_id,
      };
      this.updateModelID = link.id;
    },
    saveLink() {
      if (this.action == "edit") {
        this.updateLink();
      } else {
        this.createLink();
      }
    },
    createLink() {
      this.isLoading = true;
      const json = JSON.stringify(this.model);
      let url = createProductLinksURL;
      axios
        .post(url, json, {
          headers: {
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          this.error = response.data.error;
          this.isLoading = false;
          this.deleteModelID = null;
          this.list();
          this.closeModal();
          successToast("Sucesso", "Link adicionado com sucesso!");
          this.resetForm();
        })
        .catch((error) => {
          this.error = true;
          this.isLoading = false;
          errorToast("Falha ao realizar ação", "Aconteceu algo errado!");
        });
    },
    updateLink() {
      this.isLoading = true;
      const json = JSON.stringify(this.model);
      let url = updateProductLinksURL.replace("0", "") + this.updateModelID;
      axios
        .put(url, json, {
          headers: {
            "Content-Type": "application/json",
          },
        })
        .then((response) => {
          this.error = response.data.error;
          this.isLoading = false;
          this.deleteModelID = null;
          this.list();
          this.closeModal();
          successToast("Sucesso", "Link atualizado com sucesso!");
          this.resetForm();
        })
        .catch((error) => {
          this.error = true;
          this.isLoading = false;
          errorToast("Falha ao realizar ação", "Aconteceu algo errado!");
        });
    },
    closeModal() {
      this.openModalScript = false;
      this.openModalConfirm = false;
      this.openModalAction = false;
    },
  },
  created() {
    this.isLoading = true;
    this.list();
    this.listPlans();
  },
  mounted() {
    this.isLoading = false;
  },
};
</script>

<style></style>
