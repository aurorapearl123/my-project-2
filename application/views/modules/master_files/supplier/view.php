<div class="subheader">
	<div class="d-flex align-items-center">
		<div class="title mr-auto">
			<h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></h3>
		</div>
		<div class="subheader-tools">
			<a href="<?php echo site_url('supplier/show') ?>" class="btn btn-primary btn-raised btn-sm pill"><i class="icon left ti-angle-left md"></i> Back to List</a>
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
								<a href="<?php echo site_url('supplier/edit/'.$this->encrypter->encode($rec->supplierID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>
							</li>
							<?php } ?>
							<?php if ($roles['delete'] && !$in_used) {?>
							<li>
								<button name="cmddelete" id="cmddelete" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" onclick="deleteRecord('<?php echo $this->encrypter->encode($rec->supplierID); ?>');"><i class="la la-trash-o"></i></button>
							</li>
							<?php } ?>
							<?php if ($this->session->userdata('current_user')->isAdmin) {?>
							<li>
								<button type="button" id="recordlog" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Record Logs" onclick="popUp('<?php echo site_url('logs/record_log/supplier/supplierID/'.$this->encrypter->encode($rec->supplierID).'/Supplier') ?>', 1000, 500)"><i class="la la-server"></i></button>
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
									<td class="data-title w-10" nowrap>Supplier Name :</td>
									<td class="data-input w-20"><?php echo $rec->name; ?></td>
									<td class="data-title w-10" nowrap>Location :</td>
									<td class="data-input w-20"><?php echo $rec->location; ?></td>
									<td class="d-xxl-none"></td>
								</tr>
								<tr>
									<td class="data-title" nowrap>Contact Number :</td>
									<td class="data-input"><?php echo $rec->contactNo; ?></td>
									<td class="data-title" nowrap>Status :</td>
									<td>
										<?php 
											if ($rec->status == 1) {
												echo "<span class='badge badge-pill badge-success'>Active</span>";
											} else {
												echo "<span class='badge badge-pill badge-danger'>Inactive</span>";
											}
											?>
									</td>
									<td class="d-xxl-none"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>