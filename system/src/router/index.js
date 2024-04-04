/*
 * Web OSP by delphinpro
 * Copyright (c) 2024.
 * Licensed under MIT License
 */

import HomeView from '@/views/HomeView.vue';
import ModulesView from '@/views/ModulesView';
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
        component: SitesEditView,
    },
    {
        path     : '/modules',
        name     : 'modules',
        component: ModulesView,
    },
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

export default router;
