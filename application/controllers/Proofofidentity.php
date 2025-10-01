<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Proofofidentity extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Proofofidentity_model');
		$this->load->model('Individual_customers_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));
        
        if ($q <> '') {
            $config['base_url'] = base_url() . 'proofofidentity/index.html?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'proofofidentity/index.html?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'proofofidentity/index.html';
            $config['first_url'] = base_url() . 'proofofidentity/index.html';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Proofofidentity_model->total_rows($q);
        $proofofidentity = $this->Proofofidentity_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'proofofidentity_data' => $proofofidentity,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('proofofidentity/proofofidentity_list', $data);
    }

    public function read($id) 
    {
        $row = $this->Proofofidentity_model->get_by_id($id);
        if ($row) {
            $data = array(
		'id' => $row->id,
		'IDType' => $row->IDType,
		'IDNumber' => $row->IDNumber,
		'IssueDate' => $row->IssueDate,
		'ExpiryDate' => $row->ExpiryDate,
		'DocImageURL' => $row->DocImageURL,
		'Stamp' => $row->Stamp,
		'ClientId' => $row->ClientId,
		'photograph' => $row->photograph,
		'signature' => $row->signature,
		'Id_back' => $row->Id_back,
		'id_front' => $row->id_front,
	    );
            $this->load->view('proofofidentity/proofofidentity_read', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('proofofidentity'));
        }
    }
	public function upload()
	{
		if ( ! empty($_FILES))
		{
			$res=array();
			$config['upload_path'] = "./uploads";

			$config['allowed_types'] = "mp4|ogv|zip|mp3|gif|jpg|png|jpeg|pdf|doc|xml|docx|GIF|JPG|PNG|JPEG|PDF|DOC|XML|DOCX|xls|xlsx|csv|ppt|pptx";
			$config['file_name'] = uniqid('').rand(100,9999);
			$this->load->library('upload', $config);
			if (! $this->upload->do_upload("file")) {
				$res['status']='error';

				$res['message']='Sorry Something went wrong, we could not upload your file ';

			}else{
				$uploadData = $this->upload->data();
				$file = $uploadData['file_name'];
				$res['status']='success';

				$res['message']='Success, File uploaded ';
				$res['data']=array(
					'file_name'=>$file
				);


			}

		} else {
			$res['status']='error';

			$res['message']='Sorry file can not be empty ';

		}
		echo json_encode($res);
	}
	public function check($id)
	{
		$row1 = $this->Individual_customers_model->get_by_id($id);
		if ($row1) {
			$row  = $this->Proofofidentity_model->check($row1->ClientId);
			if($row){
				$data = array(
					'id' => $row->id,
					'IDType' => $row->IDType,
					'IDNumber' => $row->IDNumber,
					'IssueDate' => $row->IssueDate,
					'ExpiryDate' => $row->ExpiryDate,
					'DocImageURL' => $row->DocImageURL,
					'Stamp' => $row->Stamp,
					'ClientId' => $row->ClientId,
					'photograph' => $row->photograph,
					'signature' => $row->signature,
					'Id_back' => $row->Id_back,
					'id_front' => $row->id_front,
				);
				$this->load->view('admin/header');
				$this->load->view('proofofidentity/check',$data);
				$this->load->view('admin/footer');

			}else {
				$data = array(
					'id'=>"",
					'ClientId'=> $row1->ClientId
				);
				$this->load->view('admin/header');
				$this->load->view('proofofidentity/check',$data);
				$this->load->view('admin/footer');
			}


		} else {

		}
	}
    public function create() 
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('proofofidentity/create_action'),
	    'id' => set_value('id'),
	    'IDType' => set_value('IDType'),
	    'IDNumber' => set_value('IDNumber'),
	    'IssueDate' => set_value('IssueDate'),
	    'ExpiryDate' => set_value('ExpiryDate'),
	    'DocImageURL' => set_value('DocImageURL'),
	    'Stamp' => set_value('Stamp'),
	    'ClientId' => set_value('ClientId'),
	    'photograph' => set_value('photograph'),
	    'signature' => set_value('signature'),
	    'Id_back' => set_value('Id_back'),
	    'id_front' => set_value('id_front'),
	);
        $this->load->view('proofofidentity/proofofidentity_form', $data);
    }
    
    public function create_action() 
    {

            $data = array(
		'IDType' => $this->input->post('IDType',TRUE),
		'IDNumber' => $this->input->post('IDNumber',TRUE),
		'IssueDate' => $this->input->post('IssueDate',TRUE),
		'ExpiryDate' => $this->input->post('ExpiryDate',TRUE),
		'ClientId' => $this->input->post('ClientId',TRUE),
		'photograph' => $this->input->post('photograph',TRUE),
		'signature' => $this->input->post('signature',TRUE),
		'Id_back' => $this->input->post('Id_back',TRUE),
		'id_front' => $this->input->post('id_front',TRUE),
	    );
            $this->Proofofidentity_model->insert($data);
            $this->session->set_flashdata('message', 'Create Record Success');
            redirect($_SERVER['HTTP_REFERER']);

    }
    
    public function update($id) 
    {
        $row = $this->Proofofidentity_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('proofofidentity/update_action'),
		'id' => set_value('id', $row->id),
		'IDType' => set_value('IDType', $row->IDType),
		'IDNumber' => set_value('IDNumber', $row->IDNumber),
		'IssueDate' => set_value('IssueDate', $row->IssueDate),
		'ExpiryDate' => set_value('ExpiryDate', $row->ExpiryDate),
		'DocImageURL' => set_value('DocImageURL', $row->DocImageURL),
		'Stamp' => set_value('Stamp', $row->Stamp),
		'ClientId' => set_value('ClientId', $row->ClientId),
		'photograph' => set_value('photograph', $row->photograph),
		'signature' => set_value('signature', $row->signature),
		'Id_back' => set_value('Id_back', $row->Id_back),
		'id_front' => set_value('id_front', $row->id_front),
	    );
            $this->load->view('proofofidentity/proofofidentity_form', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('proofofidentity'));
        }
    }
    
    public function update_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('id', TRUE));
        } else {
            $data = array(
		'IDType' => $this->input->post('IDType',TRUE),
		'IDNumber' => $this->input->post('IDNumber',TRUE),
		'IssueDate' => $this->input->post('IssueDate',TRUE),
		'ExpiryDate' => $this->input->post('ExpiryDate',TRUE),


		'ClientId' => $this->input->post('ClientId',TRUE),
		'photograph' => $this->input->post('photograph',TRUE),
		'signature' => $this->input->post('signature',TRUE),
		'Id_back' => $this->input->post('Id_back',TRUE),
		'id_front' => $this->input->post('id_front',TRUE),
	    );

            $this->Proofofidentity_model->update($this->input->post('id', TRUE), $data);
            $this->toaster->success('Success!, KYC updated ');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    
    public function delete($id) 
    {
        $row = $this->Proofofidentity_model->get_by_id($id);

        if ($row) {
            $this->Proofofidentity_model->delete($id);
            $this->session->set_flashdata('message', 'Delete Record Success');
            redirect(site_url('proofofidentity'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('proofofidentity'));
        }
    }

    public function _rules() 
    {
	$this->form_validation->set_rules('IDType', 'idtype', 'trim|required');
	$this->form_validation->set_rules('IDNumber', 'idnumber', 'trim|required');
	$this->form_validation->set_rules('IssueDate', 'issuedate', 'trim|required');
	$this->form_validation->set_rules('ExpiryDate', 'expirydate', 'trim|required');

	$this->form_validation->set_rules('ClientId', 'clientid', 'trim|required');
	$this->form_validation->set_rules('photograph', 'photograph', 'trim|required');
	$this->form_validation->set_rules('signature', 'signature', 'trim|required');
	$this->form_validation->set_rules('Id_back', 'id back', 'trim|required');
	$this->form_validation->set_rules('id_front', 'id front', 'trim|required');

	$this->form_validation->set_rules('id', 'id', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

