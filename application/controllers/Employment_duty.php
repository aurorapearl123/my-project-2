<?php
class Employment_duty extends CI_Controller
{
    //Default Variables
    var $common_menu;
    var $roles;
    var $data;
    var $table;
    var $pfield;
    var $logfield;
    var $module;
    var $module_label;
    var $module_path;
    var $controller_page;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('generic_model','record');
        $this->module       = 'Employee'; //Parent Module 
        $this->data['controller_page']  = $this->controller_page = site_url('employment_duty');// defines contoller link
        $this->table        = 'employment_duties';                                                  // defines the default table
        $this->pfield       = 'employmentDutyID';                                                 // defines primary key
        $this->logfield     = 'duty';                                               // defines field for record log
        $this->module_path  = 'modules/'.strtolower($this->module).'/duty';             // defines module path
        
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
        $this->data['current_module']   = $this->modules[$this->module]['sub']['Employment Duty'];
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
        $this->roles['approve'] = $this->userrole_model->has_access($this->session->userdata('current_userID'),'Approve '.$this->module);
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
        if ($this->roles['create']) {
            $data['required_fields'] = array('empID'=>'Employee', 'employmentID'=>'EmploymentID', 'duty'=>'Duty');
            $data['empID']          = $this->session->userdata('current_empID');
            $data['empNo']          = $this->session->userdata('current_empNo');
            $data['employmentID']   = $this->session->userdata('current_employmentID');
            
            if ($data['empID']) {
                $this->db->where('empID', $data['empID']);
                $query = $this->db->get('employees', 1)->row();
                
                $data['employee_name'] = $query->lname.', '.$query->fname.' '.$query->mname.' '.$query->suffix;
            }

            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path.'/create');
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
    
