<template>
    <div>
        <LoadingStore />

        <Container has-border>
            <template v-slot:header-left>
                <Title>Notificações: {{ pagination.totalResults }}</Title>
                <Subtitle>Veja todas as notificações já enviadas.</Subtitle>
            </template>
            <template v-slot:content>
                <XgrowTable id="notificationsHistoryDatatable" min-height class="mt-2">
                    <template v-slot:header>
                        <th style="width: 200px;">Data da notificação</th>
                        <th style="width: 400px;">Título</th>
                        <th style="width: 200px;">Horário da notificação</th>
                        <th>Texto</th>
                    </template>
                    <template v-slot:body>
                        <template v-if="notifications.length">
                            <tr v-for="(notification, i) in notifications" :key="i">
                                <td>
                                    <p style="width: 200px;">{{ showDate(notification.run_at) }}</p>
                                </td>
                                <td>
                                    <p style="width: 400px; word-break: break-word;overflow: hidden;white-space: break-spaces;">{{ notification.title }}</p>
                                </td>
                                <td>
                                    <p style="width: 200px;">{{ showHour(notification.run_at) }}</p>
                                </td>
                                <td>
                                    <p>{{ notification.text }} </p>
                                </td>
                            </tr>
                        </template>
                        <NoResult v-else :colspan="8" title="Nenhuma notificação enviada!"
                            subtitle="Não há dados a serem exibidos. Vá até a guia de notificações, clique em nova notificação para adicionar." />
                    </template>
                    <template v-slot:footer>
                        <Pagination class="mt-4" :total-pages="pagination.totalPages" :total="pagination.totalResults"
                            :current-page="pagination.currentPage" @page-changed="onPageChange" @limit-changed="onLimitChange">
                        </Pagination>
                    </template>
                </XgrowTable>
            </template>
        </Container>
    </div>
</template>

<script>
import Container from '../../../../js/components/XgrowDesignSystem/Cards/Container.vue'
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue";
import LoadingStore from "../../../../js/components/XgrowDesignSystem/Utils/LoadingStore.vue";
import NoResult from "../../../../js/components/Datatables/NoResult.vue";
import Pagination from "../../../../js/components/Datatables/Pagination.vue";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import XgrowTable from "../../../../js/components/Datatables/Table.vue";

import axios from 'axios';
import moment from 'moment';
import formatTryCatchError from '../../../../js/components/XgrowDesignSystem/Mixins/formatTryCatchError';
import { useLoadingStore } from '../../../../js/store/components/loading';
import { mapActions, mapStores } from "pinia";

export default {
    name: 'push-notifications-forms',
    components: {
        Container,
        DefaultButton,
        LoadingStore,
        NoResult,
        Pagination,
        Subtitle,
        Title,
        XgrowTable,
    },
    data() {
        return {
            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25,
            },

            notifications: [],
        }
    },
    computed: {
        ...mapStores(useLoadingStore),
    },
    methods: {
        ...mapActions(useLoadingStore, ['setLoading']),
        onPageChange(page) {
            this.pagination.currentPage = page;
            this.getNotifications();
        },
        onLimitChange(offset) {
            this.pagination.limit = offset;
            this.pagination.currentPage = 1;
            this.getNotifications();
        },
        showDate(date) {
            return moment(date).format('DD/MM/YYYY');
        },
        showHour(date) {
            return moment(date).format('HH:mm');
        },
        async getNotifications() {
            const [ offset, page ] = [ this.pagination.limit, this.pagination.currentPage ];

            const params = { offset, page, is_sent: 1  }; //is_sent = 0 para agendamentos futuros && 1 para histórico

            this.loadingStore.setLoading(true);

            await axios.get(getNotifications, { params })
                .then(({ data }) => {
                    const { push_notifications } = data.response;
                    const { per_page, total, current_page } = push_notifications;
                    const { pagination } = this;
                    this.notifications = push_notifications.data;

                    pagination.currentPage = current_page;
                    pagination.limit = per_page;
                    pagination.totalResults = total;
                    pagination.totalPages = total > 1 ? Math.ceil(total / per_page) : 1;
                })
                .catch((error) => {
                    console.error(error);
                    errorToast("Ocorreu um erro na solicitação!", formatTryCatchError(error));
                });

            this.loadingStore.setLoading();
        },
    },
    async mounted() {
        await this.getNotifications()
    }
}
</script>