<div class="subheader">
	<div class="d-flex align-items-center">
		<div class="title mr-auto">
			<h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></h3>
		</div>
		<div class="subheader-tools">
			<a href="<?php echo site_url('complaint/show') ?>" class="btn btn-primary btn-raised btn-xs pill"><i class="icon ti-angle-left"></i> Back to List</a>
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
							
                            <li>
                                <a href="<?php echo site_url('complaint/confirm/'.$this->encrypter->encode($rec->comID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Confirm"><i class="la la-thumbs-up"></i></a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('complaint/cancel/'.$this->encrypter->encode($rec->comID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Cancel"><i class="la la-ban"></i></a>
                            </li>
							<?php if ($roles['edit']) {?>
							<li>
								<a href="<?php echo site_url('complaint/edit/'.$this->encrypter->encode($rec->comID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>
							</li>
							<?php } ?>
							<?php if ($roles['delete'] && !$in_used) {?>
							<li>
								<button name="cmddelete" id="cmddelete" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" onclick="deleteRecord('<?php echo $this->encrypter->encode($rec->comID); ?>');"><i class="la la-trash-o"></i></button>
							</li>
							<?php } ?>
							<?php if ($this->session->userdata('current_user')->isAdmin) {?>
							<li>
								<button type="button" id="recordlog" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Record Logs" onclick="popUp('<?php echo site_url('logs/record_log/complaint/comID/'.$this->encrypter->encode($rec->comID).'/Complaint') ?>', 1000, 500)"><i class="la la-server"></i></button>
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
									<td class="data-title" style="width:120px" nowrap>Branch:</td>
									<td class="data-input" style="width:300px" nowrap><?php echo $rec->branchName; ?></td>
									<td class="data-title" style="width:160px" nowrap>Date of Complaint:</td>
									<td class="data-input" style="width:300px" nowrap><?php echo date('M d, Y',strtotime($rec->date))?></td>
								</tr>
								<tr>
									<td class="data-title" style="width:120px" nowrap>Customer</td>
									<td class="data-input" style="width:300px" nowrap><?php echo $rec->fname. ' '.$rec->mname.' '.$rec->lname ?></td>
								</tr>
								<tr>
									
									<td class="data-title" style="width:120px" nowrap>Order:</td>
									<td class="data-input" style="width:300px" nowrap><?php echo $rec->serviceType ?></td>
								</tr>
								<tr>
									<td class="data-title" style="width:120px" nowrap>Complaint</td>
									<td class="data-input" style="width:300px" nowrap><?php echo $rec->complaint ?></td>
									<td class="data-title"></td>
									<td class="data-title"></td>									
									<td></td>
								</tr>
								<tr>
									<td class="data-title" style="width:150px" nowrap>Created By:</td>
									<td class="data-input" style="width:300px" nowrap><?php echo $rec->createdByFirst. ' '.$rec->createdByMiddle.' '.$rec->createdByLast ?></td>
								</tr>

								<tr>
									<td class="data-title" style="width:150px" nowrap>Confirmed By:</td>
									<td class="data-input" style="width:300px" nowrap><?php echo $rec->confirmedByFirst. ' '.$rec->confirmedByMiddle.' '.$rec->confirmedByLast ?></td>
								</tr>

								<tr>
									<td class="data-title" style="width:150px" nowrap>Cancelled By:</td>
									<td class="data-input" style="width:300px" nowrap><?php echo $rec->cancelledByFirst. ' '.$rec->cancelledByMiddle.' '.$rec->cancelledByLast ?></td>
								</tr>								

								<tr>
									<td class="data-title" nowrap>Status:</td>
									<td class="data-input">
										<?php 
											if ($rec->status == 1) {
												echo "<span class='badge badge-pill badge-warning'>Pending</span>";
											} else if ($rec->status == 2) {
												echo "<span class='badge badge-pill badge-success'>Approve</span>";
											} else if ($rec->status == 0) {
												echo "<span class='badge badge-pill badge-danger'>Disapprove</span>";
											} else{
												echo "<span class='badge badge-pill badge-secondary'>Cancelled</span>";
											}
										?>
									</td>
									
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>