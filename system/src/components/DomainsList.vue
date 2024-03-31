<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024.
  Licensed under MIT License
  --------------------------->

<script>
import http from '@/services/http';

export default {
  name: 'DomainsList',

  props: {
    domains: Array,
  },

  methods: {
    openConsole(host) {
      console.log({ host });
      http.post('sites/console', { host });
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
            >
              {{ domain.siteUrl.replace('http://', '').replace('https://', '') }}
            </a>
            <span class="text-muted" v-else>{{
                domain.siteUrl.replace('http://', '').replace('https://', '')
              }}</span>
          </div>
        </td>
        <td class="text-muted"><small>{{ domain.engine }}</small></td>
        <td class="text-success"><small v-if="domain.ssl"><i class="bi bi-lock-fill"></i></small></td>
        <td class="ps-4">
          <div class="d-flex align-items-center gap-0.5 justify-end" v-if="!domain.enabled">
            <span class="text-muted">Сайт отключён</span>
            <i class="bi bi-exclamation-triangle-fill text-danger"></i>
          </div>
          <div class="d-flex align-items-center gap-0.5 justify-end" v-else-if="!domain.isAvailable">
            <span class="text-muted">Модуль {{ domain.engine }} отсутствует или выключен</span>
            <i class="bi bi-exclamation-triangle-fill text-danger"></i>
          </div>
          <div class="d-flex align-items-center gap-0.5 justify-end" v-else-if="!domain.isValidRoot">
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
              <button @click="openConsole(domain.host)" class="btn btn-icon" title="Консоль">
                <i class="bi bi-terminal"></i>
              </button>
              <!--<button @click="openConsole(domain.consoleUrl)" class="btn btn-icon" title="Настройки">
                <i class="bi bi-gear"></i>
              </button>-->
            </div>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<style lang="scss"></style>
