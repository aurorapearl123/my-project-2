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
                            <table class="table-form">
                                <tbody>
                                <tr>
                                    <td class="form-label">
                                        <label for="config">Branch :<span class="asterisk">*</span></label>
                                    </td>
                                    <td class="form-group form-input">
                                        <input type="text" class="form-control" name="branchName" id="branchName" title="Branch Name" value="<?php echo $branchName;?>" readonly>
                                        <input type="hidden" class="form-control" name="branchID" id="branchID" title="Branch Id" value="<?php echo $branchID;?>" readonly>
                                        <input type="hidden" id="cloth_category_size" name="cloth_category_size" value="<?php echo count($clothes_categories)?>">
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
                                                <option value="<?php echo $row->custID ?>"><?php echo $row->fname .' '. $row->mname .' '.$row->lname ?></option>
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
                                <tr>
<!--                                    <td class="form-label">Service :</td>-->
<!--                                    <td class="form-group form-input select-service-type">-->
<!--                                        <select class="form-control" id="serviceID" name="serviceID" data-live-search="true" livesearchnormalize="true" title="Service" required>-->
<!--                                            <option value="" selected>&nbsp;</option>-->
<!--                                            --><?php //foreach($service_types as $row) { ?>
<!--                                                <option value="--><?php //echo $row->serviceID ?><!--">--><?php //echo $row->serviceType ?><!--</option>-->
<!--                                            --><?php //} ?>
<!--                                        </select>-->
<!---->
<!--                                    </td>-->
                                    <td>
                                        <button type="button" class="btn btn-primary" id="id-add-services">ADD SERVICES</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- end create header details -->
