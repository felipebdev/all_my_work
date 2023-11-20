<template>
    <div class="ob-view">
        <span>Visualização</span>
        <div class="ob-view__type">
            <DefaultButton text="" icon="fas fa-desktop-alt" :class="{ 'active': device === 'desktop' }" status=""
                class="p-0" @click="$emit('device', 'desktop')" />
            <PipeVertical v-if="!noMobile" />
            <DefaultButton v-if="!noMobile" text="" icon="fas fa-mobile-alt" :class="{ 'active': device === 'mobile' }"
                status="" class="p-0" @click="$emit('device', 'mobile')" />
        </div>
    </div>
</template>

<script>
import PipeVertical from "../Utils/PipeVertical";
import DefaultButton from "../Buttons/DefaultButton";

export default {
    name: "PreviewPlatform",
    components: { DefaultButton, PipeVertical },
    props: {
        device: { type: String, default: 'desktop' },
        noMobile: { type: Boolean, default: false }
    }
}
</script>

<style lang="scss" scoped>
.ob-view {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.5rem;
    margin-bottom: 1rem;

    span {
        text-transform: uppercase;
        font-weight: 700;
        font-size: 0.75rem;
        line-height: 1rem;
    }

    &__type {
        display: flex;
        gap: 1rem;
        align-items: center;
        justify-content: center;
        padding: 4px 12px;
        border: 0.5px solid #818181;
        border-radius: 4px;

        button {
            font-size: 21px;
            color: #818181;
            cursor: pointer;

            &.active {
                color: #93BC1E;
            }
        }
    }
}
</style>
