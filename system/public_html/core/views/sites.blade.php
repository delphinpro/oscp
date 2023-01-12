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
                <td class="p-0">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-box-arrow-in-right fs-3 text-light me-2"></i>
                    <a href="http://{{ $domain->host }}" target="_blank">http://{{ $domain->host }}</a>
                  </div>
                </td>
                <td class="p-0">
                  @if ($domain->ssl)
                    <div class="d-flex align-items-center">
                      <i class="bi bi-box-arrow-in-right fs-3 text-light me-2"></i>
                      <a href="https://{{ $domain->host }}" target="_blank">https://{{ $domain->host }}</a>
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
