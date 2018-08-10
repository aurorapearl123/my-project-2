<div class="modal fade" id="modal-training" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">New Training Program</h4>
            </div>
            <div class="modal-body">
                <div class="table-row">
                    <table class="table-form">
                        <tr>
                            <td class="form-label">Course<span class="asterisk">*</span></td>
                            <td class="form-group form-input"><input type="text" class="form-control" name="course" id="course" title="Course" required></td>
                        </tr>
                        <tr>
                            <td class="form-label">Organizer<span class="asterisk">*</span></td>
                            <td class="form-group form-input"><input type="text" class="form-control" name="organizer" id="organizer" title="Organizer" required></td>
                        </tr>
                        <tr>
                            <td class="form-label">Venue<span class="asterisk">*</span></td>
                            <td class="form-group form-input"><input type="text" class="form-control" name="venue" id="venue" title="Venue" required></td>
                        </tr>
                        <tr>
                            <td class="form-label">No. of Hourse</td>
                            <td class="form-group form-input"><input type="text" class="form-control" name="noHours" id="noHours"></td>
                        </tr>
                        <tr>
                            <td class="form-label">Start Date</td>
                            <td class="form-group form-input"><input type="text" class="form-control datepicker" name="tstartDate" id="tstartDate" data-toggle="datetimepicker" data-target="#tstartDate"></td>
                        </tr>
                        <tr>
                            <td class="form-label">End Date</td>
                            <td class="form-group form-input"><input type="text" class="form-control datepicker" name="tendDate" id="tendDate" data-toggle="datetimepicker" data-target="#tendDate"></td>
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
                <button type="submit" class="btn btn-primary btn-raised pill" name="cmdSaveTraining" id="cmdSaveTraining">Save</button>
                <button type="button" class="btn btn-outline-danger btn-raised pill" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-training-edit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Training Program</h4>
            </div>
            <div class="modal-body">
                <div class="table-row">
                    <input type="hidden" class="form-control" id="etrainingID">
                    <table class="table-form">
                        <tbody>
                            <tr>
                                <td class="form-label">Course<span class="asterisk">*</span></td>
                                <td class="form-group form-input"><input type="text" class="form-control" name="ecourse" id="ecourse" title="Course" required></td>
                            </tr>
                            <tr>
                                <td class="form-label">Organizer<span class="asterisk">*</span></td>
                                <td class="form-group form-input"><input type="text" class="form-control" name="eorganizer" id="eorganizer" title="Organizer" required></td>
                            </tr>
                            <tr>
                                <td class="form-label">Venue<span class="asterisk">*</span></td>
                                <td class="form-group form-input"><input type="text" class="form-control" name="evenue" id="evenue" title="Venue" required></td>
                            </tr>
                            <tr>
                                <td class="form-label">No. of Hourse</td>
                                <td class="form-group form-input"><input type="text" class="form-control" name="enoHours" id="enoHours"></td>
                            </tr>
                            <tr>
                                <td class="form-label">Start Date</td>
                                <td class="form-group form-input"><input type="text" class="form-control datepicker" name="testartDate" id="testartDate" data-toggle="datetimepicker" data-target="#testartDate"></td>
                            </tr>
                            <tr>
                                <td class="form-label">End Date</td>
                                <td class="form-group form-input"><input type="text" class="form-control datepicker" name="teendDate" id="teendDate" data-toggle="datetimepicker" data-target="#teendDate"></td>
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
                <?php if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'Delete Existing Training Program')) {?>
                <button type="button" class="btn btn-warning btn-raised pill" id="cmdDeleteTraining">Delete</button>
                <?php }?>
                <?php if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'Edit Existing Training Program')) {?>
                <button type="button" class="btn btn-primary btn-raised pill" name="cmdUpdateTraining" id="cmdUpdateTraining">Update</button>
                <?php }?>
                <button type="button" class="btn btn-outline-danger btn-raised pill" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$('#cmdSaveTraining').click(function(){
    if (check_fields('#modal-training')) {
    	$(this).attr('disabled','disabled');
    	$(this).addClass('loader');
    	// submit
       	saveTraining();
    }
});

