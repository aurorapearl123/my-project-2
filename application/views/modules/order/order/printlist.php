<table width="100%" cellspacing="0" cellpadding="5" style="font-size: 12px;">
	<tr style="font-weight: bold;font-size: 11px;">
		<td class="top left">COMPANY CODE</td>
		<td class="top left">COMPANY NAME</td>
		<td class="top left">EMAIL</td>
		<td class="top left">ADDRESS</td>
		<td class="top left">HEAD</td>
		<td class="top left right" align="center">STATUS</td>
	</tr>
	<?php 
		foreach ($records as $row) {
		?>
	<tr>
		<td class="top left"><?php echo $row->companyCode ?></td>
		<td class="top left"><?php echo $row->companyName ?></td>
		<td class="top left"><?php echo $row->companyEmail ?></td>
		<td class="top left"><?php echo $row->companyAddress ?></td>
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
		<td class="top" colspan="6">&nbsp;</td>
	</tr>
</table>
