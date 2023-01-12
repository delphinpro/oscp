<div class="nav nav-tabs main-tabs">
  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#sites" type="button">Сайты</button>
  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#modules" type="button">Модули</button>
  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#domains" type="button">Домены</button>
</div>
<div class="tab-content">
  <div class="tab-pane fade p-3 border border-1 border-top-0" id="sites">
    @include('sites')
  </div>
  <div class="tab-pane fade p-3 border border-1 border-top-0" id="modules">
    @include('modules')
  </div>
  <div class="tab-pane fade p-3 border border-1 border-top-0" id="domains">
    @include('domains')
  </div>
</div>
