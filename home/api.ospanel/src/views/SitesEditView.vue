<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2023-2024.
  Licensed under MIT License
  --------------------------->

<script>
import Checkbox from '@/components/Checkbox';
import FileSelector from '@/components/FileSelector.vue';
import FormCheckbox from '@/components/FormCheckbox.vue';
import FormInput from '@/components/FormInput.vue';
import FormSelect from '@/components/FormSelect.vue';
import http from '@/services/http';
import { mapActions, mapMutations, mapState } from 'vuex';

function toLowerCaseField(arr, key) {
  return arr.map(function (e) {
    e[key] = e[key].toLowerCase();
    return e;
  });
}

export default {
  name: 'SiteEditView',

  components: {
    FormCheckbox,
    FormInput,
    FormSelect,
    FileSelector,
    Checkbox,
  },

  data: () => ({
    isCreating: false,

    php_engines     : [],
    nginx_engines   : [],
    restartAfterSave: true,

    oldHost   : null,
    prevEngine: null,

    ready: false,
    defaults: {
      aliases          : '',
      enabled          : '',
      environment      : '',
      ip               : '',
      nginx_engine     : '',
      node_engine      : '',
      php_engine       : '',
      project_dir      : '',
      project_url      : '',
      public_dir       : '',
      ssl              : '',
      ssl_cert_file    : '',
      ssl_key_file     : '',
      start_command    : '',
      terminal_codepage: '',
    },

    site: {
      host             : '',
      aliases          : '',
      enabled          : false,
      environment      : '',
      php_engine       : '',
      nginx_engine     : '',
      node_engine      : '',
      ip               : '',
      base_dir         : '',
      project_dir      : '',
      public_dir       : '',
      ssl              : false,
      ssl_cert_file    : '',
      ssl_key_file     : '',
      start_command    : '',
      terminal_codepage: '',
      admin_path       : null,
    },

    errorHost   : false,
    errorBaseDir: false,
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
      return this.php_engines.filter(e => e.enabled).sort((a, b) => b.name.localeCompare(a.name))[0];
    },
  },

  async created() {
    this.restartAfterSave = localStorage.getItem('restartAfterSave') === 'true';

    this.isCreating = this.$route['name'] === 'siteCreate';
    let pageTitle = this.isCreating ? 'Новый сайт' : 'Настройка сайта ' + this.$route['params'].host;

    this.$store.commit('setPageTitle', pageTitle);

    try {

      this.showLoader();

      this.defaults = await http.get('sites/defaults');

      let mods = await http.get('modules/engines');
      this.php_engines = toLowerCaseField(mods?.php_engines ?? [], 'name');
      this.nginx_engines = toLowerCaseField(mods?.nginx_engines ?? [], 'name');

      if (!this.isCreating) {
        const res = await http.get('sites/edit', { host: this.$route['params'].host });
        this.site = res.site;
        this.site.use_system_env = res.site.host_modules.indexOf('System') !== -1;
        this.prevEngine = res.site.php_engine;
        this.oldHost = res.site.host;
        this.ready = true;
      } else {
        this.ready = true;
        this.prevEngine = this.defaults.php_engine || '';//this.defaultEngine.name;
        this.site.php_engine = this.defaults.php_engine || '';//this.defaultEngine.name;
        this.site.nginx_engine = this.defaults.nginx_engine;
        this.site.enabled = !!this.defaults.enabled;
        this.site.ssl = !!this.defaults.ssl;
      }

    } catch (message) {
      this.showMessage({ message, style: 'danger', timeout: 5 }).then();
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
        await this.showErrorMessage({ message: 'Не заполнено поле "Хост"' });
        this.errorHost = true;
        return;
      }

      if (this.site.base_dir.trim().length === 0) {
        await this.showErrorMessage({ message: 'Не указано расположение сайта' });
        this.errorBaseDir = true;
        return;
      }

      let requiredRestart = [];
      if (this.php_engines.find(engine => engine.name === this.prevEngine)?.enabled) requiredRestart.push(this.prevEngine);
      if (this.prevEngine !== this.site.php_engine) {
        if (this.php_engines.find(engine => engine.name === this.site.php_engine)?.enabled) requiredRestart.push(this.site.php_engine);
      }

      this.showLoader();

      try {
        if (this.isCreating) {
          this.res = await http.post('sites/create', this.site);
        } else {
          this.res = await http.post('sites/save', {
            old_host: this.oldHost,
            ...this.site,
          });
        }
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
        if (this.php_engines.find(engine => engine.name === this.prevEngine)?.enabled) requiredRestart.push(this.prevEngine);

        let timeout = 3;
        let message = `Сайт ${this.site.host} удалён.`;

        if (this.restartAfterSave && requiredRestart.length) {
          message += `<br>Выполняется перезагрузка` +
              (requiredRestart.length > 1 ? ' модулей: ' : ' модуля: ') +
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

        this.$router['push']('/sites');
      }
    },
  },

};

