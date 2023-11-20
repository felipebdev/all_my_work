<template>
    <div class="menu">
        <ul class="menu__list">
            <div class="logo">LOGO</div>
            <li
                v-for="item in menu"
                :key="item._id"
                class="menu__item"
            >
                <div class="info">
                    <div class="menu__item__icon">
                        <svg
                            v-if="getIconByName(item.icon, item.iconCategory)"
                            style="fill: #ADDF45"
                            :title="getIconByName(item.icon, item.iconCategory).name"
                            :view-box.camel="getIconByName(item.icon, item.iconCategory).viewBox"
                        >
                            <path :d="getIconByName(item.icon, item.iconCategory).svg"></path>
                        </svg>
                    </div>

                    <p class="menu__item__text">
                        {{item.title}}
                    </p>
                </div>
            </li>
        </ul>

        <div class="menu__fake-content">
            <div class="fake-title"></div>
            <div class="fake-subtitle"></div>
            <div class="fake-button"></div>
        </div>

        <div class="menu__action">
            <DefaultButton
                class="menu__action__button"
                @click="$emit('newItem')"
                icon="fas fa-plus"
                :outline="true"
                text="Adicionar novo link"
            />
        </div>
    </div>
</template>

<script>
import DefaultButton from "../../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";

import { mapActions, mapState, mapStores } from "pinia";
import { useDesignConfigMenu } from "../../../../../js/store/design-config-menu.js";

export default {
    name: "PreviewHorizontal",
    components: { DefaultButton },
    props: {
        menu: { type: Array, required: true },
    },
    data() {
        return { }
    },
    computed: {
        ...mapStores(useDesignConfigMenu),
        ...mapState(useDesignConfigMenu, [
            "contentTypeOptions",
            "courseOptions",
            "contentOptions",
        ]),
    },
    methods: {
        ...mapActions(useDesignConfigMenu, ["getIconByName"])
    },
}
</script>

<style lang="scss" scoped>
.menu {
    background: #252932;
    border: 5px dashed #626775;
    height: 350px;
    max-width: 600px;

    .logo {
        color: #252932;
        font-size: 16px;
        line-height: 22px;
        background: #3D4353;
        padding: 10px;
        font-weight: 700;
    }

    &__list {
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 25px;
        overflow-x: auto;
        background: #222429;
    }

    &__item {
        height: 26px;
        display: flex;
        justify-content: space-between;
        align-items: center;

        &__icon {
            width: 23px;
            height: 23px;
            color: #ADDF45;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        &__text {
            color: #FFFFFF;
            font-size: 16px;
            line-height: 26px;
            width: max-content;
        }

        .info {
            width: 100%;
            box-sizing: content-box;
            display: flex;
            gap: 10px;
        }
    }

    &__fake-content {
        margin-top: 10px;
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 14px;

        .fake-title, .fake-subtitle {
            height: 24px;
            background: #7A7F8D;
        }

        .fake-title {
            width: 70%;
        }

        .fake-subtitle {
            width: 48%;
        }
        .fake-button {
            background: #ADDF45;
            border-radius: 4px;
            width: 27%;
            height: 30px;
        }
    }

    &__action {
        width: 100%;
        margin-top: 10px;
        padding: 18px;

        &__button {
            width: 100%;
        }
    }
}
</style>
