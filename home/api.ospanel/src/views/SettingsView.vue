<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024-2025.
  Licensed under MIT License
  --------------------------->

<script>
import Checkbox from '@/components/Checkbox.vue';
import FormCheckbox from '@/components/FormCheckbox.vue';
import FormInput from '@/components/FormInput.vue';
import http from '@/services/http';
import { mapActions } from 'vuex';

export default {
  name: 'SettingsView',

  components: {
    FormCheckbox,
    FormInput,
    Checkbox,
  },

  data: () => ({
    /** @var {Settings|object} settings */
    settings: {},

    ready: false,

    tab : 'general',
    tabs: {
      general : 'Основные',
      menu    : 'Меню',
      projects: 'Проекты',
      modules : 'Модули',
      smtp    : 'SMTP',
      env     : 'Окружение',
    },
  }),

  async created() {
    const lastTab = localStorage.getItem('last_settings_tab');
    if (lastTab && this.tabs[lastTab]) {
      this.tab = lastTab;
    }

    const res = await http.get('settings');
    this.settings = res.settings;
    this.ready = true;
  },

  mounted() {
    this.$store.commit('setPageTitle', 'Настройки');
  },

  methods: {
    ...mapActions({
      systemReload: 'systemReload',
    }),

    switchTab(tab) {
      this.tab = tab;
      localStorage.setItem('last_settings_tab', tab);
    },
    save() {
      http.post('settings', this.settings).then(() => {
        this.systemReload();
      });
    },
  },

};
</script>

