<div class="modal fade" id="modal-experience" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">New Work Experience</h4>
            </div>
            <div class="modal-body">
                <div class="table-row">
                    <table class="table-form">
                        <tr>
                            <td class="form-label">Type<span class="asterisk">*</span></td>
                            <td class="form-group form-input">
                                <select id="type" name="type" class="form-control" data-live-search="false" liveSearchNormalize="false" title="Type" required>
                                    <option value="1">Position</option>
                                    <option value="2">Unit</option>
                                    <option value="3">Location</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="form-label">Designation<span class="asterisk">*</span></td>
                            <td class="form-group form-input"><input type="text" class="form-control" name="designation" id="designation" title="Designation" required></td>
                        </tr>
                        <tr>
                            <td class="form-label">Company<span class="asterisk">*</span></td>
                            <td class="form-group form-input"><input type="text" class="form-control" name="company" id="company" title="Company" required></td>
                        </tr>
                        <tr>
                            <td class="form-label">Employment<span class="asterisk">*</span></td>
                            <td class="form-group form-input"><input type="text" class="form-control" name="employment" id="employment" title="Employment" required></td>
                        </tr>
                        <tr>
                            <td class="form-label">Salary Type</td>
                            <td class="form-group form-input">
                                <select id="salaryType" name="type" class="form-control" data-live-search="false" liveSearchNormalize="false">
                                    <option value="1">Monthly</option>
                                    <option value="2">Daily</option>
                                    <option value="3">Hourly</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="form-label">Basic Salary</td>
                            <td class="form-group form-input"><input type="text" class="form-control" name="basicSalary" id="basicSalary"></td>
                        </tr>
                        <tr>
                            <td class="form-label">Start Date</td>
                            <td class="form-group form-input"><input type="text" class="form-control datepicker" name="startDate" id="startDate" data-toggle="datetimepicker" data-target="#startDate"></td>
                        </tr>
                        <tr>
                            <td class="form-label">End Date</td>
                            <td class="form-group form-input"><input type="text" class="form-control datepicker" name="endDate" id="endDate" data-toggle="datetimepicker" data-target="#endDate"></td>
                        </tr>
                        <tr>
                            <td class="form-label ">Remarks</td>
                            <td class="form-group form-input"><textarea class="form-control" id="remarks" value=""></textarea></td>
                            <td>&nbsp;</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-raised pill" name="cmdSaveExperience" id="cmdSaveExperience">Save</button>
                <button type="button" class="btn btn-outline-danger btn-raised pill" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-experience-edit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Work Experience</h4>
            </div>
            <div class="modal-body">
                <div class="table-row">
                    <input type="hidden" class="form-control" id="eexperienceID">
                    <table class="table-form">
                        <tbody>
                            <tr>
                                <td class="form-label">Type<span class="asterisk">*</span></td>
                                <td class="form-group form-input">
                                    <select id="etype" name="etype" class="form-control" data-live-search="false" liveSearchNormalize="false" title="Type" required>
                                        <option value="1">Position</option>
                                        <option value="2">Unit</option>
                                        <option value="3">Location</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="form-label">Designation<span class="asterisk">*</span></td>
                                <td class="form-group form-input"><input type="text" class="form-control" name="edesignation" id="edesignation" title="Designation" required></td>
                            </tr>
                            <tr>
                                <td class="form-label">Company<span class="asterisk">*</span></td>
                                <td class="form-group form-input"><input type="text" class="form-control" name="ecompany" id="ecompany" title="Company" required></td>
                            </tr>
                            <tr>
                                <td class="form-label">Employment<span class="asterisk">*</span></td>
                                <td class="form-group form-input"><input type="text" class="form-control" name="eemployment" id="eemployment" title="Employment" required></td>
                            </tr>
                            <tr>
                                <td class="form-label">Salary Type</td>
                                <td class="form-group form-input">
                                    <select id="esalaryType" name="esalaryType" class="form-control" data-live-search="false" liveSearchNormalize="false">
                                        <option value="1">Monthly</option>
                                        <option value="2">Daily</option>
                                        <option value="3">Hourly</option>
                                    </select>
                                 </td>
                            </tr>
                            <tr>
                                <td class="form-label">Basic Salary</td>
                                <td class="form-group form-input"><input type="text" class="form-control" name="ebasicSalary" id="ebasicSalary"></td>
                            </tr>
                            <tr>
                                <td class="form-label">Start Date</td>
                                <td class="form-group form-input"><input type="text" class="form-control datepicker" name="estartDate" id="estartDate" data-toggle="datetimepicker" data-target="#estartDate"></td>
                            </tr>
                            <tr>
                                <td class="form-label">End Date</td>
                                <td class="form-group form-input"><input type="text" class="form-control datepicker" name="eendDate" id="eendDate" data-toggle="datetimepicker" data-target="#eendDate"></td>
                            </tr>
                            <tr>
                                <td class="form-label ">Remarks</td>
                                <td class="form-group form-input"><textarea class="form-control" id="eremarks" value=""></textarea></td>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <?php if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'Delete Existing Work Experience')) {?>
                <button type="button" class="btn btn-warning btn-raised pill" id="cmdDeleteExperience">Delete</button>
                <?php }?>
                <?php if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'Edit Existing Edit Experience')) {?>
                <button type="button" class="btn btn-primary btn-raised pill" name="cmdUpdateExperience" id="cmdUpdateExperience">Update</button>
                <?php }?>
                <button type="button" class="btn btn-outline-danger btn-raised pill" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$('#cmdSaveExperience').click(function(){
    if (check_fields('#modal-experience')) {
    	$(this).attr('disabled','disabled');
    	$(this).addClass('loader');
    	// submit
       	saveExperience();
    }
});

