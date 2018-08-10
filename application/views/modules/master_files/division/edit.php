
<div class="subheader">
  <div class="d-flex align-items-center">
    <div class="title mr-auto">
      <h3><i class="icon left ti-layout-column2"></i> Division</h3>
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
              <h4 class="head-text">Edit Division</h4>
            </div>
          </div>
          <div class="card-head-tools"></div>
        </div>
        <div class="card-body">
          <form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url('division/update') ?>">
           <input type="hidden" class="form-control" name="divisionID" id="divisionID" value="<?php echo $rec->divisionID ?>" />
           <div class="table-row">
           	<table class="table-form column-3">
           		<tbody>
           			<tr>
           				<td class="form-label">Division Code <span class="asterisk">*</span></td>
           				<td class="form-group form-input">
           					<input type="text" class="form-control" name="divisionCode" id="divisionCode" value="<?php echo $rec->divisionCode?>" required>
           				</td>
           				<td class="form-label">Company <span class="asterisk">*</span></td>
           				<td class="form-group form-input">
           					<select id="companyID" name="companyID" class="form-control" data-live-search="true" liveSearchNormalize="true" required>
	                            <option value="">&nbsp;</option>
	                            <?php 
		                            $results = $this->db->get('companies')->result();
		                            foreach($results as $res){
	                            ?>
	                            <option value="<?php echo $res->companyID?>" <?php if($res->companyID == $rec->companyID){echo "selected";}?>><?php echo $res->companyName?></option>
	                            <?php }?>
                            </select>
           				</td>
           				<td class="d-xxl-none"></td>
           			</tr>
           			<tr>
           				<td class="form-label" style="width:12%">Division Name <span class="asterisk">*</span></td>
           				<td class="form-group form-input" style="width:21.33%">
           					<input type="text" class="form-control" name="divisionName" id="divisionName" value="<?php echo $rec->divisionName?>" required>
           				</td>
           				<td class="form-label" style="width:12%">Branch <span class="asterisk">*</span></td>
           				<td class="form-group form-input" style="width:21.33%">
           					<select id="branchID" name="branchID" class="form-control" data-live-search="true" liveSearchNormalize="true" required>
	                            <option value="">&nbsp;</option>
	                            <?php 
		                            $results = $this->db->get('branches')->result();
		                            foreach($results as $res){
	                            ?>
	                            <option value="<?php echo $res->branchID?>" <?php if($res->branchID == $rec->branchID){echo "selected";}?>><?php echo $res->branchName?></option>
	                            <?php }?>
                            </select>
           				</td>
           				<td class="d-xxl-none"></td>
           			</tr>
           			<tr>
           				<td class="form-label">Division Abbr <span class="asterisk">*</span></td>
           				<td class="form-group form-input">
           					<input type="text" class="form-control" name="divisionAbbr" id="divisionAbbr" value="<?php echo $rec->divisionAbbr?>" required>
           				</td>
           				<td class="form-label">Department <span class="asterisk">*</span></td>
           				<td class="form-group form-input">
           					<select id="deptID" name="deptID" class="form-control" data-live-search="true" liveSearchNormalize="true" required>
	                            <option value="">&nbsp;</option>
	                            <?php 
		                            $results = $this->db->get('departments')->result();
		                            foreach($results as $res){
	                            ?>
	                            <option value="<?php echo $res->deptID?>" <?php if($res->deptID == $rec->deptID){echo "selected";}?>><?php echo $res->deptName?></option>
	                            <?php }?>
                            </select>
           				</td>
           				<td class="d-xxl-none"></td>
           					</tr>
           			<tr>
           				<td class="form-label">Division Contact <span class="asterisk">*</span></td>
           				<td class="form-group form-input">
           					<input type="text" class="form-control" name="divisionContact" id="divisionContact" value="<?php echo $rec->divisionContact?>" required>
           				</td>
           				<td class="form-label">Division Email </td>
           				<td class="form-group form-input">
           					<input type="text" class="form-control" name="divisionEmail" id="divisionEmail" value="<?php echo $rec->divisionEmail?>">
           				</td>
           				<td class="d-xxl-none"></td>
           			</tr>
           			<tr>
           				<td class="form-label align-text-top pt-5">Division Head <span class="asterisk">*</span></td>
           				<td class="form-group form-input">
           					<?php 
                                  $this->db->where('status',1);
                                  $heads = $this->db->get('employees')->result();
                                  ?>
                                <select class="form-control" id="divisionHeadID" name="divisionHeadID" data-live-search="true" livesearchnormalize="true" style="width:200px">
                                <option value="">&nbsp;</option>
                                <?php foreach($heads as $row) {?>
                                    <option value="<?php echo $row->empID ?>" <?php if($row->empID == $rec->divisionHeadID){echo "selected";}?>><?php echo $row->lname.", ".$row->fname." ".$row->mname ?></option>
                                <?php } ?>
                                </select>
           				</td>
           				<td class="form-label align-text-top pt-5">Head Title <span class="asterisk">*</span></td>
           				<td class="form-group form-input">
           					<input type="text" class="form-control" name="divisionHeadTitle" id="divisionHeadTitle" value="<?php echo $rec->divisionHeadTitle?>" required>
           				</td>
           				<td class="d-xxl-none"></td>
           			</tr>
           			<tr>
           				<td class="form-label">Status <span class="asterisk">*</span></td>
           				<td class="form-group form-input">
           					<select id="status" name="status" class="form-control">
           						<option value="">&nbsp;</option>
           						<option value="1" <?php if($rec->status==1){echo "selected";}?>>Active</option>
           						<option value="0" <?php if($rec->status==0){echo "selected";}?>>Inactive</option>
           					</select>
           				</td>
           				<td class="form-label">Remarks</td>
           				<td class="form-group form-input">
           					<textarea class="form-control" name="remarks" id="remarks"><?php echo $rec->remarks?></textarea>
           				</td>
           				<td class="d-xxl-none"></td>
           			</tr>
           		</tbody>
           </table>
          </div>
          <div class="form-sepator solid"></div>
          <div class="form-group mb-0">
            <input class="btn btn-primary btn-raised pill" name="cmdSave" type="submit" id="cmdSave" value=" Save " />
            <input class="btn btn-outline-danger btn-raised pill" name="cmdCancel" type="button" id="cmdCancel" value=" Cancel " onclick="window.location='<?php echo site_url('division/view/'.$this->encrypter->encode($rec->divisionID)) ?>'" />
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>