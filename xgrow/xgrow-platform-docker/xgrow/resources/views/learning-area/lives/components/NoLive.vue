<template>
    <div class="w-100 py-3 d-flex gap-3 align-items-center justify-content-center flex-column text-center">
        <img src='/xgrow-vendor/assets/img/no-lives.svg' alt='Você não possui lives no momento!'/>
        <Title>Você não possui lives no momento :(</Title>
        <Subtitle>
            Mas não se procupe, você pode criar uma a qualquer momento<br>
            clicando no botão acima.
        </Subtitle>
    </div>
</template>

<script>
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";

export default {
    name: "NoLive",
    components: {Subtitle, Title}
}
</script>

<style scoped>

</style>
