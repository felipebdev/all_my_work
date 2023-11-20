<template>
    <router-link :to="{name: url}">
        <div class="lacard p-2 pb-3">
            <div class="lacard-img">
                <img :src="img" :alt="title">
            </div>
            <div class="lacard-text mt-2">
                <p>{{ title }}</p>
                <div class="lacard-divisor"></div>
                <p>{{ subtitle }}</p>
            </div>
        </div>
    </router-link>
</template>

<script>
export default {
    name: "PageButton",
    props: {
        img: {type: String, required: true},
        title: {type: String, required: true},
        subtitle: {type: String, required: true},
        url: {type: String, default: null}
    }
}
</script>

<style lang="scss" scoped>
a {
    color: transparent;
}

.lacard {
    background-color: #222429;
    border-radius: 10px;
    height: 100%;
    cursor: pointer;
    transition-duration: 0.2s;
    width: 215px;
    border: 1px solid transparent;

    .lacard-img {
        border-radius: 5px;
        background: radial-gradient(150.27% 150.27% at 42.04% 0%, #FFFFFF 0%, rgba(255, 255, 255, 0) 100%);
        opacity: 0.8;
        width: 100%;
        max-height: 185px;
        padding: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition-duration: 0.5s;

        img {
            max-height: 130px;
            max-width: 143px;
            opacity: 0.6;
            transition-duration: 0.5s;
        }
    }

    .lacard-text {
        p {
            text-align: center;
            transition-duration: 0.2s;
            color: #FFFFFF;

            &:first-child {
                font-weight: bold;
                font-size: 0.875rem;
            }

            &:last-child {
                font-size: 0.75rem;
            }
        }

        .lacard-divisor {
            border-bottom: 2px solid #FFFFFF;
            opacity: 0.05;
            margin: .75rem 0;
        }
    }

    &:hover,
    &:focus {
        border: 1px solid #ADFF2F;
        -webkit-box-shadow: 0 0 30px -20px #ADFF2F;
        -moz-box-shadow: 0 0 30px -20px #ADFF2F;
        box-shadow: 0 0 30px -20px #ADFF2F;

        .lacard-img {
            opacity: 1;

            img {
                opacity: 1;
            }
        }

        .lacard-text {
            p {
                color: #ADFF2F;
            }
        }
    }
}
</style>
