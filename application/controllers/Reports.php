<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Reports extends CI_Controller {
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
	
	public function __construct() {
		parent::__construct ();
		$this->load->model ( 'generic_model', 'records' );
		$this->module = 'Reports';
		$this->data ['controller_page'] = $this->controller_page = site_url ( 'reports' ); // defines contoller link
		$this->table = 'shifts'; // defines the default table
		$this->pfield = $this->data ['pfield'] = 'shiftID'; // defines primary key
		$this->logfield = 'shiftName';
		$this->module_path = 'modules/' . strtolower ( str_replace ( " ", "_", $this->module ) ) . '/reports'; // defines module path
		

		// check for maintenance period
		if ($this->config_model->getConfig ( 'Maintenance Mode' ) == '1') {
			header ( 'location: ' . site_url ( 'maintenance_mode' ) );
		}
		
		// check user session
		if (! $this->session->userdata ( 'current_user' )->sessionID) {
			header ( 'location: ' . site_url ( 'login' ) );
		}
	}
	
	private function submenu()
	{
		//submenu setup
		require_once ('modules.php');
		
		foreach ( $modules as $mod ) {
			//modules/<module>/
			// - <menu>
			// - <metadata>
			//var_dump ( $mod );
			$this->load->view ( 'modules/' . str_replace ( " ", "_", strtolower ( $mod ) ) . '/metadata' );
		}
		
		$this->data ['modules'] 			= $this->modules;
		$this->data ['current_main_module'] = $this->modules[$this->module]['main']; // defines the current main module
		$this->data['current_module'] 		= $this->modules[$this->module]['sub']['Reports']; // defines the current sub module
		
		// check roles
		$this->check_roles ();
		$this->data ['roles'] = $this->roles;
	}
	
	private function check_roles()
	{
		// check roles
		$this->roles ['create'] = $this->userrole_model->has_access ( $this->session->userdata ( 'current_userID' ), 'Add ' . $this->module );
		$this->roles ['view'] 	= $this->userrole_model->has_access ( $this->session->userdata ( 'current_userID' ), 'View ' . $this->module );
		$this->roles ['edit'] 	= $this->userrole_model->has_access ( $this->session->userdata ( 'current_userID' ), 'Edit Existing ' . $this->module );
		$this->roles ['delete'] = $this->userrole_model->has_access ( $this->session->userdata ( 'current_userID' ), 'Delete Existing ' . $this->module );
		$this->roles ['approve']= $this->userrole_model->has_access ( $this->session->userdata ( 'current_userID' ), 'Approve ' . $this->module );
	}
	
	
	public function index() {
		$this->attendance_summary ();
	}
	
	public function attendance_summary($branchID = 0, $deptID = 0, $divisionID = 0, $attendanceID = 0, $payrollGroupID = 0, $employeeTypeID = 0, $limit = 100, $offset = 0) {
		$this->submenu ();
		$data = $this->data;
		
		// check roles
		if ($this->roles ['create']) {
			$data['required_fields'] = array('companyID'=>'Company','attendanceID'=>'Attendance Period'
			);
				$data['branchID']  			= $this->input->post('branchID');
				$data['deptID']				= $this->input->post('deptID');
				$data['divisionID'] 		= $this->input->post('divisionID');
				$data['payrollPeriodID'] 	= $this->input->post('payrollPeriodID');
				$data['payrollGroupID'] 	= $this->input->post('payrollGroupID');
				$data['attendanceID']  		= $this->input->post('attendanceID');
				$data['employeeTypeID'] 	= $this->input->post('employeeTypeID');
				$data['limit'] 				= $this->input->post('limit');
				$data['offset']     		= 0;
				
				// set sessions
				$this->session->set_userdata('current_branchID', $data['branchID']);
				$this->session->set_userdata('current_deptID', $data['deptID']);
				$this->session->set_userdata('current_divisionID', $data['divisionID']);
				$this->session->set_userdata('current_payrollGroupID', $data['payrollGroupID']);
				$this->session->set_userdata('current_attendanceID', $data['attendanceID']);
				$this->session->set_userdata('current_employeeTypeID', $data['employeeTypeID']);
				$this->session->set_userdata('current_limit', $data['limit']);
				$this->session->set_userdata('current_offset', $data['offset']);
												
				$this->db->where('employments.branchID', $data['branchID']);
				if ($data['deptID']) {
					$this->db->where('employments.deptID', $data['deptID']);
				}
				if ($data['divisionID']) {
					$this->db->where('employments.divisionID', $data['divisionID']);
				}
				if ($data['payrollGroupID']) {
					$this->db->where('employments.payrollGroupID', $data['payrollGroupID']);
				}
				if (!empty($data['employeeTypeID'])) {
					$this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
				}
				$this->db->where('employments.isBiometric', 1);
				$this->db->where('employments.status', 1);
				
				// get
				$data['ttl_rows'] = $config['total_rows'] = $this->db->count_all_results('employments');
				
				$employeeTypeID = 0;
				if (!empty($data['employeeTypeID'])) {
					$employeeTypeID = implode('_', $data['employeeTypeID']);
				}
				
				// set pagination
				$config['base_url'] 	= $this->controller_page."/attendance_summary/".$data['branchID']."/".$data['deptID']."/".$data['divisionID']."/".$data['payrollGroupID']."/".$data['attendanceID']."/".$employeeTypeID."/".$data['limit']."/";
				$config['per_page'] 	= $data['limit'];
				$config['uri_segment'] 	= 10;
				$this->pagination->initialize($config);			
				
				$this->db->select('employments.*');
				$this->db->select('employees.empNo');
				$this->db->select('employees.fname');
				$this->db->select('employees.suffix');
				$this->db->select('job_positions.positionCode');
    			$this->db->select('job_titles.jobTitle');
				$this->db->from('employments');
				$this->db->join('employees','employments.empID=employees.empID','left');
				$this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
				$this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
				$this->db->where('employments.branchID', $data['branchID']);
				if ($data['deptID']) {
					$this->db->where('employments.deptID', $data['deptID']);
				}
				if ($data['divisionID']) {
					$this->db->where('employments.divisionID', $data['divisionID']);
				}
				if ($data['payrollGroupID']) {
					$this->db->where('employments.payrollGroupID', $data['payrollGroupID']);
				}
				if (!empty($data['employeeTypeID'])) {
					$this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
				}
				$this->db->where('employments.isBiometric', 1);
				$this->db->where('employments.status', 1);
				
				if ($data['limit']) {
					if ($data['offset']) {
						$this->db->limit($data['limit'],$data['offset']);
					} else {
						$this->db->limit($data['limit']);
					}
				}
				
				$data['records'] = $this->db->get();
				// analyze attendance
				$this->db->where('payrollPeriodID', $data['payrollPeriodID']);
				$payroll = $this->db->get('payroll_periods', 1)->row();
				
				$start      = strtotime($payroll->startDate);
				$end        = strtotime($payroll->endDate);
				$data['log']= array();
				
				if ($data['records']->num_rows()) {
					foreach ($data['records']->result() as $row) {
						$res['tardy_count'] = 0;
						$res['tardy_min']   = 0;
						$res['tardy_date']  = "";
						$res['ut_count'] 	= 0;
						$res['ut_min']   	= 0;
						$res['ut_date']  	= "";
						$res['absent_count']= 0;
						$res['absent_date'] = "";
						$res['SUSPN']	= 0;
						$res['ORDR']	= 0;
						$res['SL']		= 0;
						$res['VL']		= 0;
						$res['SPL']		= 0;
						
						for ($current = $start; $current <= $end; $current = strtotime('+1 day', $current)) {
							$dtr = $this->dtrlog->analyze($row->employmentID, $current);													
							
							if (!empty ($dtr)) {
								foreach ($dtr as $info) { 
									// tardiness
									if (strlen($info['tardy_date']) > 5) {
										$res['tardy_count']  += 2;
									} elseif (intval($info['tardy']) > 0) {
										$res['tardy_count']  += 1;
									}
									$res['tardy_min']    += intval($info['tardy']);
									$res['tardy_date']   .= ($info['tardy_date']) ? ' '.$info['tardy_date'] : '';
									
									// undertime
									if (strlen($info['ut_date']) > 5) {
										$res['ut_count']  += 2;
									} elseif (intval($info['ut_date']) > 0) {
										$res['ut_count']  += 1;
									}
									$res['ut_min']   	 += intval($info['undertime']);
									$res['ut_date']  	 .= ($info['ut_date']) ? ' '.$info['ut_date'] : '';
									
									// absences
									$res['absent_count'] += ($info['remarks'] == 'ABSENT') ? 1 : 0;
									$res['absent_date']  .= ($info['remarks'] == 'ABSENT') ? date(' j', strtotime($info['base'])) : '';
									
									// leaves, orders, suspension
									$res[$info['los']]   += ($info['los']) ? $info['los_day'] : 0;
									
								}
							}
						}
						$data['log'][$row->employmentID] = $res;
					}
				}	
				//echo $this->db->last_query();	
						
				$this->db->order_by('rank', 'asc');
				$this->db->order_by('code', 'asc');
				$data['leave_types'] =  $this->db->get('leave_types');
				
//			} else {
//				$data['branchID']		= ($this->session->userdata('current_branchID')) ? $this->session->userdata('current_branchID') : 0;
//				$data['deptID']			= ($this->session->userdata('current_deptID')) ? $this->session->userdata('current_deptID') : 0;
//				$data['divisionID']		= ($this->session->userdata('current_divisionID')) ? $this->session->userdata('current_divisionID') : 0;
//				$data['payrollGroupID']	= ($this->session->userdata('current_payrollGroupID')) ? $this->session->userdata('current_payrollGroupID') : 0;
//				$data['attendanceID']	= ($this->session->userdata('current_attendanceID')) ? $this->session->userdata('current_attendanceID') : 0;
//				$data['employeeTypeID']	= ($this->session->userdata('current_employeeTypeID')) ? $this->session->userdata('current_employeeTypeID') : array();
//				$data['limit'] 		 	= ($this->session->userdata('current_limit')) ? $this->session->userdata('current_limit') : 25;
//				$data['offset'] 		= ($this->session->userdata('current_offset')) ? $this->session->userdata('current_offset') : 0;
//				
//				$this->db->where('attendanceID', 0);
//				$data['records'] 		= $this->db->get('attendance');
//			}
				
			// load views
			$this->load->view("header", $data);
			$this->load->view($this->module_path."/attendance_summary", $data);
			$this->load->view("footer");
		
		} else {
			// no access this page
			$data ['class'] = "danger";
			$data ['msg'] = "Sorry, you don't have access to this page!";
			$data ['urlredirect'] = "";
			$this->load->view ( 'header', $data );
			$this->load->view ( 'message' );
			$this->load->view ( 'footer' );
		}
	}
	
	public function tardiness_summary($branchID = 0, $deptID = 0, $divisionID = 0, $attendanceID = 0, $payrollGroupID = 0, $employeeTypeID = 0, $limit = 100, $offset = 0) {
		$this->submenu ();
		$data = $this->data;
		
		// check roles
		if ($this->roles ['create']) {
			$data['required_fields'] = array('branchID'=>'Branch','attendanceID'=>'Attendance Period'
			);
				$data['branchID']  			= $this->input->post('branchID');
				$data['deptID']				= $this->input->post('deptID');
				$data['divisionID'] 		= $this->input->post('divisionID');
				$data['payrollPeriodID'] 	= $this->input->post('payrollPeriodID');
				$data['payrollGroupID'] 	= $this->input->post('payrollGroupID');
				$data['attendanceID']  		= $this->input->post('payrollPeriodID');
				$data['employeeTypeID'] 	= $this->input->post('employeeTypeID');
				$data['limit'] 				= $this->input->post('limit');
				$data['offset']     		= 0;
				
				// set sessions
				$this->session->set_userdata('current_branchID', $data['branchID']);
				$this->session->set_userdata('current_deptID', $data['deptID']);
				$this->session->set_userdata('current_divisionID', $data['divisionID']);
				$this->session->set_userdata('current_payrollGroupID', $data['payrollGroupID']);
				$this->session->set_userdata('current_attendanceID', $data['attendanceID']);
				$this->session->set_userdata('current_employeeTypeID', $data['employeeTypeID']);
				$this->session->set_userdata('current_limit', $data['limit']);
				$this->session->set_userdata('current_offset', $data['offset']);
	
				$this->db->where('employments.branchID', $data['branchID']);
				if ($data['deptID']) {
					$this->db->where('employments.deptID', $data['deptID']);
				}
				if ($data['divisionID']) {
					$this->db->where('employments.divisionID', $data['divisionID']);
				}
				if ($data['payrollGroupID']) {
					$this->db->where('employments.payrollGroupID', $data['payrollGroupID']);
				}
				if (!empty($data['employeeTypeID'])) {
					$this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
				}
				$this->db->where('employments.isBiometric', 1);
				$this->db->where('employments.status', 1);
	
				// get
				$data['ttl_rows'] = $config['total_rows'] = $this->db->count_all_results('employments');
	
				$employeeTypeID = 0;
				if (!empty($data['employeeTypeID'])) {
					$employeeTypeID = implode('_', $data['employeeTypeID']);
				}
				
				// set pagination
				$config['base_url'] 	= $this->controller_page."/tardiness_summary/".$this->encrypter->decode($data['branchID'])."/".$this->encrypter->decode($data['deptID'])."/".$this->encrypter->decode($data['divisionID'])."/".$this->encrypter->decode($data['payrollGroupID'])."/".$this->encrypter->decode($data['attendanceID'])."/".$employeeTypeID."/".$data['limit']."/";
				$config['per_page'] 	= $data['limit'];
				$config['uri_segment'] 	= 10;
				$this->pagination->initialize($config);
	
				$this->db->select('employments.*');
				$this->db->select('employees.empNo');
				$this->db->select('employees.fname');
				$this->db->select('employees.suffix');
				$this->db->select('job_positions.positionCode');
    			$this->db->select('job_titles.jobTitle');
				$this->db->from('employments');
				$this->db->join('employees','employments.empID=employees.empID','left');
				$this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
				$this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
				$this->db->where('employments.branchID', $data['branchID']);
				if ($data['deptID']) {
					$this->db->where('employments.deptID', $data['deptID']);
				}
				if ($data['divisionID']) {
					$this->db->where('employments.divisionID', $data['divisionID']);
				}
				if ($data['payrollGroupID']) {
					$this->db->where('employments.payrollGroupID', $data['payrollGroupID']);
				}
				if (!empty($data['employeeTypeID'])) {
					$this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
				}
				$this->db->where('employments.isBiometric', 1);
				$this->db->where('employments.status', 1);

	
				$data['records'] = $this->db->get();
	
				// analyze attendance
				$this->db->where('payrollPeriodID', $data['attendanceID']);
				$payroll = $this->db->get('payroll_periods', 1)->row();
	
				$start      = strtotime($payroll->startDate);
				$end        = strtotime($payroll->endDate);
				$data['log']= array();
					
				if ($data['records']->num_rows()) {
					foreach ($data['records']->result() as $row) {
						$res = array();
						$res['employmentID']= $row->employmentID;
						$res['empNo'] 		= $row->empNo;
						$res['employee'] 	= $row->lname.', '.$row->fname.' '.substr($row->mname, 0, 1).' '.$row->suffix;
						$res['position'] 	= $row->jobTitle;
						$res['basicSalary'] = $row->basicSalary;
						$res['tardy_count'] = 0;
						$res['tardy_min']   = 0;
						$res['tardy_date']  = "";
						for ($current = $start; $current <= $end; $current = strtotime('+1 day', $current)) {
							$dtr = $this->dtrlog->analyze($row->employmentID, $current);
								
							if (!empty ($dtr)) {
								foreach ($dtr as $info) { 
								    $att[] = $dtr;
									if (strlen($info['tardy_date']) > 5) { 
										$res['tardy_count']  += 2;
									} elseif (intval($info['tardy']) > 0) {
										$res['tardy_count']  += 1;
									}
									$res['tardy_min']    += intval($info['tardy']);
									$res['tardy_date']   .= ($info['tardy_date']) ? ' '.$info['tardy_date'] : '';															
								}
							}
						}
						
						if ($res['tardy_count'] > 0) {
							$data['log'][] = $res;
						}
					}
				}
	
				// sorting
				for($r=0; $r<count($data['log']); $r++) {
					for($c=$r+1; $c<count($data['log']); $c++) {
						if ($data['log'][$r]['tardy_count'] < $data['log'][$c]['tardy_count']) {
							// swap
							$temp = array();
							$temp = $data['log'][$r];
							$data['log'][$r] = $data['log'][$c];
							$data['log'][$c] = $temp;
						}
					}
				}
			// load views
			$this->load->view("header", $data);
			$this->load->view($this->module_path."/tardiness_summary", $data);
			$this->load->view("footer");
		
		} else {
			// no access this page
			$data ['class'] = "danger";
			$data ['msg'] = "Sorry, you don't have access to this page!";
			$data ['urlredirect'] = "";
			$this->load->view ( 'header', $data );
			$this->load->view ( 'message' );
			$this->load->view ( 'footer' );
		}
	}
	
	public function undertime_summary($branchID = 0, $deptID = 0, $divisionID = 0, $attendanceID = 0, $payrollGroupID = 0, $employeeTypeID = 0, $limit = 100, $offset = 0) {
		$this->submenu ();
		$data = $this->data;
		
		// check roles
		if ($this->roles ['create']) {
			$data['required_fields'] = array('companyID'=>'Company','attendanceID'=>'Attendance Period'
			);
				$data['branchID']  			= $this->input->post('branchID');
				$data['deptID']				= $this->input->post('deptID');
				$data['divisionID'] 		= $this->input->post('divisionID');
				$data['payrollPeriodID'] 	= $this->input->post('payrollPeriodID');
				$data['payrollGroupID'] 	= $this->input->post('payrollGroupID');
				$data['attendanceID']  		= $this->input->post('payrollPeriodID');
				$data['employeeTypeID'] 	= $this->input->post('employeeTypeID');
				$data['limit'] 				= $this->input->post('limit');
				$data['offset']     		= 0;
				
				// set sessions
				$this->session->set_userdata('current_branchID', $data['branchID']);
				$this->session->set_userdata('current_deptID', $data['deptID']);
				$this->session->set_userdata('current_divisionID', $data['divisionID']);
				$this->session->set_userdata('current_payrollGroupID', $data['payrollGroupID']);
				$this->session->set_userdata('current_attendanceID', $data['attendanceID']);
				$this->session->set_userdata('current_employeeTypeID', $data['employeeTypeID']);
				$this->session->set_userdata('current_limit', $data['limit']);
				$this->session->set_userdata('current_offset', $data['offset']);
												
				$this->db->where('employments.branchID', $data['branchID']);
				if ($data['deptID']) {
					$this->db->where('employments.deptID', $data['deptID']);
				}
				if ($data['divisionID']) {
					$this->db->where('employments.divisionID', $data['divisionID']);
				}
				if ($data['payrollGroupID']) {
					$this->db->where('employments.payrollGroupID', $data['payrollGroupID']);
				}
				if (!empty($data['employeeTypeID'])) {
					$this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
				}
				$this->db->where('employments.isBiometric', 1);
				$this->db->where('employments.status', 1);
				
				// get
				$data['ttl_rows'] = $config['total_rows'] = $this->db->count_all_results('employments');
				
				$employeeTypeID = 0;
				if (!empty($data['employeeTypeID'])) {
					$employeeTypeID = implode('_', $data['employeeTypeID']);
				}
				
				// set pagination
				$config['base_url'] 	= $this->controller_page."/undertime_summary/".$data['branchID']."/".$data['deptID']."/".$data['divisionID']."/".$data['payrollGroupID']."/".$data['attendanceID']."/".$employeeTypeID."/".$data['limit']."/";
				$config['per_page'] 	= $data['limit'];
				$config['uri_segment'] 	= 10;
				$this->pagination->initialize($config);			
				
				$this->db->select('employments.*');
				$this->db->select('employees.empNo');
				$this->db->select('employees.fname');
				$this->db->select('employees.suffix');
				$this->db->select('job_positions.positionCode');
    			$this->db->select('job_titles.jobTitle');
				$this->db->from('employments');
				$this->db->join('employees','employments.empID=employees.empID','left');
				$this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
				$this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
				$this->db->where('employments.branchID', $data['branchID']);
				if ($data['deptID']) {
					$this->db->where('employments.deptID', $data['deptID']);
				}
				if ($data['divisionID']) {
					$this->db->where('employments.divisionID', $data['divisionID']);
				}
				if ($data['payrollGroupID']) {
					$this->db->where('employments.payrollGroupID', $data['payrollGroupID']);
				}
				if (!empty($data['employeeTypeID'])) {
					$this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
				}
				$this->db->where('employments.isBiometric', 1);
				$this->db->where('employments.status', 1);

				$data['records'] = $this->db->get();
				
				// analyze attendance
				$this->db->where('payrollPeriodID', $data['payrollPeriodID']);
				$payroll = $this->db->get('payroll_periods', 1)->row();
				//-----------------------------------------------------------------------------------------------------
				
				$start      = strtotime($payroll->startDate);
				$end        = strtotime($payroll->endDate);
				
				$data['log']= array();
				
				if ($data['records']->num_rows()) {
					foreach ($data['records']->result() as $row) {
						$res = array();
						$res['employmentID']= $row->employmentID;
						$res['empNo'] 		= $row->empNo;
						$res['employee'] 	= $row->lname.', '.$row->fname.' '.substr($row->mname, 0, 1).' '.$row->suffix;
						$res['position'] 	= $row->jobTitle;
						$res['basicSalary'] = $row->basicSalary;
						$res['ut_count'] 	= 0;
						$res['ut_min']   	= 0;
						$res['ut_date']  	= "";
						for ($current = $start; $current <= $end; $current = strtotime('+1 day', $current)) {
							$dtr = $this->dtrlog->analyze($row->employmentID, $current);
							if (!empty ($dtr)) {
								foreach ($dtr as $info) { $att[] = $dtr;
									if (strlen($info['ut_date']) > 5) {
										$res['ut_count']  += 2;
									} elseif (intval($info['ut_date']) > 0) {
										$res['ut_count']  += 1;
									}
									$res['ut_min']    += intval($info['undertime']);
									$res['ut_date']   .= ($info['ut_date']) ? ' '.$info['ut_date'] : '';
								}
							}
						}
						
						if ($res['ut_count'] > 0) {
							$data['log'][] = $res;
						}
					}
				}
	
				// sorting
				for($r=0; $r<count($data['log']); $r++) {
					for($c=$r+1; $c<count($data['log']); $c++) {
						if ($data['log'][$r]['ut_count'] < $data['log'][$c]['ut_count']) {
							// swap
							$temp = array();
							$temp = $data['log'][$r];
							$data['log'][$r] = $data['log'][$c];
							$data['log'][$c] = $temp;
						}
					}
				}	
			// load views
			$this->load->view("header", $data);
			$this->load->view($this->module_path."/undertime_summary", $data);
			$this->load->view("footer");
		
		} else {
			// no access this page
			$data ['class'] = "danger";
			$data ['msg'] = "Sorry, you don't have access to this page!";
			$data ['urlredirect'] = "";
			$this->load->view ( 'header', $data );
			$this->load->view ( 'message' );
			$this->load->view ( 'footer' );
		}
	}
	
	public function notice_to_credit($branchID = 0, $deptID = 0, $divisionID = 0, $attendanceID = 0, $payrollGroupID = 0, $employeeTypeID = 0, $limit = 100, $offset = 0) {
		$this->submenu ();
		$data = $this->data;
		
		// check roles
		if ($this->roles ['create']) {
			$data['required_fields'] = array('companyID'=>'Company','attendanceID'=>'Attendance Period'
			);
				$data['branchID']  			= $this->input->post('branchID');
				$data['deptID']				= $this->input->post('deptID');
				$data['divisionID'] 		= $this->input->post('divisionID');
				$data['payrollPeriodID'] 	= $this->input->post('payrollPeriodID');
				$data['payrollGroupID'] 	= $this->input->post('payrollGroupID');
				$data['attendanceID']  		= $this->input->post('attendanceID');
				$data['employeeTypeID'] 	= $this->input->post('employeeTypeID');
				$data['limit'] 				= $this->input->post('limit');
				$data['offset']     		= 0;
				
				// set sessions
				$this->session->set_userdata('current_branchID', $data['branchID']);
				$this->session->set_userdata('current_deptID', $data['deptID']);
				$this->session->set_userdata('current_divisionID', $data['divisionID']);
				$this->session->set_userdata('current_payrollGroupID', $data['payrollGroupID']);
				$this->session->set_userdata('current_attendanceID', $data['attendanceID']);
				$this->session->set_userdata('current_employeeTypeID', $data['employeeTypeID']);
				$this->session->set_userdata('current_limit', $data['limit']);
				$this->session->set_userdata('current_offset', $data['offset']);
												
				$this->db->where('employments.branchID', $data['branchID']);
				if ($data['deptID']) {
					$this->db->where('employments.deptID', $data['deptID']);
				}
				if ($data['divisionID']) {
					$this->db->where('employments.divisionID', $data['divisionID']);
				}
				if ($data['payrollGroupID']) {
					$this->db->where('employments.payrollGroupID', $data['payrollGroupID']);
				}
				if (!empty($data['employeeTypeID'])) {
					$this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
				}
				$this->db->where('employments.isBiometric', 1);
				$this->db->where('employments.status', 1);
				
				// get
				$data['ttl_rows'] = $config['total_rows'] = $this->db->count_all_results('employments');
				
				$employeeTypeID = 0;
				if (!empty($data['employeeTypeID'])) {
					$employeeTypeID = implode('_', $data['employeeTypeID']);
				}
				
				// set pagination
				$config['base_url'] 	= $this->controller_page."/attendance_summary/".$data['branchID']."/".$data['deptID']."/".$data['divisionID']."/".$data['payrollGroupID']."/".$data['attendanceID']."/".$employeeTypeID."/".$data['limit']."/";
				$config['per_page'] 	= $data['limit'];
				$config['uri_segment'] 	= 10;
				$this->pagination->initialize($config);			
				
				$this->db->select('employments.*');
				$this->db->select('employees.empNo');
				$this->db->select('employees.fname');
				$this->db->select('employees.suffix');
				$this->db->select('job_positions.positionCode');
    			$this->db->select('job_titles.jobTitle');
				$this->db->from('employments');
				$this->db->join('employees','employments.empID=employees.empID','left');
				$this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
				$this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
				$this->db->where('employments.branchID', $data['branchID']);
				if ($data['deptID']) {
					$this->db->where('employments.deptID', $data['deptID']);
				}
				if ($data['divisionID']) {
					$this->db->where('employments.divisionID', $data['divisionID']);
				}
				if ($data['payrollGroupID']) {
					$this->db->where('employments.payrollGroupID', $data['payrollGroupID']);
				}
				if (!empty($data['employeeTypeID'])) {
					$this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
				}
				$this->db->where('employments.isBiometric', 1);
				$this->db->where('employments.status', 1);
				
				if ($data['limit']) {
					if ($data['offset']) {
						$this->db->limit($data['limit'],$data['offset']);
					} else {
						$this->db->limit($data['limit']);
					}
				}
				
				$data['records'] = $this->db->get();
				
				
				// analyze attendance
				$this->db->where('payrollPeriodID', $data['payrollPeriodID']);
				$payroll = $this->db->get('payroll_periods', 1)->row();
				
				$start      = strtotime($payroll->startDate);
				$end        = strtotime($payroll->endDate);
				$data['log']= array();
				
				if ($data['records']->num_rows()) {
					foreach ($data['records']->result() as $row) {
						$res['tardy_count'] = 0;
						$res['tardy_min']   = 0;
						$res['tardy_date']  = "";
						$res['ut_count'] 	= 0;
						$res['ut_min']   	= 0;
						$res['ut_date']  	= "";
						$res['absent_count']= 0;
						$res['absent_date'] = "";
						$res['SUSPN']	= 0;
						$res['ORDR']	= 0;
						$res['SL']		= 0;
						$res['VL']		= 0;
						$res['SPL']		= 0;
						
						for ($current = $start; $current <= $end; $current = strtotime('+1 day', $current)) {
							$dtr = $this->dtrlog->analyze($row->employmentID, $current);													
							
							if (!empty ($dtr)) {
								foreach ($dtr as $info) { 
									// tardiness
									if (strlen($info['tardy_date']) > 5) {
										$res['tardy_count']  += 2;
									} elseif (intval($info['tardy']) > 0) {
										$res['tardy_count']  += 1;
									}
									$res['tardy_min']    += intval($info['tardy']);
									$res['tardy_date']   .= ($info['tardy_date']) ? ' '.$info['tardy_date'] : '';
									
									// undertime
									if (strlen($info['ut_date']) > 5) {
										$res['ut_count']  += 2;
									} elseif (intval($info['ut_date']) > 0) {
										$res['ut_count']  += 1;
									}
									$res['ut_min']   	 += intval($info['undertime']);
									$res['ut_date']  	 .= ($info['ut_date']) ? ' '.$info['ut_date'] : '';
									
									// absences
									$res['absent_count'] += ($info['remarks'] == 'ABSENT') ? 1 : 0;
									$res['absent_date']  .= ($info['remarks'] == 'ABSENT') ? date(' j', strtotime($info['base'])) : '';
									
									// leaves, orders, suspension
									$res[$info['los']]   += ($info['los']) ? $info['los_day'] : 0;
									
								}
							}
						}
						$data['log'][$row->employmentID] = $res;
					}
				}				
				$this->db->order_by('rank', 'asc');
				$this->db->order_by('code', 'asc');
				$data['leave_types'] =  $this->db->get('leave_types');
				
			// load views
			$this->load->view("header", $data);
			$this->load->view($this->module_path."/notice_to_credit", $data);
			$this->load->view("footer");
		
		} else {
			// no access this page
			$data ['class'] = "danger";
			$data ['msg'] = "Sorry, you don't have access to this page!";
			$data ['urlredirect'] = "";
			$this->load->view ( 'header', $data );
			$this->load->view ( 'message' );
			$this->load->view ( 'footer' );
		}
	}
	public function tax_alpha_list($branchID = 0, $deptID = 0, $divisionID = 0, $attendanceID = 0, $payrollGroupID = 0, $employeeTypeID = 0, $limit = 100, $offset = 0) {
		$this->submenu ();
		$data = $this->data;
		
		// check roles
		if ($this->roles ['create']) {
			$data['required_fields'] = array('companyID'=>'Company','attendanceID'=>'Attendance Period'
			);
				$data['branchID']  			= $this->input->post('branchID');
				$data['deptID']				= $this->input->post('deptID');
				$data['divisionID'] 		= $this->input->post('divisionID');
				$data['payrollPeriodID'] 	= $this->input->post('payrollPeriodID');
				$data['payrollGroupID'] 	= $this->input->post('payrollGroupID');
				$data['attendanceID']  		= $this->input->post('attendanceID');
				$data['employeeTypeID'] 	= $this->input->post('employeeTypeID');
				$data['limit'] 				= $this->input->post('limit');
				$data['offset']     		= 0;
				
				// set sessions
				$this->session->set_userdata('current_branchID', $data['branchID']);
				$this->session->set_userdata('current_deptID', $data['deptID']);
				$this->session->set_userdata('current_divisionID', $data['divisionID']);
				$this->session->set_userdata('current_payrollGroupID', $data['payrollGroupID']);
				$this->session->set_userdata('current_attendanceID', $data['attendanceID']);
				$this->session->set_userdata('current_employeeTypeID', $data['employeeTypeID']);
				$this->session->set_userdata('current_limit', $data['limit']);
				$this->session->set_userdata('current_offset', $data['offset']);
												
				$this->db->where('employments.branchID', $data['branchID']);
				if ($data['deptID']) {
					$this->db->where('employments.deptID', $data['deptID']);
				}
				if ($data['divisionID']) {
					$this->db->where('employments.divisionID', $data['divisionID']);
				}
				if ($data['payrollGroupID']) {
					$this->db->where('employments.payrollGroupID', $data['payrollGroupID']);
				}
				if (!empty($data['employeeTypeID'])) {
					$this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
				}
				$this->db->where('employments.isBiometric', 1);
				$this->db->where('employments.status', 1);
				
				// get
				$data['ttl_rows'] = $config['total_rows'] = $this->db->count_all_results('employments');
				
				$employeeTypeID = 0;
				if (!empty($data['employeeTypeID'])) {
					$employeeTypeID = implode('_', $data['employeeTypeID']);
				}
				
				// set pagination
				$config['base_url'] 	= $this->controller_page."/attendance_summary/".$data['branchID']."/".$data['deptID']."/".$data['divisionID']."/".$data['payrollGroupID']."/".$data['attendanceID']."/".$employeeTypeID."/".$data['limit']."/";
				$config['per_page'] 	= $data['limit'];
				$config['uri_segment'] 	= 10;
				$this->pagination->initialize($config);			
				
				$this->db->select('employments.*');
				$this->db->select('employees.empNo');
				$this->db->select('employees.fname');
				$this->db->select('employees.suffix');
				$this->db->select('job_positions.positionCode');
    			$this->db->select('job_titles.jobTitle');
				$this->db->from('employments');
				$this->db->join('employees','employments.empID=employees.empID','left');
				$this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
				$this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
				$this->db->where('employments.branchID', $data['branchID']);
				if ($data['deptID']) {
					$this->db->where('employments.deptID', $data['deptID']);
				}
				if ($data['divisionID']) {
					$this->db->where('employments.divisionID', $data['divisionID']);
				}
				if ($data['payrollGroupID']) {
					$this->db->where('employments.payrollGroupID', $data['payrollGroupID']);
				}
				if (!empty($data['employeeTypeID'])) {
					$this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
				}
				$this->db->where('employments.isBiometric', 1);
				$this->db->where('employments.status', 1);
				
				if ($data['limit']) {
					if ($data['offset']) {
						$this->db->limit($data['limit'],$data['offset']);
					} else {
						$this->db->limit($data['limit']);
					}
				}
				
				$data['records'] = $this->db->get();
				// analyze attendance
				$this->db->where('payrollPeriodID', $data['payrollPeriodID']);
				$payroll = $this->db->get('payroll_periods', 1)->row();
				
				$start      = strtotime($payroll->startDate);
				$end        = strtotime($payroll->endDate);
				$data['log']= array();
				
				if ($data['records']->num_rows()) {
					foreach ($data['records']->result() as $row) {
						$res['tardy_count'] = 0;
						$res['tardy_min']   = 0;
						$res['tardy_date']  = "";
						$res['ut_count'] 	= 0;
						$res['ut_min']   	= 0;
						$res['ut_date']  	= "";
						$res['absent_count']= 0;
						$res['absent_date'] = "";
						$res['SUSPN']	= 0;
						$res['ORDR']	= 0;
						$res['SL']		= 0;
						$res['VL']		= 0;
						$res['SPL']		= 0;
						
						for ($current = $start; $current <= $end; $current = strtotime('+1 day', $current)) {
							$dtr = $this->dtrlog->analyze($row->employmentID, $current);													
							
							if (!empty ($dtr)) {
								foreach ($dtr as $info) { 
									// tardiness
									if (strlen($info['tardy_date']) > 5) {
										$res['tardy_count']  += 2;
									} elseif (intval($info['tardy']) > 0) {
										$res['tardy_count']  += 1;
									}
									$res['tardy_min']    += intval($info['tardy']);
									$res['tardy_date']   .= ($info['tardy_date']) ? ' '.$info['tardy_date'] : '';
									
									// undertime
									if (strlen($info['ut_date']) > 5) {
										$res['ut_count']  += 2;
									} elseif (intval($info['ut_date']) > 0) {
										$res['ut_count']  += 1;
									}
									$res['ut_min']   	 += intval($info['undertime']);
									$res['ut_date']  	 .= ($info['ut_date']) ? ' '.$info['ut_date'] : '';
									
									// absences
									$res['absent_count'] += ($info['remarks'] == 'ABSENT') ? 1 : 0;
									$res['absent_date']  .= ($info['remarks'] == 'ABSENT') ? date(' j', strtotime($info['base'])) : '';
									
									// leaves, orders, suspension
									$res[$info['los']]   += ($info['los']) ? $info['los_day'] : 0;
									
								}
							}
						}
						$data['log'][$row->employmentID] = $res;
					}
				}				
				$this->db->order_by('rank', 'asc');
				$this->db->order_by('code', 'asc');
				$data['leave_types'] =  $this->db->get('leave_types');
				
			// load views
			$this->load->view("header", $data);
			$this->load->view($this->module_path."/tax_alpha_list", $data);
			$this->load->view("footer");
		
		} else {
			// no access this page
			$data ['class'] = "danger";
			$data ['msg'] = "Sorry, you don't have access to this page!";
			$data ['urlredirect'] = "";
			$this->load->view ( 'header', $data );
			$this->load->view ( 'message' );
			$this->load->view ( 'footer' );
		}
	}
	
	public function print_attendance_summary($branchID = 0, $deptID = 0, $divisionID = 0, $attendanceID = 0, $payrollGroupID = 0, $employeeTypeID = 0, $limit = 100, $offset = 0)
	{
		//************** general settings *******************
		// load submenu
		$this->submenu();
		$data = $this->data;
	
		$data['title'] 		= "Summary of Attendance";
		// **************************************************
	
		// check roles
		//if ($this->roles['attendance_summary']) {
			$data['branchID']  		= $this->encrypter->decode($branchID);
			$data['deptID']			= $this->encrypter->decode($deptID);
			$data['divisionID'] 	= $this->encrypter->decode($divisionID);
			$data['payrollGroupID'] = $this->encrypter->decode($payrollGroupID);
			$data['attendanceID'] 	= $this->encrypter->decode($attendanceID);
			$data['employeeTypeID'] = explode('_', $employeeTypeID);
	
			$this->db->where('employments.branchID', $data['branchID']);
			$this->db->where('employments.deptID', $data['deptID']);
			if ($data['divisionID']) {
				$this->db->where('employments.divisionID', $data['divisionID']);
			}
			if ($data['payrollGroupID']) {
				$this->db->where('employments.payrollGroupID', $data['payrollGroupID']);
			}
			if (!empty($data['employeeTypeID'])) {
				$this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
			}
			$this->db->where('employments.isBiometric', 1);
			$this->db->where('employments.status', 1);
			
			// get
			$data['ttl_rows'] = $config['total_rows'] = $this->db->count_all_results('employments');
			
			$employeeTypeID = 0;
			if (!empty($data['employeeTypeID'])) {
				$employeeTypeID = implode('_', $data['employeeTypeID']);
			}
			
			// set pagination
			$config['base_url'] 	= $this->controller_page."/attendance_summary/".$this->encrypter->decode($data['branchID'])."/".$this->encrypter->decode($data['deptID'])."/".$this->encrypter->decode($data['divisionID'])."/".$this->encrypter->decode($data['payrollGroupID'])."/".$this->encrypter->decode($data['attendanceID'])."/".$employeeTypeID."/".$data['limit']."/";
			$config['per_page'] 	= $data['limit'];
			$config['uri_segment'] 	= 10;
			$this->pagination->initialize($config);
			
			$this->db->select('employments.*');
			$this->db->select('employees.empNo');
			$this->db->select('employees.fname');
			$this->db->select('employees.suffix');
			$this->db->select('job_positions.positionCode');
    		$this->db->select('job_titles.jobTitle');
			$this->db->from('employments');
			$this->db->join('employees','employments.empID=employees.empID','left');
			$this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
			$this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
			$this->db->where('employments.branchID', $data['branchID']);
			$this->db->where('employments.deptID', $data['deptID']);
			if ($data['divisionID']) {
				$this->db->where('employments.divisionID', $data['divisionID']);
			}
			if ($data['payrollGroupID']) {
				$this->db->where('employments.payrollGroupID', $data['payrollGroupID']);
			}
			if (!empty($data['employeeTypeID'])) {
				$this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
			}
			$this->db->where('employments.isBiometric', 1);
			$this->db->where('employments.status', 1);
			
			if ($data['limit']) {
				if ($data['offset']) {
					$this->db->limit($data['limit'],$data['offset']);
				} else {
					$this->db->limit($data['limit']);
				}
			}
			
			$data['records'] = $this->db->get();
			
			// analyze attendance
			$this->db->where('payrollPeriodID', $data['attendanceID']);
			$payroll = $this->db->get('payroll_periods', 1)->row();
			
			$start      = strtotime($payroll->startDate);
			$end        = strtotime($payroll->endDate);
			$data['log']= array();
				
			if ($data['records']->num_rows()) {
				foreach ($data['records']->result() as $row) {
					$res['tardy_count'] = 0;
					$res['tardy_min']   = 0;
					$res['tardy_date']  = "";
					$res['ut_count'] 	= 0;
					$res['ut_min']   	= 0;
					$res['ut_date']  	= "";
					$res['absent_count']= 0;
					$res['absent_date'] = "";
					for ($current = $start; $current <= $end; $current = strtotime('+1 day', $current)) {
						$dtr = $this->dtrlog->analyze($row->employmentID, $current);
							
						if (!empty ($dtr)) {
							foreach ($dtr as $info) {
								// tardiness
								if (strlen($info['tardy_date']) > 5) {
									$res['tardy_count']  += 2;
								} elseif (intval($info['tardy']) > 0) {
									$res['tardy_count']  += 1;
								}
								$res['tardy_min']    += intval($info['tardy']);
								$res['tardy_date']   .= ($info['tardy_date']) ? ' '.$info['tardy_date'] : '';
									
								// undertime
								if (strlen($info['ut_date']) > 5) {
									$res['ut_count']  += 2;
								} elseif (intval($info['ut_date']) > 0) {
									$res['ut_count']  += 1;
								}
								$res['ut_min']   	 += intval($info['undertime']);
								$res['ut_date']  	 .= ($info['ut_date']) ? ' '.$info['ut_date'] : '';
									
								// absences
								$res['absent_count'] += ($info['remarks'] == 'ABSENT') ? 1 : 0;
								$res['absent_date']  .= ($info['remarks'] == 'ABSENT') ? date(' j', strtotime($info['base'])) : '';
									
								// leaves, orders, suspension
								$res[$info['los']]   += ($info['los']) ? $info['los_day'] : 0;
									
							}
						}
					}
					$data['log'][$row->employmentID] = $res;
				}
			}
			
			$this->db->order_by('rank', 'asc');
			$this->db->order_by('code', 'asc');
			$data['leave_types'] =  $this->db->get('leave_types');
			
			$this->db->where('branchID', $data['branchID']);
			$company = $this->db->get('branches', 1)->row();
			
			$this->db->where('deptID', $data['deptID']);
			$office = $this->db->get('departments', 1)->row();					
			
			$this->db->where('divisionID', $data['divisionID']);
			$division = $this->db->get('divisions', 1)->row();
			
			$this->db->where('payrollPeriodID', $data['attendanceID']);
			$payroll = $this->db->get('payroll_periods', 1)->row();
			
			$data['pdf_paging'] = TRUE;						
			$data['title']      = "SUMMARY OF ATTENDANCE";
			$data['modulename'] = "SUMMARY OF ATTENDANCE";
			$data['subnote']    = $office->deptName;
			if (!empty($division)) {
				$data['subnote2']   = $division->divisionName;
				$data['subnote3']   = $payroll->payrollPeriod;
			} else {
				$data['subnote2']   = $payroll->payrollPeriod;
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
				
			$html 	= $this->load->view($this->module_path.'/print_attendance_summary', $data, TRUE);
			$this->mpdf->WriteHTML($html);
				
			$this->mpdf->Output("SUMMARY_OF_ATTENDANCE.pdf","I");
		//} else {
		//	$data["display"] = "block";
		//	$data["class"] 	 = "errorbox";
		//	$data["msg"] 	 = "Sorry, you don't have access to this page!";
		//	$data["urlredirect"] = "";
		//	$this->load->view("print_header",$data);
		//	$this->load->view("message",$data);
		//	$this->load->view("print_footer");
		//}
	}
	public function print_tardiness_summary($branchID = 0, $deptID = 0, $divisionID = 0, $payrollGroupID = 0, $attendanceID = 0, $employeeTypeID = 0)
	{
		//************** general settings *******************
		// load submenu
		$this->submenu();
		$data = $this->data;
	
		$data['title'] 		= "Summary of Tardiness";
		// **************************************************
	
		// check roles
		//if ($this->roles['tardiness_summary']) {
			$data['branchID']  		= $this->encrypter->decode($branchID);
			$data['deptID']			= $this->encrypter->decode($deptID);
			$data['divisionID'] 	= $this->encrypter->decode($divisionID);
			$data['payrollGroupID'] = $this->encrypter->decode($payrollGroupID);
			$data['attendanceID'] 	= $this->encrypter->decode($attendanceID);
			$data['employeeTypeID'] = explode('_', $employeeTypeID);
	
			$this->db->where('employments.branchID', $data['branchID']);
			$this->db->where('employments.deptID', $data['deptID']);
			if ($data['divisionID']) {
				$this->db->where('employments.divisionID', $data['divisionID']);
			}
			if ($data['payrollGroupID']) {
				$this->db->where('employments.payrollGroupID', $data['payrollGroupID']);
			}
			if (!empty($data['employeeTypeID'])) {
				$this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
			}
			$this->db->where('employments.isBiometric', 1);
			$this->db->where('employments.status', 1);
			
			// get
			$data['ttl_rows'] = $config['total_rows'] = $this->db->count_all_results('employments');
				
			$employeeTypeID = 0;
			if (!empty($data['employeeTypeID'])) {
				$employeeTypeID = implode('_', $data['employeeTypeID']);
			}
				
			// set pagination
			$config['base_url'] 	= $this->controller_page."/tardiness_summary/".$this->encrypter->decode($data['branchID'])."/".$this->encrypter->decode($data['deptID'])."/".$this->encrypter->decode($data['divisionID'])."/".$this->encrypter->decode($data['payrollGroupID'])."/".$this->encrypter->decode($data['attendanceID'])."/".$employeeTypeID."/".$data['limit']."/";
			$config['per_page'] 	= $data['limit'];
			$config['uri_segment'] 	= 10;
			$this->pagination->initialize($config);
			
			$this->db->select('employments.*');
			$this->db->select('employees.empNo');
			$this->db->select('employees.fname');
			$this->db->select('employees.suffix');
			$this->db->select('job_positions.positionCode');
    		$this->db->select('job_titles.jobTitle');
			$this->db->from('employments');
			$this->db->join('employees','employments.empID=employees.empID','left');
			$this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
			$this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
			$this->db->where('employments.branchID', $data['branchID']);
			$this->db->where('employments.deptID', $data['deptID']);
			if ($data['divisionID']) {
				$this->db->where('employments.divisionID', $data['divisionID']);
			}
			if ($data['payrollGroupID']) {
				$this->db->where('employments.payrollGroupID', $data['payrollGroupID']);
			}
			if (!empty($data['employeeTypeID'])) {
				$this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
			}
			$this->db->where('employments.isBiometric', 1);
			$this->db->where('employments.status', 1);
			
			$data['records'] = $this->db->get();

			// analyze attendance
			$this->db->where('payrollPeriodID', $data['attendanceID']);
			$payroll = $this->db->get('payroll_periods', 1)->row();

			$start      = strtotime($payroll->startDate);
			$end        = strtotime($payroll->endDate);
			$data['log']= array();
				
			if ($data['records']->num_rows()) {
				foreach ($data['records']->result() as $row) {
					$res = array();
					$res['employmentID']= $row->employmentID;
					$res['empNo'] 		= $row->empNo;
					$res['employee'] 	= $row->lname.', '.$row->fname.' '.substr($row->mname, 0, 1).' '.$row->suffix;
					$res['position'] 	= $row->jobTitle;
					$res['basicSalary'] = $row->basicSalary;
					$res['tardy_count'] = 0;
					$res['tardy_min']   = 0;
					$res['tardy_date']  = "";
					for ($current = $start; $current <= $end; $current = strtotime('+1 day', $current)) {
						$dtr = $this->dtrlog->analyze($row->employmentID, $current);
							
						if (!empty ($dtr)) {
							foreach ($dtr as $info) { $att[] = $dtr;
								if (strlen($info['tardy_date']) > 5) { 
									$res['tardy_count']  += 2;
								} elseif (intval($info['tardy']) > 0) {
									$res['tardy_count']  += 1;
								}
								$res['tardy_min']    += intval($info['tardy']);
								$res['tardy_date']   .= ($info['tardy_date']) ? ' '.$info['tardy_date'] : '';															
							}
						}
					}
					if ($res['tardy_count'] > 0) {
						$data['log'][] = $res;
					}
				}
			}

			// sorting
			for($r=0; $r<count($data['log']); $r++) {
				for($c=$r+1; $c<count($data['log']); $c++) {
					if ($data['log'][$r]['tardy_count'] < $data['log'][$c]['tardy_count']) {
						// swap
						$temp = array();
						$temp = $data['log'][$r];
						$data['log'][$r] = $data['log'][$c];
						$data['log'][$c] = $temp;
					}
				}
			}
				
			$this->db->where('branchID', $data['branchID']);
			$company = $this->db->get('branches', 1)->row();
				
			$this->db->where('deptID', $data['deptID']);
			$office = $this->db->get('departments', 1)->row();
				
			$this->db->where('divisionID', $data['divisionID']);
			$division = $this->db->get('divisions', 1)->row();
				
			$this->db->where('payrollPeriodID', $data['attendanceID']);
			$payroll = $this->db->get('payroll_periods', 1)->row();
				
			$data['pdf_paging'] = TRUE;
			$data['title']      = "SUMMARY OF TARDINESS";
			$data['modulename'] = "SUMMARY OF TARDINESS";
			$data['subnote']    = $office->officeName;
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
	
			$html 	= $this->load->view($this->module_path.'/print_tardiness_summary', $data, TRUE);
			$this->mpdf->WriteHTML($html);
	
			$this->mpdf->Output("SUMMARY_OF_TARDINESS.pdf","I");
		//} else {
		//	$data["display"] = "block";
		//	$data["class"] 	 = "errorbox";
		//	$data["msg"] 	 = "Sorry, you don't have access to this page!";
		//	$data["urlredirect"] = "";
		//	$this->load->view("print_header",$data);
		//	$this->load->view("message",$data);
		//	$this->load->view("print_footer");
		//}
	}
	public function create() {
		$this->submenu ();
		$data = $this->data;
		
		// check roles
		if ($this->roles ['create']) {
			
			// load views
			$this->load->view ( 'header', $data );
			$this->load->view ( $this->module_path . '/create' );
			$this->load->view ( 'footer' );
		
		} else {
			// no access this page
			$data ['class'] = "danger";
			$data ['msg'] = "Sorry, you don't have access to this page!";
			$data ['urlredirect'] = "";
			$this->load->view ( 'header', $data );
			$this->load->view ( 'message' );
			$this->load->view ( 'footer' );
		}
	}
	
	public function save() {
		//load submenu
		$this->submenu ();
		$data = $this->data;
		$table_fields = array ('shiftName', 'shiftType', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun','workHours', 'remarks');
		
		// check role
		if ($this->roles ['create']) {
			$this->records->table = $this->table;
			$this->records->fields = array ();
			
			foreach ( $table_fields as $fld ) {
				$this->records->fields [$fld] = trim ( $this->input->post ( $fld ) );
			}
			
			$this->records->fields['in1']      		= date('H:i:s', strtotime($this->input->post('tlin')));
            $this->records->fields['out1']   		= date('H:i:s', strtotime($this->input->post('tlout')));
			$this->records->fields['startTardy1']	= date('H:i:s', strtotime($this->input->post('tlta')));
            $this->records->fields['startLog1']		= date('H:i:s', strtotime($this->input->post('tlel')));
            
            $this->records->fields['in2']			= date('H:i:s', strtotime($this->input->post('spin')));
            $this->records->fields['out2']			= date('H:i:s', strtotime($this->input->post('spout')));
            $this->records->fields['startTardy2']	= date('H:i:s', strtotime($this->input->post('spta')));
            $this->records->fields['startLog2']		= date('H:i:s', strtotime($this->input->post('spel')));
			
			if ($this->records->save ()) {
				$this->records->fields = array ();
				$id = $this->records->where ['shiftID'] = $this->db->insert_id ();
				$this->records->retrieve ();
				// record logs
				$logs = "Record - " . trim ( $this->input->post ( $this->logfield ) );
				$this->log_model->table_logs ( $data ['current_module'] ['module_label'], $this->table, $this->pfield, $this->records->field->$data ['pfield'], 'Insert', $logs );
				
				$logfield = $this->pfield;
				// success msg
				$data ["class"] = "success";
				$data ["msg"] = $this->data ['current_module'] ['module_label'] . " successfully saved.";
				$data ["urlredirect"] = $this->controller_page . "/view/" . $this->encrypter->encode ( $id );
				$this->load->view ( "header", $data );
				$this->load->view ( "message" );
				$this->load->view ( "footer" );
			
			} else {
				// error
				$data ["class"] = "danger";
				$data ["msg"] = "Error in saving the " . $this->data ['current_module'] ['module_label'] . "!";
				$data ["urlredirect"] = "";
				$this->load->view ( "header", $data );
				$this->load->view ( "message" );
				$this->load->view ( "footer" );
			}
		} else {
			// error
			$data ["class"] = "danger";
			$data ["msg"] = "Sorry, you don't have access to this page!";
			$data ["urlredirect"] = "";
			$this->load->view ( "header", $data );
			$this->load->view ( "message" );
			$this->load->view ( "footer" );
		}
	}
	
	public function edit($id) {
		$this->submenu ();
		$data = $this->data;
		$id = $this->encrypter->decode ( $id );
		
		if ($this->roles ['edit']) {
			// for retrieve with joining tables -------------------------------------------------
			// set table
			$this->records->table = $this->table;
			// set fields for the current table
			$this->records->setFields ();
			// set where
			$this->records->where [$this->pfield] = $id;
			// execute retrieve
			$this->records->retrieve ();
			// ----------------------------------------------------------------------------------
			$data ['rec'] = $this->records->field;
			
			// load views
			$this->load->view ( 'header', $data );
			$this->load->view ( $this->module_path . '/edit' );
			$this->load->view ( 'footer' );
		} else {
			// no access this page
			$data ['class'] = "danger";
			$data ['msg'] = "Sorry, you don't have access to this page!";
			$data ['urlredirect'] = "";
			$this->load->view ( 'header', $data );
			$this->load->view ( 'message' );
			$this->load->view ( 'footer' );
		}
	}
	
	public function update() {
		// load submenu
		$this->submenu ();
		$data = $this->data;
		$table_fields = array ('shiftName', 'shiftType', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun','workHours', 'remarks','status');
		
		// check roles
		if ($this->roles ['edit']) {
			$this->records->table = $this->table;
			$this->records->fields = array ();
			
			foreach ( $table_fields as $fld ) {
				$this->records->fields [$fld] = trim ( $this->input->post ( $fld ) );
			}
			
			$this->records->fields['in1']      		= date('H:i:s', strtotime($this->input->post('tlin')));
            $this->records->fields['out1']   		= date('H:i:s', strtotime($this->input->post('tlout')));
			$this->records->fields['startTardy1']	= date('H:i:s', strtotime($this->input->post('tlta')));
            $this->records->fields['startLog1']		= date('H:i:s', strtotime($this->input->post('tlel')));
            
            $this->records->fields['in2']			= date('H:i:s', strtotime($this->input->post('spin')));
            $this->records->fields['out2']			= date('H:i:s', strtotime($this->input->post('spout')));
            $this->records->fields['startTardy2']	= date('H:i:s', strtotime($this->input->post('spta')));
            $this->records->fields['startLog2']		= date('H:i:s', strtotime($this->input->post('spel')));
			
			$this->records->pfield = $this->pfield;
			$this->records->pk = $this->encrypter->decode ( $this->input->post ( $this->pfield ) );
			
			// field logs here
			$wasChange = $this->log_model->field_logs ( $data ['current_module'] ['module_label'], $this->table, $this->pfield, $this->encrypter->decode ( $this->input->post ( $this->pfield ) ), 'Update', $this->records->fields );
			
			if ($this->records->update ()) {
				// record logs
				if ($wasChange) {
					$logs = "Record - " . trim ( $this->input->post ( $this->logfield ) );
					$this->log_model->table_logs ( $data ['current_module'] ['module_label'], $this->table, $this->pfield, $this->records->pk, 'Update', $logs );
				}
				
				// successful
				$data ["class"] = "success";
				$data ["msg"] = $this->data ['current_module'] ['module_label'] . " successfully updated.";
				$data ["urlredirect"] = $this->controller_page . "/view/" . trim ( $this->input->post ( $this->pfield ) );
				$this->load->view ( "header", $data );
				$this->load->view ( "message" );
				$this->load->view ( "footer" );
			} else {
				// error
				$data ["class"] = "danger";
				$data ["msg"] = "Error in updating the " . $this->data ['current_module'] ['module_label'] . "!";
				$data ["urlredirect"] = $this->controller_page . "/view/" . $this->records->pk;
				$this->load->view ( "header", $data );
				$this->load->view ( "message" );
				$this->load->view ( "footer" );
			}
		} else {
			// error
			$data ["class"] = "danger";
			$data ["msg"] = "Sorry, you don't have access to this page!";
			$data ["urlredirect"] = "";
			$this->load->view ( "header", $data );
			$this->load->view ( "message" );
			$this->load->view ( "footer" );
		}
	}
	
	public function delete($id = 0) {
		// load submenu
		$this->submenu ();
		$data = $this->data;
		$id = $this->encrypter->decode ( $id );
		
		// check roles
		if ($this->roles ['delete']) {
			// set fields
			$this->records->fields = array ();
			// set table
			$this->records->table = $this->table;
			// set where
			$this->records->where [$this->pfield] = $id;
			// execute retrieve
			$this->records->retrieve ();
			
			if (! empty ( $this->records->field )) {
				$this->records->pfield = $this->pfield;
				$this->records->pk = $id;
				
				// record logs
				$rec_value = $this->records->field->name;
				
				// check if in used
				if (! $this->_in_used ( $id )) {
					if ($this->records->delete ()) {
						// record logs
						$logfield = $this->logfield;
						
						$logs = "Record - " . $this->records->field->$logfield;
						$this->log_model->table_logs ( $data ['current_module'] ['module_label'], $this->table, $this->pfield, $this->records->pk, 'Delete', $logs );
						
						// successful
						$data ["class"] = "success";
						$data ["msg"] = $this->data ['current_module'] ['module_label'] . " successfully deleted.";
						$data ["urlredirect"] = $this->controller_page . "/";
						$this->load->view ( "header", $data );
						$this->load->view ( "message" );
						$this->load->view ( "footer" );
					} else {
						// error
						$data ["class"] = "danger";
						$data ["msg"] = "Error in deleting the " . $this->data ['current_module'] ['module_label'] . "!";
						$data ["urlredirect"] = "";
						$this->load->view ( "header", $data );
						$this->load->view ( "message" );
						$this->load->view ( "footer" );
					}
				} else {
					// error
					$data ["class"] = "danger";
					$data ["msg"] = "Data integrity constraints.";
					$data ["urlredirect"] = "";
					$this->load->view ( "header", $data );
					$this->load->view ( "message" );
					$this->load->view ( "footer" );
				}
			
			} else {
				// error
				$data ["class"] = "danger";
				$data ["msg"] = $this->module . " record not found!";
				$data ["urlredirect"] = "";
				$this->load->view ( "header", $data );
				$this->load->view ( "message" );
				$this->load->view ( "footer" );
			}
		} else {
			echo "no roles";
			// error
			$data ["class"] = "danger";
			$data ["msg"] = "Sorry, you don't have access to this page!";
			$data ["urlredirect"] = "";
			$this->load->view ( "header", $data );
			$this->load->view ( "message" );
			$this->load->view ( "footer" );
		}
	}
	
	public function view($id) {
		$id = $this->encrypter->decode ( $id );
		
		// load submenu
		$this->submenu ();
		$data = $this->data;
		// $this->roles['view'] = 1;
		if ($this->roles ['view']) {
			// for retrieve with joining tables -------------------------------------------------
			$this->db->select ( $this->table . ".*" );
			
			$this->db->from ( $this->table );
			
			$this->db->where ( $this->pfield, $id );
			// ----------------------------------------------------------------------------------
			$data ['rec'] = $this->db->get ()->row ();
			
			$data ['in_used'] = $this->_in_used ( $id );
			
			// record logs
			if ($this->config_model->getConfig ( 'Log all record views' ) == '1') {
				$logs = "Record - " . $this->records->field->name;
				$this->log_model->table_logs ( $this->module, $this->table, $this->pfield, $this->records->field->$data ['pfield'], 'View', $logs );
			}
			
			//load views
			$this->load->view ( 'header', $data );
			$this->load->view ( $this->module_path . '/view' );
			$this->load->view ( 'footer' );
		} else {
			// no access this page
			$data ['class'] = "danger";
			$data ['msg'] = "Sorry, you don't have access to this page!";
			$data ['urlredirect'] = "";
			$this->load->view ( 'header', $data );
			$this->load->view ( 'message' );
			$this->load->view ( 'footer' );
		}
	}
	
	public function show() {
		//************** general settings *******************
		// load submenu
		$this->submenu ();
		$data = $this->data;
		
		// **************************************************
		// variable:field:default_value:operator
		// note: dont include the special query field filter                
		$condition_fields = array (array ('variable' => 'shiftName', 'field' => $this->table . '.shiftName', 'default_value' => '', 'operator' => 'like_both' ), array ('variable' => 'in1', 'field' => $this->table . '.in1', 'default_value' => '', 'operator' => 'where' ), array ('variable' => 'out1', 'field' => $this->table . '.out1', 'default_value' => '', 'operator' => 'where' ), array ('variable' => 'in2', 'field' => $this->table . '.in2', 'default_value' => '', 'operator' => 'where' ), array ('variable' => 'out2', 'field' => $this->table . '.out2', 'default_value' => '', 'operator' => 'where' ), array ('variable' => 'workHours', 'field' => $this->table . '.workHours', 'default_value' => '', 'operator' => 'where' ), array ('variable' => 'breakHours', 'field' => $this->table . '.breakHours', 'default_value' => '', 'operator' => 'where' ), array ('variable' => 'status', 'field' => $this->table . '.status', 'default_value' => '1', 'operator' => 'where' ), array ('variable' => 'rank', 'field' => $this->table . '.rank', 'default_value' => '', 'operator' => 'where' ) );
		
		// sorting fields
		$sorting_fields = array ('shiftName' => 'asc' );
		
		$controller = $this->uri->segment ( 1 );
		
		if ($this->uri->segment ( 3 ))
			$offset = $this->uri->segment ( 3 );
		else
			$offset = 0;
		
		// source of filtering
		$filter_source = 0; // default/blank
		if ($this->input->post ( 'filterflag' ) || $this->input->post ( 'sortby' )) {
			$filter_source = 1;
		} else {
			foreach ( $condition_fields as $key ) {
				if ($this->input->post ( $key ['variable'] )) {
					$filter_source = 1; // form filters
					break;
				}
			}
		}
		
		if (! $filter_source) {
			foreach ( $condition_fields as $key ) {
				if ($this->session->userdata ( $controller . '_' . $key ['variable'] ) || $this->session->userdata ( $controller . '_sortby' ) || $this->session->userdata ( $controller . '_sortorder' )) {
					$filter_source = 2; // session
					break;
				}
			}
		}
		
		switch ($filter_source) {
			case 1 :
				foreach ( $condition_fields as $key ) {
					$$key ['variable'] = trim ( $this->input->post ( $key ['variable'] ) );
				}
				
				$sortby = trim ( $this->input->post ( 'sortby' ) );
				$sortorder = trim ( $this->input->post ( 'sortorder' ) );
				
				break;
			case 2 :
				foreach ( $condition_fields as $key ) {
					$$key ['variable'] = $this->session->userdata ( $controller . '_' . $key ['variable'] );
				}
				
				$sortby = $this->session->userdata ( $controller . '_sortby' );
				$sortorder = $this->session->userdata ( $controller . '_sortorder' );
				break;
			default :
				foreach ( $condition_fields as $key ) {
					$$key ['variable'] = $key ['default_value'];
				}
				$sortby = "";
				$sortorder = "";
		}
		
		if ($this->input->post ( 'limit' )) {
			if ($this->input->post ( 'limit' ) == "All")
				$limit = "";
			else
				$limit = $this->input->post ( 'limit' );
		} else if ($this->session->userdata ( $controller . '_limit' )) {
			$limit = $this->session->userdata ( $controller . '_limit' );
		} else {
			$limit = 25; // default limit
		}
		
		// set session variables
		foreach ( $condition_fields as $key ) {
			$this->session->set_userdata ( $controller . '_' . $key ['variable'], $$key ['variable'] );
		}
		$this->session->set_userdata ( $controller . '_sortby', $sortby );
		$this->session->set_userdata ( $controller . '_sortorder', $sortorder );
		$this->session->set_userdata ( $controller . '_limit', $limit );
		
		// assign data variables for views
		foreach ( $condition_fields as $key ) {
			$data [$key ['variable']] = $$key ['variable'];
		}
		
		// select
		$this->db->select ( $this->table . '.*' );
		
		// from
		$this->db->from ( $this->table );
		
		// join
		

		// where
		// set conditions here
		foreach ( $condition_fields as $key ) {
			$operators = explode ( '_', $key ['operator'] );
			$operator = $operators [0];
			// check if the operator is like
			if (count ( $operators ) > 1) {
				// like operator
				if (trim ( $$key ['variable'] ) != '' && $key ['field'])
					$this->db->$operator ( $key ['field'], $$key ['variable'], $operators [1] );
			} else {
				if (trim ( $$key ['variable'] ) != '' && $key ['field'])
					$this->db->$operator ( $key ['field'], $$key ['variable'] );
			}
		}
		
		// get
		$data ['ttl_rows'] = $config ['total_rows'] = $this->db->count_all_results ();
		
		// set pagination   
		$config ['full_tag_open'] = "<ul class='pagination'>";
		$config ['full_tag_close'] = "</ul>";
		$config ['num_tag_open'] = "<li class='page-order'>";
		$config ['num_tag_close'] = "</li>";
		$config ['cur_tag_open'] = "<li class='page-order active'>";
		$config ['cur_tag_close'] = "</li>";
		$config ['next_tag_open'] = "<li class='page-order'>";
		$config ['next_tagl_close'] = "</li>";
		$config ['prev_tag_open'] = "<li class='page-order'>";
		$config ['prev_tagl_close'] = "</li>";
		$config ['first_tag_open'] = "<li class='page-order'>";
		$config ['first_tagl_close'] = "</li>";
		$config ['last_tag_open'] = "<li class='page-order'>";
		$config ['last_tagl_close'] = "</li>";
		
		$config ['base_url'] = $this->controller_page . '/show/';
		$config ['per_page'] = $limit;
		$this->pagination->initialize ( $config );
		
		// select
		$this->db->select ( $this->table . '.*' );
		
		// from
		$this->db->from ( $this->table );
		
		// join
		

		// where
		// set conditions here
		foreach ( $condition_fields as $key ) {
			$operators = explode ( '_', $key ['operator'] );
			$operator = $operators [0];
			// check if the operator is like
			if (count ( $operators ) > 1) {
				// like operator
				if (trim ( $$key ['variable'] ) != '' && $key ['field'])
					$this->db->$operator ( $key ['field'], $$key ['variable'], $operators [1] );
			} else {
				if (trim ( $$key ['variable'] ) != '' && $key ['field'])
					$this->db->$operator ( $key ['field'], $$key ['variable'] );
			}
		}
		
		if ($sortby && $sortorder) {
			$this->db->order_by ( $sortby, $sortorder );
			
			if (! empty ( $sorting_fields )) {
				foreach ( $sorting_fields as $fld => $s_order ) {
					if ($fld != $sortby) {
						$this->db->order_by ( $fld, $s_order );
					}
				}
			}
		} else {
			$ctr = 1;
			if (! empty ( $sorting_fields )) {
				foreach ( $sorting_fields as $fld => $s_order ) {
					if ($ctr == 1) {
						$sortby = $fld;
						$sortorder = $s_order;
					}
					$this->db->order_by ( $fld, $s_order );
					
					$ctr ++;
				}
			}
		}
		
		if ($limit) {
			if ($offset) {
				$this->db->limit ( $limit, $offset );
			} else {
				$this->db->limit ( $limit );
			}
		}
		
		// assigning variables
		$data ['sortby'] = $sortby;
		$data ['sortorder'] = $sortorder;
		$data ['limit'] = $limit;
		$data ['offset'] = $offset;
		
		// get
		$data ['records'] = $this->db->get ()->result ();
		// load views
		$this->load->view ( 'header', $data );
		$this->load->view ( $this->module_path . '/list' );
		$this->load->view ( 'footer' );
	}
	
	public function printlist() {
		// load submenu
		$this->submenu ();
		$data = $this->data;
		//sorting
		

		// variable:field:default_value:operator
		// note: dont include the special query field filter
		$condition_fields = array (array ('variable' => 'shiftName', 'field' => $this->table . '.shiftName', 'default_value' => '', 'operator' => 'like_both' ), array ('variable' => 'in1', 'field' => $this->table . '.in1', 'default_value' => '', 'operator' => 'where' ), array ('variable' => 'out1', 'field' => $this->table . '.out1', 'default_value' => '', 'operator' => 'where' ), array ('variable' => 'in2', 'field' => $this->table . '.in2', 'default_value' => '', 'operator' => 'where' ), array ('variable' => 'out2', 'field' => $this->table . '.out2', 'default_value' => '', 'operator' => 'where' ), array ('variable' => 'workHours', 'field' => $this->table . '.workHours', 'default_value' => '', 'operator' => 'where' ), array ('variable' => 'breakHours', 'field' => $this->table . '.breakHours', 'default_value' => '', 'operator' => 'where' ), array ('variable' => 'status', 'field' => $this->table . '.status', 'default_value' => '1', 'operator' => 'where' ), array ('variable' => 'rank', 'field' => $this->table . '.rank', 'default_value' => '', 'operator' => 'where' ) );
		
		// sorting fields
		$sorting_fields = array ('shiftName' => 'asc' );
		
		$controller = $this->uri->segment ( 1 );
		
		foreach ( $condition_fields as $key ) {
			$$key ['variable'] = $this->session->userdata ( $controller . '_' . $key ['variable'] );
		}
		
		$limit = $this->session->userdata ( $controller . '_limit' );
		$offset = $this->session->userdata ( $controller . '_offset' );
		$sortby = $this->session->userdata ( $controller . '_sortby' );
		$sortorder = $this->session->userdata ( $controller . '_sortorder' );
		
		// select
		$this->db->select ( $this->table . '.*' );
		
		// from
		$this->db->from ( $this->table );
		
		// join
		
		// where
		// set conditions here
		foreach ( $condition_fields as $key ) {
			$operators = explode ( '_', $key ['operator'] );
			$operator = $operators [0];
			// check if the operator is like
			if (count ( $operators ) > 1) {
				// like operator
				if (trim ( $$key ['variable'] ) != '' && $key ['field'])
					$this->db->$operator ( $key ['field'], $$key ['variable'], $operators [1] );
			} else {
				if (trim ( $$key ['variable'] ) != '' && $key ['field'])
					$this->db->$operator ( $key ['field'], $$key ['variable'] );
			}
		}
		
		if ($sortby && $sortorder) {
			$this->db->order_by ( $sortby, $sortorder );
			
			if (! empty ( $sorting_fields )) {
				foreach ( $sorting_fields as $fld => $s_order ) {
					if ($fld != $sortby) {
						$this->db->order_by ( $fld, $s_order );
					}
				}
			}
		} else {
			$ctr = 1;
			if (! empty ( $sorting_fields )) {
				foreach ( $sorting_fields as $fld => $s_order ) {
					if ($ctr == 1) {
						$sortby = $fld;
						$sortorder = $s_order;
					}
					$this->db->order_by ( $fld, $s_order );
					
					$ctr ++;
				}
			}
		}
		
		if ($limit) {
			if ($offset) {
				$this->db->limit ( $limit, $offset );
			} else {
				$this->db->limit ( $limit );
			}
		}
		
		// assigning variables
		$data ['sortby'] = $sortby;
		$data ['sortorder'] = $sortorder;
		$data ['limit'] = $limit;
		$data ['offset'] = $offset;
		
		// get
		$data ['records'] = $this->db->get ()->result ();
		
		$data ['title'] = "Shift List";
		
		//load views
		$this->load->view ( 'header_print', $data );
		$this->load->view ( $this->module_path . '/printlist' );
		$this->load->view ( 'footer_print' );
	}
	
	function exportlist() {
		// load submenu
		$this->submenu ();
		$data = $this->data;
		//sorting
		

		// variable:field:default_value:operator
		// note: dont include the special query field filter
		$condition_fields = array (array ('variable' => 'shiftName', 'field' => $this->table . '.shiftName', 'default_value' => '', 'operator' => 'like_both' ), array ('variable' => 'in1', 'field' => $this->table . '.in1', 'default_value' => '', 'operator' => 'where' ), array ('variable' => 'out1', 'field' => $this->table . '.out1', 'default_value' => '', 'operator' => 'where' ), array ('variable' => 'in2', 'field' => $this->table . '.in2', 'default_value' => '', 'operator' => 'where' ), array ('variable' => 'out2', 'field' => $this->table . '.out2', 'default_value' => '', 'operator' => 'where' ), array ('variable' => 'workHours', 'field' => $this->table . '.workHours', 'default_value' => '', 'operator' => 'where' ), array ('variable' => 'breakHours', 'field' => $this->table . '.breakHours', 'default_value' => '', 'operator' => 'where' ), array ('variable' => 'status', 'field' => $this->table . '.status', 'default_value' => '1', 'operator' => 'where' ), array ('variable' => 'rank', 'field' => $this->table . '.rank', 'default_value' => '', 'operator' => 'where' ) );
		
		// sorting fields
		$sorting_fields = array ('shiftName' => 'asc' );
		
		$controller = $this->uri->segment ( 1 );
		
		foreach ( $condition_fields as $key ) {
			$$key ['variable'] = $this->session->userdata ( $controller . '_' . $key ['variable'] );
		}
		
		$limit = $this->session->userdata ( $controller . '_limit' );
		$offset = $this->session->userdata ( $controller . '_offset' );
		$sortby = $this->session->userdata ( $controller . '_sortby' );
		$sortorder = $this->session->userdata ( $controller . '_sortorder' );
		
		// select
		$this->db->select ( $this->table . '.*' );
		
		// from
		$this->db->from ( $this->table );
		
		// join
		
		// where
		// set conditions here
		foreach ( $condition_fields as $key ) {
			$operators = explode ( '_', $key ['operator'] );
			$operator = $operators [0];
			// check if the operator is like
			if (count ( $operators ) > 1) {
				// like operator
				if (trim ( $$key ['variable'] ) != '' && $key ['field'])
					$this->db->$operator ( $key ['field'], $$key ['variable'], $operators [1] );
			} else {
				if (trim ( $$key ['variable'] ) != '' && $key ['field'])
					$this->db->$operator ( $key ['field'], $$key ['variable'] );
			}
		}
		
		if ($sortby && $sortorder) {
			$this->db->order_by ( $sortby, $sortorder );
			
			if (! empty ( $sorting_fields )) {
				foreach ( $sorting_fields as $fld => $s_order ) {
					if ($fld != $sortby) {
						$this->db->order_by ( $fld, $s_order );
					}
				}
			}
		} else {
			$ctr = 1;
			if (! empty ( $sorting_fields )) {
				foreach ( $sorting_fields as $fld => $s_order ) {
					if ($ctr == 1) {
						$sortby = $fld;
						$sortorder = $s_order;
					}
					$this->db->order_by ( $fld, $s_order );
					
					$ctr ++;
				}
			}
		}
		
		if ($limit) {
			if ($offset) {
				$this->db->limit ( $limit, $offset );
			} else {
				$this->db->limit ( $limit );
			}
		}
		
		// assigning variables
		$data ['sortby'] = $sortby;
		$data ['sortorder'] = $sortorder;
		$data ['limit'] = $limit;
		$data ['offset'] = $offset;
		
		// get
		$records = $this->db->get ()->result ();
		
		$title = "Shift List";
		$companyName = $this->config_model->getConfig ( 'Company' );
		$address = $this->config_model->getConfig ( 'Address' );
		
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
    
    		<Worksheet ss:Name='" . $title . "'>
  
    		<Table>
    		<Column ss:Index='1' ss:AutoFitWidth=\"1\" ss:Width='25.00'/>
    		<Column ss:Index='2' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='3' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='4' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
    		<Column ss:Index='5' ss:AutoFitWidth=\"1\" ss:Width='150.00'/>
    		<Column ss:Index='6' ss:AutoFitWidth=\"1\" ss:Width='150.00'/>
    		<Column ss:Index='7' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='8' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='9' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='10' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='11' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='12' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		    ";
		
		// header
		$data .= "<Row ss:StyleID='s24'>";
		$data .= "<Cell ss:MergeAcross='11'><Data ss:Type='String'></Data></Cell>";
		$data .= "</Row>";
		
		$data .= "<Row ss:StyleID='s20'>";
		$data .= "<Cell ss:MergeAcross='11'><Data ss:Type='String'>" . $companyName . "</Data></Cell>";
		$data .= "</Row>";
		$data .= "<Row ss:StyleID='s24A'>";
		$data .= "<Cell ss:MergeAcross='11'><Data ss:Type='String'>" . $address . "</Data></Cell>";
		$data .= "</Row>";
		
		$data .= "<Row ss:StyleID='s24'>";
		$data .= "<Cell ss:MergeAcross='11'><Data ss:Type='String'></Data></Cell>";
		$data .= "</Row>";
		
		$data .= "<Row ss:StyleID='s24'>";
		$data .= "<Cell ss:MergeAcross='11'><Data ss:Type='String'>" . strtoupper ( $title ) . "</Data></Cell>";
		$data .= "</Row>";
		
		$data .= "<Row ss:StyleID='s24'>";
		$data .= "<Cell ss:MergeAcross='11'><Data ss:Type='String'></Data></Cell>";
		$data .= "</Row>";
		
		$fields [] = "  ";
		$fields [] = "SHIFT NAME";
		$fields [] = "TYPE";
		$fields [] = "FIRST PERIOD";
		$fields [] = "(EARLY LOG)";
		$fields [] = "(TARDY LOG)";
		$fields [] = "SECOND PERIOD";
		$fields [] = "(EARLY LOG)";
		$fields [] = "(TARDY LOG)";
		$fields [] = "WORK HOURS";
		$fields [] = "BREAK HOURS";
		$fields [] = "STATUS";
		
		$data .= "<Row ss:StyleID='s24'>";
		//Field Name Data
		foreach ( $fields as $fld ) {
			$data .= "<Cell ss:StyleID='s23'><Data ss:Type='String'>$fld</Data></Cell>";
		}
		$data .= "</Row>";
		
		if (count ( $records )) {
			$ctr = 1;
			foreach ( $records as $row ) {
				if ($row->shiftType=="1") {
					$st = "2 Periods";
				} elseif ($row->shiftType=="2") {
					$st = "1 Period";
				}
				if($row->in1 != "00:00:00") {
					$in1  = date('h:i A', strtotime($row->in1)); 
				}
				if($row->in2 != "00:00:00") {
					$in2  = date('h:i A', strtotime($row->in2)); 
				}
				if($row->out1 != "00:00:00") {
					$out1  = date('h:i A', strtotime($row->out1)); 
				}
				if($row->out2 != "00:00:00") {
					$out2  = date('h:i A', strtotime($row->out2)); 
				}
				if($row->startTardy1 != "00:00:00") {
					$tardy1 = date('h:i A', strtotime($row->startTardy1)); 
				}
				if($row->startTardy2 != "00:00:00") {
					$tardy2 = date('h:i A', strtotime($row->startTardy2)); 
				}
				if($row->startLog1 != "00:00:00") {
					$startLog1 = date('h:i A', strtotime($row->startLog1)); 
				}
				if($row->startLog2 != "00:00:00") {
					$startLog2 = date('h:i A', strtotime($row->startLog2)); 
				}
				 
				$data .= "<Row>";
				$data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $ctr . ".</Data></Cell>";
				$data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->shiftName . "</Data></Cell>";
				$data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $st . "</Data></Cell>";
				$data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $in1.' - '.$out1. "</Data></Cell>";
				$data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $startLog1 . "</Data></Cell>";
				$data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $tardy1 . "</Data></Cell>";
				$data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $in2.' - '.$out2 . "</Data></Cell>";
				$data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $startLog2 . "</Data></Cell>";
				$data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $tardy2 . "</Data></Cell>";
				$data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->workHours . "</Data></Cell>";
				$data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->breakHours . "</Data></Cell>";
				if ($row->status == 1) {
					$data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'>Active</Data></Cell>";
				} else {
					$data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'>Inactive</Data></Cell>";
				}
				$data .= "</Row>";
				
				$ctr ++;
			}
		}
		$data .= "</Table></Worksheet>";
		$data .= "</Workbook>";
		
		//Final XML Blurb
		$filename = "shift_list";
		
		header ( "Content-type: application/octet-stream" );
		header ( "Content-Disposition: attachment; filename=$filename.xls;" );
		header ( "Content-Type: application/ms-excel" );
		header ( "Pragma: no-cache" );
		header ( "Expires: 0" );
		
		echo $data;
	
	}
	
	//Conditions and fields changes
	public function check_duplicate() {
		$this->db->where ( 'shiftName', trim ( $this->input->post ( 'shiftName' ) ) );
		
		if ($this->db->count_all_results ( $this->table ))
			echo "1"; // duplicate
		else
			echo "0";
	}

}
