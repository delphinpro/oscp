/*
 * OSPanel Web Dashboard
 * Copyright (c) 2023.
 * Licensed under MIT License
 */

const ENTRY_POINT = 'http://api.ospanel';

(function () {
    const lsKey = 'hide-off-modules';

    document.addEventListener('modules_update', function () {
        let hide = Boolean(localStorage.getItem(lsKey) ?? false);
        let switcher = document.getElementById('hide-off-modules');
        let modulesTable = document.getElementById('table-modules');
        if (switcher) switcher.checked = hide;
        if (hide && modulesTable) modulesTable.classList.add('hide-off-modules');
    });

    document.addEventListener('change', e => {
        let switcher = e.target.closest('#hide-off-modules');
        if (switcher) {
            let modulesTable = document.getElementById('table-modules');
            if (modulesTable) modulesTable.classList.toggle('hide-off-modules', switcher.cheked);
            localStorage.setItem(lsKey, switcher.checked ? '1' : '');
        }
    });
})();

setInterval(check_api_host, 1000);

http_get('/main')
    .then(res => {
        document.getElementById('main').innerHTML = res.main;
        window.domains = res.domains;
        rememberTabs('tab_group', '.site-groups');
        rememberTabs('tab_main', '.main-tabs');
        document.dispatchEvent(new CustomEvent('modules_update'));
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
