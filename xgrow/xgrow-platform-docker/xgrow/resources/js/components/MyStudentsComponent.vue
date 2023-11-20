<template>
    <div
        :class="[
      `col-xxl-${xxl}`,
      `col-xl-${xl}`,
      `col-lg-${lg}`,
      `col-md-${md}`,
      `col-sm-${sm}`,
      `col-${xs}`,
    ]"
    >
        <!-- TABS - cards grandes -->
        <ul class="activity-card-nav nav nav-tabs tabs-card-big" id="myTabCard" role="tablist">
            <li class="activity-card-item nav-item tab-card-big">
                <a class="activity-card-link nav-link active" id="online-tab-card" data-bs-toggle="tab"
                   href="#onlinecard" role="tab" aria-controls="online-card"
                   aria-selected="true">Online</a>
            </li>
            <li class="activity-card-item nav-item tab-card-big" v-if="false">
                <a class="nav-link" id="novos-tab-card" data-bs-toggle="tab" href="#novoscard"
                   role="tab" aria-controls="novos-card" aria-selected="false">Novos</a>
            </li>
        </ul>

        <div class="tab-content" id="myTabCardContent">
            <!--ONLINE-->
            <div class="tab-pane fade show active" id="onlinecard" role="tabpanel" aria-labelledby="online-card">
                <ul class="media-list">
                    <template v-if="myStudents.online.length > 0">
                        <li v-for="subscriber in myStudents.online" v-bind:key="subscriber.id"
                            class="d-flex align-items-center media">
                            <div v-if="subscriber.filename">
                                <img class="xgrow-user-avatar" :src="subscriber.filename" :alt="subscriber.name">
                            </div>
                            <div v-else>
                                <img class="xgrow-user-avatar" src="xgrow-vendor/assets/img/profile_default.jpg"
                                     :alt="subscriber.name">
                            </div>
                            <div class="media-body">
                                <p class="media-heading">{{ subscriber.name }}</p>
                                <p>{{ subscriber.email }}</p>
                            </div>
                        </li>
                    </template>
                    <template v-else>
                        <li>
                            Não há alunos online no momento.
                        </li>
                    </template>
                </ul>
            </div>


            <!--NOVOS-->
            <div class="tab-pane fade" id="novoscard" role="tabpanel" aria-labelledby="novos-tab-card">
                <ul class="media-list">
                    <template v-if="myStudents.news.length > 0">
                        <li v-for="subscriber in myStudents.news" v-bind:key="subscriber.id"
                            class="d-flex align-items-center media">
                            <div v-if="subscriber.filename">
                                <img class="xgrow-user-avatar" :src="subscriber.filename" :alt="subscriber.name">
                            </div>
                            <div v-else>
                                <img class="xgrow-user-avatar" src="xgrow-vendor/assets/img/profile_default.jpg"
                                     :alt="subscriber.name">
                            </div>
                            <div class="media-body">
                                <p class="media-heading">{{ subscriber.name }}</p>
                                <p>{{ subscriber.email }}</p>
                            </div>
                        </li>
                    </template>
                    <template v-else>
                        <li>
                            Não há novos alunos no período.
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
import bootstrapColumns from "./config/bootstrapColumnsProps";

export default {
    name: "my-student-component",
    props: {
        /*
         Bootstrap/responsiviness
         tags properties
         */
        ...bootstrapColumns,
        myStudents: {
            required: true,
            type: Object
        }
    },
};
</script>

<style lang="scss" scoped>
.media-list {
    padding: 10px;

    li {
        background-color: var(--none);
    }
}

.media-body {
    p {
        font-style: normal;
        font-weight: normal;
        font-size: 11px;
    }
}

.media-heading {
    color: var(--font-color-whited);
    font-weight: normal;
    font-size: 12.8px;
    line-height: 17px;
    text-decoration-line: underline;
    line-height: 17px;
}


.xgrow-user-avatar {
    object-fit: cover;
    border-radius: 50%;
    width: 23px;
    height: 23px;
}

.tab-content {
    background: #3B404E;
    border-radius: 5px;
    margin-top: 20px;
}

/* ========================== activity-card ==========================*/
.activity-card-nav.nav.nav-tabs.tabs-card-big {
    justify-content: flex-start;
}

.activity-card-nav.nav-tabs .nav-link {
    border: none;
    text-decoration: none; //
}

.activity-card-item.tab-card-big .nav-link {
    border-radius: 0px;
    border-bottom: 1px solid var(--font-color-whited);
}

.activity-card-item.tab-card-big .nav-link.active {
    border-radius: 0px;
    border-bottom: 1px solid var(--contrast-green3);
}

</style>
