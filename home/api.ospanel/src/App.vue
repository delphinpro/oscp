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
    hostIsAvailable: true,
  }),

  computed: {
    ...mapState({
      isLoading : state => state.isLoading,
      restarting: state => state.restarting,

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
        <div class="app__brand">
          <img alt="" class="" src="/assets/icon.svg">
          <span>OSPanel <span class="text-muted">{{ ospVersion }}</span></span>
        </div>
        <div class="app__header">
          <div id="title">{{ pageTitle }}</div>
          <div id="top"></div>
        </div>
        <div class="app__navigation">
          <side-bar class="app__sidebar"/>
          <div class="app__footer">
            <nav class="nav nav_compact">
              <button class="nav__item active" style="padding-block:0.5rem" @click="systemReload">
                <i class="bi bi-arrow-clockwise"></i>
                Перезапустить
              </button>
              <a class="nav__item muted" href="https://ospanel.io/forum/" target="_blank">
                <i class="bi bi-box-arrow-up-right"></i>
                Форум
              </a>
              <a class="nav__item muted" href="https://t.me/ospanel_chat" target="_blank">
                <i class="bi bi-box-arrow-up-right"></i>
                Telegram
              </a>
              <a class="nav__item muted"
                  href="https://github.com/OSPanel/OpenServerPanel/wiki/Документация"
                  target="_blank"
              >
                <i class="bi bi-box-arrow-up-right"></i>
                Документация
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
  min-height: 100vh;
  grid-template-columns: 17rem 1fr;
  grid-template-rows: auto 1fr;

  &__brand, &__navigation {
    border-right: 1px solid var(--hr-color);
    background: var(--app-side-bg);
  }

  &__brand, &__header {
    height: var(--app-header-height);
    padding: var(--app-header-padding-y) var(--app-header-padding-x);
    border-bottom: 1px solid var(--hr-color);
  }

  &__brand {
    display: flex;
    align-items: center;
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
