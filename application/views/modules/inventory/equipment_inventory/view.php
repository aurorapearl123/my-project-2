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
							<?php if ($roles['edit']) { ?>
							<li>
								<a href="<?php echo site_url('inventory_adjustment/edit/'.$this->encrypter->encode($rec->adjID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>
							</li>
							<?php } ?>
							<?php if ($roles['delete'] && !$in_used) { ?>
							<li>
								<button name="cmddelete" id="cmddelete" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" onclick="deleteRecord('<?php echo $this->encrypter->encode($rec->adjID); ?>');"><i class="la la-trash-o"></i></button>
							</li>
							<?php } ?>
							<?php if ($this->session->userdata('current_user')->isAdmin) { ?>
							<li>
								<button type="button" id="recordlog" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Record Logs" onclick="popUp('<?php echo site_url('logs/record_log/inventory_adjustment/adjID/'.$this->encrypter->encode($rec->adjID).'/adjustment') ?>', 1000, 500)"><i class="la la-server"></i></button>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>
                <div class="card-body">
                    <form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url("inventory_adjustment/save") ?>">
                        <div class="table-row">
                            <table class="table-form column-3">
                                <tbody>
                                    <tr>
                                        <td class="data-title" style="width:120px" nowrap>Branch:</td>
                                        <td class="data-input" style="width:300px" nowrap><?php echo $rec->branchName; ?></td>
                                        <td class="data-title" style="width:160px" nowrap>Date:</td>
                                        <td class="data-input" style="width:300px" nowrap><?php echo date('M d, Y',strtotime($rec->date))?></td>
                                        <td class="data-title" style="width:150px" nowrap>Created By:</td>
                                        <td class="data-input" style="width:300px" nowrap><?php echo $rec->createdByFirst. ' '.$rec->createdByMiddle.' '.$rec->createdByLast ?></td>
                                    </tr>
                                    

                                    <tr>
                                        <td class="data-title" nowrap>Brand:</td>
                                        <td class="data-input"><?php echo $rec->brand; ?></td>

                                        <td class="data-title" style="width:120px" nowrap>Item</td>
                                        <td class="data-input" style="width:420px" nowrap><?php echo $rec->item; ?></td>
                                        
                                        <td class="data-title" style="width:150px" nowrap>Confirmed By:</td>
                                        <td class="data-input" style="width:300px" nowrap><?php echo $rec->confirmedByFirst. ' '.$rec->confirmedByMiddle.' '.$rec->confirmedByLast ?></td>
                                    </tr>
                                    <tr>
                                        <td class="data-title" nowrap>Description:</td>
                                        <td class="data-input"><?php echo $rec->description; ?></td>
                                    </tr>

                                    <tr>
                                        <td class="data-title" nowrap>UMSR:</td>
                                        <td class="data-input"><?php echo $rec->umsr; ?></td>

                                        <td class="data-title" nowrap>QTY:</td>
                                        <td class="data-input"><?php echo $rec->qty; ?></td>
                                    </tr>

                                    <tr>
                                        <td class="data-title" nowrap>Remarks:</td>
                                        <td class="data-input"><?php echo $rec->remarks; ?></td>
                                        <td class="data-title" nowrap></td>
                                        <td class="data-title" nowrap></td>

                                        <td class="data-title" nowrap>Cancelled By:</td>
                                        <td class="data-input" style="width:300px" nowrap><?php echo $rec->cancelledByFirst. ' '.$rec->cancelledByMiddle.' '.$rec->cancelledByLast ?></td>

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