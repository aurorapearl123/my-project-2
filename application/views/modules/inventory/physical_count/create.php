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
					<form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url("physical_count/save") ?>">
						<div class="table-row">
							<table class="table-form">
								<tbody>
									<tr>
										<td class="form-label w-10" nowrap>
											<label for="config">Branch : <span class="asterisk">*</span></label>
										</td>
										<td class="form-group form-input w-20">
											 <input type="text" class="form-control" name="branchName" id="branchName" title="Branch Name" value="<?php echo $branchName;?>" readonly>
                                            <input type="hidden" class="form-control" name="branchID" id="branchID" title="Branch Id" value="<?php echo $branchID;?>" readonly>
                                            <input type="hidden" id="cloth_category_size" name="cloth_category_size" value="<?php echo count($clothes_categories)?>">
										</td>
                                        <td class="form-label w-10" nowrap>Date : </td>
                                        <td class="form-group form-input  w-20">
                                            <input type="text" class="form-control" name="date" id="date" title="Date" required value="<?php echo date('F d,Y')?>" readonly>
                                        </td>
										<td class="d-xxl-none"></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div>
                            <!-- start create header details -->
                            <?php
                                $table_str="<table id='main' class='table mt-20'>";
                                $table_str.='<thead class="thead-light"><tr>';
                                $table_str.='<th>'.'No'.'</th>';
                                $table_str.='<th>'.'Category'.'</th>';
                                $table_str.='<th>'.'Expected Qty'.'</th>';
                                $table_str.='<th>'.'Physical Qty'.'</th>';
                                $table_str.='<th>'.'Variance'.'</th>';
                                $table_str.='<tr></thead>';
                                $i = 1;
                                foreach ($items as $rows) {
                                    $table_str.='<tr>';
                                    $table_str.='<td>'.($i++).'</td>';
                                    $table_str.='<td>'.$rows->item.'</td>';
                                    //$table_str.='<td>'.$rows->expected_qty.'</td>';
                                    //$table_str.='<td>'.'<span class="expected_qty">'.$rows->expected_qty.'</span>'.'</td>';
                                    $table_str.='<td>'.'<input type="number" name="expected_qyts[]" value="'.$rows->expected_qty.'" class="border-0 expected_qty" readonly>'.'</td>';
                                     //$table_str.='<td>'.'<input type="text" value="'.$rows->expected_qty.'" class="hour">'.'</td>';
                                    //$table_str.='<td>'.'<input type="number" min="1" id="id_'.($i++).'" name="id_'.($i++).'">'.'</td>';
                                    //$table_str.='<td>'.'<input type="text" min="1" name="clothes_qtys[]" >'.'</td>';
                                    $table_str.='<td>'.'<input type="number" min="1" name="physical_counts[]" class="form-control">'.'</td>';
                                     // $table_str.='<td><input type="button" value="Update" name="updateBox" id="updateBox" /></td>';

                                    $table_str.='<td>'.'<input type="number" min="1" name="variances[]" class="variance border-0" value="" readonly>'.'</td>';
                                    $table_str.='<td style="display:none">'.'<input type="hidden" min="1" name="" value="">'.'</td>';
                                    $table_str.='<td style="display:none">'.'<input type="hidden" min="1" name="item_ids[]" value="'.$rows->itemID.'">'.'</td>';
                                    
                                    $table_str.='</tr>';
                                }
                                $table_str.="</table>";

                                echo $table_str;
                            ?>
                            <!-- end create header details -->

						</div>
                        <div class="table-row mt-20">
							<table class="table-form">
	                            <tbody>
	                            	<tr>	                            		
		                                <td class="form-label w-10 align-text-top pt-5" nowrap>
		                                    <label for="config">Conducted By :  <span class="asterisk">*</span></label>
		                                </td>
		                                <td class="form-group form-input w-40">
		                                    <input class="form-control" name="conductedBy" id="conductedBy" title="Conducted By" required>
		                                </td>
		                                <td class="d-xxl-none"></td>
	                            	</tr>
		                            <tr>
		                                <td class="form-label w-10 align-text-top pt-5" nowrap>
		                                    <label for="config">Remarks :  <span class="asterisk">*</span></label>
		                                </td>
		                                <td class="form-group form-input w-40">
		                                    <textarea class="form-control" name="remarks" id="remarks" title="Remarks" required></textarea>
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
            // $.post("<?php echo $controller_page ?>/check_duplicate", { branchID:$('#branchID').val(), suppID:$('#suppID').val(), conductedBy:$('#conductedBy').val(), remarks:$('#remarks').val()},
            //   function(data){
            //     if (parseInt(data)) {
            //         $('#cmdSave').removeClass("loader");
            //         $('#cmdSave').removeAttr('disabled');
            //         // duplicate
            //         swal("Duplicate","Record is already exist!","warning");
            //     } else {
                    // submit
                    $('#frmEntry').submit();
                    // alert($('#branchID').val());
              //   }
              // }, "text");
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
		    	  window.location = '<?php echo site_url('physical_count/show') ?>';
		      }
		    });
	    
	});
    $(function(){
       $('#main').on("keyup", 'input[type="number"]', function () {
            var expected_qty = $(this).closest('tr').find('.expected_qty').val();
            //var customerId = $(this).prev('input').val();
            var physical_count = $(this).val();
            if(physical_count == 0) {
                $(this).closest('td').next('td').find('input').val(0);
            }
            else {
                 var total = expected_qty - physical_count;
                //change positive to negative vise versa
                if(total > 0) {
                    //console.log(total);
                    //console.log("positive");
                    //change to negative
                    total = -Math.abs(total);
                    //console.log(total);
                    //console.log("-------");
                }
                else {
                    //change to positive
                    ///console.log(total);
                    //console.log("negative");
                    total = Math.abs(total);
                    //console.log(total);
                }
                var lasttd =  $(this).closest('td').next('td').find('input').val(total);
                //console.log(physical_count.length);
                if(physical_count.length == 0) {
                    $(this).closest('td').next('td').find('input').val("");
                }
            }
           
        });

    });
</script>

</script>
