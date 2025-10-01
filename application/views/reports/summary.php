<?php
$logs = get_logs('activity_logger','user_id',$this->session->userdata('user_id'));
$settings = get_by_id('settings','settings_id','1');
?>
<div class="main-content">
    <div class="page-header no-gutters has-tab" style="margin-bottom: 2px !important;">
        <h2 class="font-weight-normal">DASHBOARD SUMMARY</h2>

    </div>
    <?php

    $show = false;
    foreach ($this->session->userdata('access') as $r) {
        if ($r->controllerid == 113) {
            $show = true;
            break;
        }
    }
    ?>
    <?php
    if($show){
        ?>
        <div class="row">
            <div class="col-lg-12">
                <h2 class="heading">Revenue</h2>
                <hr class="dash" >
            </div>
        </div>
        <div class="row">

            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a class="dashboard-stat green" href="<?php echo base_url('Loan/loan_revenue') ?>">
                    <div class="visual">

                    </div>
                    <div class="details">
                        <div class="number">
                        <span><?php echo $settings->currency?> <?php
                            $ip_i = 0;

                            $ip = institutional_portfolio();
                            $paid_interest_balances = paid_interest_balances();

                            echo number_format(round($paid_interest_balances->totals),2);

                            ?></span>
                        </div>
                        <div class="desc">Total Paid/collected Interests on all Loans</div>
                    </div>
                </a>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a class="dashboard-stat green" href="<?php echo base_url('Loan/loan_revenue') ?>">
                    <div class="visual">
                        <i class="fa fa-bar-chart-o"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                        <span><?php echo $settings->currency; ?> <?php


                            $paid_lc_balances = paid_lc_balances();

                            echo number_format(round($paid_lc_balances->totals),2);
                            ?></span>
                        </div>
                        <div class="desc">Total paid/collected loan cover  on all Loans</div>
                    </div>
                </a>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a class="dashboard-stat green" href="<?php echo base_url('Loan/loan_revenue') ?>">
                    <div class="visual">
                        <i class="fa fa-bar-chart"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                        <span><?php echo $settings->currency?> <?php
                            $paid_af_balances = paid_af_balances();

                            echo number_format(round($paid_af_balances->totals),2);
                            ?></span>
                        </div>
                        <div class="desc">Total paid/collected loan administration fees on all Loans <br/></div>


                    </div>
                </a>
            </div>


        </div>
        <div class="row">
            <div class="col-lg-12">
                <h2 class="heading">Balances</h2>
                <hr class="dash" >
            </div>
        </div>
        <div class="row">

            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a class="dashboard-stat hoki" href="<?php echo base_url('Loan/balances') ?>">
                    <div class="visual">
                        <i class="fa fa-bar-chart-o"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                        <span><?php echo $settings->currency?> <?php
                            $ip_i = 0;

                            $ip = institutional_portfolio();
                            $outstanding_interest_balance = outstanding_interest_balances();

                            echo number_format(round($outstanding_interest_balance->totals),2);

                            ?></span>
                        </div>
                        <div class="desc">Total outstanding Interests on all Loans</div>
                    </div>
                </a>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a class="dashboard-stat hoki" href="<?php echo base_url('Loan/balances') ?>">
                    <div class="visual">
                        <i class="fa fa-bar-chart-o"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                        <span><?php echo $settings->currency?> <?php


                            $outstanding_lc_balance = outstanding_lc_balances();

                            echo number_format(round($outstanding_lc_balance->totals),2);
                            ?></span>
                        </div>
                        <div class="desc">Total outstanding loan cover balances on all Loans</div>
                    </div>
                </a>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a class="dashboard-stat hoki" href="<?php echo base_url('Loan/balances') ?>">
                    <div class="visual">
                        <i class="fa fa-bar-chart-o"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                        <span><?php echo $settings->currency?> <?php
                            $ip_ac = 0;

                            $outstanding_af_balances = outstanding_af_balances();

                            echo number_format(round($outstanding_af_balances->totals),2);
                            ?></span>
                        </div>
                        <div class="desc">Total outstanding loan administration balance on all Loans</div>
                    </div>
                </a>
            </div>


        </div>
        <div class="row">
            <div class="col-lg-12">
                <h2 class="heading">Loan Product-wise- outstanding balances</h2>
                <hr class="dash" >
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a class="dashboard-stat purple" href="<?php echo base_url('Loan/balances') ?>">
                    <div class="visual">
                        <i class="fa fa-usd"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                        <span><?php echo $settings->currency?> <?php

                            $totalunpaid=get_all_total_unpayments();
                            echo "MK" .number_format($totalunpaid->total_unpaid,2);

                            ?></span>
                        </div>
                        <div class="desc">Total Institutional Portfolio-outstanding balances</div>
                    </div>
                </a>
            </div>
            <?php
            $products = get_all('loan_products');
            foreach ($products as $product){
//            $product->loan_product_id
                $get_all_balances = get_all_loan_balances_by_product($product->loan_product_id);
                ?>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <a class="dashboard-stat blue" href="<?php echo base_url('Loan/balances?product=').$product->loan_product_id; ?>">
                        <div class="visual">
                            <i class="fa fa-usd"></i>
                        </div>
                        <div class="details">
                            <div class="number">
                        <span><?php echo $settings->currency ?><?php
                            $b = 0;
                            foreach ($get_all_balances as $d){
                                $b += $d->principal;
                            }
                            $round = round($b);
                            echo number_format($round,2);

                            ?></span>
                            </div>
                            <div class="desc"><?php echo $product->product_name. " (".$product->product_code.")"; ?></div>
                        </div>
                    </a>
                </div>
                <?php
            }
            ?>

        </div>

        <div class="row">
            <div class="col-lg-12">
                <h2 class="heading float-left">Arrears</h2> <h2 class="heading float-right">Payments Due</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 border-right border-success">

                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <a class="dashboard-stat hoki" href="<?php echo base_url('Reports/arrears?by_date=All&loan=All')?>">
                            <div class="visual">
                                <i class="fa fa-credit-card"></i>
                            </div>
                            <div class="details">
                                <div class="numberr">
                        <span><?php echo $settings->currency?> <?php
                            $ip_a = 0;

                            $arrea = institutional_arrears();
                            foreach ($arrea as $a){
                                $ip_a += $a->amount;
                            }
                            echo number_format(round($ip_a),2);
                            ?></span>
                                </div>
                                <div class="desc">Total Arrears</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <a class="dashboard-stat red" href="<?php echo base_url('Reports/arrears?by_date=one_day&loan=All')?>">
                            <div class="visual">
                                <i class="fa fa-usd"></i>
                            </div>
                            <div class="details">
                                <div class="numberr">
                        <span><?php echo $settings->currency?> <?php
                            $today_b = 0;
                            $today = institutional_arrears_today();

                            foreach ($today as $td){
                                $today_b += $td->amount;
                            }

                            echo number_format(round($today_b),2);
                            ?></span>
                                </div>
                                <div class="desc">One day arrears</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <a class="dashboard-stat red" href="<?php echo base_url('Reports/arrears?by_date=three_days&loan=All')?>">
                            <div class="visual">
                                <i class="fa fa-bar-chart-o"></i>
                            </div>
                            <div class="details">
                                <div class="numberr">
                        <span><?php echo $settings->currency?> <?php
                            $three_d = 0;
                            $threed = institutional_arrears_threedays();

                            foreach ($threed as $d3){
                                $three_d += $d3->amount;
                            }
                            echo number_format(round($three_d),2);
                            ?></span>
                                </div>
                                <div class="desc">Three days Arrears</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <a class="dashboard-stat red" href="<?php echo base_url('Reports/arrears?by_date=week&loan=All')?>">
                            <div class="visual">
                                <i class="fa fa-credit-card"></i>
                            </div>
                            <div class="details">
                                <div class="numberr">
                        <span><?php echo $settings->currency?> <?php
                            $week_d = 0;


                            $week = institutional_arrears_week();
                            foreach ($week as $w){
                                $week_d += $w->amount;

                            }

                            echo number_format(round($week_d),2);
                            ?></span>
                                </div>
                                <div class="desc">One week Arrears</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <a class="dashboard-stat red" href="<?php echo base_url('Reports/arrears?by_date=month&loan=All')?>">
                            <div class="visual">
                                <i class="fa fa-credit-card"></i>
                            </div>
                            <div class="details">
                                <div class="numberr">
                        <span><?php echo $settings->currency?> <?php
                            $month_d = 0;

                            $mo = institutional_arrears_month();
                            foreach ($mo as $m){
                                $month_d += $m->amount;

                            }

                            echo number_format(round($month_d),2);
                            ?></span>
                                </div>
                                <div class="desc">One Month Arrears</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <a class="dashboard-stat red" href="<?php echo base_url('Reports/arrears?by_date=2month&loan=All')?>">
                            <div class="visual">
                                <i class="fa fa-credit-card"></i>
                            </div>
                            <div class="details">
                                <div class="numberr">
                        <span><?php echo $settings->currency?> <?php
                            $one_m = 0;

                            $onem = institutional_arrears_2month();
                            foreach ($onem as $m1){
                                $one_m += $m1->amount;
                            }
                            echo number_format(round($one_m),2);
                            ?></span>
                                </div>
                                <div class="desc">Two Months Arrears</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <a class="dashboard-stat red" href="<?php echo base_url('Reports/arrears?by_date=3month&loan=All')?>">
                            <div class="visual">
                                <i class="fa fa-credit-card"></i>
                            </div>
                            <div class="details">
                                <div class="numberr">
                        <span><?php echo $settings->currency?> <?php
                            $two_m = 0;

                            $twom = institutional_arrears_3month();
                            foreach ($twom as $m2){
                                $two_m += $m2->amount;
                            }
                            echo number_format(round($two_m),2);
                            ?></span>
                                </div>
                                <div class="desc">Three Months Arrears</div>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
            <div class="col-lg-6">

                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <a class="dashboard-stat orange" href="<?php echo base_url('Reports/to_pay_today')?>">
                            <div class="visual">
                                <i class="fa fa-usd"></i>
                            </div>
                            <div class="details">
                                <div class="numberr">
                        <span><?php echo $settings->currency?> <?php
                            $payment_d = 0;
                            $payment_today = payments_today();

                            foreach ($payment_today as $pd){
                                $payment_d += $pd->amount;
                            }

                            echo number_format(round($payment_d),2);
                            ?></span>
                                </div>
                                <div class="desc">Payments due today</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <a class="dashboard-stat orange" href="<?php echo base_url('Reports/to_pay_week')?>">
                            <div class="visual">
                                <i class="fa fa-bar-chart-o"></i>
                            </div>
                            <div class="details">
                                <div class="numberr">
                        <span><?php echo $settings->currency?> <?php
                            $payment_week = 0;
                            $pw = payments_week();

                            foreach ($pw as $pww){
                                $payment_week += $pww->amount;
                            }
                            echo number_format(round($payment_week),2);
                            ?></span>
                                </div>
                                <div class="desc">Payment due this week</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <a class="dashboard-stat orange" href="<?php echo base_url('Reports/to_pay_month')?>">
                            <div class="visual">
                                <i class="fa fa-credit-card"></i>
                            </div>
                            <div class="details">
                                <div class="numberr">
                        <span><?php echo $settings->currency?> <?php
                            $p_m = 0;


                            $payment_month = payments_month();
                            foreach ($payment_month as $pmm){
                                $p_m += $pmm->amount;

                            }

                            echo number_format(round($p_m),2);
                            ?></span>
                                </div>
                                <div class="desc">Payment due this month</div>
                            </div>
                        </a>
                    </div>


                </div>
            </div>
        </div>





        <?php
//        $p=0;
//        $p1=0;
//        $totaldisb=0;
//        $gt=$this->Loan_model->sum_total_par();
//        $gt2=$this->Loan_model->sum_total2($this->session->userdata('officerid'));
//        foreach ($gt as $tamt){
//            $totaldisb +=$tamt->lm;
//        }
//        foreach ($gt2 as $tamt2){
//            if($tamt2->paid_amount >=$tamt2->principal){
////       $p = $tamt2->principal;
//                $p=0;
//                $p1 +=$p;
//
//            }elseif($tamt2->paid_amount < $tamt2->principal){
//                $p = $tamt2->principal-$tamt2->paid_amount;
//                $p1 +=$p;
//
//            }
//
//        }

        ?>




        <!--        <div class="row">-->
        <!--            <div class="col-lg-12">-->
        <!--                <h2 class="heading">Portfolio At Risk</h2>-->
        <!--                <hr class="dash" >-->
        <!--            </div>-->
        <!--        </div>-->
        <!--        <div class="row">-->
        <!--            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">-->
        <!--                <a class="dashboard-stat hoki" href="#">-->
        <!--                    <div class="visual">-->
        <!--                        <i class="fa fa-usd"></i>-->
        <!--                    </div>-->
        <!--                    <div class="details">-->
        <!--                        <div class="number">-->
        <!--                        <span>--><?php //echo $settings->currency?><!-- --><?php
//
//                            echo number_format($tzerotoseven,2);
//                            ?><!--</span>-->
        <!--                        </div>-->
        <!--                        <div class="desc">0 - 7 Days PAR</div>-->
        <!--                    </div>-->
        <!--                </a>-->
        <!--            </div>-->
        <!--            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">-->
        <!--                <a class="dashboard-stat hoki" href="#">-->
        <!--                    <div class="visual">-->
        <!--                        <i class="fa fa-bar-chart-o"></i>-->
        <!--                    </div>-->
        <!--                    <div class="details">-->
        <!--                        <div class="number">-->
        <!--                        <span>--><?php //echo $settings->currency?><!-- --><?php
//
//                            echo number_format($morethanseven,2);
//                            ?><!--</span>-->
        <!--                        </div>-->
        <!--                        <div class="desc">AGED 8- 30 Days PAR</div>-->
        <!--                    </div>-->
        <!--                </a>-->
        <!--            </div>-->
        <!--            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">-->
        <!--                <a class="dashboard-stat hoki" href="#">-->
        <!--                    <div class="visual">-->
        <!--                        <i class="fa fa-credit-card"></i>-->
        <!--                    </div>-->
        <!--                    <div class="details">-->
        <!--                        <div class="number">-->
        <!--                        <span>--><?php //echo $settings->currency?><!-- --><?php
//
//                            echo number_format(round($morethanthirty),2);
//                            ?><!--</span>-->
        <!--                        </div>-->
        <!--                        <div class="desc">AGED 31 - 60 Days PAR</div>-->
        <!--                    </div>-->
        <!--                </a>-->
        <!--            </div>-->
        <!--            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">-->
        <!--                <a class="dashboard-stat hoki" href="#">-->
        <!--                    <div class="visual">-->
        <!--                        <i class="fa fa-credit-card"></i>-->
        <!--                    </div>-->
        <!--                    <div class="details">-->
        <!--                        <div class="number">-->
        <!--                        <span>--><?php //echo $settings->currency?><!-- --><?php
//
//                            echo number_format(round($morethansixty),2);
//                            ?><!--</span>-->
        <!--                        </div>-->
        <!--                        <div class="desc">AGED 61 - 90 Days PAR</div>-->
        <!--                    </div>-->
        <!--                </a>-->
        <!--            </div>-->
        <!--            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">-->
        <!--                <a class="dashboard-stat hoki" href="#">-->
        <!--                    <div class="visual">-->
        <!--                        <i class="fa fa-credit-card"></i>-->
        <!--                    </div>-->
        <!--                    <div class="details">-->
        <!--                        <div class="number">-->
        <!--                        <span>--><?php //echo $settings->currency?><!-- --><?php
//
//                            echo number_format(round($morethanninety),2);
//                            ?><!--</span>-->
        <!--                        </div>-->
        <!--                        <div class="desc">AGED 91 - 120 Days PAR</div>-->
        <!--                    </div>-->
        <!--                </a>-->
        <!--            </div>-->
        <!--            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">-->
        <!--                <a class="dashboard-stat hoki" href="#">-->
        <!--                    <div class="visual">-->
        <!--                        <i class="fa fa-credit-card"></i>-->
        <!--                    </div>-->
        <!--                    <div class="details">-->
        <!--                        <div class="number">-->
        <!--                        <span>--><?php //echo $settings->currency?><!-- --><?php
//
//                            echo number_format(round($morethanonetwenty),2);
//                            ?><!--</span>-->
        <!--                        </div>-->
        <!--                        <div class="desc">AGED 121 - 180 Days PAR</div>-->
        <!--                    </div>-->
        <!--                </a>-->
        <!--            </div>-->
        <!--            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">-->
        <!--                <a class="dashboard-stat hoki" href="#">-->
        <!--                    <div class="visual">-->
        <!--                        <i class="fa fa-credit-card"></i>-->
        <!--                    </div>-->
        <!--                    <div class="details">-->
        <!--                        <div class="number">-->
        <!--                        <span>--><?php //echo $settings->currency?><!-- --><?php
//
//                            echo number_format(round($morethanoneeighty),2);
//                            ?><!--</span>-->
        <!--                        </div>-->
        <!--                        <div class="desc">AGED 181 - 366 Days PAR</div>-->
        <!--                    </div>-->
        <!--                </a>-->
        <!--            </div>-->
        <!--            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">-->
        <!--                <a class="dashboard-stat hoki" href="#">-->
        <!--                    <div class="visual">-->
        <!--                        <i class="fa fa-credit-card"></i>-->
        <!--                    </div>-->
        <!--                    <div class="details">-->
        <!--                        <div class="number">-->
        <!--                        <span>--><?php //echo $settings->currency?><!-- --><?php
//
//                            echo number_format(round($morethanthreesixty),2);
//                            ?><!--</span>-->
        <!--                        </div>-->
        <!--                        <div class="desc">More than 366 Days PAR</div>-->
        <!--                    </div>-->
        <!--                </a>-->
        <!--            </div>-->
        <!---->
        <!--        </div>-->

        <?php
    }else{
        ?>
        <ul class="nav nav-tabs" >
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#tab-account">Account</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tab-network">Recent activity logs</a>
            </li>

        </ul>
        <div class="container">
            <div class="tab-content m-t-15">
                <div class="tab-pane fade show active" id="tab-account" >
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Basic Information</h4>
                        </div>
                        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
                            <div class="media align-items-center">
                                <div class="avatar avatar-image  m-h-10 m-r-15" style="height: 80px; width: 80px">
                                    <img src="<?php echo base_url('uploads')?>/avatar-3.png" alt="">
                                </div>

                            </div>
                            <hr class="m-v-25">
                            <form>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label class="font-weight-semibold" for="userName">User Name:</label>
                                        <input type="text" class="form-control" id="userName" disabled placeholder="User Name" value="<?php  echo  $this->session->userdata('username')?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="font-weight-semibold" for="email">Full name:</label>
                                        <input type="text"  disabled class="form-control" id="email" placeholder="email" value="<?php echo  $this->session->userdata('Firstname')."".$this->session->userdata('Lastname') ?>">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label class="font-weight-semibold" for="phoneNumber">Designation:</label>
                                        <input type="text" class="form-control" disabled id="phoneNumber" placeholder="Phone Number" value="<?php echo  $this->session->userdata('RoleName') ?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="font-weight-semibold" for="dob">Date Joined:</label>
                                        <input type="text" class="form-control" disabled id="dob" placeholder="<?php echo $this->session->userdata('stamp');?>">
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>


                </div>
                <div class="tab-pane fade" id="tab-network">
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">My system logs</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Date time</th>
                                            <th>Activity</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php

                                        foreach ($logs as $log){
                                            ?>
                                            <tr>
                                                <td><?php  echo $log->server_time; ?></td>
                                                <td><?php echo $log->activity; ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php
    }
    ?>


</div>
