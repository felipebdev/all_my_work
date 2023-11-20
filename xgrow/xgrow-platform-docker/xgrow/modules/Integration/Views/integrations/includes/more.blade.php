<div class="dropdown x-dropdown">
    <button class="xgrow-button table-action-button" type="button"
        id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu table-menu" aria-labelledby="dropdownMenuButton1">
        @if (isset($showActions) && boolval($showActions) === true)
            <li>
                <a id="action-caller"
                    class="dropdown-item table-menu-item"
                    href="{{ route('apps.integrations.actions.index', ['integration' => $app->id]) }}"
                    style="color: var(--table-head-font);">
                    Visualizar ações
                </a>
            </li>
        @endif
        <li>
            <a class="dropdown-item table-menu-item btn-modal btn-integration-edit"
                href="javascript:void(0)"
                data-href="modal-{{ strtolower($app->type) }}"
                data-provider="{{ strtolower($app->type) }}"
                data-url="{{
                    (isset($queueable) && boolval($queueable) === true)
                    ? route('apps.integrations.show', ['integration' => $app->id])
                    : route('apps.integrations.edit', ['integration' => $app->id])
                }}"
                style="color: var(--table-head-font);">
                Editar integração
            </a>
        </li>
        <li>
            <a class="dropdown-item table-menu-item"
                href="javascript:void(0)"
                data-bs-toggle="modal"
                data-bs-target="#modal-integration-delete"
                data-url="{{
                    (isset($queueable) && boolval($queueable) === true)
                    ? route('apps.integrations.destroy', ['integration' => $app->id])
                    : route('apps.integrations.destroy', ['integration' => $app->id])
                }}"
                style="color: var(--table-head-font);">
                Excluir integração
            </a>
        </li>
    </ul>
</div>
