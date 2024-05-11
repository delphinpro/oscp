<!----------------------------
  Web OSP by delphinpro
  Copyright (c) 2024.
  Licensed under MIT License
  --------------------------->

<script>
import http from '@/services/http';
import { mapActions } from 'vuex';

export default {
  name: 'FileSelector',

  props: ['modelValue', 'required', 'error', 'showFiles', 'initialPath', 'placeholder'],
  emits: ['update:modelValue', 'selectValue'],

  data: () => ({
    isOpenBrowser: false,
    currentDir   : '',
    files        : [],
    newDir       : '',
  }),

  methods: {
    ...mapActions({
      showErrorMessage: 'showErrorMessage',
    }),
    openBrowser() {
      this.isOpenBrowser = true;
      this.currentDir = this.modelValue ?? '';
      if (!this.currentDir) {
        this.currentDir = this.initialPath ?? '';
      }
      this.readFs();
    },

    closeBrowser() {
      this.isOpenBrowser = false;
    },

    async readFs() {
      let directory = this.currentDir;
      let res = await http.post('fs', { directory, files: !!this.showFiles });
      this.files = res.list;
      this.currentDir = res.directory;
    },

    changeDir(dir) {
      this.currentDir = dir;
      this.readFs();
    },

    selectDir() {
      this.$emit('update:modelValue', this.currentDir);
      this.$emit('selectValue');
      this.closeBrowser();
    },

    async createDir() {
      if (this.newDir.trim().length === 0) {
        alert('Введите имя создаваемой папки');
      } else {
        let directory = this.currentDir;
        try {
          let res = await http.post('fs/create', { directory, newDir: this.newDir.trim(), files: !!this.showFiles });
          this.files = res.list;
          this.currentDir = res.directory;
          this.newDir = '';
        } catch (message) {
          await this.showErrorMessage({ message });
        }
      }
    },
  },

};
</script>

<template>
  <div class="file">
    <input
        :class="{withError: error}"
        :placeholder="placeholder"
        :required="required"
        :value="modelValue"
        class="input monospace"
        type="text"
        @input="$emit('update:modelValue', $event.target.value)"
    >
    <button class="btn" @click="openBrowser"><i class="bi bi-three-dots"></i></button>

    <teleport to="body">
      <div v-if="isOpenBrowser">
        <div class="modal-overlay"></div>
        <div class="modal-wrapper">
          <div class="modal">
            <div class="modal__header">
              <span style="margin-right:auto;">Выбор {{ showFiles ? 'файла' : 'папки' }}</span>
              <i class="bi bi-arrow-repeat modal__closer" @click="readFs()"></i>
              <i class="bi bi-x-lg modal__closer" @click="closeBrowser()"></i>
            </div>
            <div class="modal__body">
              <div class="top">
                <div>
                  <div :style="{direction: currentDir.length>3 ? 'rtl':'ltr'}" class="current-path">
                    {{ currentDir }}
                  </div>
                  <button class="btn" @click="selectDir">Выбрать</button>
                </div>
              </div>
              <div class="files">
                <div v-for="(name, path) in files" :key="path" class="fs-item" @click="changeDir(path)">
                  <i v-if="name==='..'" class="bi bi-arrow-90deg-up"></i>
                  <i v-else class="bi bi-folder"></i>
                  <span>{{ name }}</span>
                </div>
              </div>
              <div class="modal__actions">
                <input v-model="newDir" class="input" placeholder="Новая папка..." type="text">
                <button class="btn text-nowrap" @click="createDir">Создать здесь</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </teleport>

  </div>
</template>

<style lang="scss" scoped>
.modal-overlay,
.modal-wrapper {
  position: fixed;
  z-index: 900;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.modal-overlay {
  background: rgba(#111827, 0.3);
}

.modal-wrapper {
  display: flex;
  align-items: flex-start;
  padding-top: 8rem;
  overscroll-behavior: none;
}

.files {
  overflow: auto;
  height: 25rem;
  max-height: 60vh;
  padding: 1rem;
  user-select: none;
  border-top: 1px solid var(--hr-color);
}

.top {
  padding: 0.5rem 2rem;
  > div {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
  }
}

.current-path {
  font-family: monospace;
  font-size: 1.1rem;
  overflow: hidden;
  //margin-bottom: 1rem;
  text-align: left;
  white-space: nowrap;
  text-overflow: ellipsis;
  direction: rtl;
}

.fs-item {
  font-family: monospace;
  font-size: 1.2rem;
  display: flex;
  padding: 0.25rem 1rem;
  cursor: pointer;
  gap: 0.5rem;

  &:hover {
    background: var(--app-side-bg);
  }
}

.file {
  display: flex;
  gap: 3px;
}

.input {
  border-radius: var(--radius-half) 0 0 var(--radius-half);
}

.btn {
  border-radius: 0 var(--radius-half) var(--radius-half) 0;
}
</style>
