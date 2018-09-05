<form name="frmFilter" id="frmFilter" method="POST" action="<?php echo $controller_page ?>/show">
    <input type="hidden" id="sortby" name="sortby" value="<?php echo $sortby ?>" />
    <input type="hidden" id="sortorder" name="sortorder" value="<?php echo $sortorder ?>" />
    <div class="subheader">
        <div class="d-flex align-items-center">
            <div class="title mr-auto">
                <h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></span></h3>
            </div>
            <!--<?php if ($roles['create']) {?>
			<div class="subheader-tools">
				<a href="<?php echo $controller_page?>/create" class="btn btn-primary btn-raised btn-sm pill"><i class="icon left la la-plus"></i>Add New</a>
			</div>
			<?php } ?>-->
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
                                    <button id="btn-apply"  class="btn btn-primary btn-xs pill collapse multi-collapse show">Apply Filter</button>
                                </li>
                                <!-- <li>
                                    <button type="button" id="btn-filter" class="btn btn-outline-light bmd-btn-icon active" data-toggle="tooltip" data-placement="bottom" title="Filters" onclick="#"><i class="la la-sort-amount-asc"></i></button>
                                </li> -->
                                <li>
                                    <button type="button" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Print" onclick="popUp('<?php echo site_url('stockcard/printlist') ?>', 800, 500)"><i class="la la-print"></i></button>
                                </li>
                                <li>
                                    <button type="button" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" title="Export to Excel File" onclick="window.location='<?php echo site_url('stockcard/exportlist') ?>';"><i class="la la-file-excel-o"></i></button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!--  sorting_asc -->
                    <div class="card-body">

                        <div class="datatable-header">
                            <table class="table-form">
                                <tbody>
                                <tr>
                                    <td class="form-label" width="10%" nowrap>
                                        <label>Branch : </label>
                                    </td>
                                    <td class="form-group form-input" width="22%">
                                        <?php
                                        $this->db->select('branches.*');
                                        $this->db->from('branches');
                                        $this->db->where('status',1);
                                        $this->db->where('branchID',$this->session->userdata('current_user')->branchID);
                                        $rec = $this->db->get()->row();
                                        ?>
                                        <!-- <select class="form-control" id="branchID" name="branchID" data-live-search="true" livesearchnormalize="true" style="" title="Branch Head">
											<?php foreach($recs as $rec) {?>
											<option value="<?php echo $this->session->userdata('current_user')->branchID ?>" <?php if($this->session->userdata('current_user')->branchID == $rec->branchID) echo "selected" ?> ><?php echo $rec->branchName ?></option>
											<?php } ?>
										</select> -->
                                        <input type="hidden" class="form-control " id="branchID" name="branchID" value="<?php echo $rec->branchID; ?>"  readonly >
                                        <input type="text" class="form-control " id="branchName" name="branchName" value="<?php echo $rec->branchName; ?>" title="Branch Name" readonly >
                                    </td>
                                    <td class="form-label" width="10%" nowrap>
                                        <label>Date Period : </label>
                                    </td>
                                    <td class="form-group form-input" width="13%">
                                        <input type="text" class="form-control datepicker" id="startDate" name="startDate" data-toggle="datetimepicker" value="" data-target="#startDate" title="Start Date" required>
                                    </td>
                                    <td class="form-group form-input" width="13%">
                                        <input type="text" valign="top" class="form-control datepicker" id="endDate" name="endDate" data-toggle="datetimepicker" value="" data-target="#endDate" title="End Date" required>
                                    </td>
                                    <td class="d-xxl-none"></td>
                                </tr>
                                <tr>
                                    <td>Item</td>
                                    <td>
                                        <?php
                                        $this->db->select('items.*');
                                        $this->db->from('items');
                                        $this->db->where('status',1);
                                        $recs = $this->db->get()->result();
                                        ?>
                                        <select class="form-control" id="itemID" name="itemID" data-live-search="true" livesearchnormalize="true" title="-Select Items-" >
                                            <?php foreach($recs as $res) {?>
                                                <option value="<?php echo $res->itemID ?>" <?php if ($res->itemID==1 ) echo "selected"; ?>  ><?php echo $res->brand .' '.$res->item.' '.$res->description.' '.$res->umsr ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
</form>
</div>
<div class="datatables_wrapper">
    <div class="table-responsive scrollable-wrap">
        <table class="table table-striped hover">
            <thead class="thead-light">

            <th>Date</th>
            <th>Beginning Balance</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>End Balance</th>
            <th>Reference No</th>
            </thead>
            <tbody id="stockCardBody">
            <?php
            if (count($record)) {
                foreach($record as $row) {
                    ?>
                    <tr >
                        <td><?php echo date('M d, Y', strtotime($row->date))   ?></td>
                        <td><?php echo $row->begBal ?></td>
                        <td><?php echo $row->debit ?></td>
                        <td><?php echo $row->credit ?></td>
                        <td><?php echo $row->endBal ?></td>
                        <td><?php echo $row->refNo ?></td>
                    </tr>
                <?php }
            } else {	?>
                <tr>
                    <td colspan="7" align="center"> <i>No records found!</i></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
</div>
</div>
</div>
</div>
</div>

<script>
    $(document).ready(function(){

        var date = new Date(), y = date.getFullYear(), m = date.getMonth();
        var firstDay = new Date(y, m, 1);
        // var lastDay = new Date(y, m + 1, 0);
        var currentDate = moment().format();


        firstDay = moment(firstDay).format('MMMM DD, YYYY');
        lastDay = moment(currentDate).format('MMMM DD, YYYY');

        $('#startDate').val(firstDay);
        $('#endDate').val(lastDay);

        $('#btn-apply').on("click", function(e) {
            e.preventDefault();
            var branchID = $('#branchID').val();
            var itemID = $('#itemID').val();
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();

            var from_date = new Date(startDate);
            //var str = mydate.toString("YYYY-MM-DD");
            from_day = from_date.getDate();
            from_year = from_date.getFullYear();
            from_month = from_date.getMonth() + 1;

            var date_to = new Date(endDate);
            //var str = mydate.toString("YYYY-MM-DD");
            to_day = date_to.getDate();
            to_year = date_to.getFullYear();
            to_month = date_to.getMonth() + 1;

            // console.log(date);
            // console.log(year);
            // console.log(month);

            var _date_from = from_year + "-" + from_month + "-" + from_day;

            var _date_to = to_year + "-" + to_month + "-" + to_day;


            $('#stockCardBody').empty();


            var url = "<?php echo $controller_page ?>///getFilterStockCard";

            var table = $("#stockCardBody");

            $.ajax({
                url: url,
                data: {date_from: _date_from, date_to: _date_to, branchID: branchID, itemID: itemID},
                type: 'POST',
                success: function (data) {
                    console.log("the data");
                    data = $.parseJSON(data);
                    $.each(data.data, function(k,v){
                        console.log(v);
                        table.append("<tr>" +
                            "<td>"+v.date+"</td>" +
                            "<td>"+v.begBal+"</td>" +
                            "<td>"+v.debit+"</td>" +
                            "<td>"+v.credit+"</td>" +
                            "<td>"+v.endBal+"</td>" +
                            "<td>"+v.refNo+"</td>" +
                            "</tr>");
                    });
                }

            });
        });
    });
</script>