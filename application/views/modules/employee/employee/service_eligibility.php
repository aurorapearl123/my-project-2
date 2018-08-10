<div class="modal fade" id="modal-eligibility" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">New Service Eligibility</h4>
            </div>
            <div class="modal-body">
                <div class="table-row">
                    <table class="table-form">
                        <tr>
                            <td class="form-label">Eligibility<span class="asterisk">*</span></td>
                            <td class="form-group form-input"><input type="text" class="form-control" name="eligibility" id="eligibility" title="Eligibility" required></td>
                        </tr>
                        <tr>
                            <td class="form-label">Rating</td>
                            <td class="form-group form-input"><input type="text" class="form-control" name="rating" id="rating"></td>
                        </tr>
                        <tr>
                            <td class="form-label">Exam Date</td>
                            <td class="form-group form-input"><input type="text" class="form-control datepicker" name="examDate" id="examDate" data-toggle="datetimepicker" data-target="#examDate"></td>
                        </tr>
                        <tr>
                            <td class="form-label">Exam Place</td>
                            <td class="form-group form-input"><input type="text" class="form-control" name="examPlace" id="examPlace"></td>
                        </tr>
                        <tr>
                            <td class="form-label">License No.</td>
                            <td class="form-group form-input"><input type="text" class="form-control" name="licenseNo" id="licenseNo"></td>
                        </tr>
                        <tr>
                            <td class="form-label">Date Licensed</td>
                            <td class="form-group form-input"><input type="text" class="form-control datepicker" name="dateLicense" id="dateLicense" data-toggle="datetimepicker" data-target="#dateLicense"></td>
                        </tr>
                        <tr>
                            <td class="form-label">Date Expired</td>
                            <td class="form-group form-input"><input type="text" class="form-control datepicker" name="dateExpired" id="dateExpired" data-toggle="datetimepicker" data-target="#dateExpired"></td>
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
                <button type="submit" class="btn btn-primary btn-raised pill" name="cmdSaveEligibility" id="cmdSaveEligibility">Save</button>
                <button type="button" class="btn btn-outline-danger btn-raised pill" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-eligibility-edit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Service Eligibility</h4>
            </div>
            <div class="modal-body">
                <div class="table-row">
                    <input type="hidden" class="form-control" id="eeligibilityID">
                    <table class="table-form">
                        <tbody>
                            <tr>
                                <td class="form-label">Eligibility<span class="asterisk">*</span></td>
                                <td class="form-group form-input"><input type="text" class="form-control" name="eeligibility" id="eeligibility" title="Eligibility" required></td>
                            </tr>
                            <tr>
                                <td class="form-label">Rating</td>
                                <td class="form-group form-input"><input type="text" class="form-control" name="erating" id="erating"></td>
                            </tr>
                            <tr>
                                <td class="form-label">Exam Date</td>
                                <td class="form-group form-input"><input type="text" class="form-control datepicker" name="eexamDate" id="eexamDate" data-toggle="datetimepicker" data-target="#eexamDate"></td>
                            </tr>
                            <tr>
                                <td class="form-label">Exam Place</td>
                                <td class="form-group form-input"><input type="text" class="form-control" name="eexamPlace" id="eexamPlace"></td>
                            </tr>
                            <tr>
                                <td class="form-label">License No.</td>
                                <td class="form-group form-input"><input type="text" class="form-control" name="elicenseNo" id="elicenseNo"></td>
                            </tr>
                            <tr>
                                <td class="form-label">Date Licensed</td>
                                <td class="form-group form-input"><input type="text" class="form-control datepicker" name="edateLicense" id="edateLicense" data-toggle="datetimepicker" data-target="#edateLicense"></td>
                            </tr>
                            <tr>
                                <td class="form-label">Date Expired</td>
                                <td class="form-group form-input"><input type="text" class="form-control datepicker" name="edateExpired" id="edateExpired"  data-toggle="datetimepicker" data-target="#edateExpired"></td>
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
                <?php if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'Delete Existing Service Eligibility')) {?>
                <button type="button" class="btn btn-warning btn-raised pill" id="cmdDeleteEligibility">Delete</button>
                <?php }?>
                <?php if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'Edit Existing Service Eligibility')) {?>
                <button type="button" class="btn btn-primary btn-raised pill" name="cmdUpdateEligibility" id="cmdUpdateEligibility">Update</button>
                <?php }?>
                <button type="button" class="btn btn-outline-danger btn-raised pill" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$('#cmdSaveEligibility').click(function(){
    if (check_fields('#modal-eligibility')) {
    	$(this).attr('disabled','disabled');
    	$(this).addClass('loader');
    	// submit
       	saveEligibility();
    }
});

