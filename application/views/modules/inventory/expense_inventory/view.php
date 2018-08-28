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
							<h4 class="head-text">View <?php echo $current_module['module_label'] ?></h4>
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
					</div>
				</div>
			</div>
		</div>
	</div>
</div>