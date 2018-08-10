<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends CI_Controller
{
    //Default Variables
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
        $this->load->model('generic_model','record');
        $this->module       = 'Employee';
        $this->table        = 'employees';                                                 
        $this->pfield       = $this->data['pfield'] = 'empID';                                                 
        $this->logfield     = 'empNo';
        $this->module_path  = 'modules/employee/employee';           
        $this->data['controller_page']  = $this->controller_page = site_url('employee');
        
        // check for maintenance period
        if ($this->config_model->getConfig('Maintenance Mode')=='1') {
            header('location: '.site_url('maintenance_mode'));
        }
        
        // check user session
        if (!$this->session->userdata('current_user')->sessionID) {
            header('location: '.site_url('login'));
        }
    }

    private function submenu()
    {
        //submenu setup
        require_once('modules.php');

        foreach($modules as $mod) {
            //modules/<module>/
            // - <menu>
            // - <metadata>
            $this->load->view('modules/'.str_replace(" ","_",strtolower($mod)).'/metadata');
        }

        $this->data['modules']   = $this->modules;
        $this->data['current_main_module']   = $this->modules[$this->module]['main'];              // defines the current main module
        $this->data['current_module']   = $this->modules[$this->module]['sub']['Employee'];      // defines the current sub module
        // check roles
        $this->check_roles();
        $this->data['roles']   = $this->roles;
    }
    
    private function check_roles()
    {
        $this->roles['create']  = $this->userrole_model->has_access($this->session->userdata('current_userID'),'Add '.$this->module);
        $this->roles['view']    = $this->userrole_model->has_access($this->session->userdata('current_userID'),'View '.$this->module);
        $this->roles['edit']    = $this->userrole_model->has_access($this->session->userdata('current_userID'),'Edit Existing '.$this->module);
        $this->roles['delete']  = $this->userrole_model->has_access($this->session->userdata('current_userID'),'Delete Existing '.$this->module);
        $this->roles['print']   = $this->userrole_model->has_access($this->session->userdata('current_userID'),'Print '.$this->module);
        $this->roles['export']  = $this->userrole_model->has_access($this->session->userdata('current_userID'),'Export '.$this->module);
    }
    
    public function index()
    {
        $this->show();
    }
    
    public function create()
    {
       $this->submenu();
        $data = $this->data;
        // check roles
        //if ($this->roles['create']) {
            $data['required_fields'] = array('empNo'=>'Employee No.', 'lname'=>'Last Name', 'fname'=>'First Name', 'sex'=>'Sex', 'civilStatus'=>'Civil Status',
               'birthDate'=>'Birth Date', 'nationality'=>'Nationality','currentStreet'=>'Current House No./Street', 
               'currentBarangayID'=>'Current Barangay', 'currentCityID'=>'Current City/Town', 'currentProvinceID'=>'Current Province',
               'currentCountryID'=>'Current Country','dateEmployed'=>'Date Employed','companyID'=>'Company','officeID'=>'Office',
               'employeeTypeID'=>'Employment Type','jobPositionID'=>'Position','basicSalary'=>'Basic Salary');

            $data['countryID']      = ($this->session->userdata('current_countryID')) ? $this->session->userdata('current_countryID') : $this->config_model->getConfig('Default Country Option');
            $data['provinceID']     = ($this->session->userdata('current_provinceID')) ? $this->session->userdata('current_countryID') : $this->config_model->getConfig('Default Province Option');
            $data['nationality']    = ($this->session->userdata('current_nationality')) ? $this->session->userdata('current_nationality') : $this->config_model->getConfig('Default Nationality Option');
            $data['languages']      = ($this->session->userdata('current_languages')) ? $this->session->userdata('current_languages') : $this->config_model->getConfig('Default Languages Option');
            $data['companyID']      = $this->session->userdata('current_companyID');
            $data['officeID']       = $this->session->userdata('current_officeID');
            $data['divisionID']     = $this->session->userdata('current_divisionID');
            $data['employeeTypeID'] = $this->session->userdata('current_employeeTypeID');
            
            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path.'/create');
            $this->load->view('footer');
