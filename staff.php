<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("cache-Control: no-store, no-cache, must-revalidate");
header("cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/* ===================================================

 * Author :Ammar Naji Ahmed.
 * E-mail :amar_ash@yahoo.com
 * Phone  :0176937076
 
 * Copyright : NI Solution ========================================================== */

 class staff extends CI_Controller{
	 
    function __construct(){
		
        parent::__construct();
        $this->check_isvalidated();
		 $this->check_isStaff();
		
    }
    
    public function index($day=NULL){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		$data['name'] = $name;
		$data['role'] = $role;
		
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		
		$result = $this->ndo_model->get_all_pat_d($center_id);
		$data['patient'] = $result['row'];
		
		$datestring = "%Y-%m-%d";
        $time = time();
		$date = mdate($datestring, $time);
		
		if($day == Null){
		$day = date('l', strtotime($date));
		}
		
		$result = $this->ndo_model->get_day_slot_sch($center_id, $day);
		$data['slot'] = $result['row'];
		$data['day'] = $day;
		$data['date'] = $date;

		$result = $this->ndo_model->get_all_pat($center_id);
		$data['pat'] = $result['row'];
		
		$result = $this->ndo_model->get_all_machine($center_id);
		$data['machine'] = $result['row'];
		$result = $this->ndo_model->get_all_shift($center_id);
		$data['shift'] = $result['row'];
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/dashboard', $data);
	  $this->load->view('footer');
	
    }
	
    private function check_isvalidated(){
        if(! $this->session->userdata('validated')){
            redirect('index');
        }
		
    }
	
	  
    private function check_isStaff(){
		$role= $this->session->userdata('role');
        if($role == "clerk" || $role == "nurse" || $role == "medical_assistant"){
            
        }else{redirect('index');}
		
	}
    
    public function logout(){
        $this->session->sess_destroy();
        redirect('index');
    }
	
	
	
	
	//change password 
	
	public function password_page(){
		$staff_id= $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		
		$data['name'] = $name;
		$data['role'] = $role;
		
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/password_page', $data);
	  $this->load->view('footer');
		
    }
	
	public function change_password(){
		$staff_id= $this->session->userdata('staff_id');
		$old_pass = $this->security->xss_clean($this->input->post('old_pass'));
		$new_pass = $this->security->xss_clean($this->input->post('new_pass'));
		$confirm_pass = $this->security->xss_clean($this->input->post('confirm_pass'));
		
		if($old_pass!="" && $new_pass!="" && $confirm_pass!=""){
		$this->load->model('ndo_model');
		$password = $this->ndo_model->get_staff_password($staff_id);
		$old_pass = md5($old_pass);
		if($password == $old_pass){
			if(strlen($new_pass) >= 6){
			if($new_pass == $confirm_pass){
			$new_pass = md5($new_pass);
			 $data = array(
			  'password' => $new_pass,
                 ); 
			$this->ndo_model->change_staff_password($data, $staff_id);
			
			$this->session->set_flashdata('success_msg', 'Password Changed Successfully.');
	redirect('staff/password_page');	
			
			}else{
				
		$this->session->set_flashdata('error_msg', 'The New Password and The Retyped Password Does Not Match. Try again.');
	redirect('staff/password_page');}
			}else{$this->session->set_flashdata('error_msg', 'Password Must Contain Atleast Six characters. Try again.');
	redirect('staff/password_page');}
		}else{
		$this->session->set_flashdata('error_msg', 'The Old Password You Entered is Not Correct. Try again.');
	redirect('staff/password_page');}
		}else{
		$this->session->set_flashdata('error_msg', 'Some Fields were Left Empty. Try again.');
	redirect('staff/password_page');}
		
    }
	
	
	
	
	//.........................................................
	
	 // manage profile
	
	 public function profile(){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		
		$data['name'] = $name;
		$data['role'] = $role;
		
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		
		$data['center_name'] = $this->ndo_model->get_center_name($center_id);
		
		$res = $this->ndo_model->get_all_country();
		$data['country'] = $res['row'];
		
		$res = $this->ndo_model->get_all_state();
		$data['state'] = $res['row'];
		
		$res = $this->ndo_model->get_all_city();
		$data['city'] = $res['row'];
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/profile', $data);
	  $this->load->view('footer');
	
    }
	
	 public function edit_profile(){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		
		$data['name'] = $name;
		$data['role'] = $role;
		
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		$data['center_name'] = $this->ndo_model->get_center_name($center_id);
		
		$res = $this->ndo_model->get_all_country();
		$data['country'] = $res['row'];
		
		$res = $this->ndo_model->get_all_state();
		$data['state'] = $res['row'];
		
		$res = $this->ndo_model->get_all_city();
		$data['city'] = $res['row'];
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/edit_profile', $data);
	  $this->load->view('footer');
	
    }
	
	public function save_edit_profile(){
		$staff_id = $this->session->userdata('staff_id');
		$name = $this->input->post('name');
		$title = $this->input->post('title');
		$status = $this->input->post('status');
		$phone = $this->input->post('phone');
		$email = $this->input->post('email');
		$mobile = $this->input->post('mobile');
		$website = $this->input->post('website');
		$country_id = $this->input->post('country');
		$state_id = $this->input->post('state');
		$city_id = $this->input->post('city');
		$address = $this->input->post('address');
		$comment = $this->input->post('comment');
		$path = './img/staff/'.$staff_id;
       
		
		if($name != '' && $staff_id != '' && $phone !='' && $email !='' && $address !='' && $country_id !='' && $state_id !=''){
			
			
			
			
			$this->form_validation->set_rules('email', 'Email',
			 'trim|required|valid_email|xss_clean');
			
			if($this->form_validation->run() !== false){
			
			if($_FILES['fileInput']['error'] != 4){
				if (!file_exists($path)){
					
					mkdir('./img/staff/'.$staff_id, 0777);
					
				}
			
			 $config['upload_path'] = $path;
			 $config['file_name'] = "profile_pic.jpg";
			 $config['overwrite'] = TRUE;
             $config['allowed_types'] = '*';
             $config['max_size'] = 0;

             $this->upload->initialize($config);


             $this->upload->do_upload('fileInput');
		     $data = $this->upload->data();

             $full_path = $data['full_path'];
	   		 $file_name = $data['file_name'];

			
			$data = array(
			'name' => $name,
			'title' => $title,
			'phone' => $phone,
			'mobile' => $mobile,
			'email' => $email,
			'status' => $status,
			'country_id' => $country_id,
			'state_id' => $state_id,
			'city_id' => $city_id,
			'address' => $address,
			'comment' => $comment,
			'website' => $website,
			'image_name' => $file_name,
			'image_path' => $full_path
				);	
			$this->load->model('ndo_model');
			$result=$this->ndo_model->update_staff_profile($data, $staff_id);

	redirect('staff/profile');
			
				
			}else{
			
			$data = array(
			'name' => $name,
			'title' => $title,
			'phone' => $phone,
			'mobile' => $mobile,
			'email' => $email,
			'status' => $status,
			'country_id' => $country_id,
			'state_id' => $state_id,
			'city_id' => $city_id,
			'address' => $address,
			'comment' => $comment,
			'website' => $website
				);	
			$this->load->model('ndo_model');
			$result=$this->ndo_model->update_staff_profile($data, $staff_id);


	redirect('staff/profile');
				
				
				
				
			}
			}
			else{
		$this->session->set_flashdata('error_msg', 'Invalid E-mail.');
	redirect('staff/edit_profile');
		}       
			
			
		}
		else{
		$this->session->set_flashdata('error_msg', ' Required Fields Must Not Be Left Blank. Try Again');
	redirect('staff/edit_profile');
		}       
		
	}
	
	//...................................................
	
	
	// manage patient
	
	public function sort_patient($type){
	
		$this->session->set_userdata('sort_pat', $type);
		
		
		redirect('staff/pat_list');
	
	}
	
	public function pat_list(){
		
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		
		$data['name'] = $name;
		$data['role'] = $role;
		
		$sort_pat = $this->session->userdata('sort_pat');
		
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		
		$result = $this->ndo_model->get_all_pat($center_id);
		$data['pat'] = $result['row'];
		
		if($sort_pat == "all"){
		
		$this->load->view('staff/header', $data);
		$this->load->view('staff/side_menu', $data);
		$this->load->view('staff/pat_list', $data);
		$this->load->view('footer');
		
		}else{
		
			$datestring = "%Y-%m-%d";
			$time = time();
			$date = mdate($datestring, $time);
			$day = date('l', strtotime($date));
			$result = $this->ndo_model->get_day_slot_sch($center_id, $day);
			$data['slot'] = $result['row'];
			$result = $this->ndo_model->get_all_shift($center_id);
			$data['shift'] = $result['row'];
			
			$this->load->view('staff/header', $data);
			$this->load->view('staff/side_menu', $data);
			$this->load->view('staff/today_pat_list', $data);
			$this->load->view('footer');
			
			
		}
	
    }
    
    public function pat_list_data(){
    
    	$staff_id = $this->session->userdata('staff_id');
    	$this->load->model('ndo_model');
    	$data['staff'] = $this->ndo_model->get_staff($staff_id);
    	foreach($data['staff'] as $row):{
    		$name = $row->name;
    		$role = $row->role;
    	}endforeach;
    
    	$data['name'] = $name;
    	$data['role'] = $role;
    
    	$sort_pat = $this->session->userdata('sort_pat');
    
    	$center_id =  $this->ndo_model->get_center_id($staff_id);
    	$data['center_header'] = $this->ndo_model->get_center($center_id);
    
    	$result = $this->ndo_model->get_all_pat($center_id);
    	$data['pat'] = $result['row'];
    
    
    		$this->load->view('staff/header', $data);
    		$this->load->view('staff/side_menu', $data);
    		$this->load->view('staff/pat_list_data', $data);
    		$this->load->view('footer');
    
    	
    }
    
    public function off_treatment(){
    
    	$staff_id = $this->session->userdata('staff_id');
    	$this->load->model('ndo_model');
    	$data['staff'] = $this->ndo_model->get_staff($staff_id);
    	foreach($data['staff'] as $row):{
    		$name = $row->name;
    		$role = $row->role;
    	}endforeach;
    
    	$data['name'] = $name;
    	$data['role'] = $role;
    
    	$center_id =  $this->ndo_model->get_center_id($staff_id);
    	$data['center_header'] = $this->ndo_model->get_center($center_id);
    
    	$result = $this->ndo_model->get_deleted_pat($center_id);
    	$data['pat'] = $result['row'];
    	
    	$result = $this->ndo_model->get_all_staff_d();
    	$data['staff_all'] = $result['row'];
    
    
    		$this->load->view('staff/header', $data);
    		$this->load->view('staff/side_menu', $data);
    		$this->load->view('staff/off_treatment_pat_list', $data);
    		$this->load->view('footer');
    
    }
	
	 public function patient(){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		
		$data['name'] = $name;
		$data['role'] = $role;
		
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		$result = $this->ndo_model->get_all_running_machine($center_id);
		$data['machine'] = $result['row'];
		$result = $this->ndo_model->get_all_shift($center_id);
		$data['shift'] = $result['row'];
		$result = $this->ndo_model->get_all_slot_sch($center_id);
		$data['slot'] = $result['row'];
		$result = $this->ndo_model->get_all_neph();
		$data['neph'] = $result['row'];
		$result = $this->ndo_model->get_center_superviser_neph($center_id);
		$data['neph_superviser'] = $result['row'];
		$result = $this->ndo_model->get_all_doc();
		$data['doc'] = $result['row'];
		$result = $this->ndo_model->get_center_superviser_doc($center_id);
		$data['doc_superviser'] = $result['row'];
		$result = $this->ndo_model->get_all_pat($center_id);
		$data['pat'] = $result['row'];
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/add_pat', $data);
	  $this->load->view('footer');
	
    }
    
    public function patient_data(){
    	$staff_id = $this->session->userdata('staff_id');
    	$this->load->model('ndo_model');
    	$data['staff'] = $this->ndo_model->get_staff($staff_id);
    	foreach($data['staff'] as $row):{
    		$name = $row->name;
    		$role = $row->role;
    	}endforeach;
    
    	$data['name'] = $name;
    	$data['role'] = $role;
    
    	$center_id =  $this->ndo_model->get_center_id($staff_id);
    	$data['center_header'] = $this->ndo_model->get_center($center_id);
    	$result = $this->ndo_model->get_all_running_machine($center_id);
    	$data['machine'] = $result['row'];
    	$result = $this->ndo_model->get_all_shift($center_id);
    	$data['shift'] = $result['row'];
    	$result = $this->ndo_model->get_all_slot_sch($center_id);
    	$data['slot'] = $result['row'];
    	$result = $this->ndo_model->get_all_neph();
    	$data['neph'] = $result['row'];
    	$result = $this->ndo_model->get_center_superviser_neph($center_id);
    	$data['neph_superviser'] = $result['row'];
    	$result = $this->ndo_model->get_all_doc();
    	$data['doc'] = $result['row'];
    	$result = $this->ndo_model->get_center_superviser_doc($center_id);
    	$data['doc_superviser'] = $result['row'];
    	$result = $this->ndo_model->get_all_pat($center_id);
    	$data['pat'] = $result['row'];
    	$result = $this->ndo_model->get_all_staff_d();
    	$data['staff'] = $result['row'];
    
    	$this->load->view('staff/header', $data);
    	$this->load->view('staff/side_menu', $data);
    	$this->load->view('staff/add_pat_data', $data);
    	$this->load->view('footer');
    
    }
	
	public function add_pat(){
		
		$staff_id = $this->session->userdata('staff_id');
		
		$datestring = "%Y-%m-%d";
        $time = time();
		$current_date = mdate($datestring, $time);
		
		$datestring = "%H:%i:%s";
        $time = time();
		$current_time = mdate($datestring, $time);
		
		$this->load->model('ndo_model');
		$this->load->library('MY_Upload');
		$this->load->library('create_id');
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		
		$pat_id = $this->create_id->get_id();
		$history_id = $this->create_id->get_id();
		$medical_history_id = $this->create_id->get_id();
		
		//personal info
		
		$name = $this->input->post('name');
		$pat_ic = $this->input->post('ic');
		$age = $this->input->post('age');
		$gender = $this->input->post('gender');
		$email = $this->input->post('emails');
		$phoneNo = $this->input->post('phones');
		$mobileNo = $this->input->post('mobiles');
		$country_id = $this->input->post('country');
		$state_id = $this->input->post('state');
		$city_id = $this->input->post('city');
		$address = $this->input->post('addresss');
		$nk_name = $this->input->post('nknames');
		$nk_job = $this->input->post('nkjobs');
		$nk_relation = $this->input->post('relations');
		$nk_email = $this->input->post('nkemails');
		$nk_phoneNo = $this->input->post('nkphones');
		$nk_mobileNO = $this->input->post('nkmobiles');
		$nkcountry_id = $this->input->post('nkcountry');
		$nkstate_id = $this->input->post('nkstate');
		$nkcity_id = $this->input->post('nkcity');
		$nk_address = $this->input->post('nkaddresss');
		
		
		
		if($nk_phoneNo == ""){
		$nk_phoneNo = $phoneNo;	
		}
		if($nk_address == ""){
		$nk_address = $address;	
		}
		
		$age_regex = '/^(19|20|21|22|23|24|25)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';
		
		if (!preg_match($age_regex, $age)) {
			$age = "";
		}
		
		$data = array(
			'pat_id' => $pat_id,
			'history_id' => $history_id
				);
		
		$ret = $this->ndo_model->add_pat($data, $pat_ic);
		
		
		
		if($ret['result']== true){
		
		//.....................
		
		//medical info
		
		$refer_from = $this->input->post('refer_from');
		$refer_date = $this->input->post('refer_date');
		$diag_result = $this->input->post('diag_result');
		$sponsor = $this->input->post('sponsor');
		$path1 = './pat_doc/letter/'.$pat_id;
		$path2 = './pat_doc/pic/'.$pat_id;
		$neph_incharge = $this->input->post('neph_incharge');
		$doc_incharge = $this->input->post('doc_incharge');
		$neph_sec = $this->input->post('neph_sec');
		$doc_sec = $this->input->post('doc_sec');
		$status = "waiting list";
		
		
		
		//......................
		//gses medical problem
		$obstructive_uropathy = $this->input->post('ou');
		$analgesic_nephropathy = $this->input->post('an');
		$prostatic_hyperplasia = $this->input->post('ph');
		$lupus_nephritis = $this->input->post('ln');
		$gses_other = $this->input->post('gses_other');
		
		$problem1 = array(
			'ou' => $obstructive_uropathy,
			'an' => $analgesic_nephropathy,
			'ph' => $prostatic_hyperplasia,
			'ln' => $lupus_nephritis,
			'other' => $gses_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//endo medical problem
		$dm_type2 = $this->input->post('dm');
		$hyperlipidemia = $this->input->post('hl');
		$hypercholesterolemia = $this->input->post('hc');
		$secondary_hyperparathyroidism = $this->input->post('sh');
		$tertiary_hyperparathyroidism = $this->input->post('th');
		$post_hyperparathyroidism = $this->input->post('php');
		$endo_other = $this->input->post('endo_other');
		
		$problem2 = array(
			'dm' => $dm_type2,
			'hl' => $hyperlipidemia,
			'hc' => $hypercholesterolemia,
			'sh' => $secondary_hyperparathyroidism,
			'th' => $tertiary_hyperparathyroidism,
			'ph' => $post_hyperparathyroidism,
			'other' => $endo_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//gastro medical problem
		$upper_gastrointestinal_bleeding = $this->input->post('ugb');
		$ugb_hemorrhoids = $this->input->post('ugbh');
		$liver_disease = $this->input->post('ld');
		$gastro_other = $this->input->post('gastro_other');
		
		$problem3 = array(
			'ugb' => $upper_gastrointestinal_bleeding,
			'ugbh' => $ugb_hemorrhoids,
			'ld' => $liver_disease,
			'other' => $gastro_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//res_system medical problem
		$chronic_obstructive_airway_disease = $this->input->post('coad');
		$bronchial_asthma = $this->input->post('ba');
		$bronchiectasis = $this->input->post('bc');
		$pulmonary_tuberculosis = $this->input->post('pt');
		$pt_completed_treatment = $this->input->post('pt_end_date');
		$pt_date_started = $this->input->post('pt_start_date');
		$res_other = $this->input->post('res_system_other');
		
		$problem4 = array(
			'coad' => $chronic_obstructive_airway_disease,
			'ba' => $bronchial_asthma,
			'b' => $bronchiectasis,
			'pt' => $pulmonary_tuberculosis,
			'pt_completed_date' => $pt_completed_treatment,
			'pt_started_date' => $pt_date_started,
			'other' => $res_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//musc medical problem
		$gouty_arthropathy = $this->input->post('ga');
		$below_knee_amputation = $this->input->post('bka');
		$above_knee_amputation = $this->input->post('aka');
		$rays_amputation = $this->input->post('ra');
		$arthritis = $this->input->post('art');
		$musc_other = $this->input->post('musc_other');
		
		$problem5 = array(
			'ga' => $gouty_arthropathy,
			'bka' => $below_knee_amputation,
			'aka' => $above_knee_amputation,
			'ra' => $rays_amputation,
			'a' => $arthritis,
			'other' => $musc_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//card medical problem
		$ischemic_heart_disease = $this->input->post('ihd');
		$hypertension = $this->input->post('hypertension');
		$myocardial_infarct = $this->input->post('mi');
		$post_cabg = $this->input->post('post_cabg');
		$card_other = $this->input->post('card_other');
		
		$problem6 = array(
			'ihd' => $ischemic_heart_disease,
			'h' => $hypertension,
			'mi' => $myocardial_infarct,
			'pc' => $post_cabg,
			'other' => $card_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//hema medical problem
		$chronic_myeloid_leukemia = $this->input->post('cml');
		$acute_myeloid_leukemia = $this->input->post('aml');
		$lymphoma = $this->input->post('lymphoma');
		$anemia = $this->input->post('anemia');
		$polycythemia = $this->input->post('poly');
		$hema_other = $this->input->post('hema_other');
		
		$problem7 = array(
			'cml' => $chronic_myeloid_leukemia,
			'aml' => $acute_myeloid_leukemia,
			'l' => $lymphoma,
			'a' => $anemia,
			'p' => $polycythemia,
			'other' => $hema_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//neuro medical problem
		$cerebrovascular_accident = $this->input->post('cva');
		$peripheral_neuropathy = $this->input->post('pn');
		$neuro_other = $this->input->post('neuro_other');
		
		$problem8 = array(
			'cva' => $cerebrovascular_accident,
			'pn' => $peripheral_neuropathy,
			'other' => $neuro_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//other medical problem
		$other_problem = $this->input->post('other_problem');
		
		$problem9 = array(
			'other' => $other_problem,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		
		
		//arb blocker medication
		$losartan = $this->input->post('losartan');
		$losartan_hctz = $this->input->post('losartan_hctz');
		$ibersartan = $this->input->post('ibersartan');
		$ibersartan_hctz = $this->input->post('ibersartan_hctz');
		$valsartan = $this->input->post('valsartan');
		$olmesartan = $this->input->post('olmesartan');
		$telmisartan = $this->input->post('telmisartan');
		$arb_other = $this->input->post('arb_other');
		
		$medic1 = array(
			'losartan' => $losartan,
			'losartan_hctz' => $losartan_hctz,
			'ibersartan' => $ibersartan,
			'ibersartan_hctz' => $ibersartan_hctz,
			'valsartan' => $valsartan,
			'olmesartan' => $olmesartan,
			'telmisartan' => $telmisartan,
			'other' => $arb_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//beta blocker medication
		$atenolol = $this->input->post('atenolol');
		$metaprolol = $this->input->post('metaprolol');
		$bisoprolol = $this->input->post('bisoprolol');
		$carvidelol = $this->input->post('carvidelol');
		$beta_other = $this->input->post('beta_other');
		
		$medic2 = array(
			'atenolol' => $atenolol,
			'metaprolol' => $metaprolol,
			'bisoprolol' => $bisoprolol,
			'carvidelol' => $carvidelol,
			'other' => $beta_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//ace inhibitors medication
		$enalapril = $this->input->post('enalapril');
		$captopril = $this->input->post('captopril');
		$ace_other = $this->input->post('ace_other');
		
		$medic3 = array(
			'enalapril' => $enalapril,
			'captopril' => $captopril,
			'other' => $ace_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//ccb blocker medication
		$amlodipine = $this->input->post('amlodipine');
		$felodipine = $this->input->post('felodipine');
		$ccb_other = $this->input->post('ccb_other');
		
		$medic4 = array(
			'amlodipine' => $amlodipine,
			'felodipine' => $felodipine,
			'other' => $ccb_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//activated vitamin D medication
		$calcitriol = $this->input->post('calcitriol');
		$calcidol = $this->input->post('calcidol');
		$vitamin_other = $this->input->post('vitamin_other');
		
		$medic5 = array(
			'calcitriol' => $calcitriol,
			'calcidol' => $calcidol,
			'other' => $vitamin_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//hematinics medication
		$ferrous_fumarate = $this->input->post('ferrous_fumarate');
		$b_complex = $this->input->post('b_complex');
		$folate = $this->input->post('folate');
		$hematinics_other = $this->input->post('hematinics_other');
		
		$medic6 = array(
			'ferrous_fumarate' => $ferrous_fumarate,
			'b_complex' => $b_complex,
			'folate' => $folate,
			'other' => $hematinics_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//aab blocker medication
		$prazosin = $this->input->post('prazosin');
		$aab_other = $this->input->post('aab_other');
		
		$medic7 = array(
			'prazosin' => $prazosin,
			'other' => $aab_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//vasodilators medication
		$isordil = $this->input->post('isordil');
		$minoxidil = $this->input->post('minoxidil');
		$vasodilators_other = $this->input->post('vasodilators_other');
		
		$medic8 = array(
			'isordil' => $isordil,
			'minoxidil' => $minoxidil,
			'other' => $vasodilators_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//phosphate binders medication
		$calcium_carbonate = $this->input->post('calcium_carbonate');
		$phosphate_other = $this->input->post('phosphate_other');
		
		$medic9 = array(
			'calcium_carbonate' => $calcium_carbonate,
			'other' => $phosphate_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//diuretics medication
		$frusemide = $this->input->post('frusemide');
		$diuretics_other = $this->input->post('diuretics_other');
		
		$medic10 = array(
			'frusemide' => $frusemide,
			'other' => $diuretics_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		
		//erythorpoetin medication
		$eprex = $this->input->post('eprex');
		$recormon = $this->input->post('recormon');
		$binocrit = $this->input->post('binocrit');
		$mircela = $this->input->post('mircela');
		$ery_other = $this->input->post('ery_other');
		
		$medic11 = array(
			'eprex' => $eprex,
			'recormon' => $recormon,
			'binocrit' => $binocrit,
			'mircela' => $mircela,
			'other' => $ery_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		
		//other medication
		$other_medic = $this->input->post('other_medic');
		
		$medic12 = array(
			'other' => $other_medic,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		
		
		if (!file_exists($path2)){
					mkdir($path2, 0777);
				}
				
				if($_FILES['profile_pic']['error'] != 4){
			
			 $config['upload_path'] = $path2;
			 $config['file_name'] = "profile_pic.jpg";
             $config['allowed_types'] = '*';
             $config['max_size'] = 0;

             $this->upload->initialize($config);


             $this->upload->do_upload('profile_pic');
		     $data = $this->upload->data();

             $pic_full_path = $data['full_path'];
	   		 $pic_file_name = $data['file_name'];

			}else{
			$pic_full_path = "";
			$pic_file_name = "";	
			}
			
			$data = array(
			'pic_path' => $pic_full_path,
			'pic_name' => $pic_file_name
				);
		
		   $this->ndo_model->update_pat_info($data, $pat_id);
		
			//history track
			$data = array(
			'history_id' => $history_id,
			'pat_ic' => $pat_ic,
			'pat_id' => $pat_id,
			'name' => $name,
			'age' => $age,
			'gender' => $gender,
			'email' => $email,
			'phoneNo' => $phoneNo,
			'mobileNo' => $mobileNo,
			'country_id' => $country_id,
			'state_id' => $state_id,
			'city_id' => $city_id,
			'address' => $address,
			'nk_name' => $nk_name,
			'nk_job' => $nk_job,
			'nk_relation' => $nk_relation,
			'nk_email' => $nk_email,
			'nk_phoneNo' => $nk_phoneNo,
			'nk_mobileNo' => $nk_mobileNO,
			'nkcountry_id' => $nkcountry_id,
			'nkstate_id' => $nkstate_id,
			'nkcity_id' => $nkcity_id,
			'nk_address' => $nk_address,
			'refer_from' => $refer_from,
			'refer_date' => $refer_date,
			'diag_result' => $diag_result,
			'sponsor' => $sponsor,
			'status' => $status,
			'deleted' => -1,
			'center_id' => $center_id,
			'neph_id' => $neph_incharge,
			'doc_id' => $doc_incharge,
			'action' => "created",
			'action_by' => $staff_id,
			'action_date' => date('Y-m-d H:i:s'),
			'medical_history_id' => $medical_history_id
				);
				
			$this->ndo_model->add_pat_history($data);
			
			if (is_array($neph_sec)){
			foreach ($neph_sec as $sec_neph):{
			if($sec_neph != $neph_incharge){
       		 $data = array(
						'neph_id' => $sec_neph,
						'pat_id' => $pat_id
								);
       		 $this->ndo_model->add_sec_neph($data);
			 }
			}
    		 endforeach;
			}
		
			if (is_array($doc_sec)){
    		 foreach ($doc_sec as $sec_doc):{
    		 if($sec_doc != $doc_incharge){
    		 $data = array(
    		 		'doc_id' => $sec_doc,
    		 		'pat_id' => $pat_id
    		 );
    		 $this->ndo_model->add_sec_doc($data);
    		 }
    		 }
    		 endforeach;
			}
		
			
			if($sponsor != "Not Sponsored"){
				
				$result = $this->ndo_model->get_all_running_machine($center_id);
				$data['machine'] = $result['row'];
			$i=1;
			foreach($data['machine'] as $mrow):
				$result = $this->ndo_model->get_all_shift($center_id);
				$data['shift'] = $result['row'];
				$j=1;
				foreach($data['shift'] as $srow):
					$slot_id = $this->input->post('s'.$j.'s'.$i.'sun');
					if($slot_id != ""){
					$data = array(
						'slot_id' => $slot_id,
						'shift_id' => $srow->shift_id,
						'slot' => "slot".$i,
						'day' => "Sunday",
						'pat_id' => $pat_id,
						'center_id' => $center_id
								);	
					$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
					$status = "on treatment";
					}
				 $j++; endforeach; 
				 $i++; endforeach;  
				 
				 $result = $this->ndo_model->get_all_running_machine($center_id);
				 $data['machine'] = $result['row'];
				 $i=1;
				 foreach($data['machine'] as $mrow):
				 $result = $this->ndo_model->get_all_shift($center_id);
				 $data['shift'] = $result['row'];
				 $j=1;
				 foreach($data['shift'] as $srow):
				 $slot_id = $this->input->post('s'.$j.'s'.$i.'mon');
				 if($slot_id != ""){
				 	$data = array(
				 			'slot_id' => $slot_id,
				 			'shift_id' => $srow->shift_id,
				 			'slot' => "slot".$i,
				 			'day' => "Monday",
				 			'pat_id' => $pat_id,
				 			'center_id' => $center_id
				 	);
				 	$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
				 	$status = "on treatment";
				 }
				 $j++; endforeach;
				 $i++; endforeach;
				 
				 $result = $this->ndo_model->get_all_running_machine($center_id);
				 $data['machine'] = $result['row'];
				 $i=1;
				 foreach($data['machine'] as $mrow):
				 $result = $this->ndo_model->get_all_shift($center_id);
				 $data['shift'] = $result['row'];
				 $j=1;
				 foreach($data['shift'] as $srow):
				 $slot_id = $this->input->post('s'.$j.'s'.$i.'tue');
				 if($slot_id != ""){
				 	$data = array(
				 			'slot_id' => $slot_id,
				 			'shift_id' => $srow->shift_id,
				 			'slot' => "slot".$i,
				 			'day' => "Tuesday",
				 			'pat_id' => $pat_id,
				 			'center_id' => $center_id
				 	);
				 	$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
				 	$status = "on treatment";
				 }
				 $j++; endforeach;
				 $i++; endforeach;
				 
				 $result = $this->ndo_model->get_all_running_machine($center_id);
				 $data['machine'] = $result['row'];
				 $i=1;
				 foreach($data['machine'] as $mrow):
				 $result = $this->ndo_model->get_all_shift($center_id);
				 $data['shift'] = $result['row'];
				 $j=1;
				 foreach($data['shift'] as $srow):
				 $slot_id = $this->input->post('s'.$j.'s'.$i.'wed');
				 if($slot_id != ""){
				 	$data = array(
				 			'slot_id' => $slot_id,
				 			'shift_id' => $srow->shift_id,
				 			'slot' => "slot".$i,
				 			'day' => "Wednesday",
				 			'pat_id' => $pat_id,
				 			'center_id' => $center_id
				 	);
				 	$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
				 	$status = "on treatment";
				 }
				 $j++; endforeach;
				 $i++; endforeach;
				 
				 $result = $this->ndo_model->get_all_running_machine($center_id);
				 $data['machine'] = $result['row'];
				 $i=1;
				 foreach($data['machine'] as $mrow):
				 $result = $this->ndo_model->get_all_shift($center_id);
				 $data['shift'] = $result['row'];
				 $j=1;
				 foreach($data['shift'] as $srow):
				 $slot_id = $this->input->post('s'.$j.'s'.$i.'thu');
				 if($slot_id != ""){
				 	$data = array(
				 			'slot_id' => $slot_id,
				 			'shift_id' => $srow->shift_id,
				 			'slot' => "slot".$i,
				 			'day' => "Thursday",
				 			'pat_id' => $pat_id,
				 			'center_id' => $center_id
				 	);
				 	$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
				 	$status = "on treatment";
				 }
				 $j++; endforeach;
				 $i++; endforeach;
				 
				 $result = $this->ndo_model->get_all_running_machine($center_id);
				 $data['machine'] = $result['row'];
				 $i=1;
				 foreach($data['machine'] as $mrow):
				 $result = $this->ndo_model->get_all_shift($center_id);
				 $data['shift'] = $result['row'];
				 $j=1;
				 foreach($data['shift'] as $srow):
				 $slot_id = $this->input->post('s'.$j.'s'.$i.'fri');
				 if($slot_id != ""){
				 	$data = array(
				 			'slot_id' => $slot_id,
				 			'shift_id' => $srow->shift_id,
				 			'slot' => "slot".$i,
				 			'day' => "Friday",
				 			'pat_id' => $pat_id,
				 			'center_id' => $center_id
				 	);
				 	$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
				 	$status = "on treatment";
				 }
				 $j++; endforeach;
				 $i++; endforeach;
				 
				 $result = $this->ndo_model->get_all_running_machine($center_id);
				 $data['machine'] = $result['row'];
				 $i=1;
				 foreach($data['machine'] as $mrow):
				 $result = $this->ndo_model->get_all_shift($center_id);
				 $data['shift'] = $result['row'];
				 $j=1;
				 foreach($data['shift'] as $srow):
				 $slot_id = $this->input->post('s'.$j.'s'.$i.'sat');
				 if($slot_id != ""){
				 	$data = array(
				 			'slot_id' => $slot_id,
				 			'shift_id' => $srow->shift_id,
				 			'slot' => "slot".$i,
				 			'day' => "Saturday",
				 			'pat_id' => $pat_id,
				 			'center_id' => $center_id
				 	);
				 	$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
				 	$status = "on treatment";
				 }
				 $j++; endforeach;
				 $i++; endforeach;
				
				
		
			}
			$data = array(
						'status' => $status
								);
			$this->ndo_model->update_pat_history($data, $history_id);
			
			
			if (!file_exists($path1)){
					mkdir($path1, 0777);
				}
				
			$config['upload_path'] = $path1;
			$config['allowed_types'] = '*';
			$config['max_size'] = 0;
			$config['overwrite'] = FALSE;
				
			$this->upload->initialize($config);
				
				
			if($this->upload->do_multi_upload('refer_letter')){
				$file = $this->upload->get_multi_upload_data();
						
				foreach($file as $array):{
					$data = array(
							'type' => "ref",
							'upload_date' => date('Y-m-d H:i:s'),
							'uploaded_by' => $staff_id,
							'path' => $array['full_path'],
							'name' => $array['file_name'],
							'pat_id' => $pat_id
							
									
					);
					$this->ndo_model->add_letter($data);
						
				}endforeach;
						
			}
			 
			$this->upload->initialize($config);
			 
			 
			if($this->upload->do_multi_upload('diag_letter')){
				$file = $this->upload->get_multi_upload_data();
			
				foreach($file as $array):{
					$data = array(
							'type' => "diag",
							'upload_date' => date('Y-m-d H:i:s'),
							'uploaded_by' => $staff_id,
							'path' => $array['full_path'],
							'name' => $array['file_name'],
							'pat_id' => $pat_id
			
					);
					$this->ndo_model->add_letter($data);
			
				}endforeach;
			
			}
			
			$this->upload->initialize($config);
			
			
			if($this->upload->do_multi_upload('sponsor_letter')){
				$file = $this->upload->get_multi_upload_data();
					
				foreach($file as $array):{
					$data = array(
							'type' => "spon",
							'upload_date' => date('Y-m-d H:i:s'),
							'uploaded_by' => $staff_id,
							'path' => $array['full_path'],
							'name' => $array['file_name'],
							'pat_id' => $pat_id
								
					);
					$this->ndo_model->add_letter($data);
						
				}endforeach;
					
			}
			
			if($_FILES['img1']['error'] != 4){
			
			 $config['upload_path'] = $path2;
			 $config['file_name'] = "img1.jpg";
             $config['allowed_types'] = '*';
             $config['max_size'] = 0;

             $this->upload->initialize($config);


             $this->upload->do_upload('img1');
		     $data = $this->upload->data();

             $img1_path = $data['full_path'];
	   		 $img1_file_name = $data['file_name'];
			 
			 $img1_date = $this->input->post('img1_date');
			 $img1_comment = $this->input->post('img1_comment');
			 $data = array(
			'img_name' => $img1_file_name,
			'img_path' => $img1_path,
			'img_date' => $img1_date,
			'img_comment' => $img1_comment,
			'pat_id' => $pat_id
			
				);
			$this->ndo_model->add_medical_img($data);
			 

			}
			if($_FILES['img2']['error'] != 4){
			
			 $config['upload_path'] = $path2;
			 $config['file_name'] = "img2.jpg";
             $config['allowed_types'] = '*';
             $config['max_size'] = 0;

             $this->upload->initialize($config);


             $this->upload->do_upload('img2');
		     $data = $this->upload->data();

             $img2_path = $data['full_path'];
	   		 $img2_file_name = $data['file_name'];
			 
			 $img2_date = $this->input->post('img2_date');
			 $img2_comment = $this->input->post('img2_comment');
			 $data = array(
			'img_name' => $img2_file_name,
			'img_path' => $img2_path,
			'img_date' => $img2_date,
			'img_comment' => $img2_comment,
			'pat_id' => $pat_id
			
				);
			$this->ndo_model->add_medical_img($data);
			 

			}
			if($_FILES['img3']['error'] != 4){
			
			 $config['upload_path'] = $path2;
			 $config['file_name'] = "img3.jpg";
             $config['allowed_types'] = '*';
             $config['max_size'] = 0;

             $this->upload->initialize($config);


             $this->upload->do_upload('img3');
		     $data = $this->upload->data();

             $img3_path = $data['full_path'];
	   		 $img3_file_name = $data['file_name'];
			 
			 $img3_date = $this->input->post('img3_date');
			 $img3_comment = $this->input->post('img3_comment');
			 $data = array(
			'img_name' => $img3_file_name,
			'img_path' => $img3_path,
			'img_date' => $img3_date,
			'img_comment' => $img3_comment,
			'pat_id' => $pat_id
			
				);
			$this->ndo_model->add_medical_img($data);
			 

			}
			
			$data = array(
			'pat_id' => $pat_id,
			'center_id' => $center_id
				);
			$this->ndo_model->add_clinical_summary($data);
			
			
			$this->ndo_model->add_pat_med_problem($problem1, $problem2, $problem3, $problem4, $problem5, $problem6, $problem7, $problem8, $problem9);
			
			
			$this->ndo_model->add_pat_medication($medic1, $medic2, $medic3, $medic4, $medic5, $medic6, $medic7, $medic8, $medic9, $medic10, $medic11, $medic12);
			
			$this->session->set_flashdata('success_msg', ' Patient Is Added Successfully.');
	redirect('staff/pat_list');
			
		}else{
			$this->session->set_flashdata('error_msg', ' Patient already exists in System Database.');
	redirect('staff/pat_list');
			
			
		}
				
    }
    
    public function add_pat_data(){
    
    	$staff_id = $this->session->userdata('staff_id');
    
    	$datestring = "%Y-%m-%d";
    	$time = time();
    	$current_date = mdate($datestring, $time);
    
    	$datestring = "%H:%i:%s";
    	$time = time();
    	$current_time = mdate($datestring, $time);
    
    	$this->load->model('ndo_model');
    	$this->load->library('MY_Upload');
    	$this->load->library('create_id');
    	$center_id =  $this->ndo_model->get_center_id($staff_id);
    
    	$pat_id = $this->create_id->get_id();
    	$history_id = $this->create_id->get_id();
    	$medical_history_id = $this->create_id->get_id();
    
    	//personal info
    	$staff_who_change = $this->input->post('staff_who_change');
    	$creation_date = $this->input->post('creation_date');
    	$creation_time = $this->input->post('creation_time');
    	
    	$time_stamp = $creation_date." ".$creation_time;
    
    	$name = $this->input->post('name');
    	$pat_ic = $this->input->post('ic');
    	$age = $this->input->post('ages');
    	$gender = $this->input->post('gender');
    	$email = $this->input->post('emails');
    	$phoneNo = $this->input->post('phones');
    	$mobileNo = $this->input->post('mobiles');
    	$address = $this->input->post('addresss');
    	$nk_name = $this->input->post('nknames');
    	$nk_job = $this->input->post('nkjobs');
    	$nk_relation = $this->input->post('relations');
    	$nk_email = $this->input->post('nkemails');
    	$nk_phoneNo = $this->input->post('nkphones');
    	$nk_mobileNO = $this->input->post('nkmobiles');
    	$nk_address = $this->input->post('nkaddresss');
    
    
    
    	if($nk_phoneNo == ""){
    		$nk_phoneNo = $phoneNo;
    	}
    	if($nk_address == ""){
    		$nk_address = $address;
    	}
    
    	$age_regex = '/^(19|20|21|22|23|24|25)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';
    
    	if (!preg_match($age_regex, $age)) {
    		$age = "";
    	}
    
    	$data = array(
    			'pat_id' => $pat_id,
    			'history_id' => $history_id
    	);
    
    	$ret = $this->ndo_model->add_pat($data, $pat_ic);
    
    
    
    	if($ret['result']== true){
    
    		//.....................
    
    		//medical info
    
    		$refer_from = $this->input->post('refer_from');
    		$refer_date = $this->input->post('refer_date');
    		$diag_result = $this->input->post('diag_result');
    		$sponsor = $this->input->post('sponsor');
    		$path1 = './pat_doc/letter/'.$pat_id;
    		$path2 = './pat_doc/pic/'.$pat_id;
    		$neph_incharge = $this->input->post('neph_incharge');
    		$doc_incharge = $this->input->post('doc_incharge');
    		$status = "waiting list";
    
    
    
    		//......................
    		//gses medical problem
    		$obstructive_uropathy = $this->input->post('ou');
    		$analgesic_nephropathy = $this->input->post('an');
    		$prostatic_hyperplasia = $this->input->post('ph');
    		$lupus_nephritis = $this->input->post('ln');
    		$gses_other = $this->input->post('gses_other');
    
    		$problem1 = array(
    				'ou' => $obstructive_uropathy,
    				'an' => $analgesic_nephropathy,
    				'ph' => $prostatic_hyperplasia,
    				'ln' => $lupus_nephritis,
    				'other' => $gses_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//endo medical problem
    		$dm_type2 = $this->input->post('dm');
    		$hyperlipidemia = $this->input->post('hl');
    		$hypercholesterolemia = $this->input->post('hc');
    		$secondary_hyperparathyroidism = $this->input->post('sh');
    		$tertiary_hyperparathyroidism = $this->input->post('th');
    		$post_hyperparathyroidism = $this->input->post('php');
    		$endo_other = $this->input->post('endo_other');
    
    		$problem2 = array(
    				'dm' => $dm_type2,
    				'hl' => $hyperlipidemia,
    				'hc' => $hypercholesterolemia,
    				'sh' => $secondary_hyperparathyroidism,
    				'th' => $tertiary_hyperparathyroidism,
    				'ph' => $post_hyperparathyroidism,
    				'other' => $endo_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//gastro medical problem
    		$upper_gastrointestinal_bleeding = $this->input->post('ugb');
    		$ugb_hemorrhoids = $this->input->post('ugbh');
    		$liver_disease = $this->input->post('ld');
    		$gastro_other = $this->input->post('gastro_other');
    
    		$problem3 = array(
    				'ugb' => $upper_gastrointestinal_bleeding,
    				'ugbh' => $ugb_hemorrhoids,
    				'ld' => $liver_disease,
    				'other' => $gastro_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//res_system medical problem
    		$chronic_obstructive_airway_disease = $this->input->post('coad');
    		$bronchial_asthma = $this->input->post('ba');
    		$bronchiectasis = $this->input->post('bc');
    		$pulmonary_tuberculosis = $this->input->post('pt');
    		$pt_completed_treatment = $this->input->post('pt_end_date');
    		$pt_date_started = $this->input->post('pt_start_date');
    		$res_other = $this->input->post('res_system_other');
    
    		$problem4 = array(
    				'coad' => $chronic_obstructive_airway_disease,
    				'ba' => $bronchial_asthma,
    				'b' => $bronchiectasis,
    				'pt' => $pulmonary_tuberculosis,
    				'pt_completed_date' => $pt_completed_treatment,
    				'pt_started_date' => $pt_date_started,
    				'other' => $res_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//musc medical problem
    		$gouty_arthropathy = $this->input->post('ga');
    		$below_knee_amputation = $this->input->post('bka');
    		$above_knee_amputation = $this->input->post('aka');
    		$rays_amputation = $this->input->post('ra');
    		$arthritis = $this->input->post('art');
    		$musc_other = $this->input->post('musc_other');
    
    		$problem5 = array(
    				'ga' => $gouty_arthropathy,
    				'bka' => $below_knee_amputation,
    				'aka' => $above_knee_amputation,
    				'ra' => $rays_amputation,
    				'a' => $arthritis,
    				'other' => $musc_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//card medical problem
    		$ischemic_heart_disease = $this->input->post('ihd');
    		$hypertension = $this->input->post('hypertension');
    		$myocardial_infarct = $this->input->post('mi');
    		$post_cabg = $this->input->post('post_cabg');
    		$card_other = $this->input->post('card_other');
    
    		$problem6 = array(
    				'ihd' => $ischemic_heart_disease,
    				'h' => $hypertension,
    				'mi' => $myocardial_infarct,
    				'pc' => $post_cabg,
    				'other' => $card_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//hema medical problem
    		$chronic_myeloid_leukemia = $this->input->post('cml');
    		$acute_myeloid_leukemia = $this->input->post('aml');
    		$lymphoma = $this->input->post('lymphoma');
    		$anemia = $this->input->post('anemia');
    		$polycythemia = $this->input->post('poly');
    		$hema_other = $this->input->post('hema_other');
    
    		$problem7 = array(
    				'cml' => $chronic_myeloid_leukemia,
    				'aml' => $acute_myeloid_leukemia,
    				'l' => $lymphoma,
    				'a' => $anemia,
    				'p' => $polycythemia,
    				'other' => $hema_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//neuro medical problem
    		$cerebrovascular_accident = $this->input->post('cva');
    		$peripheral_neuropathy = $this->input->post('pn');
    		$neuro_other = $this->input->post('neuro_other');
    
    		$problem8 = array(
    				'cva' => $cerebrovascular_accident,
    				'pn' => $peripheral_neuropathy,
    				'other' => $neuro_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//other medical problem
    		$other_problem = $this->input->post('other_problem');
    
    		$problem9 = array(
    				'other' => $other_problem,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    
    		//medical problem summary
    		$problem_summary = $this->input->post('sum_problem');
    		$examination_summary = $this->input->post('sum_examination');
    
    		$summary = array(
    				'problem_summary' => $problem_summary,
    				'examination_summary' => $examination_summary,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    
    		//arb blocker medication
    		$losartan = $this->input->post('losartan');
    		$losartan_hctz = $this->input->post('losartan_hctz');
    		$ibersartan = $this->input->post('ibersartan');
    		$ibersartan_hctz = $this->input->post('ibersartan_hctz');
    		$valsartan = $this->input->post('valsartan');
    		$olmesartan = $this->input->post('olmesartan');
    		$telmisartan = $this->input->post('telmisartan');
    		$arb_other = $this->input->post('arb_other');
    
    		$medic1 = array(
    				'losartan' => $losartan,
    				'losartan_hctz' => $losartan_hctz,
    				'ibersartan' => $ibersartan,
    				'ibersartan_hctz' => $ibersartan_hctz,
    				'valsartan' => $valsartan,
    				'olmesartan' => $olmesartan,
    				'telmisartan' => $telmisartan,
    				'other' => $arb_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//beta blocker medication
    		$atenolol = $this->input->post('atenolol');
    		$metaprolol = $this->input->post('metaprolol');
    		$bisoprolol = $this->input->post('bisoprolol');
    		$carvidelol = $this->input->post('carvidelol');
    		$beta_other = $this->input->post('beta_other');
    
    		$medic2 = array(
    				'atenolol' => $atenolol,
    				'metaprolol' => $metaprolol,
    				'bisoprolol' => $bisoprolol,
    				'carvidelol' => $carvidelol,
    				'other' => $beta_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//ace inhibitors medication
    		$enalapril = $this->input->post('enalapril');
    		$captopril = $this->input->post('captopril');
    		$ace_other = $this->input->post('ace_other');
    
    		$medic3 = array(
    				'enalapril' => $enalapril,
    				'captopril' => $captopril,
    				'other' => $ace_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//ccb blocker medication
    		$amlodipine = $this->input->post('amlodipine');
    		$felodipine = $this->input->post('felodipine');
    		$ccb_other = $this->input->post('ccb_other');
    
    		$medic4 = array(
    				'amlodipine' => $amlodipine,
    				'felodipine' => $felodipine,
    				'other' => $ccb_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//activated vitamin D medication
    		$calcitriol = $this->input->post('calcitriol');
    		$calcidol = $this->input->post('calcidol');
    		$vitamin_other = $this->input->post('vitamin_other');
    
    		$medic5 = array(
    				'calcitriol' => $calcitriol,
    				'calcidol' => $calcidol,
    				'other' => $vitamin_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//hematinics medication
    		$ferrous_fumarate = $this->input->post('ferrous_fumarate');
    		$b_complex = $this->input->post('b_complex');
    		$folate = $this->input->post('folate');
    		$hematinics_other = $this->input->post('hematinics_other');
    
    		$medic6 = array(
    				'ferrous_fumarate' => $ferrous_fumarate,
    				'b_complex' => $b_complex,
    				'folate' => $folate,
    				'other' => $hematinics_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//aab blocker medication
    		$prazosin = $this->input->post('prazosin');
    		$aab_other = $this->input->post('aab_other');
    
    		$medic7 = array(
    				'prazosin' => $prazosin,
    				'other' => $aab_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//vasodilators medication
    		$isordil = $this->input->post('isordil');
    		$minoxidil = $this->input->post('minoxidil');
    		$vasodilators_other = $this->input->post('vasodilators_other');
    
    		$medic8 = array(
    				'isordil' => $isordil,
    				'minoxidil' => $minoxidil,
    				'other' => $vasodilators_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//phosphate binders medication
    		$calcium_carbonate = $this->input->post('calcium_carbonate');
    		$phosphate_other = $this->input->post('phosphate_other');
    
    		$medic9 = array(
    				'calcium_carbonate' => $calcium_carbonate,
    				'other' => $phosphate_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//diuretics medication
    		$frusemide = $this->input->post('frusemide');
    		$diuretics_other = $this->input->post('diuretics_other');
    
    		$medic10 = array(
    				'frusemide' => $frusemide,
    				'other' => $diuretics_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    
    		//erythorpoetin medication
    		$eprex = $this->input->post('eprex');
    		$recormon = $this->input->post('recormon');
    		$binocrit = $this->input->post('binocrit');
    		$mircela = $this->input->post('mircela');
    		$ery_other = $this->input->post('ery_other');
    
    		$medic11 = array(
    				'eprex' => $eprex,
    				'recormon' => $recormon,
    				'binocrit' => $binocrit,
    				'mircela' => $mircela,
    				'other' => $ery_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    
    		//other medication
    		$other_medic = $this->input->post('other_medic');
    
    		$medic12 = array(
    				'other' => $other_medic,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    
    		//plan
    		$clinical_plan = $this->input->post('clinical_plan');
    
    		$plan = array(
    				'clinical_plan' => $clinical_plan,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    
    		if (!file_exists($path2)){
    			mkdir($path2, 0777);
    		}
    
    		if($_FILES['profile_pic']['error'] != 4){
    				
    			$config['upload_path'] = $path2;
    			$config['file_name'] = "profile_pic.jpg";
    			$config['allowed_types'] = '*';
    			$config['max_size'] = 0;
    
    			$this->upload->initialize($config);
    
    
    			$this->upload->do_upload('profile_pic');
    			$data = $this->upload->data();
    
    			$pic_full_path = $data['full_path'];
    			$pic_file_name = $data['file_name'];
    
    		}else{
    			$pic_full_path = "";
    			$pic_file_name = "";
    		}
    			
    		$data = array(
    				'pic_path' => $pic_full_path,
    				'pic_name' => $pic_file_name
    		);
    
    		$this->ndo_model->update_pat_info($data, $pat_id);
    
    		//history track
    		$data = array(
    				'history_id' => $history_id,
    				'pat_ic' => $pat_ic,
    				'pat_id' => $pat_id,
    				'name' => $name,
    				'age' => $age,
    				'gender' => $gender,
    				'email' => $email,
    				'phoneNo' => $phoneNo,
    				'mobileNo' => $mobileNo,
    				'address' => $address,
    				'nk_name' => $nk_name,
    				'nk_job' => $nk_job,
    				'nk_relation' => $nk_relation,
    				'nk_email' => $nk_email,
    				'nk_phoneNo' => $nk_phoneNo,
    				'nk_mobileNo' => $nk_mobileNO,
    				'nk_address' => $nk_address,
    				'refer_from' => $refer_from,
    				'refer_date' => $refer_date,
    				'diag_result' => $diag_result,
    				'sponsor' => $sponsor,
    				'status' => $status,
    				'deleted' => -1,
    				'center_id' => $center_id,
    				'neph_id' => $neph_incharge,
    				'doc_id' => $doc_incharge,
    				'action' => "created",
    				'action_by' => $staff_who_change,
    				'action_date' => $time_stamp,
    				'medical_history_id' => $medical_history_id
    		);
    
    		$this->ndo_model->add_pat_history($data);
    			
    			
    		if($sponsor != "Not Sponsored" && $diag_result != "infection"){
    
    			$result = $this->ndo_model->get_all_running_machine($center_id);
    			$data['machine'] = $result['row'];
    			$i=1;
    			foreach($data['machine'] as $mrow):
    			$result = $this->ndo_model->get_all_shift($center_id);
    			$data['shift'] = $result['row'];
    			$j=1;
    			foreach($data['shift'] as $srow):
    			$slot_id = $this->input->post('s'.$j.'s'.$i.'sun');
    			if($slot_id != ""){
    				$data = array(
    						'slot_id' => $slot_id,
    						'shift_id' => $srow->shift_id,
    						'slot' => "slot".$i,
    						'day' => "Sunday",
    						'pat_id' => $pat_id,
    						'center_id' => $center_id
    				);
    				$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
    				$status = "on treatment";
    			}
    			$j++; endforeach;
    			$i++; endforeach;
    				
    			$result = $this->ndo_model->get_all_running_machine($center_id);
    			$data['machine'] = $result['row'];
    			$i=1;
    			foreach($data['machine'] as $mrow):
    			$result = $this->ndo_model->get_all_shift($center_id);
    			$data['shift'] = $result['row'];
    			$j=1;
    			foreach($data['shift'] as $srow):
    			$slot_id = $this->input->post('s'.$j.'s'.$i.'mon');
    			if($slot_id != ""){
    				$data = array(
    						'slot_id' => $slot_id,
    						'shift_id' => $srow->shift_id,
    						'slot' => "slot".$i,
    						'day' => "Monday",
    						'pat_id' => $pat_id,
    						'center_id' => $center_id
    				);
    				$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
    				$status = "on treatment";
    			}
    			$j++; endforeach;
    			$i++; endforeach;
    				
    			$result = $this->ndo_model->get_all_running_machine($center_id);
    			$data['machine'] = $result['row'];
    			$i=1;
    			foreach($data['machine'] as $mrow):
    			$result = $this->ndo_model->get_all_shift($center_id);
    			$data['shift'] = $result['row'];
    			$j=1;
    			foreach($data['shift'] as $srow):
    			$slot_id = $this->input->post('s'.$j.'s'.$i.'tue');
    			if($slot_id != ""){
    				$data = array(
    						'slot_id' => $slot_id,
    						'shift_id' => $srow->shift_id,
    						'slot' => "slot".$i,
    						'day' => "Tuesday",
    						'pat_id' => $pat_id,
    						'center_id' => $center_id
    				);
    				$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
    				$status = "on treatment";
    			}
    			$j++; endforeach;
    			$i++; endforeach;
    				
    			$result = $this->ndo_model->get_all_running_machine($center_id);
    			$data['machine'] = $result['row'];
    			$i=1;
    			foreach($data['machine'] as $mrow):
    			$result = $this->ndo_model->get_all_shift($center_id);
    			$data['shift'] = $result['row'];
    			$j=1;
    			foreach($data['shift'] as $srow):
    			$slot_id = $this->input->post('s'.$j.'s'.$i.'wed');
    			if($slot_id != ""){
    				$data = array(
    						'slot_id' => $slot_id,
    						'shift_id' => $srow->shift_id,
    						'slot' => "slot".$i,
    						'day' => "Wednesday",
    						'pat_id' => $pat_id,
    						'center_id' => $center_id
    				);
    				$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
    				$status = "on treatment";
    			}
    			$j++; endforeach;
    			$i++; endforeach;
    				
    			$result = $this->ndo_model->get_all_running_machine($center_id);
    			$data['machine'] = $result['row'];
    			$i=1;
    			foreach($data['machine'] as $mrow):
    			$result = $this->ndo_model->get_all_shift($center_id);
    			$data['shift'] = $result['row'];
    			$j=1;
    			foreach($data['shift'] as $srow):
    			$slot_id = $this->input->post('s'.$j.'s'.$i.'thu');
    			if($slot_id != ""){
    				$data = array(
    						'slot_id' => $slot_id,
    						'shift_id' => $srow->shift_id,
    						'slot' => "slot".$i,
    						'day' => "Thursday",
    						'pat_id' => $pat_id,
    						'center_id' => $center_id
    				);
    				$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
    				$status = "on treatment";
    			}
    			$j++; endforeach;
    			$i++; endforeach;
    				
    			$result = $this->ndo_model->get_all_running_machine($center_id);
    			$data['machine'] = $result['row'];
    			$i=1;
    			foreach($data['machine'] as $mrow):
    			$result = $this->ndo_model->get_all_shift($center_id);
    			$data['shift'] = $result['row'];
    			$j=1;
    			foreach($data['shift'] as $srow):
    			$slot_id = $this->input->post('s'.$j.'s'.$i.'fri');
    			if($slot_id != ""){
    				$data = array(
    						'slot_id' => $slot_id,
    						'shift_id' => $srow->shift_id,
    						'slot' => "slot".$i,
    						'day' => "Friday",
    						'pat_id' => $pat_id,
    						'center_id' => $center_id
    				);
    				$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
    				$status = "on treatment";
    			}
    			$j++; endforeach;
    			$i++; endforeach;
    				
    			$result = $this->ndo_model->get_all_running_machine($center_id);
    			$data['machine'] = $result['row'];
    			$i=1;
    			foreach($data['machine'] as $mrow):
    			$result = $this->ndo_model->get_all_shift($center_id);
    			$data['shift'] = $result['row'];
    			$j=1;
    			foreach($data['shift'] as $srow):
    			$slot_id = $this->input->post('s'.$j.'s'.$i.'sat');
    			if($slot_id != ""){
    				$data = array(
    						'slot_id' => $slot_id,
    						'shift_id' => $srow->shift_id,
    						'slot' => "slot".$i,
    						'day' => "Saturday",
    						'pat_id' => $pat_id,
    						'center_id' => $center_id
    				);
    				$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
    				$status = "on treatment";
    			}
    			$j++; endforeach;
    			$i++; endforeach;
    
    
    
    		}
    		$data = array(
    				'status' => $status
    		);
    		$this->ndo_model->update_pat_history($data, $history_id);
    			
    			
    		if (!file_exists($path1)){
    			mkdir($path1, 0777);
    		}
    
    		$config['upload_path'] = $path1;
    		$config['allowed_types'] = '*';
    		$config['max_size'] = 0;
    		$config['overwrite'] = FALSE;
    
    		$this->upload->initialize($config);
    
    
    		if($this->upload->do_multi_upload('refer_letter')){
    			$file = $this->upload->get_multi_upload_data();
    
    			foreach($file as $array):{
    				$data = array(
    						'type' => "ref",
    						'upload_date' => date('Y-m-d H:i:s'),
    						'uploaded_by' => $staff_id,
    						'path' => $array['full_path'],
    						'name' => $array['file_name'],
    						'pat_id' => $pat_id
    							
    							
    				);
    				$this->ndo_model->add_letter($data);
    
    			}endforeach;
    
    		}
    
    		$this->upload->initialize($config);
    
    
    		if($this->upload->do_multi_upload('diag_letter')){
    			$file = $this->upload->get_multi_upload_data();
    				
    			foreach($file as $array):{
    				$data = array(
    						'type' => "diag",
    						'upload_date' => date('Y-m-d H:i:s'),
    						'uploaded_by' => $staff_id,
    						'path' => $array['full_path'],
    						'name' => $array['file_name'],
    						'pat_id' => $pat_id
    							
    				);
    				$this->ndo_model->add_letter($data);
    					
    			}endforeach;
    				
    		}
    			
    		$this->upload->initialize($config);
    			
    			
    		if($this->upload->do_multi_upload('sponsor_letter')){
    			$file = $this->upload->get_multi_upload_data();
    				
    			foreach($file as $array):{
    				$data = array(
    						'type' => "spon",
    						'upload_date' => date('Y-m-d H:i:s'),
    						'uploaded_by' => $staff_id,
    						'path' => $array['full_path'],
    						'name' => $array['file_name'],
    						'pat_id' => $pat_id
    
    				);
    				$this->ndo_model->add_letter($data);
    
    			}endforeach;
    				
    		}
    			
    		if($_FILES['img1']['error'] != 4){
    				
    			$config['upload_path'] = $path2;
    			$config['file_name'] = "img1.jpg";
    			$config['allowed_types'] = '*';
    			$config['max_size'] = 0;
    
    			$this->upload->initialize($config);
    
    
    			$this->upload->do_upload('img1');
    			$data = $this->upload->data();
    
    			$img1_path = $data['full_path'];
    			$img1_file_name = $data['file_name'];
    
    			$img1_date = $this->input->post('img1_date');
    			$img1_comment = $this->input->post('img1_comment');
    			$data = array(
    					'img_name' => $img1_file_name,
    					'img_path' => $img1_path,
    					'img_date' => $img1_date,
    					'img_comment' => $img1_comment,
    					'pat_id' => $pat_id
    						
    			);
    			$this->ndo_model->add_medical_img($data);
    
    
    		}
    		if($_FILES['img2']['error'] != 4){
    				
    			$config['upload_path'] = $path2;
    			$config['file_name'] = "img2.jpg";
    			$config['allowed_types'] = '*';
    			$config['max_size'] = 0;
    
    			$this->upload->initialize($config);
    
    
    			$this->upload->do_upload('img2');
    			$data = $this->upload->data();
    
    			$img2_path = $data['full_path'];
    			$img2_file_name = $data['file_name'];
    
    			$img2_date = $this->input->post('img2_date');
    			$img2_comment = $this->input->post('img2_comment');
    			$data = array(
    					'img_name' => $img2_file_name,
    					'img_path' => $img2_path,
    					'img_date' => $img2_date,
    					'img_comment' => $img2_comment,
    					'pat_id' => $pat_id
    						
    			);
    			$this->ndo_model->add_medical_img($data);
    
    
    		}
    		if($_FILES['img3']['error'] != 4){
    				
    			$config['upload_path'] = $path2;
    			$config['file_name'] = "img3.jpg";
    			$config['allowed_types'] = '*';
    			$config['max_size'] = 0;
    
    			$this->upload->initialize($config);
    
    
    			$this->upload->do_upload('img3');
    			$data = $this->upload->data();
    
    			$img3_path = $data['full_path'];
    			$img3_file_name = $data['file_name'];
    
    			$img3_date = $this->input->post('img3_date');
    			$img3_comment = $this->input->post('img3_comment');
    			$data = array(
    					'img_name' => $img3_file_name,
    					'img_path' => $img3_path,
    					'img_date' => $img3_date,
    					'img_comment' => $img3_comment,
    					'pat_id' => $pat_id
    						
    			);
    			$this->ndo_model->add_medical_img($data);
    
    
    		}
    			
    		$data = array(
    				'pat_id' => $pat_id,
    				'center_id' => $center_id
    		);
    		$this->ndo_model->add_clinical_summary($data);
    			
    			
    		$this->ndo_model->add_pat_med_problem($problem1, $problem2, $problem3, $problem4, $problem5, $problem6, $problem7, $problem8, $problem9, $summary);
    			
    			
    		$this->ndo_model->add_pat_medication($medic1, $medic2, $medic3, $medic4, $medic5, $medic6, $medic7, $medic8, $medic9, $medic10, $medic11, $medic12, $plan);
    			
    		$this->session->set_flashdata('success_msg', ' Patient Is Added Successfully.');
    		redirect('staff/pat_list_data');
    			
    	}else{
    		$this->session->set_flashdata('error_msg', ' Patient already exists in System Database.');
    		redirect('staff/pat_list_data');
    			
    			
    	}
    
    }
	
	public function view_pat($pat_id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		
		$data['name'] = $name;
		$data['role'] = $role;
		
		$res = $this->ndo_model->get_all_country();
		$data['country'] = $res['row'];
		
		$res = $this->ndo_model->get_all_state();
		$data['state'] = $res['row'];
		
		$res = $this->ndo_model->get_all_city();
		$data['city'] = $res['row'];
		
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		$result = $this->ndo_model->get_pat_info_id($pat_id);
		$data['patient'] = $result;
		$result = $this->ndo_model->get_history_info($pat_id);
		$data['history'] = $result;
		$data['id'] = $pat_id;
		$result = $this->ndo_model->get_letter($pat_id);
		$data['letter'] = $result;
		$result = $this->ndo_model->get_pat_pic($pat_id);
		$data['pic'] = $result['row'];
		$result = $this->ndo_model->get_all_slot_sch($center_id);
		$data['slot'] = $result['row'];
		$result = $this->ndo_model->get_pat_sch($pat_id);
		$data['pat_shc'] = $result['row'];
		
		//secondary specialist
		
		$result = $this->ndo_model->get_secondary_neph($pat_id);
		$data['sec_neph'] = $result['row'];
		$data['secneph_number_row'] = $result['num_row'];
		
		$result = $this->ndo_model->get_secondary_doc($pat_id);
		$data['sec_doc'] = $result['row'];
		$data['secdoc_number_row'] = $result['num_row'];
		
		$result = $this->ndo_model->get_all_staff();
		$data['staff'] = $result['row'];
		$result = $this->ndo_model->get_center_superviser_neph($center_id);
		$data['neph_superviser'] = $result['row'];
		$result = $this->ndo_model->get_all_neph();
		$data['neph'] = $result['row'];
		$result = $this->ndo_model->get_center_superviser_doc($center_id);
		$data['doc_superviser'] = $result['row'];
		$result = $this->ndo_model->get_all_doc();
		$data['doc'] = $result['row'];
		$result = $this->ndo_model->get_all_pat($center_id);
		$data['pat'] = $result['row'];
		
		$result = $this->ndo_model->get_all_n_review($pat_id);
		$data['n_review'] = $result['row'];
		$data['n_review_num_row'] = $result['num_row'];
		$result = $this->ndo_model->get_last_n_review($pat_id);
		$data['last_n_review'] = $result;
		$result = $this->ndo_model->get_all_d_review($pat_id);
		$data['d_review'] = $result['row'];
		$data['d_review_num_row'] = $result['num_row'];
		$result = $this->ndo_model->get_last_d_review($pat_id);
		$data['last_d_review'] = $result;
		
		$result = $this->ndo_model->get_all_staff_d();
		$data['staff_d'] = $result['row'];
		$result = $this->ndo_model->get_all_neph_d();
		$data['neph_d'] = $result['row'];
		$result = $this->ndo_model->get_all_doc_d();
		$data['doc_d'] = $result['row'];
		$result = $this->ndo_model->get_all_owner_d();
		$data['owner_d'] = $result['row'];
		$result = $this->ndo_model->get_all_admin_d();
		$data['admin_d'] = $result['row'];
		$result = $this->ndo_model->get_all_running_machine($center_id);
		$data['machine'] = $result['row'];
		$result = $this->ndo_model->get_all_shift($center_id);
		$data['shift'] = $result['row'];
		
		
		//get medical problem
		
		$result = $this->ndo_model->get_gses($pat_id);
		$data['gses'] = $result['row'];
		$result = $this->ndo_model->get_endo($pat_id);
		$data['endo'] = $result['row'];
		$result = $this->ndo_model->get_gastro($pat_id);
		$data['gastro'] = $result['row'];
		$result = $this->ndo_model->get_res_system($pat_id);
		$data['res_system'] = $result['row'];
		$result = $this->ndo_model->get_musc($pat_id);
		$data['musc'] = $result['row'];
		$result = $this->ndo_model->get_card($pat_id);
		$data['card'] = $result['row'];
		$result = $this->ndo_model->get_hema($pat_id);
		$data['hema'] = $result['row'];
		$result = $this->ndo_model->get_neuro($pat_id);
		$data['neuro'] = $result['row'];
		$result = $this->ndo_model->get_other_problem($pat_id);
		$data['other_problem'] = $result['row'];
		
		
		//get medication
		
		$result = $this->ndo_model->get_arb($pat_id);
		$data['arb'] = $result['row'];
		$result = $this->ndo_model->get_beta($pat_id);
		$data['beta'] = $result['row'];
		$result = $this->ndo_model->get_ace($pat_id);
		$data['ace'] = $result['row'];
		$result = $this->ndo_model->get_ccb($pat_id);
		$data['ccb'] = $result['row'];
		$result = $this->ndo_model->get_vitamin_d($pat_id);
		$data['vitamin'] = $result['row'];
		$result = $this->ndo_model->get_hematinics($pat_id);
		$data['hematinics'] = $result['row'];
		$result = $this->ndo_model->get_aab($pat_id);
		$data['aab'] = $result['row'];
		$result = $this->ndo_model->get_vas($pat_id);
		$data['vas'] = $result['row'];
		$result = $this->ndo_model->get_phosphate($pat_id);
		$data['phosphate'] = $result['row'];
		$result = $this->ndo_model->get_diur($pat_id);
		$data['diur'] = $result['row'];
		$result = $this->ndo_model->get_ery($pat_id);
		$data['ery'] = $result['row'];
		$result = $this->ndo_model->get_other_medic($pat_id);
		$data['other_medic'] = $result['row'];
		
		
		
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/view_pat', $data);
	  $this->load->view('footer');
	
    }
    
    public function view_pat_data($pat_id){
    	$staff_id = $this->session->userdata('staff_id');
    	$this->load->model('ndo_model');
    	$data['staff'] = $this->ndo_model->get_staff($staff_id);
    	foreach($data['staff'] as $row):{
    		$name = $row->name;
    		$role = $row->role;
    	}endforeach;
    
    	$data['name'] = $name;
    	$data['role'] = $role;
    
    	$center_id =  $this->ndo_model->get_center_id($staff_id);
    	$data['center_header'] = $this->ndo_model->get_center($center_id);
    	$result = $this->ndo_model->get_pat_info_id($pat_id);
    	$data['patient'] = $result;
    	$result = $this->ndo_model->get_history_info($pat_id);
    	$data['history'] = $result;
    	$data['id'] = $pat_id;
    	$result = $this->ndo_model->get_letter($pat_id);
    	$data['letter'] = $result;
    	$result = $this->ndo_model->get_pat_pic($pat_id);
    	$data['pic'] = $result['row'];
    	$result = $this->ndo_model->get_all_slot_sch($center_id);
    	$data['slot'] = $result['row'];
    	$result = $this->ndo_model->get_pat_sch($pat_id);
    	$data['pat_shc'] = $result['row'];
    
    	$result = $this->ndo_model->get_all_staff();
    	$data['staff'] = $result['row'];
    	$result = $this->ndo_model->get_center_superviser_neph($center_id);
    	$data['neph_superviser'] = $result['row'];
    	$result = $this->ndo_model->get_all_neph();
    	$data['neph'] = $result['row'];
    	$result = $this->ndo_model->get_center_superviser_doc($center_id);
    	$data['doc_superviser'] = $result['row'];
    	$result = $this->ndo_model->get_all_doc();
    	$data['doc'] = $result['row'];
    	$result = $this->ndo_model->get_all_pat($center_id);
    	$data['pat'] = $result['row'];
    
    	$result = $this->ndo_model->get_all_staff_d();
    	$data['staff_d'] = $result['row'];
    	$result = $this->ndo_model->get_all_neph_d();
    	$data['neph_d'] = $result['row'];
    	$result = $this->ndo_model->get_all_doc_d();
    	$data['doc_d'] = $result['row'];
    	$result = $this->ndo_model->get_all_owner_d();
    	$data['owner_d'] = $result['row'];
    	$result = $this->ndo_model->get_all_admin_d();
    	$data['admin_d'] = $result['row'];
    	$result = $this->ndo_model->get_all_running_machine($center_id);
    	$data['machine'] = $result['row'];
    	$result = $this->ndo_model->get_all_shift($center_id);
    	$data['shift'] = $result['row'];
    
    
    	//get medical problem
    
    	$result = $this->ndo_model->get_gses($pat_id);
    	$data['gses'] = $result['row'];
    	$result = $this->ndo_model->get_endo($pat_id);
    	$data['endo'] = $result['row'];
    	$result = $this->ndo_model->get_gastro($pat_id);
    	$data['gastro'] = $result['row'];
    	$result = $this->ndo_model->get_res_system($pat_id);
    	$data['res_system'] = $result['row'];
    	$result = $this->ndo_model->get_musc($pat_id);
    	$data['musc'] = $result['row'];
    	$result = $this->ndo_model->get_card($pat_id);
    	$data['card'] = $result['row'];
    	$result = $this->ndo_model->get_hema($pat_id);
    	$data['hema'] = $result['row'];
    	$result = $this->ndo_model->get_neuro($pat_id);
    	$data['neuro'] = $result['row'];
    	$result = $this->ndo_model->get_other_problem($pat_id);
    	$data['other_problem'] = $result['row'];
    	$result = $this->ndo_model->get_problem_summary($pat_id);
    	$data['summary'] = $result['row'];
    
    	//get medication
    
    	$result = $this->ndo_model->get_arb($pat_id);
    	$data['arb'] = $result['row'];
    	$result = $this->ndo_model->get_beta($pat_id);
    	$data['beta'] = $result['row'];
    	$result = $this->ndo_model->get_ace($pat_id);
    	$data['ace'] = $result['row'];
    	$result = $this->ndo_model->get_ccb($pat_id);
    	$data['ccb'] = $result['row'];
    	$result = $this->ndo_model->get_vitamin_d($pat_id);
    	$data['vitamin'] = $result['row'];
    	$result = $this->ndo_model->get_hematinics($pat_id);
    	$data['hematinics'] = $result['row'];
    	$result = $this->ndo_model->get_aab($pat_id);
    	$data['aab'] = $result['row'];
    	$result = $this->ndo_model->get_vas($pat_id);
    	$data['vas'] = $result['row'];
    	$result = $this->ndo_model->get_phosphate($pat_id);
    	$data['phosphate'] = $result['row'];
    	$result = $this->ndo_model->get_diur($pat_id);
    	$data['diur'] = $result['row'];
    	$result = $this->ndo_model->get_ery($pat_id);
    	$data['ery'] = $result['row'];
    	$result = $this->ndo_model->get_other_medic($pat_id);
    	$data['other_medic'] = $result['row'];
    	$result = $this->ndo_model->get_medic_plan($pat_id);
    	$data['plan'] = $result['row'];
    
    
    
    	$this->load->view('staff/header', $data);
    	$this->load->view('staff/side_menu', $data);
    	$this->load->view('staff/view_pat_data', $data);
    	$this->load->view('footer');
    
    }
	
	public function edit_pat($pat_id){
		$staff_id = $this->session->userdata('staff_id');
		
		$datestring = "%Y-%m-%d";
        $time = time();
		$current_date = mdate($datestring, $time);
		
		$datestring = "%H:%i:%s";
        $time = time();
		$current_time = mdate($datestring, $time);
		
		$this->load->library('create_id');
		
		$history_id = $this->create_id->get_id();
		$medical_history_id = $this->create_id->get_id();
		
		//personal info
		$name = $this->input->post('name');
		$pat_ic = $this->input->post('ic');
		$age = $this->input->post('age');
		$gender = $this->input->post('gender');
		$email = $this->input->post('email');
		$phoneNo = $this->input->post('phone');
		$mobileNo = $this->input->post('mobile');
		$country_id = $this->input->post('country');
		$state_id = $this->input->post('state');
		$city_id = $this->input->post('city');
		$address = $this->input->post('address');
		$nk_name = $this->input->post('nkname');
		$nk_job = $this->input->post('nkjob');
		$nk_relation = $this->input->post('relation');
		$nk_email = $this->input->post('nkemail');
		$nk_phoneNo = $this->input->post('nkphone');
		$nk_mobileNO = $this->input->post('nkmobile');
		$nkcountry_id = $this->input->post('nkcountry');
		$nkstate_id = $this->input->post('nkstate');
		$nkcity_id = $this->input->post('nkcity');
		$nk_address = $this->input->post('nkaddress');
		
		if($name != "" && $pat_ic !=""){
			
		$this->load->model('ndo_model');
		$result = $this->ndo_model->get_pat_info_id($pat_id);
		$data['patient'] = $result;
		foreach($data['patient'] as $row){
		$old_pat_ic = $row->pat_ic;
		$pic_name = $row->pic_name;
		$status = $row->status;
		}
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		
		$age_regex = '/^(19|20|21|22|23|24|25)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';
		
		if (!preg_match($age_regex, $age)) {
			$age = "";
		}
		
		
		
		//.....................
		
		//medical info
		
		$refer_from = $this->input->post('refer_from');
		$refer_date = $this->input->post('refer_date');
		$diag_result = $this->input->post('diag_result');
		$sponsor = $this->input->post('sponsor');
		$path1 = './pat_doc/letter/'.$pat_id;
		$path2 = './pat_doc/pic/'.$pat_id;
		$neph_incharge = $this->input->post('neph_incharge');
		$doc_incharge = $this->input->post('doc_incharge');
		
		
		
		
		//......................
		//gses medical problem
		$obstructive_uropathy = $this->input->post('ou');
		$analgesic_nephropathy = $this->input->post('an');
		$prostatic_hyperplasia = $this->input->post('ph');
		$lupus_nephritis = $this->input->post('ln');
		$gses_other = $this->input->post('gses_other');
		
		$problem1 = array(
			'ou' => $obstructive_uropathy,
			'an' => $analgesic_nephropathy,
			'ph' => $prostatic_hyperplasia,
			'ln' => $lupus_nephritis,
			'other' => $gses_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//endo medical problem
		$dm_type2 = $this->input->post('dm');
		$hyperlipidemia = $this->input->post('hl');
		$hypercholesterolemia = $this->input->post('hc');
		$secondary_hyperparathyroidism = $this->input->post('sh');
		$tertiary_hyperparathyroidism = $this->input->post('th');
		$post_hyperparathyroidism = $this->input->post('php');
		$endo_other = $this->input->post('endo_other');
		
		$problem2 = array(
			'dm' => $dm_type2,
			'hl' => $hyperlipidemia,
			'hc' => $hypercholesterolemia,
			'sh' => $secondary_hyperparathyroidism,
			'th' => $tertiary_hyperparathyroidism,
			'ph' => $post_hyperparathyroidism,
			'other' => $endo_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//gastro medical problem
		$upper_gastrointestinal_bleeding = $this->input->post('ugb');
		$ugb_hemorrhoids = $this->input->post('ugbh');
		$liver_disease = $this->input->post('ld');
		$gastro_other = $this->input->post('gastro_other');
		
		$problem3 = array(
			'ugb' => $upper_gastrointestinal_bleeding,
			'ugbh' => $ugb_hemorrhoids,
			'ld' => $liver_disease,
			'other' => $gastro_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//res_system medical problem
		$chronic_obstructive_airway_disease = $this->input->post('coad');
		$bronchial_asthma = $this->input->post('ba');
		$bronchiectasis = $this->input->post('bc');
		$pulmonary_tuberculosis = $this->input->post('pt');
		$pt_completed_treatment = $this->input->post('pt_end_date');
		$pt_date_started = $this->input->post('pt_start_date');
		$res_other = $this->input->post('res_system_other');
		
		$problem4 = array(
			'coad' => $chronic_obstructive_airway_disease,
			'ba' => $bronchial_asthma,
			'b' => $bronchiectasis,
			'pt' => $pulmonary_tuberculosis,
			'pt_completed_date' => $pt_completed_treatment,
			'pt_started_date' => $pt_date_started,
			'other' => $res_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//musc medical problem
		$gouty_arthropathy = $this->input->post('ga');
		$below_knee_amputation = $this->input->post('bka');
		$above_knee_amputation = $this->input->post('aka');
		$rays_amputation = $this->input->post('ra');
		$arthritis = $this->input->post('art');
		$musc_other = $this->input->post('musc_other');
		
		$problem5 = array(
			'ga' => $gouty_arthropathy,
			'bka' => $below_knee_amputation,
			'aka' => $above_knee_amputation,
			'ra' => $rays_amputation,
			'a' => $arthritis,
			'other' => $musc_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//card medical problem
		$ischemic_heart_disease = $this->input->post('ihd');
		$hypertension = $this->input->post('hypertension');
		$myocardial_infarct = $this->input->post('mi');
		$post_cabg = $this->input->post('post_cabg');
		$card_other = $this->input->post('card_other');
		
		$problem6 = array(
			'ihd' => $ischemic_heart_disease,
			'h' => $hypertension,
			'mi' => $myocardial_infarct,
			'pc' => $post_cabg,
			'other' => $card_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//hema medical problem
		$chronic_myeloid_leukemia = $this->input->post('cml');
		$acute_myeloid_leukemia = $this->input->post('aml');
		$lymphoma = $this->input->post('lymphoma');
		$anemia = $this->input->post('anemia');
		$polycythemia = $this->input->post('poly');
		$hema_other = $this->input->post('hema_other');
		
		$problem7 = array(
			'cml' => $chronic_myeloid_leukemia,
			'aml' => $acute_myeloid_leukemia,
			'l' => $lymphoma,
			'a' => $anemia,
			'p' => $polycythemia,
			'other' => $hema_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//neuro medical problem
		$cerebrovascular_accident = $this->input->post('cva');
		$peripheral_neuropathy = $this->input->post('pn');
		$neuro_other = $this->input->post('neuro_other');
		
		$problem8 = array(
			'cva' => $cerebrovascular_accident,
			'pn' => $peripheral_neuropathy,
			'other' => $neuro_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//other medical problem
		$other_problem = $this->input->post('other_problem');
		
		$problem9 = array(
			'other' => $other_problem,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		
		
		//arb blocker medication
		$losartan = $this->input->post('losartan');
		$losartan_hctz = $this->input->post('losartan_hctz');
		$ibersartan = $this->input->post('ibersartan');
		$ibersartan_hctz = $this->input->post('ibersartan_hctz');
		$valsartan = $this->input->post('valsartan');
		$olmesartan = $this->input->post('olmesartan');
		$telmisartan = $this->input->post('telmisartan');
		$arb_other = $this->input->post('arb_other');
		
		$medic1 = array(
			'losartan' => $losartan,
			'losartan_hctz' => $losartan_hctz,
			'ibersartan' => $ibersartan,
			'ibersartan_hctz' => $ibersartan_hctz,
			'valsartan' => $valsartan,
			'olmesartan' => $olmesartan,
			'telmisartan' => $telmisartan,
			'other' => $arb_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//beta blocker medication
		$atenolol = $this->input->post('atenolol');
		$metaprolol = $this->input->post('metaprolol');
		$bisoprolol = $this->input->post('bisoprolol');
		$carvidelol = $this->input->post('carvidelol');
		$beta_other = $this->input->post('beta_other');
		
		$medic2 = array(
			'atenolol' => $atenolol,
			'metaprolol' => $metaprolol,
			'bisoprolol' => $bisoprolol,
			'carvidelol' => $carvidelol,
			'other' => $beta_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//ace inhibitors medication
		$enalapril = $this->input->post('enalapril');
		$captopril = $this->input->post('captopril');
		$ace_other = $this->input->post('ace_other');
		
		$medic3 = array(
			'enalapril' => $enalapril,
			'captopril' => $captopril,
			'other' => $ace_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//ccb blocker medication
		$amlodipine = $this->input->post('amlodipine');
		$felodipine = $this->input->post('felodipine');
		$ccb_other = $this->input->post('ccb_other');
		
		$medic4 = array(
			'amlodipine' => $amlodipine,
			'felodipine' => $felodipine,
			'other' => $ccb_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//activated vitamin D medication
		$calcitriol = $this->input->post('calcitriol');
		$calcidol = $this->input->post('calcidol');
		$vitamin_other = $this->input->post('vitamin_other');
		
		$medic5 = array(
			'calcitriol' => $calcitriol,
			'calcidol' => $calcidol,
			'other' => $vitamin_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//hematinics medication
		$ferrous_fumarate = $this->input->post('ferrous_fumarate');
		$b_complex = $this->input->post('b_complex');
		$folate = $this->input->post('folate');
		$hematinics_other = $this->input->post('hematinics_other');
		
		$medic6 = array(
			'ferrous_fumarate' => $ferrous_fumarate,
			'b_complex' => $b_complex,
			'folate' => $folate,
			'other' => $hematinics_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//aab blocker medication
		$prazosin = $this->input->post('prazosin');
		$aab_other = $this->input->post('aab_other');
		
		$medic7 = array(
			'prazosin' => $prazosin,
			'other' => $aab_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//vasodilators medication
		$isordil = $this->input->post('isordil');
		$minoxidil = $this->input->post('minoxidil');
		$vasodilators_other = $this->input->post('vasodilators_other');
		
		$medic8 = array(
			'isordil' => $isordil,
			'minoxidil' => $minoxidil,
			'other' => $vasodilators_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//phosphate binders medication
		$calcium_carbonate = $this->input->post('calcium_carbonate');
		$phosphate_other = $this->input->post('phosphate_other');
		
		$medic9 = array(
			'calcium_carbonate' => $calcium_carbonate,
			'other' => $phosphate_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		//diuretics medication
		$frusemide = $this->input->post('frusemide');
		$diuretics_other = $this->input->post('diuretics_other');
		
		$medic10 = array(
			'frusemide' => $frusemide,
			'other' => $diuretics_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		
		//erythorpoetin medication
		$eprex = $this->input->post('eprex');
		$recormon = $this->input->post('recormon');
		$binocrit = $this->input->post('binocrit');
		$mircela = $this->input->post('mircela');
		$ery_other = $this->input->post('ery_other');
		
		$medic11 = array(
			'eprex' => $eprex,
			'recormon' => $recormon,
			'binocrit' => $binocrit,
			'mircela' => $mircela,
			'other' => $ery_other,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		
		//other medication
		$other_medic = $this->input->post('other_medic');
		
		$medic12 = array(
			'other' => $other_medic,
			'pat_id' => $pat_id,
			'medical_history_id' => $medical_history_id
				);
		//...............................................
		
				
		$data = array(
			'history_id' => $history_id
				);
		
		$query_result =$this->ndo_model->validate_and_update_pat_info($data, $pat_id, $pat_ic, $old_pat_ic);
		
		if($query_result != false){
		
		if (!file_exists($path2)){
					mkdir($path2, 0777);
				}
			if($pic_name == NULL || $pic_name==""){
				if($_FILES['profile_pic']['error'] != 4){
			
			 $config['upload_path'] = $path2;
			 $config['file_name'] = "profile_pic.jpg";
			 $config['overwrite'] = TRUE;
             $config['allowed_types'] = '*';
             $config['max_size'] = 0;

             $this->upload->initialize($config);


             $this->upload->do_upload('profile_pic');
		     $data = $this->upload->data();

             $pic_full_path = $data['full_path'];
	   		 $pic_file_name = $data['file_name'];
			 
			 $data = array(
			'pic_path' => $pic_full_path,
			'pic_name' => $pic_file_name
				);
			$this->ndo_model->update_pat_info($data, $pat_id);	

			}
		}
		
		//history track
		
			$data = array(
				'history_id' => $history_id,
				'pat_ic' => $pat_ic,
				'pat_id' => $pat_id,
				'name' => $name,
				'age' => $age,
				'gender' => $gender,
				'email' => $email,
				'phoneNo' => $phoneNo,
				'mobileNo' => $mobileNo,
				'country_id' => $country_id,
				'state_id' => $state_id,
				'city_id' => $city_id,
				'address' => $address,
				'nk_name' => $nk_name,
				'nk_job' => $nk_job,
				'nk_relation' => $nk_relation,
				'nk_email' => $nk_email,
				'nk_phoneNo' => $nk_phoneNo,
				'nk_mobileNo' => $nk_mobileNO,
				'nkcountry_id' => $nkcountry_id,
				'nkstate_id' => $nkstate_id,
				'nkcity_id' => $nkcity_id,
				'nk_address' => $nk_address,
				'refer_from' => $refer_from,
				'refer_date' => $refer_date,
				'diag_result' => $diag_result,
				'sponsor' => $sponsor,
				'status' => $status,
				'deleted' => -1,
				'center_id' => $center_id,
				'neph_id' => $neph_incharge,
				'doc_id' => $doc_incharge,
				'action' => "edited",
				'action_by' => $staff_id,
				'action_date' => date('Y-m-d H:i:s'),
				'medical_history_id' => $medical_history_id
		);
		
			
			$this->ndo_model->add_pat_history($data);
			
			if($sponsor != "Not Sponsored"){
				if($status != "on treatment"){
			
					$result = $this->ndo_model->get_all_running_machine($center_id);
					$data['machine'] = $result['row'];
					$i=1;
					foreach($data['machine'] as $mrow):
					$result = $this->ndo_model->get_all_shift($center_id);
					$data['shift'] = $result['row'];
					$j=1;
					foreach($data['shift'] as $srow):
					$slot_id = $this->input->post('s'.$j.'s'.$i.'sun');
					if($slot_id != ""){
						$data = array(
								'slot_id' => $slot_id,
								'shift_id' => $srow->shift_id,
								'slot' => "slot".$i,
								'day' => "Sunday",
								'pat_id' => $pat_id,
								'center_id' => $center_id
						);
						$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
						$status = "on treatment";
						$data = array(
								'status' => $status
						);
						$this->ndo_model->update_pat_history($data, $history_id);
					}
					$j++; endforeach;
					$i++; endforeach;
						
					$result = $this->ndo_model->get_all_running_machine($center_id);
					$data['machine'] = $result['row'];
					$i=1;
					foreach($data['machine'] as $mrow):
					$result = $this->ndo_model->get_all_shift($center_id);
					$data['shift'] = $result['row'];
					$j=1;
					foreach($data['shift'] as $srow):
					$slot_id = $this->input->post('s'.$j.'s'.$i.'mon');
					if($slot_id != ""){
						$data = array(
								'slot_id' => $slot_id,
								'shift_id' => $srow->shift_id,
								'slot' => "slot".$i,
								'day' => "Monday",
								'pat_id' => $pat_id,
								'center_id' => $center_id
						);
						$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
						$status = "on treatment";
						$data = array(
								'status' => $status
						);
						$this->ndo_model->update_pat_history($data, $history_id);
					}
					$j++; endforeach;
					$i++; endforeach;
						
					$result = $this->ndo_model->get_all_running_machine($center_id);
					$data['machine'] = $result['row'];
					$i=1;
					foreach($data['machine'] as $mrow):
					$result = $this->ndo_model->get_all_shift($center_id);
					$data['shift'] = $result['row'];
					$j=1;
					foreach($data['shift'] as $srow):
					$slot_id = $this->input->post('s'.$j.'s'.$i.'tue');
					if($slot_id != ""){
						$data = array(
								'slot_id' => $slot_id,
								'shift_id' => $srow->shift_id,
								'slot' => "slot".$i,
								'day' => "Tuesday",
								'pat_id' => $pat_id,
								'center_id' => $center_id
						);
						$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
						$status = "on treatment";
						$data = array(
								'status' => $status
						);
						$this->ndo_model->update_pat_history($data, $history_id);
					}
					$j++; endforeach;
					$i++; endforeach;
						
					$result = $this->ndo_model->get_all_running_machine($center_id);
					$data['machine'] = $result['row'];
					$i=1;
					foreach($data['machine'] as $mrow):
					$result = $this->ndo_model->get_all_shift($center_id);
					$data['shift'] = $result['row'];
					$j=1;
					foreach($data['shift'] as $srow):
					$slot_id = $this->input->post('s'.$j.'s'.$i.'wed');
					if($slot_id != ""){
						$data = array(
								'slot_id' => $slot_id,
								'shift_id' => $srow->shift_id,
								'slot' => "slot".$i,
								'day' => "Wednesday",
								'pat_id' => $pat_id,
								'center_id' => $center_id
						);
						$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
						$status = "on treatment";
						$data = array(
								'status' => $status
						);
						$this->ndo_model->update_pat_history($data, $history_id);
					}
					$j++; endforeach;
					$i++; endforeach;
						
					$result = $this->ndo_model->get_all_running_machine($center_id);
					$data['machine'] = $result['row'];
					$i=1;
					foreach($data['machine'] as $mrow):
					$result = $this->ndo_model->get_all_shift($center_id);
					$data['shift'] = $result['row'];
					$j=1;
					foreach($data['shift'] as $srow):
					$slot_id = $this->input->post('s'.$j.'s'.$i.'thu');
					if($slot_id != ""){
						$data = array(
								'slot_id' => $slot_id,
								'shift_id' => $srow->shift_id,
								'slot' => "slot".$i,
								'day' => "Thursday",
								'pat_id' => $pat_id,
								'center_id' => $center_id
						);
						$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
						$status = "on treatment";
						$data = array(
								'status' => $status
						);
						$this->ndo_model->update_pat_history($data, $history_id);
					}
					$j++; endforeach;
					$i++; endforeach;
						
					$result = $this->ndo_model->get_all_running_machine($center_id);
					$data['machine'] = $result['row'];
					$i=1;
					foreach($data['machine'] as $mrow):
					$result = $this->ndo_model->get_all_shift($center_id);
					$data['shift'] = $result['row'];
					$j=1;
					foreach($data['shift'] as $srow):
					$slot_id = $this->input->post('s'.$j.'s'.$i.'fri');
					if($slot_id != ""){
						$data = array(
								'slot_id' => $slot_id,
								'shift_id' => $srow->shift_id,
								'slot' => "slot".$i,
								'day' => "Friday",
								'pat_id' => $pat_id,
								'center_id' => $center_id
						);
						$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
						$status = "on treatment";
						$data = array(
								'status' => $status
						);
						$this->ndo_model->update_pat_history($data, $history_id);
					}
					$j++; endforeach;
					$i++; endforeach;
						
					$result = $this->ndo_model->get_all_running_machine($center_id);
					$data['machine'] = $result['row'];
					$i=1;
					foreach($data['machine'] as $mrow):
					$result = $this->ndo_model->get_all_shift($center_id);
					$data['shift'] = $result['row'];
					$j=1;
					foreach($data['shift'] as $srow):
					$slot_id = $this->input->post('s'.$j.'s'.$i.'sat');
					if($slot_id != ""){
						$data = array(
								'slot_id' => $slot_id,
								'shift_id' => $srow->shift_id,
								'slot' => "slot".$i,
								'day' => "Saturday",
								'pat_id' => $pat_id,
								'center_id' => $center_id
						);
						$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
						$status = "on treatment";
						$data = array(
								'status' => $status
						);
						$this->ndo_model->update_pat_history($data, $history_id);
					}
					$j++; endforeach;
					$i++; endforeach;
				}
		
			}else{
				$this->ndo_model->delete_pat_sch($pat_id);
				$status = "waiting list";
				$data = array(
						'status' => $status
								);
			$this->ndo_model->update_pat_history($data, $history_id);
				
			}
				
			
		$this->ndo_model->add_pat_med_problem($problem1, $problem2, $problem3, $problem4, $problem5, $problem6, $problem7, $problem8, $problem9);
			
			
			$this->ndo_model->add_pat_medication($medic1, $medic2, $medic3, $medic4, $medic5, $medic6, $medic7, $medic8, $medic9, $medic10, $medic11, $medic12);
			
			
		    $this->session->set_flashdata('success_msg', ' Changes Saved Successfully.');
			redirect('staff/view_pat/'.$pat_id);
		}else{
		$this->session->set_flashdata('error_msg', ' The Inserted IC already Assigned to another Patient !.');
			redirect('staff/view_pat/'.$pat_id);	
		}
		}else{
			
		$this->session->set_flashdata('error_msg', ' Name and IC Fields Can not Left Empty!.');
			redirect('staff/view_pat/'.$pat_id);	
			
		}
				
    }
    
    
    
    public function edit_pat_data($pat_id){
    	$staff_id = $this->session->userdata('staff_id');
    
    	$datestring = "%Y-%m-%d";
    	$time = time();
    	$current_date = mdate($datestring, $time);
    
    	$datestring = "%H:%i:%s";
    	$time = time();
    	$current_time = mdate($datestring, $time);
    
    	$this->load->library('create_id');
    
    	$history_id = $this->create_id->get_id();
    	$medical_history_id = $this->create_id->get_id();
    
    	//personal info
    	$staff_who_change = $this->input->post('staff_who_change');
    	$change_date = $this->input->post('change_date');
    	$change_time = $this->input->post('change_time');
    	
    	$time_stamp = $change_date." ".$change_time;
    	
    	$name = $this->input->post('name');
    	$pat_ic = $this->input->post('ic');
    	$age = $this->input->post('age');
    	$gender = $this->input->post('gender');
    	$email = $this->input->post('email');
    	$phoneNo = $this->input->post('phone');
    	$mobileNo = $this->input->post('mobile');
    	$address = $this->input->post('address');
    	$nk_name = $this->input->post('nkname');
    	$nk_job = $this->input->post('nkjob');
    	$nk_relation = $this->input->post('relation');
    	$nk_email = $this->input->post('nkemail');
    	$nk_phoneNo = $this->input->post('nkphone');
    	$nk_mobileNO = $this->input->post('nkmobile');
    	$nk_address = $this->input->post('nkaddress');
    
    	if($name != "" && $pat_ic !=""){
    			
    		$this->load->model('ndo_model');
    		$result = $this->ndo_model->get_pat_info_id($pat_id);
    		$data['patient'] = $result;
    		foreach($data['patient'] as $row){
    			$old_pat_ic = $row->pat_ic;
    			$pic_name = $row->pic_name;
    			$status = $row->status;
    		}
    		$center_id =  $this->ndo_model->get_center_id($staff_id);
    
    		$age_regex = '/^(19|20|21|22|23|24|25)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';
    
    		if (!preg_match($age_regex, $age)) {
    			$age = "";
    		}
    
    
    
    		//.....................
    
    		//medical info
    
    		$refer_from = $this->input->post('refer_from');
    		$refer_date = $this->input->post('refer_date');
    		$diag_result = $this->input->post('diag_result');
    		$sponsor = $this->input->post('sponsor');
    		$path1 = './pat_doc/letter/'.$pat_id;
    		$path2 = './pat_doc/pic/'.$pat_id;
    		$neph_incharge = $this->input->post('neph_incharge');
    		$doc_incharge = $this->input->post('doc_incharge');
    
    
    
    
    		//......................
    		//gses medical problem
    		$obstructive_uropathy = $this->input->post('ou');
    		$analgesic_nephropathy = $this->input->post('an');
    		$prostatic_hyperplasia = $this->input->post('ph');
    		$lupus_nephritis = $this->input->post('ln');
    		$gses_other = $this->input->post('gses_other');
    
    		$problem1 = array(
    				'ou' => $obstructive_uropathy,
    				'an' => $analgesic_nephropathy,
    				'ph' => $prostatic_hyperplasia,
    				'ln' => $lupus_nephritis,
    				'other' => $gses_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//endo medical problem
    		$dm_type2 = $this->input->post('dm');
    		$hyperlipidemia = $this->input->post('hl');
    		$hypercholesterolemia = $this->input->post('hc');
    		$secondary_hyperparathyroidism = $this->input->post('sh');
    		$tertiary_hyperparathyroidism = $this->input->post('th');
    		$post_hyperparathyroidism = $this->input->post('php');
    		$endo_other = $this->input->post('endo_other');
    
    		$problem2 = array(
    				'dm' => $dm_type2,
    				'hl' => $hyperlipidemia,
    				'hc' => $hypercholesterolemia,
    				'sh' => $secondary_hyperparathyroidism,
    				'th' => $tertiary_hyperparathyroidism,
    				'ph' => $post_hyperparathyroidism,
    				'other' => $endo_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//gastro medical problem
    		$upper_gastrointestinal_bleeding = $this->input->post('ugb');
    		$ugb_hemorrhoids = $this->input->post('ugbh');
    		$liver_disease = $this->input->post('ld');
    		$gastro_other = $this->input->post('gastro_other');
    
    		$problem3 = array(
    				'ugb' => $upper_gastrointestinal_bleeding,
    				'ugbh' => $ugb_hemorrhoids,
    				'ld' => $liver_disease,
    				'other' => $gastro_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//res_system medical problem
    		$chronic_obstructive_airway_disease = $this->input->post('coad');
    		$bronchial_asthma = $this->input->post('ba');
    		$bronchiectasis = $this->input->post('bc');
    		$pulmonary_tuberculosis = $this->input->post('pt');
    		$pt_completed_treatment = $this->input->post('pt_end_date');
    		$pt_date_started = $this->input->post('pt_start_date');
    		$res_other = $this->input->post('res_system_other');
    
    		$problem4 = array(
    				'coad' => $chronic_obstructive_airway_disease,
    				'ba' => $bronchial_asthma,
    				'b' => $bronchiectasis,
    				'pt' => $pulmonary_tuberculosis,
    				'pt_completed_date' => $pt_completed_treatment,
    				'pt_started_date' => $pt_date_started,
    				'other' => $res_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//musc medical problem
    		$gouty_arthropathy = $this->input->post('ga');
    		$below_knee_amputation = $this->input->post('bka');
    		$above_knee_amputation = $this->input->post('aka');
    		$rays_amputation = $this->input->post('ra');
    		$arthritis = $this->input->post('art');
    		$musc_other = $this->input->post('musc_other');
    
    		$problem5 = array(
    				'ga' => $gouty_arthropathy,
    				'bka' => $below_knee_amputation,
    				'aka' => $above_knee_amputation,
    				'ra' => $rays_amputation,
    				'a' => $arthritis,
    				'other' => $musc_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//card medical problem
    		$ischemic_heart_disease = $this->input->post('ihd');
    		$hypertension = $this->input->post('hypertension');
    		$myocardial_infarct = $this->input->post('mi');
    		$post_cabg = $this->input->post('post_cabg');
    		$card_other = $this->input->post('card_other');
    
    		$problem6 = array(
    				'ihd' => $ischemic_heart_disease,
    				'h' => $hypertension,
    				'mi' => $myocardial_infarct,
    				'pc' => $post_cabg,
    				'other' => $card_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//hema medical problem
    		$chronic_myeloid_leukemia = $this->input->post('cml');
    		$acute_myeloid_leukemia = $this->input->post('aml');
    		$lymphoma = $this->input->post('lymphoma');
    		$anemia = $this->input->post('anemia');
    		$polycythemia = $this->input->post('poly');
    		$hema_other = $this->input->post('hema_other');
    
    		$problem7 = array(
    				'cml' => $chronic_myeloid_leukemia,
    				'aml' => $acute_myeloid_leukemia,
    				'l' => $lymphoma,
    				'a' => $anemia,
    				'p' => $polycythemia,
    				'other' => $hema_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//neuro medical problem
    		$cerebrovascular_accident = $this->input->post('cva');
    		$peripheral_neuropathy = $this->input->post('pn');
    		$neuro_other = $this->input->post('neuro_other');
    
    		$problem8 = array(
    				'cva' => $cerebrovascular_accident,
    				'pn' => $peripheral_neuropathy,
    				'other' => $neuro_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//other medical problem
    		$other_problem = $this->input->post('other_problem');
    
    		$problem9 = array(
    				'other' => $other_problem,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    
    		//medical problem summary
    		$problem_summary = $this->input->post('sum_problem');
    		$examination_summary = $this->input->post('sum_examination');
    
    		$summary = array(
    				'problem_summary' => $problem_summary,
    				'examination_summary' => $examination_summary,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    
    		//arb blocker medication
    		$losartan = $this->input->post('losartan');
    		$losartan_hctz = $this->input->post('losartan_hctz');
    		$ibersartan = $this->input->post('ibersartan');
    		$ibersartan_hctz = $this->input->post('ibersartan_hctz');
    		$valsartan = $this->input->post('valsartan');
    		$olmesartan = $this->input->post('olmesartan');
    		$telmisartan = $this->input->post('telmisartan');
    		$arb_other = $this->input->post('arb_other');
    
    		$medic1 = array(
    				'losartan' => $losartan,
    				'losartan_hctz' => $losartan_hctz,
    				'ibersartan' => $ibersartan,
    				'ibersartan_hctz' => $ibersartan_hctz,
    				'valsartan' => $valsartan,
    				'olmesartan' => $olmesartan,
    				'telmisartan' => $telmisartan,
    				'other' => $arb_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//beta blocker medication
    		$atenolol = $this->input->post('atenolol');
    		$metaprolol = $this->input->post('metaprolol');
    		$bisoprolol = $this->input->post('bisoprolol');
    		$carvidelol = $this->input->post('carvidelol');
    		$beta_other = $this->input->post('beta_other');
    
    		$medic2 = array(
    				'atenolol' => $atenolol,
    				'metaprolol' => $metaprolol,
    				'bisoprolol' => $bisoprolol,
    				'carvidelol' => $carvidelol,
    				'other' => $beta_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//ace inhibitors medication
    		$enalapril = $this->input->post('enalapril');
    		$captopril = $this->input->post('captopril');
    		$ace_other = $this->input->post('ace_other');
    
    		$medic3 = array(
    				'enalapril' => $enalapril,
    				'captopril' => $captopril,
    				'other' => $ace_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//ccb blocker medication
    		$amlodipine = $this->input->post('amlodipine');
    		$felodipine = $this->input->post('felodipine');
    		$ccb_other = $this->input->post('ccb_other');
    
    		$medic4 = array(
    				'amlodipine' => $amlodipine,
    				'felodipine' => $felodipine,
    				'other' => $ccb_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//activated vitamin D medication
    		$calcitriol = $this->input->post('calcitriol');
    		$calcidol = $this->input->post('calcidol');
    		$vitamin_other = $this->input->post('vitamin_other');
    
    		$medic5 = array(
    				'calcitriol' => $calcitriol,
    				'calcidol' => $calcidol,
    				'other' => $vitamin_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//hematinics medication
    		$ferrous_fumarate = $this->input->post('ferrous_fumarate');
    		$b_complex = $this->input->post('b_complex');
    		$folate = $this->input->post('folate');
    		$hematinics_other = $this->input->post('hematinics_other');
    
    		$medic6 = array(
    				'ferrous_fumarate' => $ferrous_fumarate,
    				'b_complex' => $b_complex,
    				'folate' => $folate,
    				'other' => $hematinics_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//aab blocker medication
    		$prazosin = $this->input->post('prazosin');
    		$aab_other = $this->input->post('aab_other');
    
    		$medic7 = array(
    				'prazosin' => $prazosin,
    				'other' => $aab_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//vasodilators medication
    		$isordil = $this->input->post('isordil');
    		$minoxidil = $this->input->post('minoxidil');
    		$vasodilators_other = $this->input->post('vasodilators_other');
    
    		$medic8 = array(
    				'isordil' => $isordil,
    				'minoxidil' => $minoxidil,
    				'other' => $vasodilators_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//phosphate binders medication
    		$calcium_carbonate = $this->input->post('calcium_carbonate');
    		$phosphate_other = $this->input->post('phosphate_other');
    
    		$medic9 = array(
    				'calcium_carbonate' => $calcium_carbonate,
    				'other' => $phosphate_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    		//diuretics medication
    		$frusemide = $this->input->post('frusemide');
    		$diuretics_other = $this->input->post('diuretics_other');
    
    		$medic10 = array(
    				'frusemide' => $frusemide,
    				'other' => $diuretics_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    
    		//erythorpoetin medication
    		$eprex = $this->input->post('eprex');
    		$recormon = $this->input->post('recormon');
    		$binocrit = $this->input->post('binocrit');
    		$mircela = $this->input->post('mircela');
    		$ery_other = $this->input->post('ery_other');
    
    		$medic11 = array(
    				'eprex' => $eprex,
    				'recormon' => $recormon,
    				'binocrit' => $binocrit,
    				'mircela' => $mircela,
    				'other' => $ery_other,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    
    		//other medication
    		$other_medic = $this->input->post('other_medic');
    
    		$medic12 = array(
    				'other' => $other_medic,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    
    		//plan
    		$clinical_plan = $this->input->post('clinical_plan');
    
    		$plan = array(
    				'clinical_plan' => $clinical_plan,
    				'pat_id' => $pat_id,
    				'medical_history_id' => $medical_history_id
    		);
    		//...............................................
    
    		$data = array(
    				'history_id' => $history_id
    		);
    
    		$query_result =$this->ndo_model->validate_and_update_pat_info($data, $pat_id, $pat_ic, $old_pat_ic);
    
    		if($query_result != false){
    
    			if (!file_exists($path2)){
    				mkdir($path2, 0777);
    			}
    			if($pic_name == NULL || $pic_name==""){
    				if($_FILES['profile_pic']['error'] != 4){
    						
    					$config['upload_path'] = $path2;
    					$config['file_name'] = "profile_pic.jpg";
    					$config['overwrite'] = TRUE;
    					$config['allowed_types'] = '*';
    					$config['max_size'] = 0;
    
    					$this->upload->initialize($config);
    
    
    					$this->upload->do_upload('profile_pic');
    					$data = $this->upload->data();
    
    					$pic_full_path = $data['full_path'];
    					$pic_file_name = $data['file_name'];
    
    					$data = array(
    							'pic_path' => $pic_full_path,
    							'pic_name' => $pic_file_name
    					);
    					$this->ndo_model->update_pat_history($data, $history_id);
    
    				}
    			}
    
    			//history track
    
    			$data = array(
    					'history_id' => $history_id,
    					'pat_ic' => $pat_ic,
    					'pat_id' => $pat_id,
    					'name' => $name,
    					'age' => $age,
    					'gender' => $gender,
    					'email' => $email,
    					'phoneNo' => $phoneNo,
    					'mobileNo' => $mobileNo,
    					'address' => $address,
    					'nk_name' => $nk_name,
    					'nk_job' => $nk_job,
    					'nk_relation' => $nk_relation,
    					'nk_email' => $nk_email,
    					'nk_phoneNo' => $nk_phoneNo,
    					'nk_mobileNo' => $nk_mobileNO,
    					'nk_address' => $nk_address,
    					'refer_from' => $refer_from,
    					'refer_date' => $refer_date,
    					'diag_result' => $diag_result,
    					'sponsor' => $sponsor,
    					'status' => $status,
    					'deleted' => -1,
    					'center_id' => $center_id,
    					'neph_id' => $neph_incharge,
    					'doc_id' => $doc_incharge,
    					'action' => "edited",
    					'action_by' => $staff_who_change,
    					'action_date' => $time_stamp,
    					'medical_history_id' => $medical_history_id
    			);
    
    				
    			$this->ndo_model->add_pat_history($data);
    				
    			if($sponsor != "Not Sponsored" && $diag_result != "infection"){
    				if($status != "on treatment"){
    						
    					$result = $this->ndo_model->get_all_running_machine($center_id);
    					$data['machine'] = $result['row'];
    					$i=1;
    					foreach($data['machine'] as $mrow):
    					$result = $this->ndo_model->get_all_shift($center_id);
    					$data['shift'] = $result['row'];
    					$j=1;
    					foreach($data['shift'] as $srow):
    					$slot_id = $this->input->post('s'.$j.'s'.$i.'sun');
    					if($slot_id != ""){
    						$data = array(
    								'slot_id' => $slot_id,
    								'shift_id' => $srow->shift_id,
    								'slot' => "slot".$i,
    								'day' => "Sunday",
    								'pat_id' => $pat_id,
    								'center_id' => $center_id
    						);
    						$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
    						$status = "on treatment";
    						$data = array(
    								'status' => $status
    						);
    						$this->ndo_model->update_pat_history($data, $history_id);
    					}
    					$j++; endforeach;
    					$i++; endforeach;
    
    					$result = $this->ndo_model->get_all_running_machine($center_id);
    					$data['machine'] = $result['row'];
    					$i=1;
    					foreach($data['machine'] as $mrow):
    					$result = $this->ndo_model->get_all_shift($center_id);
    					$data['shift'] = $result['row'];
    					$j=1;
    					foreach($data['shift'] as $srow):
    					$slot_id = $this->input->post('s'.$j.'s'.$i.'mon');
    					if($slot_id != ""){
    						$data = array(
    								'slot_id' => $slot_id,
    								'shift_id' => $srow->shift_id,
    								'slot' => "slot".$i,
    								'day' => "Monday",
    								'pat_id' => $pat_id,
    								'center_id' => $center_id
    						);
    						$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
    						$status = "on treatment";
    						$data = array(
    								'status' => $status
    						);
    						$this->ndo_model->update_pat_history($data, $history_id);
    					}
    					$j++; endforeach;
    					$i++; endforeach;
    
    					$result = $this->ndo_model->get_all_running_machine($center_id);
    					$data['machine'] = $result['row'];
    					$i=1;
    					foreach($data['machine'] as $mrow):
    					$result = $this->ndo_model->get_all_shift($center_id);
    					$data['shift'] = $result['row'];
    					$j=1;
    					foreach($data['shift'] as $srow):
    					$slot_id = $this->input->post('s'.$j.'s'.$i.'tue');
    					if($slot_id != ""){
    						$data = array(
    								'slot_id' => $slot_id,
    								'shift_id' => $srow->shift_id,
    								'slot' => "slot".$i,
    								'day' => "Tuesday",
    								'pat_id' => $pat_id,
    								'center_id' => $center_id
    						);
    						$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
    						$status = "on treatment";
    						$data = array(
    								'status' => $status
    						);
    						$this->ndo_model->update_pat_history($data, $history_id);
    					}
    					$j++; endforeach;
    					$i++; endforeach;
    
    					$result = $this->ndo_model->get_all_running_machine($center_id);
    					$data['machine'] = $result['row'];
    					$i=1;
    					foreach($data['machine'] as $mrow):
    					$result = $this->ndo_model->get_all_shift($center_id);
    					$data['shift'] = $result['row'];
    					$j=1;
    					foreach($data['shift'] as $srow):
    					$slot_id = $this->input->post('s'.$j.'s'.$i.'wed');
    					if($slot_id != ""){
    						$data = array(
    								'slot_id' => $slot_id,
    								'shift_id' => $srow->shift_id,
    								'slot' => "slot".$i,
    								'day' => "Wednesday",
    								'pat_id' => $pat_id,
    								'center_id' => $center_id
    						);
    						$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
    						$status = "on treatment";
    						$data = array(
    								'status' => $status
    						);
    						$this->ndo_model->update_pat_history($data, $history_id);
    					}
    					$j++; endforeach;
    					$i++; endforeach;
    
    					$result = $this->ndo_model->get_all_running_machine($center_id);
    					$data['machine'] = $result['row'];
    					$i=1;
    					foreach($data['machine'] as $mrow):
    					$result = $this->ndo_model->get_all_shift($center_id);
    					$data['shift'] = $result['row'];
    					$j=1;
    					foreach($data['shift'] as $srow):
    					$slot_id = $this->input->post('s'.$j.'s'.$i.'thu');
    					if($slot_id != ""){
    						$data = array(
    								'slot_id' => $slot_id,
    								'shift_id' => $srow->shift_id,
    								'slot' => "slot".$i,
    								'day' => "Thursday",
    								'pat_id' => $pat_id,
    								'center_id' => $center_id
    						);
    						$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
    						$status = "on treatment";
    						$data = array(
    								'status' => $status
    						);
    						$this->ndo_model->update_pat_history($data, $history_id);
    					}
    					$j++; endforeach;
    					$i++; endforeach;
    
    					$result = $this->ndo_model->get_all_running_machine($center_id);
    					$data['machine'] = $result['row'];
    					$i=1;
    					foreach($data['machine'] as $mrow):
    					$result = $this->ndo_model->get_all_shift($center_id);
    					$data['shift'] = $result['row'];
    					$j=1;
    					foreach($data['shift'] as $srow):
    					$slot_id = $this->input->post('s'.$j.'s'.$i.'fri');
    					if($slot_id != ""){
    						$data = array(
    								'slot_id' => $slot_id,
    								'shift_id' => $srow->shift_id,
    								'slot' => "slot".$i,
    								'day' => "Friday",
    								'pat_id' => $pat_id,
    								'center_id' => $center_id
    						);
    						$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
    						$status = "on treatment";
    						$data = array(
    								'status' => $status
    						);
    						$this->ndo_model->update_pat_history($data, $history_id);
    					}
    					$j++; endforeach;
    					$i++; endforeach;
    
    					$result = $this->ndo_model->get_all_running_machine($center_id);
    					$data['machine'] = $result['row'];
    					$i=1;
    					foreach($data['machine'] as $mrow):
    					$result = $this->ndo_model->get_all_shift($center_id);
    					$data['shift'] = $result['row'];
    					$j=1;
    					foreach($data['shift'] as $srow):
    					$slot_id = $this->input->post('s'.$j.'s'.$i.'sat');
    					if($slot_id != ""){
    						$data = array(
    								'slot_id' => $slot_id,
    								'shift_id' => $srow->shift_id,
    								'slot' => "slot".$i,
    								'day' => "Saturday",
    								'pat_id' => $pat_id,
    								'center_id' => $center_id
    						);
    						$this->ndo_model->add_pat_sch($data, $slot_id, $center_id);
    						$status = "on treatment";
    						$data = array(
    								'status' => $status
    						);
    						$this->ndo_model->update_pat_history($data, $history_id);
    					}
    					$j++; endforeach;
    					$i++; endforeach;
    				}
    
    			}else{
    				$this->ndo_model->delete_pat_sch($pat_id);
    				$status = "waiting list";
    				$data = array(
    						'status' => $status
    				);
    				$this->ndo_model->update_pat_history($data, $history_id);
    
    			}
    
    				
    			$this->ndo_model->add_pat_med_problem($problem1, $problem2, $problem3, $problem4, $problem5, $problem6, $problem7, $problem8, $problem9, $summary);
    				
    				
    			$this->ndo_model->add_pat_medication($medic1, $medic2, $medic3, $medic4, $medic5, $medic6, $medic7, $medic8, $medic9, $medic10, $medic11, $medic12, $plan);
    				
    				
    			$this->session->set_flashdata('success_msg', ' Changes Saved Successfully.');
    			redirect('staff/view_pat_data/'.$pat_id);
    		}else{
    			$this->session->set_flashdata('error_msg', ' The Inserted IC already Assigned to another Patient !.');
    			redirect('staff/view_pat_data/'.$pat_id);
    		}
    	}else{
    			
    		$this->session->set_flashdata('error_msg', ' Name and IC Fields Can not Left Empty!.');
    		redirect('staff/view_pat_data/'.$pat_id);
    			
    	}
    
    }
    
    function get_n_review()
    {
    
    
    	$review_id = $this->input->post('review_id');
    
    	$this->load->model('ndo_model');
    
    
    	$res = $this->ndo_model->get_all_neph_d();
    	$data['neph_d'] = $res['row'];
    	$res = $this->ndo_model->get_specific_n_review($review_id);
    	$results['specific_n_review'] = $res;
    
    	foreach($results['specific_n_review'] as $row):{
    		foreach($data['neph_d'] as $nrow):{
    			if($row->neph_id == $nrow->neph_id){
    				$neph_name = $nrow->name;
    			}
    
    		} endforeach;
    		 
    	} endforeach;
    
    
    	$results['neph_name'] = $neph_name;
    	echo json_encode($results);
    
    }
    
    function get_d_review()
    {
    
    
    	$review_id = $this->input->post('review_id');
    
    	$this->load->model('ndo_model');
    
    
    	$res = $this->ndo_model->get_all_doc_d();
    	$data['doc_d'] = $res['row'];
    	$res = $this->ndo_model->get_specific_d_review($review_id);
    	$results['specific_d_review'] = $res;
    
    	foreach($results['specific_d_review'] as $row):{
    		foreach($data['doc_d'] as $drow):{
    			if($row->doc_id == $drow->doc_id){
    				$doc_name = $drow->name;
    			}
    
    		} endforeach;
    		 
    	} endforeach;
    
    
    	$results['doc_name'] = $doc_name;
    	echo json_encode($results);
    
    }
	
	public function delete_profile_pic($pat_id){
		$path = "";
		$this->load->model('ndo_model');
		$result = $this->ndo_model->get_pat_info_id($pat_id);
		$data['info'] = $result;
		
		foreach($data['info'] as $result):{
			
			$path = $result->pic_path;
				
			
			
		}
		endforeach;
		
		unlink($path);
		$data = array(
			'pic_path' => "",
			'pic_name' =>""
			
				);
		$this->ndo_model->update_pat_info($data, $pat_id);
			
		 $this->session->set_flashdata('success_msg', ' Profile Picture Deleted Successfully.');
			redirect('staff/view_pat/'.$pat_id);
	
    }
	
	public function add_img($pat_id){
		
		$path2 = './pat_doc/pic/'.$pat_id;
		
		
     if($_FILES['med_img']['error'] != 4){
			
			 $config['upload_path'] = $path2;
			 $config['file_name'] = "img.jpg";
             $config['allowed_types'] = '*';
             $config['max_size'] = 0;

             $this->upload->initialize($config);


             $this->upload->do_upload('med_img');
		     $data = $this->upload->data();

             $img_path = $data['full_path'];
	   		 $img_file_name = $data['file_name'];
			 
			 $img_date = $this->input->post('img_date');
			 $img_comment = $this->input->post('img_comment');
			 $data = array(
			'img_name' => $img_file_name,
			'img_path' => $img_path,
			'img_date' => $img_date,
			'img_comment' => $img_comment,
			'pat_id' => $pat_id
			
				);
			$this->load->model('ndo_model');
			$this->ndo_model->add_medical_img($data);
			
			}
			
			 $this->session->set_flashdata('success_msg', 'Image is Added Successfully.');
			redirect('staff/view_pat/'.$pat_id);
	
    }
	
	public function delete_img($id, $pat_id){
		$path = "";
		$this->load->model('ndo_model');
		$result = $this->ndo_model->get_pat_pic($pat_id);
		$data['pic'] = $result;
		
		foreach($data['pic'] as $result):{
			if($result->id == $id){
			$path = $result->img_path;
				
			}
			
		}
		endforeach;
		
		unlink($path);
		$this->ndo_model->delete_pic($id);
			
		 $this->session->set_flashdata('success_msg', ' Image is Deleted Successfully.');
			redirect('staff/view_pat/'.$pat_id);
	
    }
    
    public function add_diag_letter($pat_id){
    	
    	$staff_id = $this->session->userdata('staff_id');
    	$path1 = './pat_doc/letter/'.$pat_id;
    
    
    	if($_FILES['diag_letter']['error'] != 4){
    			
    		$config['upload_path'] = $path1;
    		$config['allowed_types'] = '*';
    		$config['max_size'] = 0;
    		$config['overwrite'] = FALSE;
    
    		$this->upload->initialize($config);
    
    
    		$this->upload->do_upload('diag_letter');
    		$data = $this->upload->data();
    
    		$letter_path = $data['full_path'];
    		$letter_name = $data['file_name'];
    
    		$data = array(
    				'type' => "diag",
					'upload_date' => date('Y-m-d H:i:s'),
					'uploaded_by' => $staff_id,
					'path' => $letter_path,
					'name' => $letter_name,
					'pat_id' => $pat_id
    					
    		);
    		$this->load->model('ndo_model');
    		$this->ndo_model->add_letter($data);
    			
    	}
    		
    	$this->session->set_flashdata('success_msg', 'Diagnosis Letter is Added Successfully.');
    	redirect('staff/view_pat/'.$pat_id);
    
    }
    
    public function add_sponsor_letter($pat_id){
    	 
    	$staff_id = $this->session->userdata('staff_id');
    	$path1 = './pat_doc/letter/'.$pat_id;
    
    
    	if($_FILES['sponsor_letter']['error'] != 4){
    		 
    		$config['upload_path'] = $path1;
    		$config['allowed_types'] = '*';
    		$config['max_size'] = 0;
    		$config['overwrite'] = FALSE;
    
    		$this->upload->initialize($config);
    
    
    		$this->upload->do_upload('sponsor_letter');
    		$data = $this->upload->data();
    
    		$letter_path = $data['full_path'];
    		$letter_name = $data['file_name'];
    
    		$data = array(
    				'type' => "spon",
    				'upload_date' => date('Y-m-d H:i:s'),
    				'uploaded_by' => $staff_id,
    				'path' => $letter_path,
    				'name' => $letter_name,
    				'pat_id' => $pat_id
    					
    		);
    		$this->load->model('ndo_model');
    		$this->ndo_model->add_letter($data);
    		 
    	}
    
    	$this->session->set_flashdata('success_msg', 'Sponsorship Letter is Added Successfully.');
    	redirect('staff/view_pat/'.$pat_id);
    
    }
    
    public function add_refer_letter($pat_id){
    
    	$staff_id = $this->session->userdata('staff_id');
    	$path1 = './pat_doc/letter/'.$pat_id;
    
    
    	if($_FILES['refer_letter']['error'] != 4){
    		 
    		$config['upload_path'] = $path1;
    		$config['allowed_types'] = '*';
    		$config['max_size'] = 0;
    		$config['overwrite'] = FALSE;
    
    		$this->upload->initialize($config);
    
    
    		$this->upload->do_upload('refer_letter');
    		$data = $this->upload->data();
    
    		$letter_path = $data['full_path'];
    		$letter_name = $data['file_name'];
    
    		$data = array(
    				'type' => "ref",
    				'upload_date' => date('Y-m-d H:i:s'),
    				'uploaded_by' => $staff_id,
    				'path' => $letter_path,
    				'name' => $letter_name,
    				'pat_id' => $pat_id
    					
    		);
    		$this->load->model('ndo_model');
    		$this->ndo_model->add_letter($data);
    		 
    	}
    
    	$this->session->set_flashdata('success_msg', 'Reference Letter is Added Successfully.');
    	redirect('staff/view_pat/'.$pat_id);
    
    }
	
	public function download_letter($id){
		$this->load->model('ndo_model');
		$result = $this->ndo_model->get_single_letter($id);
		$data['letter'] = $result;
		foreach($data['letter'] as $result):{
			
			$letter_path = $result->path;
			$letter_name = $result->name;
			
		} endforeach;

		$data = file_get_contents($letter_path);
		force_download($letter_name, $data);


	}
	
 public function delete_letter($id, $pat_id){
 	
		$this->load->model('ndo_model');
		$result = $this->ndo_model->get_single_letter($id);
		$data['letter'] = $result;
		
		foreach($data['letter'] as $result):{
			
			$path = $result->path;
			
		}
		endforeach;
		
		unlink($path);
		$this->ndo_model->delete_letter($id);
			
		 $this->session->set_flashdata('success_msg', ' Letter is Deleted Successfully.');
			redirect('staff/view_pat/'.$pat_id);
	
    }
	
	public function delete_pat_sch($pat_id){
		
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		
		$this->load->library('create_id');
		
		$history_id = $this->create_id->get_id();
		
		$this->ndo_model->delete_pat_sch($pat_id);
		
		$result = $this->ndo_model->get_pat_info_id($pat_id);
		$data['patient'] = $result;
		
		foreach($data['patient'] as $row):{
			$medical_history_id = $row->medical_history_id;
			$pat_ic = $row->pat_ic;
			$name = $row->name;
			$age = $row->age;
			$gender = $row->gender;
			$email = $row->email;
			$phoneNo = $row->phoneNo;
			$mobileNo = $row->mobileNo;
			$address = $row->address;
			$nk_name = $row->nk_name;
			$nk_job = $row->nk_job;
			$nk_relation = $row->nk_relation;
			$nk_email = $row->nk_email;
			$nk_phoneNo = $row->nk_phoneNo;
			$nk_mobileNo = $row->nk_phoneNo;
			$nk_address = $row->nk_address;
			$refer_from = $row->refer_from;
			$refer_date = $row->refer_date;
			$diag_result = $row->diag_result;
			$sponsor = $row->sponsor;
			$deleted = $row->deleted;
			$center_id = $row->center_id;
			$neph_incharge = $row->neph_id;
			$doc_incharge = $row->doc_id;
		}endforeach;
		
		$data = array(
				'history_id' => $history_id
		);
		
		$this->ndo_model->update_pat_info($data, $pat_id);
		
		$data = array(
				'history_id' => $history_id,
				'pat_ic' => $pat_ic,
				'pat_id' => $pat_id,
				'name' => $name,
				'age' => $age,
				'gender' => $gender,
				'email' => $email,
				'phoneNo' => $phoneNo,
				'mobileNo' => $mobileNo,
				'address' => $address,
				'nk_name' => $nk_name,
				'nk_job' => $nk_job,
				'nk_relation' => $nk_relation,
				'nk_email' => $nk_email,
				'nk_phoneNo' => $nk_phoneNo,
				'nk_mobileNo' => $nk_mobileNo,
				'nk_address' => $nk_address,
				'refer_from' => $refer_from,
				'refer_date' => $refer_date,
				'diag_result' => $diag_result,
				'sponsor' => $sponsor,
				'status' => "waiting list",
				'deleted' => $deleted,
				'center_id' => $center_id,
				'neph_id' => $neph_incharge,
				'doc_id' => $doc_incharge,
				'action' => "edited",
				'action_by' => $staff_id,
				'action_date' => date('Y-m-d H:i:s'),
				'action_reason' => "shcedule deleted",
				'medical_history_id' => $medical_history_id
		);
		
		$this->ndo_model->add_pat_history($data);
		$this->session->set_flashdata('success_msg', ' Patient Schedule is Deleted Successfully.');
		redirect('staff/view_pat/'.$pat_id);
		
		}
		
		public function add_sec_neph($pat_id){
		
			$this->load->model('ndo_model');
			
			foreach ($this->input->post('neph_sec') as $sec_neph):
				$data = array(
						'neph_id' => $sec_neph,
						'pat_id' => $pat_id
				);
				$this->ndo_model->add_sec_neph($data);
			endforeach;
				
			$this->session->set_flashdata('success_msg', 'Secondary Nephrologist Added Successfully.');
			redirect('staff/view_pat/'.$pat_id);
		
		}
		
		public function add_sec_doc($pat_id){
		
			$this->load->model('ndo_model');
				
			foreach ($this->input->post('doc_sec') as $sec_doc):
				$data = array(
						'doc_id' => $sec_doc,
						'pat_id' => $pat_id
				);
				$this->ndo_model->add_sec_doc($data);
			endforeach;
		
			$this->session->set_flashdata('success_msg', 'Secondary Doctor Added Successfully.');
			redirect('staff/view_pat/'.$pat_id);
		
		}
		
		public function delete_sec_neph($pat_id, $id){
			
			$this->load->model('ndo_model');
			
			$this->ndo_model->delete_sec_neph($id);
				
			$this->session->set_flashdata('success_msg', ' Secondary Nephrologist Deleted Successfully.');
			redirect('staff/view_pat/'.$pat_id);
		
		}
		
		public function delete_sec_doc($pat_id, $id){
				
			$this->load->model('ndo_model');
				
			$this->ndo_model->delete_sec_doc($id);
		
			$this->session->set_flashdata('success_msg', ' Secondary Doctor Deleted Successfully.');
			redirect('staff/view_pat/'.$pat_id);
		
		}
	
	public function delete_pat($id){
		$staff_id = $this->session->userdata('staff_id');
		$datestring = "%Y-%m-%d";
        $time = time();
		$current_date = mdate($datestring, $time);
		
		$this->load->library('create_id');
		
		$history_id = $this->create_id->get_id();
		
		$datestring = "%H:%i:%s";
        $time = time();
		$current_time = mdate($datestring, $time);
		
		$reason = $this->input->post('reason');
		
		$this->load->model('ndo_model');
		
		$this->ndo_model->delete_pat_sch($id);
		
		$result = $this->ndo_model->get_pat_info_id($id);
		$data['patient'] = $result;
		
		foreach($data['patient'] as $row):{
			$medical_history_id = $row->medical_history_id;
			$pat_ic = $row->pat_ic;
			$name = $row->name;
			$age = $row->age;
			$gender = $row->gender;
			$email = $row->email;
			$phoneNo = $row->phoneNo;
			$mobileNo = $row->mobileNo;
			$address = $row->address;
			$nk_name = $row->nk_name;
			$nk_job = $row->nk_job;
			$nk_relation = $row->nk_relation;
			$nk_email = $row->nk_email;
			$nk_phoneNo = $row->nk_phoneNo;
			$nk_mobileNo = $row->nk_phoneNo;
			$nk_address = $row->nk_address;
			$refer_from = $row->refer_from;
			$refer_date = $row->refer_date;
			$diag_result = $row->diag_result;
			$sponsor = $row->sponsor;
			$center_id = $row->center_id;
			$neph_incharge = $row->neph_id;
			$doc_incharge = $row->doc_id;
		}endforeach;
		
		$data = array(
				'history_id' => $history_id
		);
		
		$this->ndo_model->update_pat_info($data, $id);
		
		$data = array(
			'history_id' => $history_id,
			'pat_ic' => $pat_ic,
			'pat_id' => $id,
			'name' => $name,
			'age' => $age,
			'gender' => $gender,
			'email' => $email,
			'phoneNo' => $phoneNo,
			'mobileNo' => $mobileNo,
			'address' => $address,
			'nk_name' => $nk_name,
			'nk_job' => $nk_job,
			'nk_relation' => $nk_relation,
			'nk_email' => $nk_email,
			'nk_phoneNo' => $nk_phoneNo,
			'nk_mobileNo' => $nk_mobileNo,
			'nk_address' => $nk_address,
			'refer_from' => $refer_from,
			'refer_date' => $refer_date,
			'diag_result' => $diag_result,
			'sponsor' => $sponsor,
			'status' => "waiting list",
			'deleted' => 1,
			'center_id' => $center_id,
			'neph_id' => $neph_incharge,
			'doc_id' => $doc_incharge,
			'action' => "treatment terminated",
			'action_by' => $staff_id,
			'action_date' => date('Y-m-d H:i:s'),
			'action_reason' =>$reason,
			'medical_history_id' => $medical_history_id
				);
				
			$this->ndo_model->add_pat_history($data);
		$this->session->set_flashdata('success_msg', ' Patient Is Deleted Successfully.');
			redirect('staff/pat_list');
		
		
	}
	
	
	
	//.........................................................
	
	
	//dialysis record 
	
	public function d_record($pat_id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		
		$data['name'] = $name;
		$data['role'] = $role;
		
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		
		$result = $this->ndo_model->get_all_center_d();
		$data['center'] = $result['row'];
		
		$data['pat_name'] = $this->ndo_model->get_pat_name($pat_id);
		$result = $this->ndo_model->get_pat_record($pat_id);
		$data['record'] = $result['row'];
		$data['number_row'] = $result['num_row'];
		$result = $this->ndo_model->get_all_staff_d();
		$data['staff'] = $result['row'];
		$result = $this->ndo_model->get_all_machine($center_id);
		$data['machine'] = $result['row'];
		$result = $this->ndo_model->get_all_machine_all_centers();
		$data['all_machine'] = $result['row'];
		$data['id']= $pat_id;
		
		$result = $this->ndo_model->get_all_neph_d();
		$data['neph_d'] = $result['row'];
		$result = $this->ndo_model->get_all_doc_d();
		$data['doc_d'] = $result['row'];
		$result = $this->ndo_model->get_all_n_review($pat_id);
		$data['n_review'] = $result['row'];
		$data['n_review_num_row'] = $result['num_row'];
		$result = $this->ndo_model->get_last_n_review($pat_id);
		$data['last_n_review'] = $result;
		$result = $this->ndo_model->get_all_d_review($pat_id);
		$data['d_review'] = $result['row'];
		$data['d_review_num_row'] = $result['num_row'];
		$result = $this->ndo_model->get_last_d_review($pat_id);
		$data['last_d_review'] = $result;
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/pat_record', $data);
	  $this->load->view('footer');
	
    }
	
	public function add_record_page($pat_id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		
		$data['name'] = $name;
		$data['role'] = $role;
		
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		
		$data['observation_no']= $this->input->post('observeation_no');
		
		$center_id =  $this->ndo_model->get_center_id($staff_id);

		$data['pat_name'] = $this->ndo_model->get_pat_name($pat_id);
		$result = $this->ndo_model->get_all_staff_of_center($center_id);
		$data['staff'] = $result['row'];
		$data['id']= $pat_id;
		
		$result = $this->ndo_model->get_all_running_machine($center_id);
		$data['machine'] = $result['row'];
		
		$datestring = "%Y-%m-%d";
        $time = time();
		$date = mdate($datestring, $time);
		$data['date']= $date;
		
		$result = $this->ndo_model->get_all_neph_d();
		$data['neph_d'] = $result['row'];
		$result = $this->ndo_model->get_all_doc_d();
		$data['doc_d'] = $result['row'];
		$result = $this->ndo_model->get_all_n_review($pat_id);
		$data['n_review'] = $result['row'];
		$data['n_review_num_row'] = $result['num_row'];
		$result = $this->ndo_model->get_last_n_review($pat_id);
		$data['last_n_review'] = $result;
		$result = $this->ndo_model->get_all_d_review($pat_id);
		$data['d_review'] = $result['row'];
		$data['d_review_num_row'] = $result['num_row'];
		$result = $this->ndo_model->get_last_d_review($pat_id);
		$data['last_d_review'] = $result;
		
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/add_record', $data);
	  $this->load->view('footer');
	
    }
	
	public function add_record($pat_id, $observation_no){
		$this->load->model('ndo_model');
		$this->load->library('create_id');
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
			$center_id = $row->center_id;
		}endforeach;
		
	 $record_id = $this->create_id->get_id();

	$data = array(
			'record_id' => $record_id,
			'pat_id' => $pat_id,
			'date' => $this->input->post('treat_date'),
			'arrival_time' => $this->input->post('arrive_time'),
			'leaving_time' => $this->input->post('leave_time'),
			'starting_staff' => $this->input->post('starting_staff'),
			'ending_staff' => $this->input->post('ending_staff'),
			'starting_time' => $this->input->post('starting_time'),
			'ending_time' => $this->input->post('ending_time'),
			'type' => $this->input->post('type'),
			'machine_id' => $this->input->post('machine'),
			'center_id' => $center_id,
			'remark' => $this->input->post('remark')
				);	
				
	$this->ndo_model->add_record($data);
	
	$data = array(
			'm_1' => $this->input->post('m1'),
			'm_2' => $this->input->post('m2'),
			'm_3' => $this->input->post('m3'),
			'm_4' => $this->input->post('m4'),
			'i_1' => $this->input->post('i1'),
			'i_2' => $this->input->post('i2'),
			'i_3' => $this->input->post('i3'),
			'i_4' => $this->input->post('i4'),
			'dry_weight' => $this->input->post('dry_wehight'),
			'dialyzer' => $this->input->post('dailyzer'),
			'usage' => $this->input->post('usage'),
			'loading' => $this->input->post('loading'),
			'dialysate' => $this->input->post('dialysate'),
			'uf_goal' => $this->input->post('uf_goal'),
			'epo' => $this->input->post('epo'),
			'size' => $this->input->post('size'),
			'hourly' => $this->input->post('hourly'),
			'td' => $this->input->post('td_min'),
			'qb' => $this->input->post('qb'),
			'qd' => $this->input->post('qd'),
			'bicarbonate' => $this->input->post('bicarbonate'),
			'record_id' => $record_id
				);	
				
	$this->ndo_model->add_hd_treatment($data);
	
	$data = array(
			'sob' => $this->input->post('sob'),
			'bo' => $this->input->post('fobo'),
			'et' => $this->input->post('ef'),
			'thrill' => $this->input->post('thrill'),
			'inflamation' => $this->input->post('inflamation'),
			'haematoma' => $this->input->post('haematoma'),
			'brach' => $this->input->post('brach'),
			'rad' => $this->input->post('rad'),
			'pc' => $this->input->post('pc'),
			'fc' => $this->input->post('fc'),
			'sub' => $this->input->post('sub'),
			'ijv' => $this->input->post('ijv'),
			'femoral' => $this->input->post('femoral'),
			't_sub' => $this->input->post('t_sub'),
			'record_id' => $record_id
				);	
				
	$this->ndo_model->add_pre_hd_assessment($data);
	
	$data = array(
			'pre_pb' => $this->input->post('b/p_pre'),
			'pre_pulse' => $this->input->post('pulse_pre'),
			'pre_temp' => $this->input->post('temp_pre'),
			'pre_weight' => $this->input->post('weight_pre'),
			'post_pb' => $this->input->post('b/p_post'),
			'post_pulse' => $this->input->post('pulse_post'),
			'post_temp' => $this->input->post('temp_post'),
			'post_weight' => $this->input->post('weight_post'),
			'record_id' => $record_id
				);	
				
	$this->ndo_model->add_observation($data);
	
	for($i=1; $i<=$observation_no; $i++){
		$data = array(
			'record_id' => $record_id,
			'starting_time' => $this->input->post('time_start'.$i),
			'bp' => $this->input->post('bp'.$i),
			'pulse' => $this->input->post('pulse'.$i),
			'vp' => $this->input->post('vp'.$i),
			'tmp' => $this->input->post('tmp'.$i),
			'bf' => $this->input->post('b/f'.$i),
			'hep' => $this->input->post('hep'.$i),
			'ns' => $this->input->post('n/s'.$i)
				);
		$this->ndo_model->add_observation_detail($data);
	}
	
	$data = array(
			'comfortable' => $this->input->post('comfortable'),
			'weak' => $this->input->post('weak'),
			'giddiness' => $this->input->post('giddiness'),
			'hypotension' => $this->input->post('hd_hypo'),
			'hypertension' => $this->input->post('hd_hyper'),
			'sob' => $this->input->post('post_hd_sob'),
			'fistula' => $this->input->post('fistula_thrill'),
			'action' => $this->input->post('post_hd_action'),
			'record_id' => $record_id
				);	
				
	$this->ndo_model->add_post_hd_assessment($data);
	
	$data = array(
			'chills_rigor' => $this->input->post('cr'),
			'hypotension' => $this->input->post('critical_hypo'),
			'hypertension' => $this->input->post('critical_hyper'),
			'cramp' => $this->input->post('cramp'),
			'chest_pain' => $this->input->post('cp'),
			'vomit' => $this->input->post('vomit'),
			'volume' => $this->input->post('critical_blood_v'),
			'cause' => $this->input->post('cause'),
			'other' => $this->input->post('critical_other'),
			'ward' => $this->input->post('ward'),
			'action' => $this->input->post('critical_action'),
			'record_id' => $record_id
				);	
				
	$this->ndo_model->add_critical_incident($data);
	
	$data = array(
			'blood_volume' => $this->input->post('perform_blood_v'),
			'machine_model' => $this->input->post('machine_model'),
			'residual_fbv' => $this->input->post('r_fbv'),
			'percentage_fbv' => $this->input->post('p_fbv'),
			'prescribe_ktv' => $this->input->post('p_ktv'),
			'delivered_ktv' => $this->input->post('d_ktv'),
			'record_id' => $record_id
				);	
				
	$this->ndo_model->add_performance_measurement($data);
		
	
	
    //$this->d_record($ic, $success_msg, NULL);
	$this->session->set_flashdata('success_msg', 'Record Is Added Successfully.');
	redirect('staff/view_record/'.$record_id.'/'.$pat_id);	
		
   
	
    }
	
	public function view_record($id, $pat_id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		
		$data['name'] = $name;
		$data['role'] = $role;
		
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		$result = $this->ndo_model->get_pat_record($pat_id);
		$data['pat_name'] = $this->ndo_model->get_pat_name($pat_id);
		$result = $this->ndo_model->get_all_staff_of_center($center_id);
		$data['staff'] = $result['row'];
		$result = $this->ndo_model->get_record_detail($id);
		$data['record'] = $result['row'];
		$result = $this->ndo_model->get_hd_treatment($id);
		$data['treatment'] = $result['row'];
		$result = $this->ndo_model->get_pre_assessment($id);
		$data['pre_assessment'] = $result['row'];
		$result = $this->ndo_model->get_observation($id);
		$data['observation'] = $result['row'];
		$result = $this->ndo_model->get_observation_detail($id);
		$data['ob_detail'] = $result['row'];
		$result = $this->ndo_model->get_post_assessment($id);
		$data['post_assessment'] = $result['row'];
		$result = $this->ndo_model->get_critical_incident($id);
		$data['critical'] = $result['row'];
		$result = $this->ndo_model->get_performance_measurement($id);
		$data['perform'] = $result['row'];
		$data['id']= $pat_id;
		
		$result = $this->ndo_model->get_all_neph_d();
		$data['neph_d'] = $result['row'];
		$result = $this->ndo_model->get_all_doc_d();
		$data['doc_d'] = $result['row'];
		$result = $this->ndo_model->get_all_n_review($pat_id);
		$data['n_review'] = $result['row'];
		$data['n_review_num_row'] = $result['num_row'];
		$result = $this->ndo_model->get_last_n_review($pat_id);
		$data['last_n_review'] = $result;
		$result = $this->ndo_model->get_all_d_review($pat_id);
		$data['d_review'] = $result['row'];
		$data['d_review_num_row'] = $result['num_row'];
		$result = $this->ndo_model->get_last_d_review($pat_id);
		$data['last_d_review'] = $result;
		
		$data['center_id']= $center_id;
		
		$result = $this->ndo_model->get_all_machine_all_centers();
		$data['all_machine'] = $result['row'];
		
		$result = $this->ndo_model->get_all_machine($center_id);
		$data['machine'] = $result['row'];
		
		$result = $this->ndo_model->get_all_center_d();
		$data['center'] = $result['row'];
		
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/view_record', $data);
	  $this->load->view('footer');
	
    }
	
	public function edit_record($record_id, $pat_id, $observation_no){
	
	$data = array(
			'date' => $this->input->post('treat_date'),
			'arrival_time' => $this->input->post('arrive_time'),
			'leaving_time' => $this->input->post('leave_time'),
			'starting_staff' => $this->input->post('starting_staff'),
			'ending_staff' => $this->input->post('ending_staff'),
			'starting_time' => $this->input->post('starting_time'),
			'ending_time' => $this->input->post('ending_time'),
			'type' => $this->input->post('type'),
			'machine_id' => $this->input->post('machine'),
			'remark' => $this->input->post('remark')
				);	
				
	$this->load->model('ndo_model');
	$this->ndo_model->edit_record($data, $record_id);
	
	$data = array(
			'm_1' => $this->input->post('m1'),
			'm_2' => $this->input->post('m2'),
			'm_3' => $this->input->post('m3'),
			'm_4' => $this->input->post('m4'),
			'i_1' => $this->input->post('i1'),
			'i_2' => $this->input->post('i2'),
			'i_3' => $this->input->post('i3'),
			'i_4' => $this->input->post('i4'),
			'dry_weight' => $this->input->post('dry_wehight'),
			'dialyzer' => $this->input->post('dailyzer'),
			'usage' => $this->input->post('usage'),
			'loading' => $this->input->post('loading'),
			'dialysate' => $this->input->post('dialysate'),
			'uf_goal' => $this->input->post('uf_goal'),
			'epo' => $this->input->post('epo'),
			'size' => $this->input->post('size'),
			'hourly' => $this->input->post('hourly'),
			'td' => $this->input->post('td_min'),
			'qb' => $this->input->post('qb'),
			'qd' => $this->input->post('qd'),
			'bicarbonate' => $this->input->post('bicarbonate')
				);
				
	$this->ndo_model->edit_treatment($data, $record_id);
	
	$data = array(
			'sob' => $this->input->post('sob'),
			'bo' => $this->input->post('fobo'),
			'et' => $this->input->post('ef'),
			'thrill' => $this->input->post('thrill'),
			'inflamation' => $this->input->post('inflamation'),
			'haematoma' => $this->input->post('haematoma'),
			'brach' => $this->input->post('brach'),
			'rad' => $this->input->post('rad'),
			'pc' => $this->input->post('pc'),
			'fc' => $this->input->post('fc'),
			'sub' => $this->input->post('sub'),
			'ijv' => $this->input->post('ijv'),
			'femoral' => $this->input->post('femoral'),
			't_sub' => $this->input->post('t_sub')
				);	
				
	$this->ndo_model->edit_pre_assessment($data, $record_id);
	
	$data = array(
			'pre_pb' => $this->input->post('b/p_pre'),
			'pre_pulse' => $this->input->post('pulse_pre'),
			'pre_temp' => $this->input->post('temp_pre'),
			'pre_weight' => $this->input->post('weight_pre'),
			'post_pb' => $this->input->post('b/p_post'),
			'post_pulse' => $this->input->post('pulse_post'),
			'post_temp' => $this->input->post('temp_post'),
			'post_weight' => $this->input->post('weight_post')
				);	
				
	$this->ndo_model->edit_observation($data, $record_id);
	
	
	
	$this->ndo_model->delete_all_ob_detail($record_id);
	for($i=1; $i<=$observation_no; $i++){
		$data = array(
			'record_id' => $record_id,
			'starting_time' => $this->input->post('time_start'.$i),
			'bp' => $this->input->post('bp'.$i),
			'pulse' => $this->input->post('pulse'.$i),
			'vp' => $this->input->post('vp'.$i),
			'tmp' => $this->input->post('tmp'.$i),
			'bf' => $this->input->post('b/f'.$i),
			'hep' => $this->input->post('hep'.$i),
			'ns' => $this->input->post('n/s'.$i)
				);
		$this->ndo_model->add_observation_detail($data);
	}
	
	$data = array(
			'comfortable' => $this->input->post('comfortable'),
			'weak' => $this->input->post('weak'),
			'giddiness' => $this->input->post('giddiness'),
			'hypotension' => $this->input->post('hd_hypo'),
			'hypertension' => $this->input->post('hd_hyper'),
			'sob' => $this->input->post('post_hd_sob'),
			'fistula' => $this->input->post('fistula_thrill'),
			'action' => $this->input->post('post_hd_action')
				);	
				
	$this->ndo_model->edit_post_assessment($data, $record_id);
	
	$data = array(
			'chills_rigor' => $this->input->post('cr'),
			'hypotension' => $this->input->post('critical_hypo'),
			'hypertension' => $this->input->post('critical_hyper'),
			'cramp' => $this->input->post('cramp'),
			'chest_pain' => $this->input->post('cp'),
			'vomit' => $this->input->post('vomit'),
			'volume' => $this->input->post('critical_blood_v'),
			'cause' => $this->input->post('cause'),
			'other' => $this->input->post('critical_other'),
			'ward' => $this->input->post('ward'),
			'action' => $this->input->post('critical_action')
				);	
				
	$this->ndo_model->edit_critical_incident($data, $record_id);
	
	$data = array(
			'blood_volume' => $this->input->post('perform_blood_v'),
			'machine_model' => $this->input->post('machine_model'),
			'residual_fbv' => $this->input->post('r_fbv'),
			'percentage_fbv' => $this->input->post('p_fbv'),
			'prescribe_ktv' => $this->input->post('p_ktv'),
			'delivered_ktv' => $this->input->post('d_ktv')
				);	
				
	$this->ndo_model->edit_performance_measurement($data, $record_id);
	
    //$this->view_record($id, $ic);	
	redirect('staff/view_record/'.$record_id."/".$pat_id);		
      
	
    }
	
	public function add_ob_detail($record_id, $pat_id){
		$this->load->model('ndo_model');
		
		$data = array(
			'record_id' => $record_id,
			'starting_time' => $this->input->post('time_start'),
			'bp' => $this->input->post('bp'),
			'pulse' => $this->input->post('pulse'),
			'vp' => $this->input->post('vp'),
			'tmp' => $this->input->post('tmp'),
			'bf' => $this->input->post('b/f'),
			'hep' => $this->input->post('hep'),
			'ns' => $this->input->post('n/s')
				);
		$this->ndo_model->add_observation_detail($data);
		
		//$this->view_record($record_id, $ic);
		redirect('staff/view_record/'.$record_id."/".$pat_id);	
		
	}
	
	public function delete_ob_detail($record_id, $pat_id, $id){
		$this->load->model('ndo_model');
		$this->ndo_model->delete_ob_detail($id);
		//$this->view_record($record_id, $ic);
		redirect('staff/view_record/'.$record_id."/".$pat_id);
		
		
	}
	
	/*
	public function delete_record($id, $ic){
		$this->load->model('ndo_model');
		$this->ndo_model->delete_record($id);
		$success_msg = "PO Deleted Successfully.";
		$this->d_record($ic, $success_msg, NULL);
		
		
	}
	
	*/
	
	
	//................................................
	
	//single clinical and biochemisry summary
	
	public function view_summary($pat_id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		
		$data['name'] = $name;
		$data['role'] = $role;
		
		$data['pat_id'] = $pat_id;
		 $center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		 $data['pat_name'] = $this->ndo_model->get_pat_name($pat_id);
		 $result = $this->ndo_model->get_clinical_summary($pat_id);
		$data['clinical'] = $result['row'];
		$result = $this->ndo_model->get_clinical_xyz($pat_id);
		$data['clinical_xyz'] = $result['row'];
		
		$result = $this->ndo_model->get_all_neph_d();
		$data['neph_d'] = $result['row'];
		$result = $this->ndo_model->get_all_doc_d();
		$data['doc_d'] = $result['row'];
		$result = $this->ndo_model->get_all_n_review($pat_id);
		$data['n_review'] = $result['row'];
		$data['n_review_num_row'] = $result['num_row'];
		$result = $this->ndo_model->get_last_n_review($pat_id);
		$data['last_n_review'] = $result;
		$result = $this->ndo_model->get_all_d_review($pat_id);
		$data['d_review'] = $result['row'];
		$data['d_review_num_row'] = $result['num_row'];
		$result = $this->ndo_model->get_last_d_review($pat_id);
		$data['last_d_review'] = $result;
		
		
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/view_summary', $data);
	  $this->load->view('footer');
	
    }
	
	public function edit_summary($pat_id){
		$this->load->model('ndo_model');
		
		$data = array(
			'blood_group' => $this->input->post('blood_group'),
			'vascular_access' => $this->input->post('vascular_access'),
			'hepatitis_status' => $this->input->post('hepatitis_status'),
			'hep_titre' => $this->input->post('hep_titre'),
			'premorbid' => $this->input->post('premorbid')
				);
		$this->ndo_model->edit_clinical_summary($data, $pat_id);
		
		$this->session->set_flashdata('success_msg', ' Data was Saved Successfully.');
		
		redirect('staff/view_summary/'.$pat_id);	
		
	}
	
	public function add_xyz($pat_id){
		$this->load->model('ndo_model');
		
		$data = array(
			'pat_id' => $pat_id,
			'date' => $this->input->post('date'),
			'hb' => $this->input->post('hb'),
			'fer' => $this->input->post('fer'),
			'sat' => $this->input->post('sat'),
			'sr_iron' => $this->input->post('sr_iron'),
			'tibc' => $this->input->post('tibc'),
			'albumin' => $this->input->post('albumin'),
			'ipth' => $this->input->post('ipth'),
			'kt_v' => $this->input->post('kt_v'),
			'urr' => $this->input->post('urr'),
			'alp' => $this->input->post('alp'),
			'calcuim' => $this->input->post('calcium_bio'),
			'po4' => $this->input->post('po_bio'),
			'tran' => $this->input->post('tran_bio'),
			'dry_weight' => $this->input->post('dry_weight_bio'),
			'height' => $this->input->post('height_bio'),
			'hep_antibody' => $this->input->post('hep_antibody_bio'),
			'epo' => $this->input->post('epo_bio'),
			'clinical_diagnosis' => $this->input->post('clinical_diagnosis_bio'),
			'other' => $this->input->post('other')
				);
		$this->ndo_model->add_clinical_xyz($data);
		
		
		redirect('staff/view_summary/'.$pat_id);	
		
	}
	
	public function view_summary_detail($pat_id, $id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		
		$data['name'] = $name;
		$data['role'] = $role;
		
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		
		$data['pat_id'] = $pat_id;
		$data['pat_name'] = $this->ndo_model->get_pat_name($pat_id);
		$result = $this->ndo_model->get_clinical_xyz_detail($id);
		$data['clinical_xyz'] = $result['row'];
		
		$result = $this->ndo_model->get_all_neph_d();
		$data['neph_d'] = $result['row'];
		$result = $this->ndo_model->get_all_doc_d();
		$data['doc_d'] = $result['row'];
		$result = $this->ndo_model->get_all_n_review($pat_id);
		$data['n_review'] = $result['row'];
		$data['n_review_num_row'] = $result['num_row'];
		$result = $this->ndo_model->get_last_n_review($pat_id);
		$data['last_n_review'] = $result;
		$result = $this->ndo_model->get_all_d_review($pat_id);
		$data['d_review'] = $result['row'];
		$data['d_review_num_row'] = $result['num_row'];
		$result = $this->ndo_model->get_last_d_review($pat_id);
		$data['last_d_review'] = $result;
		
	  $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/view_summary_detail', $data);
	  $this->load->view('footer');
	  	
	}
	
	public function edit_summary_detail($pat_id, $id){
		$this->load->model('ndo_model');
		$data = array(
			'hb' => $this->input->post('hb'),
			'fer' => $this->input->post('fer'),
			'sat' => $this->input->post('sat'),
			'sr_iron' => $this->input->post('sr_iron'),
			'tibc' => $this->input->post('tibc'),
			'albumin' => $this->input->post('albumin'),
			'ipth' => $this->input->post('ipth'),
			'kt_v' => $this->input->post('kt_v'),
			'urr' => $this->input->post('urr'),
			'alp' => $this->input->post('alp'),
			'calcuim' => $this->input->post('calcium_bio'),
			'po4' => $this->input->post('po_bio'),
			'tran' => $this->input->post('tran_bio'),
			'dry_weight' => $this->input->post('dry_weight_bio'),
			'height' => $this->input->post('height_bio'),
			'hep_antibody' => $this->input->post('hep_antibody_bio'),
			'epo' => $this->input->post('epo_bio'),
			'clinical_diagnosis' => $this->input->post('clinical_diagnosis_bio'),
			'other' => $this->input->post('other')
				);
				
				$this->ndo_model->edit_clinical_xyz($data, $id);
		
		redirect('staff/view_summary_detail/'.$pat_id.'/'.$id);
		
		
	}
	
	public function delete_xyz($pat_id, $id){
		$this->load->model('ndo_model');
		$this->ndo_model->delete_xyz($id);
		
		redirect('staff/view_summary/'.$pat_id);
		
		
	}
	
	//............................................
	
	//group biochem summary
	
	public function bio_summary(){
$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$staff_center = $row->center_id;
		$name = $row->name;
		$role = $row->role;		
		}endforeach;
		$data['name'] = $name;
		$data['role'] = $role;
		$data['center'] = $staff_center;
		
		$data['center_header'] = $this->ndo_model->get_center($staff_center);
				
$type = $this->input->post('summary');
if($type != ""){
	if($type == "A"){	
$year = $this->input->post('yearA');
$month = $this->input->post('monthA');
$data['month'] = $month;
$data['year'] = $year;


$dateObj   = DateTime::createFromFormat('!m', $month);
$monthName = $dateObj->format('F');

$data['date'] = $monthName.'/'.$year;

$result = $this->ndo_model->get_clinical_xyz_report($year, $month);
$data['clinical_report'] = $result['row'];
$data['number_row'] = $result['num_row'];


   
   $result = $this->ndo_model->get_all_pat($staff_center);
		$data['patient'] = $result['row'];
		
		
		 $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/summaryA', $data);
	  $this->load->view('footer');
		

	}elseif($type == "B"){
	$year = $this->input->post('yearB');
	$data['year'] = $year;
	
$result = $this->ndo_model->get_all_pat($staff_center);
$data['pat'] = $result['row'];
$pat_number_row = $result['num_row'];


	
	


	
	$data['ery'] = $this->ndo_model->get_all_ery();
	
	$data['reportB'] = $this->ndo_model->get_clinical_xyz_reportB($year);

	


 
 
	




$data['date'] = $year;
	 $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/summaryB', $data);
	  $this->load->view('footer');

	}
	  
}else{
	//type null
	$this->session->set_flashdata('error_msg', 'Please make sure you select summary type.');
	redirect('staff/pat_list');
}
		
	}
	
	//patient history
	
	public function view_history($pat_id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		
		$data['name'] = $name;
		$data['role'] = $role;
		
		$data['pat_id'] = $pat_id;
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		 $data['pat_name'] = $this->ndo_model->get_pat_name($pat_id);
		 $result = $this->ndo_model->get_history_info($pat_id);
		$data['history'] = $result;
		$result = $this->ndo_model->get_pat_info_id($pat_id);
		$data['patient'] = $result;
		
		$result = $this->ndo_model->get_all_center_d();
		$data['center'] = $result['row'];
		
		$result = $this->ndo_model->get_all_transfer();
		$data['transfer'] = $result['row'];
		
		$result = $this->ndo_model->get_all_staff_d();
		$data['staff_d'] = $result['row'];
		$result = $this->ndo_model->get_all_neph_d();
		$data['neph_d'] = $result['row'];
		$result = $this->ndo_model->get_all_doc_d();
		$data['doc_d'] = $result['row'];
		$result = $this->ndo_model->get_all_owner_d();
		$data['owner_d'] = $result['row'];
		$result = $this->ndo_model->get_all_admin_d();
		$data['admin_d'] = $result['row'];
		
		$result = $this->ndo_model->get_all_n_review($pat_id);
		$data['n_review'] = $result['row'];
		$data['n_review_num_row'] = $result['num_row'];
		$result = $this->ndo_model->get_last_n_review($pat_id);
		$data['last_n_review'] = $result;
		$result = $this->ndo_model->get_all_d_review($pat_id);
		$data['d_review'] = $result['row'];
		$data['d_review_num_row'] = $result['num_row'];
		$result = $this->ndo_model->get_last_d_review($pat_id);
		$data['last_d_review'] = $result;
		
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/history', $data);
	  $this->load->view('footer');
	
    }
	
	public function view_history_detail($history_id, $pat_id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		
		$data['name'] = $name;
		$data['role'] = $role;
		
		$res = $this->ndo_model->get_all_country();
		$data['country'] = $res['row'];
		
		$res = $this->ndo_model->get_all_state();
		$data['state'] = $res['row'];
		
		$res = $this->ndo_model->get_all_city();
		$data['city'] = $res['row'];
		
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		
		$result = $this->ndo_model->get_history_info($pat_id);
		$data['patient'] = $result;
		
		$result = $this->ndo_model->get_all_transfer();
		$data['transfer'] = $result['row'];
		
		$result = $this->ndo_model->get_all_center_d();
		$data['center'] = $result['row'];
		
		foreach($data['patient'] as $row):{
			if($row->history_id == $history_id){
			$medical_history_id = $row->medical_history_id;
			}
		}endforeach;
		
		$data['pat_name'] = $this->ndo_model->get_pat_name($pat_id);
		$data['history_id'] = $history_id;
		$data['pat_id'] = $pat_id;
		$result = $this->ndo_model->get_history_info_detail($history_id);
		$data['history'] = $result;
		
		$result = $this->ndo_model->get_all_staff_d();
		$data['staff_d'] = $result['row'];
		$result = $this->ndo_model->get_all_neph_d();
		$data['neph_d'] = $result['row'];
		$result = $this->ndo_model->get_all_doc_d();
		$data['doc_d'] = $result['row'];
		$result = $this->ndo_model->get_all_owner_d();
		$data['owner_d'] = $result['row'];
		$result = $this->ndo_model->get_all_admin_d();
		$data['admin_d'] = $result['row'];
		
		$result = $this->ndo_model->get_all_n_review($pat_id);
		$data['n_review'] = $result['row'];
		$data['n_review_num_row'] = $result['num_row'];
		$result = $this->ndo_model->get_last_n_review($pat_id);
		$data['last_n_review'] = $result;
		$result = $this->ndo_model->get_all_d_review($pat_id);
		$data['d_review'] = $result['row'];
		$data['d_review_num_row'] = $result['num_row'];
		$result = $this->ndo_model->get_last_d_review($pat_id);
		$data['last_d_review'] = $result;
		//get medical problem history
		
		$result = $this->ndo_model->get_gses_his($medical_history_id);
		$data['gses'] = $result['row'];
		$result = $this->ndo_model->get_endo_his($medical_history_id);
		$data['endo'] = $result['row'];
		$result = $this->ndo_model->get_gastro_his($medical_history_id);
		$data['gastro'] = $result['row'];
		$result = $this->ndo_model->get_res_system_his($medical_history_id);
		$data['res_system'] = $result['row'];
		$result = $this->ndo_model->get_musc_his($medical_history_id);
		$data['musc'] = $result['row'];
		$result = $this->ndo_model->get_card_his($medical_history_id);
		$data['card'] = $result['row'];
		$result = $this->ndo_model->get_hema_his($medical_history_id);
		$data['hema'] = $result['row'];
		$result = $this->ndo_model->get_neuro_his($medical_history_id);
		$data['neuro'] = $result['row'];
		$result = $this->ndo_model->get_other_problem_his($medical_history_id);
		$data['other_problem'] = $result['row'];
		
		//get medication history
		
		$result = $this->ndo_model->get_arb_his($medical_history_id);
		$data['arb'] = $result['row'];
		$result = $this->ndo_model->get_beta_his($medical_history_id);
		$data['beta'] = $result['row'];
		$result = $this->ndo_model->get_ace_his($medical_history_id);
		$data['ace'] = $result['row'];
		$result = $this->ndo_model->get_ccb_his($medical_history_id);
		$data['ccb'] = $result['row'];
		$result = $this->ndo_model->get_vitamin_d_his($medical_history_id);
		$data['vitamin'] = $result['row'];
		$result = $this->ndo_model->get_hematinics_his($medical_history_id);
		$data['hematinics'] = $result['row'];
		$result = $this->ndo_model->get_aab_his($medical_history_id);
		$data['aab'] = $result['row'];
		$result = $this->ndo_model->get_vas_his($medical_history_id);
		$data['vas'] = $result['row'];
		$result = $this->ndo_model->get_phosphate_his($medical_history_id);
		$data['phosphate'] = $result['row'];
		$result = $this->ndo_model->get_diur_his($medical_history_id);
		$data['diur'] = $result['row'];
		$result = $this->ndo_model->get_ery_his($medical_history_id);
		$data['ery'] = $result['row'];
		$result = $this->ndo_model->get_other_medic_his($medical_history_id);
		$data['other_medic'] = $result['row'];
		
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/history_detail', $data);
	  $this->load->view('footer');
	
    }
	//..............................................
	
	//check patient avaibality
	
	function check_patient_availability()
{
    $pat_ic   = $this->input->post('pat_ic');
    
	$this->load->model('ndo_model');
    $result = $this->ndo_model->get_all_patient_ic( $pat_ic );  #send the post variable to the model
    //value got from the get metho
   
    if( $result != false ){
        echo "0";
    }else{
        echo "1";
    }

}

	
	
	//.........................................
	
	//nephrologists
	
	public function view_neph(){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		
		$data['name'] = $name;
		$data['role'] = $role;
		
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		
		$data['center_id'] = $center_id;
		$result = $this->ndo_model->get_center_superviser_neph($center_id);
		$data['supervision'] = $result['row'];
		$data['supervision_num_row'] = $result['num_row'];
		
		$result = $this->ndo_model->get_all_neph();
		$data['neph'] = $result['row'];
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/view_neph', $data);
	  $this->load->view('footer');
	
    }
	
	public function view_neph_info($neph_id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		
		$data['name'] = $name;
		$data['role'] = $role;
		
		 $center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		 
		 $result = $this->ndo_model->get_neph_supervision_request($center_id, $neph_id);
		$data['supervision'] = $result['row'];
		$data['supervision_num_row'] = $result['num_row'];
		 
		$result = $this->ndo_model->get_neph_request($center_id, $neph_id);
		$data['request'] = $result['row'];
		$result = $this->ndo_model->get_all_center();
		$data['center'] = $result['row']; 
		$data['neph'] = $this->ndo_model->get_neph($neph_id);
		
		$res = $this->ndo_model->get_all_country();
		$data['country'] = $res['row'];
		
		$res = $this->ndo_model->get_all_state();
		$data['state'] = $res['row'];
		
		$res = $this->ndo_model->get_all_city();
		$data['city'] = $res['row'];
		
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/neph_info', $data);
	  $this->load->view('footer');
	
    }
	
	//supervision request
	public function req_sup_neph($neph_id){
	 $staff_id = $this->session->userdata('staff_id');
	 $this->load->model('ndo_model');
	 $center_id =  $this->ndo_model->get_center_id($staff_id);
	 $data['neph'] = $this->ndo_model->get_neph($neph_id);
	 foreach($data['neph'] as $row):{
	$num_supervision = $row->supervision;	 
	 }endforeach;
	
	if($num_supervision != 0){
		
	$data = array(
			'neph_id' => $neph_id,
			'center_id' => $center_id,
			'status' => "pending"
				);	
	$this->ndo_model->req_sup_neph($data);
	
	$this->session->set_flashdata('success_msg', 'Request is Sent Successfully. Please Wait for Nephrologist Approval');
	redirect('staff/view_neph_info/'.$neph_id);
		
	}else{
		$this->session->set_flashdata('error_msg', ' Nephrologist Appear to be Full at the Moment');
	redirect('staff/view_neph_info/'.$neph_id);
		
	}
	
    }
	
	public function update_req_sup_neph($neph_id, $id){
	 $staff_id = $this->session->userdata('staff_id');
	 $this->load->model('ndo_model');
	 $center_id =  $this->ndo_model->get_center_id($staff_id);
	 $data['neph'] = $this->ndo_model->get_neph($neph_id);
	 foreach($data['neph'] as $row):{
	$num_supervision = $row->supervision;	 
	 }endforeach;
	
	if($num_supervision != 0){
		
	$data = array(
			'status' => "pending"
				);	
	$this->ndo_model->update_req_sup_neph($data, $id);
	
	$this->session->set_flashdata('success_msg', 'Request is Sent Successfully. Please Wait for Nephrologist Approval');
	redirect('staff/view_neph_info/'.$neph_id);
		
	}else{
		$this->session->set_flashdata('error_msg', ' Nephrologist Appear to be Full at the Moment');
	redirect('staff/view_neph_info/'.$neph_id);
		
	}
	
    }
	
	public function cancel_req_sup_neph($neph_id, $id){
	 $this->load->model('ndo_model');
	 $staff_id = $this->session->userdata('staff_id');
	 $this->load->model('ndo_model');
	 $center_id =  $this->ndo_model->get_center_id($staff_id);
	 
	 
	 $result = $this->ndo_model->get_neph_supervision_request($center_id, $neph_id);
	$data['supervision'] = $result['row'];
	 foreach($data['supervision'] as $row):{
		$status = $row->status; 
	 }endforeach;
	if($status == "pending"){
		
		$this->ndo_model->delete_neph_sup_req($id);
	
	$this->session->set_flashdata('success_msg', 'Request is Canceled');
	redirect('staff/view_neph_info/'.$neph_id);
		
	}else{
		$this->session->set_flashdata('error_msg', ' Request already '.$status.' by Nephrologist');
	redirect('staff/view_neph_info/'.$neph_id);
		
	}
	
    }
	
	
	//visit request
	
	public function request_neph($neph_id){
	 $staff_id = $this->session->userdata('staff_id');
	 $this->load->model('ndo_model');
	 $center_id =  $this->ndo_model->get_center_id($staff_id);
	$request_date = $this->input->post('request_date');	
	$request_time = $this->input->post('request_time');
	
	if($request_date !="" && $request_time !=""){
		
	$data = array(
			'date' => $request_date,
			'time' => $request_time,
			'status' => "pending",
			'neph_id' => $neph_id,
			'center_id' => $center_id
				);	
	$this->ndo_model->request_neph($data);
	
	$this->session->set_flashdata('success_msg', 'Request is Sent Successfully. Please Wait for Nephrologist Approval');
	redirect('staff/view_neph_info/'.$neph_id);
		
	}else{
		$this->session->set_flashdata('error_msg', ' Date and Time of Visiting Request Must be Set Correctly');
	redirect('staff/view_neph_info/'.$neph_id);
		
	}
	
    }
	
	public function cancel_req_neph($neph_id, $id){
	 $this->load->model('ndo_model');
	 $result = $this->ndo_model->get_neph_req_detail($id);
	 $data['detail'] = $result['row'];
	 foreach($data['detail'] as $row):{
		$status = $row->status; 
	 }endforeach;
	if($status == "pending"){
		
		$this->ndo_model->delete_neph_req($id);
	
	$this->session->set_flashdata('success_msg', 'Request is Canceled');
	redirect('staff/view_neph_info/'.$neph_id);
		
	}else{
		$this->session->set_flashdata('error_msg', ' Request already '.$status.' by Nephrologist');
	redirect('staff/view_neph_info/'.$neph_id);
		
	}
	
    }
	
	
	//.........................................
	
	//doctor
	
	public function view_doc(){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		
		$data['name'] = $name;
		$data['role'] = $role;
		
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		
		$data['center_id'] = $center_id;
		$result = $this->ndo_model->get_center_superviser_doc($center_id);
		$data['supervision'] = $result['row'];
		$data['supervision_num_row'] = $result['num_row'];
		
		$result = $this->ndo_model->get_all_doc();
		$data['doc'] = $result['row'];
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/view_doc', $data);
	  $this->load->view('footer');
	
    }
	
	public function view_doc_info($doc_id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		
		$data['name'] = $name;
		$data['role'] = $role;
		
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		 
		 $result = $this->ndo_model->get_doc_supervision_request($center_id, $doc_id);
		$data['supervision'] = $result['row'];
		$data['supervision_num_row'] = $result['num_row'];
		 
		$result = $this->ndo_model->get_doc_request($center_id, $doc_id);
		$data['request'] = $result['row'];
		$result = $this->ndo_model->get_all_center();
		$data['center'] = $result['row']; 
		$data['doc'] = $this->ndo_model->get_doc($doc_id);
		
		$res = $this->ndo_model->get_all_country();
		$data['country'] = $res['row'];
		
		$res = $this->ndo_model->get_all_state();
		$data['state'] = $res['row'];
		
		$res = $this->ndo_model->get_all_city();
		$data['city'] = $res['row'];
		
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/doc_info', $data);
	  $this->load->view('footer');
	
    }
	
	//supervision request
	public function req_sup_doc($doc_id){
	 $staff_id = $this->session->userdata('staff_id');
	 $this->load->model('ndo_model');
	 $center_id =  $this->ndo_model->get_center_id($staff_id);
	 $data['doc'] = $this->ndo_model->get_doc($doc_id);
	 foreach($data['doc'] as $row):{
	$num_supervision = $row->supervision;	 
	 }endforeach;
	
	if($num_supervision != 0){
		
	$data = array(
			'doc_id' => $doc_id,
			'center_id	' => $center_id,
			'status' => "pending"
				);	
	$this->ndo_model->req_sup_doc($data);
	
	$this->session->set_flashdata('success_msg', 'Request is Sent Successfully. Please Wait for Doctor Approval');
	redirect('staff/view_doc_info/'.$doc_id);
		
	}else{
		$this->session->set_flashdata('error_msg', ' Doctor Appear to be Full at the Moment');
	redirect('staff/view_doc_info/'.$doc_id);
		
	}
	
    }
	
	public function update_req_sup_doc($doc_id, $id){
	 $staff_id = $this->session->userdata('staff_id');
	 $this->load->model('ndo_model');
	 $center_id =  $this->ndo_model->get_center_id($staff_id);
	 $data['doc'] = $this->ndo_model->get_doc($doc_id);
	 foreach($data['doc'] as $row):{
	$num_supervision = $row->supervision;	 
	 }endforeach;
	
	if($num_supervision != 0){
		
	$data = array(
			'status' => "pending"
				);	
	$this->ndo_model->update_req_sup_doc($data, $id);
	
	$this->session->set_flashdata('success_msg', 'Request is Sent Successfully. Please Wait for Doctor Approval');
	redirect('staff/view_doc_info/'.$doc_id);
		
	}else{
		$this->session->set_flashdata('error_msg', ' Doctor Appear to be Full at the Moment');
	redirect('staff/view_doc_info/'.$doc_id);
		
	}
	
    }
	
	public function cancel_req_sup_doc($doc_id, $id){
	 $this->load->model('ndo_model');
	 $staff_id = $this->session->userdata('staff_id');
	 $this->load->model('ndo_model');
	 $center_id =  $this->ndo_model->get_center_id($staff_id);
	 
	 
	 $result = $this->ndo_model->get_doc_supervision_request($center_id, $doc_id);
	$data['supervision'] = $result['row'];
	 foreach($data['supervision'] as $row):{
		$status = $row->status; 
	 }endforeach;
	if($status == "pending"){
		
		$this->ndo_model->delete_doc_sup_req($id);
	
	$this->session->set_flashdata('success_msg', 'Request is Canceled');
	redirect('staff/view_doc_info/'.$doc_id);
		
	}else{
		$this->session->set_flashdata('error_msg', ' Request already '.$status.' by Doctor');
	redirect('staff/view_doc_info/'.$doc_id);
		
	}
	
    }
	
	
	//visit request
	
	public function request_doc($doc_id){
	 $staff_id = $this->session->userdata('staff_id');
	 $this->load->model('ndo_model');
	 $center_id =  $this->ndo_model->get_center_id($staff_id);
	$request_date = $this->input->post('request_date');	
	$request_time = $this->input->post('request_time');
	
	if($request_date !="" && $request_time !=""){
		
	$data = array(
			'date' => $request_date,
			'time' => $request_time,
			'status' => "pending",
			'doc_id' => $doc_id,
			'center_id' => $center_id
				);	
	$this->ndo_model->request_doc($data);
	
	$this->session->set_flashdata('success_msg', 'Request is Sent Successfully. Please Wait for Doctor Approval');
	redirect('staff/view_doc_info/'.$doc_id);
		
	}else{
		$this->session->set_flashdata('error_msg', ' Date and Time of Visiting Request Must be Set Correctly');
	redirect('staff/view_doc_info/'.$doc_id);
		
	}
	
    }
	
	public function cancel_req_doc($doc_id, $id){
	 $this->load->model('ndo_model');
	 $result = $this->ndo_model->get_doc_req_detail($id);
	 $data['detail'] = $result['row'];
	 foreach($data['detail'] as $row):{
		$status = $row->status; 
	 }endforeach;
	if($status == "pending"){
		
		$this->ndo_model->delete_doc_req($id);
	
	$this->session->set_flashdata('success_msg', 'Request is Canceled');
	redirect('staff/view_doc_info/'.$doc_id);
		
	}else{
		$this->session->set_flashdata('error_msg', ' Request already '.$status.' by Doctor');
	redirect('staff/view_doc_info/'.$doc_id);
		
	}
	
    }
	//...........................................
	
	// manage center
	
	public function center(){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		if($role == "clerk")
		{
		$data['name'] = $name;
		$data['role'] = $role;
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		
		$res = $this->ndo_model->get_all_country();
		$data['country'] = $res['row'];
		
		$res = $this->ndo_model->get_all_state();
		$data['state'] = $res['row'];
		
		$res = $this->ndo_model->get_all_city();
		$data['city'] = $res['row'];
		
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/center', $data);
	  $this->load->view('footer');
	  
		}else{
		redirect('staff/index');	
		}
	
    }
	
	public function edit_center(){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		if($role == "clerk")
		{
		$data['name'] = $name;
		$data['role'] = $role;
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		
		$res = $this->ndo_model->get_all_country();
		$data['country'] = $res['row'];
		
		$res = $this->ndo_model->get_all_state();
		$data['state'] = $res['row'];
		
		$res = $this->ndo_model->get_all_city();
		$data['city'] = $res['row'];
		
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/edit_center', $data);
	  $this->load->view('footer');
	  
		}else{
		redirect('staff/index');	
		}
	
    }
	
	 public function save_edit_center(){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$role = $row->role;	
		}endforeach;
		if($role == "clerk")
		{
		
		$center_id =  $this->ndo_model->get_center_id($staff_id);	
			
		$name = $this->input->post('name');
		$zip_code = $this->input->post('zip');
		$phone = $this->input->post('phone');
		$email = $this->input->post('email');
		$fax = $this->input->post('fax');
		$country_id = $this->input->post('country');
		$state_id = $this->input->post('state');
		$city_id = $this->input->post('city');
		$address = $this->input->post('address');
		$path = './img/center/'.$center_id;
		
		if($name != '' && $zip_code != '' && $phone !='' && $email !='' && $address !='' && $country_id !='' && $state_id !=''){
			
			
			
			
			$this->form_validation->set_rules('email', 'Email',
			 'trim|required|valid_email|xss_clean');
			
			if($this->form_validation->run() !== false){
				
			if($_FILES['imgInput']['error'] != 4){
				if (!file_exists($path)){
					
					mkdir($path, 0777);
					
				}
			
			 $config['upload_path'] = $path;
			 $config['file_name'] = "center_image.jpg";
			 $config['overwrite'] = TRUE;
             $config['allowed_types'] = '*';
             $config['max_size'] = 0;

             $this->upload->initialize($config);


             $this->upload->do_upload('imgInput');
		     $data = $this->upload->data();

             $img_full_path = $data['full_path'];
	   		 $img_file_name = $data['file_name'];
			 
			 $data = array(
			'image_name' => $img_file_name,
			'image_path' => $img_full_path
				);	
	
			$this->ndo_model->update_center($data, $center_id);
			 
			}
			
			if($_FILES['logoInput']['error'] != 4){
				if (!file_exists($path)){
					
					mkdir($path, 0777);
					
				}
			
			 $config['upload_path'] = $path;
			 $config['file_name'] = "center_logo.png";
			 $config['overwrite'] = TRUE;
             $config['allowed_types'] = '*';
             $config['max_size'] = 0;

             $this->upload->initialize($config);


             $this->upload->do_upload('logoInput');
		     $data = $this->upload->data();

             $logo_full_path = $data['full_path'];
	   		 $logo_file_name = $data['file_name'];
			 
			 $data = array(
			'logo_name' => $logo_file_name,
			'logo_path' => $logo_full_path
				);	
	
			$this->ndo_model->update_center($data, $center_id);
			 
			}
			
			$data = array(
			'name' => $name,
			'zip_code' => $zip_code,
			'phone' => $phone,
			'email' => $email,
			'fax' => $fax,
			'country_id' => $country_id,
			'state_id' => $state_id,
			'city_id' => $city_id,
			'address' => $address
				);	
	
			$this->ndo_model->update_center($data, $center_id);
			

			redirect('staff/center');
				
				
			}
			else{$this->session->set_flashdata('error_msg', ' Invalid E-mail.');
	redirect('staff/edit_center');
		}       	
		}
		else{$this->session->set_flashdata('error_msg', ' Required Fields Must Not Be Left Blank. Try Again');
	redirect('staff/edit_center');
		}
		
	   }else{
		redirect('staff/index');   
	   }
		
	}
	
	//.............................................
	
	//manage machine
	
	public function manage_machine(){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
			$name = $row->name;
			$role = $row->role;
		}endforeach;
		
			$data['name'] = $name;
			$data['role'] = $role;
			$center_id =  $this->ndo_model->get_center_id($staff_id);
			$data['center_header'] = $this->ndo_model->get_center($center_id);
			$result = $this->ndo_model->get_all_machine($center_id);
			$data['machine'] = $result['row'];
	
	
			$this->load->view('staff/header', $data);
			$this->load->view('staff/side_menu', $data);
			$this->load->view('staff/machine', $data);
			$this->load->view('footer');
			 
		
	
	}
	
	public function add_machine(){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$this->load->library('create_id');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
			$name = $row->name;
			$role = $row->role;
		}endforeach;
		
			$dateTime = date('Y-m-d H:i:s');
			$center_id =  $this->ndo_model->get_center_id($staff_id);
			$machine_id = $this->create_id->get_id();
			$info_id = $this->create_id->get_id();
			
			$data = array(
			'machine_id' => $machine_id,
			'info_id' => $info_id,
			'center_id' => $center_id,
				);
			$this->ndo_model->add_machine($data);
			
			$status = $this->input->post('status');
			$risk = $this->input->post('risk');
			
			if($risk == "High"){
				$status = 0;
			}
			
			$data = array(
					'machine_id' => $machine_id,
					'info_id' => $info_id,
					'center_id' => $center_id,
					'name' => $this->input->post('name'),
					'status' => $status,
					'risk' => $risk,
					'comment' => $this->input->post('comment'),
					'action' => "Created",
					'action_by' => $staff_id,
					'action_time' => $dateTime,
			);
			$this->ndo_model->add_machine_info($data);
			$this->session->set_flashdata('success_msg', ' Machine Successfully Inserted.');
			redirect('staff/manage_machine');
	
		
	
	}
	
	public function edit_machine_page($machine_id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
			$name = $row->name;
			$role = $row->role;
		}endforeach;
		
			$data['name'] = $name;
			$data['role'] = $role;
			$center_id =  $this->ndo_model->get_center_id($staff_id);
			$data['center_header'] = $this->ndo_model->get_center($center_id);
			$result = $this->ndo_model->get_machine($machine_id);
			$data['machine'] = $result['row'];
	
	
			$this->load->view('staff/header', $data);
			$this->load->view('staff/side_menu', $data);
			$this->load->view('staff/edit_machine', $data);
			$this->load->view('footer');
	
		
	
	}
	
	public function edit_machine($machine_id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$this->load->library('create_id');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
			$name = $row->name;
			$role = $row->role;
		}endforeach;
		
			$dateTime = date('Y-m-d H:i:s');
			$center_id =  $this->ndo_model->get_center_id($staff_id);
			$info_id = $this->create_id->get_id();
				
			$data = array(
					'info_id' => $info_id,
			);
			$this->ndo_model->edit_machine($data, $machine_id);
			
			$status = $this->input->post('status');
			$risk = $this->input->post('risk');
				
			if($risk == "High"){
				$status = 0;
			}
				
			$data = array(
					'machine_id' => $machine_id,
					'info_id' => $info_id,
					'center_id' => $center_id,
					'name' => $this->input->post('name'),
					'status' => $status,
					'risk' => $risk,
					'comment' => $this->input->post('comment'),
					'action' => "Edited",
					'action_by' => $staff_id,
					'action_time' => $dateTime,
			);
			$this->ndo_model->add_machine_info($data);
			$this->session->set_flashdata('success_msg', ' Machine Successfully Updated.');
			redirect('staff/edit_machine_page/'.$machine_id);
	
		
	
	}
	
	public function machine_usage($machine_id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
			$name = $row->name;
			$role = $row->role;
		}endforeach;
	
		$data['name'] = $name;
		$data['role'] = $role;
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		$result = $this->ndo_model->get_all_pat_d($center_id);
		$data['patient'] = $result['row'];
		$result = $this->ndo_model->get_machine($machine_id);
		$data['machine'] = $result['row'];
		$result = $this->ndo_model->get_machine_usage($machine_id);
		$data['machine_usage'] = $result['row'];
	
	
		$this->load->view('staff/header', $data);
		$this->load->view('staff/side_menu', $data);
		$this->load->view('staff/machine_usage', $data);
		$this->load->view('footer');
	
	
	
	}
	
	public function machine_history($machine_id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
			$name = $row->name;
			$role = $row->role;
		}endforeach;
	
		$data['name'] = $name;
		$data['role'] = $role;
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		
		$result = $this->ndo_model->get_machine($machine_id);
		$data['machine'] = $result['row'];
		
		$result = $this->ndo_model->get_machine_history($machine_id);
		$data['machine_history'] = $result['row'];
		
		$result = $this->ndo_model->get_all_staff();
		$data['staff_all'] = $result['row'];
			
		$this->load->view('staff/header', $data);
		$this->load->view('staff/side_menu', $data);
		$this->load->view('staff/machine_history', $data);
		$this->load->view('footer');
	
	
	
	}
	
	//.............................................
	
	//manage shift
	
	public function manage_shift(){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
			$name = $row->name;
			$role = $row->role;
		}endforeach;
		if($role == "clerk")
		{
			$data['name'] = $name;
			$data['role'] = $role;
			$center_id =  $this->ndo_model->get_center_id($staff_id);
			$data['center_header'] = $this->ndo_model->get_center($center_id);
			$result = $this->ndo_model->get_all_shift($center_id);
			$data['shift'] = $result['row'];
	
	
			$this->load->view('staff/header', $data);
			$this->load->view('staff/side_menu', $data);
			$this->load->view('staff/shift', $data);
			$this->load->view('footer');
	
		}else{
			redirect('staff/index');
		}
	
	}
	
	public function add_shift(){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$this->load->library('create_id');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
			$name = $row->name;
			$role = $row->role;
		}endforeach;
		if($role == "clerk")
		{
			$center_id =  $this->ndo_model->get_center_id($staff_id);
			$shift_id = $this->create_id->get_id();
				
			$data = array(
					'shift_id' => $shift_id,
					'from' => $this->input->post('start_time'),
					'to' => $this->input->post('end_time'),
					'center_id' => $center_id,
			);
			$this->ndo_model->add_shift($data);
				
			$this->session->set_flashdata('success_msg', ' Shift Successfully Added.');
			redirect('staff/manage_shift');
	
		}else{
			redirect('staff/index');
		}
	
	}
	
	public function edit_shift_page($shift_id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
			$name = $row->name;
			$role = $row->role;
		}endforeach;
		if($role == "clerk")
		{
			$data['name'] = $name;
			$data['role'] = $role;
			$center_id =  $this->ndo_model->get_center_id($staff_id);
			$data['center_header'] = $this->ndo_model->get_center($center_id);
			$result = $this->ndo_model->get_shift($shift_id);
			$data['shift'] = $result['row'];
	
	
			$this->load->view('staff/header', $data);
			$this->load->view('staff/side_menu', $data);
			$this->load->view('staff/edit_shift', $data);
			$this->load->view('footer');
	
		}else{
			redirect('staff/index');
		}
	
	}
	
	public function edit_shift($shift_id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$this->load->library('create_id');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
			$name = $row->name;
			$role = $row->role;
		}endforeach;
		if($role == "clerk")
		{
			$center_id =  $this->ndo_model->get_center_id($staff_id);
	
			$data = array(
					'from' => $this->input->post('start_time'),
					'to' => $this->input->post('end_time')
			);
			$this->ndo_model->edit_shift($data, $shift_id);
	
			$this->session->set_flashdata('success_msg', ' Shift Successfully Updated.');
			redirect('staff/edit_shift_page/'.$shift_id);
	
		}else{
			redirect('staff/index');
		}
	
	}
	
	public function delete_shift($shift_id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
			$name = $row->name;
			$role = $row->role;
		}endforeach;
		if($role == "clerk")
		{
			$center_id =  $this->ndo_model->get_center_id($staff_id);
	
			$this->ndo_model->delete_shift($shift_id);
	
			$this->session->set_flashdata('success_msg', ' Shift Successfully Deleted.');
			redirect('staff/manage_shift');
	
		}else{
			redirect('staff/index');
		}
	
	}
	
	//...............................................
	
	// manage medical staff
	
	public function med_staff(){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		if($role == "clerk")
		{
		$data['name'] = $name;
		$data['role'] = $role;
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		
		$result = $this->ndo_model->get_all_staff_for_clerk($center_id);
		$data['staff'] = $result['row'];
		
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/med_staff', $data);
	  $this->load->view('footer');
	  
		}else{
		redirect('staff/index');	
		}
	
    }
	
	public function add_med_staff(){
		$year = date("Y");
		$month = date("m");
		
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$this->load->library('create_id');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$role = $row->role;	
		}endforeach;
		if($role == "clerk")
		{
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		
		$name = $this->input->post('name');
		$med_staff_ic = $this->input->post('ic');
		$title = $this->input->post('title');
		$status = $this->input->post('status');
		$phone = $this->input->post('phone');
		$email = $this->input->post('email');
		$mobile = $this->input->post('mobile');
		$website = $this->input->post('website');
		$country_id = $this->input->post('country');
		$state_id = $this->input->post('state');
		$city_id = $this->input->post('city');
		$address = $this->input->post('address');
		$comment = $this->input->post('comment');
		$role = $this->input->post('role');
       
		
		if($name != '' && $med_staff_ic != '' && $phone !='' && $email !='' && $address !='' && $role !='' && $country_id !='' && $state_id !=''){
			
			
			
			
			$this->form_validation->set_rules('email', 'Email',
			 'trim|required|valid_email|xss_clean');
			
			if($this->form_validation->run() !== false){
			$med_staff_id = $this->create_id->get_id();
			$password = md5($med_staff_ic);
			$data = array(
			'name' => $name,
			'staff_ic' => $med_staff_ic,
			'staff_id' => $med_staff_id,
			'title' => $title,
			'phone' => $phone,
			'mobile' => $mobile,
			'email' => $email,
			'status' => $status,
			'country_id' => $country_id,
			'state_id' => $state_id,
			'city_id' => $city_id,
			'address' => $address,
			'comment' => $comment,
			'website' => $website,
			'password' => $password,
			'center_id' => $center_id,
			'role' => $role
				);	
			$result=$this->ndo_model->add_staff($data, $med_staff_ic);
			if($result !==false){
			$this->session->set_flashdata('success_msg', ' Medical Staff Successfully Inserted.');
	redirect('staff/med_staff');
			}else{
			$this->session->set_flashdata('error_msg', ' The IC already Assigned to Another User in The System. For Further Info, Please Contact the Adminstration.');
	redirect('staff/med_staff');
			}
			}
			else{
			$this->session->set_flashdata('error_msg', ' Invalid E-mail.');
	redirect('staff/med_staff');
		}       
		}
		else{
		$this->session->set_flashdata('error_msg', ' Required Fields Must Not Be Left Blank. Try Again');
	redirect('staff/med_staff');	
		}       
	  
		}else{
		redirect('staff/index');	
		}
	
    }
	
	public function view_med_staff($id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		if($role == "clerk")
		{
		$data['name'] = $name;
		$data['role'] = $role;
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		
		$data['staff'] = $this->ndo_model->get_staff($id);
		
		$res = $this->ndo_model->get_all_country();
		$data['country'] = $res['row'];
		
		$res = $this->ndo_model->get_all_state();
		$data['state'] = $res['row'];
		
		$res = $this->ndo_model->get_all_city();
		$data['city'] = $res['row'];
		
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/view_med_staff', $data);
	  $this->load->view('footer');
	  
		}else{
		redirect('staff/index');	
		}
	
    }
	
	public function edit_med_staff($id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$name = $row->name;
		$role = $row->role;	
		}endforeach;
		if($role == "clerk")
		{
		$data['name'] = $name;
		$data['role'] = $role;
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$data['center_header'] = $this->ndo_model->get_center($center_id);
		
		$data['staff'] = $this->ndo_model->get_staff($id);
		
		$res = $this->ndo_model->get_all_country();
		$data['country'] = $res['row'];
		
		$res = $this->ndo_model->get_all_state();
		$data['state'] = $res['row'];
		
		$res = $this->ndo_model->get_all_city();
		$data['city'] = $res['row'];
		
		
      $this->load->view('staff/header', $data);
	  $this->load->view('staff/side_menu', $data);
	  $this->load->view('staff/edit_med_staff', $data);
	  $this->load->view('footer');
	  
		}else{
		redirect('staff/index');	
		}
	
    }
	
	public function save_edit_med_staff($id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$role = $row->role;	
		}endforeach;
		if($role == "clerk")
		{
		$center_id =  $this->ndo_model->get_center_id($staff_id);
		$name = $this->input->post('name');
		$new_med_staff_ic = $this->input->post('ic');
		$title = $this->input->post('title');
		$status = $this->input->post('status');
		$phone = $this->input->post('phone');
		$email = $this->input->post('email');
		$mobile = $this->input->post('mobile');
		$website = $this->input->post('website');
		$country_id = $this->input->post('country');
		$state_id = $this->input->post('state');
		$city_id = $this->input->post('city');
		$address = $this->input->post('address');
		$comment = $this->input->post('comment');
		$role = $this->input->post('role');
		
		$data['staff'] = $this->ndo_model->get_staff($id);
		
		foreach($data['staff'] as $row):{
		$old_med_staff_ic = $row->staff_ic;	
		}endforeach;
		
       
		
		if($name != '' && $new_med_staff_ic != '' && $phone !='' && $email !='' && $address !='' && $country_id !='' && $state_id !=''){
			
			
			
			
			$this->form_validation->set_rules('email', 'Email',
			 'trim|required|valid_email|xss_clean');
			
			if($this->form_validation->run() !== false){
			
			$data = array(
			'name' => $name,
			'staff_ic' => $new_med_staff_ic,
			'title' => $title,
			'phone' => $phone,
			'mobile' => $mobile,
			'email' => $email,
			'status' => $status,
			'country_id' => $country_id,
			'state_id' => $state_id,
			'city_id' => $city_id,
			'address' => $address,
			'comment' => $comment,
			'website' => $website,
			'role' => $role
				);	
			$result=$this->ndo_model->update_staff_with_validation($data, $id, $new_med_staff_ic, $old_med_staff_ic);
			
			if($result != false){

			redirect('staff/view_med_staff/'.$id);
			
			}else{
			$this->session->set_flashdata('error_msg', ' The IC Belongs To Another User in The System . Contact Adminstration For Further Information');
	redirect('staff/edit_med_staff/'.$id);
			}
				
			}
			else{
			$this->session->set_flashdata('error_msg', ' Invalid E-mail.');
	redirect('staff/edit_med_staff/'.$id);
		}       
		}
		else{
		$this->session->set_flashdata('error_msg', ' Required Fields Must Not Be Left Blank. Try Again');
	redirect('staff/edit_med_staff/'.$id);	
		}
	  
		}else{
		redirect('staff/index');	
		}
	
    }
	
	public function delete_med_staff($id){
		$staff_id = $this->session->userdata('staff_id');
		$this->load->model('ndo_model');
		$data['staff'] = $this->ndo_model->get_staff($staff_id);
		foreach($data['staff'] as $row):{
		$role = $row->role;	
		}endforeach;
		if($role == "clerk")
		{
		$this->ndo_model->delete_staff($id);
		$this->session->set_flashdata('success_msg', ' Staff Is Deleted Successfully.');
	redirect('staff/med_staff');
	  
		}else{
		redirect('staff/index');	
		}
	
    }
    
    //get country/state/city
    
    function get_state()
    {
    	$country_id = $this->input->post('country_id');
    	$this->load->model('admin_model');
    
    	$res = $this->admin_model->get_all_state_for_country($country_id);
    	$results['state'] = $res['row'];
    
    	echo json_encode($results);
    
    }
    
    function get_city()
    {
    	$state_id = $this->input->post('state_id');
    	$this->load->model('admin_model');
    
    	$res = $this->admin_model->get_all_city_for_state($state_id);
    	$results['city'] = $res['row'];
    
    	echo json_encode($results);
    
    }
	
	
	
	
	
	//.............................................
	
 }
 ?>