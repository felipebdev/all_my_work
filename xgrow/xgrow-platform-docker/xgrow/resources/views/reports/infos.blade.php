    <div class="row">

        <div class="col-lg-3 col-md-6">
            <div class="card py-1 pr-1">
                <a href="#">
                    <div class="card-body container d-flex p-2">
                        <div class="d-flex justify-content-center"
                             style="background-color: #28a745;padding: 16px;border-radius: 10px;height: 50%;width: 25%;">
                            <i class="fa fa-check" style="color:white;font-size:25px" aria-hidden="true"></i>
                        </div>
                        <div class="d-flex flex-column" style="margin-left:10px;margin-top: 5px;">
                            <h2 class="font-weight-bold mb-0">{{ $totalSales }}</h2>
                            <span class="text-muted" style="line-height: 0;"><small>Pago</small></span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card py-1 pr-1">
                <a href="#">
                    <div class="card-body container d-flex p-2">
                        <div class="d-flex justify-content-center"
                             style="background-color: #ede9a3;padding: 16px;border-radius: 10px;height: 50%;width: 25%;">
                            <i class="fa fa-exclamation" style="color:white;font-size:25px" aria-hidden="true"></i>
                        </div>
                        <div class="d-flex flex-column" style="margin-left:10px;margin-top: 5px;">
                            <h2 class="font-weight-bold mb-0">{{  $totalPending }}</h2>
                            <span class="text-muted" style="line-height: 0;"><small>Pendente</small></span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card py-1 pr-1">
                <a href="#">
                    <div class="card-body container d-flex p-2">
                        <div class="d-flex justify-content-center"
                             style="background-color: #dc3545;padding: 16px;border-radius: 10px;height: 50%;width: 25%;">
                            <i class="fa fa-times" style="color:white;font-size:25px" aria-hidden="true"></i>
                        </div>
                        <div class="d-flex flex-column" style="margin-left:10px;margin-top: 5px;">
                            <h2 class="font-weight-bold mb-0">{{ $totalCanceled }}</h2>
                            <span class="text-muted" style="line-height: 0;"><small>Cancelado</small></span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

    </div>
