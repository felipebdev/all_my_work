
$(function() {
    $(document).ready(function() {
        $('#subscriber-table').DataTable({
            //dom: 'lBfrtip',
            dom: '<"fandone-bar"<"fandone-bar-left"l><"fandone-bar-right"Bf>>rtip',
            aoColumnDefs: [
                { "bSortable": false, "aTargets": [ 8 ] },
                { "bSearchable": false, "aTargets": [ 8 ] },
                {
                    targets:2, render:function(data){
                        return moment(data).format('DD/MM/YYYY');
                       
                    }
                }
            ],
            processing: true,
            serverSide: true,
            ajax: '{!! route('datatables.subscribers') !!}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render:function ( data, type, row, meta ) {
                        return (data == null) ? '' : moment(data).format('DD/MM/YY');
                    }
                },
                { data: 'status', name: 'status' },
                {
                    data: 'last_acess',
                    name: 'last_acess',
                    render:function ( data, type, row, meta ) {
                        return (data == null) ? '' : moment(data).format('DD/MM/YY');
                    }
                },
                { data: 'plan_name', name: 'plans.name' },
                { name: 'id', defaultContent: ''},
                {
                    title: 'Alterar',
                    data: null,
                    createdCell: function(td, cellData, rowData, row, col){
                        let href    = `/subscribers/${cellData.id}/edit`;
                        let buttons = ` <a href="${href}" class="fandone-edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <button type="button" onclick="delSubscriber(${row}, ${cellData.id},'${cellData.name}')" class="fandone-delete">
                                            <i class="fa fa-trash"></i>
                                        </button>`
                        $(td).html(buttons)
                    }
                }
            ],
            buttons: [
                {
                    extend: 'print', text: '<img class="fandone-bar-img" src="/images/icon_print.png">',
                    className: ''
                },
                {
                    extend: 'pdf', text: '<img class="fandone-bar-img" src="/images/icon_pdf.png">',
                    className: ''
                },
                {
                    extend: 'csv', text: '<img class="fandone-bar-img" src="/images/icon_csv.png">',
                    className: ''
                },
                {
                    extend: 'excel', text: '<img class="fandone-bar-img" src="/images/icon_xls.png">',
                    className: ''
                },
            ],
            language: {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            },
            initComplete: function (settings, json) {
                $(".buttons-csv").removeClass("dt-button buttons-csv");
                $(".buttons-excel").removeClass("dt-button buttons-excel");
                $(".buttons-pdf").removeClass("dt-button buttons-pdf");
                $(".buttons-print").removeClass("dt-button buttons-print");
            }
        });
    });
});

$('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');

function delSubscriber(row, subscriberId, subscriberName)
{
    if (!confirm(`Deseja excluir o assinante ${subscriberName}?`)) {
        return false
    }

    $.ajax({
        type: 'POST',
        url: "{{URL::route('subscribers.destroy')}}",
        dataType: 'json',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: {
            'id': subscriberId,
            '_token': "{{ csrf_token() }}"
        },
        success: function (data) {
            let oTable = $('#plan-table').DataTable();
                oTable.row( $(this).parents('tr') ).remove().draw();
            alert("Registro excluído com sucesso!")
        },
        error: function (data) {
            alert("Houve um erro na exclusão do registro: " + data.responseJSON.message)
        }
    });
}