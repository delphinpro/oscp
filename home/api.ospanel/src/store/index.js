/*
 * Web OSP by delphinpro
 * Copyright (c) 2024-2025.
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
        /** @var {GeneralInfo} main */
        main    : {
            apiDomain : 'api.ospanel',
            apiEngine : 'PHP-8.1',
            webApiUrl : null,
            cliApiUrl : null,
            ospVersion: null,
            ospDate   : null,
        },
        settings: {},
        version : '1.1',

        pageTitle: null,

        isLoading : false,
        sysMessage: null,
        restarting: false,
    },

    getters: {},

    mutations: {
        setRestarting(state, value) { state.restarting = value; },

        setMainData(state, params) {
            state.main = params.main;
            state.settings = params.settings;
        },

        setCliApiUrl(state, value) {
            let cliApiUrl = (new URL(value)).pathname;
            state.cliApiUrl = cliApiUrl;
            http.configure({ cliApiUrl: cliApiUrl });
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

        systemReload({ commit }) {
            commit('setRestarting', true);
            window.ping = false;
            http.apiCall('restart').finally(() => {
                setTimeout(() => window.ping = true, 3000);
            });
        },
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
