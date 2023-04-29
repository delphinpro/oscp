@php /** @var \OpenServer\DTO\Module[] $modules */ @endphp

<div class="form-check form-switch">
  <input class="form-check-input" type="checkbox" role="switch" id="hide-off-modules">
  <label class="form-check-label" for="hide-off-modules">Скрывать отключённые</label>
</div>

<table class="table table-sm table-hover" id="table-modules">
  <thead>
    <tr>
      <th>Модуль</th>
      <th>Статус</th>
      <th>IP</th>
      <th>Версия</th>
      <th>Тип</th>
      <th>Совместимость</th>
      <th class="text-end">
        Действия
        <i class="bi bi-question-circle"
          data-bs-toggle="modal"
          data-bs-target="#m-help-modules"
          style="cursor:pointer"
        ></i>
      </th>
    </tr>
  </thead>
  <tbody>
    @foreach ($modules as $module)
      <tr class="{{ $module->enabled ? '' : 'state-off' }}">
        <td>
          <span class="d-flex align-items-center">
            <span class="bulb me-2
            {{ $module->enabled ? 'bg-success' : 'bg-danger' }}
            {{ $module->init ? 'bg-white' : '' }}"
            ></span>
            <span>{{ $module->name }}</span>
          </span>
        </td>
        <td>{{ $module->status }}</td>
        <td class="monospace">
          {{ $module->ip() }}{{ $module->port() ? ':'.$module->port() : '' }}
        </td>
        <td>{{ $module->version }}</td>
        <td>{{ $module->type }}</td>
        <td>{{ $module->compatible }}</td>
        <td class="text-end actions">
          <div class="btn-group btn-group-sm">
            <button class="btn btn-sm btn-light" onclick="module_action('init', '{{ $module->name }}')">
              <i class="bi bi-info-circle"></i>
            </button>
            <button class="btn btn-sm btn-light" onclick="module_action('restart', '{{ $module->name }}')">
              <i class="bi bi-arrow-repeat"></i>
            </button>
            @if($module->enabled)
              <button class="btn btn-sm btn-light" onclick="module_action('off', '{{ $module->name }}')">
                <i class="bi bi-power"></i>
              </button>
            @else
              <button class="btn btn-sm btn-light" onclick="module_action('on', '{{ $module->name }}')">
                <i class="bi bi-power"></i>
              </button>
            @endif
          </div>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>
