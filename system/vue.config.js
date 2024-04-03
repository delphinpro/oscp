/*
 * Web OSP by delphinpro
 * Copyright (c) 2024.
 * Licensed under MIT License
 */

const { defineConfig } = require('@vue/cli-service');
const path = require('path');

const isDev = process.env.NODE_ENV !== 'production';

module.exports = defineConfig({
    transpileDependencies: true,
    outputDir            : path.resolve(__dirname, 'public_html'),
    filenameHashing: false,
    chainWebpack   : config => {
        config.plugin('define').tap((definitions) => {
            Object.assign(definitions[0], {
                __VUE_OPTIONS_API__                    : 'true',
                __VUE_PROD_DEVTOOLS__                  : 'false',
                __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: 'false',
            });
            return definitions;
        });
        if (!isDev) {
            config.plugins.delete('html');
            config.plugins.delete('preload');
            config.plugins.delete('prefetch');
        }
    },

    configureWebpack: {
        devServer: {
            headers: {
                'Access-Control-Allow-Origin'     : '*',
                'Access-Control-Allow-Methods'    : 'OPTIONS, GET, POST, PUT, DELETE',
                'Access-Control-Allow-Headers'    : 'Authorization, Origin, X-Requested-With, Accept, X-PINGOTHER, Content-Type',
                'Access-Control-Allow-Credentials': 'true',
            },
            proxy  : {
                '/api/*': {
                    target: 'http://ospanel',
                    secure      : false,
                    changeOrigin: false,
                },
            },
        },
    },

});
