<ul id="{{ $id }}" class="nav nav-tabs" role="tablist">

    @foreach($args as $arg)
        <li class="nav-item">
            <a class="nav-link" id="{{$arg['id']}}-tab" data-toggle="tab" href="#{{$arg['id']}}" role="tab" aria-controls="{{$arg['id']}}" aria-selected="true" data-index="{{$loop->index}}">{{$arg['label']}}</a>
        </li>
    @endforeach
</ul>
