<div class="subheader">
    <div class="d-flex align-items-center">
        <div class="title mr-auto">
            <h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></h3>
        </div>
        <div class="subheader-tools">
            <a href="<?php echo site_url('physical_count/show') ?>" class="btn btn-primary btn-raised btn-sm pill"><i class="icon left ti-angle-left md"></i> Back to List</a>
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
                            <h4 class="head-text">View <?php echo $current_module['module_label'] ?>
                                <?php if($rec->dateApproved == '0000-00-00 00:00:00' && $rec->dateCancelled == '0000-00-00 00:00:00' ) : ?>
                                    <button type="button" class="btn btn-primary btn-xs pill ml-15" id="confirm">Confirm</button>
                                    <button type="button" class="btn btn-outline-danger btn-xs pill" id="cancel">Cancel</button>
                                <?php endif;?>
                            </h4>
                        </div>
                    </div>
                    <div class="card-head-tools">
                        <ul class="tools-list">
                            <?php if ($roles['edit']) { ?>
                                <li>
                                    <a href="<?php echo site_url('physical_count/edit/'.$this->encrypter->encode($rec->pcID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>
                                </li>
                            <?php } ?>
                            <?php if ($roles['delete'] && !$in_used && $rec->status != 2) { ?>
                                <li>
                                    <button name="cmddelete" id="cmddelete" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" onclick="deleteRecord('<?php echo $this->encrypter->encode($rec->pcID); ?>');"><i class="la la-trash-o"></i></button>
                                </li>
                            <?php } ?>
                            <?php if ($this->session->userdata('current_user')->isAdmin) { ?>
                                <li>
                                    <button type="button" id="recordlog" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Record Logs" onclick="popUp('<?php echo site_url('logs/record_log/physical_count/pcID/'.$this->encrypter->encode($rec->prID).'/Physical count') ?>', 1000, 500)"><i class="la la-server"></i></button>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url("physical_count/save") ?>">
                        <div class="data-view">
                            <table class="view-table">
                                <tbody>
                                    <tr>                                        
                                        <td  class="data-title w-10">Physical Count No : </td>
                                        <td class="data-input w-20">
                                            <input type="text" class="border-0" name="pcNo" id="pcNo" title="Physical Count No" required value="<?php echo 'pc'.str_pad($rec->pcNo,4,'0',STR_PAD_LEFT); ?>" readonly>

                                        </td>
                                        <td class="d-xxl-none"></td>
                                    </tr>
                                    <tr>
                                        <td class="data-title w-10">
                                            <label for="config">Branch : <span class="asterisk">*</span></label>
                                        </td>
                                        <td class="data-input w-20">
                                            <input type="text" class="border-0" name="branchName" id="branchName" title="Branch Name" value="<?php echo $rec->branchName;?>" readonly>
                                            <input type="hidden" class="form-control" name="branchID" id="branchID" title="Branch Id" value="<?php echo $rec->branchID;?>" readonly>
                                        </td>
                                        <td  class="data-title w-10">Date : </td>
                                        <td class="data-input w-20">
                                            <input type="text" class="border-0" name="date" id="date" title="Date" required value="<?php echo date('F d, Y',strtotime($rec->date))?>" readonly>
                                        </td>
                                        <td class="d-xxl-none"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div>
                            <!-- start create header details -->
                            <?php
                            $table_str="<table class='table mt-20' id='table_physical_count'>";
                            $table_str.='<thead class="thead-light"><tr>';
                            $table_str.='<th>'.'No'.'</th>';
                            $table_str.='<th>'.'Category'.'</th>';
                            $table_str.='<th>'.'Expected Qty'.'</th>';
                            $table_str.='<th>'.'Physical Qty'.'</th>';
                            $table_str.='<th>'.'Variance'.'</th>';
                            $table_str.='<tr></thead>';
                            $i = 1;
                            foreach ($items as $rows) {
                                $table_str.='<tr>';
                                $table_str.='<td>'.($i++).'</td>';
                                $table_str.='<td>'.$rows->item.'</td>';
                                $table_str.='<td style="display:none">'.$rows->itemID.'</td>';
                                $table_str.='<td>'.$rows->systemQty.'</td>';
                                //$table_str.='<td>'.'<input type="number" min="1" id="id_'.($i++).'" name="id_'.($i++).'">'.'</td>';
                                $table_str.='<td>'.'<span>'.$rows->actualQty.'</span>'.'</td>';
                                $table_str.='<td>'.'<span>'.$rows->variance.'</span>'.'</td>';
                                $table_str.='</tr>';
                            }
                            $table_str.="</table>";

                            echo $table_str;
                            ?>
                            <!-- end create header details -->
                        </div>
                        <div class="data-view mt-10">
                            <table class="view-table">
                                <tbody>
                                    <tr>                                        
                                        <td class="data-title text-left w-10">
                                            Conducted By :
                                        </td>
                                        <td class="form-group form-input">
                                            <span><?php echo $rec->conductedBy?></span>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="data-title text-left w-10">
                                            Remarks :
                                        </td>
                                        <td class="form-group form-input">
                                            <span><?php echo $rec->remarks?></span>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-sepator solid mx-0"></div>
                        <div class="data-view">
                            <table class="view-table">
                                <tbody id="table-date-result">
                                    <tr>
                                       <div class="container-date">
                                           <td class="data-title w-10">Created By :</td>
                                           <td class="data-input w-20">
                                               <span><?php echo $rec->firstName .' '.$rec->middleName .' '.$rec->lastName;?></span>
                                           </td>
                                           <td class="data-title w-10">Date Created : </td>
                                           <td class="data-input w-20">
                                               <span><?php echo date('M d, Y H:i:s',strtotime($rec->dateCreated))?></span>
                                           </td>
                                           <td class="d-xxl-none"></td>
                                       </div>
                                    </tr>
                                    <?php if($rec->dateApproved != '0000-00-00 00:00:00'  ) { ?>
                                    <tr>
                                        <td class="data-title">Approved by :  </td>
                                        <td class="data-input">
                                            <span id="id_approvedBy"><?php echo $rec->approvedFirstName .' '.$rec->approvedMiddleName .' '.$rec->approvedLastName;?></span>
                                        </td>
                                        <td class="data-title">Approved date : </td>
                                        <td class="data-input">                                            
                                            <span id="id_approved_date"><?php echo date('M d, Y H:i:s',strtotime($rec->dateApproved))?></span>
                                        </td>
                                        <td class="d-xxl-none"></td>
                                    </tr>
                                    <?php } ?>
                                    <?php if($rec->dateCancelled != '0000-00-00 00:00:00' ) { ?>
                                    <tr>
                                        <td class="data-title">Cancelled by :  </td>
                                        <td class="data-input">
                                            <span id="id_cancel_by"><?php echo $rec->cancelledFirstName .' '.$rec->cancelledMiddleName .' '.$rec->cancelledLastName;?></span>
                                        </td>
                                        <td class="data-title">Cancelled date : </td>
                                        <td class="data-input">                                            
                                            <span id="id_date_cancel"><?php echo date('M d, Y H:i:s',strtotime($rec->dateCancelled))?></span>
                                        </td>
                                        <td class="d-xxl-none"></td>
                                    </tr>

                                    <tr>
                                        <td class="data-title">Status :</td>
                                        <td>
                                                <?php 
                                                    if ($rec->status == 1) {
                                                        echo "<span class='badge badge-pill badge-warning'>Pending</span>";
                                                    } else if ($rec->status == 2) {
                                                        echo "<span class='badge badge-pill badge-success'>Confirmed</span>";
                                                    } else if ($rec->status == 0) {
                                                        echo "<span class='badge badge-pill badge-danger'>Cancelled</span>";
                                                    } 
                                                ?>
                                        </td>
                                        
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        var total = 0;
        $("tr.item").each(function() {

            var price = $(this).find("input.name").val(),
                amount = $(this).find("input.id").val();
            total += parseFloat(amount);
        });

        $('#ttlAmount').val(total);

        $("#confirm").on('click', function(){

            var reffNo = "<?php echo $this->router->fetch_class().'_'.rand(); ?>";

                    //check variance if positive update item inventory and update stock card


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

                  var pcID = "<?php echo $rec->pcID; ?>";

                  //get variance
                  var table = $('#table_physical_count');
                  table.find('tr').each(function(i){
                      var debit = 0, credit = 0, begBal = 0, endBal = 0;
                      var $tds = $(this).find('td');
                      var item_id = $tds.eq(2).text();
                      var variance = $tds.eq(5).text();
                      var expected_qty = $tds.eq(3).text();
                      var physical_qty = $tds.eq(4).text();


                      //alert(item_id);
                      if(item_id != "") {
                          //check variance if positive update item inventory and update stock card
                          if(variance > 0) {
                              //positivE

                              var updated_inventory_qty = +expected_qty + +variance;
                              //console.log("updated inventory", updated_inventory_qty);
                              addStockCardAndItemInventory(reffNo, item_id, updated_inventory_qty, debit, credit, begBal, endBal, pcID);


                          }
                          else {
                              //negative
                              //cast to positive
                              var cast_variance = Math.abs(variance);
                              //console.log("negative", cast_variance)
                              var updated_inventory_qty = +expected_qty - +cast_variance;
                              //console.log("result for negative", updated_inventory_qty);
                              addStockCardAndItemInventory(reffNo, item_id, updated_inventory_qty, debit, credit, begBal, endBal, pcID);


                          }

                      }

                  });


                  $('#confirm').hide();
                  $('#cancel').hide();
                  var approved_by = "<?php echo $rec->firstName .' '.$rec->middleName .' '.$rec->lastName;?>";
                  $("#id_approvedBy").text(approved_by);
                  $("#id_approved_date").text("<?php echo date('Y-m-d h:is')?>");
                  var date = '<?php echo date('F d, Y',strtotime(date('Y-m-d')))?>';
                  $('#table-date-result').empty();
                  var _table = $('#table-date-result');
                  _table.append($('<tr>')
                      .append($('<td>').attr('class', 'data-title w-10').text('Approved by :'))
                      .append($('<td>').attr('class', 'data-input w-20')
                          .append($('<span>').text(approved_by)
                          )
                      )
                      .append($('<td>').attr('class', 'data-input w-20').text('Date Approved : ')).append($('<td>').attr('class', 'data-input w-20')
                          .append($('<span>').text(date)
                          ))

                  );

                }

            });
        });
        $('#cancel').on('click', function(){
            var pcID = "<?php echo $rec->pcID; ?>";
            updateCancel(pcID);
        });

    });

    //updateStockCardAndItemInventory
    //reffNo, item_id, variance, debit, credit, begBal, endBal pcID
    function addStockCardAndItemInventory(reffNo, item_id, variance, debit, credit, begBal, endBal, pcID, sign)
    {
        $.post("<?php echo $controller_page ?>/updateStockCardAndItemInventory", { reffNo: reffNo, item_id: item_id, variance: variance, debit: debit, credit: credit, begBal: begBal, endBal: endBal, pcID: pcID , sign: sign},
            function(data, status){
                console.log(data);
                if(status == "success") {
                    var approved_by = "<?php echo $rec->firstName .' '.$rec->middleName .' '.$rec->lastName;?>";
                    $("#id_approvedBy").text(approved_by);
                    $("#id_approved_date").text("<?php echo date('Y-m-d h:is')?>");
                }
            });
    }

    function updateCancel(pcID)
    {

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
                $.post("<?php echo $controller_page ?>/updateCancelledBy", { pcID: pcID },
                    function(data, status){
                        console.log(data);
                        if(status == "success") {
                           //window.location.reload();
                            $('#cancel').hide();
                            $('#confirm').hide();
                            var cancel_by = "<?php echo $rec->firstName .' '.$rec->middleName .' '.$rec->lastName;?>";
                            $("#id_cancel_by").text(cancel_by);
                            $("#id_date_cancel").text("<?php echo date('Y-m-d h:i:s')?>");

                        }
                    });
          }
        });     
    }

</script>