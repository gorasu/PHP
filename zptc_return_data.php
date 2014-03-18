<?
/** z_return_data
*
* Модуль предназначен для избежания множественного определения аргументов функции.
*
*/
    class z_return_data{
   	 private $_a_default; // данные по умолчанию
   	 private $_a_required; //  обязательные данные
   	 private $_exit; // прекращать ли выполнения скрипта если нет обязательных данных
   	 private $_echo_error; // Строка с ошибкой, если пустая то ошибка не выводится
   	 
            	/**
            	*
            	* @param (str)$echo_error - Строка с ошибкой, если пустая то ошибка не выводится
            	* @param (bool)$exit(true) - прекращать ли выполнения скрипта если нет обязательных данных
            	*
            	*
            	**/
            	function __construct($echo_error = 'No key <b>%key%</b> in $a_data',$exit = true){
                	$this->_echo_error = $echo_error;
                	$this->_exit = $exit;
            	}

            	/**Подготовка данных*/
   	 function prepare_data($a_data,$a_settings = array()){
   		 $a_data = (array)$a_data;
                    	$this->_a_required = (array)$this->_a_required;
                    	$this->_a_default = (array)$this->_a_default;
   		 $a_data =  array_merge($this->_a_default,$a_data);
   		 /**Осуществлять ли проверку на пустое значение*/
   		 $is_empty_check = isset($a_settings['is_empty_check']) && $a_settings['is_empty_check'] == true ? true : false;

   		 
   		 $is_value_empty = false;
   		 foreach($this->_a_required as $key){
   		 
   		 /** Проверка на пустое значение */    
   		 if($is_empty_check){
   			 $is_value_empty = false;
   			 $is_value_empty = empty($a_data[$key]);
   			 
   		 }    
   		 
   		 if(!array_key_exists($key,$a_data) || $is_value_empty){
   			 
   			   if($this->_echo_error) {
   			 echo '<BR>'.str_ireplace('%key%',$key,$this->_echo_error . '<BR>');
   			 
   			 foreach(debug_backtrace() as $a_value){
   			 if(@$a_value['class'] != __CLASS__) {
   			 continue;
   			 }
   				 echo 'FILE - '.$a_value['file'].'<BR>';   			 
   				 echo 'LINE - '.$a_value['line'].'<BR>';   			 
   				 echo 'FUNCTION - '.$a_value['function'].'<BR>';
   				 echo 'CLASS - '.$a_value['class'].'<BR>';    
   				 echo('<hr>');
   			 }
   					 
   					 
   					 if($this->_exit) {
   						 exit;
   					 }
   				 }
   			 }
   		 }
   		 return $a_data;
   	 }
   	 
           	 
            	function return_data($a_data,$a_default = array(),$a_required = array(),$a_settings = array()){

   		 
   	 	$this->_a_default = $a_default;
                	$this->_a_required = $a_required;
                	return $this->prepare_data($a_data,$a_settings);
               	 
            	}
            	function rd($a_data,$a_default = array(),$a_required = array(),$a_settings = array()){
           	 
   	 	return $this->return_data($a_data,$a_default,$a_required);
            	}

    }
?>
