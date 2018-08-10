<?php 
class Payrollcalc {
		
	public function wtax($employmentID, $taxID, $salary, $payrollPeriod="")  
	{
		$CI =& get_instance();
		
		$records = array();
		
		$totalDeduction	  = 0;
		$taxableDeduction = 0;
		$taxableIncentive = 0;
		
		$firstHalf = false;
		if ($payrollPeriod->type == 'SM') {
			$firstHalf = (date('d',strtotime($payrollPeriod->startDate)) == '01') ? true : false;
		}
		
		// get tax exemptions
		$CI->db->where('taxID', $taxID);
		$exemption = $CI->db->get('tax_exemptions', 1)->row();
		
		
		// calculate contribution
		$result = $this->contribution($employmentID, $salary, $payrollPeriod);
		$totalDeduction 	= $result['totalEmployeeShare'];
		$taxableDeduction 	= $result['taxableDeduction'];			
		
		// calculate loans
		$result = $this->loan($employmentID, $salary, $payrollPeriod);
		$totalDeduction 	+= $result['totalDeduction'];
		$taxableDeduction 	+= $result['taxableDeduction'];		
		
		// calculate incentives
		$result = $this->incentive($employmentID, $payrollPeriod);
		$totalIncentive 	= $result['totalIncentive'];
		$taxableIncentive 	= $result['taxableIncentive'];
		
		$totalGross  = $salary + $totalIncentive; // gross
		$netPay 	 = $totalGross - $totalDeduction; // montly income
		
		// calculate withholding tax
		$CI->db->where('employmentID', $employmentID);
		$CI->db->where('status', 1);
		$withholding = $CI->db->get('tax_withholding');
		 
		// gross taxable
		$taxableIncome	= ($netPay + $taxableDeduction + $taxableIncentive) - $totalIncentive;					
		$taxableIncome *= 12;
		
		if ($exemption->personalExemption > 0) {
			// deduct personal and additional exemptions
			$taxableIncome = $taxableIncome - ($exemption->personalExemption + $exemption->additionalExemption);							
		}
	
		if ($taxableIncome > 0) {
			if ($taxableIncome <= 10000) {
				$withholdingTax = $taxableIncome * 0.05;
			} else if ($taxableIncome <= 30000) {
				$withholdingTax = 500 + (($taxableIncome - 10000) * 0.1);
			} else if ($taxableIncome <= 70000) {
				$withholdingTax = 2500 + (($taxableIncome - 30000) * 0.15);
			} else if ($taxableIncome <= 140000) {
				$withholdingTax = 8500 + (($taxableIncome - 70000) * 0.2);
			} else if ($taxableIncome <= 250000) {
				$withholdingTax = 22500 + (($taxableIncome - 140000) * 0.25);
			} else if ($taxableIncome <= 500000) {
				$withholdingTax = 50000 + (($taxableIncome - 250000) * 0.3);
			} else {
				$withholdingTax = 125000 + (($taxableIncome - 500000) * 0.34);
			}
				
			// monthly payroll
			$withholdingTax /= 12;
			// format before divide with 2 decimal only
			$withholdingTax = number_format($withholdingTax,2,'.','');
			$records['amount'] = $withholdingTax;
			
			if ($payrollPeriod->type == 'SM') {
				$records['amount']  = ($firstHalf) ? $this->fnumber_format($records['amount'] / 2, '.') : round($records['amount'] / 2, 2, PHP_ROUND_HALF_UP);
			}
			
			if ($payrollPeriod->type == 'SM') {
			    if ($pay->isPayment == '0') {
			        $records['amount']  = ($firstHalf) ? $this->fnumber_format($records['amount'] / 2, '.') : round($records['amount'] / 2, 2, PHP_ROUND_HALF_UP);
			    } elseif ($pay->isPayment == '1') {
			        $records['amount']  = ($firstHalf) ? $this->fnumber_format($records['amount'] / 2, '.') : 0;
			    } elseif ($pay->isPayment == '2') {
			        $records['amount']  = (!$firstHalf) ? $this->fnumber_format($records['amount'] / 2, '.') : 0;
			    }
			} 
		}	
		
		return $records;
	}
	
