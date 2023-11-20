<template>
    <div class="dropdown">
        <button :class="`btn ${status} dropdown-toggle`" type="button" :id="`dropdownMenu-${id}`"
                data-bs-toggle="dropdown" aria-expanded="false">
            {{ text }} <span class="vertical">|</span>
        </button>
        <ul v-if="menu.length > 0" class="dropdown-menu" :aria-labelledby="`dropdownMenu-${id}`">
            <li v-for="(item, i) in menu" :key="i">
                <template v-if="item.link">
                    <a class="dropdown-item d-flex gap-3" :href="item.link">
                        <img class="icon-menu" :src="item.icon" :alt="item.label"/>
                        {{ item.label }}
                    </a>
                </template>
                <template v-else>
                    <button class="dropdown-item d-flex gap-3" @click="item.click">
                        <img class="icon-menu" :src="item.icon" :alt="item.label"/>
                        {{ item.label }}
                    </button>
                </template>
            </li>
        </ul>
    </div>
</template>

<script>
import PipeVertical from "../Utils/PipeVertical";

export default {
    name: "DropdownButton",
    components: {PipeVertical},
    props: {
        id: {type: String, required: true},
        text: {type: String, required: true},
        status: {type: String, default: 'secondary'},
        menu: {type: Array, default: []}
    }
}
</script>

<style lang='scss' scoped>
.btn {
    height: 42px;
    border-radius: 8px;
    padding-left: 1.5rem;
    padding-right: 1.5rem;
    border: none;
    color: #FFFFFF;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);
    z-index: 2;

    .vertical {
        color: #7B9F12;
    }

    &:hover {
        filter: brightness(1.1);
        color: #FFFFFF;
    }

    &:disabled {
        background-color: #393d49 !important;
        color: #595b63 !important;
        cursor: not-allowed;
    }

    &.success {
        background: #93BC1E;
    }

    &.danger {
        background: #E22222;
    }

    &.warning {
        background: #E28A22;
    }

    &.info {
        background: #393D49;
    }

    &.dark {
        background: #222429;
    }
}

.outline {
    background-color: transparent;
    border: 1px solid #FFFFFF;

    &:hover {
        background: #E22222;
        filter: brightness(1.1);
        border: 1px solid #E22222;
    }
}

.btn:focus, .btn:active {
    outline: none !important;
    box-shadow: none;
}

ul {
    background-color: #93bc1d;
    font-weight: 600;
    border: none;
    z-index: 1;
    margin-top: -1px;
    border-radius: 0 6px 6px 6px;

    li {
        .dropdown-item {
            color: #FFFFFF;
            font-weight: 600;
            box-shadow: none;
            border-radius: 0;

            &:hover {
                background: #A6C64D;
                text-decoration: none;
                color: #FFFFFF;
            }

            .icon-menu {
                width: 24px;
                height: 24px;
            }
        }
    }
}
</style>
