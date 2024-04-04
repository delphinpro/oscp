/*
 * Web OSP by delphinpro
 * Copyright (c) 2024.
 * Licensed under MIT License
 */

import http from '@/services/http';
import modules from '@/store/modules/modules';
import sites from '@/store/modules/sites';
import { createStore } from 'vuex';

const isDebug = process.env.NODE_ENV !== 'production';

let sysMessageTimeout;

export default createStore({
    state: {
        apiDomain : 'api.ospanel',
        apiEngine : 'PHP-8.1',
        webApiUrl : null,
        cliApiUrl : null,
        ospVersion: null,
        ospDate   : null,
        settings  : {},

        pageTitle: null,

        isLoading : false,
        sysMessage: null,
    },

    getters: {},

    mutations: {
        setMainData(state, params) {
            let cliApiUrl = new URL(params.cliApiUrl);

            state.apiDomain = params.apiDomain;
            state.apiEngine = params.apiEngine;
            state.webApiUrl = params.webApiUrl;
            state.cliApiUrl = cliApiUrl.pathname;
            state.ospVersion = params.version;
            state.ospDate = params.releaseDate;
            state.settings = params.settings;
        },

        updateSetting(state, { key, value }) {
            let keys = key.split('.');
            let last = keys.pop();
            let target = state.settings;
            keys.forEach(k => target = target[k]);
            target[last] = value;
        },

        setPageTitle(state, value) { state.pageTitle = value; },

        showLoader(state) { state.isLoading = true; },
        hideLoader(state) { state.isLoading = false; },

        setSystemMessage(state, systemMessage) {
            state.sysMessage = systemMessage;
        },
    },

    actions: {
        loadMainData({ commit, state }) {
            return http.get('/main').then(res => {
                commit('setMainData', res);
                http.configure({ cliApiUrl: state.cliApiUrl });
            });
        },

        showMessage({ commit, dispatch }, { message, title = null, style = 'info', timeout = false }) {
            commit('setSystemMessage', { message, title, style });
            clearTimeout(sysMessageTimeout);
            if (typeof timeout === 'number') {
                sysMessageTimeout = setTimeout(() => dispatch('hideMessage'), timeout * 1000);
            }
        },
        showSuccessMessage({ dispatch }, { message, title = null, style = 'success', timeout = 5 }) {
            dispatch('showMessage', { message, title, style, timeout });
        },
        showErrorMessage({ dispatch }, { message, title = null, style = 'danger', timeout = 5 }) {
            dispatch('showMessage', { message, title, style, timeout });
        },
        hideMessage({ commit }) {
            clearTimeout(sysMessageTimeout);
            commit('setSystemMessage', null);
        },

        showLoader({ commit }) { commit('showLoader'); },
        hideLoader({ commit }) { commit('hideLoader'); },
    },

    modules: {
        sites,
        modules,
    },

    strict : isDebug,
    plugins: isDebug ? [
        // createLogger(),
    ] : [],
});
