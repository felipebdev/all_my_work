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
<script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/echarts/echarts-all.js"></script>
<script src="/vendor/wrappixel/monster-admin/4.2.1/monster/js/dashboard5.js"></script>

@endpush

<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<!-- Row -->
<div class="row">
    <!-- Column -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <ul class="list-inline float-right">
                    <li>
                        <h6 class="text-muted"><i class="fa fa-circle mr-1 text-success"></i>2016</h6>
                    </li>
                    <li>
                        <h6 class="text-muted"><i class="fa fa-circle mr-1 text-info"></i>2019</h6>
                    </li>
                </ul>
                <h4 class="card-title">Total Revenue</h4>
                <div class="clear"></div>
                <div class="total-revenue" style="height: 240px;"></div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <div class="col-lg-6">
        <!-- Row -->
        <div class="row">
            <!-- Column -->
            <div class="col-sm-6">
                <div class="card card-body">
                    <!-- Row -->
                    <div class="row pt-2 pb-2">
                        <!-- Column -->
                        <div class="col pr-0">
                            <h1 class="font-light">86</h1>
                            <h6 class="text-muted">New Clients</h6></div>
                        <!-- Column -->
                        <div class="col text-right align-self-center">
                            <div data-label="20%" class="css-bar mb-0 css-bar-primary css-bar-20"><i class="mdi mdi-account-circle"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <div class="col-sm-6">
                <div class="card card-body">
                    <!-- Row -->
                    <div class="row pt-2 pb-2">
                        <!-- Column -->
                        <div class="col pr-0">
                            <h1 class="font-light">248</h1>
                            <h6 class="text-muted">All Projects</h6></div>
                        <!-- Column -->
                        <div class="col text-right align-self-center">
                            <div data-label="30%" class="css-bar mb-0 css-bar-danger css-bar-20"><i class="mdi mdi-briefcase-check"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <div class="col-sm-6">
                <div class="card card-body">
                    <!-- Row -->
                    <div class="row pt-2 pb-2">
                        <!-- Column -->
                        <div class="col pr-0">
                            <h1 class="font-light">352</h1>
                            <h6 class="text-muted">New Items</h6></div>
                        <!-- Column -->
                        <div class="col text-right align-self-center">
                            <div data-label="40%" class="css-bar mb-0 css-bar-warning css-bar-40"><i class="mdi mdi-star-circle"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <div class="col-sm-6">
                <div class="card card-body">
                    <!-- Row -->
                    <div class="row pt-2 pb-2">
                        <!-- Column -->
                        <div class="col pr-0">
                            <h1 class="font-light">159</h1>
                            <h6 class="text-muted">Invoices</h6></div>
                        <!-- Column -->
                        <div class="col text-right align-self-center">
                            <div data-label="60%" class="css-bar mb-0 css-bar-info css-bar-60"><i class="mdi mdi-receipt"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Row -->
<!-- Row -->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block">
                    <h4 class="card-title">Sales of the Month</h4>
                    <div class="ml-auto">
                        <select class="custom-select">
                            <option selected>January</option>
                            <option value="1">February</option>
                            <option value="2">March</option>
                            <option value="3">April</option>
                        </select>
                    </div>
                </div>
                <!-- Row -->
                <div class="row mt-4">
                    <div class="col-md-7">
                        <div id="sales-donute" style="width:100%; height:300px;"></div>
                        <div class="round-overlap"><i class="mdi mdi-cart"></i></div>
                    </div>
                    <div class="col-md-5 align-self-center">
                        <h1 class="mb-0">65<small>%</small></h1>
                        <h6 class="text-muted">160 Sales January</h6>
                        <ul class="list-icons mt-4">
                            <li><i class="fa fa-circle text-purple"></i> Organic Sales</li>
                            <li><i class="fa fa-circle text-success"></i> Search Engine</li>
                            <li><i class="fa fa-circle text-info"></i> Marketing</li>
                        </ul>
                    </div>
                </div>
                <!-- Row -->
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <ul class="list-inline float-right">
                    <li>
                        <h6 class="text-muted"><i class="fa fa-circle mr-1 text-success"></i>Net</h6>
                    </li>
                    <li>
                        <h6 class="text-muted"><i class="fa fa-circle mr-1 text-info"></i>Growth</h6>
                    </li>
                </ul>
                <h4 class="card-title">Income of the Year</h4>
                <div class="income-year" style="height: 327px;"></div>
            </div>
        </div>
    </div>
