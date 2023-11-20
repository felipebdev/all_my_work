<h2>Cards</h2>
<h3 class="d-inline">Card with Image
    <a href="#code1" data-toggle="collapse">
        <i class="fa fa-code" data-toggle="tooltip" title="Get code"></i>
    </a>
</h3>
<p class="text-muted m-t-0">For the code click on above code icon</p>
<div class="row">
    <div class="col-lg-3 col-md-6">
        @component('component-library.ui-elements.ui-card',
        [
            'url'           => '/vendor/wrappixel/monster-admin/4.2.1/assets/images/big/img1.jpg',
            'alt'           => 'Card image cap',
            'title'         => 'Card title',
            'action'        => '#',
            'text'          => "Some quick example text to build on the card title and make up the bulk of the card's content.",
            'button_text'   => 'Go somewhere',
        ])
        @endcomponent
    </div>

    <div class="col-lg-3 col-md-6">
        @component('component-library.ui-elements.ui-card',
        [
            'url'           => '/vendor/wrappixel/monster-admin/4.2.1/assets/images/big/img2.jpg',
            'alt'           => 'Card image cap',
            'title'         => 'Card title',
            'action'        => '#',
            'text'          => "Some quick example text to build on the card title and make up the bulk of the card's content.",
            'button_text'   => 'Go somewhere',
        ])
        @endcomponent
    </div>

    <div class="col-lg-3 col-md-6">
        @component('component-library.ui-elements.ui-card',
        [
            'url'           => '/vendor/wrappixel/monster-admin/4.2.1/assets/images/big/img3.jpg',
            'alt'           => 'Card image cap',
            'title'         => 'Card title',
            'action'        => '#',
            'text'          => "Some quick example text to build on the card title and make up the bulk of the card's content.",
            'button_text'   => 'Go somewhere',
        ])
        @endcomponent
    </div>

    <div class="col-lg-3 col-md-6 img-responsive">
        @component('component-library.ui-elements.ui-card',
        [
            'url'           => '/vendor/wrappixel/monster-admin/4.2.1/assets/images/big/img4.jpg',
            'alt'           => 'Card image cap',
            'title'         => 'Card title',
            'action'        => '#',
            'text'          => "Some quick example text to build on the card title and make up the bulk of the card's content.",
            'button_text'   => 'Go somewhere',
        ])
        @endcomponent
    </div>
</div>

<h2>User Cards</h2>
<div class="row el-element-overlay">
    <div class="col-md-12">
        <h3 class="card-title">Fade-in effect</h3>
        <h6 class="card-subtitle m-b-20 text-muted">
            You can use by default <code>el-overlay</code>
        </h6>
    </div>
    <div class="col-lg-3 col-md-6">
        @component('component-library.ui-elements.ui-user-card',
        [
                'url'       => '/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/1.jpg',
                'alt'       => 'user',
                'action'    => '#',
                'title'     => 'Genelia Deshmukh',
                'subtitle'  => 'Managing Director',
        ])
        @endcomponent
    </div>
    <div class="col-lg-3 col-md-6">
        @component('component-library.ui-elements.ui-user-card',
        [
            'url'       => '/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/2.jpg',
            'alt'       => 'user',
            'action'    => '#',
            'title'     => 'Genelia Deshmukh',
            'subtitle'  => 'Managing Director',
        ])
        @endcomponent
    </div>
    <div class="col-lg-3 col-md-6">
        @component('component-library.ui-elements.ui-user-card',
        [
            'url'       => '/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/3.jpg',
            'alt'       => 'user',
            'action'    => '#',
            'title'     => 'Genelia Deshmukh',
            'subtitle'  => 'Managing Director',
        ])
        @endcomponent
    </div>
    <div class="col-lg-3 col-md-6">
        @component('component-library.ui-elements.ui-user-card',
        [
            'url'       => '/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/4.jpg',
            'alt'       => 'user',
            'action'    => '#',
            'title'     => 'Genelia Deshmukh',
            'subtitle'  => 'Managing Director',
       ])
        @endcomponent
    </div>
