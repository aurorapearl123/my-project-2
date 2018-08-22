<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
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
        $this->module       = 'Dashboard';
        $this->data['controller_page']  = $this->controller_page = site_url('dashboard');// defines contoller link
        $this->table        = 'companies';                                                  // defines the default table
        $this->pfield       = $this->data['pfield'] = 'companyID';                                                 // defines primary key
        $this->logfield     = 'companyCode';
        $this->module_path  = 'modules/'.strtolower(str_replace(" ","_",$this->module)).'/dashboard';             // defines module path
        
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

        $this->data['modules']               = $this->modules;
        $this->data['current_main_module']   = $this->modules[$this->module]['main'];              // defines the current main module
        $this->data['current_module']        = $this->modules[$this->module]['sub']['Dashboard'];      // defines the current sub module
        // check roles
        $this->check_roles();
        $this->data['roles']                 = $this->roles;
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
        $this->dashboard();
    }
    
    public function dashboard()
    {
        $this->submenu();
        $data = $this->data;
        
        
        $this->db->select('branches.branchCode');
        $this->db->select('departments.*');
        $this->db->from('departments');
        $this->db->join('branches', 'departments.branchID=branches.branchID', 'left');
        $this->db->where('departments.status >',0);
        $data['departments'] = $this->db->get()->result();
        
//        $this->db->where('status >',0);
//        $data['jobs'] = $this->db->get('job_titles')->result();
//
//        $deptTardyArray = $this->_department_tardiness();
//        $data['deptTardyArray'] = $deptTardyArray;
//
//        $mainChart = $this->_get_main_container_chart();
//        $data['attendance_percentage_array'] = $mainChart['attendance_percentage_array'];
//        $data['leaves_percentage_array'] = $mainChart['leaves_percentage_array'];
//        $data['loans_percentage_array'] = $mainChart['loans_percentage_array'];
//
//        $birthdays = $this->_get_birthdays();
//        $data['birthdays'] = $birthdays;
//
//        $employment_stats = $this->_get_employment_stats();
//        $data['promotions'] = $employment_stats['promotions'];
//        $data['demotions'] = $employment_stats['demotions'];
//        $data['transferred'] = $employment_stats['transferred'];
//        $data['terminated'] = $employment_stats['terminated'];
//        $data['active'] = $employment_stats['active'];
//        $data['inactive'] = $employment_stats['inactive'];
//
//        $data['count_leaves_this_month'] = $this->_count_leaves_this_month();
        

       // load views
        $this->load->view('header', $data);
        
        $this->load->view($this->module_path.'/dashboard');
        // $this->load->view($this->module_path.'/departments_view');
        $this->load->view('footer');
    }

    public function print_dashboard()
    {
        $this->submenu();
        $data = $this->data;
        
        $this->db->where('status >',0);
        $data['departments'] = $this->db->get('departments')->result();
        
        $this->db->where('status >',0);
        $data['jobs'] = $this->db->get('job_titles')->result();

        $data['title'] = "LHPrime Dashboard";

        $deptTardyArray = $this->_department_tardiness();
        $data['deptTardyArray'] = $deptTardyArray;

        //load views
        $this->load->view('header_print', $data);
        $this->load->view($this->module_path.'/printdashboard');
        $this->load->view('footer_print');
    }

}

