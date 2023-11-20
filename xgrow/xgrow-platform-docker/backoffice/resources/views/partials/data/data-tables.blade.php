@if(!@isset($classes))
    @php
        $classes = '';
    @endphp
@endif
@if(!@isset($attributes))
    @php
        $attributes = '';
    @endphp
@endif
@if(!@isset($value))
    @php
        $value = '';
    @endphp
@endif

<div class="table-container table-responsive table-bordered m-t-30" {{$attributes}}>

    <div class="table-loader">

        <div class="spinner-container">

            <div class="spinner-border text-success" role="status">
                <span class="sr-only">Loading...</span>
            </div>

        </div>

    </div>

    <div class="table-content">

        @if(@isset($title))
            <h1>{{$title}}</h1>
        @endif

        <table id="{{$id}}" class="table table-bordered {{$classes}}">

            <thead>

            <tr class="header-names">

                @foreach ($columns as $column)

                    <th data-name="{{$column['name']}}" data-index="{{$loop->index}}" class="sort-column">

                        <span class="label">{{$column['label']}}</span>
                        <span class="badge badge-warning badge-sort hidden" data-index="{{$loop->index}}"></span>

                    </th>

                @endforeach

            </tr>

            </thead>

            <tbody>

            </tbody>

            <tfoot>

            <tr class="header-names">

                @foreach ($columns as $column)

                    <th data-name="{{$column['name']}}" data-index="{{$loop->index}}" class="sort-column">
                        <span class="label">{{$column['label']}}</span>
                        <span class="badge badge-warning badge-sort hidden" data-index="{{$loop->index}}"></span>

                    </th>

                @endforeach

            </tr>

            </tfoot>

        </table>

    </div>

</div>
