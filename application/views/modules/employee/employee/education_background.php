<div class="modal fade" id="modal-education" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">New Educational Background</h4>
            </div>
            <div class="modal-body">
                <div class="table-row">
                    <table class="table-form">
                        <tbody>
                            <tr>
                                <td class="form-label">Level<span class="asterisk">*</span></td>
                                <td class="form-group form-input">
                                    <input type="text" class="form-control" name="level" id="level" title="Level" required>
                                </td>
                            </tr>
                            <tr>
                                <td class="form-label">Degree<span class="asterisk">*</span></td>
                                <td class="form-group form-input">
                                    <input type="text" class="form-control" name="degree" id="degree" title="Degree" required>
                                </td>
                            </tr>
                            <tr>
                                <td class="form-label">School<span class="asterisk">*</span></td>
                                <td class="form-group form-input">
                                    <input type="text" class="form-control" name="school" id="school" title="School" required>
                                </td>
                            </tr>
                            <tr>
                                <td class="form-label">Year Grad<span class="asterisk">*</span></td>
                                <td class="form-group form-input">
                                    <input type="text" class="form-control" name="yearGrad" id="yearGrad" title="Year Graduated" required>
                                </td>
                            </tr>
                            <tr>
                                <td class="form-label">Earned</td>
                                <td class="form-group form-input">
                                    <input type="text" class="form-control" name="levelEarn" id="levelEarn">
                                </td>
                            </tr>
                            <tr>
                                <td class="form-label">Awards/Honors</td>
                                <td class="form-group form-input">
                                    <input type="text" class="form-control" name="honors" id="honors">
                                </td>
                            </tr>
                            <tr>
                                <td class="form-label ">Remarks</td>
                                <td class="form-group form-input">
                                    <textarea class="form-control" id="remarks" value=""></textarea>
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="form-label align-text-top pt-5">Status</td>
                                <td class="form-group form-input">
                                    <select id="status" name="status" class="form-control" data-live-search="false" liveSearchNormalize="false">
                                        <option value="1">Graduated</option>
                                        <option value="2">Ongoing</option>
                                        <option value="3">Not Continued</option>
                                        <option value="4">Withdrawed</option>
                                        <option value="5">Dropout</option>
                                    </select>
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-raised pill" name="cmdSaveEducation" id="cmdSaveEducation">Save</button>
                <button type="button" class="btn btn-outline-danger btn-raised pill" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-education-edit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Educational Background</h4>
            </div>
            <div class="modal-body">
                <div class="table-row">
                    <input type="hidden" class="form-control" id="educationID">
                    <table class="table-form">
                        <tbody>
                            <tr>
                                <td class="form-label">Level<span class="asterisk">*</span></td>
                                <td class="form-group form-input">
                                    <input type="text" class="form-control" name="level" id="level" title="Level" required>
                                </td>
                            </tr>
                            <tr>
                                <td class="form-label">Degree<span class="asterisk">*</span></td>
                                <td class="form-group form-input">
                                    <input type="text" class="form-control" name="degree" id="degree" title="Degree" required>
                                </td>
                            </tr>
                            <tr>
                                <td class="form-label">School<span class="asterisk">*</span></td>
                                <td class="form-group form-input">
                                    <input type="text" class="form-control" name="school" id="school" title="School" required>
                                </td>
                            </tr>
                            <tr>
                                <td class="form-label">Year Grad<span class="asterisk">*</span></td>
                                <td class="form-group form-input">
                                    <input type="text" class="form-control" name="yearGrad" id="yearGrad" title="Year Graduated" required>
                                </td>
                            </tr>
                            <tr>
                                <td class="form-label">Earned</td>
                                <td class="form-group form-input">
                                    <input type="text" class="form-control" name="levelEarn" id="levelEarn">
                                </td>
                            </tr>
                            <tr>
                                <td class="form-label">Awards/Honors</td>
                                <td class="form-group form-input">
                                    <input type="text" class="form-control" name="honors" id="honors">
                                </td>
                            </tr>
                            <tr>
                                <td class="form-label ">Remarks</td>
                                <td class="form-group form-input">
                                    <textarea class="form-control" id="remarks" value=""></textarea>
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="form-label align-text-top pt-5">Status</td>
                                <td class="form-group form-input">
                                    <select id="status" name="status" class="form-control" data-live-search="false" liveSearchNormalize="false">
                                        <option value="1">Graduated</option>
                                        <option value="2">Ongoing</option>
                                        <option value="3">Not Continued</option>
                                        <option value="4">Withdrawed</option>
                                        <option value="5">Dropout</option>
                                    </select>
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <?php if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'Delete Existing Education Background')) {?>
                <button type="button" class="btn btn-warning btn-raised pill" id="cmdDeleteEducation">Delete</button>
                <?php }?>
                <?php if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'Edit Existing Education Background')) {?>
                <button type="button" class="btn btn-primary btn-raised pill" name="cmdUpdateEducation" id="cmdUpdateEducation">Update</button>
                <?php }?>
                <button type="button" class="btn btn-outline-danger btn-raised pill" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$('#cmdSaveEducation').click(function(){
    if (check_fields('#modal-education')) {
    	$(this).attr('disabled','disabled');
    	$(this).addClass('loader');
    	// submit
       	saveEducation();
    }
});

