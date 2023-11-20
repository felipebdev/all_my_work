<template>
    <section>
        <Breadcrumb :items="breadcrumbs" class="mb-3" />
        <div class="xgrow-tabs nav nav-tabs mb-3">
            <a
                class="xgrow-tab-item nav-item nav-link"
                @click="setTab('notificationForms')"
                :class="{ active: tab === 'notificationForms' }"
            >
                Notificações
            </a>
            <a
                class="xgrow-tab-item nav-item nav-link"
                @click="setTab('notificationsHistory')"
                :class="{ active: tab === 'notificationsHistory' }"
            >
                Histórico de Mensagens
            </a>
            </div>
            <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show" :class="{ active: tab === 'notificationForms' }">
                <notificationsForms v-if="tab === 'notificationForms'" />
            </div>
            <div class="tab-pane fade show" :class="{ active: tab === 'notificationsHistory' }">
                <notificationsHistory v-if="tab === 'notificationsHistory'" />
            </div>
        </div>

    </section>
</template>

<script>
import Breadcrumb from '../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb.vue'
import notificationsForms from './tabs/notifications-forms.vue';
import notificationsHistory from './tabs/notifications-history.vue';

export default {
    name: 'push-notification',
    components: {
        Breadcrumb,
        notificationsForms,
        notificationsHistory
    },
    data() {
        return {
            /** Breadcrumbs */
            breadcrumbs: [
                { title: "Recursos", link: "/" },
                { title: "Push notification", link: false },
            ],

            tab: "notificationForms",
        }
    },
    methods: {
        setTab(value) {
            this.tab = value;
        },
    }
}
</script>

<style lang="scss" scoped>
    .nav-link {
        cursor: pointer;
    }
</style>