 <!-- Add Attachment modal -->
    <div class="modal fade" id="modal-attachment" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <form method="post" name="frmAttachment" id="frmAttachment" action="<?php echo $controller_page ?>/upload_attachment" enctype="multipart/form-data">
            <input type="hidden" id="fempID" name="fempID" value="<?php echo $rec->empID?>"/>
            <div class="modal-header">
              <h4 class="modal-title">New Attachment</h4>
            </div>
            <div class="modal-body">
              <div class="table-row">
                <table class="table-form">
                  <tbody>
                    <tr>
                      <td class="form-label">
                        Filename<span class="asterisk">*</span>
                      </td>
                      <td class="form-group form-input">
                        <input type="text" class="form-control" name="filename" id="fileName" title="Filename" required>
                      </td>
                    </tr>
                    <tr>
                        <td class="form-label ">
                         Description
                       </td>
                       <td class="form-group form-input">
                        <textarea class="form-control" name="description" id="description" value=""></textarea>
                       </td>
                       <td>&nbsp;</td>
                     </tr>
                    <tr>
                        <td class="form-label ">
                         Attachment File
                       </td>
                       <td class="form-group form-input">
                        <input type="file" class="form-control filestyle" id="fuserfile" name="fuserfile">
                       </td>
                       <td>&nbsp;</td>
                     </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary btn-raised pill" name="cmdSaveAttachment" id="cmdSaveAttachment">Upload</button>
              <button type="button" class="btn btn-outline-danger btn-raised pill" data-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
  </div>

<script>
function showAttachments() {
    $.ajax({
        url: '<?php echo site_url('employee/show_attachments')?>',
        data: { empID: '<?php echo $this->encrypter->encode($rec->empID)?>' },
        type: 'POST',
        dataType:'json',
        success:function(response) {
            if (response.status == '1') {
                
                list = '';
                for (i = 0; i < response.records.length; i++) {
                    record = response.records[i];
                    
					list += '<tr>';
					list += '	<td><span>'+record.fileName+'</span></td>';
					list += '	<td><span>'+record.description+'</span></td>';
					list += '	<td><span>'+record.dateUploaded+'</span></td>';
					list += '	<td><span><a href="<?php echo base_url('assets/records/employee/'.$rec->empID.'/a')?>'+record.id+record.fileExt+'" download="'+record.fileName+record.fileExt+'">Download<a></span></td>';
					list += '</tr>';
                }

                $('#attachments tbody').html(list);
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