<table width="100%" cellspacing="0" cellpadding="5" style="font-size: 12px;">
	<tr style="font-weight: bold;font-size: 11px;">
		<td class="top left">BRANCH NAME</td>
		<td class="top left">BRAND</td>
		<td class="top left">ITEM</td>
		<td class="top left">DESCRIPTION</td>
		<td class="top left">UMSR</td>		
		<td class="top left">QTY</td>		
		<td class="top left">REORDER LEVEL</td>		
		
	</tr>
	<?php 
		foreach ($records as $row) {
		?>
	<tr>
		<td class="top left"><?php echo $row->branchName ?></td>
		<td class="top left"><?php echo $row->brand ?></td>
		<td class="top left"><?php echo $row->item ?></td>
		<td class="top left"><?php echo $row->description ?></td>
		<td class="top left"><?php echo $row->umsr ?></td>
		<td class="top left"><?php echo $row->qty ?></td>
		<td class="top left"><?php echo $row->reorderlvl ?></td>
	</tr>
	<?php 
		}
		?>
	<tr style="background-color: #ffffff;">
		<td class="top" colspan="7">&nbsp;</td>
	</tr>
</table>
