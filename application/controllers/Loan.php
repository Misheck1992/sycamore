<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require APPPATH . '/libraries/FPDF.php';

class Loan extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Loan_model');
        $this->load->model('Groups_model');
        $this->load->model('Customer_groups_model');
        $this->load->model('charges_model');
        $this->load->model('Account_model');
        $this->load->model('Individual_customers_model');
        $this->load->model('Loan_products_model');
        $this->load->model('Transactions_model');
        $this->load->model('Group_loan_tracker_model');
        $this->load->model('Tellering_model');
        $this->load->model('Collateral_model');

        $this->load->model('Loan_products_model');
        $this->load->model('Payement_schedules_model');
        $this->load->model('Loan_customer_first_drafr_model');
        $this->load->library('form_validation');

    }
    public function file_add(){
        $this->load->view('import');
    }

    // Helper function to get customer name for activity logging
    private function get_customer_name_for_loan($loan_id) {
        $loan_details = get_by_id('loan', 'loan_id', $loan_id);
        $customer_name = '';

        if (!empty($loan_details)) {
            if ($loan_details->customer_type == 'individual') {
                $customer = get_by_id('individual_customers', 'id', $loan_details->loan_customer);
                if (!empty($customer)) {
                    $customer_name = $customer->Firstname . ' ' . $customer->Lastname;
                }
            } elseif ($loan_details->customer_type == 'group') {
                $group = get_by_id('groups', 'group_id', $loan_details->loan_customer);
                if (!empty($group)) {
                    $customer_name = $group->group_name;
                }
            }
        }

        return array(
            'customer_name' => $customer_name,
            'loan_details' => $loan_details
        );
    }
    public function correct_loan(){
        $this->Loan_model->delete_replace_loans();
    }
    function update_figures(){
        $l = $this->Loan_model->get_all_by_product();
        $count = 0;
        foreach ($l as $item){

            echo $item->loan_principal;
            $count ++;
        }
        echo "total count".$count;
    }
    function test_hhtp(){


        $a = false;
        if ($a){
            http_response_code(200);
        }else{
            http_response_code(401);

        }
        echo json_encode(array('message'=>"zathela","data"=>array("name"=>"misheck")));
    }
// 	function import_preview()
// 	{
// 		if(isset($_FILES["file"]["name"])) {
// 			$path = $_FILES["file"]["tmp_name"];
// 			$object = PHPExcel_IOFactory::load($path);
// 			foreach ($object->getWorksheetIterator() as $worksheet) {
// 				$highestRow = $worksheet->getHighestRow();
// 				$highestColumn = $worksheet->getHighestColumn();
// 				for ($row = 2; $row <= $highestRow; $row++) {


// 					$title = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
// 					$fname = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
// 					$mdame = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
// 					$lastname = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
// 					$gender = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
// 					$dob = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
// 					$phone = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
// 					$village = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
// 					$ta = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
// 					$group_name = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
// 					$city = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
// 					$marital = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
// 					$country = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
// 					$mresidential_status = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
// 					$profession = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
// 					$source_of_income = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
// 					$gross = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
// 					$customer_created_on = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
// 					$loan_number = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
// 					$loan_product = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
// 					$loan_effective_date = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
// 					$pricipal = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
// 					$loan_period = $worksheet->getCellByColumnAndRow(22, $row)->getValue();
// 					$period_type = $worksheet->getCellByColumnAndRow(23, $row)->getValue();
// 					$interest = $worksheet->getCellByColumnAndRow(24, $row)->getValue();
// 					$next_payment_number = $worksheet->getCellByColumnAndRow(25, $row)->getValue();
// 					$loan_added_by = $worksheet->getCellByColumnAndRow(26, $row)->getValue();
// 					$loan_status = $worksheet->getCellByColumnAndRow(27, $row)->getValue();
// 					$loan_added_date = $worksheet->getCellByColumnAndRow(28, $row)->getValue();
// 					$total_repaid = $worksheet->getCellByColumnAndRow(29, $row)->getValue();
// 					$pricipal_paid = $worksheet->getCellByColumnAndRow(30, $row)->getValue();
// 					$interest_paid = $worksheet->getCellByColumnAndRow(31, $row)->getValue();


// 					//$added_by = $this->session->userdata('istitution_code');
// 					$data = array(
// 						'Title' => $title,
// 						'Firstname' => $fname,
// 						'Middlename' => $mdame,
// 						'Lastname' => $lastname,
// 						'Gender' => $gender,
// 						'DateOfBirth' => $dob,
// 						'PhoneNumber' => $phone,
// 						'Village' => $village,
// 						'TA' => $ta,
// 						'ClubName' => $group_name,
// 						'City' => $city,
// 						'MarritalStatus' => $marital,
// 						'Country' => $country,
// 						'ResidentialStatus' => $mresidential_status,
// 						'Profession' => $profession,
// 						'SourceOfIncome' => $source_of_income,
// 						'GrossMonthlyIncome' => $gross,
// 						'CreatedOnCustomer' => $customer_created_on,
// 						'loan_number' => $loan_number,
// 						'loan_product' => $loan_product,
// 						'loan_effectve_date' => $loan_effective_date,
// 						'loan_principal' => $pricipal,
// 						'loan_period' => $loan_period,
// 						'period_type' => $period_type,
// 						'loan_interest' => $interest,
// 						'next_payment_number' => $next_payment_number,
// 						'loan_added_by' => $loan_added_by,
// 						'loan_status' => $loan_status,
// 						'loan_added_date' => $loan_added_date,
// 						'Totalrepaid' => $total_repaid,
// 						'PrincipalPaid' => $pricipal_paid,
// 						'InteresrPaid' => $interest_paid,
// 					);

// 					$this->Loan_customer_first_drafr_model->insert($data);
// 				}



// 			}
// 		}
// 	}
    function add_loan_products(){
        $this->Loan_customer_first_drafr_model->add_loan_products();
    }	function migrate_customer(){
    $this->Loan_customer_first_drafr_model->insert_c();
}
    function convert_date(){
        $r =	$this->Loan_customer_first_drafr_model->get_all();

        foreach ($r as $l){
            $my_date = date('Y-m-d', strtotime($l->CreatedOnCustomer));

            $this->Loan_customer_first_drafr_model->update($l->id,array('CreatedOnCustomer'=>$my_date));
        }


    }
    function convert_date1(){
        $r =	$this->Loan_customer_first_drafr_model->get_all_active();
        $c = 0;
        foreach ($r as $l){
            $this->Loan_model->update1($l->customer_id, array('loan_status'=>'Active'));
        }
        echo $c;

    }
    function update2(){
        $r =	$this->Loan_model->get_all2();
        $c = 0;
        foreach ($r as $l){
            $this->Payement_schedules_model->update1($l->loan_id, array('status'=>'PAID'));
        }
        echo $c;

    }
    function update_loan_payment(){
        $r =	$this->Loan_customer_first_drafr_model->get_all_active();
        $c = 0;
        foreach ($r as $l){
            $this->Loan_model->update1($l->customer_id, array('loan_status'=>'Active'));
        }
        echo $c;

    }
    function add_groups(){
        $this->Loan_customer_first_drafr_model->add_groups();
    }
    function add_customer_to_group(){
        $this->Loan_customer_first_drafr_model->add_customer_to_group();
    }
    function csv_loan_create(){
        $r =	$this->Loan_model->get_all();

        foreach ($r as $l){
            $this->Loan_model->add_loan($l->loan_principal, $l->loan_period, $l->loan_product_id, $l->loan_effectve_date,$l->customer_id,'N/A','N/A',7);

        }


    }
    function reprocess_loan(){
        $r =	$this->Loan_model->get_all_list();

        foreach ($r as $l){
            $this->Loan_model->add_loan_rerun($l->loan_principal, $l->loan_period, $l->loan_product, $l->loan_date,$l->loan_customer,$l->customer_type,$l->loan_number , $l->loan_id);

        }
//print_r($r);
    }
    function get_by_customer($id){
        $res = '<option>-select loan number-</option>';
        $data = $this->Loan_model->get_user_loan($id);

        foreach ($data as $dd){
            $res .='<option value="'.$dd->loan_id.'">'.$dd->loan_number.'</option>';
        }
        echo  $res;

    }

    // Controller function to search for customers
    public function search_customer($customerType) {
        $searchTerm = $this->input->get('search');

        // Adjust the query based on customer type (individual/group)
        if ($customerType == 'individual') {
            $result = get_all_customer('individual_customers', $searchTerm);
        } else {
            $result = get_all_customer('groups', $searchTerm);
        }

        // Prepare the response HTML
        $res = '<option value="">--select customer--</option>';
        foreach ($result as $customer) {
            if ($customerType == 'individual') {
                $res .= '<option value="'. $customer->id.'">'. $customer->Firstname . " " . $customer->Lastname.'</option>';
            } else {
                $res .= '<option value="'. $customer->group_id.'">'. $customer->group_name . " (" . $customer->group_code.')</option>';
            }
        }

        // Log or output the response for debugging
        echo $res;
        // die();  // Add die to stop further execution and check output in the browser
    }


