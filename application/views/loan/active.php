<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">All  loans</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">All   loans</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
            <div style="overflow-y: auto"">
            <table  id="data-table" class="tableCss">
                <thead>
                <tr>

                    <th>#</th>
                    <th>Loan Number</th>
                    <th>Loan Product</th>
                    <th>Loan Customer</th>
                    <th>Loan Date</th>
                    <th>Loan Principal</th>
                    <th>Loan Period</th>
                    <th>Period Type</th>
                    <th>Loan Interest</th>
                    <th>Loan Amount Total</th>
                    <th>Loan File</th>


                    <th>Loan Status</th>
                    <th>Funds Source</th>
                    <th>Customer Group</th>
                    <th>Batch</th>
                    <th>Loan Added Date</th>
                    <th>Loan Added By</th>
                    <th>Action</th>

                </tr>
                </thead>
                <tbody><?php
                $n = 1;

                foreach ($loan_data as $loan)
                {
                    if($loan->customer_type=='group'){
                        $group = $this->Groups_model->get_by_id($loan->loan_customer);

                        $customer_name = $group->group_name.'('.$group->group_code.')';
                        $preview_url = "Customer_groups/members/";
                    }elseif($loan->customer_type=='individual'){
                        $indi = $this->Individual_customers_model->get_by_id($loan->loan_customer);
                        $customer_name = $indi->Firstname.' '.$indi->Lastname;
                        $preview_url = "Individual_customers/view/";
                    }
                    ?>
                    <tr>

                        <td><?php echo $n ?></td>
                        <td><?php echo $loan->loan_number ?></td>
                        <td><?php echo $loan->product_name ?></td>
                        <td><a href="<?php echo base_url($preview_url).$loan->loan_customer?>""><?php echo $customer_name?></a></td>
                        <td><?php echo $loan->loan_date ?></td>
                        <td>ZMW<?php echo number_format($loan->loan_principal,2) ?></td>
                        <td><?php echo $loan->loan_period ?></td>
                        						<td><?php echo $loan->period_type ?></td>
                        <td><?php echo $loan->loan_interest ?>%</td>
                        <td>ZMW<?php echo number_format($loan->loan_amount_total,2) ?></td>
                        <td><a href="<?php echo base_url('uploads/').$loan->worthness_file?>" download >Download file <i class="fa fa-download fa-flip"></i></a></td>

                        <td><?php echo $loan->loan_status ?></td>
                        <td><?php echo isset($loan->funds_source_name) ? $loan->funds_source_name : 'N/A' ?></td>
                        <td><?php echo isset($loan->customer_group_name) ? $loan->customer_group_name : 'N/A' ?></td>
                        <td><?php echo isset($loan->batch_number) ? $loan->batch_number : 'N/A' ?></td>
                        <td><?php echo $loan->loan_added_date ?></td>
                        <td><?php
                            $gbyid = get_by_id('employees','id', $loan->loan_added_by);
                            echo $gbyid->Firstname.' '.$gbyid->Lastname ?></td></td>
                        <td>
                            <a href="<?php echo base_url('loan/view/').$loan->loan_id?>" class="btn btn-sm btn-primary">View</a>
                            <a href="<?php echo base_url('loan/report/').$loan->loan_id?>" class="btn btn-sm btn-info" target="_blank">Report</a>
                            <a href="#" onclick="assign_loan_officer('<?php echo $loan->loan_number?>','<?php echo $loan->loan_id?>')" class="btn btn-sm btn-warning">Assign Officer</a>
                        </td>

                    </tr>
                    <?php
                    $n ++;
                }
                ?>
                </tbody>
            </table>
        </div>
        </div>
    </div>
</div>
