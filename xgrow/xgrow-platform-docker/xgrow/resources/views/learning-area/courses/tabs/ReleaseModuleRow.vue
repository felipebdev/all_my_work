<template>
    <div class="d-flex w-100 align-items-baseline gap-3">
        <div class="text-center" style="width: 20px;">{{ item.position }}</div>
        <div class="text-uppercase w-100">{{ item.name }}</div>
    </div>
</template>

<script>

export default {
    name: "ReleaseModuleRow",
    props: {
        item: { type: Object },
        index: { type: Number }
    },
}
</script>
