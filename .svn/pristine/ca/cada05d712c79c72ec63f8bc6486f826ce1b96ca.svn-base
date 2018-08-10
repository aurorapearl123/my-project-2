
<div class="subheader">
  <div class="d-flex align-items-center">
    <div class="title mr-auto">
      <h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></h3>
    </div>
    <div class="subheader-tools">
      <a href="<?php echo $controller_page; ?>" class="btn btn-primary btn-raised btn-xs pill"><i class="icon ti-angle-left"></i> Back to List</a>
    </div>
  </div>
</div>
<div class="content">
  <div class="row">
    <div class="col-12">
      <div class="card-box">
        <div class="card-head">
          <div class="head-caption">
            <div class="head-title">
              <h4 class="head-text">View <?php echo $current_module['module_label'] ?></h4>
            </div>
          </div>
          
          <div class="card-head-tools">
            <ul class="tools-list">
              <?php if ($roles['re-assign'] && $rec->status==1) {?>
              <li>
                <a href="javascript: reassign();" id="reassignBtn" class="btn btn-primary btn-raised btn-xs pill"><i class="icon ti-direction-alt"></i> Re-assign</a>
              </li>
              <?php } ?>
              <?php if ($roles['promote'] && $rec->status==1) {?>
              <li>
                <a href="javascript: promote();" id="promoteBtn" class="btn btn-primary btn-raised btn-xs pill"><i class="icon ti-flag-alt"></i> Promote</a>
              </li>
              <?php } ?>
              <?php if ($roles['demote'] && $rec->status==1) {?>
              <li>
                <a href="javascript: demote();" id="demoteBtn" class="btn btn-primary btn-raised btn-xs pill"><i class="icon ti-thumb-down"></i> Demote</a>
              </li>
              <?php } ?>
              <?php if ($roles['terminate'] && $rec->status==1) {?>
              <li>
                <a href="javascript: terminate();" id="terminateBtn" class="btn btn-primary btn-raised btn-xs pill"><i class="icon ti-alert"></i> Terminate</a>
              </li>
              <?php } ?>
              <?php if ($roles['edit'] && $rec->status==1) {?>
              <li>
                <a href="<?php echo $controller_page.'/edit/'.$this->encrypter->encode($rec->employmentID); ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>
              </li>
              <?php } ?>
              <?php if ($roles['delete'] && !$in_used && $rec->status==1) {?>
              <li>
                <button name="cmddelete" id="cmddelete" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" onclick="deleteRecord('<?php echo $this->encrypter->encode($rec->employmentID) ?>');"><i class="la la-trash-o"></i></button>
              </li>
              <?php } ?>
              <?php if ($this->session->userdata('current_user')->isAdmin) {?>
              <li>
                <button type="button" id="recordlog" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Record Logs" onclick="popUp('<?php echo site_url('logs/record_log/employments/employmentID/'.$this->encrypter->encode($rec->employmentID).'/Employment') ?>', 1000, 500)"><i class="la la-server"></i></button>
              </li>
              <?php } ?>
            </ul>
          </div>
        </div>
        

        <div class="card-body">
          <div class="data-view">
            <!-- First Table Start -->
            <table class="view-table column-3">

                <tbody>
                  <tr>
                    <td class="data-title" width="12%">Employment No:</td>
                    <td class="data-input" width="30%"><?php echo $rec->employmentNo; ?></td>
                    <td class="data-title" width="12%">Position No:</td>
                    <td class="data-input" width=""><?php echo $rec->positionCode?></td>
                    
                    <td class="data-input"></td>
                  </tr>
                  <tr>
                    <td class="data-title">Employee:</td>
                    <td class="data-input"><?php echo $rec->lname." , ".$rec->fname." ".$rec->mname." ".$rec->suffix; ?></td>
                    <td class="data-title">
                    <?php 
                    switch($rec->status) {
                        case 1:
                            echo "Date Hired";
                            break;
                        case 2:
                            echo "Date Covered";
                            break;
                        case 3:
                            echo "Date Covered";
                            break;
                        case 4:
                            echo "Date Covered";
                            break;
                        case 5:
                            echo "Date Covered";
                            break;
                        case 0:
                            echo "Date Hired";
                            break;
                    }
                    ?>
                    :</td>
                    <td class="data-input"><?php echo date('F d Y', strtotime($rec->dateAppointed)) ?> <?php echo ($rec->dateTerminated!="0000-00-00") ? " - ".date('F d Y', strtotime($rec->dateTerminated)) : "" ?></td>
                    
                    <td class="data-input"></td>
                  </tr>
                  <tr>
                    <td class="data-title">Branch:</td>
                    <td class="data-input"><?php echo $rec->branchName?></td>
                    <td class="data-title">Department:</td>
                    <td class="data-input"><?php echo $rec->deptName; ?></td>
                    
                    <td class="data-input"></td>
                  </tr>
                  <tr>
                    <td class="data-title">Section:</td>
                    <td class="data-input"><?php echo $rec->divisionName; ?></td>
                    <td class="data-title">Employment Type:</td>
                    <td class="data-input"><?php echo $rec->employeeType?></td>
                    
                    <td class="data-input"></td>
                  </tr>
                  <tr>
                    <td class="data-title">Job Position:</td>
                    <td class="data-input"><?php echo $rec->jobTitle ?></td>
                    <td class="data-title">Basic Salary:</td>
                    <td class="data-input">
                    <?php echo number_format($rec->basicSalary, 2) ?> 
                    ( 
                     <?php 
                     switch ($rec->salaryType) {
                       case "1" : echo "Monthly"; break;
                       case "2" : echo "Daily"; break;
                       case "3" : echo "Hourly"; break;
                     }?>
                     )
                   </td>
                   
                    <td class="data-input">
                  </tr>
                  <tr>
                    <td class="data-title">Payroll Group:</td>
                    <td class="data-input"><?php echo $rec->payrollGroup ?></td>
                    <td class="xl-none"></td>
                  </tr>
                  <tr>
                    <td class="data-title">Status:</td>
                    <td class="data-input">
                      <?php if($rec->status==1) {?>
                       <span class='badge badge-pill badge-success'><i class="icon left la la-check"></i> Active</span>  
                    <?php } else if($rec->status==0) {?>
                      <span class='badge badge-pill badge-danger'><i class="icon left la la-ban"></i> Inactive</span>  
                    <?php } else if($rec->status==2) {?>
                      <span class='badge badge-pill badge-primary'><i class="icon left la la-hand-0-right"></i> Re-assigned - <?php echo date("F d, Y", strtotime($rec->dateTerminated))  ?></span>  
                    <?php } else if($rec->status==3) {?>
                      <span class='badge badge-pill badge-primary'><i class="icon left la la-thumbs-up"></i> Promoted - <?php echo date("F d, Y", strtotime($rec->dateTerminated))  ?></span>  
                    <?php } else if($rec->status==4) {?>
                      <span class='badge badge-pill badge-primary'><i class="icon left la la-hands-o-down"></i> Demoted - <?php echo date("F d, Y", strtotime($rec->dateTerminated))  ?></span>  
                    <?php } else if($rec->status==5) {?>
                    <span class='badge badge-pill badge-danger'><i class="icon left la la-user-times"></i> Terminated - <?php echo date("F d, Y", strtotime($rec->dateTerminated))  ?> </span>   
                    <?php }?>
                    <?php if($rec->isBiometric==1) {?>
                      <span class='badge badge-pill badge-success'><i class="icon left la la-hand-pointer-o"></i> Biometric</span>  
                    <?php } else if($rec->isBiometric==0) {?>
                      <span class='badge badge-pill badge-danger'><i class="icon left la la-user"></i> Biometric Exempted</span>      
                    <?php }?>
                    <?php if($rec->withBasicContribution==1) {?>
                      <span class='badge badge-pill badge-success'><i class="icon left la la-money"></i> With Basic Contribution</span>               
                    <?php }?> 
                    </td>
                    <td class="xl-none"></td>
                  </tr>
              </tbody>
            </table>
            <!-- First Table End -->
            
            
          <!-- Third Table End -->


        </div>
      </div>
    </div>
  </div>
