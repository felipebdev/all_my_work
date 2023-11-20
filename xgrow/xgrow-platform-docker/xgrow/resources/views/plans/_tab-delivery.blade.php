@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
    <script>
        let plansList = $('#limitedList');

        $('#planDelivery').change(function (e) {
            e.target.value === 'unlimited' ? plansList.hide('slow') : plansList.show('slow');
        });

        $('#selectAll').click(function () {
            $('.selected-course').not(this).prop('checked', this.checked);
            $('.selected-section').not(this).prop('checked', this.checked);
        });

        function deleteRestriction(type, id) {
            axios.put('/plans/restrictions/delete', {type: type, id: id, _token: "{{ csrf_token() }}"})
                .then((res) => {
                    if (res.status === 200) {
                        successToast('Item removido.', res.data.message.toString());
                        $(`#span_${type}_${id}`).hide('slow');
                        $(`#${type}_${res.data.id}`).prop("checked", false);
                    }
                })
                .catch((error) => {
                    errorToast('Erro ao remover item', 'Ocorreu um erro ao remover esse item, por favor tente mais tarde.');
                });
        }

        function showDeleteRestrinctionModal(type, id) {
            const modalEl = $('#deleteRestrictionModal');
            $('#btnDeleteRestriction').attr('onClick', `deleteRestriction("${type}", ${id})`)
            modalEl.modal('show');
        }

        function showHidePlans(hasDelivery) {
            if (hasDelivery) {
                plansList.show('slow');
                $('#planDelivery').val('limited');
            } else {
                plansList.hide('slow');
                $('#planDelivery').val('unlimited');
            }
        }

        showHidePlans({{$hasDelivery}});
    </script>
@endpush

<div class="tab-pane fade show {{ Request::get('delivery') ? 'active' : '' }}" id="nav-delivery" role="tabpanel"
     aria-labelledby="nav-delivery-tab">
    @if ($plan->id > 0)
        {!! Form::model($plan, ['method' => 'put', 'enctype' => 'multipart/form-data', 'route' => ['plans.restrictions', 'id' => $plan->id], 'novalidate' => true]) !!}
    @endif

    {{ csrf_field() }}

    <div class="xgrow-card card-dark p-0 mt-4">
        <div class="xgrow-card-body p-3">
            <h5 class="xgrow-card-title my-3" style="font-size: 1.5rem; line-height: inherit">
                Entregas
            </h5>

            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::select('planDelivery', $planDelivery, null, ['class' => 'xgrow-select', 'id' => 'planDelivery']); !!}
                        {!! Form::label('planDelivery', 'Selecione o que deseja entregar') !!}
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <ul class="px-0 xgrow-medium-italic">
                        <li class="mb-2">
                            <span style="color: var(--contrast-green3)">Entrega ilimitada</span> - Todo o conteúdo atual
                            e futuro dentro da sua Área de Aprendizado será liberado
                            sem nenhum tipo de restrição.
                        </li>
                        <li class="my-2">
                            <span style="color: var(--contrast-green3)">Entrega selecionada</span> - Apenas o conteúdo
                            selecionado será liberado dentro da Área de Aprendizado.
                        </li>
                    </ul>
                </div>
            </div>

            <div class="row" id="limitedList">
                <div class="col-lg-12 col-md-12 col-sm-12 my-3">
                    <div class="xgrow-check d-flex align-items-center">
                        <input type="checkbox" name="select_all" id="selectAll" style="margin-right: 5px">
                        <label for="selectAll">Selecionar todos</label>
                    </div>
                </div>
                <!-- Lista de Cursos -->
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="row" id="listCourses">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <h6>Cursos</h6>
                            <hr>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <ul class="p-0">
                                @forelse($courses as $course)
                                    <li>
                                        <div class="xgrow-check mb-2">
                                            <input type="checkbox" name="course_ids[]" id="course_{{$course->id}}"
                                                   class="selected-course" value="{{$course->id}}"
                                                   @if (in_array($course->id, $coursePlanPluck)) checked @endif>
                                            <label for="course_{{$course->id}}">{{$course->name}}</label>
                                        </div>
                                    </li>
                                @empty
                                    <li><p>Não há cursos cadastrados ainda.</p></li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Lista de Seções -->
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="row" id="listSections">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <h6>Seções</h6>
                            <hr>
                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <ul class="p-0">
                                @forelse($sections as $section)
                                    <li>
                                        <div class="xgrow-check mb-2">
                                            <input type="checkbox" name="section_ids[]" id="section_{{$section->id}}"
                                                   @if (in_array($section->id, $sectionPlanPluck)) checked @endif
                                                   class="selected-section" value="{{$section->id}}">
                                            <label for="course_{{$section->id}}">{{$section->name}}</label>
                                        </div>
                                    </li>
                                @empty
                                    <li><p>Não há seções cadastradas ainda.</p></li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            <!-- Entregas já existentes -->
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    @if (count($coursePlan) == 0 &&count($sectionPlan) == 0 )
                        <p>Não há cursos ou seções restringidas para esse plano.</p>
                    @else
                        <h6 class="mt-5">Entregas existentes para esse plano</h6>
                        <hr>
                        <table id="delivery-table"
                               class="mt-3 xgrow-table table table-hover text-light table-responsive dataTable overflow-auto no-footer"
                               style="width:100%">
                            <thead>
                            <tr class="card-black" style="border: 4px solid var(--black-card-color)">
                                <th class="w-50">Nome</th>
                                <th class="w-25">Tipo</th>
                                <th class="w-25"></th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($coursePlan as $course)
                                <tr id="span_course_{{$course->id}}">
                                    <td>{{$course->course->name}}</td>
                                    <td>Curso</td>
                                    <td style="text-align: end; padding-right: 2rem">
                                        <a href="javascript:void(0)" style="color: inherit"
                                           onclick="showDeleteRestrinctionModal('course',{{$course->id}})">
                                            Remover item
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            @foreach($sectionPlan as $section)
                                <tr id="span_section_{{$section->id}}">
                                    <td>{{$section->section->name}}</td>
                                    <td>Seção</td>
                                    <td style="text-align: end; padding-right: 2rem">
                                        <a href="javascript:void(0)" style="color: inherit"
                                           onclick="showDeleteRestrinctionModal('section',{{$section->id}})">
                                            Remover item
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
            </div>


            <div class="xgrow-card-footer p-3 border-top mt-4">
                <input class="xgrow-button" id="btnSaveDelivery" type="submit" value="Salvar alterações">
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>

{{-- Modal --}}
<div class="modal-sections modal fade" id="deleteRestrictionModal" tabindex="-1"
     aria-labelledby="deleteRestrictionModal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="modal-header">
                <p class="modal-title" id="deleteCourseModal">Excluir item da restrição</p>
            </div>
            <div class="modal-body">
                Você tem certeza que deseja excluir este item?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal" id="btnDeleteRestriction"
                        aria-label="Close">
                    Sim, excluir
                </button>
                <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal" aria-label="Close">
                    Não, manter
                </button>
            </div>
        </div>
    </div>
</div>
