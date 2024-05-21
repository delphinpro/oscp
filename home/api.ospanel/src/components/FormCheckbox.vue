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
    label     : String,
    modelValue: [Array, Boolean],
    hint      : String,
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
  <div class="form-row">
    <div class="col-span-2 d-flex gap-1 align-items-center">
      <checkbox v-model="model" :label="label"/>
      <code v-if="hint" class="form-hint text-muted">{{ hint }}</code>
    </div>
  </div>
</template>
