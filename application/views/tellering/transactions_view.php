<style>
.report-card {
    border: 2px solid #153505;
    border-radius: 14px;
    padding: 25px;
    margin-bottom: 20px;
    background: #fff;
}
.report-card h4 {
    color: #153505;
    font-weight: 700;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e9ecef;
}
.report-card .form-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
}
.report-card label {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
    display: block;
    font-size: 13px;
}
.report-card input[type="date"] {
    border: 1px solid #ced4da;
    border-radius: 6px;
    padding: 8px 12px;
    width: 100%;
    font-size: 14px;
    transition: border-color 0.2s;
}
.report-card input[type="date"]:focus {
    border-color: #153505;
    outline: none;
    box-shadow: 0 0 0 3px rgba(21, 53, 5, 0.1);
}
.btn-green {
    background-color: #153505;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s;
}
.btn-green:hover {
    background-color: #1e4a08;
    color: #fff;
}
.info-badge {
    display: inline-block;
    background: #e8f5e9;
    color: #2e7d32;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-top: 10px;
}
</style>
<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Loan Transactions</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">Loan Transactions</span>
            </nav>
        </div>
    </div>

    <!-- Background Report Generation Section -->
    <div class="card">
        <div class="report-card">
            <h4><i class="fa fa-cogs" style="margin-right:8px;"></i>Generate Loan Deposits Report (Background)</h4>
            <p style="color:#666; margin-bottom:15px;">For large datasets, generate the report in the background. You will be redirected to the Reports page to track progress and download when ready.</p>
            <form action="<?php echo base_url('Tellering/generate_loan_deposits_report') ?>" method="POST">
                <div class="form-section">
                    <div class="row">
                        <div class="col-md-4">
                            <label>From Date</label>
                            <input type="date" name="from" value="">
                        </div>
                        <div class="col-md-4">
                            <label>To Date</label>
                            <input type="date" name="to" value="">
                        </div>
                        <div class="col-md-4" style="display:flex; align-items:flex-end; padding-bottom:2px;">
                            <button type="submit" class="btn-green"><i class="fa fa-rocket" style="margin-right:5px;"></i>Generate Report</button>
                        </div>
                    </div>
                </div>
                <span class="info-badge"><i class="fa fa-clock" style="margin-right:5px;"></i>Leave dates empty to include all dates. Report processes in the background.</span>
            </form>
        </div>
    </div>
</div>
