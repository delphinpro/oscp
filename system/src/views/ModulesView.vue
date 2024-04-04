<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2023-2024.
  Licensed under MIT License
  --------------------------->

<script>
import Alert from '@/components/Alert';
import Checkbox from '@/components/Checkbox';
import http from '@/services/http';
import { mapActions, mapState } from 'vuex';

export default {
  name: 'ModulesView',

  components: {
    Alert,
    Checkbox,
  },

  data: () => ({
    hideDisabled: false,
  }),

  computed: {
    ...mapState({
      modules: state => state.modules.modules,
    }),

    filteredModules() {
      if (this.modules === null) return [];

      return Object.values(this.modules)
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

  methods: {
    ...mapActions({
      loadModules: 'loadModules',
      showLoader : 'showLoader',
      hideLoader : 'hideLoader',
      showMessage: 'showMessage',
      hideMessage: 'hideMessage',
    }),

    loadData() {
      this.showLoader();
      this.loadModules().then(() => this.hideLoader());
    },

    moduleAction(command, module) {
      this.showLoader();
      http.apiCall(`/${command}/${module}/`)
          .then(res => {
            this.showSuccessMessage(res);
            this.loadData();
          }).catch(err => this.showErrorMessage(err.message));
    },

    moduleRestart(module) {
      this.showLoader();
      http.apiCall(`/off/${module}/`)
          .then(res => {
            this.showSuccessMessage(res);
            http.apiCall(`/on/${module}/`)
                .then(res => {
                  this.showSuccessMessage(res);
                  this.loadData();
                }).catch(err => this.showErrorMessage(err.message));
          }).catch(err => this.showErrorMessage(err.message));
    },

    showSuccessMessage(message) {
      this.showMessage({ message, style: 'success', timeout: 5 });
    },

    showErrorMessage(message) {
      this.showMessage({ title: 'Ошибка', message, style: 'danger', timeout: 5 });
      this.loadData();
    },
  },

};

</script>

<template>
  <div>

    <Checkbox v-model="hideDisabled" label="Скрывать отключённые"/>

    <alert v-if="!filteredModules.length" class="mt-1" message="Модули не загружены"/>

    <table v-else class="table-modules">
      <thead>
        <tr>
          <th>Модуль</th>
          <th>IP</th>
          <th>Версия</th>
          <th>Тип</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="module in filteredModules">
          <td>
            <span class="d-flex align-items-center gap-0.5">
              <span :class="{'bg-success': module.enabled, 'bg-danger': !module.enabled, 'bg-white': module.init}"
                  class="bulb"
              ></span>
              <span class="mono text-nowrap">{{ module.name }}</span>
            </span>
          </td>
          <td class="text-muted monospace">{{ module.ip }}{{ module.port ? ':' + module.port : '' }}</td>
          <td class="text-muted monospace">{{ module.version }}</td>
          <td class="text-muted">{{ module.compatible }}</td>
          <td class="text-end">
            <div class="btn-group justify-end">
              <button
                  v-if="!module.enabled"
                  class="btn btn-icon"
                  title="Инициализировать модуль"
                  @click="moduleAction('init', module.name)"
              ><i class="bi bi-info-circle"></i>
              </button>
              <button v-if="module.enabled" class="btn btn-icon" @click="moduleRestart(module.name)">
                <i class="bi bi-arrow-repeat"></i>
              </button>
              <button v-if="module.enabled"
                  class="btn btn-icon"
                  title="Выключить модуль"
                  @click="moduleAction('off', module.name)"
              ><i class="bi bi-power"></i>
              </button>
              <button v-else
                  class="btn btn-icon"
                  title="Включить модуль"
                  @click="moduleAction('on', module.name)"
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
  </div>
</template>

<style lang="scss">
</style>
