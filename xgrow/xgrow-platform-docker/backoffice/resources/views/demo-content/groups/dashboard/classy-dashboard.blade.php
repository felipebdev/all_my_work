@push('before-styles')

<!-- chartist CSS -->
<link href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/chartist-js/dist/chartist.min.css" rel="stylesheet">
<link href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/chartist-js/dist/chartist-init.css" rel="stylesheet">
<link href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css" rel="stylesheet">
<link href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/css-chart/css-chart.css" rel="stylesheet">

@endpush

@push('after-scripts')

<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/chartist-js/dist/chartist.min.js"></script>
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.min.js"></script>
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/sparkline/jquery.sparkline.min.js"></script>
<!-- Chart JS -->
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/echarts/echarts-all.js"></script>
<!-- Flot Charts JavaScript -->
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/flot/excanvas.js"></script>
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/flot/jquery.flot.js"></script>
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/flot/jquery.flot.time.js"></script>
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/flot.tooltip/js/jquery.flot.tooltip.min.js"></script>
<script src="/vendor/wrappixel/monster-admin/4.2.1/monster/js/dashboard3.js"></script>

@endpush

<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<!-- Row -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="row">
                <div class="col-lg-6 col-md-4">
                    <div class="card-body">
                        <h3>Android Vs iOS</h3>
                        <h6 class="card-subtitle mb-0">check the difference of OS</h6> </div>
                </div>
                <div class="col-lg-3 col-md-4 border-right align-self-center">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="col-8 p-0 align-self-center">
                                <h3 class="mb-0">31568</h3>
                                <h5 class="text-muted mb-0">Growth</h5> </div>
                            <div class="col-4 text-right">
                                <div class="round align-self-center round-success"><i class="mdi mdi-android"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 align-self-center">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="col-8 p-0 align-self-center">
                                <h3 class="mb-0">15478</h3>
                                <h5 class="text-muted mb-0">Loss</h5> </div>
                            <div class="col-4 text-right">
                                <div class="round align-self-center bg-inverse"><i class="mdi mdi-apple"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <hr class="mt-0"> </div>
            <div class="chartist-chart andvios mt-5" style="height:350px;"> </div>
        </div>
    </div>
</div>
<!-- Row -->
<div class="row">
    <!-- Column -->
    <div class="col-lg-6 col-md-6">
        <div class="card card-inverse card-primary">
            <div class="card-body">
                <div class="d-flex">
                    <div class="mr-3 align-self-center">
                        <h1 class="text-white"><i class="ti-pie-chart"></i></h1></div>
                    <div>
                        <h3 class="card-title">Bandwidth usage</h3>
                        <h6 class="card-subtitle">March  2019</h6> </div>
                </div>
                <div class="row">
                    <div class="col-5 align-self-center">
                        <font class="display-7 text-white">50 GB</font>
                    </div>
                    <div class="col-7 align-self-center">
                        <div class="usage chartist-chart" style="height:120px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-6 col-md-6">
        <div class="card card-inverse card-danger">
            <div class="card-body">
                <div class="d-flex">
                    <div class="mr-3 align-self-center">
                        <h1 class="text-white"><i class="icon-cloud-download"></i></h1></div>
                    <div>
                        <h3 class="card-title">Download count</h3>
                        <h6 class="card-subtitle">March  2019</h6> </div>
                </div>
                <div class="row">
                    <div class="col-5 align-self-center">
                        <font class="display-7 text-white">35487</font>
                    </div>
                    <div class="col-7 text-right">
                        <div class="spark-count" style="height:120px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
