<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024.
  Licensed under MIT License
  --------------------------->

<script>
export default {
  name: 'Checkbox',

  props: {
    label     : String,
    value     : String,
    modelValue: [Array, Boolean],
  },

  emits: ['update:modelValue'],

  computed: {
    model: {
      get() {
        return this.modelValue;
      },
      set(value) {
        this.$emit('update:modelValue', value);
      },
    },
  },
};
</script>

<template>
  <div class="checkbox">
    <label class="checkbox__label">
      <input
          v-model="model"
          :value="value"
          class="checkbox__input"
          type="checkbox"
      >
      <span class="checkbox__indicator">
        <i class="bi bi-check"></i>
      </span>
      <span v-if="label" class="checkbox__text">{{ label }}</span>
    </label>
  </div>
</template>

<style lang="scss" scoped>
.checkbox {
  font-size: 1rem;
  line-height: 1.2rem;
  display: flex;
  user-select: none;
  &__label {
    display: flex;
    gap: 0.5rem;
  }
  &__input {
    display: none;
  }
  &__indicator {
    font-size: 1.5rem;
    line-height: 1rem;
    display: flex;
    align-items: center;
    flex-shrink: 0;
    justify-content: center;
    width: 1.2rem;
    height: 1.2rem;
    color: var(--success-color);
    border: 1px solid var(--hr-color-light);
    border-radius: var(--radius-half);
    background: #1f2937;
    .bi { display: none; }
  }
  &__label:hover &__indicator {
    box-shadow: 0 0 3px rgba(#22c55e, 0.5);
  }
  &__input:checked ~ &__indicator {
    .bi { display: block; }
  }
}
</style>
