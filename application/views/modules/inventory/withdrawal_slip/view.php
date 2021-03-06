<div class="subheader">
    <div class="d-flex align-items-center">
        <div class="title mr-auto">
            <h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></h3>
        </div>
        <div class="subheader-tools">
            <a href="<?php echo site_url('withdrawal_slip/show') ?>" class="btn btn-primary btn-raised btn-sm pill"><i class="icon ti-angle-left left md"></i> Back to List</a>
        </div>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="card-head">
                    <div class="head-caption">
                        <div class="head-title">
                            <h4 class="head-text">View <?php echo $current_module['module_label'] ?>
                                <?php if($rec->dateApproved == '0000-00-00 00:00:00' && $rec->dateCancelled == '0000-00-00 00:00:00' ) : ?>
                                    <button type="button" class="btn btn-primary btn-xs pill ml-15" id="confirm">Confirm</button>
                                    <button type="button" class="btn btn-outline-danger btn-xs pill" id="cancel">Cancel</button>
                                <?php endif;?>
                            </h4>
                        </div>
                    </div>
                    <div class="card-head-tools">
                        <ul class="tools-list">
                            <?php if ($roles['edit']) { ?>
                            <li>
                                <a href="<?php echo site_url('withdrawal_slip/edit/'.$this->encrypter->encode($rec->wsID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>
                            </li>
                            <?php } ?>
                            <?php if ($roles['delete'] && !$in_used) { ?>
                            <li>
                                <button name="cmddelete" id="cmddelete" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" onclick="deleteRecord('<?php echo $this->encrypter->encode($rec->wsID); ?>');"><i class="la la-trash-o"></i></button>
                            </li>
                            <?php } ?>
                            <?php if ($this->session->userdata('current_user')->isAdmin) { ?>
                            <li>
                                <button type="button" id="recordlog" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Record Logs" onclick="popUp('<?php echo site_url('logs/record_log/withdrawal_slip/pcID/'.$this->encrypter->encode($rec->wsID).'/Withdrawal slip') ?>', 1000, 500)"><i class="la la-server"></i></button>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url("withdrawal_slip/save") ?>">
                        <div class="data-view">
                            <table class="view-table">
                                <tbody>                                    
                                    <tr>                                        
                                        <td class="data-title" style="width:13%" nowrap>Withdrawal Slip No : </td>
                                        <td class="data-input" style="width:22%">
                                            <input type="text" class="border-0" name="wsNo" id="wsNo" title="Withdrawal Slip No " required value="<?php echo 'pc'.str_pad($rec->wsNo,4,'0',STR_PAD_LEFT); ?>" >
                                        </td>
                                        <td class="d-xxl-none"></td>
                                    </tr>
                                    <tr>
                                        <td class="data-title w-10">
                                            <label for="config">Branch : <span class="asterisk">*</span></label>
                                        </td>
                                        <td class="data-input w-20">
                                            <input type="text" class="border-0" name="branchName" id="branchName" title="Branch Name" value="<?php echo $rec->branchName;?>" readonly>
                                            <input type="hidden" class="form-control" name="branchID" id="branchID" title="Branch Id" value="<?php echo $rec->branchID;?>" readonly>
                                        </td>
                                        <td class="data-title w-10">Date : </td>
                                        <td class="data-input w-20">
                                            <input type="text" class="border-0" name="date" id="date" title="Date" required value="<?php echo date('F d, Y',strtotime($rec->date))?>" readonly>
                                        </td>
                                        <td class="d-xxl-none"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div> 
                            <!-- start create header details -->
                            <?php
                                $table_str="<table class='table  mt-20' id='tb'>";
                                $table_str.='<thead class="thead-light"><tr>';
                                $table_str.='<th>'.'No'.'</th>';
                                $table_str.='<th>'.'Category'.'</th>';
                                $table_str.='<th>'.'Quantity'.'</th>';
                                $table_str.='<tr></thead>';
                                $i = 1;
                                foreach ($items as $rows) {
                                    $table_str.='<tr>';
                                    $table_str.='<td>'.($i++).'</td>';
                                    $table_str.='<td>'.$rows->item.'</td>';
                                    $table_str.='<td>'.$rows->qty.'</td>';
                                    $table_str.='<td style="display:none">'.$rows->itemID.'</td>';
                                    $table_str.='<td style="display:none">'.$rows->inventory_qty.'</td>';
                                    //$table_str.='<td>'.'<input type="number" min="1" id="id_'.($i++).'" name="id_'.($i++).'">'.'</td>';
                                   
                                    $table_str.='</tr>';
                                }
                                $table_str.="</table>";

                                echo $table_str;
                            ?>
                            <!-- end create header details -->
                        </div>
                        <br>

                        <div class="data-view">
                            <table class="view-table">
                                <tbody>
                                    <tr>                                        
                                        <td class="data-title w-10">
                                            <label for="config">Withdrawned By :  <span class="asterisk">*</span></label>
                                        </td>
                                        <td class="form-group form-input">
                                            <span><?php echo $rec->withdrawnedBy?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="data-title w-10">
                                            <label for="config">Remarks :  <span class="asterisk">*</span></label>
                                        </td>
                                        <td class="form-group form-input">
                                            <span><?php echo $rec->remarks?></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-sepator solid mx-0"></div>
                        <div class="data-view">
                            <table class="view-table">
                                <tbody>
                                    <tr>
                                        <td class="data-title w-10">
                                            <label for="config">Created By : </label>
                                        </td>
                                        <td class="data-input w-20">
                                           <span><?php echo $rec->firstName .' '.$rec->middleName .' '.$rec->lastName;?></span>
                                        </td>

                                        <td class="data-title w-10">Date Created : </td>
                                        <td class="data-input w-20">
                                            <span><?php echo date('M d, Y H:i:s',strtotime($rec->dateCreated))?></span>
                                        </td>
                                        <td class="d-xxl-none"></td>
                                    </tr>
                                    <?php if($rec->dateApproved != '0000-00-00 00:00:00' ) { ?>
                                    <tr>
                                        <td class="data-title">Approved by:  </td>
                                        <td class="data-input">
                                            <span id="id_approvedBy"><?php echo $rec->approvedFirstName .' '.$rec->approvedMiddleName .' '.$rec->approvedLastName;?></span>
                                        </td>
                                        <td class="data-title">Approved date: </td>
                                        <td class="data-input">
                                            <span id="id_approved_date"><?php echo date('M d, Y H:i:s',strtotime($rec->dateApproved))?></span>
                                        </td>
                                        <td class="d-xxl-none"></td>
                                    </tr>
                                    <?php } ?>
                                    <?php if( $rec->dateCancelled != '0000-00-00 00:00:00' ) { ?>
                                    <tr>
                                        <td class="data-title">Cancelled by:  </td>
                                        <td class="data-input">
                                            <span id="id_cancel_by"><?php echo $rec->cancelledFirstName .' '.$rec->cancelledMiddleName .' '.$rec->cancelledLastName;?></span>
                                        </td>
                                        <td class="data-title">Cancelled date: </td>
                                        <td class="data-input">
                                            <span id="id_date_cancelled"><?php echo date('M d, Y H:i:s',strtotime($rec->dateCancelled))?></span>
                                        </td>
                                        <td class="d-xxl-none"></td>
                                    </tr>
                                    <?php } ?>
                                        
                                    <tr>
                                        <td class="data-title">Status :</td>
                                        <td>
                                                <?php 
                                                    if ($rec->status == 1) {
                                                        echo "<span class='badge badge-pill badge-warning'>Pending</span>";
                                                    } else if ($rec->status == 2) {
                                                        echo "<span class='badge badge-pill badge-success'>Confirmed</span>";
                                                    } else if ($rec->status == 0) {
                                                        echo "<span class='badge badge-pill badge-danger'>Cancelled</span>";
                                                    } 
                                                ?>
                                        </td>
                                        
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="cancelModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title">Are you sure you want to cancel this item?</h4>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary pill btn-raised" data-dismiss="modal" id="cancel_proceed">Proceed</button>
                <button type="button" class="btn btn-outline-danger pill btn-raised" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="confirmModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title">Are you sure you want to confirm this item?</h4>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary pill btn-raised" data-dismiss="modal" id="confirm_proceed">Proceed</button>
                <button type="button" class="btn btn-outline-danger pill btn-raised" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<script>
    $(document).ready(function(){
        var total = 0;
        var wsID = "<?php echo $rec->wsID; ?>";
         $("tr.item").each(function() {

                    var price = $(this).find("input.name").val(),
                        amount = $(this).find("input.id").val();
                    total += parseFloat(amount);
            });

        $('#ttlAmount').val(total);

        $("#confirm").on('click', function(){
            //alert("confirm proceed");
            console.log("enter here");

            var table = $("#tb");
            table.find('tr').each(function(i){
                var $tds = $(this).find('td');
                var item_id = $tds.eq(3).text();
                var inventory_qty = $tds.eq(4).text();
                if(item_id != "") {
                    //console.log("result");
                    //console.log(item_id);
                    //console.log(inventory_qty);
                    var variance = inventory_qty;
                    var debit = 0;
                    var credit = inventory_qty;
                    var begBal = inventory_qty;
                    var endBal = inventory_qty;

                    // addStockCard(item_id,  debit, credit, begBal, endBal, wsID, variance);

                   swal({
                      title: "You are performing 'CONFIRM' action.",
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

                            $.post("<?php echo $controller_page ?>/updateApprovedBy", { wsID: wsID },
                                function(data, status){
                                    if(status == "success") {   

                                        $.post("<?php echo $controller_page ?>/addStockCard", {item_id: item_id, debit: debit, credit: credit, begBal: begBal, endBal: endBal, wsID: wsID, variance: variance },
                                            function(data, status){
                                                console.log(data);
                                                if(status == "success") {
                                                   window.location.reload(); 
                                                }
                                            });

                                       

                                    }
                                }
                            );         

                      }
                    });     
                }

            });

        });
        $('#cancel').on('click', function(){
            updateCancel();
            //alert("hello");
        });
    });


    function updateCancel()
    {
        var wsID = "<?php echo $rec->wsID; ?>";
        //updateCanceledBy
        //updateCanceledBy

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
        .then((willCancel) => {
          if (willCancel.value) {
                $.post("<?php echo $controller_page ?>/updateCancelledBy", { wsID: wsID },
                    function(data, status){
                        console.log(data);
                        if(status == "success") {
                           window.location.reload();
                        }
                    });
          }
        });     
    }


</script>