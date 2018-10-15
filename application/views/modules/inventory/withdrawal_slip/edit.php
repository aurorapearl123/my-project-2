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
                    <form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url('withdrawal_slip/update') ?>">
                        <input type="hidden" name="wsID" id="wsID" value="<?php echo $this->encrypter->encode($rec->wsID) ?>" />
                        <div class="table-row">
                            <table class="table-form column-3">
                                <tbody>
                                    <tr>
                                        <td class="form-label" style="width:13%" nowrap>
                                            <label for="config">Branch : <span class="asterisk">*</span></label>
                                        </td>
                                        <td class="form-group form-input" style="width:22%">
                                             <input type="text" class="form-control" name="branchName" id="branchName" title="Branch Name" value="<?php echo $branchName;?>" readonly>
                                            <input type="hidden" class="form-control" name="branchID" id="branchID" title="Branch Id" value="<?php echo $branchID;?>" readonly>
                                            <input type="hidden" id="cloth_category_size" name="cloth_category_size" value="<?php echo count($clothes_categories)?>">
                                        </td>
                                        <td class="form-label" style="width:13%" nowrap>Date : </td>
                                        <td class="form-group form-input" style="width:22%">
                                            <input type="text" class="form-control" name="date" id="date" title="Date" required value="<?php echo $rec->date?>" readonly>
                                        </td>
                                        <td class="d-xxl-none"></td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                        </div>
                        <div>
                            <!-- start create header details -->
                            <table class="table mt-20" id="tb">
                                <thead class="thead-light">
                                    <tr class="tr-header">
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>Option</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($items as $item) : ?>
                                        <tr class="item">
                                            <td class="id"><?php echo $item->item?></>
                                            <td style="display:none">
                                                <input type="hidden"  name="item_ids[]" value="<?php echo $item->itemID;?>" class="item_id" readonly>
                                            </td>
                                            <td>
                                                <input type="text"  name="quantities[]" value="<?php echo $item->qty; ?>" class="name border-0" readonly>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-outline-light bmd-btn-icon btn-xs remove"><i class="icon la la-trash-o sm"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach;?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="form-group form-input w-25">
                                            <select class="form-control w-80" id="itemID" name="itemID" data-live-search="true" livesearchnormalize="true" title="Item" required>
                                                <option value="" selected>&nbsp;</option>
                                                <?php foreach($items_select as $row) { ?>
                                                    <option value="<?php echo $row->itemID ?>"><?php echo $row->brand?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td class="form-group form-input w-25">
                                            <input type="number" class="form-control w-80" name="quantity" id="quantity" title="quantity" min="0" value="<?php echo $quantity?>">
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
                                        <td class="form-group form-input w-40" nowrap>
                                            <textarea class="form-control" name="remarks" id="remarks" title="Remarks" required><?php echo $rec->remarks?></textarea>
                                        </td>
                                        </td>
                                        <td></td>
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
                  window.location = '<?php echo site_url('withdrawal_slip/show') ?>';
              }
            });
        
    });
    $(function(){
        //console.log("add local storage");
        var items = '<?php echo json_encode($items);?>';
        //console.log("items", items);
        var items = JSON.parse(items);
        var ids = [];
        $.each(items, function(k,v){
            //console.log("item id", v['itemID']);
            ids.push(v['itemID']);
        });
        var new_items = JSON.stringify(ids);
        localStorage.setItem("WITHDRAWAL-ITEM-ID", new_items);


        $('#addMore').on('click', function() {
            var item = $('#itemID').val();
            var item_text = $('#itemID option:selected').text();
            var quantity = $('#quantity').val();
            var price = $('#price').val();
            if(quantity != "") {

                var item_ids = localStorage.getItem("WITHDRAWAL-ITEM-ID");
                var ids = [];
                if(item_ids) {
                    var ids_new = localStorage.getItem("WITHDRAWAL-ITEM-ID");
                    var ids_new = JSON.parse(ids_new);
                    for(var i = 0; i < ids_new.length; i++) {
                        if(ids_new[i] ===  item) {
                            swal("Already Exist ",item_text,"warning");
                            return false;
                        }
                        else {
                            var new_ids = localStorage.getItem("WITHDRAWAL-ITEM-ID");
                            var new_ids = JSON.parse(new_ids);
                            new_ids.push(item);
                            var store_ids = JSON.stringify(new_ids);
                            localStorage.setItem("WITHDRAWAL-ITEM-ID", store_ids);
                        }
                    }
                }
                else {
                    ids.push(item);
                    var item_id = JSON.stringify(ids);
                    localStorage.setItem("WITHDRAWAL-ITEM-ID", item_id);
                }

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
                    .append($('<td><input type="text" name="quantities[]" value="'+quantity+'" class="id border-0" readonly>'))
                    .append($('<td><a href="javascript:void(0);" class="btn btn-outline-light bmd-btn-icon btn-xs remove"><span class="icon la la-trash-o sm"></span></a>'))
                );
                //$table_str.='<td style="display:none">'.'<input type="hidden" min="1" name="clothes_ids[]" value="'.$rows->clothesCatID.'">'.'</td>';
                $('#itemID option').attr('selected', false);
                $('#quantity').val('');
                $('#price').val('');
                var total = 0;

                //loop table to calculate the amount
                $("tr.item").each(function() {
                    var price = $(this).find("input.name").val(),
                        quantity = $(this).find("input.id").val();
                    total += parseFloat(quantity);
                });

                $('#ttlAmount').val(total);

            }




        });
        $(document).on('click', '.remove', function() {
            console.log("remove");
            var total = 0;
            var trIndex = $(this).closest("tr").index();

            //remove local storage
            var new_ids = localStorage.getItem("WITHDRAWAL-ITEM-ID");
            var item_id = $(this).closest("tr").find('input').val();
            var new_ids = JSON.parse(new_ids);
            for(var x = 0; x < new_ids.length; x++) {
                if(new_ids[x] === item_id) {
                    delete new_ids[x];
                }
            }

            var ids = JSON.stringify(new_ids);
            localStorage.setItem("WITHDRAWAL-ITEM-ID", ids);

            //if(trIndex>1) {
                $(this).closest("tr").remove();

                $("tr.item").each(function() {
                    var price = $(this).find("input.name").val(),
                        amount = $(this).find("input.id").val();
                    total += parseInt(amount);
                });

                $('#ttlAmount').val(total);
           /* } else {
                alert("Sorry!! Can't remove first row!");
            }*/


        });
    });
</script>

