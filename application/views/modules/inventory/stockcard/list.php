<form name="frmFilter" id="frmFilter" method="POST" action="<?php echo $controller_page ?>/show">
	<input type="hidden" id="sortby" name="sortby" value="<?php echo $sortby ?>" />
	<input type="hidden" id="sortorder" name="sortorder" value="<?php echo $sortorder ?>" />
	<div class="subheader">
		<div class="d-flex align-items-center">
			<div class="title mr-auto">
				<h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></span></h3>
			</div>
			<!--<?php if ($roles['create']) {?>
			<div class="subheader-tools">
				<a href="<?php echo $controller_page?>/create" class="btn btn-primary btn-raised btn-sm pill"><i class="icon left la la-plus"></i>Add New</a>
			</div>
			<?php } ?>-->
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
									<button id="btn-apply"  class="btn btn-primary btn-xs pill collapse multi-collapse show">Apply Filter</button>
								</li>
								<!-- <li>
									<button type="button" id="btn-filter" class="btn btn-outline-light bmd-btn-icon active" data-toggle="tooltip" data-placement="bottom" title="Filters" onclick="#"><i class="la la-sort-amount-asc"></i></button>
								</li> -->
								<li>
									<button type="button" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Print" onclick="popUp('<?php echo site_url('stockcard/printlist') ?>', 800, 500)"><i class="la la-print"></i></button>
								</li>
								<li>
									<button type="button" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Export to Excel File" onclick="window.location='<?php echo site_url('stockcard/exportlist') ?>';"><i class="la la-file-excel-o"></i></button>
								</li>
							</ul>
						</div>
					</div>
					<!--  sorting_asc -->
					<div class="card-body">

						<div class="datatable-header">
	                      	<form>
		                      	<table class="table-form">
		                          <tbody>
		                            <tr>
		                              <td class="form-label" width="10%" nowrap>
		                                <label>Branch : </label>
		                              </td>
		                              <td class="form-group form-input" width="22%">
										<?php 
											$this->db->select('branches.*');
											$this->db->from('branches');
											$this->db->where('status',$this->session->userdata('current_user')->branchID);
											$recs = $this->db->get()->result();
											?>
										<select class="form-control" id="branchID" name="branchID" data-live-search="true" livesearchnormalize="true" style="" title="Branch Head">
											<option value="" selected>&nbsp;</option>
											<?php foreach($recs as $rec) {?>
											<option value="<?php echo $rec->branchID ?>" <?php if($this->session->userdata('current_user')->branchID == $rec->branchID) echo "selected" ?> ><?php echo $rec->branchName ?></option>
											<?php } ?>
											<!-- <option value="<?php echo $res->branchID ?>" <?php if ($res->branchID==$rec->branchID ) echo "selected"; ?>  ><?php echo $res->branchName ?></option> -->
										</select>
		                              </td>
		                              <td class="form-label" width="10%" nowrap>
		                                <label>Date Period : </label>
		                              </td>
		                              <td class="form-group form-input" width="13%">
		                                <input type="text" class="form-control datepicker" id="startDate" name="startDate" data-toggle="datetimepicker" value="" data-target="#startDate" title="Start Date" required>
		                              </td>
		                              <td class="form-group form-input" width="13%">	
		                              	<input type="text" valign="top" class="form-control datepicker" id="endDate" name="endDate" data-toggle="datetimepicker" value="" data-target="#endDate" title="End Date" required>
		                              </td>
		                              <td class="d-xxl-none"></td>
		                            </tr>
		                            <tr>
		                            	<td>Item</td>
			            				<td>
				                            <?php 
				                                $this->db->select('items.*');
				                                $this->db->from('items');
				                                $this->db->where('status',1);
				                                $recs = $this->db->get()->result();                                             
				                            ?>
				                            <select class="form-control" id="itemID" name="itemID" data-live-search="true" livesearchnormalize="true" title="-Select Items-" >
				                                <?php foreach($recs as $res) {?>
				                                <option value="<?php echo $res->itemID ?>" <?php if ($res->itemID==$rec->itemID ) echo "selected"; ?>  ><?php echo $res->brand .' '.$res->item.' '.$res->description.' '.$res->umsr ?></option>
				                                <?php } ?>
				                            </select>
				                        </td>
		                            </tr>
		                          </tbody>
		                        </table>
	                        </form>
	                    </div>						
                        <div class="datatables_wrapper">
							<div class="table-responsive scrollable-wrap">				
								<table class="table table-striped hover">
									<thead class="thead-light">
										<th>Date</th>
										<th>Particulars</th>
										<th>Beginning Balance</th>
										<th>Debit</th>
										<th>Credit</th>
										<th>End Balance</th>
										<th>Reference No</th>
									</thead>
									<tbody id="stockCardBody">
										<?php 
											if (count($record)) {
											    foreach($record as $row) {
											    ?>
										<tr >
											<td><?php echo date('M d, Y',strtotime($row->date))?></td>
											<td><?php echo $row->brand. ' '.$row->item.' '.$row->description.' ('.$row->umsr.')' ?></td>
											<td><?php echo $row->begBal ?></td>
											<td><?php echo $row->debit ?></td>											
											<td><?php echo $row->credit ?></td>											
											<td><?php echo $row->endBal ?></td>											
											<td><?php echo $row->refNo ?></td>											
										</tr>
										<?php }
											} else {	?>
										<tr>
											<td colspan="7" align="center"> <i>No records found!</i></td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<script>
    $(document).ready(function(){
        $('#btn-apply').on("click", function(){
            console.log("hello");
        });
    });
</script>