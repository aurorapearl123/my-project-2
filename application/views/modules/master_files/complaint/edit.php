<div class="subheader">
	<div class="d-flex align-items-center">
		<div class="title mr-auto">
			<h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></h3>
		</div>
		<div class="subheader-tools"></div>
	</div>
</div>
<div class="content">
	<div class="row">
		<div class="col-12">
			<div class="card-box">
				<div class="card-head">
					<div class="head-caption">
						<div class="head-title">
							<h4 class="head-text">Edit <?php echo $current_module['module_label'] ?></h4>
						</div>
					</div>
					<div class="card-head-tools"></div>
				</div>
				<div class="card-body">
					<form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url('complaint/update') ?>">
						<input type="hidden" name="comID" id="comID" value="<?php echo $this->encrypter->encode($rec->comID) ?>" />
						<div class="table-row">
							<table class="table-form">
								<tbody>
									<tr>
										<td class="form-label" width="13%" nowrap>Branch : <span class="asterisk">*</span></td>
										<td class="form-group form-input" width="22%">
											<?php 
												$this->db->select('branches.*');
												$this->db->from('branches');
												$this->db->where('status',1);
												$recs = $this->db->get()->result();												
											?>
											<select class="form-control" id="branchID" name="branchID" data-live-search="true" livesearchnormalize="true" title="-Select Branch-" >
												<?php foreach($recs as $res) {?>
												<option value="<?php echo $res->branchID ?>" <?php if ($res->branchID==$rec->branchID ) echo "selected"; ?>  ><?php echo $res->branchName ?></option>
												<?php } ?>
											</select>

										</td>
										<td class="form-label" width="13%" nowrap>Date of Complaint : <span class="asterisk">*</span></td>
										<td class="form-group data-input" width="22%">
											<input type="text" class="form-control datepicker" id="date" name="date" data-toggle="datetimepicker" value="<?php echo date('M d, Y',strtotime($rec->date))?>" data-target="#date" title="Date of Complain" required>
										</td>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
										<td class="form-label" nowrap>Customer : <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<?php 
												$this->db->where('status',1);
												$recs = $this->db->get('customers')->result();
												?>
											<select class="form-control" id="custID" name="custID" data-live-search="true" livesearchnormalize="true" title="-Select Customer-" onchange="get_orders()">
												<?php foreach($recs as $res) {?>
												<option value="<?php echo $res->custID ?>" <?php if ($res->custID==$rec->custID ) echo "selected"; ?> ><?php echo $res->fname. ' '.$res->mname.' '.$res->lname ?></option>
												<?php } ?>
											</select>
										</td>
										<td class="form-label" nowrap>Order : <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<?php 
												$this->db->select('order_headers.*');
												$this->db->select('service_types.serviceType');
												$this->db->from('order_headers');
												$this->db->join('service_types','order_headers.serviceID=service_types.serviceID','left');
												$this->db->where('order_headers.status',1);
												$recs = $this->db->get()->result();
												?>
											<select class="form-control" id="orderID" name="orderID" data-live-search="true" livesearchnormalize="true" title="Orders" >
												<option value="" selected>&nbsp;</option>
												<?php foreach($recs as $res) { ?>
												<option value="<?php echo $res->orderID ?>" <?php if($res->orderID == $rec->orderID){ echo "selected";} ?>><?php echo $res->serviceType ?></option>
												<?php } ?>
											</select>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
										<td class="form-label" nowrap>Contact No : <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="contactNo" id="contactNo" value="<?php echo $rec->contactNo ?>" title="Contact No" required>
										</td>
										<!-- <td class="form-label" nowrap>Status : <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<select id="status" name="status" class="form-control" title="Status" required>
												<option value="">&nbsp;</option>
												<option value="1" <?php if($rec->status==2){ echo "selected"; } ?>>Confirmed</option>
												<option value="0" <?php if($rec->status==1){ echo "selected"; } ?>>Pending</option>
												<option value="0" <?php if($rec->status==0){ echo "selected"; } ?>>Disapproved</option>
												<option value="0" <?php if($rec->status==-1){ echo "selected"; } ?>>Cancelled</option>

											</select>
										</td> -->
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
										<td class="form-label" nowrap>Complaint : <span class="asterisk">*</span></td>
										<td class="form-group form-input">											
											<textarea rows="2" type="text" class="form-control" name="complaint" id="complaint"  title="Complaint" required><?php echo $rec->complaint?></textarea>
										</td>
										<td class="d-xxl-none" colspan="3"></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="form-sepator solid"></div>
						<div class="form-group mb-0">
							<button class="btn btn-primary btn-raised pill" type="button" name="cmdSave" id="cmdSave">
							Save
							</button>
							<input type="button" id="cmdCancel" class="btn btn-outline-danger btn-raised pill" value="Cancel"/>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$('#cmdSave').click(function(){
		if (check_fields()) {
			$('#cmdSave').attr('disabled','disabled');
	    	$('#cmdSave').addClass('loader');
	       	$('#frmEntry').submit();
		}
	});
	
	function check_fields()
	{
		 var valid = true;
		 var req_fields = "";
		 
		 $('#frmEntry [required]').each(function(){
		    if($(this).val()=='' ) {
		    	req_fields += "<br/>" + $(this).attr('title');
			    valid = false;
		    } 
		 })
		 
		 if (!valid) {
		 	swal("Required Fields",req_fields,"warning");
		 }
		 
		 return valid;
	}
	
	$('#cmdCancel').click(function(){
		swal({
		      title: "Are you sure?",
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
		    	  window.location = '<?php echo site_url('complaint/show') ?>';
		      }
		    });
	    
	});
</script>