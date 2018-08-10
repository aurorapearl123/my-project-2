<?php
class Tableview
{
	public $width = '';
	public $height = '';


	// var $records;

	// public function __construct($records)
	// {
	// 	$this->records = $records;
	// }

	public function table_view($record, $column=1)
	{
		$view = '';

		$view .= '<div class="data-view"><table class="view-table">';
		if (!empty($record)) {
			foreach ($record as $rec) {
				$view .= '<tr>';

				$view .= '<td class="data-title" width="120" nowrap>'.$rec['label'].'</td>';
				$view .= '<td class="data-input" width="420" nowrap>'.$rec['val'].'</td>';

				$view .= '<td class="d-xxl-none" width=""></td>';
				$view .= '</tr>';
			}
		}
		$view .= '</table></div>';
		return $view;
	}


	public function table_normal($records, $display=[])
	{
		$view = '';

		$view .= '<table class="table">';
		$view .= '<thead class="'.$thead_class.'"><tr>';
		$view .= $this->htmlhelper->tabular_header($headers, $sortby, $sortorder);
		$view .= '</tr></thead>';
		$view .= '<tbody>';
		if (!empty($records)) {
			foreach($records as $row) {

				$view .= '<tr onclick="'.$controller_page.'./view/'.$id.'">';
				if (!empty($display)) {
					foreach ($display as $val) {
						$view .= '<td>'.$row->$val.'</td>';
					}
				}
				$view .= '</tr>';

			}
		} else {
			$view .= '<tr><td colspan="'.count($headers).'" align="center"> <i>No records found!</i></td></tr>';
		}
		$view .= '</tbody></table>';
		return $view;
	}

	public function table_striped_main($header='', $records, $display=[], $pfield='')
	{
		$view = '';

		$view .= '<table class="table table-striped">';
		$view .= '<thead><tr>';
		$view .= $header;
		$view .= '</tr></thead>';
		$view .= '<tbody>';
		if (!empty($records)) {
			foreach($records as $row) {

				$view .= '<tr onclick="'.$controller_page.'./view/'.$this->encrypter->encode($row->$pfield).'">';
				if (!empty($display)) {
					foreach ($display as $val) {
						$view .= '<td>'.$row->$val.'</td>';
					}
				}
				$view .= '</tr>';

			}
		} else {
			$view .= '<tr><td colspan="'.count($display).'" align="center"> <i>No records found!</i></td></tr>';
		}
		$view .= '</tbody></table>';
		return $view;
	}

	public function table_striped_tbody($records, $display=[], $pfield='')
	{
		$view = '';


		$view .= '<tbody>';
		if (!empty($records)) {
			foreach($records as $row) {

				$view .= '<tr onclick="'.$controller_page.'./view/'.$this->encrypter->encode($row->$pfield).'">';
				if (!empty($display)) {
					foreach ($display as $val) {
						$view .= '<td>'.$row->$val.'</td>';
					}
				}
				$view .= '</tr>';

			}
		} else {
			$view .= '<tr><td colspan="'.count($display).'" align="center"> <i>No records found!</i></td></tr>';
		}
		$view .= '</tbody>';
		return $view;
	}

	// public function table_form()
	// {
	// 	$view = '';

	// 	$table_open = '<table class="table-form column-3">'
	// 	$thead_open = '<thead class="'.$thead_class.'">';
	// }

// 	// table-form column-3
// <div class="table-row">
// 							<table class="table-form">



}