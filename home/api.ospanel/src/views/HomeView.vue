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
      ospVersion: (state) => state.ospVersion,
      ospDate   : (state) => state.ospDate,
      webApiUrl : (state) => state.webApiUrl,
      cliApiUrl : (state) => state.cliApiUrl,
      apiDomain : (state) => state.apiDomain,
      apiEngine : (state) => state.apiEngine,
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
