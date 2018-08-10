          <div class="subheader">
            <div class="d-flex align-items-center">
              <div class="title mr-auto">
                <h3><i class="icon left la la-group"></i> Employee Profile</h3>
              </div>
              <div class="subheader-tools">
                <a href="<?php echo $controller_page?>/show" class="btn btn-primary btn-raised btn-sm pill"><i class="icon ti-angle-left"></i> Back to List</a>
              </div>
            </div>
          </div>
          <div class="content">
            <div class="row">
              <div class="col-xl-3">
                <div class="card-box full-height">
                  <div class="card-body">
                    <div class="card-user py-35">
                      <div class="card-user-pic">
                        <?php 
		                $image = "assets/records/employee/".$rec->empID."/avatar".$rec->imageExtension; 
		    			if ( ! is_file($image)) {
		    				// no image
							if ($rec->sex=="M") {
								 $image = "assets/records/employee/male_no_image.png";
							} else {
								 $image = "assets/records/employee/female_no_image.png";
							}
		    			}
		                ?>
                        <img src="<?php echo base_url($image)?>" class="text-center rounded" alt="" width="40">
                        <button type="button" class="btn bmd-btn-icon" data-toggle="modal" data-target="#modal1"><i class="la la-camera-retro"></i></a>
                      </div>
                      <div class="card-user-details mt-25">
                        <span class="card-user-name"><?php echo $rec->fname.' '.$rec->lname?></span>
                        <span class="card-user-position d-block mt-10"><?php echo $employment->jobTitle?></span>
                        <span class="card-user-position d-block">ID: <?php echo $rec->empNo?></span>
                        <span class="card-user-position d-block">Status: 
                        	<?php 
                              	if($rec->status == 1){
                              		echo "<span class='badge badge-pill badge-success'>Active</span>";
                              	}elseif($rec->status == 0){
                              		echo "<span class='badge badge-pill badge-light'>Inactive</span>";
                              	}elseif($rec->status == 2){
                              		echo "<span class='badge badge-pill badge-info'>Retired</span>";
                              	}elseif($rec->status == 3){
                              		echo "<span class='badge badge-pill badge-dark'>Deceased</span>";
                              	}
                             ?>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-9">
                <div class="card-box">
                  <div class="card-head">
                    <div class="head-caption">
                      <div class="head-title">
                        <h4 class="head-text"><?php echo $rec->fname.' '.$rec->mname.' '.$rec->lname.' '.$rec->suffix.' '.$rec->title?></h4>
                      </div>
                    </div>
                    <div class="card-head-tools">
                      <ul class="tools-list">
                       <?php if ($roles['edit']) {?>
                          <li>
                            <a href="<?php echo $controller_page ?>/edit/<?php echo $this->encrypter->encode($rec->$pfield)?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>
                          </li>
                          <?php } ?>
                       <!-- <li>
                          <button class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Re-Enroll Fingerprint"><i class="la la-hand-o-up"></i></button>
                        </li>
                        <li>
                          <button class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Print"><i class="la la-print"></i></button>
                        </li> --> 
                        <li>
                            <button type="button" id="recordlog" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Record Logs" onclick="popUp('<?php echo site_url('logs/record_log/employees/'.$pfield.'/'.$this->encrypter->encode($rec->$pfield).'/Employee') ?>', 1000, 500)"><i class="la la-server"></i></button>
                          </li>
                      </ul>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-12">
                        <div class="subtitle">
                          <h5 class="title"><i class="icon left la la-user-secret"></i> Personal Information</h5>
                        </div>
                        <div class="data-view">
                          <table class="view-table">
                            <tbody>
                              <tr>
                                <td class="data-title w-15">ID Number:</td>
                                <td class="data-input w-30"><?php echo $rec->empNo?></td>
                                <td class="w-15"></td>
                                <td class="w-30"></td>
                              </tr>
                              <tr>
                                <td class="data-title">Full Name:</td>
                                <td class="data-input"><?php echo $rec->fname.' '.$rec->lname.' '.$rec->mname.' '.$rec->suffix?></td>
                                <td class="data-title">Title:</td>
                                <td class="data-input"><?php echo $rec->title?></td>
                              </tr>
                              <tr>
                                <td class="data-title">Nickname:</td>
                                <td class="data-input"><?php echo $rec->nickname?></td>
                                <td class="data-title">Sex:</td>
                                <td class="data-input"><?php if($rec->sex == 'M'){echo "Male";}else{echo "Female";}?></td>
                              </tr>
                              <tr>
                                <td class="data-title">Date of Birth:</td>
                                <td class="data-input"><?php echo date('M d, Y',strtotime($rec->birthDate))?></td>
                                <td class="data-title">Date of Place:</td>
                                <td class="data-input"><?php echo $rec->empNo?></td>
                              </tr>
                              <tr>
                                <td class="data-title">Civil Status:</td>
                                <td class="data-input"><?php echo $rec->civilStatus?></td>
                                <td class="data-title">Nationality:</td>
                                <td class="data-input"><?php echo $rec->nationality?></td>
                              </tr>
                              <tr>
                                <td class="data-title">Language:</td>
                                <td class="data-input"><?php echo $rec->languages?></td>
                              </tr>
                              <tr>
                                <td class="data-title">Remarks:</td>
                                <td class="data-input" colspan="3"><?php echo $rec->remarks?></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
             </div>
             <div class="row">
              <div class="col-12">
                <div class="card-box">
                  <div class="card-head">
                    <div class="head-caption">
                      <div class="head-title">
                        <ul id="inner-tab" class="nav nav-tabs w-icon">
                          <li class="nav-item" data-nav="1">
                            <a class="nav-link active" href="#/">
                              <i class="icon left la la-user"></i>
                              <span class="nav-text">Employee Details</span>
                            </a>
                          </li>
                          <li class="nav-item" data-nav="2">
                            <a class="nav-link" href="#/">
                              <i class="icon left la la-file-text"></i>
                              <span class="nav-text">Credentials</span>
                            </a>
                          </li>
                          <li class="nav-item" data-nav="3">
                            <a class="nav-link" href="#/">
                              <i class="icon left la la-briefcase"></i>
                              <span class="nav-text">Employment Details</span>
                            </a>
                          </li>
                          <li class="nav-item" data-nav="6">
                            <a class="nav-link" href="#/">
                              <i class="icon left la la-money"></i>
                              <span class="nav-text">Salary & Wages</span>
                            </a>
                          </li>
                          <li class="nav-item" data-nav="4">
                            <a class="nav-link" href="#/">
                              <i class="icon left la la-car"></i>
                              <span class="nav-text">Leaves & Trip Tickets</span>
                            </a>
                          </li>
                          <li class="nav-item" data-nav="5">
                            <a class="nav-link" href="#/">
                              <i class="icon left la la-calendar"></i>
                              <span class="nav-text">Attendance & Shifts</span>
                            </a>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div class="card-head-tools"></div>
                  </div>
                  <div class="card-body content1 tab-content">
                    <div class="subtitle">
                      <h5 class="title"><i class="icon left la la-mobile-phone"></i> Contact Information</h5>
                    </div>
                    <div class="data-view">
                      <table class="view-table">
                        <tbody>
                          <tr>
                            <td class="data-title w-15">Telephone No:</td>
                            <td class="data-input w-30"><?php echo $rec->telephone?></td>
                            <td class="data-title w-15">Mobile No:</td>
                            <td class="data-input w-30"><?php echo $rec->mobile?></td>
                          </tr>
                          <tr>
                            <td class="data-title">Work Email:</td>
                            <td class="data-input"><?php echo $rec->workEmail?></td>
                            <td class="data-title">Personal Email:</td>
                            <td class="data-input"><?php echo $rec->personalEmail?></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="subtitle mt-20">
                      <h5 class="title"><i class="icon left la la-street-view"></i> Current Address</h5>
                    </div>
                    <div class="data-view">
                      <table class="view-table">
                        <tbody>
                          <tr>
                            <td class="data-title w-15">House No. / Street:</td>
                            <td class="data-input w-30"><?php echo $rec->currentStreet?></td>
                            <td class="data-title w-15">Barangay:</td>
                            <td class="data-input w-30"><?php echo $rec->currentBarangay?></td>
                          </tr>
                          <tr>
                            <td class="data-title">City/Town:</td>
                            <td class="data-input"><?php echo $rec->currentCity?></td>
                            <td class="data-title">Province:</td>
                            <td class="data-input"><?php echo $rec->currentProvince?></td>
                          </tr>
                          <tr>
                            <td class="data-title">Country:</td>
                            <td class="data-input"><?php echo $rec->currentCountry?></td>
                            <td class="data-title">Zipcode:</td>
                            <td class="data-input"><?php echo $rec->currentZipcode?></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="subtitle mt-20">
                      <h5 class="title"><i class="icon left la la-street-view"></i> Permanent Address</h5>
                    </div>
                    <div class="data-view">
                      <table class="view-table">
                        <tbody>
                          <tr>
                            <td class="data-title w-15">House No. / Street:</td>
                            <td class="data-input w-30"><?php echo $rec->provinceBarangay?></td>
                            <td class="data-title w-15">Barangay:</td>
                            <td class="data-input w-30"><?php echo $rec->provinceBarangay?></td>
                          </tr>
                          <tr>
                            <td class="data-title">City/Town:</td>
                            <td class="data-input"><?php echo $rec->provinceCity?></td>
                            <td class="data-title">Province:</td>
                            <td class="data-input"><?php echo $rec->provinceProvince?></td>
                          </tr>
                          <tr>
                            <td class="data-title">Country:</td>
                            <td class="data-input"><?php echo $rec->provinceCountry?></td>
                            <td class="data-title">Zipcode:</td>
                            <td class="data-input"><?php echo $rec->provinceZipcode?></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="subtitle mt-20">
                      <h5 class="title"><i class="icon left ti-id-badge"></i> Identification Numbers</h5>
                    </div>
                    <div class="data-view">
                      <table class="view-table">
                        <tbody>
                          <tr>
                            <td class="data-title w-15">TIN:</td>
                            <td class="data-input w-30"><?php echo $rec->tin?></td>
                            <td class="data-title w-15">SSS No:</td>
                            <td class="data-input w-30"><?php echo $rec->sssNo?></td>
                          </tr>
                          <tr>
                            <td class="data-title">Philhealth No:</td>
                            <td class="data-input"><?php echo $rec->philhealthNo?></td>
                            <td class="data-title">Pag-ibig No:</td>
                            <td class="data-input"><?php echo $rec->pagibigNo?></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="subtitle mt-20">
                      <h5 class="title"><i class="icon left la la-heartbeat"></i> Medical Information</h5>
                    </div>
                    <div class="data-view">
                      <table class="view-table">
                        <tbody>
                          <tr>
                            <td class="data-title w-15">Blood Type:</td>
                            <td class="data-input w-30"><?php echo $rec->bloodType?></td>
                            <td class="data-title w-15">Eye Color:</td>
                            <td class="data-input w-30"><?php echo $rec->eyeColor?></td>
                          </tr>
                          <tr>
                            <td class="data-title">Height:</td>
                            <td class="data-input"><?php echo $rec->height?></td>
                            <td class="data-title">Weight:</td>
                            <td class="data-input"><?php echo $rec->weight?></td>
                          </tr>
                          <tr>
                            <td class="data-title">Medical Condition:</td>
                            <td class="data-input" colspan="3"><?php echo $rec->medicalCondition?></td>
                          </tr>
                          <tr>
                            <td class="data-title">Medical History:</td>
                            <td class="data-input" colspan="3"><?php echo $rec->medicalHistory?></td>
                          </tr>
                          <tr>
                            <td class="data-title">Distinguishing Marks:</td>
                            <td class="data-input" colspan="3"><?php echo $rec->distinguishingMarks?></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <?php if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'View Family Member')) {?>
                    <div class="subtitle mt-20">
                      <h5 class="title mr-auto"><i class="icon left la la-child"></i> Family Members</h5>
                      <div class="subtitle-tools">
                        <?php if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'Add Family Member')) {?>
                        <button class="btn btn-primary btn-raised bmd-btn-fab bmd-btn-fab-sm" data-toggle="modal" data-target="#modal3"><i class="icon la la-plus md"></i></button>
                        <?php }?>
                      </div>
                    </div>
                    <div class="datatables_wrapper">
                      <div class="table-responsive-xl">
                        <table id="family-members" class="table hover">
                          <thead class="thead-light">
                            <tr>
                              <th class="">Family Name</th>
                              <th class="">Relationship</th>
                              <th class="">Birth Date</th>
                              <th class="">Occupation</th>
                              <th class="">Dependent</th>
                              <th class="">Beneficiary</th>
                              <th class="">Contact Person</th>
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <br>
                    <?php }?>
                    
                    <div class="subtitle mt-20">
                      <h5 class="title mr-auto"><i class="icon left la la-paperclip"></i> Attachments</h5>
                      <div class="subtitle-tools">
                        <button class="btn btn-primary btn-raised bmd-btn-fab bmd-btn-fab-sm" id="btn-add-attachment"><i class="icon la la-plus md"></i></button>
                      </div>
                    </div>
                    <div class="datatables_wrapper">
                      <div class="table-responsive-xl">
                        <table id="attachments" class="table hover">
                          <thead class="thead-light">
                            <tr>
                              <th class="">Attachment</th>
                              <th class="">Description</th>
                              <th class="">Date Uploaded</th>
                              <th class="">File</th>
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  
                  <?php if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'View Education Background')) {?>
                  <div class="card-body content2 tab-content" style="display: none">
                    <div class="subtitle mt-20">
                      <h5 class="title mr-auto"><i class="icon left la la-book"></i> Educational Background</h5>
                      <div class="subtitle-tools">
                        <?php if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'Add Education Background')) {?>
                        <button class="btn btn-primary btn-raised bmd-btn-fab bmd-btn-fab-sm" data-toggle="modal" data-target="#modal-education"><i class="icon la la-plus md"></i></button>
                        <?php }?>
                      </div>
                    </div>
                    <div class="datatables_wrapper">
                      <div class="table-responsive-xl">
                        <table id="education" class="table hover">
                          <thead class="thead-light">
                            <tr>
                              <th class="">Level</th>
                              <th class="">Year Grad</th>
                              <th class="">Degree</th>
                              <th class="">School</th>
                              <th class="">Awards/Honors</th>
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <br>
                    <?php }?>
                    
                    <?php if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'View Service Eligibility')) {?>
                    <div class="subtitle mt-20">
                      <h5 class="title mr-auto"><i class="icon left la la-certificate"></i> Service Eligibilities</h5>
                      <div class="subtitle-tools">
                        <?php if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'Add Service Eligibility')) {?>
                        <button class="btn btn-primary btn-raised bmd-btn-fab bmd-btn-fab-sm" data-toggle="modal" data-target="#modal-eligibility"><i class="icon la la-plus md"></i></button>
                        <?php }?>
                      </div>
                    </div>
                    <div class="datatables_wrapper">
                      <div class="table-responsive-xl">
                        <table id="eligibilities" class="table hover">
                          <thead class="thead-light">
                            <tr>
                              <th class="">Eligibility</th>
                              <th class="">Rating</th>
                              <th class="">Exam Date</th>
                              <th class="">Exam Place</th>
                              <th class="">License No.</th>
                              <th class="">Date Licensed</th>
                              <th class="">Date Expired</th>
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <br>
                    <?php }?>
                    
                    <?php if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'View Work Experience')) {?>
                    <div class="subtitle mt-20">
                      <h5 class="title mr-auto"><i class="icon left la la-cutlery"></i> Work Experiences</h5>
                      <div class="subtitle-tools">
                        <?php if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'Add Work Experience')) {?>
                        <button class="btn btn-primary btn-raised bmd-btn-fab bmd-btn-fab-sm" data-toggle="modal" data-target="#modal-experience"><i class="icon la la-plus md"></i></button>
                        <?php }?>
                      </div>
                    </div>
                    <div class="datatables_wrapper">
                      <div class="table-responsive-xl">
                        <table id="experiences" class="table hover">
                          <thead class="thead-light">
                            <tr>
                              <th class="">Company</th>
                              <th class="">Designation</th>
                              <th class="">Salary/Wage</th>
                              <th class="">Inclusive Date</th>
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <br>
                    <?php }?>
                    
                    <?php if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'View Training Program')) {?>
                    <div class="subtitle mt-20">
                      <h5 class="title mr-auto"><i class="icon left la la-clipboard"></i> Training Programs</h5>
                      <div class="subtitle-tools">
                        <?php if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'Add Training Program')) {?>
                        <button class="btn btn-primary btn-raised bmd-btn-fab bmd-btn-fab-sm" data-toggle="modal" data-target="#modal-training"><i class="icon la la-plus md"></i></button>
                        <?php }?>
                      </div>
                    </div>
                    <div class="datatables_wrapper">
                      <div class="table-responsive-xl">
                        <table id="trainings" class="table hover">
                          <thead class="thead-light">
                            <tr>
                              <th class="">Course</th>
                              <th class="">Organizer</th>
                              <th class="">Venue</th>
                              <th class="">No. of Hours</th>
                              <th class="">Started</th>
                              <th class="">Ended</th>
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <?php }?>
                  </div>
                  <div class="card-body content3 tab-content" style="display: none">
                        <!-- First Table Start -->
                        <table class="view-table">
                            <tbody>
                              <tr>
                                <td class="data-title" width="12%" nowrap>Employed Since:</td>
                                <td class="data-input" width="35%" ><?php echo date('M d, Y', strtotime($rec->dateEmployed))?></td>
                                <td class="data-title" width="12%" nowrap></td>
                                <td class="data-title"></td>
                                <td class="data-title"></td>
                              </tr>
                              <tr>
                                <td class="data-title" width="" nowrap>Last Appointment:</td>
                                <td class="data-input"><?php echo date('M d, Y', strtotime($rec->lastAppointment))?></td>
                                <td class="data-title" width="" nowrap></td>
                                <td class="data-title"></td>
                                <td class="data-title"></td>
                              </tr>
                              <tr>
                                <td class="data-title" width="" nowrap>Recommended By:</td>
                                <td class="data-input"><?php echo $rec->recommendedBy?></td>
                                <td class="data-title" width="" nowrap></td>
                                <td class="data-title"></td>
                                <td class="data-title"></td>
                              </tr>
                              </table>
                              
                          <div class="subtitle mt-20">
                          <h5 class="title mr-auto"><i class="icon left ti-id-badge"></i> Current Appointment</h5>
                         </div>
                         <table class="view-table">
                              <tr>
                                <td class="data-title" width="12%" nowrap>
                                    <?php 
                                    switch($employment->status) {
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
                                </td>
                                <td class="data-input" width="35%"><?php echo date('F d Y', strtotime($employment->dateAppointed)) ?> <?php echo ($employment->dateTerminated!="0000-00-00") ? " - ".date('F d Y', strtotime($employment->dateTerminated)) : "" ?></td>
                                <td class="data-title" width="12%" nowrap></td>
                                <td class="data-title"></td>
                                <td class="data-title"></td>
                              </tr>
                              <tr>
                                <td class="data-title" width="12%" nowrap>Employment No:</td>
                                <td class="data-input" width="35%"><?php echo $employment->employmentNo?></td>
                                <td class="data-title" width="12%" nowrap>Position No:</td>
                                <td class="data-input"><?php echo $employment->positionCode?></td>
                                <td class="data-input"></td>
                              </tr>
                              <tr>
                                <td class="data-title">Branch:</td>
                                <td class="data-input"><?php echo $employment->branchName?></td>
                                <td class="data-title">Department:</td>
                                <td class="data-input"><?php echo $employment->deptName?></td>
                                <td class="data-input"></td>
                              </tr>
                              <tr>
                                <td class="data-title">Section:</td>
                                <td class="data-input"><?php echo $employment->divisionName?></td>
                                <td class="data-title" nowrap>Employment Type:</td>
                                <td class="data-input"><?php echo $employment->employeeType?></td>
                                <td class="data-input"></td>
                              </tr>
                              <tr>
                                <td class="data-title">Job Position:</td>
                                <td class="data-input"><?php echo $employment->jobTitle?></td>
                                <td class="data-title" nowrap>Basic Salary:</td>
                                <td class="data-input">
                                <?php echo number_format($employment->basicSalary, 2) ?> 
                                    ( 
                                     <?php 
                                     switch ($employment->salaryType) {
                                       case "1" : echo "Monthly"; break;
                                       case "2" : echo "Daily"; break;
                                       case "3" : echo "Hourly"; break;
                                     }?>
                                     )
                               </td>
                                <td class="data-input"></td>
                              </tr>
                          </tbody>
                        </table>
                        <!-- First Table End -->
                        
                        <div class="subtitle mt-20">
                          <h5 class="title mr-auto"><i class="icon left ti-id-badge"></i> Appointment History</h5>
                        </div>
                        <div class="datatables_wrapper">
                          <div class="table-responsive-xl">
                            <table id="appointments" class="table hover">
                              <thead class="thead-light">
                                <tr>
                                  <th class="">Appointment</th>
                                  <th class="">Branch</th>
                                  <th class="">Department</th>
                                  <th class="">Section</th>
                                  <th class="">Employment</th>
                                  <th class="">Position</th>
                                  <th class="">Salary</th>
                                  <th class="">Status</th>
                                </tr>
                              </thead>
                              <tbody>
                              </tbody>
                            </table>
                          </div>
                        </div>
                        
                      <!-- Third Table End -->
            
            
                  </div>
                  <div class="card-body content6 tab-content" style="display: none">
                    <div class="data-view">
                        <!-- First Table Start -->
                        <table class="view-table">
                            <tbody>
                              <tr>
                                <td class="data-title" width="12%" nowrap>Employed Since:</td>
                                <td class="data-input" width="35%" ><?php echo date('M d, Y', strtotime($rec->dateEmployed))?></td>
                                <td class="data-title" width="12%" nowrap></td>
                                <td class="data-title"></td>
                                <td class="data-title"></td>
                              </tr>
                              <tr>
                                <td class="data-title" width="" nowrap>Last Appointment:</td>
                                <td class="data-input"><?php echo date('M d, Y', strtotime($rec->lastAppointment))?></td>
                                <td class="data-title" width="" nowrap></td>
                                <td class="data-title"></td>
                                <td class="data-title"></td>
                              </tr>
                              <tr>
                                <td class="data-title" width="" nowrap>Recommended By:</td>
                                <td class="data-input"><?php echo $rec->recommendedBy?></td>
                                <td class="data-title" width="" nowrap></td>
                                <td class="data-title"></td>
                                <td class="data-title"></td>
                              </tr>
                              <tr>
                                <td class="data-title" colspan="5" nowrap>
                                    <div class="subtitle">
                                      <h5 class="title"><i class="icon left ti-id-badge"></i> Current Appointment</h5>
                                    </div>
                                </td>
                              </tr>
                              <tr>
                                <td class="data-title" width="" nowrap>Employment No:</td>
                                <td class="data-input"><?php echo $employment->employmentNo?></td>
                                <td class="data-title" width="" nowrap>Position No:</td>
                                <td class="data-input"><?php echo $employment->positionCode?></td>
                                <td class="data-input"></td>
                              </tr>
                              <tr>
                                <td class="data-title">Branch:</td>
                                <td class="data-input"><?php echo $employment->branchName?></td>
                                <td class="data-title">Department:</td>
                                <td class="data-input"><?php echo $employment->deptName?></td>
                                <td class="data-input"></td>
                              </tr>
                              <tr>
                                <td class="data-title">Section:</td>
                                <td class="data-input"><?php echo $employment->divisionName?></td>
                                <td class="data-title" nowrap>Employment Type:</td>
                                <td class="data-input"><?php echo $employment->employeeType?></td>
                                <td class="data-input"></td>
                              </tr>
                              <tr>
                                <td class="data-title">Job Position:</td>
                                <td class="data-input"><?php echo $employment->jobTitle?></td>
                                <td class="data-title" nowrap>Basic Salary:</td>
                                <td class="data-input">
                                <?php echo number_format($employment->basicSalary, 2) ?> 
                                    ( 
                                     <?php 
                                     switch ($employment->salaryType) {
                                       case "1" : echo "Monthly"; break;
                                       case "2" : echo "Daily"; break;
                                       case "3" : echo "Hourly"; break;
                                     }?>
                                     )
                               </td>
                                <td class="data-input"></td>
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

    <!-- Change Profile Pic modal -->
    <div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
          <form method="post" name="frmEntry" id="frmEntry" action="<?php echo $controller_page ?>/upload" enctype="multipart/form-data">
            <div class="modal-body">
              <div class="card-user mb-30">
                <div class="card-user-pic">
                	<input type="hidden" id="imgID" name="imgID" value="<?php echo $rec->empID?>"/>
                  <?php 
		                $image = "assets/records/employee/".$rec->empID."/avatar".$rec->imageExtension; 
		    			if ( ! is_file($image)) {
		    				// no image
							if ($rec->sex=="M") {
								 $image = "assets/records/employee/male_no_image.png";
							} else {
								 $image = "assets/records/employee/female_no_image.png";
							}
		    			}
		                ?>
                        <img src="<?php echo base_url($image)?>" class="text-center rounded" alt="" width="40">
                </div>
              </div>
              <div class="form-group">
                <input type="file" class="form-control filestyle" id="userfile" name="userfile">
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" id="cmdsave" class="btn btn-primary btn-raised pill">Save</button>
              <button type="button" id="close" class="btn btn-outline-danger btn-raised pill" data-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>
   
<script>
$(document).ready(function() {
    showFamilyMembers();
    showAttachments();

    $('#inner-tab .nav-item').click(function(){
    	$('#inner-tab .nav-item').find('.nav-link').removeClass('active');
    	$(this).find('.nav-link').addClass('active');

    	contentNo = $(this).attr('data-nav');

    	if (contentNo == 1) {
    	    showFamilyMembers();
    	    showAttachments();
    	    
			$('.tab-content').hide();
			$('.content1').show();
    	} else if (contentNo == 2) {
    	    showEducation();
    	    showEligibilities();
    	    showExperiences();
    	    showTrainings();
    	    
    	    $('.tab-content').hide();
			$('.content2').show();
    	} else if (contentNo == 3) {
    	    showEmployments();
    	    
    	    $('.tab-content').hide();
			$('.content3').show();
    	} else if (contentNo == 6) {
    	    
    	    $('.tab-content').hide();
			$('.content6').show();
    	} else {
    	    $('.content1').hide();
    	}
    });
    
    $(this).on({
        click: function () {
            $('#modal-attachment').modal('show');
        },
    }, '#btn-add-attachment');
    
    $(this).on({
        click: function () {
            $('#modal4').modal('show');
            viewFamily($(this).attr('data-id'));
        },
    }, '.row-family');
    
    $(this).on({
        click: function () {
            $('#modal-education-edit').modal('show');
            viewEducation($(this).attr('data-id'));
        },
    }, '.row-education');
    
    $(this).on({
        click: function () {
            $('#modal-eligibility-edit').modal('show');
            viewEligibility($(this).attr('data-id'));
        },
    }, '.row-eligibility');
    
    $(this).on({
        click: function () {
            $('#modal-experience-edit').modal('show');
            viewExperience($(this).attr('data-id'));
        },
    }, '.row-experience');
    
    $(this).on({
        click: function () {
            $('#modal-training-edit').modal('show');
            viewTraining($(this).attr('data-id'));
        },
    }, '.row-training');
});

function showEmployments() {
    $.ajax({
        url: '<?php echo site_url('employee/show_employments')?>',
        data: { empID: '<?php echo $this->encrypter->encode($rec->empID)?>' },
        type: 'POST',
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                
                list = '';
                for (i = 0; i < response.records.length; i++) {
                    record = response.records[i];
                    
					list += '<tr class="" data-id="'+record.employmentID+'">';
					list += '	<td><span>'+record.dateAppointed+'</span></td>';
					list += '	<td><span>'+record.branchName+'</span></td>';
					list += '	<td><span>'+record.deptName+'</span></td>';
					list += '	<td><span>'+record.divisionName+'</span></td>';
					list += '	<td><span>'+record.employeeType+'</span></td>';
					list += '	<td><span>'+record.jobTitle+'</span></td>';
					list += '	<td><span>'+format_number(record.basicSalary)+'</span></td>';
					if (record.status == '1') {
					    list += '	<td><span>Active</span></td>';
					} else if (record.status == '2') {
					    list += '	<td><span>Re-assigned</span></td>';
					} else if (record.status == '3') {
					    list += '	<td><span>Promoted</span></td>';
					} else if (record.status == '4') {
					    list += '	<td><span>Demoted</span></td>';
					} else if (record.status == '5') {
					    list += '	<td><span>Terminated</span></td>';
					} else if (record.status == '6') {
					    list += '	<td><span>Resigned</span></td>';
					} else if (record.status == '7') {
					    list += '	<td><span>EOC</span></td>';
					}
					list += '</tr>';
                }

                $('#appointments tbody').html(list);
            } else if (response.status == '0') {
                if (response.message) {
                    alert(response.message);
                }
            } else {
                alert(response.message, 1);
            }
        }, error:function(xhr) {
        }
    });
}

function clearFields(container) {
    $(container+' :input').each(function() {
		this.value = "";
    });
}

function check_fields(form)
{
	 var valid = true;
	 var req_fields = "";
	 
	 $(form+' [required]').each(function(){
	    if($(this).val()=='' ) {
	    	req_fields += "<br/>" + $(this).attr('title');
		    valid = false;
	    } 
	 })
	 
	 if (!valid) {
	 	swal("Required Fields", req_fields,"warning");
	 }
	 
	 return valid;
}

function format_number(num) {
    var rgx  = /(\d+)(\d{3})/;
    
    num += '';
    x    = num.split('.');
    x1   = x[0];
//     x2   = (x.length > 1) ? '.' + x[1] : '.00'; 
    x2   = (x.length > 1) ? '.00' : '.00'; 
    
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    } 
    
    return (x1 + x2);
}
</script>