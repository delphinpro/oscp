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
import { mapState } from 'vuex';

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
    console.log('App mounted');
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

    this.$store.dispatch('loadMainData');
  },

  unmounted() {
    clearInterval(pingInterval);
  },

  methods: {
    systemReload() {
      http.get('/restart').then(res => {
        console.log(res);
      });
    },
  },

};
</script>

<template>
  <div>
    <div class="error" v-if="error || noHost">
      <pre v-if="error">{{ error }}</pre>
      <div v-if="noHost">
        <Alert :title="'Хост '+ apiHost +' недоступен'">
          Необходимо добавить домен <code>{{ apiDomain }}</code>
          и включить модуль <code>{{ apiEngine }}</code>.
        </Alert>
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
    <template v-else>
      <div class="app">
        <div class="app__brand">
          <img class="" src="/assets/icon.svg" alt="">
          <span>OSPanel <span class="text-muted">{{ ospVersion }}</span></span>
        </div>
        <div class="app__header">
          <div id="title">{{ pageTitle }}</div>
          <button class="btn" @click="systemReload">Перезагрузить</button>
        </div>
        <div class="app__navigation">
          <side-bar/>
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
.app {
  width: 100%;
  min-height: 100vh;
  display: grid;
  grid-template-columns: 17rem 1fr;
  grid-template-rows: auto 1fr;

  &__brand, &__navigation {
    background: #1f2937;
    border-right: 1px solid var(--hr-color);
  }

  &__brand, &__header {
    border-bottom: 1px solid var(--hr-color);
  }

  &__brand {
    padding: 1rem 1.5rem;
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
        font-size: 1rem;
        font-family: monospace;
      }
    }
  }

  &__header {
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
  }

  &__navigation {
    padding: 1rem 0.5rem;
    hr {
      --hr-color: var(--hr-color-light);
      margin: 1rem -0.5rem;
    }
  }

  &__main {
    padding: 1rem 1.5rem;
    min-width: 0;
  }
}

#title {
  font-size: 1.5rem;
}
</style>
