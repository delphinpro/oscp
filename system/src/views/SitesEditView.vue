<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2023-2024.
  Licensed under MIT License
  --------------------------->

<script>
import Checkbox from '@/components/Checkbox';
import FileSelector from '@/components/FileSelector.vue';
import http from '@/services/http';
import { mapActions, mapMutations, mapState } from 'vuex';

export default {
  name: 'SiteEditView',

  components: {
    FileSelector,
    Checkbox,
  },

  data: () => ({
    isCreating: false,

    engines         : [],
    restartAfterSave: true,

    oldHost   : null,
    prevEngine: null,

    ready: false,

    site: {
      host                : '',
      aliases             : null,
      auto_configure      : null,
      enabled             : null,
      engine              : null,
      ip                  : null,
      log_format          : null,
      cgi_dir             : null,
      public_dir          : null,
      ssl                 : null,
      ssl_auto_cert       : null,
      ssl_cert_file       : null,
      ssl_key_file        : null,
      project_add_commands: null,
      project_add_modules : null,
      project_home_dir    : null,
      project_use_sys_env : null,
      terminal_codepage   : null,
      admin_path          : null,
    },
  }),

  watch: {
    restartAfterSave(v) {
      localStorage.setItem('restartAfterSave', v);
    },
  },

  computed: {
    ...mapState({
      isGrouped   : state => state.sites.grouped,
      allSites    : state => state.sites.sites,
      currentGroup: state => state.sites.selected,
    }),

    defaultEngine() {
      return this.engines.filter(e => e.enabled).sort((a, b) => b.name.localeCompare(a.name))[0];
    },
  },

  async created() {
    this.restartAfterSave = localStorage.getItem('restartAfterSave') === 'true';

    this.isCreating = this.$route.name === 'siteCreate';
    let pageTitle = this.isCreating ? 'Новый сайт' : 'Настройка сайта ' + this.$route.params.host;

    this.$store.commit('setPageTitle', pageTitle);

    this.showLoader();

    this.engines = (await http.get('modules/engines'))?.engines ?? [];

    if (!this.isCreating) {
      try {
        const res = await http.post('sites/data', { host: this.$route.params.host });
        this.site = res.site;
        this.prevEngine = res.site.engine;
        this.oldHost = res.site.host;
        this.ready = true;
      } catch (message) {
        this.showMessage({ message, style: 'danger', timeout: 5 }).then();
      }
    } else {
      this.ready = true;
      this.site.prevEngine = this.defaultEngine.name;
      this.site.engine = this.defaultEngine.name;
    }


    this.hideLoader();

  },

  methods: {
    ...mapMutations({
      showLoader: 'showLoader',
      hideLoader: 'hideLoader',
    }),
    ...mapActions({
      modRestart      : 'moduleRestart',
      showErrorMessage: 'showErrorMessage',
      showMessage     : 'showMessage',
      hideMessage     : 'hideMessage',
      loadSites       : 'loadSites',
    }),

    async save() {

      if (this.site.host.trim().length === 0) {
        this.showErrorMessage({ message: 'Не заполнено поле "Хост"' }).then();
        return;
      }

      let requiredRestart = [];
      if (this.engines.find(engine => engine.name === this.prevEngine)?.enabled) requiredRestart.push(this.prevEngine);
      if (this.prevEngine !== this.site.engine) {
        if (this.engines.find(engine => engine.name === this.site.engine)?.enabled) requiredRestart.push(this.site.engine);
      }

      this.showLoader();

      try {
        await http.post('sites/save', {
          old_host: this.oldHost,
          ...this.site,
        });
      } catch (message) {
        await this.showMessage({ message, style: 'danger', timeout: 5 });
        this.hideLoader();
        return;
      }

      let timeout = 3;
      let message = `Сайт ${this.site.host} сохранён.`;

      if (this.restartAfterSave && requiredRestart.length) {
        message += `<br>Выполняется перезагрузка ` +
            (requiredRestart.length > 1 ? 'модулей: ' : 'модуля: ') +
            requiredRestart.join(', ') + '.';
        timeout = 10;
      }

      await this.showMessage({ message, style: 'success', timeout });
      await this.loadSites();

      if (this.restartAfterSave && requiredRestart.length) {
        for (let engine of requiredRestart) {
          await this.modRestart(engine);
        }
        await this.hideMessage();
      }

      this.hideLoader();

      this.$router.push('/sites');
    },

    async deleteSite() {
      if (confirm('Вы уверены, что хотите удалить сайт?')) {

        this.showLoader();

        await http.post('sites/delete', {
          host: this.oldHost,
        });

        let requiredRestart = [];
        if (this.engines.find(engine => engine.name === this.prevEngine)?.enabled) requiredRestart.push(this.prevEngine);

        let timeout = 3;
        let message = `Сайт ${this.site.host} удалён.`;

        if (this.restartAfterSave && requiredRestart.length) {
          message += `<br>Выполняется перезагрузка ` +
              (requiredRestart.length > 1 ? 'модулей: ' : 'модуля: ') +
              requiredRestart.join(', ') + '.';
          timeout = 10;
        }

        await this.showMessage({ message, style: 'success', timeout });
        await this.loadSites();

        if (this.restartAfterSave && requiredRestart.length) {
          for (let engine of requiredRestart) {
            await this.modRestart(engine);
          }
          await this.hideMessage();
        }

        this.hideLoader();

        this.$router.push('/sites');
      }
    },
  },

};

