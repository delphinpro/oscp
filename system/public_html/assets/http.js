function http_request(url, method = 'GET', data = null) {
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

  showLoader();

  return fetch(url, {
    method,
    body,
  })
    .then(res => res.headers.get('Content-Type') === 'application/json'
      ? res.json()
      : res.text().then(message => ({
        status: 500,
        message,
      })))
    .then(res => {
      hideLoader();
      if (res.status === 200) {
        if (res.message) {
          message(res.message);
        }
        return res['payload'];
      } else {
        throw Error(res['message'] ?? 'Server Error');
      }
    })
    .catch(err => {
      hideLoader();
      let msg = err.message.replace('<br />', '').trim();
      if (msg.indexOf('Stack trace') !== -1) {
        let parts = msg.split('Stack trace:', 2);
        msg = `<pre style="white-space:pre-wrap;">${parts[0]}</pre>
        <pre class="m-0">Stack trace:\n${parts[1].trim()}</pre>`;
      } else {
        msg = `<pre style="white-space:pre-wrap;" class="m-0">${msg}</pre>`;
      }
      message(msg, 'danger');
      throw Error(err);
    });
}

function http_get(url) {
  return http_request(ENTRY_POINT + '/api' + url);
}

function http_post(url, data = {}) {
  return http_request(ENTRY_POINT + '/api' + url, 'POST', data);
}

let hasError = false;

function check_api_host() {
  fetch(ENTRY_POINT + '/ping')
    .then(res => res.json())
    .then(() => {
      if (hasError) {
        document.getElementById('error').classList.remove('show');
        location.reload();
      }
    })
    .catch(err => {
      console.log('CHECK API HOST:', err.message);
      document.getElementById('error').classList.add('show');
      hasError = true;
    });
}
