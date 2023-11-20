<template>
    <div class="live-card d-flex flex-column gap-3 p-3 h-100 rounded-2 mb-4">
        <div class="live-card__header d-flex flex-column justify-content-between align-items-center pb-2"
            :style="`background-image: url('${live.thumbnail}')`">
            <div class="live-card__header_info d-flex justify-content-between align-items-center flex-wrap p-2">
                <span v-html="liveStatus"></span>
                <ButtonDetail>
                    <router-link :to="{ name: 'lives-edit', params: { id: live._id } }">
                        <li class="option">
                            <button class="option-btn">
                                <i class="fa fa-pencil"></i> Editar
                            </button>
                        </li>
                    </router-link>
                    <!-- <router-link :to="{name: 'lives-index'}">
                        <li class="option">
                            <button class="option-btn">
                                <i class="fa fa-times-circle"></i> Encerrar live
                            </button>
                        </li>
                    </router-link> -->
                    <li class="option">
                        <button class="option-btn" @click="$emit('delete', live._id)">
                            <i class="fa fa-trash text-danger"></i> Excluir live
                        </button>
                    </li>
                </ButtonDetail>
            </div>
            <div class="live-card__header_details d-flex align-items-center gap-3" v-if="isFuture">
                <i class="fa fa-satellite-dish"></i>
                <div>
                    <span>{{ dateFromNow }}</span>
                    <p>{{ literalDate }}</p>
                </div>
            </div>

        </div>
        <div class="live-card__info d-flex align-items-center gap-3">
            <img :src="live.authorImg ?? 'https://las.xgrow.com/background-default.png'" :alt="live.author"
                class="rounded-circle">
            <div class="live-card__info_name">
                <p>{{ live.title }}</p>
                <span>{{ live.author }}</span>
            </div>
        </div>
        <div class="live-card__stats d-flex gap-4 flex-wrap" v-if="false">
            <span><i class="fa fa-eye"></i> {{ live.views ?? 0 }}</span>
            <span><i class="fa fa-thumbs-up"></i> {{ live.likes ?? 0 }}</span>
            <span><i class="fa fa-comment"></i> {{ live.comments ?? 0 }}</span>
        </div>
        <div class="live-card__footer pt-3 d-flex gap-2 flex-wrap">
            <DefaultButton text="Acessar live" status="success" :on-click="() => accessLive()" />

            <router-link :to="{ name: 'lives-edit', params: { id: live._id } }">
                <DefaultButton text="Editar live" icon="fa fa-pencil" outline />
            </router-link>
        </div>
    </div>
</template>

<script>
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import ButtonDetail from "../../../../js/components/Datatables/ButtonDetail";
import moment from "moment";
import 'moment/locale/pt-br'
import { RouterLink } from 'vue-router'

export default {
    name: "LiveCard",
    components: { ButtonDetail, DefaultButton, Col, RouterLink },
    props: {
        live: { type: Object, required: true }
    },
    data() {
        return {
            isFuture: false
        }
    },
    computed: {
        liveStatus: function () {
            const now = Date.now();
            const onLive = now > new Date(this.live.date) && now < new Date(this.live.finishDate);

            if (onLive) {
                return '<span class="badge bg-danger live-badge"><i class="fa fa-satellite-dish"></i> AO VIVO</span>';
            }

            if (!onLive && now < new Date(this.live.finishDate) && this.live.isEnabled) {
                this.isFuture = true;
                return '<span class="badge bg-success live-badge">ATIVA</span>';
            }

            if (!onLive && now < new Date(this.live.finishDate) && !this.live.isEnabled) {
                this.isFuture = true;
                return '<span class="badge bg-danger live-badge">INATIVA</span>';
            }
        },
        literalDate: function () {
            return moment(this.live.date).format("DD [de] MMMM, HH:mm[h]");
        },
        dateFromNow: function () {
            // return moment(this.live.date).format("DD [de] MMMM, HH:mm[h]");
            return 'Ao vivo ' + moment(this.live.date).locale('pt-br').endOf('minutes').fromNow();
        },
        accessLive: function () {
            setTimeout(() => {
                window.location.href = this.live.link;
            }, 200);
        },
    }
}
</script>

<style lang="scss" scoped>
.live-card {
    background: #242832;
    width: 360px;

    &__header {
        height: 220px;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;

        &_info {
            width: 100%;
        }

        &_details {
            width: 90%;
            background: rgba(28, 27, 30, 0.8);
            backdrop-filter: blur(15px);
            padding: 10px 20px;
            border-radius: 5px;

            p {
                font-weight: 700;
            }

            svg {
                font-size: 1.25rem;
            }
        }
    }

    &__info {
        img {
            height: 64px;
            border: 2px solid #646D85;
        }
    }

    &__info_name {
        p {
            font-family: 'Open Sans', serif;
            font-style: normal;
            font-weight: 700;
            font-size: 1rem;
            line-height: 160%;
        }

        span {
            font-family: 'Open Sans', serif;
            font-style: normal;
            font-weight: 600;
            font-size: 0.75rem;
            line-height: 160%;
            color: #E8E8E8;
        }
    }

    &__stats {
        padding-left: 78px;
    }

    &__footer {
        border-top: 1px solid #424653;
    }
}
</style>
