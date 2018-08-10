<div class="subheader">
	<div class="d-flex align-items-center">
		<div class="title mr-auto">
			<h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></h3>
		</div>
		<div class="subheader-tools">
			<a href="<?php echo site_url('employment_type/show') ?>" class="btn btn-primary btn-raised btn-xs pill"><i class="icon ti-angle-left"></i> Back to List</a>
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
							<?php if ($roles['edit']) { ?>
							<li>
								<a href="<?php echo site_url('employment_type/edit/'.$this->encrypter->encode($rec->employeeTypeID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>
							</li>
							<?php } ?>
							<?php if ($roles['delete'] && !$in_used) { ?>
							<li>
								<button name="cmddelete" id="cmddelete" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" onclick="deleteRecord('<?php echo $this->encrypter->encode($rec->employeeTypeID); ?>');"><i class="la la-trash-o"></i></button>
							</li>
							<?php } ?>
							<?php if ($this->session->userdata('current_user')->isAdmin) { ?>
							<li>
								<button type="button" id="recordlog" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Record Logs" onclick="popUp('<?php echo site_url('logs/record_log/employee_types/employmentTypeID/'.$this->encrypter->encode($rec->employeeTypeID).'/Employment&Type') ?>', 1000, 500)"><i class="la la-server"></i></button>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<div class="card-body">
					<div class="data-view">
						<table class="view-table">
							<tbody>
								<tr>
									<td class="data-title" nowrap>Type:</td>
									<td class="data-input" nowrap><?php echo $rec->employeeType; ?></td>
									<td colspan="3"></td>
								</tr>
								<tr>
									<td class="data-title" style="width:120px" nowrap>Salary Type:</td>
									<td class="data-input" style="width:320px" nowrap>
									<?php 
                            		if ($rec->salaryType=="1") {
                            			echo "Monthly";
                            		} elseif ($rec->salaryType=="2") {
                            			echo "Daily";
                            		} elseif ($rec->salaryType=="3") {
                            			echo "Hourly";
                            		}?> 
									</td>
									<td class="data-title" style="width:120px" nowrap>Default Salary:</td>
									<td class="data-input" style="width:320px" nowrap><?php echo ($rec->defaultSalary > 0) ? number_format($rec->defaultSalary, 2) : "--"; ?></td>
									<td></td>
								</tr>
								<tr>
									<td class="data-title" nowrap>Working Days:</td>
									<td class="data-input" nowrap><?php echo ($rec->workingDays > 0) ? $rec->workingDays : "--" ?></td>
									<td class="data-title" nowrap>Days With Pay:</td>
									<td class="data-input" nowrap>
									<?php $withPay = explode(',', $rec->withPay)?>
                            		<select class="form-control" name="withPay" id="withPay[]" multiple size="9" disabled>
                            			<option value="WD" <?php echo (in_array('WD', $withPay)) ? 'selected' : ''?>>Whole Day Work</option>
                                        <option value="HDA" <?php echo (in_array('HDA', $withPay)) ? 'selected' : ''?>>Half Day Work Morning</option>
                                        <option value="HDP" <?php echo (in_array('HDP', $withPay)) ? 'selected' : ''?>>Half Day Work Afternoon</option>
                                        <option value="RH" <?php echo (in_array('RH', $withPay)) ? 'selected' : ''?>>Regular Holiday</option>
                                        <option value="SH" <?php echo (in_array('SH', $withPay)) ? 'selected' : ''?>>Special Holiday</option>
                                        <option value="NW" <?php echo (in_array('NW', $withPay)) ? 'selected' : ''?>>No Work</option>
                            		</select>
									</td>
									<td></td>
								</tr>
								<tr>
								<td class="data-title" valign="top" nowrap>Basic Contributions:</td>
								<td class="data-input" nowrap>
								<ul class="list-group">
								<?php 
                        		$this->db->where('status',1);
                        		$this->db->order_by('rank','asc');
                        		$this->db->order_by('classification','asc');
                        		$types = $this->db->get('pay_classifications');
                        		
                        		$basicContributions = explode(",",$rec->basicContributions);
                        		if ($types->num_rows()) {
                        			foreach ($types->result() as $type) {
                        				$this->db->where('classID', $type->classID);
                        				$this->db->where('isBasic', 1);
                        				$this->db->where('status', 1);
                        				$this->db->order_by('rank','asc');
                        				$this->db->order_by('name','asc');
                        				$data = $this->db->get('premiums');
                        			     
                        				if ($data->num_rows()) {
                        					foreach ($data->result() as $row) {
                        						if (in_array($row->premiumID, $basicContributions)) {
                        						    echo "<li class='list-group-order'>$row->name</li>";
                        						} else {
                        							echo "<li class='list-group-order'>$row->name</li>";
                        						}
                        					}
                        				}
                        			}
                        		}				
                        		?>
                                </ul>
                                </td>
                                </tr>
								<tr>
									<td class="data-title" nowrap>Remarks:</td>
									<td class="data-input" nowrap><?php echo $rec->remarks; ?></td>
									<td class="data-title" nowrap></td>
									<td></td>
									<td></td>
								</tr>
								

							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