// Controller function to get loans by customer ID
    public function get_by_loan_transact($id) {
        // Fetch the loan data by customer ID
        $data = $this->Loan_model->get_user_loan($id);

        // Prepare the response HTML for the loan dropdown
        $res = '<option value="">--select loan--</option>';
        foreach ($data as $loan) {
            $res .= '<option value="'. $loan->loan_id .'">'. $loan->loan_number . '</option>';
        }
        echo $res;
    }


    function get_by_customer_type($id){


        $res = '<option>-select customer-</option>';
        if($id=='individual'){
            $customer = get_all('individual_customers');

            foreach ($customer as $c){
                $res .='<option value="'. $c->id.'">'.$c->Firstname. " ".$c->Lastname.'</option>';
            }
            echo  $res;
        }
        else{
            $customer = get_all('groups');

            foreach ($customer as $c){
                $res .='<option value="'. $c->group_id.'">'.$c->group_name. " ".$c->group_code.'</option>';
            }
            echo  $res;

        }
        $data = $this->Loan_model->get_user_loan($id);

        foreach ($data as $dd){
            $res .='<option value="'.$dd->loan_id.'">'.$dd->loan_number.'</option>';
        }
        echo  $res;

    }
    function get_charges($id){
        $re = array();
        $charge_value = 0;
        $loan =	$this->Loan_model->get_by_id($id);
        $charge = get_by_id('charges','charge_id','1');
        if($charge->charge_type=="Fixed"){
            $charge_value = $charge->fixed_amount;
        }elseif($charge->charge_type=="Variable"){
            $charge_value =  ($charge->variable_value/100) *  ($loan->loan_principal);

        }
        echo $charge_value;


    }
    function get_late_charg($id){
        $re = array();
        $charge_value = 0;

        $loan =	$this->Loan_model->get_by_id($id);
        $loanproduct =	$this->Loan_products_model->get_by_id($loan->loan_product);

        $charge_value =  ($loanproduct->penalty/100) *  ($loan->loan_amount_term);


        echo $charge_value;


    }

    public function add(){
        $data['customers'] =$this->Individual_customers_model->get_all_active();
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/add_loan',$data);
        $this->load->view('admin/footer');
    }
    public function add_group(){
        $data['customers'] =$this->Groups_model->get_all_active();
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/add_loan_group',$data);
        $this->load->view('admin/footer');
    }
    
    public function add_group_members(){
        $data['customers'] = $this->Groups_model->get_all_active();
        
        // Generate batch number: BATCH + current date + random number
        $batch_number = 'BATCH' . date('Ymd') . rand(1000, 9999);
        $data['batch_number'] = $batch_number;
        
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/add_group_member_loans',$data);
        $this->load->view('admin/footer');
    }
    
    public function group_file(){
        $data['groups'] = $this->Groups_model->get_all_active();
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/group_file',$data);
        $this->load->view('admin/footer');
    }
    
    public function get_group_batches(){
        // Set content type to JSON
        header('Content-Type: application/json');
        
        try {
            $group_id = $this->input->post('group_id');
            
            if (!$group_id) {
                echo json_encode(['success' => false, 'message' => 'Group ID is required']);
                return;
            }
            
            // Check if database connection exists
            if (!$this->db) {
                echo json_encode(['success' => false, 'message' => 'Database connection not available']);
                return;
            }
            
            // Get distinct batches for the group
            $this->db->distinct()
                     ->select('batch')
                     ->from('loan')
                     ->where('group_id', $group_id)
                     ->where('batch IS NOT NULL')
                     ->where('batch !=', '')
                     ->order_by('batch', 'DESC');
            
            $batches = $this->db->get()->result();
            
            // Check for database errors
            if ($this->db->error()['code'] != 0) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $this->db->error()['message']]);
                return;
            }
            
            if ($batches && count($batches) > 0) {
                echo json_encode(['success' => true, 'batches' => $batches]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No batches found for this group']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
        }
    }
    
    public function group_batch_loans($batch = null){
        if (!$batch) {
            redirect('loan/group_file');
            return;
        }
        
        // Get all loans for the selected batch
        $this->db->select('loan.*, individual_customers.Firstname, individual_customers.Lastname, 
                          individual_customers.ClientId, loan_products.product_name,
                          groups.group_name, groups.group_code')
                 ->from('loan')
                 ->join('individual_customers', 'individual_customers.id = loan.loan_customer', 'left')
                 ->join('loan_products', 'loan_products.loan_product_id = loan.loan_product', 'left')
                 ->join('groups', 'groups.group_id = loan.group_id', 'left')
                 ->where('loan.batch', $batch)
                 ->order_by('loan.loan_id', 'ASC');
        
        $data['loans'] = $this->db->get()->result();
        $data['batch'] = $batch;
        
        // Check user permissions for batch actions
        $data['permissions'] = $this->get_user_batch_permissions();
        
        if (empty($data['loans'])) {
            $this->session->set_flashdata('error', 'No loans found for this batch');
            redirect('loan/group_file');
            return;
        }
        
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/group_batch_loans',$data);
        $this->load->view('admin/footer');
    }
    
    private function get_user_batch_permissions() {
        // Get user's accessible menu items from session
        $user_access = $this->session->userdata('access');
        
        if(empty($user_access)) {
            return array(
                'can_pay_off' => false,
                'can_pay' => false,
                'can_disburse' => false,
                'can_recommend' => false,
                'can_approve' => false
            );
        }
        
        // Get the menu item IDs that user has access to
        $accessible_ids = array();
        foreach($user_access as $access) {
            $accessible_ids[] = $access->controllerid;
        }
        
        if(empty($accessible_ids)) {
            return array(
                'can_pay_off' => false,
                'can_pay' => false,
                'can_disburse' => false,
                'can_recommend' => false,
                'can_approve' => false
            );
        }
        
        // Check for specific group loan permissions in menuitems table
        $this->db->select('method');
        $this->db->from('menuitems');
        $this->db->where_in('id', $accessible_ids);
        $this->db->where_in('method', array(
            'group_loan.pay_off',
            'group_loan.pay', 
            'group_loan.disburse',
            'group_loan.recommend',
            'group_loan.approve'
        ));
        $user_methods = $this->db->get()->result();
        
        // Convert to simple array of methods
        $methods = array();
        foreach($user_methods as $method) {
            $methods[] = $method->method;
        }
        
        // Return permission flags
        return array(
            'can_pay_off' => in_array('group_loan.pay_off', $methods),
            'can_pay' => in_array('group_loan.pay', $methods),
            'can_disburse' => in_array('group_loan.disburse', $methods),
            'can_recommend' => in_array('group_loan.recommend', $methods),
            'can_approve' => in_array('group_loan.approve', $methods)
        );
    }
    
    public function batch_recommend(){
        header('Content-Type: application/json');
        
        $batch = $this->input->post('batch');
        
        if(!$batch) {
            echo json_encode(['success' => false, 'message' => 'Batch number is required']);
            return;
        }
        
        try {
            // Get all loans in this batch
            $this->db->where('batch', $batch);
            $this->db->where('loan_status !=', 'DELETED');
            $loans = $this->db->get('loan')->result();
            
            if(empty($loans)) {
                echo json_encode(['success' => false, 'message' => 'No loans found in this batch']);
                return;
            }
            
            $recommended_count = 0;
            $skipped_count = 0;
            $errors = [];
            
            foreach($loans as $loan) {
                // Skip already recommended loans
                if($loan->loan_status == 'RECOMMENDED') {
                    $skipped_count++;
                    continue;
                }
                
                // Update loan status to RECOMMENDED
                $update_data = array(
                    'loan_status' => 'RECOMMENDED',
                    'loan_approved_by' => $this->session->userdata('user_id'),
                    'approved_date' => date('Y-m-d H:i:s')
                );
                
                $this->Loan_model->update($loan->loan_id, $update_data);
                
                // Check if the update was successful by verifying the database
                $this->db->where('loan_id', $loan->loan_id);
                $this->db->where('loan_status', 'RECOMMENDED');
                $updated_loan = $this->db->get('loan')->row();
                
                if($updated_loan) {
                    $recommended_count++;
                    
                    // Log activity
                    $logger = array(
                        'user_id' => $this->session->userdata('user_id'),
                        'activity' => 'RECOMMENDED a loan (Batch: '.$batch.')'
                    );
                    log_activity($logger);
                } else {
                    $errors[] = 'Failed to recommend loan: ' . $loan->loan_number;
                }
            }
            
            $message = "Batch recommendation completed. ";
            $message .= "Recommended: {$recommended_count} loans. ";
            if($skipped_count > 0) {
                $message .= "Skipped: {$skipped_count} (already recommended). ";
            }
            if(!empty($errors)) {
                $message .= "Errors: " . implode(', ', $errors);
            }
            
            echo json_encode([
                'success' => true,
                'message' => $message,
                'recommended_count' => $recommended_count,
                'skipped_count' => $skipped_count,
                'error_count' => count($errors)
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function batch_approve(){
        header('Content-Type: application/json');
        
        $batch = $this->input->post('batch');
        
        if(!$batch) {
            echo json_encode(['success' => false, 'message' => 'Batch number is required']);
            return;
        }
        
        try {
            // Get all RECOMMENDED loans in this batch
            $this->db->where('batch', $batch);
            $this->db->where('loan_status', 'RECOMMENDED');
            $loans = $this->db->get('loan')->result();
            
            if(empty($loans)) {
                echo json_encode(['success' => false, 'message' => 'No RECOMMENDED loans found in this batch']);
                return;
            }
            
            $approved_count = 0;
            $errors = [];
            
            foreach($loans as $loan) {
                // Update loan status to APPROVED
                $update_data = array(
                    'loan_status' => 'APPROVED',
                    'loan_approved_by' => $this->session->userdata('user_id'),
                    'approved_date' => date('Y-m-d H:i:s')
                );
                
                $this->Loan_model->update($loan->loan_id, $update_data);
                
                // Check if the update was successful by verifying the database
                $this->db->where('loan_id', $loan->loan_id);
                $this->db->where('loan_status', 'APPROVED');
                $updated_loan = $this->db->get('loan')->row();
                
                if($updated_loan) {
                    $approved_count++;
                    
                    // Log activity
                    $logger = array(
                        'user_id' => $this->session->userdata('user_id'),
                        'activity' => 'APPROVED a loan (Batch: '.$batch.')'
                    );
                    log_activity($logger);
                } else {
                    $errors[] = 'Failed to approve loan: ' . $loan->loan_number;
                }
            }
            
            $message = "Batch approval completed. ";
            $message .= "Approved: {$approved_count} loans. ";
            if(!empty($errors)) {
                $message .= "Errors: " . implode(', ', $errors);
            }
            
            echo json_encode([
                'success' => true,
                'message' => $message,
                'approved_count' => $approved_count,
                'error_count' => count($errors)
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function batch_disburse(){
        header('Content-Type: application/json');
        
        $batch = $this->input->post('batch');
        
        if(!$batch) {
            echo json_encode(['success' => false, 'message' => 'Batch number is required']);
            return;
        }
        
        try {
            // Get all APPROVED loans in this batch
            $this->db->where('batch', $batch);
            $this->db->where('loan_status', 'APPROVED');
            $loans = $this->db->get('loan')->result();
            
            if(empty($loans)) {
                echo json_encode(['success' => false, 'message' => 'No APPROVED loans found in this batch']);
                return;
            }
            
            $disbursed_count = 0;
            $errors = [];
            
            foreach($loans as $loan) {
                try {
                    // Process cash transaction (same as individual disbursement)
                    $this->cash_transaction($loan->loan_id);
                    
                    // Process pay off loan (activates the loan and creates payment schedule)
                    $this->pay_off_loan($loan->loan_id);
                    
                    // Update loan status to ACTIVE
                    $update_data = array(
                        'loan_status' => 'ACTIVE',
                        'disbursed' => 'Yes',
                        'disbursed_by' => $this->session->userdata('user_id'),
                        'disbursed_date' => date('Y-m-d H:i:s')
                    );
                    
                    $this->Loan_model->update($loan->loan_id, $update_data);
                    
                    // Check if the update was successful by verifying the database
                    $this->db->where('loan_id', $loan->loan_id);
                    $this->db->where('loan_status', 'ACTIVE');
                    $updated_loan = $this->db->get('loan')->row();
                    
                    if($updated_loan) {
                        $disbursed_count++;
                        
                        // Log activity
                        $logger = array(
                            'user_id' => $this->session->userdata('user_id'),
                            'activity' => 'DISBURSED a loan (Batch: '.$batch.')'
                        );
                        log_activity($logger);
                        
                        // Send SMS notification if enabled
                        $notify = get_by_id('sms_settings','id','1');
                        if($notify->loan_disbursement=='Yes') {
                            $customer = $this->Loan_model->loan_user($loan->loan_id);
                            send_sms($customer->PhoneNumber,'Dear customer, loan has been approved, you can call numbers below for more');
                        }
                    } else {
                        $errors[] = 'Failed to disburse loan: ' . $loan->loan_number;
                    }
                    
                } catch (Exception $e) {
                    $errors[] = 'Error disbursing loan ' . $loan->loan_number . ': ' . $e->getMessage();
                }
            }
            
            $message = "Batch disbursement completed. ";
            $message .= "Disbursed: {$disbursed_count} loans. ";
            if(!empty($errors)) {
                $message .= "Errors: " . implode(', ', $errors);
            }
            
            echo json_encode([
                'success' => true,
                'message' => $message,
                'disbursed_count' => $disbursed_count,
                'error_count' => count($errors)
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function get_next_payment_info(){
        header('Content-Type: application/json');
        
        $loan_id = $this->input->post('loan_id');
        
        if(!$loan_id) {
            echo json_encode(['success' => false, 'message' => 'Loan ID is required']);
            return;
        }
        
        try {
            // Get loan details
            $loan = $this->Loan_model->get_by_id($loan_id);
            if(!$loan) {
                echo json_encode(['success' => false, 'message' => 'Loan not found']);
                return;
            }
            
            // Get customer name
            $customer_name = '';
            if($loan->customer_type == 'individual') {
                $customer = $this->Individual_customers_model->get_by_id($loan->loan_customer);
                $customer_name = $customer ? $customer->Firstname . ' ' . $customer->Lastname : 'Unknown Customer';
            } else {
                $group = $this->Groups_model->get_by_id($loan->loan_customer);
                $customer_name = $group ? $group->group_name : 'Unknown Group';
            }
            
            // Get next payment details - find the first unpaid payment
            $this->db->where('loan_id', $loan_id);
            $this->db->where('status', 'NOT PAID');
            $this->db->order_by('payment_number', 'ASC');
            $this->db->limit(1);
            $next_payment = $this->db->get('payement_schedules')->row();
            
            if(!$next_payment) {
                echo json_encode(['success' => false, 'message' => 'No pending payments found']);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'customer_name' => $customer_name,
                'payment_number' => $next_payment->payment_number,
                'amount' => $next_payment->amount,
                'amount_formatted' => number_format($next_payment->amount, 2),
                'due_date' => $next_payment->payment_schedule
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function get_payoff_info(){
        header('Content-Type: application/json');
        
        $loan_id = $this->input->post('loan_id');
        
        if(!$loan_id) {
            echo json_encode(['success' => false, 'message' => 'Loan ID is required']);
            return;
        }
        
        try {
            // Get loan details
            $loan = $this->Loan_model->get_by_id($loan_id);
            if(!$loan) {
                echo json_encode(['success' => false, 'message' => 'Loan not found']);
                return;
            }
            
            // Get customer name
            $customer_name = '';
            if($loan->customer_type == 'individual') {
                $customer = $this->Individual_customers_model->get_by_id($loan->loan_customer);
                $customer_name = $customer ? $customer->Firstname . ' ' . $customer->Lastname : 'Unknown Customer';
            } else {
                $group = $this->Groups_model->get_by_id($loan->loan_customer);
                $customer_name = $group ? $group->group_name : 'Unknown Group';
            }
            
            // Get next payment details - find the first unpaid payment
            $this->db->where('loan_id', $loan_id);
            $this->db->where('status', 'NOT PAID');
            $this->db->order_by('payment_number', 'ASC');
            $this->db->limit(1);
            $next_payment = $this->db->get('payement_schedules')->row();
            
            if(!$next_payment) {
                echo json_encode(['success' => false, 'message' => 'No pending payments found']);
                return;
            }
            
            // Get all payments for this loan for payoff calculation
            $this->db->where('loan_id', $loan_id);
            $this->db->order_by('payment_number', 'ASC');
            $payments = $this->db->get('payement_schedules')->result();
            
            // Calculate pay off amount (same logic as original pay off page)
            $loan_period = $loan->loan_period;
            $halfSchedules = $loan_period / 2;
            $total_payoff = 0;
            $v = $this->getMedianSchedule($loan_period);
            
            // Get middle schedule
            $this->db->where('payment_number', $v);
            $this->db->where('loan_id', $loan_id);
            $get_middle_schedule = $this->db->get('payement_schedules')->row();
            
            if(!$get_middle_schedule) {
                echo json_encode(['success' => false, 'message' => 'Could not find middle payment schedule']);
                return;
            }
            
            // Check if the current schedule is less than 50% of the total schedules
            if ($next_payment->payment_number <= $v) {
                // Loop through only $v schedules
                for ($i = 0; $i < $v; $i++) {
                    if(isset($payments[$i])) {
                        $total_payoff += $payments[$i]->amount;
                    }
                }
            } else {
                $total_payoff = $get_middle_schedule->amount;
            }
            
            $payoff_amount = $total_payoff + $get_middle_schedule->loan_balance;
            $total_balance = $get_middle_schedule->loan_balance + $get_middle_schedule->amount;
            
            echo json_encode([
                'success' => true,
                'customer_name' => $customer_name,
                'payment_number' => $next_payment->payment_number,
                'payoff_amount' => $payoff_amount,
                'payoff_amount_formatted' => number_format($payoff_amount, 2),
                'repay_amounts' => $total_payoff,
                'total_balance' => $total_balance,
                'middle_payment' => $v
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public function get_group_members(){
        // Set content type to JSON
        header('Content-Type: application/json');
        
        $group_id = $this->input->post('group_id');
        
        // Debug: Log the received group_id
        log_message('debug', 'Group ID received: ' . $group_id);
        
        if($group_id){
            try {
                // First check if group exists
                $group = $this->Groups_model->get_by_id($group_id);
                if(!$group) {
                    echo json_encode(['error' => 'Group not found with ID: ' . $group_id]);
                    return;
                }
                
                // Get members
                $members = $this->Customer_groups_model->get_members($group_id);
                
                // Debug: Log member count
                log_message('debug', 'Members found: ' . count($members));
                
                if(empty($members)) {
                    echo json_encode(['error' => 'No members found for group: ' . $group->group_name]);
                    return;
                }
                
                echo json_encode($members);
            } catch (Exception $e) {
                log_message('error', 'Error in get_group_members: ' . $e->getMessage());
                echo json_encode(['error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['error' => 'No group ID provided']);
        }
    }
    public function calculator(){
        $data['result'] = '';
        $menu_toggle['toggles'] = 41;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/calculator',$data);
        $this->load->view('admin/footer');
    }
    function calculate(){
        $id = $this->input->get('loan_type');
        $exist = $this->Loan_products_model->get_by_id($id);

        if ($exist) {
            if($exist->method=="Straight line"){
                $principal = (($exist->processing_fees/100)*$this->input->get('amount'))+$this->input->get('amount');
                $result = $this->calculate_amortization_weekly($principal,$this->input->get('loan_type'),$this->input->get('months'),$this->input->get('loan_date'));
                $data['result'] = $result;
                $menu_toggle['toggles'] = 41;
                $this->load->view('admin/header', $menu_toggle);
                $this->load->view('loan/calculator',$data);
                $this->load->view('admin/footer');
            }else{
                $result = $this->Loan_model->calculate($this->input->get('amount'), $this->input->get('months'), $this->input->get('loan_type'), $this->input->get('loan_date'));
                $data['result'] = $result;
                $menu_toggle['toggles'] = 41;
                $this->load->view('admin/header', $menu_toggle);
                $this->load->view('loan/calculator',$data);
                $this->load->view('admin/footer');
            }

        } else {

        }
    }

    public function calculate_amortization_weekly($loan_amount, $product_id, $loan_term, $start_date) {
        // Calculate the total number of payments
        $loan = check_exist_in_table('loan_products','loan_product_id',$product_id);
        $num_payments = $loan_term;
        $interest_rate =  $loan->interest ;
        // Calculate the weekly interest rate
        $weekly_interest_rate = ($interest_rate / 100) / 52;

        // Calculate the payment amount
        $payment_amount = $loan_amount / $num_payments;

        // Calculate the interest amount for each payment
        $interest_amount = ($loan_amount * $weekly_interest_rate);
        $total_interest_amount = ($loan_amount * $weekly_interest_rate)*$loan_term;

        // Calculate the principal amount for each payment
        $principal_amount = $payment_amount - $interest_amount;

        // Initialize the amortization schedule array
        $amortization_schedule = array();

        // Initialize the payment date to the given start date
        $payment_date = new DateTime($start_date);

        // Loop through each payment period and calculate the payment details


        $table = '<div id="calculator"><h3>Loan Info</h3>';
        $table = $table . '<table border="1" class="table">';
        $table = $table . '<tr><td>Loan Name:</td><td>' . $loan->product_name . '</td></tr>';
        $table = $table . '<tr><td>Interest:</td><td>' . $loan->interest . '%</td></tr>';
        $table = $table . '<tr><td>Admin Fee %:</td><td>' . $loan->admin_fees . '%</td></tr>';
        $table = $table . '<tr><td>Loan cover %:</td><td>' . $loan->loan_cover . '%</td></tr>';
        $table = $table . '<tr><td>Terms:</td><td>' . $loan_term . '/'. $loan->frequency . '</td></tr>';
        $table = $table . '<tr><td>Loan start date:</td><td>' . $start_date . '</td></tr>';
        $table = $table . '<tr><td>Loan effective date:</td><td>' . $start_date . '</td></tr>';

        $table = $table . '<tr><td>Frequency:</td><td> ' . $loan->frequency . ' </td></tr>';
        $table = $table . '</table>';
        $table = $table . '<h3>Computation</h3>';
        $table = $table . '<table>';
        $table = $table . '<tr><td>Loan Amount:</td><td> ' . $this->config->item('currency_symbol') . number_format($loan_amount, 2, '.', ',') . '</td></tr>';
        $table = $table . '<tr><td>Total interest:</td><td> ' . $this->config->item('currency_symbol') . number_format(($total_interest_amount), 2) . '</td></tr>';
        $table = $table . '<tr><td>Total Admin fee:</td><td> ' . $this->config->item('currency_symbol') . number_format((0), 2) . '</td></tr>';
        $table = $table . '<tr><td>Total Loan cover:</td><td> ' . $this->config->item('currency_symbol') . number_format((0), 2) . '</td></tr>';
        $table = $table . '<tr><td>Amount Per Term:</td><td> ' . $this->config->item('currency_symbol') . number_format($payment_amount, 2) . '</td></tr>';

        $table = $table . '<tr><td>Total Payment:</td><td> ' . $this->config->item('currency_symbol') . number_format($loan_amount+$total_interest_amount, 2, '.', ',') . '</td></tr>';
        $table = $table . '</table>';

        //$monthly_payment = $amount*($i/12)*pow((1+$i/12),$months)/(pow((1+$i/12),$months)-1);


        $table = $table . '<table class="table" >

				<tr>
					<th width="30" align="center"><b>Pmt</b></th>
					<th width="60" align="center"><b>Payment</b></th>
					<th width="60" align="center"><b>Principal</b></th>
					<th width="60" align="center"><b>Interest</b></th>
					
					<th width="60" align="center"><b>Admin Fee</b></th>
				
					<th width="60" align="center"><b>Loan cover</b></th>
				
					<th width="70" align="center"><b>Balance</b></th>
				</tr>	
			';


        $table = $table . "<tr>";
        $table = $table . "<td width='30'>0</td>";
        $table = $table . "<td width='60'>&nbsp;</td>";
        $table = $table . "<td width='60'>&nbsp;</td>";

        $table = $table . "<td width='85'>&nbsp;</td>";
        $table = $table . "<td width='85'>&nbsp;</td>";
        $table = $table . "<td width='85'>&nbsp;</td>";


        $table = $table . "<td width='70'>" . round($loan_amount, 2) . "</td>";
        $table = $table . "</tr>";



        for ($i = 1; $i <= $num_payments; $i++) {
            // Check if the payment date falls on a weekend (Saturday or Sunday)
            if ($payment_date->format('N') >= 6) {
                // If so, adjust the payment date to the next available weekday (Monday)
                $payment_date->modify('next monday');
            }

            // Calculate the remaining loan balance
            $loan_balance = $loan_amount - ($i * $payment_amount);

            // Calculate the interest and principal amounts for this payment
            $interest_payment = ($i == 1) ? $interest_amount : $interest_amount + ($loan_balance * $weekly_interest_rate);
            $principal_payment = $payment_amount - $interest_payment;

            // Add the payment details to the amortization schedule array
            $amortization_schedule[] = array(
                'payment_number' => $i,
                'payment_date' => $payment_date->format('Y-m-d'),
                'payment_amount' => $payment_amount,
                'interest_amount' => $interest_payment,
                'principal_amount' => $principal_payment,
                'loan_balance' => $loan_balance,
            );

            $table = $table . "<tr class='table_info'>";
            $table = $table . "<td>" . $i . "</td>";
            $table = $table . "<td>" . number_format(($payment_amount), 2) . "</td>";
            $table = $table . "<td>" . number_format($principal_payment, 2) . "</td>";
            $table = $table . "<td>" . number_format($interest_payment, 2) . "</td>";

            $table = $table . "<td>" . number_format(0, 2) . "</td>";

            $table = $table . "<td>" . number_format(0, 2) . "</td>";
            ;
            $table = $table . "<td>" . number_format($loan_balance, 2) . "</td>";
            $table = $table . "</tr>";




            // Move the payment date to the next week
            $payment_date->modify('+1 week');
        }

        // Return the amortization schedule
        $table = $table . "<tr style='color: white; background-color: #0e9970'>";
        $table = $table . "<td width='30'>-</td>";
        $table = $table . "<td width='30'>-</td>";

        $table = $table . "<td width='70'>-</td>";
        $table = $table . "<td width='70'>-</td>";
        $table = $table . "<td width='70'>-</td>";
        $table = $table . "<td width='70'>-</td>";
        $table = $table . "<td width='70'>-</td>";
        $table = $table . "</tr>";
        $table = $table . '</table></div>';

        return $table;
    }

    function create_act()
    {
        $has_loan = $this->db->select("*")->from('loan')->where('loan_customer', $this->input->post('customer'))->where('customer_type', $this->input->post('customer_type'))->where('loan_status', 'ACTIVE')->get()->row();

//print_r($exist);
//print_r($has_loan);
        if (!empty($has_loan)) {


            $this->db->select_sum('paid_amount');
            $this->db->where('loan_id', $has_loan->loan_id)->where('status', 'NOT PAID');
            $query = $this->db->get('payement_schedules');

            if ($this->input->post('amount') < $query->paid_amount) {
                $this->toaster->error('Error, Sorry principal should be greater than curren ACTIVE loan balance for top up to pass through');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        $branch = get_by_id('employees','id', $this->input->post('user'));
        $branch_id = get_by_id('branches','Code', $branch->BranchCode);
        $id = $this->input->post('loan_type');
        $exist = $this->Loan_products_model->get_by_id($id);
        $this->load->library('upload');//loading the library
        $imagePath = realpath(APPPATH . '../uploads/');//this is your real path APPPATH means you are at the application folder
        $number_of_files_uploaded = count($_FILES['files']['name']);
        $name = $this->input->post('name');
        $ctype = $this->input->post('type');
        $serial = $this->input->post('serial');
        $value = $this->input->post('value');
        $description = $this->input->post('desc');
        $result = $this->Loan_model->add_loan($this->input->post('amount'), $this->input->post('months'), $this->input->post('loan_type'), $this->input->post('loan_date'), $this->input->post('customer'), $this->input->post('customer_type'), $this->input->post('worthness_file'), $this->input->post('narration'), $this->input->post('user'), $branch_id->id, $this->input->post('funds_source'));


        for ($i = 0; $i < $number_of_files_uploaded; $i++) {
            $_FILES['userfile']['name'] = $_FILES['files']['name'][$i];
            $_FILES['userfile']['type'] = $_FILES['files']['type'][$i];
            $_FILES['userfile']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
            $_FILES['userfile']['error'] = $_FILES['files']['error'][$i];
            $_FILES['userfile']['size'] = $_FILES['files']['size'][$i];
            //configuration for upload your images
            $config = array(
                'file_name' => rand(100, 1000) . $_FILES['userfile']['name'],
                'allowed_types' => '*',
                'max_size' => 20000,
                'overwrite' => FALSE,
                'upload_path'
                => $imagePath
            );
            $this->upload->initialize($config);
            $errCount = 0;//counting errrs
            if (!$this->upload->do_upload()) {
                $error = array('error' => $this->upload->display_errors());
                $carImages[] = array(
                    'errors' => $error
                );//saving arrors in the array
            } else {

                $filename = $this->upload->data();


                $data = array(
                    'loan_id' => $result,
                    'collateral_name' => $name[$i],
                    'collateral_type' => $ctype[$i],
                    'serial' => $serial[$i],
                    'estimated_price' => $value[$i],
                    'attachement' => $config['file_name'],
                    'description' => $description[$i],
                    'added_by' => $this->session->userdata('user_id'),
                );

                $this->Collateral_model->insert($data);

            }//if file uploaded

        }//for loop ends here


        $this->toaster->success('Success, loan was created  pending authorisation');

        redirect('loan/track');



    }

    function create_group_member_loans_act()
    {
        // Prevent any output that might cause header issues
        ob_start();
        
        try {
            // Get general loan parameters (same for all members)
            $group_id = $this->input->post('group_id');
            $loan_type = $this->input->post('loan_type');
            $loan_date = $this->input->post('loan_date');
            $loan_period = $this->input->post('loan_period'); // Same term for all members
            $batch_number = $this->input->post('batch_number'); // Batch number for all members
            $funds_source = $this->input->post('funds_source');
            $worthness_file = $this->input->post('worthness_file');
            $narration = $this->input->post('narration');
            $user = $this->input->post('user');
            
            // Get member-specific data
            $member_ids = $this->input->post('member_ids');
            $member_amounts = $this->input->post('member_amounts');
            
            // Get branch info from officer
            $branch = get_by_id('employees','id', $user);
            if (!$branch) {
                throw new Exception('Invalid officer selected');
            }
            
            $branch_id = get_by_id('branches','Code', $branch->BranchCode);
            if (!$branch_id) {
                throw new Exception('Branch not found for officer');
            }
            
            // Validate inputs
            if (empty($member_ids) || empty($member_amounts)) {
                ob_end_clean();
                $this->toaster->error('Error: No members selected or amounts specified');
                redirect($_SERVER['HTTP_REFERER']);
                return;
            }
        
            $created_loans = 0;
            $failed_loans = 0;
            
            // Loop through each member and create individual loans
            for ($i = 0; $i < count($member_ids); $i++) {
                $member_id = $member_ids[$i];
                $amount = $member_amounts[$i];
                
                // Skip if amount is empty or zero
                if (empty($amount) || $amount <= 0) {
                    continue;
                }
                
                try {
                    // Check if member already has an active loan
                    $has_loan = $this->db->select("*")
                        ->from('loan')
                        ->where('loan_customer', $member_id)
                        ->where('customer_type', 'individual')
                        ->where('loan_status', 'ACTIVE')
                        ->get()->row();
                    
                    if (!empty($has_loan)) {
                        // Skip this member if they already have an active loan
                        $failed_loans++;
                        continue;
                    }
                    
                    // Create individual loan for this member
                    $result = $this->Loan_model->add_loan(
                        $amount,                    // loan amount (individual per member)
                        $loan_period,              // loan term (same for all)
                        $loan_type,                // loan product (same for all)
                        $loan_date,                // loan date (same for all)
                        $member_id,                // customer (individual member)
                        'individual',              // customer type (individual)
                        $worthness_file,           // worthiness file (same for all)
                        $narration,                // narration (same for all)
                        $user,                     // added by (same for all)
                        $branch_id->id,            // branch (same for all)
                        $funds_source,             // funds source (same for all)
                        $batch_number,             // batch number (same for all)
                        'Yes',                     // from_group = Yes for group member loans
                        $group_id                  // group_id = the selected group
                    );
                    
                    if ($result) {
                        $created_loans++;
                    }
                    
                } catch (Exception $e) {
                    $failed_loans++;
                    // Log error or continue with next member
                    log_message('error', 'Failed to create loan for member ' . $member_id . ': ' . $e->getMessage());
                }
            }
            
            // Clear output buffer
            ob_end_clean();
            
            // Show success/error messages
            if ($created_loans > 0) {
                $this->toaster->success("Success: Created {$created_loans} individual loans for group members");
            }
            
            if ($failed_loans > 0) {
                $this->toaster->warning("Warning: {$failed_loans} loans could not be created (members may already have active loans)");
            }
            
            if ($created_loans == 0) {
                $this->toaster->error('Error: No loans were created. Please check member selection and amounts.');
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                redirect('loan/track');
            }
            
        } catch (Exception $e) {
            // Clear output buffer in case of error
            ob_end_clean();
            
            // Log the error
            log_message('error', 'Error in create_group_member_loans_act: ' . $e->getMessage());
            
            // Show error message and redirect
            $this->toaster->error('Error: ' . $e->getMessage());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }


    function create_act_edit(){
        $row = get_by_id('approval_edits','approval_edits_id',$this->session->userdata('loan_data'));
        $data_new = json_decode($row->new_info);

        $this->Loan_model->add_loan_edit($row->id,$data_new->loan_principal, $data_new->loan_period, $data_new->sy_loan_product, $data_new->loan_date,$data_new->sy_loan_customer,$data_new->customer_type,$data_new->loan_worthness_file,$data_new->narration,$data_new->sy_added_by);
        $this->toaster->success('Success, loan edit was authorised  pending authorisation');
        redirect('loan/track');


    }
    public  function create_act_delete()
    {
        $row = get_by_id('approval_edits','approval_edits_id',$this->session->userdata('loan_delete'));

        $this->Loan_model->update($row->id,array('loan_status'=>'DELETED'));
        $this->toaster->success('Success, loan delete was authorised and archived');
        redirect('loan/delete_approve');
    }
    function create_acti(){

        $group = $this->Groups_model->check($this->input->post('group_id'));
        $branch = get_by_id('employees','id', $this->input->post('user'));
        $branch_id = get_by_id('branches','Code', $branch->BranchCode);

        if(!empty($group)){
            $user_gotten = $this->Group_loan_tracker_model->validate($this->input->post('group_id'),$this->input->post('customer'),$group->id);
            if(!empty($user_gotten)){
                $this->toaster->error('Error, Sorry this member has received his shares already from this group');
                redirect($_SERVER['HTTP_REFERER']);
            }else{
                $validate_trans = $this->Group_loan_tracker_model->validate_trans($group->id);
                if(($validate_trans->amount+$this->input->post('amount')) > $group->amount){
                    $this->toaster->error('Error, Sorry this group has no enough amount to create this loan contract, please add smaller amount');
                    redirect($_SERVER['HTTP_REFERER']);
                }else{

                    $result = $this->Loan_model->add_loan($this->input->post('amount'), $this->input->post('months'), $this->input->post('loan_type'), $this->input->post('loan_date'),$this->input->post('customer'),$this->input->post('worthness_file'),$this->input->post('narration'),$this->input->post('user'), $branch_id->id);
                    $data['result'] = $result;
                    $this->toaster->success('Success, customer was created  pending authorisation');
                    $data = array(
                        'disbursement_id' => $group->id,
                        'group_id' => $this->input->post('group_id',TRUE),
                        'customer_id' => $this->input->post('customer',TRUE),
                        'amount' => $this->input->post('amount',TRUE),

                    );

                    $this->Group_loan_tracker_model->insert($data);
                    redirect('loan/track');
                }
            }
        }else{
            $this->toaster->error('Error, Sorry this group has no amount assigned yet , Please assign group amount first');
            redirect($_SERVER['HTTP_REFERER']);
        }





    }
    function initiated(){
        $data['loan_data'] = $this->Loan_model->get_all('RECOMMENDED');
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/loan_list', $data);
        $this->load->view('admin/footer');
    }
    public function transaction_reversal()
    {
        $trans_id = $this->input->get('tid');
        $loan_number =  $this->input->get('account');
        $get_account = $this->Tellering_model->get_teller_account($this->session->userdata('user_id'));
        $tid = "TR-S" . rand(100, 9999) . date('Y') . date('m') . date('d');
        $date = date("Y-m-d");
        if(empty($get_account)){
            $this->toaster->error('You are not authorized to do this transaction');
            redirect($_SERVER['HTTP_REFERER']);
        }
        else
        {
            $get_transaction =  get_one_where('transaction', 'transaction_id ="'.$trans_id.'" AND account_number ="'.$loan_number.'" ');
            $amount = $get_transaction->credit;
            $recepientt = get_by_id('account', 'collection_account', 'Yes');
            $do_transactions = $this->Account_model->transfer_funds($recepientt->account_number,$loan_number, $amount, $tid, $date);
            if ($do_transactions == 'success') {
                $mode = 'withdraw';
                $res = $this->Account_model->cash_transaction_modified($get_account->account, $loan_number, $amount, $mode, $tid, $date,'reversal');
                $this->db->where('transaction_id',$get_transaction->transaction_id)->update('transaction', array('reversed'=>'Yes'));
                $this->db->where('transaction_id',$tid)->update('transaction', array('reversed'=>'Yes'));
                if ($res == 'success') {
                    $reverse_loans_repayments = $this->db->where('ref',$trans_id)->get('transactions')->result();
                    foreach ($reverse_loans_repayments as $to_act){

                        $get_schedules = $this->db->where('loan_id',$to_act->loan_id)->where('payment_number',$to_act->payment_number)
                            ->get('payement_schedules')->row();
                        $to_remove_amount = $get_schedules->paid_amount;
//                        caculate
                        $to_update_amount = $to_remove_amount-$to_act->amount;
                        if($to_update_amount <=0){
                            $this->db->where('id',$get_schedules->id)->update('payement_schedules',array('paid_amount'=>$to_update_amount,"status"=>"NOT PAID","partial_paid"=>"NO"));
                        }else{
                            $this->db->where('id',$get_schedules->id)->update('payement_schedules',array('paid_amount'=>$to_update_amount));

                        }
                        $this->db->where('loan_id',$to_act->loan_id)->update('loan',array('loan_status'=>"ACTIVE"));
                    }
                    $this->toaster->success('Transaction reversal was successful');
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }


        }
    }

    function loan_revenue(){
        $data['loan_data'] = $this->Loan_model->get_all_revenue();
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('reports/revenue_payments', $data);
        $this->load->view('admin/footer');
    }
    function balances(){
        $product = $this->input->get('product');
        $officer= $this->input->get('officer');
        $loan= $this->input->get('loan');
        $from= $this->input->get('from');
        $to= $this->input->get('to');
        $data['loan_data'] = $this->Loan_model->get_all_balances($product, $officer, $loan, $from, $to);
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('reports/outstanding_balances', $data);
        $this->load->view('admin/footer');
    }
    function recommend(){
        $data['loan_data'] = $this->Loan_model->get_all('INITIATED');
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/recommend', $data);
        $this->load->view('admin/footer');
    }
    function restructure(){
        $data['loan_data'] = $this->Loan_model->get_all('');
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/restructure', $data);
        $this->load->view('admin/footer');
    }
    function track(){
        $data['loan_data'] = $this->Loan_model->get_all('');
        $menu_toggle['toggles'] = 23;


        $user = $this->input->get('user');
        $branch = $this->input->get('branch');
        $product = $this->input->get('product');
        $status = $this->input->get('status');
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $search = $this->input->get('search');
        if($branch==7042){

            $branchgp=7;
        }
        else{
            $branchgp=6;
        }

        if($search=="filter"){
            $data['loan_data'] = $this->Loan_model->get_filter($user,$branch,$branchgp,$product,$status,$from,$to);
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('loan/track', $data);
            $this->load->view('admin/footer');
        }elseif($search=='export pdf'){
            $data['loan_data'] = $this->Loan_model->get_filter($user,$branch,$branchgp,$product,$status,$from,$to);
            $data['officer'] = ($user=="All") ? "All Officers" : get_by_id('employees','id',$user)->Firstname;
            $data['product'] =($product=="All") ? "All Products" : get_by_id('loan_products','loan_product_id',$product)->product_name;
            $data['from'] = $from;
            $data['to'] = $to;
            $this->load->library('Pdf');
            $html = $this->load->view('loan/loan_report_pdf', $data,true);
            $this->pdf->createPDF($html, "loan report as on".date('Y-m-d'), true,'A4','landscape');
        } elseif($search=='export excel'){
            $data_excel = $this->Loan_model->get_filter($user,$branch,$branchgp,$product,$status,$from,$to);
            $this->excel($data_excel);
        }else{
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('loan/track', $data);
            $this->load->view('admin/footer');
        }

    }
    function individual_track(){
        $idd=$this->session->userdata('user_id');
        $data['loan_data'] = $this->Loan_model->track_individual($idd);
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/track', $data);
        $this->load->view('admin/footer');
    }
    function loan_repayment(){
        $data['loan_data'] = $this->Loan_model->get_all('ACTIVE');
        $menu_toggle['toggles'] = 52;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/loan_repayment', $data);
        $this->load->view('admin/footer');
    }
    function loan_repayment_pay_off(){
        $data['loan_data'] = $this->Loan_model->get_all('ACTIVE');
        $menu_toggle['toggles'] = 52;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/loan_repayment_pay_off', $data);
        $this->load->view('admin/footer');
    }
    function assign(){
        $data['loan_data'] = $this->Loan_model->get_all('');
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/active', $data);
        $this->load->view('admin/footer');
    }
    function closed(){
        $data['loan_data'] = $this->Loan_model->get_all('CLOSED');
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/closed', $data);
        $this->load->view('admin/footer');
    }

    function approved(){
        $data['loan_data'] = $this->Loan_model->get_all('APPROVED');
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/approved', $data);
        $this->load->view('admin/footer');
    }
    function disbursed(){
        $data['loan_data'] = $this->Loan_model->get_disbursed();
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/active_loans', $data);
        $this->load->view('admin/footer');
    }
    function write_off(){
        $data['loan_data'] = $this->Loan_model->get_all('ACTIVE');
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/write_off', $data);
        $this->load->view('admin/footer');
    }
    function rejected(){
        $data['loan_data'] = $this->Loan_model->get_all('REJECTED');
        $this->load->view('admin/header');
        $this->load->view('loan/approved', $data);
        $this->load->view('admin/footer');
    }
    function written_off(){
        $data['loan_data'] = $this->Loan_model->get_all('WRITTEN_OFF');
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/written_off', $data);
        $this->load->view('admin/footer');
    }
    function write_off_approve(){
        $data['loan_data'] = $this->Loan_model->get_all_mod('ACTIVE');
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/write_off_approval', $data);
        $this->load->view('admin/footer');
    }

    function delete_permanent(){
        $data['loan_data'] = $this->Loan_model->get_all('');
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/delete_p', $data);
        $this->load->view('admin/footer');
    }
    public function pay_advance(){
        $loan_number = $this->input->post('loan_id');
        $pay_number = $this->input->post('payment_number');
        $amount = $this->input->post('amount');
        $amount_total = 0;
        for($i=0;$i <count($pay_number);$i++){
            $amount_total += $amount;
        }
        $loan_account = get_by_id('loan','loan_id',$loan_number);
        $recepientt = get_by_id('account','collection_account','Yes');
        $check = $this->Account_model->get_account($loan_account->loan_number);
        if($check->balance >= $amount_total) {

            $result = $this->Payement_schedules_model->pay_advance($loan_number, $amount, $pay_number);
            if ($result) {



                //get loan products id
                $dataloanproductsid = get_by_id2('loan','loan_id ='.$loan_number);
                $remainingschedule = ( $dataloanproductsid ->loan_period-$pay_number);
                $counter=1;
                $pay_number=$pay_number+1;

                $realaccountbalance= $check->balance;
                while($counter <= $remainingschedule)
                {


                    $topup = $this->Payement_schedules_model->topnew_pay($loan_number,$pay_number,$amount,$realaccountbalance);
                    $pay_number=$pay_number+1;
                    $counter=$counter+1;
                }
                // Get loan and customer details for enhanced logging
                $customer_data = $this->get_customer_name_for_loan($loan_number);
                $payment_numbers_str = implode(', ', $pay_number);

                $logger = array(
                    'user_id' => $this->session->userdata('user_id'),
                    'activity' => 'Advance Loan Payment: ' .
                                  ' | Amount: MWK ' . number_format($amount_total, 2) .
                                  ' | Client: ' . $customer_data['customer_name'] .
                                  ' | Loan #: ' . (!empty($customer_data['loan_details']) ? $customer_data['loan_details']->loan_number : $loan_number) .
                                  ' | Payment #s: ' . $payment_numbers_str
                );
                log_activity($logger);

                $this->toaster->success('Success, advance payment was successful');
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                $this->toaster->error('Ops!, Sorry advance payment failed P7');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }else{
            $this->toaster->error('Ops!, Sorry advance payment failed, You dont have enough funds to perform this transactions');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    public function finish_loan(){
        $loan_number = $this->input->post('loan_id');
        $pay_number = $this->input->post('payment_number');
        $middlepayment = $this->input->post('middlepayment');
        $repay_amounts = round($this->input->post('repay_amounts'));
        $amount = $this->input->post('amount');
        $totalbalance = $this->input->post('totalbalance');

        $proof = $this->input->post('pay_proof');
        $paid_date = $this->input->post('pdate');
        $loan_account = get_by_id('loan', 'loan_id', $loan_number);
        $tid = "TR-S" . rand(100, 9999) . date('Y') . date('m') . date('d');
        $get_account = $this->Tellering_model->get_teller_account($this->session->userdata('user_id'));
        if(empty($get_account)){
            $this->toaster->error('You are not authorized to do this transaction');
            redirect($_SERVER['HTTP_REFERER']);
        }else {
            $teller_account = $get_account->account;

            $mode = 'deposit';

            $res =	$this->Account_model->cash_transaction_modified($teller_account,$loan_account->loan_number,$amount,$mode,$tid,$paid_date,'deposit');
            if($res=='success') {

                $recepientt = get_by_id('account', 'collection_account', 'Yes');
                $check = $this->Account_model->get_account($loan_account->loan_number);
                if ($check->balance >= $amount) {
                    $do_transactions = $this->Account_model->transfer_funds($loan_account->loan_number, $recepientt->account_number, $amount, $tid, $paid_date);
                    if ($do_transactions == 'success') {
                        $this->Payement_schedules_model->new_pay_new($loan_number, $pay_number, $repay_amounts, $paid_date, $tid);
                        $logger = array(

                            'user_id' => $this->session->userdata('user_id'),
                            'activity' => 'Paid a loan, loan ID:' . ' ' . $loan_number . ' ' . ' from payment number' . ' ' . $pay_number .
                                ' ' . 'amount' . ' ' . $amount


                        );
                        log_activity($logger);
                        $this->nullify_schedules($loan_number,$middlepayment,$totalbalance);
                        $this->db->where('loan_id',$loan_number)->update('loan',array('paid_off'=>"YES"));
                        $this->toaster->success('Success, payment was successful');
                        redirect($_SERVER['HTTP_REFERER']);
                    } else {
                        $this->toaster->error('Error!, Sorry Payment failed');
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                } else {
                    $this->toaster->error('Error!, balance not enough for payment');
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }else{
                $this->toaster->error('Error!, deposit module failed');
                redirect($_SERVER['HTTP_REFERER']);
            }

        }


    }
    function nullify_schedules($loan_id,$middle, $totalbalance)
    {
        $this->db->where('loan_id',$loan_id)
            ->where('payment_number',$middle)->update('payement_schedules',array('paid_amount'=>$totalbalance,'loan_balance'=>0,'partial_paid'=>'NO','status'=>'PAID'));
        $this->db->where('loan_id',$loan_id)
            ->where('payment_number >',$middle)
            ->update('payement_schedules',array('amount'=>0,'principal'=>0,'interest'=>0,'padmin_fee'=>0,'ploan_cover'=>0,'paid_amount'=>0,'loan_balance'=>0,'partial_paid'=>'NO','status'=>'PAID'));
        $this->db->where('loan_id',$loan_id)->update('loan',array('loan_status'=>'CLOSED'));
    }
    function finish_loan_backup(){
        $tid="TR-S".rand(100,9999).date('Y').date('m').date('d');
        $loan_number = $this->input->post('loan_id');
        $pay_number = $this->input->post('payment_number');
        $amount = $this->input->post('amount');
        $proof = $this->input->post('pay_proof');
        $loan_account = get_by_id('loan','loan_id',$loan_number);
        $recepientt = get_by_id('account','collection_account','Yes');
        $check = $this->Account_model->get_account($loan_account->loan_number);
        if($check->balance >= $amount){
            $do_transactions = $this->Account_model->transfer_funds($loan_account->loan_number, $recepientt->account_number, $amount, $tid);
            if($do_transactions=='success'){
                $result = $this->Payement_schedules_model->finish_pay($loan_number,$pay_number,$amount);

                if($result== true){

                    $logger = array(

                        'user_id' => $this->session->userdata('user_id'),
                        'activity' => 'Paid a loan, loan ID:'.' '.$loan_number.' '.' payment number'.' '.$pay_number.
                            ' '.'amount'.' '.$amount,
                        'activity_cate' => 'loan_repayment'

                    );
                    log_activity($logger);
                    $this->toaster->success('Success, payment was successful');
                    redirect($_SERVER['HTTP_REFERER']);
                }else{
                    $this->toaster->error('Ops!, Sorry payment failed P2');
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }else{
                $this->toaster->error('Ops!, Sorry payment failed, Error P2');
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else{
            $this->toaster->error('Ops!, Sorry payment failed loan account savings does not have enough funds');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    public function cash_transaction_second($account1,$amount,$tid,$teller_account,$date){

        $mode = 'deposit';
        $this->Account_model->cash_transaction($teller_account,$account1,$amount,$mode,$tid,$date);
        return true;
    }
    public function pay_loan(){
        $loan_number = $this->input->post('loan_id');
        $pay_number = $this->input->post('payment_number');
        $number_with_commas = $this->input->post('amount');
        $amount = str_replace(',', '', $number_with_commas);
        $topay_amount = $this->input->post('topay_amount');
        $input_datetime = $this->input->post('pdate');
        $datetime = new DateTime($input_datetime);

// Format the DateTime object to the desired format
        $proof = $datetime->format('Y-m-d H:i:s');

        $loan_n = get_by_id('loan','loan_id',$loan_number);
        $collection_acccount = get_by_id('account','collection_account','Yes');

        $get_account = $this->Tellering_model->get_teller_account($this->session->userdata('user_id'));
        $tid = "TR-S" . rand(100, 9999) . date('Y') . date('m') . date('d');
        $mode = 'deposit';
        if(!empty($get_account)){
            $r =	$this->Payement_schedules_model->new_pay_new($loan_number, $pay_number, $amount, $proof,$tid);

            if(!empty($r)){
                $teller_account = $get_account->account;
                $this->Account_model->cash_transaction_modified($teller_account,$loan_n->loan_number,$amount,$mode,$tid,$proof,'deposit');


                $this->Account_model->transfer_funds1($loan_n->loan_number, $collection_acccount->account_number,$amount, $tid,$proof);

                // Get loan and customer details for enhanced logging
                $customer_data = $this->get_customer_name_for_loan($loan_number);

                $logger = array(
                    'user_id' => $this->session->userdata('user_id'),
                    'activity' => 'Loan Payment: Trans Ref: ' . $tid .
                                  ' | Amount: MWK ' . number_format($amount, 2) .
                                  ' | Client: ' . $customer_data['customer_name'] .
                                  ' | Loan #: ' . (!empty($customer_data['loan_details']) ? $customer_data['loan_details']->loan_number : $loan_number) .
                                  ' | Payment #: ' . $pay_number
                );
                log_activity($logger);

                $this->toaster->success('Success, payment was successful');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        else{
            $this->toaster->error('Sorry, you are not authorised to make payments');
            redirect($_SERVER['HTTP_REFERER']);
        }





    }

    public function pay_loan_old(){
        $loan_number = $this->input->post('loan_id');
        $pay_number = $this->input->post('payment_number');
        $amount = $this->input->post('amount');
        $topay_amount = $this->input->post('topay_amount');
        $proof = $this->input->post('pdate');

        $tid = "TR-S" . rand(100, 9999) . date('Y') . date('m') . date('d');

        $loan_account = get_by_id('loan', 'loan_id', $loan_number);
        $recepientt = get_by_id('account', 'collection_account', 'Yes');
        $check = $this->Account_model->get_account($loan_account->loan_number);
        if ($topay_amount >= $amount) {
            $do_transactions = $this->Account_model->transfer_funds($loan_account->loan_number, $recepientt->account_number, $amount, $tid);
            if ($do_transactions == 'success') {
                $result = $this->Payement_schedules_model->new_pay($loan_number, $pay_number, $amount, $proof);

                if ($result == true) {

                    $logger = array(

                        'user_id' => $this->session->userdata('user_id'),
                        'activity' => 'Paid a loan, loan ID:' . ' ' . $loan_number . ' ' . ' payment number' . ' ' . $pay_number .
                            ' ' . 'amount' . ' ' . $amount,


                    );
                    log_activity($logger);
                    $this->toaster->success('Success, payment was successful');
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    $this->toaster->error('Ops!, Sorry payment failed P2');
                    redirect($_SERVER['HTTP_REFERER']);
                }
            } else {
                $this->toaster->error('Ops!, Sorry payment failed, Error P2');
                redirect($_SERVER['HTTP_REFERER']);
            }
        } elseif ($check->balance > 0 && $check->balance < $amount) {
            $topay_amount = $check->balance;
            $this->Account_model->transfer_funds($loan_account->loan_number, $recepientt->account_number, $topay_amount, $tid);
            $r = $this->Payement_schedules_model->new_pay($loan_number, $pay_number, $topay_amount, $proof);

            $logger = array(

                'user_id' => $this->session->userdata('user_id'),
                'activity' => 'Paid a loan, loan ID:' . ' ' . $loan_number . ' ' . ' payment number' . ' ' . $pay_number .
                    ' ' . 'amount' . ' ' . $topay_amount,


            );
            log_activity($logger);
            $data = array(
                'ref' => "GF." . date('Y') . date('m') . date('d') . '.' . rand(100, 999),
                'loan_id' => $this->input->post('loan_id', TRUE),
                'amount' => $topay_amount,
                'transaction_type' => 2,
                'payment_number' => $this->input->post('payment_number'),
                'added_by' => $this->session->userdata('user_id')

            );

            $this->Transactions_model->insert($data);
            $this->toaster->success('Success, payment was successful');
            redirect($_SERVER['HTTP_REFERER']);

        } else {
            $this->toaster->error('Ops!, Sorry payment failed loan account savings does not have enough funds');
            redirect($_SERVER['HTTP_REFERER']);
        }

    }
    public function pay_loan_r(){
        $loan_number = $this->input->post('loan_id');
        $pay_number = $this->input->post('payment_number');
        $pay_number_r = $this->input->post('payment_numberr');
        $amount = $this->input->post('amount');
        $proof = $this->input->post('pay_proof');
        if($this->input->post('payment_method')=="0") {
            $tid = "TR-S" . rand(100, 9999) . date('Y') . date('m') . date('d');

            $loan_account = get_by_id('loan', 'loan_id', $loan_number);
            $recepientt = get_by_id('account', 'collection_account', 'Yes');
            $check = $this->Account_model->get_account($loan_account->loan_number);
            if ($check->balance >= $amount) {
                $do_transactions = $this->Account_model->transfer_funds($loan_account->loan_number, $recepientt->account_number, $amount, $tid);
                if ($do_transactions == 'success') {
                    $result = $this->Payement_schedules_model->new_pay($loan_number, $pay_number, $amount);

                    if ($result == true) {

                        $logger = array(

                            'user_id' => $this->session->userdata('user_id'),
                            'activity' => 'Paid a loan, loan ID:' . ' ' . $loan_number . ' ' . ' payment number' . ' ' . $pay_number .
                                ' ' . 'amount' . ' ' . $amount,
                            'activity_cate' => 'loan_repayment'

                        );
                        log_activity($logger);
                        $this->toaster->success('Success, payment was successful');
                        redirect($_SERVER['HTTP_REFERER']);
                    } else {
                        $this->toaster->error('Ops!, Sorry payment failed P2');
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                } else {
                    $this->toaster->error('Ops!, Sorry payment failed, Error P2');
                    redirect($_SERVER['HTTP_REFERER']);
                }
            } elseif ($check->balance > 0 && $check->balance < $amount) {
                $topay_amount = $check->balance;
                $this->Account_model->transfer_funds($loan_account->loan_number, $recepientt->account_number, $topay_amount, $tid);
                $r = $this->Payement_schedules_model->new_pay($loan_number, $pay_number, $topay_amount);

                $logger = array(

                    'user_id' => $this->session->userdata('user_id'),
                    'activity' => 'Paid a loan, loan ID:' . ' ' . $loan_number . ' ' . ' payment number' . ' ' . $pay_number .
                        ' ' . 'amount' . ' ' . $topay_amount,
                    'activity_cate' => 'loan_repayment'

                );
                log_activity($logger);
                $data = array(
                    'ref' => "GF." . date('Y') . date('m') . date('d') . '.' . rand(100, 999),
                    'loan_id' => $this->input->post('loan_id', TRUE),
                    'amount' => $topay_amount,
                    'transaction_type' => 2,
                    'payment_number' => $this->input->post('payment_number'),
                    'added_by' => $this->session->userdata('user_id')

                );

                $this->Transactions_model->insert($data);
                $this->toaster->success('Success, payment was successful');
                redirect($_SERVER['HTTP_REFERER']);

            } else {
                $this->toaster->error('Ops!, Sorry payment failed loan account savings does not have enough funds');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        else{
            $this->Payement_schedules_model->new_pay($loan_number, $pay_number, $amount);
            $this->Rescheduled_payments_model->new_pay($loan_number, $pay_number_r, $amount);
            $logger = array(

                'user_id' => $this->session->userdata('user_id'),
                'activity' => 'Paid a loan, loan ID:'.$loan_number,
                'activity_cate' => 'loan_repayment'

            );
            log_activity($logger);

            $data = array(
                'ref' => "CF." . date('Y') . date('m') . date('d') . '.' . rand(100, 999),
                'loan_id' => $loan_number,
                'amount' => $amount,
                'transaction_type' => 1,
                'payment_number' => 0,
                'method' => $this->input->post('payment_method'),
                'payment_proof' => $proof,
                'reference' => $this->input->post('reference'),
                'added_by' => $this->session->userdata('user_id')

            );

            $this->Transactions_model->insert($data);
            $this->toaster->success('Success, payment was successful');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    public function pay_late_loan(){
        $transid = "TXN.".date('Y').date('m').date('d').'.'.rand(100,999);
        $loan_number = $this->input->post('loan_id');

        $pay_number = $this->input->post('payment_number');

        $lamount = $this->input->post('lamount');
        $amount = $this->input->post('amount');
        $recepientt = get_by_id('account','collection_account','Yes');
        $sender = get_by_id('loan','loan_id',$loan_number);
//     make deductions first
        $get_sender_balance = get_by_id('account','account_number',$sender->loan_number);

        $check_if_paid = $this->Transactions_model->get_by_loan($loan_number);
        if(!empty($check_if_paid)){
            $get_sender_balance2 = get_by_id('account','account_number',$sender->loan_number);

            if($get_sender_balance2->balance >= $amount){

                $this->Account_model->transfer_funds($sender->loan_number, $recepientt->account_number,$amount, $transid);
                $result = $this->Payement_schedules_model->new_pay($loan_number,$pay_number,$amount);
                $logger = array(

                    'user_id' => $this->session->userdata('user_id'),
                    'activity' => 'Paid a loan, loan ID:'.' '.$loan_number.' '.' payment number'.' '.$pay_number.
                        ' '.'amount'.' '.$amount,
                    'activity_cate' => 'loan_repayment'

                );
                log_activity($logger);
                $data = array(
                    'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                    'loan_id' => $this->input->post('loan_id',TRUE),
                    'amount' => $amount,
                    'transaction_type' => 2,
                    'payment_number' =>  $this->input->post('payment_number'),
                    'added_by' => $this->session->userdata('user_id')

                );

                $this->Transactions_model->insert($data);
                $this->toaster->success('Success, payment was successful');
                redirect($_SERVER['HTTP_REFERER']);
            }
            elseif($get_sender_balance2->balance > 0 && $get_sender_balance2->balance < $amount){
                $topay_amount =  $get_sender_balance->balance;
                $this->Account_model->transfer_funds($sender->loan_number, $recepientt->account_number,$topay_amount, $transid);
                $result = $this->Payement_schedules_model->new_pay($loan_number,$pay_number,$topay_amount);
                $logger = array(

                    'user_id' => $this->session->userdata('user_id'),
                    'activity' => 'Paid a loan, loan ID:'.' '.$loan_number.' '.' payment number'.' '.$pay_number.
                        ' '.'amount'.' '.$topay_amount,
                    'activity_cate' => 'loan_repayment'

                );
                log_activity($logger);
                $data = array(
                    'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                    'loan_id' => $this->input->post('loan_id',TRUE),
                    'amount' => $topay_amount,
                    'transaction_type' => 2,
                    'payment_number' =>  $this->input->post('payment_number'),
                    'added_by' => $this->session->userdata('user_id')

                );

                $this->Transactions_model->insert($data);
                $this->toaster->success('Success, payment was successful');
                redirect($_SERVER['HTTP_REFERER']);

            }
            else{
                $this->toaster->error('Ops!, Sorry payment failed loan account  does not have enough funds');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        else{
            if($get_sender_balance->balance > $lamount){


                $pay_late_first = $this->Account_model->transfer_funds($sender->loan_number, $recepientt->account_number,$lamount, $transid);


                if($pay_late_first=='success'){

                    $get_sender_balance2 = get_by_id('account','account_number',$sender->loan_number);


                    if($get_sender_balance2->balance > $amount){

                        $this->Account_model->transfer_funds($sender->loan_number, $recepientt->account_number,$amount, $transid);
                        $result = $this->Payement_schedules_model->new_pay($loan_number,$pay_number,$amount);
                        $logger = array(

                            'user_id' => $this->session->userdata('user_id'),
                            'activity' => 'Paid a loan, loan ID:'.' '.$loan_number.' '.' payment number'.' '.$pay_number.
                                ' '.'amount'.' '.$amount,
                            'activity_cate' => 'loan_repayment'

                        );
                        log_activity($logger);
                        $data = array(
                            'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                            'loan_id' => $this->input->post('loan_id',TRUE),
                            'amount' => $amount,
                            'transaction_type' => 2,
                            'payment_number' =>  $this->input->post('payment_number'),
                            'added_by' => $this->session->userdata('user_id')

                        );

                        $this->Transactions_model->insert($data);
                        $this->toaster->success('Success, payment was successful');
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                    elseif($get_sender_balance2->balance > 0 && $get_sender_balance2->balance < $amount){

                        $topay_amount =  $get_sender_balance2->balance;



                        $this->Account_model->transfer_funds($sender->loan_number, $recepientt->account_number,$topay_amount, $transid);
                        $result = $this->Payement_schedules_model->new_pay($loan_number,$pay_number,$topay_amount);
                        $logger = array(

                            'user_id' => $this->session->userdata('user_id'),
                            'activity' => 'Paid a loan, loan ID:'.' '.$loan_number.' '.' payment number'.' '.$pay_number.
                                ' '.'amount'.' '.$topay_amount,
                            'activity_cate' => 'loan_repayment'

                        );
                        log_activity($logger);
                        $data = array(
                            'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                            'loan_id' => $this->input->post('loan_id',TRUE),
                            'amount' => $topay_amount,
                            'transaction_type' => 2,
                            'payment_number' =>  $this->input->post('payment_number'),
                            'added_by' => $this->session->userdata('user_id')

                        );

                        $this->Transactions_model->insert($data);
                        $this->toaster->success('Success, payment was successful');
                        redirect($_SERVER['HTTP_REFERER']);

                    }
                    else{
                        $this->toaster->error('Ops!, Sorry payment failed loan account  does not have enough funds');
                        redirect($_SERVER['HTTP_REFERER']);
                    }


                }else{
                    $this->toaster->error('Ops!, Sorry payment failed');
                    redirect($_SERVER['HTTP_REFERER']);
                }

            }
            else{
                $this->toaster->error('Ops!, Sorry late fee payment failed loan account  does not have enough funds');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }


    }



    function view($id){
        $row = $this->Loan_model->get_by_id($id);
        $payments = $this->Payement_schedules_model->get_all_by_id($row->loan_id);

        if($row->customer_type=='group'){
            $group = $this->Groups_model->get_by_id($row->loan_customer);

            $customer_name = $group->group_name.'('.$group->group_code.')';
            $preview_url = "Customer_groups/members/";
        }elseif($row->customer_type=='individual'){
            $indi = $this->Individual_customers_model->get_by_id($row->loan_customer);
            $customer_name = $indi->Firstname.' '.$indi->Lastname;
            $preview_url = "Individual_customers/view/";
        }

        $data = array(
            'loan_id' => $row->loan_id,
            'loan_number' => $row->loan_number,
            'loan_product' => $row->product_name,
            'product_code' => $row->product_code,
            'branch' => $row->branch,
            'customer_type' => $row->customer_type,
            'loan_customer' => $customer_name,
            'preview_url' => $preview_url,
            'customer_id' => $row->loan_customer,
            'loan_date' => $row->loan_date,
            'loan_principal' => $row->loan_principal,
            'loan_period' => $row->loan_period,
            'period_type' => $row->period_type,
            'loan_interest' => $row->loan_interest,
            'loan_interest_amount' => $row->loan_interest_amount,
            'admin_fees_amount' => $row->admin_fees_amount,
            'loan_cover_amount' => $row->loan_cover_amount,
            'loan_amount_total' => $row->loan_amount_total,
            'loan_amount_term' => $row->loan_amount_term,
            'next_payment_id' => $row->next_payment_id,
            'loan_added_by' => $row->loan_added_by,
            'loan_approved_by' => $row->loan_approved_by,
            'loan_status' => $row->loan_status,
            'officer' => $row->Firstname." ".$row->Lastname,
            'loan_added_date' => $row->loan_added_date,
            'payments'=>$payments
        );
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/view',$data);
        $this->load->view('admin/footer');
    }
    function edit_single_loan_request($id){
        $row = $this->Loan_model->get_by_id($id);
        $payments = $this->Payement_schedules_model->get_all_by_id($row->loan_id);
//        $files = $this->Loan_files_model->get_by_loans($row->loan_id);
//  $scores = $this->Loan_recommendation_model->get_by_loan($row->loan_id);

        if($row->customer_type=='group'){
            $group = $this->Groups_model->get_by_id($row->loan_customer);

            $customer_name = $group->group_name.'('.$group->group_code.')';
            $preview_url = "Customer_groups/members/";
            $view = "edit_loan_group";
        }elseif($row->customer_type=='individual'){
            $indi = $this->Individual_customers_model->get_by_id($row->loan_customer);
            $customer_name = $indi->Firstname.' '.$indi->Lastname;
            $preview_url = "Individual_customers/view/";
            $view = "edit_loan";
        }
        $customers =$this->Individual_customers_model->get_all_active();
        $data = array(
            'loan_id' => $row->loan_id,
            'loan_number' => $row->loan_number,
            'loan_product' => $row->product_name,
            'loan_product_id' => $row->loan_product,
            'customer_type' => $row->customer_type,
            'loan_customer' => $customer_name,
            'preview_url' => $preview_url,
            'customer_id' => $row->loan_customer,
            'loan_date' => $row->loan_date,
            'loan_principal' => $row->loan_principal,
            'loan_period' => $row->loan_period,
            'period_type' => $row->period_type,
            'loan_interest' => $row->loan_interest,
            'loan_interest_amount' => $row->loan_interest_amount,
            'loan_amount_total' => $row->loan_amount_total,
            'loan_amount_term' => $row->loan_amount_term,
            'next_payment_id' => $row->next_payment_id,
            'loan_added_by' => $row->loan_added_by,
            'loan_approved_by' => $row->loan_approved_by,
            'loan_status' => $row->loan_status,
            'loan_added_date' => $row->loan_added_date,
            'payments'=>$payments,
            'customers'=>$customers,
            'customer'=>$row->loan_customer,

        );
        $menu_toggle['toggles'] = 23;

        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/'.$view,$data);
        $this->load->view('admin/footer');
    }
    public function edit_action(){

        $row = $this->Loan_model->get_by_id($this->input->post('loan_id'));


        if($row->customer_type=='group'){
            $group = $this->Groups_model->get_by_id($row->loan_customer);

            $customer_name = $group->group_name.'('.$group->group_code.')';
            $preview_url = "Customer_groups/members/";
        }elseif($row->customer_type=='individual'){
            $indi = $this->Individual_customers_model->get_by_id($row->loan_customer);
            $customer_name = $indi->Firstname.' '.$indi->Lastname;
            $preview_url = "Individual_customers/view/";
        }

        if($this->input->post('customer_type')=='group'){
            $group1 = $this->Groups_model->get_by_id($this->input->post('customer'));

            $customer_name1 = $group1->group_name.'('.$group1->group_code.')';
            $preview_url1 = "Customer_groups/members/";
        }elseif($this->input->post('customer_type')=='individual'){
            $indi1 = $this->Individual_customers_model->get_by_id($this->input->post('customer'));
            $customer_name1 = $indi1->Firstname.' '.$indi1->Lastname;
            $preview_url1 = "Individual_customers/view/";
        }
        $loan_number = str_replace(' ', '', $this->input->post('loan_number'));
        $product_n = get_by_id('loan_products','loan_product_id',$this->input->post('loan_type'));
        $added_by1 = get_by_id('employees','id',$this->input->post('user'));
        $result = array(
            'loan_id' => $row->loan_id,
            'loan_number'=> $loan_number,
            'sy_loan_product'=>$this->input->post('loan_type'),
            'loan_product'=>$product_n->product_name,
            'sy_loan_customer'=>$this->input->post('customer'),
            'loan_customer'=>$customer_name1,
            'customer_type'=> $this->input->post('customer_type'),
            'preview_url' => $preview_url1,
            'customer_id' => $row->loan_customer,
            'loan_date'=>$this->input->post('loan_date'),
            'loan_principal'=>$this->input->post('amount'),
            'loan_period'=>$this->input->post('months'),
            'loan_worthness_file'=>$this->input->post('worthness_file'),
            'narration'=>$this->input->post('narration'),
            'sy_added_by'=>$this->input->post('user'),
            'added_by'=>$added_by1->Firstname." ".$added_by1->Lastname,

        );
        $added_by = get_by_id('employees','id',$row->loan_added_by);
        $data = array(
            'loan_id' => $row->loan_id,
            'loan_number' => $row->loan_number,
            'loan_product' => $row->product_name,
            'loan_customer' => $customer_name,
            'customer_type' => $row->customer_type,
            'preview_url' => $preview_url,
            'customer_id' => $row->loan_customer,
            'loan_date' => $row->loan_date,
            'loan_principal' => $row->loan_principal,
            'loan_period' => $row->loan_period,
            'loan_worthness_file'=>$row->worthness_file,
            'narration'=>$row->narration,
            'loan_added_by' => $added_by->Firstname." ".$added_by->Lastname,


        );




        $logger = array(
            'type' => 'Loan edit',
            'old_info' => json_encode($data),
            'new_info' => json_encode($result),
            'id'=> $this->input->post('loan_id'),
            'summary'=> $this->input->post('loan_number'),

            'Initiated_by' => $this->session->userdata('user_id')

        );
        auth_logger($logger);
        $this->toaster->success('You successfully, initiated loan edit, wait for approval');
        redirect('Loan/restructure');
    }
    public function delete_action($id){

        $row = $this->Loan_model->get_by_id($id);


        if($row->customer_type=='group'){
            $group = $this->Groups_model->get_by_id($row->loan_customer);

            $customer_name = $group->group_name.'('.$group->group_code.')';
            $preview_url = "Customer_groups/members/";
        }elseif($row->customer_type=='individual'){
            $indi = $this->Individual_customers_model->get_by_id($row->loan_customer);
            $customer_name = $indi->Firstname.' '.$indi->Lastname;
            $preview_url = "Individual_customers/view/";
        }


        $added_by = get_by_id('employees','id',$row->loan_added_by);
        $data = array(
            'loan_id' => $row->loan_id,
            'loan_number' => $row->loan_number,
            'loan_product' => $row->product_name,
            'loan_customer' => $customer_name,
            'customer_type' => $row->customer_type,
            'preview_url' => $preview_url,
            'customer_id' => $row->loan_customer,
            'loan_date' => $row->loan_date,
            'loan_principal' => $row->loan_principal,
            'loan_period' => $row->loan_period,
            'loan_worthness_file'=>$row->worthness_file,
            'narration'=>$row->narration,
            'loan_added_by' => $added_by->Firstname." ".$added_by->Lastname,


        );




        $logger = array(
            'type' => 'Loan delete',
            'old_info' => json_encode($data),
            'new_info' => json_encode($data),
            'id'=> $id,
            'summary'=> "Delete of data",

            'Initiated_by' => $this->session->userdata('user_id')

        );
        auth_logger($logger);
        redirect('Loan/restructure');
    }
    function edit_recommend(){
        $menu_toggle['toggles'] = 23;

        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/edit_recommend');
        $this->load->view('admin/footer');
    }
    function edit_approve(){
        $menu_toggle['toggles'] = 23;

        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/edit_approve');
        $this->load->view('admin/footer');
    }
    function delete_recommend(){
        $menu_toggle['toggles'] = 23;

        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/delete_recommend');
        $this->load->view('admin/footer');
    }
    function delete_approve(){
        $menu_toggle['toggles'] = 23;

        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/delete_approve');
        $this->load->view('admin/footer');
    }
    function repayment_view($id){
        $row = $this->Loan_model->get_by_id($id);
        $payments = $this->Payement_schedules_model->get_all_by_id($row->loan_id);

        if($row->customer_type=='group'){
            $group = $this->Groups_model->get_by_id($row->loan_customer);

            $customer_name = $group->group_name.'('.$group->group_code.')';
            $preview_url = "Customer_groups/members/";
        }elseif($row->customer_type=='individual'){
            $indi = $this->Individual_customers_model->get_by_id($row->loan_customer);
            $customer_name = $indi->Firstname.' '.$indi->Lastname;
            $preview_url = "Individual_customers/view/";
        }

        $data = array(
            'loan_id' => $row->loan_id,
            'loan_number' => $row->loan_number,
            'loan_product' => $row->product_name,
            'customer_type' => $row->customer_type,
            'loan_customer' => $customer_name,
            'preview_url' => $preview_url,
            'customer_id' => $row->loan_customer,
            'loan_date' => $row->loan_date,
            'loan_principal' => $row->loan_principal,
            'loan_period' => $row->loan_period,
            'period_type' => $row->period_type,
            'loan_interest' => $row->loan_interest,
            'loan_interest_amount' => $row->loan_interest_amount,
            'admin_fees_amount' => $row->admin_fees_amount,
            'loan_cover_amount' => $row->loan_cover_amount,
            'loan_amount_total' => $row->loan_amount_total,
            'loan_amount_term' => $row->loan_amount_term,
            'next_payment_id' => $row->next_payment_id,
            'loan_added_by' => $row->loan_added_by,
            'loan_approved_by' => $row->loan_approved_by,
            'loan_status' => $row->loan_status,
            'loan_added_date' => $row->loan_added_date,
            'payments'=>$payments,
            'officer'=>$row->Firstname.' '.$row->Lastname
        );
        $menu_toggle['toggles'] = 52;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/loan_repayment_view',$data);
        $this->load->view('admin/footer');
    }
    function repayment_view_pay_off($id){
        $row = $this->Loan_model->get_by_id($id);
        $payments = $this->Payement_schedules_model->get_all_by_id($row->loan_id);

        if($row->customer_type=='group'){
            $group = $this->Groups_model->get_by_id($row->loan_customer);

            $customer_name = $group->group_name.'('.$group->group_code.')';
            $preview_url = "Customer_groups/members/";
        }elseif($row->customer_type=='individual'){
            $indi = $this->Individual_customers_model->get_by_id($row->loan_customer);
            $customer_name = $indi->Firstname.' '.$indi->Lastname;
            $preview_url = "Individual_customers/view/";
        }

        $data = array(
            'loan_id' => $row->loan_id,
            'loan_number' => $row->loan_number,
            'loan_product' => $row->product_name,
            'customer_type' => $row->customer_type,
            'loan_customer' => $customer_name,
            'preview_url' => $preview_url,
            'customer_id' => $row->loan_customer,
            'loan_date' => $row->loan_date,
            'loan_principal' => $row->loan_principal,
            'loan_period' => $row->loan_period,
            'period_type' => $row->period_type,
            'loan_interest' => $row->loan_interest,
            'loan_interest_amount' => $row->loan_interest_amount,
            'admin_fees_amount' => $row->admin_fees_amount,
            'loan_cover_amount' => $row->loan_cover_amount,
            'loan_amount_total' => $row->loan_amount_total,
            'loan_amount_term' => $row->loan_amount_term,
            'next_payment_id' => $row->next_payment_id,
            'loan_added_by' => $row->loan_added_by,
            'loan_approved_by' => $row->loan_approved_by,
            'loan_status' => $row->loan_status,
            'loan_added_date' => $row->loan_added_date,
            'payments'=>$payments,
            'officer'=>$row->Firstname.' '.$row->Lastname
        );
        $menu_toggle['toggles'] = 52;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/loan_repayment_view_pay_off',$data);
        $this->load->view('admin/footer');
    }


    function report($id){
        $row = $this->Loan_model->get_by_id_r($id);
        $payments = $this->Payement_schedules_model->get_all_by_id($row->loan_id);
        $maturity_date = $this->Payement_schedules_model->get_last_payment($row->loan_id);
        $first_payment = $this->Payement_schedules_model->get_first_payment($row->loan_id);
        if($row->customer_type=='group'){
            $group = $this->Groups_model->get_by_id($row->loan_customer);

            $customer_name = $group->group_name.'('.$group->group_code.')';

        }elseif($row->customer_type=='individual'){
            $indi = $this->Individual_customers_model->get_by_id($row->loan_customer);
            $customer_name = $indi->Firstname.' '.$indi->Lastname;

        }
        $branchname=get_by_id('branches','Code',$row->branch);
        $bname=$branchname->BranchName;
        $data = array(
            'loan_id' => $row->loan_id,
            'maturity_date' => $maturity_date->payment_schedule,
            'maturity_pay' => $maturity_date->amount,
            'first_payment' => $first_payment->amount,
            'first_payment_date' => $first_payment->payment_schedule,
            'loan_number' => $row->loan_number,
            'loan_product' => $row->product_name,
            'branch_name' => $bname,
            'loan_customer' => $customer_name,
            'customer_id' => $row->id,
            'loan_date' => $row->loan_date,
            'loan_principal' => $row->loan_principal,
            'loan_period' => $row->loan_period,
            'period_type' => $row->period_type,
            'loan_interest' => $row->loan_interest,
            'loan_amount_total' => $row->loan_amount_total,
            'loan_amount_term' => $row->loan_amount_term,
            'next_payment_id' => $row->next_payment_id,
            'loan_added_by' => $row->loan_added_by,
            'loan_approved_by' => $row->loan_approved_by,
            'loan_status' => $row->loan_status,
            'loan_added_date' => $row->loan_added_date,
            'officer' => $row->efname." ".$row->elname,
            'payments'=>$payments,
            'product_name'=>$row->product_name,
        );
        $this->load->library('Pdf');
        $html = $this->load->view('loan/report', $data,true);
        $this->pdf->createPDF($html, $data['loan_customer']." loan report as on ".date('Y-m-d'), true);


    }
    
    function batch_report($batch){
        // Get all loans for this batch
        $this->db->select('l.*, p.product_name, p.product_code, ic.Firstname, ic.Lastname, ic.ClientId, 
                          g.group_name, g.group_code, e.Firstname as efname, e.Lastname as elname');
        $this->db->from('loan l');
        $this->db->join('loan_products p', 'l.loan_product = p.loan_product_id');
        $this->db->join('individual_customers ic', 'l.loan_customer = ic.id', 'left');
        $this->db->join('groups g', 'l.loan_customer = g.group_id', 'left');
        $this->db->join('employees e', 'l.loan_added_by = e.id');
        $this->db->where('l.batch', $batch);
        $this->db->order_by('l.loan_id', 'ASC');
        $loans = $this->db->get()->result();
        
        if(empty($loans)) {
            show_error('No loans found for batch: ' . $batch);
            return;
        }
        
        $batch_data = array();
        
        foreach($loans as $loan) {
            $payments = $this->Payement_schedules_model->get_all_by_id($loan->loan_id);
            $maturity_date = $this->Payement_schedules_model->get_last_payment($loan->loan_id);
            $first_payment = $this->Payement_schedules_model->get_first_payment($loan->loan_id);
            
            if($loan->customer_type=='group'){
                $group = $this->Groups_model->get_by_id($loan->loan_customer);
                $customer_name = $group->group_name.'('.$group->group_code.')';
            }elseif($loan->customer_type=='individual'){
                $indi = $this->Individual_customers_model->get_by_id($loan->loan_customer);
                $customer_name = $indi->Firstname.' '.$indi->Lastname;
            }
            
            $branchname=get_by_id('branches','Code',$loan->branch);
            $bname = $branchname ? $branchname->BranchName : 'N/A';
            
            $loan_data = array(
                'loan_id' => $loan->loan_id,
                'maturity_date' => $maturity_date->payment_schedule,
                'maturity_pay' => $maturity_date->amount,
                'first_payment' => $first_payment->amount,
                'first_payment_date' => $first_payment->payment_schedule,
                'loan_number' => $loan->loan_number,
                'loan_product' => $loan->product_name,
                'branch_name' => $bname,
                'loan_customer' => $customer_name,
                'customer_id' => $loan->loan_customer,
                'loan_date' => $loan->loan_date,
                'loan_principal' => $loan->loan_principal,
                'loan_period' => $loan->loan_period,
                'period_type' => $loan->period_type,
                'loan_interest' => $loan->loan_interest,
                'loan_amount_total' => $loan->loan_amount_total,
                'loan_amount_term' => $loan->loan_amount_term,
                'next_payment_id' => $loan->next_payment_id,
                'loan_added_by' => $loan->loan_added_by,
                'loan_approved_by' => $loan->loan_approved_by,
                'loan_status' => $loan->loan_status,
                'loan_added_date' => $loan->loan_added_date,
                'officer' => $loan->efname." ".$loan->elname,
                'payments'=>$payments,
                'product_name'=>$loan->product_name,
                'collaterals' => get_all_by_id('collateral','loan_id', $loan->loan_id)
            );
            
            $batch_data[] = $loan_data;
        }
        
        $data = array(
            'batch' => $batch,
            'loans' => $batch_data,
            'group_name' => !empty($loans) && $loans[0]->customer_type == 'group' ? $loans[0]->group_name : 'Mixed Group',
            'total_loans' => count($loans),
            'total_amount' => array_sum(array_column($loans, 'loan_principal')),
            'branch_name' => !empty($loans) && !empty($loans[0]->branch) ? (get_by_id('branches','Code',$loans[0]->branch) ? get_by_id('branches','Code',$loans[0]->branch)->BranchName : 'N/A') : 'N/A'
        );
        
        $this->load->library('Pdf');
        $html = $this->load->view('loan/batch_report', $data, true);
        $this->pdf->createPDF($html, "Batch ".$batch." loan report as on ".date('Y-m-d'), true);
    }
    
    function pv(){
        $this->load->view('testv');
    }
    function approval_action(){
        $action = $this->input->get('action');
        $id= $this->input->get('id');
        $customer = $this->Loan_model->loan_user($id);
        $by = 'loan_approved_by';
        $by_date = 'approved_date';
        if($action =="REJECTED"){
            $by = 'rejected_by';
            $by_date = 'rejected_date';
        }
        if($action =="WRITTEN_OFF"){
            $by = 'written_off_by';
            $by_date = 'written_off_date';
        }
        if($action =="WRITE_OFF"){
            $by = 'written_off_by';
            $by_date = 'written_off_date';
        }
        $logger = array(
            'user_id' => $this->session->userdata('user_id'),
            'activity' => $action.' '.' a loan'

        );
        log_activity($logger);
        $notify = get_by_id('sms_settings','id','1');
        if($action =="ACTIVE"){

            $by = 'disbursed_by';
            $by_date = 'disbursed_date';

            $this->cash_transaction($id);
            $this->pay_off_loan($id);
        }else{
            $this->Loan_model->update($id,array('loan_status'=>$action,$by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s')));
        }
        if($notify->loan_disbursement=='Yes' && $action =="ACTIVE"){
            send_sms($customer->PhoneNumber,'Dear customer, loan has been approved, you can call numbers below for more');
        }
        $this->toaster->success('Success, your action successful');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function getMedianSchedule($totalSchedules) {
        // Validate that totalSchedules is an integer


        // Calculate the middle index
        if ($totalSchedules % 2 == 0) {
            // For even number of schedules, return the higher middle index
            $medianScheduleIndex = $totalSchedules / 2;
        } else {
            // For odd number of schedules, return the middle index
            $medianScheduleIndex = round($totalSchedules / 2);
        }

        return (int)$medianScheduleIndex;
    }
    public function pay_off_loan($loan_id){
        $exist = $this->db->select("*")->from('loan')->where('loan_id',$loan_id)->get()->row();
        $has_loan = $this->db->select("*")->from('loan')->where('loan_customer',$exist->loan_customer)->where('loan_status','ACTIVE')->get()->row();
        $by = 'disbursed_by';
        $by_date = 'disbursed_date';
        $action = "ACTIVE";
//print_r($exist);
//print_r($has_loan);
        if(empty($has_loan)){
            $this->Loan_model->update($loan_id,array('loan_status'=>$action,'disbursed'=>'Yes',$by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s')));

        }
        else {

            $this->Loan_model->update($loan_id,array('loan_status'=>$action,'disbursed'=>'Yes',$by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s')));

//    $halfSchedules = $has_loan->loan_period / 2;
            $total_payoff = 0;
            $v = $this->getMedianSchedule($has_loan->loan_period);
            $loan_number = $has_loan->loan_number;
            $pay_number = $has_loan->next_payment_id;
            $middlepayment = $v;

            $get_middle_schedule = get_by_id2('payement_schedules', 'payment_number =' . $v . ' AND loan_id =' . $has_loan->loan_id);
            $payments = $this->db->select("*")->from('payement_schedules')->where('loan_id',$has_loan->loan_id)->get()->result();
            // Check if the current schedule is less than 50% of the total schedules
            if ($has_loan->next_payment_id <= $v) {
                // Ensure $v is an integer (you can adjust this logic based on your requirements)

                // Loop through only $v schedules
                for ($i = 0; $i < $v; $i++) {
                    // Assuming $payments is your array of payment objects
                    $total_payoff += $payments[$i]->amount;
                }
            } else {
                $total_payoff = $get_middle_schedule->amount;
            }



            $repay_amounts = $total_payoff;

            $amount = $total_payoff + $get_middle_schedule->loan_balance;
            $totalbalance = $get_middle_schedule->loan_balance + $get_middle_schedule->amount;
//    echo $has_loan->loan_id.",";
//    echo $repay_amounts.",";
//    echo $amount.",";
//    echo $totalbalance.",";
//
//    exit();

            $paid_date = date('Y-m-d');
            $loan_account = get_by_id('loan', 'loan_id', $has_loan->loan_id);
            $tid = "TR-S" . rand(100, 9999) . date('Y') . date('m') . date('d');
            $get_account = $this->Tellering_model->get_teller_account(72);


            $teller_account = $get_account->account;

            $mode = 'deposit';

            $res = $this->Account_model->cash_transaction_modified($teller_account, $loan_account->loan_number, $amount, $mode, $tid, $paid_date, 'deposit');
            if ($res == 'success') {

                $recepientt = get_by_id('account', 'collection_account', 'Yes');
                $check = $this->Account_model->get_account($loan_account->loan_number);
                if ($check->balance >= $amount) {
                    $do_transactions = $this->Account_model->transfer_funds($loan_account->loan_number, $recepientt->account_number, $amount, $tid, $paid_date);
                    if ($do_transactions == 'success') {


                        $this->Payement_schedules_model->new_pay_new($has_loan->loan_id, $pay_number, $repay_amounts, $paid_date, $tid);
                        $logger = array(

                            'user_id' => $this->session->userdata('user_id'),
                            'activity' => 'Paid a loan, loan ID:' . ' ' . $loan_number . ' ' . ' from payment number' . ' ' . $pay_number .
                                ' ' . 'amount' . ' ' . $amount


                        );
                        log_activity($logger);
                        $this->nullify_schedules($has_loan->loan_id, $middlepayment, $totalbalance);
                        $this->db->where('loan_id', $has_loan->loan_id)->update('loan',array('paid_off'=>"YES"));
                        return true;
                    } else {

                        return false;
                    }
                } else {

                    $this->toaster->error('Error!, balance not enough for payment');
                    return false;
                }
            } else {
                $this->toaster->error('Error!, deposit module failed');

                return false;
            }


        }

    }
    public function cash_transaction($loan_id){
        $tid="TR-S".rand(100,9999).date('Y').date('m').date('d');
        $result = array();
        $get_account = $this->Tellering_model->get_teller_account1();
        $teller_account = $get_account->account;
        $get_l = get_by_id('loan','loan_id',$loan_id);
        $charge = get_by_id('loan_products','loan_product_id', $get_l->loan_product);
        $charge_value =  ($charge->processing_fees/100)*$get_l->loan_principal;

        $account = $get_l->loan_number;
        $amount = $charge_value;
        $mode = 'deposit';
        $res =	$this->Account_model->cash_transaction($teller_account,$account,$amount,$mode,$tid,date('Y-m-d'));
        if($res=='success'){
            $result['status']= 'success';

            $tid="TR-S".rand(100,9999).date('Y').date('m').date('d');


            $this->Account_model->transfer_funds($account, $teller_account, $amount, $tid);

            $data = array(
                'ref' => "GF." . date('Y') . date('m') . date('d') . '.' . rand(100, 999),
                'loan_id' => $loan_id,
                'amount' => $amount,
                'transaction_type' => 1,
                'payment_number' => 0,
                'added_by' => $this->session->userdata('user_id')

            );

            $this->Transactions_model->insert($data);





        }



    }
    function write_action(){
        $action = $this->input->get('action');
        $id= $this->input->get('id');
        $by = 'loan_approved_by';
        $by_date = 'approved_date';
        if($action =="REJECTED"){
            $by = 'rejected_by';
            $by_date = 'rejected_date';
        }
        if($action =="WRITTEN_OFF"){
            $by = 'write_off_approved_by';
            $by_date = 'write_off_approval_date';
        }
        if($action =="WRITE_OFF"){
            $by = 'written_off_by';
            $by_date = 'written_off_date';
        }
        $logger = array(
            'user_id' => $this->session->userdata('user_id'),
            'activity' => $action.' '.' a loan'

        );
        log_activity($logger);
        if($action =="ACTIVE"){
            $by = 'written_off_by';

            $by_date = 'written_off_date';
            $this->Loan_model->update($id,array('loan_status'=>$action, $by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s')));
        }else{
            $this->Loan_model->update($id,array('loan_status'=>$action,$by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s')));
        }

        $this->toaster->success('Success, your action successful');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function index()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));

        if ($q <> '') {
            $config['base_url'] = base_url() . 'loan/index.html?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'loan/index.html?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'loan/index.html';
            $config['first_url'] = base_url() . 'loan/index.html';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Loan_model->total_rows($q);
        $loan = $this->Loan_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'loan_data' => $loan,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('admin/header');
        $this->load->view('loan/loan_list', $data);
        $this->load->view('admin/footer');

    }

    public function read($id)
    {
        $row = $this->Loan_model->get_by_id($id);
        if ($row) {
            $data = array(
                'loan_id' => $row->loan_id,
                'loan_number' => $row->loan_number,
                'loan_product' => $row->loan_product,
                'loan_customer' => $row->loan_customer,
                'loan_date' => $row->loan_date,
                'loan_principal' => $row->loan_principal,
                'loan_period' => $row->loan_period,
                'period_type' => $row->period_type,
                'loan_interest' => $row->loan_interest,
                'loan_amount_total' => $row->loan_amount_total,
                'next_payment_id' => $row->next_payment_id,
                'loan_added_by' => $row->loan_added_by,
                'loan_approved_by' => $row->loan_approved_by,
                'loan_status' => $row->loan_status,
                'loan_added_date' => $row->loan_added_date,
            );
            $this->load->view('loan/loan_read', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('loan'));
        }
    }

    public function create()
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('loan/create_action'),
            'loan_id' => set_value('loan_id'),
            'loan_number' => set_value('loan_number'),
            'loan_product' => set_value('loan_product'),
            'loan_customer' => set_value('loan_customer'),
            'loan_date' => set_value('loan_date'),
            'loan_principal' => set_value('loan_principal'),
            'loan_period' => set_value('loan_period'),
            'period_type' => set_value('period_type'),
            'loan_interest' => set_value('loan_interest'),
            'loan_amount_total' => set_value('loan_amount_total'),
            'next_payment_id' => set_value('next_payment_id'),
            'loan_added_by' => set_value('loan_added_by'),
            'loan_approved_by' => set_value('loan_approved_by'),
            'loan_status' => set_value('loan_status'),
            'loan_added_date' => set_value('loan_added_date'),
        );
        $this->load->view('loan/loan_form', $data);
    }

    public function create_action()
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = array(
                'loan_number' => $this->input->post('loan_number',TRUE),
                'loan_product' => $this->input->post('loan_product',TRUE),
                'loan_customer' => $this->input->post('loan_customer',TRUE),
                'loan_date' => $this->input->post('loan_date',TRUE),
                'loan_principal' => $this->input->post('loan_principal',TRUE),
                'loan_period' => $this->input->post('loan_period',TRUE),
                'period_type' => $this->input->post('period_type',TRUE),
                'loan_interest' => $this->input->post('loan_interest',TRUE),
                'loan_amount_total' => $this->input->post('loan_amount_total',TRUE),
                'next_payment_id' => $this->input->post('next_payment_id',TRUE),
                'loan_added_by' => $this->input->post('loan_added_by',TRUE),
                'loan_approved_by' => $this->input->post('loan_approved_by',TRUE),
                'loan_status' => $this->input->post('loan_status',TRUE),
                'loan_added_date' => $this->input->post('loan_added_date',TRUE),
            );

            $this->Loan_model->insert($data);
            $this->session->set_flashdata('message', 'Create Record Success');
            redirect(site_url('loan'));
        }
    }

    public function update($id)
    {
        $row = $this->Loan_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('loan/update_action'),
                'loan_id' => set_value('loan_id', $row->loan_id),
                'loan_number' => set_value('loan_number', $row->loan_number),
                'loan_product' => set_value('loan_product', $row->loan_product),
                'loan_customer' => set_value('loan_customer', $row->loan_customer),
                'loan_date' => set_value('loan_date', $row->loan_date),
                'loan_principal' => set_value('loan_principal', $row->loan_principal),
                'loan_period' => set_value('loan_period', $row->loan_period),
                'period_type' => set_value('period_type', $row->period_type),
                'loan_interest' => set_value('loan_interest', $row->loan_interest),
                'loan_amount_total' => set_value('loan_amount_total', $row->loan_amount_total),
                'next_payment_id' => set_value('next_payment_id', $row->next_payment_id),
                'loan_added_by' => set_value('loan_added_by', $row->loan_added_by),
                'loan_approved_by' => set_value('loan_approved_by', $row->loan_approved_by),
                'loan_status' => set_value('loan_status', $row->loan_status),
                'loan_added_date' => set_value('loan_added_date', $row->loan_added_date),
            );
            $this->load->view('loan/loan_form', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('loan'));
        }
    }

    public function update_action()
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('loan_id', TRUE));
        } else {
            $data = array(
                'loan_number' => $this->input->post('loan_number',TRUE),
                'loan_product' => $this->input->post('loan_product',TRUE),
                'loan_customer' => $this->input->post('loan_customer',TRUE),
                'loan_date' => $this->input->post('loan_date',TRUE),
                'loan_principal' => $this->input->post('loan_principal',TRUE),
                'loan_period' => $this->input->post('loan_period',TRUE),
                'period_type' => $this->input->post('period_type',TRUE),
                'loan_interest' => $this->input->post('loan_interest',TRUE),
                'loan_amount_total' => $this->input->post('loan_amount_total',TRUE),
                'next_payment_id' => $this->input->post('next_payment_id',TRUE),
                'loan_added_by' => $this->input->post('loan_added_by',TRUE),
                'loan_approved_by' => $this->input->post('loan_approved_by',TRUE),
                'loan_status' => $this->input->post('loan_status',TRUE),
                'loan_added_date' => $this->input->post('loan_added_date',TRUE),
                'loan_added_by'=>$this->input->post('loan_added_by', TRUE)
            );

            $this->Loan_model->update($this->input->post('loan_id', TRUE), $data);
            $this->session->set_flashdata('message', 'Update Record Success');
            redirect(site_url('loan'));
        }
    }
    public function update_action2()
    {

        $data = array(
            'loan_added_by'=>$this->input->post('loan_added_by', TRUE)
        );

        $this->Loan_model->update($this->input->post('loan_id', TRUE), $data);
        $this->session->set_flashdata('success', 'Update Record Success');
        redirect($_SERVER['HTTP_REFERER']);

    }

    public function delete($id)
    {
        $row = $this->Loan_model->get_by_id($id);

        if ($row) {
            $this->Loan_model->delete($id);
            $this->toaster->success('Success, your action successful');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function delete_data($id)
    {
        $row = $this->Loan_model->get_by_id($id);

        if ($row) {
            $this->Loan_model->delete_data($id);
            $this->toaster->success('Success, your action successful');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    function loan_report(){
        $data['loan_data'] = array();
        $this->load->view('admin/header');
        $this->load->view('loan/loan_report',$data);
        $this->load->view('admin/footer');
    }
    function loan_report_write_off(){
        $data['loan_data'] = array();
        $this->load->view('admin/header');
        $this->load->view('loan/loan_report_written_off',$data);
        $this->load->view('admin/footer');
    }
    /**
     * Send a request to generate a loan portfolio report
     * This function collects filter parameters, sends them to the Node.js backend,
     * and redirects the user to the reports page
     */
    public function portfolio_filter()
    {
        // Collect all the filter parameters from the POST request
        $product = $this->input->post('productid');
        $officer = $this->input->post('officer');
        $branch = $this->input->post('branch');
        $status = $this->input->post('status');
        $date_from = $this->input->post('date_from');
        $date_to = $this->input->post('date_to');

        // Initialize cURL session
        $ch = curl_init();

        // Set the URL of the endpoint
        $url = "http://localhost:4500/generate-report-portfolio";

        // Prepare the data to be sent to the Node.js backend
        $data = [
            "report_type" => "Loan Portfolio Report",
            "user" => $this->session->userdata('Firstname')." ".$this->session->userdata('Lastname'),
            "user_id" => $this->session->userdata('user_id'),
            "officer" => $officer,
            "product" => $product,
            "branch" => $branch,
            "status" => $status,
            "from_date" => $date_from,
            "to_date" => $date_to,
            "branchgp" => $branch // Using the same branch ID for groups - adjust if needed
        ];

        // Convert the data array to JSON
        $jsonData = json_encode($data);

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            // Handle cURL error
            $this->toaster->error('Error: ' . curl_error($ch));
            redirect(site_url('report'));
        } else {
            // Display success message and redirect
            $this->toaster->success('Success, Loan Portfolio Report is being processed. You may continue with other tasks and check back later for the completed report.');
            redirect(site_url('report'));
        }

        // Close the cURL session
        curl_close($ch);
    }
    public function portfolio_filter_write_off()
    {
        // Collect all the filter parameters from the POST request
        $product = $this->input->post('productid');
        $officer = $this->input->post('officer');
        $branch = $this->input->post('branch');
        $status = $this->input->post('status');
        $date_from = $this->input->post('date_from');
        $date_to = $this->input->post('date_to');

        // Initialize cURL session
        $ch = curl_init();

        // Set the URL of the endpoint
        $url = "http://localhost:4500/generate-report-portfolio-write-off";

        // Prepare the data to be sent to the Node.js backend
        $data = [
            "report_type" => "Loan Portfolio Write-Off Report",
            "user" => $this->session->userdata('Firstname')." ".$this->session->userdata('Lastname'),
            "user_id" => $this->session->userdata('user_id'),
            "officer" => $officer,
            "product" => $product,
            "branch" => $branch,
            "status" => $status,
            "from_date" => $date_from,
            "to_date" => $date_to,
            "branchgp" => $branch // Using the same branch ID for groups - adjust if needed
        ];

        // Convert the data array to JSON
        $jsonData = json_encode($data);

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            // Handle cURL error
            $this->toaster->error('Error: ' . curl_error($ch));
            redirect(site_url('report'));
        } else {
            // Display success message and redirect
            $this->toaster->success('Success, Loan Portfolio Write Off Report is being processed. You may continue with other tasks and check back later for the completed report.');
            redirect(site_url('report'));
        }

        // Close the cURL session
        curl_close($ch);
    }

    function loan_report_search(){
        $user = $this->input->get('user');
        $branch = $this->input->get('branch');
        $product = $this->input->get('product');
        $status = $this->input->get('status');
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $search = $this->input->get('search');
        if($branch==7042){

            $branchgp=7;
        }
        else{
            $branchgp=6;
        }

        if($search=="filter"){
            $data['loan_data'] = $this->Loan_model->get_filter($user,$branch,$branchgp,$product,$status,$from,$to);
            $this->load->view('admin/header');
            $this->load->view('loan/loan_report',$data);
            $this->load->view('admin/footer');
        }elseif($search=='pdf'){
            $data['loan_data'] = $this->Loan_model->get_filter($user,$branch,$branchgp,$product,$status,$from,$to);
            $data['officer'] = ($user=="All") ? "All Officers" : get_by_id('employees','id',$user)->Firstname;
            $data['product'] =($product=="All") ? "All Products" : get_by_id('loan_products','loan_product_id',$product)->product_name;
            $data['from'] = $from;
            $data['to'] = $to;
            $this->load->library('Pdf');
            $html = $this->load->view('loan/loan_report_pdf', $data,true);
            $this->pdf->createPDF($html, "loan report as on".date('Y-m-d'), true,'A4','landscape');
        }
        elseif($search=='excel'){
            $data['loan_data'] = $this->Loan_model->get_filter($user,$branch,$branchgp,$product,$status,$from,$to);
            $data['officer'] = ($user=="All") ? "All Officers" : get_by_id('employees','id',$user)->Firstname;
            $data['product'] =($product=="All") ? "All Products" : get_by_id('loan_products','loan_product_id',$product)->product_name;
            $data['from'] = $from;
            $data['to'] = $to;

            $html_tableloanreport = $this->load->view('reports/loan_report_excel', $data, true); // Load the view with the data
            $this->output->set_content_type('text/html')->set_output($html_tableloanreport);


        }

    }
    function loan_report_projection(){

        $this->load->view('admin/header');
        $this->load->view('loan/loan_report_projectn');
        $this->load->view('admin/footer');
    }
    function loan_report_search_projection(){
//		$user = $this->input->get('user');
//		$product = $this->input->get('product');
//		$status = $this->input->get('status');
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $search = $this->input->get('search');
        if($search=="filter"){


            $result = $this->Payement_schedules_model->get_filter_projection($from,$to);
            $amount= $this->Payement_schedules_model->get_filter_projections($from,$to);
            $pri= $this->Payement_schedules_model->get_filter_projection_principal($from,$to);
            $inter= $this->Payement_schedules_model->get_filter_projection_interest($from,$to);
            $data = array(
                'amount'=>$amount['amount'],
                'interest'=>$inter['interest'],
                'principal'=>$pri['principal'],
                'paid_amount'=>$result['paid_amount']

            );

            $this->load->view('admin/header');
            $this->load->view('loan/loan_report_projections',$data);
            $this->load->view('admin/footer');
        }elseif($search=='pdf'){
            $data['loan_data'] = $this->Loan_model->get_filter($from,$to);
//			$data['officer'] = ($user=="All") ? "All Officers" : get_by_id('employees','id',$user)->Firstname;
//			$data['product'] =($product=="All") ? "All Products" : get_by_id('loan_products','loan_product_id',$product)->product_name;
            $data['from'] = $from;
            $data['to'] = $to;
            $this->load->library('Pdf');
            $html = $this->load->view('loan/loan_report_pdf', $data,true);
            $this->pdf->createPDF($html, "loan report as on".date('Y-m-d'), true,'A4','landscape');
        }

    }

    public function _rules()
    {
        $this->form_validation->set_rules('loan_number', 'loan number', 'trim|required');
        $this->form_validation->set_rules('loan_product', 'loan product', 'trim|required');
        $this->form_validation->set_rules('loan_customer', 'loan customer', 'trim|required');
        $this->form_validation->set_rules('loan_date', 'loan date', 'trim|required');
        $this->form_validation->set_rules('loan_principal', 'loan principal', 'trim|required|numeric');
        $this->form_validation->set_rules('loan_period', 'loan period', 'trim|required');
        $this->form_validation->set_rules('period_type', 'period type', 'trim|required');
        $this->form_validation->set_rules('loan_interest', 'loan interest', 'trim|required');
        $this->form_validation->set_rules('loan_amount_total', 'loan amount total', 'trim|required|numeric');
        $this->form_validation->set_rules('next_payment_id', 'next payment id', 'trim|required');
        $this->form_validation->set_rules('loan_added_by', 'loan added by', 'trim|required');
        $this->form_validation->set_rules('loan_approved_by', 'loan approved by', 'trim|required');
        $this->form_validation->set_rules('loan_status', 'loan status', 'trim|required');
        $this->form_validation->set_rules('loan_added_date', 'loan added date', 'trim|required');

        $this->form_validation->set_rules('loan_id', 'loan_id', 'trim');
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }
    function edit(){
        $this->Loan_model->edits();
    }

    function initiate_edit_loan()
    {
        $data['loan_data'] = $this->Loan_model->get_all('');
        $menu_toggle['toggles'] = 23;

        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/initiate_edit_loan', $data);
        $this->load->view('admin/footer');

    }

    public function excel($edata)
    {
//		$this->load->helper('exportexcel_helper');
        $namaFile = "loan-report.xls";
        $judul = "loan";
        $tablehead = 0;
        $tablebody = 1;
        $nourut = 1;
        //penulisan header
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename=" . $namaFile . "");
        header("Content-Transfer-Encoding: binary ");

        xlsBOF();

        $kolomhead = 0;
        xlsWriteLabel($tablehead, $kolomhead++, "No");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Number");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Product");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Customer");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Date");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Principal");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Period");
        xlsWriteLabel($tablehead, $kolomhead++, "Period Type");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Interest Rate");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Interest Amount");
        xlsWriteLabel($tablehead, $kolomhead++, "Admin Fee Rate");
        xlsWriteLabel($tablehead, $kolomhead++, "Admin Fees Amount");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Cover Rate");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Cover Amount");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Amount Term");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Amount Total");
        xlsWriteLabel($tablehead, $kolomhead++, "Next Payment Id");
        xlsWriteLabel($tablehead, $kolomhead++, "Worthness File");
        xlsWriteLabel($tablehead, $kolomhead++, "Narration");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Added By");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Approved By");
        xlsWriteLabel($tablehead, $kolomhead++, "Approved Date");
        xlsWriteLabel($tablehead, $kolomhead++, "Rejected By");
        xlsWriteLabel($tablehead, $kolomhead++, "Rejected Date");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Status");
        xlsWriteLabel($tablehead, $kolomhead++, "Disbursed");
        xlsWriteLabel($tablehead, $kolomhead++, "Disbursed By");
        xlsWriteLabel($tablehead, $kolomhead++, "Disbursed Date");
        xlsWriteLabel($tablehead, $kolomhead++, "Written Off By");
        xlsWriteLabel($tablehead, $kolomhead++, "Write Off Approved By");
        xlsWriteLabel($tablehead, $kolomhead++, "Write Off Approval Date");
        xlsWriteLabel($tablehead, $kolomhead++, "Written Off Date");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Added Date");

        foreach ($edata as $data) {
            $kolombody = 0;



            if($data->customer_type=='group'){
                $group = $this->Groups_model->get_by_id($data->loan_customer);

                $customer_name = $group->group_name.'('.$group->group_code.')';

            }elseif($data->customer_type=='individual'){
                $indi = $this->Individual_customers_model->get_by_id($data->loan_customer);
                $customer_name = $indi->Firstname.' '.$indi->Lastname.' ('.$indi->ClientId.')';

            }

            //ubah xlsWriteLabel menjadi xlsWriteNumber untuk kolom numeric
            xlsWriteNumber($tablebody, $kolombody++, $nourut);
            xlsWriteLabel($tablebody, $kolombody++, $data->loan_number);
            xlsWriteLabel($tablebody, $kolombody++, $data->product_name);
            xlsWriteLabel($tablebody, $kolombody++, $customer_name);
            xlsWriteLabel($tablebody, $kolombody++, $data->loan_date);
            xlsWriteNumber($tablebody, $kolombody++, $data->loan_principal);
            xlsWriteNumber($tablebody, $kolombody++, $data->loan_period);
            xlsWriteLabel($tablebody, $kolombody++, $data->period_type);
            xlsWriteNumber($tablebody, $kolombody++, $data->loan_interest);
            xlsWriteNumber($tablebody, $kolombody++, $data->loan_interest_amount);
            xlsWriteNumber($tablebody, $kolombody++, $data->admin_fee);
            xlsWriteNumber($tablebody, $kolombody++, $data->admin_fees_amount);
            xlsWriteNumber($tablebody, $kolombody++, $data->loan_cover);
            xlsWriteNumber($tablebody, $kolombody++, $data->loan_cover_amount);
            xlsWriteNumber($tablebody, $kolombody++, $data->loan_amount_term);
            xlsWriteNumber($tablebody, $kolombody++, $data->loan_amount_total);
            xlsWriteNumber($tablebody, $kolombody++, $data->next_payment_id);
            xlsWriteLabel($tablebody, $kolombody++, $data->worthness_file);
            xlsWriteLabel($tablebody, $kolombody++, $data->narration);
            xlsWriteLabel($tablebody, $kolombody++, $data->efname." ".$data->elname);
            xlsWriteLabel($tablebody, $kolombody++, $data->approverfname." ".$data->approverlname);
            xlsWriteLabel($tablebody, $kolombody++, $data->approved_date);
            xlsWriteLabel($tablebody, $kolombody++, $data->rejecterfname." ".$data->rejecterlname);
            xlsWriteLabel($tablebody, $kolombody++, $data->rejected_date);
            xlsWriteLabel($tablebody, $kolombody++, $data->loan_status);
            xlsWriteLabel($tablebody, $kolombody++, $data->disbursed);
            xlsWriteLabel($tablebody, $kolombody++, $data->disburserfname." ".$data->disburserlname);
            xlsWriteLabel($tablebody, $kolombody++, $data->disbursed_date);
            xlsWriteLabel($tablebody, $kolombody++, $data->written_off_by);
            xlsWriteLabel($tablebody, $kolombody++, $data->write_off_approved_by);
            xlsWriteLabel($tablebody, $kolombody++, $data->write_off_approval_date);
            xlsWriteLabel($tablebody, $kolombody++, $data->written_off_date);
            xlsWriteLabel($tablebody, $kolombody++, $data->loan_added_date);

            $tablebody++;
            $nourut++;
        }

        xlsEOF();
        exit();
    }

    public function word()
    {
        header("Content-type: application/vnd.ms-word");
        header("Content-Disposition: attachment;Filename=loan.doc");

        $data = array(
            'loan_data' => $this->Loan_model->get_all(),
            'start' => 0
        );

        $this->load->view('loan/loan_doc',$data);
    }
    public  function fixdate(){
        $this->db->select('*');
        $this->db->from('transaction');
        $this->db->group_by('transaction_id');
        $query = $this->db->get();
        $result = $query->result();

// Now $result contains all records associated with distinct transaction_id values

        foreach ($result as $r){
            $this->db->where('transaction_id',$r->transaction_id)->update('transaction',array('system_time'=>$r->server_time));
        }
// Now $result contains distinct transaction_id values

    }

}

