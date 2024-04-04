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
            return res.modules;
        });
    },
};

export default {
    state,
    getters,
    mutations,
    actions,
};
