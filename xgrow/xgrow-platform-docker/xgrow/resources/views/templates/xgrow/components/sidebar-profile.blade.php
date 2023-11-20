<a href="/user">
    <div class="user-profile">
        <div class="profile-img">
        @if(isset(Auth::user()->thumb->filename))
            <img src="{{ Auth::user()->thumb->filename }}" style="width:50px;height:50px;object-fit: cover;" alt="user" />
        @else
           <img src="{{ asset('images/profile.png') }}" style="width:50px;height:50px;object-fit: cover;" alt="user" alt="user" />
        @endif
        </div>
        <div class="profile-text"> <a href="#" class="link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
            @if(Auth::check())
                {{Auth::user()->name}}
            @endif
        <span class="caret"></span></a>
        </div>
    </div>
</a>
