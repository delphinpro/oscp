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

    configure({ baseUrl }) {
        this.#baseUrl = baseUrl;
        //     console.log({ baseUrl: this.#baseUrl });
    }

    makeUrl(url) {
        url = url.startsWith('/') ? url.slice(1) : url;
        return this.#baseUrl + '/api/' + url;
    }

    request(url, method = 'GET', data = null) {

        let body = data;

        if (method.toUpperCase() === 'POST') {
            if (!(data instanceof FormData) && data !== null) {
                body = new FormData();
                for (let key in data) {
                    body.append(key, data[key]);
                }
            }
            // if (body instanceof FormData) {
            //   for (var pair of body.entries()) {
            //     console.log(pair[0] + ' = ' + pair[1]);
            //   }
            // }
        }

        // showLoader();

        return fetch(this.makeUrl(url), { method, body })
            .then(parseResponse)
            .then(res => {
                // hideLoader();
                if (res.status === 200) {
                    if (res.message) {
                        // message(res.message);
                    }
                    return res['payload'];
                } else {
                    throw Error(res['message'] ?? 'Server Error');
                }
            })
    }

    get(url) {
        return this.request(url);
    }

    post(url, data = {}) {
        return this.request(url, 'POST', data);
    }
};
