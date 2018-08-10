<?php

class Test extends CI_Controller
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
        $this->module = 'aaaaaaa';
        $this->data['controller_page'] = $this->controller_page = site_url('aaaaa');
        $this->table = 'payroll';
        $this->pfield = $this->data['pfield'] = 'payrollID';
        $this->logfield = 'payrollNo';
        $this->module_path = 'modules/' . strtolower(str_replace(" ", "_", $this->module)) . '/aaaaa';

        if (! $this->session->userdata('current_user')->sessionID) {
            header('location: ' . site_url('login'));
        }
    }


    public function index()
    {
        
        echo $this->obj->input_field('myid');
        
        // echo $input_field->add();
    }
    public function index99()
    {
        // echo 1;
        $payrollID = 20;
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
            // echo $this->db->last_query();


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
        echo $this->record->field->branchID.' branchID </br>';
        echo $this->record->field->divisionID.' divisionID </br>';
        if ($this->record->field->branchID == 0) {
            $this->db->where('companyID', $this->record->field->companyID);
                // $this->db->order_by('rank', 'asc');
            $data['groups'] = $this->db->get('branches')->result();

            $data['groupID']    = 'branchID';
            $data['group_label'] = 'branchName';
        } elseif ($this->record->field->divisionID == 0) {
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
            
            var_dump($data);
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

        public function index6()
        {
            $data = $this->data;
            $data = $this->_create($data);

            var_dump($data);
        }

        private function _create($data)
        {
            $data['my_array'] = array(1,2,3,4,5,6,7,8);
            // set sessions
            $this->session->set_userdata('current_companyID', $data['companyID']);
            $this->session->set_userdata('current_officeID', $data['officeID']);
            $this->session->set_userdata('current_divisionID', $data['divisionID']);
            $this->session->set_userdata('current_payrollGroupID', $data['payrollGroupID']);
            $this->session->set_userdata('current_payrollPeriodID', $data['payrollPeriodID']);
            $this->session->set_userdata('current_attendancePeriodID', $data['attendancePeriodID']);
            return $data;
        }

        public function index5()
        {

            $total_count = 0;

        //first get all type of shifts
            $results = $this->db->get('shifts')->result();
            var_dump($results);

        //foreach type of shift get total employee shifts
        //e.g in regular shift 4 employees belongw in regular shift
            foreach ($results as $res) {

                $this->db->where('shiftID', $res->shiftID);
                $total_shift_schedules = $this->db->get('shift_schedules')->num_rows();
            // echo 'Total Exmployees for Regular shift: '.$total_shift_schedules;


            //for each type of shift get it's total attendance for today
                $this->db->select('employees.*');
                $this->db->from('employees');
                $this->db->join('attendance', 'employees.empID=attendance.empID', 'left');
                $this->db->where('date', date('2018-06-22'));
                $this->db->where('attendanceType', 1);
                $this->db->where('shiftID', $res->shiftID);
                $total_attendance = $this->db->get()->num_rows();

            //subtract total attendance to total employee shifts
                $a = $total_shift_schedules - $total_attendance;

                $total_count += $a;
            }

            echo $total_count;

        }

        public function index4()
        {
            $this->db->select('leave_dates.startDate');
            $this->db->select('leave_dates.endDate');
            $this->db->from('leaves');
            $this->db->join('leave_dates', 'leaves.leaveID=leave_dates.leaveID', 'left');
            $results = $this->db->get()->result();

            foreach ($results as $res) {
                $startMonth = date('m',strtotime($res->startDate));
                $endMonth = date('m',strtotime($res->endDate));

                if (($startMonth == date('m')) || ($endMonth == date('m'))) {
                    $count += 1;
                }
            }
        }

        public function index3()
        {


            $id = 1;
            $field = 'branchID';
            $table = 'branches';
            $select = "employees.fname, employees.lname";
            $join = "employees|branches.branchHeadID=employees.empID|left";
            $mainSelect = "";
            $this->db->select($table.'.*');
            if ($select != '') {

                $selects = explode(',', $select);
                foreach ($selects as $s) {
                    $this->db->select($s);
                }
            }

            $this->db->from($table);

            if ($join != '') {
                $joins = explode(',', $join);
                foreach ($joins as $j) {
                    $x = explode('|', $j);
                    $this->db->join($x[0], $x[1], $x[2]);
                }
            }


            $this->db->where($field,$id);

            $record = $this->db->get()->row();
            echo $this->db->last_query();

        }

        public function index2()
        {

        // $dept_obj->dept = array();

        // $dept_object
            $deptArr = array();

            $dept_arr = $this->_department_tardiness();
            foreach ($dept_arr as $dept) {
                // var_dump($dept);
                $dept_obj = new stdClass();
                $dept_obj->name = $dept['name'];
                $dept_obj->total = $dept['total_percentage'];
                $dept_obj->data = $dept['deptData'];
                
                // echo 'tom';
                array_push($deptArr, $dept_obj);
            }

            $s_data['dept_tardy'] = $deptArr;

            $this->session->set_userdata('s_data', $s_data);

        // select
            $this->db->select($this->table.'.*');
            $this->db->select('loans.principal');
            $this->db->select('loans.payment');
            $this->db->select('loans.balance');
            $this->db->select('loans.dateFiled');
            $this->db->select('loan_types.name');
            $this->db->select('loan_types.abbr');
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
            $this->db->join('loans', $this->table.'.payID=loans.payID', 'left');
            $this->db->join('loan_types', 'loans.loanTypeID=loan_types.loanTypeID', 'left');
            $this->db->join('employments', $this->table.'.employmentID=employments.employmentID', 'left');
            $this->db->join('employees', 'employments.empID=employees.empID', 'left');
            $this->db->join('branches', 'employments.branchID=branches.branchID', 'left');
            $this->db->join('departments', 'employments.deptID=departments.deptID', 'left');
            $this->db->join('divisions', 'employments.divisionID=divisions.divisionID', 'left');
            $this->db->get();
            echo $this->db->last_query();


        }


        private function _department_tardiness()
        {
            $departments = $this->db->get('departments')->result();
            $department_employees = array();
            $deptTardyArray = array();

            $months = array(
                date('Y-'.'01'),
                date('Y-'.'02'),
                date('Y-'.'03'),
                date('Y-'.'04'),
                date('Y-'.'05'),
                date('Y-'.'06'),
                date('Y-'.'07'),
                date('Y-'.'08'),
                date('Y-'.'09'),
                date('Y-'.'10'),
                date('Y-'.'11'),
                date('Y-'.'12'),
            );

            foreach ($departments as $dept) {
                $this->db->select('employments.employmentNo');
                $this->db->select('employments.employmentNo');
                $this->db->select('employments.empID');
                $this->db->select('departments.deptID');
                $this->db->select('departments.deptName');

                $this->db->from('employments');
                $this->db->join('departments', 'employments.deptID=departments.deptID', 'left');
                $this->db->where('deptName', $dept->deptName);
                $department_employees[$dept->deptName] = $this->db->get()->result(); 
            }


            foreach ($departments as $dept) {
            // echo $dept->deptName;
                $total_late    = 0;
                $total_notlate = 0;
                $total_logins  = 0;

                $deptData = array();
                $monthly_percentage_array = array();


                foreach ($department_employees[$dept->deptName] as $dept_employee) {
                    $attendance     = $this->_attendance($dept_employee->empID);
                    $total_late    += $attendance['late_count'];
                    $total_notlate += $attendance['notlate_count'];
                    $total_logins  += $attendance['login_count'];

                }

                foreach ($months as $month) {
                    $total_late_m    = 0;
                    $total_notlate_m = 0;
                    $total_logins_m  = 0;
                    $month_name = '';
                    switch ($month) {
                        case date('Y-'.'01'):
                        $month_name = 'Jan';
                        break;
                        case date('Y-'.'02'):
                        $month_name = 'Feb';
                        break;
                        case date('Y-'.'03'):
                        $month_name = 'Mar';
                        break;
                        case date('Y-'.'04'):
                        $month_name = 'Apr';
                        break;
                        case date('Y-'.'05'):
                        $month_name = 'May';
                        break;
                        case date('Y-'.'06'):
                        $month_name = 'Jun';
                        break;
                        case date('Y-'.'07'):
                        $month_name = 'Jul';
                        break;
                        case date('Y-'.'08'):
                        $month_name = 'Aug';
                        break;
                        case date('Y-'.'09'):
                        $month_name = 'Sep';
                        break;
                        case date('Y-'.'10'):
                        $month_name = 'Oct';
                        break;
                        case date('Y-'.'11'):
                        $month_name = 'Nov';
                        break;
                        case date('Y-'.'12'):
                        $month_name = 'Dec';
                        break;
                        default:
                        $month_name = '';
                    }

                    foreach ($department_employees[$dept->deptName] as $dept_employee) {    
                        $attendance_m     = $this->_attendance_by_month($dept_employee->empID, $month);              
                        $total_late_m    += $attendance_m['late_count'];
                        $total_logins_m  += $attendance_m['login_count'];                   
                    }

                    $total_monthly_percentage = ($total_late_m/$total_logins_m) * 100;
                    $deptDataDict = array('month'=>$month_name, 'total'=>$total_monthly_percentage);
                    array_push($deptData, $deptDataDict);
                }



                $total_percentage = ($total_late/$total_logins) * 100;
                $deptTardyDict    = array('name'=>$dept->deptName, 'total_late'=>$total_late, 'total_notlate'=>$total_notlate, 'total_logins'=>$total_logins, 'total_percentage'=>$total_percentage, 'deptData'=>$deptData);
                array_push($deptTardyArray, $deptTardyDict);
            }

            return $deptTardyArray;
        }

        private function _attendance($empID)
        {
            $this->db->like('date', date("Y-m"));
            $this->db->where('empID', $empID);

            $results       = $this->db->get('attendance')->result();
            $late_count    = 0;
            $notlate_count = 0;
            $login_count   = 0;


            foreach ($results as $res) {
                $a = $res->tardy + $res->undertime;
                if ($a) {
                    $late_count += 1;
                } else {
                    $notlate_count += 1;
                }
                $login_count += 1;
            }

            return array('late_count'=>$late_count, 'notlate_count'=>$notlate_count, 'login_count'=>$login_count);
        }

        private function _attendance_by_month($empID, $month)
        {
            $this->db->like('date', $month);
            $this->db->where('empID', $empID);

            $results       = $this->db->get('attendance')->result();
            $late_count    = 0;
            $notlate_count = 0;
            $login_count   = 0;


            foreach ($results as $res) {
                $a = $res->tardy + $res->undertime;
                if ($a) {
                    $late_count += 1;
                } else {
                    $notlate_count += 1;
                }
                $login_count += 1;
            }

            return array('late_count'=>$late_count, 'notlate_count'=>$notlate_count, 'login_count'=>$login_count);
        }

    }




//get shift schedules
// $this->db->select('shift_schedules.shiftScheduleID');
// $this->db->select('shift_schedules.shiftID as schedShiftID');
// $this->db->select('shift_schedules.date');
// $this->db->select('shift_schedules.remarks as schedule_remarks');
// $this->db->select('employees.lname');
// $this->db->select('employees.fname');
// $this->db->select('employees.mname');
// $this->db->select('shifts.status as shiftStatus');

// $this->db->from($this->table);
// // $this->db->join('companies',$this->table.'.companyID=companies.companyID','left');
// // $this->db->join('branches',$this->table.'.branchID=branches.branchID','left');
// $this->db->join('employees',$this->table.'.empID=employees.empID','left');
// $this->db->join('shifts','shift_schedules.shiftID=shifts.shiftID','left');
// // ----------------------------------------------------------------------------------
// $data['rec'] = $this->db->get()->result();