<div class="tab-pane fade {{ Route::current()->getName() == 'forum.moderation' ? ' active show' : '' }}"
     id="nav-approved" role="tabpanel" aria-labelledby="nav-approved-tab">
    <div class="xgrow-card card-dark">
        <div class="xgrow-card-body">
            @if(count($posts) > 0)
                @if (Route::current()->getName() == 'forum.moderation')
                    <div class="table-responsive m-t-30">
                        <table id="content-table"
                               class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                               style="width:100%">
                            <thead>
                            <tr class="card-black" style="border: 4px solid var(--black-card-color)">
                                <th style="width: 5%">
                                    <div class="form-check">
                                        {!! Form::checkbox('checkAll', null, null, ['id' => 'checkAll', 'class' => 'form-check-input']) !!}
                                    </div>
                                </th>
                                <th style="width: 25%">Aluno</th>
                                <th style="width: 50%">Post</th>
                                <th style="width: 20%">Data e hora</th>
                                <th style="width: 100px">Tópico</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                @endif
            @else
                <div
                    style="background: var(--black-card-color) -50%;display: flex;justify-content: center;padding: 1rem;align-items: center;">
                    <div class="text-center">
                        <p>Não há posts para moderar</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
