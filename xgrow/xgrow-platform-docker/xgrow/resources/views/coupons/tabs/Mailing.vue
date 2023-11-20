<template>
    <div class="xgrow-card card-dark py-4">
        <Title class="mb-3">Mailing</Title>

        <Subtitle style="font-weight: 600">
            Adicionar aluno manualmente
        </Subtitle>

        <Row>
            <Col sm="12" md="6" lg="6" xl="6">
                <Input
                    id="name"
                    placeholder="Insira o nome completo do aluno..."
                    label="Nome"
                />
            </Col>
            <Col sm="12" md="6" lg="6" xl="6">
                <Input
                    id="email"
                    placeholder="Insira o e-mail do aluno..."
                    label="E-mail"
                />
            </Col>
            <Col sm="12" md="12" lg="12" xl="12">
                <Input
                    id="notes"
                    placeholder="Insira uma nota sobre o cupom..."
                    label="Notas"
                />
            </Col>
        </Row>

        <Row>
            <div class="d-flex justify-content-end align-items-center py-4">
                <DefaultButton status="success" text="Adicionar" />
            </div>
        </Row>

        <hr class="mb-4" />

        <CsvImportInstructions />

        <Checkbox
            id="termsAndConditions"
            label="Declaro que li as instruções acima."
            :checked="termsAndConditions"
            @input="termsAndConditions = !termsAndConditions"
        />

        <Row class="mt-4">
            <Col sm="12" md="6" lg="6" xl="6">
                <div class="import__file w-100">
                    <h6>Importar lista de alunos</h6>
                    <p style="max-width: 558px">
                    Selecione um arquivo CSV conforme o modelo informado acima para realizar a
                    importação. O tamanho máximo suportado do arquivo é de 10MB.
                    </p>
                    <DefaultButton
                    outline
                    text="Adicionar arquivo CSV"
                    icon="fas fa-upload"
                    class="my-4"
                    :disabled="!termsAndConditions"
                    :on-click="() => toggleModal('modalImport', true)"
                    />
                </div>
            </Col>
        </Row>


        <Row>
            <div class="d-flex justify-content-end align-items-center py-4">
                <DefaultButton
                    status="success"
                    text="Importar CSV"
                    :disabled="!termsAndConditions"
                    @click="sendToast"
                />
            </div>
        </Row>

        <hr class="mb-4" />

        <Table id="mailingTable">
            <template v-slot:title>
                <div class="xgrow-table-header w-100">
                    <Title class="mb-3">
                        Lista de alunos: {{ pagination.totalResults }}
                    </Title>
                </div>
            </template>
            <template v-slot:filter>
                <div class="d-flex gap-3 align-items-end flex-wrap">
                    <Input
                        style="margin: 0px;width: 350px;"
                        id="searchIpt"
                        icon="<i class='fa fa-search'></i>"
                        placeholder="Pesquise pelo nome ou e-mail do aluno..."
                        class="search-input"
                        v-model="filter.search"
                    />
                </div>
            </template>
            <template v-slot:thead>
                <th>ID</th>
                <th>Nome</th>
                <th>Notas</th>
                <th>E-mail enviado</th>
                <th style="width: 60px"></th>
            </template>
            <template v-if="results.length > 0" v-slot:tbody>
                <tr v-for="item in results" :key="item.id"></tr>
            </template>
            <template v-else v-slot:tbody>
                <tr>
                    <td colspan="5" class="no-result">
                        <img class="text-center" src='/xgrow-vendor/assets/img/new-no-result.svg' alt='Nenhum resultado encontrado.'/>

                        <Title class="mb-3 justify-content-center">
                            Você não possui alunos em seu mailing :(
                        </Title>

                        <Subtitle
                            style="font-weight: 600"
                            class="justify-content-center"
                        >
                            Mas não se procupe, você pode adicionar alunos
                            manualmente ou importar uma lista nos campos acima.
                        </Subtitle>
                    </td>
                </tr>
            </template>
            <template v-slot:footer>
                <Pagination
                    class="mt-4"
                    :total-pages="pagination.totalPages"
                    :total="pagination.totalResults"
                    :current-page="pagination.currentPage"
                    @page-changed="
                        (page) => paginationChange('currentPage', page)
                    "
                    @limit-changed="(page) => paginationChange('limit', page)"
                />
            </template>
        </Table>

        <ImportFile :is-open="modalImport" :toggle="toggleModal"/>
        <Map :is-open="modalMap" :toggle="toggleModal"/>
    </div>
</template>

<script>
import Alert from "../../../js/components/XgrowDesignSystem/Alert/Alert";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col";
import DefaultButton from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Input from "../../../js/components/XgrowDesignSystem/Form/Input";
import Row from "../../../js/components/XgrowDesignSystem/Utils/Row";
import Subtitle from "../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title";
import Checkbox from "../../../js/components/XgrowDesignSystem/Form/Checkbox";
import Pagination from "../../../js/components/Datatables/Pagination";
import Table from "../../../js/components/XgrowDesignSystem/DraggableTable/Table";
import NoResultVue from "../../../js/components/Datatables/NoResult.vue";
import ImportFile from "../modal/ImportFile";
import Map from "../modal/Map";
import CsvImportInstructions from '../../../js/components/XgrowDesignSystem/Alert/CsvImportInstructions'

export default {
    name: "Mailing",
    components: {
        CsvImportInstructions,
        Alert,
        Col,
        DefaultButton,
        Input,
        Row,
        Subtitle,
        Title,
        Checkbox,
        Pagination,
        Table,
        NoResultVue,
        ImportFile,
        Map
    },
    data() {
        return {
            termsAndConditions: false,
            results: [],
            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25,
            },
            filter: {
                search: "",
            },
            modalImport: false,
            modalMap: false,
        };
    },
    methods: {
        toggleModal(modal, status) {
            this[modal] = status
        },
        sendToast() {
            successToast('Lista carregada!', 'A lista de alunos que você configurou foi carregada com sucesso! Clique aqui para visualizar e finalizar a importação.')
        }
    }
};
</script>

<style lang="scss" scoped>
.alert-list {
    margin: 0;

    &__item {
        list-style: initial !important;
        font-size: 14px;
    }
}

.no-result {
    background: #333844;
    text-align: center;
    padding: 8px 0;
    border-radius: 8px;
}
</style>
