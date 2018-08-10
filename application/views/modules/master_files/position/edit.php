
<div class="subheader">
  <div class="d-flex align-items-center">
    <div class="title mr-auto">
      <h3><i class="icon left la la-street-view"></i> Position</h3>
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
              <h4 class="head-text">Edit Position</h4>
            </div>
          </div>
          <div class="card-head-tools"></div>
        </div>
        <div class="card-body">
          <form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url('position/update') ?>">
            <input type="hidden" name="positionID" id="positionID" value="<?php echo $this->encrypter->encode($rec->positionID) ?>" />
            <div class="table-row">
              <table class="table-form">
                <tbody>
                  <tr>
                    <td class="form-label" style="width:150px">
                      <label for="positionCode">Position. Code<span class="asterisk">*</span></label>
                    </td>
                    <td class="form-group form-input">
                      <input type="text" class="form-control" name="positionCode" id="positionCode" value="<?php echo $rec->positionCode ?>" style="width:200px" required>
                    </td>
                  </tr>
                  <tr>
                    <td class="form-label">
                     <label for="positionName">Position Name<span class="asterisk">*</span></label>
                   </td>
                   <td class="form-group form-input">
                    <input type="text" class="form-control" name="positionName" id="positionName" value="<?php echo $rec->positionName ?>" style="width:400px" required>
                   </td>
                 </tr>
                 <tr>
                  <td class="form-label form-input">
                    <label for="status">Status</label>
                  </td>
                  <td class="form-group form-input">
                    <select id="status" name="status" class="form-control" data-live-search="true" liveSearchNormalize="true" style="width:200px" required>
                    	<option value="1" <?php if($rec->status==1){echo "selected";}?>>Active</option>
                    	<option value="0" <?php if($rec->status==0){echo "selected";}?>>Inactive</option>
                    </select>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="form-sepator solid"></div>
          <div class="form-group mb-0">
            <input class="btn btn-primary btn-raised pill" name="cmdSave" type="submit" id="cmdSave" value=" Save " />
            <input class="btn btn-outline-danger btn-raised pill" name="cmdCancel" type="button" id="cmdCancel" value=" Cancel " onclick="window.location='<?php echo site_url('position/view/'.$this->encrypter->encode($rec->positionID)) ?>'" />
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>