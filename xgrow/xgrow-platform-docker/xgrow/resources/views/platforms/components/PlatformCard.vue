<template>
    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
        <div class="card">
            <div class="info">
                <div class="left">
                    <img :src="image === null ? 'https://las.xgrow.com/background-default.png' : image" />
                </div>
                <div class="right">
                    <p class="title">{{ title }}</p>
                    <p class="date">Criado em: {{ formatDateBR }}</p>

                    <!-- <span class="inner-alert" v-if="pendingDoc">
                        <i class="fas fa-exclamation-circle"></i>
                        Documentos pendentes.
                    </span> -->

                    <div class="menu" v-if="canEdit">
                        <button :id="`dpo-${platformId}`" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu" :aria-labelledby="`dpo-${platformId}`">
                            <li>
                                <a class="dropdown-item" :href="void (0)" @click="$emit('getPlatform', platformId)"
                                    style="cursor: pointer">
                                    <i class="fas fa-pen"></i> Alterar imagem
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="buttons">
                <button @click="selectPlatform" ref="selectPlatform" data-redirect="home" :data-platform='platformId'>
                    <i class="fas fa-arrow-circle-right"></i>
                    Acessar plataforma
                </button>
                <a :href="learningAreaLink" target="_blank">
                    <i class="fas fa-book"></i>
                    Área de aprendizagem
                </a>
            </div>
        </div>
    </div>
</template>

<script>
import moment from "moment";

export default {
    name: "platform-card-component",
    props: {
        title: {
            type: String,
            required: true,
        },
        createdAt: {
            type: String,
            required: false,
            default: "",
        },
        pendingDoc: {
            type: Boolean,
            required: false,
            default: false,
        },
        learningAreaLink: {
            type: String,
            required: true,
        },
        platformId: {
            type: String,
            required: true,
        },
        image: {
            type: String,
            default: "https://las.xgrow.com/background-default.png",
        },
        canEdit: {
            type: Boolean,
            required: false,
            default: false,
        },
    },
    methods: {
        /** Redirect to correct platform */
        selectPlatform: function () {
            const platform = this.$refs.selectPlatform.getAttribute('data-platform');
            const redirect = this.$refs.selectPlatform.getAttribute('data-redirect');
            document.querySelector('#iptPlatform').value = platform;
            document.querySelector('#iptRedirect').value = redirect;
            document.querySelector('#formPlatform').submit();
        },
    },
    computed: {
        formatDateBR: function () {
            return moment(this.createdAt).format("DD/MM/YYYY");
        }
    }
};
</script>

<style scoped lang="scss">
@import "../../../sass/util.scss";

.inner-alert {
    font-size: pxToRem(10px);
    line-height: pxToRem(16px);
    font-weight: 600;
    color: #f0bb7d;
    background-color: #3d3736;
    padding: pxToRem(2px) pxToRem(4px);
    border-radius: pxToRem(4px);

    i {
        color: #e28a22;
    }
}

.card {
    background-color: #2a2e39;
    width: 100%;
    padding: pxToRem(8px);
    border-radius: pxToRem(8px);
    color: #ffffff;
    font-family: "Open Sans";
    margin-bottom: pxToRem(24px);
    transition-duration: 0.2s;

    &:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
    }

    .info {
        display: flex;

        .left {
            width: pxToRem(100px);
            height: pxToRem(100px);

            img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: center center;
                border-radius: pxToRem(8px);
            }
        }

        .right {
            padding-left: pxToRem(12px);
            position: relative;
            flex: 1;

            .title {
                font-size: pxToRem(16px);
                line-height: pxToRem(25.6px);
                font-weight: 600;
                padding-right: pxToRem(36px);
            }

            .date {
                font-size: pxToRem(10px);
                line-height: pxToRem(16px);
                font-weight: 600;
                margin-bottom: pxToRem(8px);
            }

            .menu {
                position: absolute;
                top: 0;
                right: 0;
                display: flex;
                flex-direction: column;
                align-items: flex-end;

                button {
                    width: pxToRem(32px);
                    height: pxToRem(32px);
                    border: none;
                    border-radius: pxToRem(6px);
                    background-color: #222429;
                    color: #ffffff;
                    font-size: pxToRem(8px) pxToRem(12px);
                    transition-duration: 0.2s;

                    &:focus,
                    &:hover {
                        border: 1px solid #ffffff;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
                    }
                }
            }

            .dropdown-menu {
                background-color: #222429;
                font-size: pxToRem(12.8px);
                border: 2px solid #222429;
                border-radius: pxToRem(8px);

                &.show {
                    transform: translate(-128px, 36px) !important;
                }

                a {
                    color: #ffffff;

                    i {
                        font-size: pxToRem(9.75px);
                        color: #93bc1e;
                        margin-right: pxToRem(5px);
                    }

                    &:hover {
                        background-color: #222429;
                    }
                }
            }
        }
    }

    .buttons {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        margin: 10px 0 5px 0;

        button,
        a {
            text-decoration: none;
            color: #ffffff;
            border: 2px solid transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background-color: #3D4353;
            border-radius: 6px;
            padding: 10px;
            font-size: 12px;
            font-weight: 500;
            width: 100%;

            &:hover,
            &:focus {
                border: 2px solid #93bc1e;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
            }
        }

        button>i,
        a>i {
            color: #93bc1e;
        }
    }
}

@media only screen and (max-width: 400px) {
    .card {
        .buttons {
            flex-direction: column;
            align-items: center;
            justify-content: center;

            &> :first-child {
                margin-right: 0;
                width: 100%;
            }

            &> :last-child {
                margin: pxToRem(5px) 0 0 0;
            }

            button,
            a {
                width: 100%;
            }
        }
    }
}
</style>
