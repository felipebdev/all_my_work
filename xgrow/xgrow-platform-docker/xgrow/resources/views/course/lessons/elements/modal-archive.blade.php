<div class="modal-sections modal fade" tabindex="-1" id="modalArchive" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="d-flex w-100 justify-content-end p-3 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="course modal-header">
                <p class="modal-title course" id="modalArchiveTitle">
                    <i class="fas fa-paperclip me-2"></i> Arquivo
                </p>
            </div>
            <div class="modal-body d-block">
                <div class="modal-body-content">
                    <div class="course-modal-delete">
                        <a
                            href="javascript:void(0)"
                            @click.prevent="callConfirmationModal({
                                title: 'Deseja realmente excluir este item?',
                                text: 'Não há maneira de recuperar esse item após sua exclusão.',
                                confirmText: 'Sim, excluir',
                                confirmFunction: () => deleteObj('modalArchive', archiveModal.id)
                            })">
                            <i class="fas fa-trash-alt"></i> Excluir item
                        </a>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        <input spellcheck="false" autocomplete="off" id="archiveTitle" v-model="archiveModal.title"
                               type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="archiveTitle">Título do arquivo</label>
                    </div>

                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mt-2">
                        <select id="author_id" class="xgrow-select" name="author_id" v-model="archiveModal.authorId"
                                @change.prevent="callAuthorModal($event)">
                            <option value="" hidden disabled selected>Selecione um autor</option>
                            <option value="__newauthor__">+ Adicionar um novo autor</option>
                            <option v-for="author in authors" :value="author.id">[[ author.name ]]</option>
                        </select>
                        <label for="author_id">Autor</label>
                    </div>

                    <template v-if="typeof archiveModal.file === 'string' || archiveModal.file instanceof String">
                        <div class="info ms-3" v-if="archiveModal.file != null">
                            <small>Arquivo: [[ archiveModal.file ]]</small><br>
                        </div>
                    </template>
                    <template v-else>
                        <p class="mb-2">Escolha o arquivo:</p>
                        <div class="d-flex">
                            <div class="button">
                                <label for="fileUpload" class="custom-file-upload" v-if="archiveModal.file == null">
                                    <i class="fa fa-upload" aria-hidden="true"></i> upload
                                </label>
                                <a href="javascript:void(0)" class="custom-file-upload delete" v-else @click.prevent="archiveModal.file = null">
                                    <i class="fas fa-times"></i>
                                </a>
                                <input id="fileUpload" type="file" @change.prevent="readFile"
                                    accept=".xlsx,.xls,.doc,.docx,.ppt,.pptx,.pdf,application/pdf,.csv,
                                    application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
                                    application/vnd.ms-excel,application/msword,
                                    application/vnd.openxmlformats-officedocument.wordprocessingml.document"/>
                            </div>
                            <div class="info ms-3" v-if="archiveModal.file != null">
                                <small>Nome do arquivo: [[ archiveModal.file.name ]]</small><br>
                                <small>Tamanho: [[ (archiveModal.file.size/1024000).toFixed(2) ]] MB</small>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="course-button btn btn-outline-success"
                        data-bs-dismiss="modal" id="modalArchiveCancel" @click="closeModal('modalArchive')"> Cancelar
                </button>
                <button type="button" class="xgrow-button course-button border-light" id="modalArchiveSave"
                        @click="saveCurrentModal"><i class="fa fa-check"></i> Aplicar configurações
                </button>
            </div>
        </div>
    </div>
</div>
