    <div class="subheader">
    <div class="d-flex align-items-center">
        <div class="title mr-auto">
            <h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></h3>
        </div>
        <div class="subheader-tools">
            <a href="<?php echo site_url('order/show') ?>" class="btn btn-primary btn-raised btn-sm pill"><i class="icon left ti-angle-left md"></i> Back to List</a>
        </div>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-lg-8 col-xxl-9">
            <div class="card-box">
                <div class="card-head">
                    <div class="head-caption">
                        <div class="head-title">
                            <h4 class="head-text">View <?php echo $current_module['module_label'] ?>

                                <?php if ($rec[0]['status'] == 1): ?>
                                    <span id="span_wash">
                                           <button type="button" class="btn btn-primary btn-xs pill ml-15" id="wash">Wash</button>
                                          <button type="button" class="btn btn-outline-danger btn-xs pill" id="cancel">Cancel</button>
                                      </span>

                                <?php elseif ($rec[0]['status'] == 2): ?>
                                    <span id="span_ready">
                                      <button type="button" class="btn btn-primary btn-xs pill ml-15" id="fold">Fold</button>
                                      <button type="button" class="btn btn-outline-danger btn-xs pill" id="cancel">Cancel</button>
                                  </span>
                                <?php elseif ($rec[0]['status'] == 3): ?>
                                    <span id="span_ready">
                                      <button type="button" class="btn btn-primary btn-xs pill ml-15" id="ready">Ready</button>
                                      <button type="button" class="btn btn-outline-danger btn-xs pill" id="cancel">Cancel</button>
                                  </span>

                                <?php else: ?>

                                    <a href="<?php echo site_url('order/edit/'.$this->encrypter->encode($rec[0]['orderID'])) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>

                                <?php endif; ?>


                                <span id="result-date"></span>
                            </h4>
                        </div>
                    </div>
                    <div class="card-head-tools">
                        <ul class="tools-list">
                            <?php if ($roles['edit']) { ?>
                                <li>

                                    <?php if ($rec[0]['status'] == 6): ?>


                                    <?php elseif ($rec[0]['status'] == 5): ?>


                                    <?php elseif ($rec[0]['status'] == 4): ?>


                                    <?php else: ?>

                                        <a href="<?php echo site_url('order/edit/'.$this->encrypter->encode($rec[0]['orderID'])) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>

                                    <?php endif; ?>



                                </li>
                            <?php } ?>
                            <?php if ($roles['delete'] && !$in_used) { ?>
                                <li>
                                    <button name="cmddelete" id="cmddelete" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" onclick="deleteRecord('<?php echo $this->encrypter->encode($rec[0]['orderID']); ?>');"><i class="la la-trash-o"></i></button>
                                </li>
                            <?php } ?>
                            <?php if ($this->session->userdata('current_user')->isAdmin) { ?>
                                <li>
                                    <button type="button" id="recordlog" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Record Logs" onclick="popUp('<?php echo site_url('logs/record_log/order_headers/orderID/'.$this->encrypter->encode($rec[0]['orderID']).'/Order') ?>', 1000, 500)"><i class="la la-server"></i></button>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url("order/save") ?>">
                        <div class="data-view">
                            <table class="view-table">
                                <tbody>
                                <tr>
                                    <td class="data-title w-10">Branch :</td>
                                    <td class="data-input w-20"><?php echo $rec[0]['branch_name'];?></td>
                                    <td class="data-title w-10">Date :</td>
                                    <td class="data-input w-20"><?php echo date('F d, Y')?></td>
                                </tr>
                                <tr>
                                    <td class="data-title">Customer Name :</td>
                                    <td class="data-input"><?php echo $rec[0]['fname'] .' '.$rec[0]['mname']. ' '.$rec[0]['lname'];?></td>
                                    <td class="data-title">is discounted</td>
                                    <td class="data-input border-0">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="isDiscounted" id="isDiscounted" value="<?php if($rec->isDiscounted == 'Y'){
                                                    echo "Y";
                                                }
                                                else {
                                                    echo "N";
                                                };?>" aria-label="..." <?php if($rec->isDiscounted == 'Y') echo "checked='checked'";?> onclick="return false;"> &nbsp;
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
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
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($rec[0]['order_details'] as $detail) : ?>
                                            <tr class="item">
                                                <td><?php echo $detail['serviceType']?></td>
                                                <td><?php echo $detail['qty']?></td>
                                                <td><?php echo $detail['unit']?></td>
                                                <td><?php echo $detail['rate']?></td>
                                                <td><?php echo $detail['amount']?></td>

                                            </tr>
                                        <?php endforeach;?>
                                        </tbody>
                                        <tfoot>

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
                                    foreach ($rec[0]['order_details'] as $rows) {
                                        foreach($rows['categories'] as $row) {
                                            $table_str.='<tr class="categories">';
                                            //$table_str.='<td>'.'<input type="number" min="1" id="id_'.($i++).'" name="id_'.($i++).'">'.'</td>';
                                            $table_str.='<td style="width: 100px" align="left">'.'<input type="text" min="1" name="clothes_qtys[]" onkeypress="return isNumber(event)" value="'.$row->qty.'" class="form-control category-quantity" readonly>'.'</td>';
                                            $table_str.='<td style="display:none">'.'<input type="hidden" min="1" name="clothes_ids[]" value="'.$row->clothesCatID.'" class="category-id">'.'</td>';
                                            $table_str.='<td>'.$row->category.'</td>';
                                            $table_str.='</tr>';
                                        }

                                    }
                                    $table_str.="</table>";

                                    echo $table_str;
                                    ?>
                                    <!-- end create header details -->
                                </div>
                            </div>
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
                    <form method="post" name="frmEntry" id="frmEntry2" action="<?php echo site_url("order/save") ?>">
                        <div class="data-view">
                            <table class="view-table">
                                <tbody>
                                <tr>
                                    <td class="data-title">Order #:</td>
                                    <td class="py-5"><input type="text" class="form-control" name="deliveryFee" id="deliveryFee"  value="<?php echo $rec[0]['osNo'];?>" readonly></td>
                                </tr>
                                <tr>
                                    <td class="data-title">Deliver Fee:</td>
                                    <td class="py-5"><input type="text" class="form-control" name="deliveryFee" id="deliveryFee"  value="<?php echo $rec[0]['deliveryFee'];?>" readonly></td>
                                </tr>
                                <tr>
                                    <td class="data-title">Amount :</td>
                                    <td class="py-5"><input type="number" class="form-control" name="ttlAmount" id="ttlAmount" value="<?php echo $rec[0]['ttlAmount'];?>"  readonly></td>
                                </tr>
                                <tr>
                                    <td class="data-title">Remarks :</td>
                                    <td class="py-5"><input type="text" class="form-control"  value="<?php echo $rec[0]['remarks'];?>"  readonly></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-sepator solid mx-0"></div>
                        <div class="data-view">
                            <table class="view-table">
                                <tbody id="tbody-display-date">


                                <tr>
                                    <td>Prepared by : &nbsp;&nbsp;<span><?php echo $rec[0]['user_first_name'] .' '.$rec[0]['user_middle_name'].' '.$rec[0]['user_last_name'];?></span></td>
                                </tr>

                                <?php if ($rec[0]['status'] == 2): ?>
                                    <span id="span_wash_button">
                                           <tr>
                                            <td>Date washed : &nbsp;&nbsp;<span><?php echo date('M d, Y H:i:s',strtotime($rec[0]['dateWashed'])) ;?></span></td>
                                           </tr>
                                      </span>
                                <?php elseif ($rec[0]['status'] == 3): ?>
                                    <span id="span_ready_button">
                                      <tr>
                                        <td>Date Fold : &nbsp;&nbsp;<span><?php echo $rec[0]['dateFold'];?></span></td>
                                    </tr>
                                  </span>
                                <?php elseif ($rec[0]['status'] == 4): ?>
                                    <span id="span_ready_button">
                                       <tr>
                                        <td>Date Ready : &nbsp;&nbsp;<span><?php echo $rec[0]['dateReady'];?></span></td>
                                    </tr>
                                    </span>
                                <?php elseif ($rec[0]['status'] == 1): ?>
                                <tr>
                                    <td>
                                        <span id="span_created_button"> Date Created : &nbsp;&nbsp; <?php echo $rec[0]['dateCreated'];?></span>
                                    </td>
                                </tr>
                                        <?php endif; ?>


                                </tbody>
                            </table>
                        </div>
                        <div class="mt-10">
                            <?php if ($rec[0]['custSign']) { ?>
                                <p>Signature : </p>
                                <div style="max-height:200px">

                                    <img src="<?php echo $rec[0]['custSign']; ?>" alt="Red dot" style="max-height: 200px; max-width: 100%;" />

                                </div>
                            <?php } ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<!-- <div class="modal fade" id="washModal" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title">Are you sure you want to update this item to wash?</h4>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="wash_proceed">Proceed</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div> -->


