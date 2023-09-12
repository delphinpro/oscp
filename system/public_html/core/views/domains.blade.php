@php /** @var \OpenServer\DTO\Domain[] $group */ @endphp

<div class="mb-3">
  <button class="btn btn-success"
    type="button"
    data-bs-toggle="modal"
    data-bs-target="#modal-domain"
  >Добавить домен
  </button>
</div>
<div class="alert alert-warning">
  Для применения изменений необходимо перезапустить соответствующий модуль
</div>
<table class="table table-sm table-hover">
  <thead>
    <tr>
      <th class="text-end">Домен</th>
      <th class="text-center">Статус</th>
      <th>Модуль</th>
      <th>IP</th>
      <th>DOCUMENT_ROOT</th>
      <th class="text-center">SSL</th>
      <th class="text-end">Действия</th>
    </tr>
  </thead>
  @foreach ($domains as $groupName => $group)
    <tbody>
      @if ($groupName !== TLD)
        <tr class="text-bg-light">
          <th class="font-monospace text-end">.{{ $groupName }}</th>
          <th class="font-monospace" colspan="6"></th>
        </tr>
      @endif
      @foreach ($group as $domain)
        <tr>
          <td class="text-end">
            <span class="font-monospace text-primary">{{ $domain->host }}</span>
          </td>
          <td class="text-center">
            <span class="bulb ms-2 {{ $domain->enabled ? 'bg-success' : 'bg-danger' }}"></span>
          </td>
          <td>{{ $domain->engine }}</td>
          <td class="font-monospace">
            @if($domain->ip !== 'auto')
              {{ $domain->ip }}
            @else
              {{ $domain->realIp() }}
            @endif
          </td>
          <td class="font-monospace {{ !$domain->isValidRoot() ? 'text-danger' : 'text-muted' }}">
            <div style="max-width:100%;word-break:break-all;font-size:1rem;">
              {{ $domain->public_dir }}
              @if (!$domain->isValidRoot())
                <i class="bi bi-exclamation-triangle-fill text-danger" title="Путь не существует"></i>
              @endif
            </div>
          </td>
          <td class="text-center">
            <i class="bi {{ $domain->ssl ? 'text-success bi-check-lg' : 'text-danger bi-x-lg' }}"></i>
          </td>
          <td class="text-end actions">
            @if ($_SERVER['HTTP_HOST'] !== $domain->host)
              <div class="btn-group btn-group-sm">
                <button class="btn btn-light"
                  name="action"
                  value="{{ $domain->enabled ? 'off' : 'on' }}"
                  title="{{ $domain->enabled ? 'Выключить' : 'Включить' }}"
                  onclick="domain_action('{{ $domain->enabled ? 'off' : 'on' }}', '{{ $domain->host }}')"
                ><i class="bi bi-power"></i></button>
                <button class="btn btn-light"
                  data-bs-toggle="modal"
                  data-bs-target="#modal-domain"
                  data-host="{{ $domain->host }}"
                  title="Изменить настройки домена"
                ><i class="bi bi-pencil-square"></i></button>
                <button class="btn btn-danger"
                  name="action"
                  value="delete"
                  title="Удалить домен"
                  data-host="{{ $domain->host }}"
                  onclick="domain_delete('{{ $domain->host }}')"
                ><i class="bi bi-trash3"></i></button>
              </div>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  @endforeach
</table>
