<template>
  <div class="form-group" :class="{ 'no-label': label === '' || label === null }">
    <select
      :id="id"
      :class="firstTime ? 'first-time' : ''"
      @change="
        $emit('update:modelValue', $event.target.value);
        firstTime = false;
      "
      :disabled="disabled"
      :required="required"
    >
      <option value="" selected disabled>{{ placeholder }}</option>
      <option
        v-for="{ value, name } in options"
        :selected="modelValue === value"
        :key="value"
        :value="value"
      >
        {{ name }}
      </option>
    </select>
    <label :for="id">{{ label }}</label>
    <span v-if="icon" v-html="icon" :style="`color: ${iconColor}`"></span>
  </div>
</template>

<script>
export default {
  name: "Select",
  props: {
    id: {
      type: String,
      required: true,
    },
    label: {
      type: String,
      default: "",
    },
    placeholder: {
      type: String,
      default: "Selecione uma opção",
    },
    modelValue: {
      type: [String, Number, Boolean],
      default: "",
    },
    icon: {
      type: String,
      default: null,
    },
    iconColor: {
      type: String,
      default: "#ffffff",
    },
    type: {
      type: String,
      default: "text",
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    options: {
      type: Array,
      default: () => [],
    },

    required: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      firstTime: true,
    };
  },
};
</script>

<style scoped>
.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin: 12px 0;
  position: relative;
}

.form-group.no-label > select {
  padding-top: 0 !important;
}

select {
  height: 65px;
  background-color: #252932;
  border: none;
  border-bottom: 1px solid #ffffff;
  padding-left: 12px;
  padding-top: 24px;
  padding-right: 36px;
  color: #e7e7e7;
  transition: border 0.5s ease;
}

select::placeholder {
  color: #c1c5cf;
  font-weight: 400;
}

select:focus {
  border-bottom: 1px solid #93bc1e;
}

label {
  display: inline-block;
  position: absolute;
  color: #ffffff;
  font-size: 14px;
  padding-left: 12px;
  font-weight: 700;
  top: 6px;
}

select:focus ~ label,
select:focus ~ span {
  color: #93bc1e;
}

select:not(:placeholder-shown) {
  border-bottom-color: #f96c6c;
}

select:valid:not(:placeholder-shown) {
  border-bottom-color: #93bc1e;
  color: #e7e7e7;
}

select:valid:not(:placeholder-shown) ~ label,
select:valid:not(:placeholder-shown) ~ span {
  color: #93bc1e;
}

span {
  color: #ffffff;
  position: absolute;
  right: 12px;
  top: 18px;
}

select:disabled {
  cursor: not-allowed;
  background-color: #393d49;
  border-color: #595b63;
}

select:disabled ~ label,
select:disabled ~ span {
  color: #595b63;
}

.first-time {
  border-bottom-color: inherit !important;
}
</style>