$('#cmdUpdateTraining').click(function(){
    if (check_fields('#modal-training-edit')) {
    	$(this).attr('disabled','disabled');
    	$(this).addClass('loader');
    	// submit
       	updateTraining();
    }
});

$('#cmdDeleteTraining').click(function(){
	$(this).attr('disabled','disabled');
	$(this).addClass('loader');
	// submit
   	deleteTraining();
});

function showTrainings() {
    $.ajax({
        url: '<?php echo site_url('training_program/show')?>',
        data: { empID: '<?php echo $this->encrypter->encode($rec->empID)?>' },
        type: 'POST',
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                
                list = '';
                for (i = 0; i < response.records.length; i++) {
                    record = response.records[i];
                    
					list += '<tr class="row-training" data-id="'+record.trainingID+'">';
					list += '	<td><span>'+record.course+'</span></td>';
					list += '	<td><span>'+record.organizer+'</span></td>';
					list += '	<td><span>'+record.venue+'</span></td>';
					list += '	<td><span>'+record.noHours+'</span></td>';
					list += '	<td><span>'+((record.startDate != '00/00/0000') ? record.startDate : '')+'</span></td>';
					list += '	<td><span>'+((record.endDate != '00/00/0000') ? record.endDate : '')+'</span></td>';
					list += '</tr>';
                }

                $('#trainings tbody').html(list);
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

function saveTraining() {
    var formData = new FormData();
    var fields   = ['course', 'organizer', 'venue', 'noHours', 'remarks'];

    for (i = 0; i < fields.length; i++) {
        formData.append(fields[i], $('#modal-training #'+fields[i]).val());
    }

    formData.append('startDate', $('#modal-training #tstartDate').val());
    formData.append('endDate', $('#modal-training #tendDate').val());

    formData.append('empID', '<?php echo $this->encrypter->encode($rec->empID)?>');

    $.ajax({
        url: '<?php echo site_url('training_program/save')?>',
        data: formData,
        type: 'POST',
        processData: false,
        contentType: false,
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                showTrainings();
                clearFields('#modal-training');
                
                $('#modal-training').modal('hide');
                $('#cmdSaveTraining').prop('disabled',false);
            	$('#cmdSaveTraining').removeClass('loader');
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

function viewTraining(trainingID) {
    $.ajax({
        url: '<?php echo site_url('training_program/view')?>',
        data: { trainingID: trainingID },
        type: 'POST',
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                record = response.record;
                
                $('#modal-training-edit #etrainingID').val(record.trainingID);
                $('#modal-training-edit #ecourse').val(record.course);
                $('#modal-training-edit #eorganizer').val(record.organizer);
                $('#modal-training-edit #evenue').val(record.venue);
                $('#modal-training-edit #enoHours').val(record.noHours);
                $('#modal-training-edit #testartDate').val(record.startDate);
                $('#modal-training-edit #teendDate').val(record.endDate);
                $('#modal-training-edit #eremarks').val(record.remarks);
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

function updateTraining() {
    var formData = new FormData();
    var fields   = ['course', 'organizer', 'venue', 'noHours', 'remarks'];

    for (i = 0; i < fields.length; i++) {
        formData.append(fields[i], $('#modal-training-edit #e'+fields[i]).val());
    }

    formData.append('startDate', $('#modal-training-edit #testartDate').val());
    formData.append('endDate', $('#modal-training-edit #teendDate').val());

    formData.append('trainingID', $('#modal-training-edit #etrainingID').val());

    $.ajax({
        url: '<?php echo site_url('training_program/update')?>',
        data: formData,
        type: 'POST',
        processData: false,
        contentType: false,
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                showTrainings();
                clearFields('#modal-training-edit');
                
                $('#modal-training-edit').modal('hide');
                $('#cmdUpdateTraining').prop('disabled',false);
            	$('#cmdUpdateTraining').removeClass('loader');
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

function deleteTraining() {
    $.ajax({
        url: '<?php echo site_url('training_program/delete')?>',
        data: { trainingID: $('#modal-training-edit #etrainingID').val() },
        type: 'POST',
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                showTrainings();
                clearFields('#modal-training-edit');
                
                $('#modal-training-edit').modal('hide');
                $('#cmdDeleteTraining').prop('disabled',false);
            	$('#cmdDeleteTraining').removeClass('loader');
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