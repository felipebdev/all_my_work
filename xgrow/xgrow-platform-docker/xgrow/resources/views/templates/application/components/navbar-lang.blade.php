@if(Auth::check())
    <!-- fix undefined url error -->
    @if(@isset(Auth::user()->platform->url))
	<li class="nav-item dropdown">
	    <a href="{!! Auth::user()->platform->url !!}" target="_blank" class="nav-link dropdown-toggle text-muted waves-effect waves-dark" aria-haspopup="true" aria-expanded="false" >
	        <i class="fa fa-eye"></i>
	    </a>
	</li>
    @endif
@endif