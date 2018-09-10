
</div> <!-- <div class="content-wrapper"> END OF BODY -->


</div> <!-- <div class="page-body"> -->

  <footer class="page-footer">
    <div class="container">
      <div class="d-flex">
        <div class="footer-copyright">
          <p class="mb-0"><?php echo date('Y') ?> &copy; <a href="http://www.iWash.com" class="link" target="_new">iWash</a>.</p>
        </div>
      </div>
    </div>
  </footer>

</div>


<script src="<?php echo base_url('assets/js/popper.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/bootstrap.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/plugins.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/modernizr.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/plugins/highcharts/js/highcharts.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/highcharts/js/modules/data.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/highcharts/js/modules/drilldown.js') ?>"></script>
<script src="<?php echo base_url('assets/plugins/ckeditor/ckeditor.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/custom.js') ?>" type="text/javascript"></script>

<!-- Change Password -->
<div class="modal fade" id="changePassModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form method="post" name="frmChangePassword" id="frmChangePassword" action="<?php echo site_url("user/save_password") ?>">
        <input type="hidden" name="userID" id="userID" value="<?php echo $this->session->userdata('current_user')->userID ?>" />
        <input type="hidden" name="pageName" id="pageName" value="userProfile" />
        <div class="modal-header">
          <h4 class="modal-title">Change Password</h4>
        </div>
        <div class="modal-body">
          <div class="table-row">
            <table class="table-form">
              <tbody>
                <tr>
                  <td class="form-label">
                    <label for="employee">New Password • <span class="asterisk">*</span></label>
                  </td>
                  <td class="form-group form-input">
                    <input type="password" class="form-control" name="userPswd" id="userPswd" required>
                  </td>
                </tr>
                <tr>
                  <td class="form-label">
                    <label for="fmname">Re-Password • <span class="asterisk">*</span></label>
                  </td>
                  <td class="form-group form-input">
                    <input type="password" class="form-control" name="rePswd" id="rePswd" required>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="saveNewPasswordBtn" class="btn btn-primary btn-raised pill">Save</button>
          <button type="button" class="btn btn-outline-danger btn-raised pill" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>


<?php 
if (!empty($js)) {
  foreach ($js as $script) {
    ?>
    <script src="<?php echo base_url('assets/js/'.$script) ?>" type="text/javascript"></script>
    <?php 
  }
}

if (!empty($plugin_js)) {
  foreach ($plugin_js as $script) {
    ?>
    <script src="<?php echo base_url('assets/js/plugins/'.$script) ?>" type="text/javascript"></script>
    <?php 
  }
}
?>

