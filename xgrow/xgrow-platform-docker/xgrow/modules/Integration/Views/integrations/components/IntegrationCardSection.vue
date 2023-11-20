<template>
    <div class="row my-3">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <h4>{{ title }}</h4>
            <hr>
        </div>
    </div>
</template>

<script>
export default {
    name: "IntegrationCardSection",
    props: {
        title: {required: true, type: String}
    }
};
</script>

<style scoped>
h4{
    font-size: 16px;
    font-weight: 700;
}
hr {
    border: 1px solid #414655;
    margin: 10px 0;
}
</style>
