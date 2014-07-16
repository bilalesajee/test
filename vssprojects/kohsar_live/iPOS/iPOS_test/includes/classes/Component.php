<?php
/*
		params: type		=	TYPE OF THE COMPONENT
								[d 	=	dropdown or combo]
				name		=	NAME OF THE COMPONENT
								[If braces '[]' is with name then make it multiple]
				event		=	EVENT AND JAVASCRIPT FUNCTION TO BE CALLED
								[onChange = "javascript:callMe('1')"]
				vals_labels	=	TWO DIMENSIONAL ARRAY OF VALUES AND LABELS FOR OPTIONS IN COMPONENT
				selected	=	ARRAY OF VALUES THAT NEED TO BE SELECTED
				size		=	LENGTH OF THE COMPONENT
				css		=	CSS Class
		
*/
class Component
{
	private  $type			=	"";
	private  $name			=	"";
	private  $event			=	"";
	private  $vals_labels 	=	array();
	private  $selected		=	array();
	private  $size			=	"";
	private  $css 			=	"";
	private  $value_field	=	"";
	private  $label_field	=	"";
	/*********************************makeCombo()*******************************/
	public function makeComponent($type,$name,$vals_labels,$value,$label,$size,$selected=array(),$event="",$css="")
	{
		$this->type			=	$type;
		$this->name			=	$name;
		$this->event		=	$event;
		$this->vals_labels	=	$vals_labels;
		$this->value_field	=	$value;
		$this->label_field	=	$label;
		$this->selected		=	$selected;
		$this->css			=	"class=".$css;
		$this->size			=	$size;
		
		switch(strtolower($type))
		{
			case 'd':
			{
				return ($this->makeCombo());
			}
			
		}//switch
	}
	private function makeCombo()
	{
		$i	=	0;
		$multiple	=	"";
		$pos = strpos($this->name, '[');
		if ($pos != false)
		{
			//make multiple combo
			
			$multiple	=	" multiple=multiple ";
			$emptyoption	=	"<option value=''> None </option>";
		}
		else
		{
			$optionname		=	explode('[',ucfirst($this->name));
			$emptyoption	=	"<option value=''>Select $optionname[0]</option>";
		}
		
		$component	=	"<select name=\"$this->name\" id=\"$this->name\" $multiple size=\"$this->size\" $this->event $this->css>";
		$component	.=	$emptyoption;
		foreach($this->vals_labels as $val_label)
		{
			$i	+=1;	
				if (is_array($val_label))
				{
					$selected	=	"";
					$index		=	$this->value_field;
					$label		=	$this->label_field;
					if (@in_array($val_label[$index],$this->selected))
					{
						$selected	=	"  selected=\"selected\"  ";
					}
					$component	.=	"<option value=\"$val_label[$index]\" $selected>
										$val_label[$label]
									</option>";
				}
		}//foreach
       $component	.="</select>";
	   if($i!=0)
	   {
	   		$return	=	$component;
		}
		else
		{
			$return	=	"<span class='error'>Sorry! No Record found.</span>";
		}
		return $return;
	}//makeCombo
}//end of class
?>