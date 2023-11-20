@push('before-styles')

<!-- chartist CSS -->
<link href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/chartist-js/dist/chartist.min.css" rel="stylesheet">
<link href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/chartist-js/dist/chartist-init.css" rel="stylesheet">
<link href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css" rel="stylesheet">
<link href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/css-chart/css-chart.css" rel="stylesheet">

@endpush

@push('after-scripts')

<!-- chartist chart -->
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/chartist-js/dist/chartist.min.js"></script>
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.min.js"></script>
<!-- Chart JS -->
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/Chart.js/Chart.min.js"></script>
<script src="/vendor/wrappixel/monster-admin/4.2.1/monster/js/dashboard2.js"></script>

@endpush

<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<div class="row">
    <!-- Column -->
    <div class="col-md-6 col-lg-3 col-xlg-3">
        <div class="card card-inverse card-info">
            <div class="box bg-info text-center">
                <h1 class="font-light text-white">2,064</h1>
                <h6 class="text-white">Sessions</h6>
            </div>
        </div>
    </div>
    <!-- Column -->
    <div class="col-md-6 col-lg-3 col-xlg-3">
        <div class="card card-primary card-inverse">
            <div class="box text-center">
                <h1 class="font-light text-white">1,738</h1>
                <h6 class="text-white">Users</h6>
            </div>
        </div>
    </div>
    <!-- Column -->
    <div class="col-md-6 col-lg-3 col-xlg-3">
        <div class="card card-inverse card-success">
            <div class="box text-center">
                <h1 class="font-light text-white">5963</h1>
                <h6 class="text-white">Page Views</h6>
            </div>
        </div>
    </div>
    <!-- Column -->
    <div class="col-md-6 col-lg-3 col-xlg-3">
        <div class="card card-inverse card-warning">
            <div class="box text-center">
                <h1 class="font-light text-white">10%</h1>
                <h6 class="text-white">Bounce Rate</h6>
            </div>
        </div>
    </div>
</div>
<!-- Row -->
<div class="row">
    <!-- Column -->
    <div class="col-12">
        <div class="card">
            <div class="row">
                <div class="col-lg-4">
                    <div class="p-3">
                        <h2 class="font-medium text-inverse">Welcome Steave</h2>
                        <h6 class="card-subtitle">you have 4 new messages</h6>
                        <div class="message-box mt-4">
                            <div class="message-widget">
                                <!-- Message -->
                                <a href="#">
                                    <div class="user-img"> <img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/1.jpg" alt="user" class="img-circle"> <span class="profile-status online float-right"></span> </div>
                                    <div class="mail-contnet">
                                        <h5>Pavan kumar</h5> <span class="mail-desc">Lorem Ipsum is simply dummy text of the printing and type setting industry. Lorem Ipsum has been.</span> <span class="time">9:30 AM</span> </div>
                                </a>
                                <!-- Message -->
                                <a href="#">
                                    <div class="user-img"> <img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/2.jpg" alt="user" class="img-circle"> <span class="profile-status busy float-right"></span> </div>
                                    <div class="mail-contnet">
                                        <h5>Sonu Nigam</h5> <span class="mail-desc">I've sung a song! See you at</span> <span class="time">9:10 AM</span> </div>
                                </a>
                                <!-- Message -->
                                <a href="#">
                                    <div class="user-img"> <span class="round">A</span> <span class="profile-status away float-right"></span> </div>
                                    <div class="mail-contnet">
                                        <h5>Arijit Sinh</h5> <span class="mail-desc">Simply dummy text of the printing and typesetting industry.</span> <span class="time">9:08 AM</span> </div>
                                </a>
                                <!-- Message -->
                                <a href="#">
                                    <div class="user-img"> <img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/4.jpg" alt="user" class="img-circle"> <span class="profile-status offline float-right"></span> </div>
                                    <div class="mail-contnet">
                                        <h5>Pavan kumar</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:02 AM</span> </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 border-left">
                    <div class="card-body">
                        <div class="d-flex no-block align-items-center">
                            <h4 class="font-medium text-inverse">Product Calculation</h4>
                            <div class="ml-auto">
                                <ul class="list-inline">
                                    <li class="pl-0">
                                        <h6 class="text-muted"><i class="fa fa-circle mr-1 text-success"></i>2016</h6>
                                    </li>
                                    <li>
                                        <h6 class="text-muted"><i class="fa fa-circle mr-1 text-info"></i>2019</h6>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="total-revenue4" style="height: 350px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
