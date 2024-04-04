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
import { mapActions, mapState } from 'vuex';

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
    error : null,

    temp: null,
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
    http.configure({
      baseUrl: this.apiHost,
    });
  },

  mounted() {
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
            this.error = null;
            this.noHost = false;
          })
          .catch(err => {
            console.log('CHECK API HOST:', err.message);
            if (err.message === 'Failed to fetch') {
              this.noHost = true;
            } else {
              this.noHost = false;
              this.error = err.message;
            }
          });

    }, PING_INTERVAL);

    this.loadMainData();
  },

  unmounted() {
    clearInterval(pingInterval);
  },

  methods: {
    ...mapActions({
      loadMainData: 'loadMainData',
      showMessage : 'showMessage',
    }),
    systemReload() {
      this.noHost = true;
      this.showMessage({ title: 'Выполняется перезагрузка', style: 'danger', timeout: 5 });
      window.ping = false;
      http.get('/restart').then();
      setTimeout(() => window.ping = true, 5000);
    },
  },

};
</script>

<template>
  <div>
    <div v-if="error || noHost" class="error">
      <div class="wnd">
        <alert v-if="error" :message="error"/>
        <div v-if="noHost">
          <alert :message="'Необходимо добавить домен <code>'+apiDomain+'</code> и включить модуль <code>'+apiEngine+'</code>.'"
              :title="'Хост '+ apiHost +' недоступен'"
              danger
          />
          <p>1. Добавьте в файл <code>OSPanel/config/domains.ini</code> секцию следующего содержания:</p>
          <pre class="text-bg-dark p-3">[{{ apiDomain }}]
enabled         = on
engine          = {{ apiEngine }}
public_dir      = &#123;root_dir&#125;\system\public_html</pre>
          <p>2. Откройте интерфейс командной строки и выполните команду:</p>
          <pre class="text-bg-dark p-3">osp on {{ apiEngine }}</pre>
          <p>3. Обновите эту страницу</p>
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
  min-height: 100vh;
  padding: 3rem 0;
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
</style>
