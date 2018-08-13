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
                            <?php if($rec->dateApproved == '0000-00-00 00:00:00' && $rec->dateCancelled == '0000-00-00 00:00:00' ) : ?>
                                <button type="button" class="btn btn-primary btn-xs" id="confirm">Confirm</button>
                                <button type="button" class="btn btn-warning btn-xs" id="cancel">Cancel</button>
                            <?php endif;?>
                            <li>
                                <a href="<?php echo site_url('inventory_adjustment/approve/'.$this->encrypter->encode($rec->adjID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Approve"><i class="la la-thumbs-up"></i></a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('inventory_adjustment/cancel/'.$this->encrypter->encode($rec->adjID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Cancel"><i class="la la-ban"></i></a>
                            </li>
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
                                    <td class="data-title" nowrap>Branch:</td>
                                    <td class="data-input"><?php echo $rec->branchName; ?></td>
                                    <td class="data-title" nowrap>Date:</td>
                                    <td class="data-input"><?php echo date('M d, Y',strtotime($rec->date))?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="data-title" style="width:120px" nowrap>Brand</td>
                                    <td class="data-input" style="width:420px" nowrap><?php echo $rec->brand; ?></td>
                                    <td class="data-title">Item</td>
                                    <td class="data-input" style="width:420px" nowrap><?php echo $rec->item; ?></td>
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
                                    <td class="data-title"></td>
                                    <td></td>
                                </tr>

                                    <tr>
                                        <td class="data-title" style="width:120px" nowrap>Created By:</td>
                                        <td class="data-input" style="width:300px" nowrap><?php echo $rec->createdByFirst. ' '.$rec->createdByMiddle.' '.$rec->createdByLast ?></td>


                                    </tr>

                                    <tr>

                                        <td class="data-title" style="width:" nowrap>Confirmed By:</td>
                                        <td class="data-input" style="width:" nowrap><?php echo $rec->confirmedByFirst. ' '.$rec->confirmedByMiddle.' '.$rec->confirmedByLast ?></td>
    

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
                                                echo "<span class='badge badge-pill badge-success'>Active</span>";
                                            } else {
                                                echo "<span class='badge badge-pill badge-danger'>Inactive</span>";
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

<!-- Modal -->
<div class="modal fade" id="confirmModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title">Are you sure you want to confirm this item?</h4>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="confirm_proceed">Proceed</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<script>
    $(document).ready(function(){

        $('#confirm').on('click', function(){
            $("#confirmModal").modal();
        });
        $('#cancel').on('click', function(){
            $("#cancelModal").modal();
        });

        $("#confirm_proceed").on('click', function(){
            var adjID = "<?php echo $rec->adjID; ?>";
            var itemID = "<?php echo $rec->itemID; ?>";
            var qty = "<?php echo $rec->qty; ?>";
            var debit = qty;
            var credit = 0;
            var begBal = qty;
            var endBal = qty;
            addStockCard(itemID, debit, credit, begBal, endBal, adjID);

        });

        $('#cancel_proceed').on('click', function(){
            updateCancel();
        });
    });

    function addStockCard(item_id, debit, credit, begBal, endBal, adjID)
    {

        $.post("<?php echo $controller_page ?>/addStockCard", { item_id: item_id, debit: debit, credit: credit, begBal: begBal, endBal: endBal, adjID: adjID },
            function(data, status){
                console.log(data);
                if(status == "success") {
                    $('#confirm').hide();
                    $('#cancel').hide();
                    var approved_by = "<?php echo $rec->firstName .' '.$rec->middleName .' '.$rec->lastName;?>";
                    $("#id_approvedBy").text(approved_by);
                    $("#id_approved_date").text("<?php echo date('Y-m-d h:is')?>");
                }
            });
    }

    function updateCancel()
    {
        var adjID = "<?php echo $rec->adjID; ?>";

        $.post("<?php echo $controller_page ?>/updateCanceledBy", { adjID: adjID },
            function(data, status){
                console.log(data);
                if(status == "success") {
                    $('#confirm').hide();
                    $('#cancel').hide();
                    var approved_by = "<?php echo $rec->firstName .' '.$rec->middleName .' '.$rec->lastName;?>";
                    $("#id_cancel_by").text(approved_by);
                    $("#id_date_cancelled").text("<?php echo date('Y-m-d h:is')?>");
                }
            });
    }

</script>