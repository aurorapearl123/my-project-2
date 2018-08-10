<table width="100%" cellspacing="0" cellpadding="5" style="font-size: 12px;">
	<tr style="font-weight: bold;font-size: 11px;">
		<td class="top left">TYPE</td>
		<td class="top left">SALARY TYPE</td>
		<td class="top left">DEFAULT SALARY</td>
		<td class="top left">WORKING DAYS</td>
		<td class="top left">STATUS</td>
		<td class="top left right" align="center">RANK</td>
	</tr>
	<?php 
	foreach($records as $row) {
		?>
		<tr>
			<td class="top left"><?php echo $row->employeeType ?></td>
			<td class="top left">
				<?php 
				if ($row->salaryType=="1") {
					echo "Monthly";
				} elseif ($row->salaryType=="2") {
					echo "Daily";
				} elseif ($row->salaryType=="3") {
					echo "Hourly";
				}?>
			</td>
			<td class="top left"><?php echo ($row->defaultSalary > 0) ? number_format($row->defaultSalary, 2) : "--"; ?></td>
			<td class="top left"><?php echo ($row->workingDays > 0) ? $row->workingDays : "--" ?></td>
			<td class="top left">
				<?php
				if ($row->status=="1") {
					echo "<span class='badge badge-pill badge-success'>Active</span>"; 
				} else if ($row->status=="0") {
					echo "<span class='badge badge-pill badge-danger'>Inactive</span>"; 
				}?>
			</td>
			<td class="top left right" align="center"><?php echo $row->rank ?></td>
		</tr>
		<?php 
	}
	?>
	<tr style="background-color: #ffffff;">
		<td class="top" colspan="6">&nbsp;</td>
	</tr>
</table>
