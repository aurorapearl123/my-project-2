<div class="subheader">
	<div class="d-flex align-items-center">
		<div class="title mr-auto">
			<h3><i class="icon left la la-user"></i>Audit Logs</h3>
		</div>
		<div class="subheader-tools"></div>
	</div>
</div>
<div class="content">
	<div class="row">
		<div class="col-12">
			<div class="card-box full-body">
				<div class="card-head">
					<div class="head-caption">
						<div class="head-title">
							<h4 class="head-text">User Activity Logs</h4>
						</div>
					</div>
					<div class="card-head-tools">
						<ul class="tools-list">
							<li>
								<?php $path = 'logs/printlist2/'.$user.'/'.$startDate.'/'.$endDate ?>
								<button type="button" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Print" onclick="popUp('<?php echo site_url($path) ?>', 800, 500)"><i class="la la-print"></i></button>
							</li>
							<li>
								<button type="button" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Export to Excel File" onclick="window.location='<?php echo site_url('logs/exportlist2/').$user.'/'.$startDate.'/'.$endDate ?>';"><i class="la la-file-excel-o"></i></button>
							</li>
						</ul>
					</div>
				</div>
				<div class="card-body">
					<form method="post" name="frmLogs" id="frmLogs" action="<?php echo site_url('logs/user_log') ?>" class="mb-0">
						<div class="datatable-header">
							<table class="table-form">
								<tbody>
									<tr>
										<td class="form-label" width="10%">
											<label for="lname">User : </label>
										</td>
										<td class="form-group form-input" width="22%">
											<select id="userID" name="userID" class="form-control">
												<option value="" <?php if ($user == 0) { echo "selected"; } ?>>&nbsp;</option>
												<?php foreach ($users as $row) { ?>
												<option value="<?php echo $row->userID ?>" <?php if ($row->userID == $user) { echo "selected"; }?>><?php echo $row->lastName.', '.$row->firstName ?></option>
												<?php } ?>
											</select>
										</td>
										<td class="form-label" width="5%" nowrap>
											<label for="lname">From : </label>
										</td>
										<td class="form-group form-input" width="13%" >
											<input type="text" class="form-control datepicker" name="startDate" id="startDate" data-toggle="datetimepicker" data-target="#startDate" value="<?php echo date('M d, Y',strtotime($startDate))?>">
										</td>
										<td class="form-label" width="5%" nowrap>
											<label for="lname">To : </label>
										</td>
										<td class="form-group form-input" width="13%">
											<input type="text" class="form-control datepicker" name="endDate" id="endDate" data-toggle="datetimepicker" data-target="#endDate" value="<?php echo date('M d, Y',strtotime($endDate))?>">
										</td>
										<td class="form-group form-input" width="5%">
											<input type="submit" class="btn btn-xs btn-primary pill" name="cmdSubmit" id="cmdSubmit" value="Show Logs" />
										</td>
										<td class="d-xxl-none"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</form>
					<div class="form-group mb-0">
						<table class="table table-striped hover">
							<thead class="thead-light">
								<tr>
									<th class="w-20">Date/Time</th>
									<th class="w-20">Workstation</th>
									<th class="w-25">Module</th>
									<th class="w-25">Operation</th>
									<th class="w-25">Logs</th>
								</tr>
							</thead>
							<tbody>
								<?php
									if (!empty($dataRec)) {
										foreach($dataRec as $row) {
									?>
								<tr>
									<td><span><?php echo $row->date ?></span></td>
									<td><span><?php echo $row->host ?></span></td>
									<td><span><?php echo $row->module ?></span></td>
									<td><span><?php echo $row->operation ?></span></td>
									<td><span><?php echo $row->logs ?></span></td>
								</tr>
								<?php
										}
									} else {
								
								?>
								<tr>
									<td class="p-20" nowrap align="center" colspan="10">No results found.</td>
								</tr>
								<?php
									}
									?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
