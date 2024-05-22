<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024.
  Licensed under MIT License
  --------------------------->

<script>
export default {
  name: 'FormSelect',

  props   : {
    label     : String,
    modelValue: String,
    hint      : String,
    options   : Array,
    required  : { type: Boolean, default: false },
    empty     : String,
    valueKey  : { type: String, default: 'value' },
    textKey   : { type: String, default: 'text' },
    group     : { type: Boolean, default: false },
  },
  emits   : [
    'update:modelValue',
  ],
  computed: {
    model: {
      get() { return this.modelValue; },
      set(value) { this.$emit('update:modelValue', value); },
    },
  },
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
      <select
          v-model="model"
          class="select"
      >
        <option v-if="empty" value="">{{ empty }}</option>
        <template v-if="group">
          <optgroup v-for="(group, name) in options" :label="name">
            <option v-for="option in group" :value="option[valueKey]">{{ option[textKey] }}</option>
          </optgroup>
        </template>
        <template v-else>
          <option v-for="option in options" :value="option[valueKey]">{{ option[textKey] }}</option>
        </template>
      </select>
    </div>
  </div>
</template>

<style lang="scss" scoped>

</style>
