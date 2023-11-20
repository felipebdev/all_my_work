<div class="tab-pane fade show" id="progressSummary"
    :class="{'active': activeScreen.toString() === 'progress.summary'}">

    <a href="javascript:void(0)" class="back-button"
        @click.prevent="backToAll">
        <i class="fas fa-chevron-left"></i> <span>Voltar</span>
    </a>

    <div class="mt-3">
        <h4>Detalhes do aluno - [[ details.subscriber.username ]]</h4>
        <small class="second-text">Veja em detalhes os acessos do aluno na plataforma</small>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <h5>Dados do aluno</h5>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 col-12 mb-2">
            <p class="second-text">Nome: [[ details.subscriber.username ]]</p>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 col-12 mb-2">
            <p class="second-text">Email: [[ details.subscriber.email ]]</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <h5>Dados de acesso</h5>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 col-12 mb-2">
            <p class="second-text">Curso: [[ details.accessData.course ]]</p>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 col-12 mb-2">
            <p class="second-text">[[ details.accessData.percentage * 100 ]]% concluído</p>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 col-12 mb-2">
            <p class="second-text">Primeiro acesso: [[ details.accessData.firstAccess ]]</p>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-xs-12 col-12 mb-2">
            <p class="second-text">Último acesso: [[ details.accessData.lastAccess ]]</p>
        </div>
    </div>

    <xgrow-table-component id="subscriber-access">
        <template v-slot:header>
            <th>Curso</th>
            <th>Módulo</th>
            <th>Progresso</th>
        </template>
        <template v-slot:body>
            <tr v-for="item in details.courseProgress" :key="item.moduleId">
                <td>[[ details.accessData.course ]]</td>
                <td>[[ item.moduleName ]]</td>
                <td>[[ item.percentModuleCompleted * 100 ]]%</td>
            </tr>
        </template>
    </xgrow-table-component>
</div>
