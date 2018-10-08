<style type="text/css">

</style>

<div class="subheader">
	<div class="d-flex align-items-center">
		<div class="title mr-auto">
			<h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></h3>
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
							<div class="row">
								<div class="col-md-12 inline" >

									<h4 class="head-text">Sales Report</h4>
								</div>
							</div>
						</div>

					</div>
				</div>
				<!-- <div class="card-body"> -->
					<!-- Tab 1 start -->
					<div class="card-body card-body-div ">
						
							<div class="row card-body-row ">

								<div class="col-md-6 inline">	

                                    <select class="form-control" id="branchID" name="branchID" data-live-search="true" livesearchnormalize="true" title="Branch" required ">
                                        <option value="" selected>&nbsp;</option>
                                        <?php
                                        	if($branches){                                        		
	                                        	foreach($branches as $branch) {	 ?>
		                                            <option value="<?php echo $branch->branchID ?>" 
		                                            	<?php if($branch->branchID == $this->session->userdata('current_user')->branchID ) 
		                                            	echo "selected"?> ><?php echo $branch->branchName ?>
		                                        	</option>
                                          <?php }
                                        	}else{ ?>
		                                            <option value="<?php echo $branch->branchID ?>" 
		                                            	<?php if($branch->branchID == $this->session->userdata('current_user')->branchID ) 
		                                            	echo "selected"?> ><?php echo $branch->branchName ?>
		                                        	</option>
                                           <?php } ?>
                                    </select>
								</div>

								<div class="col-md-6 inline">								
                                    <select class="form-control" id="configID" name="configID" data-live-search="true" livesearchnormalize="true" title="Config" required>
                                        <option value="" selected>&nbsp;</option>                                        	
                                            <option value="<?php echo $config->configID ?>" selected><?php echo $config->value ?></option>
                                    </select>
								</div>
							</div>

                        <div class="container">
                            <div class="row">
                                <div class="col-sm">
                                    <canvas id="myChart" width="400" height="400"></canvas>
                                </div>
                                <div class="col-sm">
                                    <canvas id="myChart2" width="400" height="400"></canvas>
                                </div>
                                <div class="col-sm">

                                </div>
                            </div>
                        </div>


						
					</div>
					<!-- Tab 1 End -->
				<!-- </div> -->
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

    var ctx = document.getElementById("myChart");
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
            datasets: [{
                label: '# of Votes',
                data: [12, 19, 3, 5, 2, 3],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            }
        }
    });

    var ctx2 = document.getElementById("myChart2");
    var myDoughnutChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [10, 20, 30]
            }],

            // These labels appear in the legend and in the tooltips when hovering different arcs
            labels: [
                'Red',
                'Yellow',
                'Blue'
            ]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            }
        }
    });

	/*changeBranch();



	function salesReport(total){
		$('#salesReportChart').highcharts({

			chart: {
				type: 'column'
			},
			title: {
				text: ''
			},
			subtitle: {
				text: ''
			},
			colors: ['rgb(76, 186, 209, 0.75)'],
			xAxis: {
				gridLineWidth: 0,
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May' , 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
				allowDecimals: false,
			},
			yAxis: {
				gridLineWidth: 0,
				min: 0,
				title: {
					text: 'Sales'
				}
			},
			tooltip: {
				width: 200,
				pointFormat: '<span class="text-primary"> <b>{series.name}</b></span>: {point.y:,.0f}<br>',
				crosshairs: true,
				shared: true
			},
			legend: false,
			plotOptions: {
				series: {
					lineWidth: 1,
					radius: 0,
					symbol: 'square'
				},
				area: {
					pointStart: 0,
					marker: {
						enabled: false,
						radius: 0,
						states: {
							hover: {
								enabled: true
							}
						}
					}
				}
			},
			series: [{
				name: 'Monthly Sales',
				data: total
			}]
		});
	}*/
	
</script>

