<template>
    <div>
        <LoadingStore />

        <Container has-border>
                <template v-slot:header-left>
                    <Title>Notificações: {{ pagination.totalResults }}</Title>
                    <Subtitle>Veja todas as notificações cadastradas ou adicione novas.</Subtitle>
                </template>
                <template v-slot:header-right>
                    <DefaultButton status="success" icon="fas fa-plus" text="Nova notificação" @click="openEditOrCreateModal()" />
                </template>
                <template v-slot:content>
                    <XgrowTable id="notificationsFormDatatable" min-height class="mt-2">
                        <template v-slot:header>
                            <th style="width: 200px;">Data da notificação</th>
                            <th style="width: 400px;">Título</th>
                            <th style="width: 200px;">Horário da notificação</th>
                            <th>Texto</th>
                            <th style="width: 50px;"></th>
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
                                    <td>
                                        <ButtonDetail>
                                            <li class="option">
                                                <button class="option-btn"  @click="openEditOrCreateModal('edit', notification)">
                                                    <i class="fa fa-pencil"></i> Editar Notificação
                                                </button>
                                            </li>
                                            <li class="option">
                                                <button class="option-btn"
                                                    @click="openDeleteModal(notification.id)">
                                                    <i class="fa fa-trash text-danger"></i> Excluir Notificação
                                                </button>
                                            </li>
                                        </ButtonDetail>
                                    </td>
                                </tr>
                            </template>
                            <NoResult v-else :colspan="8" title="Nenhuma notificação encontrada!"
                                subtitle="Não há dados a serem exibidos. Clique em nova notificação para adicionar." />
                        </template>
                        <template v-slot:footer>
                            <Pagination class="mt-4" :total-pages="pagination.totalPages" :total="pagination.totalResults"
                                :current-page="pagination.currentPage" @page-changed="onPageChange" @limit-changed="onLimitChange">
                            </Pagination>
                        </template>
                    </XgrowTable>
                </template>
            </Container>

            <CreateOrEditNotificationModal
                v-if="notificationModal.isOpen"
                :form="notificationModal.form"
                :isEdit="notificationModal.isEdit"
                :isOpen="notificationModal.isOpen"
                @payload="notificationModal.isEdit ? updateNotification() : createNotification()"
                @close="closeModal"
            />

            <DeleteNotificationModal
                v-if="deleteModal.isOpen"
                :isOpen="deleteModal.isOpen"
                @close="closeModal"
                @confirm="deleteNotification(deleteModal.id)"
            />
    </div>
</template>

<script>
import ButtonDetail from "../../../../js/components/Datatables/ButtonDetail.vue";
import Container from '../../../../js/components/XgrowDesignSystem/Cards/Container.vue'
import CreateOrEditNotificationModal from '../modals/CreateOrEditNotification.vue';
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue";
import DeleteNotificationModal from '../modals/DeleteNotification.vue';
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
import * as Yup from 'yup';
import { pt } from 'yup-locale-pt';

Yup.setLocale(pt);

const notificationSchema = Yup.object().shape({
    title: Yup.string().required("O título é obrigatório"),
    text: Yup.string().required("O texto da mensagem é obrigatório"),
    date: Yup.string()
        .required('Definir a data é obrigatório')
        .test('date-validation', 'A data deve ser futura', (value, ctx) => {
            const currentDate = moment();
            const hour = moment(ctx.parent.time, 'HH:mm');
            const date = moment(value, 'DD/MM/YYYY').set({ hour: hour.hour(), minute: hour.minute() });
            return date.isValid() && date.isAfter(currentDate);
        }
    ),
    time: Yup.string().required('Definir a hora do envio é obrigatório'),
})

export default {
    name: 'push-notifications-forms',
    components: {
        ButtonDetail,
        Container,
        CreateOrEditNotificationModal,
        DefaultButton,
        DeleteNotificationModal,
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

            notificationModal: {
                isOpen: false,
                isEdit: false,
                form: {
                    title: '', text: '', time: '', date: ''
                }
            },

            deleteModal: {
                isOpen: false,
                id: ''
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
        closeModal() {
            this.notificationModal.isOpen = false;
            this.notificationModal.isEdit = false;
            this.notificationModal.form =  {
                title: '', text: '', time: '', date: ''
            }
            this.deleteModal.isOpen = false;
            this.deleteModal.id = '';
        },
        openEditOrCreateModal(type = 'create', notification) {
            this.notificationModal.isOpen = true;

            if (type == 'edit') {
                this.notificationModal.isEdit = true;

                const [ date, time ] = [this.showDate(notification.run_at), this.showHour(notification.run_at)]

                this.notificationModal.form = { ...notification, date, time  }
            }
        },
        openDeleteModal(id) {
            this.deleteModal.isOpen = true;
            this.deleteModal.id = id;
        },
        showDate(date) {
            return moment(date).format('DD/MM/YYYY');
        },
        showHour(date) {
            return moment(date).format('HH:mm');
        },
        async getNotifications() {
            const [ offset, page ] = [ this.pagination.limit, this.pagination.currentPage ];

            const params = { offset, page, is_sent: 0  }; //is_sent = 0 para agendamentos futuros && 1 para histórico

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
        async createNotification() {
            const { title, text, date, time } = this.notificationModal.form;

            const [ data, hour ] = [ moment(date, 'DD/MM/YYYY'), moment(time, 'HH:mm') ];

            const run_at = data.clone().set({ hour: hour.hour(), minute: hour.minute() }).toLocaleString();

            try {
                await notificationSchema.validate(this.notificationModal.form,  { abortEarly: false });
            }
            catch ({errors}) {
                errorToast('Erro de preenchimento', errors.join('\n'));
                return
            }

            this.loadingStore.setLoading(true);

            await axios.post(storeNotifications, { title, text, run_at })
                .then(({ data }) => {
                    successToast("Sucesso!", data.message);
                    this.getNotifications();
                    this.closeModal();
                })
                .catch((error) => {
                    console.error(error);
                    errorToast("Ocorreu um erro na solicitação!", formatTryCatchError(error));
                });

            this.loadingStore.setLoading();
        },
        async updateNotification() {
            const { title, text, date, time, id } = this.notificationModal.form;

            const [ data, hour ] = [ moment(date, 'DD/MM/YYYY'), moment(time, 'HH:mm') ];

            const run_at = data.clone().set({ hour: hour.hour(), minute: hour.minute() }).toLocaleString();

            try {
                await notificationSchema.validate(this.notificationModal.form,  { abortEarly: false });
            }
            catch ({ errors }) {
                errorToast('Erro de preenchimento', errors.join('\n'));
                return
            }

            this.loadingStore.setLoading(true);

            await axios.patch(updateNotifications.replace('notification_id', id), { title, text, run_at })
                .then(({ data }) => {
                    successToast("Sucesso!", data.message);
                    this.getNotifications();
                    this.closeModal();
                })
                .catch((error) => {
                    console.error(error);
                    errorToast("Ocorreu um erro na solicitação!", formatTryCatchError(error));
                });

            this.loadingStore.setLoading();
        },
        async deleteNotification(id) {
            this.loadingStore.setLoading(true);

            await axios.delete(deleteNotifications.replace('notification_id', id))
                .then(({ data }) => {
                    successToast("Sucesso!", data.message);
                    this.getNotifications();
                    this.closeModal();
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