</div>
<!-- Row -->
<div class="row">
    <!-- Column -->
    <div class="col-lg-6 col-md-6">
        <div class="card card-body">
            <h3 class="card-title">Notification</h3>
            <div class="message-box">
                <div class="message-widget">
                    <!-- Message -->
                    <a href="#">
                        <div class="user-img"><span class="round bg-primary"><i class="mdi mdi-email"></i></span></div>
                        <div class="mail-contnet">
                            <h5>You have 3 new messages</h5> <span class="mail-desc">Daniel Kristeen, Hanna Giover, Jeffry Brown</span> <span class="time">9:30 AM</span> </div>
                    </a>
                    <!-- Message -->
                    <a href="#">
                        <div class="user-img"><span class="round bg-danger"><i class="mdi mdi-earth"></i></span></div>
                        <div class="mail-contnet">
                            <h5>Newsfeed available </h5> <span class="mail-desc">Todays headlines : Breakdancing Grandma Proves ..</span> <span class="time">9:10 AM</span> </div>
                    </a>
                    <!-- Message -->
                    <a href="#">
                        <div class="user-img"> <span class="round bg-success"><i class="mdi mdi-currency-usd"></i></span></div>
                        <div class="mail-contnet">
                            <h5>2 Invoices to pay</h5> <span class="mail-desc">$3500 from Krishnan, $2000 from Akhil</span> <span class="time">9:08 AM</span> </div>
                    </a>
                    <!-- Message -->
                    <a href="#">
                        <div class="user-img"><span class="round"><i class="mdi mdi-comment-check-outline"></i></span></div>
                        <div class="mail-contnet">
                            <h5>15 New comments</h5> <span class="mail-desc">Jhonny : Hey this stuff is awesome and how can i ..</span> <span class="time">9:02 AM</span> </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <div class="col-lg-6 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block">
                    <h3 class="card-title">Visit source</h3>
                    <div class="ml-auto">
                        <select class="custom-select">
                            <option selected="">January</option>
                            <option value="1">February</option>
                            <option value="2">March</option>
                            <option value="3">April</option>
                        </select>
                    </div>
                </div>
                <div id="m-piechart" style="width:100%; height:278px"></div>
                <div class="text-center">
                    <ul class="list-inline mt-3">
                        <li>
                            <h6 class="text-muted"><i class="fa fa-circle mr-1 text-success"></i>Mobile</h6> </li>
                        <li>
                            <h6 class="text-muted"><i class="fa fa-circle mr-1 text-primary"></i>Desktop</h6> </li>
                        <li>
                            <h6 class="text-muted"><i class="fa fa-circle mr-1 text-danger"></i>Tablet</h6> </li>
                        <li>
                            <h6 class="text-muted"><i class="fa fa-circle mr-1 text-muted"></i>Other</h6> </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Row -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <ul class="list-inline float-right">
                    <li>
                        <h6 class="text-muted"><i class="fa fa-circle mr-1 text-success"></i>Free</h6> </li>
                    <li>
                        <h6 class="text-muted"><i class="fa fa-circle mr-1 text-primary"></i>Premium</h6> </li>
                </ul>
                <h3 class="card-title">Download Count</h3>
                <h6 class="card-subtitle">you can check the count</h6>
                <div class="download-state chartist-chart" style="height:300px"></div>
            </div>
        </div>
    </div>
</div>
<!-- Row -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="row">
                <!-- Column -->
                <div class="col-lg-5 col-xlg-3 col-md-6">
                    <div class="card-body">
                        <h3 class="card-title mb-4">Reviews</h3>
                        <span class="mt-5 display-6">31560</span>
                        <h6 class="card-subtitle mb-4">April the product got 234 reviews</h6>
                        <a href="javascript:void(0)" class="mr-3"><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/1.jpg" alt="user" class="img-circle" width="50" /></a>
                        <a href="javascript:void(0)" class="mr-3"><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/2.jpg" alt="user"  class="img-circle" width="50" /></a>
                        <a href="javascript:void(0)" class="mr-3"><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/3.jpg" alt="user"  class="img-circle" width="50" /></a>
                        <a href="javascript:void(0)" class="mr-3"><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/4.jpg" alt="user" class="img-circle" width="50" /></a>
                        <div class="clearfix"></div>
                        <button type="button" class="btn btn-success mt-5">Read reviews</button>
                    </div>
                </div>
                <!-- Column -->
                <div class="col-lg-7 col-xlg-9 col-md-6 border-left pl-0">
                    <ul class="product-review">
                        <li>
                            <span class="text-muted display-5"><i class="mdi mdi-emoticon-cool"></i></span>
                            <div class="dl ml-2">
                                <h3 class="card-title">Positive Reviews</h3>
                                <h6 class="card-subtitle">25547 Reviews</h6>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 65%; height:6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </li>
                        <li>
                            <span class="text-muted display-5"><i class="mdi mdi-emoticon-sad"></i></span>
                            <div class="dl ml-2">
                                <h3 class="card-title">Negative Reviews</h3>
                                <h6 class="card-subtitle">5478 Reviews</h6>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 15%; height:6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </li>
                        <li>
                            <span class="text-muted display-5"><i class="mdi mdi-emoticon-neutral"></i></span>
                            <div class="dl ml-2">
                                <h3 class="card-title">Neutral Reviews</h3>
                                <h6 class="card-subtitle">457 Reviews</h6>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 35%; height:6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </li>
                    </ul>
                </div>
                <!-- Column -->
            </div>
        </div>
    </div>
