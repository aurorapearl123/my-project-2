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
                        <h4 class="head-text">Add Position</h4>
                      </div>
                    </div>
                    <div class="card-head-tools"></div>
                  </div>
                  <div class="card-body">
                    <form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url("position/save") ?>">
                      <div class="table-row">
                        <table class="table-form">
                          <tbody>
                            <tr>
                              <td class="form-label" style="width:150px">
                                <label for="config">Position Code<span class="asterisk">*</span></label>
                              </td>
                              <td class="form-group form-input">
                                <input type="text" class="form-control" name="positionCode" id="positionCode" style="width:200px" required>
                              </td>
                              <td class="d-xxl2-none"></td>
                            </tr>
                            <tr>
                              <td class="form-label">
                               <label for="occupation">Position Name<span class="asterisk">*</span></label>
                              </td>
                              <td class="form-group form-input">
                                 <input type="text" class="form-control" name="positionName" id="positionName" style="width:400px" required>
                              </td>
                              <td class="d-xxl2-none"></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <div class="form-sepator solid"></div>
                      <div class="form-group mb-0">
                        <input type="submit" name="cmdSave" id="cmdSave" value="Save" class="btn btn-primary btn-raised pill" onclick="save();"/>
                        <input type="button" id="cmdCancel" class="btn btn-outline-danger btn-raised pill" value="Cancel"/>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
<script>
function save() 
{
	if (check_form('frmEntry')) {
		$.post("<?php echo $controller_page ?>/check_duplicate", { 
			empID: $('#empID').val(),
			skill: $('#skill').val() },
		  function(data){
		    if (parseInt(data)) {
		    	// duplicate
		    	alert("Error: record is already exist!");
		    } else {
		    	// no duplicate
		    	$('#frmEntry').submit();
		    }
		  }, "text");
	}
}

$('#cmdCancel').click(function(){
    window.location = '<?php echo site_url('position/show') ?>';
});

</script>