</div>
<div class="row el-element-overlay">
    <div class="col-md-12">
        <h3 class="card-title">Scroll down effect</h3>
        <h6 class="card-subtitle m-b-20 text-muted">You can use scroll down effect <code>el-overlay scrl-dwn</code></h6>
    </div>
    <div class="col-lg-3 col-md-6">
        @component('component-library.ui-elements.ui-user-card',
        [
            'url'       => '/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/5.jpg',
            'alt'       => 'user',
            'action'    => '#',
            'title'     => 'Genelia Deshmukh',
            'subtitle'  => 'Managing Director',
            'effect'    => 'scrl-dwn',
        ])
        @endcomponent
    </div>
    <div class="col-lg-3 col-md-6">
        @component('component-library.ui-elements.ui-user-card',
        [
            'url'       => '/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/6.jpg',
            'alt'       => 'user',
            'action'    => '#',
            'title'     => 'Genelia Deshmukh',
            'subtitle'  => 'Managing Director',
            'effect'    => 'scrl-dwn',
        ])
        @endcomponent
    </div>
    <div class="col-lg-3 col-md-6">
        @component('component-library.ui-elements.ui-user-card',
        [
            'url'       => '/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/7.jpg',
            'alt'       => 'user',
            'action'    => '#',
            'title'     => 'Genelia Deshmukh',
            'subtitle'  => 'Managing Director',
            'effect'    => 'scrl-dwn',
        ])
        @endcomponent
    </div>
    <div class="col-lg-3 col-md-6">
        @component('component-library.ui-elements.ui-user-card',
        [
            'url'       => '/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/8.jpg',
            'alt'       => 'user',
            'action'    => '#',
            'title'     => 'Genelia Deshmukh',
            'subtitle'  => 'Managing Director',
            'effect'    => 'scrl-dwn',
        ])
        @endcomponent
    </div>
</div>
<div class="row el-element-overlay">
    <div class="col-md-12">
        <h4 class="card-title">Scroll up effect</h4>
        <h6 class="card-subtitle m-b-20 text-muted">You can use by default <code>el-overlay scrl-up</code></h6></div>
    <div class="col-lg-3 col-md-6">
        @component('component-library.ui-elements.ui-user-card',
        [
            'url'       => '/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/1.jpg',
            'alt'       => 'user',
            'action'    => '#',
            'title'     => 'Genelia Deshmukh',
            'subtitle'  => 'Managing Director',
            'effect'    => 'scrl-up',
        ])
        @endcomponent
    </div>
    <div class="col-lg-3 col-md-6">
        @component('component-library.ui-elements.ui-user-card',
        [
            'url'       => '/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/2.jpg',
            'alt'       => 'user',
            'action'    => '#',
            'title'     => 'Genelia Deshmukh',
            'subtitle'  => 'Managing Director',
            'effect'    => 'scrl-up',
        ])
        @endcomponent
    </div>
    <div class="col-lg-3 col-md-6">
        @component('component-library.ui-elements.ui-user-card',
         [
            'url'       => '/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/3.jpg',
            'alt'       => 'user',
            'action'    => '#',
            'title'     => 'Genelia Deshmukh',
            'subtitle'  => 'Managing Director',
            'effect'    => 'scrl-up',
         ])
        @endcomponent
    </div>
    <div class="col-lg-3 col-md-6">
        @component('component-library.ui-elements.ui-user-card',
         [
            'url'       => '/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/4.jpg',
            'alt'       => 'user',
            'action'    => '#',
            'title'     => 'Genelia Deshmukh',
            'subtitle'  => 'Managing Director',
            'effect'    => 'scrl-up',
         ])
        @endcomponent
    </div>
</div>

