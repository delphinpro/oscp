<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024.
  Licensed under MIT License
  --------------------------->
<script>

import Checkbox from '@/components/Checkbox';

export default {
  name: 'HomeView',

  components: {
    Checkbox,
  },

  data: () => ({}),

  computed: {
    settings() {
      return this.$store.state.settings;
    },
    groupDomains: {
      get() {
        return this.settings.menu?.group_projects_by_tld || false;
      },
      set(val) {
        this.$store.commit('updateSetting', { key: 'menu.group_projects_by_tld', value: val });
      },
    },

  },

  mounted() {
    this.$store.commit('setPageTitle', 'Сводная информация');
  },
};

</script>
<template>
  <div>
    <dl>
      <dt>Версия:</dt>
      <dd class="mono">{{ $store.state.ospVersion }}</dd>

      <dt>Дата релиза:</dt>
      <dd class="mono">{{ $store.state.ospDate }}</dd>

      <dt>Рабочий домен панели:</dt>
      <dd class="mono">{{ $store.state.apiDomain }}</dd>

      <dt>Версия PHP для панели:</dt>
      <dd class="mono">{{ $store.state.apiEngine }}</dd>

      <dt>Web API url:</dt>
      <dd class="mono">{{ $store.state.webApiUrl }}</dd>

      <dt>CLI API url:</dt>
      <dd class="mono">{{ $store.state.cliApiUrl }}</dd>
    </dl>

    <dl>
      <dt>
        <Checkbox v-model="groupDomains" label="Группировать сайты по доменной зоне"/>
      </dt>
      <dd></dd>
    </dl>

    <pre>{{ $store.state.settings }}</pre>
  </div>
</template>