	public function incentive($employmentID, $payrollPeriod, $payslipID=0)
	{
		$CI =& get_instance();
	
		$records = array();
		$records['incentives']			= array();
		$records['totalIncentive'] 		= 0;
		$records['taxableIncentive'] 	= 0;
		
		$firstHalf = false;
		if ($payrollPeriod->type == 'SM') {
			$firstHalf = (date('d', strtotime($payrollPeriod->startDate)) == '01') ? true : false;
		}
		
		// get active incentives
		$CI->db->select('incentive_details.*');
		$CI->db->select('incentives.incentiveTypeID');
		$CI->db->from('incentive_details');
		$CI->db->join('incentives','incentive_details.payID=incentives.payID','left');
		$CI->db->where('incentive_details.employmentID', $employmentID);
		if ($payrollPeriod->endDate) {
			$CI->db->where('incentive_details.effectivity <=', $payrollPeriod->endDate);
		}
		$CI->db->where('incentive_details.status', 1);
		$CI->db->where('incentives.status', 1);
		$incentives = $CI->db->get();
		
		if ($incentives->num_rows()) {
			foreach ($incentives->result() as $pay) {
				$info = array();
				$info['payslipID'] 		 = $payslipID;
				$info['payID'] 			 = $pay->payID;
				$info['incentiveTypeID'] = $pay->incentiveTypeID;
				
			    if ($payrollPeriod->type == 'SM') {
			        if ($pay->isPayment == '0') {
			            $info['amount']  	 = ($firstHalf) ? $this->fnumber_format($pay->amount / 2, '.') : round($pay->amount / 2, 2, PHP_ROUND_HALF_UP);
			        } elseif ($pay->isPayment == '1') {
			            $info['amount']  	 = ($firstHalf) ? round($pay->amount, 2, PHP_ROUND_HALF_UP) : 0;
			        } elseif ($pay->isPayment == '2') {
			            $info['amount']  	 = (!$firstHalf) ? round($pay->amount, 2, PHP_ROUND_HALF_UP) : 0;
			        }
			    } else {
			        $info['amount']  	 = round($pay->amount, 2, PHP_ROUND_HALF_UP);
			    }
			    
				if ($info['amount'] > 0) {
    				$records['incentives'][] = $info;
    				$records['totalIncentive'] += $info['amount'];
				}
			}
		}
		
		// check total allowances for any excess of maximum non-taxable allowance
		$max_non_taxable_allowance = $CI->config_model->getConfig('Maximum Non-Taxable Allowance');
		if ($records['totalIncentive'] > $max_non_taxable_allowance) {
			$records['taxableIncentive'] = $records['totalIncentive'] - $max_non_taxable_allowance;
		}			
	
		return $records;
	}
	
