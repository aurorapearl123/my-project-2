<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_pay extends CI_Controller
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
        // set variables
        $this->data['current_module'] = $this->module = 'Incentive & Deduction';
        $this->module_label = 'Pay Settings';
        $this->table        = 'employments';
        $this->module_path  = 'modules/Pay';
        $this->module_path_submenu  = 'modules/Pay/submenu';
        $this->pfield = 'employmentID';
        $this->logfield = 'employmentNo';
        $this->data['controller_page'] = $this->controller_page = site_url('menu_pay');
        // check if under maintenance
        if ($this->config_model->getConfig('Maintenance Mode')=='1') {
            header('location: '.site_url('maintenance_mode'));
        }
        // check if loggedin
    }

    private function submenu()
    {
        //submenu setup
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
        if ($this->userrole_model->has_access($this->session->userdata('current_userID'),'View Incentive & Deduction')) {
            $this->overview();
        } elseif ($this->userrole_model->has_access($this->session->userdata('current_userID'),'View Contribution')) {
            header('location: '.site_url('contribution'));
        } elseif ($this->userrole_model->has_access($this->session->userdata('current_userID'),'View Loan')) {
            header('location: '.site_url('loan'));
        } elseif ($this->userrole_model->has_access($this->session->userdata('current_userID'),'View Incentive')) {
            header('location: '.site_url('incentive'));
        } elseif ($this->userrole_model->has_access($this->session->userdata('current_userID'),'View Withholding Tax')) {
            header('location: '.site_url('tax_withholding'));
        } elseif ($this->userrole_model->has_access($this->session->userdata('current_userID'),'View Account Classification')) {
                header('location: '.site_url('pay_classification'));
        } elseif ($this->userrole_model->has_access($this->session->userdata('current_userID'),'View Premium')) {
            header('location: '.site_url('premium'));
        } elseif ($this->userrole_model->has_access($this->session->userdata('current_userID'),'View Loan Type')) {
            header('location: '.site_url('loan_type'));
        } elseif ($this->userrole_model->has_access($this->session->userdata('current_userID'),'View Incentive Type')) {
            header('location: '.site_url('incentive_type'));
        } elseif ($this->userrole_model->has_access($this->session->userdata('current_userID'),'View Tax Exemption')) {
            header('location: '.site_url('tax_exemption'));
        } elseif ($this->userrole_model->has_access($this->session->userdata('current_userID'),'View Contribution Table')) {
            header('location: '.site_url('contribution_table'));
        } else {
            header('location: '.site_url('user/profile'));
        }
    }
    
    //More Pages
    public function overview($id=0)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;

        // check roles
        if ($this->roles['view']) {
            $data['required_fields'] = array('empID'=>'Employee', 'loanTypeID'=>'Loan Type','principal'=>'Principal','terms'=>'Terms',
                'startDate'=>'Start Date','endDate'=>'End Date','dateFiled'=>'Date Filed');

            if ($id) {
                $data['empID']  = $this->encrypter->decode($id);
            } else {
                $data['empID']  = $this->session->userdata('current_empID');
            }

            if ($data['empID']) { 
                $this->db->where('empID', $data['empID']);
                $query = $this->db->get('employees', 1)->row();

                $data['empNo']          = $query->empNo;
                $data['employee_name']  = $query->lname.', '.$query->fname.' '.$query->mname.' '.$query->suffix;
            }

            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path.'/overview');
            $this->load->view('footer');
        }
    }

    public function setPayOverviewEncrypt($empID=0, $showInactive=0)
    {
        $data['empID']          = $this->encrypter->decode($empID);
        $data['showInactive']   = $showInactive;
                
        // load views
        echo $this->load->view($this->module_path.'/pay_setting', $data, true);
    }

    public function pay_record($id=0, $showInactive=0)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);

        // check roles
        if ($this->roles['view']) {
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->record->table = $this->table;
            // set fields for the current table
            $this->record->setFields();
            // extend fields - join tables
            $this->record->fields[] = 'employees.empNo';
            $this->record->fields[] = 'employees.fname';
            $this->record->fields[] = 'employees.suffix';
            $this->record->fields[] = 'employees.imageExtension';
            $this->record->fields[] = 'employees.taxID';
            $this->record->fields[] = 'companies.companyName';
            $this->record->fields[] = 'offices.officeName';
            $this->record->fields[] = 'divisions.divisionName';
            $this->record->fields[] = 'detailedCompany.companyName as detailedCompanyName';
            $this->record->fields[] = 'detailedOffice.officeName as detailedOfficeName';
            $this->record->fields[] = 'detailedDivision.divisionName as detailedDivisionName';
            $this->record->fields[] = 'employee_types.employeeType';
            $this->record->fields[] = 'job_positions.positionCode';
            $this->record->fields[] = 'job_titles.jobTitle';
            $this->record->fields[] = 'payroll_groups.payrollGroup';
            $this->record->fields[] = 'shifts.shiftName';
            $this->record->fields[] = 'agencies.agencyName';
            $this->record->fields[] = 'tax_exemptions.exemption';
                
            // set joins
            $this->record->joins[]  = array('employees',$this->table.'.empID=employees.empID','left');
            $this->record->joins[]  = array('companies',$this->table.'.companyID=companies.companyID','left');
            $this->record->joins[]  = array('offices',$this->table.'.officeID=offices.officeID','left');
            $this->record->joins[]  = array('divisions',$this->table.'.divisionID=divisions.divisionID','left');
            $this->record->joins[]  = array('companies detailedCompany',$this->table.'.detailedCompanyID=detailedCompany.companyID','left');
            $this->record->joins[]  = array('offices detailedOffice',$this->table.'.detailedOfficeID=detailedOffice.officeID','left');
            $this->record->joins[]  = array('divisions detailedDivision',$this->table.'.detailedDivisionID=detailedDivision.divisionID','left');
            $this->record->joins[]  = array('employee_types',$this->table.'.employeeTypeID=employee_types.employeeTypeID','left');
            $this->record->joins[]  = array('job_positions',$this->table.'.jobPositionID=job_positions.jobPositionID','left');
            $this->record->joins[]  = array('job_titles','job_positions.jobTitleID=job_titles.jobTitleID','left');
            $this->record->joins[]  = array('payroll_groups',$this->table.'.payrollGroupID=payroll_groups.payrollGroupID','left');
            $this->record->joins[]  = array('shifts',$this->table.'.shiftID=shifts.shiftID','left');
            $this->record->joins[]  = array('agencies',$this->table.'.agencyID=agencies.agencyID','left');
            $this->record->joins[]  = array('tax_exemptions','employees.taxID=tax_exemptions.taxID','left');
            // set where
            $this->record->where[$this->table.'.'.$this->pfield] = $id;
                
            // execute retrieve
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;    

            $data['showInactive'] = $showInactive;
            $this->session->set_userdata('current_empID', $this->record->field->empID);
                
            // record logs
            if ($this->config_model->getConfig('Log all record views') == '1') {
                $logfield = $this->logfield;
                $logs = "Record - ".$this->record->field->$logfield;
                $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->field->$data['pfield'], 'View', $logs);
            }
                
            // check if record is used in other tables
            $data['inUsed'] = false;

            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path.'/pay_record');
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

    public function update_auto_calc()
    {
        $id             = $this->input->post('id');
        $module         = $this->input->post('module');
        $taxID          = $this->input->post('taxID');
        $premiumID      = $this->input->post('premiumID');
        $basicSalary    = $this->input->post('basicSalary');
        $employmentID   = $this->input->post('employmentID');
        $data           = array();
        
        if ($module == 'contribution') {
            $this->db->where('premiumID', $premiumID);
            $this->db->where('startSalary <=', $basicSalary);
            $this->db->where('endSalary >=', $basicSalary);
            $share = $this->db->get('schedule_contribution', 1)->row();
            
            $data['employeeShare'] = ($share->shareType == 1) ? number_format($share->employeeShare, 2) : number_format($basicSalary * ($share->employeeShare / 100), 2);
            $data['employerShare'] = ($share->shareType == 1) ? number_format($share->employerShare, 2) : number_format($basicSalary * ($share->employerShare / 100), 2);
        } else {
            $data['amount'] = 0;
            $result = array();
            if ($taxID) {               
                $result = $this->payrollcalc->wtax($employmentID, $taxID, $basicSalary);            
                $data['amount'] = $result['amount'];
            }
        }
    
        echo json_encode($data);
    }








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
