<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fw extends CI_Controller
{
   var $menu;
   var $module;
   var $modules;
   var $data;
    
   
   public function __construct()
   {
       parent::__construct();
       if ($this->config_model->getConfig('Maintenance Mode')=='1') {
           header('location: '.site_url('maintenance_mode'));
       }
   }
   
   public function index()
   {
       // current module
       $this->module = 'Employee';
       
       $this->submenu();
       $data = $this->data;
       $data['current_module'] = $this->modules[$this->module];
       // $data['rec'] = $this->session->userdata('current_user');
       
       $this->load->view('header',$data);
//        $this->load->view('page');
       $this->load->view('footer');
   }
   
   public function submenu()
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
   
   public function page2()
   {
       $this->load->view('header');
       $this->load->view('footer');
   }
}
