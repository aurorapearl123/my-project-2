<div class="subheader">
	<div class="d-flex align-items-center">
		<div class="title mr-auto">
			<h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></h3>
		</div>
		<div class="subheader-tools">
			<a href="<?php echo site_url('section/show') ?>" class="btn btn-primary btn-raised btn-xs pill"><i class="icon ti-angle-left"></i> Back to List</a>
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
							<?php if ($roles['edit']) {?>
							<li>
								<a href="<?php echo site_url('section/edit/'.$this->encrypter->encode($rec->divisionID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>
							</li>
							<?php } ?>
							<?php if ($roles['delete'] && !$in_used) {?>
							<li>
								<button name="cmddelete" id="cmddelete" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" onclick="deleteRecord('<?php echo $this->encrypter->encode($rec->divisionID); ?>');"><i class="la la-trash-o"></i></button>
							</li>
							<?php } ?>
							<?php if ($this->session->userdata('current_user')->isAdmin) {?>
							<li>
								<button type="button" id="recordlog" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Record Logs" onclick="popUp('<?php echo site_url('logs/record_log/divisions/divisionID/'.$this->encrypter->encode($rec->divisionID).'/Section') ?>', 1000, 500)"><i class="la la-server"></i></button>
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
									<td class="data-title" style="width:120px" nowrap>Section Code:</td>
									<td class="data-input" style="width:320px" nowrap><?php echo $rec->divisionCode; ?></td>
									<td class="data-title" style="width:120px" nowrap>Company:</td>
									<td class="data-input" style="width:320px" nowrap><?php echo $rec->companyCode.' - '.$rec->companyName; ?></td>
									<td class="data-input"></td>
								</tr>
								<tr>
									<td class="data-title" nowrap>Section Name:</td>
									<td class="data-input" nowrap><?php echo $rec->divisionName; ?></td>
									<td class="data-title" nowrap>Branch:</td>
									<td class="data-input" nowrap><?php echo $rec->branchCode.' - '.$rec->branchName; ?></td>
									<td class="data-input"></td>
								</tr>
								<tr>
									<td class="data-title" nowrap>Section Abbr:</td>
									<td class="data-input" nowrap><?php echo $rec->divisionAbbr; ?></td>
									<td class="data-title" nowrap>Department:</td>
									<td class="data-input" nowrap><?php echo $rec->deptCode.' - '.$rec->deptName; ?></td>
									<td class="data-input"></td>
								</tr>
								<tr>
									<td class="data-title" nowrap>Section Contact:</td>
									<td class="data-input" nowrap><?php echo $rec->divisionContact; ?></td>
									<td class="data-title" nowrap>Section Email:</td>
									<td class="data-input" nowrap><?php echo $rec->divisionEmail; ?></td>
									<td class="data-input"></td>
								</tr>
								<tr>
									<td class="data-title" nowrap>Section Head:</td>
									<td class="data-input" nowrap><?php echo $rec->fname.' '.$rec->mname.' '.$rec->lname; ?></td>
									<td class="data-title" nowrap>Head Title:</td>
									<td class="data-input" nowrap><?php echo $rec->divisionHeadTitle; ?></td>
								</tr>
								<tr>
									<td class="data-title" nowrap>Status:</td>
									<td class="data-input">
										<?php 
											if ($rec->status == 1) {
												echo "<span class='badge badge-pill badge-success'>Active</span>";
											} else {
												echo "<span class='badge badge-pill badge-danger'>Inactive</span>";
											}
											?>
									</td>
									<td class="data-title" nowrap>&nbsp;</td>
									<td class="data-title" nowrap><?php //echo $rec->remarks; ?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
