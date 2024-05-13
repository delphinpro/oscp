<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024.
  Licensed under MIT License
  --------------------------->
<script>

import { mapState } from 'vuex';

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
    this.$store.commit('setPageTitle', 'Open Server Panel');
  },

  methods: {},
};

</script>
<template>
  <div>

    <dl>
      <dt>Версия:</dt>
      <dd class="mono">{{ ospVersion }}</dd>

      <dt>Дата релиза:</dt>
      <dd class="mono">{{ ospDate }}</dd>
    </dl>

    <dl>
      <dt>Количество доменов:</dt>
      <dd class="mono">{{ totalDomains }}</dd>

      <dt>Отключённые:</dt>
      <dd class="mono">{{ disabledDomains }}</dd>

      <dt>С ошибками:</dt>
      <dd class="mono">{{ problemDomains }}</dd>
    </dl>

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
</template>

<style lang="scss" scoped>
</style>
