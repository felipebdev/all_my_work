<div class="modal-sections modal fade" tabindex="-1" id="modalText" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="d-flex w-100 justify-content-end p-3 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="course modal-header">
                <p class="modal-title course" id="modalTextTitle">
                    <i class="fas fa-align-left me-2"></i> Texto
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
                            confirmFunction: () => deleteObj('modalText', textModal.id)
                        })">
                        <i class="fas fa-trash-alt"></i> Excluir item
                    </a>
                </div>
                <div class="modal-body-content">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        <input spellcheck="false" autocomplete="off" id="textTitle" v-model="textModal.title"
                               type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="textTitle">Título do texto</label>
                    </div>

                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mt-2">
                        <select id="author_id" class="xgrow-select" name="author_id" v-model="textModal.authorId"
                                @change.prevent="callAuthorModal($event)">
                            <option value="" hidden disabled selected>Selecione um autor</option>
                            <option value="__newauthor__">+ Adicionar um novo autor</option>
                            <option v-for="author in authors" :value="author.id">[[ author.name ]]</option>
                        </select>
                        <label for="author_id">Autor</label>
                    </div>

                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        <textarea id="text" rows="7" cols="54" style="resize:none" name="text" v-model="textModal.text"
                                  class="mui--is-not-empty mui--is-untouched mui--is-pristine"></textarea>
                        <label for="text">Texto</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="course-button btn btn-outline-success"
                        data-bs-dismiss="modal" id="modalTextCancel" @click="closeModal('modalText')"> Cancelar
                </button>
                <button type="button" class="xgrow-button course-button border-light" id="modalTextSave"
                        @click="saveCurrentModal"><i class="fa fa-check"></i> Aplicar configurações
                </button>
            </div>
        </div>
    </div>
</div>
