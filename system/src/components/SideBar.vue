<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024.
  Licensed under MIT License
  --------------------------->

<script>
import { mapGetters, mapMutations, mapState } from 'vuex';

export default {
  name: 'SideBar',

  computed: {
    ...mapState({
      selectedGroup: state => state.sites.selected,
    }),
    ...mapGetters({
      groupNames: 'groupNames',
    }),
  },

  methods: {
    ...mapMutations({
      selectGroup: 'selectGroup',
    }),
  },
};
</script>

<template>
  <div>
    <nav>
      <router-link :to="{ name: 'home' }">Сводка</router-link>
      <router-link :to="{ name: 'modules' }">Модули</router-link>
      <router-link :to="{ name: 'sites' }">Сайты</router-link>
    </nav>
    <div v-if="selectedGroup && groupNames.length">
      <hr>
      <nav>
        <span class="text-muted">Группы сайтов:</span>
        <button v-for="name in groupNames"
            @click="selectGroup(name)"
            class="mono"
            :class="{active: selectedGroup === name}"
        ><span v-if="name!=='TLD'">.</span>{{ name }}
        </button>
      </nav>
    </div>
  </div>
</template>

<style scoped lang="scss">
nav {
  display: grid;
  gap: 4px;
  a,
  button {
    cursor: pointer;
    background: none;
    border: none;
    text-align: left;
    font-size: 1rem;
    line-height: 1.4;
    color: var(--body-color);
    text-decoration: none;
    border-radius: 8px;
    padding: 0.5rem 1.5rem;
    max-width: 100%;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;

    &.router-link-exact-active,
    &.active {
      background: #374151;
    }

    &:hover {
      background: #4b5563;
    }
  }
  > span {
    font-size: 0.9rem;
  }
}
</style>
