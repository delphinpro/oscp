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
import http, { ServerError } from '@/services/http';
import { mapActions, mapMutations } from 'vuex';

function toLowerCaseField(arr, key) {
  return arr.map(function (e) {
    e[key] = e[key]?.toLowerCase();
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
    server_error   : null,
    php_engines    : [],
    nginx_engines  : [],
    prevPhpEngine  : null,
    prevNginxEngine: null,
    ready          : false,
    oldHost        : null,

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

      isValidRoot: true,
    },

    resolvedPublicDir : null,
    resolvedProjectDir: null,

    errorHost   : false,
    errorBaseDir: false,

    res: null,
  }),

  async created() {
    this.$store.commit('setPageTitle', 'Сайт ' + this.$route['params'].host);

    try {

      this.showLoader();
      this.defaults = await http.get('sites/defaults');
      let mods = await http.get('modules/engines');

      this.php_engines = { Apache: [], FastCGI: [] };

      toLowerCaseField(mods?.php_engines ?? [], 'name').forEach(e => {
        if (e.alt_name.includes('Apache')) {
          this.php_engines.Apache.push(e);
        }
        if (e.alt_name.includes('FastCGI')) {
          this.php_engines.FastCGI.push(e);
        }
      });

      this.nginx_engines = toLowerCaseField(mods?.nginx_engines ?? [], 'name');

      const res = await http.get('sites/edit', { host: this.$route['params'].host });
      this.site = {
        host             : res.host,
        aliases          : res.site.computed.aliases,
        enabled          : res.site.computed.enabled,
        environment      : res.site.computed.environment ?? '',
        php_engine       : (res.site.computed.php_engine || this.defaults.php_engine)?.toLowerCase() ?? '',
        nginx_engine     : (res.site.computed.nginx_engine || this.defaults.nginx_engine)?.toLowerCase() ?? '',
        node_engine      : res.site.config?.node_engine ?? '',
        ip               : res.site.computed.ip ?? '',
        base_dir         : res.site.computed.base_dir,
        project_dir      : res.site.computed.project_dir,
        public_dir       : res.site.computed.public_dir,
        ssl              : res.site.computed.ssl,
        ssl_cert_file    : '',
        ssl_key_file     : '',
        start_command    : res.site.config?.start_command ?? '',
        terminal_codepage: '',
        admin_path       : res.site.config?.admin_path ?? '',

        isValidRoot: res.site.isValidRoot,
      };

      this.resolvedProjectDir = res.site.computed.project_dir;
      this.resolvedPublicDir = res.site.computed.public_dir;
      this.prevPhpEngine = res.site.computed.php_engine?.toLowerCase() || null;
      this.prevNginxEngine = res.site.computed.nginx_engine?.toLowerCase() || null;
      this.oldHost = res.site.host;

      this.ready = true;

    } catch (err) {
      if (err instanceof ServerError) {
        this.server_error = err.message;
      } else {
        this.showMessage({ message: err.message, style: 'danger', timeout: 5 }).then();
      }
    }

    this.hideLoader();

  },

  methods: {
    ...mapMutations({
      showLoader: 'showLoader',
      hideLoader: 'hideLoader',
    }),
    ...mapActions({
      systemReload    : 'systemReload',
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

      this.res = null;

      try {

        this.showLoader();

        this.res = await http.post('sites/save', {
          old_host: this.oldHost,
          ...this.site,
        });

        localStorage.setItem('LAST_PAGE', 'sites/' + this.site.host);
        await this.systemReload();

      } catch (err) {

        await this.showMessage({ message: err.message, style: 'danger', timeout: 5 });

      } finally {

        this.hideLoader();

      }
    },

    async deleteSite() {
      if (confirm('Вы уверены, что хотите удалить сайт?')) {

        this.showLoader();

        await http.post('sites/delete', {
          host: this.oldHost,
        });

        this.hideLoader();

        localStorage.setItem('LAST_PAGE', 'sites');
        await this.systemReload();
      }
    },
  },

};

</script>

<template>
  <div>
    <teleport to="#top">
      <div class="d-flex align-items-center gap-0.5">
        <router-link :to="{ name: 'sites' }" class="btn">
          <i class="bi bi-arrow-left"></i>
          <span class="text-nowrap">Назад</span>
        </router-link>
        <button v-if="ready" class="btn" @click="save">Сохранить</button>
        <button v-if="ready" class="btn" @click="deleteSite">Удалить</button>
      </div>
    </teleport>

    <div v-if="server_error" class="alert alert_danger mb-1">{{ server_error }}</div>


    <div v-if="ready" class="form">
      <!-- @formatter:off -->
      <form-checkbox v-model="site.enabled" hint="enabled" label="Домен включён"/>
      <form-checkbox v-model="site.ssl" hint="ssl" label="Включить HTTPS"/>
      <form-input v-model="site.host" :has-error="errorHost" label="Хост" required @input="errorHost = false"/>
      <form-input v-model="site.aliases" desc="Несколько алиасов указываются через пробел" hint="aliases" label="Алиасы"/>
      <form-input v-model="site.ip" hint="ip" label="IP-адрес"/>
      <form-select v-model="site.php_engine" :group="true" :options="php_engines" empty="Не требуется" hint="php_engine" label="PHP" text-key="opt_name" value-key="name"/>
      <form-select v-model="site.nginx_engine" :options="nginx_engines" empty="Не требуется" hint="nginx_engine" label="Nginx" text-key="opt_name" value-key="name"/>
      <form-input v-model="site.node_engine" hint="node_engine" label="NodeJS"/>
      <form-input v-model="site.base_dir" disabled hint="{base_dir}" label="Расположение сайта"/>
      <div class="form-row">
        <label class="form-label"><span> Публичный каталог <code class="form-hint text-muted">public_dir</code></span></label>
        <file-selector v-model="site.public_dir" :error="!site.isValidRoot" :initial-path="resolvedPublicDir" :placeholder="defaults.public_dir" :required="true" @select-value="site.isValidRoot = true"/>
      </div>
      <div class="form-row">
        <label class="form-label"><span> Рабочий каталог <code class="form-hint text-muted">project_dir</code></span></label>
        <file-selector v-model="site.project_dir" :initial-path="resolvedProjectDir" :placeholder="defaults.project_dir"/>
      </div>
      <form-input v-model="site.start_command" :placeholder="defaults.start_command" hint="start_command" label="Доп. команда"/>
      <form-input v-model="site.environment" :placeholder="defaults.environment" hint="environment" label="Доп. окружение"/>
      <div class="col-span-2 text-muted"><i><small>Пользовательские настройки</small></i></div>
      <form-input v-model="site.admin_path" desc="Указывать только путь от корня сайта. Например: <code>/admin</code>" hint="admin_path" label="URL панели управления"/>
      <!-- @formatter:on -->
    </div>

  </div>
</template>

<style lang="scss" scoped>
</style>
