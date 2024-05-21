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

function arrayToFormData(formData, data, parentKey) {
    if (data &&
        typeof data === 'object' &&
        !(data instanceof Date) &&
        !(data instanceof File)
    ) {
        Object.keys(data).forEach(key => {
            arrayToFormData(formData, data[key], parentKey ? `${parentKey}[${key}]` : key);
        });
    } else {
        const value = data == null ? '' : data;
        formData.append(parentKey, value);
    }
}

export class ServerError {
    constructor(message) {
        this.name = 'ServerError';
        this.message = message;
    }
}

export default new class {
    #baseUrl;
    #cliApiUrl;

    configure(options = {}) {
        this.#baseUrl = options.baseUrl || this.#baseUrl;
        this.#cliApiUrl = options.cliApiUrl || this.#cliApiUrl;
    }

    request(url, method = 'GET', body = null) {

        url = this.#baseUrl + '/' + (url.startsWith('/') ? url.slice(1) : url);

        console.log(`REQUEST: ${method} ${url}`);

        return fetch(url, { method, body })
            .then(parseResponse)
            .then(res => {
                if (res.status === 200) {
                    return res['payload'];
                } else {
                    throw new ServerError(res['message'] ?? 'Server Error');
                }
            });
    }

    get(url, data = {}) {
        const queryString = this.buildQueryString(data ?? {});

        if (queryString) {
            url += '?' + queryString;
        }

        return this.request(url, 'GET');
    }

    post(url, data = {}) {
        let body = new FormData();

        arrayToFormData(body, data);

        return this.request(url, 'POST', body);
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

    buildQueryString(obj) {
        return Object.keys(obj)
            .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(obj[key])}`)
            .join('&');
    }
};
