<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Applicant extends CI_Controller
{

    var $menu;

    var $roles;

    var $data;

    var $table;

    var $pfield;

    var $logfield;

    var $module;

    var $modules;

    var $module_path;

    var $controller_page;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('generic_model', 'record');
        $this->module = 'Recruitment';
        $this->data['controller_page'] = $this->controller_page = site_url('applicant'); // defines contoller link
        $this->table = 'applicants'; // defines the default table
        $this->pfield = $this->data['pfield'] = 'applicantID'; // defines primary key
        $this->logfield = 'applicantNo';
        $this->module_path = 'modules/' . strtolower(str_replace(" ", "_", $this->module)) . '/applicant'; // defines module path
                                                                                                  
        // check for maintenance period
        if ($this->config_model->getConfig('Maintenance Mode') == '1') {
            header('location: ' . site_url('maintenance_mode'));
        }
        
        // check user session 
        if (! $this->session->userdata('current_user')->sessionID) {
            header('location: ' . site_url('login'));
        }
    }

    private function submenu()
    {
        // submenu setup
        require_once ('modules.php');
        
        foreach ($modules as $mod) {
            // modules/<module>/
            // - <menu>
            // - <metadata>
            $this->load->view('modules/' . str_replace(" ", "_", strtolower($mod)) . '/metadata');
        }
        
        $this->data['modules'] = $this->modules;
        // var_dump($this->data['modules']);

        $this->data['current_main_module'] = $this->modules[$this->module]['main']; // defines the current main module
        $this->data['current_module'] = $this->modules[$this->module]['sub']['Applicant']; // defines the current sub module
                                                                                                  
        // check roles
        $this->check_roles();
        $this->data['roles'] = $this->roles;
    }

    private function check_roles()
    {
        // check roles
        $this->roles['create']  = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Add '.$this->module);
        $this->roles['view']    = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View '.$this->module);
        $this->roles['edit']    = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Edit Existing '.$this->module);
        $this->roles['delete']  = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Delete Existing '.$this->module);
        $this->roles['approve'] = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Approve '.$this->module);
    }

    private function _in_used($id = 0)
    {
        $tables = array();
        
        if(!empty($tables)) {     
            foreach($tables as $table) {
                $this->db->where($this->pfield, $id);
                $result_count = $this->db->count_all_results($table);
                
                if($result_count > 0) {
                    return true;
                }
            }                               
        } 
        return false;
    }

    public function index()
    {
        $this->show();
    }

    public function create()
    {
        // echo 'hello';
        $this->submenu();
        $data = $this->data;
        // check roles
        if ($this->roles['create']) {
            $data['jobPositionID']  = $this->session->userdata('current_jobPositionID');
            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path . '/create');
            $this->load->view('footer');
        } else {
            // no access this page
            $data['class'] = "danger";
            $data['msg'] = "Sorry, you don't have access to this page!";
            $data['urlredirect'] = "";
            $this->load->view('header', $data);
            $this->load->view('message');
            $this->load->view('footer');
        }
    }

    public function save()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        
        // check role
        if ($this->roles['create']) {
            $this->record->table  = $this->table;
            $this->record->fields = array();
            
			$table_fields = array('applicantNo','lname','fname','mname','suffix','title','nickname','sex','civilStatus','birthPlace','nationality','bloodType','height',
								  'weight','medicalCondition','accidents','languages','telephone','mobile','workEmail','personalEmail','currentStreet',
								  'currentBarangayID','currentCityID','currentProvinceID','currentCountryID','provinceStreet','provinceBarangayID',
								  'provinceCityID','provinceProvinceID','provinceCountryID','recommendedBy');
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }           
            
            $this->record->fields['applicantNo']    = $genNo = $this->_generateID();
            $this->record->fields['birthDate']      = date('Y-m-d', strtotime($this->input->post('birthDate')));          

            if ($this->record->save()) {
                $id = $this->db->insert_id();
                $this->frameworkhelper->increment_series('Applicant Series');
                
                $this->record->fields = array();
                $this->record->where['applicantNo']  = $genNo;
                $this->record->retrieve();             
                
				// build folder for this applicant
				mkdir("records/applicant/".$genNo);
				
				// upload picture
    			$config['upload_path'] 	= 'records/applicant/'.$genNo.'/';
    			$config['allowed_types']= 'jpg|png|bmp|JPG|JPEG|PNG|BMP';
    			$config['max_size']		= '1000';
    			$config['max_width']  	= '800';
    			$config['max_height']  	= '800';    			
    			
    			$this->load->library('upload', $config);
    			
    			if (is_file($_FILES['userfile']['tmp_name'])) {
    				if ( ! $this->upload->do_upload()) {
    					$error = array('error' => $this->upload->display_errors());
    				} else { 	
	    				// rename uploaded image
	    				$data['upload_data'] = $this->upload->data(); 
	    				$fn = "2x2";
	    				rename($config['upload_path'].$data['upload_data']['file_name'], $config['upload_path'].$fn.$data['upload_data']['file_ext']);
	    				// save image to database
	    				$this->db->where('applicantID',$this->record->field->applicantID);
	    				$this->db->update('applicants',array('imageExtension'=>$data['upload_data']['file_ext']));     
	    				// generate thumbnail
	    				$this->_create_thumbnail($config['upload_path'].$fn.$data['upload_data']['file_ext']);
    				}					
    			}				


                
                $this->session->set_userdata('current_jobPositionID', $this->record->field->jobPostID);
                
                // record logs
                $pfield = $this->pfield;
                
                // $logs = "Record - " . trim($this->input->post($this->logfield));
                // $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->field->$pfield, 'Insert', $logs);
                
                // $logfield = $this->pfield;
                
                // success msg
                $data["class"] = "success";
                $data["msg"] = $data['current_module']['module_label'] . " successfully saved.";
                $data["urlredirect"] = $this->controller_page . "/view/" . $this->encrypter->encode($id);
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            } else {
                // error
                $data["class"] = "danger";
                $data["msg"] = "Error in saving the " . $this->data['current_module']['module_label'] . "!";
                ;
                $data["urlredirect"] = "";
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            }
        } else {
            // error
            $data["class"] = "danger";
            $data["msg"] = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header", $data);
            $this->load->view("message");
            $this->load->view("footer");
        }
    }

    public function edit($id)
    {
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);
        
        if ($this->roles['edit']) {
            $data['required_fields'] = array('jobPositionID'=>'Position', 'duty'=>'Duty','date'=>'Date Posted');                
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->record->table = $this->table;
            // set fields for the current table
            $this->record->setFields();
            // extend fields - join tables                  
            // $this->record->fields[] = 'job_titles.jobTitle';
            // $this->record->fields[] = 'job_titles.jobDescription';
            // $this->record->fields[] = 'job_positions.companyID';
            // $this->record->fields[] = 'job_positions.branchID';
            // $this->record->fields[] = 'job_positions.divisionID';
            // $this->record->fields[] = 'job_positions.employeeTypeID';
            // $this->record->fields[] = 'companies.companyName';
            // $this->record->fields[] = 'branches.branchName';
            // $this->record->fields[] = 'divisions.divisionName';
            // $this->record->fields[] = 'employee_types.employeeType';
            // set joins
            // $this->record->joins[]  = array('job_positions',$this->table.'.jobPositionID=job_positions.jobPositionID','left');
            // $this->record->joins[]  = array('job_titles','job_positions.jobTitleID=job_titles.jobTitleID','left');
            // $this->record->joins[]  = array('companies','job_positions.companyID=companies.companyID','left');
            // $this->record->joins[]  = array('branches','job_positions.branchID=branches.branchID','left');
            // $this->record->joins[]  = array('divisions','job_positions.divisionID=divisions.divisionID','left');
            // $this->record->joins[]  = array('employee_types','job_positions.employeeTypeID=employee_types.employeeTypeID','left');          
            // set where
            $this->record->where[$this->table.'.'.$this->pfield] = $id;
            // execute retrieve
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;
            
            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path . '/edit');
            $this->load->view('footer');
        } else {
            // no access this page
            $data['class'] = "danger";
            $data['msg'] = "Sorry, you don't have access to this page!";
            $data['urlredirect'] = "";
            $this->load->view('header', $data);
            $this->load->view('message');
            $this->load->view('footer');
        }
    }

    public function update()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($this->input->post($this->pfield));
        $table_fields = array('lname','fname','mname','suffix','title','nickname','sex','civilStatus','birthPlace','nationality','bloodType','height',
              'weight','medicalCondition','languages','telephone','mobile','workEmail','personalEmail','currentStreet',
              'currentBarangayID','currentCityID','currentProvinceID','currentCountryID','provinceStreet','provinceBarangayID',
              'provinceCityID','provinceProvinceID','provinceCountryID','recommendedBy'
              );
        
        // check roles
        if ($this->roles['edit']) {
            $this->record->table = $this->table;
            $this->record->fields = array();
            
            foreach ($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }
            
            $this->record->fields['birthDate']      = date('Y-m-d', strtotime($this->input->post('birthDate')));          
            $this->record->pfield = $this->pfield;
            $this->record->pk = $id;
            
            // field logs here
            $wasChange = $this->log_model->field_logs($data['current_module']['module_label'], $this->table, $this->pfield, $id, 'Update', $this->record->fields);
            
            if ($this->record->update()) {
                // record logs
                if ($wasChange) {
                    $logs = "Record - " . trim($this->input->post($this->logfield));
                    $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->pk, 'Update', $logs);
                }
                
                // successful
                $data["class"] = "success";
                $data["msg"] = $this->data['current_module']['module_label'] . " successfully updated.";
                $data["urlredirect"] = $this->controller_page . "/view/" . $this->encrypter->encode($id);
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            } else {
                // error
                $data["class"] = "danger";
                $data["msg"] = "Error in updating the " . $this->data['current_module']['module_label'] . "!";
                $data["urlredirect"] = $this->controller_page . "/view/" . $this->record->pk;
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            }
        } else {
            // error
            $data["class"] = "danger";
            $data["msg"] = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header", $data);
            $this->load->view("message");
            $this->load->view("footer");
        }
    }

    public function delete($id = 0)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);
        
        // check roles
        if ($this->roles['delete']) {
            // set fields
            $this->record->fields = array();
            // set table
            $this->record->table = $this->table;
            // set where
            $this->record->where[$this->pfield] = $id;
            // execute retrieve
            $this->record->retrieve();
            // echo $this->db->last_query();
            
            if (! empty($this->record->field)) {
                $this->record->pfield = $this->pfield;
                $this->record->pk = $id;
                
                // record logs
                $rec_value = $this->record->field->name;
                
                // check if in used
                if (! $this->_in_used($id)) {
                    if ($this->record->delete()) {
                        $logfield = $this->logfield;
                        
                        // record logs
                        $logs = "Record - " . $this->record->field->$logfield;
                        $this->log_model->table_logs($this->data['current_module']['module_label'], $this->table, $this->pfield, $this->record->pk, 'Delete', $logs);
                        
                        // successful
                        $data["class"] = "success";
                        $data["msg"] = $this->data['current_module']['module_label'] . " successfully deleted.";
                        $data["urlredirect"] = $this->controller_page . "/";
                        $this->load->view("header", $data);
                        $this->load->view("message");
                        $this->load->view("footer");
                    } else {
                        // error
                        $data["class"] = "danger";
                        $data["msg"] = "Error in deleting the " . $this->data['current_module']['module_label'] . "!";
                        $data["urlredirect"] = "";
                        $this->load->view("header", $data);
                        $this->load->view("message");
                        $this->load->view("footer");
                    }
                } else {
                    // error
                    $data["class"] = "danger";
                    $data["msg"] = "Data integrity constraints.";
                    $data["urlredirect"] = "";
                    $this->load->view("header", $data);
                    $this->load->view("message");
                    $this->load->view("footer");
                }
            } else {
                // error
                $data["class"] = "danger";
                $data["msg"] = $this->data['current_module']['module_label'] . " record not found!";
                $data["urlredirect"] = "";
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            }
        } else {
            // error
            $data["class"] = "danger";
            $data["msg"] = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header", $data);
            $this->load->view("message");
            $this->load->view("footer");
        }
    }

    public function view($id)
    {
        $id = $this->encrypter->decode($id);
        
        // load submenu
        $this->submenu();
        $data = $this->data;
        
        $this->roles['view'] = 1;
        if ($this->roles['view']) {
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->record->table = $this->table;
            
            // set fields for the current table
            $this->record->setFields();
            
            // extend fields - join tables
         
            $this->record->fields[] = 'applicants.*';           
            // $this->record->fields[] = 'job_posts.postNo';
            // $this->record->fields[] = 'job_posts.datePosted';
            // $this->record->fields[] = 'job_titles.jobTitle';
            // $this->record->fields[] = 'job_titles.jobDescription';
            // $this->record->fields[] = 'companies.companyName';
            // $this->record->fields[] = 'branches.branchName';
            // $this->record->fields[] = 'divisions.divisionName';
            // $this->record->fields[] = 'employee_types.employeeType';
            // set joins
            // $this->record->joins[]  = array('applicants',$this->table.'.applicantID=applicants.applicantID','left');
            // $this->record->joins[]  = array('job_posts',$this->table.'.jobPostID=job_posts.jobPostID','left');
            // $this->record->joins[]  = array('job_positions','job_posts.jobPositionID=job_positions.jobPositionID','left');
            // $this->record->joins[]  = array('job_titles','job_positions.jobTitleID=job_titles.jobTitleID','left');
            // $this->record->joins[]  = array('companies','job_positions.companyID=companies.companyID','left');
            // $this->record->joins[]  = array('branches','job_positions.branchID=branches.branchID','left');
            // $this->record->joins[]  = array('divisions','job_positions.divisionID=divisions.divisionID','left');
            // $this->record->joins[]  = array('employee_types','job_positions.employeeTypeID=employee_types.employeeTypeID','left');  
            
            // set joins
            
            // set where
            $this->record->where[$this->pfield] = $id;
            
            // execute retrieve
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;
            
            $data['in_used'] = $this->_in_used($id);
            // record logs
            $pfield = $this->pfield;
            if ($this->config_model->getConfig('Log all record views') == '1') {
                $logs = "Record - " . $this->record->field->name;
                $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->field->$pfield, 'View', $logs);
            }
            // var_dump($data['rec']);
            
            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path . '/view');
            // $this->load->view($this->module_path.'/attachment');            
            $this->load->view($this->module_path.'/educational_background');
            $this->load->view($this->module_path.'/service_eligibility');
            $this->load->view($this->module_path.'/work_experience');
            $this->load->view($this->module_path.'/training_program');
            $this->load->view('footer');
        } else {
            // no access this page
            $data['class'] = "danger";
            $data['msg'] = "Sorry, you don't have access to this page!";
            $data['urlredirect'] = "";
            $this->load->view('header', $data);
            $this->load->view('message');
            $this->load->view('footer');
        }
    }

    public function show()
    {
        // ************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;
        
        $data['activetab'] = 1;
        // **************************************************
        // variable:field:default_value:operator
        // note: dont include the special query field filter                
        $condition_fields = array(
            array('variable'=>'applicantNo', 'field'=>'applicants.applicantNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'lname', 'field'=>'applicants.lname', 'default_value'=>'', 'operator'=>'like_both'),
            // array('variable'=>'postNo', 'field'=>'job_positions.postNo', 'default_value'=>'', 'operator'=>'like_both'),
            // array('variable'=>'jobTitleID', 'field'=>'job_positions.jobTitleID', 'default_value'=>'', 'operator'=>'where'),
            // array('variable'=>'companyID', 'field'=>'job_positions.companyID', 'default_value'=>'', 'operator'=>'where'),
            // array('variable'=>'branchID', 'field'=>'job_positions.branchID', 'default_value'=>'', 'operator'=>'where'),
            // array('variable'=>'divisionID', 'field'=>'job_positions.divisionID', 'default_value'=>'', 'operator'=>'where'),
            // array('variable'=>'employeeTypeID', 'field'=>'job_positions.employeeTypeID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'1', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('lname'=>'asc');
        
        $controller = $this->uri->segment(1);
        
        if ($this->uri->segment(4))
            $offset = $this->uri->segment(4);
        else
            $offset = 0;

        // source of filtering
        $filter_source = 0; // default/blank
        if ($this->input->post('filterflag') || $this->input->post('sortby')) {
            $filter_source = 1;
        } else {
            foreach($condition_fields as $key) {
                if ($this->input->post($key['variable'])) {
                    $filter_source = 1; // form filters
                    break;
                }
            }
        }
        
        if (!$filter_source) {
            foreach($condition_fields as $key) {
                if ($this->session->userdata($controller.'_'.$key['variable'])) {
                    $filter_source = 2; // session
                    break;
                }
            }
        }
        switch($filter_source) 
        {
            case 1:
                foreach($condition_fields as $key) {
                    $$key['variable'] = trim($this->input->post($key['variable']));
                }
                
                $date = trim($this->input->post('date'));

                $sortby     = trim($this->input->post('sortby'));
                $sortorder  = trim($this->input->post('sortorder'));
                
                break;
            case 2:
                foreach($condition_fields as $key) {
                    $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
                }
                
                $date = $this->session->userdata($controller.'_date');
                
                $sortby     = $this->session->userdata($controller.'_sortby');
                $sortorder  = $this->session->userdata($controller.'_sortorder');
                break;
            default:
                foreach($condition_fields as $key) {                    
                    $$key['variable'] = $key['default_value'];
                }
                
                $date = "";
                
                $sortby     = "";
                $sortorder  = "";
        }           

        if ($this->input->post('limit')) {
            if ($this->input->post('limit')=="All")
                $limit = "";
            else
                $limit = $this->input->post('limit');
        } else if ($this->session->userdata($controller.'_limit')) {
            $limit = $this->session->userdata($controller.'_limit');
        } else {
            $limit = ($pageType) ? 10 : 25; // default limit
        }
        
        if ($pageType==3) {
            foreach($condition_fields as $key) {
                if ($key['variable']=='jobPositionID') {
                    $$key['variable'] = $this->encrypter->decode(trim($this->input->post('id')));
                }
            }
        }
        
        // set session variables
        foreach($condition_fields as $key) {
            $this->session->set_userdata($controller.'_'.$key['variable'], $$key['variable']);
        }
        
        $this->session->set_userdata($controller.'_date', $date);
        
        $this->session->set_userdata($controller.'_sortby', $sortby);
        $this->session->set_userdata($controller.'_sortorder', $sortorder);
        $this->session->set_userdata($controller.'_limit', $limit);
            
        // assign data variables for views
        foreach($condition_fields as $key) {
            $data[$key['variable']] = $$key['variable'];
        }           
        
        $data['date'] = $date;
        
        // select
        $this->db->select($this->table.'.*');
        // $this->db->select('applicants.applicantNo');
        // $this->db->select('applicants.lname');
        // $this->db->select('applicants.fname');
        // $this->db->select('applicants.mname');
        // $this->db->select('applicants.suffix');
        // $this->db->select('job_posts.postNo');
        // $this->db->select('job_titles.jobTitle');
        // $this->db->select('job_titles.jobDescription');
        // $this->db->select('companies.companyAbbr');
        // $this->db->select('branches.branchAbbr');
        // $this->db->select('divisions.divisionAbbr');
        // $this->db->select('employee_types.employeeType');

        // from
        $this->db->from($this->table);
        
        // join
        // $this->db->join('applicants',$this->table.'.applicantID=applicants.applicantID','left');
        // $this->db->join('job_posts',$this->table.'.jobPostID=job_posts.jobPostID','left');
        // $this->db->join('job_positions','job_posts.jobPositionID=job_positions.jobPositionID','left');
        // $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID','left');
        // $this->db->join('companies','job_positions.companyID=companies.companyID','left');
        // $this->db->join('branches','job_positions.branchID=branches.branchID','left');
        // $this->db->join('divisions','job_positions.divisionID=divisions.divisionID','left');
        // $this->db->join('employee_types','job_positions.employeeTypeID=employee_types.employeeTypeID','left');
        
        // where
        // set conditions here
        foreach($condition_fields as $key) {
            $operators = explode('_',$key['operator']);
            $operator = $operators[0];
            // check if the operator is like
            if (count($operators)>1) {
                // like operator
                if (trim($$key['variable'])!='' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable'], $operators[1]); 
            } else {
                if (trim($$key['variable'])!='' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable']);
            }
        }
        
        if($date) {
            $this->db->like($this->table.'.date',date('Y-m-d',strtotime($date)));
        }
        
        // get
        $data['ttl_rows'] = $config['total_rows'] = $this->db->count_all_results();
        
        // set pagination   
        $config['base_url'] = $this->controller_page.'/show/'.$pageType.'/';
        $config['per_page'] = $limit;
        $config['uri_segment'] = 4;
        $this->pagination->initialize($config);
        
        
        // select
        $this->db->select($this->table.'.*');
        // $this->db->select('applicants.applicantNo');
        // $this->db->select('applicants.lname');
        // $this->db->select('applicants.fname');
        // $this->db->select('applicants.mname');
        // $this->db->select('applicants.suffix');
        // $this->db->select('job_posts.postNo');
        // $this->db->select('job_titles.jobTitle');
        // $this->db->select('job_titles.jobDescription');
        // $this->db->select('companies.companyAbbr');
        // $this->db->select('branches.branchAbbr');
        // $this->db->select('divisions.divisionAbbr');
        // $this->db->select('employee_types.employeeType');

        // from
        $this->db->from($this->table);
        
        // join
        // $this->db->join('applicants',$this->table.'.applicantID=applicants.applicantID','left');
        // $this->db->join('job_posts',$this->table.'.jobPostID=job_posts.jobPostID','left');
        // $this->db->join('job_positions','job_posts.jobPositionID=job_positions.jobPositionID','left');
        // $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID','left');
        // $this->db->join('companies','job_positions.companyID=companies.companyID','left');
        // $this->db->join('branches','job_positions.branchID=branches.branchID','left');
        // $this->db->join('divisions','job_positions.divisionID=divisions.divisionID','left');
        // $this->db->join('employee_types','job_positions.employeeTypeID=employee_types.employeeTypeID','left');
        
        // where
        // set conditions here
        foreach($condition_fields as $key) {
            $operators = explode('_',$key['operator']);
            $operator = $operators[0];
            // check if the operator is like
            if (count($operators)>1) {
                // like operator
                if (trim($$key['variable'])!='' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable'], $operators[1]); 
            } else {
                if (trim($$key['variable'])!='' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable']);
            }
        }

        if($date) {
            $this->db->like($this->table.'.date',date('Y-m-d',strtotime($date)));
        }
        
        if ($sortby && $sortorder) {
            $this->db->order_by($sortby, $sortorder);

            if (!empty($sorting_fields)) {
                foreach($sorting_fields as $fld=>$s_order) {
                    if ($fld != $sortby) {
                        $this->db->order_by($fld, $s_order);
                    }
                }
            }
        } else {
            $ctr = 1;
            if (!empty($sorting_fields)) {
                foreach($sorting_fields as $fld=>$s_order) {
                    if ($ctr == 1) {
                        $sortby     = $fld;
                        $sortorder  = $s_order;
                    }
                    $this->db->order_by($fld, $s_order);
                    
                    $ctr++;
                }
            }
        }
            
        if ($limit) {
            if ($offset) {
                $this->db->limit($limit,$offset); 
            } else {
                $this->db->limit($limit); 
            }
        }
        
        // assigning variables
        $data['sortby']     = $sortby;
        $data['sortorder']  = $sortorder;
        $data['limit']      = $limit;
        
        // get
        $data['records'] = $this->db->get();
        
        // load views
        $this->load->view('header', $data);
        $this->load->view($this->module_path . '/list');
        $this->load->view('footer');
    }


    public function printlist()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        // sorting
        
        $condition_fields = array(
            array('variable'=>'applicationNo', 'field'=>'applicants.applicationNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'applicationNo', 'field'=>'applicants.applicationNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'applicantNo', 'field'=>'applicants.applicantNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'lname', 'field'=>'applicants.lname', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'postNo', 'field'=>'job_positions.postNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'jobTitleID', 'field'=>'job_positions.jobTitleID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'companyID', 'field'=>'job_positions.companyID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'officeID', 'field'=>'job_positions.officeID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'divisionID', 'field'=>'job_positions.divisionID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'employeeTypeID', 'field'=>'job_positions.employeeTypeID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'1', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('date'=>'desc','lname'=>'asc');
        
        $controller = $this->uri->segment(1);
        
        if ($this->uri->segment(3))
            $offset = $this->uri->segment(3);
        else
            $offset = 0;

        foreach($condition_fields as $key) {
            $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
        }
        
        $date = $this->session->userdata($controller.'_date');
        
        $sortby     = $this->session->userdata($controller.'_sortby');
        $sortorder  = $this->session->userdata($controller.'_sortorder');
        $limit      = $this->session->userdata($controller.'_limit');
        
        
        // select
        $this->db->select($this->table.'.*');
        $this->db->select('applicants.applicantNo');
        $this->db->select('applicants.lname');
        $this->db->select('applicants.fname');
        $this->db->select('applicants.mname');
        $this->db->select('applicants.suffix');
        $this->db->select('job_posts.postNo');
        $this->db->select('job_titles.jobTitle');
        $this->db->select('job_titles.jobDescription');
        $this->db->select('companies.companyAbbr');
        $this->db->select('offices.officeAbbr');
        $this->db->select('divisions.divisionAbbr');
        $this->db->select('employee_types.employeeType');

        // from
        $this->db->from($this->table);
        
        // join
        $this->db->join('applicants',$this->table.'.applicantID=applicants.applicantID','left');
        // $this->db->join('job_posts',$this->table.'.jobPostID=job_posts.jobPostID','left');
        $this->db->join('job_positions','job_posts.jobPositionID=job_positions.jobPositionID','left');
        $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID','left');
        $this->db->join('companies','job_positions.companyID=companies.companyID','left');
        $this->db->join('offices','job_positions.officeID=offices.officeID','left');
        $this->db->join('divisions','job_positions.divisionID=divisions.divisionID','left');
        $this->db->join('employee_types','job_positions.employeeTypeID=employee_types.employeeTypeID','left');  
        
        // where
        // set conditions here
        foreach($condition_fields as $key) {
            $operators = explode('_',$key['operator']);
            $operator = $operators[0];
            // check if the operator is like
            if (count($operators)>1) {
                // like operator
                if (trim($$key['variable'])!='' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable'], $operators[1]); 
            } else {
                if (trim($$key['variable'])!='' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable']);
            }
        }   
        
        if($date) {
            $this->db->like($this->table.'.date',date('Y-m-d',strtotime($date)));
        }
        
        if ($sortby && $sortorder) {
            $this->db->order_by($sortby, $sortorder);

            if (!empty($sorting_fields)) {
                foreach($sorting_fields as $fld=>$s_order) {
                    if ($fld != $sortby) {
                        $this->db->order_by($fld, $s_order);
                    }
                }
            }
        } else {
            $ctr = 1;
            if (!empty($sorting_fields)) {
                foreach($sorting_fields as $fld=>$s_order) {
                    if ($ctr == 1) {
                        $sortby     = $fld;
                        $sortorder  = $s_order;
                    }
                    $this->db->order_by($fld, $s_order);
                    
                    $ctr++;
                }
            }
        }
            
        if ($limit) {
            if ($offset) {
                $this->db->limit($limit,$offset); 
            } else {
                $this->db->limit($limit); 
            }
        }
        
        // get
        $data['records'] = $this->db->get();
        
        $data['header1'] = $this->module_label." List";
        
        // load views
        $this->load->view('header_print', $data);
        $this->load->view($this->module_path . '/printlist');
        $this->load->view('footer_print');
    }

    public function exportlist()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        // sorting
        
        // variable:field:default_value:operator
        // note: dont include the special query field filter
        $condition_fields = array(
            array(
                'variable' => 'groupName',
                'field' => $this->table . '.name',
                'groupName' => '',
                'operator' => 'like_both'
            ),
            array(
                'variable' => 'groupDesc',
                'field' => $this->table . '.value',
                'groupDesc' => '',
                'operator' => 'like_both'
            ),
            array(
                'variable' => 'rstatus',
                'field' => $this->table . '.rstatus',
                'default_value' => '',
                'operator' => 'where'
            )
        );
        
        // sorting fields
        $sorting_fields = array(
            'groupName' => 'asc'
        );
        
        $controller = $this->uri->segment(1);
        
        foreach ($condition_fields as $key) {
            $$key['variable'] = $this->session->userdata($controller . '_' . $key['variable']);
        }
        
        $limit = $this->session->userdata($controller . '_limit');
        $offset = $this->session->userdata($controller . '_offset');
        $sortby = $this->session->userdata($controller . '_sortby');
        $sortorder = $this->session->userdata($controller . '_sortorder');
        
        // select
        $this->db->select($this->table . '.*');
        
        // from
        $this->db->from($this->table);
        
        // join
        
        // where
        // set conditions here
        foreach ($condition_fields as $key) {
            $operators = explode('_', $key['operator']);
            $operator = $operators[0];
            // check if the operator is like
            if (count($operators) > 1) {
                // like operator
                if (trim($$key['variable']) != '' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable'], $operators[1]);
            } else {
                if (trim($$key['variable']) != '' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable']);
            }
        }
        
        if ($sortby && $sortorder) {
            $this->db->order_by($sortby, $sortorder);
            
            if (! empty($sorting_fields)) {
                foreach ($sorting_fields as $fld => $s_order) {
                    if ($fld != $sortby) {
                        $this->db->order_by($fld, $s_order);
                    }
                }
            }
        } else {
            $ctr = 1;
            if (! empty($sorting_fields)) {
                foreach ($sorting_fields as $fld => $s_order) {
                    if ($ctr == 1) {
                        $sortby = $fld;
                        $sortorder = $s_order;
                    }
                    $this->db->order_by($fld, $s_order);
                    
                    $ctr ++;
                }
            }
        }
        
        if ($limit) {
            if ($offset) {
                $this->db->limit($limit, $offset);
            } else {
                $this->db->limit($limit);
            }
        }
        
        // assigning variables
        $data['sortby'] = $sortby;
        $data['sortorder'] = $sortorder;
        $data['limit'] = $limit;
        $data['offset'] = $offset;
        
        // get
        $records = $this->db->get()->result();
        
        $title = "Group List";
        $companyName = $this->config_model->getConfig('Company');
        $address = $this->config_model->getConfig('Address');
        
        // XML Blurb
        $data = "<?xml version='1.0'?>
            
    		<?mso-application progid='Excel.Sheet'?>
            
    		<Workbook xmlns='urn:schemas-microsoft-com:office:spreadsheet' xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns:ss='urn:schemas-microsoft-com:office:spreadsheet' xmlns:html='http://www.w3.org/TR/REC-html40'>
    		<Styles>
            <Style ss:ID='s20'>
    	        <Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>
    		  <Font ss:Bold='1' ss:Size='14'/>
    		</Style>
            
    		<Style ss:ID='s23'>
    		  <Font ss:Bold='1'/>
	        <Borders>
                    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
                   </Borders>
    		</Style>
            
    		<Style ss:ID='s24'>
                <Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>
                <Font ss:Bold='1'/>
            </Style>
            
	        <Style ss:ID='s24A'>
                <Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>
            </Style>
            
	        <Style ss:ID='s24B'>
                <Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>
	           <Borders>
                    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
                   </Borders>
            </Style>
            
            <Style ss:ID='s25'>
                <Alignment ss:Horizontal='Right' ss:Vertical='Bottom'/>
                <NumberFormat ss:Format='#,##0.00_ ;[Red]\-#,##0.00\ '/>
                <Font ss:Bold='1'/>
            </Style>
            <Style ss:ID='s26'>
                <Alignment ss:Horizontal='Right' ss:Vertical='Bottom'/>
                <NumberFormat ss:Format='#,##0.00_ ;[Red]\-#,##0.00\ '/>
	           <Borders>
                    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
                   </Borders>
            </Style>
            <Style ss:ID='s27'>
    		      <Borders>
                    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
                   </Borders>
            </Style>
    		</Styles>
            
    		<Worksheet ss:Name='" . $title . "'>
    		    
    		<Table>
    		<Column ss:Index='1' ss:AutoFitWidth=\"1\" ss:Width='25.00'/>
    		<Column ss:Index='2' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='3' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='4' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		    ";
        
        // header
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s20'>";
        $data .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'>" . $companyName . "</Data></Cell>";
        $data .= "</Row>";
        $data .= "<Row ss:StyleID='s24A'>";
        $data .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'>" . $address . "</Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'>" . strtoupper($title) . "</Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='3'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
        
        $fields[] = "  ";
        $fields[] = "GROUP";
        $fields[] = "DESCRIPTION";
        $fields[] = "STATUS";
        
        $data .= "<Row ss:StyleID='s24'>";
        // Field Name Data
        foreach ($fields as $fld) {
            $data .= "<Cell ss:StyleID='s23'><Data ss:Type='String'>$fld</Data></Cell>";
        }
        $data .= "</Row>";
        
        if (count($records)) {
            $ctr = 1;
            foreach ($records as $row) {
                $data .= "<Row>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $ctr . ".</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->groupName . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->groupDesc . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->rstatus . "</Data></Cell>";
                $data .= "</Row>";
                
                $ctr ++;
            }
        }
        $data .= "</Table></Worksheet>";
        $data .= "</Workbook>";
        
        // Final XML Blurb
        $filename = "group_list";
        
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$filename.xls;");
        header("Content-Type: application/ms-excel");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        echo $data;
    }


    private function _generateID()
    {
        $idSeries   = $this->config_model->getConfig('Job Post Series');
        $idLength   = $this->config_model->getConfig('Job Post Series Length');
    
        $id  = str_pad($idSeries, $idLength, "0", STR_PAD_LEFT);
        return "JP".date('y').$id;
    }
    
    // AJAX HANDLER FUNCTIONS
    public function check_duplicate()
    {
        $this->record->table = $this->table;
        $this->record->where['applicantNo']  = trim($this->input->post('applicantNo'));
        $this->record->retrieve();
        if (!empty($this->record->field))
            echo "1"; // duplicate
        else 
            echo "0";
    }
    
    public function getPositionDuties()
    {
        $jobPositionID = trim($this->input->post('jobPositionID'));
        
        if ($jobPositionID) {
            $this->db->where('jobPositionID', $jobPositionID);
        }
        $this->db->order_by('duty','asc');
        $records = $this->db->get('position_duties');
        echo $this->frameworkhelper->get_json_data($records, 'jobPostID', 'duty');
    }   
    
    public function display_session()
    {               
        echo var_dump($_SESSION);
    }


    public function show_educations()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $applicantID    = $this->encrypter->decode($this->input->post('applicantID'));
    
        $this->db->select('applicant_education_backgrounds.*');
        $this->db->where('applicantID', $applicantID);
        $records = $this->db->get('applicant_education_backgrounds')->result();
         
        $response->status  = 1;
        $response->records  = $records;
    
        echo json_encode($response);
    }
    
    public function save_education()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $applicantID    = $this->encrypter->decode($this->input->post('applicantID'));
    
        $this->db->set('applicantID', $applicantID);
        $this->db->set('level', trim($this->input->post('level')));
        $this->db->set('degree', trim($this->input->post('degree')));
        $this->db->set('school', trim($this->input->post('school')));
        $this->db->set('yearStart', trim($this->input->post('yearStart')));
        $this->db->set('yearGrad', trim($this->input->post('yearGrad')));
        $this->db->set('levelEarn', trim($this->input->post('levelEarn')));
        $this->db->set('honors', trim($this->input->post('honors')));
        $this->db->set('remarks', trim($this->input->post('remarks')));
        $this->db->set('status', trim($this->input->post('status')));
        $this->db->insert('applicant_education_backgrounds');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function update_education()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $this->db->set('level', trim($this->input->post('level')));
        $this->db->set('degree', trim($this->input->post('degree')));
        $this->db->set('school', trim($this->input->post('school')));
        $this->db->set('yearStart', trim($this->input->post('yearStart')));
        $this->db->set('yearGrad', trim($this->input->post('yearGrad')));
        $this->db->set('levelEarn', trim($this->input->post('levelEarn')));
        $this->db->set('honors', trim($this->input->post('honors')));
        $this->db->set('remarks', trim($this->input->post('remarks')));
        $this->db->set('status', trim($this->input->post('status')));
        $this->db->where('educationID', trim($this->input->post('educationID')));
        $this->db->update('applicant_education_backgrounds');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function delete_education()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $this->db->where('educationID', trim($this->input->post('educationID')));
        $this->db->delete('applicant_education_backgrounds');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function view_education()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $educationID = trim($this->input->post('educationID'));

        $this->db->select('applicant_education_backgrounds.*');
        $this->db->where('educationID', $educationID);
        $record = $this->db->get('applicant_education_backgrounds', 1)->row();
         
        $response->status  = 1;
        $response->record  = $record;
    
        echo json_encode($response);
    }
    
    public function show_eligibilities()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $applicantID    = $this->encrypter->decode($this->input->post('applicantID'));
    
        $this->db->select('applicant_eligibilities.*');
        $this->db->select('DATE_FORMAT(applicant_eligibilities.examDate, "%m/%d/%Y") as examDate');
        $this->db->select('DATE_FORMAT(applicant_eligibilities.dateLicense, "%m/%d/%Y") as dateLicense');
        $this->db->select('DATE_FORMAT(applicant_eligibilities.dateExpired, "%m/%d/%Y") as dateExpired');
        $this->db->where('applicant_eligibilities.applicantID', $applicantID);
        $this->db->order_by('applicant_eligibilities.examDate', 'asc');
        $records = $this->db->get('applicant_eligibilities')->result();
         
        $response->status  = 1;
        $response->records  = $records;
    
        echo json_encode($response);
    }
    
    public function save_eligibility()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $applicantID    = $this->encrypter->decode($this->input->post('applicantID'));
    
        $this->db->set('applicantID', $applicantID);
        $this->db->set('eligibility', trim($this->input->post('eligibility')));
        $this->db->set('rating', trim($this->input->post('rating')));
        
        if (trim($this->input->post('examDate')) != '') {
            $this->db->set('examDate', date('Y-m-d', strtotime(trim($this->input->post('examDate')))));
        }
        
        $this->db->set('examPlace', trim($this->input->post('examPlace')));
        $this->db->set('licenseNo', trim($this->input->post('licenseNo')));
        
        if (trim($this->input->post('dateLicense')) != '') {
            $this->db->set('dateLicense', date('Y-m-d', strtotime(trim($this->input->post('dateLicense')))));
        }
        
        if (trim($this->input->post('dateExpired')) != '') {
            $this->db->set('dateExpired', date('Y-m-d', strtotime(trim($this->input->post('dateExpired')))));
        } 
        
        $this->db->set('remarks', trim($this->input->post('remarks')));
        $this->db->insert('applicant_eligibilities');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function update_eligibility()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $this->db->set('eligibility', trim($this->input->post('eligibility')));
        $this->db->set('rating', trim($this->input->post('rating')));
        
        if (trim($this->input->post('examDate')) != '') {
            $this->db->set('examDate', date('Y-m-d', strtotime(trim($this->input->post('examDate')))));
        } else {
            $this->db->set('examDate', '0000-00-00');
        }
        
        $this->db->set('examPlace', trim($this->input->post('examPlace')));
        $this->db->set('licenseNo', trim($this->input->post('licenseNo')));
        
        if (trim($this->input->post('dateLicense')) != '') {
            $this->db->set('dateLicense', date('Y-m-d', strtotime(trim($this->input->post('dateLicense')))));
        } else {
            $this->db->set('dateLicense', '0000-00-00');
        }
        
        if (trim($this->input->post('dateExpired')) != '') {
            $this->db->set('dateExpired', date('Y-m-d', strtotime(trim($this->input->post('dateExpired')))));
        } else {
            $this->db->set('dateExpired', '0000-00-00');
        }
        
        $this->db->set('remarks', trim($this->input->post('remarks')));
        $this->db->where('eligibilityID', trim($this->input->post('eligibilityID')));
        $this->db->update('applicant_eligibilities');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function delete_eligibility()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $this->db->where('eligibilityID', trim($this->input->post('eligibilityID')));
        $this->db->delete('applicant_eligibilities');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function view_eligibility()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $eligibilityID = trim($this->input->post('eligibilityID'));
    
        $this->db->select('applicant_eligibilities.*');
        $this->db->select('DATE_FORMAT(applicant_eligibilities.examDate, "%M %d %Y") as examDate');
        $this->db->select('DATE_FORMAT(applicant_eligibilities.dateLicense, "%M %d %Y") as dateLicense');
        $this->db->select('DATE_FORMAT(applicant_eligibilities.dateExpired, "%M %d %Y") as dateExpired');
        $this->db->where('eligibilityID', $eligibilityID);
        $record = $this->db->get('applicant_eligibilities', 1)->row();
         
        $response->status  = 1;
        $response->record  = $record;
    
        echo json_encode($response);
    }
    
    public function show_experiences()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $applicantID    = $this->encrypter->decode($this->input->post('applicantID'));
    
        $this->db->select('applicant_work_experiences.*');

        $this->db->where('applicant_work_experiences.applicantID', $applicantID);
        $records = $this->db->get('applicant_work_experiences')->result();
         
        $response->status  = 1;
        $response->records  = $records;
    
        echo json_encode($response);
    }
    
    public function save_experience()
    {
        // echo 'save experience';
        // echo $this->encrypter->decode($this->input->post('applicantID'));
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $applicantID     = $this->encrypter->decode($this->input->post('applicantID'));
    
        $this->db->set('applicantID', $applicantID);
        $this->db->set('type', trim($this->input->post('type')));
        $this->db->set('designation', trim($this->input->post('designation')));
        $this->db->set('company', trim($this->input->post('company')));
        $this->db->set('employment', trim($this->input->post('employment')));
        $this->db->set('basicSalary', trim($this->input->post('basicSalary')));
        $this->db->set('salaryType', trim($this->input->post('salaryType')));
        $this->db->set('startYear', date('Y', strtotime($this->input->post('wc_startDate'))));
        $this->db->set('startMonth', date('m', strtotime($this->input->post('wc_startDate'))));
        $this->db->set('startDay', date('d', strtotime($this->input->post('wc_startDate'))));

        $this->db->set('endYear', date('Y', strtotime($this->input->post('wc_endDate'))));
        $this->db->set('endMonth', date('m', strtotime($this->input->post('wc_endDate'))));
        $this->db->set('endDay', date('d', strtotime($this->input->post('wc_endDate'))));
                
        $this->db->set('remarks', trim($this->input->post('remarks')));
        $this->db->insert('applicant_work_experiences');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function update_experience()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';

        $this->db->set('type', trim($this->input->post('etype')));
        $this->db->set('designation', trim($this->input->post('edesignation')));
        $this->db->set('company', trim($this->input->post('ecompany')));
        $this->db->set('employment', trim($this->input->post('eemployment')));
        $this->db->set('basicSalary', trim($this->input->post('ebasicSalary')));
        $this->db->set('salaryType', trim($this->input->post('esalaryType')));
        
        $this->db->set('startYear', date('Y', strtotime($this->input->post('we_startDate'))));
        $this->db->set('startMonth', date('m', strtotime($this->input->post('we_startDate'))));
        $this->db->set('startDay', date('d', strtotime($this->input->post('we_startDate'))));

        $this->db->set('endYear', date('Y', strtotime($this->input->post('we_endDate'))));
        $this->db->set('endMonth', date('m', strtotime($this->input->post('we_endDate'))));
        $this->db->set('endDay', date('d', strtotime($this->input->post('we_endDate'))));
        
        $this->db->set('remarks', trim($this->input->post('eremarks')));
        $this->db->where('workID', trim($this->input->post('workID')));
        $this->db->update('applicant_work_experiences');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function delete_experience()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $this->db->where('workID', trim($this->input->post('workID')));
        $this->db->delete('applicant_work_experiences');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function view_experience()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $workID = trim($this->input->post('workID'));
    
        $this->db->select('applicant_work_experiences.*');
        $this->db->where('workID', $workID);
        $record = $this->db->get('applicant_work_experiences', 1)->row();
         
        $response->status  = 1;
        $response->record  = $record;
    
        echo json_encode($response);
    }
    
    public function show_trainings()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $applicantID    = $this->encrypter->decode($this->input->post('applicantID'));
    
        $this->db->select('applicant_trainings.*');
        $this->db->where('applicant_trainings.applicantID', $applicantID);
        $records = $this->db->get('applicant_trainings')->result();
         
        $response->status  = 1;
        $response->records  = $records;
    
        echo json_encode($response);
    }
    
    public function save_training()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $applicantID     = $this->encrypter->decode($this->input->post('applicantID'));
    
        $this->db->set('applicantID', $applicantID);
        $this->db->set('course', trim($this->input->post('course')));
        $this->db->set('organizer', trim($this->input->post('organizer')));
        $this->db->set('venue', trim($this->input->post('venue')));
        $this->db->set('noHours', trim($this->input->post('noHours')));
    
        $this->db->set('startYear', date('Y', strtotime($this->input->post('tpc_startDate'))));
        $this->db->set('startMonth', date('m', strtotime($this->input->post('tpc_startDate'))));
        $this->db->set('startDay', date('d', strtotime($this->input->post('tpc_startDate'))));

        $this->db->set('endYear', date('Y', strtotime($this->input->post('tpc_endDate'))));
        $this->db->set('endMonth', date('m', strtotime($this->input->post('tpc_endDate'))));
        $this->db->set('endDay', date('d', strtotime($this->input->post('tpc_endDate'))));
    
        $this->db->set('remarks', trim($this->input->post('remarks')));
        $this->db->insert('applicant_trainings');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function update_training()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $this->db->set('course', trim($this->input->post('ecourse')));
        $this->db->set('organizer', trim($this->input->post('eorganizer')));
        $this->db->set('venue', trim($this->input->post('evenue')));
        $this->db->set('noHours', trim($this->input->post('enoHours')));
    
        $this->db->set('startYear', date('Y', strtotime($this->input->post('tpe_startDate'))));
        $this->db->set('startMonth', date('m', strtotime($this->input->post('tpe_startDate'))));
        $this->db->set('startDay', date('d', strtotime($this->input->post('tpe_startDate'))));

        $this->db->set('endYear', date('Y', strtotime($this->input->post('tpe_endDate'))));
        $this->db->set('endMonth', date('m', strtotime($this->input->post('tpe_endDate'))));
        $this->db->set('endDay', date('d', strtotime($this->input->post('tpe_endDate'))));
    
        $this->db->set('remarks', trim($this->input->post('eremarks')));
        $this->db->where('trainingID', trim($this->input->post('trainingID')));
        $this->db->update('applicant_trainings');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function delete_training()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $this->db->where('trainingID', trim($this->input->post('trainingID')));
        $this->db->delete('applicant_trainings');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function view_training()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $trainingID = trim($this->input->post('trainingID'));
    
        $this->db->select('applicant_trainings.*');                
        $this->db->where('applicant_trainings.trainingID', $trainingID);
        $record = $this->db->get('applicant_trainings', 1)->row();
         
        $response->status  = 1;
        $response->record  = $record;
    
        echo json_encode($response);
    }
    
}