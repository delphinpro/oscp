<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2023-2024.
  Licensed under MIT License
  --------------------------->

<script>
import Checkbox from '@/components/Checkbox';
import http from '@/services/http';
import { mapActions, mapState } from 'vuex';

export default {
  name: 'SiteEditView',

  components: {
    Checkbox,
  },

  data: () => ({
    engines       : [],
    selectedEngine: null,

    oldHost   : null,
    prevEngine: null,

    ready: false,

    site: {
      host                : null,
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

  computed: {
    ...mapState({
      isGrouped   : state => state.sites.grouped,
      allSites    : state => state.sites.sites,
      currentGroup: state => state.sites.selected,
    }),

  },

  created() {
    this.$store.commit('setPageTitle', 'Настройка сайта ' + this.$route.params.host);
    this.showLoader();
    Promise.all([
      http.get('modules/engines').then(({ engines }) => {
        this.engines = engines;
        if (engines.length) {
          this.selectedEngine = engines[0]?.name;
        }
      }),
      http.post('sites/data', { host: this.$route.params.host }).then(({ site }) => {
        this.site = site;
        this.prevEngine = site.engine;
        this.oldHost = site.host;
        this.ready = true;
      }).catch(err => {
        this.showMessage({ message: err, style: 'danger', timeout: 5 });
      }),
    ]).then(() => {
      this.hideLoader();
    });
  },

  methods: {
    ...mapActions({
      showLoader : 'showLoader',
      hideLoader : 'hideLoader',
      showMessage: 'showMessage',
      loadSites  : 'loadSites',
    }),

    async save() {
      await http.post('sites/save', {
        old_host: this.oldHost,
        ...this.site,
      });

      await this.showMessage({ message: `Сайт ${this.site.host} сохранён`, style: 'success', timeout: 3 });
      await this.loadSites();

      this.$router.push('/sites');
    },

    deleteSite() {
      if (confirm('Вы уверены, что хотите удалить сайт?')) {}
    },
  },

};

</script>

<template>
  <div>
    <div class="d-flex align-items-center space-between gap-0.5 mb-2">
      <div class="d-flex gap-0.5">
        <router-link :to="{ name: 'sites' }" class="btn">
          <i class="bi bi-arrow-left"></i>
          <span class="text-nowrap">Назад</span>
        </router-link>
        <button v-if="ready" class="btn" @click="save">Сохранить</button>
      </div>
      <button v-if="ready" class="btn" @click="deleteSite">Удалить</button>
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
          <input v-model="site.public_dir"
              :class="{withError: !this.site.isValidRoot}"
              class="input monospace"
              required
              type="text"
          >
        </div>
      </div>
      <div class="row">
        <label class="col-form-label">
          Корень проекта
          <br><code class="text-muted">project_home_dir</code>
        </label>
        <div>
          <input v-model="site.project_home_dir" class="input monospace" type="text">
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