</div>
<!-- Row -->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="float-right">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-secondary">Today</button>
                        <button type="button" class="btn btn-secondary">Week</button>
                        <button type="button" class="btn btn-secondary">Month</button>
                    </div>
                </div>
                <h4 class="card-title">Members Activity</h4>
                <h6 class="card-subtitle">what members preformance / weekly status</h6>
                <div class="table-responsive mt-5">
                    <table class="table table-hover v-middle">
                        <thead>
                        <tr>
                            <th style="width: 60px;"> Member </th>
                            <th> Name </th>
                            <th> Earnings </th>
                            <th> Posts </th>
                            <th> Reviews </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <img class="img-circle" src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/1.jpg" alt="user" width="50"> </td>
                            <td>
                                <a href="javascript:;">Govinda</a>
                            </td>
                            <td> $325 </td>
                            <td> 45 </td>
                            <td>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star-half-full text-warning"></i>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img class="img-circle" src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/2.jpg" alt="user" width="50"> </td>
                            <td>
                                <a href="javascript:;">Genelia</a>
                            </td>
                            <td> $225 </td>
                            <td> 35 </td>
                            <td>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star-half-full text-warning"></i>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img class="img-circle" src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/3.jpg" alt="user" width="50"> </td>
                            <td>
                                <a href="javascript:;">Hrithik</a>
                            </td>
                            <td> $185 </td>
                            <td> 28 </td>
                            <td>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star-half-full text-warning"></i>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img class="img-circle" src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/4.jpg" alt="user" width="50"> </td>
                            <td>
                                <a href="javascript:;">Salman</a>
                            </td>
                            <td> $125 </td>
                            <td> 25 </td>
                            <td>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star-half-full text-warning"></i>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img class="img-circle" src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/2.jpg" alt="user" width="50"> </td>
                            <td>
                                <a href="javascript:;">Genelia</a>
                            </td>
                            <td> $225 </td>
                            <td> 35 </td>
                            <td>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star-half-full text-warning"></i>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="text-center">
                        <button class="btn btn-success">Check more</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Customer Support</h4>
                <h6 class="card-subtitle">24 new support ticket request generate</h6>
            </div>
            <div class="comment-widgets">
                <!-- Comment Row -->
                <div class="d-flex flex-row comment-row">
                    <div class="p-2"><span class="round"><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/1.jpg" alt="user" width="50"></span></div>
                    <div class="comment-text w-100">
                        <h5>James Anderson</h5>
                        <p class="mb-1">Lorem Ipsum is simply dummy text of the printing and type setting industry. Lorem Ipsum has beenorem Ipsum is simply dummy text of the printing and type setting industry.</p>
                        <div class="comment-footer">
                            <span class="text-muted float-right">April 14, 2016</span>
                            <span class="label label-light-info">Pending</span>
                            <span class="action-icons">
                                                    <a href="javascript:void(0)"><i class="ti-pencil-alt"></i></a>
                                                    <a href="javascript:void(0)"><i class="ti-check"></i></a>
                                                    <a href="javascript:void(0)"><i class="ti-heart"></i></a>
                                                </span>
                        </div>
                    </div>
                </div>
                <!-- Comment Row -->
                <div class="d-flex flex-row comment-row active">
                    <div class="p-2"><span class="round"><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/2.jpg" alt="user" width="50"></span></div>
                    <div class="comment-text active w-100">
                        <h5>Michael Jorden</h5>
                        <p class="mb-1">Lorem Ipsum is simply dummy text of the printing and type setting industry. Lorem Ipsum has beenorem Ipsum is simply dummy text of the printing and type setting industry..</p>
                        <div class="comment-footer ">
                            <span class="text-muted float-right">April 14, 2016</span>
                            <span class="label label-light-success">Approved</span>
                            <span class="action-icons active">
                                                    <a href="javascript:void(0)"><i class="ti-pencil-alt"></i></a>
                                                    <a href="javascript:void(0)"><i class="icon-close"></i></a>
                                                    <a href="javascript:void(0)"><i class="ti-heart text-danger"></i></a>
                                                </span>
                        </div>
                    </div>
                </div>
                <!-- Comment Row -->
                <div class="d-flex flex-row comment-row">
                    <div class="p-2"><span class="round"><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/3.jpg" alt="user" width="50"></span></div>
                    <div class="comment-text w-100">
                        <h5>Johnathan Doeting</h5>
                        <p class="mb-1">Lorem Ipsum is simply dummy text of the printing and type setting industry. Lorem Ipsum has beenorem Ipsum is simply dummy text of the printing and type setting industry.</p>
                        <div class="comment-footer">
                            <span class="text-muted float-right">April 14, 2016</span>
                            <span class="label label-light-danger">Rejected</span>
                            <span class="action-icons">
                                                    <a href="javascript:void(0)"><i class="ti-pencil-alt"></i></a>
                                                    <a href="javascript:void(0)"><i class="ti-check"></i></a>
                                                    <a href="javascript:void(0)"><i class="ti-heart"></i></a>
                                                </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Row -->
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Today's Schedule</h4>
                <h6 class="card-subtitle">check out your daily schedule</h6>
                <div class="steamline mt-5">
                    <div class="sl-item">
                        <div class="sl-left bg-success"> <i class="ti-user"></i></div>
                        <div class="sl-right">
                            <div class="font-medium">Meeting today <span class="sl-date"> 5pm</span></div>
                            <div class="desc">you can write anything </div>
                        </div>
                    </div>
                    <div class="sl-item">
                        <div class="sl-left bg-info"><i class="fa fa-image"></i></div>
                        <div class="sl-right">
                            <div class="font-medium">Send documents to Clark</div>
                            <div class="desc">Lorem Ipsum is simply </div>
                        </div>
                    </div>
                    <div class="sl-item">
                        <div class="sl-left"> <img class="img-circle" alt="user" src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/1.jpg"> </div>
                        <div class="sl-right">
                            <div><a href="#">Gohn Doe</a> <span class="sl-date">5 minutes ago</span></div>
                            <div class="desc">Call today with gohn doe </div>
                        </div>
                    </div>
                    <div class="sl-item">
                        <div class="sl-left"> <img class="img-circle" alt="user" src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/2.jpg"> </div>
                        <div class="sl-right">
                            <div class="font-medium">Go to the Doctor <span class="sl-date">5 minutes ago</span></div>
                            <div class="desc">Contrary to popular belief</div>
                        </div>
                    </div>
                    <div class="sl-item">
                        <div class="sl-left"> <img class="img-circle" alt="user" src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/3.jpg"> </div>
                        <div class="sl-right">
                            <div><a href="#">Tiger Sroff</a> <span class="sl-date">5 minutes ago</span></div>
                            <div class="desc">Approve meeting with tiger
                                <br><a href="javascript:void(0)" class="btn mt-2 mr-1 btn-rounded btn-outline-success">Apporve</a> <a href="javascript:void(0)" class="btn mt-2 btn-rounded btn-outline-danger">Refuse</a> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block">
                    <h4 class="card-title">Sales Overview</h4>
                    <div class="ml-auto">
                        <select class="custom-select">
                            <option selected="">March</option>
                            <option value="1">February</option>
                            <option value="2">May</option>
                            <option value="3">April</option>
                        </select>
                    </div>
                </div>
                <h6 class="card-subtitle">Check the monthly sales </h6>
            </div>
            <div class="card-body bg-light">
                <div class="row">
                    <div class="col-6">
                        <h2 class="mb-0">March 2019</h2>
                        <h4 class="font-light mt-0">Report for this month</h4></div>
                    <div class="col-6 align-self-center display-6 text-info text-right">$3,690</div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover no-wrap">
                    <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>NAME</th>
                        <th>STATUS</th>
                        <th>DATE</th>
                        <th>PRICE</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="text-center">1</td>
                        <td class="txt-oflo">Elite admin</td>
                        <td><span class="label label-success label-rouded">SALE</span> </td>
                        <td class="txt-oflo">April 18, 2019</td>
                        <td><span class="text-success">$24</span></td>
                    </tr>
                    <tr>
                        <td class="text-center">2</td>
                        <td class="txt-oflo">Real Homes WP Theme</td>
                        <td><span class="label label-info label-rouded">EXTENDED</span></td>
                        <td class="txt-oflo">April 19, 2019</td>
                        <td><span class="text-info">$1250</span></td>
                    </tr>
                    <tr>
                        <td class="text-center">3</td>
                        <td class="txt-oflo">Ample Admin</td>
                        <td><span class="label label-info label-rouded">EXTENDED</span></td>
                        <td class="txt-oflo">April 19, 2019</td>
                        <td><span class="text-info">$1250</span></td>
                    </tr>
                    <tr>
                        <td class="text-center">4</td>
                        <td class="txt-oflo">Medical Pro WP Theme</td>
                        <td><span class="label label-danger label-rouded">TAX</span></td>
                        <td class="txt-oflo">April 20, 2019</td>
                        <td><span class="text-danger">-$24</span></td>
                    </tr>
                    <tr>
                        <td class="text-center">5</td>
                        <td class="txt-oflo">Hosting press html</td>
                        <td><span class="label label-warning label-rouded">SALE</span></td>
                        <td class="txt-oflo">April 21, 2019</td>
                        <td><span class="text-success">$24</span></td>
                    </tr>
                    <tr>
                        <td class="text-center">6</td>
                        <td class="txt-oflo">Digital Agency PSD</td>
                        <td><span class="label label-success label-rouded">SALE</span> </td>
                        <td class="txt-oflo">April 23, 2019</td>
                        <td><span class="text-danger">-$14</span></td>
                    </tr>
                    <tr>
                        <td class="text-center">7</td>
                        <td class="txt-oflo">Helping Hands WP Theme</td>
                        <td><span class="label label-warning label-rouded">member</span></td>
                        <td class="txt-oflo">April 22, 2019</td>
                        <td><span class="text-success">$64</span></td>
                    </tr>
                    <tr>
                        <td class="text-center">8</td>
                        <td class="txt-oflo">Ample Admin</td>
                        <td><span class="label label-info label-rouded">EXTENDED</span></td>
                        <td class="txt-oflo">April 19, 2019</td>
                        <td><span class="text-info">$1250</span></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Column -->
