<script type="text/javascript">
$(function () {
    var chart;
    
    $(document).ready(function () {
    	
    	// Build the chart
        $('#container').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Total Employees Per Department'
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: 'Employees Percentage',
                data: [
					<?php 
					foreach ($departments as $d) {
					$this->db->where('deptID',$d->deptID);
  					$this->db->where('status',1);
  					$rows = $this->db->count_all_results('employments');
  					?>
  					
                    ['<?php echo $d->deptName ?>',   <?php echo $rows ?>],
                    <?php } ?>
                ]
            }]
        });
    });
    
});
</script>
<script type="text/javascript">
$(function () {
    var chart;
    
    $(document).ready(function () {

      $('#container_2').highcharts({
    chart: {
        type: 'column'
    },
    title: {
        text: 'Tardiness stats <?php echo date("M, Y"); ?>'
    },
    subtitle: {
        text: 'Total percentage of lates per department.'
    },
    xAxis: {
        type: 'category'
    },
    yAxis: {
        title: {
            text: 'Total late percentage'
        }

    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:.1f}%'
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
    },
    series: [
        {
            name: "Department",
            colorByPoint: true,
            data: [
            <?php foreach ($deptTardyArray as $dept) { ?>
                {
                    name: '<?php echo $dept["name"]; ?>',
                    y: parseInt('<?php echo $dept["total_percentage"]; ?>'),
                    drilldown: "Chrome"
                },
            <?php } ?>
            ]
        }
    ]

      });
    });
    
});
</script>

<div class="content">
  <div class="row">
    <div class="col-12">
      <!-- <div class="card-box"> -->
        <div class="card-body">
          <div class="data-view">
            <table width="100%">
              <tbody>
                <tr>
                  <td style="width:50%" valign="top">
                  	<table class="table table-striped hover" align=center" width="100%">
                  		<thead>
	                  		<tr>
	                  			<td align="center" style="border-left:1px solid #EAF2F8" nowrap>DEPARTMENT</td>
	                  			<td align="center" style="border-left:1px solid #EAF2F8;border-right:1px solid #EAF2F8" nowrap>STAFF</td>
	                  		</tr>
                  		</thead>
                  		<?php 
	                  		foreach ($departments as $dep) {
                  		?>
                  		<tr>
                  			<td align="left" style="border-left:1px solid #EAF2F8" nowrap>
                  				<?php echo $dep->deptCode.' - '.$dep->deptName?>
                  			</td>
                  			<td align="center" style="width:100px; border-left:1px solid #EAF2F8; border-right:1px solid #EAF2F8" nowrap>
                  				<?php 
                  					$this->db->where('deptID',$dep->deptID);
                  					$this->db->where('status',1);
                  					echo $rows = $this->db->count_all_results('employments');
                  					$ttl += $rows;
                  				?>
                  			</td>
                  		</tr>
                  		<?php } ?>
                  		<tr>
                  			<td align="right" style="border-left:1px solid #EAF2F8" nowrap>TOTAL EMPLOYEES : </td>
                  			<td align="center" style="border-left:1px solid #EAF2F8;border-right:1px solid #EAF2F8" nowrap>
                  				<?php echo $ttl;?>
                  			</td>
                  		</tr>
                  	</table>
                  	<br>
                  	
                  </td>
                  <td valign="top">
                  	<table class="table table-striped hover" align=center">
                  		<thead>
                  		<tr>
                        <td align="center" style=" border-left:1px solid #EAF2F8" nowrap>JOB TITLES</td>
                        <td align="center" style=" border-left:1px solid #EAF2F8" nowrap>Occupied</td>
                  			<td align="center" style=" border-left:1px solid #EAF2F8" nowrap>Vacant</td>

                  			<td align="center" style=" border-left:1px solid #EAF2F8;border-right:1px solid #EAF2F8" nowrap>POSITIONS</td>
                  		</tr>
                  		</thead>
                  		<?php 
	                  		foreach ($jobs as $job) {
                  		?>
                  		<tr>
                        <!-- vacant 1, occupied 2 -->
                  			<td align="left" style="border-left:1px solid #EAF2F8" nowrap>
                  				<?php echo $job->code.' - '.$job->jobTitle?>
                  			</td>
                        <td align="center" style="border-left:1px solid #EAF2F8" nowrap>
                          <?php 
                            $this->db->where('jobTitleID',$job->jobTitleID);
                            $this->db->where('status',2);
                            echo $rows = $this->db->count_all_results('job_positions');
                          ?>
                        </td>
                        <td align="center" style="border-left:1px solid #EAF2F8" nowrap>
                          <?php 
                            $this->db->where('jobTitleID',$job->jobTitleID);
                            $this->db->where('status',1);
                            echo $rows = $this->db->count_all_results('job_positions');
                          ?>
                        </td>
                  			<td align="center" style="border-left:1px solid #EAF2F8;border-right:1px solid #EAF2F8" nowrap>
                  				<?php 
                  					$this->db->where('jobTitleID',$job->jobTitleID);
                  					$this->db->where('status >',0);
                  					echo $rows = $this->db->count_all_results('job_positions');
                  					$ttl += $rows;
                  				?>
                  			</td>
                  		</tr>
                  		<?php } ?>
                  		<tr>
                  			<td align="right" style="border-left:1px solid #EAF2F8" nowrap>TOTAL : </td>
                        <td align="center" style="border-left:1px solid #EAF2F8;border-right:1px solid #EAF2F8" nowrap>
                        </td>
                        <td align="center" style="border-left:1px solid #EAF2F8;border-right:1px solid #EAF2F8" nowrap>
                        </td>
                  			<td align="center" style="border-left:1px solid #EAF2F8;border-right:1px solid #EAF2F8" nowrap>
                  				<?php echo $ttl;?>
                  			</td>
                  		</tr>
                  	</table>
                  </td>
                </tr>

                <!-- Next set of rows -->
                <tr>
                  <td><div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div></td>
                  <td><!-- Chart Here -->
                    <div id="container_2" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
<!--       </div> -->
    </div>
  </div>
</div>
