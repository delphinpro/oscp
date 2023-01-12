function http_get(url) {
  showLoader();
  return fetch(ENTRY_POINT + url)
    .then(res => res.json())
    .then(res => {
      hideLoader();
      if (res.status === 200) {
        return res['payload'];
      } else {
        throw Error(res['message'] ?? 'Server Error');
      }
    })
    .catch(err => {
      console.log('CATCH', err.message);
      hideLoader();
      throw Error(err);
    });
}

function http_post(url, data = {}) {
  showLoader();
  let fd = data;

  if (!(data instanceof FormData)) {
    fd = new FormData();
    for (let key in data) {
      fd.append(key, data[key]);
    }
  }

  for (var pair of fd.entries()) {
    // console.log(pair[0] + ' = ' + pair[1]);
  }

  return fetch(ENTRY_POINT + url, {
    method: 'POST',
    body  : fd,
  })
    .then(res => res.json())
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
      console.log('CATCH', err.message);
      hideLoader();
      throw Error(err);
    });
}

let hasError = false;

function check_api_host() {
  fetch(ENTRY_POINT + '/')
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
