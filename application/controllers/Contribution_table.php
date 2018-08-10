<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contribution_table extends CI_Controller
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
        $this->table        = 'schedule_contribution';                                                 
        $this->pfield       = $this->data['pfield'] = 'bracketID';                                                 
        $this->logfield     = 'premiumID';
        $this->module_path  = 'modules/salary_and_wages/contribution_table';           
        $this->data['controller_page']  = $this->controller_page = site_url('contribution_table');
        
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
        $this->data['current_module']   = $this->modules[$this->module]['sub']['Contribution Table'];      // defines the current sub module
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
            $data['gradeLimit'] = $this->config_model->getConfig('Salary Grade Limit');
            $data['stepLimit']  = $this->config_model->getConfig('Salary Step Limit');
            
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

        // check role
        if ($this->roles['create']) {
            if ($this->input->post('chkAdd')) {
                $batch = array();
                foreach($this->input->post('chkAdd') as $bracketID) {
                    if ($this->input->post('startSalary_'.$bracketID)!="") {
                        $info = array();
                        $info['premiumID'] 	 		= trim($this->input->post('premiumID'));
                        $info['startSalary'] 		= $this->input->post('startSalary_'.$bracketID);
                        $info['endSalary'] 			= $this->input->post('endSalary_'.$bracketID);
                        $info['baseSalary'] 		= $this->input->post('baseSalary_'.$bracketID);
                        $info['shareType'] 	    	= $this->input->post('shareType_'.$bracketID);
                        $info['employeeShare'] 		= $this->input->post('employeeShare_'.$bracketID);
                        $info['employerShare'] 		= $this->input->post('employerShare_'.$bracketID);
                        $info['totalContribution'] 	= $this->input->post('totalContribution_'.$bracketID);
                        $info['dateUpdated'] 	    = date('Y-m-d H:i:s');
                        $info['lastUpdateBy'] 	    = $this->session->userdata('current_userID');
                        $batch[] = $info;
                    }
                }
            
                $this->db->insert_batch('schedule_contribution', $batch);
            
                // record logs
                $logs = "Record - ".trim($this->input->post($this->logfield));
                $this->log_model->table_logs($this->module, $this->table, $this->pfield, trim($this->input->post('premiumID')), 'Insert', $logs);
            
                $logfield = $this->pfield;
                // successful
                $data["class"]   = "success";
                $data["msg"]     = $this->data['current_module']['module_label']." successfully saved.";
                $data["urlredirect"] = $this->controller_page."/view/".$this->encrypter->encode(trim($this->input->post('premiumID')));
                $this->load->view("header",$data);
                $this->load->view("message");
                $this->load->view("footer");
            } else {
                // error
                $data["class"]   = "danger";
                $data["msg"]     = "No data received!";
                $data["urlredirect"] = "";
                $this->load->view("header",$data);
                $this->load->view("message");
                $this->load->view("footer");
            }
            
			$logfield = $this->pfield;
			// success msg
            $data["class"]   = "success";
            $data["msg"]     = $this->data['current_module']['module_label']." successfully saved.";
            $data["urlredirect"] = $this->controller_page."/show";
            $this->load->view("header",$data);
            $this->load->view("message");
            $this->load->view("footer");
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
            // get premium
			$this->db->where('premiumID', $id);
			$data['rec'] = $this->db->get('premiums', 1)->row();
			
			// get schedules
			$this->db->where('premiumID', $id);
			$this->db->order_by('startSalary','asc');
			$this->db->order_by('endSalary','asc');
			$data['schedules'] = $this->db->get($this->table);
			
			$data['gradeLimit'] = $this->config_model->getConfig('Salary Grade Limit');
			$data['stepLimit']  = $this->config_model->getConfig('Salary Step Limit');

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
        $response = new stdClass();
        $response->status       = 0;
        $response->message      = "";
        
        // load submenu
        $this->submenu();
        $data = $this->data;

        // check roles
        if ($this->roles['edit']) {
            $premiumID      = trim($this->input->post('premiumID'));
            $bracketID      = trim($this->input->post('bracketID'));
            $startSalary    = trim($this->input->post('startSalary'));
            $endSalary      = trim($this->input->post('endSalary'));
            $baseSalary     = trim($this->input->post('baseSalary'));
            $shareType      = trim($this->input->post('shareType'));
            $employeeShare  = trim($this->input->post('employeeShare'));
            $employerShare  = trim($this->input->post('employerShare'));
            $totalContribution = trim($this->input->post('totalContribution'));
            
            $this->db->set('startSalary', $startSalary);
            $this->db->set('endSalary', $endSalary);
            $this->db->set('baseSalary', $baseSalary);
            $this->db->set('shareType', $shareType);
            $this->db->set('employeeShare', $employeeShare);
            $this->db->set('employerShare', $employerShare);
            $this->db->set('totalContribution', $totalContribution);
            $this->db->where('bracketID', $bracketID);
            $this->db->update('schedule_contribution');
            
            $logs = "Record - Start ".$startSalary." End ".$endSalary;
            $this->log_model->table_logs($this->data['current_module']['module_label'], $this->table, 'bracketID', $bracketID, 'Update', $logs);
            
        } 
        
        echo json_encode($response);
    }

    public function delete($id=0)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);

        // check roles
        if ($this->roles['delete']) {
            $this->db->where('premiumID', $id);
            $this->db->delete($this->table);
            
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
            // get premium
			$this->db->where('premiumID', $id);
			$data['rec'] = $this->db->get('premiums', 1)->row();
			
			// get schedules
			$this->db->select($this->table.'.*');
			$this->db->select('users.userName');
			$this->db->from($this->table);
			$this->db->join('users',$this->table.'.lastUpdateBy=users.userID','left');
			$this->db->where($this->table.'.premiumID', $id);
			$this->db->order_by($this->table.'.startSalary','asc');
			$this->db->order_by($this->table.'.endSalary','asc');
			$data['schedules'] = $this->db->get();
            
			$data['gradeLimit'] = $this->config_model->getConfig('Salary Grade Limit');
			$data['stepLimit']  = $this->config_model->getConfig('Salary Step Limit');
			
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
			array('variable'=>'premiumID', 'field'=>$this->table.'.premiumID', 'default_value'=>'', 'operator'=>'where'),			
		);
		
		// sorting fields
		$sorting_fields = array('premiumID'=>'asc');
        
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
    	$this->db->select('premiums.name');	

    	// from
    	$this->db->from($this->table);
    	
    	// join   	
    	$this->db->join('premiums', $this->table.'.premiumID=premiums.premiumID', 'left');
        
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
        
        $this->db->group_by($this->table.'.premiumID');
        
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
    	$this->db->select('premiums.name');	

    	// from
    	$this->db->from($this->table);
    	
    	// join   	
    	$this->db->join('premiums', $this->table.'.premiumID=premiums.premiumID', 'left');
        
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
        
        $this->db->group_by($this->table.'.premiumID');
        
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
            array('variable'=>'code', 'field'=>$this->table.'.code', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'name', 'field'=>$this->table.'.name', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'isTaxable', 'field'=>$this->table.'.isTaxable', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'isBasic', 'field'=>$this->table.'.isBasic', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('name'=>'asc');
        
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
            array('variable'=>'code', 'field'=>$this->table.'.code', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'name', 'field'=>$this->table.'.name', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'isTaxable', 'field'=>$this->table.'.isTaxable', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'isBasic', 'field'=>$this->table.'.isBasic', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('name'=>'asc');
        
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
    		<Column ss:Index='3' ss:AutoFitWidth=\"1\" ss:Width='200.00'/>
    		<Column ss:Index='4' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
    		<Column ss:Index='5' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='6' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		    ";
    
        // header
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='6'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
    
        $data .= "<Row ss:StyleID='s20'>";
        $data .= "<Cell ss:MergeAcross='6'><Data ss:Type='String'>".$companyName."</Data></Cell>";
        $data .= "</Row>";
        $data .= "<Row ss:StyleID='s24A'>";
        $data .= "<Cell ss:MergeAcross='6'><Data ss:Type='String'>".$address."</Data></Cell>";
        $data .= "</Row>";
    
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='6'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
         
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='6'><Data ss:Type='String'>".strtoupper($title)."</Data></Cell>";
        $data .= "</Row>";
         
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='6'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
    
        $fields[]="#";
        $fields[]="ACCOUNT CODE";
        $fields[]="PREMIUM NAME";
        $fields[]="ABBREVIATION";
        $fields[]="TAXABLE";
        $fields[]="BASIC";
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
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->code."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->name."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->abbr."</Data></Cell>";
                if  ($row->isTaxable == 1) {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'>Taxable</Data></Cell>";
                } else {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'></Data></Cell>";
                }
                if  ($row->isBasic == 1) {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'>Basic</Data></Cell>";
                } else {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'></Data></Cell>";
                }
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
        $filename = "Premium List";
    
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
        $this->db->where('code', trim($this->input->post('code')));
        $this->db->where('name', trim($this->input->post('name')));
      
        if ($this->db->count_all_results($this->table))
            echo "1"; // duplicate
        else 
            echo "0";
    }

    private function _in_used($id=0)
    {
        $tables = array('contributions'=>'premiumID');
    
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
