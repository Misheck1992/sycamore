<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Payement_schedules_model extends CI_Model
{

    public $table = 'payement_schedules';
    public $id = '';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
    }

    // get all
    function get_all()
    {
        $this->db->order_by($this->id, $this->order);
        return $this->db->get($this->table)->result();
    }
	function get_all_by_id($id)
	{
		$this->db->select('*');
		$this->db->order_by($this->id, $this->order);
		$this->db->join('loan','loan.loan_id = payement_schedules.loan_id');
		$this->db->where('payement_schedules.loan_id',$id);
		return $this->db->get($this->table)->result();
	}
    public function new_pay_new($loan_number,$pay_number,$amount, $date, $tid){
        $this->db->select("*")->from($this->table);
        $this->db->where('loan_id', $loan_number);
        $this->db->where('payment_number', $pay_number);
        $get_real_amount = $this->db->get()->row();
        $to_pay = $get_real_amount->amount - $get_real_amount->paid_amount ;


        if(intval($to_pay) > intval($amount) ){


            $final_paid = $amount + $get_real_amount->paid_amount ;
            $data = array(
                'partial_paid'=>'YES',

                'paid_amount'=>$final_paid,
                'paid_date'=> $date
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);

            $transaction = array(
                'ref' =>$tid,
                'loan_id' => $loan_number,
                'amount' => $final_paid,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'added_by' => $this->session->userdata('user_id'),
                'date_stamp'=> $date

            );
            $this->db->insert('transactions',$transaction);
            return true;

        }
        elseif(intval($amount) > intval($to_pay)) {

            $our_amount = $amount;
//get all loans
            $this->db->select("*")
                ->from($this->table)

                ->where('loan_id', $loan_number)
                ->where('status', 'NOT PAID');
//                ->or_where('partial_paid', 'NO');
            $this->db->order_by('payment_number', 'ASC');

            $result = $this->db->get()->result();

            foreach ($result as $lr){




                if($our_amount < ($lr->amount-$lr->paid_amount) ){
                    $data = array(
                        'partial_paid'=>'YES',
                        'paid_amount'=>$our_amount,
                        'paid_date'=> $date
                    );
                    $this->db->where('loan_id', $loan_number);
                    $this->db->where('payment_number',  $lr->payment_number);
                    $this->db->update($this->table,$data);

                    $transaction = array(
                        'ref' => $tid,
                        'loan_id' => $loan_number,
                        'amount' => $our_amount ,
                        'payment_number' =>  $lr->payment_number,
                        'transaction_type' => 3,
                        'payment_proof' => 'null',
                        'added_by' => $this->session->userdata('user_id'),
                        'date_stamp'=> $date

                    );
                    $this->db->insert('transactions',$transaction);
                    return true;
                }
                elseif($our_amount==($lr->amount-$lr->paid_amount)){
                    $data = array(
                        'partial_paid'=>'NO',
                        'status'=>'PAID',
                        'paid_amount'=>$our_amount,
                        'paid_date'=> $date
                    );
                    $this->db->where('loan_id', $loan_number);
                    $this->db->where('payment_number', $lr->payment_number);
                    $this->db->update($this->table,$data);
                    $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$lr->payment_number+1));
                    $count_schedules = $this->count_payments($loan_number);
                    if(intval($count_schedules) == intval($lr->payment_number)){
                        $this->db->where('loan_id', $loan_number)->
                        update('loan',array('loan_status'=>'CLOSED'));
                    }
                    $transaction = array(
                        'ref' => $tid,
                        'loan_id' => $loan_number,
                        'amount' => $our_amount,
                        'payment_number' => $lr->payment_number,
                        'transaction_type' => 3,
                        'payment_proof' => 'null',
                        'added_by' => $this->session->userdata('user_id'),
                        'date_stamp'=> $date

                    );
                    $this->db->insert('transactions',$transaction);
                    return true;
                }
                else{
                    $our_amount = $our_amount - ($lr->amount-$lr->paid_amount);
                    $data = array(
                        'partial_paid'=>'NO',
                        'status'=>'PAID',
                        'paid_amount'=>$lr->amount,
                        'paid_date'=> $date
                    );
                    $this->db->where('loan_id', $loan_number);
                    $this->db->where('payment_number', $lr->payment_number);
                    $this->db->update($this->table,$data);
                    $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$lr->payment_number+1));
                    $count_schedules = $this->count_payments($loan_number);
                    if(intval($count_schedules) == intval($lr->payment_number)){
                        $this->db->where('loan_id', $loan_number)->
                        update('loan',array('loan_status'=>'CLOSED'));
                    }
                    $transaction = array(
                        'ref' => $tid,
                        'loan_id' => $loan_number,
                        'amount' => $lr->amount,
                        'payment_number' => $lr->payment_number,
                        'transaction_type' => 3,
                        'payment_proof' => 'null',
                        'added_by' => $this->session->userdata('user_id'),
                        'date_stamp'=> $date

                    );
                    $this->db->insert('transactions',$transaction);
                }

            }
            return true;

        }

        elseif(intval($to_pay) === intval($amount)){

            $new_to_pay = $amount;
            $final_paid = $new_to_pay + $get_real_amount->paid_amount ;

            $data = array(
                'partial_paid'=>'NO',
                'status'=>'PAID',
                'paid_amount'=>$final_paid,
                'paid_date'=> $date
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);
            $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
            $count_schedules = $this->count_payments($loan_number);
            if(intval($count_schedules) == intval($pay_number)){
                $this->db->where('loan_id', $loan_number)->
                update('loan',array('loan_status'=>'CLOSED'));
            }
            $transaction = array(
                'ref' => $tid,
                'loan_id' => $loan_number,
                'amount' => $final_paid,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'added_by' => $this->session->userdata('user_id'),
                'date_stamp'=> $date

            );
            $this->db->insert('transactions',$transaction);
            return true;

        }
        else{

        }
    }
    public function pay_off($loan_number,$pay_number,$amount, $date, $tid){
        $this->db->select("*")->from($this->table);
        $this->db->where('loan_id', $loan_number);
        $this->db->where('payment_number', $pay_number);
        $get_real_amount = $this->db->get()->row();
        $to_pay = $get_real_amount->amount - $get_real_amount->paid_amount ;


        if(intval($to_pay) > intval($amount) ){


            $final_paid = $amount + $get_real_amount->paid_amount ;
            $data = array(
                'partial_paid'=>'YES',

                'paid_amount'=>$final_paid,
                'paid_date'=> $date
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);

            $transaction = array(
                'ref' =>$tid,
                'loan_id' => $loan_number,
                'amount' => $final_paid,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'added_by' => $this->session->userdata('user_id'),
                'date_stamp'=> $date

            );
            $this->db->insert('transactions',$transaction);
            return true;

        }
        elseif(intval($amount) > intval($to_pay)) {

            $our_amount = $amount;
//get all loans
            $this->db->select("*")
                ->from($this->table)

                ->where('loan_id', $loan_number)
                ->where('status', 'NOT PAID');
//                ->or_where('partial_paid', 'NO');
            $this->db->order_by('payment_number', 'ASC');

            $result = $this->db->get()->result();

            foreach ($result as $lr){




                if($our_amount < ($lr->amount-$lr->paid_amount) ){
                    $data = array(
                        'partial_paid'=>'YES',
                        'paid_amount'=>$our_amount,
                        'paid_date'=> $date
                    );
                    $this->db->where('loan_id', $loan_number);
                    $this->db->where('payment_number',  $lr->payment_number);
                    $this->db->update($this->table,$data);

                    $transaction = array(
                        'ref' => $tid,
                        'loan_id' => $loan_number,
                        'amount' => $our_amount ,
                        'payment_number' =>  $lr->payment_number,
                        'transaction_type' => 3,
                        'payment_proof' => 'null',
                        'added_by' => $this->session->userdata('user_id'),
                        'date_stamp'=> $date

                    );
                    $this->db->insert('transactions',$transaction);
                    return true;
                }
                elseif($our_amount==($lr->amount-$lr->paid_amount)){
                    $data = array(
                        'partial_paid'=>'NO',
                        'status'=>'PAID',
                        'paid_amount'=>$our_amount,
                        'paid_date'=> $date
                    );
                    $this->db->where('loan_id', $loan_number);
                    $this->db->where('payment_number', $lr->payment_number);
                    $this->db->update($this->table,$data);
                    $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$lr->payment_number+1));
                    $count_schedules = $this->count_payments($loan_number);
                    if(intval($count_schedules) == intval($lr->payment_number)){
                        $this->db->where('loan_id', $loan_number)->
                        update('loan',array('loan_status'=>'CLOSED'));
                    }
                    $transaction = array(
                        'ref' => $tid,
                        'loan_id' => $loan_number,
                        'amount' => $our_amount,
                        'payment_number' => $lr->payment_number,
                        'transaction_type' => 3,
                        'payment_proof' => 'null',
                        'added_by' => $this->session->userdata('user_id'),
                        'date_stamp'=> $date

                    );
                    $this->db->insert('transactions',$transaction);
                    return true;
                }
                else{
                    $our_amount = $our_amount - ($lr->amount-$lr->paid_amount);
                    $data = array(
                        'partial_paid'=>'NO',
                        'status'=>'PAID',
                        'paid_amount'=>$lr->amount,
                        'paid_date'=> $date
                    );
                    $this->db->where('loan_id', $loan_number);
                    $this->db->where('payment_number', $lr->payment_number);
                    $this->db->update($this->table,$data);
                    $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$lr->payment_number+1));
                    $count_schedules = $this->count_payments($loan_number);
                    if(intval($count_schedules) == intval($lr->payment_number)){
                        $this->db->where('loan_id', $loan_number)->
                        update('loan',array('loan_status'=>'CLOSED'));
                    }
                    $transaction = array(
                        'ref' => $tid,
                        'loan_id' => $loan_number,
                        'amount' => $lr->amount,
                        'payment_number' => $lr->payment_number,
                        'transaction_type' => 3,
                        'payment_proof' => 'null',
                        'added_by' => $this->session->userdata('user_id'),
                        'date_stamp'=> $date

                    );
                    $this->db->insert('transactions',$transaction);
                }

            }
            return true;

        }

        elseif(intval($to_pay) === intval($amount)){

            $new_to_pay = $amount;
            $final_paid = $new_to_pay + $get_real_amount->paid_amount ;

            $data = array(
                'partial_paid'=>'NO',
                'status'=>'PAID',
                'paid_amount'=>$final_paid,
                'paid_date'=> $date
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);
            $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
            $count_schedules = $this->count_payments($loan_number);
            if(intval($count_schedules) == intval($pay_number)){
                $this->db->where('loan_id', $loan_number)->
                update('loan',array('loan_status'=>'CLOSED'));
            }
            $transaction = array(
                'ref' => $tid,
                'loan_id' => $loan_number,
                'amount' => $final_paid,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'added_by' => $this->session->userdata('user_id'),
                'date_stamp'=> $date

            );
            $this->db->insert('transactions',$transaction);
            return true;

        }
        else{

        }
    }
	function get_total($id)
	{
		$this->db->select('SUM(amount) as total_payment, SUM(paid_amount) as paid_amount');

		$this->db->where('payement_schedules.loan_id',$id);
		return $this->db->get($this->table)->row();
	}
	function edits()
	{
		$this->db->select('*');


		$r= $this->db->get('defect_loand')->result();
		$count = 0;
		foreach ($r as $rr){
			$this->db->where('loan_id', $rr->loan_id);
			$this->db->delete($this->table);
			$count ++;
		}
		echo $count;
	}
	  function get_all_by_idPayNumber($id,$paymentnumber)
	{
		$this->db->select('*');
		$this->db->order_by($this->id, $this->order);
		$this->db->join('loan','loan.loan_id = payement_schedules.loan_id');
		$this->db->where('payement_schedules.loan_id',$id);
        $this->db->where('payement_schedules.payment_number',$paymentnumber);
		return $this->db->get($this->table)->result();
	}
	
	 // update data
     function updateTopup($id,$paymentnumber, $data)
     {
         $this->db->where('loan_id', $id);
         $this->db->where('payment_number', $paymentnumber);
         $this->db->update($this->table, $data);
     }
     
   function get_next($pay_number,$id)
    {

        $this->db->where('loan_id',$id);
        $this->db->where('payment_number',$pay_number);
        return $this->db->get($this->table)->row();
    }

    // get data by id
    function get_by_id($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->get($this->table)->row();
    }
    function out_pay($loan_number, $pay_number, $amount, $date)
    {
$tid = "ST." . date('Y') . date('m') . date('d') . '.' . rand(100, 999);
        // Get all loans
        $this->db->select("*")
            ->from($this->table)
            ->where('loan_id', $loan_number)

            ->where('status', 'NOT PAID');

        $result = $this->db->get()->result();
        $balance = $amount;
        foreach ($result as $lr) {

            $amount_to_pay = ($lr->amount - $lr->paid_amount);

            // If the balance is already 0, break out of the loop
            if ($balance <= 0 ) {
                break;
            }

            if ($amount_to_pay == $balance) {
                $data = array(
                    'partial_paid' => 'NO',
                    'status' => 'PAID',
                    'paid_amount' => $balance,
                    'paid_date' => $date
                );
                $balance = 0;
            } elseif ($amount_to_pay > $balance) {
                $data = array(
                    'partial_paid' => 'YES',
                    'paid_amount' => $balance + $lr->paid_amount,
                    'paid_date' => $date
                );
                $balance = 0;
            } else {
                $data = array(
                    'partial_paid' => 'NO',
                    'status' => 'PAID',
                    'paid_amount' => $lr->amount,
                    'paid_date' => $date
                );
                $balance -= $lr->amount;
            }

            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $lr->payment_number);
            $this->db->update($this->table, $data);
            $this->db->where('loan_id', $loan_number)->update('loan', array('next_payment_id' => $pay_number));
            $count_schedules = $this->count_payments($loan_number);
            if (intval($count_schedules) == intval($lr->payment_number)) {
                $this->db->where('loan_id', $loan_number)->
                update('loan', array('loan_status' => 'CLOSED'));
            }

            $transaction = array(
                'ref' => $tid,
                'loan_id' => $loan_number,
                'amount' => 0,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'added_by' => $this->session->userdata('user_id'),
                'date_stamp' => $date

            );
            $this->db->insert('transactions', $transaction);
            $pay_number++;
        }
        return $tid;
    }


    public function new_pay($loan_number,$pay_number,$amount, $date){
        $this->db->select("*")->from($this->table);
        $this->db->where('loan_id', $loan_number);
        $this->db->where('payment_number', $pay_number);
        $get_real_amount = $this->db->get()->row();
        $to_pay = $get_real_amount->amount - $get_real_amount->paid_amount ;


        if(intval($to_pay) > intval($amount) ){


            $final_paid = $amount + $get_real_amount->paid_amount ;
            $data = array(
                'partial_paid'=>'YES',

                'paid_amount'=>$final_paid,
                'paid_date'=> $date
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);

            $transaction = array(
                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                'loan_id' => $loan_number,
                'amount' => $final_paid,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'added_by' => $this->session->userdata('user_id'),
                'date_stamp'=> $date

            );
            $this->db->insert('transactions',$transaction);
            return true;

        }
        elseif(intval($amount) > intval($to_pay)) {

            $our_amount = $amount;
//get all loans
            $this->db->select("*")
                ->from($this->table)

                ->where('loan_id', $loan_number)
                ->where('status', 'NOT PAID');
//                ->or_where('partial_paid', 'NO');
            $this->db->order_by('payment_number', 'ASC');

            $result = $this->db->get()->result();

            foreach ($result as $lr){




                if($our_amount < ($lr->amount-$lr->paid_amount) ){
                    $data = array(
                        'partial_paid'=>'YES',
                        'paid_amount'=>$our_amount,
                        'paid_date'=> $date
                    );
                    $this->db->where('loan_id', $loan_number);
                    $this->db->where('payment_number',  $lr->payment_number);
                    $this->db->update($this->table,$data);

                    $transaction = array(
                        'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                        'loan_id' => $loan_number,
                        'amount' => $our_amount ,
                        'payment_number' =>  $lr->payment_number,
                        'transaction_type' => 3,
                        'payment_proof' => 'null',
                        'added_by' => $this->session->userdata('user_id'),
                        'date_stamp'=> $date

                    );
                    $this->db->insert('transactions',$transaction);
                    return true;
                }
                elseif($our_amount==($lr->amount-$lr->paid_amount)){
                    $data = array(
                        'partial_paid'=>'NO',
                        'status'=>'PAID',
                        'paid_amount'=>$our_amount,
                        'paid_date'=> $date
                    );
                    $this->db->where('loan_id', $loan_number);
                    $this->db->where('payment_number', $lr->payment_number);
                    $this->db->update($this->table,$data);
                    $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$lr->payment_number+1));
                    $count_schedules = $this->count_payments($loan_number);
                    if(intval($count_schedules) == intval($lr->payment_number)){
                        $this->db->where('loan_id', $loan_number)->
                        update('loan',array('loan_status'=>'CLOSED'));
                    }
                    $transaction = array(
                        'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                        'loan_id' => $loan_number,
                        'amount' => $our_amount,
                        'payment_number' => $lr->payment_number,
                        'transaction_type' => 3,
                        'payment_proof' => 'null',
                        'added_by' => $this->session->userdata('user_id'),
                        'date_stamp'=> $date

                    );
                    $this->db->insert('transactions',$transaction);
                    return true;
                }
                else{
                    $our_amount = $our_amount - ($lr->amount-$lr->paid_amount);
                    $data = array(
                        'partial_paid'=>'NO',
                        'status'=>'PAID',
                        'paid_amount'=>$lr->amount,
                        'paid_date'=> $date
                    );
                    $this->db->where('loan_id', $loan_number);
                    $this->db->where('payment_number', $lr->payment_number);
                    $this->db->update($this->table,$data);
                    $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$lr->payment_number+1));
                    $count_schedules = $this->count_payments($loan_number);
                    if(intval($count_schedules) == intval($lr->payment_number)){
                        $this->db->where('loan_id', $loan_number)->
                        update('loan',array('loan_status'=>'CLOSED'));
                    }
                    $transaction = array(
                        'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                        'loan_id' => $loan_number,
                        'amount' => $lr->amount,
                        'payment_number' => $lr->payment_number,
                        'transaction_type' => 3,
                        'payment_proof' => 'null',
                        'added_by' => $this->session->userdata('user_id'),
                        'date_stamp'=> $date

                    );
                    $this->db->insert('transactions',$transaction);
                }

            }
            return true;

        }

        elseif(intval($to_pay) === intval($amount)){

            $new_to_pay = $amount;
            $final_paid = $new_to_pay + $get_real_amount->paid_amount ;

            $data = array(
                'partial_paid'=>'NO',
                'status'=>'PAID',
                'paid_amount'=>$final_paid,
                'paid_date'=> $date
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);
            $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
            $count_schedules = $this->count_payments($loan_number);
            if(intval($count_schedules) == intval($pay_number)){
                $this->db->where('loan_id', $loan_number)->
                update('loan',array('loan_status'=>'CLOSED'));
            }
            $transaction = array(
                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                'loan_id' => $loan_number,
                'amount' => $final_paid,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'added_by' => $this->session->userdata('user_id'),
                'date_stamp'=> $date

            );
            $this->db->insert('transactions',$transaction);
            return true;

        }
        else{

        }
    }
    
    
    //
    
    
                     public function topnew_pay($loan_number,$pay_number,$amount,$realaccountbalance){
                    
                     
                         
                         $amountComing=$amount;
                         
                            $datapaymentschedule = get_by_id2($this->table,'loan_id ='.$loan_number.'  AND payment_number ='.$pay_number);
                            //get loan products id
                             $dataloanproductsid = get_by_id2('loan','loan_id ='.$loan_number);
                                   //get loan products details
                            
                             $dataloanproducts = get_by_id2('loan_products','loan_product_id ='.$dataloanproductsid->loan_product);
                              $this->db->select("*")->from($this->table);
        $this->db->where('loan_id', $loan_number);
        $this->db->where('payment_number', $pay_number);
        $get_real_amount = $this->db->get()->row();
        $to_pay = $get_real_amount->amount - $get_real_amount->paid_amount ;
		                 

                           
                            if($datapaymentschedule ->loan_balance>$realaccountbalance){
                             $newbalance=($datapaymentschedule ->loan_balance- $realaccountbalance);
                            }
                            else {
                                 $newbalance=$realaccountbalance-$to_pay;
                            }
                           
                             //interest
   
                         	$amount_interest = $amount *( ($dataloanproducts ->interest/100)*12);


		


		//total payments applying interest
		$amount_total =$newbalance + $amount_interest * $dataloanproductsid ->loan_period * 1;

		//payment per term
		$amount_term = number_format(round($newbalance / ($dataloanproductsid ->loan_period * 1), 2) + $amount_interest, 2, '.', '');

	
		//$monthly_payment = $amount*($i/12)*pow((1+$i/12),$months)/(pow((1+$i/12),$months)-1);

		$i = ($dataloanproducts->interest / 100) * 12;
		$af = ($dataloanproducts->admin_fees / 100) * 12;
		$lc = ($dataloanproducts->loan_cover / 100) * 12;
		$total_deduction = $i + $af + $lc;

       $months=  $dataloanproductsid->loan_period;
		$monthly_payment = $amount * ($total_deduction / 12) * pow((1 + $total_deduction / 12), $months) / (pow((1 + $total_deduction / 12), $months) - 1);
		$monthly_payment1 = $amount * ($total_deduction / 12) * pow((1 + $total_deduction / 12), $months) / (pow((1 + $total_deduction / 12), $months) - 1);
		$current_balance = $amount;
		$current_balance1 = $amount;
		$payment_counter = 1;
		$total_interest = 0;
		$total_interest1 = 0;
		$total_admin_fees = 0;
		$total_admin_fees1 = 0;
		$total_loan_cover = 0;
		$total_loan_cover1 = 0;



		$ii=1;





		while($current_balance1 > 0) {
			//create rows



			$towards_interest1 = ($i / 12) * $current_balance1;  //this calculates the portion of your monthly payment that goes towards interest
			$towards_fees = ($af / 12) * $current_balance1;  //this calculates the portion of your monthly payment that goes towards administration fees
			$towards_lc = ($lc / 12) * $current_balance1;  //this calculates the portion of your monthly payment that goes towards administration fees

			if ($monthly_payment1 > $current_balance1) {
				$monthly_payment1 = $current_balance1 + $towards_interest1 + $towards_fees + $towards_lc;

			}
			$towards_balance1 = $monthly_payment1 - ($towards_interest1 + $towards_fees + $towards_lc);
			$total_interest1 = $total_interest1 + $towards_interest1;
			$total_admin_fees = $total_admin_fees + $towards_fees;
			$total_loan_cover = $total_loan_cover + $towards_lc;
			$current_balance1 = $current_balance1 - $towards_balance1;

		}



		
		
		
		               
        if(intval($to_pay) > intval($newbalance) ){


            $final_paid = $amount + $get_real_amount->paid_amount ;
            $data = array(
                'partial_paid'=>'YES',
                'amount' => $monthly_payment,
                'principal' =>$realaccountbalance,
                 'interest' => 	$amount_interest ,
                  'loan_balance' => $newbalance,
                 
                'paid_amount'=>$final_paid
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);

            $transaction = array(
                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                'loan_id' => $loan_number,
                'amount' => $final_paid,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'added_by' => $this->session->userdata('user_id')

            );
            $this->db->insert('transactions',$transaction);
            return true;

        }elseif(intval($to_pay) === intval($newbalance)){


            $new_to_pay = $newbalance;
            $final_paid = $new_to_pay + $get_real_amount->paid_amount ;

            $data = array(
                'partial_paid'=>'NO',
                'status'=>'PAID',
                'paid_amount'=>$final_paid
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);
            $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
                $count_schedules = $this->count_payments($loan_number);
                if(intval($count_schedules) == intval($pay_number)){
                    $this->db->where('loan_id', $loan_number)->
                    update('loan',array('loan_status'=>'CLOSED'));
                }
            $transaction = array(
                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                'loan_id' => $loan_number,
                'amount' => $final_paid,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'added_by' => $this->session->userdata('user_id')

            );
            $this->db->insert('transactions',$transaction);
            return true;

        }else{

        }
    }
    
    
    //
    public function finish_pay($loan_number,$pay_number,$amount){
        $this->db->select("*")->from($this->table);
        $this->db->where('loan_id', $loan_number);
        $this->db->where('payment_number', $pay_number);


            $data = array(
                'partial_paid'=>'NO',
                'status'=>'PAID',
                'paid_amount'=>$amount
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);
            $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
        $this->db->where('loan_id', $loan_number)->
        update('loan',array('loan_status'=>'CLOSED'));
            $transaction = array(
                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                'loan_id' => $loan_number,
                'amount' => $amount,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'added_by' => $this->session->userdata('user_id')

            );
            $this->db->insert('transactions',$transaction);
            return true;
            
    }
    function count_payments($loan_number){
        $this->db->select("*")->from($this->table);
        $this->db->where('loan_id', $loan_number);
        return $this->db->count_all_results();
    }
    function pay($loan_number,$pay_number,$amount)
    {
//get payment id
        $this->db->select("*")->from($this->table);
            $this->db->where('loan_id', $loan_number);
        $this->db->where('payment_number', $pay_number);
        $get_real_amount = $this->db->get()->row();
            if($get_real_amount->partial_paid == 'Yes'){
                $new_paid_bal = $get_real_amount->amount - $get_real_amount->paid_amount;
                $data = array(
                    'partial_paid'=>'Yes',
                    'paid_amount'=>$amount
                );
                $this->db->where('loan_id', $loan_number);
                $this->db->where('payment_number', $pay_number);
                $this->db->update($this->table,$data);
//            $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
                $transaction = array(
                    'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                    'loan_id' => $loan_number,
                    'amount' => $amount,
                    'payment_number' => $pay_number,
                    'transaction_type' => 3,
                    'payment_proof' => 'null',
                    'added_by' => $this->session->userdata('user_id')

                );
                $this->db->insert('transactions',$transaction);

            }else{

            }
        if($get_real_amount->amount == $amount){
            $data = array(
                'status'=>'PAID',
                'paid_amount'=>$amount
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);
            $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
            $transaction = array(
                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                'loan_id' => $loan_number,
                'amount' => $amount,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'added_by' => $this->session->userdata('user_id')

            );
            $this->db->insert('transactions',$transaction);
        }
        else{
            $data = array(
                'partial_paid'=>'Yes',
                'paid_amount'=>$amount
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);
//            $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
            $transaction = array(
                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                'loan_id' => $loan_number,
                'amount' => $amount,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'added_by' => $this->session->userdata('user_id')

            );
            $this->db->insert('transactions',$transaction);
        }



		return true;
    }
    public function sum_interests($from,$to){
	$this->db->select('SUM(interest) as interest');
	$this->db->from('payement_schedules')->where('status','PAID');

	// $this->db->join('lend_payments','lend_payments.borrower_loan_id=lend_borrower_loans.id');

	if($from !="" && $to !=""){
		$this->db->where('payment_schedule BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

	}

	return $this->db->get()->row();
}
public function sum_admin($from,$to){
	$this->db->select('SUM(padmin_fee) as admin_fee');
	$this->db->from('payement_schedules')->where('status','PAID');

	// $this->db->join('lend_payments','lend_payments.borrower_loan_id=lend_borrower_loans.id');

	if($from !="" && $to !=""){
		$this->db->where('payment_schedule BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

	}

	return $this->db->get()->row();
}
public function sum_cover($from,$to){
	$this->db->select('SUM(ploan_cover) as loan_cover');
	$this->db->from('payement_schedules')->where('status','PAID');

	// $this->db->join('lend_payments','lend_payments.borrower_loan_id=lend_borrower_loans.id');

	if($from !="" && $to !=""){
		$this->db->where('payment_schedule BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

	}

	return $this->db->get()->row();
}
public function bad_debits($from,$to){
	$this->db->select('SUM(principal) as principal')->join('loan','loan.loan_id=payement_schedules.loan_id');
	$this->db->from('payement_schedules')->where('loan_status','DEFAULTED');

	// $this->db->join('lend_payments','lend_payments.borrower_loan_id=lend_borrower_loans.id');

	if($from !="" && $to !=""){
		$this->db->where('payment_schedule BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

	}

	return $this->db->get()->row();
}
	function get_last_payment($loan_number)
	{
		//get last payment info
		$this->db->from($this->table);
		$this->db->where('loan_id', $loan_number);
		$this->db->order_by('payment_schedule', 'DESC');
		$this->db->limit(1);
		$result = $this->db->get();

		if ($result->num_rows() > 0) {

			return $result->row();
		}

		return FALSE;
	}
	function get_first_payment($loan_number)
	{
		//get last payment info
		$this->db->from($this->table);
		$this->db->where('loan_id', $loan_number);
		$this->db->order_by('payment_schedule', 'ASC');
		$this->db->limit(1);
		$result = $this->db->get();

		if ($result->num_rows() > 0) {
			return $result->row();
		}

		return FALSE;
	}
    function pay_advance($loan_number,$amount,$arr)
    {
		for($i=0;$i <count($arr);$i++){

			$data = array(
				'status'=>'PAID',
				'paid_amount'=>$amount
			);
			$this->db->where('loan_id', $loan_number);
			$this->db->where('payment_number', $arr[$i]);
			$this->db->update($this->table,$data);
			$this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$arr[$i]+1));

			$transaction = array(
				'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
				'loan_id' => $loan_number,
				'amount' => $amount,
				
				'payment_number' => $arr[$i],
				'transaction_type' => 3,
				'added_by' => $this->session->userdata('user_id')

			);
			$this->db->insert('transactions',$transaction);


		}


		return true;
    }
    
    // get total rows
    function total_rows($q = NULL) {
        $this->db->like('', $q);
	$this->db->or_like('id', $q);
	$this->db->or_like('customer', $q);
	$this->db->or_like('loan_id', $q);
	$this->db->or_like('payment_schedule', $q);
	$this->db->or_like('payment_number', $q);
	$this->db->or_like('amount', $q);
	$this->db->or_like('principal', $q);
	$this->db->or_like('interest', $q);
	$this->db->or_like('paid_amount', $q);
	$this->db->or_like('loan_balance', $q);
	$this->db->or_like('status', $q);
	$this->db->or_like('loan_date', $q);
	$this->db->or_like('paid_date', $q);
	$this->db->or_like('marked_due', $q);
	$this->db->or_like('marked_due_date', $q);
	$this->db->from($this->table);
        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($limit, $start = 0, $q = NULL) {
        $this->db->order_by($this->id, $this->order);
        $this->db->like('', $q);
	$this->db->or_like('id', $q);
	$this->db->or_like('customer', $q);
	$this->db->or_like('loan_id', $q);
	$this->db->or_like('payment_schedule', $q);
	$this->db->or_like('payment_number', $q);
	$this->db->or_like('amount', $q);
	$this->db->or_like('principal', $q);
	$this->db->or_like('interest', $q);
	$this->db->or_like('paid_amount', $q);
	$this->db->or_like('loan_balance', $q);
	$this->db->or_like('status', $q);
	$this->db->or_like('loan_date', $q);
	$this->db->or_like('paid_date', $q);
	$this->db->or_like('marked_due', $q);
	$this->db->or_like('marked_due_date', $q);
	$this->db->limit($limit, $start);
        return $this->db->get($this->table)->result();
    }

    // insert data
    function insert($data)
    {
        $this->db->insert($this->table, $data);
    }

    // update data
    function update1($id, $data)
    {
        $this->db->where('loan_id', $id);
        $this->db->update($this->table, $data);
    }
	function update($id, $data)
    {
        $this->db->where($this->id, $id);
        $this->db->update($this->table, $data);
    }

    // delete data
    function delete($id)
    {
        $this->db->where($this->id, $id);
        $this->db->delete($this->table);
    }
//    arrears
function  arrears($loan,$from,$to,$by_date,$idofficer ){
    	$this->db->select("*,employees.Firstname as efname,individual_customers.Firstname as ifname,individual_customers.Lastname as ilname,employees.Lastname as elname,")->from($this->table)
			->join('loan','loan.loan_id = payement_schedules.loan_id')
			->join('loan_products', 'loan_products.loan_product_id = loan.loan_product', 'LEFT')
			->join('individual_customers','individual_customers.id = payement_schedules.customer','LEFT')
			->join('employees','employees.id = loan.loan_added_by','LEFT')
			->where('payment_schedule < CURDATE()')
			->where('payement_schedules.status','NOT PAID')
			->where('loan.loan_status','ACTIVE')
    ->where('loan.loan_status <>','DELETED');
	if($from !="" && $to !=""){
		$this->db->where('payment_schedule BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

	}

	if($by_date=="one_day"){
		$this->db->where('payement_schedules.payment_schedule = SUBDATE(CURDATE(),1)');
	}
	if($by_date=="three_days"){
		$this->db->where('payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),3) AND SUBDATE(CURDATE(),1)');

	}
	if($by_date=="week"){
		$this->db->where('payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),7) AND SUBDATE(CURDATE(),1)');

	}
	if($by_date=="month"){
		$this->db->where('payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),30) AND SUBDATE(CURDATE(),1)');

	}
	if($by_date=="2month"){
		$this->db->where('payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),60) AND SUBDATE(CURDATE(),1)');

	}if($by_date=="3month"){
		$this->db->where('payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),90) AND SUBDATE(CURDATE(),1)');

	}
	if($loan !="All"){
		$this->db->where('payement_schedules.loan_id',$loan);
	}
    if($idofficer !="All"){
        $this->db->where('loan.loan_added_by',$idofficer );
    }
    return	$this->db->get()->result();
}
function  payment_today(){
	date_default_timezone_set("Africa/Blantyre");
	$date = new DateTime("now");

	$curr_date = $date->format('Y-m-d');


	$this->db->select("*,employees.Firstname as efname,individual_customers.Firstname as ifname,individual_customers.Lastname as ilname,employees.Lastname as elname,")->from($this->table)
		->join('loan','loan.loan_id = payement_schedules.loan_id')
		->join('loan_products', 'loan_products.loan_product_id = loan.loan_product', 'LEFT')
		->join('individual_customers','individual_customers.id = payement_schedules.customer','LEFT')
		->join('employees','employees.id = loan.loan_added_by','LEFT')
			->where('DATE(payment_schedule)',$curr_date)
			->where('payement_schedules.status','NOT PAID')
			->where('loan.loan_status','ACTIVE');

    return	$this->db->get()->result();
}
function  payment_month(){
	date_default_timezone_set("Africa/Blantyre");
	$date = new DateTime("now");

	$curr_date = $date->format('m');


	$this->db->select("*,employees.Firstname as efname,individual_customers.Firstname as ifname,individual_customers.Lastname as ilname,employees.Lastname as elname,")->from($this->table)
		->join('loan','loan.loan_id = payement_schedules.loan_id','LEFT')
		->join('loan_products', 'loan_products.loan_product_id = loan.loan_product', 'LEFT')
		->join('individual_customers','individual_customers.id = payement_schedules.customer','LEFT')
		->join('employees','employees.id = loan.loan_added_by','LEFT')
			->where('MONTH(payment_schedule)',$curr_date)
			->where('payement_schedules.status','NOT PAID')
        ->join('branches','branches.id = loan.branch','LEFT')
			->where('loan.loan_status','ACTIVE');

    return	$this->db->get()->result();
}
function  payment_date($from,$to,$user,$product, $branch){
	date_default_timezone_set("Africa/Blantyre");
	$date = new DateTime("now");
	$curr_date = $date->format('m');
	$this->db->select("*,employees.Firstname as efname,individual_customers.Firstname as ifname,individual_customers.Lastname as ilname,employees.Lastname as elname,")->from($this->table)
		->join('loan','loan.loan_id = payement_schedules.loan_id','LEFT')
		->join('loan_products', 'loan_products.loan_product_id = loan.loan_product', 'LEFT')
		->join('individual_customers','individual_customers.id = payement_schedules.customer','LEFT')
		->join('employees','employees.id = loan.loan_added_by','LEFT')
		->join('branches','branches.id = loan.branch','LEFT')
		->where('payement_schedules.status','NOT PAID')
			->where('loan.loan_status','ACTIVE');
	if($from !="" && $to !=""){
		$this->db->where('payment_schedule BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');
	}
	if($user !=""){
        $this->db->where('loan.loan_added_by',$user);
    }
	if($product !=""){
        $this->db->where('loan.loan_product',$product);
    }
    if($branch !=""){
        $this->db->where('loan.branch',$branch);
    }
    return	$this->db->get()->result();
}
function  payment_week(){
	date_default_timezone_set("Africa/Blantyre");
	$date_start = strtotime('last Sunday');
	$week_start = date('Y-m-d', $date_start);
	$date_end = strtotime('next Sunday');
	$week_end = date('Y-m-d', $date_end);


	$this->db->select("*,employees.Firstname as efname,individual_customers.Firstname as ifname,individual_customers.Lastname as ilname,employees.Lastname as elname,")->from($this->table)
		->join('loan','loan.loan_id = payement_schedules.loan_id')
		->join('loan_products', 'loan_products.loan_product_id = loan.loan_product', 'LEFT')
		->join('individual_customers','individual_customers.id = payement_schedules.customer','LEFT')
		->join('employees','employees.id = loan.loan_added_by','LEFT');
			$this->db->where('payment_schedule >=', $week_start);
			$this->db->where('payment_schedule <=', $week_end);
			$this->db->where('payement_schedules.status','NOT PAID')
                ->join('branches','branches.id = loan.branch','LEFT')
				->where('loan.loan_status','ACTIVE');

    return	$this->db->get()->result();
}


	function get_filter_projection($from,$to)
	{

		$this->db->select_sum("paid_amount")
			->join('loan','loan.loan_id = payement_schedules.loan_id')
			->where('loan.loan_status','ACTIVE')
			->where('status','PAID');

		$this->db->where('payment_schedule BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

		$this->db->order_by('payement_schedules.loan_id', 'DESC');
		$result = $this->db-> get($this->table)->row();
//		$result = $this->db-> get()->row();
		return array(

			'paid_amount'=>$result->paid_amount
		);
	}
    function next_payment($user, $product, $branch) {
        date_default_timezone_set("Africa/Blantyre");

        $this->db->select("*,employees.Firstname as efname,individual_customers.Firstname as ifname,individual_customers.Lastname as ilname,employees.Lastname as elname,")
            ->from($this->table)
            ->join('loan', 'loan.loan_id = payement_schedules.loan_id')
            ->join('loan_products', 'loan_products.loan_product_id = loan.loan_product', 'LEFT')
            ->join('individual_customers','individual_customers.id = payement_schedules.customer','LEFT')
            ->join('employees','employees.id = loan.loan_added_by','LEFT')
            ->join('branches','branches.id = loan.branch','LEFT')
            ->where('loan.loan_status','ACTIVE')
            ->where('payement_schedules.payment_number = loan.next_payment_id');

        if($user !=""){
            $this->db->where('loan.loan_added_by', $user);
        }
        if($product !=""){
            $this->db->where('loan.loan_product', $product);
        }
        if($branch !=""){
            $this->db->where('loan.branch', $branch);
        }
        return $this->db->get()->result();
    }
	function get_filter_projections($from,$to)
	{

		$this->db->select_sum('amount')
			->join('loan','loan.loan_id = payement_schedules.loan_id')
			->where('loan.loan_status','ACTIVE');


		$this->db->where('payment_schedule BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

		$this->db->order_by('payement_schedules.loan_id', 'DESC');
		$result = $this->db-> get($this->table)->row();
		return array(
			'amount'=>$result->amount,

		);
	}

	function get_filter_projection_principal($from,$to)
	{
		$this->db->select_sum('principal')
			->join('loan','loan.loan_id = payement_schedules.loan_id')
			->where('loan.loan_status','ACTIVE');

		$this->db->where('payment_schedule BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

		$this->db->order_by('payement_schedules.loan_id', 'DESC');
		$result = $this->db-> get($this->table)->row();
		return array(
			'principal'=>$result->principal,

		);
	}

	function get_filter_projection_interest($from,$to)
	{
		$this->db->select_sum('interest')
			->join('loan','loan.loan_id = payement_schedules.loan_id')
			->where('loan.loan_status','ACTIVE');


		$this->db->where('payment_schedule BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

		$this->db->order_by('payement_schedules.loan_id', 'DESC');
		$result = $this->db-> get($this->table)->row();
		return array(
			'interest'=>$result->interest,

		);
	}
}

