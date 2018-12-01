<?php


class validate
{

var $error;
var $i;

function validate()
{
  $this->error = array();
  $this->i = 0;
   register_shutdown_function(array(&$this, "UnMyObject"));
}

function UnMyObject() {
   unset($this->error); // not needed but used to demonstrate the register_shutdown_function
   unset($this->i);
 }

function clear_error()
{
  $this->error = array();
}


function is_nempty($name,$param)
{
if (!empty($param)) {
	return true; 
   } 
else{
	$this->error[$this->i] = $name;
	$this->i += 1;
	return false;
	}
 }
 
function is_integer($name,$param)
{
if (preg_match ("/^([0-9]+)$/", $param)) {
	return true; 
   } 
else{
	$this->error[$this->i] = $name;
	$this->i += 1;
	return false;
	}
 }


function is_height($name,$param)
{
if (preg_match ("/^([0-9]+\.*[0-9]*)$/", $param)) {
	return true; 
   } 
else{
	$this->error[$this->i] = $name;
	$this->i += 1;
	return false;
	}
 }
 

function is_alpha($name,$param)
{
if (preg_match("/^([a-zA-Z]+)$/", $param)){
	return true;
	}
else{
	$this->error[$this->i] = $name;
	$this->i += 1;
	return false;
	}
}


function is_alphaname($name,$param)
{
//$a = str_replace(" ","",$param);
if (preg_match("/^([a-zA-Z\-\ \']+)$/", $param)){
	return true;
	}
else{
	$this->error[$this->i] = $name;
	$this->i += 1;
	return false;
	}
}


function is_alphanum($name,$param)
{
if (preg_match("/^([a-zA-Z0-9]+)$/", $param)){
	return true;
	}
else{
	$this->error[$this->i] = $name;
	$this->i += 1;
	return false;
	}
}


function is_email($name,$param)
{
if (preg_match("/^([a-zA-Z0-9_\-\.]+@[a-zA-Z0-9_\-]+\.[a-zA-Z0-9]{2,3}\.*[a-zA-Z0-9]*)$/", $param)){
	return true;
	}
else{
	$this->error[$this->i] = $name;
	$this->i += 1;
	return false;
	}
}


function is_phone($name,$param)
{
if (preg_match("/^([0-9\-\ ]+)$/", $param)){
	return true;
	}
else{
	$this->error[$this->i] = $name;
	$this->i += 1;
	return false;
	}
}

function is_l_phone($name,$param)
{
if (preg_match("/^([0-9]{2,2}-[0-9]{7,7})$/", $param)){
	return true;
	}
else{
	$this->error[$this->i] = $name;
	$this->i += 1;
	return false;
	}
}


function is_m_phone($name,$param)
{
if (preg_match("/^(080[2-7]{1,1}[0-9]{7,7})$/", $param)){
	return true;
	}
else{
	$this->error[$this->i] = $name;
	$this->i += 1;
	return false;
	}
}


function is_username($name,$param)
{
if (preg_match("/^([a-zA-Z0-9_\-]{5,20})$/", $param)){
	return true;
	}
else{
	$this->error[$this->i] = $name;
	$this->i += 1;
	return false;
	}
}


function is_password($name,$param)
{
if (preg_match("/^([a-zA-Z0-9_\-]{5,20})$/", $param)){
	return true;
	}
else{
	$this->error[$this->i] = $name;
	$this->i += 1;
	return false;
	}
}



function is_in_array($name,$param1,$param2)
{
if (in_array($param1,$param2)){
	return true;
	}
else{
	$this->error[$this->i] = $name;
	$this->i += 1;
	return false;
	}
}

function is_btw_year($name,$param1,$param2,$param3)
{
if (($param2<=$param1)&&($param1<=$param3)){
	return true;
	}
else{
	$this->error[$this->i] = $name;
	$this->i += 1;
	return false;
	}
}

function show_error()
{
$t='';
foreach($this->error as $key => $value)
{ if($key == 0){$t= $value;}
 else{$t.= ', '.$value;}
 }
 
 	if($t!=''){$t = 'Please fill the following field(s): <span class="err">'.$t.'</span> correctly';}
	
 return $t;
 }

function get_error()
{
return $this->error;
}

}
?>