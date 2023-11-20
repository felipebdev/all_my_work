<div class="modal-sections modal fade" tabindex="-1" id="modalContent" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="d-flex w-100 justify-content-end p-3 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="course modal-header">
                <p class="modal-title course" id="modalContentTitle">
                    <i class="fa fa-file-alt me-2"></i> Conteúdo
                </p>
            </div>
            <div class="modal-body d-block">
                <div class="course-modal-delete">
                    <a
                        href="javascript:void(0)"
                        @click.prevent="callConfirmationModal({
                            title: 'Deseja realmente excluir este item?',
                            text: 'Não há maneira de recuperar esse item após sua exclusão.',
                            confirmText: 'Sim, excluir',
                            confirmFunction: () => deleteObj('modalContent', contentModal.id)
                        })">
                        <i class="fas fa-trash-alt"></i> Excluir item
                    </a>
                </div>
                <div class="modal-body-content">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        <input spellcheck="false" autocomplete="off" id="title" v-model="contentModal.title" type="text"
                               class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="title">Título do conteúdo</label>
                    </div>

                    <model-select
                        :options="contentOption"
                        v-model="contentOptionSelected"
                        placeholder="Conteúdo"
                        class="xgrow-vue-select2 mb-3">
                    </model-select>

                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mt-2">
                        <select id="authorId" class="xgrow-select" v-model="contentModal.authorId"
                                @change.prevent="callAuthorModal($event)" disabled>
                            <option value="" hidden disabled selected></option>
                            <option v-for="author in authors" :value="author.id">[[ author.name ]]</option>
                        </select>
                        <label for="authorId">Autor</label>
                    </div>

                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-0">
                        <textarea id="description" rows="7" cols="54" style="resize:none"
                                  v-model="contentModal.description"
                                  class="mui--is-not-empty mui--is-untouched mui--is-pristine"></textarea>
                        <label for="description">Descrição</label>
                    </div>
                    <small class="caracter-counter">Máximo 120 caracteres</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="course-button btn btn-outline-success" data-bs-dismiss="modal"
                        id="modalContentCancel" @click="closeModal('modalContent')">
                    Cancelar
                </button>
                <button type="button" class="xgrow-button course-button border-light" id="modalContentSave"
                        @click="saveCurrentModal">
                    <i class="fa fa-check"></i> Aplicar configurações
                </button>
            </div>
        </div>
    </div>
</div>