<script>
  $('#saveNewPasswordBtn').click(function() {
    if ($('#userPswd').val() == $('#rePswd').val()) {
      $('#frmChangePassword').submit();
    } else {
      alert("Passwords does not matched!");
    }
  });

  $('#saveNewPassPopupBtn').click(function(){
    $('#changePassModal').modal('show');
  })


  function deleteRecord(id)
  {
    swal({
      title: "Are you sure?",
      text: "Once deleted, you will not be able to recover this record!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    })
    .then((willDelete) => {
      if (willDelete.value) {
        window.location='<?php echo $controller_page."/delete/" ?>'+id;
      }
    });
  }


  function loadRecords(id, target, source)
  {
    $('#container_'+target).hide();
    $('#loading_img_'+target).show(); 
    $.ajax({
      type  : "POST",
      url   : source+'/3',
      data  : {id: id},
      timeout : <?php echo $this->config_model->getConfig('Request Timeout');?>,
      success : function(data){
        $('#container_'+target).html(data); 
        $('#loading_img_'+target).hide();
        $('#container_'+target).show();
      },
      error : function(xhr,textStatus,errorThrown) {  //alert(errorThrown);
        $('#loading_img_'+target).hide();
        if(textStatus=="timeout"){
          $('#msgstatus_'+target).html(' <div class="errorbox" style="display:block;" id="bigcontainer"><div class="boxcontent" id="msgcontainer"><strong>Sorry, the request has been longer than necessary. Please refresh the page or contact an Administrator.</strong></div></div>').slideDown();
        }else{        
          $('#msgstatus_'+target).html(' <div class="errorbox" style="display:block;" id="bigcontainer"><div class="boxcontent" id="msgcontainer"><strong>Internal Server Error! Please contact an Administrator.</strong></div></div>').slideDown();
        }
      }               
    });
  }

  function display_session_items(item_name, display_area) 
  {   
    $.post('<?php echo $controller_page ?>/display_session_items/'+display_area, { sessionSet: item_name },
      function(data){
        $('#'+display_area).html(data);
      }, "text");
  }

  function add_session_item(form_source, fields, display_area, do_reset=1, returnField="", success_msg="", error_msg="", checkDuplicate=0, duplicateField="", callback="") 
  {

    if (form_source) {
      if (success_msg=="") {
        success_msg = "Successfully added!";
      }

      if (error_msg=="") {
        error_msg = "Adding failed!";
      }
      fields = fields.replace(/,/gi,"_"); 
      $.post('<?php echo site_url()?>'+"sessionmanager/push_session_item/"+fields+"/"+checkDuplicate+"/"+duplicateField, $('#'+form_source).serialize(),
        function(data){
        //alert(data);
        if (parseInt(data)==1) {
        //alert(success_msg);
      } else if (parseInt(data)==2) {
        alert("Duplicate data");
      } else {
        alert(error_msg);
      }

      if (do_reset) {
        // reset form
        resetForm(form_source);
        // return field
        if (returnField) {
          $('#'+form_source+' #'+returnField).focus();
        }
      }
      if (display_area != "") {
        display_session_items($('#'+form_source+' #sessionSet').val(), display_area);
      }

      if (callback != "") {
        eval(callback);
      }
      
    }, "text");
    }
  }

  function delete_session_item(item_name, item_id, display_area,callback="") 
  {
    swal({
      title: "Are you sure you want to delete this column?",
      text: "",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes',
      cancelButtonText: 'No'
    })
    .then((willDelete) => {
      if (willDelete.value) {
        $.post('<?php echo site_url()?>'+"/sessionmanager/delete_session_item", { sessionSet: item_name, targetID: item_id },
          function(data){
            if (parseInt(data)==1) {
          //alert("Successfully deleted!");

        } else {
          alert("Delete failed!");
        }
          //        
          if (display_area != "") {
            display_session_items(item_name, display_area);
          }
          //
          fn = window[callback];
          if (typeof fn === "function") fn();
          //
          if (callback != "") {
            eval(callback);
          }
        }, "text");
      }
    });




//  reply=confirm("Confirm delete row?");
//    
//    if (reply==true) {
//    $.post(site_url+"/sessionmanager/delete_session_item", { sessionSet: item_name, targetID: item_id },
//      function(data){
//      if (parseInt(data)==1) {
//        //alert("Successfully deleted!");
//      } else {
//        alert("Delete failed!");
//      }
//        
//      if (display_area != "") {
//        display_session_items(item_name, display_area);
//      }
//
//      fn = window[callback];
//      if (typeof fn === "function") fn();
//
////      if (callback != "") {
////        eval(callback);
////      }
//      }, "text");
//    }
}

function is_session_empty(item_name) 
{
  $.post(site_url+"/sessionmanager/is_session_empty/"+item_name, {},
    function(data){
      if (parseInt(data)==1) {

      } else {

      }
    }, "text");
}

function clear_session(sessionSet)
{
  $.post(site_url+"/sessionmanager/clear_session/", { 
    sessionSet: sessionSet});
}


function resetForm(id) {
  $('#'+id).each(function(){
    this.reset();
  });
}
</script>

<script type="text/javascript">

// function get_calendar_events() {
// console.log('asssss');
//   var table = 'holidays_events';
//   var select = ['eventDate'];
//   $.ajax({
//     type: 'ajax',
//     method: 'POST',
//     url: '<?php //echo base_url(); ?>generic_ajax/get_table',
//     data: {table: table, select: select},
//     dataType: 'json',
//         success: function(data) {
//           console.log('success', data);
          
//           console.log(response.data);
//           for (var i = 0; i < data.length; i++) {

//             // $('.datepicker').datepicker('update', data[i].eventDate);
//             console.log(response.eventDate, 'calendar_events');
//           }

//           $.each(response, function(val){
//             console.log(val.eventDate, 'calendar_events');
//           });
          
//           },
//           error: function() {
//             console.log('error');
//           }
//     });

// }
</script>
<script type="text/javascript">
    function isNumber(evt)
    {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode != 46 && charCode > 31
            && (charCode < 48 || charCode > 57))
            return false;

        return true;
    }
</script>



</body>
</html>