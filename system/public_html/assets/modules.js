/*
 * OSPanel Web Dashboard
 * Copyright (c) 2023.
 * Licensed under MIT License
 */

function module_action(action, module) {
    showLoader();
    if (action === 'restart') {
        window.ping = false;
        api.request(`/mod/off/${module}/`)
            .then(res => res.text())
            .then(res => message(res))
            .then(() => {
                api.request(`/mod/on/${module}/`)
                    .then(res => res.text())
                    .then(res => {
                        message(res);
                    })
                    .then(() => {
                        http_post(`/module/`)
                            .then(res => {
                                if (res) {
                                    document.getElementById('modules').innerHTML = res;
                                    document.dispatchEvent(new CustomEvent('modules_update'));
                                }
                                window.ping = true;
                                hideLoader();
                            });
                    });
            });
    } else {
        api.request(`/mod/${action}/${module}/`)
            .then(res => res.text())
            .then(res => {
                message(res);
                http_post(`/module/`)
                    .then(res => {
                        if (res) {
                            document.getElementById('modules').innerHTML = res;
                            document.dispatchEvent(new CustomEvent('modules_update'));
                        }
                        hideLoader();
                    });
            });
    }
}
