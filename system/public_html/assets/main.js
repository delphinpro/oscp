/*
 * OSPanel Web Dashboard
 * Copyright (c) 2023.
 * Licensed under MIT License
 */

const ENTRY_POINT = 'http://api.ospanel';

setInterval(check_api_host, 1000);

http_get('/main')
    .then(res => {
        document.getElementById('main').innerHTML = res.main;
        window.domains = res.domains;
        rememberTabs('tab_group', '.site-groups');
        rememberTabs('tab_main', '.main-tabs');
    });

document.addEventListener('click', function (e) {
    let link = e.target.closest('.open-console');
    if (link) {
        e.preventDefault();
        e.stopPropagation();
        let href = link.dataset.api;

        api.request(href)
            .then(res => res.text());
    }
});
