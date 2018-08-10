<div class="subheader">
	<div class="d-flex align-items-center">
		<div class="title mr-auto">
			<h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></h3>
		</div>
		<div class="subheader-tools">
			<a href="<?php echo site_url('order/show') ?>" class="btn btn-primary btn-raised btn-xs pill"><i class="icon ti-angle-left"></i> Back to List</a>
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
							<?php if ($roles['edit']) { ?>
							<li>
								<a href="<?php echo site_url('physical_count/edit/'.$this->encrypter->encode($rec->prID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>
							</li>
							<?php } ?>
							<?php if ($roles['delete'] && !$in_used) { ?>
							<li>
								<button name="cmddelete" id="cmddelete" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" onclick="deleteRecord('<?php echo $this->encrypter->encode($rec->prID); ?>');"><i class="la la-trash-o"></i></button>
							</li>
							<?php } ?>
							<?php if ($this->session->userdata('current_user')->isAdmin) { ?>
							<li>
								<button type="button" id="recordlog" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Record Logs" onclick="popUp('<?php echo site_url('logs/record_log/physical_count/pcID/'.$this->encrypter->encode($rec->prID).'/Physical count') ?>', 1000, 500)"><i class="la la-server"></i></button>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>
                <div class="card-body">
                    <form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url("physical_count/save") ?>">
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
                                    <td class="form-label" style="width:12%" nowrap>Date : </td>
                                    <td class="form-group form-input" style="width:21.33%">
                                        <input type="text" class="border-0" name="date" id="date" title="Date" required value="<?php echo $rec->date?>" readonly>
                                    </td>
                                    <td class="d-xxl-none" colspan="3"></td>
                                </tr>
                                </tbody>
                            </table>
                            <hr>
                            
                                
                                                        <!-- start create header details -->
                            <?php
                                $table_str="<table border='0' class='table table-sm'>";
                                $table_str.='<tr>';
                                $table_str.='<th>'.'No'.'</th>';
                                $table_str.='<th>'.'Category'.'</th>';
                                $table_str.='<th>'.'Expected Qty'.'</th>';
                                $table_str.='<th>'.'Physical Qty'.'</th>';
                                $table_str.='<th>'.'Variance'.'</th>';
                                $table_str.='<tr>';
                                $i = 1;
                                foreach ($items as $rows) {
                                    $table_str.='<tr>';
                                    $table_str.='<td>'.($i++).'</td>';
                                    $table_str.='<td>'.$rows->item.'</td>';
                                      $table_str.='<td>'.$rows->systemQty.'</td>';
                                    //$table_str.='<td>'.'<input type="number" min="1" id="id_'.($i++).'" name="id_'.($i++).'">'.'</td>';
                                    $table_str.='<td>'.'<span>'.$rows->actualQty.'</span>'.'</td>';
                                    $table_str.='<td>'.'<span>'.$rows->variance.'</span>'.'</td>';
                                    $table_str.='</tr>';
                                }
                                $table_str.="</table>";

                                echo $table_str;
                            ?>
                            <!-- end create header details -->
                        </div>
                        <br>

                        <table class="table-form column-3">
                            <tbody>
                            <tr>
                                <td class="form-label" nowrap>
                                    <label for="config">Remarks :  <span class="asterisk">*</span></label>
                                </td>
                                <td class="form-group form-input" nowrap>
                                    <span><?php echo $rec->remarks?></span>
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
                                    <span><?php echo $rec->approvedFirstName .' '.$rec->approvedMiddleName .' '.$rec->approvedLastName;?></span>
                                </td>
                                <td>
                                </td>
                                <td class="form-label" style="width:12%" nowrap>Approved date: </td>
                                <td class="form-group form-input" style="width:21.33%">
                                    <span><?php echo $rec->dateApproved?></span>
                                </td>
                                <td class="d-xxl-none"></td>
                            </tr>

                            <tr>
                                <td class="form-label">Cancelled by:  </td>
                                <td class="form-group form-input">
                                    <span><?php echo $rec->cancelledFirstName .' '.$rec->cancelledMiddleName .' '.$rec->cancelledLastName;?></span>
                                </td>
                                <td>
                                </td>
                                <td class="form-label" style="width:12%" nowrap>Cancelled date: </td>
                                <td class="form-group form-input" style="width:21.33%">
                                    <span><?php echo $rec->dateCancelled?></span>
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
<script>
    $(document).ready(function(){
        var total = 0;
         $("tr.item").each(function() {

                    var price = $(this).find("input.name").val(),
                        amount = $(this).find("input.id").val();
                    total += parseFloat(amount);
            });

            $('#ttlAmount').val(total);
    });

</script>