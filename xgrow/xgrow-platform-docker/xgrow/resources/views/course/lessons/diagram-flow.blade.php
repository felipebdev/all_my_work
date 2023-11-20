<div class="xgrow-exp" id="diagramScreen" v-if="screen.toString() === 'diagram'">
    <div class="row mb-4">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-0">
                <input spellcheck="false" autocomplete="off" id="flowName" v-model="flow.name"
                       type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                <label for="flowName">Nome do fluxo</label>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 align-self-center justify-content-end">
            <div class="d-flex align-items-center justify-content-end my-2">
                <p class="zoom-label">[[zoom]]%</p>
                <button class="zoom-buttons out" @click="zoomOut"><i class="fa fa-minus"></i></button>
                <button class="zoom-buttons in" @click="zoomIn"><i class="fa fa-plus"></i></button>
            </div>
        </div>
    </div>
    <div class="d-flex">
        <div class="gojs-diagram-palette-panel">
            <div class="gojs-diagram-palette-header">
                <i class="fa fa-th-list"></i>
                Itens do fluxo
            </div>
            <div id="goJsPalette" class="gojs-diagram-palette"></div>
        </div>
        <diagram ref="diag"
                 v-bind:model-data="diagramData"
                 v-on:model-changed="modelChanged"
                 v-on:changed-selection="changedSelection"
                 class="gojs-diagram"
                 v-on:obj="objReceive"
                 v-on:zoom="displayZoom">
        </diagram>
    </div>
</div>
