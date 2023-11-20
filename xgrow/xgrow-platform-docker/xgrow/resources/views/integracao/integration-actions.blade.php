@php
    use App\Http\Controllers\IntegracaoActionController as action;
    $actions = action::index($webhook->id)["actions"];
@endphp

<div class="xgrow-card-header align-items-center">
    <p class="xgrow-card-title">Ações da integração:</p>
    <button class="xgrow-button" type="button" style="height: 40px;"
        data-bs-toggle="modal" data-bs-target="#exampleModal">+ Novo</button>
</div>

<!-- MODAL -->
<div class="modal-sections modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="modal-header">
                <p class="modal-title" id="exampleModalLabel">
                    Nova ação
                </p>
            </div>
            <div class="modal-body">
                @include('integracao._formAction')
            </div>
        </div>
    </div>
</div>
<!-- MODAL -->

<table id="integrations-table" class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer">
    <thead>
        <tr class="card-black" style="border: 2px solid var(--black1)">
            <th>Id da Ação</th>
            <th>Descrição</th>
            <th>Gatilho</th>
            <th>Ação</th>
            <th>Status</th>
            <th width="5%"></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($actions as $key => $val)
            <tr>
                <td>{{ $val->action_id }}</td>
                <td>{{ $val->description }}</td>
                <td>{{ $val->trigger }}</td>
                <td>{{ $val->action }}</td>
                <td>
                    {{-- <label for="status" class="mr-3">{{ $val->status === 'active' ? 'Ativo' : 'Não Ativo' }}</label> --}}
                    <div class="ckbx-style-8">
                        <form id="activeForm-{{$val->action_id}}" action="{{route('integracaoAction.updateStatus',['id'=>$val->action_id,'webhookId'=>$webhook->id])}}" method="post">
                            @csrf
                            @method('PUT')
                            <input onclick="event.preventDefault();document.getElementById('activeForm-{{$val->action_id}}').submit();" type="checkbox" id="status-{{$val->action_id}}" name="status" value="active" @if (isset($val->status))
                            {{ $val->status == 'active' ? 'checked' : ''}}
                            @else
                            checked
                            @endif
                            >
                            <label for="status-{{$val->action_id}}"></label>
                        </form>
                    </div>
                </td>
                <td>
                    <form id="delete-{{$val->action_id}}" action="{{route('integracaoAction.destroy',['id'=>$val->action_id,'webhookId'=>$webhook->id])}}" method="post">
                        @csrf
                        @method('DELETE')
                        <a href="#" onclick="event.preventDefault();document.querySelector('form#delete-{{$val->action_id}}').submit();">
                            <i class="fa fa-trash" style="color:red;"></i>
                        </a>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">Nenhuma ação foi cadastrada.</td>
            </tr>
        @endforelse
    </tbody>
</table>