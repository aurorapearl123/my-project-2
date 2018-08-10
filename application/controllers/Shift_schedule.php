<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shift_schedule extends CI_Controller
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
        $this->module       = 'Attendance and Shift';
        $this->data['controller_page']  = $this->controller_page = site_url('shift_schedule');		// defines contoller link
        $this->table        = 'shift_schedules';                                                  	// defines the default table
        $this->pfield = $this->data['pfield'] = 'shiftScheduleID';                                	// defines primary key
        $this->logfield     = 'empID';
        $this->module_path  = 'modules/'.strtolower(str_replace(" ","_",$this->module)).'/shift_schedule'; // defines module path
        
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
        $this->data['current_module']        = $this->modules[$this->module]['sub']['Shift Schedule'];      // defines the current sub module
        // check roles
        $this->check_roles();
        $this->data['roles']   = $this->roles;
    }
    
    private function check_roles()
    {
        // check roles
        $this->roles['create']  = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Add '.$this->data['current_module']['module_label']);
        $this->roles['view']    = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View '.$this->data['current_module']['module_label']);
        $this->roles['edit']    = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Edit Existing '.$this->data['current_module']['module_label']);
        $this->roles['delete']  = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Delete Existing '.$this->data['current_module']['module_label']);
    }
    
    private function _in_used($id=0)
    {
        $tables = array('employments'=>'deptID');
    
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
    
    public function index()
    {
        $this->create();
    }
	public function create($empID=0,$employmentID=0,$startDate=0,$endDate=0,$tab=1)
    {
        $this->submenu();
        $data = $this->data;
        $data['tab'] = $tab;
        
    	if ($empID && $startDate && $endDate) {
			$data['empID'] 			= $empID;
			$data['employmentID'] 	= $employmentID;
			$data['startDate'] 		= date('Y-m-d', $startDate);
			$data['endDate'] 		= date('Y-m-d', $endDate);
		
			// set sessions
			$this->session->set_userdata('current_empID', $data['empID']);
			$this->session->set_userdata('current_employmentID', $data['employmentID']);
			$this->session->set_userdata('current_startDate', $data['startDate']);
			$this->session->set_userdata('current_endDate', $data['endDate']);
		} else {
			$data['empID']			= ($this->session->userdata('current_empID')) ? $this->session->userdata('current_empID') : 0;
			$data['empNo']			= ($this->session->userdata('current_empNo')) ? $this->session->userdata('current_empNo') : "";
			$data['employmentID']	= ($this->session->userdata('current_employmentID')) ? $this->session->userdata('current_employmentID') : 0;
			$data['startDate'] 		= ($this->session->userdata('current_startDate')) ? $this->session->userdata('current_startDate') : date('m/d/Y', strtotime(date('Y').'-'.date('m').'-01'));
			$data['endDate'] 		= ($this->session->userdata('current_endDate')) ? $this->session->userdata('current_endDate') : date('m/d/Y', strtotime(date('Y').'-'.date('m').'-'.date('t')));
		}
        // check roles
        if ($this->roles['create']) {
        	if ($data['empID']) {	
				$this->db->where('empID', $data['empID']);
				$query = $this->db->get('employees', 1)->row();
			
				$data['empNo']		   = $query->empNo;
				$data['employee_name'] = $query->lname.', '.$query->fname.' '.$query->mname.' '.$query->suffix;
				
				$this->session->set_userdata('current_empNo', $data['empNo']);
			} elseif ($data['employmentID']) {
				$this->db->select('employments.*');
				$this->db->select('employees.lname');
				$this->db->select('employees.fname');
				$this->db->select('employees.mname');
				$this->db->select('employees.suffix');
				$this->db->from('employments');
				$this->db->join('employees','employments.empID=employees.empID','left');
				$this->db->where('employments.empID', $data['empID']);
				$this->db->where('employments.employmentID', $data['employmentID']);
				$this->db->limit(1);
				$query = $this->db->get()->row();
					
				$data['employee_name'] = $query->lname.', '.$query->fname.' '.$query->mname.' '.$query->suffix;
			}
			
			$this->db->select('shift_schedules.shiftScheduleID');
			$this->db->select('shift_schedules.shiftID as schedShiftID');
			$this->db->select('shift_schedules.date');
			$this->db->select('shift_schedules.remarks as schedule_remarks');
			$this->db->select('shifts.*');
			$this->db->from('shift_schedules');
			$this->db->join('shifts','shift_schedules.shiftID=shifts.shiftID','left');
			$this->db->where('shift_schedules.empID', $data['empID']);
			$this->db->where('shift_schedules.employmentID', $data['employmentID']);
			$this->db->where('shift_schedules.date >= ', $data['startDate']);
			$this->db->where('shift_schedules.date <= ', $data['endDate']);
			$this->db->order_by('shift_schedules.date', 'asc');
			$data['records'] = $this->db->get();
			
			$data['dates'] = array();			
			$data['start'] = strtotime($data['startDate']);
			$data['end'] = strtotime($data['endDate']);
			
			for ($current = $data['start']; $current <= $data['end']; $current = strtotime('+1 day', $current)) {
				$info = array('shiftID'=>'0','shiftType'=>'1','in1'=>'','out1'=>'','in2'=>'','out2'=>'','remarks'=>'');
				$data['dates'][$current] = $info;
			}
			
			if ($data['records']->num_rows()) {
				foreach ($data['records']->result() as $row) {
					$info = array('shiftID'=>$row->schedShiftID,'shiftType'=>$row->shiftType,'in1'=>$row->in1,'out1'=>$row->out1,'in2'=>$row->in2,'out2'=>$row->out2,'remarks'=>$row->schedule_remarks);
					$data['dates'][strtotime($row->date)] = $info;
				}
			}
        	//echo $this->db->last_query();
            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path.'/create');
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

	public function create2($empID=0,$employmentID=0,$startDate=0,$endDate=0,$tab=2)
    {
        $this->submenu();
        $data = $this->data;
        $data['tab'] = $tab;
        
        // check roles
        if ($this->roles['create']) {

            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path.'/create2');
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
    
	public function print_employee($empID=0,$employmentID=0,$startDate=0,$endDate=0,$tab=1)
    {
        $this->submenu();
        $data = $this->data;
        $data['tab'] = $tab;
        echo $empID;
    	if ($empID && $startDate && $endDate) {
			$data['empID'] 			= $empID;
			$data['employmentID'] 	= $employmentID;
			$data['startDate'] 		= date('Y-m-d', $startDate);
			$data['endDate'] 		= date('Y-m-d', $endDate);
	
			// set sessions
			$this->session->set_userdata('current_empID', $data['empID']);
			$this->session->set_userdata('current_employmentID', $data['employmentID']);
			$this->session->set_userdata('current_startDate', $data['startDate']);
			$this->session->set_userdata('current_endDate', $data['endDate']);
		} else {
			$data['empID']			= ($this->session->userdata('current_empID')) ? $this->session->userdata('current_empID') : 0;
			$data['empNo']			= ($this->session->userdata('current_empNo')) ? $this->session->userdata('current_empNo') : "";
			$data['employmentID']	= ($this->session->userdata('current_employmentID')) ? $this->session->userdata('current_employmentID') : 0;
			$data['startDate'] 		= ($this->session->userdata('current_startDate')) ? $this->session->userdata('current_startDate') : date('m/d/Y', strtotime(date('Y').'-'.date('m').'-01'));
			$data['endDate'] 		= ($this->session->userdata('current_endDate')) ? $this->session->userdata('current_endDate') : date('m/d/Y', strtotime(date('Y').'-'.date('m').'-'.date('t')));
		}
		// check roles
		if ($this->roles['view'] || ($this->session->userdata('current_userType')=='ess' && $this->session->userdata('current_empID')==$data['empID'])) {
			$this->db->select('employees.empNo');
			$this->db->select('employees.fname');
			$this->db->select('employees.suffix');
			$this->db->select('employments.employmentNo');
			$this->db->select('employments.lname');				
			$this->db->select('employments.mname');				
			$this->db->select('employments.title');
			$this->db->select('employee_types.employeeType');
			$this->db->select('job_titles.jobTitle');
			$this->db->from('employments');				
			$this->db->join('employees','employments.empID=employees.empID', 'left');
			$this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID', 'left');
			$this->db->join('job_titles','employments.jobTitleID=job_titles.jobTitleID', 'left');
			$this->db->where('employments.empID', $data['empID']);
			//$this->db->where('employments.employmentID', $data['employmentID']);
			$this->db->limit(1);
			$data['rec'] = $this->db->get()->row();
			
			//$data['if'] 	 = $this->db->last_query();
			$data['empNo']   = $data['rec']->empNo;
			$data['name']    = $data['rec']->lname.', '.$data['rec']->fname.' '.substr($data['rec']->mname, 0, 1).' '.$data['rec']->suffix;
			$data['jobTitle']= $data['rec']->jobTitle;
				
			$this->db->select('shift_schedules.shiftScheduleID');
			$this->db->select('shift_schedules.shiftID as schedShiftID');
			$this->db->select('shift_schedules.date');
			$this->db->select('shift_schedules.remarks as schedule_remarks');
			$this->db->select('shifts.*');
			$this->db->from('shift_schedules');
			$this->db->join('shifts','shift_schedules.shiftID=shifts.shiftID','left');
			$this->db->where('shift_schedules.empID', $data['empID']);
			$this->db->where('shift_schedules.employmentID', $data['employmentID']);
			$this->db->where('shift_schedules.date >= ', $data['startDate']);
			$this->db->where('shift_schedules.date <= ', $data['endDate']);
			$this->db->order_by('shift_schedules.date', 'asc');
			$data['records'] = $this->db->get();
				
			$data['dates'] = array();
			$data['start'] = strtotime($data['startDate']);
			$data['end'] = strtotime($data['endDate']);
				
			for ($current = $data['start']; $current <= $data['end']; $current = strtotime('+1 day', $current)) {
				$info = array('shiftID'=>'0','shiftType'=>'1','in1'=>'','out1'=>'','in2'=>'','out2'=>'','workHours'=>'0','remarks'=>'');
				$data['dates'][$current] = $info;
			}
				
			$twoPeriod = false;
			if ($data['records']->num_rows()) {
				foreach ($data['records']->result() as $row) {
					$info = array('shiftID'=>$row->schedShiftID,'shiftType'=>$row->shiftType,'in1'=>$row->in1,'out1'=>$row->out1,'in2'=>$row->in2,'out2'=>$row->out2,'workHours'=>$row->workHours,'remarks'=>$row->schedule_remarks);
					$data['dates'][strtotime($row->date)] = $info;
					
					if ($row->shiftType==1) {
						$twoPeriod = true;
					}
				}
			}
				
			if (date('M', strtotime($data['startDate'])) == date('M', strtotime($data['endDate']))) {
				$data['period'] = date('M', strtotime($data['startDate']));
				$data['period'] .= ' '.date('j', strtotime($data['startDate']));
				$data['period'] .= ' - '.date('j, Y', strtotime($data['endDate']));
			} else {										
				if (date('Y', strtotime($data['startDate'])) == date('Y', strtotime($data['endDate']))) {
					$data['period'] = date('M j', strtotime($data['startDate']));
					$data['period'] .= ' - '.date('M j', strtotime($data['endDate']));
					$data['period'] .= ', '.date('Y', strtotime($data['startDate']));
				} else {
					$data['period'] = date('M j, Y', strtotime($data['startDate']));
					$data['period'] .= ' - '.date('M j, Y', strtotime($data['endDate']));
				}
			} 			
			
			$data['pdf_paging'] = false;
			$data['title']      = "SHIFT SCHEDULE";
			$data['modulename'] = "SHIFT SCHEDULE";				
	
			// load pdf class
			$this->load->library('mpdf');										
			
			// load pdf class
			if (count($data['dates']) > 15) {
				$this->mpdf->mpdf('en-GB',array(100,285),10,'Garamond',3,3,3,10,0,0,'P');
			} else {
				$this->mpdf->mpdf('en-GB',array(100,190),10,'Garamond',3,3,3,10,0,0,'P');
			}
			$this->mpdf->setTitle($data['title']);
			$this->mpdf->SetDisplayMode('fullpage');
			$this->mpdf->shrink_tables_to_fit = 1;
			$this->mpdf->SetWatermarkImage(base_url().'images/logo/watermark.png');
			$this->mpdf->watermark_font = 'DejaVuSansCondensed';
			$this->mpdf->watermarkImageAlpha = 0.1;
			$this->mpdf->watermarkImgBehind = TRUE;
			$this->mpdf->showWatermarkImage = TRUE;
				
			$footer = $this->load->view('print_pdf_footer', $data, TRUE);
			$this->mpdf->SetHTMLFooter($footer);
			$html 	= ($twoPeriod) ? $this->load->view($this->module_path.'/print_emp_schedule_2_period', $data, TRUE) : $this->load->view($this->module_path.'/print_emp_schedule_1_period', $data, TRUE);
			$this->mpdf->WriteHTML($html);
			
			$this->mpdf->Output("SHIFT_SCHEDULE.pdf","I");				
		} else {
			$data["display"] = "block";
			$data["class"] 	 = "errorbox";
			$data["msg"] 	 = "Sorry, you don't have access to this page!";
			$data["urlredirect"] = "";
			if ($header) { $this->load->view("header".$page,$data); }
			$this->load->view("message",$data);
			if ($header) { $this->load->view("footer".$page); }
		}
    }
    
	public function set_group()
    {
        $this->submenu();
        $data = $this->data;
        
        // check roles
        if ($this->roles['create']) {

            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path.'/create2');
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
    
    public function updateRec()
    {
    	$response = new stdClass();
        $response->status  = 0;
        $response->message = '';
        
    	$empID 			= $this->input->post('empID');
    	$employmentID 	= $this->input->post('employmentID');
    	$startDate 		= $this->input->post('startDate');
    	$endDate 		= $this->input->post('endDate');
    	$shiftID 		= date('Y-m-d',$this->input->post('shiftID'));
    	$shiftVal 		= $this->input->post('shiftVal');
    	$remarks 		= "";
    	
        // check roles
        //if ($this->roles['edit']) {
        	$this->db->where('empID', $empID);
	        $this->db->where('employmentID', $employmentID);
	        $this->db->where('date', $shiftID);
	        $emps = $this->db->get('shift_schedules')->row();
        	if (!empty($emps)) { 
        		$this->db->set('shiftID',$shiftVal);
        		$this->db->set('dateUpdated',date('Y-m-d H:i:s'));
        		$this->db->set('updatedBy',$this->session->userdata('current_user')->userID);
        		$this->db->where('empID',$empID);
        		$this->db->where('employmentID',$employmentID);
        		$this->db->where('date',$shiftID);
        		$this->db->update('shift_schedules');
        	} else {
        		$this->db->set('empID',$empID);
        		$this->db->set('employmentID',$employmentID);
        		$this->db->set('shiftID',$shiftVal);
        		$this->db->set('date',$shiftID);
        		$this->db->set('remarks',$remarks);
        		$this->db->insert('shift_schedules');
        	}
        	
        	$this->db->select('shift_schedules.*');
        	$this->db->select('TIME_FORMAT(shifts.in1, "%h:%i %p") as in1');
        	$this->db->select('TIME_FORMAT(shifts.in2, "%h:%i %p") as in2');
        	$this->db->select('TIME_FORMAT(shifts.out1, "%h:%i %p") as out1');
        	$this->db->select('TIME_FORMAT(shifts.out2, "%h:%i %p") as out2');
        	
        	$this->db->from('shift_schedules');
        	
        	$this->db->join('shifts',$this->table.'.shiftID=shifts.shiftID','left');
        	
        	$this->db->where('empID', $empID);
	        $this->db->where('employmentID', $employmentID);
	        $this->db->where('date', $shiftID);
	        $emps = $this->db->get()->row();
	        
        	//updated successfully
        //} else {
        //   // error
        //    $data["class"]   = "danger";
        //    $data["msg"]     = "Sorry, you don't have access to this page!";
        //    $data["urlredirect"] = "";
        //    $this->load->view("header",$data);
        //    $this->load->view("message");
        //    $this->load->view("footer");
        //}
        $response->emps = $emps;
        $response->status = 1;
        echo json_encode($response);
    }

    public function delete($id=0)
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
            
            if (!empty($this->record->field)) {
                $this->record->pfield   = $this->pfield;
                $this->record->pk       = $id;

                // record logs
                $rec_value = $this->record->field->name;
                           
                // check if in used
                if (!$this->_in_used($id)) {
                    if ($this->record->delete()) {
                        $logfield = $this->logfield;
                        // record logs
                        $logs = "Record - ".$this->record->field->$logfield;
                        $this->log_model->table_logs($this->data['current_module']['module_label'], $this->table, $this->pfield, $this->record->pk, 'Delete', $logs);
                        // successful
                        $data["class"]   = "success";
                        $data["msg"]     = $this->data['current_module']['module_label']." successfully deleted.";
                        $data["urlredirect"] = $this->controller_page."/";
                        $this->load->view("header",$data);
                        $this->load->view("message");
                        $this->load->view("footer");
                    } else {
                        // error
                        $data["class"]   = "danger";
                        $data["msg"]     = "Error in deleting the ".$this->data['current_module']['module_label']."!";
                        $data["urlredirect"] = "";
                        $this->load->view("header",$data);
//                         $this->load->view("message");
                        $this->load->view("footer");
                    }
                } else {
                    // error
                    $data["class"]   = "danger";
                    $data["msg"]     = "Data integrity constraints.";
                    $data["urlredirect"] = "";
                    $this->load->view("header",$data);
                    $this->load->view("message");
                    $this->load->view("footer");
                }
                
            } else {
                // error
                $data["class"]   = "danger";
                $data["msg"]     = $this->data['current_module']['module_label']." record not found!";
                $data["urlredirect"] = "";
                $this->load->view("header",$data);
                $this->load->view("message");
                $this->load->view("footer");
            }
        } else {
            // error
            $data["class"]   = "danger";
            $data["msg"]     = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header",$data);
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
            $this->db->select($this->table.".*");
            $this->db->select('companies.companyCode');
            $this->db->select('companies.companyName');
            $this->db->select('branches.branchCode');
            $this->db->select('branches.branchName');
            $this->db->select('employees.lname');
            $this->db->select('employees.fname');
            $this->db->select('employees.mname');
            $this->db->from($this->table);
            $this->db->join('companies',$this->table.'.companyID=companies.companyID','left');
            $this->db->join('branches',$this->table.'.branchID=branches.branchID','left');
            $this->db->join('employees',$this->table.'.deptHead=employees.empID','left');
            $this->db->where($this->pfield, $id);
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->db->get()->row();
            
            $data['in_used'] = $this->_in_used($id);
            
            // record logs
            if ($this->config_model->getConfig('Log all record views') == '1') {
                $logs = "Record - ".$this->record->field->name;
                $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->field->$data['pfield'], 'View', $logs);
            }
            
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
        //************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;
        
        // **************************************************
        // variable:field:default_value:operator
        // note: dont include the special query field filter                
        $condition_fields = array(
            //array('variable'=>'companyID', 'field'=>$this->table.'.companyID', 'default_value'=>'', 'operator'=>'where'),
            //array('variable'=>'branchID', 'field'=>$this->table.'.branchID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'empID', 'field'=>$this->table.'.empID', 'default_value'=>'', 'operator'=>'where'),
            //array('variable'=>'deptName', 'field'=>$this->table.'.deptName', 'default_value'=>'', 'operator'=>'like_both'),
            //array('variable'=>'lname', 'field'=>'employees.lname', 'default_value'=>'', 'operator'=>'like'),
            //array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('empID'=>'asc');
        
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
            $limit = 25; // default limit
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

        // from
        $this->db->from($this->table);
        
        // join
        //$this->db->join('employees',$this->table.'.deptHead=employees.empID','left');
        
        // where
        // set conditions here
        foreach($condition_fields as $key) {
            $operators = explode('_',$key['operator']);
            $operator  = $operators[0];
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
        
        // from
        $this->db->from($this->table);
        
        // join
        
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
        $data['offset']     = $offset;
        
        // get
        $data['records'] = $this->db->get()->result();
        // load views
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
            array('variable'=>'companyID', 'field'=>$this->table.'.companyID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'branchID', 'field'=>$this->table.'.branchID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'deptCode', 'field'=>$this->table.'.deptCode', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'deptName', 'field'=>$this->table.'.deptName', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'lname', 'field'=>'employees.lname', 'default_value'=>'', 'operator'=>'like'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('deptName'=>'asc');
        
        $controller = $this->uri->segment(1);
        
        foreach($condition_fields as $key) {
            $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
        }
        
        $limit      = $this->session->userdata($controller.'_limit');
        $offset     = $this->session->userdata($controller.'_offset');
        $sortby     = $this->session->userdata($controller.'_sortby');
        $sortorder  = $this->session->userdata($controller.'_sortorder');
        
        // select
        $this->db->select($this->table.'.*');
        $this->db->select('companies.companyCode');
        $this->db->select('companies.companyName');
        $this->db->select('branches.branchCode');
        $this->db->select('employees.lname');
        $this->db->select('employees.fname');
        
        // from
        $this->db->from($this->table);
        
        // join
        $this->db->join('companies',$this->table.'.companyID=companies.companyID','left');
        $this->db->join('branches',$this->table.'.branchID=branches.branchID','left');
        $this->db->join('employees',$this->table.'.deptHead=employees.empID','left');
        
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
        $data['offset']     = $offset;
        
        // get
        $data['records'] = $this->db->get()->result();
        
        $data['title'] = "Department List";

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
            array('variable'=>'companyID', 'field'=>$this->table.'.companyID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'branchID', 'field'=>$this->table.'.branchID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'deptCode', 'field'=>$this->table.'.deptCode', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'deptName', 'field'=>$this->table.'.deptName', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'lname', 'field'=>'employees.lname', 'default_value'=>'', 'operator'=>'like'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('deptName'=>'asc');
        
        $controller = $this->uri->segment(1);
        
        foreach($condition_fields as $key) {
            $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
        }
        
        $limit      = $this->session->userdata($controller.'_limit');
        $offset     = $this->session->userdata($controller.'_offset');
        $sortby     = $this->session->userdata($controller.'_sortby');
        $sortorder  = $this->session->userdata($controller.'_sortorder');
        
        // select
        $this->db->select($this->table.'.*');
        $this->db->select('companies.companyCode');
        $this->db->select('companies.companyName');
        $this->db->select('branches.branchCode');
        $this->db->select('employees.lname');
        $this->db->select('employees.fname');
        
        // from
        $this->db->from($this->table);
        
        // join
        $this->db->join('companies',$this->table.'.companyID=companies.companyID','left');
        $this->db->join('branches',$this->table.'.branchID=branches.branchID','left');
        $this->db->join('employees',$this->table.'.deptHead=employees.empID','left');
        
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
        $data['offset']     = $offset;
        
        // get
        $records = $this->db->get()->result();
        
    
        $title          = "Department List";
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
    		<Column ss:Index='7' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		    ";
    
        // header
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='6'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
    
        $data .= "<Row ss:StyleID='s20'>";
        $data .= "<Cell ss:MergeAcross='6'><Data ss:Type='String'>".$companyName."</Data></Cell>";
        $data .= "</Row>";
        $data .= "<Row ss:StyleID='s24A'>";
        $data .= "<Cell ss:MergeAcross='6'><Data ss:Type='String'>".$address."</Data></Cell>";
        $data .= "</Row>";
    
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='6'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
         
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='6'><Data ss:Type='String'>".strtoupper($title)."</Data></Cell>";
        $data .= "</Row>";
         
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='6'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
    
        $fields[]="  ";
        $fields[]="COMPANY";
        $fields[]="BRANCH";
        $fields[]="DEPT CODE";
        $fields[]="DEPT NAME";
        $fields[]="DEPT HEAD";
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
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->companyCode."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->branchCode."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->deptCode."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->deptName."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->lname." , ".$row->fname."</Data></Cell>";
                if  ($row->status == 1) {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'>Active</Data></Cell>";
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
        $filename = "department_list";
    
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
        $this->db->where('companyID', trim($this->input->post('companyID')));
        $this->db->where('branchID', trim($this->input->post('branchID')));
        $this->db->where('deptCode', trim($this->input->post('deptCode')));
      
        if ($this->db->count_all_results($this->table))
            echo "1"; // duplicate
        else 
            echo "0";
    }

}
