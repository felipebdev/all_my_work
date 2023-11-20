<div id="flowScreen" style="min-height: 60vh;" class="row" v-if="screen.toString() === 'flow'">
    <panel :icon="'fa-project-diagram'" :title="'Jornadas de conteúdo'" :inverse="true">
        <h3 class="experience-panel-item-title">JORNADAS DISPONÍVEIS</h3>
        <ul class="p-0" style="overflow-y: scroll; height: 50vh">
            <panel-item
                v-for="(module, index) in modules"
                :key="module.id"
                :title="module.name"
                :steps="module.contents.length ?? 0"
                :module-id="module.id"
                :active="index === activeIndex"
                :index="index"
                :diagram="module.diagram"
                @set-active="activeIndex = $event"
            >
            </panel-item>
        </ul>
    </panel>
    <panel :icon="'fa-th-list'" :title="'Passos do fluxo'" :inverse="false">
        <div class="xgrow-vi-loading" v-if="loadingFlow == true">
            <div class="loader"></div>
        </div>
        <template v-else>
            <p class="bold">[[moduleTitleFlow.title]]</p>
            <p class="experience-panel-item-step">
                [[moduleTitleFlow.steps]] passo[[moduleTitleFlow.steps > 1 ? 's' : '']]
            </p>
            <ul class="experience-panel-timeline my-4 p-3 d-flex flex-column"
                style="background-color: #3B404E; border-radius: 6px">
                <template v-if="moduleTitleFlow.steps > 0">
                    <panel-item-timeline :icon="'start'" :title="'Início'"></panel-item-timeline>
                    <panel-item-timeline :icon="content.category" :title="content.title" v-for="content in contents"
                                        :key="content.id"></panel-item-timeline>
                    <panel-item-timeline :icon="'end'" :title="'Fim'"></panel-item-timeline>
                </template>
                <panel-item-timeline v-else
                                    :title="'Você ainda não adicionou nenhum passo nesse flow, por favor clique em editar e adicione os passos desejados.'">
                </panel-item-timeline>
            </ul>
            <hr>
            <div class="text-end">
                <button class="xgrow-button border-light" type="button" @click="editFlow(moduleTitleFlow.id)">
                    <i class="fa fa-pen"></i> Editar
                </button>
            </div>
        </template>
    </panel>
</div>
