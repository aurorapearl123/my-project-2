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

    private function _count_leaves_this_month()
    {
        $count = 0;
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
        return $count;
    }

    private function _get_birthdays()
    {
        $this->db->like('birthDate', date('-m-'));
        $query = $this->db->get('employees')->result();
        return $query;
    }

    private function _get_employment_stats()
    {
        $this->db->where('status', 3);
        $promotions = $this->db->get('employments')->num_rows();

        $this->db->where('status', 4);
        $demotions = $this->db->get('employments')->num_rows();

        $this->db->where('status', 2);
        $transferred = $this->db->get('employments')->num_rows();

        $this->db->where('status', 5);
        $terminated = $this->db->get('employments')->num_rows();

        $this->db->where('status', 1);
        $active = $this->db->get('employments')->num_rows();

        $this->db->where('status', 0);
        $inactive = $this->db->get('employments')->num_rows();

        return array('promotions'=>$promotions, 'demotions'=>$demotions, 'transferred'=>$transferred, 'terminated'=>$terminated, 'active'=>$active, 'inactive'=>$inactive);
    }

    private function _department_tardiness()
    {
                $this->db->select('branches.branchCode');
        $this->db->select('departments.*');
        $this->db->from('departments');
        $this->db->join('branches', 'departments.branchID=branches.branchID', 'left');
        $this->db->where('departments.status >',0);
        $departments = $this->db->get()->result();
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
                if (!$total_logins_m) {
                    $total_monthly_percentage = 0;
                } else {
                    $total_monthly_percentage = ($total_late_m/$total_logins_m) * 100;  
                }
                
                $deptDataDict = array('month'=>$month_name, 'total'=>$total_monthly_percentage);
                array_push($deptData, $deptDataDict);
            }

            $total_percentage = ($total_late/$total_logins) * 100;
            $deptTardyDict    = array('name'=>$dept->branchCode.' '.$dept->deptName, 'total_late'=>$total_late, 'total_notlate'=>$total_notlate, 'total_logins'=>$total_logins, 'total_percentage'=>$total_percentage, 'deptData'=>$deptData);
            array_push($deptTardyArray, $deptTardyDict);
        }

    return $deptTardyArray;
}

private function _attendance($empID)
{
    $month = date('Y-m');
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

function _get_main_container_chart()
{

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

    $this->db->select("shift_schedules.*");
    $this->db->select('shift_schedules.shiftID as schedShiftID');
    $this->db->select('shifts.status as shiftStatus');
    $this->db->from('employments');
    $this->db->join('shift_schedules','employments.employmentID=shift_schedules.employmentID','left');
    $this->db->join('shifts','shift_schedules.shiftID=shifts.shiftID','left');
    $this->db->where('shifts.status', 1);
    $this->db->where('employments.status !=', 0);
    $shiftSchedules = $this->db->get()->num_rows();

        //22 default number working days philippines
    $total_shifts = $shiftSchedules*22;
    $attendance_percentage_array = array();

    $this->db->like('dateFiled', date('Y'));
    $total_leaves = $this->db->get('leaves')->num_rows();
    $leaves_percentage_array = array();

    $this->db->like('dateFiled', date('Y'));
    $total_loans = $this->db->get('loans')->num_rows();
    $loans_percentage_array = array();

    foreach ($months as $month) {

            // Present start
        $this->db->like('date', $month);
        $total_attendance = $this->db->get('attendance')->num_rows();

        if (!$total_shifts) {
            $total_attendance_percentage = 0;
        } else {
            $total_attendance_percentage = number_format((($total_attendance/$total_shifts)*100), 0);
        }
        

        array_push($attendance_percentage_array, $total_attendance_percentage);            
            // Present end

            //Loan start
        $this->db->where('status', 0);
        $this->db->like('dateFiled', $month);
        $approved_loans = $this->db->get('loans')->num_rows();

        if (!$total_loans) {
            $total_loans_percentage = 0;
        } else {
            $total_loans_percentage = number_format(($approved_loans/$total_loans)*100, 0);
        }
        
        array_push($loans_percentage_array, $total_loans_percentage);
            //Loan end

            //Leaves start
        $this->db->where('status', 0);
        $this->db->like('dateFiled', $month);
        $approved_leaves = $this->db->get('leaves')->num_rows();

        if (!$total_leaves) {
            $total_leaves_percentage = 0;
        } else {
            $total_leaves_percentage = number_format(($approved_leaves/$total_leaves)*100, 0);
        }
        
        array_push($leaves_percentage_array, $total_leaves_percentage);
            //Leaves end


    }

    return array('attendance_percentage_array'=>$attendance_percentage_array, 'leaves_percentage_array'=>$leaves_percentage_array, 'loans_percentage_array'=>$loans_percentage_array);
}









    //Attendance Manual Insert
    // INSERT INTO `attendance` (`attendanceID`, `empID`, `employmentID`, `shiftID`, `date`, `attendanceType`, `login1`, `logout1`, `login2`, `logout2`, `hours`, `tardy`, `undertime`, `remarks`, `note`, `isEdited`, `status`) VALUES (NULL, '1', '1', '1', '2018-06-22', '1', '2018-06-22 08:05:00', '2018-06-22 12:00:00', '2018-06-22 13:00:00', '2018-06-22 17:00:00', '8', '5', '0', 'late', '', '0', '0');

    //No late - change empID, employmendID, date, login1-date, logout1-date, login2-date, logout2-date
    // INSERT INTO `attendance` (`attendanceID`, `empID`, `employmentID`, `shiftID`, `date`, `attendanceType`, `login1`, `logout1`, `login2`, `logout2`, `hours`, `tardy`, `undertime`, `remarks`, `note`, `isEdited`, `status`) VALUES (NULL, '64', '3', '1', '2018-06-23', '1', '2018-06-23 08:00:00', '2018-06-22 12:00:00', '2018-06-22 13:00:00', '2018-06-22 17:00:00', '8', '0', '0', '', '', '0', '0');

    //Employees
    // Mario
    // employmentID = 1
    // empID = 1
    // shiftID = 1

    // Dayrocs Cherilyn
    // employmentID = 3
    // empID = 64
    // shiftID = 1

    // Joseph Pableo 
    // employmentID = 4
    // empID = 65
    // shiftID = 1

    // Tom Mantilla
    // employmentID = 5
    // empID = 66
    // shiftID = 1
}

