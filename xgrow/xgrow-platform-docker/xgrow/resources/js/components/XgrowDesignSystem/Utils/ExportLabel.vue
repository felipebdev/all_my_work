<template>
    <div class="export-buttons d-flex align-items-center">
        <span class="vertical"></span>
        <span class="export">EXPORTAR:</span>
        <slot></slot>
    </div>
</template>

<script>
export default {
    name: "ExportLabel"
}
</script>

<style lang="scss" scoped>
.vertical {
    margin: 0 10px 0 0;
    width: 2px;
    height: 36px;
    background: #414655;
}
.export{
    font-size: .75rem;
    color: #646D85;
    margin: 0 10px 0 0;
    font-weight: 700;
}
</style>
