
<div class="subheader">
  <div class="d-flex align-items-center">
    <div class="title mr-auto">
      <h3><i class="icon left ti-layout-column2"></i> Division</h3>
    </div>
    <div class="subheader-tools">
      <a href="<?php echo site_url('division/show') ?>" class="btn btn-primary btn-raised btn-md pill"><i class="icon ti-angle-left"></i> Back to List</a>
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
              <h4 class="head-text">View Division</h4>
            </div>
          </div>
          <div class="card-head-tools">
            <ul class="tools-list">
              <li>
                <a href="<?php echo site_url('division/edit/'.$this->encrypter->encode($rec->divisionID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>
              </li>
              <li>
                <button name="cmddelete" id="cmddelete" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" onclick="deleteRecord('<?php echo $this->encrypter->encode($rec->divisionID); ?>');"><i class="la la-trash-o"></i></button>
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
                  <td class="data-title" width="12%">Division Code:</td>
                  <td class="data-input"><?php echo $rec->divisionCode; ?></td>
                  <td class="data-title" width="12%">Company:</td>
                  <td class="data-input"><?php echo $rec->companyName; ?></td>
                  <td class="data-input"></td>
                </tr>
                <tr>
                  <td class="data-title">Division Name:</td>
                  <td class="data-input"><?php echo $rec->divisionName; ?></td>
                  <td class="data-title">Branch:</td>
                  <td class="data-input"><?php echo $rec->branchName; ?></td>
                  <td class="data-input"></td>
                </tr>
                <tr>
                  <td class="data-title">Division Abbr:</td>
                  <td class="data-input"><?php echo $rec->divisionAbbr; ?></td>
                  <td class="data-title">Department:</td>
                  <td class="data-input"><?php echo $rec->deptName; ?></td>
                  <td class="data-input"></td>
                </tr>
                <tr>
                  <td class="data-title">Division Contact:</td>
                  <td class="data-input"><?php echo $rec->divisionContact; ?></td>
                  <td class="data-title">Division Email:</td>
                  <td class="data-input"><?php echo $rec->divisionEmail; ?></td>
                  <td class="data-input"></td>
                </tr>
                <tr>
                  <td class="data-title">Division Head:</td>
                  <td class="data-input"><?php echo $rec->fname.' '.$rec->mname.' '.$rec->lname; ?></td>
                  <td class="data-title">Head Title:</td>
                  <td class="data-input"><?php echo $rec->divisionHeadTitle; ?></td>
                </tr>
                <tr>
                  <td class="data-title">Status:</td>
                  <td class="data-input"><?php if($rec->status==1){ echo "Active"; }else{ echo "Inactive"; } ?></td>
                  <td class="data-title">Remarks:</td>
                  <td class="data-input"><?php echo $rec->remarks; ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
