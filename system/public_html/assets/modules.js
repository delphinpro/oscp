/*
 * OSPanel Web Dashboard
 * Copyright (c) 2023.
 * Licensed under MIT License
 */

function module_action(action, module) {
    http_post(`/module/${action}/`, { module })
        .then(res => {
            if (res) {
                document.getElementById('modules').innerHTML = res;
            }
        });
}