</div>
<!-- Row -->
<div class="row">
    <!-- col -->
    <div class="col-lg-3 col-md-6">
        <div class="card bg-info">
            <div class="card-body">
                <div id="myCarousel" class="carousel slide" data-ride="carousel">
                    <!-- Carousel items -->
                    <div class="carousel-inner">
                        <div class="carousel-item active flex-column">
                            <i class="fab fa-twitter fa-2x text-white"></i>
                            <p class="text-white">25th Jan</p>
                            <h3 class="text-white font-light">Now Get <span class="font-bold">50% Off</span><br>
                                on buy</h3>
                            <div class="text-white mt-3">
                                <i>- john doe</i>
                            </div>
                        </div>
                        <div class="carousel-item flex-column">
                            <i class="fab fa-twitter fa-2x text-white"></i>
                            <p class="text-white">25th Jan</p>
                            <h3 class="text-white font-light">Now Get <span class="font-bold">50% Off</span><br>
                                on buy</h3>
                            <div class="text-white mt-3">
                                <i>- john doe</i>
                            </div>
                        </div>
                        <div class="carousel-item flex-column">
                            <i class="fab fa-twitter fa-2x text-white"></i>
                            <p class="text-white">25th Jan</p>
                            <h3 class="text-white font-light">Now Get <span class="font-bold">50% Off</span><br>
                                on buy</h3>
                            <div class="text-white mt-3">
                                <i>- john doe</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- col -->
    <div class="col-lg-3 col-md-6">
        <div class="card bg-primary">
            <div class="card-body">
                <div id="myCarousel2" class="carousel slide vert" data-ride="carousel">
                    <!-- Carousel items -->
                    <div class="carousel-inner">
                        <div class="carousel-item active flex-column">
                            <i class="fab fa-facebook fa-2x text-white"></i>
                            <p class="text-white">25th Jan</p>
                            <h3 class="text-white">Now Get <span class="font-bold">50% Off</span><br>
                                on buy</h3>
                            <div class="text-white mt-3">
                                <i>- john doe</i>
                            </div>
                        </div>
                        <div class="carousel-item flex-column">
                            <i class="fab fa-facebook fa-2x text-white"></i>
                            <p class="text-white">25th Jan</p>
                            <h3 class="text-white">Now Get <span class="font-bold">50% Off</span><br>
                                on buy</h3>
                            <div class="text-white mt-3">
                                <i>- john doe</i>
                            </div>
                        </div>
                        <div class="carousel-item flex-column"> <i class="fab fa-facebook fa-2x text-white"></i>
                            <p class="text-white">25th Jan</p>
                            <h3 class="text-white">Now Get <span class="font-bold">50% Off</span><br>
                                on buy</h3>
                            <div class="text-white mt-3">
                                <i>- john doe</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- col -->
    <div class="col-lg-3 col-md-6">
        <div class="card bg-success">
            <div class="card-body">
                <div id="myCarousel3" class="carousel slide" data-ride="carousel">
                    <!-- Carousel items -->
                    <div class="carousel-inner">
                        <div class="carousel-item active flex-column">
                            <i class="fa fa-map-marker fa-2x text-white"></i>
                            <p class="text-white">25th Jan</p>
                            <h3 class="text-white">Now Get <span class="font-bold">50% Off</span><br>
                                on buy</h3>
                            <div>
                                <button class="btn btn-secondary b-0 waves-effect waves-light mt-3">Default</button>
                            </div>
                        </div>
                        <div class="carousel-item flex-column">
                            <i class="fa fa-map-marker fa-2x text-white"></i>
                            <p class="text-white">25th Jan</p>
                            <h3 class="text-white">Now Get <span class="font-bold">50% Off</span><br>
                                on buy</h3>
                            <div>
                                <button class="btn btn-secondary b-0 waves-effect waves-light mt-3">Default</button>
                            </div>
                        </div>
                        <div class="carousel-item flex-column">
                            <i class="fa fa-map-marker fa-2x text-white"></i>
                            <p class="text-white">25th Jan</p>
                            <h3 class="text-white">Now Get <span class="font-bold">50% Off</span><br>
                                on buy</h3>
                            <div>
                                <button class="btn btn-secondary b-0 waves-effect waves-light mt-3">Default</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- col -->
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div id="myCarousel4" class="carousel vert slide" data-ride="carousel">
                    <!-- Carousel items -->
                    <div class="carousel-inner">
                        <div class="carousel-item active flex-column">
                            <i class="fa fa-map-marker fa-2x"></i>
                            <p>25th Jan</p>
                            <h3>Now Get <span class="font-bold">50% Off</span><br>on buy</h3>
                            <div>
                                <button class="btn btn-info justify-content-start waves-effect waves-light mt-3">Default</button>
                            </div>
                        </div>
                        <div class="carousel-item flex-column">
                            <i class="fa fa-map-marker fa-2x"></i>
                            <p>25th Jan</p>
                            <h3>Now Get <span class="font-bold">50% Off</span><br>
                                on buy</h3>
                            <div>
                                <button class="btn btn-success d-inline waves-effect waves-light mt-3">Default</button>
                            </div>
                        </div>
                        <div class="carousel-item flex-column"> <i class="fa fa-map-marker fa-2x"></i>
                            <p>25th Jan</p>
                            <h3>Now Get <span class="font-bold">50% Off</span><br>
                                on buy</h3>
                            <div>
                                <button class="btn btn-primary waves-effect waves-light mt-3">Default</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- col -->
