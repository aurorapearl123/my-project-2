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
								<a href="<?php echo site_url('order/edit/'.$this->encrypter->encode($rec->orderID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>
							</li>
							<?php } ?>
							<?php if ($roles['delete'] && !$in_used) { ?>
							<li>
								<button name="cmddelete" id="cmddelete" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" onclick="deleteRecord('<?php echo $this->encrypter->encode($rec->orderID); ?>');"><i class="la la-trash-o"></i></button>
							</li>
							<?php } ?>
							<?php if ($this->session->userdata('current_user')->isAdmin) { ?>
							<li>
								<button type="button" id="recordlog" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Record Logs" onclick="popUp('<?php echo site_url('logs/record_log/order_headers/orderID/'.$this->encrypter->encode($rec->orderID).'/Order') ?>', 1000, 500)"><i class="la la-server"></i></button>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>
                <div class="card-body">
                    <form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url("order/save") ?>">
                        <div class="table-row">
                            <table class="table-form column-3">
                                <tbody>
                                <tr>
                                    <td class="form-label" nowrap>
                                        <label for="config">Branch : </label>
                                    </td>
                                    <td class="form-group form-input" nowrap>
                                        <input type="text" class="border-0" name="branchName" id="branchName" title="Branch Name" value="<?php echo $rec->branchName;?>" readonly>
                                        <input type="hidden" class="form-control" name="branchID" id="branchID" title="Branch Id" value="<?php echo $branchID;?>" readonly>
                                        <input type="hidden" id="cloth_category_size" name="cloth_category_size" value="<?php echo count($clothes_categories)?>">
                                    </td>
                                    <td width="450px">

                                    </td>
                                    <td class="form-label" style="width:12%" nowrap>Date</td>
                                    <td class="form-group form-input" style="width:21.33%">
                                        <input type="text" class="border-0" name="date" id="date" title="Date" required value="<?php echo date('Y-m-d')?>" readonly>
                                    </td>
                                    <td class="d-xxl-none" colspan="3"></td>
                                </tr>
                                <tr>
                                    <td class="form-label" style="width:12%" nowrap>Customer Name : </td>

                                    <td class="form-group form-input" style="width:21.33%" nowrap>
                                        <input type="text" class="border-0" name="customerName" id="customerName" title="Branch Name" value="<?php echo $rec->fname .' '.$rec->mname. ' '.$rec->lname;?>" readonly>
                                    </td>
                                    <td>
                                    </td>
                                    <td class="form-label" style="width:12%" nowrap>is discounted <span class="asterisk">*</span></td>
                                    <td class="form-group form-input" style="width:21.33%">
                                        <div class="form-check">
                                            <input class="form-check-input position-static" type="checkbox" name="isDiscounted" id="isDiscounted" value="<?php if($rec->isDiscounted == 'Y'){
                                                echo "Y";
                                            }
                                            else {
                                                echo "N";
                                            };?>" aria-label="..." <?php if($rec->isDiscounted == 'Y') echo "checked='checked'";?> onclick="return false;">
                                        </div>
                                    </td>
                                    <td class="d-xxl-none"></td>
                                </tr>
                                <tr>
                                    <td class="form-label">Service </td>
                                    <td class="form-group form-input">
                                        <input type="text" class="border-0" name="service" id="service" title="Branch Name" value="<?php echo $rec->serviceType;?>" readonly>
                                        <input type="hidden" id="serviceID" name="serviceID" value="<?php echo $rec->serviceID; ?>">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <!-- start create header details -->
                            <?php
                            $table_str="<table border='1' class='table table-sm'>";
                            $table_str.='<tr>';
                            $table_str.='<th>'.'Quantity'.'</th>';
                            $table_str.='<th>'.'Clothes Category'.'</th>';
                            $table_str.='<tr>';
                            $i = 1;
                            foreach ($clothes_categories as $rows) {
                                $table_str.='<tr>';
                                $table_str.='<td style="width: 100px" align="left">'.'<input type="number" min="1" name="clothes_qtys[]" value="'.$rows->qty.'" class="border-0" readonly >'.'</td>';
                                $table_str.='<td>'.$rows->category.'</td>';
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
                                    <label for="config">Rate :  <span class="asterisk">*</span></label>
                                </td>
                                <td class="form-group form-input" nowrap>
                                    <input type="text" class="form-control" name="rate" id="rate" title="Branch Name" value="<?php echo $rec->rate;?>" readonly required>
                                </td>
                                <td width="450px">

                                </td>
                                <td class="form-label" style="width:12%" nowrap>Quantity in kilos : </td>
                                <td class="form-group form-input" style="width:21.33%">
                                    <input type="number" class="form-control" name="qty" id="qty" min="0" title="Quantity in kilos" value="<?php echo $rec->qty;?>" readonly>
                                </td>
                                <td class="d-xxl-none" colspan="3"></td>
                            </tr>
                            <tr>
                                <td class="form-label" style="width:12%" nowrap>Sub total:  <span class="asterisk">*</span></td>
                                <td class="form-group form-input" style="width:21.33%" nowrap>
                                    <input type="number" class="form-control" name="subtotal" id="subtotal" min="0" title="Company Abbr" readonly required>
                                </td>
                                <td>

                                </td>
                                <td class="form-label" style="width:12%" nowrap>Deliver Fee:  <span class="asterisk">*</span></td>
                                <td class="form-group form-input" style="width:21.33%" nowrap>
                                    <input type="text" class="form-control" name="deliveryFee" id="deliveryFee" title="Delivery Fee"  value="<?php echo $rec->deliveryFee;?>" readonly>
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

                        <div class="form-sepator solid"></div>
                        <div class="form-group mb-0">
                            <table class="table-form column-3">
                                <tbody>
                                    <tr>
                                        <td class="form-label">Prepared by : </td>
                                        <td class="form-group form-input">
                                            <span><?php echo $rec->firstName .' '.$rec->middleName.' '.$rec->lastName;?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="form-label">Date washed : </td>
                                        <td class="form-group form-input">
                                            <span><?php echo $rec->dateCreated;?></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="form-label">Date Fold : </td>
                                        <td class="form-group form-input">
                                            <span><?php echo $rec->dateCreated;?></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="form-label">Date Ready : </td>
                                        <td class="form-group form-input">
                                            <span><?php echo $rec->dateCreated;?></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="form-label">Cancelled by : </td>
                                        <td class="form-group form-input">
                                            <span><?php echo $rec->firstName .' '.$rec->middleName.' '.$rec->lastName;?></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="form-label">Date Released : </td>
                                        <td class="form-group form-input">
                                            <span><?php echo $rec->dateCreated;?></span>
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
<script>
    $(document).ready(function(){
        var rate = parseInt($('#rate').val());
        var quantity_kilo = parseInt($('#qty').val());
        var total = quantity_kilo + rate;

        $('#subtotal').val(total);

        var subtotal = parseInt($('#subtotal').val());
        var deliveryFee = parseInt($('#deliveryFee').val());
        var total = subtotal + deliveryFee;

        $('#ttlAmount').val(total);
    });

</script>