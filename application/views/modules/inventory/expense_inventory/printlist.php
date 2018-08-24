<table width="100%" cellspacing="0" cellpadding="5" style="font-size: 12px;">
	<tr style="font-weight: bold;font-size: 11px;">
		<td class="top left">REFERENCE NO</td>
		<td class="top left">DATE</td>
		<td class="top left">TOTAL AMOUNT</td>
		<td class="top left">REMARKS</td>
		<td class="top left right" align="center">STATUS</td>
	</tr>
	<?php 
		foreach($records as $row) {
		?>
	<tr>
		<td class="top left"><?php echo $row->expNo ?></td>
		<td class="top left"><?php echo $row->date ?></td>
		<td class="top left"><?php echo $row->ttlAmount ?></td>
		<td class="top left"><?php echo $row->remarks ?></td>		
		<td class="top left right" align="center">
			<?php 
				if ($row->status == 0) {
					echo "<font color='red'>Cancelled</font>";
				} else if ($row->status == 1) {
					echo "<font color='orange'>Pending</font>";
				} else if ($row->status == 2) {
					echo "<font color='green'>Confirmed</font>";
				}
				?>
		</td>
	</tr>
	<?php 
		}
		?>
	<tr style="background-color: #ffffff;">
		<td class="top" colspan="5">&nbsp;</td>
	</tr>
</table>
