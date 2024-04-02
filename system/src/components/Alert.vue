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
  <div class="alert" :class="classes">
    <div class="alert__heading" v-if="title">{{ title }}</div>
    <div class="alert__body" v-if="message" v-html="message"></div>
  </div>
</template>

<style lang="scss">
.alert {
  background: #1f2937;
  border: 1px solid #10161d;
  padding: 1rem 2.25rem 1rem 1rem;
  border-radius: var(--radius);
  box-shadow: 0 3px 10px rgba(#000, 0.5);

  &__heading {
    font-size: 1.3em;
  }

  &__heading + &__body {
    margin-top: 0.5em;
  }

  &_danger {
    background: #b53c3c;
    border-color: #b53c3c;
  }
  &_success {
    background: #347618;
    border-color: #347618;
  }
}
</style>
