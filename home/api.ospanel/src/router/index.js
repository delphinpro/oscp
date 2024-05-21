/*
 * Web OSP by delphinpro
 * Copyright (c) 2024.
 * Licensed under MIT License
 */

import HomeView from '@/views/HomeView.vue';
import ModulesView from '@/views/ModulesView';
import SettingsView from '@/views/SettingsView.vue';
import SitesCreateView from '@/views/SitesCreateView.vue';
import SitesEditView from '@/views/SitesEditView.vue';
import SitesView from '@/views/SitesView';
import { createRouter, createWebHashHistory } from 'vue-router';

const routes = [
    {
        path     : '/',
        name     : 'home',
        component: HomeView,
    },
    {
        path     : '/sites',
        name     : 'sites',
        component: SitesView,
    },
    {
        path     : '/sites/:host',
        name     : 'siteEdit',
        component: SitesEditView,
    },
    {
        path     : '/sites/create',
        name     : 'siteCreate',
        component: SitesCreateView,
    },
    {
        path     : '/modules',
        name     : 'modules',
        component: ModulesView,
    },
    {
        path     : '/settings',
        name     : 'settings',
        component: SettingsView,
    },
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

router.afterEach((to, from, failure) => {
    if (!failure) {
        localStorage.setItem('LAST_PAGE', to.path);
    }
});

export default router;
