<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024.
  Licensed under MIT License
  --------------------------->

<script>
import Checkbox from '@/components/Checkbox.vue';

export default {
  name      : 'FormCheckbox',
  components: { Checkbox },

  props: {
    label     : { type: String, default: '' },
    modelValue: Boolean,
    hint      : String,
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
  <div class="form-row">
    <div class="col-span-2 d-flex gap-1 align-items-center">
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
      <code v-if="hint" class="form-hint text-muted">{{ hint }}</code>
    </div>
  </div>
</template>
