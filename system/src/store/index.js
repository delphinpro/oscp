/*
 * Web OSP by delphinpro
 * Copyright (c) 2024.
 * Licensed under MIT License
 */

import http from '@/services/http';
import { createLogger, createStore } from 'vuex';
import sites from './modules/sites';

const isDebug = process.env.NODE_ENV !== 'production';

let timeout;

export default createStore({
    state: {
        apiDomain : 'api.ospanel',
        apiEngine : null,
        webApiUrl : null,
        cliApiUrl : null,
        ospVersion: null,
        ospDate   : null,
        settings  : {},

        pageTitle: null,

        modules: null,

        isLoading : false,
        sysMessage: null,
    },

    getters: {},

    mutations: {
        setMainData(state, params) {
            state.apiDomain = params.apiDomain;
            state.apiEngine = params.apiEngine;
            state.webApiUrl = params.webApiUrl;
            state.cliApiUrl = params.cliApiUrl;
            state.ospVersion = params.version;
            state.ospDate = params.releaseDate;
            state.settings = params.settings;
        },

        updateSetting(state, { key, value }) {
            let keys = key.split('.');
            let last = keys.pop();
            let target = state.settings;
            keys.forEach(k => target = target[k]);
            // console.log({ keys, key, value, target });
            target[last] = value;
        },

        setPageTitle(state, value) { state.pageTitle = value; },

        setModules(state, value) { state.modules = value; },

        showLoader(state) { state.isLoading = true; },
        hideLoader(state) { state.isLoading = false; },

        showMessage(state, msg) {
            state.sysMessage = msg;
            clearTimeout(timeout);
            setTimeout(() => {
                state.sysMessage = null;
            }, 5000);
        },
        hideMessage(state) { state.sysMessage = null; },
    },

    actions: {
        loadMainData(store) {
            http.get('/main').then(res => {
                // console.log(res.settings.main);
                store.commit('setMainData', res);
            });
        },
    },

    modules: {
        sites,
    },

    strict : isDebug,
    plugins: isDebug ? [
        createLogger(),
    ] : [],
});