	public function contribution($employmentID, $salary, $payrollPeriod="", $payslipID=0)
	{
		$CI =& get_instance();
	
		$records = array();
		$records['contributions']		= array();
		$records['totalEmployeeShare'] 	= 0;
		$records['totalEmployerShare'] 	= 0;
		$records['totalContribution'] 	= 0;
		$records['totalDeduction']  	= 0;
		$records['taxableDeduction']	= 0;
		
		$firstHalf = false;
		if ($payrollPeriod->type == 'SM') {
			$firstHalf = (date('d',strtotime($payrollPeriod->startDate)) == '01') ? true : false;
		}
	
		// get active contributions
		$CI->db->select('contribution_details.*');
		$CI->db->select('premiums.premiumID');
		$CI->db->select('premiums.name');
		$CI->db->select('premiums.isTaxable');
		$CI->db->from('contribution_details');
		$CI->db->join('contributions','contribution_details.payID=contributions.payID','left');
		$CI->db->join('premiums','contributions.premiumID=premiums.premiumID','left');
		$CI->db->where('contribution_details.employmentID', $employmentID);
		if ($payrollPeriod->endDate) {
			$CI->db->where('contribution_details.effectivity <=', $payrollPeriod->endDate);
		}
		$CI->db->where('contribution_details.status', 1);
		$CI->db->where('contributions.status', 1);
		$contributions = $CI->db->get();
		
		if ($contributions->num_rows()) {
			foreach ($contributions->result() as $pay) { 
				$info = array();
				$info['payslipID'] = $payslipID;
				$info['payID'] 	   = $pay->payID;
				$info['premiumID'] = $pay->premiumID;
				
				if ($pay->isAutomatic) {
					$CI->db->where('premiumID', $pay->premiumID);
					$CI->db->where('startSalary <=', $salary);
					$CI->db->where('endSalary >=', $salary);
					$share = $CI->db->get('schedule_contribution', 1)->row();
						
					if (!empty ($share)) {
						$pay->employeeShare = ($share->shareType == 1) ? $share->employeeShare : $salary * ($share->employeeShare / 100);
						$pay->employerShare = ($share->shareType == 1) ? $share->employerShare : $salary * ($share->employerShare / 100);
					}
				}
				
				if ($payrollPeriod->type == 'SM') {
				    if ($pay->isPayment == '0') {
				        $info['employeeShare']	= ($firstHalf) ? $this->fnumber_format($pay->employeeShare / 2, '.') : round($pay->employeeShare / 2, 2, PHP_ROUND_HALF_UP);
					    $info['employerShare']	= ($firstHalf) ? $this->fnumber_format($pay->employerShare / 2, '.') : round($pay->employerShare / 2, 2, PHP_ROUND_HALF_UP);
				    } elseif ($pay->isPayment == '1') {
				        $info['employeeShare']	= ($firstHalf) ? $this->fnumber_format($pay->employeeShare / 2, '.') : 0;
					    $info['employerShare']	= ($firstHalf) ? $this->fnumber_format($pay->employerShare / 2, '.') : 0;
				    } elseif ($pay->isPayment == '2') {
				        $info['employeeShare']	= (!$firstHalf) ? $this->fnumber_format($pay->employeeShare / 2, '.') : 0;
					    $info['employerShare']	= (!$firstHalf) ? $this->fnumber_format($pay->employerShare / 2, '.') : 0;
				    }
				} else {
				    $info['employeeShare']	= round($pay->employeeShare, 2, PHP_ROUND_HALF_UP);
					$info['employerShare']	= round($pay->employerShare, 2, PHP_ROUND_HALF_UP);
				}
				
				$info['totalContribution']  = $info['employeeShare'] + $info['employerShare'];
					
				$records['totalEmployeeShare']   += $info['employeeShare'];
				$records['totalEmployerShare']   += $info['employerShare'];
				
				if ($pay->isTaxable && !$pay->isWtaxExempted) {					
					$records['taxableDeduction'] 	 += $info['employeeShare'];
				} else {
					if ($payrollPeriod->type == 'SM') {
						$records['taxableDeduction'] += ($firstHalf) ? $this->fnumber_format($pay->taxableAmount / 2, '.') : round($pay->taxableAmount / 2, 2, PHP_ROUND_HALF_UP);
					} else {
						$records['taxableDeduction'] += $pay->taxableAmount;
					}
					
					if ($payrollPeriod->type == 'SM') {
					    if ($pay->isPayment == '0') {
					        $records['taxableDeduction'] += ($firstHalf) ? $this->fnumber_format($pay->taxableAmount / 2, '.') : round($pay->taxableAmount / 2, 2, PHP_ROUND_HALF_UP);
					    } elseif ($pay->isPayment == '1') {
					        $records['taxableDeduction'] += ($firstHalf) ? $this->fnumber_format($pay->taxableAmount / 2, '.') : 0;
					    } elseif ($pay->isPayment == '2') {
					        $records['taxableDeduction'] += (!$firstHalf) ? $this->fnumber_format($pay->taxableAmount / 2, '.') : 0;
					    }
					} else {
					    $records['taxableDeduction'] += $pay->taxableAmount;
					}
				}	

				$records['contributions'][] = $info;
			}
		}
	
		return $records;
	}
	
