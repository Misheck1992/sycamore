<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Group Batch Loans - <?php echo $batch; ?></h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a href="<?php echo base_url('loan/group_file')?>" class="breadcrumb-item">Group File</a>
                <span class="breadcrumb-item active">Batch: <?php echo $batch; ?></span>
            </nav>
        </div>
    </div>
    
    <!-- Batch Summary -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Batch Summary</h4>
                </div>
                <div class="col-md-4 text-right">
                    <?php 
                    // Check loan statuses in this batch
                    $has_recommendable = false; // loans that can be recommended (not already recommended/approved/active)
                    $has_recommended = false;   // loans with RECOMMENDED status
                    $has_approved = false;      // loans with APPROVED status
                    
                    foreach($loans as $check_loan) {
                        if(!in_array($check_loan->loan_status, ['RECOMMENDED', 'APPROVED', 'ACTIVE', 'CLOSED', 'DELETED'])) {
                            $has_recommendable = true;
                        }
                        if($check_loan->loan_status == 'RECOMMENDED') {
                            $has_recommended = true;
                        }
                        if($check_loan->loan_status == 'APPROVED') {
                            $has_approved = true;
                        }
                    }
                    
                    if($has_recommendable && $permissions['can_recommend']): ?>
                    <button class="btn btn-warning mr-2" onclick="recommendBatch('<?php echo $batch; ?>')">
                        <i class="fas fa-thumbs-up mr-2"></i>Recommend Batch
                    </button>
                    <?php endif;
                    if($has_recommended && $permissions['can_approve']): ?>
                    <button class="btn btn-primary mr-2" onclick="approveBatch('<?php echo $batch; ?>')">
                        <i class="fas fa-check mr-2"></i>Approve Batch
                    </button>
                    <?php endif; ?>
                    <?php if($has_approved && $permissions['can_disburse']): ?>
                    <button class="btn btn-danger mr-2" onclick="disburseBatch('<?php echo $batch; ?>')">
                        <i class="fas fa-money-bill-wave mr-2"></i>Disburse Batch
                    </button>
                    <?php endif; ?>
                    <a href="<?php echo base_url('loan/batch_report/').$batch; ?>" class="btn btn-success" target="_blank">
                        <i class="fas fa-file-pdf mr-2"></i>Print Batch Report
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5>Total Loans</h5>
                        <h3 class="text-primary"><?php echo count($loans); ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5>Total Amount</h5>
                        <h3 class="text-success">MK<?php echo number_format(array_sum(array_column($loans, 'loan_principal')), 2); ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5>Group</h5>
                        <h3 class="text-info"><?php echo !empty($loans) ? $loans[0]->group_name . ' (' . $loans[0]->group_code . ')' : 'N/A'; ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <h5>Batch Number</h5>
                        <h3 class="text-warning"><?php echo $batch; ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Individual Loan Details -->
    <?php 
    $loan_counter = 1;
    foreach ($loans as $loan): 
        // Calculate payment schedule totals
        $this->db->select('
            SUM(CASE WHEN status = "PAID" THEN amount ELSE 0 END) as total_paid,
            SUM(CASE WHEN status = "NOT PAID" THEN amount ELSE 0 END) as total_outstanding,
            COUNT(*) as total_payments
        ');
        $this->db->where('loan_id', $loan->loan_id);
        $payment_summary = $this->db->get('payement_schedules')->row();
    ?>
    
    <!-- Loan Section LOAN <?php echo $loan->loan_number; ?> -->
    <div class="card loan-section mb-4">
        <div class="card-header loan-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>
                        LOAN <?php echo $loan->loan_number; ?>: <?php echo $loan->Firstname . ' ' . $loan->Lastname; ?>
                    </h4>
                    <small class="text-muted">Customer: <?php echo $loan->Firstname . ' ' . $loan->Lastname; ?></small>
                </div>
                <div class="col-md-4 text-right">
                    <span class="badge badge-<?php echo $loan->loan_status == 'ACTIVE' ? 'success' : ($loan->loan_status == 'CLOSED' ? 'secondary' : 'warning'); ?> badge-lg">
                        <?php echo $loan->loan_status; ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Loan Info -->
                <div class="col-lg-4 border-right">
                    <h5 class="section-title">Loan Information</h5>
                    <table class="loan-info-table">
                        <tr>
                            <td>Loan Number:</td>
                            <td><strong><?php echo $loan->loan_number; ?></strong></td>
                        </tr>
                        <tr>
                            <td>Product:</td>
                            <td><?php echo $loan->product_name; ?></td>
                        </tr>
                        <tr>
                            <td>Customer:</td>
                            <td>
                                <a href="<?php echo base_url('individual_customers/view/').$loan->loan_customer; ?>">
                                    <?php echo $loan->Firstname . ' ' . $loan->Lastname; ?>
                                    <small>(<?php echo $loan->ClientId; ?>)</small>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>Customer Type:</td>
                            <td><?php echo ucfirst($loan->customer_type); ?></td>
                        </tr>
                        <tr>
                            <td>Loan Status:</td>
                            <td><span class="badge badge-<?php echo $loan->loan_status == 'ACTIVE' ? 'success' : 'secondary'; ?>"><?php echo $loan->loan_status; ?></span></td>
                        </tr>
                        <tr>
                            <td>Loan Date:</td>
                            <td><?php echo date('M d, Y', strtotime($loan->loan_date)); ?></td>
                        </tr>
                    </table>
                </div>
                
                <!-- Financial Info -->
                <div class="col-lg-4 border-right">
                    <h5 class="section-title">Financial Details</h5>
                    <table class="loan-info-table">
                        <tr>
                            <td>Principal:</td>
                            <td><strong>MK<?php echo number_format($loan->loan_principal, 2); ?></strong></td>
                        </tr>
                        <tr>
                            <td>Interest Rate:</td>
                            <td><?php echo $loan->loan_interest; ?>%</td>
                        </tr>
                        <tr>
                            <td>Interest Amount:</td>
                            <td>MK<?php echo number_format($loan->loan_interest_amount, 2); ?></td>
                        </tr>
                        <tr>
                            <td>Total Amount:</td>
                            <td><strong>MK<?php echo number_format($loan->loan_amount_total, 2); ?></strong></td>
                        </tr>
                        <tr>
                            <td>Loan Period:</td>
                            <td><?php echo $loan->loan_period; ?> <?php echo $loan->period_type; ?></td>
                        </tr>
                        <tr>
                            <td>Payment Term:</td>
                            <td>MK<?php echo number_format($loan->loan_amount_term, 2); ?></td>
                        </tr>
                    </table>
                </div>
                
                <!-- Payment Status -->
                <div class="col-lg-4">
                    <h5 class="section-title">Payment Status</h5>
                    <table class="loan-info-table">
                        <tr>
                            <td>Total Payments:</td>
                            <td><?php echo $payment_summary ? $payment_summary->total_payments : 0; ?></td>
                        </tr>
                        <tr>
                            <td>Amount Paid:</td>
                            <td class="text-success"><strong>MK<?php echo number_format($payment_summary ? $payment_summary->total_paid : 0, 2); ?></strong></td>
                        </tr>
                        <tr>
                            <td>Outstanding:</td>
                            <td class="text-danger"><strong>MK<?php echo number_format($payment_summary ? $payment_summary->total_outstanding : 0, 2); ?></strong></td>
                        </tr>
                        <tr>
                            <td>Progress:</td>
                            <td>
                                <?php 
                                $progress = $payment_summary && $loan->loan_amount_total > 0 ? 
                                    ($payment_summary->total_paid / $loan->loan_amount_total) * 100 : 0;
                                ?>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: <?php echo $progress; ?>%"></div>
                                </div>
                                <small><?php echo number_format($progress, 1); ?>% Complete</small>
                            </td>
                        </tr>
                    </table>
                    
                    <!-- Action Buttons -->
                    <div class="mt-3">
                        <a href="<?php echo base_url('loan/view/').$loan->loan_id; ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye mr-1"></i>View Details
                        </a>
                        <a href="<?php echo base_url('loan/report/').$loan->loan_id; ?>" class="btn btn-info btn-sm" target="_blank">
                            <i class="fas fa-file-pdf mr-1"></i>Print Report
                        </a>
                        <button class="btn btn-warning btn-sm" onclick="toggleAmortization(<?php echo $loan->loan_id; ?>)">
                            <i class="fas fa-table mr-1"></i>View Amortization
                        </button>
                        <?php if (strtoupper(trim($loan->loan_status)) == 'ACTIVE'): ?>
                        <?php if ($permissions['can_pay']): ?>
                        <button class="btn btn-success btn-sm" onclick="payLoanBatch(<?php echo $loan->loan_id; ?>, '<?php echo $loan->loan_number; ?>')">
                            <i class="fas fa-money-bill-wave mr-1"></i>Pay Now
                        </button>
                        <?php endif; ?>
                        <?php if ($permissions['can_pay_off']): ?>
                        <button class="btn btn-warning btn-sm" onclick="payOffLoanBatch(<?php echo $loan->loan_id; ?>, '<?php echo $loan->loan_number; ?>')">
                            <i class="fas fa-hand-holding-usd mr-1"></i>Pay Off
                        </button>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php
                        $last_receipt = get_last($loan->loan_number);
                        if(!empty($last_receipt)){
                        ?>
                        <a href="<?php echo base_url('account/print_loan_receipt/').$last_receipt->id; ?>" target="_blank" class="btn btn-secondary btn-sm">
                            <i class="fas fa-print mr-1"></i>Latest Receipt
                        </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Amortization Schedule (Initially Hidden) -->
        <div id="amortization-<?php echo $loan->loan_id; ?>" class="amortization-section" style="display: none;">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-table mr-2"></i>Amortization Schedule - LOAN <?php echo $loan->loan_number; ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered amortization-table">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Payment #</th>
                                <th class="text-center">Payment Date</th>
                                <th class="text-center">Principal</th>
                                <th class="text-center">Interest</th>
                                <th class="text-center">Admin Fee</th>
                                <th class="text-center">Loan Cover</th>
                                <th class="text-center">Total Payment</th>
                                <th class="text-center">Amount Paid</th>
                                <th class="text-center">Balance</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Get payment schedule for this loan
                            $this->db->where('loan_id', $loan->loan_id);
                            $this->db->order_by('payment_number', 'ASC');
                            $payment_schedules = $this->db->get('payement_schedules')->result();
                            
                            foreach ($payment_schedules as $payment): 
                                $status_class = '';
                                $status_text = $payment->status;
                                
                                if($payment->payment_schedule < date('Y-m-d') && $payment->status == 'NOT PAID') {
                                    $status_class = 'table-danger';
                                    $status_text = 'OVERDUE';
                                } elseif($payment->payment_schedule == date('Y-m-d') && $payment->status == 'NOT PAID') {
                                    $status_class = 'table-warning';
                                    $status_text = 'DUE TODAY';
                                } elseif($payment->status == 'PAID') {
                                    $status_class = 'table-success';
                                }
                                
                                if($payment->partial_paid == "YES") {
                                    $status_text .= ' (Partial)';
                                }
                            ?>
                            <tr class="<?php echo $status_class; ?>">
                                <td><?php echo $payment->payment_number; ?></td>
                                <td><?php echo date('M d, Y', strtotime($payment->payment_schedule)); ?></td>
                                <td>MK<?php echo number_format($payment->principal, 2); ?></td>
                                <td>MK<?php echo number_format($payment->interest, 2); ?></td>
                                <td>MK<?php echo number_format($payment->padmin_fee, 2); ?></td>
                                <td>MK<?php echo number_format($payment->ploan_cover, 2); ?></td>
                                <td><strong>MK<?php echo number_format($payment->amount, 2); ?></strong></td>
                                <td class="text-success">MK<?php echo number_format($payment->paid_amount, 2); ?></td>
                                <td>MK<?php echo number_format($payment->loan_balance, 2); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $payment->status == 'PAID' ? 'success' : ($status_text == 'OVERDUE' ? 'danger' : ($status_text == 'DUE TODAY' ? 'warning' : 'secondary')); ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Loan Section Separator -->
        <div class="loan-separator">
            <div class="separator-line"></div>
            <div class="separator-text">End of LOAN <?php echo $loan->loan_number; ?></div>
            <div class="separator-line"></div>
        </div>
    </div>
    
    <?php 
    $loan_counter++;
    endforeach; 
    ?>
    
    <!-- Back Button -->
    <div class="text-center mt-4">
        <a href="<?php echo base_url('loan/group_file'); ?>" class="btn btn-secondary btn-lg">
            <i class="fas fa-arrow-left mr-2"></i>Back to Group File
        </a>
    </div>
</div>

<!-- Payment Modal -->
<div aria-hidden="true" class="onboarding-modal modal fade" id="batch_payment_modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <span></span><button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Loan Payment</h4>
                <p style="color: red;">Enter deposit amount</p>
                <table id="payment-loan-info">
                    <tr>
                        <td style="text-align: right;padding-right: 10px;" width="150">Loan #</td>
                        <td id="payment-loan-number"></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;padding-right: 10px;" width="150">Customer</td>
                        <td id="payment-customer-name"></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;padding-right: 10px;" width="150">Next Payment #</td>
                        <td id="payment-payment-number"></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;padding-right: 10px;">Payment Amount</td>
                        <td id="payment-amount">MK 0.00</td>
                    </tr>
                </table>
                <form action="<?php echo base_url('loan/pay_loan')?>" class="form-row" method="POST">
                    <div class="form-group col-lg-12" style="padding: 2em;">
                        <label for="amount">Enter deposit amount</label>
                        <input type="hidden" name="loan_id" id="payment-loan-id">
                        <input type="hidden" name="payment_number" id="payment-payment-num">
                        <input style="border: thin red solid;" type="text" class="form-control" name="amount" onkeyup="formatNumber(this)" value="0" placeholder="Enter pay amount" required />
                        <input type="hidden" name="topay_amount" id="payment-topay-amount">
                        <input type="hidden" name="payment_method" value="0">
                        <div class="form-group col-12 mt-3">
                            <label for="pdate">Payment date</label>
                            <input type="datetime-local" class="form-control" name="pdate" id="pdate" />
                        </div>
                    </div>
                    <button class="btn btn-sm btn-block btn-danger" type="submit">Submit Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Pay Off Modal -->
<div aria-hidden="true" class="onboarding-modal modal fade" id="batch_payoff_modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <span></span><button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Loan Pay Off</h4>
                <p style="color: orange;">Are you sure you want to finish loan payments? Please check below calculations first.</p>
                <p style="color: red;">This kind of transaction cannot be reversed - do it with caution!</p>
                <table id="payoff-loan-info">
                    <tr>
                        <td style="text-align: right;padding-right: 10px;" width="150">Loan #</td>
                        <td id="payoff-loan-number"></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;padding-right: 10px;" width="150">Customer</td>
                        <td id="payoff-customer-name"></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;padding-right: 10px;" width="150">Payment #</td>
                        <td id="payoff-payment-number"></td>
                    </tr>
                    <tr>
                        <td style="text-align: right;padding-right: 10px;">Pay Off Amount</td>
                        <td id="payoff-amount">MK 0.00</td>
                    </tr>
                </table>
                <form action="<?php echo base_url('loan/finish_loan')?>" class="form-row" method="POST">
                    <div class="form-group col-lg-12" style="padding: 2em;">
                        <label for="amount">Pay Off Amount</label>
                        <input type="hidden" name="loan_id" id="payoff-loan-id">
                        <input type="hidden" name="payment_number" id="payoff-payment-num">
                        <input type="hidden" name="repay_amounts" id="payoff-repay-amounts">
                        <input type="hidden" name="totalbalance" id="payoff-total-balance">
                        <input type="hidden" name="middlepayment" id="payoff-middle-payment">
                        <input style="border: thin red solid;" type="text" class="form-control" name="amount" id="payoff-amount-input" readonly required />
                        <input type="hidden" name="payment_method" value="0">
                        <input type="hidden" name="pay_proof" value="Null">
                        <div class="form-group col-12 mt-3">
                            <label for="pdate">Payment date</label>
                            <input type="date" class="form-control" name="pdate" id="payoff-pdate" />
                        </div>
                    </div>
                    <button class="btn btn-sm btn-block btn-danger" type="submit">Pay Off Loan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles for Group Batch Loans */
.loan-section {
    border: 2px solid #e3e6f0;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.loan-section:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.loan-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px 8px 0 0;
}

.section-title {
    color: #5a5c69;
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e3e6f0;
}

.loan-info-table {
    width: 100%;
    margin-bottom: 1rem;
}

.loan-info-table td {
    padding: 0.4rem 0;
    vertical-align: top;
}

.loan-info-table td:first-child {
    font-weight: 500;
    color: #5a5c69;
    width: 40%;
    padding-right: 1rem;
}

.stat-card {
    text-align: center;
    padding: 1rem;
    background: #f8f9fc;
    border-radius: 8px;
    border-left: 4px solid #4e73df;
}

.stat-card h5 {
    margin-bottom: 0.5rem;
    color: #5a5c69;
    font-size: 0.9rem;
    font-weight: 500;
}

.stat-card h3 {
    margin: 0;
    font-weight: 700;
}

.badge-lg {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}

.progress {
    height: 10px;
    border-radius: 5px;
    overflow: hidden;
    background-color: #e9ecef;
}

.progress-bar {
    transition: width 0.6s ease;
}

.loan-separator {
    display: flex;
    align-items: center;
    margin: 2rem 0 0 0;
    padding: 1rem;
    background: #f8f9fc;
}

.separator-line {
    flex: 1;
    height: 2px;
    background: linear-gradient(to right, transparent, #e3e6f0, transparent);
}

.separator-text {
    padding: 0 2rem;
    font-weight: 600;
    color: #858796;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.border-right {
    border-right: 1px solid #e3e6f0 !important;
}

@media (max-width: 768px) {
    .border-right {
        border-right: none !important;
        border-bottom: 1px solid #e3e6f0;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
    }
}

.btn-sm {
    margin: 0.2rem;
}

.card-header {
    font-weight: 500;
}

/* Amortization Section Styles */
.amortization-section {
    border-top: 1px solid #e3e6f0;
    animation: slideDown 0.3s ease-in-out;
}

.amortization-table {
    font-size: 0.9rem;
    margin-bottom: 0;
}

.amortization-table th {
    background-color: #f8f9fc;
    color: #5a5c69;
    font-weight: 600;
    padding: 0.75rem 0.5rem;
    border-color: #e3e6f0;
    font-size: 0.85rem;
}

.amortization-table td {
    text-align: center;
    padding: 0.6rem 0.4rem;
    vertical-align: middle;
    border-color: #e3e6f0;
    font-size: 0.85rem;
}

.amortization-table .table-success {
    background-color: rgba(40, 167, 69, 0.1);
}

.amortization-table .table-danger {
    background-color: rgba(220, 53, 69, 0.1);
}

.amortization-table .table-warning {
    background-color: rgba(255, 193, 7, 0.1);
}

@keyframes slideDown {
    from {
        opacity: 0;
        max-height: 0;
    }
    to {
        opacity: 1;
        max-height: 1000px;
    }
}

.table-responsive {
    max-height: 400px;
    overflow-y: auto;
}
</style>

<script>
function toggleAmortization(loanId) {
    var amortizationSection = document.getElementById('amortization-' + loanId);
    var button = event.target;
    
    if (amortizationSection.style.display === 'none' || amortizationSection.style.display === '') {
        amortizationSection.style.display = 'block';
        button.innerHTML = '<i class="fas fa-eye-slash mr-1"></i>Hide Amortization';
        button.classList.remove('btn-warning');
        button.classList.add('btn-secondary');
    } else {
        amortizationSection.style.display = 'none';
        button.innerHTML = '<i class="fas fa-table mr-1"></i>View Amortization';
        button.classList.remove('btn-secondary');
        button.classList.add('btn-warning');
    }
}

function payLoanBatch(loanId, loanNumber) {
    // Get loan information via AJAX to populate the modal
    $.ajax({
        url: '<?php echo base_url("loan/get_next_payment_info"); ?>',
        type: 'POST',
        data: {
            loan_id: loanId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Populate modal with loan information
                $('#payment-loan-number').text(loanNumber);
                $('#payment-customer-name').text(response.customer_name);
                $('#payment-payment-number').text(response.payment_number);
                $('#payment-amount').text('MK ' + response.amount_formatted);
                
                // Set form values
                $('#payment-loan-id').val(loanId);
                $('#payment-payment-num').val(response.payment_number);
                $('#payment-topay-amount').val(response.amount);
                
                // Show the modal
                $("#batch_payment_modal").modal('show');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error fetching payment information');
        }
    });
}

function formatNumber(input) {
    // Remove any non-digit characters except for decimal point
    let value = input.value.replace(/[^\d.]/g, '');
    
    // Format the number with commas
    if (value) {
        let parts = value.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        input.value = parts.join('.');
    }
}

function recommendBatch(batchNumber) {
    if (!confirm('Are you sure you want to recommend ALL loans in batch ' + batchNumber + '? This action will change the status of all loans in this batch to RECOMMENDED.')) {
        return;
    }
    
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    button.disabled = true;
    
    $.ajax({
        url: '<?php echo base_url("loan/batch_recommend"); ?>',
        type: 'POST',
        data: {
            batch: batchNumber
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Success: ' + response.message);
                // Refresh the page to show updated statuses
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            alert('Error: Failed to process batch recommendation. Please try again.');
            console.error('AJAX Error:', error);
        },
        complete: function() {
            // Restore button state
            button.innerHTML = originalText;
            button.disabled = false;
        }
    });
}

function approveBatch(batchNumber) {
    if (!confirm('Are you sure you want to approve ALL RECOMMENDED loans in batch ' + batchNumber + '? This action will change the status of all RECOMMENDED loans in this batch to APPROVED.')) {
        return;
    }
    
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    button.disabled = true;
    
    $.ajax({
        url: '<?php echo base_url("loan/batch_approve"); ?>',
        type: 'POST',
        data: {
            batch: batchNumber
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Success: ' + response.message);
                // Refresh the page to show updated statuses
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            alert('Error: Failed to process batch approval. Please try again.');
            console.error('AJAX Error:', error);
        },
        complete: function() {
            // Restore button state
            button.innerHTML = originalText;
            button.disabled = false;
        }
    });
}

function disburseBatch(batchNumber) {
    if (!confirm('Are you sure you want to disburse ALL APPROVED loans in batch ' + batchNumber + '? This action will:\n- Change status to ACTIVE\n- Create payment schedules\n- Process cash transactions\n- Send SMS notifications (if enabled)\n\nThis action cannot be undone.')) {
        return;
    }
    
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    button.disabled = true;
    
    $.ajax({
        url: '<?php echo base_url("loan/batch_disburse"); ?>',
        type: 'POST',
        data: {
            batch: batchNumber
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Success: ' + response.message);
                // Refresh the page to show updated statuses
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            alert('Error: Failed to process batch disbursement. Please try again.');
            console.error('AJAX Error:', error);
        },
        complete: function() {
            // Restore button state
            button.innerHTML = originalText;
            button.disabled = false;
        }
    });
}

function payOffLoanBatch(loanId, loanNumber) {
    // Get loan pay off information via AJAX
    $.ajax({
        url: '<?php echo base_url("loan/get_payoff_info"); ?>',
        type: 'POST',
        data: {
            loan_id: loanId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Populate modal with loan information
                $('#payoff-loan-number').text(loanNumber);
                $('#payoff-customer-name').text(response.customer_name);
                $('#payoff-payment-number').text(response.payment_number);
                $('#payoff-amount').text('MK ' + response.payoff_amount_formatted);
                
                // Set form values
                $('#payoff-loan-id').val(loanId);
                $('#payoff-payment-num').val(response.payment_number);
                $('#payoff-repay-amounts').val(response.repay_amounts);
                $('#payoff-total-balance').val(response.total_balance);
                $('#payoff-middle-payment').val(response.middle_payment);
                $('#payoff-amount-input').val(response.payoff_amount);
                
                // Show the modal
                $("#batch_payoff_modal").modal('show');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error fetching pay off information');
        }
    });
}
</script>