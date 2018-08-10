<?php 
class Dtrlog {
		
	public function analyze($employmentID, $current)  
	{
		$CI =& get_instance();
		
		$records = array();
		
		// get payroll details
		$CI->db->where('date', date('Y-m-d', $current));		
		$payroll_details = $CI->db->get('payroll_details');
			
		$dates = array();
		if ($payroll_details->num_rows()) {
			foreach ($payroll_details->result() as $row) {
				$dates[strtotime($row->date)] = $row->eventCode;
			}
		}
		
		$current_date = date('Y-m-d', $current);
		$next_date = date('Y-m-d', strtotime('+1 day', $current));
		
		$work_hours = intval($CI->config_model->getConfig('Total Working Hours For Flexible Hours Shift Per Day'));
		
		// get daily time records
		$CI->db->select('attendance.*');
		$CI->db->select('shifts.shiftName');
		$CI->db->select('shifts.shiftType');
		$CI->db->select('shifts.workHours');
		$CI->db->select('shifts.in1');
		$CI->db->select('shifts.out1');
		$CI->db->select('shifts.in2');
		$CI->db->select('shifts.out2');
		$CI->db->select('shifts.startTardy1');
		$CI->db->select('shifts.startTardy2');
		$CI->db->from('attendance');
		$CI->db->join('shifts','attendance.shiftID=shifts.shiftID','left');
		$CI->db->where('attendance.employmentID', $employmentID);
		$CI->db->where('attendance.attendanceType', 1);
		$CI->db->where('attendance.date', date('Y-m-d', $current));					
		$CI->db->order_by('attendance.date', 'asc');
		$CI->db->order_by('attendance.login1', 'asc');
		$query = $CI->db->get();
		
		if ($query->num_rows()) {
			foreach ($query->result() as $row) {
				$startTardy1 = ((strtotime($row->startTardy1)) >= strtotime($row->in1)) ? strtotime($current_date.' '.$row->startTardy1) : strtotime($next_date.' '.$row->startTardy1);
				$startTardy2 = ((strtotime($row->startTardy2)) >= strtotime($row->in1)) ? strtotime($current_date.' '.$row->startTardy2) : strtotime($next_date.' '.$row->startTardy2);
				$startOut1   = ((strtotime($row->out1)) > strtotime($row->in1)) ? strtotime($current_date.' '.$row->out1) : strtotime($next_date.' '.$row->out1);
				$startOut2   = ((strtotime($row->out2)) > strtotime($row->in1)) ? strtotime($current_date.' '.$row->out2) : strtotime($next_date.' '.$row->out2);
				
				$info = array();
				$info['base'] 			= date('Y-m-d', $current);
				$info['attendanceID'] 	= $row->attendanceID;
				$info['attendanceType'] = $row->attendanceType;
				$info['shiftName'] 		= $row->shiftName;
				$info['shiftID'] 		= $row->shiftID;
				$info['shiftType'] 		= $row->shiftType;
				$info['date']			= $row->date;
				$info['in1']			= $row->in1;
				$info['out1']			= $row->out1;
				$info['in2']			= $row->in2;
				$info['out2']			= $row->out2;
				$info['login1'] 		= ($row->login1 != '0000-00-00 00:00:00') ? $row->login1 : '';
				$info['logout1']		= ($row->logout1 != '0000-00-00 00:00:00') ? $row->logout1 : '';
				$info['login2'] 		= ($row->login2 != '0000-00-00 00:00:00') ? $row->login2 : '';
				$info['logout2']		= ($row->logout2 != '0000-00-00 00:00:00') ? $row->logout2 : '';
				$info['hours']	    	= $row->hours;
				$info['tardy']	    	= $row->tardy;
				$info['undertime']		= $row->undertime;
				$info['remarks']		= $row->remarks;
				$info['remarks_long']	= $row->remarks;
				$info['note']		    = $row->note;
				$info['workHours']		= $row->workHours;
				$info['schedule']     = 1;
				
				if ($row->shiftID == -2) {
					$row->workHours = 8;
				}

				if ($info['hours'] < $row->workHours && $info['remarks'] != 'DAY OFF') { 
					// check leaves
					$query = "SELECT `leaves`.`leaveID`, `leaves`.`leaveNo`, `leave_types`.`code`, `leave_types`.`leaveType`, `leave_dates`.`status`, `leave_dates`.`workCover`, `leave_dates`.`days`, `leave_dates`.`hours`, `leave_dates`.`startHour`, `leave_dates`.`endHour`
							  FROM (`leave_dates`)
							  LEFT JOIN `leaves` ON `leave_dates`.`leaveID`=`leaves`.`leaveID`
							  LEFT JOIN `leave_types` ON `leaves`.`leaveTypeID`=`leave_types`.`leaveTypeID`
							  WHERE `leaves`.`employmentID` = ".$employmentID."
							  	AND `leaves`.`status` = 2
						  		AND `leave_dates`.`status` > 1
							    AND '".date('Y-m-d', $current)."' >= `leave_dates`.`startDate`
							    AND '".date('Y-m-d', $current)."' <= `leave_dates`.`endDate`";
					$res = $CI->db->query($query);
					if (!$res->num_rows()) {
						// check orders
						$query = "SELECT `orders`.`orderID`, `orders`.`orderNo`, `orders`.`type`, `order_details`.`status`, `order_details`.`workCover`, `order_details`.`days`, `order_details`.`hours`, `order_details`.`startHour`, `order_details`.`endHour`
								  FROM (`order_details`)
								  LEFT JOIN `orders` ON `order_details`.`orderID`=`orders`.`orderID`
								  WHERE `order_details`.`employmentID` = ".$employmentID."
								  	AND `order_details`.`status` = 2
								    AND '".date('Y-m-d', $current)."' >= `order_details`.`startDate`
								    AND '".date('Y-m-d', $current)."' <= `order_details`.`endDate`";
						$res = $CI->db->query($query);
						if (!$res->num_rows()) {
							// check for half days
							if ($dates[$current]=="HDA") {
								$hours = ($row->workHours / 2);
								$info['remarks']     .= ' Half Day';											
									
								if ($row->tardy > 0) {
									$info['hours']	  = (($row->hours + $hours) <= $row->workHours) ? ($row->hours + $hours) : $row->workHours;
									$info['tardy'] 	  = ($row->tardy <= ($hours * 60)) ? 0 : ($row->tardy - ($hours * 60));
									$info['period']   = 2;
								}
							} elseif ($dates[$current]=="HDP") {
								$hours = ($row->workHours / 2);
								$info['remarks']     .= ' Half Day';											
									
								if ($row->undertime > 0) {
									$info['hours']	  = (($row->hours + $hours) <= $row->workHours) ? ($row->hours + $hours) : $row->workHours;
									$info['undertime']= ($row->undertime <= ($hours * 60)) ? 0 : ($row->undertime - ($hours * 60));
								}
							} else {
								if ($info['hours'] > 0) {
									if ($row->tardy > 0) {
										if ($info['login1'] && strtotime($info['login1']) > $startTardy1) {
											$info['tardy_date']	= date('ja', strtotime($info['login1']));
											$info['period'] = 1;
										}
										if ($info['login2'] && strtotime($info['login2']) > $startTardy2) {
											$info['tardy_date']	.= date(' ja', strtotime($info['login2']));
											$info['period'] = 2;
										}									
									}
									if ($row->undertime > 0) { 
										if ($info['logout1'] && strtotime($info['logout1']) < $startOut1) { 
											$info['ut_date']	= date('ja', strtotime($info['logout1']));
											 
										} elseif (!$info['logout1'] || !$info['login1']) { 
											$info['ut_date']	= date('ja', $startOut1);
										}
										if ($info['logout2'] && strtotime($info['logout2']) < $startOut2) {  
											$info['ut_date']	.= date(' ja', strtotime($info['logout2']));
										} elseif ($info['logout2']!= '' || $info['login2'] != '') { 
											$info['ut_date']	.= date(' ja', $startOut2);
										}
									}
									$info['tardy']  	= $row->tardy;
									$info['undertime']  = $row->undertime;
								} else {
									$info['tardy'] 	    = 0;
									$info['undertime']  = 0;
									$info['remarks']    = 'ABSENT';
								}
							}
						} else {
							$rec = $res->row();
							$order_hours = $rec->hours;
							
							$info['los']		  = 'ORDR';
							$info['los_day']	  = $rec->hours / $row->workHours;
							$info['url'] 	   	  = site_url('order/view/'.$CI->encrypter->encode($rec->orderID).'/1');
							$info['withPay']   	  = 1;
							switch ($rec->type) {
								case 1 : $info['remarks'] = $rec->orderNo; $info['remarks_long'] = "On Memorandum Order [".$rec->orderNo."]"; break;
								case 2 : $info['remarks'] = $rec->orderNo; $info['remarks_long'] = "On Office Order [".$rec->orderNo."]"; break;
								case 3 : $info['remarks'] = $rec->orderNo; $info['remarks_long'] = "On Official Travel [".$rec->orderNo."]"; break;
							}
							$info['hours']	      = (($row->hours + $order_hours) <= $row->workHours) ? ($row->hours + $order_hours) : $row->workHours;
								
							if ($row->tardy > 0 && ($rec->workCover==2 || $rec->workCover==4)) {
								$info['tardy'] 	  = ($row->tardy <= ($order_hours * 60)) ? 0 : ($row->tardy - ($order_hours * 60));
								$order_hours     -= ($info['tardy'] / 60);
							}
							if ($row->undertime > 0 && ($rec->workCover==2 || $rec->workCover==4)) {
								$info['undertime']= ($row->undertime <= ($order_hours * 60)) ? 0 : ($row->undertime - ($order_hours * 60));
							}
						}
					} else { 
						$rec = $res->row();
						$leave_hours = $rec->hours;
						
						$info['los']		  = $rec->code;
						$info['los_day']	  = $rec->hours / $row->workHours;
						$info['url'] 	   	  = site_url('leave/view/'.$CI->encrypter->encode($rec->leaveID).'/1');
						$info['withPay']   	  = ($rec->status==2) ? 1 : 0;
						$info['remarks']   	  = $rec->code.' #'.$rec->leaveNo;
						$info['remarks_long'] = $rec->leaveType.' ['.$rec->leaveNo.']';
						$info['hours']	      = (($row->hours + $leave_hours) <= $row->workHours) ? ($row->hours + $leave_hours) : $row->workHours;
											
						if ($row->tardy > 0 && ($rec->workCover==2 || $rec->workCover==4)) {
							$info['tardy'] 	  = ($row->tardy <= ($leave_hours * 60)) ? 0 : ($row->tardy - ($leave_hours * 60));																		 
							$leave_hours     -= ($info['tardy'] / 60);
						}
						if ($row->undertime > 0 && ($rec->workCover==3 || $rec->workCover==4)) {
							$info['undertime']= ($row->undertime <= ($leave_hours * 60)) ? 0 : ($row->undertime - ($leave_hours * 60));
						} 
					}
					
					if ((($info['tardy'] + $info['undertime']) / 60) >= $row->workHours) {
						$info['tardy'] 	   = 0;
						$info['undertime'] = 0;
						$info['remarks']   = 'ABSENT';
					} 
				}								
				
				$info['eventCode']		= $dates[strtotime($row->date)];
				$records[] 		= $info;
			}
		} else {
			// check shift schedule
			$CI->db->select('shifts.workHours');
			$CI->db->from('shift_schedules');
			$CI->db->join('shifts','shift_schedules.shiftID=shifts.shiftID', 'left');
			$CI->db->where('shift_schedules.employmentID', $employmentID);	
			$CI->db->where('shift_schedules.shiftID >', 0);
			$CI->db->where('shift_schedules.date', date('Y-m-d', $current));
			$sched = $CI->db->get();
			
			if ($sched->num_rows()) {			
				// check leaves
				$query = "SELECT `leaves`.`leaveID`, `leaves`.`leaveNo`, `leave_types`.`code`, `leave_types`.`leaveType`, `leave_dates`.`status`, `leave_dates`.`days`, `leave_dates`.`hours`  
						  FROM (`leave_dates`)
						  LEFT JOIN `leaves` ON `leave_dates`.`leaveID`=`leaves`.`leaveID` 
						  LEFT JOIN `leave_types` ON `leaves`.`leaveTypeID`=`leave_types`.`leaveTypeID`
						  WHERE `leaves`.`employmentID` = ".$employmentID."
						  	AND `leaves`.`status` = 2
					  		AND `leave_dates`.`status` > 1
						    AND '".date('Y-m-d', $current)."' >= `leave_dates`.`startDate` 
						    AND '".date('Y-m-d', $current)."' <= `leave_dates`.`endDate`";
				$res = $CI->db->query($query);
				if (!$res->num_rows()) {
					// check orders
					$query = "SELECT `orders`.`orderID`, `orders`.`orderNo`, `orders`.`type`, `order_details`.`status`, `order_details`.`days`, `order_details`.`hours`
						  FROM (`order_details`)
						  LEFT JOIN `orders` ON `order_details`.`orderID`=`orders`.`orderID`
						  WHERE `order_details`.`employmentID` = ".$employmentID."
						  	AND `order_details`.`status` = 2
						    AND '".date('Y-m-d', $current)."' >= `order_details`.`startDate`
						    AND '".date('Y-m-d', $current)."' <= `order_details`.`endDate`";
					$res = $CI->db->query($query);
					if (!$res->num_rows()) {
						// check suspensions
						$query = "SELECT `suspensions`.`suspensionID`, `suspensions`.`suspensionNo`, `suspensions`.`type`, `suspensions`.`status`
						  FROM (`suspension_details`)
						  LEFT JOIN `suspensions` ON `suspension_details`.`suspensionID`=`suspensions`.`suspensionID`
						  WHERE `suspension_details`.`employmentID` = ".$employmentID."
						  	AND `suspension_details`.`status` = 2
						    AND '".date('Y-m-d', $current)."' >= `suspensions`.`startDate`
						    AND '".date('Y-m-d', $current)."' <= `suspensions`.`endDate`";
						$res = $CI->db->query($query);
						if (!$res->num_rows()) {
							// check for holidays and no work
							if ($dates[$current]=="HDA") {
								$shift = $sched->result();
								$info = array();
								$info['base'] 	      = date('Y-m-d', $current);
								$info['attendanceID'] = 0;
								$info['tardy']   	  = ($shift[0]->workHours / 2) * 60;
								$info['withPay']   	  = 0;
								$info['remarks'] 	  = 'HALF DAY'; 
								$info['eventCode']    = $dates[$current];
								$info['schedule']     = 1;
								$records[]    = $info;
							} elseif ($dates[$current]=="HDP") {
								$shift = $sched->result()->row();
								$info = array();
								$info['base'] 	      = date('Y-m-d', $current);
								$info['attendanceID'] = 0;
								$info['undertime']    = ($shift[0]->workHours / 2) * 60;
								$info['withPay']   	  = 0;
								$info['remarks'] 	  = 'HALF DAY';
								$info['eventCode']    = $dates[$current];
								$info['schedule']     = 1;
								$records[]    = $info;
							} else {
								$info['base'] 	   	  = date('Y-m-d', $current);
								$info['attendanceID'] = 0;
								$info['withPay']   	  = 0;
								$info['remarks']   	  = 'ABSENT';
								$info['eventCode'] 	  = $dates[$current];
								$info['schedule']     = 1;
								$records[] 	  = $info;
								$rec = $res->row();
							}
						} else {	
							$rec = $res->row();
							$info  = array();
							$info['base'] 	   	  = date('Y-m-d', $current);
							$info['attendanceID'] = 0;
							$info['url'] 	   	  = site_url('suspension/view/'.$CI->encrypter->encode($rec->suspensionID).'/1');
							$info['withPay']   	  = ($rec->status==2) ? 1 : 0;
							$info['remarks']   	  = $rec->suspensionNo;
							$info['los']		  = 'SUSPN';
							$info['los_day']	  = 1;
							$info['eventCode'] 	  = $dates[$current];
							$info['schedule']     = 1;
							$records[] 	  = $info;
						}
					} else {
						$rec = $res->row();
						$info  = array();
						$info['base'] 	   	  = date('Y-m-d', $current);
						$info['attendanceID'] = 0;
						$info['url'] 	   	  = site_url('order/view/'.$CI->encrypter->encode($rec->orderID).'/1');
						$info['withPay']   	  = 1;
						switch ($rec->type) {
							case 1 : $info['remarks'] = $rec->orderNo; $info['remarks_long'] = "On Memorandum Order [".$rec->orderNo."]"; break;
							case 2 : $info['remarks'] = $rec->orderNo; $info['remarks_long'] = "On Office Order [".$rec->orderNo."]"; break;
							case 3 : $info['remarks'] = $rec->orderNo; $info['remarks_long'] = "On Official Travel [".$rec->orderNo."]"; break;
						}
						$info['los']		 = 'ORDR';
						$info['los_day']	 = 1;
						$info['eventCode']   = $dates[$current];
						$info['schedule']    = 1;
						$records[]   = $info;
					}
				} else {
					$rec = $res->row(); 
					$info  = array();
					$info['base'] 	   	  = date('Y-m-d', $current);
					$info['attendanceID'] = 0;
					$info['url'] 	   	  = site_url('leave/view/'.$CI->encrypter->encode($rec->leaveID).'/1');
					$info['withPay']   	  = ($rec->status==2) ? 1 : 0;
					$info['remarks']   	  = $rec->code.' ['.$rec->leaveNo.']';
					$info['remarks_long'] = $rec->leaveType.' ['.$rec->leaveNo.']';
					$info['los']		  = $rec->code;
					$info['los_day']	  = 1;
					$info['leave_date']	  = date('j', $current);
					$info['eventCode'] 	  = $dates[$current];
					$info['schedule']     = 1;
					$records[] 	  = $info;
				}
			} else {
				$info = array();
				$info['base'] 	      = date('Y-m-d', $current);
				$info['attendanceID'] = 0;
				switch ($dates[$current]) {
					default : $info['remarks'] = ''; break;
					case "RH" : $info['remarks'] = 'REGULAR HOLIDAY'; break;
					case "SH" : $info['remarks'] = 'SPECIAL HOLIDAY'; break;
				}
				$info['eventCode']    = $dates[$current];
				$info['schedule']     = 0;
				$records[]    = $info;
			}
		}
		return $records;
	}
	