</div>
<!-- Row -->
<!-- Row -->
<div class="row">
    <div class="col-lg-6">
        <div class="card earning-widget">
            <div class="card-body">
                <div class="card-title">
                    <div class="d-flex no-block">
                        <h4 class="card-title mb-0">Total Earning</h4>
                        <div class="ml-auto">
                            <select class="custom-select">
                                <option selected="">March</option>
                                <option value="1">February</option>
                                <option value="2">May</option>
                                <option value="3">April</option>
                            </select>
                        </div>
                    </div>
                    <h2 class="mt-0">$586</h2>
                </div>
            </div>
            <div class="card-body border-top">
                <table class="table v-middle no-border">
                    <tbody>
                    <tr>
                        <td style="width:40px"><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/1.jpg" width="50" class="img-circle" alt="logo"></td>
                        <td>Andrew Simon</td>
                        <td class="text-right"><span class="label label-light-info">$2300</span></td>
                    </tr>
                    <tr>
                        <td><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/2.jpg" width="50" class="img-circle" alt="logo"></td>
                        <td>Daniel Kristeen</td>
                        <td class="text-right"><span class="label label-light-success">$3300</span></td>
                    </tr>
                    <tr>
                        <td><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/3.jpg" width="50" class="img-circle" alt="logo"></td>
                        <td>Dany John</td>
                        <td class="text-right"><span class="label label-light-primary">$4300</span></td>
                    </tr>
                    <tr>
                        <td><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/4.jpg" width="50" class="img-circle" alt="logo"></td>
                        <td>Chris gyle</td>
                        <td class="text-right"><span class="label label-light-warning">$5300</span></td>
                    </tr>
                    <tr>
                        <td><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/5.jpg" width="50" class="img-circle" alt="logo"></td>
                        <td>Opera mini</td>
                        <td class="text-right"><span class="label label-light-danger">$4567</span></td>
                    </tr>
                    <tr>
                        <td><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/6.jpg" width="50" class="img-circle" alt="logo"></td>
                        <td>Microsoft edge</td>
                        <td class="text-right"><span class="label label-light-megna">$7889</span></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Feeds</h4>
                <ul class="feeds">
                    <li>
                        <div class="bg-light-info"><i class="fas fa-bell"></i></div> You have 4 pending tasks. <span class="text-muted">Just Now</span></li>
                    <li>
                        <div class="bg-light-success"><i class="ti-server"></i></div> Server #1 overloaded.<span class="text-muted">2 Hours ago</span></li>
                    <li>
                        <div class="bg-light-warning"><i class="ti-shopping-cart"></i></div> New order received.<span class="text-muted">31 May</span></li>
                    <li>
                        <div class="bg-light-danger"><i class="ti-user"></i></div> New user registered.<span class="text-muted">30 May</span></li>
                    <li>
                        <div class="bg-light-inverse"><i class="fas fa-bell"></i></div> New Version just arrived. <span class="text-muted">27 May</span></li>
                    <li>
                        <div class="bg-light-info"><i class="fas fa-bell"></i></div> You have 4 pending tasks. <span class="text-muted">Just Now</span></li>
                    <li>
                        <div class="bg-light-danger"><i class="ti-user"></i></div> New user registered.<span class="text-muted">30 May</span></li>
                    <li>
                        <div class="bg-light-inverse"><i class="fas fa-bell"></i></div> New Version just arrived. <span class="text-muted">27 May</span></li>
                    <li>
                        <div class="bg-light-primary"><i class="ti-settings"></i></div> You have 4 pending tasks. <span class="text-muted">27 May</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================== -->
<!-- End PAge Content -->
<!-- ============================================================== -->