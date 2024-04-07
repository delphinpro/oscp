<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2023-2024.
  Licensed under MIT License
  --------------------------->
<script>

import Alert from '@/components/Alert';
import Loader from '@/components/Loader';
import SideBar from '@/components/SideBar.vue';
import SystemMessage from '@/components/SystemMessage';
import http from '@/services/http';
import { mapActions, mapMutations, mapState } from 'vuex';

const PING_INTERVAL = 1000;
let pingInterval;

export default {
  name: 'App',

  components: {
    SideBar,
    Alert,
    Loader,
    SystemMessage,
  },

  data: () => ({
    noHost: false,
  }),

  computed: {
    ...mapState({
      isLoading: state => state.isLoading,

      apiEngine : state => state.apiEngine,
      apiDomain : state => state.apiDomain,
      ospVersion: state => state.ospVersion,
      pageTitle : state => state.pageTitle,
      sysMessage: state => state.sysMessage,
    }),

    apiHost() { return 'http://' + this.apiDomain; },
  },

  created() {
    document.title = 'OSPanel';
    this.setCliApiUrl(window.CLI_API_URL);
    http.configure({
      baseUrl: this.apiHost,
    });
  },

  async mounted() {
    this.startPing();
    try {
      this.showLoader();
      await this.loadMainData();
      await this.loadSites();
    } catch (err) {
      this.noHost = true;
      console.log(err);
    }
    this.hideLoader();
  },

  unmounted() {
    clearInterval(pingInterval);
  },

  methods: {
    ...mapMutations({
      showLoader  : 'showLoader',
      hideLoader  : 'hideLoader',
      setCliApiUrl: 'setCliApiUrl',
    }),
    ...mapActions({
      loadMainData      : 'loadMainData',
      loadSites         : 'loadSites',
      showSuccessMessage: 'showSuccessMessage',
      showErrorMessage  : 'showErrorMessage',
    }),

    systemReload() {
      this.noHost = true;
      this.showErrorMessage({ title: 'Выполняется перезагрузка' });
      window.ping = false;
      http.get('/restart').then();
      setTimeout(() => window.ping = true, 5000);
    },

    async enableEngine() {
      this.showLoader();

      try {
        let message = await http.apiCall(`/on/${this.apiEngine}/`);
        await this.showSuccessMessage({ message });
        this.noHost = false;
        location.reload();
      } catch (message) {
        await this.showErrorMessage({ message, title: 'Ошибка' });
      }

      this.hideLoader();
    },

    startPing() {
      pingInterval = setInterval(() => {
        if (!window.ping || !this.apiDomain) return;
        fetch(this.apiHost + '/ping')
            .then(res => {
              if (res.ok) return res.json();
              return res.text();
            })
            .then(res => {
              if (typeof res === 'string') {
                throw new Error(res.toString());
              }
              if (this.noHost) {
                location.reload();
              }
              this.noHost = false;
            })
            .catch(() => {
              this.noHost = true;
            });
      }, PING_INTERVAL);
    },
  },

};
</script>

<template>
  <div>
    <div v-if="noHost" class="error">
      <div>
        <div class="modal modal-danger">
          <div class="modal__header">
            <span style="margin-right:auto;">Ошибка</span>
          </div>
          <div class="modal__body">
            <div class="modal-content">
              <h2 style="margin-top: 0;font-weight:400;">Хост <code>{{ apiHost }}</code> недоступен</h2>
              <p>Необходимо добавить домен <code>{{ apiDomain }}</code>
                и включить модуль <code>{{ apiEngine }}</code>. </p>
              <p>1. Добавьте в файл <code>OSPanel/config/domains.ini</code> секцию следующего содержания:</p>
              <pre class="text-bg-dark p-3" style="font-size:1.3em">[{{ apiDomain }}]
enabled         = on
engine          = {{ apiEngine }}
public_dir      = &#123;root_dir&#125;\system\public_html</pre>
              <p>2. Откройте интерфейс командной строки и выполните команду:</p>
              <pre class="text-bg-dark p-3" style="font-size:1.3em">osp on {{ apiEngine }}</pre>
              <button class="btn text-nowrap" @click="enableEngine">Включить модуль {{ apiEngine }}</button>
              <p>3. Обновите эту страницу</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <template v-else>
      <div class="app">
        <div class="app__brand">
          <img alt="" class="" src="/assets/icon.svg">
          <span>OSPanel <span class="text-muted">{{ ospVersion }}</span></span>
        </div>
        <div class="app__header">
          <div id="title">{{ pageTitle }}</div>
          <div v-if="$route.params.host">
            <a href="https://github.com/OSPanel/OpenServerPanel/wiki/Документация/c31bf93751abb63672d5627a31d896f7f751ab65#настройка-доменов"
                target="_blank"
            >Справка <small class="bi bi-box-arrow-up-right"></small></a>
          </div>
        </div>
        <div class="app__navigation">
          <side-bar class="app__sidebar"/>
          <div class="app__footer">
            <nav class="nav nav_compact">
              <button class="nav__item active" style="padding-block:0.5rem" @click="systemReload">Перезагрузка</button>
              <a class="nav__item muted" href="https://ospanel.io/forum/" target="_blank">Форум
                <i class="font-small bi bi-box-arrow-up-right"></i>
              </a>
              <a class="nav__item muted" href="https://t.me/ospanel_chat" target="_blank">
                Telegram <i class="font-small bi bi-box-arrow-up-right"></i>
              </a>
            </nav>
          </div>
        </div>
        <div class="app__main">
          <router-view/>
        </div>
      </div>
    </template>
    <SystemMessage v-if="sysMessage"/>
    <Loader v-if="isLoading"/>
  </div>
</template>

<style lang="scss">
.error {
  display: flex;
  justify-content: center;
  min-height: 100vh;
  padding: 3rem 0;
  background: #20344b;
  .wnd {
    max-width: 60%;
    margin: 0 auto;
  }
}

.app {
  display: grid;
  width: 100%;
  min-height: 100vh;
  grid-template-columns: 17rem 1fr;
  grid-template-rows: auto 1fr;

  &__brand, &__navigation {
    border-right: 1px solid var(--hr-color);
    background: var(--app-side-bg);
  }

  &__brand, &__header {
    border-bottom: 1px solid var(--hr-color);
  }

  &__brand {
    display: flex;
    align-items: center;
    padding: var(--app-header-padding-y) var(--app-header-padding-x);
    gap: 1rem;
    img {
      flex-shrink: 0;
      width: 24px;
      height: 24px;
      object-fit: contain;
    }
    span {
      font-size: 1.2rem;
      span {
        font-family: monospace;
        font-size: 1rem;
      }
    }
  }

  &__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--app-header-padding-y) var(--app-header-padding-x);
  }

  &__navigation {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    hr {
      margin: 1rem -0.5rem;
      --hr-color: var(--hr-color-light);
    }
  }
  &__sidebar {
    padding: 1rem 0.5rem;
  }
  &__footer {
    position: sticky;
    bottom: 0;
    padding: 1rem 0.5rem;
    border-top: 1px solid var(--hr-color);
    background: var(--app-side-bg);
    box-shadow: -5px 0 10px rgba(#000, 0.7);
  }

  &__main {
    min-width: 0;
    padding: 1rem 1.5rem;
  }
}

#title {
  font-size: 1.5rem;
}

.modal-content {
  padding: 2rem;
}
</style>
