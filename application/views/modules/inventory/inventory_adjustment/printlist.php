<table width="100%" cellspacing="0" cellpadding="5" style="font-size: 12px;">
	<tr style="font-weight: bold;font-size: 11px;">
		<td class="top left">DATE</td>
		<td class="top left">BRANCH NAME</td>
		<td class="top left">BRAND</td>
		<td class="top left">ITEM</td>
		<td class="top left">DESCRIPTION</td>
		<td class="top left">UMSR</td>
		<td class="top left">ADJUSTMENT TYPE</td>
		<td class="top left">QTY</td>
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
		<td class="top left"><?php echo $row->item ?></td>
		<td class="top left"><?php echo $row->description ?></td>
		<td class="top left"><?php echo $row->umsr ?></td>
		<td class="top left"><?php echo $row->adjType ?></td>
		<td class="top left"><?php echo $row->qty ?></td>
		<td class="top left"><?php echo $row->remarks ?></td>		
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
		<td class="top" colspan="6">&nbsp;</td>
	</tr>
</table>
