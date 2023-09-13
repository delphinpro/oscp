/*
 * OSPanel Web Dashboard
 * Copyright (c) 2023.
 * Licensed under MIT License
 */

/**
 * @typedef {object} Domain
 * @property {string} host
 * @property {string} aliases
 * @property {string} engine
 * @property {string} public_dir
 * @property {string} project_home_dir
 * @property {boolean} enabled
 * @property {string} cgi_dir
 * @property {string} ip
 * @property {string} log_format
 * @property {boolean} auto_configure
 * @property {boolean} ssl
 * @property {string} ssl_cert_file
 * @property {string} ssl_key_file
 * @property {string} project_add_modules
 * @property {string} project_add_commands
 * @property {boolean} project_use_sys_env
 */

function domain_action(action, host) {
    http_post(`/domain/${action}/`, { host })
        .then(res => {
            window.domains = res['domainsList'];
            document.getElementById('domains').innerHTML = res.domains;
            document.getElementById('sites').innerHTML = res['sites'];
            rememberTabs('tab_group', '.site-groups');
        });
}

function domain_delete(host) {
    if (confirm(`Настройки домена ${host} будут удалены.\nПодтвердить удаление?`)) {
        http_post(`/domain/delete/`, { host })
            .then(res => {
                window.domains = res['domainsList'];
                document.getElementById('domains').innerHTML = res.domains;
                document.getElementById('sites').innerHTML = res['sites'];
                rememberTabs('tab_group', '.site-groups');
            });
    }
}

/**
 * @param {HTMLFormElement} form
 */
function domain_update(form) {
    try {
        const fd = new FormData(form);
        http_post(`/domain/${form.elements.action.value}/`, fd)
            .then(res => {
                window.domains = res['domainsList'];
                document.getElementById('domains').innerHTML = res.domains;
                document.getElementById('sites').innerHTML = res['sites'];
                rememberTabs('tab_group', '.site-groups');
                const modal = bootstrap.Modal.getInstance(document.querySelector('#modal-domain'));
                modal.hide();
            });
    } catch { }
    return false;
}

/**
 * @param {HTMLElement} modal
 * @param {Domain} data
 */
function updateModalForm(modal, data) {
    modal.querySelector('input[name=action]').value = !!data ? 'update' : 'create';
    modal.querySelector('input[name=old_host]').value = data?.host ?? '';

    modal.querySelector('input[type=checkbox][name=enabled]').checked = data?.enabled ?? true;
    modal.querySelector('input[type=checkbox][name=ssl]').checked = data?.ssl ?? false;
    modal.querySelector('input[name=host]').value = data?.host ?? '';
    modal.querySelector('input[name=aliases]').value = data?.aliases ?? '';
    modal.querySelector('input[name=ip]').value = data?.ip ?? 'auto';
    modal.querySelector('select[name=engine]').value = data?.engine ?? 'PHP-8.1';
    modal.querySelector('input[name=public_dir]').value = data?.public_dir ?? '';
    modal.querySelector('input[name=project_home_dir]').value = data?.project_home_dir ?? '';
    modal.querySelector('input[name=admin_path]').value = data?.admin_path ?? '';
    modal.querySelector('input[name=project_add_modules]').value = data?.project_add_modules ?? '';
    modal.querySelector('input[name=project_add_commands]').value = data?.project_add_commands ?? '';
    modal.querySelector('input[type=checkbox][name=project_use_sys_env]').checked = data?.project_use_sys_env ?? false;
}

const domainModal = document.getElementById('modal-domain');
domainModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    const hostName = button.dataset.host;
    // console.log(hostName, window.domains[hostName], window.domains);
    updateModalForm(domainModal, window.domains[hostName] ?? null);
});
