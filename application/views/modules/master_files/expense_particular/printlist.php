<table width="100%" cellspacing="0" cellpadding="5" style="font-size: 12px;">
	<tr style="font-weight: bold;font-size: 11px;">
		<td class="top left">BRAND</td>
		<td class="top left">MODEL</td>
		<td class="top left">SERIAL NO</td>
		<td class="top left">NAME</td>
		<td class="top left">DESCRIPTION</td>
		<td class="top left right" align="center">STATUS</td>
	</tr>
	<?php 
		foreach($records as $row) {
		?>
	<tr>
		<td class="top left"><?php echo $row->brand ?></td>
		<td class="top left"><?php echo $row->model ?></td>
		<td class="top left"><?php echo $row->serialNo ?></td>
		<td class="top left"><?php echo $row->name ?></td>
		<td class="top left"><?php echo $row->description ?></td>
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