	public function calculate($shift, $current_date, $login1, $logout1, $login2, $logout2)
	{
		$CI =& get_instance();
	
		$records = array();
		$noLog   = array();
		$prev_date    = date('Y-m-d', strtotime('-1 day', strtotime($current_date)));
		$next_date    = date('Y-m-d', strtotime('+1 day', strtotime($current_date)));
		
		/***** FIRST PERIOD *********************************/
		$login 		= strtotime($current_date.' '.$shift->in1);
		$logout     = ((strtotime($shift->out1)) > strtotime($shift->in1)) ? strtotime($current_date.' '.$shift->out1) : strtotime($next_date.' '.$shift->out1);
		$startLog   = ((strtotime($shift->startLog1)) <= strtotime($shift->in1)) ? strtotime($current_date.' '.$shift->startLog1) : strtotime($prev_date.' '.$shift->startLog1);
		$startTardy = ((strtotime($shift->startTardy1)) >= strtotime($shift->in1)) ? strtotime($current_date.' '.$shift->startTardy1) : strtotime($next_date.' '.$shift->startTardy1);
		$endLog     = $logout; 
		if ($shift->shiftType==1) {
			$work_min = floor(($logout - $login) / 60);
		} else {
			$work_min = floor($shift->workHours * 60);
		}
		
		// calculate first period
		if ($login1 || $logout1) {
			// calculate tardiness
			if ($login1) {
				$tardy += ($login1 > $startTardy) ? floor(($login1 - $login) / 60) : 0;
			} else {
				$tardy += $work_min;
			}			
				
			// calculate undertime
			if ($logout1) {
				$undertime += ($logout1 < $logout) ? floor(($logout - $logout1) / 60) : 0;
			} else {
				$undertime += $work_min;
			}

			// calculate hours
			if ($login1 && $logout1) {
				$in      = ($login1 < $login) ? $login : $login1;
				$in      = ($in <= $startTardy) ? $login : $login1;
				$out     = ($logout1 > $logout) ? $logout : $logout1;
				if ($shift->shiftType==1) {
					$ttl_min = (($out - $in) / 60); 
				} else {
					$ttl_min = (($out - $in) / 60) - ($shift->breakHours * 60);
				}
				
				$hours  += (($ttl_min / 60) <= ($work_min / 60)) ? ($ttl_min / 60) : ($work_min / 60);
			} else {
				$hours  += 0;
				$noLog[1] = true;
			}
		} else {
			$tardy += $work_min;
		}
			
		if ($shift->shiftType==1) {
			/***** SECOND PERIOD *********************************/
			$login 		= strtotime($current_date.' '.$shift->in2);
			$logout     = ((strtotime($shift->out2)) > strtotime($shift->in2)) ? strtotime($current_date.' '.$shift->out2) : strtotime($next_date.' '.$shift->out2);
			$startLog   = ((strtotime($shift->startLog2)) <= strtotime($shift->in2)) ? strtotime($current_date.' '.$shift->startLog2) : strtotime($prev_date.' '.$shift->startLog2);
			$work_min   = ($logout - $login) / 60;
				
			// get second login
			$startTardy = ((strtotime($shift->startTardy2)) >= strtotime($shift->in2)) ? strtotime($current_date.' '.$shift->startTardy2) : strtotime($next_date.' '.$shift->startTardy2);
			
			// get second logout
			$endLog = strtotime('+2 hours', $logout);
				
			if ($log->logTime) {
				$logout2 = strtotime($log->logTime);
			}
				
			// calculate second period
			if ($login2 || $logout2) {
				// calculate tardiness
				if ($login2) {
					$tardy += ($login2 > $startTardy) ? floor(($login2 - $login) / 60) : 0;
				} else {
					$tardy += $work_min;
				}
		
				// calculate undertime
				if ($logout2) {
					$undertime += ($logout2 < $logout) ? floor(($logout - $logout2) / 60) : 0;
				} else {
					$undertime += $work_min;
				}
		
				// calculate hours
				if ($login2 && $logout2) {
					$in      = ($login2 < $login) ? $login : $login2;
					$in      = ($in <= $startTardy) ? $login : $login2;
					$out     = ($logout2 > $logout) ? $logout : $logout2;
					$ttl_min = ($out - $in) / 60;
						
					$hours  += (($ttl_min / 60) <= ($work_min / 60)) ? ($ttl_min / 60) : ($work_min / 60);
				} else {
					$hours  += 0;
					$noLog[2] = true;
				}
			} else {
				$undertime += $work_min;;
			}
		}
		
		// recalculate hours
		$hours = ($hours > $shift->workHours) ? $shift->workHours : $hours;
		
		$records['tardy'] = $tardy;
		$records['undertime'] = $undertime;
		$records['hours'] = $hours;
		$records['noLog'] = $noLog;
	
		return $records;
	}
	