</div>
</div>









<!-- Add Duties & Responsibilities Modal -->
<div class="modal fade" id="addDutiesModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form method="post" name="frmEntry2" id="frmEntry2" action="">
        <div class="modal-header">
          <h4 class="modal-title">Add Duties & Responsibilities</h4>
        </div>
        <div class="modal-body">
          <div class="table-row">
            <table class="table-form">
              <tbody>
                <tr>
                  <td class="form-label">
                    <label for="employee">Employee <span class="asterisk">*</span></label>
                  </td>
                  <td class="form-group form-input">
                    <select name="empID2" id="empID2" class="form-control" data-live-search="true" liveSearchNormalize="true">
                      <?php           
                      $this->db->where('status', 1);
                        // $this->db->order_by('employeeName','asc');
                      $results = $this->db->get('employees')->result();
                      foreach($results as $res){
                        ?>
                        <option value="<?php echo $this->encrypter->encode($res->empID) ?>"><?php echo $res->empNo.' - '.$res->lname.', '.$res->fname.' '.$res->mname ?></option>
                      <?php }?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class="form-label">
                    <label for="fmname">Employment <span class="asterisk">*</span></label>
                  </td>
                  <td class="form-group form-input">
                    <select name="employmentID2" id="employmentID2" class="form-control" data-live-search="true" liveSearchNormalize="true">
                      <?php       
                      $this->db->select('employments.employmentID');
                      $this->db->select('employments.employmentNo');
                      $this->db->select('companies.companyName');
                      $this->db->select('branches.branchName');
                      $this->db->select('divisions.divisionName');
                      $this->db->select('employee_types.employeeType');
                      $this->db->select('job_positions.positionCode');
                      $this->db->select('job_titles.jobTitle');
                      $this->db->from('employments');
                      $this->db->join('companies','employments.companyID=companies.companyID', 'left');
                      $this->db->join('branches','employments.branchID=branches.branchID', 'left');
                      $this->db->join('divisions','employments.divisionID=divisions.divisionID', 'left');
                      $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID', 'left');
                      $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
                      $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
                      // $this->db->where('employments.empID', $this->encrypter->decode($rec->empID));
                      // $this->db->where('employments.status', 1);
                      // $this->db->order_by('employments.dateAppointed','asc');
                      $results = $this->db->get()->result();
                      foreach($results as $res){
                        ?>
                        <option value="<?php echo $res->employmentID ?>"><?php echo $res->employmentNo.' - '.$res->companyName.', '.$res->branchName.', '.$res->employeeType. ', '.$res->jobTitle; ?></option>
                        <!-- var_dump($res); -->
                        <!-- $res->employmentNo.' - '.$res->companyName.', '.$res->branchName.', '.$res->employeeType. ', '.$res->jobTitle; -->
                      <?php }?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class="form-label">
                    <label for="fmname">Duty <span class="asterisk">*</span></label>
                  </td>
                  <td class="form-group form-input">
                    <textarea class="form-control" name="duty2" id="duty2"></textarea>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="cmdSave2" class="btn btn-primary btn-raised pill">Save</button>
          <button type="button" class="btn btn-outline-danger btn-raised pill" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>







