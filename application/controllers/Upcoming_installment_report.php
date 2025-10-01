<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Upcoming Installment Report Controller
 */
class Upcoming_installment_report extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Load necessary models
        $this->load->model('Loan_model');
        $this->load->model('Groups_model');
        $this->load->model('Individual_customers_model');
        $this->load->model('Payement_schedules_model');
        // Load custom helpers

    }

    /**
     * Default method - displays the upcoming installment report
     */
    public function index() {
        $data = array();



        // Load view
        $this->load->view('admin/header');
        $this->load->view('reports/upcoming_installment_report', $data);
        $this->load->view('admin/footer');
    }
    /**
     * Process upcoming installment report via Node.js backend
     */
    public function upcoming_installment_filter()
    {
        $branch = $this->input->post('branch');
        $officer = $this->input->post('user');
        $product = $this->input->post('product');

        // Initialize cURL session
        $ch = curl_init();

        // Set the URL of the endpoint
        $url = "http://localhost:4300/generate-report-upcoming-installment";

        // Prepare the data to be sent
        $data = [
            "report_type" => "UPCOMING_INSTALLMENT",
            "user" => $this->session->userdata('Firstname')." ".$this->session->userdata('Lastname'),
            "user_id" => $this->session->userdata('user_id'),
            "branch" => $branch,
            "officer" => $officer,
            "product" => $product
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
            $this->toaster->error('Error: ' . curl_error($ch));
        } else {
            // Success message
            $this->toaster->success('Success! Upcoming Installment Report is being processed. You may continue with other tasks and check back later for the report.');
        }

        // Close the cURL session
        curl_close($ch);

        // Redirect to main Report controller
        redirect(site_url('report'));
    }
    /**
     * Get the number of installments in arrears for a loan
     */
    private function get_installments_in_arrears($loan_id) {
        $today = date('Y-m-d');

        $query = $this->db->select('COUNT(*) as count')
            ->from('payement_schedules')
            ->where('loan_id', $loan_id)
            ->where('status', 'NOT PAID')
            ->where('payment_schedule <', $today)
            ->get();

        return $query->row()->count;
    }

    /**
     * Get the last transaction date for a loan
     */
    private function get_last_transaction_date($loan_id) {
        $query = $this->db->select('date_stamp')
            ->from('transactions')
            ->where('loan_id', $loan_id)
            ->order_by('date_stamp', 'DESC')
            ->limit(1)
            ->get();

        if ($query->num_rows() > 0) {
            return $query->row()->date_stamp;
        }

        return 'No payments';
    }

    /**
     * Get overpayment or balance in settlement account
     */
    private function get_overpayment($loan_id) {

        return 0;
    }
}