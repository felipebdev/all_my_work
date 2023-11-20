<h2>Cards</h2>
<h3 class="d-inline">Card with Image <a href="#code1" data-toggle="collapse"><i class="fa fa-code" data-toggle="tooltip" title="Get code"></i></a></h3>
<p class="text-muted m-t-0">For the code click on above code icon</p>
<div class="row">
    @forelse($data['cards'] as $card)
        <div class="col-lg-3 col-md-6 img-responsive">

            <div class="card {{ isset($card->class) ? $card->class : '' }}  {{ isset($card->text_align) ? $card->text_align : '' }}
            {{ isset($card->card_width) ?$card->card_width : '' }}">

                @if(isset($card->url))
                    <img class="card-img-top img-responsive" src="{{ $card->url }}" alt="{{ $card->alt }}">
                @endif

                @if(isset($card->header))
                    <div class="card-header">
                        @if(isset($card->class))
                            <h4 class="m-b-0 text-white">{{ $card->header }}</h4>
                        @else
                            {{ $card->header }}
                        @endif
                        @if(isset($card->has_actions))
                            <div class="card-actions">
                                <a class="" data-action="collapse"><i class="ti-minus"></i></a>
                                <a class="btn-minimize" data-action="expand"><i class="mdi mdi-arrow-expand"></i></a>
                                <a class="btn-close" data-action="close"><i class="ti-close"></i></a>
                            </div>
                        @endif
                    </div>
                @endif

                @if(isset($card->title) || isset($card->subtitle) || isset($card->text) || isset($card->button_text) || isset($card->action)
                 || isset($card->button_class) || $card->slot != "")
                    <div class="card-body">
                        @if(isset($card->title))
                            <h4 class="card-title">{{ $card->title }}</h4>
                        @endif

                        @if(isset($card->subtitle))
                            <h4 class="card-subtitle ">{{ $card->subtitle }}</h4>
                        @endif

                        @if(isset($card->text))
                            <p class="card-text">{{ $card->text }}</p>
                        @endif
                        {{ $card->slot }}
                        @if(isset($card->action) && isset($card->button_text))
                            <a href="{{ $card->action }}" class="btn {{ isset($card->button_class) ? $card->button_class : 'btn-primary' }}">{{ $card->button_text }}</a>
                        @endif
                    </div>
                @endif
                @if(isset($card->footer) && !empty($card->footer))
                    <div class="card-footer">{{ $card->footer }}</div>
                @endif

            </div>

        </div>
    @empty
        No Cards here!
    @endforelse
</div>

