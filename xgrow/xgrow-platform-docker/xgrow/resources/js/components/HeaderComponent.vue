<template>
    <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="header" :class="{ 'simple-layout': simpleLayout }">
            <!-- Text and action button -->
            <div class="info">
                <p class="big-text">{{ headerText }}</p>
                <button
                    class="action-btn"
                    v-if="button.hasOwnProperty('action') && button.hasOwnProperty('text') && !simpleLayout"
                    @click.prevent="button.action">
                    <i v-if="button.hasOwnProperty('icon')" :class="[button.icon]"></i>
                    {{ button.text }}
                </button>
            </div>

            <!-- Filter combobox, search field and view mode toggle -->
            <div class="functions">
                <!-- Filter combobox -->
                <div class="filter" v-if="false">
                    <p class="small-text">FILTRAR:</p>
                    <xgrow-multiselect
                        :options="['Opção 1', 'Opção 2', 'Opção 3']"
                        placeholder="Selecione uma opção"
                        :searchable="true"
                    />
                </div>

                <!-- Search Field -->
                <div class="search">
                    <slot name="search"></slot>
                </div>

                <!-- View mode toggle -->
                <div class="view">
                    <p class="small-text">VISUALIZAÇÃO:</p>
                    <button
                        :class="[viewMode === 'list' ? 'active' : '']"
                        @click.prevent="changeViewMode('list')"
                    >
                        <i class="fas fa-list-ul"></i>
                    </button>
                    <button
                        :class="[viewMode === 'grid' ? 'active' : '']"
                        @click.prevent="changeViewMode('grid')"
                    >
                        <i class="fas fa-th-large"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

export default {
    name: "header-component",
    components: {
        "xgrow-multiselect": Multiselect,
    },
    props: {
        /** Layout Props */
        simpleLayout: {
            type: Boolean,
            required: false,
            default: false,
        },
        viewMode: {
            type: String,
            required: true,
        },
        changeViewMode: {
            type: Function,
            required: true,
        },
        /**
         * Button props value
         *
         * {
         *    text: "Display text",
         *    action: () => {},
         *    icon: "fas fa-plus" // Font-awesome pattern
         * }
         */
        button: {
            type: Object,
            required: false,
            default: {},
        },

        /** Value Props */
        label: {
            type: String,
            required: true,
        },
        totalResults: {
            type: [Number, String],
            required: false,
            default: null,
        },
    },
    computed: {
        headerText: function () {
            return this.totalResults !== null
                ? `${this.label}: ${this.totalResults}`
                : this.label;
        },
    },
};
</script>

<style scoped lang="scss">
@import "../../sass/util.scss";

.small-text {
    color: #ffffff;
    font-size: pxToRem(10px);
    line-height: pxToRem(16px);
    font-weight: 700;
}

.big-text {
    color: #ffffff;
    font-size: pxToRem(24px);
    line-height: pxToRem(32.68px);
    font-weight: 700;
}

.header,
.info,
.functions,
.filter,
.search,
.view {
    display: flex;
    align-items: center;
}

.header {
    justify-content: space-between;
    width: 100%;
    padding: 1.5rem 1.25rem;
    margin-bottom: pxToRem(24px);
    border-radius: pxToRem(8px);
    font-family: "Open Sans", serif;
    color: #ffffff;
    background-color: #2a2e39;
    flex-wrap: wrap;

    .functions,
    .info {
        width: 100%;
    }

    .functions {
        margin-top: pxToRem(16px);
        justify-content: space-between;
    }

    &.simple-layout {
        flex-wrap: nowrap;

        .functions,
        .info {
            width: auto;
        }

        .functions {
            justify-content: flex-end;
            margin-top: 0;
        }

        .info {
            justify-content: flex-start;
            padding-bottom: 0;
            border-bottom: none;
        }
    }
}

.info {
    .big-text {
        padding-right: pxToRem(16px);
    }

    justify-content: space-between;
    padding-bottom: pxToRem(16px);
    border-bottom: 2px solid #414655;
}

.functions {
    flex: 1;
    width: 100%;
}

.action-btn {
    color: #ffffff;
    padding: pxToRem(9px) pxToRem(16px);
    border-radius: pxToRem(8px);
    border: none;
    background: #93bc1e;
    font-weight: 700;
    font-size: pxToRem(14px);
    line-height: pxToRem(22.4px);

    &:hover {
        box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.25);
    }

    i {
        margin-right: pxToRem(5px);
    }
}

.view {
    button {
        background-color: #2a2e39;
        color: #646d85;
        font-size: pxToRem(20px);
        width: pxToRem(39px);
        height: pxToRem(39px);
        border: none;
        border-radius: pxToRem(8px);
        transition-duration: 0.2s;

        &.active {
            color: #93bc1e;
        }

        &:hover,
        &:focus {
            border: 1px solid #93bc1e;
            color: #93bc1e;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.25);
        }
    }

    p {
        padding-right: pxToRem(8px);
    }
}

.filter,
.search {
    flex: 1;
    max-width: 50%;
}

/** Styling search field for adapt to the header compact design */
.search {
    padding-right: pxToRem(16px);

    &:deep(.form-group) {
        width: 100%;
        margin: 0;

        label {
            display: none;
        }

        input {
            height: pxToRem(39px);
            padding-top: 0;
            font-size: pxToRem(14px);
        }

        span {
            top: pxToRem(7px);
        }
    }
}

/** Styling multiselect for adapt to the header compact design */
.filter {
    padding-right: pxToRem(16px);

    p {
        padding-right: pxToRem(8px);
    }

    .multiselect {
        font-size: pxToRem(14px) !important;
        min-height: 39px !important;
        border-bottom: 1px solid #ffffff !important;

        &.is-open.is-active {
            &:deep(.multiselect-search) {
                border-bottom: 1px solid #93bc1e !important;
            }

            &:deep(.multiselect-caret) {
                background-color: #93bc1e !important;
            }
        }

        &:deep(.multiselect-search) {
            min-height: 39px !important;
            border-bottom: 1px solid #ffffff !important;
        }

        &:deep(.multiselect-dropdown) {
            .multiselect-options {
                .multiselect-option {
                    span {
                        font-size: pxToRem(14px) !important;
                    }
                }
            }
        }

        &:deep(.multiselect-caret) {
            background-color: #ffffff !important;
        }
    }
}

/** Responsiveness Adjustment */
@media only screen and (max-width: 1200px) {
    .header {
        &.simple-layout {
            flex-wrap: wrap;

            .functions,
            .info {
                width: 100%;
            }

            .functions {
                margin-top: pxToRem(16px);
            }
        }
    }
}

@media only screen and (max-width: 900px) {
    .header {
        .functions {
            flex-wrap: wrap;

            .filter,
            .search,
            .view {
                padding: 0;
                max-width: 100%;
                width: 100%;
                flex: unset;
            }

            .search,
            .view {
                margin-top: 16px;
            }
        }
    }
}

@media only screen and (max-width: 768px) {
    .info {
        flex-wrap: wrap-reverse;
    }

    .action-btn {
        width: 100%;
        margin-bottom: pxToRem(16px);
    }
}
</style>
