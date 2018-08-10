<?php
class Obj {

	public function __construct($params=array())
	{

	}

	public function input_field($id='', $type='text', $class='form-control', $required=false, $readonly=false)
	{
		$view  = '<input class="'.$class.'" ';
		$view .= 'type="'.$type.'" ';
		$view .= 'id="'.$id.'" ';
		$view .= 'name="'.$id.'" ';
		$view .= 'label="'.ucfirst($id).'" ';
		// $view .= 'style="'.$this->CI->style.'" ';
		// $view .= 'value="'.$this->CI->value.'" ';
		if ($required) {
			$view .= ' required ';
		}

		if ($readonly) {
			$view .= ' readonly ';
		}

		$view .= '/>';

		return $view;
	}

	public function datepicker($id='', $value='', $required=false, $width='')
	{
		$view  = '<input class="form-control datepicker" ';
		$view .= 'type="text" ';
		$view .= 'id="'.$id.'" ';
		$view .= 'name="'.$id.'" ';
		$view .= 'label="'.ucfirst($id).'" ';
		$view .= 'data-target="#'.$id.'" ';
		$view .= 'maxlength="20" data-toggle="datetimepicker" ';
		$view .= 'style="width: '.$width.'px;" ';


		if ($value != '') {
			$view .= ' value="'.$value.'" ';
		}

		if ($required) {
			$view .= ' required ';
		}

		$view .= '/>';

		return $view;
	}
	
}