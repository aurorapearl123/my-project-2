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
					<form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url('equipment_monitoring/update') ?>">
						<input type="hidden" name="emID" id="emID" value="<?php echo $this->encrypter->encode($rec->emID) ?>" />
                        <div class="table-row">
                            <table class="table-form column-3">
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
                                            <select class="form-control" id="branchID" name="branchID" data-live-search="true" livesearchnormalize="true" style="" title="-Select Branch-" >
                                                <?php foreach($recs as $res) {?>
                                                <option value="<?php echo $res->branchID ?>" <?php if ($res->branchID==$rec->branchID ) echo "selected"; ?>  ><?php echo $res->branchName ?></option>
                                                <?php } ?>
                                            </select>

                                        </td>
                                        <td class="form-label" width="13%" nowrap>Date : </td>
                                        <td class="form-group form-input" width="22%">
                                            <input type="text" class="form-control datepicker" id="date" name="date" data-toggle="datetimepicker" value="<?php echo date('M d, Y',strtotime($rec->date))?>" data-target="#date" title="Date" required>
                                        </td>
                                        <td class="d-xxl-none"></td>
                                    </tr>
                                    <tr> 
                                        <td class="form-label" nowrap>Equipment : <span class="asterisk">*</span></td>
                                        <td class="form-group form-input">
                                        <?php 
                                            $this->db->select('equipments.*');
                                            $this->db->from('equipments');
                                            $this->db->where('status',1);
                                            $recs = $this->db->get()->result();                                             
                                        ?>
                                        <select class="form-control" id="equipmentID" name="equipmentID" data-live-search="true" livesearchnormalize="true" style="" title="-Select Equipments-" >
                                            <?php foreach($recs as $res) {?>
                                            <option value="<?php echo $res->equipmentID ?>" <?php if ($res->equipmentID==$rec->equipmentID ) echo "selected"; ?>  ><?php echo $res->brand .' '.$res->model.' '.$res->serialNo.' '.$res->name.' '.$res->description ?></option>
                                            <?php } ?>
                                        </select>
                                        </td>      
                                    </tr>
                                    <tr>
                                        <td class="form-label" nowrap>Remarks : <span class="asterisk">*</span></td>
                                        <td class="form-group form-input">
                                            <textarea rows="2" type="text" class="form-control" name="remarks" id="remarks" title="Remarks" required><?php echo $rec->remarks ?></textarea>
                                        </td>
                                        <td class="d-xxl-none"></td>
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
            .then((willCancel) => {
              if (willCancel.value) {
                  window.location = '<?php echo site_url('equipment_monitoring/show') ?>';
              }
            });
        
    });
</script>