<!--                        <div id="services-container">-->
<!--                            <div class="row">-->
<!--                                <div class="col-md-8">.col-md-8</div>-->
<!--                                <div class="col-md-4">.col-md-4</div>-->
<!--                            </div>-->
<!--                        </div>-->

                        <div id="service-container">


                        </div>

                        <div class="table-row mt-30">
                            <table class="table-form">
                                <tbody>
                                <tr>
                                    <td class="form-label">
                                        <label for="config">Rate : <span class="asterisk">*</span></label>
                                    </td>
                                    <td class="form-group form-input">
                                        <input type="text" class="form-control" name="rate" id="rate" title="Branch Name" readonly required>
                                    </td>
                                    <td class="form-label" style="width:13%">Quantity in kilos : </td>
                                    <td class="form-group form-input" style="width:22%">
                                        <span id="span_error" style="color: red"></span>
                                        <input type="number" class="form-control quantity" name="qty" id="qty" min="0" title="Quantity in kilos" required>
                                    </td>
                                    <td class="d-xxl-none"></td>
                                </tr>
                                <tr>
                                    <td class="form-label" style="width:13%">Sub total:  <span class="asterisk">*</span></td>
                                    <td class="form-group form-input" style="width:22%">
                                        <input type="number" class="form-control" name="subtotal" id="subtotal" min="0" title="Company Abbr" readonly required>
                                    </td>
                                    <td class="form-label" style="width:13%">Deliver Fee:  <span class="asterisk">*</span></td>
                                    <td class="form-group form-input" style="width:22%">
                                        <input type="number" class="form-control" name="deliveryFee" id="deliveryFee" title="Delivery Fee" required>
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

        $("#id-add-services").on("click",function () {
            createTableContainer();
        });
    });

    function waitForCloseClick(serviceID) {
        console.log(serviceID);
        console.log("close select");
        $.post("<?php echo $controller_page ?>/getCategories", { service_id: serviceID },
           function(data, status){
               if(data != 0)  {

                   console.log("response");
                   //console.log(data);



                   createTableContainer();
                   data = $.parseJSON(data);
                   $.each(data, function(k,v){
                       console.log(v);
                   });



               }

           });
        return false;
    }

    function createTableContainer()
    {

        var value = 3;
        var id_container = 3;
        var text = "Sample";
        var remove_more_id = "test";
        var the_id = "test";
        var ul_id = "test"
        var qty = 34;
        var class_quantity = "test";
        var UNIT = "fds";
        var REGULAR_RATE = "23";
        var amount = 234;
        var table = $('<div data-service-id="'+value+'">').attr('class', "data-table data-table-init card").attr('id', id_container)
            .append($('<div>').attr('class', "card-header")
                .append($('<span>').text("Type : "+text.capitalize()).attr('name',value))
                .append($('<div>').append($('<i>').attr('class', "la la-trash-o")))
            )
            .append($('<div>').attr('class', "card-content")
                .append($('<table>').attr('id', 'order-table'+the_id)
                    .append($('<tr>').attr('id', 'tr-head'+the_id))
                    .append($('<tbody>'))
                )
            )
            //quantity
            .append($('<ul>').attr('id', ul_id)
                .append($('<li>')
                    .append($('<div>').attr('class','item-title label').text("Quantity")
                            .append($('<input>').attr('placeholder', "Please input Quantity").attr('type', 'number').attr('value', qty).attr('style', 'border:none').attr('class', class_quantity)
                        )
                    )
                )
                //unit
                .append($('<li>')
                    .append($('<div>').attr('class','item-title label').text("UNIT")
                        .append($('<input>').attr('placeholder', "Unit").attr('value', UNIT).attr('readonly', true).attr('style', 'border:none'))
                    )
                )
                //rate
                .append($('<li>')
                    .append($('<div>').attr('class','item-title label').text("RATE")
                        .append($('<input>').attr('placeholder', "Rate").attr('value', REGULAR_RATE).attr('readonly', true).attr('style', 'border:none'))
                    )
                )
                //amount
                .append($('<li>')
                    .append($('<div>').attr('class','item-title label').text("AMOUNT")
                        .append($('<input>').attr('placeholder', "Amount").attr('readonly', true).attr('style','border:none').attr('class', 'my-amount').attr('value', amount))
                    )

                )
            );


        var select_container = $('<select data-live-search="true" livesearchnormalize="true" title="Service">').attr('class', 'form-control');

        var selectValues = '<?php echo json_encode($services); ?>';
        //var test = JSON.parse(test);
        //console.log(test);

        var selectValues = $.parseJSON(selectValues);
        console.log("THE RESULT");
        console.log(selectValues);
        // var obj = selectValues;
        // Object.keys(obj).forEach(key => {
        //     console.log(obj[key]);
        // });
        $.each(selectValues, function(key, value) {
            // console.log("KEY");
            // console.log(key);
            // console.log('VALUE');
            //console.log(value);
            $.each(value, function(k, v){
                //console.log(v);
                console.log("KEY");
                console.log(k);
                console.log('VALUE');
                console.log(v);
                select_container
                        .append($("<option></option>")
                            .attr("value",k)
                            .text(v));

            });
        });

        //var selectValues = { "1": "test 1", "2": "test 2" };

        // $.each(selectValues, function(key, value) {
        //     // console.log("THE KEY");
        //     // console.log(key);
        //     // console.log("THE VALUE");
        //     //console.log(value);
        //     select_container
        //         .append($("<option></option>")
        //             .attr("value",key)
        //             .text(value));
        // });

        var container = $('<div>').attr('class', 'services-container')
                .append($('<div>').attr('class', 'row')
                .append($('<div>').attr('class', 'col-md-8').append(select_container))
                .append($('<div>').attr('class', 'col-md-4').append(table).append($('<br>'))
            )
        );
        $('#service-container').append(container);
    }

    String.prototype.capitalize = function() {
        return this.charAt(0).toUpperCase() + this.slice(1);
    }


    //    $("#deliveryFee").prop('disabled', true);
    //
    //    $(".select-service-type").change(function(){
    //        //$("#span_error").html("");
    //        var service_id = $('#serviceID').val();
    //        if(service_id.length != 0) {
    //            $("#qty").prop('disabled', false);
    //            //
    //        }
    //
    //    });
    //
    //    $('#isDiscounted').change(function() {
    //        //$('#textbox1').val($(this).is(':checked'));
    //        //alert("hello");
    //        if($(this).is(':checked')) {
    //            $('#rate').val('');
    //            var service_id = $('#serviceID').val();
    //            check_service_discount(1, service_id);
    //        }
    //        else {
    //            $('#rate').val('');
    //            $('isDiscounted').attr('value','Y');
    //            var service_id = $('#serviceID').val();
    //            check_service_discount(0, service_id);
    //        }
    //    });
    //
    //    $('#serviceID').on('change', function(){
    //        $('#rate').val('');
    //        var isDiscounted = 0;
    //        if ($("#isDiscounted").is(":checked")) {
    //            isDiscounted = 1;
    //            $('isDiscounted').attr('value','Y');
    //        }
    //        else {
    //            $('isDiscounted').attr('value','N');
    //        }
    //
    //        var service_id = $('#serviceID').val();
    //        check_service_discount(isDiscounted, service_id);
    //
    //    });
    //    //event for quantity in kilos
    //
    //    /* var service_id = $('#serviceID').val();
    //     if(service_id.length == 0) {
    //         $("#qty").prop('disabled', true);
    //         $("#deliveryFee").prop('disabled', true);
    //         console.log("disable me");
    //     }*/
    //    //check service
    //    checkService();
    //    $('#qty').keyup(function(){
    //        //alert('Hello');
    //        $("#deliveryFee").prop('disabled', false);
    //        var qty = $("#qty").val();
    //        //console.log(qty);
    //        // if(isNaN(qty)) {
    //        //     $("#deliveryFee").prop('disabled', true);
    //        //
    //        // }
    //
    //
    //        if($('#rate').val() == "") {
    //            //alert("please input rate");
    //            // message = "Please select service type.";
    //
    //        }
    //        else {
    //            var rate = parseInt($('#rate').val());
    //            var quantity_kilo = parseInt($('#qty').val());
    //            var total = quantity_kilo * rate;
    //
    //            $('#subtotal').val(total);
    //        }
    //
    //        calculate_data();
    //    });
    //    //$("#span_error").append(message);
    //
    //    $('#deliveryFee').keyup(function(){
    //        //alert('Hello');
    //        if($('#subtotal').val() == "") {
    //            alert("please input subtotal");
    //        }
    //        else {
    //            var subtotal = parseInt($('#subtotal').val());
    //            var deliveryFee = parseInt($('#deliveryFee').val());
    //            var total = subtotal + deliveryFee;
    //
    //            $('#ttlAmount').val(total);
    //        }
    //    });
    //
    //
    //});
    //
    //function check_service_discount(isDescounted,service_id)
    //{
    //    if ($("#isDiscounted").is(":checked")) {
    //        isDiscounted = 1;
    //    }
    //    $.post("<?php //echo $controller_page ?>///check_service_discount", { service_id: service_id, is_discounted: isDescounted },
    //        function(data, status){
    //            if(data != 0)  {
    //                $('#rate').val(data);
    //
    //                var rate = parseInt($('#rate').val());
    //                var quantity_kilo = parseInt($('#qty').val());
    //                var total = quantity_kilo * rate;
    //                $('#subtotal').val(total);
    //                //initial total amount
    //                var subtotal = parseInt($('#subtotal').val());
    //                var deliveryFee = parseInt($('#deliveryFee').val());
    //                var totalAmount = subtotal + deliveryFee;
    //
    //                $('#ttlAmount').val(totalAmount);
    //
    //            }
    //
    //        });
    //}
    //
    //function checkService()
    //{
    //    var service_id = $('#serviceID').val();
    //    if(service_id.length == 0) {
    //        $("#qty").prop('disabled', true);
    //        $("#deliveryFee").prop('disabled', true);
    //        console.log("disable me");
    //    }
    //}
    //
    //
    //function calculate_data()
    //{
    //    var total = $('#ttlAmount').val();
    //    var sub_total = $('#subtotal').val();
    //    var deliveryFee = $('#deliveryFee').val();
    //
    //    var amount = +sub_total + +deliveryFee;
    //    console.log("delivery fee", deliveryFee);
    //    console.log("sub total",sub_total);
    //    console.log("total",total);
    //    console.log("amount", amount);
    //    $('#ttlAmount').val(amount);
    //}

</script>