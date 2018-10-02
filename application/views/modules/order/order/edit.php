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
                    <form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url('order/update') ?>">
                        <input type="hidden" name="orderID" id="orderID" value="<?php echo $this->encrypter->encode($rec[0]['orderID']) ?>" />
                        <div class="table-row">
                            <table class="table-form">
                                <tbody>
                                <tr>
                                    <td class="form-label">
                                        <label for="config">Branch :<span class="asterisk">*</span></label>
                                    </td>
                                    <td class="form-group form-input">
                                        <input type="text" class="form-control" name="branchName" id="branchName" title="Branch Name" value="<?php echo $rec[0]['branch_name'];?>" readonly>
                                        <input type="hidden" class="form-control" name="branchID" id="branchID" title="Branch Id" value="<?php echo $rec[0]['branch_id'];?>" readonly>

                                    </td>
                                    <td class="form-label" style="width:13%">Date :</td>
                                    <td class="form-group form-input" style="width:22%">
                                        <input type="text" class="form-control" name="date" id="date" title="Date" required value="<?php echo date('Y-m-d')?>" readonly>
                                    </td>
                                    <td class="d-xxl-none"></td>
                                </tr>
                                <tr>
                                    <td class="form-label" style="width:13%" nowrap>Customer Name : <span class="asterisk">*</span></td>
                                    <td class="form-group form-input" style="width:22%" nowrap>
                                        <select class="form-control" id="custID" name="custID" data-live-search="true" livesearchnormalize="true" title="Customer Name" required>
                                            <option value="" selected>&nbsp;</option>
                                            <?php foreach($customers as $row) { ?>
                                                <option value="<?php echo $row->custID ?>"><?php if($row->custID == $rec[0]['custID']){echo "selected";}?> <?php echo $row->fname .' '. $row->mname .' '.$row->lname ?></option>

                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td class="form-label" style="width:13%">is discounted <span class="asterisk">*</span></td>
                                    <td class="form-input" style="width:22%">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="isDiscounted" id="isDiscounted" value="Y"> &nbsp;
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- end create header details -->
                        <br>
                        <div>
                            <div class="row">
                                <div class="col-md-8" id="services-container">
                                    <!-- start create header details -->
                                    <table class="table" id="tb">
                                        <thead class="thead-light">
                                        <tr class="tr-header">
                                            <th>Item</th>
                                            <th>QTY</th>
                                            <th>UNIT</th>
                                            <th>RATE</th>
                                            <th>Amount</th>
                                            <th>Option</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($rec[0]['order_details'] as $detail) : ?>
                                            <tr class="item">
                                                <td class="id"><?php echo $detail['serviceType']?></>
                                                <td style="display:none">
                                                    <input type="hidden"  name="item_ids[]" value="<?php echo $detail['serviceID'];?>" class="item_id" readonly>
                                                </td>
                                                <td class="form-group form-input pl-5" width="15%">
                                                    <input type="number" class="name form-control w-80" name="qty[]" title="QTY" min="0"  value="<?php echo $detail['qty']?>">
                                                </td>
                                                <td class="form-group form-input pl-5" width="15%">
                                                    <input type="text" class="unit form-control w-80" name="unit[]" title="UNIT" min="0"  readonly value="<?php echo $detail['unit']?>">
                                                </td>
                                                <td class="form-group form-input pl-5" width="15%">
                                                    <input type="number" class="rate form-control w-80" name="rate[]"  title="RATE" min="0"  readonly value="<?php echo $detail['rate']?>">
                                                </td>
                                                <td class="form-group form-input pl-5" width="15%">
                                                    <input type="number" class="id form-control w-80" name="amount[]"  title="Amount" min="0"  readonly value="<?php echo $detail['amount']?>">
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0);" class="btn btn-outline-light bmd-btn-icon btn-xs remove" title="Delete"><i class="icon la la-trash-o sm"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach;?>
                                        </tbody>
                                        <tfoot>
                                        <tr class="item">
                                            <td class="form-group form-input pl-5" width="15%">
                                                <select class="form-control" id="serviceID" name="serviceID" data-live-search="true" livesearchnormalize="true" title="Service">
                                                    <option value="" selected>&nbsp;</option>
                                                    <?php foreach($service_types as $row) { ?>
                                                        <option value="<?php echo $row->serviceID ?>"><?php echo $row->serviceType ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td class="form-group form-input pl-5" width="15%">
                                                <input type="number" class="name form-control w-80" name="qty[]" id="qty" title="QTY" min="0"  value="<?php echo $qty?>">
                                            </td>
                                            <td class="form-group form-input pl-5" width="15%">
                                                <input type="text" class="unit form-control w-80" name="unit[]" id="unit" title="UNIT" min="0"  readonly value="<?php echo $unit?>">
                                            </td>
                                            <td class="form-group form-input pl-5" width="15%">
                                                <input type="number" class="rate form-control w-80" name="rate[]" id="rate" title="RATE" min="0"  readonly value="<?php echo $rate?>">
                                            </td>
                                            <td class="form-group form-input pl-5" width="15%">
                                                <input type="number" class="id form-control w-80" name="amount[]" id="amount" title="Amount" min="0"  readonly value="<?php echo $amount?>">
                                            </td>
                                            <td class="form-group form-input pl-5" width="10%"">
                                            <button id="addMore" type="button" class="btn btn-primary btn-xs pill btn-raised">add</button>
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                    <!-- end create header details -->
                                </div>
                                <div class="col-md-4">
                                    <!-- start create header details -->
                                    <?php
                                    $table_str="<table border='0' class='table table-sm'>";
                                    $table_str.='<tr>';
                                    $table_str.='<th>'.'Quantity'.'</th>';
                                    $table_str.='<th>'.'Category'.'</th>';
                                    $table_str.='<tr>';
                                    $i = 1;
                                    foreach ($clothes_categories as $rows) {
                                        $table_str.='<tr class="categories">';
                                        //$table_str.='<td>'.'<input type="number" min="1" id="id_'.($i++).'" name="id_'.($i++).'">'.'</td>';
                                        $table_str.='<td style="width: 100px" align="left">'.'<input type="text" min="1" name="clothes_qtys[]" value="'.$rows['qty'].'" onkeypress="return isNumber(event)" class="form-control category-quantity">'.'</td>';
                                        $table_str.='<td style="display:none">'.'<input type="hidden" min="1" name="clothes_ids[]" value="'.$rows['clothesCatID'].'" class="category-id">'.'</td>';
                                        $table_str.='<td style="display:none">'.'<input type="hidden" min="1"  value="'.$rows['serviceID'].'" class="service-id">'.'</td>';
                                        $table_str.='<td>'.$rows['category'].'</td>';
                                        $table_str.='</tr>';
                                    }
                                    $table_str.="</table>";

                                    echo $table_str;
                                    ?>
                                    <!-- end create header details -->
                                </div>
                            </div>
                        </div>



                        <div class="table-row mt-30">
                            <table class="table-form">
                                <tbody>

                                <tr>
                                    <td class="form-label" style="width:13%">Amount:  <span class="asterisk">*</span></td>
                                    <td class="form-group form-input" style="width:22%">
                                        <input type="number" class="form-control" name="ttlAmount" id="ttlAmount" title="Total Amount" value="<?php echo $rec[0]['ttlAmount']?>" readonly required>
                                    </td>
                                    <td class="form-label" style="width:13%">Deliver Fee: </td>
                                    <td class="form-group form-input" style="width:22%">
                                        <input type="number" class="form-control" name="deliveryFee" id="deliveryFee" value="<?php echo $rec[0]['deliveryFee']?>" title="Delivery Fee">
                                    </td>
                                    <td class="d-xxl-none"></td>
                                </tr>
                                <tr>
                                    <td class="form-label" style="width:13%">Remarks:  </td>
                                    <td class="form-group form-input" >
                                        <textarea id="remarks" name="remarks" style="width:300%"><?php echo $rec[0]['remarks']?></textarea>
                                        <input type="hidden" id="services_data" name="services_data">
                                        <input type="hidden" id="categories_data" name="categories_data">
                                    </td>
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
</div>
<script>
    $('#cmdSave').click(function(){
        if (check_fields()) {
            $('#cmdSave').attr('disabled','disabled');
            $('#cmdSave').addClass('loader');

            var amounts = [];
            $("tr.item").each(function() {
                var amount = $(this).find("input.id").val();
                var unit = $(this).find("input.unit").val();
                var rate = $(this).find("input.rate").val();
                var qty = $(this).find("input.name").val();
                var service_id = $(this).find("input.item_id").val();
                //$("tr td:first-child").val();
                // console.log("first child", rate);

                // service_id : data_bind,
                /* amount: amount,
                 unit : unit,
                 rate : rate,
                 quantity: quantity,*/

                if(amount != "") {
                    amounts.push({
                        amount: amount,
                        rate: rate,
                        unit: unit,
                        quantity: qty,
                        service_id : service_id
                    });
                }

                // total_amount += +amount;
            });
            var amounts = JSON.stringify(amounts);

            var categories = [];
            $("tr.categories").each(function(){
                var category_quantity = $(this).find("input.category-quantity").val();
                var category_id = $(this).find("input.category-id").val();
                var service_id = $(this).find("input.service-id").val();
                console.log("the service id");
                console.log(service_id);

                /* category_data.push({
                     service_id : data_bind_table,
                     category_quantity: category_quantity,
                     category_id : category_id
                 });*/

                if(category_quantity != "") {
                    categories.push({
                        service_id: service_id,
                        category_quantity: category_quantity,
                        category_id: category_id
                    });
                }
                //console.log("category quantity ", category_quantity);
            });

            var categories_data = JSON.stringify(categories);
            $('#services_data').val(amounts);
            $('#categories_data').val(categories_data);
            console.log("service data");
            console.log(amounts);
            console.log("categories");
            console.log(categories);

            localStorage.clear();
            console.log("local storage clear");

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
        console.log("save services ids");
        //set initial state.
        //save local storage service id
        var the_service_ids = '<?php echo json_encode($rec[0]['order_details']);?>';
        //console.log("the array");
        //console.log(JSON.parse(the_service_ids));
        var service_ids = JSON.parse(the_service_ids);
        console.log("the array");
        console.log(service_ids);
        var ids = [];
        $.each(service_ids, function(k,v){
            ids.push(v['serviceID']);
        });

        // ids.push(item);
        var service_ids = JSON.stringify(ids);
        localStorage.setItem("service_ids", service_ids);

        $('#addMore').on('click', function() {
            var item = $('#serviceID').val();
            var item_text = $('#serviceID option:selected').text();
            var amount = $('#amount').val();
            var qty = $('#qty').val();
            var rate = $('#rate').val();
            var unit = $('#unit').val();
            if(amount != "" && qty != "") {

                //console.log("THE AMOUNT", qty);

                var ids = [];
                var service_ids = localStorage.getItem("service_ids");
                console.log("the storage", service_ids);
                if(service_ids) {
                    //save local storage
                    var new_service_ids = localStorage.getItem("service_ids");
                    var new_service_ids = JSON.parse(service_ids);
                    for(i = 0; i < new_service_ids.length; i++) {
                        if(new_service_ids[i] === item) {
                            //alert("service already exist");
                            swal("Already Exist ",item_text,"warning");
                            console.log("exits");
                            return false;
                        }
                        else {
                            var new_ids = localStorage.getItem("service_ids");
                            var new_ids = JSON.parse(new_ids);
                            new_ids.push(item);

                            var service_ids = JSON.stringify(new_ids);
                            //console.log("ADD SERVICE ID");
                            //console.log(ids);
                            localStorage.setItem("service_ids", service_ids);
                        }
                    }

                }
                else {
                    ids.push(item);
                    var service_ids = JSON.stringify(ids);
                    localStorage.setItem("service_ids", service_ids);
                }


                //loop table to check duplicate

                $('#tb').append($('<tr class="item">')
                    .append($('<td id="item[]">').text(item_text))
                    .append($('<td style="display:none"><input type="hidden" name="service_ids[]" value="'+item+'" class="item_id"  readonly>'))
                    .append($('<td><input type="number" name="qtys[]" value="'+qty+'" class="name form-control new-quantity" >'))
                    .append($('<td><input type="text"  name="unit[]" value="'+unit+'" class="unit form-control" readonly>'))
                    .append($('<td><input type="number"  name="rate[]" value="'+rate+'" class="rate form-control" readonly>'))
                    .append($('<td><input type="number" name="amounts[]" value="'+amount+'" class="id form-control" readonly >'))
                    .append($('<td><a href="javascript:void(0);" class="btn btn-outline-light bmd-btn-icon btn-xs remove"><i class="icon la la-trash-o sm"></i></a>'))
                );
                //$table_str.='<td style="display:none">'.'<input type="hidden" min="1" name="clothes_ids[]" value="'.$rows->clothesCatID.'">'.'</td>';
                $('#itemID option').attr('selected', false);


               // calculateAmountForTotal();
                //clear all field
                $('#amount').val('');
                //var qty = $('.new-quantity').val('');
                $('#rate').val('');
                $('#unit').val('');
                $(this).closest('td').prev().prev().prev().prev().find('input').val('');
            }
        });

    });

    $(document).on('change', '#serviceID', function(){
        //var optionSelected = $("option:selected", this);
        var valueSelected = this.value;

        var services = '<?php echo json_encode($service_types); ?>';
        var services = JSON.parse(services);

        //console.log(services);
        //console.log(valueSelected);
        for( x in services) {
            //console.log(services[x]);
            //console.log(services[x]['serviceID']);
            if(services[x]['serviceID'] == valueSelected) {
                // console.log(services[x]['serviceID']);
                // console.log(services[x]['serviceType']);
                // console.log(services[x]['regRate']);
                // console.log(services[x]['discountedRate']);
                // console.log(services[x]['unit']);
                $(this).closest('td').next('td').next().find('input').val(services[x]['unit']);
                $(this).closest('td').next('td').next().next().find('input').val(services[x]['regRate']);

                var value = $(this).closest('td').next('td').find('input').val();
                calculateAmountSelect(this, value);
                //calculateAmountForTotal();
            }
        }


    });

    function calculateAmountSelect(_this, value)
    {
        if(value !== "") {
            var rate = $(_this).closest('td').next('td').next('td').next('td').find('input').val();
            var product = rate * value;
            $(_this).closest('td').next('td').next('td').next('td').next('td').find('input').val(product);
        }

    }

    $(document).on('keyup', '#qty', function(){
        var value = $(this).val();
        calculateAmount(this, value);
        calculateAmountForTotal();
    });

    function calculateAmountForTotal()
    {
        //console.log("calculate total");
        var total_amount = 0;
        $("tr.item").each(function() {
            var amount = $(this).find("input.id").val();
            console.log("the amount to sum", amount);
            total_amount += +amount;
        });

        //console.log("the amount", total_amount);
        $('#ttlAmount').val(total_amount);

    }

    function calculateAmount(_this, value)
    {
        var rate = $(_this).closest('td').next('td').next('td').find('input').val();
        var product = rate * value;
        $(_this).closest('td').next('td').next('td').next('td').find('input').val(product);
    }



    $(document).on('click', '.remove', function() {
        var service_id = $(this).closest("tr").find('input').val();
        //console.log("the service id");
        //console.log(service_id);

        //remove the ids from service ids
        var new_ids = localStorage.getItem("service_ids");
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
        localStorage.setItem("service_ids", the_ids);
        //new_ids.push(item);

        var totalAmt = 0;
        $(this).closest("tr").remove();
        $("tr.item").each(function() {
            var amount = $(this).find("input.id").val();
            //console.log("the value of amount");
            //console.log(amount);
            if(amount != "") {
                totalAmt += parseInt(amount);
            }

        });
        $('#ttlAmount').val(totalAmt);

    });



</script