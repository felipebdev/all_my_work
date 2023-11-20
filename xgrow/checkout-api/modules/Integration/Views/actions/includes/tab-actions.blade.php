<div class="xgrow-card card-dark">
    <div class="xgrow-card-header align-items-center justify-content-between flex-wrap">
        <div>
            <h5><strong>{{ Str::ucfirst($integration->type) }}</strong></h5>
            <small>{{ $integration->description }}</small>
        </div>

        <button class="xgrow-button border-light new-integration btn-open-modal"
            data-provider="{{ $integration->type }}">
            <i class="fa fa-plus" aria-hidden="true"></i> Nova ação
        </button>
    </div>

    <div class="xgrow-card-body">
        <div class="table-responsive m-t-30">
            <table id="plan-table"
                class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                style="width:100%">
                <thead>
                    <tr class="card-black" style="border: 4px solid var(--black-card-color)">
                        <th>Ativo</th>
                        <th>Nome</th>
                        <th>Produtos</th>
                        <th>Evento</th>
                        <th>Ação</th>
                        <th class="no-export"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($actions as $action)
                        <tr>
                            @if (boolval($action->is_active) === true)
                                <td><i class="fa fa-check-circle" title="Ativo"></i></td>
                            @else
                                <td><i class="fa fa-times-circle" title="Inativo"></i></td>
                            @endif
                            <td>{{ $action->description }}</td>
                            <td>
                                @foreach ($action->plans as $plan)
                                    <a href="/plans/{{ $plan->id ?? 'javascript:void(0)' }}/edit" 
                                        style="color: inherit">
                                        {{ $plan->name ?? '-' }}@if (!$loop->last), @endif
                                    </a>
                                @endforeach
                            </td>
                            <td>{{ trans("apps::lang.integrations.events.{$action->event}") }}</td>
                            <td>{{ trans("apps::lang.integrations.actions.{$action->action}") }}</td>
                            <td>
                                <div class="d-flex align-items-center justify-content-end">
                                    <div class="dropdown x-dropdown">
                                        <button class="xgrow-button table-action-button m-1" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu table-menu" aria-labelledby="dropdownMenuButton1">
                                            <li>
                                                <a class="dropdown-item table-menu-item btn-action-edit" 
                                                    href="javascript:void(0)"
                                                    data-provider="{{ strtolower($integration->type) }}"
                                                    data-url="{{ route('apps.integrations.actions.show', ['integration' => $integration->id, 'action' => $action->id]) }}">
                                                    Editar ação
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item table-menu-item" 
                                                    href="javascript:void(0)"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modal-integration-delete"
                                                    data-url="{{ route('apps.integrations.actions.destroy', ['integration' => $integration->id, 'action' => $action->id]) }}">
                                                    Excluir ação
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="6">Nenhuma ação cadastrada!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>