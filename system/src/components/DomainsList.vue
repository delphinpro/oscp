<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024.
  Licensed under MIT License
  --------------------------->

<script>
import http from '@/services/http';
import { mapActions } from 'vuex';

export default {
  name: 'DomainsList',

  props: {
    domains: Array,
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
  <table class="table table-borderless">
    <tbody>
      <tr v-for="domain in domains">
        <td style="width: 33%;">
          <div class="d-flex align-items-center text-nowrap mono">
            <a
                v-if="domain.enabled && domain.isValidRoot && domain.isAvailable"
                :href="domain.siteUrl"
                target="_blank"
            >{{ showDomain(domain.siteUrl) }}</a>
            <span v-else class="text-muted">{{ showDomain(domain.siteUrl) }}</span>
          </div>
        </td>
        <td class="text-muted"><small>{{ domain.engine }}</small></td>
        <td class="text-success"><small v-if="domain.ssl"><i class="bi bi-lock-fill"></i></small></td>
        <td class="ps-4">
          <div class="d-flex align-items-center gap-1 justify-end">
            <div v-if="!domain.enabled" class="d-flex align-items-center gap-0.5 justify-end">
              <span class="text-muted">Сайт отключён</span>
              <i class="bi bi-exclamation-triangle-fill text-danger"></i>
            </div>
            <div v-else-if="!domain.isAvailable" class="d-flex align-items-center gap-0.5 justify-end">
              <span class="text-muted">Модуль {{ domain.engine }} отсутствует или выключен</span>
              <i class="bi bi-exclamation-triangle-fill text-danger"></i>
            </div>
            <div v-else-if="!domain.isValidRoot" class="d-flex align-items-center gap-0.5 justify-end">
              <span class="text-muted">Неверная папка домена</span>
              <i class="bi bi-exclamation-triangle-fill text-danger"></i>
            </div>
            <div v-else>
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
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<style lang="scss"></style>
