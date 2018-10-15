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
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <span>order sales</span>
                            <div class="container">
                                <div class="row">
                                    <div class="col-sm">
                                        <select class="form-control" id="branchID" name="branchID" data-live-search="true" livesearchnormalize="true" title="Branch" >
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
                                    <div class="col-sm">
                                        <select class="form-control" id="year" name="year" data-live-search="true" livesearchnormalize="true" title="Year" required>
                                            <option value="" selected>&nbsp;</option>
                                            <?php foreach($years as $year) { ?>
                                                <option value="<?php echo $year ?>"<?php if($year == Date('Y-m-d')){echo "selected";}?>><?php echo $year?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm">
                                        <button style="font-size:24px" id="search"> <i class="la la-search"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div id="salesReportChart" style="min-width: 310px; height:500px"></div>
                        </div>
                        <div class="col">
                            <span>Expense</span>
                            <div class="container">
                                <div class="row">
                                    <div class="col-sm">
                                        <select class="form-control" id="expense-branchID" name="branchID" data-live-search="true" livesearchnormalize="true" title="Branch" >
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
                                    <div class="col-sm">
                                        <select class="form-control" id="expense-year" name="year" data-live-search="true" livesearchnormalize="true" title="Year" required>
                                            <option value="" selected>&nbsp;</option>
                                            <?php foreach($years as $year) { ?>
                                                <option value="<?php echo $year ?>"<?php if($year == Date('Y-m-d')){echo "selected";}?>><?php echo $year?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm">
                                        <button style="font-size:24px" id="search-expense"> <i class="la la-search"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div id="salesReportChart2" style="min-width: 310px; height:500px"></div>
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

    $(function(){
        var branchID = $('#branchID').val();
        var year = $('#year').val();

        orderSales(branchID, year);
        expenseSales(branchID, year);

        $('#search').on("click", function(){
            console.log("on click");
            var branchID = $('#branchID').val();
            var year = $('#year').val();
            //console.log("year ", year);
            //console.log("branch id", branchID);
            orderSales(branchID, year);
        });



        $('#search-expense').on("click", function(){
            console.log("on click expense id");
            var branchID = $('#expense-branchID').val();
            var year = $('#expense-year').val();
            //console.log("year ", year);
            //console.log("branch id", branchID);
            expenseSales(branchID, year);
        });

    });

    function orderSales(branchID, year){

        //var branchID = $('#branchID').val();

        console.log("get order sales");
        $.ajax({
            method: 'POST',
            url: '<?php echo base_url("dashboard/getChartOrderSales") ?>',
            data: { branchID: branchID, year: year },
            dataType: 'json',
            success: function(data) {
                //console.log("get order sales");
                //console.log(data);
                salesReport(data);
            }
        });
    }


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
    }

    //expense
    function expenseSales(branchID, year){

        var branchID = $('#expense-branchID').val();
        var year = $('#expense-year').val();

        $.ajax({
            method: 'POST',
            url: '<?php echo base_url("dashboard/getChartExpense") ?>',
            data: { branchID: branchID, year: year },
            dataType: 'json',
            success: function(data) {

                expenseReport(data);
            }
        });
    }

    function expenseReport(total){
        $('#salesReportChart2').highcharts({

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
    }

</script>