	function organize_log($empID, $employmentID, $current)
	{
		$CI =& get_instance();
		$batch = array();
		
		$current_date = date('Y-m-d', $current);
		$prev_date    = date('Y-m-d', strtotime('-1 day', $current));
		$next_date    = date('Y-m-d', strtotime('+1 day', $current));
		
		//$CI->db->select('biometricID');
		//$CI->db->where('empID', $empID);
		//$empID = $CI->db->get('employees', 1)->row()->biometricID;

		$CI->db->where('employmentID', $employmentID);
		$CI->db->where('date', $current_date);
		$exists = $CI->db->count_all_results('attendance');

		if (!$exists) { 
			// get shift for the day
			$CI->db->select('shift_schedules.shiftID as schedShiftID');
			$CI->db->select('shifts.*');
			$CI->db->from('shift_schedules');
			$CI->db->join('shifts','shift_schedules.shiftID=shifts.shiftID','left');
			$CI->db->where('shift_schedules.employmentID', $employmentID);
			$CI->db->where('shift_schedules.date', $current_date);
			//$CI->db->where('shifts.shiftID IS NOT NULL');
			$shifts = $CI->db->get();
			
			if ($shifts->num_rows()) {
				foreach ($shifts->result() as $shift) { 
					if ($shift->schedShiftID=='-3') {
						$CI->db->select('employments.allowedShiftID');
						$CI->db->from('employments');
						$CI->db->join('employees','employments.empID=employees.empID','left');						
						$CI->db->where('employees.empID', $empID);
						$CI->db->where('employments.status', 1);
						$CI->db->limit(1);
						$allowedShiftID = $CI->db->get()->row()->allowedShiftID;
						$allowedShifts  = explode(',', $allowedShiftID);
						
						// get login
						$CI->db->where('empID', $empID);
						$CI->db->like('logTime', date('Y-m-d', strtotime($current_date)));
						$CI->db->where_in('logType', 'C/In');
						$CI->db->order_by('logTime', 'asc');
						$login = $CI->db->get('kiosk_logs', 1)->row();
							
						if (!empty($login)) {
							$CI->db->where_in('shiftID', $allowedShifts);
							$CI->db->where('status', 1);
							$expectedShifts = $CI->db->get('shifts');
							
							if ($expectedShifts->num_rows()) {
								foreach ($expectedShifts->result() as $sh) {
									$expectedLogin 		= strtotime($current_date.' '.$sh->in1);
									$expectedStartLog 	= strtotime($current_date.' '.$sh->startLog1);
									$expectedLogout 	= ((strtotime($sh->out1)) > strtotime($sh->in1)) ? strtotime($current_date.' '.$sh->out1) : strtotime($next_date.' '.$sh->out1);								
										
									if (strtotime($login->logTime) >= $expectedStartLog && strtotime($login->logTime) <= strtotime('+4 hours', $expectedLogin)) { 
										$shift->schedShiftID = $sh->shiftID;
										break;
									}
								}
							}							
						}
					}

					if ($shift->schedShiftID!='-1' && $shift->schedShiftID!='-2' && $shift->schedShiftID!='-3') {
					    if ($shift->schedShiftID == $CI->config_model->getConfig('Office Hours Shift') && date('N', strtotime($current_date)) == 5) {
					       $shift->schedShiftID = $CI->config_model->getConfig('Office Hours Shift - Friday');
					    }
					    			
						$CI->db->where('shiftID', $shift->schedShiftID);
						$shift = $CI->db->get('shifts', 1)->row();

						$login1  = "";
						$logout1 = "";
						$login2  = "";
						$logout2 = "";
						$hours   = 0;
						$tardy   = 0;
						$undertime  = 0;
						$noLog   = array();
		
						/***** FIRST PERIOD *********************************/
						$login 		= strtotime($current_date.' '.$shift->in1);
						$logout     = ((strtotime($shift->out1)) > strtotime($shift->in1)) ? strtotime($current_date.' '.$shift->out1) : strtotime($next_date.' '.$shift->out1);
						$startLog   = ((strtotime($shift->startLog1)) <= strtotime($shift->in1)) ? strtotime($current_date.' '.$shift->startLog1) : strtotime($prev_date.' '.$shift->startLog1);
						if ($shift->shiftType==1) {
							$work_min = floor(($logout - $login) / 60);
						} else {
							$work_min = floor($shift->workHours * 60);
						}
		
						// get first login
						$startTardy = ((strtotime($shift->startTardy1)) >= strtotime($shift->in1)) ? strtotime($current_date.' '.$shift->startTardy1) : strtotime($next_date.' '.$shift->startTardy1);
						$endLog     = $logout;
		
						$CI->db->where('empID', $empID);
						$CI->db->where('logTime >=', date('Y-m-d H:i:s', $startLog));
						$CI->db->where('logTime <', date('Y-m-d H:i:s', $endLog));
						$CI->db->where('logType', 'C/In');
						$CI->db->order_by('logTime', 'asc');
						$log = $CI->db->get('kiosk_logs', 1)->row();
		
						if ($log->logTime) {
							$login1 = strtotime($log->logTime);
						}
		
						// get first logout
						if ($shift->shiftType==1) {
							$endLog = ((strtotime($shift->startLog2)) >= strtotime($shift->out1)) ? strtotime($current_date.' '.$shift->startLog2) : strtotime($next_date.' '.$shift->startLog2);
						} else {
							$endLog = strtotime('+6 hours', $logout);
						}
		
						$CI->db->where('empID', $empID);
						if ($login1) {
							$CI->db->where('logTime >', date('Y-m-d H:i:s', $login1));
						} else {
							$CI->db->where('logTime >', date('Y-m-d H:i:s', $startLog));
						}
						$CI->db->where('logTime <=', date('Y-m-d H:i:s', $endLog));
						$CI->db->where('logType', 'C/Out');
						$CI->db->order_by('logTime', 'asc');
						$log = $CI->db->get('kiosk_logs', 1)->row();

						if ($log->logTime) {
							$logout1 = strtotime($log->logTime);
						}
		
						// calculate first period
						if ($login1 || $logout1) {
							// calculate tardiness
							if ($login1) {
								$tardy += ($login1 > $startTardy) ? floor(($login1 - $login) / 60) : 0;
							} else {
								$tardy += $work_min;
							}
		
							// calculate undertime
							if ($logout1) {
								$undertime += ($logout1 < $logout) ? floor(($logout - $logout1) / 60) : 0;
							} else {
								$undertime += $work_min;
							}
		
							// calculate hours
							if ($login1 && $logout1) {
								$in      = ($login1 < $login) ? $login : $login1;
								$in      = ($in <= $startTardy) ? $login : $login1;
								$out     = ($logout1 > $logout) ? $logout : $logout1;
								if ($shift->shiftType==1) {
									$ttl_min = (($out - $in) / 60);
								} else {
									$ttl_min = (($out - $in) / 60) - ($shift->breakHours * 60);
								}
		
								$hours  += (($ttl_min / 60) <= ($work_min / 60)) ? ($ttl_min / 60) : ($work_min / 60);
							} else {
								$hours  += 0;
								$noLog[1] = true;
							}
						} else {
							$tardy += $work_min;
						}
						
						if ($shift->shiftType==1) {
							/***** SECOND PERIOD *********************************/
							$login 		= strtotime($current_date.' '.$shift->in2);
							$logout     = ((strtotime($shift->out2)) >= strtotime($shift->in2)) ? strtotime($current_date.' '.$shift->out2) : strtotime($next_date.' '.$shift->out2);
							$startLog   = ((strtotime($shift->startLog2)) <= strtotime($shift->in2)) ? strtotime($current_date.' '.$shift->startLog2) : strtotime($prev_date.' '.$shift->startLog2);
							$work_min   = ($logout - $login) / 60;
								
							// get second login
							$startTardy = ((strtotime($shift->startTardy2)) >= strtotime($shift->in2)) ? strtotime($current_date.' '.$shift->startTardy2) : strtotime($next_date.' '.$shift->startTardy2);
							$endLog     = $logout;
								
							$CI->db->where('empID', $empID);
							$CI->db->where('logTime >=', date('Y-m-d H:i:s', $startLog));
							$CI->db->where('logTime <', date('Y-m-d H:i:s', $endLog));
							$CI->db->where('logType', 'C/In');
							$CI->db->order_by('logTime', 'asc');
							$log = $CI->db->get('kiosk_logs', 1)->row();
								
							if ($log->logTime) {
								$login2 = strtotime($log->logTime);
							}
								
							// get second logout
							$endLog = strtotime('+6 hours', $logout);
								
							$CI->db->where('empID', $empID);
							if ($login2) {
								$CI->db->where('logTime >', date('Y-m-d H:i:s', $login2));
							} else {
								$CI->db->where('logTime >', date('Y-m-d H:i:s', $startLog));
							}
							$CI->db->where('logTime <=', date('Y-m-d H:i:s', $endLog));
							$CI->db->where('logType', 'C/Out');
							$CI->db->order_by('logTime', 'desc');
							$log = $CI->db->get('kiosk_logs', 1)->row();
								
							if ($log->logTime) {
								$logout2 = strtotime($log->logTime);
							}
								
							// calculate second period
							if ($login2 || $logout2) {
								// calculate tardiness
								if ($login2) {
									$tardy += ($login2 > $startTardy) ? floor(($login2 - $login) / 60) : 0;
								} else {
									$undertime += $work_min;
								}
		
								// calculate undertime
								if ($undertime < $work_min) {
									if ($logout2) {
										$undertime += ($logout2 < $logout) ? floor(($logout - $logout2) / 60) : 0;
									} else {
										$undertime += $work_min;
									}
								}
		
								// calculate hours
								if ($login2 && $logout2) {
									$in      = ($login2 < $login) ? $login : $login2;
									$in      = ($in <= $startTardy) ? $login : $login2;
									$out     = ($logout2 > $logout) ? $logout : $logout2;
									$ttl_min = ($out - $in) / 60;
		
									$hours  += (($ttl_min / 60) <= ($work_min / 60)) ? ($ttl_min / 60) : ($work_min / 60);
								} else {
									$hours  += 0;
									$noLog[2] = true;
								}
							} else {
								$undertime += $work_min;
							}
						}
		
						// recalculate hours
						$hours = ($hours > $shift->workHours) ? $shift->workHours : $hours;
		
						if ($login1 != "" || $logout1 != "" || $login2 != "" || $logout2 != "") {
							if ($noLog[1]) {
								$remarks = ($noLog[2]) ? 'UT' : 'Tardy';
							} elseif ($noLog[2]) {
								$remarks = ($tardy > 0 && $tardy < $work_min) ? 'Tardy' : '';
								$remarks .= ($remarks!="") ? '/UT' : 'UT';
							} else {
								$remarks = ($tardy > 0) ? 'Tardy' : '';
								if ($undertime > 0) {
									$remarks .= ($remarks!="") ? '/UT' : 'UT';
								}
							}
								
							$info = array();
							$info['empID'] 		  	= $empID;
							$info['employmentID'] 	= $employmentID;
							$info['shiftID'] 	    = $shift->shiftID;
							$info['date']         	= $current_date;
							$info['attendanceType'] = 1;
							$info['login1'] 	    = ($login1) ? date('Y-m-d H:i:s', $login1) : "0000-00-00 00:00:00";
							$info['logout1'] 	    = ($logout1) ? date('Y-m-d H:i:s', $logout1) : "0000-00-00 00:00:00";
							$info['login2'] 	    = ($login2) ? date('Y-m-d H:i:s', $login2) : "0000-00-00 00:00:00";
							$info['logout2'] 	    = ($logout2) ? date('Y-m-d H:i:s', $logout2) : "0000-00-00 00:00:00";
							$info['hours'] 			= $hours;
							$info['tardy'] 			= $tardy;
							$info['undertime'] 		= $undertime;
							$info['remarks'] 		= $remarks;
							$batch[] = $info;
						}
					} elseif ($shift->schedShiftID=='-2') {
						$work_min = intval($CI->config_model->getConfig('Total Working Hours For Flexible Hours Shift Per Day')) * 60;
						// get login
						$CI->db->where('empID', $empID);	
						$CI->db->like('logTime', date('Y-m-d', strtotime($current_date)));
						$CI->db->where_in('logType', 'C/In');						
						$CI->db->order_by('logTime', 'asc');
						$login = $CI->db->get('kiosk_logs', 1)->row();
						
						if (!empty($login)) {
							// get logout
							$CI->db->where('empID', $empID);
							$CI->db->where('logTime >', $login->logTime);
							$CI->db->where_in('logType', 'C/Out');
							$CI->db->order_by('logTime', 'asc');
							$logout = $CI->db->get('kiosk_logs', 1)->row();
							
							if (!empty($logout)) {
								$hours 		= 0;
								$tardy 		= 0;
								$undertime  = 0;
								$remarks	= '';																
																							
								// calculate hours
								$total      = strtotime($logout->logTime) - strtotime($login->logTime);
								$hours      = floor($total / 60 / 60);								
								$minutes    = floor((($total - ($hours * 60 * 60)) / 60) / 60);
									
								$hours 		= $hours.'.'.$minutes;
								if ($hours >= 8) { 
									$hours  = 8;
								} else {
									$hours  	= $total / 60 / 60;
									$undertime  = 480 - ($hours * 60);
									$remarks 	= 'UT';
								}
																							
								$info = array();
								$info['empID'] 		  	= $empID;
								$info['employmentID'] 	= $employmentID;
								$info['shiftID'] 	    = $shift->schedShiftID;
								$info['date']         	= $current_date;
								$info['attendanceType'] = 1;
								$info['login1'] 	    = (!empty($login)) ? $login->logTime : '0000-00-00 00:00:00';
								$info['logout1'] 	    = (!empty($logout)) ? $logout->logTime : '0000-00-00 00:00:00';
								$info['login2'] 	    = "0000-00-00 00:00:00";
								$info['logout2'] 	    = "0000-00-00 00:00:00";
								$info['hours'] 			= $hours;
								$info['tardy'] 			= 0;
								$info['undertime'] 		= $undertime;
								$info['remarks'] 		= $remarks;
								$batch[] = $info;
							}	
						}
					} elseif ($shift->schedShiftID=='-1') {
						$info = array();
						$info['empID'] 		  	= $empID;
						$info['employmentID'] 	= $employmentID;
						$info['shiftID'] 	    = $shift->schedShiftID;
						$info['date']         	= $current_date;
						$info['attendanceType'] = 1;
						$info['login1'] 	    = "0000-00-00 00:00:00";
						$info['logout1'] 	    = "0000-00-00 00:00:00";
						$info['login2'] 	    = "0000-00-00 00:00:00";
						$info['logout2'] 	    = "0000-00-00 00:00:00";
						$info['hours'] 			= 0;
						$info['tardy'] 			= 0;
						$info['undertime'] 		= 0;
						$info['remarks'] 		= 'DAY OFF';
						$batch[] = $info;
					}
				}
			}
		}	

		return $batch;
	}
}