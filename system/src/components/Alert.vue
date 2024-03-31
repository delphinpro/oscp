<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024.
  Licensed under MIT License
  --------------------------->
<script>
export default {
  name    : 'Alert',
  props   : {
    danger : { type: Boolean, default: false },
    success: { type: Boolean, default: false },
    title  : { type: String, default: '' },
  },
  computed: {
    classes() {
      return {
        alert_danger : this.danger,
        alert_success: this.success,
      };
    },
  },
};
</script>

<template>
  <div class="alert" :class="classes">
    <div class="alert__heading" v-if="title">{{ title }}</div>
    <slot/>
  </div>
</template>

<style lang="scss">
.alert {
  background: #1f2937;
  padding: 1rem 2.25rem 1rem 1rem;
  border-radius: var(--radius);
  box-shadow: 0 2px 5px rgba(black, 0.3);

  &__heading {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
  }

  &_danger {
    background: #b53c3c;
  }
  &_success {
    background: #347618;
  }
}
</style>
