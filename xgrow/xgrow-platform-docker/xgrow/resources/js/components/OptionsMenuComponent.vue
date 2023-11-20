<template>
    <button class="btn options" type="button" :id="`dropdownMenuButton-${id}`" data-bs-toggle="dropdown"
            aria-expanded="false">
        <i class="fas fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu" :aria-labelledby="`dropdownMenuButton-${id}`" role="menu">
        <slot></slot>
    </ul>
</template>

<script>
import {v4 as uuidv4} from 'uuid';

export default {
    name: "options-menu-component",
    data() {
        return {
            id: null
        }
    },
    created() {
        this.id = uuidv4()
    }
};
</script>

<style scoped lang="scss">
@import "../../sass/util.scss";

.options {
    background-color: #222429;
    border-radius: 8px;
    color: #ffffff;
    font-size: pxToRem(12.8px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 10px;
    width: 35px;
}

.dropdown-menu {
    border-radius: pxToRem(8px);
    background-color: #222429;
    border: 2px solid #222429;
    min-width: 250px;

    &:deep(.option) {
        color: #ffffff;

        .option-btn {
            background-color: #222429;
            color: #ffffff;
            width: 100%;
            padding: .5rem .75rem ;
            text-align: left;
            border: none;

            &:hover {
                background-color: #1a1d1f;
            }
        }

        a {
            text-decoration: none;
            display: inline-block;
        }

        .option-btn > i, .option-btn > .material-symbols-outlined {
            font-size: 12px;
            color: #93bc1e;
            margin-right: 5px;
        }

        .option-btn > .material-symbols-outlined{
            font-size: 14px;
        }
    }
}


</style>
