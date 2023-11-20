<template>
    <tr>
        <td>
            <div class="info">
                <div class="imagem">
                    <img :src="image === null ? 'https://las.xgrow.com/background-default.png' : image" />
                </div>
                <p class="title">{{ title }}</p>
                <!-- <div class="inner-alert" v-if="pendingDoc">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="message">Documentos pendentes</span>
                </div> -->
            </div>
        </td>
        <td>
            <div class="mt-2">
                <p class="date">{{ formatDateBR }}</p>
            </div>
        </td>
        <td>
            <div class="buttons">
                <button @click="selectPlatform" ref="selectPlatform" data-redirect="home" :data-platform='platformId'
                    class="hack">
                    <i class="fas fa-arrow-circle-right"></i>
                    Acessar plataforma
                </button>
                <a :href="learningAreaLink" target="_blank" class="hack">
                    <i class="fas fa-book"></i>
                    Área de aprendizagem
                </a>
            </div>
        </td>
        <td v-if="canEdit">
            <div class="menu">
                <button :id="`dpl-${platformId}`" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                </button>

                <ul class="dropdown-menu" :aria-labelledby="`dpl-${platformId}`">
                    <li>
                        <a class="dropdown-item" :href="void (0)" @click="$emit('getPlatform', platformId)"
                            style="cursor: pointer">
                            <i class="fas fa-pen"></i> Alterar imagem
                        </a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
</template>

<script>
import moment from "moment";

export default {
    name: "platform-list-item-component",
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
        changeImage: function (id) {
            console.log(id);
        }
    },
    computed: {
        formatDateBR: function () {
            return moment(this.createdAt).format("DD/MM/YYYY");
        },
    },
};
</script>

<style scoped lang="scss">
@import "../../../sass/util.scss";

.inner-alert {
    cursor: pointer;
    position: relative;

    i {
        color: #e28a22;

        &:hover+.message {
            display: flex;
        }
    }

    .message {
        display: none;
        position: absolute;
        top: -35px;
        left: -5px;
        min-width: max-content;
        background-color: #1a1a1a;
        padding: pxToRem(5px) pxToRem(8px);
        border-radius: pxToRem(4px);
    }
}

.info {
    display: flex;
    align-items: center;
    min-width: 376px;

    .imagem {
        width: pxToRem(60px);
        height: pxToRem(42px);
        margin-right: pxToRem(10px);

        img {
            border-radius: pxToRem(4px);
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
        }
    }

    .title {
        margin-right: pxToRem(10px);
    }
}

.buttons {
    display: flex;
    align-items: center;
    justify-content: flex-end;

    &> :first-child {
        margin-right: pxToRem(5px);
        padding: pxToRem(6.5px) pxToRem(12px);
    }

    button,
    a {
        width: 180px;
        height: 36px;
        text-decoration: none;
        color: #ffffff;
        font-weight: 500;
        font-size: pxToRem(12px);
        line-height: pxToRem(19.2px);
        background-color: #3d4353;
        border: 2px solid #3d4353;
        padding: pxToRem(4.5px) 0;
        border-radius: pxToRem(6px);
        transition-duration: 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
        text-align: left;
        min-width: pxToRem(157px);

        i {
            font-size: pxToRem(12.5px);
            color: #93bc1e;
        }

        &:hover,
        &:focus {
            border: 2px solid #93bc1e;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.25);
        }
    }
}

.menu {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;

    button {
        right: 0;
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
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.25);
        }

        &:focus+.option {
            display: flex;
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
</style>
