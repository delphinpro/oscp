function http_request(url, method = 'GET', data = null) {
  let body = null;

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
      message(`<pre>${err.message.replace('<br />', '').trim()}</pre>`, 'danger');
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
