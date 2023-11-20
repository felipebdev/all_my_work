@extends('templates.horizontal.main')

@section('jquery')

@endsection

@push('before-styles')

<link rel="stylesheet" type="text/css" href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/datatables/media/css/dataTables.bootstrap4.css">

@endpush

@push('before-scripts')
<script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@push('after-scripts')

<!-- This is data table -->
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/datatables/datatables.min.js"></script>
<!-- start - This is for export functionality only -->
<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
<!-- end - This is for export functionality only -->

<link href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/chartist-js/dist/chartist.min.css" rel="stylesheet">
<link href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/chartist-js/dist/chartist-init.css" rel="stylesheet">
<link href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css" rel="stylesheet">
<link href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/css-chart/css-chart.css" rel="stylesheet">
<script>
    $(function() {
        $(document).ready(function() {
            $('#platform-table').DataTable({
                dom: 'l<br>Bfrtip',
                "aoColumnDefs": [{
                        "bSortable": false,
                        "aTargets": [3]
                    },
                    {
                        "bSearchable": false,
                        "aTargets": [3]
                    }
                ],
                buttons: [{
                        extend: 'copy',
                        text: 'Copiar'
                    },
                    'csv', 'excel', 'pdf',
                    {
                        extend: 'print',
                        text: 'Imprimir'
                    },
                ],
                language: {
                    "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
                }
            });
        });
    });
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
</script>

<style> 
.card-counter{
    box-shadow: 2px 2px 10px #DADADA;
    margin: 5px;
    padding: 20px 10px;
    background-color: #fff;
    height: 100px;
    border-radius: 5px;
    transition: .3s linear all;
  }

  .card-counter:hover{
    box-shadow: 4px 4px 20px #DADADA;
    transition: .3s linear all;
  }

  .card-counter.primary{
    background-color: #007bff;
    color: #FFF;
  }

  .card-counter.danger{
    background-color: #ef5350;
    color: #FFF;
  }  

  .card-counter.success{
    background-color: #66bb6a;
    color: #FFF;
  }  

  .card-counter.info{
    background-color: #26c6da;
    color: #FFF;
  }  

  .card-counter i{
    font-size: 5em;
    opacity: 0.2;
  }

  .card-counter .count-numbers{
    position: absolute;
    right: 35px;
    top: 20px;
    font-size: 32px;
    display: block;
  }

  .card-counter .count-name{
    position: absolute;
    right: 35px;
    top: 65px;
    font-style: italic;
    text-transform: capitalize;
    opacity: 0.5;
    display: block;
    font-size: 18px;
  }
</style>

@endpush

@section('content')

<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Plataformas</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="/platforms">Plataformas</a></li>
            <li class="breadcrumb-item active">Indicadores</li>
        </ol>
    </div>
</div>

<div class="container">
    <div class="row">
    <div class="col-md-3">
      <div class="card-counter primary">
        <i class="fa fa-code-fork"></i>
        <span class="count-numbers">{{ $plans }}</span>
        <span class="count-name">Produtos</span>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card-counter primary">
        <i class="fa fa-code-fork"></i>
        <span class="count-numbers">{{ $coupons }}</span>
        <span class="count-name">Cupons</span>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card-counter danger">
        <i class="fa fa-ticket"></i>
        <span class="count-numbers">{{ $users }}</span>
        <span class="count-name">Usuários</span>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card-counter success">
        <i class="fa fa-database"></i>
        <span class="count-numbers">{{ $contents }}</span>
        <span class="count-name">Counteúdos</span>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card-counter info">
        <i class="fa fa-users"></i>
        <span class="count-numbers">{{ $sections }}</span>
        <span class="count-name">Seções</span>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card-counter info">
        <i class="fa fa-users"></i>
        <span class="count-numbers">{{ $courses }}</span>
        <span class="count-name">Cursos</span>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card-counter info">
        <i class="fa fa-users"></i>
        <span class="count-numbers">{{ $authors }}</span>
        <span class="count-name">Autores</span>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card-counter info">
        <i class="fa fa-users"></i>
        <span class="count-numbers">{{ $subscribers }}</span>
        <span class="count-name">Alunos</span>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card-counter info">
        <i class="fa fa-users"></i>
        <span class="count-numbers">{{ $leads }}</span>
        <span class="count-name">Leads</span>
      </div>
    </div>

  </div>
</div>


@endsection