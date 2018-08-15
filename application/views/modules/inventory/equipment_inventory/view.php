<div class="subheader">
    <div class="d-flex align-items-center">
        <div class="title mr-auto">
            <h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></h3>
        </div>
        <div class="subheader-tools">
            <a href="<?php echo site_url('equipment_inventory/show') ?>" class="btn btn-primary btn-raised btn-xs pill"><i class="icon ti-angle-left"></i> Back to List</a>
        </div>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="card-head">
                    <div class="head-caption">
                        <div class="head-title">
                            <h4 class="head-text">View <?php echo $current_module['module_label'] ?></h4>
                        </div>
                    </div>
                    <div class="card-head-tools">
                        <ul class="tools-list">    
                            <?php if ($roles['edit']) {?>
                            <li>
                                <a href="<?php echo site_url('equipment_inventory/edit/'.$this->encrypter->encode($rec->eqID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>
                            </li>
                            <?php } ?>
                            <?php if ($roles['delete'] && !$in_used) {?>
                            <li>
                                <button name="cmddelete" id="cmddelete" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" onclick="deleteRecord('<?php echo $this->encrypter->encode($rec->eqID); ?>');"><i class="la la-trash-o"></i></button>
                            </li>
                            <?php } ?>
                            <?php if ($this->session->userdata('current_user')->isAdmin) {?>
                            <li>
                                <button type="button" id="recordlog" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Record Logs" onclick="popUp('<?php echo site_url('logs/record_log/equipment_inventory/eqID/'.$this->encrypter->encode($rec->eqID).'/Equipment Inventory') ?>', 1000, 500)"><i class="la la-server"></i></button>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="data-view">
                        <table class="view-table">
                            <tbody>
                                <tr>                                    
                                    <td class="data-title" style="width:120px"  nowrap>Reference No:</td>
                                    <td class="data-input"><?php echo $rec->assetNo; ?></td>
                                </tr>
                                <tr>
                                    <td class="data-title" style="width:120px"  nowrap>Branch:</td>
                                    <td class="data-input"><?php echo $rec->branchName; ?></td>
                                    <td class="data-title" style="width:300px" nowrap>Date:</td>
                                    <td class="data-input"><?php echo date('M d, Y',strtotime($rec->date))?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="data-title" style="width:120px" nowrap>Brand</td>
                                    <td class="data-input"  nowrap><?php echo $rec->brand; ?></td>
                                    <td class="data-title" style="width:300px">Model</td>
                                    <td class="data-input"  nowrap><?php echo $rec->model; ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="data-title" style="width:120px" nowrap>Serial No:</td>
                                    <td class="data-input"><?php echo $rec->serialNo; ?></td>
                                    <td class="data-title" style="width:300px">Name</td>
                                    <td class="data-input"  nowrap><?php echo $rec->name; ?></td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td class="data-title" nowrap>Description:</td>
                                    <td class="data-input"><?php echo $rec->description; ?></td>
                                    <td class="data-title"></td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td class="data-title" nowrap>Status:</td>
                                    <td class="data-input">
                                        <?php 
                                            if ($rec->status == 1) {
                                                echo "<span class='badge badge-pill badge-success'>Active</span>";
                                            } else {
                                                echo "<span class='badge badge-pill badge-danger'>Inactive</span>";
                                           
                                            } 
                                            ?>
                                    </td>
                                    <td class="data-title"></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    function confirm(){
       swal({
          title: "You are performing 'CONFIRM' action.",
          text: "Do you still want to continue?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes',
          cancelButtonText: 'No'
        })
        .then((willConfirm) => {
          if (willConfirm.value) {
            var eqID = "<?php echo $rec->eqID; ?>";
                $.post("<?php echo $controller_page ?>/updateApprovedBy", { eqID: eqID },
                    function(data, status){
                        if(status == "success") {   
                            window.location.reload(); 
                        }
                    });         
          }
        });     
    }//end of function confirm

    function cancel(){

       swal({
          title: "You are performing 'CANCEL' action.",
          text: "Do you still want to continue?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes',
          cancelButtonText: 'No'
        })
        .then((willCancel) => {
          if (willCancel.value) {
            var eqID = "<?php echo $rec->eqID; ?>";
                $.post("<?php echo $controller_page ?>/updateCancelledBy", { eqID: eqID },
                    function(data, status){                     
                        if(status == "success") {
                           window.location.reload();                            
                        }
                });
          }
        });     
    }//end of function cancel
</script>