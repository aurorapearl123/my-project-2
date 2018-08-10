<form name="frmFilter" id="frmFilter" method="POST" action="<?php echo $controller_page ?>/show">
          <div class="subheader">
            <div class="d-flex align-items-center">
              <div class="title mr-auto">
                <h3><i class="icon left ti-layout-column2"></i> Division <span class="counter">(<?php echo number_format($ttl_rows,0)?>)</span></h3>
              </div>
              <div class="subheader-tools">
                <a href="<?php echo $controller_page?>/create" class="btn btn-primary btn-raised btn-md pill"><i class="icon left la la-plus lg"></i>Add New</a>
              </div>
            </div>
          </div>
          <div class="content">
            <div class="row">
              <div class="col-12">
                <div class="card-box full-body">
                  <div class="card-head">
                    <div class="head-caption">
                      <div class="head-title">
                        <h4 class="head-text">Division List</h4>
                      </div>
                    </div>
                    <div class="card-head-tools">
                      <ul class="tools-list">
                        <li>
                          <button id="btn-apply" class="btn btn-primary btn-sm pill collapse multi-collapse show">Apply Filter</button>
                        </li>
                        <li>
                          <button type="button" id="btn-filter" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Filters" onclick="#"><i class="la la-sort-amount-asc"></i></button>
                        </li>
                        <li>
                          <button class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Print"><i class="la la-print"></i></button>
                        </li>
                        <li>
                          <button class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="PDF File"><i class="la la-file-pdf-o"></i></button>
                        </li>
                        <li>
                          <button class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Excel File"><i class="la la-file-excel-o"></i></button>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="datatables_wrapper">
                      <div class="table-responsive">
                        <table class="table table-striped has-img hover">
                          <thead>
                            <tr>
                              <th class="w-10 sorting_asc">Code</th>
                              <th class="w-15 sorting_desc">Division Name</th>
                              <th class="w-15 sorting_desc">Department</th>
                              <th class="w-15 sorting_desc">Branch</th>
                              <th class="w-15 sorting_asc">Company</th>
                              <th class="w-10 sorting_desc">Email</th>
                              <th class="w-15 sorting_desc">Head</th>
                              <th class="w-10">Status</th>
                            </tr>
                            <tr id="filter-group" class="collapse multi-collapse table-filter show">
                              <th class="form-group form-input">
                                <input type="text" class="form-control" id="divisionCode" name="divisionCode" style="width:100px">
                              </th>
                              <th class="form-group form-input">
                                <input type="text" class="form-control" id="divisionName" name="divisionName" style="width:150px">
                              </th>
                              <th class="form-group form-input">
                                <select id="deptID" name="deptID" class="form-control" data-live-search="true" liveSearchNormalize="true">
                                  <option value="">&nbsp;</option>
                                  <?php 
                                  	$results = $this->db->get('departments')->result();
                                  	foreach($results as $res){
                                  ?>
                                  	<option value="<?php echo $res->deptID?>"><?php echo $res->departmentName?></option>
                                  <?php }?>
                                </select>
                              </th>
                              <th class="form-group form-input">
                                <select id="branchID" name="branchID" class="form-control" data-live-search="true" liveSearchNormalize="true">
                                  <option value="">&nbsp;</option>
                                  <?php 
                                  	$results = $this->db->get('branches')->result();
                                  	foreach($results as $res){
                                  ?>
                                  	<option value="<?php echo $res->branchID?>"><?php echo $res->branchName?></option>
                                  <?php }?>
                                </select>
                              </th>
                              <th class="form-group form-input">
                                <select id="companyID" name="companyID" class="form-control" data-live-search="true" liveSearchNormalize="true">
                                  <option value="">&nbsp;</option>
                                  <?php 
                                  	$results = $this->db->get('companies')->result();
                                  	foreach($results as $res){
                                  ?>
                                  	<option value="<?php echo $res->companyID?>"><?php echo $res->companyName?></option>
                                  <?php }?>
                                </select>
                              </th>
                              <th class="form-group form-input">
                                <input type="text" class="form-control" id="divisionEmail" name="divisionEmail" style="width:150px">
                              </th>
                              <th class="form-group form-input">
                                <?php 
                                  $this->db->where('status',1);
                                  $heads = $this->db->get('employees')->result();
                                  ?>
                                <select class="form-control" id="branchHeadID" name="branchHeadID" data-live-search="true" livesearchNormalize="true">
	                                <option value="">&nbsp;</option>
	                                <?php foreach($heads as $row) {?>
	                                    <option value="<?php echo $row->empID ?>"><?php echo $row->lname.", ".$row->fname." ".$row->mname ?></option>
	                                <?php } ?>
                                </select>
                              </th>
                              <th>
                                <select class="form-control" id="status" name="status">
                                  <option value="">&nbsp;</option>
                                  <option value="1">Active</option>
                                  <option value="0">Inactive</option>
                                </select>
                              </th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php if ($records->num_rows()) {
                          foreach($records->result() as $row) {
                          ?>
                            <tr onclick="location.href='<?php echo $controller_page."/view/".$this->encrypter->encode($row->divisionID); ?>'">
                              <td><span><?php echo $row->divisionCode ?></span></td>
                              <td><span><?php echo $row->divisionName ?></span></td>
                              <td><span><?php echo $row->deptName ?></span></td>
                              <td><span><?php echo $row->branchName ?></span></td>
                              <td><span><?php echo $row->companyName ?></span></td>
                              <td><span><?php echo $row->divisionEmail ?></span></td>
                              <td><span><?php echo $row->fname.' '.$row->mname.' '.$row->lname ?></span></td>
                              <td>
                              		<?php 
                              			if($row->status == 1){
                              				echo "<span class='badge badge-pill badge-success'>Active</span>";
                              			}elseif($row->status == 0){
                              				echo "<span class='badge badge-pill badge-light'>Inactive</span>";
                              			}
                              		?>
                              </td>
                            </tr>
                            <?php }
                          	}?>
                          </tbody>
                        </table>
                      </div>
                      <div class="datatable-footer d-flex">
                        <div class="datatable-pagination">
                          <ul class="pagination">
                            <li class="page-item first disabled">
                              <a class="page-link" href="#" tabindex="-2"><i class="icon ti-angle-double-left"></i></a>
                            </li>
                            <li class="page-item prev disabled">
                              <a class="page-link" href="#" tabindex="-1"><i class="icon ti-angle-left"></i></a>
                            </li>
                            <?php 
