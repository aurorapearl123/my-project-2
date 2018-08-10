<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Training_program extends CI_Controller
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
        $this->module       = 'Training Program';
        $this->table        = 'employee_trainings';                                                 
        $this->pfield       = $this->data['pfield'] = 'trainingID';                                                 
        $this->logfield     = 'course';
        $this->module_path  = 'modules/employee/employee';           
        $this->data['controller_page']  = $this->controller_page = site_url('training_program');
        
        // check for maintenance period
        if ($this->config_model->getConfig('Maintenance Mode')=='1') {
            header('location: '.site_url('maintenance_mode'));
        }
        
        // check user session
        if (!$this->session->userdata('current_user')->sessionID) {
            header('location: '.site_url('login'));
        }
    }

    private function check_roles()
    {
        // check roles
        $this->roles['create']  = $this->userrole_model->has_access($this->session->userdata('current_userID'),'Add '.$this->module);
        $this->roles['view']    = $this->userrole_model->has_access($this->session->userdata('current_userID'),'View '.$this->module);
        $this->roles['edit']    = $this->userrole_model->has_access($this->session->userdata('current_userID'),'Edit Existing '.$this->module);
        $this->roles['delete']  = $this->userrole_model->has_access($this->session->userdata('current_userID'),'Delete Existing '.$this->module);
    }
    
    public function index()
    {
    }
    
    public function save()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
        
        $table_fields = array('course', 'organizer', 'venue', 'noHours', 'remarks');

        // check role
        if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'Add '.$this->module)) {
            $this->records->table = $this->table;
            $this->records->fields = array();
            
            foreach($table_fields as $fld) {
                $this->records->fields[$fld] = trim($this->input->post($fld));
            }
            
            if (trim($this->input->post('startDate')) != '') {
                $this->db->set('startDate', date('Y-m-d', strtotime(trim($this->input->post('startDate')))));
            }
            if (trim($this->input->post('endDate')) != '') {
                $this->db->set('endDate', date('Y-m-d', strtotime(trim($this->input->post('endDate')))));
            }
            $this->records->fields['empID']    = $this->encrypter->decode($this->input->post('empID'));
            
            if ($this->records->save()) {
                $this->records->fields = array();
                $pkey = $this->records->where[$this->pfield] = $this->db->insert_id();
                $this->records->retrieve();  
                
				// record logs
				$pfield = $this->pfield;
				$logs = "Record - ".trim($this->input->post($this->logfield));
				$this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->records->field->$pfield, 'Insert', $logs);
				
				$logfield = $this->pfield;
				// success msg
				$response->status  = 1;
                $response->message = $this->module." successfully saved.";
               
            } else {
                // error
                $response->message = "Error in saving the ".strtolower($this->module)."!";
            }
        } else {
            // error
            $response->message = "Sorry, you don't have access to this page!";
        }
        
        echo json_encode($response);
    }
    
    public function update()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
        
        $table_fields = array('course', 'organizer', 'venue', 'noHours', 'remarks');

        // check roles
        if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'Edit Existing '.$this->module)) {
            $this->records->table = $this->table;
            $this->records->fields = array();
            
            foreach($table_fields as $fld) {
                $this->records->fields[$fld] = trim($this->input->post($fld));
            }
            
            if (trim($this->input->post('startDate')) != '') {
                $this->db->set('startDate', date('Y-m-d', strtotime(trim($this->input->post('startDate')))));
            }
            if (trim($this->input->post('endDate')) != '') {
                $this->db->set('endDate', date('Y-m-d', strtotime(trim($this->input->post('endDate')))));
            }
            
            $this->records->pfield   = $this->pfield;
            $this->records->pk       = $this->input->post($this->pfield);
            
            // field logs here
            $wasChange = $this->log_model->field_logs($this->module, $this->table, $this->pfield, $this->input->post($this->pfield), 'Update', $this->records->fields);
            
            if ($this->records->update()) {
                // record logs
                if ($wasChange) {
                    $logs = "Record - ".trim($this->input->post($this->logfield));
                    $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->records->pk, 'Update', $logs);
                }
                    
                // successful
                $response->status  = 1;
                $response->message = $this->module." successfully updated.";
            } else {
                // error
                $response->message = "Error in updating the ".$this->module."!";
            }
        } else {
            // error
            $response->message = "Sorry, you don't have access to this page!";
        }
        
        echo json_encode($response);
    }

    public function delete($id=0)
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
        
        $id = trim($this->input->post('trainingID'));
        
        // check roles
        if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'Delete Existing '.$this->module)) {
            // set fields
            $this->records->fields = array();
            // set table
            $this->records->table = $this->table;
            // set where
            $this->records->where[$this->pfield] = $id;
            // execute retrieve
            $this->records->retrieve();
            
            if (!empty($this->records->field)) {
                $this->records->pfield   = $this->pfield;
                $this->records->pk       = $id;
                
                // record logs
                $logfield = $this->records->field->name;
                                
                if ($this->records->delete()) {
                    
                    // record logs
					$logs = "Record - ".$logfield;
					$this->log_model->table_logs($this->module, $this->table, $this->pfield, $id, 'Delete', $logs);
                    
                    // successful
					$response->status  = 1;
                    $response->message = $this->module." successfully deleted.";
                } else {
                    // error
                    $response->message = "Error in deleting the ".$this->module."!";
                }
            } else {
                // error
                $response->message = $this->module." record not found!";
            }
        } else {
            // error
            $response->message = "Sorry, you don't have access to this page!";
        }
        
        echo json_encode($response);
    }
    
    public function view()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
        
        $id = trim($this->input->post($this->pfield));
        
        if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'View '.$this->module)) {
            // for retrieve with joining tables -------------------------------------------------
            $this->db->select($this->table.".*");
            $this->db->select('DATE_FORMAT('.$this->table.'.startDate, "%M %d %Y") as startDate');
            $this->db->select('DATE_FORMAT('.$this->table.'.endDate, "%M %d %Y") as endDate');
            $this->db->from($this->table);
            $this->db->where($this->pfield, $id);
            // ----------------------------------------------------------------------------------
            $record = $this->db->get()->row();
            
            // record logs
            if ($this->config_model->getConfig('Log all record views') == '1') {
                $pfield   = $this->pfield;
                $logfield = $this->logfield;
                $logs     = "Record - ".$record->$logfield;
                $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->records->field->$pfield, 'View', $logs);
            }
            
            $response->status  = 1;
            $response->record  = $record;
        } else {
            // error
            $response->message = "Sorry, you don't have access to this page!";
        }
        
        echo json_encode($response);
    }
    
    public function show()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        // check role
        if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'View '.$this->module)) {
            $empID    = $this->encrypter->decode($this->input->post('empID'));
        
            $this->db->select('employee_trainings.*');
            $this->db->select('DATE_FORMAT('.$this->table.'.startDate, "%m/%d/%Y") as startDate');
            $this->db->select('DATE_FORMAT('.$this->table.'.endDate, "%m/%d/%Y") as endDate');
            $this->db->where('employee_trainings.empID', $empID);
            $records = $this->db->get('employee_trainings')->result();
            	
            $response->status  = 1;
            $response->records  = $records;
        } else {
            // error
            $response->message = "Sorry, you don't have access to this page!";
        }
    
        echo json_encode($response);
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
            array('variable'=>'code', 'field'=>$this->table.'.code', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'name', 'field'=>$this->table.'.name', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'isTaxable', 'field'=>$this->table.'.isTaxable', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'isBasic', 'field'=>$this->table.'.isBasic', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'abbr', 'field'=>$this->table.'.abbr', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('name'=>'asc');
        
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
        
        $data['current_module']['module_label']." List";

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
            array('variable'=>'code', 'field'=>$this->table.'.code', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'name', 'field'=>$this->table.'.name', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'isTaxable', 'field'=>$this->table.'.isTaxable', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'isBasic', 'field'=>$this->table.'.isBasic', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'abbr', 'field'=>$this->table.'.abbr', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('name'=>'asc');
        
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
        $records = $this->db->get()->result();
        
    
        $title          = $data['current_module']['module_label']." List";
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
    		<Column ss:Index='2' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
    		<Column ss:Index='3' ss:AutoFitWidth=\"1\" ss:Width='200.00'/>
    		<Column ss:Index='4' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
    		<Column ss:Index='5' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='6' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
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
    
        $fields[]="#";
        $fields[]="ACCOUNT CODE";
        $fields[]="INCENTIVE NAME";
        $fields[]="ABBREVIATION";
        $fields[]="TAXABLE";
        $fields[]="BASIC";
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
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->code."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->name."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->abbr."</Data></Cell>";
                if  ($row->isTaxable == 1) {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'>Taxable</Data></Cell>";
                } else {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'></Data></Cell>";
                }
                if  ($row->isBasic == 1) {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'>Basic</Data></Cell>";
                } else {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'></Data></Cell>";
                }
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
        $filename = "Incentive Type List";
    
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$filename.xls;");
        header("Content-Type: application/ms-excel");
        header("Pragma: no-cache");
        header("Expires: 0");
         
        echo $data;
    
    }
}
