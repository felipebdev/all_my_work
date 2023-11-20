<template>
    <div>
        <button class="btn options" type="button" :id="`dropdownMenuButton-${id}`" data-bs-toggle="dropdown"
            aria-expanded="false" data-bs-offset="10,20">
            <i class="fas fa-ellipsis-v"></i>
        </button>
        <ul class="dropdown-menu" :aria-labelledby="`dropdownMenuButton-${id}`" role="menu">
            <slot></slot>
        </ul>
    </div>
</template>

<script>
import { v4 as uuidv4 } from 'uuid';

export default {
    name: "ButtonDetail",
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
.options {
    background-color: #222429;
    border-radius: 8px;
    color: #ffffff;
    font-size: 0.812rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 10px;
    width: 35px;
    text-decoration: none;
}

.dropdown-menu {
    border-radius: 0.5rem;
    background-color: #222429;
    border: 2px solid #222429;
    min-width: 250px;

    &:deep(.option) {
        color: #ffffff;

        .option-btn {
            background-color: #222429;
            color: #ffffff;
            width: 100%;
            padding: .5rem .75rem;
            text-align: left;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;

            &:hover {
                background-color: #1a1d1f;
                text-decoration: none;
            }
        }

        a {
            text-decoration: none;
            display: inline-block;

            &:hover {
                text-decoration: none;
            }
        }

        .option-btn>i,
        .option-btn>svg,
        .option-btn>.material-symbols-outlined {
            font-size: 14px;
            color: #93bc1e;
            margin-right: 5px;
        }
    }
}
</style>
