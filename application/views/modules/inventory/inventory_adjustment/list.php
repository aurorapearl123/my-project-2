<form name="frmFilter" id="frmFilter" method="POST" action="<?php echo $controller_page ?>/show">
	<input type="hidden" id="sortby" name="sortby" value="<?php echo $sortby ?>" />
	<input type="hidden" id="sortorder" name="sortorder" value="<?php echo $sortorder ?>" />
	<div class="subheader">
		<div class="d-flex align-items-center">
			<div class="title mr-auto">
                <ul class="tools-list">
                    <li>
                        <h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></span></h3>
                    </li>
                    <li>
                        <!--                                        <input type="text" name="country" id="autocomplete" placeholder="Search"/>-->
                        <div class="input-group">
                            <input type="text" class="search-bar-my pl-25" placeholder="Search" id="autocomplete" >
                            <span class="position-absolute"><i class="icon left la la-search"></i> </span>
                        </div>

                    </li>
                </ul>
			</div>
			<?php if ($roles['create']) {?>
			<div class="subheader-tools">
				<a href="<?php echo $controller_page?>/create" class="btn btn-primary btn-raised btn-sm pill"><i class="icon left la la-plus"></i>Add New</a>
			</div>
			<?php } ?>
		</div>
	</div>
	<div class="content">
		<div class="row">
			<div class="col-12">
				<div class="card-box full-body">
					<div class="card-head">
						<div class="head-caption">
							<div class="head-title">
								<h4 class="head-text"><?php echo $current_module['module_label'] ?> List</h4>
							</div>
						</div>
						<div class="card-head-tools">
							<ul class="tools-list">
								<li>
									<button id="btn-apply" type="submit" class="btn btn-primary btn-xs pill collapse multi-collapse show">Apply Filter</button>
								</li>
								<li>
									<button type="button" id="btn-filter" class="btn btn-outline-light bmd-btn-icon active" data-toggle="tooltip" data-placement="bottom" title="Filters" onclick="#"><i class="la la-sort-amount-asc"></i></button>
								</li>
								<li>
									<button type="button" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Print" onclick="popUp('<?php echo site_url('inventory_adjustment/printlist') ?>', 800, 500)"><i class="la la-print"></i></button>
								</li>
								<li>
									<button type="button" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Export to Excel File" onclick="window.location='<?php echo site_url('inventory_adjustment/exportlist') ?>';"><i class="la la-file-excel-o"></i></button>
								</li>
							</ul>
						</div>
					</div>
					<!--  sorting_asc -->
					<div class="card-body">
						<div class="datatables_wrapper">
							<div class="table-responsive scrollable-wrap">
								<table class="table table-striped hover">
									<thead>
										<tr class="thead-light">
											<?php 
												if($this->session->userdata('current_user')->isAdmin){
												$headers = array(
												    array('column_header'=>'Adj No','column_field'=>'adjNo','width'=>'w-5','align'=>''),
												    array('column_header'=>'Date','column_field'=>'date','width'=>'w-10','align'=>''),
												    array('column_header'=>'Branch Name','column_field'=>'branchName','width'=>'w-10','align'=>''),
												    array('column_header'=>'Brand','column_field'=>'brand','width'=>'w-10','align'=>''),
												    array('column_header'=>'Item','column_field'=>'item','width'=>'w-10','align'=>''),
												    // array('column_header'=>'Description','column_field'=>'description','width'=>'w-10','align'=>''),
												    array('column_header'=>'UMSR','column_field'=>'umsr','width'=>'w-5','align'=>''),
												    array('column_header'=>'Adjustment Type (+/-)','column_field'=>'adjType','width'=>'w-10','align'=>''),	    
												    array('column_header'=>'QTY','column_field'=>'qty','width'=>'w-5','align'=>''),
												    array('column_header'=>'Status','column_field'=>'status','width'=>'w-5','align'=>'center'),
												);
												}else{

												$headers = array(
												    array('column_header'=>'Adj No','column_field'=>'adjNo','width'=>'w-5','align'=>''),
												    array('column_header'=>'Date','column_field'=>'date','width'=>'w-10','align'=>''),
												    array('column_header'=>'Brand','column_field'=>'brand','width'=>'w-10','align'=>''),
												    array('column_header'=>'Item','column_field'=>'item','width'=>'w-10','align'=>''),
												    // array('column_header'=>'Description','column_field'=>'description','width'=>'w-10','align'=>''),
												    array('column_header'=>'UMSR','column_field'=>'umsr','width'=>'w-5','align'=>''),
												    array('column_header'=>'Adjustment Type (+/-)','column_field'=>'adjType','width'=>'w-10','align'=>''),	    
												    array('column_header'=>'QTY','column_field'=>'qty','width'=>'w-5','align'=>''),
												    array('column_header'=>'Status','column_field'=>'status','width'=>'w-5','align'=>'center'),
												);
												}
												echo $this->htmlhelper->tabular_header($headers, $sortby, $sortorder);
												?>
										</tr>

									</thead>
									<tbody>
										<?php 
											if (count($records)) {
											    foreach($records as $row) {
											    ?>
										<tr onclick="location.href='<?php echo $controller_page."/view/".$row->adjID; ?>'">
											<td><?php echo $row->adjNo ?></td>
											<td><?php echo date('F d Y', strtotime($row->date))   ?></td>	
											<?php if($this->session->userdata('current_user')->isAdmin){ ?>
											<td><?php echo $row->branchName ?></td>
											<?php } ?>
											<td><?php echo $row->brand ?></td>
											<td><?php echo $row->item ?></td>
											<td><?php echo $row->umsr ?></td>
											<td style="text-align:center">
												<?php 
													if ($row->adjType == 'DR') {
														echo "<span class='badge badge-pill badge-info'>Debit</span>";
													} else if ($row->adjType == 'CR') {
														echo "<span class='badge badge-pill badge-info'>Credit</span>";
													} 
												?>
											</td>
											<td><?php echo $row->qty ?></td>											
											<td align="center">
												<?php 
													if ($row->status == 1) {
														echo "<span class='badge badge-pill badge-warning'>Pending</span>";
													} else if ($row->status == 2) {
														echo "<span class='badge badge-pill badge-success'>Confirmed</span>";
													} else if ($row->status == 0) {
														echo "<span class='badge badge-pill badge-danger'>Cancelled</span>";
													} 
												?>
											</td>
										</tr>
										<?php }
											} else {	?>
										<tr>
											<td colspan="11" align="center"> <i>No records found!</i></td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
							<div class="datatable-footer d-flex">
								<div class="datatable-pagination">
									Pages: &nbsp;&nbsp; 
								</div>
								<div class="datatable-pagination">
									<?php 
										$pagination = $this->pagination->create_links(); 
										
										if ($pagination) {
										     echo $pagination;      
										} else {
										    echo "1";
										}
										?>
								</div>
								<div class="datatable-pager-info float-right ml-auto">
									<div class="d-flex">
										<div class="datatable-pager-size">
											<div class="dataTables_length">
												<select aria-controls="table1" class="form-control select-sm pill" tabindex="-98" id="limit" name="limit" onchange="$('#frmFilter').submit();">
													<option value="25" <?php if ($limit==25) echo "selected"; ?>>25</option>
													<option value="50" <?php if ($limit==50) echo "selected"; ?>>50</option>
													<option value="75" <?php if ($limit==75) echo "selected"; ?>>75</option>
													<option value="100" <?php if ($limit==100) echo "selected"; ?>>100</option>
													<option value="125" <?php if ($limit==125) echo "selected"; ?>>125</option>
													<option value="150" <?php if ($limit==150) echo "selected"; ?>>150</option>
													<option value="175" <?php if ($limit==175) echo "selected"; ?>>175</option>
													<option value="200" <?php if ($limit==200) echo "selected"; ?>>200</option>
													<option value="all" <?php if ($limit=='All') echo "selected"; ?>>All</option>
												</select>
											</div>
										</div>
										<div class="datatable-pager-detail">
											<div class="dataTables_info">Displaying <?php echo ($offset+1) ?> - <?php if ($offset+$limit < $ttl_rows) { echo ($offset+$limit); } else  { echo $ttl_rows; } ?> of <?php echo number_format($ttl_rows,0)?> records</div>
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
</form>


<script>
    $(document).ready(function(){
        var base_url = '<?php echo base_url(); ?>';
        var $project = $('#autocomplete');
        $project.autocomplete({
            source: function( request, response ) {
                var url = "<?php echo $controller_page ?>/elastic_search";

                $.ajax({
                    url: url,
                    data: { search : request.term},
                    type: 'POST',
                    dataType: 'json',
                    success: function (data) {
                        console.log("the data");
                        console.log(data);
                        var customer = [];
                        $.each(data, function(k,v){
                            $.each(v, function(key, val){
                                //console.log("key", key);
                                //console.log("value", val.customer);
                                //console.log("image", val.profile);
                                var name = val.remarks;
                                var profile = "";
                                // var url = URL.createObjectURL(val.profile);
                                if(typeof name === 'undefined') {

                                }else {
                                    customer.push({
                                        "label": name,
                                        "value": name,
                                        "branch": val.branch,
                                        "date": val.date,
                                        "icon": "jquery_32x32.png",
                                        "adjustment" : val.adjustment,
                                        "profile" : profile,
                                        "status" : val.status,
                                        "id" : val.id,
                                        "remarks" : val.remarks,
                                        "item" : val.item,
                                        "item_id" : val.item_id
                                    });
                                }

                            });
                        });

                        response(customer);

                    },
                    error: function (errorMessage) { // error callback

                        console.log("error "+errorMessage.responseText);
                    }
                });
                //console.log(request.term);

                //response(cCities);
            },
            select: function(event, ui) {
                //$('#zipCode').val(zipCode[ui.item.value]);
                console.log("you click me");
                console.log(ui.item.id);
                var url = '<?php echo $controller_page."/view/"; ?>';
                location.href=url+ui.item.id;

                //get all data to display on table
            },
        })

        $project.data( "ui-autocomplete" )._renderItem = function( ul, item ) {

            var $li = $('<li>'),
                $img = $('<img class="search-image">');
            //var urlCreator = window.URL || window.webkitURL;
            //var imageUrl = URL.createObjectURL( item.profile );
            var image = item.profile  ? item.profile : base_url+'assets/img/users/noimage.gif';
            $img.attr({
                // src: 'https://jqueryui.com/resources/demos/autocomplete/images/' + item.icon,
                src: image,
                alt: item.value
            });


            $li.attr('data-value', item.value);
            $li.append('<a href="#">');
            //$li.find('a').append($img).append(item.value +" "+item.date);
            $li.find('a').append($img)
                .append($('<span>').attr('class', 'result-search').text(item.item)
                    .append($('<br>'))
                    .append($('<span style="font-size: 0.7em;">').text(item.value))
                );

            return $li.appendTo(ul);

        };
    });
</script>

