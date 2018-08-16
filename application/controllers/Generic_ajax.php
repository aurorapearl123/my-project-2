<?php
class Generic_ajax extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
	    // check for maintenance period
        if ($this->config_model->getConfig('Maintenance Mode')=='1') {
            header('location: '.site_url('maintenance_mode'));
        }
        
        // check user session
        if (!$this->session->userdata('current_user')->sessionID) {
            header('location: '.site_url('login'));
        }

	}
	
	public function get_branches()
	{
	    $companyID = trim($this->input->post('companyID'));
	
	    $this->db->where('companyID',$companyID);
	    $this->db->order_by('branchName','asc');
	    $records = $this->db->get('branches');
	    echo $this->frameworkhelper->get_json_data($records, 'branchID', 'branchName');
	}
	
	public function get_emps()
	{
	    $empID = trim($this->input->post('empID'));
	
	    $this->db->where('empID',$empID);
	    $this->db->where('status',1);
	    $this->db->order_by('employmentNo','asc');
	    $records = $this->db->get('employments');
	    echo $this->frameworkhelper->get_json_data($records, 'employmentID', 'employmentNo');
	}
	
	public function get_departments()
	{
	    $branchID = trim($this->input->post('branchID'));
	
	    $this->db->select('departments.*');
	    $this->db->select('branches.branchCode');
	    $this->db->from('departments');
	    $this->db->join('branches','departments.branchID=branches.branchID','left');
	    $this->db->where('departments.branchID',$branchID);
	    $this->db->order_by('departments.deptName','asc');
	    $records = $this->db->get();
	    
	    echo $this->frameworkhelper->get_json_data($records, 'deptID', 'deptName');
	}
	
	public function get_sections()
	{
	    $deptID = trim($this->input->post('deptID'));
	
	    $this->db->select('divisions.*');
	    $this->db->select('departments.deptCode');
	    $this->db->from('divisions');
	    $this->db->join('departments','divisions.deptID=departments.deptID','left');
	    $this->db->where('divisions.deptID',$deptID);
	    $this->db->order_by('divisions.divisionName','asc');
	    $records = $this->db->get();
	    
	    echo $this->frameworkhelper->get_json_data($records, 'divisionID', 'divisionName');
	}
	
	public function get_code_branches()
	{
	    $companyID = trim($this->input->post('companyID'));
	
	    $this->db->where('companyID',$companyID);
	    $this->db->order_by('branchCode','asc');
	    $records = $this->db->get('branches');
	    echo $this->frameworkhelper->get_json_data($records, 'branchID', 'branchCode');
	}
	
	public function get_divisions()
	{
	    $deptID = trim($this->input->post('deptID'));
	
	    $this->db->where('deptID',$deptID);
	    $this->db->order_by('deptName','asc');
	    $records = $this->db->get('departments');
	    echo $this->frameworkhelper->get_json_data($records, 'deptID', 'deptName');
	}
	
	
	public function get_code_departments()
	{
	    $branchID = trim($this->input->post('branchID'));
	
	    $this->db->select('departments.*');
	    $this->db->select('branches.branchCode');
	    $this->db->from('departments');
	    $this->db->join('branches','departments.branchID=branches.branchID','left');
	    $this->db->where('departments.branchID',$branchID);
	    $this->db->order_by('departments.deptCode','asc');
	    $records = $this->db->get();
	    echo $this->frameworkhelper->get_json_data($records, 'branchID', 'deptCode');
	}
	
	public function get_code_sections()
	{
	    $deptID = trim($this->input->post('deptID'));
	
	    $this->db->select('divisions.*');
	    $this->db->select('departments.deptCode');
	    $this->db->from('divisions');
	    $this->db->join('departments','divisions.deptID=departments.deptID','left');
	    $this->db->where('divisions.deptID',$deptID);
	    $this->db->order_by('divisions.divisionCode','asc');
	    $records = $this->db->get();
	    echo $this->frameworkhelper->get_json_data($records, 'divisionID', 'divisionCode');
	}
	
	public function get_plantilla()
	{
	    $branchID = trim($this->input->post('branchID'));
	    $deptID = trim($this->input->post('deptID'));
	    $divisionID = trim($this->input->post('divisionID'));
	
	    $this->db->select('job_positions.*');
	    $this->db->select('job_titles.jobTitle');
	    $this->db->from('job_positions');
	    $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID','left');
	    $this->db->where('job_positions.branchID',$branchID);
	    $this->db->where('job_positions.deptID',$deptID);
	    $this->db->where('job_positions.divisionID',$divisionID);
	    $this->db->where('job_positions.status',1);
	    $this->db->order_by('job_titles.jobTitle','asc');
	    $records = $this->db->get();
	    
	    $display = array('jobTitle'=>' - ','positionCode'=>'');
	    
	    echo $this->frameworkhelper->get_json_data($records, 'jobPositionID', $display);
	}
	
	public function get_countries()
	{
	    $this->db->order_by('country','asc');
	    $records = $this->db->get('countries');
	    echo $this->frameworkhelper->get_json_data($records, 'countryID', 'country');
	}
	
	public function get_provinces()
	{
	    $countryID = '';
	    if (trim($this->input->post('countryID'))!="") {
			$countryID = trim($this->input->post('countryID'));
		} elseif (trim($this->input->post('currentCountryID'))!="") {
			$countryID = trim($this->input->post('currentCountryID'));
		} elseif (trim($this->input->post('provinceCountryID'))!="") {
			$countryID = trim($this->input->post('provinceCountryID'));
		}
	
	    $this->db->where('countryID',$countryID);
	    $this->db->order_by('province','asc');
	    $records = $this->db->get('provinces');
	    echo $this->frameworkhelper->get_json_data($records, 'provinceID', 'province');
	}
	
	public function get_cities()
	{
	    $provinceID = '';
	    if (trim($this->input->post('provinceID'))!="") {
	        $provinceID = trim($this->input->post('provinceID'));
	    } elseif (trim($this->input->post('currentProvinceID'))!="") {
	        $provinceID = trim($this->input->post('currentProvinceID'));
	    } elseif (trim($this->input->post('provinceProvinceID'))!="") {
	        $provinceID = trim($this->input->post('provinceProvinceID'));
	    }
	
	    $this->db->where('provinceID',$provinceID);
	    $this->db->order_by('city','asc');
	    $records = $this->db->get('cities'); 
	    echo $this->frameworkhelper->get_json_data($records, 'cityID', 'city');
	}
	
	public function get_barangays()
	{
	    $cityID = '';
	    if (trim($this->input->post('cityID'))!="") {
	        $cityID = trim($this->input->post('cityID'));
	    } elseif (trim($this->input->post('currentCityID'))!="") {
	        $cityID = trim($this->input->post('currentCityID'));
	    } elseif (trim($this->input->post('provinceCityID'))!="") {
	        $cityID = trim($this->input->post('provinceCityID'));
	    }
	
	    $this->db->where('cityID',$cityID);
	    $this->db->order_by('barangay','asc');
	    $records = $this->db->get('barangays');
	    echo $this->frameworkhelper->get_json_data($records, 'barangayID', 'barangay');
	}
	
	public function get_employments()
	{
	   $empID = $this->input->post('empID');

	   $this->db->select('employments.employmentID');
	   $this->db->select('employments.employmentNo');
	   $this->db->select('companies.companyName');
	   $this->db->select('companies.companyAbbr');
	   $this->db->select('branches.branchAbbr');
	   $this->db->select('divisions.divisionAbbr');
	   $this->db->select('employee_types.employeeType');
	   $this->db->select('job_positions.positionCode');
	   $this->db->select('job_titles.jobTitle');
	   $this->db->from('employments');
	   $this->db->join('companies','employments.companyID=companies.companyID','left');
	   $this->db->join('branches','employments.branchID=branches.branchID','left');
	   $this->db->join('divisions','employments.divisionID=divisions.divisionID','left');
	   $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID','left');
	   $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID','left');
	   $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID','left');
	   
	   
	    $this->db->where('employments.empID', $empID);
	    $this->db->where('employments.status', 1);
	    $records = $this->db->get();
	    //echo $this->db->last_query();
	    
	    echo $this->frameworkhelper->get_json_data($records, 'employmentID', array('employmentNo'=>' - ','companyAbbr'=>' / ','branchAbbr'=>' / ','employeeType'=>' / ','jobTitle'=>''));
	}

	public function get_job_positions()
	{
		$jobTitleID 	= trim($this->input->post('jobTitleID'));
		$companyID  	= trim($this->input->post('companyID'));
		$officeID   	= trim($this->input->post('branchID'));
		$divisionID 	= trim($this->input->post('divisionID'));
		$employeeTypeID = trim($this->input->post('employeeTypeID'));
		
		if ($jobTitleID) {
			$this->db->where('job_positions.jobTitleID', $jobTitleID);
		}
		if ($companyID) {
			$this->db->where('job_positions.companyID', $companyID);
		}
		if ($branchID) {
			$this->db->where('job_positions.officeID', $officeID);
		}
		if ($divisionID) {
			$this->db->where('job_positions.divisionID', $divisionID);
		}
		if ($employeeTypeID) {
			$this->db->where('job_positions.employeeTypeID', $employeeTypeID);
		}
		$this->db->select('job_positions.*');
		$this->db->select('job_titles.jobTitle');
		$this->db->from('job_positions');
		$this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID','left');
		$this->db->where('job_positions.status', 1);
		$this->db->order_by('job_positions.rank','asc');
		$records = $this->db->get();
		echo $this->frameworkhelper->get_json_data($records, 'jobPositionID', array('positionCode'=>' - ','jobTitle'=>''));
	}	


	public function get_json_row()
	{
	    $id = trim($this->input->post('id'));
	    $field = trim($this->input->post('field'));
	    $table = trim($this->input->post('table'));
	    $select = trim($this->input->post('select'));
	    $join = trim($this->input->post('join'));

	    //Select
	    $this->db->select($table.'.*');

	    if ($select != '') {
	    	$selects = explode(',', $select);
	    	foreach ($selects as $s) {
	    		$this->db->select($s);
	    	}
	    }

	    //From
	    $this->db->from($table);

	    //Join
	    if ($join != '') {
	    	$joins = explode(',', $join);
	    	foreach ($joins as $j) {
	    		$x = explode('|', $j);
	    		$this->db->join($x[0], $x[1], $x[2]);
	    	}
	    }

	    //Where
	    $this->db->where($field,$id);

	    $record = $this->db->get()->row();
	    // var_dump($record);
	    echo json_encode($record);

	}


	//The basic CRUD Requests
	public function get_table()
	{
		$select[] = $this->input->post('select');
		if ($select) {
			foreach ($select as $value) {
				$this->db->select($value);
			}
		}
		$table = $this->input->post('table');
		$query = $this->db->get($table);
		
		if ($query->num_rows() > 0) {
           echo json_encode($query->result_array());            
        }
	}

	public function insert_table()
	{
		$table = $this->input->post('table');
		$data = $this->input->post('data');
		$query = $this->db->insert($table, $data)->row();
		$id = $this->db->insert_id();
		var_dump($query);
		$msg['success'] = false;
		$msg['type'] = 'insert';
		if ($this->db->affected_rows() > 0) {
			$msg['success'] = true;
		}
		echo json_encode($msg);
		//If you want to update a UI after this request, you need to do it in the success callback in the js, it will create an error if you make the call here. 
	}

	public function replace_table()
	{
		$field = $this->input->post('field');
		$arg = $this->input->post('param');
		$table = $this->input->post('table');
		$data = $this->input->post('data');
		$this->db->where($field, $arg);
		$this->db->replace($table, $data);
		$msg['success'] = false;
		$msg['type'] = 'replace';
		if ($this->db->affected_rows() > 0) {
			$msg['success'] = true;
		}
		echo json_encode($msg);
	}

	public function update_table()
	{
		$field = $this->input->post('field');
		$arg = $this->input->post('param');
		$table = $this->input->post('table');
		$data = $this->input->post('data');
		$this->db->where($field, $arg);
		$this->db->update($table, $data);
		$msg['success'] = false;
		$msg['type'] = 'update';
		if ($this->db->affected_rows() > 0) {
			$msg['success'] = true;
		}
		echo json_encode($msg);
	}

	public function delete_table()
	{
		$field = $this->input->post('field');
		$arg = $this->input->post('param');
		$table = $this->input->post('table');
		$this->db->where($field, $arg);
		$this->db->delete($table);
		$msg['success'] = false;
		$msg['type'] = 'add';
		if ($this->db->affected_rows() > 0) {
			$msg['success'] = true;
		}
		echo json_encode($msg);
	}


	// Get with options
	public function get_table_where()
	{
		$field = $this->input->get('field');
		$arg = $this->input->get('param');
		$table = $this->input->get('table');
		$this->db->where($field, $arg);
		$query = $this->db->get($table);
		if ($query->num_rows() > 0) {
			echo json_encode($query->result());
		} else {
			echo "Unable to load data from database.";
		}
	}

	public function get_table_select_where()
	{
		$fields = $this->input->get('fields');
		$field = $this->input->get('field');
		$arg = $this->input->get('param');
		$table = $this->input->get('table');
		$this->db->select($fields);
		$this->db->where($field, $arg);
		$query = $this->db->get($table);
		if ($query->num_rows() > 0) {
			echo json_encode($query->result());
		} else {
			echo "Unable to load data from database.";
		}
	}

	public function get_orders()
	{	
		$this->db->select('order_headers.*');
		$this->db->select('service_types.serviceType');
		$this->db->from('order_headers');
		$this->db->join('service_types','order_headers.serviceID=service_types.serviceID','left');
		$this->db->where('order_headers.custID',$this->input->post('custID'));
		
	    $this->db->order_by('orderID','desc');
	    $records = $this->db->get();
	    echo $this->frameworkhelper->get_json_data($records, 'orderID', 'serviceType');
	}


    static function addSeriesNo($className, $value, $branchId)
    {

        //$data = [];
        switch ($className) {
            case 'physical_count' :
                $data = [
                    'branchID' => $branchId,
                    'pcNo' => $value,
                ];
                Generic_ajax::insertSeriesNo($data);
                break;
            case 'receiving_report' :
                $data = [
                    'branchID' => $branchId,
                    'rrNo' => $value,
                ];
                Generic_ajax::insertSeriesNo($data);
                break;
            case 'withdrawal_slip' :
                $data = [
                    'branchID' => $branchId,
                    'wsNo' => $value,
                ];
                Generic_ajax::insertSeriesNo($data);
                break;

        }

    }

    static function insertSeriesNo($data)
    {
        self::get_instance()->db->insert('seriesno', $data);
    }


    static function addSeriesNov2($className, $value, $branchId)
    {

        //$data = [];
        switch ($className) {
            case 'physical_count' :
                $data = [
                    'branchID' => $branchId,
                    'pcNo' => $value,
                ];
                Generic_ajax::insertSeriesNo($data);
                break;
            case 'receiving_report' :
                $data = [
                    'branchID' => $branchId,
                    'rrNo' => $value,
                ];
                Generic_ajax::insertSeriesNo($data);
                break;
            case 'withdrawal_slip' :
                $data = [
                    'branchID' => $branchId,
                    'wsNo' => $value,
                ];
                Generic_ajax::insertSeriesNo($data);
                break;
            case 'inventory_adjustment' :

                $column = 'adjNo';   
                Generic_ajax::updateSeriesNo($column,$value,$branchID);
                break;
                
        }

    }

    static function updateCancelBy($table, $compareId, $id, $cancelledBy)
    {
        $data = [
            'cancelledBy' => $cancelledBy,
            'dateCancelled' => date('Y-m-d h:i:s'),
        ];
        self::get_instance()->db->where($compareId, $id);
        return self::get_instance()->db->update($table, $data);
    }

    static function addStockCard($pcID, $itemID, $debit, $credit , $endbal, $begbal, $branchID)
    {

        $data  = [
            'branchID' => $branchID,
            'itemID' => $itemID,
            'date' => date('Y-m-d'),
            'refNo' => $pcID,
            'debit' => $debit,
            'credit' => $credit,
            'endbal' => $endbal,
            'begbal' => $begbal
        ];

        //return $data;
        return self::get_instance()->db->insert('stockcard', $data);
    }


    static function updateSeriesNo($column,$value,$branchID,$seriesID)
    {	
        self::get_instance()->db->set($column,$value);        
        self::get_instance()->db->where('branchID',$branchID);
        self::get_instance()->db->update('seriesno');
    }
    
    //confirm is triggered
    static function updateApproveBy($table, $compareId, $id, $approvedBy)
    {
        $data = [
            'approvedBy' => $approvedBy,
            'dateApproved' => date('Y-m-d h:i:s'),
        ];
        self::get_instance()->db->where($compareId, $id);
        return self::get_instance()->db->update($table, $data);
    }
    //update qty sequence
    static function updateQTY($table, $branchID, $itemID, $totalQty)
    {
        self::get_instance()->db->set('qty', $totalQty);
        self::get_instance()->db->where('branchID', $branchID);
        self::get_instance()->db->where('itemID', $itemID);
        return self::get_instance()->db->update($table);
    }
    //stock card sequence
    static function insertStockCard($branchID, $itemID, $refNo, $begBal,$debit,$credit,$endBal)
    {
    	//updateStockCard($branchID, $itemID, $seriesno->adjNo,$itemInventory->qty,$qty,0,$itemInventory->qty+$qty)
        $data  = [
            'branchID' => $branchID,
            'itemID' => $itemID,
            'date' => date('Y-m-d H:i:s'),
            'refNo' => $refNo,
            'begBal' => $begBal,
            'debit' => $debit,
            'credit' => $credit,
            'endBal' => $endBal            
        ];

        return self::get_instance()->db->insert('stockcard', $data);
    }



    //updating approved/confirm by column, date and status
    static function updateApprovedBy($table, $compareId, $id, $approvedBy)
    {
        $data = [
            'approvedBy' => $approvedBy,
            'dateApproved' => date('Y-m-d h:i:s'),
            'status' => 2,
        ];

        self::get_instance()->db->where($compareId, $id);
        return self::get_instance()->db->update($table, $data);	    
    }

    //updating cancelled by column, date and status
    static function updateCancelledBy($table, $compareId, $id, $cancelledBy)
    {
        $data = [
            'cancelledBy' => $cancelledBy,
            'dateCancelled' => date('Y-m-d h:i:s'),
            'status' => 0,
        ];
        self::get_instance()->db->where($compareId, $id);
        return self::get_instance()->db->update($table, $data);
    }

    static function updateDate($date, $id, $userID)
    {
        $data = [];
        switch ($date) {
            case 'dateWashed' :
                $data = [
                    'dateWashed' => date('Y-m-d h:i:s'),
                    'status' => 2,
                ];
                break;
            case 'dateFold' :
                $data = [
                    'dateFold' => date('Y-m-d h:i:s'),
                    'status' => 3,
                ];
                break;
            case 'dateReady' :
                $data = [
                    'dateReady' => date('Y-m-d h:i:s'),
                    'status' => 4,
                ];
                break;
            case 'dateReleased' :
                $data = [
                    'dateReleased' => date('Y-m-d h:i:s'),
                    'status' => 5,
                ];
                break;
            case 'dateCancelled' :
                $data = [
                    'dateCancelled' => date('Y-m-d h:i:s'),
                    'cancelledBy' => $userID,
                    'status' => 6,
                ];
                break;
        }

        self::get_instance()->db->where("orderID", $id);
        return self::get_instance()->db->update("order_headers", $data);

    }

}	