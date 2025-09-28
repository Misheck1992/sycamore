<?php
$users = get_all('employees');
$products = get_all('loan');
?>
<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">All monthly collection report</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">-</a>
				<span class="breadcrumb-item active">All loans  collection report</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
			
			<hr>
			<p>Search results</p>
			<h5>Collection sheet this month</h5>
			<table class="table tab-content" id="data-table-collection">
				<thead>
				<tr>
					<th>#</th>
					<th>Loan Customer</th>
					<th>Loan Number</th>
					<th>Check Date</th>
					<th>Amount to collect</th>
					<th>Payment number</th>
                    <th>Officer</th>
					<th>Action</th>

				</tr>
				</thead>
				<tbody>
				<?php
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
                        <td><a href="<?php echo base_url($preview_url).$loan->loan_customer?>""><?php echo $customer_name?></a></td>

						<td><a href="<?php echo base_url('loan/view/').$loan->loan_id?>"><?php echo $loan->loan_number ?></a></td>

						<td><?php echo $loan->payment_schedule ?></td>
<!--						<td>MK--><?php //echo number_format($loan->loan_principal,2) ?><!--</td>-->
						<td><?php echo $loan->amount ?></td>
						<td><?php echo $loan->payment_number ?></td>
                        <td><?php echo $loan->efname." ".$loan->elname ?></td>
						<td><a href="<?php echo base_url('loan/view/').$loan->loan_id?>">View</a></td>

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
