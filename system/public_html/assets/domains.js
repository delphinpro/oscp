/**
 * @typedef {object} Domain
 * @property {string} host
 * @property {string} aliases
 * @property {string} engine
 * @property {string} root_directory
 * @property {boolean} enabled
 * @property {string} cgi_directory
 * @property {string} ip
 * @property {string} log_directory
 * @property {string} log_format
 * @property {boolean} self_config
 * @property {boolean} ssl
 * @property {string} ssl_cert_file
 * @property {string} ssl_key_file
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
  modal.querySelector('input[name=root_directory]').value = data?.root_directory ?? '';
}

const domainModal = document.getElementById('modal-domain');
domainModal.addEventListener('show.bs.modal', event => {
  const button = event.relatedTarget;
  const hostName = button.dataset.host;
  // console.log(hostName, window.domains[hostName], window.domains);
  updateModalForm(domainModal, window.domains[hostName] ?? null);
});
