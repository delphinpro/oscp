/*
 * Web OSP by delphinpro
 * Copyright (c) 2024.
 * Licensed under MIT License
 */

import http from '@/services/http';

const LS_KEY_SITES_GROUP = 'OSP_SITES_GROUP';

const state = () => ({
    grouped : false,
    sites   : null,
    selected: null,
});

const getters = {
    groupNames(state) {
        if (!state.grouped) return [];

        return Object.keys(state.sites);
    },
};

const mutations = {
    setGrouped: (state, value) => state.grouped = value,

    setSites: (state, value) => state.sites = value,

    selectGroup(state, value) {
        state.selected = value;
        localStorage.setItem(LS_KEY_SITES_GROUP, value);
    },

    resetGroup: state => state.selected = null,
};

const actions = {
    loadSites(ctx) {
        http.get('/sites').then(({ grouped, sites }) => {
            console.log(grouped, sites);
            ctx.commit('setSites', sites);
            ctx.commit('setGrouped', grouped);
            if (grouped) {
                let savedGroup = localStorage.getItem(LS_KEY_SITES_GROUP);
                console.log({ savedGroup });
                if (!savedGroup || ctx.getters.groupNames.indexOf(savedGroup) === -1) {
                    savedGroup = ctx.getters.groupNames.shift();
                }
                ctx.commit('selectGroup', savedGroup);
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
