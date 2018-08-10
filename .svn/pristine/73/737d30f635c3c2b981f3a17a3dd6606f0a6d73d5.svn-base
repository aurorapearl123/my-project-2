<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Division extends CI_Controller
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
        $this->load->model('generic_model','records');
        $this->module       = 'Master Files';
        $this->data['current_module']   = $this->modules[$this->module];              // defines the current module
        $this->data['controller_page']  = $this->controller_page = site_url('division');// defines contoller link
        $this->table        = 'divisions';                                                  // defines the default table
        $this->pfield       = 'divisionID';                                                 // defines primary key
        $this->logfield     = 'divisionCode';
        //$this->load->view('modules/'.str_replace(" ","_",strtolower($mod)).'/metadata');                                               // defines field for record log
        $this->module_path  = 'modules/'.strtolower(str_replace(" ","_",$this->module)).'/division';             // defines module path
        
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
        // check roles
        $this->check_roles();
        $this->data['roles']   = $this->roles;
    }
    
    private function check_roles()
    {
        // check roles
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
        //load submenu
        $this->submenu();
        $data = $this->data;
        $table_fields = array('companyID','branchID','deptID','divisionCode','divisionName','divisionAbbr','divisionHeadID','divisionHeadTitle','divisionContact','divisionEmail','remarks');

        // check role
        if ($this->roles['create']) {
            $this->records->table = $this->table;
            $this->records->fields = array();
            
            foreach($table_fields as $fld) {
                $this->records->fields[$fld] = trim($this->input->post($fld));
            }
            
            if ($this->records->save()) {
                $this->records->fields = array();
                $this->records->where[$this->pfield]  = $this->db->insert_id();
                $id=$this->db->insert_id();
                $this->records->retrieve();  
               
                
				// record logs
				$logs = "Record - ".trim($this->input->post($this->logfield));
				$this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->field->$data['pfield'], 'Insert', $logs);
				
				$logfield = $this->pfield;
				// success msg
                $data["class"]   = "success";
                $data["msg"]     = strtolower(str_replace(" ","_",$this->module))." successfully saved.";
                $data["urlredirect"] = $this->controller_page."/view/".$this->encrypter->encode($id);
                $this->load->view("header",$data);
                $this->load->view("message");
                $this->load->view("footer");
               
            } else {
                // error
                $data["class"]   = "danger";
                $data["msg"]     = "Error in saving the ".strtolower($this->module)."!";
                $data["urlredirect"] = "";
                $this->load->view("header",$data);
                $this->load->view("message");
                $this->load->view("footer");
            }
        } else {
            // error
            $data["class"]   = "danger";
            $data["msg"]     = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header",$data);
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
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->records->table = $this->table;
            // set fields for the current table
            $this->records->setFields();
            // set where
            $this->records->where[$this->pfield] = $id;
            // execute retrieve
            $this->records->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->records->field;

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
        $table_fields = array('companyID','branchID','deptID','divisionCode','divisionName','divisionAbbr','divisionHeadID','divisionHeadTitle','divisionContact','divisionEmail','remarks','status');

        // check roles
        if ($this->roles['edit']) {
            $this->records->table = $this->table;
            $this->records->fields = array();
            
            foreach($table_fields as $fld) {
                $this->records->fields[$fld] = trim($this->input->post($fld));
            }
            
            $this->records->pfield   = $this->pfield;
            //$this->records->pk       = $this->encrypter->decode($this->input->post($this->pfield));
            $this->records->pk       = $this->input->post($this->pfield);
            
            // field logs here
            $wasChange = $this->log_model->field_logs($this->module, $this->table, $this->pfield, $this->encrypter->decode($this->input->post($this->pfield)), 'Update', $this->records->fields);
            
            if ($this->records->update()) {
                // record logs
                if ($wasChange) {
                    $logs = "Record - ".trim($this->input->post($this->logfield));
                    $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->records->pk, 'Update', $logs);
                }
                    
                // successful
                $data["class"] = "success";
                $data["msg"] = $this->module." successfully updated.";
                $data["urlredirect"] = $this->controller_page."/view/".$this->encrypter->encode(trim($this->input->post($this->pfield)));
                $this->load->view("header",$data);
                $this->load->view("message");
                $this->load->view("footer");
            } else {
                // error
                $data["class"] = "danger";
                $data["msg"] = "Error in updating the ".strtolower($this->module)."!";
                $data["urlredirect"] = $this->controller_page."/view/".$this->records->pk;
                $this->load->view("header",$data);
                $this->load->view("message");
                $this->load->view("footer");
            }
        } else {
            // error
            $data["class"]   = "danger";
            $data["msg"]     = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header",$data);
            $this->load->view("message");
            $this->load->view("footer");
        }
    }

    public function delete($id=0)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);

        // check roles
        if ($this->roles['delete']) {
            // set fields
            $this->records->fields = array();
            // set table
            $this->records->table = $this->table;
            // set where
            $this->records->where[$this->pfield] = $id;
            // execute retrieve
            $this->records->retrieve();
            
            if (!empty($this->records->field)) {
                $this->records->pfield   = $this->pfield;
                $this->records->pk       = $id;
                
                $logfield = $this->logfield;
                
                // record logs
                                
                if ($this->records->delete()) {
                    
                    // record logs
					$logs = "Record - ".$this->record->field->$logfield;
					$this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Delete', $logs);
                    
                    // successful
                    $data["class"]   = "success";
                    $data["msg"]     = $this->module." successfully deleted.";
                    $data["urlredirect"] = $this->controller_page."/";
                    $this->load->view("header",$data);
                    $this->load->view("message");
                    $this->load->view("footer");
                } else {
                    // error
                    $data["class"]   = "danger";
                    $data["msg"]     = "Error in deleting the ".strtolower($this->module)."!";
                    $data["urlredirect"] = "";
                    $this->load->view("header",$data);
                    $this->load->view("message");
                    $this->load->view("footer");
                }
            } else {
                // error
                $data["class"]   = "danger";
                $data["msg"]     = $this->module." record not found!";
                $data["urlredirect"] = "";
                $this->load->view("header",$data);
                $this->load->view("message");
                $this->load->view("footer");
            }
        } else {
            // error
            $data["class"]   = "danger";
            $data["msg"]     = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header",$data);
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
        // $this->roles['view'] = 1;
        if ($this->roles['view']) {
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->records->table = $this->table;
            
            // set fields for the current table
            $this->records->setFields();
            
            $this->records->fields[]	= $this->table.'.*';
            $this->records->fields[]	= 'companies.companyName';
            $this->records->fields[]	= 'branches.branchName';
            $this->records->fields[]	= 'departments.deptName';
            $this->records->fields[]	= 'employees.fname';
            $this->records->fields[]	= 'employees.lname';
            $this->records->fields[]	= 'employees.mname';
            
			$this->records->joins[]	= array('companies',$this->table.'.companyID=companies.companyID','left');
			$this->records->joins[]	= array('branches',$this->table.'.branchID=branches.branchID','left');
			$this->records->joins[]	= array('departments',$this->table.'.deptID=departments.deptID','left');
			$this->records->joins[]	= array('employees',$this->table.'.divisionHeadID=employees.empID','left');
            
            // set where
            $this->records->where[$this->pfield] = $id;
            
            // execute retrieve
            $this->records->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->records->field;
            //echo $this->db->last_query();
            // record logs
            if ($this->config_model->getConfig('Log all record views') == '1') {
                $logs = "Record - ".$this->records->field->name;
                $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->records->field->$data['pfield'], 'View', $logs);
            }
            
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
        //************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;
        
        $data['activetab'] = 1; // list page tab
        // **************************************************
        // variable:field:default_value:operator
        // note: dont include the special query field filter                
        $condition_fields = array(
            array('variable'=>'divisionCode', 'field'=>$this->table.'.divisionCode', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'divisionName', 'field'=>$this->table.'.divisionName', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'deptID', 'field'=>$this->table.'.deptID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'branchID', 'field'=>$this->table.'.branchID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'companyID', 'field'=>$this->table.'.companyID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'divisionEmail', 'field'=>$this->table.'.divisionEmail', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'divisionAddress', 'field'=>$this->table.'.divisionAddress', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'divisionHeadID', 'field'=>$this->table.'.divisionHeadID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('divisionCode'=>'asc');
        
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
        $this->db->select('companies.companyName');
        $this->db->select('branches.branchName');
        $this->db->select('departments.deptName');
        $this->db->select('employees.fname');
        $this->db->select('employees.lname');
        $this->db->select('employees.mname');
        
        // from
        $this->db->from($this->table);
        
        // join
        $this->db->join('companies',$this->table.'.companyID=companies.companyID','left');
        $this->db->join('branches',$this->table.'.branchID=branches.branchID','left');
        $this->db->join('departments',$this->table.'.deptID=departments.deptID','left');
        $this->db->join('employees',$this->table.'.divisionHeadID=employees.empID','left');
        
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
        $config['base_url'] = $this->controller_page.'/show/';
        $config['per_page'] = $limit;
        $this->pagination->initialize($config);
        
        // select
        $this->db->select($this->table.'.*');
        $this->db->select('companies.companyName');
        $this->db->select('branches.branchName');
        $this->db->select('departments.deptName');
        $this->db->select('employees.fname');
        $this->db->select('employees.lname');
        $this->db->select('employees.mname');
        
        // from
        $this->db->from($this->table);
        
        // join
        $this->db->join('companies',$this->table.'.companyID=companies.companyID','left');
        $this->db->join('branches',$this->table.'.branchID=branches.branchID','left');
        $this->db->join('departments',$this->table.'.deptID=departments.deptID','left');
        $this->db->join('employees',$this->table.'.divisionHeadID=employees.empID','left');
        
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
        $data['records'] = $this->db->get();
        
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
         $condition_fields = array(
            array('variable'=>'divisionCode', 'field'=>$this->table.'.divisionCode', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'divisionName', 'field'=>$this->table.'.divisionName', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'divisionEmail', 'field'=>$this->table.'.divisionEmail', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'divisionAddress', 'field'=>$this->table.'.divisionAddress', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'divisionHeadID', 'field'=>$this->table.'.divisionHeadID', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'divisionContact', 'field'=>$this->table.'.divisionContact', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('divisionCode'=>'asc');

        //load views
        $this->load->view('header', $data);
        $this->load->view($this->module_path.'/printlist');
        $this->load->view('footer');
    }
    
    //Conditions and fields changes
    public function check_duplicate()
    {
        $this->db->where('divisionCode', trim($this->input->post('divisionCode')));
      
        if ($this->db->count_all_results($this->table))
            echo "1"; // duplicate
        else 
            echo "0";
    }


    public function getDivisions()
    {
        // $companyID = trim($this->input->post('companyID'));
        $companyID = 1;
        $branchID  = trim($this->input->post('branchID'));
        
        // if ($this->session->userdata('assigned_companyID')) {
        //     $this->db->where('companyID', $this->session->userdata('assigned_companyID'));
        // }
        // if ($this->session->userdata('assigned_branchID')) {
        //     $this->db->where('branchID', $this->session->userdata('assigned_branchID'));
        // }
        // if ($this->session->userdata('assigned_divisionID')) {
        //     $this->db->where('divisionID', $this->session->userdata('assigned_divisionID'));
        // }
        if ($companyID) {
            $this->db->where('companyID', $companyID);
        }
        if ($branchID) {
            $this->db->where('branchID', $branchID);
        }
        // $this->db->order_by('rank','asc');
        $this->db->order_by('divisionName','asc');
        $records = $this->db->get('divisions');
        echo $this->frameworkhelper->get_json_data($records, 'divisionID', 'divisionName');
    }

    public function getDivisionsEncrypt()
    {
        $companyID = $this->encrypter->decode(trim($this->input->post('companyID')));
        if (trim($this->input->post('branchID'))!="") {
            $officeID  = $this->encrypter->decode(trim($this->input->post('branchID')));
        }
    
        if ($this->session->userdata('assigned_companyID')) {
            $this->db->where('companyID', $this->session->userdata('assigned_companyID'));
        }
        if ($this->session->userdata('assigned_branchID')) {
            $this->db->where('branchID', $this->session->userdata('assigned_branchID'));
        }
        if ($this->session->userdata('assigned_divisionID')) {
            $this->db->where('divisionID', $this->session->userdata('assigned_divisionID'));
        }
        if ($companyID) {
            $this->db->where('companyID', $companyID);
        }
        if ($officeID) {
            $this->db->where('branchID', $branchID);
        }
        $this->db->order_by('rank','asc');
        $this->db->order_by('divisionName','asc');
        $records = $this->db->get('divisions');
        echo $this->frameworkhelper->get_json_data_encrypt($records, 'divisionID', 'divisionName');
    }
    
    public function update_rank()
    {
        $id = trim($this->input->post('id'));
        
        $this->record->table  = $this->table;
        $this->record->fields = array();
        
        $this->record->fields['rank'] = trim($this->input->post('rank'));
        $this->record->pfield   = $this->pfield;
        $this->record->pk       = $id;
        
        // field logs here
        $wasChange = $this->log_model->field_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Update Rank', $this->record->fields);
        
        if ($this->record->update()) {
            $this->record->fields = array();
            $this->record->table  = $this->table;
            $this->record->where[$this->pfield] = $id;
            $this->record->retrieve();
            // record logs
            if ($wasChange) {
                $logfield = $this->logfield;
                $logs = "Record - ".$this->record->field->$logfield;
                $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Update Rank', $logs);
                echo "1";
            } else {
                echo "0";   
            }                        
        } else { 
            echo "0";
        }
    }   

}
