<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2023-2024.
  Licensed under MIT License
  --------------------------->

<script>
import Checkbox from '@/components/Checkbox.vue';
import DomainsList from '@/components/DomainsList';
import { mapActions, mapState } from 'vuex';

export default {
  name: 'SitesView',

  components: {
    Checkbox,
    DomainsList,
  },

  data: () => ({
    filter      : null,
    hideDisabled: false,
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

  watch: {
    hideDisabled(v) {
      localStorage.setItem('sites_hideDisabled', v);
    },
  },

  async created() {
    this.hideDisabled = localStorage.getItem('sites_hideDisabled') === 'true';
    this.$store.commit('setPageTitle', 'Сайты');
    await this.showLoader();
    await this.loadSites();
    await this.hideLoader();
    this.filter = JSON.parse(localStorage.getItem('site_filter') ?? '');
  },

  methods: {
    ...mapActions({
      showLoader: 'showLoader',
      hideLoader: 'hideLoader',
      loadSites : 'loadSites',
    }),
    saveFilter() {
      localStorage.setItem('site_filter', JSON.stringify(this.filter));
    },
  },

};

</script>

<template>
  <div v-if="isReady">
    <div class="sites-bar">
      <checkbox v-model="hideDisabled" label="Скрыть отключённые"/>
      <input v-model="filter" class="input" placeholder="Поиск сайта" type="text" @change="saveFilter">
      <a class="btn">
        <i class="bi bi-plus-lg"></i>
        <span class="text-nowrap">Добавить сайт</span>
      </a>
    </div>
    <div v-if="activeDomains.length" class="domains">
      <DomainsList :domains="activeDomains"/>
    </div>
    <div v-if="problemDomains.length" class="domains">
      <h4>Сайты с ошибками</h4>
      <DomainsList :domains="problemDomains"/>
    </div>
    <div v-if="(disabledDomains.length && !hideDisabled) || (!activeDomains.length && !problemDomains.length)"
        class="domains"
    >
      <h4>Отключённые сайты</h4>
      <DomainsList :domains="disabledDomains"/>
    </div>
  </div>
</template>

<style lang="scss">
.sites-bar {
  display: grid;
  align-items: center;
  grid-template-columns: auto 1fr auto;
  gap: 0.5rem;
}
</style>