</div>
<!-- Row -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block">
                    <h4 class="card-title">Products Availability</h4>
                    <div class="ml-auto">
                        <select class="custom-select">
                            <option selected="">Electronics</option>
                            <option value="1">Kitchen</option>
                            <option value="2">Crocory</option>
                            <option value="3">Wooden</option>
                        </select>
                    </div>
                </div>
                <h6 class="card-subtitle">March  2019</h6>
                <div class="table-responsive">
                    <table class="table stylish-table">
                        <thead>
                        <tr>
                            <th style="width:90px;">Product</th>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><span class="round"><i class="ti-shopping-cart"></i></span></td>
                            <td>
                                <h6><a href="javascript:void(0)" class="link">Apple iPhone 6  Space Grey, 16 GB</a></h6><small class="text-muted">Product id : MI5457 </small></td>
                            <td>
                                <h5>357</h5></td>
                            <td>
                                <h5>$435</h5></td>
                        </tr>
                        <tr>
                            <td><span class="round bg-success"><i class="ti-shopping-cart"></i></span></td>
                            <td>
                                <h6><a href="javascript:void(0)" class="link">Fossil Marshall For Men Black Smartwatch</a></h6><small class="text-muted">Product id : MI5457 </small></td>
                            <td>
                                <h5>357</h5></td>
                            <td>
                                <h5>$435</h5></td>
                        </tr>
                        <tr>
                            <td><span class="round bg-danger"><i class="ti-shopping-cart"></i></span></td>
                            <td>
                                <h6><a href="javascript:void(0)" class="link">Sony Bravia 80cm - 32 HD Ready LED TV</a></h6><small class="text-muted">Product id : MI5457 </small></td>
                            <td>
                                <h5>357</h5></td>
                            <td>
                                <h5>$435</h5></td>
                        </tr>
                        <tr>
                            <td><span class="round bg-primary"><i class="ti-shopping-cart"></i></span></td>
                            <td>
                                <h6><a href="javascript:void(0)" class="link">Panasonic P75 Champagne Gold, 8 GB</a></h6><small class="text-muted">Product id : MI5457 </small></td>
                            <td>
                                <h5>357</h5></td>
                            <td>
                                <h5>$435</h5></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Row -->
<div class="row">
    <!-- Column -->
    <div class="col-md-12 col-lg-4 col-xlg-3">
        <div class="card"> <img class="card-img" src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/background/socialbg.jpg" alt="Card image">
            <div class="card-img-overlay card-inverse social-profile d-flex ">
                <div class="align-self-center"> <img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/1.jpg" alt="user" class="img-circle" width="100">
                    <h4 class="card-title">James Anderson</h4>
                    <h6 class="card-subtitle">@jamesandre</h6>
                    <p class="text-white">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt </p>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <div class="col-md-12 col-lg-8 col-xlg-9">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Experiances</h3>
                <div class="table-responsive">
                    <table class="table mb-0 mt-5 no-border no-wrap">
                        <tbody>
                        <tr>
                            <td style="width:90px;"><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/browser/photoshop.jpg" alt="photoshop" /></td>
                            <td style="width:200px;">
                                <h4 class="card-title">Photoshop</h4>
                                <h6 class="card-subtitle">This is a sample text</h6></td>
                            <td class="vm">
                                <div class="progress" style="width: 390px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 65%; height:6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:90px;"><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/browser/sketch.jpg" alt="sketch" /></td>
                            <td style="width:200px;">
                                <h4 class="card-title">Sketch</h4>
                                <h6 class="card-subtitle">This is a sample text</h6></td>
                            <td class="vm">
                                <div class="progress" style="width: 390px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 45%; height:6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-body border-top">
                <div class="row">
                    <div class="col-4 text-center">
                        <h2 class="text-muted">5486</h2>
                        <h6 class="text-muted">Projects</h6>
                    </div>
                    <div class="col-4 text-center">
                        <h2 class="text-muted">987</h2>
                        <h6 class="text-muted">Winning  Entries</h6>
                    </div>
                    <div class="col-4 align-self-center text-center">
                        <button type="button" class="btn btn-success">Hire me</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Row -->
