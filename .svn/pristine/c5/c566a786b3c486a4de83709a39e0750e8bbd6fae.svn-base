<form name="frmFilter" id="frmFilter" method="POST" action="<?php echo $controller_page ?>/show">
<input type="hidden" id="sortby" name="sortby" value="<?php echo $sortby ?>" />
<input type="hidden" id="sortorder" name="sortorder" value="<?php echo $sortorder ?>" />
          <div class="subheader">
            <div class="d-flex align-items-center">
              <div class="title mr-auto">
                <h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></span></h3>
              </div>
              <?php if ($roles['create']) {?>
              <div class="subheader-tools">
                <a href="<?php echo $controller_page?>/create" class="btn btn-primary btn-raised btn-xs pill"><i class="icon left la la-plus lg"></i>Add New</a>
              </div>
              <?php } ?>
            </div>
          </div>
          <div class="content">
            <div class="row">
              <div class="col-12">
                <div class="card-box full-body">
                  <div class="card-head">
                    <div class="head-caption">
                      <div class="head-title">
                        <h4 class="head-text"><?php echo $current_module['module_label'] ?> List</h4>
                      </div>
                    </div>
                    <div class="card-head-tools">
                        <ul class="tools-list">
                            <li>
                              <button id="btn-apply" type="submit" class="btn btn-primary btn-xs pill collapse multi-collapse show">Apply Filter</button>
                            </li>
                            <li>
                              <button type="button" id="btn-filter" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Filters" onclick="#"><i class="la la-sort-amount-asc"></i></button>
                            </li>
                            <?php if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Print Employments')) { ?>
                            <li>
                              <button type="button" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Print" onclick="popUp('<?php echo site_url('employment/printlist') ?>', 800, 500)"><i class="la la-print"></i></button>
                            </li>
                            <?php } ?>
                            <?php if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Export Employments')) { ?>
                            <li>
                              <button type="button" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Export to Excel File" onclick="window.location='<?php echo site_url('employment/exportlist') ?>';"><i class="la la-file-excel-o"></i></button>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
        </div>
    <div class="card-body">

      <!-- Table Wrapper Start Here -->
      <div class="datatables_wrapper">
        <!-- Table Innere Wrapper Start Here -->
        <div class="table-responsive-xl">



          <!-- Table Start Here -->
          <table class="table table-striped hover">

                     <thead>
                            <tr>
                                <?php 
                                $headers = array(
                                    array('column_header'=>'Date Hired','column_field'=>'dateAppointed','width'=>'w-5','align'=>''),
                                    array('column_header'=>'Employment No.','column_field'=>'employmentNo','width'=>'w-5','align'=>''),
                                    array('column_header'=>'Employee','column_field'=>'lname','width'=>'w-10','align'=>''),
                                    array('column_header'=>'Branch','column_field'=>'branchName','width'=>'w-10','align'=>''),
                                    array('column_header'=>'Dept','column_field'=>'deptName','width'=>'w-10','align'=>''),
                                    array('column_header'=>'Section','column_field'=>'divisionName','width'=>'w-10','align'=>''),
                                    array('column_header'=>'Employment','column_field'=>'employmentType','width'=>'w-10','align'=>''),
                                    array('column_header'=>'Position','column_field'=>'jobTitle','width'=>'w-10','align'=>'center'),
                                    array('column_header'=>'Status','column_field'=>'status','width'=>'w-5','align'=>'center')
                                );
                    
                                echo $this->htmlhelper->tabular_header($headers, $sortby, $sortorder);
                                ?>
                            </tr>
                            <tr id="filter-group" class="collapse multi-collapse table-filter show">
                              <th class="form-group form-input">
                                    <input type="text" class="form-control datepicker" name="dateAppointed" id="dateAppointed" value="<?php if ($dateAppointed) echo date("F d, Y", strtotime($dateAppointed)) ?>" data-toggle="datetimepicker" data-target="#dateAppointed">
                              </th>
                               <th class="form-group form-input">
                              <input type="text" class="form-control"  name="employmentNo" id="employmentNo" value="<?php echo $employmentNo ?>">
                            </th>
                            <th class="form-group form-input">
                              <input type="text" class="form-control"  name="lname" id="lname" value="<?php echo $lname ?>">
                            </th>
                           <th class="form-group form-input">
                                 <select id="branchID" name="branchID" class="form-control" data-live-search="true" liveSearchNormalize="true" onchange="get_departments();">
                                 <option value="">&nbsp;</option>
                                  <?php           
                                  $this->db->where('status', 1);
                                  $this->db->order_by('branchName','asc');
                                  $results = $this->db->get('branches')->result();
                                  foreach($results as $res){
                                    ?>
                                    <option value="<?php echo $res->branchID ?>" <?php if ($res->branchID == $branchID) echo "selected"; ?>><?php echo $res->branchCode ?></option>
                                  <?php }?>
                                </select>
                          </th>
                          <th class="form-group form-input">
                                 <select id="deptID" name="deptID" class="form-control" data-live-search="true" liveSearchNormalize="true" onchange="get_sections();">
                                 <option value="">&nbsp;</option>
                                  <?php           
                                  $this->db->where('status', 1);
                                  $this->db->order_by('deptName','asc');
                                  $results = $this->db->get('departments')->result();
                                  foreach($results as $res){
                                    ?>
                                    <option value="<?php echo $res->deptID ?>" <?php if ($res->deptID == $deptID) echo "selected"; ?>><?php echo $res->deptName ?></option>
                                  <?php }?>
                                </select>
                          </th>
                          <th class="form-group form-input">
                                 <select id="divisionID" name="divisionID" class="form-control" data-live-search="true" liveSearchNormalize="true">
                                 <option value="">&nbsp;</option>
                                  <?php           
                                  $this->db->where('status', 1);
                                  $this->db->order_by('divisionName','asc');
                                  $results = $this->db->get('divisions')->result();
                                  foreach($results as $res){
                                    ?>
                                    <option value="<?php echo $res->divisionID ?>" <?php if ($res->divisionID == $divisionID) echo "selected"; ?>><?php echo $res->divisionName ?></option>
                                  <?php }?>
                                </select>
                          </th>   
                            <th class="form-group form-input">
                                 <select id="employeeTypeID" name="employeeTypeID" class="form-control" data-live-search="true" liveSearchNormalize="true">
                                 <option value="">&nbsp;</option>
                                  <?php           
                                  $this->db->where('status', 1);
                                  $this->db->order_by('employeeType','asc');
                                  $results = $this->db->get('employee_types')->result();
                                  foreach($results as $res){
                                    ?>
                                    <option value="<?php echo $res->employeeTypeID ?>" <?php if ($res->employeeTypeID == $employeeTypeID) echo "selected"; ?>><?php echo $res->employeeType ?></option>
                                  <?php }?>
                                </select>
                              </th>
                               <th class="form-group form-input">
                                 <select id="jobTitleID" name="jobTitleID" class="form-control" data-live-search="true" liveSearchNormalize="true">
                                 <option value="">&nbsp;</option>
                                  <?php           
                                  $this->db->where('status', 1);
                                  $this->db->order_by('jobTitle','asc');
                                  $results = $this->db->get('job_titles')->result();
                                  foreach($results as $res){
                                    ?>
                                    <option value="<?php echo $res->jobTitleID ?>" <?php if ($res->jobTitleID == $jobTitleID) echo "selected"; ?>><?php echo $res->jobTitle ?></option>
                                  <?php }?>
                                </select>
                              </th>
                              <th>
                                <select class="form-control" id="status" name="status" style="width:100px">
                                  <option value="">&nbsp;</option>
                                  <option value="1" <?php if($status == "1") echo "selected"; ?>>Active</option>
                                  <option value="2" <?php if($status == "2") echo "selected"; ?>>Re-assigned</option>
                                  <option value="3" <?php if($status == "3") echo "selected"; ?>>Promoted</option>
                                  <option value="4" <?php if($status == "4") echo "selected"; ?>>Demoted</option>
                                  <option value="5" <?php if($status == "5") echo "selected"; ?>>Terminated</option>
                                  <option value="0" <?php if($status == "0") echo "selected"; ?>>Inactive</option>
                                </select>
                              </th>
                            </tr>
                          </thead>
            <tbody>

              <!-- Row Start Here -->

                	<?php
                	if ($records) {
                		foreach($records as $row) {
                			$id = $this->encrypter->encode($row->employmentID);
                	?>
                              <!-- Row Start Here -->
                              <tr onclick="location.href='<?php echo $controller_page ?>/view/<?php echo $id ?>'">
                                <td><?php echo date('m/d/Y', strtotime($row->dateAppointed)) ?></td>
                                <td><?php echo $row->employmentNo ?></td>
                                <td><?php echo $row->lname.", ".$row->fname." ".$row->mname." ".$row->suffix ?></td>
                                <td><?php echo $row->branchCode ?></td>
                                <td><?php echo $row->deptCode ?></td>
                                <td><?php echo $row->divisionCode ?></td>
                                <td><?php echo $row->employeeType ?></td>
                                <td><?php echo $row->jobTitle ?></td>
                                <td>
                                <?php 
                				 switch ($row->status) {
                					case "1" : echo "<span class='badge badge-pill badge-success'>Active</span>"; break;
                					case "2" : echo "<span class='badge badge-pill badge-info'>Re-assigned</span>"; break;
                					case "3" : echo "<span class='badge badge-pill badge-success'>Promoted</span>"; break;
                					case "4" : echo "<span class='badge badge-pill badge-warning'>Demoted</span>"; break;
                					case "5" : echo "<span class='badge badge-pill badge-danger'>Terminated</span>"; break;
                					case "0" : echo "<span class='badge badge-pill badge-danger'>Inactive</span>"; break;
                				}?>
                                </td>
                              </tr>
                              <!-- Row End Here -->
                              
                    <?php
                    	}
                    } else {
                    ?>
                        <tr>
                    		<td colspan="20" class="oddListRowS1">
                            	<table border="0" cellpadding="0" cellspacing="0" width="100%">
                            	<tbody>
                            	<tr>
                            		<td nowrap="nowrap" align="center"><b><i>No results found.</i></b></td>
                            	</tr>
                            	</tbody>
                            	</table>
                    		</td>
                    	</tr>
                	<?php
                	}
                	?>
              <!-- Row End Here -->

            </tbody>
          </table>
          <!-- Table End Here -->
        </div>
        <!-- Table Inner Wrapper End Here -->

        <!-- Table Footer Start Here -->
                      <div class="datatable-footer d-flex">
                        <div class="datatable-pagination">
                            Pages: &nbsp;&nbsp; 
                        </div>
                        <div class="datatable-pagination">
                        <?php 
                        $pagination = $this->pagination->create_links(); 
                        
                        if ($pagination) {
                             echo $pagination;      
                        } else {
                            echo "1";
                        }
                        ?>
                  
                        </div>
                        <div class="datatable-pager-info float-right ml-auto">
                          <div class="d-flex">
                            <div class="datatable-pager-size">
                              <div class="dataTables_length">
                                <select aria-controls="table1" class="form-control select-sm pill" tabindex="-98" id="limit" name="limit" onchange="$('#frmFilter').submit();">
                                  <option value="25" <?php if ($limit==25) echo "selected"; ?>>25</option>
                                  <option value="50" <?php if ($limit==50) echo "selected"; ?>>50</option>
                                  <option value="75" <?php if ($limit==75) echo "selected"; ?>>75</option>
                                  <option value="100" <?php if ($limit==100) echo "selected"; ?>>100</option>
                                  <option value="125" <?php if ($limit==125) echo "selected"; ?>>125</option>
                                  <option value="150" <?php if ($limit==150) echo "selected"; ?>>150</option>
                                  <option value="175" <?php if ($limit==175) echo "selected"; ?>>175</option>
                                  <option value="200" <?php if ($limit==200) echo "selected"; ?>>200</option>
                                  <option value="all" <?php if ($limit=='all') echo "selected"; ?>>All</option>
                                </select>
                              </div>
                            </div>
                            <div class="datatable-pager-detail">
                              <div class="dataTables_info">Displaying <?php echo ($offset+1) ?> - <?php if ($limit!='all') { if ($offset+$limit < $ttl_rows) { echo ($offset+$limit); } else  { echo $ttl_rows; } } else { echo number_format($ttl_rows,0); } ?> of <?php echo number_format($ttl_rows,0) ?> records</div>
                            </div>
                          </div>
                        </div>
          <!-- Table Footer Right Column End Here -->
        </div>
        <!-- Table Footer End Here -->
      </div>
      <!-- Table Wrapper End Here -->
    </div>
  </div>
</div>
</div>
</div>

</form>



<script>

<?php 
    echo "\n";
    $parameters = array('branchID');
    echo $this->htmlhelper->get_json_select('get_departments', $parameters, site_url('generic_ajax/get_code_departments'), 'deptID', '');
    
    echo "\n";
    $parameters = array('deptID');
    echo $this->htmlhelper->get_json_select('get_sections', $parameters, site_url('generic_ajax/get_code_sections'), 'divisionID', '');
?>



$('#deptID').click(function(){
	alert($(this).val());
});
					      
</script>
