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

    <div class="dl-group">

      <dl>
        <dt>Версия:</dt>
        <dd class="mono">{{ ospVersion }}</dd>

        <dt>Дата релиза:</dt>
        <dd class="mono">{{ ospDate }}</dd>
      </dl>

      <div class="dl-group-space"></div>

      <dl>
        <dt>Количество доменов:</dt>
        <dd class="mono">{{ totalDomains }}</dd>

        <dt>Отключённые:</dt>
        <dd class="mono">{{ disabledDomains }}</dd>

        <dt>С ошибками:</dt>
        <dd class="mono">{{ problemDomains }}</dd>
      </dl>

      <div class="dl-group-space"></div>

      <dl>
        <dt>Web API url:</dt>
        <dd class="mono">{{ webApiUrl }}</dd>

        <dt>CLI API url:</dt>
        <dd class="mono">{{ cliApiUrl }}</dd>

        <dt>Рабочий домен панели:</dt>
        <dd class="mono">{{ apiDomain }}</dd>

        <dt>Версия PHP для панели:</dt>
        <dd class="mono">{{ apiEngine }}</dd>
      </dl>

    </div>

    <div class="d-flex align-items-center gap-1 mt-1">
      <button class="btn text-nowrap" @click="clearStorage">Очистить локальные настройки</button>
      <div class="text-muted">Будут сброшены настройки, которые хранятся в локальном хранилище браузера</div>
    </div>

  </div>
</template>

<style lang="scss" scoped>
.dl-group {
  display: grid;
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
