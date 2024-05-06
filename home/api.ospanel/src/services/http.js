/*
 * Web OSP by delphinpro
 * Copyright (c) 2024.
 * Licensed under MIT License
 */

function parseResponse(res) {
    return res.headers.get('Content-Type') === 'application/json'
           ? res.json()
           : res.text()
               .then(message => ({ status: 500, message }));
}

export default new class {
    #baseUrl;
    #cliApiUrl;

    configure(options = {}) {
        this.#baseUrl = options.baseUrl || this.#baseUrl;
        this.#cliApiUrl = options.cliApiUrl || this.#cliApiUrl;
    }

    makeUrl(url) {
        url = url.startsWith('/') ? url.slice(1) : url;
        return this.#baseUrl + '/api/' + url;
    }

    request(url, method = 'GET', data = null) {

        url = this.makeUrl(url);
        let body = data;

        if (method.toUpperCase() === 'POST') {
            if (!(data instanceof FormData) && data !== null) {
                body = new FormData();
                for (let key in data) {
                    body.append(key, data[key]);
                }
            }
        }

        return fetch(url, { method, body })
            .then(parseResponse)
            .then(res => {
                if (res.status === 200) {
                    return res['payload'];
                } else {
                    throw Error(res['message'] ?? 'Server Error');
                }
            });
    }

    get(url) {
        return this.request(url);
    }

    post(url, data = {}) {
        return this.request(url, 'POST', data);
    }

    apiCall(action) {
        if (action.startsWith('/')) {
            action = action.slice(1);
        }
        let url = this.#cliApiUrl + '/' + action;
        return fetch(url, { method: 'GET' })
            .then(res => res.text())
            .then(res => {
                const hasError = res.includes('ПРЕДУПРЕЖДЕНИЕ') || res.includes('ОШИБКА');
                res = res.trim()
                    .replaceAll('[93m', '')
                    .replaceAll('[91m', '')
                    .replaceAll('[0m', '')
                    .split('\n')
                    .map(s => s.trim())
                    .filter(s => !s || (!s.startsWith('ПРЕДУПРЕЖДЕНИЕ') && !s.startsWith('———')))
                    .join('<br>');

                if (hasError) {
                    throw Error(res);
                }

                return res;
            });

    }
};
