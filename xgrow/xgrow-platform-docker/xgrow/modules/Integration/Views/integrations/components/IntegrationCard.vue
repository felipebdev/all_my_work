<template>
    <div class="col mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2 justify-content-between border-bottom-0">
                <div class="title d-flex gap-1 align-items-center">
                    <img :src="integration.image" :alt="'Integração com' + integration.title">
                    <p>{{ integration.title }}</p>
                </div>
                <div class="details d-flex gap-2 align-items-center">
                    <div class="status" :class="{'active': false}">
                        {{ false ? "Ativa" : "Inativa" }}
                    </div>
                    <i
                        class="fa fa-ellipsis-v"
                        :id="'dropdownMenuButton-' + integration.id"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    ></i>
                    <ul class="dropdown-menu table-menu integration-dropdown-menu"
                        :aria-labelledby="'dropdownMenuButton-' + integration.id">
                        <li>
                            <a class="dropdown-item table-menu-item" href="javascript:void(0)" @click="null">
                                <i class="fa fa-pencil"></i> Editar
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item table-menu-item" href="javascript:void(0)" @click="null">
                                <i class="fas fa-check"></i> Conectar
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                {{ resumeDetail(integration.description, 100) }}
            </div>

            <template v-if="false">
                <div class="card-footer border-top-0">
                    <hr>
                    <div class="profile d-flex gap-2 align-items-center">
                        <img src="/xgrow-vendor/assets/img/integrations/profile.svg"
                             alt="Imagem indicando a quantidade de contas ativas">
                        <p>1 conta ativa</p>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
import CardComponent from "../../../../../resources/js/components/CardComponent";

export default {
    name: "IntegrationCard",
    components: {CardComponent},
    props: {
        integration: {required: true, type: Object}
    },
    computed: {
        setStatus: function () {
            // this.integration.status
            const num = Math.floor(Math.random() * 11);
            return num > 5;
        }
    },
    methods: {
        /** Resume the description for short sentence */
        resumeDetail: function (resume, length = 14) {
            const short = resume.substring(0, length);
            return resume.length > short.length ? short + "..." : resume;
        }
    },
    created() {
        console.log(this.integration);
    }
};
</script>

<style lang="scss" scoped>
.card {
    background: #333844;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    border-radius: 10px;
    padding: 10px;
}

.card-header {
    background: #333844;

    .title {
        img {
            height: 35px;
        }
    }

    .details {
        i {
            cursor: pointer;
            padding: 6px 11px;
            border-radius: 4px;
            background-color: #222429;
        }

        .status {
            border: 1px solid #FFFFFF;
            border-radius: 6px;
            padding: 2px 6px;
            font-size: 14px;
        }

        .active {
            border-color: #ADFF2F;
            color: #ADFF2F;
        }
    }
}

.card-body {
    padding: 1rem 1rem 0 1rem;
    font-size: 14px;
    line-height: 18px;
    color: #E7E7E7;
}

.card-footer {
    background: #333844;
    padding: 0 1rem 1rem 1rem;

    hr {
        border: 1px solid #414655;
        margin: 5px 0;
    }

    .profile {
        img {
            width: 18px;
            height: 18px;
        }
    }
}

.table-menu,
.table-menu-item {
    background-color: #222429 !important;

    a {
        color: #FFFFFF;

        i {
            color: #93BC1E;
        }
    }
}

.integration-dropdown-menu.show {
    //left: -130px !important;
    top: 5px !important;
}

</style>
