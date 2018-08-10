<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_files extends CI_Controller
{
    //Default Variables
    // var $menu;
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
        $this->module       = 'Masters';
        $this->data['current_module']   = $this->modules['Masters'];              // defines the current module
        $this->data['controller_page']  = $this->controller_page = site_url('master_files');// defines contoller link
        $this->table        = 'users';                                                  // defines the default table
        $this->pfield       = 'userID';                                                 // defines primary key
        $this->logfield     = 'username';                                               // defines field for record log
        $this->module_path 	= 'modules/master_files';             // defines module path
        
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
       require_once('modules.php');
        
       foreach($modules as $mod) {
           //modules/<module>/
           // - <menu>
           // - <metadata>
           $this->load->view('modules/'.strtolower($mod).'/metadata');
       }
       
       $this->data['modules']   = $this->modules;
    }
    
    private function check_roles()
    {
        $this->roles['create']  = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Add '.$this->module);
        $this->roles['view']    = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View '.$this->module);
        $this->roles['edit']    = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Edit Existing '.$this->module);
        $this->roles['delete']  = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Delete Existing '.$this->module);
        $this->roles['approve'] = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Approve '.$this->module);
    }
    
    public function index()
    {
        $this->create();
    }
    
    public function create()
    {
    }
    
}
