<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024.
  Licensed under MIT License
  --------------------------->

<script>
export default {
  name: 'Checkbox',

  // props: [
  //   'label',
  //   'modelValue',
  // ],

  props: {
    label     : {
      type   : String,
      default: '',
    },
    modelValue: Boolean,
  },

  emits: ['update:modelValue'],

  computed: {
    isChecked() {
      return this.modelValue;
    },
  },
};
</script>

<template>
  <div class="checkbox">
    <label class="checkbox__label">
      <input class="checkbox__input"
          type="checkbox"
          :checked="isChecked"
          @change="$emit('update:modelValue', $event.target.checked)"
      >
      <span class="checkbox__indicator">
        <i class="bi bi-check"></i>
      </span>
      <span class="checkbox__text" v-if="label">{{ label }}</span>
    </label>
  </div>
</template>

<style lang="scss">
.checkbox {
  display: flex;
  font-size: 1rem;
  line-height: 1.2rem;
  user-select: none;
  &__label {
    display: flex;
    gap: 0.5rem;
  }
  &__input {
    display: none;
  }
  &__indicator {
    width: 1.2rem;
    height: 1.2rem;
    border-radius: var(--radius-half);
    background: #1f2937;
    border: 1px solid var(--hr-color-light);
    display: flex;
    font-size: 1.5rem;
    line-height: 1rem;
    align-items: center;
    justify-content: center;
    color: var(--success-color);
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