	public function loan($employmentID, $salary, $payrollPeriod="", $payslipID=0)
	{
		$CI =& get_instance();
	
		$records = array();
		$records['loans']			= array();
		$records['totalDeduction']  = 0;
		$records['taxableDeduction']= 0;
	
		$firstHalf = false;
		if ($payrollPeriod->type == 'SM') {
			$firstHalf = (date('d',strtotime($payrollPeriod->startDate)) == '01') ? true : false;
		}
	
		// get active loans
		$CI->db->select('loan_details.isPayment');
		$CI->db->select('loan_details.amount as amortization');
		$CI->db->select('loans.*');
		$CI->db->select('loan_types.isTaxable');
		$CI->db->from('loan_details');
		$CI->db->join('loans','loan_details.payID=loans.payID','left');
		$CI->db->join('loan_types','loans.loanTypeID=loan_types.loanTypeID','left');
		$CI->db->where('loan_details.employmentID', $employmentID);
		if ($payrollPeriod->endDate) {
			$CI->db->where('loans.startDate <=', $payrollPeriod->endDate);
		}
		$CI->db->where('loan_details.status', 1);
		$CI->db->where('loans.status', 1);
		$loans = $CI->db->get();
			
		if ($loans->num_rows()) {
			foreach ($loans->result() as $pay) {	
				$info = array();
				$info['payslipID'] 		 = $payslipID;
				$info['payID'] 			 = $pay->payID;
				$info['type']			 = 2;
				$info['deductionID'] 	 = $pay->loanTypeID;
				
				if ($payrollPeriod->type == 'SM') {
				    if ($pay->isPayment == '0') {
    				    if ($pay->balance >= $pay->amortization) {
    						$info['amount']  = ($firstHalf) ? $this->fnumber_format($pay->amortization / 2, '.') : round($pay->amortization / 2, 2, PHP_ROUND_HALF_UP);
    					} else {
    						$info['amount']  = ($firstHalf) ? $this->fnumber_format($pay->balance / 2, '.') : round($pay->balance / 2, 2, PHP_ROUND_HALF_UP);
    					}
				    } elseif ($pay->isPayment == '1') {
				        if ($pay->balance >= $pay->amortization) {
    						$info['amount']  = ($firstHalf) ? round($pay->amortization, 2, PHP_ROUND_HALF_UP) : 0;
    					} else {
    						$info['amount']  = ($firstHalf) ? round($pay->balance, 2, PHP_ROUND_HALF_UP) : 0;
    					}
				    } elseif ($pay->isPayment == '2') {
				        if ($pay->balance >= $pay->amortization) {
    						$info['amount']  = (!$firstHalf) ? round($pay->amortization, 2, PHP_ROUND_HALF_UP) : 0;
    					} else {
    						$info['amount']  = (!$firstHalf) ? round($pay->balance, 2, PHP_ROUND_HALF_UP) : 0;
    					}
				    }
				} else {
				    $info['amount']  	 = ($pay->balance >= $pay->amortization) ? round($pay->amortization, 2, PHP_ROUND_HALF_UP) : round($pay->balance, 2, PHP_ROUND_HALF_UP);
				}
				
				if ($info['amount'] > 0) {
    				$records['loans'][] 	 = $info;
    				$records['totalDeduction'] 	+= $info['amount'];										
    				
    				if ($pay->isTaxable) {							
    					$records['taxableDeduction'] += $info['amount'];
    				}
				}
			}
		}
	
		return $records;
	}
	