$('#cmdUpdateEducation').click(function(){
    if (check_fields('#modal-education-edit')) {
    	$(this).attr('disabled','disabled');
    	$(this).addClass('loader');
    	// submit
       	updateEducation();
    }
});

$('#cmdDeleteEducation').click(function(){
	$(this).attr('disabled','disabled');
	$(this).addClass('loader');
	// submit
   	deleteEducation();
});

function showEducation() {
    $.ajax({
        url: '<?php echo site_url('employee/show_educations')?>',
        data: { empID: '<?php echo $this->encrypter->encode($rec->empID)?>' },
        type: 'POST',
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                
                list = '';
                for (i = 0; i < response.records.length; i++) {
                    record = response.records[i];
                    
					list += '<tr class="row-education" data-id="'+record.educationID+'">';
					list += '	<td><span>'+record.level+'</span></td>';
					list += '	<td><span>'+record.yearGrad+'</span></td>';
					list += '	<td><span>'+record.degree+'</span></td>';
					list += '	<td><span>'+record.school+'</span></td>';
					list += '	<td><span>'+record.honors+'</span></td>';
					list += '</tr>';
                }

                $('#education tbody').html(list);
            } else if (response.status == '0') {
                if (response.message) {
                    alert(response.message);
                }
            } else {
                alert(response.message, 1);
            }
        }, error:function(xhr) {
        }
    });
}

function saveEducation() {
    var formData = new FormData();
    var fields   = ['level', 'degree', 'yearStart', 'yearGrad', 'school', 'levelEarn', 'honors', 'remarks', 'status'];

    for (i = 0; i < fields.length; i++) {
        formData.append(fields[i], $('#modal-education #'+fields[i]).val());
    }

    formData.append('empID', '<?php echo $this->encrypter->encode($rec->empID)?>');

    $.ajax({
        url: '<?php echo site_url('employee/save_education')?>',
        data: formData,
        type: 'POST',
        processData: false,
        contentType: false,
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                showEducation();
                clearFields('#modal-education');
                
                $('#modal-education').modal('hide');
                $('#cmdSaveEducation').prop('disabled',false);
            	$('#cmdSaveEducation').removeClass('loader');
            } else if (response.status == '0') {
                if (response.message) {
                    alert(response.message);
                }
            } else {
                alert(response.message, 1);
            }
        }, error:function(xhr) {
        }
    });
}

function updateEducation() {
    var formData = new FormData();
    var fields   = ['level', 'degree', 'yearStart', 'yearGrad', 'school', 'levelEarn', 'honors', 'remarks', 'status'];

    for (i = 0; i < fields.length; i++) {
        formData.append(fields[i], $('#modal-education-edit #'+fields[i]).val());
    }

    formData.append('educationID', $('#modal-education-edit #educationID').val());

    $.ajax({
        url: '<?php echo site_url('employee/update_education')?>',
        data: formData,
        type: 'POST',
        processData: false,
        contentType: false,
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                showEducation();
                clearFields('#modal-education-edit');
                
                $('#modal-education-edit').modal('hide');
                $('#cmdUpdateEducation').prop('disabled',false);
            	$('#cmdUpdateEducation').removeClass('loader');
            } else if (response.status == '0') {
                if (response.message) {
                    alert(response.message);
                }
            } else {
                alert(response.message, 1);
            }
        }, error:function(xhr) {
        }
    });
}

function viewEducation(educationID) {
    $.ajax({
        url: '<?php echo site_url('employee/view_education')?>',
        data: { educationID: educationID },
        type: 'POST',
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                record = response.record;
                
                $('#modal-education-edit #educationID').val(record.educationID);
                $('#modal-education-edit #level').val(record.level);
                $('#modal-education-edit #degree').val(record.degree);
                $('#modal-education-edit #school').val(record.school);
                $('#modal-education-edit #yearStart').val(record.yearStart).selectpicker('refresh');
                $('#modal-education-edit #yearGrad').val(record.yearGrad).selectpicker('refresh');
                $('#modal-education-edit #levelEarn').val(record.levelEarn);
                $('#modal-education-edit #honors').val(record.honors);
                $('#modal-education-edit #remarks').val(record.remarks);
                $('#modal-education-edit #status').val(record.status).selectpicker('refresh');
            } else if (response.status == '0') {
                if (response.message) {
                    alert(response.message);
                }
            } else {
                alert(response.message, 1);
            }
        }, error:function(xhr) {
        }
    });
}

function deleteEducation() {
    $.ajax({
        url: '<?php echo site_url('employee/delete_education')?>',
        data: { educationID: $('#modal-education-edit #educationID').val() },
        type: 'POST',
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                showEducation();
                clearFields('#modal-education-edit');
                
                $('#modal-education-edit').modal('hide');
                $('#cmdDeleteEducation').prop('disabled',false);
            	$('#cmdDeleteEducation').removeClass('loader');
            } else if (response.status == '0') {
                if (response.message) {
                    alert(response.message);
                }
            } else {
                alert(response.message, 1);
            }
        }, error:function(xhr) {
        }
    });
}
</script>