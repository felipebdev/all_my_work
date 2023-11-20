<template>
    <div class="ob-view">
        <div class="ob-view__type">
            <button
                :class="{ 'active': type === 'ul' }"
                class="p-0" @click="$emit('change', 'ul')"
            >
                <i class="fa fa-list-ul"></i>
            </button>
            <button
                :class="{ 'active': type === 'ol' }"
                class="p-0" @click="$emit('change', 'ol')"
            >
                <i class="fas fa-list-ol"></i>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: "ToggleListButton",
    props: {
        type: { type: String, default: 'ul' },
    }
}
</script>

<style lang="scss" scoped>
.ob-view {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
    margin-bottom: 1rem;

    &__type {
        display: flex;
        align-items: center;
        justify-content: center;

        button {
            background: transparent;
            border: 1px solid #FFFFFF;
            color: #FFFFFF;
            cursor: pointer;
            display: block;
            font-size: 21px;
            height: 40px;
            padding: 10px;
            width: 40px;

            &.active {
                background: #93BC1E;
                border-color: transparent;
            }

            &:first-child {
                border-top-left-radius: 8px;
                border-bottom-left-radius: 8px;
            }

            &:last-child {
                border-top-right-radius: 8px;
                border-bottom-right-radius: 8px;
            }
        }
    }
}
</style>
