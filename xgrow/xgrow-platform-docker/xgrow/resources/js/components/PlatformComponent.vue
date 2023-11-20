<template>
    <div class="platform-card px-3 pb-3 pt-4 my-5 my-sm-4 my-md-4 my-lg-4 my-xl-4">
        <span :class="['status', active ? '' : 'inactive']">
            {{ platformStatus }}
        </span>
        <div class="img mt-2 me-3">
            <img :src="image ?? 'https://las.xgrow.com/background-default.png'" />
        </div>
        <div class="info mt-2">
            <div class="texts">
                <p class="title">{{ platformName }}</p>
                <a :href="learningAreaUrl" class="url" target="_blank">
                    {{ learningAreaUrl }}
                </a>
            </div>
            <div class="buttons" v-if="!env">
                <a :href="void (0)" @click.prevent="getId(platformId)">
                    <i class="fas fa-arrow-right"></i>
                    Acessar plataforma
                </a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "platform-component",
    props: {
        active: {
            type: Boolean,
            required: false,
            default: true
        },
        platformName: {
            type: String,
            required: true
        },
        learningAreaUrl: {
            type: String,
            required: true
        },
        platformId: {
            type: String,
            required: true
        },
        image: {
            type: String,
            required: false,
            default: "https://las.xgrow.com/background-default.png"
        },
        env: {
            required: false
        }
    },
    emits: ["getId"],
    computed: {
        platformStatus: function () {
            return this.active ? "Publicada" : "Não publicada";
        },
        learningAreaUrlDisplay: function () {
            return this.learningAreaUrl.replace("https://", "").replace("http://", "");
        }
    },
    methods: {
        /** Get Id */
        getId: function (id) {
            this.$emit("getId", id);
        }
    }
};
</script>

<style lang="scss" scoped>
.platform-card {
    background-color: #2a2e39;
    border-radius: 10px;
    color: #FFF;
    box-sizing: border-box;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24) !important;
    position: relative;

    display: flex;
    flex-direction: row;
    align-items: stretch;
    justify-items: flex-start;

    .status {
        position: absolute;
        background-color: #93BC1E;
        font-weight: bold;
        font-size: 0.75em;
        line-height: 160%;
        padding: 7px 30px;
        min-width: 165px;
        border-radius: 6px;
        margin-top: -2.25rem;
        text-align: center;

        &.inactive {
            background-color: #E22222;
        }
    }

    .img {
        max-width: 165px;
        min-width: 165px;
        min-height: 100%;

        img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            border-radius: 10px;
        }
    }

    .info {
        flex: 1;

        .texts {
            border-bottom: 1px solid #222429;
            padding-bottom: 10px;
            margin-bottom: 20px;

            .title {
                font-weight: 700;
                font-size: 1.375rem;
                line-height: 1.873rem;
                color: #93BC1E;
                white-space: normal;
                word-break: normal;
            }

            .url {
                font-weight: 400;
                font-size: 1rem;
                line-height: 1.875rem;
                color: #e7e7e7;
                white-space: normal;
                word-break: break-all;
            }
        }

        .buttons {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            cursor: pointer;

            a {
                display: inline-block;
                background-color: #393D49;
                padding: 15px 25px;
                font-weight: 700;
                font-size: 0.8rem;
                line-height: 1.28rem;
                text-decoration: none;
                border-radius: 8px;
                color: #93BC1E;
                transition-duration: 0.2s;

                &:hover {
                    background-color: #313541;
                    -webkit-box-shadow: inset 0 0 0 2px #93BC1E;
                    -moz-box-shadow: inset 0 0 0 2px #93BC1E;
                    box-shadow: inset 0 0 0 2px #93BC1E;
                }

                i {
                    margin-right: 0.5rem;
                }
            }
        }
    }
}

@media only screen and (max-width: 576px) {
    .platform-card {
        flex-direction: column;

        .status {
            padding: 7px 50px;
            min-width: calc(100% - 32px);
        }

        .img {
            max-width: 100%;
            min-width: 100%;
            min-height: 105px;
            max-height: 105px;

            img {
                width: 100%;
                height: 105px;
            }
        }

        .info {
            .buttons {
                a {
                    width: 100%;
                    text-align: center;
                }
            }
        }
    }
}
</style>