</script>

<template>
  <div>
    <div class="d-flex align-items-center space-between gap-0.5 mb-2">
      <div class="d-flex align-items-center gap-0.5">
        <router-link :to="{ name: 'sites' }" class="btn">
          <i class="bi bi-arrow-left"></i>
          <span class="text-nowrap">Назад</span>
        </router-link>
        <button v-if="ready" class="btn" @click="save">Сохранить</button>
        <checkbox v-if="ready" v-model="restartAfterSave" label="Выполнить перезагрузку"/>
      </div>
      <button v-if="ready && !isCreating" class="btn" @click="deleteSite">Удалить</button>
    </div>

    <div v-if="ready" class="form">

      <div class="row">
        <div class="col-span-2 d-flex gap-1 align-items-center">
          <Checkbox v-model="site.enabled" label="Включить домен"/>
          <code class="text-muted">enabled</code>
        </div>
      </div>
      <div class="row">
        <div class="col-span-2 d-flex gap-1 align-items-center">
          <Checkbox v-model="site.ssl" label="Включить SSL"/>
          <code class="text-muted">ssl</code>
        </div>
      </div>
      <div class="row">
        <div class="col-span-2 d-flex gap-1 align-items-center">
          <Checkbox v-model="site.project_use_sys_env" label="Использовать системное окружение"/>
          <code class="text-muted">project_use_sys_env</code>
        </div>
      </div>
      <div class="row">
        <label class="col-form-label">
          Хост
        </label>
        <div>
          <input v-model="site.host" class="input monospace" required type="text">
        </div>
      </div>
      <div class="row">
        <label class="col-form-label">
          Алиасы
          <br><code class="text-muted">aliases</code>
        </label>
        <div>
          <input v-model="site.aliases" class="input monospace" type="text">
        </div>
      </div>
      <div class="row">
        <label class="col-form-label">
          IP-адрес
          <br><code class="text-muted">ip</code>
        </label>
        <div>
          <input v-model="site.ip" class="input monospace" placeholder="auto" type="text">
        </div>
      </div>
      <div class="row">
        <label class="col-form-label">
          PHP
          <br><code class="text-muted">engine</code>
        </label>
        <div>
          <select v-model="site.engine" class="select">
            <option disabled>Выберите версию PHP для сайта</option>
            <option v-for="option in engines" :value="option.name">
              {{ option.name }} {{ !option.enabled ? 'Выключен' : '' }}
            </option>
          </select>
        </div>
      </div>
      <div class="row">
        <label class="col-form-label">
          Расположение сайта
          <br><code class="text-muted">public_dir</code>
        </label>
        <div>
          <file-selector
              v-model="site.public_dir"
              :error="!site.isValidRoot && !isCreating"
              :required="true"
              @select-value="site.isValidRoot = true"
          />
        </div>
      </div>
      <div class="row">
        <label class="col-form-label">
          Корень проекта
          <br><code class="text-muted">project_home_dir</code>
        </label>
        <div>
          <file-selector v-model="site.project_home_dir" :initial-path="site.public_dir"/>
        </div>
      </div>
      <div class="row">
        <label class="col-form-label">
          Доп. команды
          <br><code class="text-muted">project_add_commands</code>
        </label>
        <div>
          <input v-model="site.project_add_commands" class="input monospace" type="text">
        </div>
      </div>
      <div class="row">
        <label class="col-form-label">
          Доп. модули
          <br><code class="text-muted">project_add_modules</code>
        </label>
        <div>
          <input v-model="site.project_add_modules" class="input monospace" type="text">
        </div>
      </div>
      <div class="col-span-2 text-muted"><i><small>Пользовательские настройки</small></i></div>
      <div class="row">
        <label class="col-form-label">
          URL панели управления
          <br><code class="text-muted">admin_path</code>
        </label>
        <div>
          <input v-model="site.admin_path" class="input monospace" type="text">
          <div class="form-help">Указывать только путь от корня сайта. Например: <code>/admin</code></div>
        </div>
      </div>
    </div>

  </div>
</template>

<style lang="scss" scoped>
.form {
  display: grid;
  align-items: center;
  max-width: 900px;
  margin-bottom: 2rem;
  grid-template-columns: auto 1fr;
  gap: 0.75rem 1rem;

  .row {
    display: contents;
  }
  .monospace {
    font-size: 1.4em;
  }
}

.form-help {
  font-size: 0.85em;
  font-style: italic;
  margin-top: 4px;
  color: var(--muted-color);
  code {
    font-size: 1.4em;
    font-style: normal;
  }
}
</style>
