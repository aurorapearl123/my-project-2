<div class="subheader">
	<div class="d-flex align-items-center">
		<div class="title mr-auto">
			<h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></h3>
		</div>
		<div class="subheader-tools">
			<a href="<?php echo site_url('expense_inventory/show') ?>" class="btn btn-primary btn-raised btn-sm pill"><i class="icon left ti-angle-left md"></i> Back to List</a>
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
                            </h4>
                            <?php endif;?>
                        </div>
					</div>
					<div class="card-head-tools">
						<ul class="tools-list">
							<?php if ($roles['edit']) {?>
							<li>
								<a href="<?php echo site_url('expense_inventory/edit/'.$this->encrypter->encode($rec->expID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>
							</li>
							<?php } ?>
							<?php if ($roles['delete'] && !$in_used) {?>
							<li>
								<button name="cmddelete" id="cmddelete" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" onclick="deleteRecord('<?php echo $this->encrypter->encode($rec->expID); ?>');"><i class="la la-trash-o"></i></button>
							</li>
							<?php } ?>
							<?php if ($this->session->userdata('current_user')->isAdmin) {?>
							<li>
								<button type="button" id="recordlog" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Record Logs" onclick="popUp('<?php echo site_url('logs/record_log/expense_inventory/expID/'.$this->encrypter->encode($rec->expID).'/Expense Particular') ?>', 1000, 500)"><i class="la la-server"></i></button>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<div class="card-body">
					<div class="data-view">
                        <!-- start create header details -->
                        <?php
                        $table_str="<table class='table mt-20' id='table_physical_count'>";
                        $table_str.='<thead class="thead-light"><tr>';
                        $table_str.='<th>'.'Particular'.'</th>';
                        $table_str.='<th>'.'Quantity'.'</th>';
                        $table_str.='<th>'.'Amount'.'</th>';
                        $table_str.='<tr></thead>';
                        $i = 1;
                        foreach ($details as $rows) {
                            $table_str.='<tr>';
                            $table_str.='<td>'.$rows->description.'</td>';

                            //$table_str.='<td>'.'<input type="number" min="1" id="id_'.($i++).'" name="id_'.($i++).'">'.'</td>';
                            $table_str.='<td>'.'<span>'.$rows->qty.'</span>'.'</td>';
                            $table_str.='<td>'.'<span>'.$rows->amount.'</span>'.'</td>';
                            $table_str.='</tr>';
                        }
                        $table_str.="</table>";

                        echo $table_str;
                        ?>
                        <!-- end create header details -->

                        <div class="table-form mt-30">
                            <table class="table-form">
                                <tbody>
                                <tr>
                                    <td class="form-label" width="13%" nowrap>
                                        <label for="config">Remarks : <span class="asterisk">*</span></label>
                                    </td>
                                    <td class="form-group form-input" width="22%">
                                        <span><?php echo $rec->remarks?></span>
                                    </td>
                                    <td></td>
                                    <td class="form-label" width="13%" nowrap>
                                        <label for="config">Total : <span class="asterisk">*</span></label>
                                    </td>
                                    <td class="form-group form-input" width="5%">
                                        <span><?php echo $rec->ttlAmount?></span>
                                    </td>
                                    <td class="d-xxl-none"></td>
                                </tr>
                                </tbody>
                            </table>

                            <div class="form-sepator solid mx-0"></div>
                            <div class="data-view">
                                <table class="view-table">
                                    <tbody>
                                    <tr>
                                        <td class="data-title w-10">Created by : </td>
                                        <td class="data-input w-20">
                                            <span><?php echo $rec->firstName .' '.$rec->middleName .' '.$rec->lastName;?></span>
                                        </td>
                                        <td class="data-title w-10">Date Created : </td>
                                        <td class="data-input w-20">
                                            <span><?php echo $rec->dateCreated; ?></span>
                                        </td>
                                        <td class="d-xxl-none"></td>
                                    </tr>
                                    <?php if($rec->dateApproved != '0000-00-00 00:00:00' ) { ?>
                                        <tr>
                                            <td class="data-title">Approved by : </td>
                                            <td class="data-input">
                                                <span id="id_approvedBy"><?php echo $rec->approvedFirstName .' '.$rec->approvedMiddleName .' '.$rec->approvedLastName;?></span>
                                            </td>
                                            <td class="data-title">Approved date : </td>
                                            <td class="data-input">
                                                <span id="id_approved_date"><?php echo $rec->dateApproved?></span>
                                            </td>
                                            <td class="d-xxl-none"></td>
                                        </tr>
                                    <?php } ?>
                                    <?php if($rec->dateCancelled != '0000-00-00 00:00:00' ) { ?>
                                    <tr>
                                        <td class="data-title">Cancelled by :  </td>
                                        <td class="data-input">
                                            <span id="id_cancel_by"><?php echo $rec->cancelledFirstName .' '.$rec->cancelledMiddleName .' '.$rec->cancelledLastName;?></span>
                                        </td>
                                        <td class="data-title">Cancelled date : </td>
                                        <td class="data-input">
                                            <span id="id_date_cancelled"><?php echo $rec->dateCancelled?></span>
                                        </td>
                                        <td class="d-xxl-none"></td>
                                        <?php } ?>
                                    </tr>
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

                        </div>
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
    $(document).ready(function() {

        $('#confirm').on('click', function(){
            $("#confirmModal").modal();
        });
        $('#cancel').on('click', function(){
            $("#cancelModal").modal();
        });

        $("#confirm_proceed").on('click', function(){
            console.log("proceed confirm");
            updateApprovedBy();
        });

        $('#cancel_proceed').on('click', function(){
            console.log("cancel me");
            updateCancel();
        });
        console.log("testing me");
    });

    function updateCancel(){
        var expID = "<?php echo $rec->expID; ?>";
        //updateCanceledBy
        //updateCanceledBy


        $.post("<?php echo $controller_page ?>/updateCanceledBy", { expID: expID },
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

    function updateApprovedBy()
    {
        var expID = "<?php echo $rec->expID; ?>";
        $.post("<?php echo $controller_page ?>/updateApproveBy", { expID: expID },
            function(data, status){
                console.log(data);
                if(status == "success") {
                    $('#confirm').hide();
                    $('#cancel').hide();
                    //need to update the date
                }
            });
    }

</script>