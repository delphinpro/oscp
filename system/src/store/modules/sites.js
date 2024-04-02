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

        return Object.values(state.sites).map(group => ({
            name     : group.name,
            count    : group.domains.length,
            hasActive: group.hasActive,
        }));
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
            ctx.commit('setSites', sites);
            ctx.commit('setGrouped', grouped);
            if (grouped) {
                let savedGroup = localStorage.getItem(LS_KEY_SITES_GROUP);
                if (!savedGroup || !ctx.getters.groupNames.map(g => g.name).includes(savedGroup)) {
                    savedGroup = ctx.getters.groupNames.shift()['name'];
                }
                if (savedGroup) ctx.commit('selectGroup', savedGroup);
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
