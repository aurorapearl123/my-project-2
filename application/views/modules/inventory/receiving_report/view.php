<div class="subheader">
	<div class="d-flex align-items-center">
		<div class="title mr-auto">
			<h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></h3>
		</div>
		<div class="subheader-tools">
			<a href="<?php echo site_url('order/show') ?>" class="btn btn-primary btn-raised btn-sm pill"><i class="icon left ti-angle-left"></i> Back to List</a>
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
							<?php if ($roles['edit']) { ?>
							<li>
								<a href="<?php echo site_url('receiving_report/edit/'.$this->encrypter->encode($rec->rrID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>
							</li>
							<?php } ?>
							<?php if ($roles['delete'] && !$in_used) { ?>
							<li>
								<button name="cmddelete" id="cmddelete" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" onclick="deleteRecord('<?php echo $this->encrypter->encode($rec->rrID); ?>');"><i class="la la-trash-o"></i></button>
							</li>
							<?php } ?>
							<?php if ($this->session->userdata('current_user')->isAdmin) { ?>
							<li>
								<button type="button" id="recordlog" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Record Logs" onclick="popUp('<?php echo site_url('logs/record_log/receiving_report/rrID/'.$this->encrypter->encode($rec->rrID).'/Receiving Report') ?>', 1000, 500)"><i class="la la-server"></i></button>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>
                <div class="card-body">
                    <form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url("receiving_report/save") ?>">
                        <div class="table-row">
                            <table class="table-form column-3">
                                <tbody>
                                <tr>
                                    <td class="form-label" nowrap>
                                        <label for="config">Branch  <span class="asterisk">*</span></label>
                                    </td>
                                    <td class="form-group form-input" nowrap>
                                        <input type="text" class="border-0" name="branchName" id="branchName" title="Branch Name" value="<?php echo $rec->branchName;?>" readonly>
                                        <input type="hidden" class="form-control" name="branchID" id="branchID" title="Branch Id" value="<?php echo $rec->branchID;?>" readonly>
                                    </td>
                                    <td width="450px">

                                    </td>
                                    <td class="form-label" style="width:12%" nowrap>Date</td>
                                    <td class="form-group form-input" style="width:21.33%">
                                        <input type="text" class="border-0" name="date" id="date" title="Date" required value="<?php echo $rec->date?>" readonly>
                                    </td>
                                    <td class="d-xxl-none" colspan="3"></td>
                                </tr>
                                <tr>
                                    <td class="form-label">Supplier </td>
                                    <td class="form-group form-input">
                                       <span><?php echo $rec->name;?></span>
                                    </td>
                                    <td>
                                    </td>
                                    <td class="form-label" style="width:12%" nowrap>Reference No: <span class="asterisk">*</span></td>
                                    <td class="form-group form-input" style="width:21.33%">
                                        <span><?php echo $rec->referenceNo?></span>
                                    </td>
                                    <td class="d-xxl-none"></td>
                                </tr>
                                </tbody>
                            </table>
                            <hr>
                            
                                
                            <!-- start create header details -->
                            <table class="table table-hover small-text" id="tb">
                                <tr class="tr-header">
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Amount</th>
                                    <?php foreach($rr_details as $detail) : ?>
                                        <tr class="item">
                                            <td><?php echo $detail->brand?></td>
                                            <td><?php echo $detail->price?></td>
                                            <td style="display:none"><?php echo $detail->itemID; ?></td>
                                            <td style="display:none"><?php echo $detail->inventory_qty;?></td>
                                            <td class="id">
                                                <input type="text" class="id border-0" value="<?php echo $detail->amount?>" readonly>
                                            </td>
                                        </tr>
                                    <?php endforeach;?>    
                                   
                            </table>
                            <!-- end create header details -->
                        </div>
                        <br>

                        <table class="table-form column-3">
                            <tbody>
                            <tr>
                                <td class="form-label" nowrap>
                                    <label for="config">Total Amount :  <span class="asterisk">*</span></label>
                                </td>
                                <td class="form-group form-input" nowrap>
                                    <input type="text" class="form-control" name="ttlAmount" id="ttlAmount" title="Branch Name" required readonly>
                                </td>


                            </tr>
                            </tbody>
                        </table>

                        <div class="form-sepator solid"></div>
                        <table class="table-form column-3">
                            <tbody>
                            <tr>
                                <td class="form-label" nowrap>
                                    <label for="config">Created By : </label>
                                </td>
                                <td class="form-group form-input" nowrap>
                                   <span><?php echo $rec->firstName .' '.$rec->middleName .' '.$rec->lastName;?></span>
                                </td>
                                <td width="450px">

                                </td>
                                <td class="form-label" style="width:12%" nowrap>Date Created : </td>
                                <td class="form-group form-input" style="width:21.33%">
                                    <span><?php echo $rec->dateCreated; ?></span>
                                </td>
                                <td class="d-xxl-none" colspan="3"></td>
                            </tr>
                            <tr>
                                <td class="form-label">Approved by:  </td>
                                <td class="form-group form-input">
                                    <span id="id_approvedBy"><?php echo $rec->approvedFirstName .' '.$rec->approvedMiddleName .' '.$rec->approvedLastName;?></span>
                                </td>
                                <td>
                                </td>
                                <td class="form-label" style="width:12%" nowrap>Approved date: </td>
                                <td class="form-group form-input" style="width:21.33%">
                                    <span id="id_approved_date"><?php echo $rec->dateApproved?></span>
                                </td>
                                <td class="d-xxl-none"></td>
                            </tr>

                            <tr>
                                <td class="form-label">Cancelled by:  </td>
                                <td class="form-group form-input">
                                    <span id="id_cancel_by"><?php echo $rec->cancelledFirstName .' '.$rec->cancelledMiddleName .' '.$rec->cancelledLastName;?></span>
                                </td>
                                <td>
                                </td>
                                <td class="form-label" style="width:12%" nowrap>Cancelled date: </td>
                                <td class="form-group form-input" style="width:21.33%">
                                    <span id="id_date_cancelled"><?php echo $rec->dateCancelled?></span>
                                </td>
                                <td class="d-xxl-none"></td>
                            </tr>
                            </tbody>
                        </table>
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
        var total = 0;
         $("tr.item").each(function() {
            var price = $(this).find("input.name").val(),
                amount = $(this).find("input.id").val();
            total += parseFloat(amount);
         });

        $('#ttlAmount').val(total);

        $('#confirm').on('click', function(){
            $("#confirmModal").modal();
        });
        $('#cancel').on('click', function(){
            $("#cancelModal").modal();
        });

        $("#confirm_proceed").on('click', function(){
            //add stock card
            //var url = "<?php //echo site_url('generic_ajax/addStockCard'); ?>//";
            //var reffNo = "<?php //echo $this->router->fetch_class().'_'.rand(); ?>//";
            //$.post(url, { reffNo: reffNo },
            //    function(data, status){
            //    console.log(data);
            //        if(status == "success") {
            //
            //        }
            //    });

            var table = $("#tb");
            table.find('tr').each(function(i){
                var $tds = $(this).find('td');
                var item_id = $tds.eq(2).text();
                var inventory_qty = $tds.eq(3).text();
                if(inventory_qty != "") {
                    console.log("inventory quantity : "+inventory_qty);
                    console.log(item_id);
                    var variance = 0;
                    var debit = inventory_qty;
                    var credit = 0;
                    var begBal = inventory_qty;
                    var endBal = inventory_qty;
                    var rrID = "<?php echo $rec->rrID; ?>";
                    addStockCard(item_id,variance, debit, credit, begBal, endBal, rrID, 0);
                }

            });
        });

        $('#cancel_proceed').on('click', function(){
            updateCancel();
        });
    });

    function addStockCard(item_id, variance, debit, credit, begBal, endBal, rrID, prID)
    {

        $.post("<?php echo $controller_page ?>/addStockCard", { reffNo: rrID, item_id: item_id, variance: variance, debit: debit, credit: credit, begBal: begBal, endBal: endBal, rrID: rrID },
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
        var rrID = "<?php echo $rec->rrID; ?>";
        //updateCanceledBy
        //updateCanceledBy


        $.post("<?php echo $controller_page ?>/updateCanceledBy", { rrID: rrID },
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