	public function contribution_sched($premiumID, $salary)
	{
		$CI =& get_instance();
	
		$records = array();
		$records['employeeShare'] = 0;
		$records['employerShare'] = 0;
	
		$CI->db->where('premiumID', $premiumID);
		$CI->db->where('startSalary <=', $salary);
		$CI->db->where('endSalary >=', $salary);
		$share = $CI->db->get('schedule_contribution', 1)->row();
			
		if (!empty ($share)) {
			$records['employeeShare'] = ($share->shareType == 1) ? $share->employeeShare : $salary * ($share->employeeShare / 100);
			$records['employerShare'] = ($share->shareType == 1) ? $share->employerShare : $salary * ($share->employerShare / 100);
		}
		
		return $records;
	}
	
	public function wtax_sched($taxID, $salary)
	{
		$CI =& get_instance();
		
		$records = array();
		
		$totalDeduction	  = 0;
		$taxableDeduction = 0;
		$taxableIncentive = 0;
		
		// get tax exemptions
		$CI->db->where('taxID', $taxID);
		$exemption = $CI->db->get('tax_exemptions', 1)->row();
		
		
		// calculate contribution
		$result = $this->contribution($employmentID, $salary, $payrollPeriod);
		$totalDeduction 	= $result['totalEmployeeShare'];
		$taxableDeduction 	= $result['taxableDeduction'];			
		
		// calculate loans
		$result = $this->loan($employmentID, $salary, $payrollPeriod);
		$totalDeduction 	+= $result['totalDeduction'];
		$taxableDeduction 	+= $result['taxableDeduction'];		
		
		// calculate incentives
		$result = $this->incentive($employmentID, $payrollPeriod);
		$totalIncentive 	= $result['totalIncentive'];
		$taxableIncentive 	= $result['taxableIncentive'];
		
		$totalGross  = $salary + $totalIncentive; // gross
		$netPay 	 = $totalGross - $totalDeduction; // montly income
		
		// calculate withholding tax
		$CI->db->where('employmentID', $employmentID);
		$CI->db->where('status', 1);
		$withholding = $CI->db->get('tax_withholding');
		 
		// gross taxable
		$taxableIncome	= ($netPay + $taxableDeduction + $taxableIncentive) - $totalIncentive;					
		$taxableIncome *= 12;
		
		if ($exemption->personalExemption > 0) {
			// deduct personal and additional exemptions
			$taxableIncome = $taxableIncome - ($exemption->personalExemption + $exemption->additionalExemption);							
		}
	
		if ($taxableIncome > 0) {
			if ($taxableIncome <= 10000) {
				$withholdingTax = $taxableIncome * 0.05;
			} else if ($taxableIncome <= 30000) {
				$withholdingTax = 500 + (($taxableIncome - 10000) * 0.1);
			} else if ($taxableIncome <= 70000) {
				$withholdingTax = 2500 + (($taxableIncome - 30000) * 0.15);
			} else if ($taxableIncome <= 140000) {
				$withholdingTax = 8500 + (($taxableIncome - 70000) * 0.2);
			} else if ($taxableIncome <= 250000) {
				$withholdingTax = 22500 + (($taxableIncome - 140000) * 0.25);
			} else if ($taxableIncome <= 500000) {
				$withholdingTax = 50000 + (($taxableIncome - 250000) * 0.3);
			} else {
				$withholdingTax = 125000 + (($taxableIncome - 500000) * 0.34);
			}
				
			// monthly payroll
			$withholdingTax /= 12;
			// format before divide with 2 decimal only
			$withholdingTax = number_format($withholdingTax,2,'.','');
			$records['amount'] = $withholdingTax;
		}	
		
		return $records;
	}
	
	function fnumber_format($number,$decimal = '.',$place = '2')
	{
		$broken_number = explode($decimal,$number);
	
		if(empty($broken_number[1])) {
			return $broken_number[0];
		} else {
			return $broken_number[0].$decimal.substr($broken_number[1], 0,2);
		}
	}
}