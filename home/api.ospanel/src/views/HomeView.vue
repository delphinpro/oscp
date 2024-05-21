<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024.
  Licensed under MIT License
  --------------------------->
<script>

import { mapActions, mapState } from 'vuex';

export default {
  name: 'HomeView',

  data: () => ({}),

  computed: {
    ...mapState({
      ospVersion: (state) => state.main.ospVersion,
      ospDate   : (state) => state.main.ospDate,
      webApiUrl : (state) => state.main.webApiUrl,
      cliApiUrl : (state) => state.main.cliApiUrl,
      apiDomain : (state) => state.main.apiDomain,
      apiEngine : (state) => state.main.apiEngine,

      totalDomains   : (state) => state.main.totalDomains,
      disabledDomains: (state) => state.main.disabledDomains,
      problemDomains : (state) => state.main.problemDomains,
    }),
  },

  mounted() {
    this.$store.commit('setPageTitle', 'Open Server Control Panel by delphinpro');
  },

  methods: {
    ...mapActions({
      showSuccessMessage: 'showSuccessMessage',
    }),
    clearStorage() {
      window.localStorage.clear();
      this.showSuccessMessage({ message: 'Локальные настройки очищены', timeout: 1 });
    },
  },
};

</script>
<template>
  <div>

    <div class="cards">

      <div class="card span-6">
        <div class="card__label">Версия:</div>
        <div class="card__value">{{ ospVersion }}</div>
      </div>
      <div class="card span-6">
        <div class="card__label">Дата релиза:</div>
        <div class="card__value">{{ ospDate }}</div>
      </div>

      <div class="card span-6">
        <div class="card__label">Всего сайтов:</div>
        <div class="d-flex align-items-center gap-1 space-between">
          <div class="card__value">{{ totalDomains }}</div>
          <router-link :to="{ name: 'siteCreate' }" class="btn">
            <i class="bi bi-plus-lg"></i>
            <span class="text-nowrap">Добавить сайт</span>
          </router-link>
        </div>
      </div>
      <div class="card span-3">
        <div class="card__label">Отключённые:</div>
        <div class="card__value">{{ disabledDomains }}</div>
      </div>
      <div class="card span-3">
        <div class="card__label">С ошибками:</div>
        <div class="card__value">{{ problemDomains }}</div>
      </div>

      <div class="card span-12">
        <div class="card__label">Web API url:</div>
        <div class="card__content mono">{{ webApiUrl }}</div>
      </div>
      <div class="card span-12">
        <div class="card__label">CLI API url:</div>
        <div class="card__content mono">{{ cliApiUrl }}</div>
      </div>
      <div class="card span-6">
        <div class="card__label">Рабочий домен панели:</div>
        <div class="card__content mono">{{ apiDomain }}</div>
      </div>
      <div class="card span-6">
        <div class="card__label">Версия PHP для панели:</div>
        <div class="card__content mono">{{ apiEngine }}</div>
      </div>

      <div class="d-flex align-items-center gap-1 mt-1 span-12">
        <button class="btn text-nowrap" @click="clearStorage">Очистить локальные настройки</button>
        <div class="text-muted" style="font-size: 0.85rem;">
          Будут сброшены настройки, которые хранятся в локальном хранилище браузера
        </div>
      </div>

    </div>


  </div>
</template>

<style lang="scss" scoped>
.cards {
  display: grid;
  max-width: 50rem;
  grid-template-columns: repeat(12, 1fr);
  gap: 1rem;
}

.card {
  display: grid;
  align-content: start;
  padding: 1rem;
  border-radius: var(--table-radius);
  background: var(--table-body-bg);
  gap: 0.25rem;
  &__label {
    font-size: 0.85rem;
    margin-bottom: 0.5rem;
    color: var(--muted-color);
  }
  &__value {
    font-size: 1.5rem;
  }
  &__content {
    word-break: break-all;
  }
}

.dl-group {
  display: grid;
  margin-top: 1rem;
  grid-template-columns: max-content 1fr;
  gap: 1px 0;
  dl {
    display: contents;
  }
  dt, dd {
    margin: 0;
    background: var(--table-body-bg);
    padding-block: 0.75rem;
    padding-inline: 1rem;
  }
  dt:nth-child(1) { border-top-left-radius: var(--table-radius); }
  dd:nth-child(2) { border-top-right-radius: var(--table-radius); }
  dd:nth-last-child(1) { border-bottom-right-radius: var(--table-radius); }
  dt:nth-last-child(2) { border-bottom-left-radius: var(--table-radius); }
  dd {
    padding-left: 2rem;
    word-break: break-all;
  }
}

.dl-group-space {
  height: 1rem;
  grid-column: span 2;
}
</style>
