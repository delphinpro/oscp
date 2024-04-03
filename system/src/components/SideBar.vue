<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024.
  Licensed under MIT License
  --------------------------->

<script>
import { mapGetters, mapMutations, mapState } from 'vuex';

export default {
  name      : 'SideBar',
  components: {},

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

    <nav class="nav">
      <router-link :to="{ name: 'home' }" class="nav__item">Сводка</router-link>
      <router-link :to="{ name: 'modules' }" class="nav__item">Модули</router-link>
      <router-link :to="{ name: 'sites' }" class="nav__item">Сайты</router-link>
    </nav>

    <div v-if="selectedGroup && hasGroups">
      <hr>

      <nav v-if="activeGroups.length" class="nav">
        <span class="nav__title">Группы сайтов:</span>
        <button v-for="group in activeGroups"
            :class="{active: selectedGroup === group.name}"
            class="nav__item nav-item mono"
            @click="selectGroup(group.name)"
        >
          <span class="nav-item__text">
            <span v-if="group.name!=='TLD'">.</span>{{ group.name }}
          </span>
          <span class="nav-item__right">({{ group.count }})</span>
        </button>
      </nav>

      <nav v-if="activeGroups.length" class="nav">
        <span class="nav__title">Неактивные:</span>
        <button v-for="group in inactiveGroups"
            :class="{active: selectedGroup === group.name}"
            class="nav__item nav-item mono muted"
            @click="selectGroup(group.name)"
        >
          <span class="nav-item__text">
            <span v-if="group.name!=='TLD'">.</span>{{ group.name }}
          </span>
          <span class="nav-item__right">({{ group.count }})</span>
        </button>
      </nav>

    </div>

  </div>
</template>

<style lang="scss" scoped>
</style>