<div class="row">
    <!-- Column -->
    <div class="col-lg-4 col-xlg-3 col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="mt-4 text-center"> <img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/5.jpg" alt="user" class="img-circle" width="150" />
                    <h4 class="card-title mt-2">Hanna Gover</h4>
                    <h6 class="card-subtitle">Accoubts Manager Amix corp</h6>
                    <div class="row text-center justify-content-md-center">
                        <div class="col-4"><a href="javascript:void(0)" class="link"><i class="icon-people"></i> <span class="font-medium">254</span></a></div>
                        <div class="col-4"><a href="javascript:void(0)" class="link"><i class="icon-picture"></i> <span class="font-medium">54</span></a></div>
                    </div>
                </div>
            </div>
            <div>
                <hr> </div>
            <div class="card-body"> <small class="text-muted">Email address </small>
                <h6>hannagover@gmail.com</h6> <small class="text-muted p-t-30 db">Phone</small>
                <h6>+91 654 784 547</h6> <small class="text-muted p-t-30 db">Address</small>
                <h6>71 Pilgrim Avenue Chevy Chase, MD 20815</h6>
                <div class="map-box">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d470029.1604841957!2d72.29955005258641!3d23.019996818380896!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e848aba5bd449%3A0x4fcedd11614f6516!2sAhmedabad%2C+Gujarat!5e0!3m2!1sen!2sin!4v1493204785508" class="w-100" height="150" style="border:0" allowfullscreen></iframe>
                </div> <small class="text-muted p-t-30 db">Social Profile</small>
                <br/>
                <button class="btn btn-circle btn-secondary"><i class="fab fa-facebook"></i></button>
                <button class="btn btn-circle btn-secondary"><i class="fab fa-twitter"></i></button>
                <button class="btn btn-circle btn-secondary"><i class="fab fa-youtube"></i></button>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-8 col-xlg-9 col-md-12">
        <div class="card">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs profile-tab" role="tablist">
                <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#home" role="tab">Timeline</a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#profile" role="tab">Profile</a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Settings</a> </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="home" role="tabpanel">
                    <div class="card-body">
                        <div class="profiletimeline">
                            <div class="sl-item">
                                <div class="sl-left"> <img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/1.jpg" alt="user" class="img-circle" /> </div>
                                <div class="sl-right">
                                    <div><a href="#" class="link">John Doe</a> <span class="sl-date">5 minutes ago</span>
                                        <p>assign a new task <a href="#"> Design weblayout</a></p>
                                        <div class="row">
                                            <div class="col-lg-3 col-md-6 mb-3"><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/big/img1.jpg" alt="user" class="img-responsive radius" /></div>
                                            <div class="col-lg-3 col-md-6 mb-3"><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/big/img2.jpg" alt="user" class="img-responsive radius" /></div>
                                            <div class="col-lg-3 col-md-6 mb-3"><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/big/img3.jpg" alt="user" class="img-responsive radius" /></div>
                                            <div class="col-lg-3 col-md-6 mb-3"><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/big/img4.jpg" alt="user" class="img-responsive radius" /></div>
                                        </div>
                                        <div class="like-comm"> <a href="javascript:void(0)" class="link mr-2">2 comment</a> <a href="javascript:void(0)" class="link mr-2"><i class="fa fa-heart text-danger"></i> 5 Love</a> </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="sl-item">
                                <div class="sl-left"> <img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/2.jpg" alt="user" class="img-circle" /> </div>
                                <div class="sl-right">
                                    <div> <a href="#" class="link">John Doe</a> <span class="sl-date">5 minutes ago</span>
                                        <div class="mt-3 row">
                                            <div class="col-md-3 col-xs-12"><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/big/img1.jpg" alt="user" class="img-responsive radius" /></div>
                                            <div class="col-md-9 col-xs-12">
                                                <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. </p> <a href="#" class="btn btn-success"> Design weblayout</a></div>
                                        </div>
                                        <div class="like-comm mt-3"> <a href="javascript:void(0)" class="link mr-2">2 comment</a> <a href="javascript:void(0)" class="link mr-2"><i class="fa fa-heart text-danger"></i> 5 Love</a> </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="sl-item">
                                <div class="sl-left"> <img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/3.jpg" alt="user" class="img-circle" /> </div>
                                <div class="sl-right">
                                    <div><a href="#" class="link">John Doe</a> <span class="sl-date">5 minutes ago</span>
                                        <p class="mt-2"> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet. Duis sagittis ipsum. Praesent mauris. Fusce nec tellus sed augue semper </p>
                                    </div>
                                    <div class="like-comm mt-3"> <a href="javascript:void(0)" class="link mr-2">2 comment</a> <a href="javascript:void(0)" class="link mr-2"><i class="fa fa-heart text-danger"></i> 5 Love</a> </div>
                                </div>
                            </div>
                            <hr>
                            <div class="sl-item">
                                <div class="sl-left"> <img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/4.jpg" alt="user" class="img-circle" /> </div>
                                <div class="sl-right">
                                    <div><a href="#" class="link">John Doe</a> <span class="sl-date">5 minutes ago</span>
                                        <blockquote class="mt-2">
                                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt
                                        </blockquote>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--second tab-->
                <div class="tab-pane" id="profile" role="tabpanel">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-xs-6 border-right"> <strong>Full Name</strong>
                                <br>
                                <p class="text-muted">Johnathan Deo</p>
                            </div>
                            <div class="col-md-3 col-xs-6 border-right"> <strong>Mobile</strong>
                                <br>
                                <p class="text-muted">(123) 456 7890</p>
                            </div>
                            <div class="col-md-3 col-xs-6 border-right"> <strong>Email</strong>
                                <br>
                                <p class="text-muted">johnathan@admin.com</p>
                            </div>
                            <div class="col-md-3 col-xs-6"> <strong>Location</strong>
                                <br>
                                <p class="text-muted">London</p>
                            </div>
                        </div>
                        <hr>
                        <p class="mt-4">Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.</p>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries </p>
                        <p>It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                        <h4 class="font-medium mt-4">Skill Set</h4>
                        <hr>
                        <h5 class="mt-4">Wordpress <span class="float-right">80%</span></h5>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width:80%; height:6px;"> <span class="sr-only">50% Complete</span> </div>
                        </div>
                        <h5 class="mt-4">HTML 5 <span class="float-right">90%</span></h5>
                        <div class="progress">
                            <div class="progress-bar bg-info" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width:90%; height:6px;"> <span class="sr-only">50% Complete</span> </div>
                        </div>
                        <h5 class="mt-4">jQuery <span class="float-right">50%</span></h5>
                        <div class="progress">
                            <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:50%; height:6px;"> <span class="sr-only">50% Complete</span> </div>
                        </div>
                        <h5 class="mt-4">Photoshop <span class="float-right">70%</span></h5>
                        <div class="progress">
                            <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:70%; height:6px;"> <span class="sr-only">50% Complete</span> </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="settings" role="tabpanel">
                    <div class="card-body">
                        <form class="form-horizontal form-material">
                            <div class="form-group">
                                <label class="col-md-12">Full Name</label>
                                <div class="col-md-12">
                                    <input type="text" placeholder="Johnathan Doe" class="form-control form-control-line">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="example-email" class="col-md-12">Email</label>
                                <div class="col-md-12">
                                    <input type="email" placeholder="johnathan@admin.com" class="form-control form-control-line" name="example-email" id="example-email">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Password</label>
                                <div class="col-md-12">
                                    <input type="password" value="password" class="form-control form-control-line">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Phone No</label>
                                <div class="col-md-12">
                                    <input type="text" placeholder="123 456 7890" class="form-control form-control-line">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Message</label>
                                <div class="col-md-12">
                                    <textarea rows="5" class="form-control form-control-line"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-12">Select Country</label>
                                <div class="col-sm-12">
                                    <select class="form-control form-control-line">
                                        <option>London</option>
                                        <option>India</option>
                                        <option>Usa</option>
                                        <option>Canada</option>
                                        <option>Thailand</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button class="btn btn-success">Update Profile</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
</div>
<!-- Row -->
<!-- ============================================================== -->
<!-- End PAge Content -->
<!-- ============================================================== -->