</div>
<!-- Row -->
<!-- Row -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block">
                    <h4 class="card-title">Projects of the Month</h4>
                    <div class="ml-auto">
                        <select class="custom-select">
                            <option selected>January</option>
                            <option value="1">February</option>
                            <option value="2">March</option>
                            <option value="3">April</option>
                        </select>
                    </div>
                </div>
                <div class="table-responsive mt-4">
                    <table class="table stylish-table no-wrap">
                        <thead>
                        <tr>
                            <th colspan="2">Assigned</th>
                            <th>Name</th>
                            <th>Priority</th>
                            <th>Budget</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="width:50px;"><span class="round">S</span></td>
                            <td>
                                <h6>Sunil Joshi</h6><small class="text-muted">Web Designer</small></td>
                            <td>Elite Admin</td>
                            <td><span class="label label-light-success">Low</span></td>
                            <td>$3.9K</td>
                        </tr>
                        <tr class="active">
                            <td><span class="round"><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/2.jpg" alt="user" width="50" /></span></td>
                            <td>
                                <h6>Andrew</h6><small class="text-muted">Project Manager</small></td>
                            <td>Real Homes</td>
                            <td><span class="label label-light-info">Medium</span></td>
                            <td>$23.9K</td>
                        </tr>
                        <tr>
                            <td><span class="round round-success">B</span></td>
                            <td>
                                <h6>Bhavesh patel</h6><small class="text-muted">Developer</small></td>
                            <td>MedicalPro Theme</td>
                            <td><span class="label label-light-danger">High</span></td>
                            <td>$12.9K</td>
                        </tr>
                        <tr>
                            <td><span class="round round-primary">N</span></td>
                            <td>
                                <h6>Nirav Joshi</h6><small class="text-muted">Frontend Eng</small></td>
                            <td>Elite Admin</td>
                            <td><span class="label label-light-success">Low</span></td>
                            <td>$10.9K</td>
                        </tr>
                        <tr>
                            <td><span class="round round-warning">M</span></td>
                            <td>
                                <h6>Micheal Doe</h6><small class="text-muted">Content Writer</small></td>
                            <td>Helping Hands</td>
                            <td><span class="label label-light-danger">High</span></td>
                            <td>$12.9K</td>
                        </tr>
                        <tr>
                            <td><span class="round round-danger">N</span></td>
                            <td>
                                <h6>Johnathan</h6><small class="text-muted">Graphic</small></td>
                            <td>Digital Agency</td>
                            <td><span class="label label-light-danger">High</span></td>
                            <td>$2.6K</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex no-block">
                    <h4 class="card-title">Weather Report</h4>
                    <div class="ml-auto">
                        <select class="custom-select">
                            <option selected>Today</option>
                            <option value="1">Weekly</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex align-items-center flex-row mt-4">
                    <div class="p-2 display-5 text-info"><i class="wi wi-day-showers"></i> <span>73<sup>°</sup></span></div>
                    <div class="p-2">
                        <h3 class="mb-0">Saturday</h3><small>Ahmedabad, India</small></div>
                </div>
                <table class="table no-border">
                    <tr>
                        <td>Wind</td>
                        <td class="font-medium">ESE 17 mph</td>
                    </tr>
                    <tr>
                        <td>Humidity</td>
                        <td class="font-medium">83%</td>
                    </tr>
                    <tr>
                        <td>Pressure</td>
                        <td class="font-medium">28.56 in</td>
                    </tr>
                    <tr>
                        <td>Cloud Cover</td>
                        <td class="font-medium">78%</td>
                    </tr>
                    <tr>
                        <td>Ceiling</td>
                        <td class="font-medium">25760 ft</td>
                    </tr>
                </table>
                <hr/>
                <ul class="list-unstyled row text-center city-weather-days">
                    <li class="col"><i class="wi wi-day-sunny"></i><span>09:30</span>
                        <h3>70<sup>°</sup></h3></li>
                    <li class="col"><i class="wi wi-day-cloudy"></i><span>11:30</span>
                        <h3>72<sup>°</sup></h3></li>
                    <li class="col"><i class="wi wi-day-hail"></i><span>13:30</span>
                        <h3>75<sup>°</sup></h3></li>
                    <li class="col"><i class="wi wi-day-sprinkle"></i><span>15:30</span>
                        <h3>76<sup>°</sup></h3></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- Row -->
