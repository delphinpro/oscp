const ENTRY_POINT = 'http://api.ospanel';

setInterval(check_api_host, 1000);

http_get('/main')
  .then(res => {
    document.getElementById('main').innerHTML = res.main;
    window.domains = res.domains;
    rememberTabs('tab_group', '.site-groups');
    rememberTabs('tab_main', '.main-tabs');
  });
