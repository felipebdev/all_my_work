<template>
    <div class="d-flex flex-column gap-3">
        <SelectWithIcon id="selectWithIcon" :options="formDeliveryOptions" v-model="formDelivery"
            @input="updateFormDelivery" />
        <div class="xg-box" v-if="formDelivery === 'scheduled'">
            <div class="xg-container xg-content ">
                <i class="xg-fa fa-regular fa-calendar"></i>
                <div class="xg-content-inner">
                    <input type="number" v-model="frequency" class="xg-input" v-maska="'###'"
                        @input="$emit('update:frequency', $event.target.value)">
                    <span>dia{{ frequency > 1 ? 's' : '' }} após</span>
                    <select class="xg-select" v-model="deliveryModel"
                        @input="$emit('update:deliveryModel', $event.target.value)">
                        <option value="lastClass">última aula</option>
                        <option value="lastModule">último módulo</option>
                    </select>
                    <select class="xg-select" v-model="deliveryOption"
                        @input="$emit('update:deliveryOption', $event.target.value)">
                        <!-- <option value="startDateCourse">do inicio do curso</option>
                        <option value="whenSubscriberStart">após a compra</option> -->
                        <option value="specificDate">da data programada</option>
                    </select>
                    <input type="date" v-model.date="deliveredAt" class="xg-input-date"
                        @input="$emit('update:deliveredAt', $event.target.value)" v-if="deliveryOption === 'specificDate'"
                        @click="$event.target.showPicker()" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { maska } from "maska";
import SelectWithIcon from "./SelectWithIcon.vue";

export default {
    name: "DeliveryPeriod",
    components: { SelectWithIcon },
    directives: { maska },
    props: {
        frequency: {
            type: [Number, String],
            default: 2
        },
        deliveryModel: {
            type: String,
            default: 'lastClass'
        },
        deliveryOption: {
            type: String,
            default: 'specificDate'
        },
        formDelivery: {
            type: String,
            default: 'sequential'
        },
        deliveredAt: {
            type: String,
            default: new Date().toISOString().slice(0, 10)
        }
    },
    data() {
        return {
            formDeliveryOptions: [
                { value: 'sequential', name: 'Livre', img: '/xgrow-vendor/assets/img/icons/mdi-list-bulleted.svg' },
                { value: 'scheduled', name: 'Programada', img: '/xgrow-vendor/assets/img/icons/mdi-send-clock.svg' },
            ],
        }
    },
    methods: {
        updateFormDelivery: function (value) {
            this.$emit('update:formDelivery', value)
        }
    }
};
</script>

<style lang="scss" scoped>
.xg-box {
    min-width: 200px;
    max-width: 490px;
    position: relative;

    .xg-content {
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;

        &-inner {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    }

    .xg-container {
        position: relative;
        width: inherit;
        background-color: #252932;
        color: #FFFFFF;
        display: flex;
        align-items: center;
        padding: .3rem .6rem;
        border: 1px solid transparent;
        border-radius: 8px;
        box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 10px 0px, rgba(0, 0, 0, 0.1) 0px 0px 0px 1px;
        height: 38px;
        border: 1px solid #646D85;

        .xg-fa {
            pointer-events: none;
            font-size: 1.1rem;
            color: #93BC1E;
        }

        .xg-input {
            width: 40px;
            text-align: center;
            background-color: #252932;
            color: #FFF;
            border: none;

            &:hover,
            &:focus {
                background-color: #333844;
            }
        }

        .xg-input-date {
            width: 90px;
            padding: 0 !important;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-color: #252932;
            border: none;
            color: #FFFFFF;
            text-align: center;
            font-size: .9rem;

            &::-webkit-calendar-picker-indicator {
                display: none;
            }

            &:hover,
            &:focus {
                background-color: #333844;
                cursor: pointer;
            }
        }

        .xg-select {
            text-align: center;
            background-color: #252932;
            color: #93BC1E;
            border: none;
            font-size: .9rem;
            cursor: pointer;
            -webkit-appearance: none;
            -moz-appearance: none;
            margin: 0 .1rem;

            &:hover,
            &:focus {
                background-color: #333844;
                cursor: pointer;
            }
        }
    }
}

@media (max-width: 768px) {
    .xg-box {
        max-width: 300px;

        .xg-content {
            overflow: overlay;
        }

        .xg-container {
            height: 60px;
        }
    }
}
</style>
