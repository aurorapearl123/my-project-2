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
        $this->table        = 'order_headers';                                                  // defines the default table
        $this->pfield       = $this->data['pfield'] = 'orderID';                                                 // defines primary key
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
                           
        $this->db->select('branchID');
        $this->db->select('branchName');
        $this->db->from('branches');
        if(!($this->session->userdata('current_user')->isAdmin)){
            $this->db->where('branchID',$this->session->userdata('current_user')->branchID);
            $data['branch'] = $this->db ->get()->row();

        } else {
            $data['branches'] = $this->db ->get()->result();    
        }
        


        //order headers total amount

        /*$this->db->select('configID');
        $this->db->select('name');
        $this->db->select('value');
        $this->db->from('config');
        $this->db->where('name', 'Start Year' );
        $data['config'] = $this->db->get()->row();*/

        //echo json_encode($data['config']);
        //die();

        $years = range(date("Y"), 1910);

        $data['years'] = $years;

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

    public function getChartOrderSales()
    {

        $branchID = $this->input->post('branchID');
        $year = $this->input->post('year');

        $total_arr = [
            $this->getSalesOrder($this->formatDateFirstDay($year, '01'), $this->formatDateLastDay($year, '01'), $branchID),
            $this->getSalesOrder($this->formatDateFirstDay($year, '02'), $this->formatDateLastDay($year, '02'), $branchID),
            $this->getSalesOrder($this->formatDateFirstDay($year, '03'), $this->formatDateLastDay($year, '03'), $branchID),
            $this->getSalesOrder($this->formatDateFirstDay($year, '04'), $this->formatDateLastDay($year, '04'), $branchID),
            $this->getSalesOrder($this->formatDateFirstDay($year, '05'), $this->formatDateLastDay($year, '05'), $branchID),
            $this->getSalesOrder($this->formatDateFirstDay($year, '06'), $this->formatDateLastDay($year, '06'), $branchID),
            $this->getSalesOrder($this->formatDateFirstDay($year, '07'), $this->formatDateLastDay($year, '07'), $branchID),
            $this->getSalesOrder($this->formatDateFirstDay($year, '08'), $this->formatDateLastDay($year, '08'), $branchID),
            $this->getSalesOrder($this->formatDateFirstDay($year, '09'), $this->formatDateLastDay($year, '09'), $branchID),
            $this->getSalesOrder($this->formatDateFirstDay($year, '10'), $this->formatDateLastDay($year, '10'), $branchID),
            $this->getSalesOrder($this->formatDateFirstDay($year, '11'), $this->formatDateLastDay($year, '11'), $branchID),
            $this->getSalesOrder($this->formatDateFirstDay($year, '12'), $this->formatDateLastDay($year, '12'), $branchID),

        ];
        echo json_encode($total_arr);

    }

    public function getChartExpense()
    {
        $branchID = $this->input->post('branchID');
        $year = $this->input->post('year');

        $total_arr = [
            $this->getSales($this->formatDateFirstDay($year, '01'), $this->formatDateLastDay($year, '01'), $branchID),
            $this->getSales($this->formatDateFirstDay($year, '02'), $this->formatDateLastDay($year, '02'), $branchID),
            $this->getSales($this->formatDateFirstDay($year, '03'), $this->formatDateLastDay($year, '03'), $branchID),
            $this->getSales($this->formatDateFirstDay($year, '04'), $this->formatDateLastDay($year, '04'), $branchID),
            $this->getSales($this->formatDateFirstDay($year, '05'), $this->formatDateLastDay($year, '05'), $branchID),
            $this->getSales($this->formatDateFirstDay($year, '06'), $this->formatDateLastDay($year, '06'), $branchID),
            $this->getSales($this->formatDateFirstDay($year, '07'), $this->formatDateLastDay($year, '07'), $branchID),
            $this->getSales($this->formatDateFirstDay($year, '08'), $this->formatDateLastDay($year, '08'), $branchID),
            $this->getSales($this->formatDateFirstDay($year, '09'), $this->formatDateLastDay($year, '09'), $branchID),
            $this->getSales($this->formatDateFirstDay($year, '10'), $this->formatDateLastDay($year, '10'), $branchID),
            $this->getSales($this->formatDateFirstDay($year, '11'), $this->formatDateLastDay($year, '11'), $branchID),
            $this->getSales($this->formatDateFirstDay($year, '12'), $this->formatDateLastDay($year, '12'), $branchID),

        ];
        echo json_encode($total_arr);
    }

    public function formatDateFirstDay($year, $month)
    {
        $d = new DateTime($year.'-'.$month.'-19');
        $d->modify('first day of this month');
        $firstDay = $d->format('Y-m-d');

        return $firstDay;
    }

    public function formatDateLastDay($year, $month)
    {

        $d = new DateTime($year.'-'.$month.'-19');
        $d->modify('last day of this month');
        $lastDay = $d->format('Y-m-d');

        return $lastDay;
    }

    public function getSalesOrder($from, $to, $branchID){
        //$this->db->where('DATE(order_headers.dateReady) >= ', Date('Y-m-d'));
        $this->db->select("SUM(order_headers.ttlAmount) AS totalAmount");
        $this->db->from("order_headers");
        //$this->db->join('service_types', 'order_headers.serviceID=service_types.serviceID', 'left');
        //$this->db->where('DATE(order_headers.date) >= ', $date);
        $this->db->where('DATE(order_headers.date) >= ', $from);
        $this->db->where('DATE(order_headers.date) <= ', $to);
        $this->db->where('branchID', $branchID);

        $query = $this->db->get();
        if($query->num_rows() > 0) {
            $res = $query->row_array();
            return (int)$res['totalAmount'];
        }
        else {
            return 0;
        }
    }

    public function getSales($from, $to, $branchID){
        //$this->db->where('DATE(order_headers.dateReady) >= ', Date('Y-m-d'));
        $this->db->select("SUM(exp_details.amount) AS totalAmount");
        $this->db->from("exp_headers");
        $this->db->join("exp_details", "exp_headers.expID =  exp_details.expID", "left");
        //$this->db->join('service_types', 'order_headers.serviceID=service_types.serviceID', 'left');
        //$this->db->where('DATE(exp_headers.date) >= ', $date);
        $this->db->where('DATE(exp_headers.date) >= ', $from);
        $this->db->where('DATE(exp_headers.date) <= ', $to);
        $this->db->where('exp_headers.branchID', $branchID);

        $query = $this->db->get();
        if($query->num_rows() > 0) {
            $res = $query->row_array();
            return (int)$res['totalAmount'];
        }
        else {
            return 0;
        }
    }

}

