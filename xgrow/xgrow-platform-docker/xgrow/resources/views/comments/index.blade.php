@extends('templates.xgrow.main')

@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/comments.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css"/>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.bootstrap4.min.css"/>

    <style>
        .xgrow-user-avatar {
            object-fit: cover;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            box-shadow: 0 0 2px 2px #FFFFFF
        }

        .xgrow-user-profile > .info {
            display: flex;
            margin-bottom: 1rem;
        }

        .xgrow-user-profile > .info > div {
            display: flex;
            flex-direction: column;
            padding-left: 1rem;
        }

        .xgrow-user-profile:nth-last-child(1) {
            margin-bottom: 0 !important;
        }

    </style>
@endpush

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js">
    </script>

    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>

    <script src=" https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script>
        let approved = {{ $approved }};
        let customTable;

        $(function () {
            customTable = $('#content-table').DataTable({
                dom: '<"d-flex align-items-center justify-content-between flex-wrap"<"create-title"><"d-flex align-items-center"<"create-buttons">><"filter-button">>' +
                    '<"filter-div mt-2">' +
                    '<"mt-2" rt>' +
                    '<"my-3 d-flex align-items-center justify-content-between flex-wrap"<"my-2"l><"my-2"p>>',
                columnDefs: [{
                    'targets': [5, 6, 7],
                    'visible': false
                }],
                lengthMenu: [
                    [10, 25, 50, -1],
                    ['10 itens por página', '25 itens por página', '50 itens por página',
                        'Todos os registros'
                    ]
                ],
                responsive: false,
                processing: true,
                serverSide: false,
                ajax: {
                    url: '/api/get-all-comments',
                    data: {
                        'status': {{ $approved }}
                    },
                },
                columns: [{
                    name: null,
                    render: function (data, type, row) {
                        return `<div class="form-check">
                                                                                        <input type="checkbox" id="${row.id}"  value="${row.id}" name="comments[]" class="comment form-check-input">
                                                                                    </div>`;
                    }
                },
                    {
                        data: 'name',
                        name: 'name',
                        className: 'details-control',
                        render: function (data, type, row) {
                            const avatar =
                                `<img src="${row.avatar ?? '/xgrow-vendor/assets/img/profile_default.jpg'}" alt="${row.name}" class="xgrow-user-avatar">`;
                            return `<div class="xgrow-user-profile">
                                                                                        <div class="info">
                                                                                            ${avatar}
                                                                                            <div>
                                                                                                <span>${row.name}</span>
                                                                                                <small>${row.email}</small>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>`;
                        }
                    },
                    {
                        data: 'text',
                        name: 'text',
                        className: 'details-control',
                        render: function (data, type, row) {
                            let resume = data.split(' ').splice(0, 28).join(' ');
                            return data.length > resume.length ? resume + '...' : resume;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        className: 'details-control',
                        render: function (data, type, row) {
                            return `${moment(data).format('DD/MM/YYYY')} às ${moment(data).format('HH[h]mm')}`;
                        }
                    },
                    {
                        data: 'image',
                        className: 'details-control',
                        render: function (data, type, row, rowID) {
                            const img =
                                `<div style="margin-right: 1rem"><img src="${data}" alt="Conteúdo" style="width:96px;height:96px;object-fit:cover;"/><small>${row.title}</small></div>`;
                            return `<div class="d-flex align-items-end">${img}</div>`;
                        }
                    },
                    {
                        data: 'section',
                        name: 'section'
                    },
                    {
                        data: 'author',
                        name: 'author'
                    },
                    {
                        data: 'course',
                        name: 'course'
                    },
                ],
                language: {
                    'url': "{{ asset('js/datatable-translate-pt-BR.json') }}"
                },
                initComplete: function (settings, json) {
                    $('.buttons-csv').removeClass('dt-button buttons-csv');
                    $('.buttons-excel').removeClass('dt-button buttons-excel');
                    $('.buttons-pdf').removeClass('dt-button buttons-pdf');
                    $('.create-buttons').html('' +
                        '<button type="button" class="remove-btn xgrow-upload-btn-lg btn mx-1 my-2" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="fa fa-trash" aria-hidden="true"></i><span>Excluir</span></button>' +
                        '<button type="button" class="approved-btn xgrow-upload-btn-lg btn mx-1 my-2" data-bs-toggle="modal" data-bs-target="#customModal"><i class="fa fa-undo" aria-hidden="true"></i><span>{{ $approved ? 'Reprovar' : 'Aprovar' }}</span></button>' +
                        '');
                    $('.create-label').html('<p class="xgrow-medium-bold me-2">Exportar em</p>');
                    $('.create-title').html(
                        '<p class="xgrow-card-title mb-3">{{ $approved == 1 ? 'Comentários publicados no canal' : 'Comentários pendentes de aprovação' }}</p>'
                    );
                    $('.dataTables_filter input').attr('placeholder', 'Pesquisar');
                    $('.filter-button').html(`
                                                                                  <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-bs-expanded="false" aria-bs-controls="collapseExample" class="xgrow-button export-button me-1" aria-expanded="true">
                                                                                    <i class="fa fa-list" aria-hidden="true"></i>
                                                                                  </button>
                                                                            `);
                    $('.filter-div').html(`
                                                                                <div class="mb-3 collapse" id="collapseExample">
                                                                                <div class="filter-container">
                                                                                    <div class="p-2 px-3">
                                                                                        <div class="row">
                                                                                            <div class="col-sm-12 col-md-12">
                                                                                                <div class="d-flex row">
                                                                                                    <div class="xgrow-form-control mui-textfield mui-textfield--float-label mb-2 col-sm-12 col-md-4">
                                                                                                        <select class="xgrow-select" name="options" id="authorSelect">
                                                                                                            <option value="option1" disabled="" selected="" hidden="">Autor</option>
                                                                                                        </select>
                                                                                                        <label for="authorSelect">Filtrar por autor:</label>
                                                                                                    </div>
                                                                                                    <div class="xgrow-form-control mui-textfield mui-textfield--float-label mb-2 col-sm-12 col-md-4">
                                                                                                        <select class="xgrow-select" name="options" id="courseSelect">
                                                                                                            <option value="option1" disabled="" selected="" hidden="">Curso</option>
                                                                                                        </select>
                                                                                                        <label for="courseSelect">Filtrar por curso:</label>
                                                                                                    </div>
                                                                                                    <div class="xgrow-form-control mui-textfield mui-textfield--float-label mb-2 col-sm-12 col-md-4">
                                                                                                        <select class="xgrow-select w-100" name="options" id="sectionSelect">
                                                                                                            <option value="option1" disabled="" selected="" hidden="">Seção</option>
                                                                                                        </select>
                                                                                                        <label for="sectionSelect">Filtrar por seção:</label>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            `);

                    //Cria os options para o conteúdo
                    // AUTHOR COLUMN
                    const authorOptions = this.api().column(6);
                    $('#authorSelect').append(`<option value="">Todos</option>`);
                    authorOptions.data().unique().sort().each((value) => {
                        $('#authorSelect').append(`<option value="${value}">${value}</option>`);
                    });

                    $('#authorSelect').on('change', function () {
                        customTable.columns(6).search(this.value).draw();
                    });

                    // COURSE COLUMN
                    const courseOptions = this.api().column(7);
                    $('#courseSelect').append(`<option value="">Todos</option>`);
                    courseOptions.data().unique().sort().each((value) => {
                        value !== null ?
                            $('#courseSelect').append(
                                `<option value="${value}">${value}</option>`) :
                            $('#courseSelect').append(
                                `<option value="${value}" disabled>Não há cursos para esse filtro</option>`
                            );
                    });

                    $('#courseSelect').on('change', function () {
                        if (this.value !== 'null') {
                            customTable.columns(7).search(this.value).draw();
                        }
                    });

                    // SECTION COLUMN
                    const sectionOptions = this.api().column(5);
                    $('#sectionSelect').append(`<option value="">Todos</option>`);
                    sectionOptions.data().unique().sort().each((value) => {
                        $('#sectionSelect').append(
                            `<option value="${value}">${value}</option>`);
                    });

                    $('#sectionSelect').on('change', function () {
                        customTable.columns(5).search(this.value).draw();
                    });

                    $('#content-table tbody').on('click', 'td.details-control', function () {
                        let tr = $(this).closest('tr');
                        let row = customTable.row(tr);

                        if (row.child.isShown()) {
                            row.child.hide();
                            tr.removeClass('shown');
                        } else {
                            row.child(rowBelow(row.data())).show();
                            tr.addClass('shown');
                        }
                    });

                    function rowBelow(row) {
                        const comment_id = row.id;
                        let listComment = '';

                        axios.get('/api/replies-by-comment', {
                            params: {
                                comment_id
                            }
                        })
                            .then(res => {
                                let comments = res.data.comments;

                                listComment += `
                                    <div class="xgrow-user-profile" style="background: var(--black-card-color);padding: 1rem 1.5rem;margin-bottom: .5rem;">
                                       <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                           <textarea id="reply-${comment_id}" spellcheck="false" class="mui--is-empty mui--is-untouched mui--is-pristine" rows="3" style="height: auto"></textarea>
                                           <label>Adicionar comentário público</label>
                                           <span onclick="document.getElementById('reply-${comment_id}').value = ''"></span>
                                       </div>
                                       <div class="d-flex justify-content-between">
                                           <button type="button" style="font-size:.9rem;margin-right:2rem;color:var(--tab-active);background:transparent;border:none;" data-bs-toggle="collapse" data-bs-target="#replies-${comment_id}">ESCONDER/EXIBIR TODAS AS RESPOSTAS</button>
                                           <div>
                                               <button type="button" style="font-size:.9rem;margin-right:2rem;color:var(--tab-active);background: transparent;border: none;" onclick="document.getElementById('reply-${comment_id}').value = ''">CANELAR</button>
                                               <button type="button"  style="font-size:.9rem;margin-right:2rem;color:var(--tab-active);background: transparent;border: none;" onclick="sendReply(${comment_id}, 'reply-${comment_id}')">ENVIAR</button>
                                           </div>
                                       </div>
                                    </div>
                                `;

                                listComment += `<div id="replies-${comment_id}" class="collapse show">`;
                                if (res.data.comments.length > 0) {
                                    comments.forEach(comment => {
                                        const subscriber = comment.subscriber_type ===
                                        'subscriber' ? comment.subscriber : {
                                            name: 'Você'
                                        };
                                        listComment += `
                                            <div class="xgrow-user-profile" style="background: var(--black-card-color);padding:1rem 1.5rem 1rem 6rem;margin-bottom: .5rem;">
                                               <div class="info">
                                                   <img src="/xgrow-vendor/assets/img/profile_default.jpg" class="xgrow-user-avatar">
                                                   <div>
                                                       <span>${subscriber.name}</span>
                                                       <small>${moment(comment.created_at).format('DD/MM/YYYY')}</small>
                                                   </div>
                                               </div>
                                               <div class="text">
                                                   ${comment.text}
                                                   <div>
                                                   <button type="button" style="font-size:.9rem;margin-right:2rem;color:var(--tab-active);background:transparent;border:none;" onclick="hiddenComment(${comment.id})"><i class="fa fa-eye"></i> <span id="btnReply-${comment.id}">${comment.approved ? 'Esconder resposta' : 'Exibir resposta'}</span></button>
                                                   <button type="button" style="font-size:.9rem;margin-right:2rem;color:var(--tab-active);background:transparent;border:none;" onclick="showDeleteModal(${comment.id})" id="btnDelete-${comment.id}"><i class="fa fa-close"></i> Excluir</button>
                                                   </div>
                                               </div>
                                            </div>
                                        `;
                                    });
                                } else {
                                    listComment += `
                                        <div style="background: var(--black-card-color) -50%;display: flex;justify-content: center;padding: 1rem;align-items: center;">
                                            <div class="text-center">
                                                <p>Sem resposta para este comentário.</p>
                                            </div>
                                        </div>
                                    `;
                                }
                                listComment += `</div>`;

                                const el = document.getElementById(`divComment-${comment_id}`);
                                const td = $('#divComment-' + comment_id).closest('td');
                                td.css('padding', 0);

                                el.innerHTML = listComment;
                            })
                            .catch(error => console.log(error));

                        return `<div id="divComment-${comment_id}">${listComment}</div>`;
                    }
                }
            });
        });

        // Move comments to approved ou pending
        function changeStatusSelected() {
            const comment_checked = $('.comment:checked');
            if (comment_checked.length > 0) {
                let arrayIDComments = [];
                comment_checked.each(function () {
                    arrayIDComments.push($(this).val());
                });

                $.ajax({
                    url: "{{ route('comments.change_status_selected') }}",
                    method: 'POST',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        comments: arrayIDComments,
                        status: {{ $approved }}
                    },
                    success: function (response) {
                        const status = ({{ $approved }}) ? 'pendentes.' : 'aprovados.';
                        successToast('Comentários movidos com sucesso!',
                            `Agora eles se encontram em itens ${status}`);
                        customTable.ajax.reload();

                    },
                    error: function (response) {
                        console.log(response);
                        errorToast('Algum erro foi encontrado!', 'Não foi possível mover os itens.');
                        customTable.ajax.reload();
                    }
                });
            } else {
                alert('Selecione ao menos um comentário');
            }
        }

        // Check All Checkboxes
        $('#checkAll').click(function () {
            $('.comment').not(this).prop('checked', this.checked);
        });

        // Approve or not the comments
        $('#approve_comments').change(function () {
            const approve_comments = ($(this).prop('checked') == true) ? 1 : 0;
            setApproveComments(approve_comments);
        });

        // Approve selected comments
        function setApproveComments(approve_comments) {
            $.ajax({
                url: "{{ route('comments.set_approve_comments') }}",
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    approve_comments
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'error') {
                        errorToast('Erro ao marcar aprovar automaticamente!',
                            response.message);
                    } else if (response.status === '1') {
                        successToast('Aprovar automaticamente ativo!',
                            `A partir de agora os comentários serão aceitos automaticamente.`);
                    } else {
                        errorToast('Aprovar automaticamente desativado.',
                            `A partir de agora os comentários não serão aceitos automaticamente.`);
                    }
                },
                error: function (response) {
                    console.log(response);
                }
            });
        }

        // Remove comments selected
        function deleteSelected() {
            const comment_checked = $('.comment:checked');
            if (comment_checked.length > 0) {

                let arrayIDComments = [];

                comment_checked.each(function () {
                    arrayIDComments.push($(this).val());
                });

                $.ajax({
                    url: "{{ route('comments.delete_selected') }}",
                    method: 'POST',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        comments: arrayIDComments,
                        status: {{ $approved }}
                    },
                    success: function (response) {
                        const status = ({{ $approved }}) ? 'pendentes.' : 'aprovados.';
                        successToast('Comentários excluídos!', `A exclusão foi feita com sucesso.`);
                        customTable.ajax.reload();

                    },
                    error: function (response) {
                        console.log(response);
                        errorToast('Algum erro aconteceu!', 'Não foi possível excluir os itens.');
                        customTable.ajax.reload();
                    }
                });
            } else {
                alert('Selecione ao menos um comentário');
            }
        }

        // Send Reply
        function sendReply(id, reply) {
            const replyComment = document.getElementById(reply);
            if (replyComment.value.trim() === '') {
                errorToast('Erro ao enviar resposta!', 'Você precisa adicionar uma resposta.');
                return false;
            }

            axios.post('/api/reply-comment', {
                text: replyComment.value,
                comment_id: id
            })
                .then(res => {
                    successToast('Comentário respondido!', 'Resposta enviada com sucesso.');
                    replyComment.value = '';
                    customTable.ajax.reload();
                }).catch(error => {
                errorToast('Algum erro aconteceu!', 'Não foi possível responder o comentário.');
            });
        }

        // Function Hidden/Show Comment
        function showHideComment(id) {
            axios.get('/api/hidden-comment', {
                params: {
                    comment_id: id
                }
            })
                .then(res => {
                    const btnReply = document.getElementById('btnReply-' + id);
                    btnReply.innerText = res.data.approved ? 'Esconder resposta' : 'Exibir resposta';
                    res.data.approved ?
                        successToast('Comentário ativado!', 'Item ativado com sucesso.') :
                        successToast('Comentário desativado!', 'Item desativado com sucesso.');
                }).catch(error => {
                errorToast('Algum erro aconteceu!', 'Não foi possível esconder o comentário.');
            });
        }

        // Call Hidden/Show comment modal
        function hiddenComment(id) {
            const msg = document.getElementById("btnReply-" + id).textContent === 'Exibir resposta' ? 'Exibir' : 'Ocultar';
            const text = document.querySelectorAll('.commentShowHideStatus');
            text.forEach(txt => txt.textContent = msg);
            document.getElementById("showHideComment").setAttribute("onClick", `showHideComment(${id})`);
            let commentShowHideModal = new bootstrap.Modal(document.getElementById('commentShowHideModal'))
            commentShowHideModal.show();
        }

        // Show Modal Delete
        function showDeleteModal(id) {
            document.getElementById("deleteCommentModalButton").setAttribute("onClick", `deleteComment(${id})`);
            let deleteOnceModal = new bootstrap.Modal(document.getElementById('deleteOnceModal'))
            deleteOnceModal.show();
        }

        // Delete Comment
        function deleteComment(id) {
            axios.get('/api/delete-comment', {
                params: {
                    comment_id: id
                }
            })
                .then(res => {
                    if (res.data.status === 'success') {
                        const replyDiv = $('#btnDelete-' + id).closest('.xgrow-user-profile');
                        successToast('Comentário removido!', 'Item removido com sucesso.');
                        replyDiv.hide('slow');
                    }
                }).catch(error => {
                errorToast('Algum erro aconteceu!', 'Não foi possível remover o comentário.');
            });
        }

    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><span>Comentários</span></li>
            <li class="breadcrumb-item active">
                <span>{{ $approved == 1 ? 'Aprovados' : 'Não aprovados' }}</span>
            </li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-sm-12">
            <div class="xgrow-card card-dark my-2 p-0" style="background: transparent;box-shadow: none;">
                <div class="xgrow-card-body p-0 m-0">
                    <div class="form-check form-switch">
                        {!! Form::checkbox('approve_comments', null, $approve_comments, ['id' => 'approve_comments', 'class' => 'form-check-input']) !!}
                        {!! Form::label('approve_comments', 'Aprovar comentários automaticamente', ['class' => 'form-check-label']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <nav class="xgrow-tabs-wrapper">
        <div class="xgrow-tabs nav nav-tabs" id="nav-tab" role="tablist">
            <a class="xgrow-tab-item nav-item nav-link {{ Route::current()->getName() == 'comments.index' ? ' active' : '' }}"
               id="nav-approved-tab" href="{{ Route('comments.index') }}" role="tab" aria-controls="nav-approved"
               aria-selected="true">Aprovados</a>

            <a class="xgrow-tab-item nav-item nav-link {{ Route::current()->getName() == 'comments.pedding' ? ' active' : '' }}"
               id="nav-pending-tab" href="{{ Route('comments.pedding') }}" role="tab" aria-controls="nav-pending"
               aria-selected="false">Validar</a>
        </div>
    </nav>

    <div class="tab-content py-3" id="nav-tabContent">
        <div class="tab-pane fade {{ Route::current()->getName() == 'comments.index' ? ' active show' : '' }}"
             id="nav-approved" role="tabpanel" aria-labelledby="nav-approved-tab">
            <div class="xgrow-card card-dark">
                <div class="xgrow-card-body">
                    @if (Route::current()->getName() == 'comments.index')
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
                                    <th style="width: 25%">Nome e E-mail</th>
                                    <th style="width: 50%">Comentário</th>
                                    <th style="width: 20%">Data e hora</th>
                                    <th style="width: 100px">Conteúdo</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="tab-pane fade {{ Route::current()->getName() == 'comments.pedding' ? ' active show' : '' }}"
             id="nav-pending" role="tabpanel" aria-labelledby="nav-pending-tab">
            <div class="xgrow-card card-dark">
                <div class="xgrow-card-body">
                    @if (Route::current()->getName() == 'comments.pedding')
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
                                    <th style="width: 25%">Nome e E-mail</th>
                                    <th style="width: 50%">Comentário</th>
                                    <th style="width: 20%">Data e hora</th>
                                    <th style="width: 100px">Conteúdo</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Modal MOVE --}}
    <div class="modal-sections modal fade" id="customModal" tabindex="-1" aria-labelledby="customModal"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-header">
                    <p class="modal-title" id="customModal">Mover
                        para {{ $approved == 0 ? 'aprovados' : 'reprovados' }}</p>
                </div>
                <div class="modal-body">
                    Você tem certeza que deseja mover os comentários selecionados
                    <br>
                    para itens {{ $approved == 0 ? 'aprovados' : 'reprovados' }}?
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal"
                            onclick="changeStatusSelected()"
                            aria-label="Close">
                        Sim, mover
                    </button>
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal" aria-label="Close">
                        Não, manter
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal DELETE SELECTED --}}
    <div class="modal-sections modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModal"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-header">
                    <p class="modal-title" id="deleteModal">Excluir comentários</p>
                </div>
                <div class="modal-body">
                    Você tem certeza que deseja excluir os comentários selecionados?
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="deleteSelected()"
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

    {{-- Modal DELETE ONCE --}}
    <div class="modal-sections modal fade" id="deleteOnceModal" tabindex="-1" aria-labelledby="deleteOnceModal"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-header">
                    <p class="modal-title" id="deleteOnceModal">Excluir comentário</p>
                </div>
                <div class="modal-body">
                    Você tem certeza que deseja excluir este comentário?
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal" id="deleteCommentModalButton"
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

    {{-- Modal SHOW/HIDE ONCE COMMENT --}}
    <div class="modal-sections modal fade" id="commentShowHideModal" tabindex="-1"
         aria-labelledby="commentShowHideModal"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-header">
                    <p class="modal-title"><span class="commentShowHideStatus"></span> comentário</p>
                </div>
                <div class="modal-body">
                    Você tem certeza que deseja <span class="commentShowHideStatus px-1 text-lowercase"></span> este
                    comentário?
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal" id="showHideComment"
                            aria-label="Close">
                        Sim, <span class="commentShowHideStatus"></span>
                    </button>
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal" aria-label="Close">
                        Não, manter
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('elements.toast')
@endsection
