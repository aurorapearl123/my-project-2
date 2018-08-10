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
					<form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url("order/save") ?>">
						<div class="table-row">
							<table class="table-form column-3">
								<tbody>
									<tr>
										<td class="form-label" nowrap>
											<label for="config">Branch  <span class="asterisk">*</span></label>
										</td>
										<td class="form-group form-input" nowrap>
											 <input type="text" class="border-0" name="branchName" id="branchName" title="Branch Name" value="<?php echo $branchName;?>" readonly>
                                            <input type="hidden" class="form-control" name="branchID" id="branchID" title="Branch Id" value="<?php echo $branchID;?>" readonly>
                                            <input type="hidden" id="cloth_category_size" name="cloth_category_size" value="<?php echo count($clothes_categories)?>">
										</td>
                                        <td width="450px">

                                        </td>
                                        <td class="form-label" style="width:12%" nowrap>Date</td>
                                        <td class="form-group form-input" style="width:21.33%">
                                            <input type="text" class="border-0" name="date" id="date" title="Date" required value="<?php echo date('Y-m-d')?>" readonly>
                                        </td>
										<td class="d-xxl-none" colspan="3"></td>
									</tr>
									<tr>
										<td class="form-label" style="width:12%" nowrap>Customer Name <span class="asterisk">*</span></td>

                                        <td class="form-group form-input" style="width:21.33%" nowrap>
                                            <select class="form-control" id="custID" name="custID" data-live-search="true" livesearchnormalize="true" title="Customer Name" required>
                                                <option value="" selected>&nbsp;</option>
                                                <?php foreach($customers as $row) { ?>
                                                    <option value="<?php echo $row->custID ?>"><?php echo $row->fname .' '. $row->mname .' '.$row->lname ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td>
                                        </td>
										<td class="form-label" style="width:12%" nowrap>is discounted <span class="asterisk">*</span></td>
										<td class="form-group form-input" style="width:21.33%">
                                            <div class="form-check">
                                                <input class="form-check-input position-static" type="checkbox" name="isDiscounted" id="isDiscounted" value="Y" aria-label="...">
                                            </div>
										</td>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
                                        <td class="form-label">Service </td>
                                        <td class="form-group form-input">
                                            <select class="form-control" id="serviceID" name="serviceID" data-live-search="true" livesearchnormalize="true" title="Service" required>
                                                <option value="" selected>&nbsp;</option>
                                                <?php foreach($service_types as $row) { ?>
                                                    <option value="<?php echo $row->serviceID ?>"><?php echo $row->serviceType ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
									</tr>
								</tbody>
							</table>
                            <!-- start create header details -->
                            <?php
                                $table_str="<table border='0' class='table table-sm'>";
                                $table_str.='<tr>';
                                $table_str.='<th>'.'Quantity'.'</th>';
                                $table_str.='<th>'.'Clothes Category'.'</th>';
                                $table_str.='<tr>';
                                $i = 1;
                                foreach ($clothes_categories as $rows) {
                                    $table_str.='<tr>';
                                    //$table_str.='<td>'.'<input type="number" min="1" id="id_'.($i++).'" name="id_'.($i++).'">'.'</td>';
                                    $table_str.='<td style="width: 100px" align="left">'.'<input type="text" min="1" name="clothes_qtys[]" onkeypress="return isNumber(event)" class="form-control">'.'</td>';
                                    $table_str.='<td style="display:none">'.'<input type="hidden" min="1" name="clothes_ids[]" value="'.$rows->clothesCatID.'">'.'</td>';
                                    $table_str.='<td>'.$rows->category.'</td>';
                                    $table_str.='</tr>';
                                }
                                $table_str.="</table>";

                                echo $table_str;
                            ?>
                            <!-- end create header details -->
						</div>
                        <br>

                        <table class="table-form column-3">
                            <tbody>
                            <tr>
                                <td class="form-label" nowrap>
                                    <label for="config">Rate :  <span class="asterisk">*</span></label>
                                </td>
                                <td class="form-group form-input" nowrap>
                                    <input type="text" class="form-control" name="rate" id="rate" title="Branch Name" readonly required>
                                </td>
                                <td width="450px">

                                </td>
                                <td class="form-label" style="width:12%" nowrap>Quantity in kilos : </td>
                                <td class="form-group form-input" style="width:21.33%">
                                    <input type="number" class="form-control" name="qty" id="qty" min="0" title="Quantity in kilos" required>
                                </td>
                                <td class="d-xxl-none" colspan="3"></td>
                            </tr>
                            <tr>
                                <td class="form-label" style="width:12%" nowrap>Sub total:  <span class="asterisk">*</span></td>
                                <td class="form-group form-input" style="width:21.33%" nowrap>
                                    <input type="number" class="form-control" name="subtotal" id="subtotal" min="0" title="Company Abbr" readonly required>
                                </td>
                                <td>

                                </td>
                                <td class="form-label" style="width:12%" nowrap>Deliver Fee:  <span class="asterisk">*</span></td>
                                <td class="form-group form-input" style="width:21.33%" nowrap>
                                    <input type="text" class="form-control" name="deliveryFee" id="deliveryFee" title="Delivery Fee" required>
                                </td>
                                <td class="d-xxl-none"></td>
                            </tr>
                            <tr>
                                <td class="form-label">Amount : </td>
                                <td class="form-group form-input">
                                    <input type="number" class="form-control" name="ttlAmount" id="ttlAmount" title="Total Amount" readonly required>
                                </td>
                            </tr>
                            </tbody>
                        </table>

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
	        $.post("<?php echo $controller_page ?>/check_duplicate", { companyCode: $('#companyCode').val() },
	          function(data){
	            if (parseInt(data)) {
	            	$('#cmdSave').removeClass("loader");
	            	$('#cmdSave').removeAttr('disabled');
	              	// duplicate
	              	swal("Duplicate","Record is already exist!","warning");
	            } else {
	            	// submit
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
		    	  window.location = '<?php echo site_url('order/show') ?>';
		      }
		    });
	    
	});
    $(document).ready(function() {
        //set initial state.


        $('#isDiscounted').change(function() {
            //$('#textbox1').val($(this).is(':checked'));
            //alert("hello");
            if($(this).is(':checked')) {
                $('#rate').val('');
                var service_id = $('#serviceID').val();
                check_service_discount(1, service_id);
            }
            else {
                $('#rate').val('');
                $('isDiscounted').attr('value','Y');
                var service_id = $('#serviceID').val();
                check_service_discount(0, service_id);
            }
        });

        $('#serviceID').on('change', function(){
            $('#rate').val('');
            var isDiscounted = 0;
            if ($("#isDiscounted").is(":checked")) {
               isDiscounted = 1;
               $('isDiscounted').attr('value','Y');
            }
            else {
                $('isDiscounted').attr('value','N');
            }

            var service_id = $('#serviceID').val();
            check_service_discount(isDiscounted, service_id);

        });
        //event for quantity in kilos
        $('#qty').keyup(function(){
            //alert('Hello');
            if($('#rate').val() == "") {
                alert("please input rate");
            }
            else {
                var rate = parseInt($('#rate').val());
                var quantity_kilo = parseInt($('#qty').val());
                var total = quantity_kilo * rate;

                $('#subtotal').val(total);
            }
        });

        $('#deliveryFee').keyup(function(){
            //alert('Hello');
            if($('#subtotal').val() == "") {
                alert("please input subtotal");
            }
            else {
                var subtotal = parseInt($('#subtotal').val());
                var deliveryFee = parseInt($('#deliveryFee').val());
                var total = subtotal + deliveryFee;

                $('#ttlAmount').val(total);
            }
        });


    });



    function check_service_discount(isDescounted,service_id)
    {
        if ($("#isDiscounted").is(":checked")) {
            isDiscounted = 1;
        }
        $.post("<?php echo $controller_page ?>/check_service_discount", { service_id: service_id, is_discounted: isDescounted },
            function(data, status){
                if(data != 0)  {
                    $('#rate').val(data);
                }

            });
    }

    // $(document).ready(function() {
    //     //set initial state.
    //     $('#textbox1').val($(this).is(':checked'));
    //
    //     $('#checkbox1').change(function() {
    //         $('#textbox1').val($(this).is(':checked'));
    //     });
    //
    //     $('#checkbox1').click(function() {
    //         if (!$(this).is(':checked')) {
    //             return confirm("Are you sure?");
    //         }
    //     });
    // });

    //link
    //http://jsfiddle.net/JsUWv/

</script>
