<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024.
  Licensed under MIT License
  --------------------------->

<script>
export default {
  name: 'FormInput',

  props: {
    modelValue : String | Number,
    label      : String,
    hint       : String,
    desc       : String,
    required   : { type: Boolean, default: false },
    disabled   : { type: Boolean, default: false },
    placeholder: String,
    hasError   : { type: Boolean, default: false },
  },

  emits: [
    'update:modelValue',
  ],
};
</script>

<template>
  <div class="form-row">
    <label class="form-label">
      <span>
        {{ label }}
        <span v-if="required" class="req">*</span>
        <code v-if="hint" class="form-hint text-muted">{{ hint }}</code>
      </span>
    </label>
    <div>
      <input
          :class="{withError: hasError}"
          :disabled="disabled"
          :placeholder="placeholder"
          :required="required"
          :value="modelValue"
          class="input monospace"
          type="text"
          @input="$emit('update:modelValue', $event.target.value)"
      >
      <div v-if="desc" class="form-desc" v-html="desc"></div>
    </div>
  </div>
</template>
