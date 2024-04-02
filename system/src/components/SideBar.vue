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

    hasGroups() {
      return !!this.groupNames.length;
    },

    activeGroups() {
      return this.groupNames.filter(group => group['hasActive']);
    },

    inactiveGroups() {
      return this.groupNames.filter(group => !group['hasActive']);
    },
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

    <div v-if="selectedGroup && hasGroups">
      <hr>

      <nav v-if="activeGroups.length">
        <span class="text-muted">Группы сайтов:</span>
        <button v-for="group in activeGroups"
            @click="selectGroup(group.name)"
            class="mono"
            :class="{active: selectedGroup === group.name}"
        >
          <span><span v-if="group.name!=='TLD'">.</span>{{ group.name }}</span>
          <span class="text-muted">({{ group.count }})</span>
        </button>
      </nav>

      <nav v-if="activeGroups.length">
        <span class="text-muted">Неактивные:</span>
        <button v-for="group in inactiveGroups"
            @click="selectGroup(group.name)"
            class="mono muted"
            :class="{active: selectedGroup === group.name}"
        >
          <span><span v-if="group.name!=='TLD'">.</span>{{ group.name }}</span>
          <span class="text-muted">({{ group.count }})</span>
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
    padding: 0.5rem 1rem;
    max-width: 100%;
    min-width: 0;
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    gap: 0.5rem;

    > span {
      &:first-child {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        min-width: 0;
      }
      &:last-child {
        font-size: 0.8em;
      }
    }

    &.muted {
      color: var(--muted-color);
    }

    &.router-link-exact-active,
    &.active {
      color: var(--body-color);
      background: #374151;
    }

    &:hover {
      color: var(--body-color);
      background: #4b5563;
    }
  }
  > span {
    font-size: 0.9rem;
    display: block;
    margin: 0.5rem 0;
  }
}
</style>