</script>

<template>
  <div>
    <teleport to="#top">
      <div class="d-flex align-items-center space-between gap-0.5 -mb-2">
        <div class="d-flex align-items-center gap-0.5">
          <router-link :to="{ name: 'sites' }" class="btn">
            <i class="bi bi-arrow-left"></i>
            <span class="text-nowrap">Назад</span>
          </router-link>
          <button v-if="ready" class="btn" @click="save">Сохранить</button>
        </div>
        <button v-if="ready && !isCreating" class="btn" @click="deleteSite">Удалить</button>
      </div>
    </teleport>

    <checkbox v-if="ready" v-model="restartAfterSave" label="Выполнить перезапуск после сохранения"/>
    <hr class="my-1">

    <div v-if="ready" class="form">

      <form-checkbox v-model="site.enabled" hint="enabled" label="Домен включён"/>
      <form-checkbox v-model="site.ssl" hint="ssl" label="Включить HTTPS"/>
      <form-input v-model="site.host" :has-error="errorHost" label="Хост" required @input="errorHost = false"/>
      <form-input
          v-model="site.aliases"
          :placeholder="defaults.aliases"
          desc="Несколько алиасов указываются через пробел"
          hint="aliases"
          label="Алиасы"
      />
      <form-input v-model="site.ip" :placeholder="defaults.ip" hint="ip" label="IP-адрес"/>
      <form-select
          v-model="site.php_engine"
          :options="php_engines"
          empty="Не требуется"
          hint="php_engine"
          label="PHP"
          text-key="opt_name"
          value-key="name"
      />
      <form-select
          v-model="site.nginx_engine"
          :options="nginx_engines"
          empty="Не требуется"
          hint="nginx_engine"
          label="Nginx"
          text-key="opt_name"
          value-key="name"
      />
      <div class="form-row">
        <label class="form-label">
          <span>
            Расположение сайта
            <span class="req">*</span>
            <code class="text-muted">{base_dir}</code>
          </span>
        </label>
        <file-selector
            v-model="site.base_dir"
            :error="(!site.isValidRoot && !isCreating) || errorBaseDir"
            :required="true"
            @select-value="site.isValidRoot = true; errorBaseDir = false"
        />
      </div>
      <div class="form-row">
        <label class="form-label">
          <span>
            Публичный каталог
            <code class="form-hint text-muted">public_dir</code>
          </span>
        </label>
        <file-selector
            v-model="site.public_dir"
            :error="!site.isValidRoot && !isCreating"
            :placeholder="defaults.public_dir"
            :required="true"
            @select-value="site.isValidRoot = true"
        />
      </div>
      <div class="form-row">
        <label class="form-label">
          <span>
            Рабочий каталог
            <code class="form-hint text-muted">project_dir</code>
          </span>
        </label>
        <file-selector
            v-model="site.project_dir"
            :initial-path="site.public_dir"
            :placeholder="defaults.project_dir"
        />
      </div>
      <form-input
          v-model="site.start_command"
          :placeholder="defaults.start_command"
          hint="start_command"
          label="Доп. команда"
      />
      <form-input
          v-model="site.environment"
          :placeholder="defaults.environment"
          hint="environment"
          label="Доп. окружение"
      />
      <div class="col-span-2 text-muted"><i><small>Пользовательские настройки</small></i></div>
      <form-input
          v-model="site.admin_path"
          desc="Указывать только путь от корня сайта. Например: <code>/admin</code>"
          hint="admin_path"
          label="URL панели управления"
      />
    </div>

  </div>
</template>

<style lang="scss" scoped>
</style>
