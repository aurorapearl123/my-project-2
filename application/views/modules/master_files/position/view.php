
<div class="subheader">
  <div class="d-flex align-items-center">
    <div class="title mr-auto">
      <h3><i class="icon left la la-street-view"></i> Positions</h3>
    </div>
    <div class="subheader-tools">
      <a href="<?php echo site_url('position/show') ?>" class="btn btn-primary btn-raised btn-md pill"><i class="icon ti-angle-left"></i> Back to List</a>
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
              <h4 class="head-text">View Position</h4>
            </div>
          </div>
          <div class="card-head-tools">
            <ul class="tools-list">
              <li>
                <a href="<?php echo site_url('position/edit/'.$this->encrypter->encode($rec->positionID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>
              </li>
              <li>
                <button name="cmddelete" id="cmddelete" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" onclick="deleteRecord('<?php echo $this->encrypter->encode($rec->positionID); ?>');"><i class="la la-trash-o"></i></button>
              </li>
              <li>
                <button id="recordlog" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Record Logs"><i class="la la-server"></i></button>
              </li>
            </ul>
          </div>
        </div>
        <div class="card-body">
          <div class="data-view">
            <table class="view-table">
              <tbody>
                <tr>
                  <td class="data-title" style="width:150px">Position Code:</td>
                  <td class="data-input"><?php echo $rec->positionCode; ?></td>
                </tr>
                <tr>
                  <td class="data-title">Position Name:</td>
                  <td class="data-input"><?php echo $rec->positionName; ?></td>
                </tr>
                <tr>
                  <td class="data-title">Status:</td>
                  <td class="data-input"><?php if($rec->status==1){ echo "Active"; }else{ echo "Inactive"; } ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<script type="text/javascript">
  function deleteRecord(id)
  {
    reply=confirm("Confirm delete?");

    if (reply==true)
      window.location='<?php echo site_url("position/delete/") ?>'+id;
  }
</script>