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
									<button type="button" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Print" onclick="popUp('<?php echo site_url('order/printlist') ?>', 800, 500)"><i class="la la-print"></i></button>
								</li>
								<li>
									<button type="button" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Export to Excel File" onclick="window.location='<?php echo site_url('order/exportlist') ?>';"><i class="la la-file-excel-o"></i></button>
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
												    array('column_header'=>'PC No','column_field'=>'pcNo','width'=>'w-10','align'=>''),
												    array('column_header'=>'Date','column_field'=>'date','width'=>'w-15','align'=>''),
												    array('column_header'=>'Branch Name','column_field'=>'branchName','width'=>'w-20','align'=>''),
												    array('column_header'=>'Conducted By','column_field'=>'conductedBy','width'=>'w-20','align'=>''),
												    array('column_header'=>'Remarks','column_field'=>'remarks','width'=>'w-25','align'=>''),
												    
												    array('column_header'=>'Status','column_field'=>'status','width'=>'w-8','align'=>'center'),
												);
												}else{

												$headers = array(
												    array('column_header'=>'PC No','column_field'=>'pcNo','width'=>'w-10','align'=>''),
												    array('column_header'=>'Date','column_field'=>'date','width'=>'w-15','align'=>''),
												    array('column_header'=>'Conducted By','column_field'=>'conductedBy','width'=>'w-20','align'=>''),
												    array('column_header'=>'Remarks','column_field'=>'remarks','width'=>'w-25','align'=>''),
												    
												    array('column_header'=>'Status','column_field'=>'status','width'=>'w-8','align'=>'center'),
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
										<tr onclick="location.href='<?php echo $controller_page."/view/".$row->pcID; ?>'">
											
											<td><?php echo $row->pcNo ?></td>
											
											<td><?php echo date('F d, Y', strtotime($row->date))   ?></td>
											<?php if($this->session->userdata('current_user')->isAdmin){ ?>
											<td><?php echo $row->branchName ?></td>
											<?php } ?>
											<td><?php echo $row->conductedBy ?></td>
											<td><?php echo $row->remarks ?></td>
											
											<td align="center">
												<?php 
													if ($row->status == 2) {
														echo "<span class='badge badge-pill badge-success'>Confirmed</span>";
													} else if ($row->status == 1) {
														echo "<span class='badge badge-pill badge-warning'>Pending</span>";
													} else if ($row->status == 0) {
														echo "<span class='badge badge-pill badge-danger'>Cancelled</span>";
													}
													?>
											</td>
										</tr>
										<?php }
											} else {	?>
										<tr>
											<td colspan="6" align="center"> <i>No records found!</i></td>
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
    $(document).ready(function() {
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
                                var name = val.conducted_by;
                                var profile = "";
                                // var url = URL.createObjectURL(val.profile);
                                if(typeof name === 'undefined') {

                                }else {
                                    customer.push({
                                        "label": name,
                                        "value": name,
                                        "id": val.id,
                                        "date": val.date,
                                        "icon": "jquery_32x32.png",
                                        "profile" : profile,
                                        "remarks" : val.remarks,
                                        'status' : val.status
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
                //console.log("you click me");
                //console.log(ui.item.id);

                var url = '<?php echo $controller_page."/view/"; ?>';
                location.href=url+ui.item.id;


                //getRrHeaderBySupplierId(ui.item.suppID);
                //$('#rr-table-body').empty();
                //get all data to display on table
            },
        })

        $project.data( "ui-autocomplete" )._renderItem = function( ul, item ) {


            var $li = $('<li>'),
                $img = $('<img style="width:50px;height: 60px">');
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
                .append($('<span >').attr('class', 'result-search').text(item.value)
                    .append($('<br>'))
                    //.append($('<span style="font-size: 0.7em;">').text(item.date))
                    .append($('<span style="font-size: 0.7em;">').text("Remark :  "+item.remarks)
                    )
                    .append($('<br>'))
                    //.append($('<span style="font-size: 0.7em;">').text(item.date))
                    .append($('<span style="font-size: 0.7em;">').text("Date : "+item.date)
                    )
                );

            return $li.appendTo(ul);

        };
    });

    function getRrHeaderBySupplierId(suppID)
    {
        var url = "<?php echo $controller_page ?>/getRrHeaderBySupplierId";

        $.ajax({
            url: url,
            data: { suppID : suppID},
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                console.log("the data here");
                console.log(data);
                var paginate = data.links;
                console.log("paginate", paginate);
                var tBody = $('#rr-table-body');
                $.each(data.records, function(k,v){
                    //var orderID = v.orderID;
                    var url = '<?php echo $controller_page."/view/";?>';
                    var base_url = url+"/"+v.rrID;
                    var status_class = "";
                    var status_text = "";
                    switch (v.status) {
                        case "1":
                            status_text = 'Pending';
                            status_class = 'badge badge-pill badge-warning';
                            break;
                        case "2":
                            status_text = 'Confirmed';
                            status_class = 'badge badge-pill badge-info';
                            break;

                        default:
                            status_text = 'Cancelled';
                            status_class = 'badge badge-pill badge-warning';

                    }
                    //console.log("THE URL", base_url);
                    tBody.append($('<tr>').attr('onclick', 'location.href="'+base_url+'"')
                        .append($('<td>').text(v.referenceNo))
                        .append($('<td>').text(v.date))
                        .append($('<td>').text(v.branchName))
                        .append($('<td>').text(v.name))
                        .append($('<td>').text(v.ttlQty))
                        .append($('<td>').text(v.ttlAmount))
                        .append($('<td>')
                            .append($('<span>').attr('class', status_class).text(status_text))
                        )
                    );
                    $('.my-pagination').html();
                    $('.my-pagination').html(1);
                });

                //console.log("hello world");
                //$('#order-table-body').append($('<tr>')
                //    .append($('<td>'))
                //);

            },
            error: function (errorMessage) { // error callback

                console.log("error "+errorMessage.responseText);
            }
        });
    }
</script>

