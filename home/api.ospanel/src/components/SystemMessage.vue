<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024.
  Licensed under MIT License
  --------------------------->

<script>
import Alert from '@/components/Alert';
import { mapActions, mapState } from 'vuex';

export default {
  name: 'SystemMessage',

  components: {
    Alert,
  },

  computed: {
    ...mapState({
      sysMessage: state => state.sysMessage,
    }),

    title() { return this.sysMessage?.title; },
    message() { return this.sysMessage?.message; },
    style() { return this.sysMessage?.style; },
  },

  methods: {
    ...mapActions({
      hideSystemMessage: 'hideMessage',
    }),
    closeMessage() {
      this.hideSystemMessage();
    },
  },
};
</script>

<template>
  <div class="system-message">
    <Alert
        :danger="style==='danger'"
        :message="message"
        :success="style==='success'"
        :title="title"
    />
    <i class="bi bi-x-lg system-message__closer" @click="closeMessage()"></i>
  </div>
</template>

<style lang="scss">
.system-message {
  position: fixed;
  z-index: 99999;
  right: 0;
  bottom: 0;
  min-width: 30rem;
  max-width: 60%;
  padding: 1rem 0.25rem 1rem 1rem;

  &__closer {
    font-size: 1rem;
    line-height: 1;
    position: absolute;
    top: 1rem;
    right: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 3.2rem;
    cursor: pointer;
    transition: 0.25s ease;
    opacity: 0.4;
    border-top-right-radius: var(--radius);

    &:hover {
      opacity: 0.8;
    }
  }
}
</style>
