<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024.
  Licensed under MIT License
  --------------------------->

<script>
import Alert from '@/components/Alert';

export default {
  name: 'SystemMessage',

  components: {
    Alert,
  },

  computed: {
    title() {
      return '';
    },
    message() {
      return this.$store.state.sysMessage;
    },
  },

  methods: {
    closeMessage() {

    },
  },
};
</script>

<template>
  <div class="system-message">
    <Alert :title="title" success>
      {{ message }}
    </Alert>
    <i class="bi bi-x-lg system-message__closer" @click="closeMessage()"></i>
  </div>
</template>

<style lang="scss">
.system-message {
  padding: 1rem 0.25rem 1rem 1rem;
  position: fixed;
  right: 0;
  top: 0;
  max-width: 60%;
  z-index: 99999;

  &__closer {
    position: absolute;
    right: 0.25rem;
    top: 1rem;
    background: rgba(#000, 0.1);
    border-top-right-radius: var(--radius);
    width: 2rem;
    height: 2rem;
    font-size: 1.5rem;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
  }
}
</style>