//						      $pagination = $this->pagination->create_links(); 
//						      
//						      if ($pagination) {
//						        echo "Page: ".$pagination."&nbsp;&nbsp;| &nbsp; ";      
//						      }
//						      
//						      echo "Total Records: ".number_format($ttl_rows,0);
						      ?>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">4</a></li>
                            <li class="page-item"><a class="page-link" href="#">5</a></li>
                            <li class="page-item more"><span class="ellipsis">...</span></li>
                            <li class="page-item"><a class="page-link" href="#">8</a></li>
                            <li class="page-item next">
                              <a class="page-link" href="#"><i class="icon ti-angle-right"></i></a>
                            </li>
                            <li class="page-item last">
                              <a class="page-link" href="#"><i class="icon ti-angle-double-right"></i></a>
                            </li>
                          </ul>
                        </div>
                        <div class="datatable-pager-info float-right ml-auto">
                          <div class="d-flex">
                            <div class="datatable-pager-size">
                              <div class="dataTables_length">
                                <select name="table1_length" aria-controls="table1" class="form-control select-sm pill" tabindex="-98" id="limit" name="limit" onchange="changeDisplay();">
                                  <option value="25">25</option>
                                  <option value="50">50</option>
                                  <option value="75">75</option>
                                  <option value="100">100</option>
                                  <option value="125">125</option>
                                  <option value="150">150</option>
                                  <option value="175">175</option>
                                  <option value="200">200</option>
                                  <option value="all">All</option>
                                </select>
                              </div>
                            </div>
                            <div class="datatable-pager-detail">
                              <div class="dataTables_info">Displaying 1 - 25 of <?php echo number_format($ttl_rows,0)?> records</div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
 <script>
 function changeDisplay()
 {
 	$('#cmdFilter').val("Apply Filters");
 	$('#frmFilter').submit();
 }
 </script>
</form>