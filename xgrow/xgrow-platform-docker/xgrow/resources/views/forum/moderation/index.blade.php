@extends('templates.xgrow.main')

@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/comments.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.bootstrap4.min.css" />

    <style>
        .xgrow-user-avatar {
            object-fit: cover;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            box-shadow: 0 0 2px 2px #FFFFFF
        }

        .xgrow-user-profile>.info {
            display: flex;
            margin-bottom: 1rem;
        }

        .xgrow-user-profile>.info>div {
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

        $(function() {
            customTable = $('#content-table').DataTable({
                dom: '<"d-flex align-items-center justify-content-between flex-wrap"<"create-title"><"d-flex align-items-center"<"create-buttons">><"filter-button">>' +
                    '<"filter-div mt-2">' +
                    '<"mt-2" rt>' +
                    '<"my-3 d-flex align-items-center justify-content-between flex-wrap"<"my-2"l><"my-2"p>>',

                lengthMenu: [
                    [10, 25, 50, -1],
                    ['10 itens por página', '25 itens por página', '50 itens por página',
                        'Todos os registros'
                    ]
                ],
                processing: true,
                serverSide: false,
                columnDefs: [{
                    'targets': [0],
                    'visible': true
                }],

                ajax: {
                    url: '/api/forum/get-all-posts',
                    data: {
                        'status': {{ $approved }}
                    },
                },
                columns: [{
                        name: null,
                        render: function(data, type, row) {
                            return `<div class="form-check">
                                        <input type="checkbox" id="${row.id}"  value="${row.id}" name="posts[]" class="post form-check-input">
                                    </div>`;
                        }
                    },
                    {
                        data: 'subscribers.name',
                        name: 'subscribers',
                        className: 'details-control',
                        render: function(data, type, row) {
                            const hasImage = row.subscribers.thumb !== null ? row.subscribers.thumb
                                .filename : '/xgrow-vendor/assets/img/profile_default.jpg';
                            const avatar =
                                `<img src="${hasImage}" alt="avatar" class="xgrow-user-avatar">`;
                            return `<div class="xgrow-user-profile">
                                        <div class="info">
                                            ${avatar}
                                            <div>
                                                <span>${row.subscribers.name ?? 'Você'}</span>
                                            </div>
                                        </div>
                                    </div>`;

                        }
                    },
                    {
                        data: 'post',
                        name: 'post',
                        className: 'details-control',
                        render: function(data, type, row) {
                            let resume = data.split(' ').splice(0, 28).join(' ');
                            return data.length > resume.length ? resume + '...' : resume;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        className: 'details-control',
                        render: function(data, type, row) {
                            return `${moment(data).format('DD/MM/YYYY')} às ${moment(data).format('h[h]mm')}`;
                        }
                    },
                    {
                        data: 'topic.title',
                        className: 'details-control',
                        name: 'topic',
                        render: function(data, type, row, rowID) {
                            const title = `<div style="margin-right:1rem">${row.topic.title}</div>`;
                            return `<div class="d-flex align-items-end">${title}</div>`;
                        }
                    },
                ],
                language: {
                    'url': "{{ asset('js/datatable-translate-pt-BR.json') }}"
                },
                initComplete: function(settings, json) {
                    $('.buttons-csv').removeClass('dt-button buttons-csv');
                    $('.buttons-excel').removeClass('dt-button buttons-excel');
                    $('.buttons-pdf').removeClass('dt-button buttons-pdf');
                    $('.create-buttons').html('' +
                        '<button type="button" class="remove-btn xgrow-upload-btn-lg btn mx-1 my-2" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="fa fa-trash" aria-hidden="true"></i><span>Excluir</span></button>' +
                        '<button type="button" class="approved-btn xgrow-upload-btn-lg btn mx-1 my-2" data-bs-toggle="modal" data-bs-target="#customModal"><i class="fa fa-undo" aria-hidden="true"></i><span>{{ $approved ? 'Reprovar' : 'Aprovar' }}</span></button>' +
                        '');
                    $('.create-label').html('<p class="xgrow-medium-bold me-2">Exportar em</p>');
                    $('.create-title').html(
                        '<p class="xgrow-card-title">{{ $approved == 1 ? 'Posts publicados no canal' : 'Posts pendentes de aprovação' }}</p>'
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
                                          <div class="col-12">
                                              <div class="xgrow-form-control mui-textfield mui-textfield--float-label my-2 col-sm-12 col-md-4">
                                                  <select class="xgrow-select" name="options" id="topicSelect">
                                                      <option value="option1" disabled="" selected="" hidden="">Autor</option>
                                                  </select>
                                                  <label for="authorSelect">Filtrar por tópico:</label>
                                              </div>
                                          </div>
                                      </div>
                                   </div>
                                </div>
                            </div>
                        `);

                    //Cria os options para o conteúdo
                    // TOPIC COLUMN
                    const authorOptions = this.api().column(4);
                    $('#topicSelect').append(`<option value="">Todos</option>`);
                    authorOptions.data().unique().sort().each((value) => {
                        $('#topicSelect').append(`<option value="${value}">${value}</option>`);
                    });

                    $('#topicSelect').on('change', function() {
                        customTable.columns(4).search(this.value).draw();
                    });

                    $('#content-table tbody').on('click', 'td.details-control', function() {
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
                        const post_id = row.id;
                        let listReplies = '';

                        axios.get('/api/forum/get-replies', {
                                params: {
                                    post_id
                                }
                            })
                            .then(res => {
                                let replies = res.data.replies;
                                listReplies += `
                                    <div class="xgrow-user-profile" style="background: var(--black-card-color);padding: 1rem 1.5rem;margin-bottom: .5rem;">
                                       <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                           <textarea id="reply-${post_id}" spellcheck="false" class="mui--is-empty mui--is-untouched mui--is-pristine" rows="3" style="height: auto"></textarea>
                                           <label>Adicionar comentário público</label>
                                           <span onclick="document.getElementById('reply-${post_id}').value = ''"></span>
                                       </div>
                                       <div class="d-flex justify-content-between">
                                           <button type="button" style="font-size:.9rem;margin-right:2rem;color:var(--tab-active);background:transparent;border:none;" data-bs-toggle="collapse" data-bs-target="#replies-${post_id}">ESCONDER/EXIBIR TODAS AS RESPOSTAS</button>
                                           <div>
                                               <button type="button" style="font-size:.9rem;margin-right:2rem;color:var(--tab-active);background: transparent;border: none;" onclick="document.getElementById('reply-${post_id}').value = ''">CANELAR</button>
                                               <button type="button"  style="font-size:.9rem;margin-right:2rem;color:var(--tab-active);background: transparent;border: none;" onclick="sendReply(${post_id}, 'reply-${post_id}')">ENVIAR</button>
                                           </div>
                                       </div>
                                    </div>
                                `;

                                listReplies += `<div id="replies-${post_id}" class="collapse show">`;

                                if (res.data.replies.length > 0) {
                                    replies.forEach(post => {
                                        const subscriber = post.subscribers_id === null ?
                                            post.platforms_users : post.subscribers;
                                        listReplies += `
                                            <div class="xgrow-user-profile" style="background: var(--black-card-color);padding:1rem 1.5rem 1rem 6rem;margin-bottom: .5rem;">
                                               <div class="info">
                                                   <img src="${subscriber.thumb.filename}" class="xgrow-user-avatar">
                                                   <div>
                                                       <span>${subscriber.name}</span>
                                                       <small>${moment(post.created_at).format('DD/MM/YYYY')}</small>
                                                   </div>
                                               </div>
                                               <div class="text">
                                                   ${post.post}
                                                   <div>
                                                   <button type="button" style="font-size:.9rem;margin-right:2rem;color:var(--tab-active);background:transparent;border:none;" onclick="hiddenPost(${post.id})"><i class="fa fa-eye"></i> <span id="btnReply-${post.id}">${post.approved ? 'Esconder resposta' : 'Exibir resposta'}</span></button>
                                                   <button type="button" style="font-size:.9rem;margin-right:2rem;color:var(--tab-active);background:transparent;border:none;" onclick="showDeleteModal(${post.id})" id="btnDelete-${post.id}"><i class="fa fa-close"></i> Excluir</button>
                                                   </div>
                                               </div>
                                            </div>
                                        `;
                                    });
                                } else {
                                    listReplies += `<div style="background: var(--black-card-color) -50%;display: flex;justify-content: center;padding: 1rem;align-items: center;">
                                                         <div class="text-center">
                                                             <p>Sem resposta para este comentário.</p>
                                                         </div>
                                                     </div>
                                                 `;
                                }
                                listReplies += `</div>`;

                                const el = document.getElementById(`divComment-${post_id}`);
                                const td = $('#divComment-' + post_id).closest('td');
                                td.css('padding', 0);

                                el.innerHTML = listReplies;
                            })
                            .catch(error => console.log(error));

                        return `<div id="divComment-${post_id}">${listReplies}</div>`;
                    }
                }
            });
        });

        // Check All Checkboxes
        $('#checkAll').click(function() {
            $('.post').not(this).prop('checked', this.checked);
        });

        // Move posts to approved ou pending
        function changeStatusSelected() {
            const post_checked = $('.post:checked');
            if (post_checked.length > 0) {
                let arrayIDComments = [];
                post_checked.each(function() {
                    arrayIDComments.push($(this).val());
                });

                $.ajax({
                    url: "/api/forum/approve-or-deny-post",
                    method: 'POST',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        posts: arrayIDComments,
                        status: {{ $approved }}
                    },
                    success: function(response) {
                        const status = ({{ $approved }}) ? 'pendentes.' : 'aprovados.';
                        successToast('Posts movidos com sucesso!',
                            `Agora eles se encontram em itens ${status}`);
                        customTable.ajax.reload();

                    },
                    error: function(response) {
                        console.log(response);
                        errorToast('Algum erro aconteceu!', 'Não foi possível mover os itens.');
                        customTable.ajax.reload();
                    }
                });
            } else {
                alert('Selecione ao menos um post');
            }
        }

        // Remove posts selected
        function deleteSelected() {
            const post_checked = $('.post:checked');
            if (post_checked.length > 0) {
                let arrayPosts = [];
                post_checked.each(function() {
                    arrayPosts.push($(this).val());
                });

                $.ajax({
                    url: "/api/forum/delete-post",
                    method: 'POST',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        posts: arrayPosts
                    },
                    success: function(response) {
                        successToast('Posts excluídos com sucesso!', `Ação feita com exito.`);
                        customTable.ajax.reload();

                    },
                    error: function(response) {
                        console.log(response);
                        errorToast('Algum erro aconteceu!', 'Não foi possível excluir os posts.');
                        customTable.ajax.reload();
                    }
                });
            } else {
                alert('Selecione ao menos um post');
            }
        }

        // Show Modal Delete
        function showDeleteModal(id) {
            document.getElementById("deleteCommentModalButton").setAttribute("onClick", `deleteReply(${id})`);
            let deleteOnceModal = new bootstrap.Modal(document.getElementById('deleteOnceModal'))
            deleteOnceModal.show();
        }

        // Send Reply
        function sendReply(id, reply) {
            const replyComment = document.getElementById(reply);
            if (replyComment.value.trim() === '') {
                errorToast('Erro ao enviar resposta!', 'Você precisa adicionar uma resposta.');
                return false;
            }
            axios.post('/api/forum/send-reply-post', {
                text: replyComment.value,
                post_id: id
            }).then(res => {
                successToast('Comentário respondido!', 'Resposta enviada com sucesso.');
                replyComment.value = '';
                customTable.ajax.reload();
            }).catch(error => {
                errorToast('Algum erro aconteceu!', 'Não foi possível responder o comentário.');
            });
        }

        // Delete Comment
        function deleteReply(id) {
            axios.post('/api/forum/delete-reply', {
                reply_id: id
            }).then(res => {
                if (res.status === 204) {
                    const replyDiv = $('#btnDelete-' + id).closest('.xgrow-user-profile');
                    successToast('Resposta removida!', 'Resposta removida com sucesso.');
                    replyDiv.hide('slow');
                }
            }).catch(error => {
                errorToast('Algum erro aconteceu!', 'Não foi possível excluir a resposta.');
            });
        }

        // Call Hidden/Show comment modal
        function hiddenPost(id) {
            const msg = document.getElementById("btnReply-" + id).textContent === 'Exibir resposta' ? 'Ocultar' : 'Exibir' ;
            const text = document.querySelectorAll('.commentShowHideStatus');
            text.forEach(txt => txt.textContent = msg);
            document.getElementById("showHideComment").setAttribute("onClick", `showHidePost(${id})`);
            let commentShowHideModal = new bootstrap.Modal(document.getElementById('commentShowHideModal'))
            commentShowHideModal.show();
        }

        // Function Hidden/ShowPost
        function showHidePost(id) {
            axios.post('/api/forum/change-status-reply', {
                reply_id: id
            }).then(res => {
                const btnReply = document.getElementById('btnReply-' + id);
                btnReply.innerText = res.data.approved ? 'Esconder resposta' : 'Exibir resposta';
                res.data.approved ?
                    successToast('Comentário ativado!', 'Ação feita com sucesso.') :
                    successToast('Comentário desativado!', 'Ação feita com sucesso.');
            }).catch(error => {
                errorToast('Algum erro aconteceu!', 'Não foi possível esconder o comentário.');
            });
        }

    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="/forum">Fórum</a></li>
            @if ($approved == 1)
                <li class="breadcrumb-item active"><a href="/forum/moderation">Aprovados</a></li>
            @else
                <li class="breadcrumb-item active"><a href="/forum/moderation/pending">Pendentes</a></li>
            @endif
        </ol>
    </nav>


    <div class="xgrow-tabs nav nav-tabs" id="nav-tab" role="tablist">
        <a class="xgrow-tab-item nav-item nav-link {{ Route::current()->getName() == 'forum.moderation' ? ' active' : '' }}"
            id="nav-approved-tab" href="{{ Route('forum.moderation') }}" role="tab" aria-controls="nav-approved"
            aria-selected="true">Aprovados</a>

        <a class="xgrow-tab-item nav-item nav-link {{ Route::current()->getName() == 'forum.moderation.pending' ? ' active' : '' }}"
            id="nav-pending-tab" href="{{ Route('forum.moderation.pending') }}" role="tab" aria-controls="nav-pending"
            aria-selected="false">Validar</a>
    </div>

    <div class="tab-content py-3" id="nav-tabContent">
        @include('forum.moderation.approved')
        @include('forum.moderation.moderation')
    </div>

    {{-- Modal MOVE --}}
    <div class="modal-sections modal fade" id="customModal" tabindex="-1" aria-labelledby="customModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-header">
                    <p class="modal-title" id="customModal">Mover
                        para {{ $approved == 1 ? 'moderação' : 'aprovados' }}</p>
                </div>
                <div class="modal-body">
                    Você tem certeza que deseja mover os posts selecionados
                    <br>
                    para itens {{ $approved == 1 ? 'moderação' : 'aprovados' }}?
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="changeStatusSelected()"
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
    <div class="modal-sections modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-header">
                    <p class="modal-title" id="deleteModal">Excluir Posts</p>
                </div>
                <div class="modal-body">
                    Você tem certeza que deseja excluir os posts selecionados?
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
                    <p class="modal-title" id="deleteOnceModal">Excluir resposta</p>
                </div>
                <div class="modal-body">
                    Você tem certeza que deseja excluir esta resposta?
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
