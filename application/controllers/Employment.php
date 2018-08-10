<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employment extends CI_Controller
{
    //Default Variables
    var $common_menu;
    var $roles;
    var $data;
    var $table;
    var $pfield;
    var $logfield;
    var $module;
    var $modules;
    var $module_label;
    var $module_path;
    var $controller_page;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('generic_model','record');
        $this->module       = 'Employee'; //Parent Module 
        $this->data['controller_page']  = $this->controller_page = site_url('employment');// defines contoller link
        $this->table        = 'employments';                                                  // defines the default table
        $this->pfield = $this->data['pfield'] = 'employmentID';                                                 // defines primary key
        $this->logfield     = 'employmentNo';                                               // defines field for record log
        $this->module_path  = 'modules/'.strtolower(str_replace(" ","_",$this->module)).'/employment';             // defines module path
        
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
        
        $this->data['modules']              = $this->modules;
        $this->data['current_main_module']  = $this->modules[$this->module]['main'];              // defines the current main module
        $this->data['current_module']       = $this->modules[$this->module]['sub']['Employment'];
        
        // load all specific assets aside from main file assets that are needed only to this specific controller
        // sample: array('custom.css','orig_style.css')
        // sample: array('custom.js','highchart.js')
        $this->data['css']  = array();          // path under assets/css
        $this->data['js']   = array();          // path under assets/js
        $this->data['plugin_css']  = array();   // path under assets/plugin
        $this->data['plugin_js']   = array();   // path under assets/plugin
        
        // check roles
        $this->check_roles();
        $this->data['roles']   = $this->roles;
    }
    
    private function check_roles()
    {
        $this->roles['create']  = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Add '.$this->data['current_module']['module_label']);
        $this->roles['view']    = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View '.$this->data['current_module']['module_label']);
        $this->roles['promote'] = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Promote '.$this->data['current_module']['module_label']);
        $this->roles['demote']  = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Demote '.$this->data['current_module']['module_label']);
        $this->roles['re-assign']= $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Re-Assign '.$this->data['current_module']['module_label']);
        $this->roles['terminate']= $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Terminate '.$this->data['current_module']['module_label']);
        $this->roles['edit']    = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Edit Existing '.$this->data['current_module']['module_label']);
        $this->roles['delete']  = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Delete Existing '.$this->data['current_module']['module_label']);
        $this->roles['approve'] = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Approve '.$this->data['current_module']['module_label']);
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
            $data['urlredirect']    = $this->controller_page.'/show';
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
            $this->record->table  = $this->table;
            $this->record->fields = array();
            
            $table_fields = array('empID', 'branchID', 'divisionID', 'deptID','employeeTypeID','jobPositionID',
                            'salaryType','basicSalary','withBasicContribution', 'payrollGroupID');
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }                                   
            
            $this->db->where('empID', $this->record->fields['empID']);
            $count = $this->db->count_all_results('employments');
            
            
            $this->db->where('empID', $this->input->post('empID'));
            $employee = $this->db->get('employees')->row();

            $this->record->fields['lname']   = $employee->lname;
            $this->record->fields['mname']   = $employee->mname;
            $this->record->fields['employmentNo']   = $employmentNo = $employee->empNo.'-'.($count+1);
            $this->record->fields['dateAppointed']  = ($this->input->post('dateAppointed')!="") ? date('Y-m-d', strtotime(trim($this->input->post('dateAppointed')))) : "0000-00-00";

            if ($this->record->save()) {
                $this->record->fields = array();
                $this->record->where['employmentNo']= $employmentNo;                
                $this->record->retrieve();          
                
                // update job position
                $this->db->set('status', 2); // occupied
                $this->db->set('employeeTypeID', $this->record->field->employeeTypeID);
                $this->db->where('jobPositionID', $this->record->field->jobPositionID);
                $this->db->update('job_positions');
                
                // update last appointment date
                $this->db->set('lastAppointment', $this->record->field->dateAppointed);
                $this->db->where('empID', $this->record->field->empID);
                $this->db->update('employees');
                
                // set basic contributions
                if ($this->input->post('withBasicContribution')==1) {
                    $basics = explode(',', $this->input->post('basicContributions'));
                    if (!empty($basics)) {                                              
                        $this->db->where('isBasic', 1);
                        $this->db->where('status', 1);
                        $premiums = $this->db->get('premiums');
                
                        if ($premiums->num_rows()) {
                            foreach ($premiums->result() as $row) {
                                $this->db->where('empID', $this->record->field->empID);
                                $this->db->where('premiumID', $row->premiumID);
                                $contribution = $this->db->get('contributions', 1)->row();
                                
                                if (!empty($contribution)) {
                                    $this->db->set('payID', $contribution->payID);
                                    $this->db->set('employmentID', $this->record->field->employmentID);
                                    $this->db->set('effectivity', $this->record->field->dateAppointed);
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
                                    $this->db->set('employmentID', $this->record->field->employmentID);
                                    $this->db->set('effectivity', $this->record->field->dateAppointed);
                                    $this->db->set('isAutomatic', 1);
                                    $this->db->set('isWtaxExempted', 1);
                                    $this->db->insert('contribution_details');
                                }
                            }
                        }
                    }
                }
                
                // record logs
                $logs = "Record - ".$employmentNo;
                $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->field->$data['pfield'], 'Insert', $logs);
                
                $logfield = $this->pfield;                

                // success msg
                $data['class']  = "success";
                $data['msg']    = $this->data['current_module']['module_label']." successfully saved.";
                $data['urlredirect']    =  $this->controller_page.'/view/'.$this->encrypter->encode($this->record->field->$logfield);
                $this->load->view('header', $data);
                $this->load->view('message');
                $this->load->view('footer');
            } else {
                // Unable to save
                $data['class']  = "danger";
                $data['msg']    = "Error in saving the ".$this->data['current_module']['module_label']."!";
                $data['urlredirect']    = $this->controller_page.'/create';
                $this->load->view('header', $data);
                $this->load->view('message');
                $this->load->view('footer');
            }

        } else {
            // no access this page
            $data['class']  = "danger";
            $data['msg']    = "Sorry, you don't have access to this page!";
            $data['urlredirect']    = $this->controller_page.'/show';;
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
        $data['pfield'] = $this->pfield;
        if ($this->roles['edit']) {
            $data['required_fields'] = array('empID'=>'Employee', 'basicSalary'=>'Salary');
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->record->table = $this->table;
            // set fields for the current table
            $this->record->setFields();
            // extend fields - join tables
            $this->record->fields[] = 'employees.fname';
            $this->record->fields[] = 'employees.suffix';
            $this->record->fields[] = 'employee_types.basicContributions';          
            $this->record->fields[] = 'job_positions.positionCode';          
            $this->record->fields[] = 'job_titles.jobTitle';          
            // set joins            
            $this->record->joins[]  = array('employees',$this->table.'.empID=employees.empID','left');
            $this->record->joins[]  = array('employee_types',$this->table.'.employeeTypeID=employee_types.employeeTypeID','left');                  
            $this->record->joins[]  = array('job_positions',$this->table.'.jobPositionID=job_positions.jobPositionID','left');                  
            $this->record->joins[]  = array('job_titles','job_positions.jobTitleID=job_titles.jobTitleID','left');                  
            // set where
            $this->record->where[$this->table.'.'.$this->pfield] = $id;
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
        $table_fields = array('branchID','deptID','divisionID','employeeTypeID','jobPositionID','basicSalary','salaryType', 'payrollGroupID');        

        if ($this->roles['edit']) {
            $this->record->table  = $this->table;
            $this->record->fields = array();
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }                   
            
//             $shifts = $this->input->post('allowedShiftID');
//             if (!empty($shifts)) {
//                 $this->record->fields['allowedShiftID'] = implode(',', $this->input->post('allowedShiftID'));
//             }
            
            $this->record->fields['dateAppointed']  = ($this->input->post('dateAppointed')!="") ? date('Y-m-d', strtotime(trim($this->input->post('dateAppointed')))) : "0000-00-00";
            
            $this->record->pfield   = $this->pfield;
            $this->record->pk       = trim($this->input->post($this->pfield));
            
            // field logs here
            $wasChange = $this->log_model->field_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->pk, 'Update', $this->record->fields);

            if ($this->record->update()) {
                $this->record->fields = array();
                $this->record->where['employmentID']= $this->record->pk;
                $this->record->retrieve();
                
                // update last appointment date
                $this->db->set('lastAppointment', $this->record->field->dateAppointed);
                $this->db->where('empID', $this->record->field->empID);
                $this->db->update('employees');
                
                // check if jobposition not the same
                if ($this->record->field->jobPositionID != $this->input->post('old_jobPositionID') ) {
                    // update job position
                    $this->db->set('status', 2); // occupied
                    $this->db->set('employeeTypeID', $this->record->field->employeeTypeID);
                    $this->db->where('jobPositionID', $this->record->field->jobPositionID);
                    $this->db->update('job_positions');
                    
                    // old position
                    $this->db->set('status', 1); // vacant
                    $this->db->set('employeeTypeID', 0);
                    $this->db->where('jobPositionID', $this->input->post('old_jobPositionID'));
                    $this->db->update('job_positions');
                }
                
              
                // record logs
                if ($wasChange) {
                    $logs = "Record - ".trim($this->input->post($this->logfield));
                    $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->pk, 'Update', $logs);
                }                

                // Successfully updated

                $data['class']  = "success";
                $data['msg']    = "Employment successfully updated.";
                $data['urlredirect']    = $this->controller_page."/view/".$this->encrypter->encode($this->record->pk);
                $this->load->view('header', $data);
                $this->load->view('message');
                $this->load->view('footer');
            } else {
                // Error updating
                $data['class']  = "danger";
                $data['msg']    = "Error in updating the employment!";
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

                if ($this->record->delete()) {
                    $logfield = $this->logfield;
                    // record logs
                    $logs = "Record - ".$this->record->field->$logfield;
                    $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->pk, 'Delete', $logs);
                    
                    // vacant the job positions
                    $this->db->set('status',1);
                    $this->db->where('jobPositionID',$this->record->field->jobPositionID);
                    $this->db->update('job_positions');

                    //Success msg
                    $data['class']  = "success";
                    $data['msg']    = $data['current_module']['module_label']." successfully deleted.";
                    $data['urlredirect']    = $this->controller_page."/show";
                    $this->load->view('header', $data);
                    $this->load->view('message');
                    $this->load->view('footer');
                } else {
                    //Error Deleting
                    $data['class']  = "danger";
                    $data['msg']    = "Error in deleting the ".$data['current_module']['module_label']."!";
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
        $this->record->fields[] = 'employees.lname';
        $this->record->fields[] = 'employees.mname';
        $this->record->fields[] = 'employees.fname';
        $this->record->fields[] = 'employees.suffix';
        $this->record->fields[] = 'companies.companyName';
        $this->record->fields[] = 'branches.branchName';
        $this->record->fields[] = 'departments.deptName';
        $this->record->fields[] = 'divisions.divisionName';
        $this->record->fields[] = 'employee_types.employeeType';
        $this->record->fields[] = 'job_positions.positionCode';
        $this->record->fields[] = 'job_titles.jobTitle';
        $this->record->fields[] = 'payroll_groups.payrollGroup';
//         $this->record->fields[] = 'shifts.shiftName';
//         $this->record->fields[] = 'agencies.agencyName';
            
        // set joins
        $this->record->joins[]  = array('employees',$this->table.'.empID=employees.empID','left');
        $this->record->joins[]  = array('companies',$this->table.'.companyID=companies.companyID','left');
        $this->record->joins[]  = array('branches',$this->table.'.branchID=branches.branchID','left');
        $this->record->joins[]  = array('departments',$this->table.'.deptID=departments.deptID','left');
        $this->record->joins[]  = array('divisions',$this->table.'.divisionID=divisions.divisionID','left');
        $this->record->joins[]  = array('employee_types',$this->table.'.employeeTypeID=employee_types.employeeTypeID','left');
        $this->record->joins[]  = array('job_positions',$this->table.'.jobPositionID=job_positions.jobPositionID','left');
        $this->record->joins[]  = array('job_titles','job_positions.jobTitleID=job_titles.jobTitleID','left');
        $this->record->joins[]  = array('payroll_groups',$this->table.'.payrollGroupID=payroll_groups.payrollGroupID','left');
//         $this->record->joins[]  = array('shifts',$this->table.'.shiftID=shifts.shiftID','left');
//         $this->record->joins[]  = array('agencies',$this->table.'.agencyID=agencies.agencyID','left');
        // set where
        $this->record->where[$this->table.'.'.$this->pfield] = $id;
            
        // execute retrieve
        $this->record->retrieve();
        // ----------------------------------------------------------------------------------

        if ($this->roles['view']) {
            $data['rec'] = $this->record->field;
            
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

        $condition_fields = array(
            array('variable'=>'employmentNo', 'field'=>$this->table.'.employmentNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'lname', 'field'=>$this->table.'.lname', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'branchID', 'field'=>$this->table.'.branchID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'deptID', 'field'=>$this->table.'.deptID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'divisionID', 'field'=>$this->table.'.divisionID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'employeeTypeID', 'field'=>$this->table.'.employeeTypeID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'jobTitleID', 'field'=>'job_positions.jobTitleID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        
        //No need for sorting functions
        // sorting fields
        $sorting_fields = array('lname'=>'asc','fname'=>'asc','employmentNo'=>'asc');
        
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
                
                $dateAppointed 	= trim($this->input->post('dateAppointed'));
                
                $sortby 	= trim($this->input->post('sortby'));
                $sortorder 	= trim($this->input->post('sortorder'));
                
                break;
            case 2:
                foreach($condition_fields as $key) {
                    $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
                }
                
                $dateAppointed 	= $this->session->userdata($controller.'_dateAppointed');
                
                $sortby 	= $this->session->userdata($controller.'_sortby');
                $sortorder 	= $this->session->userdata($controller.'_sortorder');
                break;
            default:
                foreach($condition_fields as $key) {
                    $$key['variable'] = $key['default_value'];
                }
                
                $dateAppointed 	= "";
                
                $sortby 	= "";
                $sortorder 	= "";
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
        
        $this->session->set_userdata($controller.'_dateAppointed', $dateAppointed);
        
        $this->session->set_userdata($controller.'_sortby', $sortby);
        $this->session->set_userdata($controller.'_sortorder', $sortorder);
        $this->session->set_userdata($controller.'_limit', $limit);
        
        // assign data variables for views
        foreach($condition_fields as $key) {
            $data[$key['variable']] = $$key['variable'];
        }
        
        $data['dateAppointed'] = $dateAppointed;
        
        // select
        $this->db->select($this->table.'.*');
        $this->db->select('employees.fname');
        $this->db->select('employees.suffix');
        $this->db->select('companies.companyName');
        $this->db->select('companies.companyCode');
        $this->db->select('departments.deptName');
        $this->db->select('departments.deptCode');
        $this->db->select('branches.branchName');
        $this->db->select('branches.branchCode');
        $this->db->select('divisions.divisionName');
        $this->db->select('divisions.divisionCode');
        $this->db->select('employee_types.employeeType');
        $this->db->select('job_positions.positionCode');
        $this->db->select('job_titles.jobTitle');
        
        // from
        $this->db->from($this->table);
        
        // join
        $this->db->join('employees','employments.empID=employees.empID', 'left');
        $this->db->join('companies','employments.companyID=companies.companyID', 'left');
        $this->db->join('branches','employments.branchID=branches.branchID', 'left');
        $this->db->join('departments','employments.deptID=departments.deptID', 'left');
        $this->db->join('divisions','employments.divisionID=divisions.divisionID', 'left');
        $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID', 'left');
        $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
        $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
        
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
        
        if($dateAppointed) {
            $this->db->like($this->table.'.dateAppointed',date('Y-m-d',strtotime($dateAppointed)));
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
        $this->db->select('employees.fname');
        $this->db->select('employees.suffix');
        $this->db->select('companies.companyName');
        $this->db->select('companies.companyCode');
        $this->db->select('departments.deptName');
        $this->db->select('departments.deptCode');
        $this->db->select('branches.branchName');
        $this->db->select('branches.branchCode');
        $this->db->select('divisions.divisionName');
        $this->db->select('divisions.divisionCode');
        $this->db->select('employee_types.employeeType');
        $this->db->select('job_positions.positionCode');
        $this->db->select('job_titles.jobTitle');
        
        // from
        $this->db->from($this->table);
        
        // join
        $this->db->join('employees','employments.empID=employees.empID', 'left');
        $this->db->join('companies','employments.companyID=companies.companyID', 'left');
        $this->db->join('branches','employments.branchID=branches.branchID', 'left');
        $this->db->join('departments','employments.deptID=departments.deptID', 'left');
        $this->db->join('divisions','employments.divisionID=divisions.divisionID', 'left');
        $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID', 'left');
        $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
        $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
        
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
        
        if($dateAppointed) {
            $this->db->like($this->table.'.dateAppointed',date('Y-m-d',strtotime($dateAppointed)));
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
                        $sortby 	= $fld;
                        $sortorder 	= $s_order;
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
        $data['sortby'] 	= $sortby;
        $data['sortorder'] 	= $sortorder;
        $data['limit'] 		= $limit;
        $data['offset'] 	= $offset;
        
        // get
        $data['records'] = $this->db->get()->result();
        
        //load views
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
            array('variable'=>'employmentNo', 'field'=>$this->table.'.employmentNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'lname', 'field'=>$this->table.'.lname', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'branchID', 'field'=>$this->table.'.branchID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'deptID', 'field'=>$this->table.'.deptID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'divisionID', 'field'=>$this->table.'.divisionID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'employeeTypeID', 'field'=>$this->table.'.employeeTypeID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'jobTitleID', 'field'=>'job_positions.jobTitleID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('lname'=>'asc','fname'=>'asc','employmentNo'=>'asc');
        
        $controller = $this->uri->segment(1);
        
        foreach($condition_fields as $key) {
            $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
        }
        
        $dateAppointed      = $this->session->userdata($controller.'_dateAppointed');
        
        $limit      = $this->session->userdata($controller.'_limit');
        $offset     = $this->session->userdata($controller.'_offset');
        $sortby     = $this->session->userdata($controller.'_sortby');
        $sortorder  = $this->session->userdata($controller.'_sortorder');
        
        // select
        $this->db->select($this->table.'.*');
        $this->db->select('employees.fname');
        $this->db->select('employees.suffix');
        $this->db->select('companies.companyName');
        $this->db->select('companies.companyCode');
        $this->db->select('departments.deptName');
        $this->db->select('departments.deptCode');
        $this->db->select('branches.branchName');
        $this->db->select('branches.branchCode');
        $this->db->select('divisions.divisionName');
        $this->db->select('divisions.divisionCode');
        $this->db->select('employee_types.employeeType');
        $this->db->select('job_positions.positionCode');
        $this->db->select('job_titles.jobTitle');
        
        // from
        $this->db->from($this->table);
        
        // join
        $this->db->join('employees','employments.empID=employees.empID', 'left');
        $this->db->join('companies','employments.companyID=companies.companyID', 'left');
        $this->db->join('branches','employments.branchID=branches.branchID', 'left');
        $this->db->join('departments','employments.deptID=departments.deptID', 'left');
        $this->db->join('divisions','employments.divisionID=divisions.divisionID', 'left');
        $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID', 'left');
        $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
        $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
        
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
        
        if($dateAppointed) {
            $this->db->like($this->table.'.dateAppointed',date('Y-m-d',strtotime($dateAppointed)));
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
                        $sortby 	= $fld;
                        $sortorder 	= $s_order;
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
        $data['sortby'] 	= $sortby;
        $data['sortorder'] 	= $sortorder;
        $data['limit'] 		= $limit;
        $data['offset'] 	= $offset;
        
        // get
        $data['records'] = $this->db->get()->result();
        
        $data['title'] = "Employment List";

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
            array('variable'=>'employmentNo', 'field'=>$this->table.'.employmentNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'lname', 'field'=>$this->table.'.lname', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'branchID', 'field'=>$this->table.'.branchID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'deptID', 'field'=>$this->table.'.deptID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'divisionID', 'field'=>$this->table.'.divisionID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'employeeTypeID', 'field'=>$this->table.'.employeeTypeID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'jobTitleID', 'field'=>'job_positions.jobTitleID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('lname'=>'asc');
        
        $controller = $this->uri->segment(1);
        
        foreach($condition_fields as $key) {
            $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
        }
        
        $dateAppointed      = $this->session->userdata($controller.'_dateAppointed');
        
        $limit      = $this->session->userdata($controller.'_limit');
        $offset     = $this->session->userdata($controller.'_offset');
        $sortby     = $this->session->userdata($controller.'_sortby');
        $sortorder  = $this->session->userdata($controller.'_sortorder');
        
        // select
        $this->db->select($this->table.'.*');
        $this->db->select('employees.fname');
        $this->db->select('employees.suffix');
        $this->db->select('companies.companyName');
        $this->db->select('companies.companyCode');
        $this->db->select('departments.deptName');
        $this->db->select('departments.deptCode');
        $this->db->select('branches.branchName');
        $this->db->select('branches.branchCode');
        $this->db->select('divisions.divisionName');
        $this->db->select('divisions.divisionCode');
        $this->db->select('employee_types.employeeType');
        $this->db->select('job_positions.positionCode');
        $this->db->select('job_titles.jobTitle');
        
        // from
        $this->db->from($this->table);
        
        // join
        $this->db->join('employees','employments.empID=employees.empID', 'left');
        $this->db->join('companies','employments.companyID=companies.companyID', 'left');
        $this->db->join('branches','employments.branchID=branches.branchID', 'left');
        $this->db->join('departments','employments.deptID=departments.deptID', 'left');
        $this->db->join('divisions','employments.divisionID=divisions.divisionID', 'left');
        $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID', 'left');
        $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
        $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
        
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
        
        if($dateAppointed) {
            $this->db->like($this->table.'.dateAppointed',date('Y-m-d',strtotime($dateAppointed)));
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
                        $sortby 	= $fld;
                        $sortorder 	= $s_order;
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
        $data['sortby'] 	= $sortby;
        $data['sortorder'] 	= $sortorder;
        $data['limit'] 		= $limit;
        $data['offset'] 	= $offset;
        
        // get
        $records = $this->db->get()->result();
        
    
        $title          = "Employment List";
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
    		<Column ss:Index='2' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='3' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='4' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
    		<Column ss:Index='5' ss:AutoFitWidth=\"1\" ss:Width='150.00'/>
    		<Column ss:Index='6' ss:AutoFitWidth=\"1\" ss:Width='150.00'/>
    		<Column ss:Index='7' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
    		<Column ss:Index='8' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
    		<Column ss:Index='9' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
    		<Column ss:Index='10' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		    ";
    
        // header
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='9'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
    
        $data .= "<Row ss:StyleID='s20'>";
        $data .= "<Cell ss:MergeAcross='9'><Data ss:Type='String'>".$companyName."</Data></Cell>";
        $data .= "</Row>";
        $data .= "<Row ss:StyleID='s24A'>";
        $data .= "<Cell ss:MergeAcross='9'><Data ss:Type='String'>".$address."</Data></Cell>";
        $data .= "</Row>";
    
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='9'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
         
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='9'><Data ss:Type='String'>".strtoupper($title)."</Data></Cell>";
        $data .= "</Row>";
         
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='9'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
    
        $fields[]="  ";
        $fields[]="DATE APPOINTED";
        $fields[]="EMP NO.";
        $fields[]="EMPLOYEE";
        $fields[]="BRANCH";
        $fields[]="DEPT";
        $fields[]="SECTION";
        $fields[]="EMPLOYMENT";
        $fields[]="POSITION";
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
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".date('m/d/Y', strtotime($row->dateAppointed))."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->employmentNo."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->lname.", ".$row->fname." ".$row->mname." ".$row->suffix."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->branchCode."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->deptCode."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->divisionCode."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->employeeType."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->jobTitle."</Data></Cell>";
                if  ($row->status == 1) {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'>Active</Data></Cell>";
                } else if  ($row->status == 2) {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'>Re-assigned</Data></Cell>";
                } else if  ($row->status == 3) {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'>Promoted</Data></Cell>";
                } else if  ($row->status == 4) {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'>Demoted</Data></Cell>";
                } else if  ($row->status == 5) {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'>Terminated</Data></Cell>";
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
        $filename = "employment_list";
    
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
        $this->db->where('branchID', trim($this->input->post('branchID')));
        $this->db->where('deptID', trim($this->input->post('deptID')));
        $this->db->where('divisionID', trim($this->input->post('divisionID')));
        $this->db->where('empID', trim($this->input->post('empID')));
        $this->db->where('employeeTypeID', trim($this->input->post('employeeTypeID')));
      
        if ($this->db->count_all_results($this->table))
            echo "1"; // duplicate
        else 
            echo "0";
    }

    //More pages
    public function reassign()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        // check roles
        if ($this->userrole_model->has_access($this->session->userdata('current_userID'),'View Employment Re-assignment')) {
            $this->record->table  = $this->table;
            $this->record->fields = array();
            
            $table_fields = array( 'empID', 'branchID', 'deptID', 'divisionID', 'employeeTypeID','jobPositionID','salaryType', 'basicSalary','withBasicContribution');
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }                                   
            
            $this->db->where('empID', $this->record->fields['empID']);
            $count = $this->db->count_all_results('employments');

            $this->db->where('empID', $this->input->post('empID'));
            $employee = $this->db->get('employees')->row();
            
            $this->record->fields['lname']   = $employee->lname;
            $this->record->fields['mname']   = $employee->mname;
            $this->record->fields['employmentNo']   = $employmentNo = $employee->empNo.'-'.($count+1);
            
            $this->record->fields['dateAppointed']  = ($this->input->post('dateAppointed')!="") ? date('Y-m-d', strtotime(trim($this->input->post('dateAppointed')))) : "0000-00-00";
            if ($this->record->save()) {
                $this->record->fields = array();
                $this->record->where['employmentNo']= $employmentNo;                
                $this->record->retrieve();          

                 if ($this->record->field->jobPositionID != $this->input->post('old_jobPositionID')) {
                    // update job position
                    $this->db->set('status', 2); // occupied
                    $this->db->set('employeeTypeID', $this->record->field->employeeTypeID);
                    $this->db->where('jobPositionID', $this->record->field->jobPositionID);
                    $this->db->update('job_positions');
                        
                    $this->db->set('status', 1); // vacant
                    $this->db->set('employeeTypeID', 0); // vacant
                    $this->db->where('jobPositionID', $this->input->post('old_jobPositionID'));
                    $this->db->update('job_positions');
                }
                
                // update last appointment date
                $this->db->set('lastAppointment', $this->record->field->dateAppointed);
                $this->db->where('empID', $this->record->field->empID);
                $this->db->update('employees');
                
                // update old employment
                $this->db->set('dateTerminated', $this->record->field->dateAppointed);
                $this->db->set('status', 2); // reassigned
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->update('employments');                   
                
                // transfer contributions                                                       
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->where('status', 1);
                $contribution = $this->db->get('contribution_details');
                
                if ($contribution->num_rows()) {
                    foreach ($contribution->result() as $row) {
                        $this->db->set('payID', $row->payID);
                        $this->db->set('employmentID', $this->record->field->employmentID);                     
                        $this->db->set('employeeShare', $row->employeeShare);
                        $this->db->set('employerShare', $row->employerShare);
                        $this->db->set('taxableAmount', $row->taxableAmount);
                        $this->db->set('effectivity', $row->effectivity);
                        $this->db->set('isAutomatic', $row->isAutomatic);
                        $this->db->set('isWtaxExempted', $row->isWtaxExempted);
                        $this->db->insert('contribution_details');
                    }
                }
                
                // transfer loans
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->where('status', 1);
                $loans = $this->db->get('loan_details');
                
                if ($loans->num_rows()) {
                    foreach ($loans->result() as $row) {
                        $this->db->set('payID', $row->payID);
                        $this->db->set('employmentID', $this->record->field->employmentID);
                        $this->db->set('amount', $row->amount);                     
                        $this->db->insert('loan_details');
                    }
                }
                
                // transfer incentives
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->where('status', 1);
                $incentives = $this->db->get('incentive_details');
                
                if ($incentives->num_rows()) {
                    foreach ($incentives->result() as $row) {
                        $this->db->set('payID', $row->payID);
                        $this->db->set('employmentID', $this->record->field->employmentID);
                        $this->db->set('amount', $row->amount);
                        $this->db->set('effectivity', $row->effectivity);
                        $this->db->insert('incentive_details');
                    }
                }               
                
                // update old employment contribution/loans/incentives
                $this->db->set('status', 0); // inactive
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->update('contribution_details');
                
                $this->db->set('status', 0); // inactive
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->update('loan_details');
                
                $this->db->set('status', 0); // inactive
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->update('incentive_details');
                
                $this->db->set('employmentID', $this->record->field->employmentID); // change to new employment
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->where('status', 1);
                $this->db->update('tax_withholding');
                
                // transfer shift_schedules
                $this->db->set('employmentID', $this->record->field->employmentID); // change to new employment
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->where('date >=', $this->record->field->dateAppointed);
                $this->db->update('shift_schedules');
                
                // transfer attendance
                $this->db->set('employmentID', $this->record->field->employmentID); // change to new employment
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->where('date >=', $this->record->field->dateAppointed);
                $this->db->update('attendance');
                
                // set duties & responsibilities
//              $this->db->where('jobPositio', $this->record->field->jobTitleID);
//              $this->db->where('status', 1);
//              $query = $this->db->get('job_duties');
                
//              if ($query->num_rows()) {
//                  foreach ($query->result() as $row) {
//                      $assignment = explode(',', $row->dutyFor);
                
//                      if (in_array($this->record->field->employeeTypeID, $assignment)) {
//                          $this->db->set('empID', $this->record->field->empID);
//                          $this->db->set('employmentID', $this->record->field->employmentID);
//                          $this->db->set('duty', $row->duty);
//                          $this->db->insert('employment_duties');
//                      }
//                  }
//              }

                // record logs
                $logs = "Record - ".$employmentNo;
                $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->field->$data['pfield'], 'Re-assign', $logs);
                
                $logfield = $this->pfield;

                // success msg
                $data['class']  = "success";
                $data['msg']    = $data['current_module']['module_label']." successfully re-assigned.";
                $data['urlredirect']    =  $this->controller_page.'/view/'.$this->encrypter->encode($this->record->field->$logfield);
                $this->load->view('header', $data);
                $this->load->view('message');
                $this->load->view('footer');                

            } else {
                // Unable to save
                $data['class']  = "danger";
                $data['msg']    = "Error in saving the ".$data['current_module']['module_label']."!";
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

    public function promote()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;

        // check roles
        if ($this->userrole_model->has_access($this->session->userdata('current_userID'),'View Employment Promotion')) {
            $this->record->table  = $this->table;
            $this->record->fields = array();
                
            $table_fields = array('empID', 'companyID', 'officeID', 'divisionID', 'detailedCompanyID','detailedOfficeID','detailedDivisionID','employeeTypeID','jobPositionID',
                    'salaryType','salaryGrade','salaryStep','basicSalary','bank','accountNo','payrollGroupID','shiftID','agencyID','withBasicContribution','isBiometric','remarks');
                
            $table_fields = array( 'empID', 'branchID', 'deptID', 'divisionID', 'employeeTypeID','jobPositionID','salaryType', 'basicSalary','withBasicContribution');
            
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }
                
            $this->db->where('empID', $this->record->fields['empID']);
            $count = $this->db->count_all_results('employments');
    
            $this->db->where('empID', $this->input->post('empID'));
            $employee = $this->db->get('employees')->row();
            
            $this->record->fields['lname']   = $employee->lname;
            $this->record->fields['mname']   = $employee->mname;
            $this->record->fields['employmentNo']   = $employmentNo = $employee->empNo.'-'.($count+1);
            
            $this->record->fields['dateAppointed']  = ($this->input->post('dateAppointed')!="") ? date('Y-m-d', strtotime(trim($this->input->post('dateAppointed')))) : "0000-00-00";

            if ($this->record->save()) {
                $this->record->fields = array();
                $this->record->where['employmentNo']= $employmentNo;
                $this->record->retrieve();
    
                if ($this->record->field->jobPositionID != $this->input->post('old_jobPositionID')) {
                    // update job position
                    $this->db->set('status', 2); // occupied
                    $this->db->set('employeeTypeID', $this->record->field->employeeTypeID);
                    $this->db->where('jobPositionID', $this->record->field->jobPositionID);
                    $this->db->update('job_positions');
                        
                    $this->db->set('status', 1); // vacant
                    $this->db->set('employeeTypeID', 0); // vacant
                    $this->db->where('jobPositionID', $this->input->post('old_jobPositionID'));
                    $this->db->update('job_positions');
                }
    
                // update last appointment date
                $this->db->set('lastAppointment', $this->record->field->dateAppointed);
                $this->db->where('empID', $this->record->field->empID);
                $this->db->update('employees');
    
                // update old employment
                $this->db->set('dateTerminated', $this->record->field->dateAppointed);
                $this->db->set('status', 3); // promoted
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->update('employments');
    
                // transfer contributions
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->where('status', 1);
                $contribution = $this->db->get('contribution_details');
    
                if ($contribution->num_rows()) {
                    foreach ($contribution->result() as $row) {
                        $this->db->set('payID', $row->payID);
                        $this->db->set('employmentID', $this->record->field->employmentID);
                        $this->db->set('employeeShare', $row->employeeShare);
                        $this->db->set('employerShare', $row->employerShare);
                        $this->db->set('taxableAmount', $row->taxableAmount);
                        $this->db->set('effectivity', $row->effectivity);
                        $this->db->set('isAutomatic', $row->isAutomatic);
                        $this->db->set('isWtaxExempted', $row->isWtaxExempted);
                        $this->db->insert('contribution_details');
                    }
                }
    
                // transfer loans
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->where('status', 1);
                $loans = $this->db->get('loan_details');
    
                if ($loans->num_rows()) {
                    foreach ($loans->result() as $row) {
                        $this->db->set('payID', $row->payID);
                        $this->db->set('employmentID', $this->record->field->employmentID);
                        $this->db->set('amount', $row->amount);
                        $this->db->insert('loan_details');
                    }
                }
    
                // transfer incentives
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->where('status', 1);
                $incentives = $this->db->get('incentive_details');
    
                if ($incentives->num_rows()) {
                    foreach ($incentives->result() as $row) {
                        $this->db->set('payID', $row->payID);
                        $this->db->set('employmentID', $this->record->field->employmentID);
                        $this->db->set('amount', $row->amount);
                        $this->db->set('effectivity', $row->effectivity);
                        $this->db->insert('incentive_details');
                    }
                }
    
                // update old employment contribution/loans/incentives
                $this->db->set('status', 0); // inactive
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->update('contribution_details');
    
                $this->db->set('status', 0); // inactive
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->update('loan_details');
    
                $this->db->set('status', 0); // inactive
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->update('incentive_details');
    
                $this->db->set('employmentID', $this->record->field->employmentID); // change to new employment
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->where('status', 1);
                $this->db->update('tax_withholding');
    
                // transfer shift_schedules
                $this->db->set('employmentID', $this->record->field->employmentID); // change to new employment
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->where('date >=', $this->record->field->dateAppointed);
                $this->db->update('shift_schedules');
    
                // transfer attendance
                $this->db->set('employmentID', $this->record->field->employmentID); // change to new employment
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->where('date >=', $this->record->field->dateAppointed);
                $this->db->update('attendance');
    
                // set duties & responsibilities
                //              $this->db->where('jobPositio', $this->record->field->jobTitleID);
                //              $this->db->where('status', 1);
                //              $query = $this->db->get('job_duties');
    
                //              if ($query->num_rows()) {
                //                  foreach ($query->result() as $row) {
                //                      $assignment = explode(',', $row->dutyFor);
    
                //                      if (in_array($this->record->field->employeeTypeID, $assignment)) {
                //                          $this->db->set('empID', $this->record->field->empID);
                //                          $this->db->set('employmentID', $this->record->field->employmentID);
                //                          $this->db->set('duty', $row->duty);
                //                          $this->db->insert('employment_duties');
                //                      }
                //                  }
                //              }
    
                // record logs
                $logs = "Record - ".$employmentNo;
                $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->field->$data['pfield'], 'Promote', $logs);
    
                $logfield = $this->pfield;

                // success msg
                $data['class']  = "success";
                $data['msg']    = $data['current_module']['module_label']." successfully promoted.";
                $data['urlredirect']    =  $this->controller_page.'/view/'.$this->encrypter->encode($this->record->field->$logfield);
                $this->load->view('header', $data);
                $this->load->view('message');
                $this->load->view('footer');
            } else {
                // Unable to save
                $data['class']  = "danger";
                $data['msg']    = "Error in saving the ".$data['current_module']['module_label']."!";
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

    public function demote()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;

        // check roles
        if ($this->userrole_model->has_access($this->session->userdata('current_userID'),'View Employment Demotion')) {
            $this->record->table  = $this->table;
            $this->record->fields = array();
    
            $table_fields = array('lname','mname','title','empID', 'companyID', 'officeID', 'divisionID', 'detailedCompanyID','detailedOfficeID','detailedDivisionID','employeeTypeID','jobPositionID',
                    'salaryType','salaryGrade','salaryStep','basicSalary','bank','accountNo','payrollGroupID','shiftID','agencyID','withBasicContribution','isBiometric','remarks');
    
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }
    
            $this->db->where('empID', $this->record->fields['empID']);
            $count = $this->db->count_all_results('employments');
    
            $this->record->fields['employmentNo']   = $employmentNo = $this->input->post('empNo').'-'.($count+1);
            $this->record->fields['dateAppointed']  = ($this->input->post('dateAppointed')!="") ? date('Y-m-d', strtotime(trim($this->input->post('dateAppointed')))) : "0000-00-00";
            $this->record->fields['dateTerminated'] = ($this->input->post('dateTerminated')!="") ? date('Y-m-d', strtotime(trim($this->input->post('dateTerminated')))) : "0000-00-00";
            if ($this->record->save()) {
                $this->record->fields = array();
                $this->record->where['employmentNo']= $employmentNo;
                $this->record->retrieve();
    
                $this->session->set_userdata('current_empID', $this->record->field->empID);
                $this->session->set_userdata('current_empNo', $this->record->field->empNo);
                $this->session->set_userdata('current_companyID', $this->record->field->companyID);
                $this->session->set_userdata('current_officeID', $this->record->field->officeID);
                $this->session->set_userdata('current_employeeTypeID', $this->record->field->employeeTypeID);
                $this->session->set_userdata('current_agencyID', $this->record->field->agencyID);
    
                if ($this->record->field->jobPositionID != $this->input->post('old_jobPositionID')) {
                    // update job position
                    $this->db->set('status', 2); // occupied
                    $this->db->set('employeeTypeID', $this->record->field->employeeTypeID);
                    $this->db->where('jobPositionID', $this->record->field->jobPositionID);
                    $this->db->update('job_positions');
                        
                    $this->db->set('status', 1); // vacant
                    $this->db->set('employeeTypeID', 0); // vacant
                    $this->db->where('jobPositionID', $this->input->post('old_jobPositionID'));
                    $this->db->update('job_positions');
                }
    
    
                // update last appointment date
                $this->db->set('lastAppointment', $this->record->field->dateAppointed);
                $this->db->where('empID', $this->record->field->empID);
                $this->db->update('employees');
    
                // update old employment
                $this->db->set('dateTerminated', $this->record->field->dateAppointed);
                $this->db->set('status', 4); // demoted
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->update('employments');
    
                // transfer contributions
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->where('status', 1);
                $contribution = $this->db->get('contribution_details');
    
                if ($contribution->num_rows()) {
                    foreach ($contribution->result() as $row) {
                        $this->db->set('payID', $row->payID);
                        $this->db->set('employmentID', $this->record->field->employmentID);
                        $this->db->set('employeeShare', $row->employeeShare);
                        $this->db->set('employerShare', $row->employerShare);
                        $this->db->set('taxableAmount', $row->taxableAmount);
                        $this->db->set('effectivity', $row->effectivity);
                        $this->db->set('isAutomatic', $row->isAutomatic);
                        $this->db->set('isWtaxExempted', $row->isWtaxExempted);
                        $this->db->insert('contribution_details');
                    }
                }
    
                // transfer loans
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->where('status', 1);
                $loans = $this->db->get('loan_details');
    
                if ($loans->num_rows()) {
                    foreach ($loans->result() as $row) {
                        $this->db->set('payID', $row->payID);
                        $this->db->set('employmentID', $this->record->field->employmentID);
                        $this->db->set('amount', $row->amount);
                        $this->db->insert('loan_details');
                    }
                }
    
                // transfer incentives
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->where('status', 1);
                $incentives = $this->db->get('incentive_details');
    
                if ($incentives->num_rows()) {
                    foreach ($incentives->result() as $row) {
                        $this->db->set('payID', $row->payID);
                        $this->db->set('employmentID', $this->record->field->employmentID);
                        $this->db->set('amount', $row->amount);
                        $this->db->set('effectivity', $row->effectivity);
                        $this->db->insert('incentive_details');
                    }
                }
    
                // update old employment contribution/loans/incentives
                $this->db->set('status', 0); // inactive
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->update('contribution_details');
    
                $this->db->set('status', 0); // inactive
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->update('loan_details');
    
                $this->db->set('status', 0); // inactive
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->update('incentive_details');
    
                $this->db->set('employmentID', $this->record->field->employmentID); // change to new employment
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->where('status', 1);
                $this->db->update('tax_withholding');
    
                // transfer shift_schedules
                $this->db->set('employmentID', $this->record->field->employmentID); // change to new employment
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->where('date >=', $this->record->field->dateAppointed);
                $this->db->update('shift_schedules');
    
                // transfer attendance
                $this->db->set('employmentID', $this->record->field->employmentID); // change to new employment
                $this->db->where('employmentID', $this->input->post('employmentID'));
                $this->db->where('date >=', $this->record->field->dateAppointed);
                $this->db->update('attendance');
    
                // set duties & responsibilities
                //              $this->db->where('jobPositio', $this->record->field->jobTitleID);
                //              $this->db->where('status', 1);
                //              $query = $this->db->get('job_duties');
    
                //              if ($query->num_rows()) {
                //                  foreach ($query->result() as $row) {
                //                      $assignment = explode(',', $row->dutyFor);
    
                //                      if (in_array($this->record->field->employeeTypeID, $assignment)) {
                //                          $this->db->set('empID', $this->record->field->empID);
                //                          $this->db->set('employmentID', $this->record->field->employmentID);
                //                          $this->db->set('duty', $row->duty);
                //                          $this->db->insert('employment_duties');
                //                      }
                //                  }
                //              }
    
                // record logs
                $logs = "Record - ".$employmentNo;
                $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->field->$data['pfield'], 'Demote', $logs);
    
                $logfield = $this->pfield;

                // success msg
                $data['class']  = "success";
                $data['msg']    = $data['current_module']['module_label']." successfully demoted.";
                $data['urlredirect']    =  $this->controller_page.'/view/'.$this->encrypter->encode($this->record->field->$logfield);
                
                $this->load->view('header', $data);
                $this->load->view('message');
                $this->load->view('footer');                
            } else {
                // Unable to save
                $data['class']  = "danger";
                $data['msg']    = "Error in saving the ".$data['current_module']['module_label']."!";
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

    public function terminate($id)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);
        // check roles
        if ($this->roles['terminate']) {
            // set fields
            $this->record->fields = array();
            // set table
            $this->record->table  = $this->table;
            // set where
            $this->record->where[$this->pfield] = $id;
            // execute retrieve
            $this->record->retrieve();

            if (!empty($this->record->field)) {
                $this->record->pfield   = $this->pfield;
                $this->record->pk       = $id;
                
                // update status
                $this->db->set('dateTerminated', date('Y-m-d'));
                $this->db->set('status',5);
                $this->db->where('employmentID', $id);
                $this->db->update('employments');
                
                
                // vacant position
                $this->db->set('status', 1); // vacant
                $this->db->set('employeeTypeID', 0); // vacant
                $this->db->where('jobPositionID', $this->record->field->jobPositionID);
                $this->db->update('job_positions');
                
                // record logs
                $logfield = $this->logfield;

                // record logs
                $logs = "Record - ".$this->record->field->$logfield;
                $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->pk, 'Terminated', $logs);
                
                //Successfully deleted
                $data['class']  = "success";
                $data['msg']    = $data['current_module']['module_label']." successfully terminated!";
                $data['urlredirect']    = $this->controller_page."/view/".$this->encrypter->encode($id);
                $this->load->view('header', $data);
                $this->load->view('message');
                $this->load->view('footer');
               
            } else {
                //Record not found
                $data['class']  = "danger";
                $data['msg']    = $data['current_module']['module_label']." record not found!";
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

    public function set_reassignment($id)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);
        // check roles
        if ($this->roles['re-assign']) {
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->record->table = $this->table;
            // set fields for the current table
            $this->record->setFields();
            // extend fields - join tables
            $this->record->fields[] = 'employees.empNo';
            $this->record->fields[] = 'employees.fname';
            $this->record->fields[] = 'employees.suffix';
            $this->record->fields[] = 'employee_types.basicContributions';
            $this->record->fields[] = 'job_positions.positionCode';
            $this->record->fields[] = 'job_titles.jobTitle';
            // set joins
            $this->record->joins[]  = array('employees',$this->table.'.empID=employees.empID','left');
            $this->record->joins[]  = array('employee_types',$this->table.'.employeeTypeID=employee_types.employeeTypeID','left');
            $this->record->joins[]  = array('job_positions',$this->table.'.jobPositionID=job_positions.jobPositionID','left');
            $this->record->joins[]  = array('job_titles','job_positions.jobTitleID=job_titles.jobTitleID','left');
            // set where
            $this->record->where[$this->table.'.'.$this->pfield] = $id;
            // execute retrieve
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;
            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path.'/reassign');
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

    public function set_promotion($id)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);
        // check roles
        if ($this->roles['promote']) {
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->record->table = $this->table;
            // set fields for the current table
            $this->record->setFields();
            // extend fields - join tables
            $this->record->fields[] = 'employees.empNo';
            $this->record->fields[] = 'employees.fname';
            $this->record->fields[] = 'employees.suffix';
            $this->record->fields[] = 'employee_types.basicContributions';
            $this->record->fields[] = 'job_positions.positionCode';
            $this->record->fields[] = 'job_titles.jobTitle';
            // set joins
            $this->record->joins[]  = array('employees',$this->table.'.empID=employees.empID','left');
            $this->record->joins[]  = array('employee_types',$this->table.'.employeeTypeID=employee_types.employeeTypeID','left');
            $this->record->joins[]  = array('job_positions',$this->table.'.jobPositionID=job_positions.jobPositionID','left');
            $this->record->joins[]  = array('job_titles','job_positions.jobTitleID=job_titles.jobTitleID','left');
            // set where
            $this->record->where[$this->table.'.'.$this->pfield] = $id;
            // execute retrieve
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;

            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path.'/promote');
            $this->load->view('footer');
        } else {

        }
    }

    public function set_demotion($id)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);

        // check roles
        if ($this->roles['demote']) {
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->record->table = $this->table;
            // set fields for the current table
            $this->record->setFields();
            // extend fields - join tables
            $this->record->fields[] = 'employees.empNo';
            $this->record->fields[] = 'employees.fname';
            $this->record->fields[] = 'employees.suffix';
            $this->record->fields[] = 'employee_types.basicContributions';
            $this->record->fields[] = 'job_positions.positionCode';          
            $this->record->fields[] = 'job_titles.jobTitle';     
            // set joins
            $this->record->joins[]  = array('employees',$this->table.'.empID=employees.empID','left');
            $this->record->joins[]  = array('employee_types',$this->table.'.employeeTypeID=employee_types.employeeTypeID','left');
            $this->record->joins[]  = array('job_positions',$this->table.'.jobPositionID=job_positions.jobPositionID','left');
            $this->record->joins[]  = array('job_titles','job_positions.jobTitleID=job_titles.jobTitleID','left');
            // set where
            $this->record->where[$this->table.'.'.$this->pfield] = $id;
            // execute retrieve
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;

            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path.'/demote');
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

    //More functions


    public function getEmploymentsEncrypt()
    {
        $empID = $this->encrypter->decode($this->input->post('empID'));
    
        $this->db->select('employments.employmentID');
        $this->db->select('employments.employmentNo');
        $this->db->select('companies.companyAbbr');
        //$this->db->select('offices.officeAbbr');
        $this->db->select('divisions.divisionAbbr');
        $this->db->select('employee_types.employeeType');
        $this->db->select('job_positions.positionCode');
        $this->db->select('job_titles.jobTitle');
        $this->db->from('employments');
        $this->db->join('companies','employments.companyID=companies.companyID', 'left');
        //$this->db->join('offices','employments.officeID=offices.officeID', 'left');
        $this->db->join('divisions','employments.divisionID=divisions.divisionID', 'left');
        $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID', 'left');
        $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
        $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
        $this->db->where('employments.empID', $empID);
        if ($this->session->userdata('assigned_companyID')) {
            $this->db->where('employments.companyID', $this->session->userdata('assigned_companyID'));
        }
        //if ($this->session->userdata('assigned_officeID')) {
        //    $this->db->where('employments.officeID', $this->session->userdata('assigned_officeID'));
        //}
        if ($this->session->userdata('assigned_divisionID')) {
            $this->db->where('employments.divisionID', $this->session->userdata('assigned_divisionID'));
        }
        $this->db->where('employments.status', 1);
        $this->db->order_by('employments.dateAppointed','asc');
        $records = $this->db->get();
    
        echo $this->frameworkhelper->get_json_data_encrypt($records, 'employmentID', array('employmentNo'=>' - ','companyAbbr'=>' / ','officeAbbr'=>' / ','employeeType'=>' / ','jobTitle'=>''));
    }

    public function setEmploymentsPayEncrypt($empID=0, $type="incentive")
    {
        $data['empID']  = $this->encrypter->decode($empID);
        $data['type']   = $type;        
                
        $this->db->select('employments.employmentID');
        $this->db->select('employments.employmentNo');
        $this->db->select('companies.companyAbbr');
        $this->db->select('offices.officeAbbr');
        $this->db->select('divisions.divisionAbbr');
        $this->db->select('employee_types.employeeType');
        $this->db->select('job_positions.positionCode');
        $this->db->select('job_titles.jobTitle');
        $this->db->from('employments');
        $this->db->join('companies','employments.companyID=companies.companyID', 'left');
        $this->db->join('offices','employments.officeID=offices.officeID', 'left');
        $this->db->join('divisions','employments.divisionID=divisions.divisionID', 'left');
        $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID', 'left');
        $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
        $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
        $this->db->where('employments.empID', $data['empID']);
        $this->db->where('employments.status', 1);
        $this->db->order_by('employments.dateAppointed','asc');
        $data['records'] = $this->db->get();
                
        // load views
        echo $this->load->view($this->module_path.'/set_employments_pay', $data, true); 
    }

    public function setEmployeesEncrypt($companyID=0, $officeID=0, $divisionID=0, $payrollGroupID=0, $employeeTypeID=0)
    {
        $data['companyID']      = $this->encrypter->decode($companyID);
        $data['officeID']       = $this->encrypter->decode($officeID);
        $data['divisionID']     = $this->encrypter->decode($divisionID);
        $data['payrollGroupID'] = $this->encrypter->decode($payrollGroupID);
        $data['employeeTypeID'] = explode('_', $employeeTypeID);
        $data['type']           = $type;
    
        // select
        $this->db->select($this->table.'.*');
        $this->db->select('employees.empNo');
        $this->db->select('employees.lname');
        $this->db->select('employees.suffix');
        $this->db->select('companies.companyAbbr');
        $this->db->select('offices.officeAbbr');
        $this->db->select('divisions.divisionAbbr');
        $this->db->select('employee_types.employeeType');
        $this->db->select('job_positions.positionCode');
        $this->db->select('job_titles.jobTitle');
        $this->db->from($this->table);
        $this->db->join('employees', $this->table.'.empID=employees.empID', 'left');
        $this->db->join('companies','employments.companyID=companies.companyID', 'left');
        $this->db->join('offices','employments.officeID=offices.officeID', 'left');
        $this->db->join('divisions','employments.divisionID=divisions.divisionID', 'left');
        $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID', 'left');
        $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
        $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
        if ($data['companyID']) {
            $this->db->where('employments.companyID', $data['companyID']);
        }
        if ($data['officeID']) {
            $this->db->where('employments.officeID', $data['officeID']);
        }
        if ($data['divisionID']) {
            $this->db->where('employments.divisionID', $data['divisionID']);
        }               
        $this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
        $this->db->where('employments.payrollGroupID', $data['payrollGroupID']);
        $this->db->where('employments.status', 1);
        $this->db->order_by('employments.lname','asc');
        $this->db->order_by('employees.fname','asc');
        $this->db->order_by('employments.mname','asc');
        $data['records'] = $this->db->get();
    
        // load views
        echo $this->load->view($this->module_path.'/set_employees', $data, true);
    }

    public function update_batch()
    {
        //************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;
        // **************************************************
    
        // check roles
        if ($this->roles['edit']) {
            $this->db->where('empID', $this->encrypter->decode(trim($this->input->post('id'))));
            $this->db->where('status', 1);
            $employment = $this->db->get('employments', 1)->row();
                
            $this->record->table  = $this->table;
            $this->record->fields = array();
    
            if (trim($this->input->post('field')) == 'empNo') {
                $this->record->fields['employmentNo'] = trim($this->input->post('value'));
            } elseif (trim($this->input->post('field')) == 'allowedShiftID') {
                $shifts = $this->input->post('value');
                if (!empty($shifts)) {
                    $this->record->fields['allowedShiftID'] = implode(',', $this->input->post('value'));
                }           
            } else {
                $this->record->fields[trim($this->input->post('field'))] = trim($this->input->post('value'));
            }
                
            $this->record->pfield   = $this->pfield;
            $this->record->pk       = $employment->employmentID;
    
            // field logs here
            $wasChange = $this->log_model->field_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Update', $this->record->fields);
    
            if ($this->record->update()) {
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

    public function update_field()
    {
        //************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;
        // **************************************************
    
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
                    $logs = "Record - ".trim($this->input->post('employmentNo'));
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


    //Ajax Functions
        // get record by json
    public function getJSON($table, $fields)
    {
        $this->load->model('generic_model','record');
        
        $fields = explode('_',$fields);
        // set table
        $this->record->table = $table;
        // set where
        if (!empty($fields)) {
            foreach($fields as $f) {
                $this->record->where[trim($f)] = trim($this->input->post(trim($f)));
            }
        }
        // execute retrieve
        $this->record->retrieve();
        $data = (array) $this->record->field;

        $res= json_encode($data);
        echo $res;
    }

    private function _in_used($id=0)
    {
        $tables = array();
    
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