//        } else {
//            // no access this page
//            $data['class']  = "danger";
//            $data['msg']    = "Sorry, you don't have access to this page!";
//            $data['urlredirect']    = "";
//            $this->load->view('header', $data);
//            $this->load->view('message');
//            $this->load->view('footer');
//        }
    }
    public function save()
    {
        //load submenu
        $this->submenu();
        $data = $this->data;

        // check role
        if ($this->roles['create']) {
            $this->record->table  = $this->table;
            $this->record->fields = array();
            
            $table_fields = array('empNo','lname','fname','mname','suffix','title','nickname','sex','civilStatus','birthPlace','nationality','bloodType','height',
              'weight','medicalCondition','medicalHistory','languages','telephone','mobile','workEmail','personalEmail','currentStreet',
              'currentBarangayID','currentCityID','currentProvinceID','currentCountryID','provinceStreet','provinceBarangayID',
              'provinceCityID','provinceProvinceID','provinceCountryID','tin','philhealthNo','pagibigNo','sssNo','recommendedBy'
              );
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }   

            $this->record->fields['birthDate']      = date('Y-m-d', strtotime($this->input->post('birthDate')));
            $this->record->fields['dateEmployed']   = date('Y-m-d', strtotime($this->input->post('dateEmployed')));
            $this->record->fields['lastAppointment']= date('Y-m-d', strtotime($this->input->post('dateEmployed')));
            
            if ($this->record->save()) {
                $this->record->fields = array();
                $id = $this->record->where['empID']   = $this->db->insert_id();
                $this->record->retrieve();

                // save employment
                $this->db->set('employmentNo', $this->record->field->empNo);
                $this->db->set('empID', $this->record->field->empID);               
                $this->db->set('companyID', 1);
                $this->db->set('branchID', trim($this->input->post('branchID')));
                $this->db->set('deptID', trim($this->input->post('deptID')));
                $this->db->set('divisionID', trim($this->input->post('divisionID')));
                $this->db->set('employeeTypeID', trim($this->input->post('employeeTypeID')));
                $this->db->set('jobPositionID', trim($this->input->post('jobPositionID')));
                $this->db->set('lname', $this->record->field->lname);
                $this->db->set('mname', $this->record->field->mname);
                $this->db->set('title', $this->record->field->title);
                $this->db->set('dateAppointed', $this->record->field->dateEmployed);
                $this->db->set('dateTerminated', '0000-00-00 00:00:00');
                $this->db->set('salaryType', trim($this->input->post('salaryType')));
                $this->db->set('basicSalary', trim($this->input->post('basicSalary')));
                $this->db->set('withBasicContribution', trim($this->input->post('withBasicContribution')));
                $this->db->insert('employments');
                
                $this->db->where('empID', $id);
                $employment = $this->db->get('employments', 1)->row();                

                // update job position
                $this->db->set('status', 2); // occupied
                $this->db->where('jobPositionID', $employment->jobPositionID);
                $this->db->update('job_positions');                

                // set basic contributions
                if ($this->input->post('withBasicContribution')==1) {
                    $this->db->where('employeeTypeID', $employment->employeeTypeID);
                    $employeeType = $this->db->get('employee_types', 1)->row();
                    
                    $basics = explode(',', $employeeType->basicContributions);
                    if (!empty($basics)) {                                              
                        $this->db->where_in('premiumID', $basics);
                        $this->db->where('status', 1);
                        $premiums = $this->db->get('premiums');
                
                        if ($premiums->num_rows()) {
                            foreach ($premiums->result() as $row) {
                                $this->db->where('empID', $this->record->field->empID);
                                $this->db->where('premiumID', $row->premiumID);
                                $contribution = $this->db->get('contributions', 1)->row();
                                
                                if (!empty($contribution)) {
                                    $this->db->set('payID', $contribution->payID);
                                    $this->db->set('employmentID', $employment->employmentID);
                                    $this->db->set('effectivity', $employment->dateAppointed);
                                    $this->db->set('isAutomatic', 1);
                                    $this->db->set('isWtaxExempted', 1);
                                    $this->db->insert('contribution_details');
                                } else {
                                    $this->db->set('empID', $this->record->field->empID);
                                    $this->db->set('premiumID', $row->premiumID);
                                    $this->db->insert('contributions');
                                    
                                    $this->db->where('empID', $this->record->field->empID);
                                    $this->db->where('premiumID', $row->premiumID);
                                    $contribution = $this->db->get('contributions', 1)->row();
                                    
                                    $this->db->set('payID', $contribution->payID);
                                    $this->db->set('employmentID', $employment->employmentID);
                                    $this->db->set('effectivity', $employment->dateAppointed);
                                    $this->db->set('isAutomatic', 1);
                                    $this->db->set('isWtaxExempted', 1);
                                    $this->db->insert('contribution_details');
                                }
                            }
                        }
                    }
                }                

                // set wittholding tax
                $this->db->set('empID', $id);
                $this->db->set('employmentID', $employment->employmentID);
                $this->db->set('amount', 0);
                $this->db->set('isAutomatic', 1);
                $this->db->insert('tax_withholding');

                // build folder for this employee
                mkdir("assets/records/employee/".$this->record->field->empID);

                // record logs
                $logs = "Record - ".trim($this->input->post($this->logfield));
                $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->field->$data['pfield'], 'Insert', $logs);
                
                //$logfield = $this->pfield;                
                // success msg
                $data['class']  = "success";
                $data['msg']    = $this->module." successfully saved.";
                $data['urlredirect']    = $this->controller_page."/view/".$this->encrypter->encode($this->record->field->empID);
                $this->load->view('header', $data);
                $this->load->view('message');
                $this->load->view('footer');

            } else {
                // error in saving
                $data['class']  = "danger";
                $data['msg']    = "Error in saving the ".strtolower($this->module)."!";
                $data['urlredirect']    = "";
                $this->load->view('header', $data);
                $this->load->view('message');
                $this->load->view('footer');
            }
       } else {
           // no access this page
           $data['class']  = "danger";
           $data['msg']    = "Sorry, you don't have access to this page!";
           $data['urlredirect']    = "";
           $this->load->view('header', $data);
           $this->load->view('message');
           $this->load->view('footer');
       }
    }
    
    public function edit($id)
    {
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);

        if ($this->roles['edit']) {
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->record->table = $this->table;
            // set fields for the current table
            $this->record->setFields();
            // set where
            $this->record->where[$this->pfield] = $id;
            // execute retrieve
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;

            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path.'/edit');
            $this->load->view('footer');
        } else {
            // no access this page
            $data['class']  = "danger";
            $data['msg']    = "Sorry, you don't have access to this page!";
            $data['urlredirect']    = "";
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

        $table_fields = array('empNo','lname','fname','mname','suffix','title','nickname','sex','civilStatus','birthPlace','nationality','bloodType','height',
              'weight','medicalCondition','medicalHistory','languages','telephone','mobile','workEmail','personalEmail','currentStreet',
              'currentBarangayID','currentCityID','currentProvinceID','currentCountryID','provinceStreet','provinceBarangayID',
              'provinceCityID','provinceProvinceID','provinceCountryID','tin','philhealthNo','pagibigNo','sssNo','recommendedBy'
              );

        if ($this->roles['edit']) {
            $this->record->table  = $this->table;
            $this->record->fields = array();
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }               
            
             $this->record->fields['birthDate']      = date('Y-m-d', strtotime($this->input->post('birthDate')));          

            $this->record->pfield   = $this->pfield;
            $this->record->pk       = $this->encrypter->decode(trim($this->input->post($this->pfield)));
            
            // field logs here
            $wasChange = $this->log_model->field_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Update', $this->record->fields);
            
            if ($this->record->update()) {  
                // record logs
                if ($wasChange) {
                    $logs = "Record - ".trim($this->input->post($this->logfield));
                    $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->pk, 'Update', $logs);
                }
                // Successfully updated
                $data['class']  = "success";
                $data['msg']    = $this->module." successfully updated.";
                $data['urlredirect']    = $this->controller_page."/view/".$this->encrypter->encode($this->record->pk);
                $this->load->view('header', $data);
                $this->load->view('message');
                $this->load->view('footer');
            } else {
                // Error updating
                $data['class']  = "success";
                $data['msg']    = "Error in updating the ".strtolower($this->module)."!";
                $data['urlredirect']    = $this->controller_page."/view/".$this->encrypter->encode($this->record->pk);
                $this->load->view('header', $data);
                $this->load->view('message');
                $this->load->view('footer');
            }

        } else {
            // no access this page
            $data['class']  = "danger";
            $data['msg']    = "Sorry, you don't have access to this page!";
            $data['urlredirect']    = "";
            $this->load->view('header', $data);
            $this->load->view('message');
            $this->load->view('footer');
        }
    }

    public function delete($id=0)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);

        if ($this->roles['delete'] && !$this->_inUsed($id)) {
            $this->record->fields = array();
            $this->record->table  = $this->table;
            $this->record->where[$this->pfield] = $id;
            $this->record->retrieve();
            if (!empty($this->record->field)) {
                $this->record->pfield   = $this->pfield;
                $this->record->pk       = $id;
                // record logs
                $logfield = $this->logfield;
                if ($this->record->delete()) {
                    $logs = "Record - ".$this->record->field->$logfield;
                    $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Delete', $logs);
                    //Successfully deleted
                    $data['class']  = "success";
                    $data['msg']    = $this->module." successfully deleted.";
                    $data['urlredirect']    = $this->controller_page."/show";
                    $this->load->view('header', $data);
                    $this->load->view('message');
                    $this->load->view('footer');
                } else {
                    //Error Deleting
                    $data['class']  = "danger";
                    $data['msg']    = "Error in deleting the ".strtolower($this->module)."!";
                    $data['urlredirect']    = "";
                    $this->load->view('header', $data);
                    $this->load->view('message');
                    $this->load->view('footer');
                }
            } else {
                //Record not found
                $data['class']  = "danger";
                $data['msg']    = $this->module." record not found!";
                $data['urlredirect']    = "";
                $this->load->view('header', $data);
                $this->load->view('message');
                $this->load->view('footer');
            }

        } else {
            //No access this page
            $data['class']  = "danger";
            $data['msg']    = "Sorry, you don't have access to this page!";
            $data['urlredirect']    = "";
            $this->load->view('header', $data);
            $this->load->view('message');
            $this->load->view('footer');
        }
    }
    
    public function view($id)
    {
        // load submenu
        $id = $this->encrypter->decode($id);
        
        // load submenu
        $this->submenu();
        $data = $this->data;

        // I think this is only for ess
        // Original condition is
        //$this->roles['view'] || ($this->session->userdata('current_userType')=='ess' && $this->session->userdata('current_empID')==$id)
        if ($this->roles['view']) {
            // Setup view
            $this->record->table = $this->table;            
            $this->record->setFields();                 
            $this->record->fields[] = $this->table.'.*';
            $this->record->fields[] = 'currentCountry.country as currentCountry';
            $this->record->fields[] = 'currentProvince.province as currentProvince';
            $this->record->fields[] = 'currentCity.city as currentCity';
            $this->record->fields[] = 'currentCity.zipcode as currentZipcode';
            $this->record->fields[] = 'currentBarangay.barangay as currentBarangay';
            $this->record->fields[] = 'provinceCountry.country as provinceCountry';
            $this->record->fields[] = 'provinceProvince.province as provinceProvince';
            $this->record->fields[] = 'provinceCity.city as provinceCity';
            $this->record->fields[] = 'provinceCity.zipcode as provinceZipcode';
            $this->record->fields[] = 'provinceBarangay.barangay as provinceBarangay';
            $this->record->fields[] = 'tax_exemptions.exemption';
            //set joins
            $this->record->joins[]  = array('countries currentCountry',$this->table.'.currentCountryID=currentCountry.countryID','left');
            $this->record->joins[]  = array('provinces currentProvince',$this->table.'.currentProvinceID=currentProvince.provinceID','left');
            $this->record->joins[]  = array('cities currentCity',$this->table.'.currentCityID=currentCity.cityID','left');
            $this->record->joins[]  = array('barangays currentBarangay',$this->table.'.currentBarangayID=currentBarangay.barangayID','left');
            $this->record->joins[]  = array('countries provinceCountry',$this->table.'.provinceCountryID=provinceCountry.countryID','left');
            $this->record->joins[]  = array('provinces provinceProvince',$this->table.'.provinceProvinceID=provinceProvince.provinceID','left');
            $this->record->joins[]  = array('cities provinceCity',$this->table.'.provinceCityID=provinceCity.cityID','left');
            $this->record->joins[]  = array('barangays provinceBarangay',$this->table.'.provinceBarangayID=provinceBarangay.barangayID','left');
            $this->record->joins[]  = array('tax_exemptions',$this->table.'.taxID=tax_exemptions.taxID','left');
            // set where
            $this->record->where[$this->table.'.'.$this->pfield] = $id;
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;
            
            $this->db->select('employments.*');
            $this->db->select('employees.fname');
            $this->db->select('employees.suffix');
            $this->db->select('departments.deptName');
            $this->db->select('departments.deptCode');
            $this->db->select('branches.branchName');
            $this->db->select('branches.branchCode');
            $this->db->select('divisions.divisionName');
            $this->db->select('divisions.divisionCode');
            $this->db->select('employee_types.employeeType');
            $this->db->select('job_positions.positionCode');
            $this->db->select('job_titles.jobTitle');
            $this->db->join('employees','employments.empID=employees.empID', 'left');
            $this->db->join('branches','employments.branchID=branches.branchID', 'left');
            $this->db->join('departments','employments.deptID=departments.deptID', 'left');
            $this->db->join('divisions','employments.divisionID=divisions.divisionID', 'left');
            $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID', 'left');
            $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
            $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
            $this->db->where('employments.empID', $id);
            $this->db->where('employments.status', 1);
            $data['employment'] = $this->db->get('employments', 1)->row();
            
//             $this->session->set_userdata('current_employee_tab', $data['view_tab']);
//             $this->session->set_userdata('current_empID', $this->record->field->empID);
//             $data['startDate']  = ($this->session->userdata('current_startDate')) ? $this->session->userdata('current_startDate') : date('m/d/Y', strtotime(date('Y').'-'.date('m').'-01'));
//             $data['endDate']    = ($this->session->userdata('current_endDate')) ? $this->session->userdata('current_endDate') : date('m/d/Y', strtotime(date('Y').'-'.date('m').'-'.date('t')));

            // record logs
            if ($this->config_model->getConfig('Log all record views') == '1') {
                $logfield = $this->logfield;
                $logs = "Record - ".$this->record->field->$logfield;
                $this->log_model->table_logs($this->data['current_module']['module_label'], $this->table, $this->pfield, $this->record->field->$data['pfield'], 'View', $logs);
            }

            // check if record is used in other tables
            $data['inUsed'] = $this->_in_used($id);

            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path.'/view');
            $this->load->view($this->module_path.'/family_member');
            $this->load->view($this->module_path.'/attachment');
            $this->load->view($this->module_path.'/education_background');
            $this->load->view($this->module_path.'/service_eligibility');
            $this->load->view($this->module_path.'/work_experience');
            $this->load->view($this->module_path.'/training_program');
            $this->load->view('footer');

       } else {
           // no access this page
           $data['class']  = "danger";
           $data['msg']    = "Sorry, you don't have access to this page!";
           $data['urlredirect']    = "";
           $this->load->view('header', $data);
           $this->load->view('message');
           $this->load->view('footer');
       }
    }
    
    public function show()
    {
        //************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;
        
        // **************************************************
        // variable:field:default_value:operator
        // note: dont include the special query field filter                
        $condition_fields = array(
			array('variable'=>'empNo', 'field'=>$this->table.'.empNo', 'default_value'=>'', 'operator'=>'like_both'),
			array('variable'=>'lname', 'field'=>$this->table.'.lname', 'default_value'=>'', 'operator'=>'like_both'),
			array('variable'=>'fname', 'field'=>$this->table.'.fname', 'default_value'=>'', 'operator'=>'like_both'),
			array('variable'=>'mname', 'field'=>$this->table.'.mname', 'default_value'=>'', 'operator'=>'like_both'),
			array('variable'=>'sex', 'field'=>$this->table.'.sex', 'default_value'=>'', 'operator'=>'where'),
			array('variable'=>'civilStatus', 'field'=>$this->table.'.civilStatus', 'default_value'=>'', 'operator'=>'where'),
			array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
		);
		
		// sorting fields
		$sorting_fields = array('empID'=>'desc');
        
        $controller = $this->uri->segment(1);
        
        if ($this->uri->segment(3))
            $offset = $this->uri->segment(3);
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
                if ($this->session->userdata($controller.'_'.$key['variable']) || $this->session->userdata($controller.'_sortby') || $this->session->userdata($controller.'_sortorder')) {
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

                $sortby     = trim($this->input->post('sortby'));
                $sortorder  = trim($this->input->post('sortorder'));
                
                break;
            case 2:
                foreach($condition_fields as $key) {
                    $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
                }
                
                $sortby     = $this->session->userdata($controller.'_sortby');
                $sortorder  = $this->session->userdata($controller.'_sortorder');
                break;
            default:
                foreach($condition_fields as $key) {
                    $$key['variable'] = $key['default_value'];
                }
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
            $limit = 25; // default limit
        }
        
        // set session variables
        foreach($condition_fields as $key) {
            $this->session->set_userdata($controller.'_'.$key['variable'], $$key['variable']);
        }
        $this->session->set_userdata($controller.'_sortby', $sortby);
        $this->session->set_userdata($controller.'_sortorder', $sortorder);
        $this->session->set_userdata($controller.'_limit', $limit);
            
        // assign data variables for views
        foreach($condition_fields as $key) {
            $data[$key['variable']] = $$key['variable'];
        }
        
        // select
        $this->db->select($this->table.'.*');

        // from
        $this->db->from($this->table);
        
        // join
        
        // where
        // set conditions here
        foreach($condition_fields as $key) {
            $operators = explode('_',$key['operator']);
            $operator  = $operators[0];
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
        
        // get
        $data['ttl_rows'] = $config['total_rows'] = $this->db->count_all_results();

        // set pagination   
        $config['full_tag_open']    = "<ul class='pagination'>";
        $config['full_tag_close']   = "</ul>";
        $config['num_tag_open']     = "<li class='page-order'>";
        $config['num_tag_close']    = "</li>";
        $config['cur_tag_open']     = "<li class='page-order active'>";
        $config['cur_tag_close']    = "</li>";
        $config['next_tag_open']    = "<li class='page-order'>";
        $config['next_tagl_close']  = "</li>";
        $config['prev_tag_open']    = "<li class='page-order'>";
        $config['prev_tagl_close']  = "</li>";
        $config['first_tag_open']   = "<li class='page-order'>";
        $config['first_tagl_close'] = "</li>";
        $config['last_tag_open']    = "<li class='page-order'>";
        $config['last_tagl_close']  = "</li>";
        
        $config['base_url'] = $this->controller_page.'/show/';
        $config['per_page'] = $limit;
        $this->pagination->initialize($config);
        
        // select
        $this->db->select($this->table.'.*');

        // from
        $this->db->from($this->table);
        
        // join
        
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
        $data['offset']     = $offset;
        
        // get
        $data['records'] = $this->db->get()->result();
        // load views
        $this->load->view('header', $data);
        $this->load->view($this->module_path.'/list');
        $this->load->view('footer');
    }
    
    public function printlist()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        //sorting
        
        // variable:field:default_value:operator
        // note: dont include the special query field filter
        $condition_fields = array(
			array('variable'=>'empNo', 'field'=>$this->table.'.empNo', 'default_value'=>'', 'operator'=>'like_both'),
			array('variable'=>'lname', 'field'=>$this->table.'.lname', 'default_value'=>'', 'operator'=>'like_both'),
			array('variable'=>'fname', 'field'=>$this->table.'.fname', 'default_value'=>'', 'operator'=>'like_both'),
			array('variable'=>'mname', 'field'=>$this->table.'.mname', 'default_value'=>'', 'operator'=>'like_both'),
			array('variable'=>'sex', 'field'=>$this->table.'.sex', 'default_value'=>'', 'operator'=>'where'),
			array('variable'=>'civilStatus', 'field'=>$this->table.'.civilStatus', 'default_value'=>'', 'operator'=>'where'),
			array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
		);
		
		// sorting fields
		$sorting_fields = array('empID'=>'desc');
        
        $controller = $this->uri->segment(1);
        
        foreach($condition_fields as $key) {
            $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
        }
        
        $limit      = $this->session->userdata($controller.'_limit');
        $offset     = $this->session->userdata($controller.'_offset');
        $sortby     = $this->session->userdata($controller.'_sortby');
        $sortorder  = $this->session->userdata($controller.'_sortorder');
        
        // select
        $this->db->select($this->table.'.*');

        // from
        $this->db->from($this->table);
        
        // join
        
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
        $data['offset']     = $offset;
        
        // get
        $data['records'] = $this->db->get()->result();
        
        $data['title'] = $data['current_module']['module_label']." List";

        //load views
        $this->load->view('header_print', $data);
        $this->load->view($this->module_path.'/printlist');
        $this->load->view('footer_print');
    }
    
    function exportlist()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        //sorting
    
        // variable:field:default_value:operator
        // note: dont include the special query field filter
        $condition_fields = array(
			array('variable'=>'empNo', 'field'=>$this->table.'.empNo', 'default_value'=>'', 'operator'=>'like_both'),
			array('variable'=>'lname', 'field'=>$this->table.'.lname', 'default_value'=>'', 'operator'=>'like_both'),
			array('variable'=>'fname', 'field'=>$this->table.'.fname', 'default_value'=>'', 'operator'=>'like_both'),
			array('variable'=>'mname', 'field'=>$this->table.'.mname', 'default_value'=>'', 'operator'=>'like_both'),
			array('variable'=>'sex', 'field'=>$this->table.'.sex', 'default_value'=>'', 'operator'=>'where'),
			array('variable'=>'civilStatus', 'field'=>$this->table.'.civilStatus', 'default_value'=>'', 'operator'=>'where'),
			array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
		);
		
		// sorting fields
		$sorting_fields = array('empID'=>'desc');
    
        $controller = $this->uri->segment(1);
    
        foreach($condition_fields as $key) {
            $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
        }
    
        $limit      = $this->session->userdata($controller.'_limit');
        $offset     = $this->session->userdata($controller.'_offset');
        $sortby     = $this->session->userdata($controller.'_sortby');
        $sortorder  = $this->session->userdata($controller.'_sortorder');
    
        // select
        $this->db->select($this->table.'.*');
    
        // from
        $this->db->from($this->table);
    
        // join
    
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
        $data['offset']     = $offset;
    
        // get
        $records = $this->db->get()->result();
    
    
        $title          = $data['current_module']['module_label']." List";
        $companyName    = $this->config_model->getConfig('Company');
        $address        = $this->config_model->getConfig('Address');
         
        //XML Blurb
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
                    <NumberFormat ss:Format='#,##0.00_ ;[Red]\-#,##0.00\ '/>
    		      <Borders>
                    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
                   </Borders>
            </Style>
    		</Styles>
    
    		<Worksheet ss:Name='".$title."'>
    
    		<Table>
    		<Column ss:Index='1' ss:AutoFitWidth=\"1\" ss:Width='25.00'/>
    		<Column ss:Index='2' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
    		<Column ss:Index='3' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
    		<Column ss:Index='4' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
    		<Column ss:Index='5' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
    		<Column ss:Index='6' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
    		<Column ss:Index='7' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
    		<Column ss:Index='8' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
    		<Column ss:Index='9' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
    		<Column ss:Index='10' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
    		    ";
    
        // header
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='10'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
    
        $data .= "<Row ss:StyleID='s20'>";
        $data .= "<Cell ss:MergeAcross='10'><Data ss:Type='String'>".$companyName."</Data></Cell>";
        $data .= "</Row>";
        $data .= "<Row ss:StyleID='s24A'>";
        $data .= "<Cell ss:MergeAcross='10'><Data ss:Type='String'>".$address."</Data></Cell>";
        $data .= "</Row>";
    
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='10'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
         
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='10'><Data ss:Type='String'>".strtoupper($title)."</Data></Cell>";
        $data .= "</Row>";
         
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='10'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
    
        $fields[]="#";
        $fields[]="ID NUMBER";
        $fields[]="LAST NAME";
        $fields[]="FIRST NAME";
        $fields[]="MIDDLE NAME";
        $fields[]="BIRTH DATE";
        $fields[]="AGE";
        $fields[]="SEX";
        $fields[]="CIVIL STATUS";
        $fields[]="STATUS";
    
        $data .= "<Row ss:StyleID='s24'>";
        //Field Name Data
        foreach ($fields as $fld) {
            $data .= "<Cell ss:StyleID='s23'><Data ss:Type='String'>$fld</Data></Cell>";
        }
        $data .= "</Row>";
    
        if (count($records)) {
            $ctr = 1;
            foreach ($records as $row) {
                if($row->birthDate!="0000-00-00") {
                    $bday = date("M d, Y",strtotime($row->birthDate));
                    $age  = (date('Y') - date('Y',strtotime($row->birthDate)));
                } else {
                    $bday = '';
                    $age = '';
                }
                
                if ($row->sex == 'M') {
                    $sex = "Male";
                } else {
                    $sex = "Female";
                }
                
                $data .= "<Row>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$ctr.".</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->empNo."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->lname."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->fname."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->mname."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$bday."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$age."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$sex."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->civilStatus."</Data></Cell>";
                if  ($row->status == 1) {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'>Active</Data></Cell>";
                } else {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'>Inactive</Data></Cell>";
                }
                $data .= "</Row>";
    
                $ctr++;
            }
        }
        $data .= "</Table></Worksheet>";
        $data .= "</Workbook>";
         
    
        //Final XML Blurb
        $filename = "Employee List";
    
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$filename.xls;");
        header("Content-Type: application/ms-excel");
        header("Pragma: no-cache");
        header("Expires: 0");
         
        echo $data;
    
    }

    public function check_duplicate()
    {
        $this->record->table = $this->table;
        $this->record->where['empNo']  = trim($this->input->post('empNo'));
        $this->record->retrieve();
        if (!empty($this->record->field))
            echo "1"; // duplicate
        else 
            echo "0";
    }





    // More Functions
    public function batch_update($colField="")
    {
        // load submenu
        $this->submenu();
        $data = $this->data;   
    }

    public function custom_list($colField="")
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
    }

    public function print_custom_list($colField="0")
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
    }

	public function upload()
	{
		//************** general settings *******************
		// load submenu
		$this->submenu();
		$data = $this->data;
		//************** end general settings *******************
	
		// check roles
		$this->db->where('empID',$this->input->post('imgID'));
		$data['rec'] = $this->db->get('employees')->row();
		// upload picture
        $config['upload_path']  = 'assets/records/employee/'.$data['rec']->empID.'/';
        $config['allowed_types']= 'jpg|png|bmp|JPG|JPEG|PNG|BMP';
        $config['max_size']     = '5000';
        $config['max_width']    = '2000';
        $config['max_height']   = '2000';
				 
		$this->load->library('upload', $config);
				 
		if (is_file($_FILES['userfile']['tmp_name'])) {
			
			// delete first the existing image
			@unlink($config['upload_path'].'avatar.'.$data['rec']->imageExtension);
			@unlink($config['upload_path'].'avatar_thumb'.$data['rec']->imageExtension);
			
			if ( ! $this->upload->do_upload()) {
				$error = array('error' => $this->upload->display_errors());
			} else {
				// rename uploaded image
				$data['upload_data'] = $this->upload->data();
				$fn = 'avatar';
				rename($config['upload_path'].$data['upload_data']['file_name'], $config['upload_path'].$fn.$data['upload_data']['file_ext']);
				
				// save image extension to database
				$this->db->where('empID',$data['rec']->empID);
				$this->db->update('employees',array('imageExtension'=>$data['upload_data']['file_ext']));
				
				// generate thumbnail
				$this->_create_thumbnail($config['upload_path'].$fn.$data['upload_data']['file_ext']);
					
				$data['class']  = "success";
                $data['msg']    = $this->module." image successfully saved.";
                $data['urlredirect']   = $this->controller_page."/view/".$this->encrypter->encode($this->input->post('imgID'));
                $this->load->view('header', $data); 
                $this->load->view('message');
                $this->load->view('footer');
			}
		} else {
			// error
			$data["display"] = "block";
			$data["class"] 	 = "errorbox";
			$data["msg"] 	 = "Error in uploading image!<br>See manual for reference.";
			$data["urlredirect"] = "refresh";
			$this->load->view("header_popup",$data);
			$this->load->view("message",$data);
			$this->load->view("footer_popup");
		}									
	}
	
	public function upload_attachment()
	{
	    //************** general settings *******************
	    // load submenu
	    $this->submenu();
	    $data = $this->data;
	    //************** end general settings *******************
	
	    // check roles
	    $this->db->where('empID',$this->input->post('fempID'));
	    $data['rec'] = $this->db->get('employees')->row();
	    // upload picture
	    $config['upload_path']  = 'assets/records/employee/'.$data['rec']->empID.'/';
	    $config['allowed_types']= 'jpg|png|bmp|JPG|JPEG|PNG|BMP|PDF|DOC|DOCX|pdf|doc|docx';
	    $config['max_size']     = '5000';
	    $config['max_width']    = '2000';
	    $config['max_height']   = '2000';
	    	
	    $this->load->library('upload', $config);
	    	
	    if (is_file($_FILES['fuserfile']['tmp_name'])) {
	        if ( ! $this->upload->do_upload('fuserfile')) {
	            $error = array('error' => $this->upload->display_errors());
	            
	            // error
	            $data["display"] = "block";
	            $data["class"] 	 = "errorbox";
	            $data["msg"] 	 = $this->upload->display_errors();
	            $data['urlredirect']   = $this->controller_page."/view/".$this->encrypter->encode($this->input->post('fempID'));
	            $this->load->view("header",$data);
	            $this->load->view("message",$data);
	            $this->load->view("footer");
	        } else {
	            // rename uploaded file
	            $data['upload_data'] = $this->upload->data();
	            
	            $this->db->set('empID', $data['rec']->empID);
	            $this->db->set('fileName', trim($this->input->post('filename')));
	            $this->db->set('description', trim($this->input->post('description')));
	            $this->db->set('fileExt', $data['upload_data']['file_ext']);
	            $this->db->set('dateUploaded', date('Y-m-d H:i:s'));
	            $this->db->insert('employee_attachments');
	            $attachmentID = $this->db->insert_id();
	            
	            $fn = 'a'.$attachmentID;
	            rename($config['upload_path'].$data['upload_data']['file_name'], $config['upload_path'].$fn.$data['upload_data']['file_ext']);
	
	            $data['class']  = "success";
	            $data['msg']    = $this->module." file successfully uploaded.";
	            $data['urlredirect']   = $this->controller_page."/view/".$this->encrypter->encode($this->input->post('fempID'));
	            $this->load->view('header', $data);
	            $this->load->view('message');
	            $this->load->view('footer');
	        }
	    } else {
	        // error
	        $data["display"] = "block";
	        $data["class"] 	 = "errorbox";
	        $data["msg"] 	 = "Error in uploading file!<br>See manual for reference.";
	        $data["urlredirect"] = "refresh";
	        $this->load->view("header_popup",$data);
	        $this->load->view("message",$data);
	        $this->load->view("footer_popup");
	    }
	}
	
	public function upload_image($id)
	{
		//************** general settings *******************
		// load submenu
		$this->submenu();
		$data = $this->data;
		//************** end general settings *******************
	
		if ($this->roles['edit']) {
			$this->db->where('empID',$id);
			$data['rec'] = $this->db->get('employees')->row();
			// load views
			$this->load->view('header_popup', $data);
			$this->load->view('modules/assets/img/employees', $data);
			$this->load->view('footer_popup');
		} else {
			// error
			$data["display"] = "block";
			$data["class"] 	 = "errorbox";
			$data["msg"] 	 = "Sorry, you don't have access to this page!";
			$data["urlredirect"] = "";
			$this->load->view("header_popup",$data);
			$this->load->view("message",$data);
			$this->load->view("footer_popup");
		}
	}

    public function print_record($empID)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $data['title'] = "Personal Data Sheet";
        // check roles
        if ($this->roles['view']) {
            $empID  = $this->encrypter->decode($empID);
            
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->record->table = $this->table;            
            // set fields for the current table
            $this->record->setFields();         
            // extend fields - join tables          
            $this->record->fields[] = 'currentCountry.country as currentCountry';
            $this->record->fields[] = 'currentProvince.province as currentProvince';
            $this->record->fields[] = 'currentCity.city as currentCity';
            $this->record->fields[] = 'currentCity.zipcode as currentZipcode';
            $this->record->fields[] = 'currentBarangay.barangay as currentBarangay';
            $this->record->fields[] = 'provinceCountry.country as provinceCountry';
            $this->record->fields[] = 'provinceProvince.province as provinceProvince';
            $this->record->fields[] = 'provinceCity.city as provinceCity';
            $this->record->fields[] = 'provinceCity.zipcode as provinceZipcode';
            $this->record->fields[] = 'provinceBarangay.barangay as provinceBarangay';
            $this->record->fields[] = 'tax_exemptions.exemption';
            
            // set joins
            $this->record->joins[]  = array('countries currentCountry',$this->table.'.currentCountryID=currentCountry.countryID','left');
            $this->record->joins[]  = array('provinces currentProvince',$this->table.'.currentProvinceID=currentProvince.provinceID','left');
            $this->record->joins[]  = array('cities currentCity',$this->table.'.currentCityID=currentCity.cityID','left');
            $this->record->joins[]  = array('barangays currentBarangay',$this->table.'.currentBarangayID=currentBarangay.barangayID','left');
            $this->record->joins[]  = array('countries provinceCountry',$this->table.'.provinceCountryID=provinceCountry.countryID','left');
            $this->record->joins[]  = array('provinces provinceProvince',$this->table.'.provinceProvinceID=provinceProvince.provinceID','left');
            $this->record->joins[]  = array('cities provinceCity',$this->table.'.provinceCityID=provinceCity.cityID','left');
            $this->record->joins[]  = array('barangays provinceBarangay',$this->table.'.provinceBarangayID=provinceBarangay.barangayID','left');
            $this->record->joins[]  = array('tax_exemptions',$this->table.'.taxID=tax_exemptions.taxID','left');
            
            // set where
            $this->record->where[$this->table.'.'.$this->pfield] = $empID;
            
            // execute retrieve
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;
    
            $data['pdf_paging'] = TRUE;
            $data['title']      = "PERSONAL DATA SHEET";
            $data['modulename'] = "PERSONAL DATA SHEET";            
    
            // load pdf class
            $this->load->library('mpdf');
            // load pdf class
            $this->mpdf->mpdf('en-GB',array(215.9,330.2),10,'Garamond',10,10,25,10,0,0,'P');
            $this->mpdf->setTitle($data['title']);
            $this->mpdf->SetDisplayMode('fullpage');
            $this->mpdf->shrink_tables_to_fit = 1;
            $this->mpdf->SetWatermarkImage(base_url().'images/logo/watermark.png');
            $this->mpdf->watermark_font = 'DejaVuSansCondensed';
            $this->mpdf->watermarkImageAlpha = 0.1;
            $this->mpdf->watermarkImgBehind = TRUE;
            $this->mpdf->showWatermarkImage = TRUE;
    
            // content
            $header = $this->load->view('print_pdf_header', $data, TRUE);
            $this->mpdf->SetHTMLHeader($header);
    
            $footer = $this->load->view('print_pdf_footer', $data, TRUE);
            $this->mpdf->SetHTMLFooter($footer);
    
            $html   = $this->load->view($this->module_path.'/print_record', $data, TRUE);
            $this->mpdf->WriteHTML($html);
    
            $this->mpdf->Output("PERSONAL_DATA_SHEET.pdf","I");
        } else {
            // no access this page
            $data['class']  = "danger";
            $data['msg']    = "Sorry, you don't have access to this page!";
            $data['urlredirect']    = "";
            $this->load->view('header', $data);
            $this->load->view('message');
            $this->load->view('footer');
        }
    }

    public function search()
    {
        $this->db->select('employees.empID');
        $this->db->select('employees.empNo');
        $this->db->select('employees.lname');
        $this->db->select('employees.fname');
        $this->db->select('employees.mname');
        $this->db->select('employees.suffix');
        $this->db->from('employees');
        $this->db->like('employees.empNo', $this->input->post('term'));
        $this->db->or_like('employees.lname', $this->input->post('term'));
        $this->db->or_like('employees.fname', $this->input->post('term'));
        $this->db->or_like('employees.mname', $this->input->post('term'));
        $this->db->or_like('employees.suffix', $this->input->post('term'));
        $this->db->order_by('employees.lname','asc');
        $results = $this->db->get();
    
        $data = array();
        $data['response'] = 'false';
        $data['message']  = array();
    
        if($results->num_rows()) {
            $data['response'] = 'true';
            foreach($results->result() as $res) {
                $info = array();
                $info['value']    = $res->empNo.' - '.$res->lname.', '.$res->fname.' '.$res->mname.' '.$res->suffix;
                $info['id']       = $res->empID;
                $info['category'] = "";
    
                $data['message'][] = $info;
            }
        }
    
        echo json_encode($data);
    }

    public function search_encrypt()
    {
        $this->db->select('employees.empID');
        $this->db->select('employees.empNo');
        $this->db->select('employees.lname');
        $this->db->select('employees.fname');
        $this->db->select('employees.mname');
        $this->db->select('employees.suffix');
        $this->db->from('employees');
        $this->db->like('employees.empNo', $this->input->post('term'));
        $this->db->or_like('employees.lname', $this->input->post('term'));
        $this->db->or_like('employees.fname', $this->input->post('term'));
        $this->db->or_like('employees.mname', $this->input->post('term'));
        $this->db->or_like('employees.suffix', $this->input->post('term'));
        $this->db->order_by('employees.lname','asc');
        $results = $this->db->get();
    
        $data = array();
        $data['response'] = 'false';
        $data['message']  = array();
    
        if($results->num_rows()) {
            $data['response'] = 'true';
            foreach($results->result() as $res) {
                $info = array();
                $info['value']    = $res->empNo.' - '.$res->lname.', '.$res->fname.' '.$res->mname.' '.$res->suffix;
                $info['id']       = $this->encrypter->encode($res->empID);
                $info['category'] = "";
    
                $data['message'][] = $info;
            }
        }
    
        echo json_encode($data);
    }

    private function _create_thumbnail($source="")
    {
        $this->load->library('image_lib');
	
		$config['image_library'] = 'gd2';
		$config['source_image']  = $source;
		$config['create_thumb']  = TRUE;
		$config['maintain_ratio']= FALSE;
		$config['width']  = 50;
		$config['height'] = 50;
	
		$this->image_lib->clear();
		$this->image_lib->initialize($config);
		$this->image_lib->resize();
    }

    public function reenroll($id=0, $pageType=0)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);

        $this->record->table = $this->table;
        $this->record->where[$this->pfield] = $id;
        $this->record->retrieve();

        // check roles
        if ($this->roles['edit']) {
            $this->db->set('isEnrolled', 0);
            $this->db->set('saved', 0);
            $this->db->where('empNo', $this->record->field->empNo);
            $this->db->update('fingerprints');
    
            // record logs
            $logfield = $this->logfield;
            $logs = "Record - ".$this->record->field->$logfield;
            $this->log_model->table_logs($this->module, $this->table, $this->pfield, $id, 'Re-Enroll', $logs);
    
            // successful
            // $data["display"] = "block";
            // $data["class"] = "notificationbox";
            // $data["msg"] = "This Employee is now ready to be re-enrolled.";
            // $data["urlredirect"] = (!$isPopup) ? $this->controller_page."/view/".$this->encrypter->encode($id) : "refresh";
            // $this->load->view("header".$page_type,$data);
            // $this->load->view("message",$data);
            // $this->load->view("footer".$page_type);

        } else {
            // no access this page
            $data['class']  = "danger";
            $data['msg']    = "Sorry, you don't have access to this page!";
            $data['urlredirect']    = "";
            $this->load->view('header', $data);
            $this->load->view('message');
            $this->load->view('footer');
        }
    }

    public function import_data()
    {
        $old = $this->db->get('employees_');
        
        if ($old->num_rows()) {
            foreach ($old->result() as $row) {
                $this->db->set('empNo', $row->empID);
                $this->db->set('lname', $row->lname);
                $this->db->set('fname', $row->fname);
                $this->db->set('mname', $row->mname);
                $this->db->set('nickname', $row->mname);
                $this->db->set('sex', $row->gender);
                switch ($row->cstatus) {
                    case 'M': $this->db->set('civilStatus', 'Married'); break;
                    case 'S': $this->db->set('civilStatus', 'Single'); break;
                }
                
                $this->db->set('birthDate', $row->bday);
                $this->db->set('birthPlace', $row->bplace);
                $this->db->set('nationality', $row->nationality);
                $this->db->set('bloodType', $row->bloodType);
                $this->db->set('height', $row->height);
                $this->db->set('weight', $row->weight);
                $this->db->set('languages', $row->languages);
                $this->db->set('telephone', $row->telephone);
                $this->db->set('mobile', $row->mobile);
                $this->db->set('workEmail', $row->workEmail);
                $this->db->set('personalEmail', $row->personalEmail);
                $this->db->set('currentStreet', $row->currentAdd);
                $this->db->set('provinceStreet', $row->permanentAdd);
                $this->db->set('tin', $row->tin);
                $this->db->set('philhealthNo', $row->philhealthNo);
                $this->db->set('pagibigNo', $row->pagibigNo);
                $this->db->set('sssNo', $row->sssNo);
                if ($row->dateEmployed) {
                    $this->db->set('dateEmployed', $row->dateEmployed);
                }
                if ($row->lastAppointment) {
                    $this->db->set('lastAppointment', $row->lastAppointment);
                }
                $this->db->set('bank', $row->bank);
                $this->db->set('accountNo', $row->accountNo);
                if ($row->recommendedBy) {
                    $this->db->set('recommendedBy', $row->recommendedBy);
                }
                $this->db->insert('employees');
                
                $empID = $this->db->insert_id();
                
                // set ess account
                $this->db->set('userID', md5($empID));
                $this->db->set('empID', $empID);
                $this->db->set('userName', $row->empID);
                $this->db->set('userPswd', md5($row->lname));
                $this->db->set('rstatus', 1);
                $this->db->insert('user_ess');
                
                // build folder for this employee
                mkdir("records/employee/".$row->empID);
                
                // set leave credits
                $this->db->where('employeeTypeID', 1);
                $this->db->group_by('leaveTypeID');
                $credits = $this->db->get('leave_type_earnings');
                
                if ($credits->num_rows()) {
                    foreach ($credits->result() as $credit) {
                        $this->db->set('empID', $empID);
                        $this->db->set('leaveTypeID', $credit->leaveTypeID);
                        $this->db->set('credit', $credit->earning);
                        $this->db->set('lastUpdate', date('Y-m-d H:i:s'));
                        $this->db->insert('leave_credits');
                
                        // post to ledger
                        $this->db->set('empID', $empID);
                        $this->db->set('leaveTypeID', $credit->leaveTypeID);
                        $this->db->set('date', date('Y-m-d H:i:s'));
                        $this->db->set('debit', $credit->earning);
                        $this->db->set('credit', 0);
                        $this->db->set('remarks', 'Initial Credits');
                        $this->db->set('updatedBy', $this->session->userdata('current_userID'));
                        $this->db->insert('leave_credit_ledger');
                    }
                }                                               
                
            }
        }        
    }

    public function update_batch()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;

        // check roles
        if ($this->roles['edit']) {
            $this->record->table  = $this->table;
            $this->record->fields = array();    
            
            if (trim($this->input->post('field')) == 'empNo') {
                $this->db->where('empID', $this->encrypter->decode(trim($this->input->post('id'))));
                $old_empNo = $this->db->get('employees', 1)->row()->empNo;
            }
                        
            if (trim($this->input->post('field')) == 'birthDate') {
                $this->record->fields['birthDate']  = date('Y-m-d', strtotime(trim($this->input->post('value'))));
            } else {
                $this->record->fields[trim($this->input->post('field'))] = trim($this->input->post('value'));
            }
            
            
            
            $this->record->pfield   = $this->pfield;
            $this->record->pk       = $this->encrypter->decode(trim($this->input->post('id')));
                
            // field logs here
            $wasChange = $this->log_model->field_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Update', $this->record->fields);

            if ($this->record->update()) {
                                if (trim($this->input->post('field')) == 'bank') {
                    $this->db->set('bank', trim($this->input->post('value')));
                    $this->db->where('empID', $this->record->pk);
                    $this->db->where('status', 1);
                    $this->db->update('employments');
                }
                
                if (trim($this->input->post('field')) == 'accountNo') {
                    $this->db->set('accountNo', trim($this->input->post('value')));
                    $this->db->where('empID', $this->record->pk);
                    $this->db->where('status', 1);
                    $this->db->update('employments');
                }
                
                if (trim($this->input->post('field')) == 'empNo') {
                    $this->db->set('employmentNo', trim($this->input->post('value')));
                    $this->db->where('empID', $this->record->pk);
                    $this->db->where('status', 1);
                    $this->db->update('employments');
                    
                    // update fingerprint table
                    $this->db->set('empNo', trim($this->input->post('value')));
                    $this->db->where('empNo', $old_empNo);
                    $this->db->update('fingerprints');
                                        
                    // rename folder here
                    rename("records/employee/".$old_empNo, "records/employee/".trim($this->input->post('value')));
                }
                
                // record logs
                if ($wasChange) {
                    $logs = "Record - ".trim($this->input->post('empNo'));
                    $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Update', $logs);
                }
                echo "1";
            } else {
                echo "0";
            }
        } else {
            echo "0";
        }
    }

    function set_ess()
    {
        $employees = $this->db->get('employees');
        
        if ($employees->num_rows()) {
            foreach ($employees->result() as $row) {
                // set ess account
                $this->db->set('userID', md5($row->empID));
                $this->db->set('empID', $row->empID);
                $this->db->set('userName', strtolower($row->fname[0].$row->lname));
                $this->db->set('userPswd', md5(strtolower($row->fname[0].$row->lname)));
                $this->db->set('rstatus', 1);
                $this->db->insert('user_ess');
            }
        }
    }

    public function update_field()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;

        // check roles
        if ($this->roles['edit']) {
            $this->record->table  = $this->table;
            $this->record->fields = array();
    
            $this->record->fields[trim($this->input->post('field'))] = trim($this->input->post('value'));
    
            $this->record->pfield   = $this->pfield;
            $this->record->pk       = $this->encrypter->decode(trim($this->input->post('id')));
    
            // field logs here
            $wasChange = $this->log_model->field_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Update', $this->record->fields);
            if ($this->record->update()) {
                // record logs
                if ($wasChange) {
                    $logs = "Record - ".trim($this->input->post('abbr'));
                    $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Update', $logs);
                }
                echo "1";
            } else {
                echo "0";
            }
        } else {
            echo "0";
        }
    }

    public function update_here()
    {
       $records = $this->db->query("select employees_sample.empID, employees_sample.empNo as empNo_old, employees.empNo as empNo_new from employees_sample left join employees on employees_sample.empID=employees.empID");
        
       foreach($records->result() as $row) {
           $this->db->set('empNo', $row->empNo_new);
           $this->db->where('empNo', $row->empNo_old);
           $this->db->update('fingerprints');
       }
    }





    // Private functions
    private function _in_used($id=0)
    {
        $tables = array('contributions','leave_credits');
        
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
    
    public function show_attachments()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $empID    = $this->encrypter->decode($this->input->post('empID'));
    
        $this->db->select('employee_attachments.*');
        $this->db->select('DATE_FORMAT(employee_attachments.dateUploaded, "%m/%d/%Y %r") as dateUploaded');
        $this->db->where('employee_attachments.empID', $empID);
        $records = $this->db->get('employee_attachments')->result();
        	
        $response->status  = 1;
        $response->records  = $records;
    
        echo json_encode($response);
    }
    
    public function show_educations()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $empID    = $this->encrypter->decode($this->input->post('empID'));
    
        $this->db->select('employee_education_backgrounds.*');
        $this->db->where('empID', $empID);
        $this->db->order_by('yearGrad', 'asc');
        $records = $this->db->get('employee_education_backgrounds')->result();
         
        $response->status  = 1;
        $response->records  = $records;
    
        echo json_encode($response);
    }
    
    public function save_education()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $empID    = $this->encrypter->decode($this->input->post('empID'));
    
        $this->db->set('empID', $empID);
        $this->db->set('level', trim($this->input->post('level')));
        $this->db->set('degree', trim($this->input->post('degree')));
        $this->db->set('school', trim($this->input->post('school')));
        $this->db->set('yearStart', trim($this->input->post('yearStart')));
        $this->db->set('yearGrad', trim($this->input->post('yearGrad')));
        $this->db->set('levelEarn', trim($this->input->post('levelEarn')));
        $this->db->set('honors', trim($this->input->post('honors')));
        $this->db->set('remarks', trim($this->input->post('remarks')));
        $this->db->set('status', trim($this->input->post('status')));
        $this->db->insert('employee_education_backgrounds');
         
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
        $this->db->update('employee_education_backgrounds');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function delete_education()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $this->db->where('educationID', trim($this->input->post('educationID')));
        $this->db->delete('employee_education_backgrounds');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function view_education()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $educationID = trim($this->input->post('educationID'));
    
        $this->db->where('educationID', $educationID);
        $record = $this->db->get('employee_education_backgrounds', 1)->row();
         
        $response->status  = 1;
        $response->record  = $record;
    
        echo json_encode($response);
    }
    
    public function show_eligibilities()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $empID    = $this->encrypter->decode($this->input->post('empID'));
    
        $this->db->select('employee_eligibilities.*');
        $this->db->select('DATE_FORMAT(employee_eligibilities.examDate, "%m/%d/%Y") as examDate');
        $this->db->select('DATE_FORMAT(employee_eligibilities.dateLicense, "%m/%d/%Y") as dateLicense');
        $this->db->select('DATE_FORMAT(employee_eligibilities.dateExpired, "%m/%d/%Y") as dateExpired');
        $this->db->where('employee_eligibilities.empID', $empID);
        $this->db->order_by('employee_eligibilities.examDate', 'asc');
        $records = $this->db->get('employee_eligibilities')->result();
         
        $response->status  = 1;
        $response->records  = $records;
    
        echo json_encode($response);
    }
    
    public function save_eligibility()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $empID    = $this->encrypter->decode($this->input->post('empID'));
    
        $this->db->set('empID', $empID);
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
        $this->db->insert('employee_eligibilities');
         
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
        $this->db->update('employee_eligibilities');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function delete_eligibility()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $this->db->where('eligibilityID', trim($this->input->post('eligibilityID')));
        $this->db->delete('employee_eligibilities');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function view_eligibility()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $eligibilityID = trim($this->input->post('eligibilityID'));
    
        $this->db->select('employee_eligibilities.*');
        $this->db->select('DATE_FORMAT(employee_eligibilities.examDate, "%M %d %Y") as examDate');
        $this->db->select('DATE_FORMAT(employee_eligibilities.dateLicense, "%M %d %Y") as dateLicense');
        $this->db->select('DATE_FORMAT(employee_eligibilities.dateExpired, "%M %d %Y") as dateExpired');
        $this->db->where('eligibilityID', $eligibilityID);
        $record = $this->db->get('employee_eligibilities', 1)->row();
         
        $response->status  = 1;
        $response->record  = $record;
    
        echo json_encode($response);
    }
    
    public function show_experiences()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $empID    = $this->encrypter->decode($this->input->post('empID'));
    
        $this->db->select('employee_work_experiences.*');
        $this->db->select('DATE_FORMAT(employee_work_experiences.startDate, "%m/%d/%Y") as startDate');
        $this->db->select('DATE_FORMAT(employee_work_experiences.endDate, "%m/%d/%Y") as endDate');
        $this->db->where('employee_work_experiences.empID', $empID);
        $records = $this->db->get('employee_work_experiences')->result();
         
        $response->status  = 1;
        $response->records  = $records;
    
        echo json_encode($response);
    }
    
    public function save_experience()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $empID     = $this->encrypter->decode($this->input->post('empID'));
    
        $this->db->set('empID', $empID);
        $this->db->set('type', trim($this->input->post('type')));
        $this->db->set('designation', trim($this->input->post('designation')));
        $this->db->set('company', trim($this->input->post('company')));
        $this->db->set('employment', trim($this->input->post('employment')));
        $this->db->set('basicSalary', trim($this->input->post('basicSalary')));
        $this->db->set('salaryType', trim($this->input->post('salaryType')));
        
        if (trim($this->input->post('startDate')) != '') {
            $this->db->set('startDate', date('Y-m-d', strtotime(trim($this->input->post('startDate')))));
        }
        if (trim($this->input->post('endDate')) != '') {
            $this->db->set('endDate', date('Y-m-d', strtotime(trim($this->input->post('endDate')))));
        }
        
        $this->db->set('remarks', trim($this->input->post('remarks')));
        $this->db->insert('employee_work_experiences');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function update_experience()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $this->db->set('type', trim($this->input->post('type')));
        $this->db->set('designation', trim($this->input->post('designation')));
        $this->db->set('company', trim($this->input->post('company')));
        $this->db->set('employment', trim($this->input->post('employment')));
        $this->db->set('basicSalary', trim($this->input->post('basicSalary')));
        $this->db->set('salaryType', trim($this->input->post('salaryType')));
        
        if (trim($this->input->post('startDate')) != '') {
            $this->db->set('startDate', date('Y-m-d', strtotime(trim($this->input->post('startDate')))));
        } else {
            $this->db->set('startDate', '0000-00-00');
        }
        
        if (trim($this->input->post('endDate')) != '') {
            $this->db->set('endDate', date('Y-m-d', strtotime(trim($this->input->post('endDate')))));
        } else {
            $this->db->set('endDate', '0000-00-00');
        }
        
        $this->db->set('remarks', trim($this->input->post('remarks')));
        $this->db->where('workID', trim($this->input->post('workID')));
        $this->db->update('employee_work_experiences');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function delete_experience()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $this->db->where('workID', trim($this->input->post('workID')));
        $this->db->delete('employee_work_experiences');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function view_experience()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $workID = trim($this->input->post('workID'));
    
        $this->db->select('employee_work_experiences.*');
        $this->db->select('DATE_FORMAT(employee_work_experiences.startDate, "%M %d %Y") as startDate');
        $this->db->select('DATE_FORMAT(employee_work_experiences.endDate, "%M %d %Y") as endDate');
        $this->db->where('workID', $workID);
        $record = $this->db->get('employee_work_experiences', 1)->row();
         
        $response->status  = 1;
        $response->record  = $record;
    
        echo json_encode($response);
    }
    
    public function show_trainings()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $empID    = $this->encrypter->decode($this->input->post('empID'));
    
        $this->db->select('employee_trainings.*');
        $this->db->select('DATE_FORMAT(employee_trainings.startDate, "%m/%d/%Y") as startDate');
        $this->db->select('DATE_FORMAT(employee_trainings.endDate, "%m/%d/%Y") as endDate');
        $this->db->where('employee_trainings.empID', $empID);
        $records = $this->db->get('employee_trainings')->result();
         
        $response->status  = 1;
        $response->records  = $records;
    
        echo json_encode($response);
    }
    
    public function save_training()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $empID     = $this->encrypter->decode($this->input->post('empID'));
    
        $this->db->set('empID', $empID);
        $this->db->set('course', trim($this->input->post('course')));
        $this->db->set('organizer', trim($this->input->post('organizer')));
        $this->db->set('venue', trim($this->input->post('venue')));
        $this->db->set('noHours', trim($this->input->post('noHours')));
    
        if (trim($this->input->post('startDate')) != '') {
            $this->db->set('startDate', date('Y-m-d', strtotime(trim($this->input->post('startDate')))));
        }
        if (trim($this->input->post('endDate')) != '') {
            $this->db->set('endDate', date('Y-m-d', strtotime(trim($this->input->post('endDate')))));
        }
    
        $this->db->set('remarks', trim($this->input->post('remarks')));
        $this->db->insert('employee_trainings');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function update_training()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $this->db->set('course', trim($this->input->post('course')));
        $this->db->set('organizer', trim($this->input->post('organizer')));
        $this->db->set('venue', trim($this->input->post('venue')));
        $this->db->set('noHours', trim($this->input->post('noHours')));
    
        if (trim($this->input->post('startDate')) != '') {
            $this->db->set('startDate', date('Y-m-d', strtotime(trim($this->input->post('startDate')))));
        } else {
            $this->db->set('startDate', '0000-00-00');
        }
    
        if (trim($this->input->post('endDate')) != '') {
            $this->db->set('endDate', date('Y-m-d', strtotime(trim($this->input->post('endDate')))));
        } else {
            $this->db->set('endDate', '0000-00-00');
        }
    
        $this->db->set('remarks', trim($this->input->post('remarks')));
        $this->db->where('trainingID', trim($this->input->post('trainingID')));
        $this->db->update('employee_trainings');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function delete_training()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $this->db->where('trainingID', trim($this->input->post('trainingID')));
        $this->db->delete('employee_trainings');
         
        $response->status  = 1;
    
        echo json_encode($response);
    }
    
    public function view_training()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $trainingID = trim($this->input->post('trainingID'));
    
        $this->db->select('employee_trainings.*');
        $this->db->select('DATE_FORMAT(employee_trainings.startDate, "%M %d %Y") as startDate');
        $this->db->select('DATE_FORMAT(employee_trainings.endDate, "%M %d %Y") as endDate');
        $this->db->where('employee_trainings.trainingID', $trainingID);
        $record = $this->db->get('employee_trainings', 1)->row();
         
        $response->status  = 1;
        $response->record  = $record;
    
        echo json_encode($response);
    }
    
    public function show_employments()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        $empID    = $this->encrypter->decode($this->input->post('empID'));
        
        $this->db->select('employments.*');
        $this->db->select('DATE_FORMAT(employments.dateAppointed, "%m/%d/%Y") as dateAppointed');
        $this->db->select('DATE_FORMAT(employments.dateTerminated, "%m/%d/%Y") as dateTerminated');
        $this->db->select('employees.fname');
        $this->db->select('employees.suffix');
        $this->db->select('departments.deptName');
        $this->db->select('departments.deptCode');
        $this->db->select('branches.branchName');
        $this->db->select('branches.branchCode');
        $this->db->select('divisions.divisionName');
        $this->db->select('divisions.divisionCode');
        $this->db->select('employee_types.employeeType');
        $this->db->select('job_positions.positionCode');
        $this->db->select('job_titles.jobTitle');
        $this->db->join('employees','employments.empID=employees.empID', 'left');
        $this->db->join('branches','employments.branchID=branches.branchID', 'left');
        $this->db->join('departments','employments.deptID=departments.deptID', 'left');
        $this->db->join('divisions','employments.divisionID=divisions.divisionID', 'left');
        $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID', 'left');
        $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
        $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
        $this->db->where('employments.empID', $empID);
        $records = $this->db->get('employments')->result();
         
        $response->status  = 1;
        $response->records  = $records;
    
        echo json_encode($response);
    }
}