<h2>Buttons</h2>
<div class="row">
    <div class="col-lg-12 col-xlg-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">General Buttons</h3>
                <h6 class="card-subtitle">Use a classes <code>btn btn-success</code> to quickly create a General btn.
                </h6>
                <div class="button-group">

                    @component('component-library.ui-elements.button',['text' => 'Primary'])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color' => 'btn-secondary',
                        'text'  => 'Secondary',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color' => 'btn-success',
                        'text'  => 'Success',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color' => 'btn-info',
                        'text'  => 'Info',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color' => 'btn-warning',
                        'text'  => 'Warning',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color' => 'btn-danger',
                        'text'  => 'Danger',
                    ])@endcomponent
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-xlg-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Button with outline</h3>
                <h6 class="card-subtitle">
                    Use a classes <code>btn btn-outline-success</code> to quickly create a General btn.</h6>
                <div class="button-group">

                    @component('component-library.ui-elements.button',
                        [
                            'color' => 'btn-outline-primary',
                            'text'  => 'Primary',
                        ]
                    )@endcomponent

                    @component('component-library.ui-elements.button',
                        [
                            'color' => 'btn-outline-secondary',
                            'text'  => 'Secondary',
                        ]
                    )@endcomponent

                    @component('component-library.ui-elements.button',
                        [
                            'color' => 'btn-outline-success',
                            'text'  => 'Success',
                        ]
                    )@endcomponent

                    @component('component-library.ui-elements.button',
                        [
                            'color' => 'btn-outline-info',
                            'text'  => 'Info',
                        ]
                    )@endcomponent

                    @component('component-library.ui-elements.button',
                        [
                            'color' => 'btn-outline-warning',
                            'text'  => 'Warning',
                        ]
                    )@endcomponent

                    @component('component-library.ui-elements.button',
                        [
                            'color' => 'btn-outline-danger',
                            'text'  => 'Danger',
                        ]
                    )@endcomponent

                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-xlg-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Rounded Buttons</h3>
                <h6 class="card-subtitle">Use a classes <code>btn btn-rounded btn-success</code> to quickly create a
                    General btn.</h6>
                <div class="button-group">

                    @component('component-library.ui-elements.button',
                    [
                        'color'     => 'btn-primary',
                        'rounded'   => true,
                        'text'      => 'Primary',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color'     => 'btn-secondary',
                        'rounded'   => true,
                        'text'      => 'Secondary',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color'     => 'btn-success',
                        'rounded'   => true,
                        'text'      => 'Success',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color'     => 'btn-info',
                        'rounded'   => true,
                        'text'      => 'Info',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color'     => 'btn-warning',
                        'rounded'   => true,
                        'text'      => 'Warning',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color'     => 'btn-danger',
                        'rounded'   => true,
                        'text'      => 'Danger',
                    ])@endcomponent


                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-xlg-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Rounded outlined Buttons</h3>
                <h6 class="card-subtitle">Use a classes <code>btn btn-rounded btn-outline-success</code> to quickly
                    create a General btn.</h6>
                <div class="button-group">

                    @component('component-library.ui-elements.button',
                    [
                        'color'     => 'btn-outline-primary',
                        'rounded'   => true,
                        'text'      => 'Primary',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color'     => 'btn-outline-secondary',
                        'rounded'   => true,
                        'text'      => 'Secondary',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color'     => 'btn-outline-success',
                        'rounded'   => true,
                        'text'      => 'Success',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color'     => 'btn-outline-info',
                        'rounded'   => true,
                        'text'      => 'Info',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color'     => 'btn-outline-warning',
                        'rounded'   => true,
                        'text'      => 'Warning',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color'     => 'btn-outline-danger',
                        'rounded'   => true,
                        'text'      => 'Danger',
                    ])@endcomponent

                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Button Sizes</h3>
                <h6 class="card-subtitle">Use a classes <code>btn btn-lg btn-success</code> to quickly create a General
                    btn.</h6>
                <div class="button-group">

                    @component('component-library.ui-elements.button',
                    [
                        'color' => 'btn-primary',
                        'size' => 'btn-lg',
                        'text' => 'Large .btn-lg',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color' => 'btn-secondary',
                        'text' => 'Normal .btn',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color' => 'btn-primary',
                        'size' => 'btn-sm',
                        'text' => 'Small .btn-sm',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color' => 'btn-primary',
                        'size' => 'btn-xs',
                        'text' => 'Tiny .btn-xs',
                    ])@endcomponent

                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Button Sizes with rounded</h3>
                <h6 class="card-subtitle">Use a classes <code>btn btn-lg btn-rounded btn-success</code> to create a btn.
                </h6>
                <div class="button-group">
                    @component('component-library.ui-elements.button',
                    [
                        'color'     => 'btn-primary',
                        'rounded'   => true,
                        'size'      => 'btn-lg',
                        'text'      => 'Large .btn-lg',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color'     => 'btn-secondary',
                        'rounded'   => true,
                        'text'      => 'Normal .btn',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color'     => 'btn-primary',
                        'rounded'   => true,
                        'size'      => 'btn-sm',
                        'text'      => 'Small .btn-sm',
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color'     => 'btn-primary',
                        'rounded'   => true,
                        'size'      => 'btn-xs',
                        'text'      => 'Tiny .btn-xs',
                    ])@endcomponent


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
                <h6 class="card-subtitle">Just put this to any tag <code>data-toggle="tooltip" title="Default
                        tooltip"</code></h6>
                <div class="button-box">

                    @component('component-library.ui-elements.button',
                    [
                        'color'                 => 'btn-outline-secondary',
                        'data_toggle'           => 'tooltip',
                        'data_placement'        => 'top',
                        'data_original_title'   => 'Tooltip',
                        'text'                  => 'Tooltip on top'
                    ]
                    )@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color'                 => 'btn-outline-secondary',
                        'data_toggle'           => 'tooltip',
                        'data_placement'        => 'right',
                        'data_original_title'   => 'Tooltip',
                        'text'                  => 'Tooltip on right'
                    ])
                    @endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color'                 => 'btn-outline-secondary',
                        'data_toggle'           => 'tooltip',
                        'data_placement'        => 'bottom',
                        'data_original_title'   => 'Tooltip',
                        'text'                  => 'Tooltip on bottom'
                    ])@endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color'                 => 'btn-outline-secondary',
                        'data_toggle'           => 'tooltip',
                        'data_placement'        => 'left',
                        'data_original_title'   => 'Tooltip',
                        'text'                  => 'Tooltip on left'
                    ])@endcomponent

                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Interactive demo</h3>
                <h6 class="card-subtitle">Just put this to any tag <code>data-container="body" title="Popover title"
                        data-toggle="popover" data-placement="top" data-content="..."</code></h6>
                <div class="button-box">

                    @component('component-library.ui-elements.button',
                    [
                        'color'             => 'btn-secondary',
                        'data_toggle'       => 'popover',
                        'data_placement'    => 'top',
                        'data_container'    => 'body',
                        'title'             => 'Popover title',
                        'text'              => "Popover on top"
                    ])

                        @slot('data_content')
                            Vivamus sagittis lacus vel augue laoreet rutrum faucibus.
                        @endslot

                    @endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color'             => 'btn-secondary',
                        'data_toggle'       => 'popover',
                        'data_placement'    => 'right',
                        'data_container'    => 'body',
                        'title'             => 'Popover title',
                        'text'              => 'Popover on right'
                    ])

                        @slot('data_content')
                            Vivamus sagittis lacus vel augue laoreet rutrum faucibus.
                        @endslot

                    @endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color'             => 'btn-secondary',
                        'data_toggle'       => 'popover',
                        'data_placement'    => 'bottom',
                        'data_container'    => 'body',
                        'title'             => 'Popover title',
                        'text'              => 'Popover on bottom'
                    ])

                        @slot('data_content')
                            Vivamus sagittis lacus vel augue laoreet rutrum faucibus.
                        @endslot

                    @endcomponent

                    @component('component-library.ui-elements.button',
                    [
                        'color'             => 'btn-secondary',
                        'data_toggle'       => 'popover',
                        'data_placement'    => 'left',
                        'data_container'    => 'body',
                        'title'             => 'Popover title',
                        'text'              => 'Popover on left'
                    ])

                        @slot('data_content')
                            Vivamus sagittis lacus vel augue laoreet rutrum faucibus.
                        @endslot

                    @endcomponent

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

                @component('component-library.ui-elements.tab',
                [
                    'tab' => $tabs['defaultTabs'],
                    'tabContentBorder' => true,
                ])@endcomponent

            </div>

        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body p-b-0">
                <h4 class="card-title">Customtab Tab</h4>
                <h6 class="card-subtitle">Use default tab with class <code>customtab</code></h6>
            </div>

            @component('component-library.ui-elements.tab',
            [
                'tab' => $tabs['customTabs'],
                'tabContentBorder' => true,
                'class' => 'customtab',
            ])@endcomponent

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
                        'tab' => $tabs['verticalTabs'],
                        'class' => 'tabs-vertical',
                    ])@endcomponent

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

                    @component('component-library.ui-elements.tab',
                    [
                        'tab' => $tabs['verticalCustomTabs'],
                        'class' => 'tabs-vertical',
                    ])@endcomponent

                </div>
            </div>
        </div>
    </div>
</div>
