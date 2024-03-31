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
            .catch(err => {
                // hideLoader();
                let msg = err.message.replace('<br />', '').trim();
                if (msg.indexOf('Stack trace') !== -1) {
                    let parts = msg.split('Stack trace:', 2);
                    msg = `<pre style="white-space:pre-wrap;">${parts[0]}</pre>
                           <pre class="m-0">Stack trace:\n${parts[1].trim()}</pre>`;
                } else {
                    msg = `<pre style="white-space:pre-wrap;" class="m-0">${msg}</pre>`;
                }
                console.warn(msg);
                // message(msg, 'danger');
                throw Error(err);
            });
    }

    get(url) {
        return this.request(url);
    }

    post(url, data = {}) {
        return this.request(url, 'POST', data);
    }
};
