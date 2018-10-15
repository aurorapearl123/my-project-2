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
                    <form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url("expense_inventory/save") ?>">
                        <div class="table-row">
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
												$this->db->where('status',1);
												$this->db->where('branchID',$this->session->userdata('current_user')->branchID);
												$rec = $this->db->get()->row();
											?>
											<input type="hidden" class="form-control w-80" name="branchID" id="branchID" title="branchID" value="<?php echo $rec->branchID?>" >
											<input type="text" class="form-control w-80" name="branchName" id="branchName" title="branchName" value="<?php echo $rec->branchName?>"  readonly>
		                              	</td>
                                        <td class="form-label" style="width:13%" nowrap>Date : </td>
                                        <td class="form-group form-input" style="width:22%">
                                            <input type="text" class="form-control datepicker" id="date" name="date" data-toggle="datetimepicker" value="<?php echo date('Y-m-d')?>" readonly data-target="#date" title="Date" required>
                                        </td>
                                        <td class="d-xxl-none"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div>
                            <!-- start create header details -->
                            <table class="table mt-20" id="ei_details">
                                <thead class="thead-light">
                                    <tr class="tr-header">
                                        <th>Particulars</th>
                                        <th>Quantity</th>
                                        <th>Amount</th>
                                        <th>Option</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="form-group form-input w-25">                                        	
											<?php 
												$this->db->select('expense_particulars.*');
												$this->db->from('expense_particulars');
												$this->db->where('status',1);											
												$recs = $this->db->get()->result();
											?>
                                            <select class="form-control w-80" id="particularID" name="particularID" data-live-search="true" livesearchnormalize="true" title="Item" required>
                                                <option value="" selected>&nbsp;</option>
                                                <?php foreach($recs as $rec) { ?>
                                                    <option value="<?php echo $rec->particularID ?>"><?php echo $rec->particular .' '.$rec->description?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td class="form-group form-input w-25">
                                            <input type="number" class="form-control w-80" name="quantity" id="quantity" title="quantity" min="0" value="">
                                        </td>                                        
                                        <td class="form-group form-input w-25">
                                            <input type="number" class="form-control w-80" name="amount" id="amount" title="amount" min="0" value="">
                                        </td>
                                        <td class="form-group form-input">
                                            <button id="addMore" type="button" class="btn btn-primary btn-xs btn-raised pill">add</button>
                                        </td>
                                        <td class="d-xxl-none"></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <!-- end create header details -->
                        </div>

                        <div class="table-row mt-20">
                            <table class="table-form">
                                <tbody>
                                    <tr>
                                        <td class="form-label w-10" nowrap>
                                            <label for="config">Remarks :  <span class="asterisk">*</span></label>
                                        </td>
                                        <td class="form-group form-input w-30" nowrap>
                                            <textarea class="form-control" name="remarks" id="remarks" title="Remarks" required></textarea>
                                        </td>
                                        <td class="form-label w-10" nowrap>
                                        	<label for="ttlAmount">Total Amount :  </label>
                                        </td>
                                        <td >
                                        	<input type="number" class="form-control w-40" name="ttlAmount" id="ttlAmount" title="ttlAmount" min="0" value="" readonly>
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
            //$.post("<?php //echo $controller_page ?>///check_duplicate", { companyCode: $('#companyCode').val() },
            //  function(data){
            //    if (parseInt(data)) {
            //      $('#cmdSave').removeClass("loader");
            //      $('#cmdSave').removeAttr('disabled');
            //          // duplicate
            //          swal("Duplicate","Record is already exist!","warning");
            //    } else {
            //      // submit
                     $('#frmEntry').submit();
            //    }
            //  }, "text");

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
                  window.location = '<?php echo site_url('expense_inventory/show') ?>';
              }
            });
        
    });

    $(function(){
        localStorage.removeItem('particular_ids');
        console.log("local particular clear");
        $('#addMore').on('click', function() {
            var particular = $('#particularID').val();
            var particular_text = $('#particularID option:selected').text();
            var quantity = $('#quantity').val();
            var amount = $('#amount').val();
            var total = 0;

            if(quantity != "" && amount != "" && particular != "") {

                //loop table to check duplicate
                var ids = [];
                var service_ids = localStorage.getItem("particular_ids");
                console.log("the storage", service_ids);
                if(service_ids) {
                    //save local storage
                    var new_service_ids = localStorage.getItem("particular_ids");
                    var new_service_ids = JSON.parse(service_ids);
                    for(i = 0; i < new_service_ids.length; i++) {
                        if(new_service_ids[i] === particular) {
                            //alert("service already exist");
                            swal("Already Exist ",particular_text,"warning");
                            console.log("exits");
                            return false;
                        }
                        else {
                            var new_ids = localStorage.getItem("particular_ids");
                            var new_ids = JSON.parse(new_ids);
                            new_ids.push(particular);

                            var service_ids = JSON.stringify(new_ids);
                            //console.log("ADD SERVICE ID");
                            //console.log(ids);
                            localStorage.setItem("particular_ids", service_ids);
                        }
                    }

                }
                else {
                    ids.push(particular);
                    var service_ids = JSON.stringify(ids);
                    localStorage.setItem("particular_ids", service_ids);
                }
                $('#ei_details').append($('<tr class="expense_inventory">')
                    .append($('<td id="item[]">').text(particular_text))
                    .append($('<td style="display:none"><input type="hidden" name="particularIDs[]" value="'+particular+'" class="branch_id"  readonly>'))
                    .append($('<td><input type="text" name="quantities[]" value="'+quantity+'" class="form-control" >'))
                    .append($('<td><input type="text" name="amounts[]" value="'+amount+'" class="id form-control" >'))
                    .append($('<td><a href="javascript:void(0);" class="btn btn-outline-light bmd-btn-icon btn-xs remove"><span class="icon la la-trash-o sm"></span></a>'))
                );
                
                //$('#particularID option').attr('selected', false);
                $('#quantity').val('');
                $('#amount').val('');
                //$(this).find('option:selected').remove();

                //loop table to calculate the amount
                $("tr.expense_inventory").each(function() {
                    var price = $(this).find("input.name").val(),
                        amount = $(this).find("input.id").val();
                    total += parseFloat(amount);

                });

                $('#ttlAmount').val(total);

            }

        });
        $(document).on('click', '.remove', function() {
            var total = 0;
            // var trIndex = $(this).closest("tr").index();
            // if(trIndex>1) {
            //     $(this).closest("tr").remove();
            // } else {
            //     alert("Sorry!! Can't remove first row!");
            // }

            var service_id = $(this).closest("tr").find('input').val();
            //remove the ids from service ids
            var new_ids = localStorage.getItem("particular_ids");
            var new_ids = JSON.parse(new_ids);
            for(x = 0 ;x < new_ids.length; x ++){
                if(new_ids[x] == service_id) {
                    //console.log("remove me");
                    //console.log(new_ids[x]);
                    delete new_ids[x];

                    //new_ids.splice(0, service_id);
                    //return false;
                }
            }
            //console.log("the id");
            //console.log(new_ids);
            var the_ids = JSON.stringify(new_ids);
            localStorage.setItem("particular_ids", the_ids);
            $(this).closest("tr").remove();

            $("tr.expense_inventory").each(function() {
                var price = $(this).find("input.name").val(),
                    amount = $(this).find("input.id").val();
                console.log("this is a total amount : ", amount);
                total += parseInt(amount);
            });

            $('#ttlAmount').val(total);

        });

        $(document).on('keyup', '.id',  function(){

            var rowCount = $('#ei_details tr').length;
            var total_qty = 0;
            if(rowCount == 3) {
                //console.log("equal value");

                var amount = $(this).val();
                $('#ttlAmount').val(amount);
            }
            else {

                calculateAmount();
            }


        });

        function calculateAmount() {
            var total = 0;
            $("tr.expense_inventory").each(function() {
                var price = $(this).find("input.name").val(),
                    amount = $(this).find("input.id").val();
                console.log("this is a total amount : ", amount);
                total += parseInt(amount);
            });

            $('#ttlAmount').val(total);
        }



    });
</script>

</script>