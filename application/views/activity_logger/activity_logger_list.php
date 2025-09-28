<!-- Content Wrapper START -->
<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Audit trail</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">Audit trail</a>
                <span class="breadcrumb-item active">System users trails</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick green solid;border-radius: 14px;">
            <div class="m-t-25">
                <table id="data-table1" class="table">
                    <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Activity</th>
                        <th>System Time</th>
                        <th>Server Time</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($activity_logger_data as $activity_logger)
                    {
                        ?>
                        <tr>
                            <td><?php echo $activity_logger->Firstname ." ".$activity_logger->Lastname?></td>
                            <td><?php echo $activity_logger->activity ?></td>
                            <td><?php echo $activity_logger->system_time ?></td>
                            <td><?php echo $activity_logger->server_time ?></td>
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

