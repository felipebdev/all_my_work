@push('after-scripts')
    <script>
        $(function () {
            const settingsExample = {
                'log':
                {
                },
                'smtp':
                {
                    "_comment_" : "encryption and headers are optional",
                    "port": "2525",
                    "host": "smtp.example.com",
                    "username": "",
                    "password": "",
                    "encryption": "ssl|tls (optional, recommended)",
                    "headers": {
                        "X-Custom-Header": "Header One",
                        "X-Custom-Header-2": "Header Two"
                    },
                },
                'mailgun':
                {
                    'domain': 'your-mailgun-domain',
                    'secret': 'your-mailgun-key',
                    'endpoint': 'api.eu.mailgun.net (optional)',
                },
                'mandrill':
                {
                    'secret': 'your-mandrill-key',
                },
                'ses':
                {
                    'key': 'your-ses-key',
                    'secret': 'your-ses-secret',
                    'region': 'ses-region (e.g. us-east-1)',
                },
                'postmark':
                {
                    'token': 'your-postmark-token',
                },

            };

            $('#driver').change(function () {
                const val = $(this).val();
                const settings = settingsExample[val] || 'Formato desconhecido';
                const pretty = JSON.stringify(settings, undefined, 4);
                $('#example').val(pretty);
            }).change();
        })
    </script>
@endpush

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="name">Nome do provedor</label>
            <input type="text" class="form-control" id="name" name="name" value="{{$provider['name'] ?? ''}}"
                   pattern="[a-z_]*" required maxlength="20">
        </div>
    </div>

    <div class="col-md-8">
        <div class="form-group">
            <label for="description">Descrição</label>
            <input type="text" class="form-control" id="description" name="description" value="{{$provider['description'] ?? ''}}">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="from_name">From name</label>
            <input type="text" class="form-control" id="from_name" name="from_name"
                   value="{{$provider['from_name'] ?? ''}}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="from_address">From address</label>
            <input type="text" class="form-control" id="from_address" name="from_address"
                   value="{{$provider['from_address'] ?? ''}}" required>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="service_tags">Tags de serviço (opcional, separado por vírgula)</label>
            <input type="text" class="form-control" id="service_tags" name="service_tags"
                   value="{{$provider['service_tags'] ?? ''}}">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            <label for="driver">Driver</label>
            <select class="form-control" name="driver" id="driver" required>
                @foreach($drivers as $driver)
                    <option
                        value="{{ $driver }}" {{$driver == $provider->driver ? 'selected' : ''}}
                    >{{ $driver }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="settings">Configuração (JSON)</label>
            <textarea name="settings" id="settings" class="form-control" cols="10" rows="7" required
                >{{ $provider['settings'] ?? '' }}</textarea>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="example">Exemplo de JSON <a href="https://laravel.com/docs/6.x/mail#driver-prerequisites">ℹ️️</a></label>
            <textarea name="example" id="example" class="form-control" cols="10" rows="7" required readonly></textarea>
        </div>
    </div>
</div>
