<template>
  <div class="xgrow-card steps">
    <ul>
      <li :key="step.id" v-for="(step, index) in steps">
        <div
          class="step"
          :class="[
            { 'step--active': step.id === activeStep },
            { 'step--completed': step.completed },
          ]"
        >
          <i v-if="step.completed" class="fas fa-check"></i>
        </div>
        {{ step.name }}
        <img
          v-if="steps.length !== index + 1"
          :src="step.completed ? imgStepCompleted : imgStep"
        />
      </li>
    </ul>
  </div>
</template>

<script>
import imgStep from "../../../../../public/xgrow-vendor/assets/img/icons/step-line.svg";
import imgStepCompleted from "../../../../../public/xgrow-vendor/assets/img/icons/step-line-completed.svg";

export default {
  props: {
    activeStep: {
      type: String,
      required: true,
      default: "step2",
    },
    steps: {
      type: Array,
      required: true,
      default() {
        return [
          { id: "step1", name: "Passo 1", completed: true },
          { id: "step2", name: "Passo 2", completed: false },
          { id: "step3", name: "Passo 3", completed: false },
        ];
      },
    },
  },
  data() {
    return {
      imgStep,
      imgStepCompleted,
    };
  },
};
</script>

<style lang="scss" scoped>
.steps {
  background-color: #252932;

  .step {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 34px;
    height: 34px;
    background: #333844;
    border: 4px solid #3d4353;
    border-radius: 50%;

    &--active,
    &--completed {
      border-color: #addf45;
    }

    &--completed {
      background: #addf45;
    }
  }

  ul {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 0px;
  }
  li {
    display: flex;
    align-items: center;
    gap: 14px;
    color: #fff;
  }
}
</style>
