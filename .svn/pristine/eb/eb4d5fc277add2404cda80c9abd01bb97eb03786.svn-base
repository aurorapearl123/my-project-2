<div class="subheader">
    <div class="d-flex align-items-center">
        <div class="title mr-auto">
            <h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></h3>
        </div>
        <div class="subheader-tools"></div>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="card-head">
                    <div class="head-caption">
                        <div class="head-title">
                            <h4 class="head-text">Add <?php echo $current_module['module_label'] ?></h4>
                        </div>
                    </div>
                    <div class="card-head-tools"></div>
                </div>
                <div class="card-body">
                    <form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url('inventory_adjustment/save') ?>">                        
                        <div class="table-row">
                            <table class="table-form column-3">
                                <tbody>
                                    <tr>
                                        <td class="form-label" nowrap>Branch<span class="asterisk">*</span></td>
                                        <td class="form-group form-input" width="350" nowrap>
                                            <?php 
                                                $this->db->select('branches.*');
                                                $this->db->from('branches');
                                                $this->db->where('status',1);
                                                $recs = $this->db->get()->result();                                             
                                            ?>
                                            <select class="form-control" id="branchID" name="branchID" data-live-search="true" livesearchnormalize="true" style="" title="-Select Branch-" >
                                                <?php foreach($recs as $res) {?>
                                                <option value="<?php echo $res->branchID ?>" <?php if ($res->branchID==$rec->branchID ) echo "selected"; ?>  ><?php echo $res->branchName ?></option>
                                                <?php } ?>
                                            </select>

                                        </td>
                                        <td class="form-label" width="200" nowrap>Date</td>
                                        <td class="form-group form-input" style="">
                                            <input type="text" class="form-control datepicker" id="date" name="date" data-toggle="datetimepicker" value="" data-target="#date" title="Date" style="width:350px" required>                                           
                                        </td>
                                        <td class="d-xxl-none" colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <td class="form-label" nowrap>Brand <span class="asterisk">*</span></td>
                                        <td class="form-group form-input">
                                            <input type="text" class="form-control" name="brand" id="brand" value="" title="Brand" required>
                                        </td>

                                        <td class="form-label" style="width:120px" nowrap>Item <span class="asterisk">*</span></td>
                                        <td class="form-group form-input" style="width:350px" nowrap>
                                            <input type="text" class="form-control" name="item" id="item" value="" title="Item" required>
                                        </td>
                                        
                                    </tr>

                                    <tr>
                                        
                                        <td class="form-label" style="width:120px" nowrap>Description <span class="asterisk">*</span></td>
                                        <td class="form-group form-input" style="width:350px" nowrap>
                                            <textarea rows="2" type="text" class="form-control" name="description" id="description" title="Description" required></textarea>
                                        </td>
                                        <td class="d-xxl-none"></td>
                                    </tr>

                                    <tr>
                                        <td class="form-label" style="width:120px" nowrap>UMSR <span class="asterisk">*</span></td>
                                        <td class="form-group form-input" style="width:350px" nowrap>
                                            <input type="text" class="form-control" name="umsr" id="umsr" value="" title="UMSR" required>
                                        </td>
                                        <td class="form-label" style="width:120px" nowrap>QTY <span class="asterisk">*</span></td>
                                        <td class="form-group form-input" style="width:350px" nowrap>
                                            <input type="text" class="form-control" name="qty" id="qty" value="" title="QTY" required>
                                        </td>
                                    </tr>                                                                    
                                    <tr>
                                        
                                        <td class="form-label" style="width:120px" nowrap>Remarks <span class="asterisk">*</span></td>
                                        <td class="form-group form-input" style="width:350px" nowrap>
                                            <textarea rows="2" type="text" class="form-control" name="description" id="remarks" title="Remarks" required></textarea>
                                        </td>
                                        <td class="d-xxl-none"></td>
                                    </tr>
                                </tbody>
                            </table>
                        

                        <div class="form-sepator solid"></div>
                        <div class="form-group mb-0">
                            <button class="btn btn-primary btn-raised pill" type="button" name="cmdSave" id="cmdSave">
                            Save
                            </button>
                            <input type="button" id="cmdCancel" class="btn btn-outline-danger btn-raised pill" value="Cancel"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    <?php 
        echo "\n";
        $parameters = array('custID');
        echo $this->htmlhelper->get_json_select('get_orders', $parameters, site_url('generic_ajax/get_orders'), 'orderID', '');
    ?>

    $('#cmdSave').click(function(){
        if (check_fields()) {
            $('#cmdSave').attr('disabled','disabled');
            $('#cmdSave').addClass('loader');
            $.post("<?php echo $controller_page ?>/check_duplicate", { branchID: $('#branchID').val(), custID:$('#custID').val(), orderID:$('#orderID').val()},
              function(data){
                if (parseInt(data)) {
                    $('#cmdSave').removeClass("loader");
                    $('#cmdSave').removeAttr('disabled');
                    // duplicate
                    swal("Duplicate","Record is already exist!","warning");
                } else {
                    // submit
                    $('#frmEntry').submit();
                    // alert($('#branchID').val());
                }
              }, "text");
        }
    });
    
    
    function check_fields()
    {
         var valid = true;
         var req_fields = "";
         
         $('#frmEntry [required]').each(function(){
            if($(this).val()=='' ) {
                req_fields += "<br/>" + $(this).attr('title');
                valid = false;
            } 
         })
         
         if (!valid) {
            swal("Required Fields",req_fields,"warning");
         }
         
         return valid;
    }
    
    $('#cmdCancel').click(function(){
        swal({
              title: "Are you sure?",
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
                  window.location = '<?php echo site_url('complaint/show') ?>';
              }
            });
        
    });

        
    
</script>