<script type="text/javascript">

function terminate() {
	swal({
	      title: "Confirm Termination?",
	      text: "",
	      icon: "warning",
	      showCancelButton: true,
	      confirmButtonColor: '#3085d6',
	      cancelButtonColor: '#d33',
	      confirmButtonText: 'Yes',
	      cancelButtonText: 'No'
	    })
	    .then((willDelete) => {
	      if (willDelete.value) {
	    	  window.location = '<?php echo $controller_page.'/terminate/'.$this->encrypter->encode($rec->employmentID); ?>';
	      }
	    });
}

function reassign()
{
	swal({
	      title: "Confirm Re-assignment?",
	      text: "",
	      icon: "warning",
	      showCancelButton: true,
	      confirmButtonColor: '#3085d6',
	      cancelButtonColor: '#d33',
	      confirmButtonText: 'Yes',
	      cancelButtonText: 'No'
	    })
	    .then((willDelete) => {
	      if (willDelete.value) {
	    	  window.location = '<?php echo site_url('employment/set_reassignment/'.$this->encrypter->encode($rec->employmentID)) ?>';
	      }
	    });
	
}


function promote()
{
	swal({
	      title: "Confirm Promotion?",
	      text: "",
	      icon: "warning",
	      showCancelButton: true,
	      confirmButtonColor: '#3085d6',
	      cancelButtonColor: '#d33',
	      confirmButtonText: 'Yes',
	      cancelButtonText: 'No'
	    })
	    .then((willDelete) => {
	      if (willDelete.value) {
	    	  window.location = '<?php echo site_url('employment/set_promotion/'.$this->encrypter->encode($rec->employmentID)) ?>';
	      }
	    });
	
}

function demote()
{
	swal({
	      title: "Confirm Demotion?",
	      text: "",
	      icon: "warning",
	      showCancelButton: true,
	      confirmButtonColor: '#3085d6',
	      cancelButtonColor: '#d33',
	      confirmButtonText: 'Yes',
	      cancelButtonText: 'No'
	    })
	    .then((willDelete) => {
	      if (willDelete.value) {
	    	  window.location = '<?php echo site_url('employment/set_demotion/'.$this->encrypter->encode($rec->employmentID)) ?>';
	      }
	    });
	
}

</script>