    public function save()
    {
        // $data = array();
        // $data['success'] = true;
        // echo json_encode($data);
        //load submenu
        $this->submenu();
        $data = $this->data;

        // check role
        if ($this->roles['create']) {
            $this->record->table  = $this->table;
            $this->record->fields = array();

            $table_fields = array('empID', 'employmentID', 'duty');

            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }

            if ($this->record->save()) {
                $this->record->fields = array();
                $this->record->where['empID']       = trim($this->input->post('empID'));
                $this->record->where['employmentID']= trim($this->input->post('employmentID'));
                $this->record->where['duty']        = trim($this->input->post('duty'));
                $this->record->retrieve();   
                $data['rec'] = $this->record->field;       

                // $this->session->set_userdata('current_empID', $this->record->field->empID);
                // $this->session->set_userdata('current_empNo', $this->record->field->empNo);
                // $this->session->set_userdata('current_employmentID', $this->record->field->employmentID);

                // record logs
                // $logs = "Record - ".trim($this->input->post($this->logfield));
                // $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->field->$data['pfield'], 'Insert', $logs);

                // $logfield = $this->pfield;

                // success msg
                // echo $rec
                $data['success'] = true;
                $data['message'] = 'Successfully saved.';
                echo json_encode($data);
                // $data['class']  = "success";
                // $data['msg']    = $this->module." successfully saved.";
                // $data['urlredirect']    = $this->controller_page.'';
                // $this->load->view('header', $data);
                // $this->load->view('message');
                // $this->load->view('footer');
            } else {
                // Unable to save
                $data['success'] = false;
                $data['message'] = 'Error saving data.';
                echo json_encode($data);
                // $data['class']  = "danger";
                // $data['msg']    = "Error in saving the ".strtolower($this->module)."!";
                // $data['urlredirect']    = "";
                // $this->load->view('header', $data);
                // $this->load->view('message');
                // $this->load->view('footer');
            }
        } else {
            // no access this page
            $data['success'] = false;
            $data['message'] = 'Unauthorized request.';
            echo json_encode($data);
            // $data['class']  = "danger";
            // $data['msg']    = "Sorry, you don't have access to this page!";
            // $data['urlredirect']    = "";
            // $this->load->view('header', $data);
            // $this->load->view('message');
            // $this->load->view('footer');
        }
    }
    
    public function edit($id)
    {
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);

        if ($this->roles['edit']) {
            //Retrieve record object
            $this->record->table = $this->table;
            $this->record->setFields();
            $this->record->where[$this->table.'.'.$this->pfield] = $id;
            $this->record->retrieve();
            $data['rec'] = $this->record->field;
            
            if ($this->record->field->empID) {
                $this->db->where('empID', $this->record->field->empID);
                $query = $this->db->get('employees', 1)->row();

                $data['employee_name'] = $query->lname.', '.$query->fname.' '.$query->mname.' '.$query->suffix;
            }

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
        $table_fields = array('empID', 'employmentID', 'duty');

        if ($this->roles['edit']) {
            $this->record->table  = $this->table;
            $this->record->fields = array();
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }                   
            
            $this->record->pfield   = $this->pfield;
            $this->record->pk       = trim($this->input->post($this->pfield));
            
            // field logs here
            $wasChange = $this->log_model->field_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Update', $this->record->fields);

            if ($this->record->update()) {
                // record logs
                if ($wasChange) {
                    $logs = "Record - ".trim($this->input->post($this->logfield));
                    $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Update', $logs);
                }                

                // Successfully updated
                $data['class']  = "success";
                $data['msg']    = $this->module." successfully updated.";
                $data['urlredirect']    = "";
                $this->load->view('header', $data);
                $this->load->view('message');
                $this->load->view('footer');
            } else {
                // Error updating
                $data['class']  = "danger";
                $data['msg']    = "Error in updating the ".strtolower($this->module)."!";
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

    public function delete($id=0)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);

        if ($this->roles['delete'] && !$this->_in_used($id)) {
            //Once retrieved field becomes an object
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
                    // record logs
                    $logs = "Record - ".$this->record->field->$logfield;
                    $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Delete', $logs);

                    //Success msg
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
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);
        // for retrieve with joining tables -------------------------------------------------
        // set table
        $this->record->table = $this->table;
        // set fields for the current table
        $this->record->setFields();
        // extend fields - join tables
        $this->record->fields[] = 'employees.empNo';
        $this->record->fields[] = 'employees.lname';
        $this->record->fields[] = 'employees.fname';
        $this->record->fields[] = 'employees.mname';
        $this->record->fields[] = 'employees.suffix';
        $this->record->fields[] = 'employments.employmentNo';
        $this->record->fields[] = 'companies.companyAbbr';
        $this->record->fields[] = 'offices.officeAbbr';
        $this->record->fields[] = 'detailedCompany.companyName as detailedCompanyAbbr';
        $this->record->fields[] = 'detailedOffice.officeName as detailedofficeAbbr';
        $this->record->fields[] = 'divisions.divisionAbbr';
        $this->record->fields[] = 'employee_types.employeeType';
        $this->record->fields[] = 'job_positions.positionCode';
        $this->record->fields[] = 'job_titles.jobTitle';
        // set joins
        $this->record->joins[]  = array('employees',$this->table.'.empID=employees.empID','left');
        $this->record->joins[]  = array('employments',$this->table.'.employmentID=employments.employmentID','left');
        $this->record->joins[]  = array('companies','employments.companyID=companies.companyID', 'left');
        $this->record->joins[]  = array('offices','employments.officeID=offices.officeID', 'left');
        $this->record->joins[]  = array('companies detailedCompany','employments.detailedCompanyID=detailedCompany.companyID', 'left');
        $this->record->joins[]  = array('offices detailedOffice','employments.detailedOfficeID=detailedOffice.officeID', 'left');
        $this->record->joins[]  = array('divisions','employments.divisionID=divisions.divisionID', 'left');
        $this->record->joins[]  = array('employee_types','employments.employeeTypeID=employee_types.employeeTypeID', 'left');
        $this->record->joins[]  = array('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
        $this->record->joins[]  = array('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
        // set where
        $this->record->where[$this->table.'.'.$this->pfield] = $id;

        // execute retrieve
        $this->record->retrieve();
        // ----------------------------------------------------------------------------------
        $this->roles['view'] = true;

        if ($this->roles['view']) {
            $data['rec'] = $this->record->field;
            // record logs
            if ($this->config_model->getConfig('Log all record views') == '1') {
                $logfield = $this->logfield;
                $logs = "Record - ".$this->record->field->$logfield;
                $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->field->$data['pfield'], 'View', $logs);
            }
            
            // check if record is used in other tables
            $data['inUsed'] = $this->_in_used($id);
            //load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path.'/view');
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
        // load submenu
        $this->submenu();
        $data = $this->data;
        // $tempEmploymentID = $this->encrypter->decode(trim($this->input->post('id'));     
        
        $data['activetab'] = 1; // list page tab
        $data['pageType']  = $pageType;
        switch ($pageType) {
            case 1  : $header = true; $tabs = false; $page = "_popup"; break;// popup refresh
            case 2  : $header = true; $tabs = false; $page = "_popup"; break; // popup reload select
            case 3  : $header = false; $tabs = false; $page = ""; break; // ajax page
            case 0  : $header = true; $tabs = true; $page = ""; break;
        }
        // **************************************************
        // variable:field:default_value:operator
        // note: dont include the special query field filter                
        $condition_fields = array(
            array('variable'=>'empID', 'field'=>$this->table.'.empID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'employmentID', 'field'=>$this->table.'.employmentID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'empNo', 'field'=>'employees.empNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'lname', 'field'=>'employees.lname', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'employmentNo', 'field'=>$this->table.'.employmentNo', 'default_value'=>'', 'operator'=>'like_both'),         
            array('variable'=>'duty', 'field'=>$this->table.'.duty', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('employmentDutyID'=>'desc');
        
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
            $limit = ($pageType) ? 10 : 25; // default limit
        }
        
        if ($pageType==3) {
            foreach($condition_fields as $key) {
                if ($key['variable']=='employmentID') {
                    $$key['variable'] = $this->encrypter->decode(trim($this->input->post('id')));
                }
            }
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
        $this->db->select('employees.empNo');
        $this->db->select('employees.lname');
        $this->db->select('employees.fname');
        $this->db->select('employees.mname');
        $this->db->select('employees.suffix');
        $this->db->select('employments.employmentNo');

        // from
        $this->db->from($this->table);
        
        // join     
        $this->db->join('employees', $this->table.'.empID=employees.empID', 'left');
        $this->db->join('employments', $this->table.'.employmentID=employments.employmentID', 'left');
        
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
        
        // get
        $data['ttl_rows'] = $config['total_rows'] = $this->db->count_all_results();
        
        // set pagination   
        $config['base_url'] = $this->controller_page.'/show/'.$pageType.'/';
        $config['per_page'] = $limit;
        $config['uri_segment'] = 4;
        $this->pagination->initialize($config);     
        
        // select
        $this->db->select($this->table.'.*');
        $this->db->select('employees.empNo');
        $this->db->select('employees.lname');
        $this->db->select('employees.fname');
        $this->db->select('employees.mname');
        $this->db->select('employees.suffix');
        $this->db->select('employments.employmentNo');

        // from
        $this->db->from($this->table);
        
        // join     
        $this->db->join('employees', $this->table.'.empID=employees.empID', 'left');
        $this->db->join('employments', $this->table.'.employmentID=employments.employmentID', 'left');  
        
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
        
        // get
        // $this->db->where('employmentID', $tempEmploymentID);
        $data['records'] = $this->db->get()->result_array(); 

        $data['success'] = true;
        $data['message'] = '';
        echo json_encode($data);
        //load views
        // $this->load->view('header', $data);
        // $this->load->view($this->module_path.'/list');
        // $this->load->view('footer');
    }
    
    public function printlist()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        //sorting

        //load views
        $this->load->view('header', $data);
        $this->load->view($this->module_path.'/printlist');
        $this->load->view('footer');
    }
    

    
    //Conditions and fields changes
    public function check_duplicate()
    {
        $this->record->table = $this->table;
        $this->record->where['empID']       = trim($this->input->post('empID'));
        $this->record->where['employmentID']= trim($this->input->post('employmentID'));
        $this->record->where['duty']        = trim($this->input->post('duty'));
        $this->record->retrieve();
        if (!empty($this->record->field))
            echo "1"; // duplicate
        else 
            echo "0";
    }

    public function duties($id=0)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);
    }

    public function getEmploymentDuties()
    {
        $empID = trim($this->input->post('empID'));
        $employmentID = trim($this->input->post('employmentID'));
        
        $this->db->where('empID', $empID);
        $this->db->where('employmentID', $employmentID);
        $this->db->order_by('employment_duty','asc');
        $records = $this->db->get('employment_dutys');
        echo $this->frameworkhelper->get_json_data($records, 'employmentDutyID', 'duty');
    }

    // public function display_session()
    // {               
    //     echo var_dump($_SESSION);
    // }








    // Private functions
    private function _in_used($id=0)
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
}
