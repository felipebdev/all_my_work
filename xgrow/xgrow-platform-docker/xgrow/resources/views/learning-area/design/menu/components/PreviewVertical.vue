<template>
    <div class="menu">
        <div class="menu__header">
            <div class="user-info">
                <div class="user-info__image">
                    <i class="fas fa-user"></i>
                </div>

                <div class="user-info__text">
                    <h1 class="user-info__name">Nome do usuário</h1>
                    <p class="user-info__email">emailusuario@exemplo.com</p>

                    <label>
                        <div class="progress-bar">
                            <span class="progress-bar__fill"></span>
                        </div>
                        <p class="progress-bar__label">Progresso do usuário</p>
                    </label>
                </div>
            </div>
        </div>
        <Draggable
            class="menu__list"
            :list="menu"
            item-key="position"
            ghost-class="ghost"
            @start="drag = true"
        >
            <template v-slot:item="{ element, index }">
                <li class="menu__item" :id="index">
                    <div class="info">
                        <div class="menu__item__icon">
                            <svg
                                v-if="getIconByName(element.icon, element.iconCategory)"
                                style="fill: #ADDF45"
                                :title="getIconByName(element.icon, element.iconCategory).name"
                                :view-box.camel="getIconByName(element.icon, element.iconCategory).viewBox"
                            >
                                <path :d="getIconByName(element.icon, element.iconCategory).svg"></path>
                            </svg>
                        </div>

                        <p class="menu__item__text">
                            {{ element.title }}
                        </p>
                    </div>

                    <div
                        class="actions"
                        v-if="element.type != 'live' && element.type != 'forum'"
                    >
                        <div class="actions__edit"
                            @click="$emit('editItem', {
                                element,
                                icon: getIconByName(element.icon, element.iconCategory),
                            })"
                        >
                            <i class="fas fa-pen"></i>
                        </div>
                        <div
                            class="actions__delete"
                            @click="$emit('delete', element)"
                        >
                            <i class="fas fa-trash"></i>
                        </div>
                    </div>
                </li>
            </template>
        </Draggable>
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
import Draggable from "vuedraggable";

import { mapActions, mapState, mapStores } from "pinia";
import { useDesignConfigMenu } from "../../../../../js/store/design-config-menu.js";


export default {
    name: "PreviewVertical",
    components: { DefaultButton, Draggable },
    props: {
        menu: { type: Array, required: true },
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
};
</script>

<style lang="scss" scoped>
.menu {
    background: #252932;
    border: 5px dashed #626775;
    height: 500px;
    display: flex;
    flex-direction: column;

    &__header {
        padding: 18px;
        background: #222429;
        color: #e7e7e7;
    }

    &__list {
        padding: 18px;
        margin-right: 6px;
        padding-right: 8px;
        margin-top: 10px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-height: 267px;
        overflow-y: auto;
        min-height: 255px;
    }

    &__item {
        height: 26px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #333844;
        user-select: none;
        cursor: grab;

        &:active {
            cursor: grabbing;
        }

        &__icon {
            width: 23px;
            height: 23px;
            color: #addf45;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        &__text {
            color: #ffffff;
            font-size: 16px;
            line-height: 26px;
        }

        .info {
            width: 100%;
            display: flex;
            gap: 10px;
        }

        .actions {
            display: flex;

            &__edit,
            &__delete {
                width: 23px;
                height: 23px;
                display: flex;
                align-items: center;
                justify-content: center;

                & > svg {
                    width: 15px;
                    height: 15px;
                    cursor: pointer;
                }
            }

            &__edit {
                color: #addf45;
                border-right: 1px solid #3d4353;
            }

            &__delete {
                color: #f96c6c;
            }
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

.user-info {
    display: flex;
    gap: 16px;

    &__image {
        min-width: 52px;
        width: 52px;
        height: 52px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #222429;
        background: #3d4353;
        border-radius: 50%;
        padding: 6px;
        font-size: 16px;
        margin-top: 5px;

        & > svg {
            border-radius: inherit;
        }
    }

    &__text,
    label {
        width: 100%;
    }

    &__name {
        font-weight: 600;
        font-size: 14px;
        line-height: 19px;
    }

    &__email {
        font-size: inherit;
        color: #c1c5cf;
        font-size: 14px;
        line-height: 19px;
        margin-bottom: 10px;
    }
}

.progress-bar {
    border-radius: 100px;
    height: 5px;
    width: 100%;
    background: #333844;

    &__fill {
        display: block;
        width: 60%;
        height: inherit;
        background: #addf45;
        border-radius: inherit;
    }

    &__label {
        margin-top: 4px;
        font-size: 12px;
        line-height: 19px;
        color: #c1c5cf;
    }
}
</style>
