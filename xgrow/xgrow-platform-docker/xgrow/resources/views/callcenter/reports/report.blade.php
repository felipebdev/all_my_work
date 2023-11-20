@extends('templates.xgrow.main')

@php
    $icons = [
        'gain' => 'earnings.svg',
        'lost' => 'losses.svg',
        'expired' => 'losses.svg',
        'contactless' => 'no-contact.svg',
        'pending' => 'pending.svg',
    ];
@endphp

@push('after-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script type="text/javascript" src="{{ asset('/js/utils.js') }}"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>

    <script>
        let status_filter = {
            'gain': 'Ganho',
            'lost': 'Perdido',
            'expired': 'Expirado',
            'contactless': 'Sem contato',
            'pending': 'Pendente',
        }

        $('.export').on('click', async (evt) => {
            let res = await axios.get('{{ url()->current() }}/request');
            const values = res.data.attendance_contacts;

            let data = [];
            values.map((item, index) => {
                data.push({
                    'attendance_id': item.attendance_id,
                    'attendant': item.attendance.attendant.name,
                    'subscriber': item.attendance.subscriber.name,
                    'audience': item.attendance.audience.name,
                    'date': item.created_at,
                    'status': status_filter[item.status],
                    'reason_loss': item.reasons_loss != null ? item.reasons_loss.description : '',
                    'description': item.description,
                    'ip': item.ip
                });
            });

            const title = `Relatório ${data[0].attendance_id}`;
            const filename = `relatorio-${data[0].attendance_id}`;

            let text = title + '.\n\n';
            if (evt.target.dataset.type === 'pdf') {
                const header = ['Atendente', 'Cliente', 'Público', 'Data de Contato', 'Resultado', 'Motivo de Perda', 'Descrição', 'IP'];
                pdfMakeWrapperDownloader(
                    title,
                    header,
                    data.map((item) => {
                        return [
                            item.attendant,
                            item.subscriber,
                            item.audience,
                            item.date,
                            item.status,
                            item.reason_loss,
                            item.description,
                            item.ip
                        ];
                    }),
                    filename
                )
            }
            if (evt.target.dataset.type === 'csv') {
                text = 'atendente;cliente;público;data de contato;resultado;motivo de perda;descriçao;ip\n';
                data.map((item) => {
                    text += `${item.attendant};${item.subscriber};${item.audience};${item.date};${item.status};${item.reason_loss};${item.description};${item.ip}\n`;
                });
                downloadFile(text, 'data:text/csv;charset=utf-8,', filename + '.csv');
            }
            if (evt.target.dataset.type === 'xls') {
                text = 'Atendente\tCliente\tPúblico\tData de Contato\tResultado\tMotivo de Perda\tDescrição\tIP\n';
                data.map((item) => {
                    text += `${item.attendant}\t${item.subscriber}\t${item.audience}\t${item.date}\t${item.status}\t${item.reason_loss}\t${item.description}\t${item.ip}\n`;
                });

                downloadFile(text, 'data:application/csv;charset=utf-8,', filename + '.xls');
            }
            if (evt.target.dataset.type === 'txt') {
                data.map((item) => {
                    text += `Atendente: ${item.attendant}`
                    text += ` | Cliente: ${item.subscriber}`;
                    text += ` | Público: ${item.audience}`;
                    text += ` | Data de Contato: ${item.date}`;
                    text += ` | Resultado: ${item.status}`;
                    text += ` | Motivo de perda: ${item.reason_loss}`;
                    text += ` | Descrição: ${item.description}`;
                    text += ` | IP: ${item.ip}`;
                });
                downloadFile(text, 'data:text/plain;charset=utf-8,', filename + '.txt');
            }
        });
    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('callcenter.dashboard') }}">Call center</a></li>
            <li class="breadcrumb-item active mx-2"><span>Relatórios</span></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark p-3 py-4">
        <div class="xgrow-card-header pb-3 mb-3 flex-wrap">
            <div class="d-flex align-items-center">
                <h5 class="xgrow-card-title" style="font-size: 1.5rem; line-height: inherit">
                    Relatório #{{ $attendance_id }}
                </h5>
                <h6 class="mx-2"> | {{ $attendance_contacts[0]->attendance->attendant->name }} atendendo {{ $attendance_contacts[0]->attendance->subscriber->name }}</h6>
            </div>

            <div class="d-flex">
                <button class="xgrow-button export-button me-1 export" data-type="pdf" title="Exportar em PDF">
                    <i class="fas fa-file-pdf" aria-hidden="true" data-type="pdf" style="color: red;"></i>
                </button>

                <button class="xgrow-button export-button me-1 export" data-type="csv" title="Exportar em CSV">
                    <i class="fas fa-file-csv" aria-hidden="true" data-type="csv" style="color: blue;"></i>
                </button>

                <button class="xgrow-button export-button me-1 export" data-type="xls" title="Exportar em XLSX">
                    <i class="fas fa-file-excel" aria-hidden="true" data-type="xls" style="color: green;"></i>
                </button>
            </div>
        </div>
        <div class="xgrow-card-body mb-3 d-flex flex-column-reverse">
            @php
                $i = 1;
            @endphp
            @foreach ($attendance_contacts as $contact)
                <div class="accordion accordion-flush mb-4" id="accordion-campaing-{{ $i }}">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-heading-{{ $i }}">
                            <button class="accordion-button {{ ($i == count($attendance_contacts)) ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapse-{{ $i }}" aria-expanded="{{ ($i == count($attendance_contacts)) ? 'true' : 'false' }}" aria-controls="flush-collapse-{{ $i }}"
                                style="border-radius: 10px; font-weight: bold">
                                {{ $i.'º' }} Contato
                            </button>
                        </h2>
                        <div id="flush-collapse-{{ $i }}" class="accordion-collapse collapse {{ ($i == count($attendance_contacts)) ? 'show' : '' }}" aria-labelledby="flush-heading-{{ $i }}"
                            data-bs-parent="#accordion-campaing-{{ $i }}">
                            <div class="accordion-body container-fluid" style="border-radius: 10px;">

                                {{-- AUDIENCE NAME --}}
                                <div class="row mb-3">
                                    <div class="col-xl-1 col-lg-1 col-md-2 col-sm-4 col-4">
                                        <p style="font-weight: bold">Público</p>
                                    </div>
                                    <div class="col-xl-11 col-lg-11 col-md-10 col-sm-8 col-8">
                                        <p>{{ $contact->attendance->audience->name }}</p>
                                    </div>
                                </div>

                                {{-- DATE OF CONTACT --}}
                                <hr class="my-2" style="border-color: var(--border-color)"/>
                                <div class="row my-3">
                                    <div class="col-xl-1 col-lg-1 col-md-2 col-sm-4 col-4">
                                        <p style="font-weight: bold">Data</p>
                                    </div>
                                    <div class="col-xl-11 col-lg-11 col-md-10 col-sm-8 col-8">
                                        <p>{{ DateTime::createFromFormat("Y-m-d H:i:s", $contact->created_at)->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                </div>

                                {{-- STATUS --}}
                                <hr class="my-2" style="border-color: var(--border-color)"/>
                                <div class="row my-3 align-items-center">
                                    <div class="col-xl-1 col-lg-1 col-md-2 col-sm-4 col-4">
                                        <p style="font-weight: bold">Status</p>
                                    </div>
                                    <div class="col-xl-11 col-lg-11 col-md-10 col-sm-8 col-8 d-flex align-items-center">
                                        <img src="{{ asset("xgrow-vendor/assets/img/callcenter/{$icons[$contact->status]}") }}">
                                        <p class="mx-2">{{ $status_filter[$contact->status] }}</p>
                                    </div>
                                </div>

                                {{-- REASON IF LOST --}}
                                @if ($contact->status === 'lost')
                                    <hr class="my-2" style="border-color: var(--border-color)"/>
                                    <div class="row my-3">
                                        <div class="col-xl-1 col-lg-1 col-md-2 col-sm-4 col-4">
                                            <p style="font-weight: bold">Motivo</p>
                                        </div>
                                        <div class="col-xl-11 col-lg-11 col-md-10 col-sm-8 col-8">
                                            <p>{{ $contact->reasons_loss->description }}</p>
                                        </div>
                                    </div>
                                @endif

                                {{-- DESCRIPTION --}}
                                <hr class="my-2" style="border-color: var(--border-color)"/>
                                <div class="row my-3">
                                    <div class="col-xl-1 col-lg-1 col-md-2 col-sm-4 col-4">
                                        <p style="font-weight: bold">Descrição</p>
                                    </div>
                                    <div class="col-xl-11 col-lg-11 col-md-10 col-sm-8 col-8">
                                        <p>{{ $contact->description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @php
                    $i++;
                @endphp
            @endforeach
        </div>
        <hr class="my-4" style="border-color: var(--border-color)"/>
        <div class="xgrow-card-footer">
            <button type="button" class="xgrow-button" onclick="window.location.href='{{ url()->previous() }}'">Voltar</button>
        </div>
    </div>
@endsection