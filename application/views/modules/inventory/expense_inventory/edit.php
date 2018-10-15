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
                    <form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url('expense_inventory/update') ?>">
                        <input type="hidden" name="expID" id="expID" value="<?php echo $this->encrypter->encode($rec->expID) ?>" />
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
                                        $branchRec = $this->db->get()->row();
                                        ?>
                                        <input type="hidden" class="form-control w-80" name="branchID" id="branchID" title="branchID" value="<?php echo $branchRec ->branchID?>" >
                                        <input type="text" class="form-control w-80" name="branchName" id="branchName" title="branchName" value="<?php echo ucwords($branchRec ->branchName)?>" data-branchid="<?php echo $branchRec->branchID?>" readonly>
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
                        <div class="form-sepator solid mx-0"></div>
                        <div>
                            <!-- start create header details -->
                            <table class="table" id="tb">
                                <thead class="thead-light">
                                <tr class="tr-header">
                                    <th>Particulars</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                    <th>Option</th>
                                </thead>
                                <tbody>
                                <?php foreach($details as $detail) : ?>
                                    <tr class="item">
                                        <td class="id"><?php echo $detail->description?></>
                                        <td style="display:none">
                                            <input type="hidden"  name="item_ids[]" value="<?php echo $detail->particularID;?>" class="item_id" readonly>
                                        </td>
                                        <td>
                                            <input type="text"  name="prices[]" value="<?php echo $detail->qty; ?>" class="name form-control" >

                                        </td>
                                        <td class="id">
                                            <input type="text" class="id form-control" name="amounts[]" value="<?php echo $detail->amount?>" >
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
                                            <?php foreach($particulars as $expense_particulars_rec) { ?>
                                                <option value="<?php echo $expense_particulars_rec->particularID ?>"><?php echo $expense_particulars_rec->particular .' '.$expense_particulars_rec->description?></option>
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
                                    <td class="form-label w-10" nowrap>
                                        <label for="config">Remarks :  <span class="asterisk">*</span></label>
                                    </td>
                                    <td class="form-group form-input w-30" nowrap>
                                        <textarea class="form-control" name="remarks" id="remarks" title="Remarks" required><?php echo $rec->remarks;?></textarea>
                                    </td>
                                    <td class="form-label w-15" nowrap>

                                        <label for="config">Total Amount :  <span class="asterisk">*</span></label>
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

            localStorage.removeItem('particular_ids');
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
        var items = '<?php echo json_encode($details);?>';
        //console.log("items", items);
        var items = JSON.parse(items);
        var ids = [];
        $.each(items, function(k,v){
            //console.log("item id", v['itemID']);
            ids.push(v['particularID']);
        });
        var new_items = JSON.stringify(ids);
        localStorage.setItem("particular_ids", new_items);

        $('#addMore').on('click', function() {
            //loop table to check duplicate
            var particular = $('#itemID').val();
            var particular_text = $('#itemID option:selected').text();

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


            var amount = $('#amount').val();
            var price = $('#price').val();
            if(amount != "" && price != "" && particular != "") {
                //check item for duplicate
                $("tr.item").each(function() {
                    var item_id = $(this).find("input.item_id").val();
                    console.log("item id");
                    console.log($('#itemID').val());
                    console.log(item_id);
                });

                $('#tb').append($('<tr class="item">')
                    .append($('<td id="item[]">').text(particular_text))
                    .append($('<td style="display:none"><input type="hidden" id="item_ids[]" name="item_ids[]" value="'+particular+'"  readonly>'))
                    .append($('<td><input type="text"  name="prices[]" value="'+price+'" class="name form-control" >'))
                    .append($('<td><input type="text" name="amounts[]" value="'+amount+'" class="id form-control" >'))
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
            //var trIndex = $(this).closest("tr").index();
            //if(trIndex>1) {

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

                $("tr.item").each(function() {
                    var price = $(this).find("input.name").val(),
                        amount = $(this).find("input.id").val();
                    total += parseInt(amount);
                });

                $('#ttlAmount').val(total);
            // } else {
            //     alert("Sorry!! Can't remove first row!");
            // }
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

            //loop table to calculate the amount
            $("tr.item").each(function() {
                var price = $(this).find("input.name").val(),
                    amount = $(this).find("input.id").val();
                total += parseFloat(amount);
            });

            $('#ttlAmount').val(total);
        }

    });
</script>

