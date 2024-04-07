<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2023-2024.
  Licensed under MIT License
  --------------------------->

<script>
import Alert from '@/components/Alert';
import Checkbox from '@/components/Checkbox';
import http from '@/services/http';
import { mapActions, mapMutations, mapState } from 'vuex';

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
    ...mapMutations({
      showLoader: 'showLoader',
      hideLoader: 'hideLoader',
    }),
    ...mapActions({
      loadModules       : 'loadModules',
      modRestart        : 'moduleRestart',
      showSuccessMessage: 'showSuccessMessage',
      showErrorMessage  : 'showErrorMessage',
      hideMessage       : 'hideMessage',
    }),

    async loadData() {
      try {
        this.showLoader();
        await this.loadModules();
      } catch (err) {
        console.log(err);
      }
      this.hideLoader();
    },

    async moduleAction(command, module) {
      this.showLoader();

      try {
        let message = await http.apiCall(`/${command}/${module}/`);
        await this.showSuccessMessage({ message });
        await this.loadData();
      } catch (message) {
        await this.showErrorMessage({ message, title: 'Ошибка' });
      }

      this.hideLoader();
    },

    async moduleRestart(module) {
      this.showLoader();

      try {
        window.ping = false;
        await this.showSuccessMessage({ message: 'Выполняется перезагрузка модуля ' + module });
        let message = await this.modRestart(module);
        window.ping = true;
        await this.showSuccessMessage({ message });
      } catch (message) {
        await this.showErrorMessage({ message, title: 'Ошибка' });
      }

      this.hideLoader();
    },
  },

};

</script>

<template>
  <div>

    <Checkbox v-model="hideDisabled" label="Скрыть отключённые"/>

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
              ><i class="bi bi-power text-danger"></i>
              </button>
              <button v-else
                  class="btn btn-icon"
                  title="Включить модуль"
                  @click="moduleAction('on', module.name)"
              ><i class="bi bi-power text-success"></i>
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
