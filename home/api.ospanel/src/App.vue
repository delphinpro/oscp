<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2023-2025.
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
    hostIsAvailable: true,
  }),

  computed: {
    ...mapState({
      version: state => state.version,

      isLoading : state => state.isLoading,
      restarting: state => state.restarting,

      apiEngine : state => state.main.apiEngine,
      apiDomain : state => state.main.apiDomain,
      ospVersion: state => state.main.ospVersion,
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
    const lastPage = localStorage.getItem('LAST_PAGE');
    if (lastPage) {
      // noinspection JSUnresolvedReference
      this.$router.replace({ path: lastPage });
    }
  },

  async mounted() {
    this.startPing();
    try {
      this.showLoader();
      await this.loadMainData();
      // noinspection JSUnresolvedReference
      if (this.$router.currentRoute.value.name !== 'sites') {
        await this.loadSites();
      }
    } catch (err) {
      this.hostIsAvailable = false;
      console.error(err);
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
      systemReload      : 'systemReload',
    }),

    async enableEngine() {
      this.showLoader();

      try {
        let message = await http.apiCall(`/on/${this.apiEngine}/`);
        await this.showSuccessMessage({ message });
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
            .then(res => res.ok ? res.json() : res.text())
            .then(res => {
              if (typeof res === 'string') {
                throw new Error(res.toString());
              }
              this.hostIsAvailable = true;
              if (this.restarting) {
                location.reload();
              }
            })
            .catch(() => {
              this.hostIsAvailable = false;
            });
      }, PING_INTERVAL);
    },
  },

};
</script>

<template>
  <div>
    <div v-if="restarting" class="reload-message">
      <div class="modal modal-danger">
        <div class="modal__header">
          <span style="margin-right:auto;">Перезапуск OSPanel</span>
        </div>
        <div class="modal__body">
          <div class="modal-content">
            <h2 style="margin-top: 0;font-weight:400;">Выполняется перезапуск программы</h2>
            <p>Дождитесь завершения</p>
          </div>
        </div>
      </div>
    </div>

    <div v-if="!hostIsAvailable && !restarting" class="error">
      <div>
        <div class="modal modal-danger">
          <div class="modal__header">
            <span style="margin-right:auto;">Ошибка</span>
          </div>
          <div class="modal__body">
            <div class="modal-content">
              <h2 style="margin-top: 0;font-weight:400;">Хост <code>{{ apiHost }}</code> недоступен</h2>
              <p>Выполните правильную установку web-панели.
                Проверьте наличие всех необходимых файлов в директории <code>OSPanel\home\api.ospanel</code>.</p>
              <p>Если хост сконфигурирован правильно, проверьте, не отключён ли модуль <code>{{ apiEngine }}</code></p>
              <p>Включить модуль можно используя соответствующий пункт меню программы или в консоли, выполнив
                команду:</p>
              <pre class="text-bg-dark p-3" style="font-size:1.3em">osp on {{ apiEngine }}</pre>
              <p>Или просто нажмите кнопку:</p>
              <button class="btn text-nowrap" @click="enableEngine">Включить модуль {{ apiEngine }}</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <template v-else>
      <div class="app">
        <div class="app__topbar">
          <div class="app__brand">
            <div class="app-brand">
              <img alt="" class="" src="/assets/icon.svg">
              <router-link :to="{ name: 'home' }">OSPanel <span class="text-muted">{{ ospVersion }}</span></router-link>
            </div>
            <button class="btn btn-icon" style="height:auto;" title="Перезапустить" @click="systemReload">
              <i class="bi bi-arrow-clockwise"></i>
            </button>
          </div>
          <div class="app__header">
            <div id="title">{{ pageTitle }}</div>
            <div id="top"></div>
          </div>
        </div>
        <div class="app__body">
          <div class="app__navigation">
            <side-bar class="app__sidebar"/>
          </div>
          <div class="app__main">
            <router-view/>
          </div>
          <div class="app__footer">
            <nav class="nav-inline">
              <span>Сообщество:</span>
              <a class="nav-inline__item" href="https://ospanel.io/forum/" target="_blank">
                <i class="bi bi-box-arrow-up-right"></i>
                Форум
              </a>
              <a class="nav-inline__item" href="https://t.me/ospanel_chat" target="_blank">
                <i class="bi bi-box-arrow-up-right"></i>
                Telegram
              </a>
              <a class="nav-inline__item"
                  href="https://github.com/OSPanel/OpenServerPanel/wiki/Документация"
                  target="_blank"
              >
                <i class="bi bi-box-arrow-up-right"></i>
                Документация
              </a>
            </nav>
            <div class="muted">
              © 2023-2025, OSCP by delphinpro v{{ version }}. Licensed under MIT License.
            </div>
          </div>
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

.reload-message {
  position: fixed;
  z-index: 9999;
  top: 0;
  left: 0;
  display: flex;
  width: 100%;
  height: 100%;
  background: rgba(#111827, 0.4);
  backdrop-filter: blur(5px);
  .modal {
    margin: auto;
  }
}

.app {
  display: grid;
  width: 100%;
  max-width: 75rem;
  min-height: 100vh;
  margin: 0 auto;
  border-right: 1px solid var(--hr-color);
  border-left: 1px solid var(--hr-color);
  background: var(--app-bg);
  grid-template-columns: var(--app-side-width) 1fr;
  grid-template-rows: var(--app-header-height) 1fr;

  &__topbar {
    position: sticky;
    z-index: 1;
    top: 0;
    display: grid;
    background: var(--app-bg);
    grid-template-columns: subgrid;
    grid-column: span 2;

    &::after {
      position: absolute;
      top: 100%;
      left: 0;
      width: 100%;
      height: 10px;
      content: '';
      background: linear-gradient(to bottom, rgba(#000, 0.4), transparent);
    }
  }

  &__brand {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--app-header-py) var(--app-px);
    border-right: 1px solid var(--hr-color);
    background: var(--app-side-bg);
  }

  &__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--app-header-py) var(--app-px);
  }

  &__body {
    display: grid;
    grid-template-columns: subgrid;
    grid-column: span 2;
    grid-template-rows: 1fr auto;
  }

  &__navigation {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-right: 1px solid var(--hr-color);
    background: var(--app-side-bg);
    grid-row: span 2;
    hr {
      margin: 1rem calc(var(--app-px) * -1);
    }
  }

  &__sidebar {
    position: sticky;
    top: var(--app-header-height);
    overflow: auto;
    max-height: calc(100vh - var(--app-header-height));
    padding: var(--app-py) var(--app-px);
  }

  &__footer {
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: calc(var(--app-py) / 2) var(--app-px);
    color: var(--muted-color);
    border-top: 1px solid var(--hr-color);
    background: #000;
    background: var(--app-side-bg);
  }

  &__main {
    min-width: 0;
    padding: var(--app-py) var(--app-px);
  }
}

.app-brand {
  display: flex;
  align-items: center;
  gap: 1rem;
  img {
    flex-shrink: 0;
    width: 24px;
    height: 24px;
    object-fit: contain;
  }
  a {
    font-size: 1.2rem;
    color: currentColor;
    span {
      font-family: monospace;
      font-size: 1rem;
    }
  }
}

#title {
  font-size: 1.5rem;
}

.modal-content {
  padding: 2rem;
}
</style>
