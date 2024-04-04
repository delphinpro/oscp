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
    message: { type: String, default: '' },
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
  <div :class="classes" class="alert">
    <div v-if="title" class="alert__heading">{{ title }}</div>
    <div v-if="message" class="alert__body" v-html="message"></div>
  </div>
</template>

<style lang="scss">
.alert {
  overflow: hidden;
  padding: 1rem 3.25rem 1rem 1rem;
  border: 1px solid #10161d;
  border-radius: var(--radius);
  background: #1f2937;
  box-shadow: 0 3px 10px rgba(#000, 0.5);

  &__heading {
    font-size: 1.3em;
  }

  &__heading + &__body {
    margin-top: 0.5em;
  }

  &__body {
    opacity: 0.7;
  }

  &_danger {
    border-color: #b53c3c;
    background: #b53c3c;
  }
  &_success {
    border-color: #347618;
    background: #347618;
  }
}
</style>
