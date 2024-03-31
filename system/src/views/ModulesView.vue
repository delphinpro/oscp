<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2023-2024.
  Licensed under MIT License
  --------------------------->

<script>
import Checkbox from '@/components/Checkbox';
import http from '@/services/http';
import { mapMutations } from 'vuex';

export default {
  name: 'ModulesView',

  components: {
    Checkbox,
  },

  data: () => ({
    hideDisabled: false,
  }),

  computed: {
    modules() {
      if (this.$store.state.modules === null) return [];
      return Object.values(this.$store.state.modules)
          .filter(module => !(this.hideDisabled && !module.enabled));
    },
  },

  watch: {
    hideDisabled(v) {
      localStorage.setItem('modules_hideDisabled', v);
    },
  },

  created() {
    this.hideDisabled = localStorage.getItem('modules_hideDisabled') === 'true';
  },

  mounted() {

    this.$store.commit('setPageTitle', 'Модули');
    this.loadData();
  },

  unmounted() {
  },

  methods: {
    ...mapMutations(['showLoader', 'hideLoader']),
    loadData() {
      this.showLoader();
      http.get('/modules').then(res => {
        this.$store.commit('setModules', res.modules);
        this.hideLoader();
        // this.setSelectedSiteGroup(Object.keys(res.sites).shift());
      });
    },
    moduleAction(command, module) {
      this.showLoader();
      fetch(this.$store.state.cliApiUrl + `/${command}/${module}/`, { method: 'GET' })
          .then(res => res.text())
          .then(res => {
            this.loadData();
            this.$store.commit('showMessage', res);
          })
          .catch(err => {
            this.loadData();
            this.$store.commit('showMessage', err.message);
          });
    },
    moduleRestart(module) {
      //  this.showLoader();
      http.post(`/modules/restart`, { module }).then(res => {
        console.log(res);
      });
    },
  },

};

</script>

<template>
  <div>
    <Checkbox v-model="hideDisabled" label="Скрывать отключённые"/>

    <table class="table-modules">
      <thead>
        <tr>
          <th>Модуль</th>
          <!--<th>Статус</th>-->
          <th>IP</th>
          <!--<th>Версия</th>-->
          <!--<th>Тип</th>-->
          <th>Тип</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr
            v-for="module in modules"
            class="{ { $module->enabled ? '' : 'state-off' }}"
        >
          <td>
            <span class="d-flex align-items-center gap-0.5">
              <span class="bulb"
                  :class="{'bg-success': module.enabled, 'bg-danger': !module.enabled, 'bg-white': module.init}"
              ></span>
              <span class="mono text-nowrap">{{ module.name }}</span>
            </span>
          </td>
          <!--<td>{{ module.status }}</td>-->
          <td class="monospace">
            {{ module.ip }}{{ module.port ? ':' + module.port : '' }}
          </td>
          <!--<td>{{ module.version }}</td>-->
          <!--<td>{{ module.type }}</td>-->
          <td>{{ module.compatible }}</td>
          <td class="text-end">
            <div class="btn-group justify-end">
              <button
                  class="btn btn-icon"
                  @click="moduleAction('init', module.name)"
                  title="Инициализировать модуль"
              ><i class="bi bi-info-circle"></i>
              </button>
              <button class="btn btn-icon" @click="moduleRestart(module.name)">
                <i class="bi bi-arrow-repeat"></i>
              </button>
              <button v-if="module.enabled"
                  class="btn btn-icon"
                  @click="moduleAction('off', module.name)"
                  title="Выключить модуль"
              ><i class="bi bi-power"></i>
              </button>
              <button v-else
                  class="btn btn-icon"
                  @click="moduleAction('on', module.name)"
                  title="Включить модуль"
              ><i class="bi bi-power"></i>
              </button>
              <!--<a
                  class="btn btn-icon"
                  title="Настройки модуля"
              ><i class="bi bi-gear-fill"></i>
              </a>-->
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <pre>modules: {{ modules }}</pre>
  </div>
</template>

<style lang="scss">
</style>
