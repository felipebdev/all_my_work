<template>
    <Container has-border>
        <template v-slot:header-left>
            <Title>Próximas lives: {{ results.length }}</Title>
            <Subtitle>Veja os detalhes de suas lives agendadas ou crie uma nova.</Subtitle>
        </template>
        <template v-slot:header-right>
            <router-link :to="{ name: 'lives-new' }">
                <DefaultButton status="success" icon="fas fa-plus" text="Nova live" />
            </router-link>
        </template>
        <template v-slot:content>
            <div class="d-flex align-items-center justify-content-between py-2 gap-2 flex-wrap w-100 d-none">
                <div class="d-flex gap-3 align-items-center flex-wrap">
                    <Input id="search-field" placeholder="Pesquise pelo nome da live..."
                        icon="<i class='fas fa-search'></i>" iconColor="#93BC1E" v-model="filter.searchValue"
                        style="min-width: 300px; max-width: 400px" />
                </div>
            </div>
            <Row>
                <LiveCard :live="item" v-for="item in results" :key="item._id" @delete="$emit('delete', item._id)" />
                <template v-if="results.length === 0">
                    <NoLive />
                </template>
            </Row>
        </template>
        <template v-slot:footer>
            <Pagination class="mt-4" :total-pages="pagination.totalPages" :total="pagination.totalResults"
                :current-page="pagination.currentPage" @page-changed="onPageChange" @limit-changed="onLimitChange">
            </Pagination>
        </template>
    </Container>
</template>

<script>
import Container from "../../../../js/components/XgrowDesignSystem/Cards/Container";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input";
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row";
import LiveCard from "../components/LiveCard";
import Pagination from "../../../../js/components/Datatables/Pagination";
import NoLive from "../components/NoLive";
import { RouterLink } from 'vue-router'

export default {
    name: "NextLives",
    components: { NoLive, Pagination, LiveCard, Row, Input, DefaultButton, Subtitle, Title, Container, RouterLink },
    props: {
        results: { type: Array, required: true }
    },
    data() {
        return {
            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25,
            },

            filter: {
                searchValue: ""
            }
        }
    },
    methods: {
        /** On change page */
        onPageChange: async function (page) {
            this.pagination.currentPage = page;
            // await this.getAuthors();
        },
        /** Limit by size itens */
        onLimitChange: async function (value) {
            this.pagination.limit = value;
            // await this.getAuthors();
        },
    }
}
</script>

<style lang="scss" scoped>
:deep(.form-group) {
    #search-field {
        height: 40px;
    }

    span {
        top: 7px !important;
    }
}
</style>