$('#cmdUpdateExperience').click(function(){
    if (check_fields('#modal-experience-edit')) {
    	$(this).attr('disabled','disabled');
    	$(this).addClass('loader');
    	// submit
       	updateExperience();
    }
});

$('#cmdDeleteExperience').click(function(){
	$(this).attr('disabled','disabled');
	$(this).addClass('loader');
	// submit
   	deleteExperience();
});

function showExperiences() {
    $.ajax({
        url: '<?php echo site_url('employee/show_experiences')?>',
        data: { empID: '<?php echo $this->encrypter->encode($rec->empID)?>' },
        type: 'POST',
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                
                list = '';
                for (i = 0; i < response.records.length; i++) {
                    record = response.records[i];
                    
					list += '<tr class="row-experience" data-id="'+record.workID+'">';
					list += '	<td><span>'+record.company+'</span></td>';
					list += '	<td><span>'+record.designation+'</span></td>';
					list += '	<td><span>'+record.basicSalary+'</span></td>';
					list += '	<td><span>'+record.startDate+' - '+record.endDate+'</span></td>';
					if (record.salaryType == '1') {
					    list += '	<td><span>Monthly</span></td>';
					} else if (record.salaryType == '2') {
					    list += '	<td><span>Daily</span></td>';
					} else if (record.salaryType == '3') {
					    list += '	<td><span>Hourly</span></td>';
					}
					list += '</tr>';
                }

                $('#experiences tbody').html(list);
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

function saveExperience() {
    var formData = new FormData();
    var fields   = ['type', 'designation', 'company', 'employment', 'basicSalary', 'salaryType', 'startDate', 'endDate', 'remarks'];

    for (i = 0; i < fields.length; i++) {
        formData.append(fields[i], $('#modal-experience #'+fields[i]).val());
    }

    formData.append('empID', '<?php echo $this->encrypter->encode($rec->empID)?>');

    $.ajax({
        url: '<?php echo site_url('employee/save_experience')?>',
        data: formData,
        type: 'POST',
        processData: false,
        contentType: false,
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                showExperiences();
                clearFields('#modal-experience');
                
                $('#modal-experience').modal('hide');
                $('#cmdSaveExperience').prop('disabled',false);
            	$('#cmdSaveExperience').removeClass('loader');
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

function viewExperience(experienceID) {
    $.ajax({
        url: '<?php echo site_url('employee/view_experience')?>',
        data: { workID: experienceID },
        type: 'POST',
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                record = response.record;
                
                $('#modal-experience-edit #eexperienceID').val(record.workID);
                $('#modal-experience-edit #etype').val(record.type).selectpicker('refresh');
                $('#modal-experience-edit #edesignation').val(record.designation);
                $('#modal-experience-edit #ecompany').val(record.company);
                $('#modal-experience-edit #eemployment').val(record.employment);
                $('#modal-experience-edit #esalaryType').val(record.salaryType).selectpicker('refresh');
                $('#modal-experience-edit #ebasicSalary').val(record.basicSalary);
                $('#modal-experience-edit #eremarks').val(record.remarks);
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

function updateExperience() {
    var formData = new FormData();
    var fields   = ['type', 'designation', 'company', 'employment', 'basicSalary', 'salaryType', 'startDate', 'endDate', 'remarks'];

    for (i = 0; i < fields.length; i++) {
        formData.append(fields[i], $('#modal-experience-edit #e'+fields[i]).val());
    }

    formData.append('workID', $('#modal-experience-edit #eexperienceID').val());

    $.ajax({
        url: '<?php echo site_url('employee/update_experience')?>',
        data: formData,
        type: 'POST',
        processData: false,
        contentType: false,
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                showExperiences();
                clearFields('#modal-experience-edit');
                
                $('#modal-experience-edit').modal('hide');
                $('#cmdUpdateExperience').prop('disabled',false);
            	$('#cmdUpdateExperience').removeClass('loader');
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

function deleteExperience() {
    $.ajax({
        url: '<?php echo site_url('employee/delete_experience')?>',
        data: { workID: $('#modal-experience-edit #eexperienceID').val() },
        type: 'POST',
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                showExperiences();
                clearFields('#modal-experience-edit');
                
                $('#modal-experience-edit').modal('hide');
                $('#cmdDeleteExperience').prop('disabled',false);
            	$('#cmdDeleteExperience').removeClass('loader');
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