<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024.
  Licensed under MIT License
  --------------------------->

<script>
export default {
  name: 'Checkbox',

  props: {
    label     : { type: String, default: '' },
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
      <input :checked="isChecked"
          class="checkbox__input"
          type="checkbox"
          @change="$emit('update:modelValue', $event.target.checked)"
      >
      <span class="checkbox__indicator">
        <i class="bi bi-check"></i>
      </span>
      <span v-if="label" class="checkbox__text">{{ label }}</span>
    </label>
  </div>
</template>
