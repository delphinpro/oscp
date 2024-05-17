<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024.
  Licensed under MIT License
  --------------------------->

<script>
import http from '@/services/http';
import { mapActions } from 'vuex';

// noinspection JSValidateTypes
export default {
  name: 'DomainsList',

  props: {
    /** @var {Array<Domain>} domains */
    domains    : Array,
    hideAliases: Boolean,
  },

  methods: {
    ...mapActions({
      showMessage: 'showMessage',
    }),
    openConsole(host) {
      // http.apiCall('/project/cli/' + host + '/').then(res => {
      //   console.log(res);
      // });
      http.post('sites/console', { host })
          .catch(message => this.showMessage({
                message,
                title  : 'Server error',
                style  : 'danger',
                timeout: 5,
              }),
          );
    },

    showDomain(host) {
      return host.replace('http://', '').replace('https://', '');
    },
  },
};
</script>

<template>
  <div>
    <table class="table table-borderless">
      <tbody>
        <tr v-for="domain in domains">
          <td style="width: 33%;">
            <div class="-d-flex -align-items-center text-nowrap mono">
              <template v-if="domain.computed.enabled && domain.isValidRoot && domain.isAvailable">
                <a :href="domain.siteUrl" target="_blank">{{ showDomain(domain.siteUrl) }}</a>
                <div v-if="!hideAliases && domain.computed.host_aliases" class="mt-0.5">
                  <div v-for="alias in domain.computed.host_aliases" style="padding-left: 2rem;">
                    <a :href="domain.computed.ssl ? 'https://' + alias : 'http://' + alias" target="_blank">
                      {{ alias }}
                    </a>
                  </div>
                </div>
              </template>
              <template v-else>
                <span class="text-muted">{{ showDomain(domain.siteUrl) }}</span>
                <div v-if="!hideAliases && domain.computed.host_aliases" class="mt-0.5">
                  <div v-for="alias in domain.computed.host_aliases" class="text-muted" style="padding-left: 2rem;">
                    {{ alias }}
                  </div>
                </div>
              </template>
            </div>
          </td>
          <td class="text-success text-center" style="width: 40px;">
            <small v-if="domain.computed.ssl"><i class="bi bi-lock-fill cursor-help" title="SSL включён"></i></small>
          </td>
          <td class="text-muted text-nowrap" style="width: 5%;">
            <span class="d-flex align-items-center gap-0.5">
              <i v-if="!domain.isReadyPhpEngine"
                  :title="'Модуль '+domain.computed.php_engine+' отсутствует или выключен'"
                  class="bi bi-exclamation-triangle-fill text-danger cursor-help"
              ></i>
              <small>{{ domain.computed.php_engine }}</small>
            </span>
          </td>
          <td class="text-muted text-nowrap" style="width: 5%;">
            <span class="d-flex align-items-center gap-0.5">
              <i v-if="!domain.isReadyNginxEngine"
                  :title="'Модуль '+domain.computed.nginx_engine+' отсутствует или выключен'"
                  class="bi bi-exclamation-triangle-fill text-danger cursor-help"
              ></i>
              <small>{{ domain.computed.nginx_engine }}</small>
            </span>
          </td>
          <td class="ps-4">
            <div class="d-flex align-items-center gap-1 justify-end text-end">
              <div v-if="!domain.computed.enabled" class="d-flex align-items-center gap-0.5 justify-end">
                <span class="text-muted">Сайт отключён</span>
                <i class="bi bi-exclamation-triangle-fill text-danger"></i>
              </div>
              <!--<div v-else-if="!domain.isAvailable" class="d-flex align-items-center gap-0.5 justify-end">
                <span class="text-muted">Модуль {{ domain.computed.php_engine }} отсутствует или выключен</span>
                <i class="bi bi-exclamation-triangle-fill text-danger"></i>
              </div>-->
              <div v-else-if="!domain.isValidRoot" class="d-flex align-items-center gap-0.5 justify-end">
                <span class="text-muted">Неверная папка домена</span>
                <i class="bi bi-exclamation-triangle-fill text-danger"></i>
              </div>
              <div v-if="domain.computed.enabled && domain.isValidRoot && domain.isAvailable">
                <div class="btn-group justify-end">
                  <a v-if="domain.adminUrl"
                      :href="domain.adminUrl"
                      class="btn btn-icon"
                      target="_blank"
                      title="Админка"
                  ><i class="bi bi-box-arrow-in-right"></i></a>
                  <button class="btn btn-icon" title="Консоль" @click="openConsole(domain.host)">
                    <i class="bi bi-terminal"></i>
                  </button>
                </div>
              </div>
              <div class="btn-group justify-end">
                <router-link :to="{ name: 'siteEdit', params: { host: domain.host } }"
                    class="btn btn-icon"
                    title="Настройки"
                >
                  <i class="bi bi-gear"></i>
                </router-link>
              </div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style lang="scss">
</style>
