<li class="nav-item hidden-sm-down">
    <form class="app-search" id="appSearch" method="POST" action="{{route('main.research')}}">
        @csrf
        <input type="text" name="item" class="form-control" placeholder=""><a class="srh-btn"><i class="ti-search" onclick="document.getElementById('appSearch').submit();"></i></a>
    </form>
</li>