<h2>User Cards</h2>
<div class="row el-element-overlay">
    <div class="col-md-12">
        <h3 class="card-title">Fade-in effect</h3>
        <h6 class="card-subtitle m-b-20 text-muted">You can use by default <code>el-overlay</code></h6> </div>
    @forelse($data['userCards'] as $userCard)
        @if(empty($userCard->effect))
            <div class="col-lg-3 col-md-6"  >
                <div class="card">
                    <div class="el-card-item">
                        <div class="el-card-avatar el-overlay-1">
                            @if(isset($userCard->url))
                                <img src="{{ $userCard->url }}" alt="{{ $userCard->alt }}" />
                            @endif
                            <div class="el-overlay {{ isset($userCard->effect) ? $userCard->effect : '' }}">
                                <ul class="el-info">
                                    <li>
                                        <a class="btn default btn-outline image-popup-vertical-fit" href="{{ $userCard->action }}">
                                            <i class="icon-magnifier"></i>
                                        </a>
                                    </li>
                                    <li><a class="btn default btn-outline" href="javascript:void(0);"><i class="icon-link"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="el-card-content">
                            @if(isset($userCard->title))
                                <h3 class="box-title">{{ $userCard->title }}</h3> <small>{{ isset($userCard->subtitle) ? $userCard->subtitle : '' }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @empty
        No User Cards here!
    @endforelse

</div>
<div class="row el-element-overlay">
    <div class="col-md-12">
        <h3 class="card-title">Scroll down effect</h3>
        <h6 class="card-subtitle m-b-20 text-muted">You can use scroll down effect <code>el-overlay scrl-dwn</code></h6> </div>
    @forelse($data['userCards'] as $userCard)
        @if($userCard->effect == 'scrl-dwn')
            <div class="col-lg-3 col-md-6"  >
                <div class="card">
                    <div class="el-card-item">
                        <div class="el-card-avatar el-overlay-1">
                            @if(isset($userCard->url))
                                <img src="{{ $userCard->url }}" alt="{{ $userCard->alt }}" />
                            @endif
                            <div class="el-overlay {{ isset($userCard->effect) ? $userCard->effect : '' }}">
                                <ul class="el-info">
                                    <li>
                                        <a class="btn default btn-outline image-popup-vertical-fit" href="{{ $userCard->action }}">
                                            <i class="icon-magnifier"></i>
                                        </a>
                                    </li>
                                    <li><a class="btn default btn-outline" href="javascript:void(0);"><i class="icon-link"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="el-card-content">
                            @if(isset($userCard->title))
                                <h3 class="box-title">{{ $userCard->title }}</h3> <small>{{ isset($userCard->subtitle) ? $userCard->subtitle : '' }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @empty
        No User Cards here!
    @endforelse
</div>
<div class="row el-element-overlay">
    <div class="col-md-12">
        <h3 class="card-title">Scroll up effect</h3>
        <h6 class="card-subtitle m-b-20 text-muted">You can use by default <code>el-overlay scrl-up</code></h6> </div>
    @forelse($data['userCards'] as $userCard)
        @if($userCard->effect == 'scrl-up')
            <div class="col-lg-3 col-md-6"  >
                <div class="card">
                    <div class="el-card-item">
                        <div class="el-card-avatar el-overlay-1">
                            @if(isset($userCard->url))
                                <img src="{{ $userCard->url }}" alt="{{ $userCard->alt }}" />
                            @endif
                            <div class="el-overlay {{ isset($userCard->effect) ? $userCard->effect : '' }}">
                                <ul class="el-info">
                                    <li>
                                        <a class="btn default btn-outline image-popup-vertical-fit" href="{{ $userCard->action }}">
                                            <i class="icon-magnifier"></i>
                                        </a>
                                    </li>
                                    <li><a class="btn default btn-outline" href="javascript:void(0);"><i class="icon-link"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="el-card-content">
                            @if(isset($userCard->title))
                                <h3 class="box-title">{{ $userCard->title }}</h3> <small>{{ isset($userCard->subtitle) ? $userCard->subtitle : '' }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @empty
        No User Cards here!
    @endforelse
</div>

<h2>Buttons</h2>
<div class="row">
    <div class="col-lg-12 col-xlg-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">General Buttons</h3>
                <h6 class="card-subtitle">Use a classes <code>btn btn-success</code> to quickly create a General btn.</h6>
                <div class="button-group">

                    @forelse($data['generalButtons'] as $button)
                        <button type="button" class="btn
                                {{ isset($button->color) ? $button->color : ' btn-primary ' }} waves-effect waves-light">
                            {{ isset($button->text) ? $button->text : '' }}
                        </button>
                    @empty
                        No Buttons here!
                    @endforelse

                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-xlg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Button with outline</h4>
                <h6 class="card-subtitle">Use a classes <code>btn btn-outline-success</code> to quickly create a General btn.</h6>
                <div class="button-group">
                    @forelse($data['buttonsWithOutline'] as $button)
                        <button type="button" class="btn
                                {{ isset($button->color) ? $button->color : ' btn-outline-primary ' }} waves-effect waves-light">
                            {{ isset($button->text) ? $button->text : '' }}
                        </button>
                    @empty
                        No Buttons here!
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-xlg-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Rounded Buttons</h3>
                <h6 class="card-subtitle">Use a classes <code>btn btn-rounded btn-success</code> to quickly create a General btn.</h6>
                <div class="button-group">
                    @forelse($data['generalButtons'] as $button)
                        <button type="button" class="btn btn-rounded
                                {{ isset($button->color) ? $button->color : ' btn-primary ' }} waves-effect waves-light">
                            {{ isset($button->text) ? $button->text : '' }}
                        </button>
                    @empty
                        No Buttons here!
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-xlg-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Rounded outlined Buttons</h3>
                <h6 class="card-subtitle">Use a classes <code>btn btn-rounded btn-outline-success</code> to quickly create a General btn.</h6>
                <div class="button-group">
                    @forelse($data['buttonsWithOutline'] as $button)
                        <button type="button" class="btn btn-rounded
                                {{ isset($button->color) ? $button->color : ' btn-outline-primary ' }} waves-effect waves-light">
                            {{ isset($button->text) ? $button->text : '' }}
                        </button>
                    @empty
                        No Buttons here!
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Button Sizes</h3>
                <h6 class="card-subtitle">Use a classes <code>btn btn-lg btn-success</code> to quickly create a General btn.</h6>
                <div class="button-group">
                    @forelse($data['buttonsSize'] as $button)
                        <button type="button" class="btn
                                {{ isset($button->color) ? $button->color : ' btn-primary ' }}
                        {{ isset($button->size) ? $button->size : '' }}
                                waves-effect waves-light">
                            {{ isset($button->text) ? $button->text : '' }}
                        </button>
                    @empty
                        No Buttons here!
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Button Sizes with rounded</h3>
                <h6 class="card-subtitle">Use a classes <code>btn btn-lg btn-rounded btn-success</code> to create a btn.</h6>
                <div class="button-group">
                    @forelse($data['buttonsSize'] as $button)
                        <button type="button" class="btn btn-rounded
                                {{ isset($button->color) ? $button->color : ' btn-primary ' }}
                        {{ isset($button->size) ? $button->size : '' }}
                                waves-effect waves-light">
                            {{ isset($button->text) ? $button->text : '' }}
                        </button>
                    @empty
                        No Buttons here!
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<h2>ToolTips</h2>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Interactive demo</h3>
                <h6 class="card-subtitle">Just put this to any tag <code>data-toggle="tooltip" title="Default tooltip"</code></h6>
                <div class="button-box">
                    @forelse($data['tooltips'] as $tooltip)
                        <button type="button"
                                {{ isset($tooltip->data_toggle) ? 'data-toggle=' . $tooltip->data_toggle  : '' }}
                                {{ isset($tooltip->data_placement) ? 'data-placement=' . $tooltip->data_placement  : '' }}
                                {{ isset($tooltip->data_original_title) ? ' data-original-title = ' . $tooltip->data_original_title : '' }}
                                class="btn
                                    {{ isset($tooltip->color) ? $tooltip->color : ' btn-outline-primary ' }} waves-effect waves-light">
                            {{ isset($tooltip->text) ? $tooltip->text : '' }}
                        </button>
                    @empty
                        No Tooltips here!
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Interactive demo</h3>
                <h6 class="card-subtitle">Just put this to any tag <code>data-container="body" title="Popover title" data-toggle="popover" data-placement="top" data-content="..."</code></h6>
                <div class="button-box">
                    @forelse($data['popovers'] as $popover)
                        <button type="button"
                                {{ isset($popover->data_toggle) ? 'data-toggle=' . $popover->data_toggle  : '' }}
                                {{ isset($popover->data_placement) ? 'data-placement=' . $popover->data_placement  : '' }}
                                {{ isset($popover->title) ? 'title="' . $popover->title .'"' : '' }}
                                {{ isset($popover->data_original_title) ? ' data-original-title = ' . $popover->data_original_title : '' }}
                                {{ isset($popover->data_container) ? ' data-container = ' . $popover->data_container : '' }}
                                {{ isset($popover->data_content) ? ' data-content = ' . $popover->data_content : '' }}
                                class="btn
                                {{ isset($popover->color) ? $popover->color : ' btn-primary ' }} waves-effect waves-light">
                            {{ isset($popover->text) ? $popover->text : '' }}</button>
                    @empty
                        No Popovers here!
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<h2>Tabs</h2>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Dynamic Tabs</h3>
                <h6 class="card-subtitle">Use default tab with class <code>nav-tabs & tabcontent-border </code></h6>

                @component('component-library.ui-elements.tab', ['tab' => $tabs['defaultTabs'], 'tabContentBorder' => true ])@endcomponent

            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body p-b-0">
                <h4 class="card-title">Customtab Tab</h4>
                <h6 class="card-subtitle">Use default tab with class <code>customtab</code></h6> </div>
            @component('component-library.ui-elements.tab', ['tab' => $tabs['customTabs'] , 'tabContentBorder' => true, 'class' => 'customtab'])@endcomponent
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Vertical Tab</h3>
                <h6 class="card-subtitle">Use default tab with class <code>vtabs & tabs-vertical</code></h6>
                <div class="vtabs">
                    @component('component-library.ui-elements.tab',
                    [
                        'tab' => $tabs['verticalTabs'] , 'class' => 'tabs-vertical'
                    ])
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Customtab vertical Tab</h3>
                <h6 class="card-subtitle">Use default tab with class <code>vtabs, tabs-vertical & customvtab</code></h6>
                <!-- Nav tabs -->
                <div class="vtabs customvtab">
                    @component('component-library.ui-elements.tab', ['tab' => $tabs['verticalCustomTabs'] , 'class' => 'tabs-vertical'])@endcomponent
                </div>
            </div>
        </div>
    </div>
</div>

