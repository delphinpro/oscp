/*
 * Web OSP by delphinpro
 * Copyright (c) 2024.
 * Licensed under MIT License
 */

import http from '@/services/http';

const state = () => ({
    modules: null,
});

const getters = {};

const mutations = {
    setModules(state, value) { state.modules = value; },
};

const actions = {
    loadModules({ commit }) {
        return http.get('/modules').then(res => {
            commit('setModules', res.modules);
            console.log('Modules data loaded');
        });
    },

    moduleRestart(ctx, module) {
        return new Promise(async function (resolve, reject) {
            try {
                let message = await http.apiCall(`/off/${module}/`);
                message += '<br>' + await http.apiCall(`/on/${module}/`);
                resolve(message);
            } catch (err) {
                reject(err.message);
            }
        });
    },
};

export default {
    state,
    getters,
    mutations,
    actions,
};
