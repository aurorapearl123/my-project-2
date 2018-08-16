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
					<form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url('receiving_report/update') ?>">
						<input type="hidden" name="rrID" id="rrID" value="<?php echo $this->encrypter->encode($rec->rrID) ?>" />
                        <div class="table-row">
                            <table class="table-form">
                                <tbody>
                                    <tr>
                                        <td class="form-label" width="13%">
                                            <label for="config">Branch : <span class="asterisk">*</span></label>
                                        </td>
                                        <td class="form-group form-input" width="22%">
                                            <input type="text" class="form-control" name="branchName" id="branchName" title="Branch Name" value="<?php echo $branchName;?>" readonly>
                                            <input type="hidden" class="form-control" name="branchID" id="branchID" title="Branch Id" value="<?php echo $branchID;?>" readonly>
                                            <input type="hidden" id="cloth_category_size" name="cloth_category_size" value="<?php echo count($clothes_categories)?>">
                                        </td>
                                        <td class="form-label" width="13%">Date : </td>
                                        <td class="form-group form-input" width="22%">
                                            <input type="text" class="form-control" name="date" id="date" title="Date" required value="<?php echo $rec->date?>" readonly>
                                        </td>
                                        <td class="d-xxl-none"></td>
                                    </tr>
                                    <tr>
                                        <td class="form-label">Supplier : </td>
                                        <td class="form-group form-input">
                                            <select class="form-control" id="suppID" name="suppID" data-live-search="true" livesearchnormalize="true" title="Supplier" required>
                                                <option value="" selected>&nbsp;</option>
                                                <?php foreach($suppliers as $row) { ?>
                                                    <option value="<?php echo $row->supplierID ?>"<?php if($row->supplierID == $rec->suppID){echo "selected";}?>><?php echo $row->name?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td class="form-label" nowrap>Reference No : <span class="asterisk">*</span></td>
                                        <td class="form-group form-input">
                                            <input type="text" class="form-control" name="referenceNo" id="referenceNo" title="Reference no" value="<?php echo $rec->referenceNo;?>" required>
                                        </td>
                                        <td class="d-xxl-none"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-sepator solid mx-0"></div>
                        <div>
                            <!-- start create header details -->
                            <table class="table" id="tb">
                                <thead class="thead-light">
                                    <tr class="tr-header">
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Amount</th>
                                    <th>Option</th>
                                </thead>
                                <tbody>
                                <?php foreach($rr_details as $detail) : ?>
                                    <tr class="item">
                                        <td class="id"><?php echo $detail->brand?></>
                                        <td style="display:none">
                                            <input type="hidden" id="item_ids[]" name="item_ids[]" value="<?php echo $detail->itemID;?>" class="item_id" readonly>
                                        </td>
                                        <td>
                                            <input type="text"  name="prices[]" value="<?php echo $detail->price; ?>" class="name border-0" readonly>

                                        </td>
                                        <td class="id">
                                            <input type="text" class="id border-0" name="amounts[]" value="<?php echo $detail->amount?>" readonly>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="btn btn-outline-light bmd-btn-icon btn-xs remove" title="Delete"><i class="icon la la-trash-o sm"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="form-group form-input" width="15%">
                                            <select class="form-control w-80" id="itemID" name="itemID" data-live-search="true" livesearchnormalize="true" title="Item">
                                                <option value="" selected>&nbsp;</option>
                                                <?php foreach($items as $row) { ?>
                                                    <option value="<?php echo $row->itemID ?>"><?php echo $row->brand?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td class="form-group form-input" width="15%">
                                            <input type="number" class="form-control w-80" name="price" id="price" title="Price" min="0"  value="<?php echo $price?>">
                                        </td>
                                        <td class="form-group form-input" width="15%">
                                            <input type="number" class="form-control w-80" name="amount" id="amount" title="Amount" min="0" value="<?php echo $amount?>">
                                        </td>
                                        <td class="form-group form-input" width="10%">
                                            <button id="addMore" type="button" class="btn btn-primary btn-raised pill btn-xs">Add</button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            <!-- end create header details -->
                        </div>
                        <div class="table-row mt-20">
                            <table class="table-form">
                                <tbody>
                                    <tr>
                                        <td class="form-label" width="13%" nowrap>
                                            <label for="config">Amount :  <span class="asterisk">*</span></label>
                                        </td>
                                        <td class="form-group form-input" width="22%">
                                            <input type="text" class="form-control" name="ttlAmount" id="ttlAmount" title="Branch Name"  value="<?php echo $rec->ttlAmount; ?>" required readonly>
                                        </td>
                                        <td></td>
                                        <td class="d-xxl-none"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-sepator solid"></div>
                        <div class="form-group mb-0">
                            <button class="btn btn-primary btn-raised pill" type="button" name="cmdSave" id="cmdSave">Save</button>
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
            //$.post("<?php //echo $controller_page ?>///check_duplicate", { companyCode: $('#companyCode').val() },
            //  function(data){
            //    if (parseInt(data)) {
            //      $('#cmdSave').removeClass("loader");
            //      $('#cmdSave').removeAttr('disabled');
            //          // duplicate
            //          swal("Duplicate","Record is already exist!","warning");
            //    } else {
            //      // submit
            //          $('#frmEntry').submit();
            //    }
            //  }, "text");

            // var trIndex = $(this).closest("tr").index();
            // if(trIndex>1) {
            //     $(this).closest("tr").remove();
            // } else {
            //     alert("Sorry!! Can't remove first row!");
            // }

            // var trIndex = $('#tb').closest("tr").index();
            // if(trIndex>1) {
            //     //$(this).closest("tr").remove();
            //     $('#frmEntry').submit();
            // } else {
            //     $('#cmdSave').removeClass("loader");
            //     $('#cmdSave').removeAttr('disabled');
            //     alert("Sorry!! Can't save empty items details!");
            // }
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
                  window.location = '<?php echo site_url('receiving_report/show') ?>';
              }
            });
    });
    $(function(){
        $('#addMore').on('click', function() {
            var item = $('#itemID').val();
            var item_text = $('#itemID option:selected').text();
            var amount = $('#amount').val();
            var price = $('#price').val();
            if(amount != "" && price != "") {
                //check item for duplicate
                $("tr.item").each(function() {
                    var item_id = $(this).find("input.item_id").val();
                    console.log("item id");
                    console.log($('#itemID').val());
                    console.log(item_id);
                });

                $('#tb').append($('<tr class="item">')
                    .append($('<td id="item[]">').text(item_text))
                    .append($('<td style="display:none"><input type="hidden" id="item_ids[]" name="item_ids[]" value="'+item+'"  readonly>'))
                    .append($('<td><input type="text"  name="prices[]" value="'+price+'" class="name border-0" readonly>'))
                    .append($('<td><input type="text" name="amounts[]" value="'+amount+'" class="id border-0" readonly>'))
                    .append($('<td><a href="javascript:void(0);" class="btn btn-outline-light bmd-btn-icon btn-xs remove" title="Delete"><i class="icon la la-trash-o sm"></i></a>'))
                );
                //$table_str.='<td style="display:none">'.'<input type="hidden" min="1" name="clothes_ids[]" value="'.$rows->clothesCatID.'">'.'</td>';
                $('#itemID option').attr('selected', false);
                $('#amount').val('');
                $('#price').val('');
                var total = 0;

                //loop table to calculate the amount
                $("tr.item").each(function() {
                    var price = $(this).find("input.name").val(),
                        amount = $(this).find("input.id").val();
                    total += parseFloat(amount);
                });

                $('#ttlAmount').val(total);

            }
        });
        $(document).on('click', '.remove', function() {
            var total = 0;
            var trIndex = $(this).closest("tr").index();
            if(trIndex>1) {
                $(this).closest("tr").remove();

                $("tr.item").each(function() {
                    var price = $(this).find("input.name").val(),
                        amount = $(this).find("input.id").val();
                    total += parseInt(amount);
                });

                $('#ttlAmount').val(total);
            } else {
                alert("Sorry!! Can't remove first row!");
            }
        });
    });
</script>

