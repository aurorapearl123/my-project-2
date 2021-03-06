<table width="100%" cellspacing="0" cellpadding="5" style="font-size: 12px;">
	<tr style="font-weight: bold;font-size: 11px;">
		<td class="top left">DATE</td>
		<td class="top left">BRANCH NAME</td>
		<td class="top left">BRAND</td>
		<td class="top left">MODEL</td>
		<td class="top left">SERIAL NO</td>
		<td class="top left">NAME</td>
		<td class="top left">DESCRIPTION</td>
		<td class="top left">REMARKS</td>
		<td class="top left right" align="center">STATUS</td>
	</tr>
	<?php 
		foreach ($records as $row) {
		?>
	<tr>
		<td class="top left"><?php echo $row->date ?></td>
		<td class="top left"><?php echo $row->branchName ?></td>
		<td class="top left"><?php echo $row->brand ?></td>
		<td class="top left"><?php echo $row->model ?></td>
		<td class="top left"><?php echo $row->serialNo ?></td>
		<td class="top left"><?php echo $row->name ?></td>
		<td class="top left"><?php echo $row->description ?></td>
		<td class="top left"><?php echo $row->remarks ?></td>		
		<td class="top left right" align="center">
			<?php 
				if ($row->status == 1) {
					echo "<font color='orange'>Pending</font>";
				} else if ($row->status == 2) {
					echo "<font color='green'>Confirmed</font>";
				} else if ($row->status == 0) {
					echo "<font color='red'>Cancelled</font>";
				}
			?>
		</td>
	</tr>
	<?php 
		}
		?>
	<tr style="background-color: #ffffff;">
		<td class="top" colspan="9">&nbsp;</td>
	</tr>
</table>
