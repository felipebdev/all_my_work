<template>
    <div class="mt-4" v-if="platforms.length > 0">
        <platform-component
            v-for="item in platforms" :key="item.producer_id"
            :image="item.platform_cover"
            :platform-name="item.platform_name"
            :learning-area-url="item.platform_url"
            :platform-id="item.platform_id"
            @get-id="getId"
            :env="env">
        </platform-component>
    </div>
    <div class="mt-4 xgrow-card card-dark" v-else>
        <div class="row my-3">
            <h4 class="text-center">Nenhuma coprodução encontrada...</h4>
            <small class="text-center">Para adicionar uma coprodução, vá para aba <b>"Pedido Pendentes"</b> e aceite uma
                coprodução.</small>
        </div>
    </div>
</template>

<script>
import PlatformComponent from "../../../js/components/PlatformComponent.vue";
import axios from "axios";

export default {
    name: "CoproductionsOwner",
    components: {
        "platform-component": PlatformComponent
    },
    props: {
        env: {required: false}
    },
    emits: ["getId"],
    data() {
        return {
            platforms: []
        };
    },
    methods: {
        /** Show transaction detail */
        getCoproducers: async function () {
            const res = await axios.get(coproducerOwnerUrl);
            this.platforms = res.data.response.platforms;
        },
        /** Get Id */
        getId: function (id) {
            this.$emit("getId", id);
        }
    },
    async created() {
        await this.getCoproducers();
    }
};
</script>

<style lang="scss"></style>