<!-- Row -->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Recent Comments</h4>
            </div>
            <!-- ============================================================== -->
            <!-- Comment widgets -->
            <!-- ============================================================== -->
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
                <!-- Comment Row -->
                <div class="d-flex flex-row comment-row">
                    <div class="p-2"><span class="round"><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/4.jpg" alt="user" width="50"></span></div>
                    <div class="comment-text w-100">
                        <h5>James Anderson</h5>
                        <p class="mb-1">Lorem Ipsum is simply dummy text of the printing and type setting industry. Lorem Ipsum has beenorem Ipsum is simply dummy text of the printing and type setting industry..</p>
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
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <button class="float-right btn btn-sm btn-rounded btn-success" data-toggle="modal" data-target="#myModal">Add Task</button>
                <h4 class="card-title">To Do list</h4>
                <!-- ============================================================== -->
                <!-- To do list widgets -->
                <!-- ============================================================== -->
                <div class="to-do-widget mt-3">
                    <!-- .modal for add task -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Add Task</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form>
                                        <div class="form-group">
                                            <label>Task name</label>
                                            <input type="text" class="form-control" placeholder="Enter Task Name">
                                        </div>
                                        <div class="form-group">
                                            <label>Assign to</label>
                                            <select class="custom-select form-control float-right">
                                                <option selected="">Sachin</option>
                                                <option value="1">Sehwag</option>
                                                <option value="2">Pritam</option>
                                                <option value="3">Alia</option>
                                                <option value="4">Varun</option>
                                            </select>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-success" data-dismiss="modal">Submit</button>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                    <!-- /.modal -->
                    <ul class="list-task todo-list list-group mb-0" data-role="tasklist">
                        <li class="list-group-item" data-role="task">
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" id="inputSchedule" name="inputCheckboxesSchedule">
                                <label for="inputSchedule" class=""> <span>Schedule meeting with</span> </label>
                            </div>
                            <ul class="assignedto">
                                <li><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/1.jpg" alt="user" data-toggle="tooltip" data-placement="top" title="" data-original-title="Steave"></li>
                                <li><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/2.jpg" alt="user" data-toggle="tooltip" data-placement="top" title="" data-original-title="Jessica"></li>
                                <li><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/3.jpg" alt="user" data-toggle="tooltip" data-placement="top" title="" data-original-title="Priyanka"></li>
                                <li><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/4.jpg" alt="user" data-toggle="tooltip" data-placement="top" title="" data-original-title="Selina"></li>
                            </ul>
                        </li>
                        <li class="list-group-item" data-role="task">
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" id="inputCall" name="inputCheckboxesCall">
                                <label for="inputCall" class=""> <span>Give Purchase report to</span> <span class="label label-light-danger">Today</span> </label>
                            </div>
                            <ul class="assignedto">
                                <li><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/3.jpg" alt="user" data-toggle="tooltip" data-placement="top" title="" data-original-title="Priyanka"></li>
                                <li><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/4.jpg" alt="user" data-toggle="tooltip" data-placement="top" title="" data-original-title="Selina"></li>
                            </ul>
                        </li>
                        <li class="list-group-item" data-role="task">
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" id="inputBook" name="inputCheckboxesBook">
                                <label for="inputBook" class=""> <span>Book flight for holiday</span> </label>
                            </div>
                            <div class="item-date"> 26 jun 2019</div>
                        </li>
                        <li class="list-group-item" data-role="task">
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" id="inputForward" name="inputCheckboxesForward">
                                <label for="inputForward" class=""> <span>Forward all tasks</span> <span class="label label-light-warning">2 weeks</span> </label>
                            </div>
                            <div class="item-date"> 26 jun 2019</div>
                        </li>
                        <li class="list-group-item" data-role="task">
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" id="inputRecieve" name="inputCheckboxesRecieve">
                                <label for="inputRecieve" class=""> <span>Recieve shipment</span> </label>
                            </div>
                            <div class="item-date"> 26 jun 2019</div>
                        </li>
                        <li class="list-group-item" data-role="task">
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" id="inputpayment" name="inputCheckboxespayment">
                                <label for="inputpayment" class=""> <span>Send payment today</span> </label>
                            </div>
                            <div class="item-date"> 26 jun 2019</div>
                        </li>
                        <li class="list-group-item" data-role="task">
                            <div class="checkbox checkbox-info">
                                <input type="checkbox" id="inputForward2" name="inputCheckboxesd">
                                <label for="inputForward2" class=""> <span>Important tasks</span> <span class="label label-light-success">2 weeks</span> </label>
                            </div>
                            <ul class="assignedto">
                                <li><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/1.jpg" alt="user" data-toggle="tooltip" data-placement="top" title="" data-original-title="Assign to Steave"></li>
                                <li><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/2.jpg" alt="user" data-toggle="tooltip" data-placement="top" title="" data-original-title="Assign to Jessica"></li>
                                <li><img src="/vendor/wrappixel/monster-admin/4.2.1/assets/images/users/4.jpg" alt="user" data-toggle="tooltip" data-placement="top" title="" data-original-title="Assign to Selina"></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Row -->
<!-- ============================================================== -->
<!-- End PAge Content -->
<!-- ============================================================== -->
