<div class="modal-sections modal fade" tabindex="-1" id="modalAuthors" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="d-flex w-100 justify-content-end p-3 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="course modal-header">
                <p class="modal-title course" id="modalAuthorsTitle">
                    <i class="fas fa-user me-2"></i> Autor
                </p>
            </div>
            <div class="modal-body d-block">
                <div class="modal-body-content">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        <input spellcheck="false" autocomplete="off" id="label" name="label" v-model="authorModal.name"
                                type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label>Nome do autor *</label>
                    </div>

                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        <input spellcheck="false" autocomplete="off" id="label" name="label" v-model="authorModal.email"
                            type="email" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label>E-mail do autor *</label>
                    </div>

                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                        <textarea id="description" rows="7" cols="54" style="resize:none" v-model="authorModal.curriculum"
                                name="description" class="mui--is-not-empty mui--is-untouched mui--is-pristine">
                        </textarea>
                        <label for="description">Currículo</label>
                    </div>

                    <small class="caracter-counter">* campos obrigatórios</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="course-button btn btn-outline-success"
                        data-bs-dismiss="modal" id="modalAuthorsCancel" @click="closeModal('modalAuthors')"> Cancelar
                </button>
                <button type="button" class="xgrow-button course-button border-light" id="modalAuthorsSave"
                        @click="saveAuthorModal"><i class="fa fa-check"></i> Aplicar configurações
                </button>
            </div>
        </div>
    </div>
</div>
