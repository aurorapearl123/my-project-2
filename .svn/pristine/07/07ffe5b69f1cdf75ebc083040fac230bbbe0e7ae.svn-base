 <table width="100%" cellspacing="0" cellpadding="5" style="font-size: 12px;">
 <tr style="font-weight: bold;font-size: 11px;">
    <td class="top left">DATE HIRED</td>
    <td class="top left">EMP NO.</td>
    <td class="top left">EMPLOYEE</td>
    <td class="top left">BRANCH</td>
    <td class="top left">DEPT</td>
    <td class="top left">SECTION</td>
    <td class="top left">EMPLOYMENT</td>
    <td class="top left">POSITION</td>
    <td class="top left right" align="center">STATUS</td>
 </tr>
<?php 
        foreach($records as $row) {
?>
        <tr>
            <td class="top left"><?php echo date('m/d/Y', strtotime($row->dateAppointed)) ?></td>
            <td class="top left"><?php echo $row->employmentNo ?></td>
            <td class="top left"><?php echo $row->lname.", ".$row->fname." ".$row->mname." ".$row->suffix ?></td>
            <td class="top left"><?php echo $row->branchCode ?></td>
            <td class="top left"><?php echo $row->deptCode ?></td>
            <td class="top left"><?php echo $row->divisionCode ?></td>
            <td class="top left"><?php echo $row->employeeType ?></td>
            <td class="top left"><?php echo $row->jobTitle ?></td>
            <td class="top left right" align="center">
                <?php 
    				 switch ($row->status) {
    					case "1" : echo "<font color='green'>Active</font>"; break;
    					case "2" : echo "<font color='blue'>Re-assigned</font>"; break;
    					case "3" : echo "<font color='green'>Promoted</font>"; break;
    					case "4" : echo "<font color='blue'>Demoted</font>"; break;
    					case "5" : echo "<font color='red'>Terminated</font>"; break;
    					case "0" : echo "<font color='red'>Inactive</font>"; break;
    				}?>
            </td>
        </tr>
<?php 
        }
?>
<tr style="background-color: #ffffff;">
    <td class="top" colspan="9">&nbsp;</td>
</tr>
</table>