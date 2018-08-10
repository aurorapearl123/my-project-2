<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Incentive extends CI_Controller
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
        $this->module       = 'Salary and Wages';
        $this->table        = 'incentive_details';                                                 
        $this->pfield       = $this->data['pfield'] = 'detailID';                                                 
        $this->logfield     = 'employmentID';
        $this->module_path  = 'modules/salary_and_wages/incentive';           
        $this->data['controller_page']  = $this->controller_page = site_url('incentive');
        
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
        $this->data['current_module']   = $this->modules[$this->module]['sub']['Incentive'];      // defines the current sub module
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
        $table_fields = array('employmentID','amount','isPayment','remarks');

        // check role
        if ($this->roles['create']) {
            $this->db->select('empID');
            $this->db->where('employmentID', trim($this->input->post('employmentID')));
            $employment = $this->db->get('employments', 1)->row();
            
            if (!empty($employment)) {
                $this->db->set('incentiveTypeID', trim($this->input->post('incentiveTypeID')));
                $this->db->set('empID', $employment->empID);
                $this->db->set('remarks', trim($this->input->post('remarks')));
                $this->db->set('status', 1);
                $this->db->insert('incentives');
                $payID = $this->db->insert_id();
                
                $this->records->table = $this->table;
                $this->records->fields = array();
                
                foreach($table_fields as $fld) {
                    $this->records->fields[$fld] = trim($this->input->post($fld));
                }
                
                $this->records->fields['effectivity'] = date('Y-m-d', strtotime(trim($this->input->post('effectivity'))));
                $this->records->fields['payID'] = $payID;
                
                $this->records->fields['status'] = 1;
                
                if ($this->records->save()) {
                    $this->records->fields = array();
                    $pkey = $this->records->where[$this->pfield] = $this->db->insert_id();
                    $this->records->retrieve();  
    				// record logs
    				$logs = "Record - ".trim($this->input->post($this->logfield));
    				$this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->records->field->$data['pfield'], 'Insert', $logs);
    				
    				$logfield = $this->pfield;
    				// success msg
                    $data["class"]   = "success";
                    $data["msg"]     = $this->data['current_module']['module_label']." successfully saved.";
                    $data["urlredirect"] = $this->controller_page."/view/".$this->encrypter->encode($pkey);
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
                $data["msg"]     = "No Employment selected!";
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
            $this->records->fields[] = $this->table.'.*';
            $this->records->fields[] = 'incentives.incentiveTypeID';
            $this->records->joins[]  = array('incentives',$this->table.'.payID=incentives.payID','left');
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
        $table_fields = array('amount','isPayment','remarks','status');

        // check roles
        if ($this->roles['edit']) {
            $this->records->table = $this->table;
            $this->records->fields = array();
            
            foreach($table_fields as $fld) {
                $this->records->fields[$fld] = trim($this->input->post($fld));
            }
            
            $this->records->fields['effectivity'] = date('Y-m-d', strtotime(trim($this->input->post('effectivity'))));
            
            $this->records->pfield   = $this->pfield;
            $this->records->pk       = $this->encrypter->decode($this->input->post($this->pfield));
            
            // field logs here
            $wasChange = $this->log_model->field_logs($this->module, $this->table, $this->pfield, $this->encrypter->decode($this->input->post($this->pfield)), 'Update', $this->records->fields);
            
            if ($this->records->update()) {
                // record logs
                if ($wasChange) {
                    $logs = "Record - ".trim($this->input->post($this->logfield));
                    $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->records->pk, 'Update', $logs);
                }
                    
                // successful
                $data["class"] = "success";
                $data["msg"] = $this->data['current_module']['module_label']." successfully updated.";
                $data["urlredirect"] = $this->controller_page."/view/".trim($this->input->post($this->pfield));
                $this->load->view("header",$data);
                $this->load->view("message");
                $this->load->view("footer");
            } else {
                // error
                $data["class"] = "danger";
                $data["msg"] = "Error in updating the ".strtolower($this->data['current_module']['module_label'])."!";
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
                if (!$this->_in_used($id)) {
                    $this->records->pfield   = $this->pfield;
                    $this->records->pk       = $id;
                    
                    // record logs
                    $logfield = $this->records->field->name;
                                    
                    if ($this->records->delete()) {
                        $this->db->where('payID', $this->records->field->payID);
                        $this->db->delete('incentives');
                        
                        // record logs
    					$logs = "Record - ".$logfield;
    					$this->log_model->table_logs($this->data['current_module']['module_label'], $this->table, $this->pfield, $id, 'Delete', $logs);
                        
                        // successful
                        $data["class"]   = "success";
                        $data["msg"]     = $this->data['current_module']['module_label']." successfully deleted.";
                        $data["urlredirect"] = $this->controller_page."/";
                        $this->load->view("header",$data);
                        $this->load->view("message");
                        $this->load->view("footer");
                    } else {
                        // error
                        $data["class"]   = "danger";
                        $data["msg"]     = "Error in deleting the ".strtolower($this->data['current_module']['module_label'])."!";
                        $data["urlredirect"] = "";
                        $this->load->view("header",$data);
                        $this->load->view("message");
                        $this->load->view("footer");
                    }
                } else {
                    // error
                    $data["class"]   = "danger";
                    $data["msg"]     = $this->data['current_module']['module_label']." cannot be deleted!";
                    $data["urlredirect"] = "";
                    $this->load->view("header",$data);
                    $this->load->view("message");
                    $this->load->view("footer");
                }
            } else {
                // error
                $data["class"]   = "danger";
                $data["msg"]     = $this->data['current_module']['module_label']." record not found!";
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
            $this->db->select($this->table.'.*');
            $this->db->select('incentive_types.name');
            $this->db->select('incentive_types.abbr');
            $this->db->select('employments.lname');
            $this->db->select('employments.mname');
            $this->db->select('employees.empNo');
            $this->db->select('employees.fname');
            $this->db->select('branches.branchCode');
            $this->db->select('branches.branchName');
            $this->db->select('departments.deptCode');
            $this->db->select('departments.deptName');
            $this->db->select('divisions.divisionCode');
            $this->db->select('divisions.divisionName');
            $this->db->from($this->table);
            $this->db->join('incentives', $this->table.'.payID=incentives.payID', 'left');
            $this->db->join('incentive_types', 'incentives.incentiveTypeID=incentive_types.incentiveTypeID', 'left');
            $this->db->join('employments', $this->table.'.employmentID=employments.employmentID', 'left');
            $this->db->join('employees', 'employments.empID=employees.empID', 'left');
            $this->db->join('branches', 'employments.branchID=branches.branchID', 'left');
            $this->db->join('departments', 'employments.deptID=departments.deptID', 'left');
            $this->db->join('divisions', 'employments.divisionID=divisions.divisionID', 'left');
            $this->db->where($this->pfield, $id);
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->db->get()->row();
            
            $data['in_used'] = $this->_in_used($id);
            
            // record logs
            if ($this->config_model->getConfig('Log all record views') == '1') {
                $logs = "Record - ".$this->records->field->name;
                $this->log_model->table_logs($this->data['current_module']['module_label'], $this->table, $this->pfield, $this->records->field->$data['pfield'], 'View', $logs);
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
        
        // **************************************************
        // variable:field:default_value:operator
        // note: dont include the special query field filter                
        $condition_fields = array(
            array('variable'=>'branchID', 'field'=>'employments.branchID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'deptID', 'field'=>'employments.deptID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'divisionID', 'field'=>'employments.divisionID', 'default_value'=>'', 'operator'=>'where'),
			array('variable'=>'empNo', 'field'=>'employees.empNo', 'default_value'=>'', 'operator'=>'like_both'),
			array('variable'=>'lname', 'field'=>'employees.lname', 'default_value'=>'', 'operator'=>'like_both'),
			array('variable'=>'incentiveTypeID', 'field'=>'incentives.incentiveTypeID', 'default_value'=>'', 'operator'=>'where'),
			array('variable'=>'amount', 'field'=>$this->table.'.amount', 'default_value'=>'', 'operator'=>'where'),
			array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
		);
		
		// sorting fields
		$sorting_fields = array('payID'=>'desc');
        
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
        $this->db->select('incentive_types.name');
        $this->db->select('incentive_types.abbr');
        $this->db->select('employments.lname');
        $this->db->select('employments.mname');
        $this->db->select('employees.empNo');
        $this->db->select('employees.fname');
        $this->db->select('branches.branchCode');
        $this->db->select('branches.branchName');
        $this->db->select('departments.deptCode');
        $this->db->select('departments.deptName');
        $this->db->select('divisions.divisionCode');
        $this->db->select('divisions.divisionName');

        // from
        $this->db->from($this->table);
        
        // join
        $this->db->join('incentives', $this->table.'.payID=incentives.payID', 'left');
        $this->db->join('incentive_types', 'incentives.incentiveTypeID=incentive_types.incentiveTypeID', 'left');
        $this->db->join('employments', $this->table.'.employmentID=employments.employmentID', 'left');
        $this->db->join('employees', 'employments.empID=employees.empID', 'left');
        $this->db->join('branches', 'employments.branchID=branches.branchID', 'left');
        $this->db->join('departments', 'employments.deptID=departments.deptID', 'left');
        $this->db->join('divisions', 'employments.divisionID=divisions.divisionID', 'left');
        
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
        $this->db->select('incentive_types.name');
        $this->db->select('incentive_types.abbr');
        $this->db->select('employments.lname');
        $this->db->select('employments.mname');
        $this->db->select('employees.empNo');
        $this->db->select('employees.fname');
        $this->db->select('branches.branchCode');
        $this->db->select('branches.branchName');
        $this->db->select('departments.deptCode');
        $this->db->select('departments.deptName');
        $this->db->select('divisions.divisionCode');
        $this->db->select('divisions.divisionName');

        // from
        $this->db->from($this->table);
        
        // join
        $this->db->join('incentives', $this->table.'.payID=incentives.payID', 'left');
        $this->db->join('incentive_types', 'incentives.incentiveTypeID=incentive_types.incentiveTypeID', 'left');
        $this->db->join('employments', $this->table.'.employmentID=employments.employmentID', 'left');
        $this->db->join('employees', 'employments.empID=employees.empID', 'left');
        $this->db->join('branches', 'employments.branchID=branches.branchID', 'left');
        $this->db->join('departments', 'employments.deptID=departments.deptID', 'left');
        $this->db->join('divisions', 'employments.divisionID=divisions.divisionID', 'left');
        
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
            array('variable'=>'branchID', 'field'=>'employments.branchID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'deptID', 'field'=>'employments.deptID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'divisionID', 'field'=>'employments.divisionID', 'default_value'=>'', 'operator'=>'where'),
			array('variable'=>'empNo', 'field'=>'employees.empNo', 'default_value'=>'', 'operator'=>'like_both'),
			array('variable'=>'lname', 'field'=>'employees.lname', 'default_value'=>'', 'operator'=>'like_both'),
			array('variable'=>'incentiveTypeID', 'field'=>'incentives.incentiveTypeID', 'default_value'=>'', 'operator'=>'where'),
			array('variable'=>'amount', 'field'=>$this->table.'.amount', 'default_value'=>'', 'operator'=>'where'),
			array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
		);
		
		// sorting fields
		$sorting_fields = array('payID'=>'desc');
        
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
        $this->db->select('incentive_types.name');
        $this->db->select('incentive_types.abbr');
        $this->db->select('employments.lname');
        $this->db->select('employments.mname');
        $this->db->select('employees.empNo');
        $this->db->select('employees.fname');
        $this->db->select('branches.branchCode');
        $this->db->select('branches.branchName');
        $this->db->select('departments.deptCode');
        $this->db->select('departments.deptName');
        $this->db->select('divisions.divisionCode');
        $this->db->select('divisions.divisionName');

        // from
        $this->db->from($this->table);
        
        // join
        $this->db->join('incentives', $this->table.'.payID=incentives.payID', 'left');
        $this->db->join('incentive_types', 'incentives.incentiveTypeID=incentive_types.incentiveTypeID', 'left');
        $this->db->join('employments', $this->table.'.employmentID=employments.employmentID', 'left');
        $this->db->join('employees', 'employments.empID=employees.empID', 'left');
        $this->db->join('branches', 'employments.branchID=branches.branchID', 'left');
        $this->db->join('departments', 'employments.deptID=departments.deptID', 'left');
        $this->db->join('divisions', 'employments.divisionID=divisions.divisionID', 'left');
        
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
            array('variable'=>'branchID', 'field'=>'employments.branchID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'deptID', 'field'=>'employments.deptID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'divisionID', 'field'=>'employments.divisionID', 'default_value'=>'', 'operator'=>'where'),
			array('variable'=>'empNo', 'field'=>'employees.empNo', 'default_value'=>'', 'operator'=>'like_both'),
			array('variable'=>'lname', 'field'=>'employees.lname', 'default_value'=>'', 'operator'=>'like_both'),
			array('variable'=>'incentiveTypeID', 'field'=>'incentives.incentiveTypeID', 'default_value'=>'', 'operator'=>'where'),
			array('variable'=>'amount', 'field'=>$this->table.'.amount', 'default_value'=>'', 'operator'=>'where'),
			array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
		);
		
		// sorting fields
		$sorting_fields = array('payID'=>'desc');
        
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
        $this->db->select('incentive_types.name');
        $this->db->select('incentive_types.abbr');
        $this->db->select('employments.lname');
        $this->db->select('employments.mname');
        $this->db->select('employees.empNo');
        $this->db->select('employees.fname');
        $this->db->select('branches.branchCode');
        $this->db->select('branches.branchName');
        $this->db->select('departments.deptCode');
        $this->db->select('departments.deptName');
        $this->db->select('divisions.divisionCode');
        $this->db->select('divisions.divisionName');

        // from
        $this->db->from($this->table);
        
        // join
        $this->db->join('incentives', $this->table.'.payID=incentives.payID', 'left');
        $this->db->join('incentive_types', 'incentives.incentiveTypeID=incentive_types.incentiveTypeID', 'left');
        $this->db->join('employments', $this->table.'.employmentID=employments.employmentID', 'left');
        $this->db->join('employees', 'employments.empID=employees.empID', 'left');
        $this->db->join('branches', 'employments.branchID=branches.branchID', 'left');
        $this->db->join('departments', 'employments.deptID=departments.deptID', 'left');
        $this->db->join('divisions', 'employments.divisionID=divisions.divisionID', 'left');
        
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
    		<Column ss:Index='2' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='3' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='4' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='5' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='6' ss:AutoFitWidth=\"1\" ss:Width='200.00'/>
    		<Column ss:Index='7' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
    		<Column ss:Index='8' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='9' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='10' ss:AutoFitWidth=\"1\" ss:Width='150.00'/>
    		<Column ss:Index='11' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
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
        $fields[]="BRANCH";
        $fields[]="DEPARTMENT";
        $fields[]="SECTION";
        $fields[]="ID NUMBER";
        $fields[]="EMPLOYEE NAME";
        $fields[]="INCENTIVE";
        $fields[]="AMOUNT";
        $fields[]="EFFECTIVITY";
        $fields[]="REMARKS";
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
                $data .= "<Row>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$ctr.".</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->branchCode."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->deptCode."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->divisionCode."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->empNo."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->lname.", ".$row->lname." ".$row->mname."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->abbr."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='Number'>".$row->amount."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".date('m/d/Y', strtotime($row->effectivity))."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->remarks."</Data></Cell>";
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
        $filename = "Incentive List";
    
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$filename.xls;");
        header("Content-Type: application/ms-excel");
        header("Pragma: no-cache");
        header("Expires: 0");
         
        echo $data;
    
    }
    
    //Conditions and fields changes
    public function check_duplicate()
    {
        $this->db->where('employmentID', trim($this->input->post('employmentID')));
      
        if ($this->db->count_all_results($this->table))
            echo "1"; // duplicate
        else 
            echo "0";
    }

    private function _in_used($id=0)
    {
        $tables = array('payslip_incentives'=>'payID');
    
        if(!empty($tables)) {
            foreach($tables as $table=>$fld) {
                $this->db->where($fld, $id);
                if($this->db->count_all_results($table)) {
                    return true;
                }
            }
        }
        return false;
    }
}