<template>
  <div>
    <teleport to="#top">
      <button v-if="ready" class="btn btn-success" @click="save">Сохранить</button>
    </teleport>

    <div class="alert mb-1">После изменения настроек будет выполнен перезапуск программы</div>

    <div v-if="ready">

      <div class="tabs">
        <div
            v-for="(text, value) in tabs"
            :key="value"
            :class="{active: tab === value}"
            class="tab"
            @click="switchTab(value)"
            v-text="text"
        ></div>
      </div>

      <div v-if="tab==='general'" class="tab-content">
        <h3>Основные настройки</h3>
        <div class="form">
          <form-checkbox v-model="settings.main.update_check" hint="update_check" label="Проверять обновления"/>
          <form-checkbox v-model="settings.main.use_hosts_file"
              hint="use_hosts_file"
              label="Использовать файл HOSTS"
          />
          <form-checkbox v-model="settings.main.clear_dns_cache"
              hint="clear_dns_cache"
              label="Очищать системный кэш DNS при изменении файла HOSTS"
          />
          <form-checkbox
              v-model="settings.main.task_scheduler"
              hint="task_scheduler"
              label="Включить встроенный планировщик заданий"
          />
          <form-checkbox
              v-model="settings.main.use_win_terminal"
              hint="use_win_terminal"
              label="Использовать Windows Terminal"
          />
          <form-input
              v-model="settings.main.lang"
              desc="Используйте имя любого языкового файла из каталога system\lang"
              hint="lang"
              label="Язык программы"
          />
          <form-input
              v-model="settings.main.projects_search_path"
              desc="Каталоги, в которых осуществляется поиск проектов"
              hint="projects_search_path"
              label="Расположение ваших проектов"
          />
          <form-input
              v-model="settings.main.projects_search_depth"
              desc=""
              hint="projects_search_depth"
              label="Глубина поиска проектов"
          />
          <form-input v-model="settings.main.log_max_filesize"
              desc="0 — отключает ограничение"
              hint="log_max_filesize"
              label="Максимальный размер журнала"
          />
          <form-input
              v-model="settings.main.terminal_ansi_fix"
              desc=""
              hint="terminal_ansi_fix"
              label="Поддержка ANSI-кодов управления цветом в консоли"
          />
          <div class="form-subtitle">Локальный домен API и веб-интерфейса</div>
          <form-input v-model="settings.main.api_domain" hint="api_domain" label="Домен"/>
          <form-input v-model="settings.main.api_ip" hint="api_ip" label="IPv4-адрес"/>
          <form-input v-model="settings.main.api_port" hint="api_port" label="Порт"/>
        </div><!--/.form-->
      </div>

      <div v-if="tab==='menu'" class="tab-content">
        <h3>Настройки меню</h3>
        <div class="form">
          <form-checkbox
              v-model="settings.menu.do_not_group_single_item"
              hint="do_not_group_single_item"
              label="Не группировать списки из одного элемента"
          />
          <form-checkbox v-model="settings.menu.show_icons"
              hint="show_icons"
              label="Показывать иконки в меню программы"
          />
          <form-checkbox
              v-model="settings.menu.show_tray_icon"
              hint="show_tray_icon"
              label="Показывать иконку программы в области уведомлений"
          />

          <div class="form-subtitle">Модули</div>
          <form-checkbox
              v-model="settings.menu.show_modules"
              hint="show_modules"
              label="Показывать меню управления модулями"
          />
          <form-checkbox
              v-model="settings.menu.show_modules_in_groups"
              hint="show_modules_in_groups"
              label="Группировать модули в меню"
          />
          <form-checkbox
              v-model="settings.menu.show_modules_in_submenu"
              hint="show_modules_in_submenu"
              label="Показывать меню модулей в отдельном подменю"
          />
          <form-checkbox
              v-model="settings.menu.show_hr_after_modules"
              hint="show_hr_after_modules"
              label="Показывать линию после меню модулей"
          />

          <div class="form-subtitle">Проекты</div>
          <form-checkbox
              v-model="settings.menu.show_projects"
              hint="show_projects"
              label="Показывать меню управления проектами"
          />
          <form-checkbox
              v-model="settings.menu.show_projects_in_groups"
              hint="show_projects_in_groups"
              label="Использовать группировку по доменной зоне в меню проектов"
          />
          <form-checkbox
              v-model="settings.menu.show_projects_in_submenu"
              hint="show_projects_in_submenu"
              label="Показывать меню проектов в отдельном подменю"
          />
          <form-checkbox
              v-model="settings.menu.show_hr_after_projects"
              hint="show_hr_after_projects"
              label="Показывать линию после меню проектов"
          />

          <div class="form-subtitle">Дополнения</div>
          <form-checkbox v-model="settings.menu.show_addons" hint="show_addons" label="Показывать меню дополнений"/>
          <form-checkbox
              v-model="settings.menu.show_addons_in_groups"
              hint="show_addons_in_groups"
              label="Группировать дополнения в меню"
          />
          <form-checkbox
              v-model="settings.menu.show_addons_in_submenu"
              hint="show_addons_in_submenu"
              label="Показывать меню дополнений в отдельном подменю"
          />
          <form-checkbox
              v-model="settings.menu.show_hr_after_addons"
              hint="show_hr_after_addons"
              label="Показывать линию после меню дополнений"
          />
        </div>
      </div>

      <div v-if="tab==='projects'" class="tab-content">
        <h3>Настройки по умолчанию для проектов</h3>
        <div class="form">
          <form-checkbox v-model="settings.projects.enabled" hint="enabled" label="Домен включён"/>
          <form-input v-model="settings.projects.aliases" hint="aliases" label="Псевдонимы (алиасы) домена"/>
          <form-input v-model="settings.projects.environment" hint="environment" label="Доп. окружение"/>
          <form-input v-model="settings.projects.ip" hint="ip" label="IP-адрес"/>
          <form-input v-model="settings.projects.node_engine" hint="node_engine" label="Движок NodeJS"/>
          <form-input v-model="settings.projects.php_engine" hint="php_engine" label="PHP"/>
          <form-input v-model="settings.projects.nginx_engine" hint="nginx_engine" label="Nginx"/>
          <form-input v-model="settings.projects.project_dir" hint="project_dir" label="Рабочий каталог"/>
          <form-input v-model="settings.projects.public_dir" hint="public_dir" label="Публичный каталог"/>
          <form-input v-model="settings.projects.project_url" hint="project_url" label="Адрес сайта"/>
          <form-input v-model="settings.projects.start_command" hint="start_command" label="Команда запуска"/>
          <form-input v-model="settings.projects.terminal_codepage"
              hint="terminal_codepage"
              label="Кодировка консоли"
          />
          <div class="form-subtitle">SSL</div>
          <form-checkbox v-model="settings.projects.ssl" hint="ssl" label="SSL включён"/>
          <form-input
              v-model="settings.projects.ssl_cert_file"
              desc="По умолчанию: автоматически сгенерированный сертификат"
              hint="ssl_cert_file"
              label="Файл сертификата"
          />
          <form-input
              v-model="settings.projects.ssl_key_file"
              desc="По умолчанию: автоматически сгенерированный ключ"
              hint="ssl_key_file"
              label="Файл ключа"
          />
        </div>
      </div>

      <div v-if="tab==='modules'" class="tab-content">
        <h3>Глобальные настройки модулей</h3>
      </div>

      <div v-if="tab==='smtp'" class="tab-content">
        <h3>Настройки встроенного почтового сервера</h3>
      </div>

      <div v-if="tab==='env'" class="tab-content">
        <h3>Глобальные переменные окружения модулей</h3>
      </div>

    </div>

  </div>
</template>

<style lang="scss" scoped>

</style>