<!-- Modal -->
<!-- <div class="modal fade" id="readyModal" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title">Are you sure you want to update this item to ready?</h4>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="ready_proceed">Proceed</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div> -->

<!-- Modal -->
<div class="modal fade" id="cancelModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title">Are you sure you want to cancel this item?</h4>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="cancel_proceed">Proceed</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<script>
    $(document).ready(function(){


        var orderID = "<?php echo $rec[0]['orderID']; ?>";

        //button's actions
        $('#wash').on('click', function(){
            // $("#washModal").modal();

            swal({
                title: "You are performing 'WASH' action.",
                text: "Do you still want to continue?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            })
                .then((willConfirm) => {
                    if (willConfirm.value) {

                        var dateNow = '<?php echo date('Y-m-d')?>';

                        updateDate(orderID, 'dateWashed');
                        $("#span_wash").hide();
                        $("#span_created_button").hide();

                        var listHTML = '<button type="button" class="btn btn-primary btn-xs pill ml-15" id="fold">Fold</button>';
                        listHTML += '<button type="button" class="btn btn-outline-danger btn-xs pill" id="cancel">Cancel</button>';
                        $("#result-date").append(listHTML);


                        displayDate("Date Washed : ", dateNow);


                    }
                });

        });


        //modal button's
        // $('#wash_proceed').on('click', function(){

        //     //updateDate(orderID, 'dateWashed');
        //     //update button to ready
        //     updateDate(orderID, 'dateWashed');
        //     $("#span_wash").hide();

        //     var listHTML = '<button type="button" class="btn btn-primary btn-xs pill ml-15" id="ready">Ready</button>';
        //          listHTML += '<button type="button" class="btn btn-outline-danger btn-xs pill" id="cancel">Cancel</button>';
        //     $("#result-date").append(listHTML);

        // });

        $(document).on('click', '#ready', function(){


            swal({
                title: "You are performing 'READY' action.",
                text: "Do you still want to continue?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            })
                .then((willConfirm) => {
                    if (willConfirm.value) {

                        updateDate(orderID, 'dateReady');

                        $("#result-date").html('');
                        $("#span_ready").html('');

                        var listHTML = '<button type="button" class="btn btn-outline-danger btn-xs pill" id="cancel">Cancel</button>';
                        $("#result-date").append(listHTML);
                        //window.location.reload();

                        var dateNow = '<?php echo date('Y-m-d')?>';

                        displayDate("Date Ready : ", dateNow);

                    }
                });
            //console.log("ready jprocedd");
        });

        $(document).on('click', '#cancel', function(){


            swal({
                title: "You are performing 'CANCEL' action.",
                text: "Do you still want to continue?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            })
                .then((willConfirm) => {
                    if (willConfirm.value) {

                        //updateDate(orderID, 'dateCancelled');
                        $("#result-date").html('');
                        $("#span_wash").html('');
                        $("#span_ready").html('');
                        $("#span_released").html('');


                    }
                });
            //console.log("ready jprocedd");
        });

        $(document).on('click', '#fold', function(){


            swal({
                title: "You are performing 'Fold' action.",
                text: "Do you still want to continue?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            })
                .then((willConfirm) => {
                    if (willConfirm.value) {

                        updateDate(orderID, 'dateFold');

                        $("#result-date").html('');
                        $("#span_ready").html('');

                        var listHTML = '<button type="button" class="btn btn-primary btn-xs pill ml-15" id="ready">Ready</button>';
                        listHTML += '<button type="button" class="btn btn-outline-danger btn-xs pill" id="cancel">Cancel</button>';
                        $("#result-date").append(listHTML);
                        //window.location.reload();

                        var dateNow = '<?php echo date('Y-m-d')?>';

                        displayDate("Date Fold : ", dateNow);

                    }
                });
            //console.log("ready jprocedd");
        });


        // $(document).on('click', '#ready', function(e){
        //     //alert("hello");
        //     $("#readyModal").modal();
        // });

        // $(document).on('click', '#cancel', function(e){
        //    // alert("ready");
        //     $("#cancelModal").modal();
        // });

        // $(document).on('click', '#cancel_proceed', function(){

        //     updateDate(orderID, 'dateCancelled');
        //     $("#result-date").html('');
        //     $("#span_wash").html('');
        //     $("#span_ready").html('');
        //     $("#span_released").html('');
        // });
    });

    function updateDate(orderID, date)
    {
        $.post("<?php echo $controller_page ?>/updateDate", { orID: orderID, date: date},
            function(data, status){
                console.log(data);

            });
    }
    function displayDate(label, date)
    {
        var listDisplayDate = $('<tr>').append($('<td>')
            .append(label+" "+date)
        );
        $('#tbody-display-date').append(listDisplayDate);
    }
</script>