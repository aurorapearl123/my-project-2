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
							<h4 class="head-text">Add <?php echo $current_module['module_label'] ?></h4>
						</div>
					</div>
					<div class="card-head-tools"></div>
				</div>
				<div class="card-body">
					<form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url("equipment/save") ?>">
                        <input type="hidden" id="image-value" name="image">
						<div class="table-row">
							<table class="table-form">
								<tbody>
									<tr>
										<td class="form-label" width="13%" nowrap>Brand : <span class="asterisk">*</span></td>
										<td class="form-group form-input" width="22%">
											<input type="text" class="form-control" name="brand" id="brand" title="Brand" required>
										</td>
										<td class="form-label" width="13%" nowrap>Model : <span class="asterisk">*</span></td>
										<td class="form-group form-input" width="22%">
											<input type="text" class="form-control" name="model" id="model" title="Model" required>
										</td>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
										<td class="form-label" nowrap>Serial Number : <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="serialNo" id="serialNo" title="Serial Number" required>
										</td>
										<td class="form-label" nowrap>Name : <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="name" id="name" title="Name" required>
										</td>
										<td class="d-xxl-none"></td>
									</tr>

									<tr>
										<td class="form-label" nowrap>Date Purchased : </td>
										<td class="form-group form-input">
											<input type="text" class="form-control datepicker" name="datePurchased" id="datePurchased" title="Date Purchased" data-toggle="datetimepicker" data-target="#datePurchased" >
										</td>
										<td class="form-label" nowrap>Date First Used : </td>
										<td class="form-group form-input">
											<input type="text" class="form-control datepicker" name="dateFirstUsed" id="dateFirstUsed" title="Date First Used" data-toggle="datetimepicker" data-target="#dateFirstUsed" >
											
										</td>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>

										<td class="form-label align-text-top pt-5" nowrap>Description : <span class="asterisk">*</span></td>
										<td class="form-group form-input">											
											<textarea rows="2" type="text" class="form-control" name="description" id="description" title="Description" required></textarea>
										</td>
										<td class="d-xxl-none" colspan="3"></td>
									</tr>
								</tbody>
							</table>
						</div>
                        <span>Upload Receipt :</span>
                        <form action="#" >
                            <div class="formfield"><div id="my-dropzone" class="dropzone"></div></div>
                        </form>

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
<script src="<?php echo base_url('assets/js/dropzone.js') ?>" type="text/javascript"></script>
<script>
    //Dropzone Configuration
    Dropzone.autoDiscover = false;
    $(document).ready(function(){

        $('#my-dropzone').dropzone({
            url: 'post.php',
            method: 'post',
            dictInvalidFileType: "You can't upload files of this type.",
            maxFiles: 1,
            acceptedFiles: 'image/*',
            addRemoveLinks: true,
            init: function(){
                this.on("addedfile", function(file){
                    /*console.log(file.name);
                    console.log(file.status);
                    console.log(file.type);*/

                    if (this.files.length > 1) {
                        this.removeFile(this.files[0]);
                    }

                    setTimeout(() => {
                        //console.log(file.dataURL);
                        $('#image-value').val(file.dataURL);

                    }, 2000);

                    //this.emit('thumbnail', file, "");

                    //console.log(file);
                });
            }
        });

    });
    $(document).ready(function(){
        var url = "<?php echo $controller_page ?>/check_duplicate";
        Dropzone.autoDiscover = false;
        Dropzone.options.dropzoneFileUpload = {
            url: url,
            method: "POST",
            clickable: true,
            previewsContainer:".dropzone_preview",
            multipleUploads: true,
            parallelUploads: 100,
            maxFiles:100
        }
    });
	$('#cmdSave').click(function(){
		if (check_fields()) {
	    	$('#cmdSave').attr('disabled','disabled');
	    	$('#cmdSave').addClass('loader');
	        $.post("<?php echo $controller_page ?>/check_duplicate", { brand: $('#brand').val(), model: $('#model').val(), serialNo: $('#serialNo').val(), name: $('#name').val() },
	          function(data){
	            if (parseInt(data)) {
	            	$('#cmdSave').removeClass("loader");
	            	$('#cmdSave').removeAttr('disabled');
	              	// duplicate
	              	swal("Duplicate","Record is already exist!","warning");
	            } else {
	     //        	// submit
	               	$('#frmEntry').submit();
	            }
	          }, "text");
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
		    	  window.location = '<?php echo site_url('equipment/show') ?>';
		      }
		    });
	    
	});
</script>