<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2023-2024.
  Licensed under MIT License
  --------------------------->

<script>
import DomainsList from '@/components/DomainsList';
import { mapState } from 'vuex';

export default {
  name: 'SitesView',

  components: {
    DomainsList,
  },

  data: () => ({
    filter: null,
  }),

  computed: {
    ...mapState({
      isGrouped   : state => state.sites.grouped,
      allSites    : state => state.sites.sites,
      currentGroup: state => state.sites.selected,
    }),

    isReady() {
      return this.allSites !== null && this.allSites !== undefined;
    },

    selectedSites() {
      if (!this.isGrouped) return this.allSites;

      if (!this.currentGroup) return [];
      return this.allSites[this.currentGroup]['domains'];
    },

    filteredSites() {
      if (!this.filter) return this.selectedSites;
      return this.selectedSites.filter(site => site.host.includes(this.filter));
    },

    activeDomains() {
      return this.filteredSites.filter(domain => domain.isActive);
    },

    problemDomains() {
      return this.filteredSites.filter(domain => domain.isProblem);
    },

    disabledDomains() {
      return this.filteredSites.filter(domain => domain.isDisabled);
    },
  },

  created() {
    this.$store.commit('setPageTitle', 'Сайты');
    this.$store.dispatch('loadSites');
    this.filter = JSON.parse(localStorage.getItem('site_filter') ?? '');
  },

  unmounted() {
    this.$store.commit('resetGroup');
  },

  methods: {
    saveFilter() {
      localStorage.setItem('site_filter', JSON.stringify(this.filter));
    },
  },

};

</script>

<template>
  <div v-if="isReady">
    <div class="d-flex gap-0.5">
      <input class="input" type="text" v-model="filter" @change="saveFilter" placeholder="Поиск сайта">
      <a class="btn">
        <i class="bi bi-plus-lg"></i>
        <span class="text-nowrap">Добавить сайт</span>
      </a>
    </div>
    <div class="domains" v-if="activeDomains.length">
      <DomainsList :domains="activeDomains"/>
    </div>
    <div class="domains" v-if="problemDomains.length">
      <h4>Сайты с ошибками</h4>
      <DomainsList :domains="problemDomains"/>
    </div>
    <div class="domains" v-if="disabledDomains.length">
      <h4>Отключённые сайты</h4>
      <DomainsList :domains="disabledDomains"/>
    </div>
  </div>
</template>

<style lang="scss"></style>
