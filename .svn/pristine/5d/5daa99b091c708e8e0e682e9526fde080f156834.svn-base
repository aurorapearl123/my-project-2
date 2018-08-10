<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Payroll_gnp extends CI_Controller
{

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
        $this->load->model('generic_model', 'record');
        $this->module = 'Payroll';
        $this->data['controller_page'] = $this->controller_page = site_url('payroll_gnp'); // defines contoller link
        $this->table = 'payroll'; // defines the default table
        $this->pfield = $this->data['pfield'] = 'payrollID'; // defines primary key
        $this->logfield = 'payrollNo';
        $this->module_path = 'modules/' . strtolower(str_replace(" ", "_", $this->module)) . '/payroll_gnp'; // defines module path

        // check for maintenance period
        if ($this->config_model->getConfig('Maintenance Mode') == '1') {
            header('location: ' . site_url('maintenance_mode'));
        }
        
        // check user session
        if (! $this->session->userdata('current_user')->sessionID) {
            header('location: ' . site_url('login'));
        }
    }

    private function submenu()
    {
        // submenu setup
        require_once ('modules.php');
        
        foreach ($modules as $mod) {
            // modules/<module>/
            // - <menu>
            // - <metadata>
            $this->load->view('modules/' . str_replace(" ", "_", strtolower($mod)) . '/metadata');
        }
        
        $this->data['modules'] = $this->modules;
        $this->data['current_main_module'] = $this->modules[$this->module]['main']; // defines the current main module
        $this->data['current_module'] = $this->modules[$this->module]['sub']['Payroll_gnp']; // defines the current sub module

        // check roles
        $this->check_roles();
        $this->data['roles'] = $this->roles;
    }

    private function check_roles()
    {
        // check roles
        $this->roles['create'] = $this->session->userdata('current_user')->isAdmin;
        $this->roles['view'] = $this->session->userdata('current_user')->isAdmin;
        $this->roles['edit'] = $this->session->userdata('current_user')->isAdmin;
        $this->roles['delete'] = $this->session->userdata('current_user')->isAdmin;
        $this->roles['approve'] = $this->session->userdata('current_user')->isAdmin;
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

            // $this->session->set_userdata('current_companyID', $data['companyID']);
            // $this->session->set_userdata('current_branchID', $data['branchID']);
            // $this->session->set_userdata('current_divisionID', $data['divisionID']);
            // $this->session->set_userdata('current_payrollGroupID', $data['payrollGroupID']);
            // $this->session->set_userdata('current_payrollPeriodID', $data['payrollPeriodID']);
            // $this->session->set_userdata('current_attendancePeriodID', $data['attendancePeriodID']);
            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path . '/create');
            $this->load->view('footer');
        } else {
            // no access this page
            $data['class'] = "danger";
            $data['msg'] = "Sorry, you don't have access to this page!";
            $data['urlredirect'] = "";
            $this->load->view('header', $data);
            $this->load->view('message');
            $this->load->view('footer');
        }
    }


    public function save()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;

        // check role
        if ($this->roles['create']) {
            $this->record->table  = $this->table;
            $this->record->fields = array();
            
            $this->record->fields['payrollPeriodID']    = $this->encrypter->decode($this->input->post('payrollPeriodID')).'periodID';
            $this->record->fields['companyID']          = $this->input->post('companyID').'companyID';
            $this->record->fields['branchID']           = $this->input->post('branchID');
            $this->record->fields['deptID']             = $this->input->post('deptID');
            $this->record->fields['divisionID']         = $this->input->post('divisionID');
            $this->record->fields['employeeTypeID']     = implode(',',$this->input->post('employeeTypeID'));
            $this->record->fields['payrollGroupID']     = $this->input->post('payrollGroupID');
            $this->record->fields['attendancePeriodID'] = $this->encrypter->decode($this->input->post('attendancePeriodID'));
            $this->record->fields['generatedBy']        = $this->session->userdata('current_user')->userID;
            $this->record->fields['dateGenerated']      = date('Y-m-d H:i:s');
            
            // get payroll period info
            $this->db->where('payrollPeriodID', $this->record->fields['payrollPeriodID']);
            $payrollPeriod = $this->db->get('payroll_periods', 1)->row();
            
            // attendance
            $this->db->where('payrollPeriodID', $this->record->fields['attendancePeriodID']);
            $attendancePeriod = $this->db->get('payroll_periods', 1)->row();
            
            $start    = strtotime($attendancePeriod->startDate);
            $end      = strtotime($attendancePeriod->endDate);
            
            $this->record->fields['payrollNo']          = $genNo = $this->_generateID(date('ym', strtotime($payrollPeriod->startDate)));
            
            if ($this->record->save()) {
                $id = $this->db->insert_id();
                $this->record->fields = array();
                $this->record->where['payrollNo']   = $genNo;
                $this->record->retrieve();

                $this->frameworkhelper->increment_series('General Payroll Series');
                
                if ($payrollPeriod->type == 'SM') {
                    $firstHalf = (date('d',strtotime($payrollPeriod->startDate) == '01')) ? true : false;
                }
                
                // get attendance dates
                $this->db->where('payrollPeriodID', $this->record->field->payrollPeriodID);
                $attendance_dates = $this->db->get('payroll_details');
                $days = $attendance_dates->num_rows();
                
                $employmentIDs      = $this->input->post('chkAdd');
                
                // load employees
                $this->db->select('employments.*');
                $this->db->select('employees.taxID');
                $this->db->select('employee_types.workingDays');
                $this->db->select('employee_types.employeeType');
                $this->db->select('employee_types.withPay');
                $this->db->select('tax_withholding.payID');
                $this->db->select('tax_withholding.amount as wtax');
                $this->db->select('tax_withholding.isAutomatic');
                $this->db->select('tax_exemptions.personalExemption');
                $this->db->select('tax_exemptions.additionalExemption');
                $this->db->from('employments');
                $this->db->join('employees', 'employments.empID=employees.empID', 'left');
                $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID', 'left');
                $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
                $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
                $this->db->join('tax_withholding','employments.employmentID=tax_withholding.employmentID','left');
                $this->db->join('tax_exemptions','employees.taxID=tax_exemptions.taxID','left');
                $this->db->where_in('employments.employmentID', $employmentIDs);
                $this->db->order_by('employments.lname','asc');
                $this->db->order_by('employees.fname','asc');
                $this->db->order_by('employments.mname','asc');
                $records = $this->db->get();
                
                $incentive_batch    = array();
                $premium_batch      = array();
                $loan_batch         = array();
                if ($records->num_rows()) {
                    foreach ($records->result() as $row) {                                              
                        $hoursRequired  = 0;
                        $hoursUndertime = 0;
                        $hoursRendered  = 0;
                        $daysRequired   = 0;
                        $daysUndertime  = 0;
                        $daysRendered   = 0;
                        $leavesWP       = 0;
                        $leavesWOP      = 0;
                        $lateToLeave    = 0;
                        $wop            = 0;
                        $totalIncentive = 0;
                        $totalGross     = 0;
                        $totalDeduction = 0;
                        $netPay         = 0;
                        
                        
                        $tardyMin       = 0;
                        $tardyCount     = 0;
                        $utCount        = 0;
                        $utMin          = 0;
                        $absentCount    = 0;
                        
                        // -------- calculate attendance --------------------------------------------
                        for ($current = $start; $current <= $end; $current = strtotime('+1 day', $current)) {
                            $dtr = $this->dtrlog->analyze($row->employmentID, $current);

                            if (!empty ($dtr)) {
                                foreach ($dtr as $log) {
                                    if ($log['schedule'] == '1') {
                                        $daysRequired++;
                                    }
                                    
                                    // tardiness
                                    if (strlen($log['tardy_date']) > 5) {
                                        $tardyCount  += 2;
                                    } elseif (intval($log['tardy']) > 0) {
                                        $tardyCount  += 1;
                                    }
                                    $tardyMin += intval($log['tardy']);

                                    // undertime
                                    if (strlen($log['ut_date']) > 5) {
                                        $utCount  += 2;
                                    } elseif (intval($log['ut_date']) > 0) {
                                        $utCount  += 1;
                                    }
                                    $utMin += intval($log['undertime']);

                                    // absences
                                    $absentCount += ($log['remarks'] == 'ABSENT') ? 1 : 0;

                                    // leaves, orders, suspension
                                    //                          $res[$info['los']]   += ($info['los']) ? $info['los_day'] : 0;

                                }
                            }
                        }
                        
                        $ut = $tardyMin + $utMin;
                        
                        // number of days without pay
                        $daysRequired   = $daysRequired;
                        $daysUndertime  = $absentCount;
                        $daysUndertime += $ut / 480;
                        $daysRendered   = $daysRequired - $daysUndertime;
                        
                        
                        // calculate withoutpay
                        if ($daysUndertime > 0) {
                            // get daily rate
                            $dailyRate    = $row->basicSalary / $row->workingDays;
                            $wop          = round($dailyRate * $daysUndertime, 2);
                        }
                        // ----------------------------------------------------------------------
                        
                        $totalDeduction   = $wop;

                        // -------- calculate salary --------------------------------------------
                        $monthlySalary = round($row->basicSalary - round($wop, 3), 2);
                        if ($payrollPeriod->type == 'MO') {
                            $salary = round($row->basicSalary, 2);
                            //$salary   = round($row->basicSalary - $wop, 2);
                        } else {
                            $salary = ($firstHalf) ? $this->fnumber_format(($row->basicSalary) / 2, '.') : round(($row->basicSalary) / 2, 2, PHP_ROUND_HALF_UP);
                            //$salary   = ($firstHalf) ? $this->fnumber_format(($row->basicSalary - $wop) / 2, '.') : round(($row->basicSalary - $wop) / 2, 2, PHP_ROUND_HALF_UP);
                        }
                        
                        
                        // ----------------------------------------------------------------------
                        
                        // -------- save initial payslip ----------------------------------------
                        $this->db->set('payrollID', $this->record->field->payrollID);
                        $this->db->set('empID', $row->empID);
                        $this->db->set('employmentID', $row->employmentID);
                        $this->db->set('employeeTypeID', $row->employeeTypeID);
                        $this->db->set('jobTitleID', $row->jobTitleID);
                        $this->db->set('hoursRequired', $hoursRequired);
                        $this->db->set('hoursUndertime', $hoursUndertime);
                        $this->db->set('hoursRendered', $hoursRendered);
                        $this->db->set('daysRequired', $daysRequired);
                        $this->db->set('daysUndertime', $daysUndertime);
                        $this->db->set('daysRendered', $daysRendered);
                        $this->db->set('leavesWP', $leavesWP);
                        $this->db->set('leavesWOP', $leavesWOP);
                        $this->db->set('basicRate', $salary);
                        $this->db->set('wop', $wop);
                        $this->db->insert('payslips');
                        // ----------------------------------------------------------------------
                        
                        $payslipID = $this->db->insert_id();
                        
                        // calculate incentives
                        $result = $this->payrollcalc->incentive($row->employmentID, $payrollPeriod, $payslipID);
                        $totalIncentive  = $result['totalIncentive'];   
                        $incentive_batch = $result['incentives'];                                                                                                                       

                        // calculate grosspay
                        $grossPay = $salary + $totalIncentive;

                        // calculate wtax
                        if ($row->payID) {
                            if ($row->isAutomatic) {
                                $result = $this->payrollcalc->wtax($row->employmentID, $row->taxID, $row->basicSalary, $payrollPeriod);
                                $wtax = $result['amount'];
                            } else {
                                $wtax = $row->wtax;
                            }
                            
                            
                            $info = array();
                            $info['payslipID']      = $payslipID;
                            $info['payID']          = $row->payID;
                            $info['type']           = 1;
                            $info['deductionID']    = 0;
                            if ($payrollPeriod->type == 'SM') {
                                $info['amount']  = ($firstHalf) ? $this->fnumber_format($wtax / 2, '.') : round($wtax / 2, 2, PHP_ROUND_HALF_UP);
                            } else {
                                $info['amount']  = round($wtax, 2, PHP_ROUND_HALF_UP);
                            }
                            $loan_batch[] = $info;
                            
                            $totalDeduction += $info['amount'];
                        }
                        
                        // calculate contributions
                        $result = $this->payrollcalc->contribution($row->employmentID, $row->basicSalary, $payrollPeriod, $payslipID);                      
                        $premium_batch  = $result['contributions'];
                        $totalDeduction    += $result['totalEmployeeShare'];
                        
                        // calculate loans
                        $result = $this->payrollcalc->loan($row->employmentID, $row->basicSalary, $payrollPeriod, $payslipID);
                        if (!empty($result['loans'])) {
                            foreach ($result['loans'] as $loan) {
                                $loan_batch[] = $loan;
                            }
                        }           
                        $totalDeduction    += $result['totalDeduction'];

                        // calculate net pay
                        $netPay = $grossPay - $totalDeduction;
                        
                        // update payslip
                        $this->db->set('wop', $wop);
                        $this->db->set('totalIncentive', $totalIncentive);
                        $this->db->set('totalGross', $grossPay);
                        $this->db->set('totalDeduction', $totalDeduction);
                        $this->db->set('netPay', $netPay);
                        $this->db->where('payslipID', $payslipID);
                        $this->db->update('payslips');
                        
                        if (!empty($incentive_batch)) {
                            $this->db->insert_batch('payslip_incentives', $incentive_batch);
                        }

                        if (!empty($premium_batch)) {
                            $this->db->insert_batch('payslip_contributions', $premium_batch);
                        }

                        if (!empty($loan_batch)) {
                            $this->db->insert_batch('payslip_deductions', $loan_batch);
                        }
                    }
                    
                    
                }
                
                // $this->session->set_userdata('current_companyID', $this->record->field->companyID);
                // $this->session->set_userdata('current_branchID', $this->record->field->branchID);
                // $this->session->set_userdata('current_divisionID', $this->record->field->divisionID);
                // $this->session->set_userdata('current_payrollPeriodID', $this->record->field->payrollPeriodID);
                // $this->session->set_userdata('current_attendancePeriodID', $this->record->field->attendancePeriodID);
                // $this->session->set_userdata('current_payrollGroupID', $this->record->field->payrollGroupID);
                
                // record logs
                $pfield = $this->pfield;
                
                $logs = "Record - " . trim($this->input->post($this->logfield));
                $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->field->$pfield, 'Insert', $logs);
                
                $logfield = $this->pfield;
                
                // // success msg
                $data["class"] = "success";
                $data["msg"] = $data['current_module']['module_label'] . " successfully saved.";
                $data["urlredirect"] = $this->controller_page . "/view/" . $this->encrypter->encode($id);
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            } else {
                // error
                $data["class"] = "danger";
                $data["msg"] = "Error in saving the " . $this->data['current_module']['module_label'] . "!";
                ;
                $data["urlredirect"] = "";
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            }
        } else {
            // error
            $data["class"] = "danger";
            $data["msg"] = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header", $data);
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
            $this->record->table = $this->table;
            // set fields for the current table
            $this->record->setFields();
            // set where
            $this->record->where[$this->pfield] = $id;
            // execute retrieve
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;
            
            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path . '/edit');
            $this->load->view('footer');
        } else {
            // no access this page
            $data['class'] = "danger";
            $data['msg'] = "Sorry, you don't have access to this page!";
            $data['urlredirect'] = "";
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
        $id = $this->encrypter->decode($this->input->post($this->pfield));
        $table_fields = array('countryID', 'province', 'remarks', 'status');
        
        // check roles
        if ($this->roles['edit']) {
            $this->record->table = $this->table;
            $this->record->fields = array();
            
            foreach ($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }
            
            $this->record->pfield = $this->pfield;
            $this->record->pk = $id;
            
            // field logs here
            $wasChange = $this->log_model->field_logs($data['current_module']['module_label'], $this->table, $this->pfield, $id, 'Update', $this->record->fields);
            
            if ($this->record->update()) {
                // record logs
                if ($wasChange) {
                    $logs = "Record - " . trim($this->input->post($this->logfield));
                    $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->pk, 'Update', $logs);
                }
                
                // successful
                $data["class"] = "success";
                $data["msg"] = $this->data['current_module']['module_label'] . " successfully updated.";
                $data["urlredirect"] = $this->controller_page . "/view/" . $this->encrypter->encode($id);
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            } else {
                // error
                $data["class"] = "danger";
                $data["msg"] = "Error in updating the " . $this->data['current_module']['module_label'] . "!";
                $data["urlredirect"] = $this->controller_page . "/view/" . $this->record->pk;
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            }
        } else {
            // error
            $data["class"] = "danger";
            $data["msg"] = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header", $data);
            $this->load->view("message");
            $this->load->view("footer");
        }
    }

    public function delete($id = 0)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);
        
        // check roles
        if ($this->roles['delete']) {
            // set fields
            $this->record->fields = array();
            // set table
            $this->record->table = $this->table;
            // set where
            $this->record->where[$this->pfield] = $id;
            // execute retrieve
            $this->record->retrieve();
            // echo $this->db->last_query();
            
            if (! empty($this->record->field)) {
                $this->record->pfield = $this->pfield;
                $this->record->pk = $id;
                
                // record logs
                $rec_value = $this->record->field->name;
                
                // check if in used
                if (! $this->_in_used($id)) {
                    if ($this->record->delete()) {
                        $this->db->where('payrollID', $id);
                        $payslips = $this->db->get('payslips')->result();

                        foreach ($payslips as $payslip) {
                            $this->db->where('payslipID', $payslip->payslipID);
                            $this->db->delete('payslip_contributions');

                            $this->db->where('payslipID', $payslip->payslipID);
                            $this->db->delete('payslip_deductions');

                            $this->db->where('payslipID', $payslip->payslipID);
                            $this->db->delete('payslip_incentives');                        
                        }

                        $this->db->where('payrollID', $id);
                        $this->db->delete('payslips');


                        $logfield = $this->logfield;
                        
                        // record logs
                        $logs = "Record - " . $this->record->field->$logfield;
                        $this->log_model->table_logs($this->data['current_module']['module_label'], $this->table, $this->pfield, $this->record->pk, 'Delete', $logs);
                        
                        // successful
                        $data["class"] = "success";
                        $data["msg"] = $this->data['current_module']['module_label'] . " successfully deleted.";
                        $data["urlredirect"] = $this->controller_page . "/";
                        $this->load->view("header", $data);
                        $this->load->view("message");
                        $this->load->view("footer");
                    } else {
                        // error
                        $data["class"] = "danger";
                        $data["msg"] = "Error in deleting the " . $this->data['current_module']['module_label'] . "!";
                        $data["urlredirect"] = "";
                        $this->load->view("header", $data);
                        $this->load->view("message");
                        $this->load->view("footer");
                    }
                } else {
                    // error
                    $data["class"] = "danger";
                    $data["msg"] = "Data integrity constraints.";
                    $data["urlredirect"] = "";
                    $this->load->view("header", $data);
                    $this->load->view("message");
                    $this->load->view("footer");
                }
            } else {
                // error
                $data["class"] = "danger";
                $data["msg"] = $this->data['current_module']['module_label'] . " record not found!";
                $data["urlredirect"] = "";
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            }
        } else {
            // error
            $data["class"] = "danger";
            $data["msg"] = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header", $data);
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
            $this->record->table = $this->table;
            // set fields for the current table
            $this->record->setFields();
            // extend fields - join tables      
            $this->record->fields[] = 'payroll_periods.payrollPeriod';
            $this->record->fields[] = 'attendance_periods.payrollPeriod as attendancePeriod';
            $this->record->fields[] = 'payroll_periods.type';
            $this->record->fields[] = 'companies.companyName';
            $this->record->fields[] = 'branches.branchName';
            $this->record->fields[] = 'divisions.divisionName'; 
            $this->record->fields[] = 'payroll_groups.payrollGroup';

            // set joins
            $this->record->joins[]  = array('payroll_periods',$this->table.'.payrollPeriodID=payroll_periods.payrollPeriodID','left');
            $this->record->joins[]  = array('payroll_periods attendance_periods',$this->table.'.attendancePeriodID=attendance_periods.payrollPeriodID','left');
            $this->record->joins[]  = array('companies',$this->table.'.companyID=companies.companyID','left');
            $this->record->joins[]  = array('branches',$this->table.'.branchID=branches.branchID','left');
            $this->record->joins[]  = array('divisions',$this->table.'.divisionID=divisions.divisionID','left');                
            $this->record->joins[]  = array('payroll_groups',$this->table.'.payrollGroupID=payroll_groups.payrollGroupID','left');
            // set where
            $this->record->where[$this->table.'.'.$this->pfield] = $id;
            
            // execute retrieve
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;
            
            $this->db->select('payslips.*');
            $this->db->select('employees.fname');
            $this->db->select('employees.suffix');
            $this->db->select('employments.lname');
            $this->db->select('employments.mname');
            $this->db->select('employments.employmentNo');                      
            $this->db->select('employee_types.employeeType');
            $this->db->from('payslips');
            $this->db->join('employees','payslips.empID=employees.empID','left');
            $this->db->join('employments','payslips.employmentID=employments.employmentID','left');
            $this->db->join('employee_types','payslips.employeeTypeID=employee_types.employeeTypeID','left');
            $this->db->where('payslips.payrollID', $id);
            $data['payslips'] = $this->db->get();
            
            $data['in_used'] = $this->_in_used($id);
            // record logs
            $pfield = $this->pfield;
            if ($this->config_model->getConfig('Log all record views') == '1') {
                $logs = "Record - " . $this->record->field->name;
                $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->field->$pfield, 'View', $logs);
            }
            
            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path . '/view');
            $this->load->view('footer');
        } else {
            // no access this page
            $data['class'] = "danger";
            $data['msg'] = "Sorry, you don't have access to this page!";
            $data['urlredirect'] = "";
            $this->load->view('header', $data);
            $this->load->view('message');
            $this->load->view('footer');
        }
    }

    public function show()
    {
        // ************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;
        
        $data['activetab'] = 1; // list page tab
                                // **************************************************
                                // variable:field:default_value:operator
                                // note: dont include the special query field filter
        $condition_fields = array(
            array('variable'=>'payrollNo', 'field'=>$this->table.'.payrollNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'payrollPeriodID', 'field'=>$this->table.'.payrollPeriodID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'companyID', 'field'=>$this->table.'.companyID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'branchID', 'field'=>$this->table.'.branchID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'divisionID', 'field'=>$this->table.'.divisionID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'payrollGroupID', 'field'=>$this->table.'.payrollGroupID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('payrollNo'=>'desc');
        
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
                if ($key['variable']=='payrollID') {
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
        $this->db->select('companies.companyAbbr');
        $this->db->select('branches.branchAbbr');  
        $this->db->select('branches.branchName');
        $this->db->select('divisions.divisionAbbr');
        $this->db->select('payroll_periods.payrollPeriod');
        $this->db->select('payroll_groups.payrollGroup');

        // from
        $this->db->from($this->table);
        
        // join     
        $this->db->join('companies',$this->table.'.companyID=companies.companyID', 'left');
        $this->db->join('branches', $this->table.'.branchID=branches.branchID', 'left');      
        $this->db->join('divisions', $this->table.'.divisionID=divisions.divisionID', 'left');
        $this->db->join('payroll_periods', $this->table.'.payrollPeriodID=payroll_periods.payrollPeriodID', 'left');
        $this->db->join('payroll_groups', $this->table.'.payrollGroupID=payroll_groups.payrollGroupID', 'left');
        
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
        $config['uri_segment'] = 4;
        $this->pagination->initialize($config);

        // select
        $this->db->select($this->table.'.*');
        $this->db->select('companies.companyAbbr');
        $this->db->select('branches.branchAbbr');
        $this->db->select('branches.branchName');
        $this->db->select('divisions.divisionAbbr');
        $this->db->select('payroll_periods.payrollPeriod');
        $this->db->select('payroll_groups.payrollGroup');

        // from
        $this->db->from($this->table);
        
        // join     
        $this->db->join('companies',$this->table.'.companyID=companies.companyID', 'left');
        $this->db->join('branches', $this->table.'.branchID=branches.branchID', 'left');      
        $this->db->join('divisions', $this->table.'.divisionID=divisions.divisionID', 'left');
        $this->db->join('payroll_periods', $this->table.'.payrollPeriodID=payroll_periods.payrollPeriodID', 'left');
        $this->db->join('payroll_groups', $this->table.'.payrollGroupID=payroll_groups.payrollGroupID', 'left');
        
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
        $data['records'] = $this->db->get()->result();
        
        // load views
        $this->load->view('header', $data);
        $this->load->view($this->module_path . '/list');
        $this->load->view('footer');
    }

    public function printlist()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        // sorting
        
        // variable:field:default_value:operator
        // note: dont include the special query field filter
        $condition_fields = array(
            array('variable'=>'payrollNo', 'field'=>$this->table.'.payrollNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'payrollPeriodID', 'field'=>$this->table.'.payrollPeriodID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'companyID', 'field'=>$this->table.'.companyID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'branchID', 'field'=>$this->table.'.branchID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'divisionID', 'field'=>$this->table.'.divisionID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'payrollGroupID', 'field'=>$this->table.'.payrollGroupID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('payrollNo'=>'desc');
        
        $controller = $this->uri->segment(1);
        
        if ($this->uri->segment(3))
            $offset = $this->uri->segment(3);
        else
            $offset = 0;

        foreach($condition_fields as $key) {
            $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
        }
        
        $sortby     = $this->session->userdata($controller.'_sortby');
        $sortorder  = $this->session->userdata($controller.'_sortorder');
        $limit      = $this->session->userdata($controller.'_limit');
        
        
        // select
        $this->db->select($this->table.'.*');
        $this->db->select('companies.companyAbbr');
        $this->db->select('branches.branchAbbr');  
        $this->db->select('branches.branchName');
        $this->db->select('divisions.divisionAbbr');
        $this->db->select('payroll_periods.payrollPeriod');
        $this->db->select('payroll_groups.payrollGroup');

        // from
        $this->db->from($this->table);
        
        // join     
        $this->db->join('companies',$this->table.'.companyID=companies.companyID', 'left');
        $this->db->join('branches', $this->table.'.branchID=branches.branchID', 'left');      
        $this->db->join('divisions', $this->table.'.divisionID=divisions.divisionID', 'left');
        $this->db->join('payroll_periods', $this->table.'.payrollPeriodID=payroll_periods.payrollPeriodID', 'left');
        $this->db->join('payroll_groups', $this->table.'.payrollGroupID=payroll_groups.payrollGroupID', 'left');
        
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
        
        // get
        $data['records'] = $this->db->get()->result();
        
        $data['title'] = "List";
        
        // load views
        $this->load->view('header_print', $data);
        $this->load->view($this->module_path . '/printlist');
        $this->load->view('footer_print');
    }

    public function print_sheet($payrollID = 0)
    {
        //************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;

        $payrollID          = $this->encrypter->decode($payrollID);
        $data['title']      = "General Payroll";
        // **************************************************

        // check roles
        if ($this->roles['view']) {
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->record->table = $this->table;
            // set fields for the current table
            $this->record->setFields();
            // extend fields - join tables      
            $this->record->fields[] = 'payroll_periods.payrollPeriod';
            $this->record->fields[] = 'attendance_periods.payrollPeriod as attendancePeriod';
            $this->record->fields[] = 'payroll_periods.type';
            $this->record->fields[] = 'companies.companyName';
            $this->record->fields[] = 'branches.branchName';
            $this->record->fields[] = 'divisions.divisionName'; 
            $this->record->fields[] = 'payroll_groups.payrollGroup';

            // set joins
            $this->record->joins[]  = array('payroll_periods',$this->table.'.payrollPeriodID=payroll_periods.payrollPeriodID','left');
            $this->record->joins[]  = array('payroll_periods attendance_periods',$this->table.'.attendancePeriodID=attendance_periods.payrollPeriodID','left');
            $this->record->joins[]  = array('companies',$this->table.'.companyID=companies.companyID','left');
            $this->record->joins[]  = array('branches',$this->table.'.branchID=branches.branchID','left');
            $this->record->joins[]  = array('divisions',$this->table.'.divisionID=divisions.divisionID','left');                
            $this->record->joins[]  = array('payroll_groups',$this->table.'.payrollGroupID=payroll_groups.payrollGroupID','left');
            // set where
            $this->record->where[$this->table.'.'.$this->pfield] = $payrollID;
            
            // execute retrieve
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;
            
            $this->db->select('incentive_types.*');
            $this->db->from('payslip_incentives');
            $this->db->join('payslips','payslip_incentives.payslipID=payslips.payslipID','left');
            $this->db->join('payroll','payslips.payrollID=payroll.payrollID','left');
            $this->db->join('incentive_types','payslip_incentives.incentiveTypeID=incentive_types.incentiveTypeID','left');
            $this->db->where('payslips.payrollID', $payrollID);
            $this->db->group_by('payslip_incentives.incentiveTypeID');
            $data['incentives'] = $this->db->get();

            $this->db->select('premiums.*');
            $this->db->from('payslip_contributions');
            $this->db->join('payslips','payslip_contributions.payslipID=payslips.payslipID','left');
            $this->db->join('payroll','payslips.payrollID=payroll.payrollID','left');
            $this->db->join('premiums','payslip_contributions.premiumID=premiums.premiumID','left');
            $this->db->where('payslips.payrollID', $payrollID);
            $this->db->where('payslip_contributions.employeeShare >', 0);
            $this->db->group_by('payslip_contributions.premiumID');
            $data['contributions'] = $this->db->get();

            $this->db->select('premiums.*');
            $this->db->from('payslip_contributions');
            $this->db->join('payslips','payslip_contributions.payslipID=payslips.payslipID','left');
            $this->db->join('payroll','payslips.payrollID=payroll.payrollID','left');
            $this->db->join('premiums','payslip_contributions.premiumID=premiums.premiumID','left');
            $this->db->where('payslips.payrollID', $payrollID);
            $this->db->where('payslip_contributions.employerShare >', 0);
            $this->db->group_by('payslip_contributions.premiumID');
            $data['erShares'] = $this->db->get();

            $this->db->select('loan_types.*');
            $this->db->from('payslip_deductions');
            $this->db->join('payslips','payslip_deductions.payslipID=payslips.payslipID','left');
            $this->db->join('payroll','payslips.payrollID=payroll.payrollID','left');
            $this->db->join('loan_types','payslip_deductions.deductionID=loan_types.loanTypeID','left');
            $this->db->where('payslips.payrollID', $payrollID);
            $this->db->where('payslip_deductions.type !=', 1);
            $this->db->group_by('payslip_deductions.deductionID');
            $data['deductions'] = $this->db->get();
            
            if ($this->record->field->branchID != 0) {
                $this->db->where('companyID', $this->record->field->companyID);
                // $this->db->order_by('rank', 'asc');
                $data['groups'] = $this->db->get('branches')->result();
                
                $data['groupID']    = 'branchID';
                $data['group_label'] = 'branchName';
            } elseif ($this->record->field->divisionID != 0) {
                $this->db->where('branchID', $this->record->field->branchID);
                // $this->db->order_by('rank', 'asc');
                $data['groups'] = $this->db->get('divisions')->result();
                
                $data['groupID']    = 'divisionID';
                $data['group_label'] = 'divisionName';
            }
            
            $data['records'] = array();
            foreach ($data['groups'] as $group) {
                $this->db->select('payslips.*');
                $this->db->select('employees.fname');
                $this->db->select('employees.suffix');
                $this->db->select('employments.lname');
                $this->db->select('employments.mname');
                $this->db->select('employments.employmentNo');                      
                $this->db->select('employee_types.employeeType');
                $this->db->from('payslips');
                $this->db->join('employees','payslips.empID=employees.empID','left');
                $this->db->join('employments','payslips.employmentID=employments.employmentID','left');
                $this->db->join('employee_types','payslips.employeeTypeID=employee_types.employeeTypeID','left');
                $this->db->where('payslips.payrollID', $payrollID);
                $this->db->where('employments.'.$data['groupID'], $group->$data['groupID']);
                $payslips = $this->db->get();
                
                if ($payslips->num_rows()) {
                    foreach ($payslips->result() as $payslip) {
                        $info = array();
                        $info['employmentNo'] = $payslip->employmentNo;
                        $info['employee']     = strtoupper($payslip->lname.', '.$payslip->fname.' '.$payslip->mname.' '.$payslip->suffix);
                        $info['basicRate']    = $payslip->basicRate;
                        
                        // incentives
                        if ($data['incentives']->num_rows()) {
                            foreach ($data['incentives']->result() as $incentive) {
                                $this->db->where('payslipID', $payslip->payslipID);
                                $this->db->where('incentiveTypeID', $incentive->incentiveTypeID);
                                $amount = $this->db->get('payslip_incentives')->row()->amount;
                                
                                $info[$incentive->abbr] =  (!empty($amount)) ? $amount : 0;
                            }
                        }
                        
                        $info['totalGross']    = $payslip->totalGross;   // gross (basic rate + incentives)
                        $info['wop']           = $payslip->wop;          // without pay
                        
                        // withholding tax
                        $this->db->where('payslipID', $payslip->payslipID);
                        $this->db->where('type', 1);
                        $amount = $this->db->get('payslip_deductions')->row()->amount;
                        
                        $info['wtax'] =  (!empty($amount)) ? $amount : 0;
                        
                        // contributions
                        if ($data['contributions']->num_rows()) {
                            foreach ($data['contributions']->result() as $contribution) {
                                $this->db->where('payslipID', $payslip->payslipID);
                                $this->db->where('premiumID', $contribution->premiumID);
                                $employeeShare = $this->db->get('payslip_contributions')->row()->employeeShare;

                                $info[$contribution->abbr] =  (!empty($employeeShare)) ? $employeeShare : 0;
                            }
                        }
                        
                        // contributions
                        if ($data['deductions']->num_rows()) {
                            foreach ($data['deductions']->result() as $deduction) {
                                $this->db->where('payslipID', $payslip->payslipID);
                                $this->db->where('deductionID', $deduction->loanTypeID);
                                $amount = $this->db->get('payslip_deductions')->row()->amount;

                                $info[$deduction->abbr] =  (!empty($amount)) ? $amount : 0;
                            }
                        }
                        
                        $info['netPay']    = $payslip->netPay; // net pay
                        
                        // employer shares
                        if ($data['erShares']->num_rows()) {
                            foreach ($data['erShares']->result() as $erShares) {
                                $this->db->where('payslipID', $payslip->payslipID);
                                $this->db->where('premiumID', $erShares->premiumID);
                                $employerShare = $this->db->get('payslip_contributions')->row()->employerShare;

                                $info['er_'.$erShares->abbr] =  (!empty($employerShare)) ? $employerShare : 0;
                            }
                        }
                        
                        $data['records'][$group->$data['groupID']][] = $info;
                    }
                }
            }
            
            $this->db->where('companyID', $data['companyID']);
            $company = $this->db->get('companies', 1)->row();

            $data['pdf_paging'] = TRUE;
            $data['title']      = "GENERAL PAYROLL";
            $data['modulename'] = "GENERAL PAYROLL";
            $data['subnote']    = $this->record->field->branchName;
            if (!empty($division)) {
                $data['subnote2']   = $this->record->field->divisionName;
                $data['subnote3']   = $this->record->field->payrollPeriod;
            } else {
                $data['subnote2']   = $this->record->field->payrollPeriod;
            }

            // load pdf class
            $this->load->library('mpdf');
            // load pdf class
            $this->mpdf->mpdf('en-GB',array(215.9,330.2),10,'Garamond',10,10,25,10,0,0,'L');
            $this->mpdf->setTitle($data['title']);
            $this->mpdf->SetDisplayMode('fullpage');
            $this->mpdf->shrink_tables_to_fit = 1;
            $this->mpdf->SetWatermarkImage(base_url().'images/logo/watermark.png');
            $this->mpdf->watermark_font = 'DejaVuSansCondensed';
            $this->mpdf->watermarkImageAlpha = 0.1;
            $this->mpdf->watermarkImgBehind = TRUE;
            $this->mpdf->showWatermarkImage = TRUE;

            // content
            $header = $this->load->view('print_pdf_header', $data, TRUE);
            $this->mpdf->SetHTMLHeader($header);

            $footer = $this->load->view('print_pdf_footer', $data, TRUE);
            $this->mpdf->SetHTMLFooter($footer);

            $html   = $this->load->view($this->module_path.'/print_sheet', $data, TRUE);
            $this->mpdf->WriteHTML($html);

            $this->mpdf->Output("GENERAL_PAYROLL.pdf","I");
//          $this->load->view($this->module_path.'/print_sheet', $data);
        } else {
            $data["display"] = "block";
            $data["class"]   = "errorbox";
            $data["msg"]     = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("print_header",$data);
            $this->load->view("message",$data);
            $this->load->view("print_footer");
        }
    }


    public function print_notice_to_credit($payrollID = 0)
    {
        //************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;

        $payrollID          = $this->encrypter->decode($payrollID);
        $data['title']      = "General Payroll";
        // **************************************************

        // check roles
        if ($this->roles['view']) {
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->record->table = $this->table;
            // set fields for the current table
            $this->record->setFields();
            // extend fields - join tables
            $this->record->fields[] = 'payroll_periods.payrollPeriod';
            $this->record->fields[] = 'attendance_periods.payrollPeriod as attendancePeriod';
            $this->record->fields[] = 'payroll_periods.type';
            $this->record->fields[] = 'companies.companyName';
            $this->record->fields[] = 'branches.branchName';
            $this->record->fields[] = 'divisions.divisionName';
            $this->record->fields[] = 'payroll_groups.payrollGroup';
            
            // set joins
            $this->record->joins[]  = array('payroll_periods',$this->table.'.payrollPeriodID=payroll_periods.payrollPeriodID','left');
            $this->record->joins[]  = array('payroll_periods attendance_periods',$this->table.'.attendancePeriodID=attendance_periods.payrollPeriodID','left');
            $this->record->joins[]  = array('companies',$this->table.'.companyID=companies.companyID','left');
            $this->record->joins[]  = array('branches',$this->table.'.branchID=branches.branchID','left');
            $this->record->joins[]  = array('divisions',$this->table.'.divisionID=divisions.divisionID','left');
            $this->record->joins[]  = array('payroll_groups',$this->table.'.payrollGroupID=payroll_groups.payrollGroupID','left');
            // set where
            $this->record->where[$this->table.'.'.$this->pfield] = $payrollID;

            // execute retrieve
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;
            
            $this->db->select('payslips.*');
            $this->db->select('employments.employmentNo');
            $this->db->select('employees.fname');
            $this->db->select('employees.suffix');
            $this->db->select('employments.lname');
            $this->db->select('employments.mname');
            $this->db->select('employments.accountNo');
            $this->db->select('job_positions.positionCode');
            $this->db->select('job_titles.jobTitle');
            $this->db->from('payslips');
            $this->db->join('payroll','payslips.payrollID=payroll.payrollID', 'left');
            $this->db->join('employments','payslips.employmentID=employments.employmentID','left');
            $this->db->join('employees','employments.empID=employees.empID','left');
            $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
            $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
            $this->db->where('payslips.payrollID', $payrollID);
            //$this->db->where('payslips.status', 2);
            
            $data['records'] = $this->db->get();
            
            // analyze attendance
            $this->db->where('payrollPeriodID', $this->record->field->payrollPeriodID);
            $payroll = $this->db->get('payroll_periods', 1)->row();
            
            $start      = strtotime($payroll->startDate);
            $end        = strtotime($payroll->endDate);
            $data['log']= array();

            if ($data['records']->num_rows()) {
                foreach ($data['records']->result() as $row) {
                    $res['employmentNo'] = $row->employmentNo;
                    $res['employee']    = $row->lname.', '.$row->fname.', '.$row->mname.' '.$row->suffix;
                    $res['accountNo']   = $row->accountNo;
                    $res['netPay']      = $row->netPay;
                    
                    $data['log'][$row->employmentID] = $res;
                }
            }   

            $this->db->where('companyID', $this->record->field->companyID);
            $company = $this->db->get('companies', 1)->row();

            $this->db->where('branchID', $this->record->field->branchID);
            $branch = $this->db->get('branches', 1)->row();

            $this->db->where('divisionID', $this->record->field->divisionID);
            $division = $this->db->get('divisions', 1)->row();

            $this->db->where('payrollPeriodID', $this->record->field->payrollPeriodID);
            $payroll = $this->db->get('payroll_periods', 1)->row();

            $data['pdf_paging'] = TRUE;
            $data['title']      = "NOTICE TO CREDIT";
            $data['modulename'] = "NOTICE TO CREDIT";
            $data['subnote']    = $branch->branchName;
            if (!empty($division)) {
                $data['subnote2']   = $division->divisionName;
                $data['subnote3']   = $payroll->payrollPeriod;
            } else {
                $data['subnote2']   = $payroll->payrollPeriod;
            }

            // load pdf class
            $this->load->library('mpdf');
            // load pdf class
            $this->mpdf->mpdf('en-GB',array(215.9,330.2),10,'Garamond',10,10,25,10,0,0,'P');
            $this->mpdf->setTitle($data['title']);
            $this->mpdf->SetDisplayMode('fullpage');
            $this->mpdf->shrink_tables_to_fit = 1;
            $this->mpdf->SetWatermarkImage(base_url().'images/logo/watermark.png');
            $this->mpdf->watermark_font = 'DejaVuSansCondensed';
            $this->mpdf->watermarkImageAlpha = 0.1;
            $this->mpdf->watermarkImgBehind = TRUE;
            $this->mpdf->showWatermarkImage = TRUE;

            // content
            $header = $this->load->view('print_pdf_header', $data, TRUE);
            $this->mpdf->SetHTMLHeader($header);

            $footer = $this->load->view('print_pdf_footer', $data, TRUE);
            $this->mpdf->SetHTMLFooter($footer);

            $html   = $this->load->view('modules/Report/Payroll/print_notice_to_credit', $data, TRUE);
            $this->mpdf->WriteHTML($html);

            $this->mpdf->Output("NOTICE_TO_CREDIT.pdf","I");
        } else {
            $data["display"] = "block";
            $data["class"]   = "errorbox";
            $data["msg"]     = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("print_header",$data);
            $this->load->view("message",$data);
            $this->load->view("print_footer");
        }
    }

    public function print_payslip($payrollID = 0)
    {
        //************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;

        $payrollID          = $this->encrypter->decode($payrollID);
        $data['title']      = "Payslip";
        // **************************************************

        // check roles
        if ($this->roles['view']) {
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->record->table = $this->table;
            // set fields for the current table
            $this->record->setFields();
            // extend fields - join tables      
            $this->record->fields[] = 'payroll_periods.payrollPeriod';
            $this->record->fields[] = 'attendance_periods.payrollPeriod as attendancePeriod';
            $this->record->fields[] = 'payroll_periods.type';
            $this->record->fields[] = 'companies.companyName';
            $this->record->fields[] = 'branches.branchName';
            $this->record->fields[] = 'branches.branchAbbr';
            $this->record->fields[] = 'divisions.divisionName'; 
            $this->record->fields[] = 'payroll_groups.payrollGroup';

            // set joins
            $this->record->joins[]  = array('payroll_periods',$this->table.'.payrollPeriodID=payroll_periods.payrollPeriodID','left');
            $this->record->joins[]  = array('payroll_periods attendance_periods',$this->table.'.attendancePeriodID=attendance_periods.payrollPeriodID','left');
            $this->record->joins[]  = array('companies',$this->table.'.companyID=companies.companyID','left');
            $this->record->joins[]  = array('branches',$this->table.'.branchID=branches.branchID','left');
            $this->record->joins[]  = array('divisions',$this->table.'.divisionID=divisions.divisionID','left');                
            $this->record->joins[]  = array('payroll_groups',$this->table.'.payrollGroupID=payroll_groups.payrollGroupID','left');
            // set where
            $this->record->where[$this->table.'.'.$this->pfield] = $payrollID;

            // execute retrieve
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;

            $this->db->select('payslips.*');
            $this->db->select('employees.fname');
            $this->db->select('employees.suffix');
            $this->db->select('employments.lname');
            $this->db->select('employments.mname');
            $this->db->select('employments.employmentNo');                      
            $this->db->select('employee_types.employeeType');
            $this->db->select('job_titles.jobTitle');
            $this->db->from('payslips');
            $this->db->join('employees','payslips.empID=employees.empID','left');
            $this->db->join('employments','payslips.employmentID=employments.employmentID','left');
            $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID','left');
            $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID','left');
            $this->db->join('employee_types','payslips.employeeTypeID=employee_types.employeeTypeID','left');
            $this->db->where('payslips.payrollID', $payrollID);
            $payslips = $this->db->get();
            
            $this->db->select('incentive_types.*');
            $this->db->from('payslip_incentives');
            $this->db->join('payslips','payslip_incentives.payslipID=payslips.payslipID','left');
            $this->db->join('payroll','payslips.payrollID=payroll.payrollID','left');
            $this->db->join('incentive_types','payslip_incentives.incentiveTypeID=incentive_types.incentiveTypeID','left');
            $this->db->where('payslips.payrollID', $payrollID);
            $this->db->group_by('payslip_incentives.incentiveTypeID');
            $data['incentives'] = $this->db->get();
            
            $this->db->select('premiums.*');
            $this->db->from('payslip_contributions');
            $this->db->join('payslips','payslip_contributions.payslipID=payslips.payslipID','left');
            $this->db->join('payroll','payslips.payrollID=payroll.payrollID','left');
            $this->db->join('premiums','payslip_contributions.premiumID=premiums.premiumID','left');
            $this->db->where('payslips.payrollID', $payrollID);
            $this->db->where('payslip_contributions.employeeShare >', 0);
            $this->db->group_by('payslip_contributions.premiumID');
            $data['contributions'] = $this->db->get();

            $this->db->select('premiums.*');
            $this->db->from('payslip_contributions');
            $this->db->join('payslips','payslip_contributions.payslipID=payslips.payslipID','left');
            $this->db->join('payroll','payslips.payrollID=payroll.payrollID','left');
            $this->db->join('premiums','payslip_contributions.premiumID=premiums.premiumID','left');
            $this->db->where('payslips.payrollID', $payrollID);
            $this->db->where('payslip_contributions.employerShare >', 0);
            $this->db->group_by('payslip_contributions.premiumID');
            $data['erShares'] = $this->db->get();

            $this->db->select('loan_types.*');
            $this->db->from('payslip_deductions');
            $this->db->join('payslips','payslip_deductions.payslipID=payslips.payslipID','left');
            $this->db->join('payroll','payslips.payrollID=payroll.payrollID','left');
            $this->db->join('loan_types','payslip_deductions.deductionID=loan_types.loanTypeID','left');
            $this->db->where('payslips.payrollID', $payrollID);
            $this->db->where('payslip_deductions.type !=', 1);
            $this->db->group_by('payslip_deductions.deductionID');
            $data['deductions'] = $this->db->get();

            $this->db->where('companyID', $data['companyID']);
            $company = $this->db->get('companies', 1)->row();

            $data['pdf_paging'] = FALSE;
            $data['title']      = "PAYSLIP";
            $data['modulename'] = "PAYSLIP";
            $data['subnote']    = $this->record->field->branchName;
            if (!empty($division)) {
                $data['subnote2']   = $this->record->field->divisionName;
                $data['subnote3']   = $this->record->field->payrollPeriod;
            } else {
                $data['subnote2']   = $this->record->field->payrollPeriod;
            }

            // load pdf class
            $this->load->library('mpdf');
            // load pdf class
            $this->mpdf->mpdf('en-GB',array(210,140),10,'Garamond',5,5,5,5,0,0,'P');
            $this->mpdf->setTitle($data['title']);
            $this->mpdf->SetDisplayMode('fullpage');
            $this->mpdf->shrink_tables_to_fit = 1;
            $this->mpdf->SetWatermarkImage(base_url().'assets/img/main/logo-shadow.png');
            $this->mpdf->watermark_font = 'DejaVuSansCondensed';
            $this->mpdf->watermarkImageAlpha = 0.1;
            $this->mpdf->watermarkImgBehind = TRUE;
            $this->mpdf->showWatermarkImage = TRUE;

            // content
//          $header = $this->load->view('print_pdf_header', $data, TRUE);
//          $this->mpdf->SetHTMLHeader($header);

//          $footer = $this->load->view('print_pdf_footer', $data, TRUE);
//          $this->mpdf->SetHTMLFooter($footer);
            // var_dump($payslips->result());
            if ($payslips->num_rows()) {
                foreach ($payslips->result() as $row) {
                    $data['payslip'] = $row;
                    
                    $html   = $this->load->view($this->module_path.'/print_payslip', $data, TRUE);
                    $this->mpdf->WriteHTML($html);
                }
            }

            $this->mpdf->Output("PAYSLIP.pdf","I");
        } else {
            $data["display"] = "block";
            $data["class"]   = "errorbox";
            $data["msg"]     = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("print_header",$data);
            $this->load->view("message",$data);
            $this->load->view("print_footer");
        }
    }
    

    public function exportlist()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        // sorting
        
        // variable:field:default_value:operator
        // note: dont include the special query field filter
        $condition_fields = array(
            array('variable'=>'payrollNo', 'field'=>$this->table.'.payrollNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'payrollPeriodID', 'field'=>$this->table.'.payrollPeriodID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'companyID', 'field'=>$this->table.'.companyID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'branchID', 'field'=>$this->table.'.branchID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'divisionID', 'field'=>$this->table.'.divisionID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'payrollGroupID', 'field'=>$this->table.'.payrollGroupID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('payrollNo'=>'desc');
        
        $controller = $this->uri->segment(1);
        
        if ($this->uri->segment(3))
            $offset = $this->uri->segment(3);
        else
            $offset = 0;

        foreach($condition_fields as $key) {
            $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
        }
        
        $sortby     = $this->session->userdata($controller.'_sortby');
        $sortorder  = $this->session->userdata($controller.'_sortorder');
        $limit      = $this->session->userdata($controller.'_limit');
        
        
        // select
        $this->db->select($this->table.'.*');
        $this->db->select('companies.companyAbbr');
        $this->db->select('branches.branchAbbr');  
        $this->db->select('branches.branchName');
        $this->db->select('divisions.divisionAbbr');
        $this->db->select('payroll_periods.payrollPeriod');
        $this->db->select('payroll_groups.payrollGroup');

        // from
        $this->db->from($this->table);
        
        // join     
        $this->db->join('companies',$this->table.'.companyID=companies.companyID', 'left');
        $this->db->join('branches', $this->table.'.branchID=branches.branchID', 'left');      
        $this->db->join('divisions', $this->table.'.divisionID=divisions.divisionID', 'left');
        $this->db->join('payroll_periods', $this->table.'.payrollPeriodID=payroll_periods.payrollPeriodID', 'left');
        $this->db->join('payroll_groups', $this->table.'.payrollGroupID=payroll_groups.payrollGroupID', 'left');
        
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
        
        // get
        $records = $this->db->get()->result();
        
        $title = "List";
        $companyName = $this->config_model->getConfig('Company');
        $address = $this->config_model->getConfig('Address');
        
        // XML Blurb
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

        <Worksheet ss:Name='" . $title . "'>

        <Table>
        <Column ss:Index='1' ss:AutoFitWidth=\"1\" ss:Width='25.00'/>
        <Column ss:Index='2' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
        <Column ss:Index='3' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
        <Column ss:Index='4' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
        <Column ss:Index='5' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
        <Column ss:Index='6' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
        <Column ss:Index='7' ss:AutoFitWidth=\"1\" ss:Width='150.00'/>
        <Column ss:Index='8' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
        ";
        
        // header
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='7'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s20'>";
        $data .= "<Cell ss:MergeAcross='7'><Data ss:Type='String'>" . $companyName . "</Data></Cell>";
        $data .= "</Row>";
        $data .= "<Row ss:StyleID='s24A'>";
        $data .= "<Cell ss:MergeAcross='7'><Data ss:Type='String'>" . $address . "</Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='7'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='7'><Data ss:Type='String'>" . strtoupper($title) . "</Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='7'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
        
        $fields[] = "  ";
        $fields[] = "PAYROLL NO.";
        $fields[] = "PAYROLL PERIOD";
        $fields[] = "COMPANY";
        $fields[] = "BRANCH";
        $fields[] = "SECTION";
        $fields[] = "PAYROLL GROUP";
        $fields[] = "STATUS";
        
        $data .= "<Row ss:StyleID='s24'>";
        // Field Name Data
        foreach ($fields as $fld) {
            $data .= "<Cell ss:StyleID='s23'><Data ss:Type='String'>$fld</Data></Cell>";
        }
        $data .= "</Row>";
        
        if (count($records)) {
            $ctr = 1;
            foreach ($records as $row) {
                $data .= "<Row>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $ctr . ".</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->payrollNo . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->payrollPeriod . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->companyAbbr . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->branchName . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->divisionAbbr . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->payrollGroup . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->status . "</Data></Cell>";
                $data .= "</Row>";
                
                $ctr ++;
            }
        }
        $data .= "</Table></Worksheet>";
        $data .= "</Workbook>";
        
        // Final XML Blurb
        $filename = "group_list";
        
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$filename.xls;");
        header("Content-Type: application/ms-excel");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        echo $data;
    }

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

    private function _generateID($series)
    {
        $idSeries   = $this->config_model->getConfig('General Payroll Series');
        $idLength   = $this->config_model->getConfig('General Payroll Series Length');

        $id  = str_pad($idSeries, $idLength, "0", STR_PAD_LEFT);
        return 'GNP'.$series.$id;
    }
    
    // AJAX HANDLER FUNCTIONS
    public function check_duplicate()
    {
        // set table
        $this->record->table = $this->table;
        // set where        
        $this->record->where['payrollPeriodID'] = $this->encrypter->decode($this->input->post('payrollPeriodID'));
        $this->record->where['companyID']       = $this->input->post('companyID');
        $this->record->where['branchID']        = $this->input->post('branchID');
        $this->record->where['deptID']          = $this->input->post('deptID');
        $this->record->where['divisionID']      = $this->input->post('divisionID');
        $this->record->where['employeeTypeID']  = str_replace('_',',',$this->input->post('employeeTypeID'));
        $this->record->where['payrollGroupID']  = $this->encrypter->decode($this->input->post('payrollGroupID'));
        // execute retrieve
        $this->record->retrieve();
        
        if (!empty($this->record->field))
            echo "1"; // duplicate
        else 
            echo "0";
    }   
    
    public function setEmployeesEncrypt($companyID=0, $branchID=0, $deptID=0, $divisionID=0, $payrollGroupID=0, $payrollPeriodID=0, $attendancePeriodID=0, $employeeTypeID=0)
    {
        $data['companyID']          = $this->encrypter->decode($companyID);
        $data['branchID']           = $this->encrypter->decode($branchID);
        $data['deptID']             = $this->encrypter->decode($deptID);
        $data['divisionID']         = $this->encrypter->decode($divisionID);
        $data['payrollGroupID']     = $this->encrypter->decode($payrollGroupID);
        $data['payrollPeriodID']    = $this->encrypter->decode($payrollPeriodID);
        $data['attendancePeriodID'] = $this->encrypter->decode($attendancePeriodID);
        $data['employeeTypeID']     = explode('_', $employeeTypeID);
        
        // get payroll period
        $this->db->where('payrollPeriodID', $data['payrollPeriodID']);
        $payrollPeriod = $this->db->get('payroll_periods', 1)->row();
        
        // attendance
        $this->db->where('payrollPeriodID', $data['attendancePeriodID']);
        $attendancePeriod = $this->db->get('payroll_periods', 1)->row();
        
        $start    = strtotime($attendancePeriod->startDate);
        $end      = strtotime($attendancePeriod->endDate);
        
        if ($payrollPeriod->type == 'SM') {
            $firstHalf = (date('d',strtotime($payrollPeriod->startDate) == '01')) ? true : false;
        }

        // select
        $this->db->select('employments.*');
        $this->db->select('employees.empNo');
        $this->db->select('employees.fname');
        $this->db->select('employees.suffix');
        $this->db->select('employees.taxID');
        $this->db->select('job_positions.positionCode');
        $this->db->select('job_titles.jobTitle');
        $this->db->select('employee_types.workingDays');
        $this->db->select('employee_types.employeeType');
        $this->db->select('employee_types.withPay');
        $this->db->select('tax_withholding.amount as wtax');
        $this->db->select('tax_withholding.isAutomatic');
        $this->db->select('tax_exemptions.personalExemption');
        $this->db->select('tax_exemptions.additionalExemption');        
        $this->db->from('employments');
        $this->db->join('employees', 'employments.empID=employees.empID', 'left');      
        $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID', 'left');
        $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
        $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
        $this->db->join('tax_withholding','employments.employmentID=tax_withholding.employmentID','left');
        $this->db->join('tax_exemptions','employees.taxID=tax_exemptions.taxID','left');
        if ($data['companyID']) {
            $this->db->where('employments.companyID', $data['companyID']);
        }
        if ($data['branchID']) {
            $this->db->where('employments.branchID', $data['branchID']);
        }
        if ($data['deptID']) {
            $this->db->where('employments.branchID', $data['branchID']);
        }
        if ($data['divisionID']) {
            $this->db->where('employments.divisionID', $data['divisionID']);
        }
        $this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
        $this->db->where('employments.payrollGroupID', $data['payrollGroupID']);
        $this->db->where('employments.dateAppointed <=', $payrollPeriod->endDate);
        $this->db->where('employments.status', 1);
        $this->db->order_by('employments.lname','asc');
        $this->db->order_by('employees.fname','asc');
        $this->db->order_by('employments.mname','asc');
        $records = $this->db->get();
        
        $data['records'] = array();
        if ($records->num_rows()) {
            foreach ($records->result() as $row) {
                $info = array();
                $info['employmentID'] = $row->employmentID;
                $info['employmentNo'] = $row->employmentNo;
                $info['name']         = $row->lname.', '.$row->fname.' '.$row->mname.' '.$row->suffix;
                $info['employeeType'] = $row->employeeType;
                $info['jobTitle']     = $row->jobTitle;                         
                $info['wop']= 0;
                
                $tardyMin       = 0;
                $tardyCount     = 0;
                $utCount        = 0;
                $utMin          = 0;
                $absentCount    = 0;
                
                // analyze attendance
                for ($current = $start; $current <= $end; $current = strtotime('+1 day', $current)) {
                    $dtr = $this->dtrlog->analyze($row->employmentID, $current);

                    if (!empty ($dtr)) {
                        foreach ($dtr as $log) {
                            // tardiness
                            if (strlen($log['tardy_date']) > 5) {
                                $tardyCount  += 2;
                            } elseif (intval($log['tardy']) > 0) {
                                $tardyCount  += 1;
                            }
                            $tardyMin += intval($log['tardy']);

                            // undertime
                            if (strlen($log['ut_date']) > 5) {
                                $utCount  += 2;
                            } elseif (intval($log['ut_date']) > 0) {
                                $utCount  += 1;
                            }
                            $utMin += intval($log['undertime']);

                            // absences
                            $absentCount += ($log['remarks'] == 'ABSENT') ? 1 : 0;

                            // leaves, orders, suspension
//                          $res[$info['los']]   += ($info['los']) ? $info['los_day'] : 0;

                        }
                    }
                }
                $ut = $tardyMin + $utMin;
                // number of days without pay
                $info['daysRequired']   = $row->workingDays;
                $info['daysUndertime']  = $absentCount;
                $info['daysUndertime'] += $ut / 480;
                $info['daysRendered']   = $info['daysRequired'] - $info['daysUndertime'];
                
                $row->workingDays       = ($row->workingDays) ? $row->workingDays : date('t', strtotime($payrollPeriod->startDate));
                
                // calculate withoutpay
                if ($info['daysUndertime'] > 0) {
                    // get daily rate
                    $dailyRate      = $row->basicSalary / $row->workingDays;
                    $info['wop']    = round($dailyRate * $info['daysUndertime'], 2);
                }
                
                $monthlySalary = round($row->basicSalary - $info['wop'], 2); 
                if ($payrollPeriod->type == 'MO') { 
                    $info['basicSalary'] = round($row->basicSalary, 2);
                    $salary = round($info['basicSalary'] - $info['wop'], 2);
                } else {
                    $info['basicSalary'] = ($firstHalf) ? $this->fnumber_format($row->basicSalary / 2, '.') : round($row->basicSalary / 2, 2, PHP_ROUND_HALF_UP);
                    $salary = ($firstHalf) ? $this->fnumber_format(($info['basicSalary'] - $info['wop']) / 2, '.') : round(($info['basicSalary'] - $info['wop']) / 2, 2, PHP_ROUND_HALF_UP);
                }           
                
                // calculate incentives
                $result = $this->payrollcalc->incentive($row->employmentID, $payrollPeriod);                
                $info['totalIncentive'] = $result['totalIncentive'];                                                    
                
                // calculate gross pay
                $info['grossPay'] = $info['basicSalary'] + $info['totalIncentive'];             
                
                // calculate wtax
                if ($row->isAutomatic) {
                    $result = $this->payrollcalc->wtax($row->employmentID, $row->taxID, $row->basicSalary, $payrollPeriod);
                    $wtax = $result['amount'];
                } else {
                    $wtax = $row->wtax;
                }

                if ($payrollPeriod->type == 'SM') {
                    $wtax  = ($firstHalf) ? $this->fnumber_format($wtax / 2, '.') : round($wtax / 2, 2, PHP_ROUND_HALF_UP);
                } else {
                    $wtax = round($wtax, 2, PHP_ROUND_HALF_UP);
                }

                $info['wtax'] = $wtax;

                // calculate contributions
                $result = $this->payrollcalc->contribution($row->employmentID, $row->basicSalary, $payrollPeriod);
                $info['deductions'] = $result['totalEmployeeShare'];
                $info['erShare']    = $result['totalEmployerShare'];

                // calculate loans
                $result = $this->payrollcalc->loan($row->employmentID, $row->basicSalary, $payrollPeriod);
                $info['deductions'] += $result['totalDeduction'];
                // ----------------------------------------------------------------------
                
                $info['netPay']     = $info['grossPay'] - $info['wop'] - $info['wtax'] - $info['deductions'];           
                $data['records'][]  = $info;
            }
        }

        // var_dump($data);
        // load views
        echo $this->load->view($this->module_path.'/set_employees', $data, true);
    }



    public function setEmployees($companyID=0, $branchID=0, $deptID=0, $divisionID=0, $payrollGroupID=0, $payrollPeriodID=0, $attendancePeriodID=0, $employeeTypeID=0)
    {
        $data['companyID']          = $companyID;
        $data['branchID']           = $branchID;
        $data['deptID']             = $deptID;
        $data['divisionID']         = $divisionID;
        $data['payrollGroupID']     = $payrollGroupID;
        $data['payrollPeriodID']    = $this->encrypter->decode($payrollPeriodID);
        $data['attendancePeriodID'] = $this->encrypter->decode($attendancePeriodID);
        $data['employeeTypeID']     = explode('_', $employeeTypeID);
        
        // get payroll period
        $this->db->where('payrollPeriodID', $data['payrollPeriodID']);
        $payrollPeriod = $this->db->get('payroll_periods', 1)->row();
        
        // var_dump($payrollPeriod);
        // attendance
        $this->db->where('payrollPeriodID', $data['attendancePeriodID']);
        $attendancePeriod = $this->db->get('payroll_periods', 1)->row();


        $start    = strtotime($attendancePeriod->startDate);
        $end      = strtotime($attendancePeriod->endDate);
        
        if ($payrollPeriod->type == 'SM') {
            $firstHalf = (date('d',strtotime($payrollPeriod->startDate) == '01')) ? true : false;
        }

        // select
        $this->db->select('employments.*');
        $this->db->select('employees.empNo');
        $this->db->select('employees.fname');
        $this->db->select('employees.suffix');
        $this->db->select('employees.taxID');
        $this->db->select('job_positions.positionCode');
        $this->db->select('job_titles.jobTitle');
        $this->db->select('employee_types.workingDays');
        $this->db->select('employee_types.employeeType');
        $this->db->select('employee_types.withPay');
        $this->db->select('tax_withholding.amount as wtax');
        $this->db->select('tax_withholding.isAutomatic');
        $this->db->select('tax_exemptions.personalExemption');
        $this->db->select('tax_exemptions.additionalExemption');        
        $this->db->from('employments');
        $this->db->join('employees', 'employments.empID=employees.empID', 'left');      
        $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID', 'left');
        $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
        $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
        $this->db->join('tax_withholding','employments.employmentID=tax_withholding.employmentID','left');
        $this->db->join('tax_exemptions','employees.taxID=tax_exemptions.taxID','left');
        if ($data['companyID']) {
            $this->db->where('employments.companyID', $data['companyID']);
        }
        if ($data['branchID']) {
            $this->db->where('employments.branchID', $data['branchID']);
        }
        if ($data['deptID']) {
            $this->db->where('employments.branchID', $data['branchID']);
        }
        if ($data['divisionID']) {
            $this->db->where('employments.divisionID', $data['divisionID']);
        }
        $this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
        $this->db->where('employments.payrollGroupID', $data['payrollGroupID']);
        $this->db->where('employments.dateAppointed <=', $payrollPeriod->endDate);
        $this->db->where('employments.status', 1);
        $this->db->order_by('employments.lname','asc');
        $this->db->order_by('employees.fname','asc');
        $this->db->order_by('employments.mname','asc');
        $records = $this->db->get();
        // var_dump($records);
        
        $data['records'] = array();
        if ($records->num_rows()) {
            foreach ($records->result() as $row) {

                $info = array();
                $info['employmentID'] = $row->employmentID;
                $info['employmentNo'] = $row->employmentNo;
                $info['name']         = $row->lname.', '.$row->fname.' '.$row->mname.' '.$row->suffix;
                $info['employeeType'] = $row->employeeType;
                $info['jobTitle']     = $row->jobTitle;                         
                $info['wop']= 0;
                
                $tardyMin       = 0;
                $tardyCount     = 0;
                $utCount        = 0;
                $utMin          = 0;
                $absentCount    = 0;

                // analyze attendance
                for ($current = $start; $current <= $end; $current = strtotime('+1 day', $current)) {

                    $dtr = $this->dtrlog->analyze($row->employmentID, $current);

                    if (!empty ($dtr)) {
                        foreach ($dtr as $log) {
                            // tardiness
                            if (strlen($log['tardy_date']) > 5) {
                                $tardyCount  += 2;
                            } elseif (intval($log['tardy']) > 0) {
                                $tardyCount  += 1;
                            }
                            $tardyMin += intval($log['tardy']);

                            // undertime
                            if (strlen($log['ut_date']) > 5) {
                                $utCount  += 2;
                            } elseif (intval($log['ut_date']) > 0) {
                                $utCount  += 1;
                            }
                            $utMin += intval($log['undertime']);

                            // absences
                            $absentCount += ($log['remarks'] == 'ABSENT') ? 1 : 0;

                            // leaves, orders, suspension
//                          $res[$info['los']]   += ($info['los']) ? $info['los_day'] : 0;

                        }
                    }
                }

                $ut = $tardyMin + $utMin;
                // number of days without pay
                $info['daysRequired']   = $row->workingDays;
                $info['daysUndertime']  = $absentCount;
                $info['daysUndertime'] += $ut / 480;
                $info['daysRendered']   = $info['daysRequired'] - $info['daysUndertime'];
                
                $row->workingDays       = ($row->workingDays) ? $row->workingDays : date('t', strtotime($payrollPeriod->startDate));
                
                // calculate withoutpay
                if ($info['daysUndertime'] > 0) {
                    // get daily rate
                    $dailyRate      = $row->basicSalary / $row->workingDays;
                    $info['wop']    = round($dailyRate * $info['daysUndertime'], 2);
                }
                
                $monthlySalary = round($row->basicSalary - $info['wop'], 2); 
                // echo $monthlySalary;
                if ($payrollPeriod->type == 'MO') {
                
                    $info['basicSalary'] = round($row->basicSalary, 2);
                    $salary = round($info['basicSalary'] - $info['wop'], 2);
                } else {

                    $info['basicSalary'] = ($firstHalf) ? $this->fnumber_format($row->basicSalary / 2, '.') : round($row->basicSalary / 2, 2, PHP_ROUND_HALF_UP);
                    $salary = ($firstHalf) ? $this->fnumber_format(($info['basicSalary'] - $info['wop']) / 2, '.') : round(($info['basicSalary'] - $info['wop']) / 2, 2, PHP_ROUND_HALF_UP);
                }           
                

                // calculate incentives
                $result = $this->payrollcalc->incentive($row->employmentID, $payrollPeriod); 

                $info['totalIncentive'] = $result['totalIncentive'];                                                    
                
                // calculate gross pay
                $info['grossPay'] = $info['basicSalary'] + $info['totalIncentive'];             
                
                // calculate wtax
                if ($row->isAutomatic) {
                    $result = $this->payrollcalc->wtax($row->employmentID, $row->taxID, $row->basicSalary, $payrollPeriod);
                    $wtax = $result['amount'];
                } else {
                    $wtax = $row->wtax;
                }

                if ($payrollPeriod->type == 'SM') {
                    $wtax  = ($firstHalf) ? $this->fnumber_format($wtax / 2, '.') : round($wtax / 2, 2, PHP_ROUND_HALF_UP);
                } else {
                    $wtax = round($wtax, 2, PHP_ROUND_HALF_UP);
                }

                $info['wtax'] = $wtax;

                // calculate contributions
                $result = $this->payrollcalc->contribution($row->employmentID, $row->basicSalary, $payrollPeriod);
                $info['deductions'] = $result['totalEmployeeShare'];
                $info['erShare']    = $result['totalEmployerShare'];

                // calculate loans
                $result = $this->payrollcalc->loan($row->employmentID, $row->basicSalary, $payrollPeriod);
                $info['deductions'] += $result['totalDeduction'];
                // ----------------------------------------------------------------------
                
                $info['netPay']     = $info['grossPay'] - $info['wop'] - $info['wtax'] - $info['deductions'];           
                $data['records'][]  = $info;

            }
        }

        // var_dump($data);
        // load views
        echo $this->load->view($this->module_path.'/set_employees', $data, true);
    }
    


    //Original setEmployees
//     public function setEmployees($companyID=0, $branchID=0, $deptID=0, $divisionID=0, $payrollGroupID=0, $payrollPeriodID=0, $attendancePeriodID=0, $employeeTypeID=0)
//     {
//         $data['companyID']          = $companyID;
//         $data['branchID']           = $branchID;
//         $data['deptID']             = $deptID;
//         $data['divisionID']         = $divisionID;
//         $data['payrollGroupID']     = $payrollGroupID;
//         $data['payrollPeriodID']    = $this->encrypter->decode($payrollPeriodID);
//         $data['attendancePeriodID'] = $this->encrypter->decode($attendancePeriodID);
//         $data['employeeTypeID']     = explode('_', $employeeTypeID);
        
//         // get payroll period
//         $this->db->where('payrollPeriodID', $data['payrollPeriodID']);
//         $payrollPeriod = $this->db->get('payroll_periods', 1)->row();
        
//         // var_dump($payrollPeriod);
//         // attendance
//         $this->db->where('payrollPeriodID', $data['attendancePeriodID']);
//         $attendancePeriod = $this->db->get('payroll_periods', 1)->row();


//         $start    = strtotime($attendancePeriod->startDate);
//         $end      = strtotime($attendancePeriod->endDate);
        
//         if ($payrollPeriod->type == 'SM') {
//             $firstHalf = (date('d',strtotime($payrollPeriod->startDate) == '01')) ? true : false;
//         }

//         // select
//         $this->db->select('employments.*');
//         $this->db->select('employees.empNo');
//         $this->db->select('employees.fname');
//         $this->db->select('employees.suffix');
//         $this->db->select('employees.taxID');
//         $this->db->select('job_positions.positionCode');
//         $this->db->select('job_titles.jobTitle');
//         $this->db->select('employee_types.workingDays');
//         $this->db->select('employee_types.employeeType');
//         $this->db->select('employee_types.withPay');
//         $this->db->select('tax_withholding.amount as wtax');
//         $this->db->select('tax_withholding.isAutomatic');
//         $this->db->select('tax_exemptions.personalExemption');
//         $this->db->select('tax_exemptions.additionalExemption');        
//         $this->db->from('employments');
//         $this->db->join('employees', 'employments.empID=employees.empID', 'left');      
//         $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID', 'left');
//         $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
//         $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
//         $this->db->join('tax_withholding','employments.employmentID=tax_withholding.employmentID','left');
//         $this->db->join('tax_exemptions','employees.taxID=tax_exemptions.taxID','left');
//         if ($data['companyID']) {
//             $this->db->where('employments.companyID', $data['companyID']);
//         }
//         if ($data['branchID']) {
//             $this->db->where('employments.branchID', $data['branchID']);
//         }
//         if ($data['deptID']) {
//             $this->db->where('employments.branchID', $data['branchID']);
//         }
//         if ($data['divisionID']) {
//             $this->db->where('employments.divisionID', $data['divisionID']);
//         }
//         $this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
//         $this->db->where('employments.payrollGroupID', $data['payrollGroupID']);
//         $this->db->where('employments.dateAppointed <=', $payrollPeriod->endDate);
//         $this->db->where('employments.status', 1);
//         $this->db->order_by('employments.lname','asc');
//         $this->db->order_by('employees.fname','asc');
//         $this->db->order_by('employments.mname','asc');
//         $records = $this->db->get();
//         // var_dump($records);
        
//         $data['records'] = array();
//         if ($records->num_rows()) {
//             foreach ($records->result() as $row) {

//                 $info = array();
//                 $info['employmentID'] = $row->employmentID;
//                 $info['employmentNo'] = $row->employmentNo;
//                 $info['name']         = $row->lname.', '.$row->fname.' '.$row->mname.' '.$row->suffix;
//                 $info['employeeType'] = $row->employeeType;
//                 $info['jobTitle']     = $row->jobTitle;                         
//                 $info['wop']= 0;
                
//                 $tardyMin       = 0;
//                 $tardyCount     = 0;
//                 $utCount        = 0;
//                 $utMin          = 0;
//                 $absentCount    = 0;

//                 // analyze attendance
//                 for ($current = $start; $current <= $end; $current = strtotime('+1 day', $current)) {

//                     $dtr = $this->dtrlog->analyze($row->employmentID, $current);

//                     if (!empty ($dtr)) {
//                         foreach ($dtr as $log) {
//                             // tardiness
//                             if (strlen($log['tardy_date']) > 5) {
//                                 $tardyCount  += 2;
//                             } elseif (intval($log['tardy']) > 0) {
//                                 $tardyCount  += 1;
//                             }
//                             $tardyMin += intval($log['tardy']);

//                             // undertime
//                             if (strlen($log['ut_date']) > 5) {
//                                 $utCount  += 2;
//                             } elseif (intval($log['ut_date']) > 0) {
//                                 $utCount  += 1;
//                             }
//                             $utMin += intval($log['undertime']);

//                             // absences
//                             $absentCount += ($log['remarks'] == 'ABSENT') ? 1 : 0;

//                             // leaves, orders, suspension
// //                          $res[$info['los']]   += ($info['los']) ? $info['los_day'] : 0;

//                         }
//                     }
//                 }

//                 $ut = $tardyMin + $utMin;
//                 // number of days without pay
//                 $info['daysRequired']   = $row->workingDays;
//                 $info['daysUndertime']  = $absentCount;
//                 $info['daysUndertime'] += $ut / 480;
//                 $info['daysRendered']   = $info['daysRequired'] - $info['daysUndertime'];
                
//                 $row->workingDays       = ($row->workingDays) ? $row->workingDays : date('t', strtotime($payrollPeriod->startDate));
                
//                 // calculate withoutpay
//                 if ($info['daysUndertime'] > 0) {
//                     // get daily rate
//                     $dailyRate      = $row->basicSalary / $row->workingDays;
//                     $info['wop']    = round($dailyRate * $info['daysUndertime'], 2);
//                 }
                
//                 $monthlySalary = round($row->basicSalary - $info['wop'], 2); 
//                 // echo $monthlySalary;
//                 if ($payrollPeriod->type == 'MO') {
                
//                     $info['basicSalary'] = round($row->basicSalary, 2);
//                     $salary = round($info['basicSalary'] - $info['wop'], 2);
//                 } else {

//                     $info['basicSalary'] = ($firstHalf) ? $this->fnumber_format($row->basicSalary / 2, '.') : round($row->basicSalary / 2, 2, PHP_ROUND_HALF_UP);
//                     $salary = ($firstHalf) ? $this->fnumber_format(($info['basicSalary'] - $info['wop']) / 2, '.') : round(($info['basicSalary'] - $info['wop']) / 2, 2, PHP_ROUND_HALF_UP);
//                 }           
                

//                 // calculate incentives
//                 $result = $this->payrollcalc->incentive($row->employmentID, $payrollPeriod); 

//                 $info['totalIncentive'] = $result['totalIncentive'];                                                    
                
//                 // calculate gross pay
//                 $info['grossPay'] = $info['basicSalary'] + $info['totalIncentive'];             
                
//                 // calculate wtax
//                 if ($row->isAutomatic) {
//                     $result = $this->payrollcalc->wtax($row->employmentID, $row->taxID, $row->basicSalary, $payrollPeriod);
//                     $wtax = $result['amount'];
//                 } else {
//                     $wtax = $row->wtax;
//                 }

//                 if ($payrollPeriod->type == 'SM') {
//                     $wtax  = ($firstHalf) ? $this->fnumber_format($wtax / 2, '.') : round($wtax / 2, 2, PHP_ROUND_HALF_UP);
//                 } else {
//                     $wtax = round($wtax, 2, PHP_ROUND_HALF_UP);
//                 }

//                 $info['wtax'] = $wtax;

//                 // calculate contributions
//                 $result = $this->payrollcalc->contribution($row->employmentID, $row->basicSalary, $payrollPeriod);
//                 $info['deductions'] = $result['totalEmployeeShare'];
//                 $info['erShare']    = $result['totalEmployerShare'];

//                 // calculate loans
//                 $result = $this->payrollcalc->loan($row->employmentID, $row->basicSalary, $payrollPeriod);
//                 $info['deductions'] += $result['totalDeduction'];
//                 // ----------------------------------------------------------------------
                
//                 $info['netPay']     = $info['grossPay'] - $info['wop'] - $info['wtax'] - $info['deductions'];           
//                 $data['records'][]  = $info;

//             }
//         }

//         // var_dump($data);
//         // load views
//         echo $this->load->view($this->module_path.'/set_employees', $data, true);
//     }
    
    public function display_session()
    {               
        echo var_dump($_SESSION);
    }
    
    function fnumber_format($number,$decimal = '.',$place = '2')
    {
        $broken_number = explode($decimal,$number);

        if(empty($broken_number[1])) {
            return $broken_number[0];
        } else {
            return $broken_number[0].$decimal.substr($broken_number[1], 0,2);
        }
    }

    public function confirm_record($id=0, $status=0)
    {
        //************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;

        // **************************************************
        $id = $this->encrypter->decode($id);

        $this->record->table = $this->table;
        $this->record->where[$this->pfield] = $id;
        $this->record->retrieve();

        // check roles
        if (($this->roles['approve']) || $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Order Cancel')) {
            $this->record->fields = array();

            $this->record->fields['status'] = $status;

            $this->record->pfield   = $this->pfield;
            $this->record->pk       = $id;
            
            switch ($status) {
                case 2: $operation = "Approve"; break;
                case -1: $operation = "Cancel"; break;
                case 0: $operation = "Disapprove"; break;
            }

            // field logs here
            // $wasChange = $this->log_model->field_logs($this->module, $this->table, $this->pfield, $this->record->pk, $operation, $this->record->fields);

            if ($this->record->update()) {                              
                
                
                
                
                // record logs
                // if ($wasChange) {
                //     $logfield = $this->logfield;
                //     $logs = "Record - ".$this->record->field->$logfield;
                //     $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->pk, $operation, $logs);
                // }

                // successful
                $data["class"] = "success";
                $data["msg"] = $this->data['current_module']['module_label'] . " successfully approved.";
                $data["urlredirect"] = $this->controller_page . "/view/" . $this->encrypter->encode($id);
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            } else {
                // error
                $logfield = $this->pfield;
                $data["display"] = "block";
                $data["class"] = "errorbox";
                $data["msg"] = "Error in changing the ".$this->data['current_module']['module_label']." status!";
                $data["urlredirect"] = $this->controller_page . "/view/" . $this->encrypter->encode($id);
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            }
        } else {
            // error
            $data["display"] = "block";
            $data["class"]   = "errorbox";
            $data["msg"]     = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header".$page_type,$data);
            $this->load->view("message",$data);
            $this->load->view("footer".$page_type);
        }
    }
}
