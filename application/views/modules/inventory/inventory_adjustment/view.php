<div class="subheader">
    <div class="d-flex align-items-center">
        <div class="title mr-auto">
            <h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></h3>
        </div>
        <div class="subheader-tools">
            <a href="<?php echo site_url('inventory_adjustment/show') ?>" class="btn btn-primary btn-raised btn-xs pill"><i class="icon ti-angle-left"></i> Back to List</a>
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
                            <h4 class="head-text">View <?php echo $current_module['module_label'] ?></h4>
                        </div>
                    </div>
                    <div class="card-head-tools">
                        <ul class="tools-list">
                            <?php if($rec->dateApproved == '0000-00-00 00:00:00' &&  $rec->dateCancelled == '0000-00-00 00:00:00') : ?>
                            <li>
                                <a href="javascript: confirm();" id="confirm" class="btn btn-primary btn-raised btn-xs pill"><i class="la la-thumbs-up"></i> Confirm</a> 
                            </li>
                            <?php endif;?>
                            <?php if($rec->dateApproved == '0000-00-00 00:00:00' &&  $rec->dateCancelled == '0000-00-00 00:00:00') : ?>
                            <li>
                                <a href="javascript: cancel();" id="cancel" class="btn btn-primary btn-raised btn-xs pill"><i class="la la-ban"></i> Cancel</a>   
                            </li>
                            <?php endif;?>                          
                         
                            <?php if ($roles['edit']) {?>
                                <li>
                                    <a href="<?php echo site_url('inventory_adjustment/edit/'.$this->encrypter->encode($rec->adjID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>
                                </li>
                            <?php } ?>
                            <?php if ($roles['delete'] && !$in_used) {?>
                                <li>
                                    <button name="cmddelete" id="cmddelete" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" onclick="deleteRecord('<?php echo $this->encrypter->encode($rec->adjID); ?>');"><i class="la la-trash-o"></i></button>
                                </li>
                            <?php } ?>
                            <?php if ($this->session->userdata('current_user')->isAdmin) {?>
                                <li>
                                    <button type="button" id="recordlog" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Record Logs" onclick="popUp('<?php echo site_url('logs/record_log/inventory_adjustment/adjID/'.$this->encrypter->encode($rec->adjID).'/Inventory Adjustment') ?>', 1000, 500)"><i class="la la-server"></i></button>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="data-view">
                        <table class="view-table">
                            <tbody>
                                <tr>
                                    <td class="data-title" style="width:120px" nowrap>Reference No:</td>
                                     <td class="data-input" style="width:420px"><?php echo str_pad($rec->adjNo,4,'0',STR_PAD_LEFT); ?></td>
                                </tr>
                            <tr>

                                <td class="data-title" style="width:120px" nowrap>Branch:</td>
                                <td class="data-input" style="width:420px"><?php echo $rec->branchName; ?></td>
                                <td class="data-title" style="width:150px" nowrap>Date:</td>
                                <td class="data-input" style="width:420px"><?php echo date('M d, Y',strtotime($rec->date))?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="data-title" style="width:120px" nowrap>Brand</td>
                                <td class="data-input" style="width:420px" nowrap><?php echo $rec->brand; ?></td>
                                <td class="data-title">Item</td>
                                <td class="data-input" style="width:420px" nowrap><?php echo $rec->item; ?></td>
                                <td class="data-input" style="visibility: hidden" id="itemID"><?php echo $rec->itemID; ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="data-title" nowrap>Description:</td>
                                <td class="data-input"><?php echo $rec->description; ?></td>
                                <td class="data-title"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="data-title" nowrap>UMSR:</td>
                                <td class="data-input"><?php echo $rec->umsr; ?></td>
                                <td class="data-title">QTY:</td>
                                <td class="data-input"><?php echo $rec->qty; ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="data-title" nowrap>Remarks:</td>
                                <td class="data-input"><?php echo $rec->remarks; ?></td>
                                <td class="data-title" nowrap>Adjustment Type:</td>
                                <td class="data-input">                                
                                    <?php 
                                        if ($rec->adjType == 'DR') {
                                            echo "<span class='badge badge-pill badge-info'>Add</span>";
                                        } else if ($rec->adjType == 'CR') {
                                            echo "<span class='badge badge-pill badge-info'>Subtract</span>";
                                        }
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td class="data-title" style="width:120px" nowrap>Created By:</td>
                                <td class="data-input" style="width:300px" nowrap><?php echo $rec->createdByFirst. ' '.$rec->createdByMiddle.' '.$rec->createdByLast ?></td>


                            </tr>

                            <tr>

                                <td class="data-title" style="width:" nowrap>Confirmed By:</td>
                                <td class="data-input" style="width:" nowrap><?php echo $rec->approvedByFirst. ' '.$rec->approvedByMiddle.' '.$rec->approvedByLast ?></td>


                            </tr>
                            <tr>

                                <td class="data-title" nowrap>Cancelled By:</td>
                                <td class="data-input" style="width:" nowrap><?php echo $rec->cancelledByFirst. ' '.$rec->cancelledByMiddle.' '.$rec->cancelledByLast ?></td>

                            </tr>
                            <tr>
                                <td class="data-title" nowrap>Status:</td>
                                <td class="data-input">
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
                                <td class="data-title"></td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    var adjID = "<?php echo $rec->adjID; ?>";
    var itemID = "<?php echo $rec->itemID; ?>";
    var qty = "<?php echo $rec->qty; ?>";
    var adjType = '<?php echo $rec->adjType?>';
    var debit = qty;
    var credit = 0;
    var begBal = qty;
    var endBal = qty;

    function confirm(){
        
            console.log('adjID',adjID)
            console.log('itemID',itemID)
            console.log('qty',qty)
            console.log('debit',debit)
            console.log('adjID',adjID)
            console.log('credit',credit)
            console.log('begBal',begBal)
            console.log('endBal',endBal)
            console.log('endBal',endBal)
            console.log('adjType',adjType)

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

                $.post("<?php echo $controller_page ?>/updateApprovedBy", { adjID: adjID },
                    function(data, status){
                        if(status == "success") {   
                            $.post("<?php echo $controller_page ?>/updateQTY", { itemID: itemID, qty:qty, adjType:adjType },
                                function(data, status){
                                    if(status == "success") {                                                                                   
                                        window.location.reload(); 
                                    }
                                }
                            );    
                        }
                    }
                );         

          }
        });     
    }//end of function confirm

    function cancel(){

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
            var adjID = "<?php echo $rec->adjID; ?>";
                $.post("<?php echo $controller_page ?>/updateCancelledBy", { adjID: adjID },
                    function(data, status){                     
                        if(status == "success") {
                           window.location.reload();                            
                        }
                });
          }
        });     
    }//end of function cancel
</script>