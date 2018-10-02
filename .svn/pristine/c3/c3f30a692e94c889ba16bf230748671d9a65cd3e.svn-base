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
		<div class="col-lg-8 col-xxl-9">
			<div class="card-box">
				<div class="card-head">
					<div class="head-caption">
						<div class="head-title">
							<h4 class="head-text">View <?php echo $current_module['module_label'] ?></h4>
						</div>
					</div>
					<div class="card-head-tools"></div>
				</div>
				<div class="card-body">
					<form method="post" name="frmEntry1" id="frmEntry1" >
						<input type="hidden" name="orderID" id="orderID" value="<?php echo $this->encrypter->encode($rec->orderID) ?>" />
						<div class="table-row">
							<table class="table-form">
								<tbody>
									<tr>
										<td class="form-label" style="width:13%">
											<label for="config">Branch : <span class="asterisk">*</span></label>
										</td>
										<td class="form-group form-input" style="width:22%">
											<input type="text" class="form-control" name="branchName" id="branchName" title="Branch Name" value="<?php echo $branchName;?>" readonly>
											<input type="hidden" class="form-control" name="branchID" id="branchID" title="Branch Id" value="<?php echo $branchID;?>" readonly>
											<input type="hidden" id="cloth_category_size" name="cloth_category_size" value="<?php echo count($clothes_categories)?>">
										</td>
										<td class="form-label" style="width:13%" nowrap>Date : </td>
										<td class="form-group form-input" style="width:22%">
											<input type="text" class="form-control" name="date" id="date" title="Date" required value="<?php echo date('F d, Y')?>" readonly>
										</td>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
										<td class="form-label">Customer Name : <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<input type="hidden" class="form-control" name="custID" id="custID" title="Cust ID" required value="<?php echo $customer->custID ?>" readonly>
											<input type="text" class="form-control" name="custName" id="custName" title="Cust Name" required value="<?php echo $customer->fname .' '.$customer->mname.' '.$customer->lname?>" readonly>
										</td>
										<td class="form-label">is discounted <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<div class="checkbox">
												<label>
													<input type="checkbox" name="isDiscounted" id="isDiscounted" value="<?php if($rec->isDiscounted == 'Y'){
														echo "Y";
													}
													else {
														echo "N";
													};?>" aria-label="..." <?php if($rec->isDiscounted == 'Y') echo "checked='checked'";?> disabled>&nbsp;
												</label>
											</div>
										</td>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
										<td class="form-label">Service : </td>
										<td class="form-group form-input"> 
											<input type="hidden" class="form-control" name="serviceID" id="serviceID" required value="<?php echo $service_type->serviceID ?>" readonly>
											<input type="text" class="form-control" name="serviceType" id="serviceType" title="Service Type" required value="<?php echo $service_type->serviceType ?>" readonly>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<!-- start create header details -->
						<?php
						$table_str="<table class='table mt-30'>";
						$table_str.='<thead class="thead-light"><tr>';
						$table_str.='<th>'.'Quantity'.'</th>';
						$table_str.='<th>'.'Clothes Category'.'</th>';
						$table_str.='<tr></thead>';
						$i = 1;
						foreach ($clothes_categories as $rows) {
							$table_str.='<tr>';
                  //$table_str.='<td>'.'<input type="number" min="1" id="id_'.($i++).'" name="id_'.($i++).'">'.'</td>';
                  //$table_str.='<td style="width: 100px" align="left">'.'<input type="text" min="1" name="clothes_qtys[]" onkeypress="return isNumber(event)" class="form-control">'.'</td>';
							$table_str.='<td style="width: 200px" align="left">'.'<input type="number" min="1" name="clothes_qtys[]" value="'.$rows['qty'].'" class="form-control" readonly  >'.'</td>';
							$table_str.='<td style="display:none">'.'<input type="hidden" min="1" name="clothes_ids[]" value="'.$rows['clothesCatID'].'">'.'</td>';
							$table_str.='<td>'.$rows['category'].'</td>';
							$table_str.='</tr>';
						}
						$table_str.="</table>";

						echo $table_str;
						?>
						<!-- end create header details -->
						<div class="table-row mt-30">
							<table class="table-form">
								<tbody>
									<tr>
										<td class="form-label" style="width: 13%">
											<label for="config">Rate : <span class="asterisk">*</span></label>
										</td>
										<td class="form-group form-input" style="width: 22%">
											<input type="text" class="form-control" name="rate" id="rate" title="Rate" value="<?php echo $rec->rate;?>"  readonly required>
										</td>
										<td class="form-label" style="width: 13%">Quantity in kilos : </td>
										<td class="form-group form-input" style="width: 22%">
											<input type="number" class="form-control" name="qty" id="qty" min="0" title="Quantity in kilos"  value="<?php echo $rec->qty;?>" readonly required>
										</td>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
										<td class="form-label">Sub total:  <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<input type="number" class="form-control" name="subtotal" id="subtotal" min="0" title="Company Abbr" readonly required>
										</td>
										<td class="form-label">Deliver Fee:  <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="deliveryFee" id="deliveryFee" title="Delivery Fee"  value="<?php echo $rec->deliveryFee;?>" readonly required>
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
						</div>
					</form>
				</div>
			</div>

			<div class="card-box">
				<div class="card-head">
					<div class="head-caption">
						<div class="head-title">
							<h4 class="head-text">Edit Complaints
							</h4>
						</div>
					</div>
				</div>
				<div class="card-body">
					<form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url("order/updateComments") ?>">
						<input type="hidden" name="orderID" id="orderID" value="<?php echo $this->encrypter->encode($rec->orderID) ?>" />
						<div class="row">
							<div class="col-md-6">
								<div class="data-view">
									<table class="view-table">
										<tbody>
											<tr>
												<td class="data-title w-10">Management :</td>
											</tr>
											<tr> 
												<td class="data-input">

													<textarea rows="2" type="text" class="form-control" name="adminComment" id="adminComment"  title="Complaint" required autofocus><?php echo $order_headers->adminComment; ?></textarea>
													
														
													</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="col-md-6">
								<div class="data-view">
									<table class="view-table">
										<tbody>
											<tr>
												<td class="data-title ">Customer :</td>                                              
											</tr>
											<tr>                        
												<td class="data-input"><?php echo $order_headers->custComment; ?> </td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>

						</div>

							<div class="form-sepator solid"></div>
							<div class="form-group mb-0" >
								<button class="btn btn-primary btn-raised pill" type="button" name="cmdSave" id="cmdSave">
									Save
								</button>
								<input type="button" id="cmdCancel" class="btn btn-outline-danger btn-raised pill" value="Cancel"/>
							</div>
					</form>
				</div>
			</div>
		</div>
			<div class="col-lg-4 col-xxl-3">
				<div class="card-box">
					<div class="card-head">
						<div class="head-caption">
							<div class="head-title">
								<h4 class="head-text">Details</h4>
							</div>
						</div>
					</div>
					<div class="card-body">
						<form method="post" name="frmEntry2" id="frmEntry2" >
							<div class="data-view">
								<table class="view-table">
									<tbody>
										<tr>
											<td class="data-title w-50">Rate :</td>
											<td class="py-5"><input type="text" class="form-control" name="rate" id="rate" value="<?php echo $rec->rate;?>" readonly></td>
										</tr>
										<tr>
											<td class="data-title">Quantity in kilos :</td>
											<td class="py-5"><input type="number" class="form-control" name="qty" id="qty" min="0" value="<?php echo $rec->qty;?>" readonly></td>
										</tr>
										<tr>
											<td class="data-title">Sub total:</td>
											<td class="py-5"><input type="number" class="form-control" name="subtotal" id="subtotal" min="0" readonly></td>
										</tr>
										<tr>
											<td class="data-title">Deliver Fee:</td>
											<td class="py-5"><input type="text" class="form-control" name="deliveryFee" id="deliveryFee"  value="<?php echo $rec->deliveryFee;?>" readonly></td>
										</tr>
										<tr>
											<td class="data-title">Amount :</td>
											<td class="py-5"><input type="number" class="form-control" name="ttlAmount" id="ttlAmount" readonly></td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="form-sepator solid mx-0"></div>
							<div class="data-view">
								<table class="view-table">
									<tbody>
										<tr>
											<?php foreach($users as $user) { ?>
											<td>Prepared by : &nbsp;&nbsp;<span><?php echo $user->firstName .' '.$user->middleName.' '.$user->lastName;?></span></td>
										</tr>
										<?php if($rec->dateWashed != '0000-00-00 00:00:00') { ?>
											<tr>
												<td>Date washed : &nbsp;&nbsp;<span><?php echo date('M d, Y H:i:s',strtotime($rec->dateWashed)) ;?></span></td>
											</tr>
										<?php } ?>
										<?php if($rec->dateReady != '0000-00-00 00:00:00') { ?>
											<tr>
												<td>Date Ready : &nbsp;&nbsp;<span><?php echo $rec->dateReady;?></span></td>
											</tr>
										<?php } ?>
										<?php if($rec->cancelledBy != '') { ?>
											<tr>
												<td>Cancelled by : &nbsp;&nbsp;<span><?php echo $user->cancelledFirstName .' '.$user->cancelledMiddleName.' '.$user->cancelledLastName;?></span></td>
											</tr>
										<?php } } ?>
										<?php if($rec->dateFold != '0000-00-00 00:00:00') { ?>
											<tr>
												<td>Date Fold : &nbsp;&nbsp;<span><?php echo date('M d, Y H:i:s',strtotime($rec->dateFold)) ;?></span></td>
											</tr>
										<?php } ?>
										<?php if($rec->dateReleased != '0000-00-00 00:00:00') { ?>
											<tr>
												<td>Date Released : &nbsp;&nbsp;<span><?php echo date('M d, Y H:i:s',strtotime($rec->dateReleased)) ;?></span></td>

											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
							<div class="mt-10">
								<?php if ($rec->custSign) { ?>
									<p>Signature : </p>
									<div style="max-height:200px">
										
										<img src="<?php echo $rec->custSign; ?>" alt="Red dot" style="max-height: 200px; max-width: 100%;" />
										
									</div>
								<?php } ?>
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
				window.location = '<?php echo site_url('order/show') ?>';
			}
		});
	});

    $(document).ready(function() {

        var service_id = $('#serviceID').val();
        var isDescounted = $('#isDiscounted').val();

        var discount = 0;
        if(isDescounted == "Y")
        {
            discount = 1;
            console.log();
        }

        $.post("<?php echo $controller_page ?>/check_service_discount", { service_id: service_id, is_discounted: discount },
            function(data, status){
                if(data != 0)  {
                     var rate1 = $('#rate').val(data);
                     console.log("result");
                     console.log(data);
                     //console.log("calculate");
                     //calculate all data
                     //console.log(rate1);

                     var rate = parseInt($('#rate').val());
                     var quantity_kilo = parseInt($('#qty').val());
                     var total = quantity_kilo * rate;
                     $('#subtotal').val(total);
                     //initial total amount
                     var subtotal = parseInt($('#subtotal').val());
                     var deliveryFee = parseInt($('#deliveryFee').val());
                     var totalAmount = subtotal + deliveryFee;

                     $('#ttlAmount').val(totalAmount);



                }

            }
        );
  
  
        $('#isDiscounted').change(function() {
             //$('#textbox1').val($(this).is(':checked'));
             //alert("hello");
             console.log("is discounted");
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

             calculate_data();
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

        
     function check_service_discount(isDescounted,service_id)
     {
         console.log("is discounted : ", isDescounted);
         if ($("#isDiscounted").is(":checked")) {
             isDiscounted = 1;
         }
         $.post("<?php echo $controller_page ?>/check_service_discount", { service_id: service_id, is_discounted: isDescounted },
             function(data, status){
                 if(data != 0)  {
                     $('#rate').val(data);
                     

                     var rate = parseInt($('#rate').val());
                     var quantity_kilo = parseInt($('#qty').val());
                     var total = quantity_kilo * rate;
                     $('#subtotal').val(total);
                     //initial total amount
                     var subtotal = parseInt($('#subtotal').val());
                     var deliveryFee = parseInt($('#deliveryFee').val());
                     var totalAmount = subtotal + deliveryFee;

                     $('#ttlAmount').val(totalAmount);

                 }
  
             });
     }

     function calculate_data()
     {
         var total = $('#ttlAmount').val();
         var sub_total = $('#subtotal').val();
         var deliveryFee = $('#deliveryFee').val();

         var amount = +sub_total + +deliveryFee;
         console.log("delivery fee", deliveryFee);
         console.log("sub total",sub_total);
         console.log("total",total);
         console.log("amount", amount);
         $('#ttlAmount').val(amount);
     }
  
  
    });



</script>