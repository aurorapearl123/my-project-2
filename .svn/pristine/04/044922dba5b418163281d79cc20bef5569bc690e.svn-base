<table width="100%" cellspacing="0" cellpadding="5" style="font-size: 12px;">
	<tr style="font-weight: bold;font-size: 11px;">
		<td class="top left">CODE</td>
		<td class="top left">SECTION NAME</td>
		<td class="top left">DEPARTMENT</td>
		<td class="top left">BRANCH</td>
		<td class="top left">EMAIL</td>
		<td class="top left">HEAD</td>
		<td class="top left right" align="center">STATUS</td>
	</tr>
	<?php 
		foreach($records as $row) {
		?>
	<tr>
		<td class="top left"><?php echo $row->divisionCode ?></td>
		<td class="top left"><?php echo $row->divisionName ?></td>
		<td class="top left"><?php echo $row->deptName ?></td>
		<td class="top left"><?php echo $row->branchName ?></td>
		<td class="top left"><?php echo $row->divisionEmail ?></td>
		<td class="top left"><?php echo $row->lname." , ".$row->fname ?></td>
		<td class="top left right" align="center">
			<?php 
				if ($row->status == 1) {
					echo "<font color='green'>Active</font>";
				} else {
					echo "<font color='red'>Inactive</font>";
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
