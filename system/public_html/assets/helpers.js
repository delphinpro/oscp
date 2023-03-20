/*
 * OSPanel Web Dashboard
 * Copyright (c) 2023.
 * Licensed under MIT License
 */

function showLoader() {
    document.getElementById('loader').hidden = false;
}

function hideLoader() {
    document.getElementById('loader').hidden = true;
}

function rememberTabs(lsKey, containerClass) {
    let trigger = document.querySelector(`${containerClass} [data-bs-target="${localStorage.getItem(lsKey)}"]`);
    if (!trigger) trigger = document.querySelector(`${containerClass} [data-bs-target]`);

    new bootstrap.Tab(trigger);
    bootstrap.Tab.getInstance(trigger)?.show();

    [...document.querySelectorAll(`${containerClass} [data-bs-toggle="tab"]`)].forEach(tab => {
        tab.addEventListener('shown.bs.tab', event => {
            localStorage.setItem(lsKey, event.target.dataset.bsTarget);
        });
    });
}

let messageTimeout;

function message(message, type = 'primary') {
    message = message
        .trim()
        .replaceAll('\n', '<br>')
        .replaceAll('\r', '')
        .replaceAll('', '')
        .replace('[93m', '')
        .replace('[0m', '');
    const el = document.getElementById('flash');
    el.innerHTML =
        `<div class="container"><div class="alert alert-${type} alert-dismissible fade show my-0 mx-3">
            ${message}
            <button class="btn-close" data-bs-dismiss="alert" type="button"></button>
         </div></div>`;
    clearTimeout(messageTimeout);
    messageTimeout = setTimeout(() => el.innerHTML = '', 5000);
}