$('#cmdUpdateEligibility').click(function(){
    if (check_fields('#modal-eligibility-edit')) {
    	$(this).attr('disabled','disabled');
    	$(this).addClass('loader');
    	// submit
       	updateEligibility();
    }
});

$('#cmdDeleteEligibility').click(function(){
	$(this).attr('disabled','disabled');
	$(this).addClass('loader');
	// submit
   	deleteEligibility();
});

function showEligibilities() {
    $.ajax({
        url: '<?php echo site_url('employee/show_eligibilities')?>',
        data: { empID: '<?php echo $this->encrypter->encode($rec->empID)?>' },
        type: 'POST',
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                
                list = '';
                for (i = 0; i < response.records.length; i++) {
                    record = response.records[i];
                    
					list += '<tr class="row-eligibility" data-id="'+record.eligibilityID+'">';
					list += '	<td><span>'+record.eligibility+'</span></td>';
					list += '	<td><span>'+record.rating+'</span></td>';
					list += '	<td><span>'+((record.examDate != '00/00/0000') ? record.examDate : '')+'</span></td>';
					list += '	<td><span>'+record.examPlace+'</span></td>';
					list += '	<td><span>'+record.licenseNo+'</span></td>';
					list += '	<td><span>'+((record.dateLicense != '00/00/0000') ? record.dateLicense : '')+'</span></td>';
					list += '	<td><span>'+((record.dateExpired != '00/00/0000') ? record.dateExpired : '')+'</span></td>';
					list += '</tr>';
                }

                $('#eligibilities tbody').html(list);
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

function saveEligibility() {
    var formData = new FormData();
    var fields   = ['eligibility', 'rating', 'examDate', 'examPlace', 'licenseNo', 'dateLicense', 'dateExpired', 'remarks'];

    for (i = 0; i < fields.length; i++) {
        formData.append(fields[i], $('#modal-eligibility #'+fields[i]).val());
    }

    formData.append('empID', '<?php echo $this->encrypter->encode($rec->empID)?>');

    $.ajax({
        url: '<?php echo site_url('employee/save_eligibility')?>',
        data: formData,
        type: 'POST',
        processData: false,
        contentType: false,
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                showEligibilities();
                clearFields('#modal-eligibility');
                
                $('#modal-eligibility').modal('hide');
                $('#cmdSaveEligibility').prop('disabled',false);
            	$('#cmdSaveEligibility').removeClass('loader');
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

function updateEligibility() {
    var formData = new FormData();
    var fields   = ['eligibility', 'rating', 'examDate', 'examPlace', 'licenseNo', 'dateLicense', 'dateExpired', 'remarks'];

    for (i = 0; i < fields.length; i++) {
        formData.append(fields[i], $('#modal-eligibility-edit #e'+fields[i]).val());
    }

    formData.append('eligibilityID', $('#modal-eligibility-edit #eeligibilityID').val());

    $.ajax({
        url: '<?php echo site_url('employee/update_eligibility')?>',
        data: formData,
        type: 'POST',
        processData: false,
        contentType: false,
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                showEligibilities();
                clearFields('#modal-eligibility-edit');
                
                $('#modal-eligibility-edit').modal('hide');
                $('#cmdUpdateEligibility').prop('disabled',false);
            	$('#cmdUpdateEligibility').removeClass('loader');
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

function viewEligibility(eligibilityID) {
    $.ajax({
        url: '<?php echo site_url('employee/view_eligibility')?>',
        data: { eligibilityID: eligibilityID },
        type: 'POST',
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                record = response.record;
                
                $('#modal-eligibility-edit #eeligibilityID').val(record.eligibilityID);
                $('#modal-eligibility-edit #eeligibility').val(record.eligibility);
                $('#modal-eligibility-edit #erating').val(record.rating);
                $('#modal-eligibility-edit #eexamDate').val(record.examDate);
                $('#modal-eligibility-edit #eexamPlace').val(record.examPlace);
                $('#modal-eligibility-edit #elicenseNo').val(record.licenseNo);
                $('#modal-eligibility-edit #edateLicense').val(record.dateLicense);
                $('#modal-eligibility-edit #edateExpired').val(record.dateExpired);
                $('#modal-eligibility-edit #eremarks').val(record.remarks);
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

function deleteEligibility() {
    $.ajax({
        url: '<?php echo site_url('employee/delete_eligibility')?>',
        data: { eligibilityID: $('#modal-eligibility-edit #eeligibilityID').val() },
        type: 'POST',
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                showEligibilities();
                clearFields('#modal-eligibility-edit');
                
                $('#modal-eligibility-edit').modal('hide');
                $('#cmdDeleteEligibility').prop('disabled',false);
            	$('#cmdDeleteEligibility').removeClass('loader');
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