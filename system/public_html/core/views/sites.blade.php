@php /** @var \OpenServer\DTO\Domain[] $group */ @endphp

<div class="row sites">
  <div class="col-md-3 site-groups">
    <nav class="nav flex-column">
      @foreach ($sites as $groupName => $group)
        <a class="text-end nav-link" href="#"
          data-bs-toggle="tab"
          data-bs-target="#tab-site-{{ str_replace('.', '-', $groupName) }}"
        >{{ $groupName !== TLD ? '.' : '' }}{{ $groupName }}</a>
      @endforeach
    </nav>
  </div>
  <div class="col-md-9">
    <div class="tab-content">
      @foreach ($sites as $groupName => $group)
        <div class="domains tab-pane fade" id="tab-site-{{ str_replace('.', '-', $groupName) }}">
          <table class="table table-borderless">
            @foreach ($group as $domain)
              <tr>
                <td style="width: 33%;">
                  <div class="d-flex align-items-center text-nowrap">
                    @if($domain->isValidRoot() && $domain->isAvailable())
                      <a href="{{ $domain->siteUrl() }}" target="_blank">{{ $domain->siteUrl() }}</a>
                    @else
                      {{ $domain->siteUrl() }}
                    @endif
                  </div>
                </td>
                <td class="ps-4">
                  @if (!$domain->isAvailable())
                    <div class="d-flex align-items-center">
                      <i class="bi bi-exclamation-triangle-fill fs-5 text-danger me-2 align-middle"></i>
                      <span class="text-danger">Модуль {{ $domain->engine }} отсутствует или выключен</span>
                    </div>
                  @elseif(!$domain->isValidRoot())
                    <div class="d-flex align-items-center">
                      <i class="bi bi-exclamation-triangle-fill fs-5 text-danger me-2 align-middle"></i>
                      <span class="text-danger">Неверная папка домена</span>
                    </div>
                  @elseif($domain->admin_path)
                    <div class="d-flex">
                      <a href="{{ $domain->adminUrl() }}"
                        class="text-success text-decoration-none d-flex align-items-center"
                        target="_blank"
                      >
                        <i class="bi bi-box-arrow-in-right me-2 fs-5"></i>
                        <span>Вход в админку</span>
                      </a>
                    </div>
                  @endif
                </td>
              </tr>
            @endforeach
          </table>
        </div>
      @endforeach
    </div>
  </div>
</div>
