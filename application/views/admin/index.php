<div class="main-content">
    <div class="page-header no-gutters has-tab" style="margin-bottom: 2px !important;">
        <h2 class="font-weight-normal">WELCOME- <?php echo $this->session->userdata('Firstname')?></h2>
    </div>

    <!-- Dashboard Content -->
    <div class="container-fluid">
        <!-- Stats Cards Row -->



        <!-- Recent Reports and Activities -->
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4 class="card-title">Recent Activities</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="m-t-10">
                            <?php
                            $this->db->order_by('server_time', 'DESC');
                            $this->db->where('user_id', $this->session->userdata('user_id'));
                            $this->db->limit(5);
                            $recent_activities = $this->db->get('activity_logger')->result();

                            if(empty($recent_activities)) {
                                echo '<div class="p-3">No recent activities found.</div>';
                            } else {
                                echo '<ul class="timeline timeline-alt m-0 p-3">';
                                foreach($recent_activities as $activity) {
                                    echo '<li class="timeline-item">
                                            <div class="timeline-item-head">
                                                <div class="avatar avatar-icon avatar-sm avatar-blue">
                                                    <i class="anticon anticon-check"></i>
                                                </div>
                                            </div>
                                            <div class="timeline-item-content">
                                                <div>'.$activity->activity.'</div>
                                                <span class="text-muted text-sm">'.timeago($activity->server_time).'</span>
                                            </div>
                                        </li>';
                                }
                                echo '</ul>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="#" class="btn btn-primary btn-sm">View All Activities</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4 class="card-title">Recent Reports</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $this->db->order_by('generated_time', 'DESC');
                                $this->db->limit(5);
                                $recent_reports = $this->db->get('reports')->result();

                                foreach($recent_reports as $report) {
                                    $badge_class = ($report->status == 'completed') ? 'badge-success' : 'badge-warning';
                                    $download_btn = ($report->download_link && $report->status == 'completed') ?
                                        '<a href="'.base_url('bulk_report/'.$report->download_link).'" class="btn btn-sm btn-primary"><i class="anticon anticon-download"></i></a>' :
                                        '<button disabled class="btn btn-sm btn-default"><i class="anticon anticon-download"></i></button>';

                                    echo '<tr>
                                                <td>'.$report->report_type.'</td>
                                                <td><span class="badge '.$badge_class.'">'.$report->status.'</span></td>
                                                <td>'.date('M d, Y', strtotime($report->generated_time)).'</td>
                                                <td>'.$download_btn.'</td>
                                            </tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="<?php echo base_url('report') ?>" class="btn btn-primary btn-sm">View All Reports</a>
                    </div>
                </div>
            </div>

        </div>


    </div>
</div>



<?php
// Helper function for showing time ago
function timeago($datetime) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->d >= 1) {
        return date('M d', strtotime($datetime));
    } elseif ($diff->h >= 1) {
        return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    } elseif ($diff->i >= 1) {
        return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    } else {
        return 'just now';
    }
}
?>