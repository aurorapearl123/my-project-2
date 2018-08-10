<table width="100%" cellspacing="0" cellpadding="5" style="font-size: 12px;">
	<tr style="font-weight: bold;font-size: 11px;">
		<td class="top left">POSITION NO</td>
		<td class="top left">JOB TITLE</td>
		<td class="top left">BRANCH</td>
		<td class="top left">DEPARTMENT</td>
		<td class="top left">SECTION</td>
		<td class="top left right" align="center">STATUS</td>
	</tr>
	<?php 
		foreach($records as $row) {
		?>
	<tr>
		<td class="top left"><?php echo $row->positionCode ?></td>
		<td class="top left"><?php echo $row->jobTitle ?></td>
		<td class="top left"><?php echo $row->branchName ?></td>
		<td class="top left"><?php echo $row->deptName ?></td>
		<td class="top left"><?php echo $row->divisionName ?></td>
		<td class="top left right" align="center">
			<?php 
				if ($row->status == 1) {
					echo "<span class='badge badge-pill badge-success'>Vacant</span>";
				} else if($row->status == 2){
					echo "<span class='badge badge-pill badge-primary'>Occupied</span>";
				} else if($row->status == 0){
					echo "<span class='badge badge-pill badge-danger'>Inactive</span>";
				}
				?>
		</td>
	</tr>
	<?php 
		}
		?>
	<tr style="background-color: #ffffff;">
		<td class="top" colspan="7">&nbsp;</td>
	</tr>
</table>
