<?php
/*    $a_data['select_where'] = '';
    $a_data['select_from'] = ' users';
    $a_data['select_fields'] = '*';
    $a_data['search_fields'] = array('username');
    $a_data['limit'] = 10;
        
 
    
   $class =  new z_search_in_db('павел',$a_data);
  echo  $class->prepare_query();
  */


class z_search_in_db {
    private $_settings = array();
    private $_is_init = false;
   
    function __construct(){}
    
    function init($search_string,$a_data){
	$a_default = array('limit'=>'5','search_string'=>$search_string,'translit'=>false,'max_word'=>5,'sort_prefix'=>"rev_",'return_field_title'=>'_return_data');
        $a_required = array('select_fields','search_fields','select_where','select_from');
        $this->_settings = $this->return_data($a_data,$a_default,$a_required);
	$this->_is_init = true;
    }
    
    
    /** prepare_query
    *
    * Функция которая подготавливает запрос к базе данных
    */
    function prepare_query(){
	if(!$this->_is_init){
	   exit('ERROR: call '.__CLASS__.'->init()'); 
	}
	
	
       $a_search_fields = $this->_settings['search_fields'];
       $s_search_fields = '';
       /**Поля поиска переводим в массив если они строка*/
       if(!is_array($a_search_fields)){
        $a_search_fields = array($a_search_fields); 
       }
       /**Разбиваем поля поиска в строку*/
       $s_search_fields = implode(',',$a_search_fields);
       /** Убераем кавычки в строке поиска*/
       $search_string_full = $this->_settings['search_string'];
       $search_string_full = $this->escape( $search_string_full);
       $search_string_full = trim($search_string_full);
       
       $search_string_short = $search_string_full;
       /**Транслитим если это необходимо**/
       if($this->_settings['translit']){
        $search_string_short .= ' '.$this->rus2translit($search_string_full);
       }
       /**Удаляем знаки припинания и заменяем их пробелом, для того что бы создать поиск по отдельным словам в строке*/
       $search_string_short = str_replace(array('-',',','_','.'),' ',$search_string_short);
       /**Превращаем короткую строку поиска в массив*/
       $search_array_short = explode(" ",$search_string_short);
       
       /**Максимальное количество слов в поисковом запросе*/
       $count_array_short = sizeof($search_array_short);
       if($this->_settings['max_word'] &&  $count_array_short > $this->_settings['max_word']) {
        $count_array_short = $this->_settings['max_word'];
       }
            /*Создаем строку для поиска отдельных слов в MySql*/
            $s_short_finder = '';
                    for($i = 0; $i < $count_array_short; $i++) {
                            
                            if(mb_strlen($search_array_short[$i]) < 3) {
                            continue;
                            }
                            $left = "";
                            $right = "";
                            if($i<$count_array_short/2) {
                            $left = ">";
                            $right = "<";
                            }
                            $s_short_finder .= "(".$left."+".$this->escape($search_array_short[$i])."*".$right.")";
                    }
                    
               		/* Создаем поисковые выражения используя массив с колонками. Так же делаем выражения для сортировки, под каждое выражение поиска путем присоединения к префиксу порядковый номер колонки */
		$against ='';
		$order ='';
                $_select_case ="";
                $count_search_fields = sizeof($a_search_fields);
                if($count_search_fields > 1){
                    $_select_case ="SELECT *, CASE ";
                }
		for($i=0; $i < $count_search_fields; $i++) {
			if($i > 0) {
				$against .=" , ";
				$order .=" , "; 
			}
			$against .= "
                        
                        MATCH (".$a_search_fields[$i].") AGAINST ('>\"".$search_string_full."\"<(".$s_short_finder.")' IN BOOLEAN MODE) AS ".$this->_settings['sort_prefix'].$i."
                        ";
		$_sort_name = $this->_settings['sort_prefix'].$i;
                $order .= $_sort_name." DESC
                        ";
		
                
                if($count_search_fields > 1){
                $_select_case .= " when ".$_sort_name." >= ";
                    for($z = 0; $z <$count_search_fields; $z++ ){
                     if($z == $i ){
                        continue;
                     }
                     $_select_case .=  $this->_settings['sort_prefix'].$z;
                   /**НЕ ставим знак больше перед посленей итерацией*/
                    if($z != $count_search_fields-1){
                        /**На ставим знак больше перед предпоследней итереацией если основная итерация является последней */
                        if($count_search_fields - 2 == $z && $i == $count_search_fields-1){
                           $_select_case .= ''; 
                        }
                        else {
                        $_select_case .= ' > ';
                        }
                    }}
                    $_select_case .= ' then '.$a_search_fields[$i].' ';
                }
                
		}
                if($_select_case){
                   $_select_case .= ' end as '. $this->_settings['return_field_title'];
                }
                	//$query = "SELECT * FROM companies WHERE ";
		/*Запрос в котром производится поиск компании 
		возвращается имя компании*/
	$limit = '';
        $where = '';
        if($this->_settings['select_where']){
           $where = 'AND '.ltrim(trim($this->_settings['select_where']),'AND');
        }
        if($limit = $this->_settings['limit']){
            $limit =" LIMIT ". $limit;
        }
	$query = "SELECT ".$this->_settings['select_fields'].", ".$against."
        
        FROM ".$this->_settings['select_from']."
        
        WHERE MATCH (".$s_search_fields.") AGAINST ('>\"".$search_string_full."\"<(".$s_short_finder.")' IN BOOLEAN MODE)
        
        ".$where."
        
        ORDER BY ".$order." ".$limit;
                    
       if($_select_case){
       $query =  $_select_case.' FROM ('.$query.') AS _table';
       }
       $this->_is_init = false;
       return $query;
       
        
       }
    
    
    function prepare_search_str($str,$replace = true){
                /*Заменять тире и точки или нет*/
		if($replace) {
                    $str = str_replace(array('-',',','_','.'),' ',$str);
		}
		return $str;
    }
    
    function escape($str){
        
       return  @mysql_escape_string($str);
    }
    
    
    /*Функция в которой происходит перевод 
символов в транслит*/
function rus2translit($string)
{

    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => "'",  'ы' => 'y',   'ъ' => "'",
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
 
        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => "'",  'Ы' => 'Y',   'Ъ' => "'",
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );

    return strtr($string, $converter);
}
 
 
    function return_data($a_data,$a_default = array(),$a_required = array(),$echo_error = 'No key <b>%key%</b> in $a_data',$exit = true){
			$a_data = array_change_key_case ($a_data,CASE_LOWER);
			$a_data =  array_merge($a_default,$a_data);

			foreach($a_required as $key){
				if($echo_error) {
					if(!isset($a_data[$key])){
				
				
				echo '<BR>'.str_ireplace('%key%',$key,$echo_error . '<BR>');
				
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
						 
						 
						if($exit) {
							exit;
						}
					}
				}
			}
			return $a_data;
} 
	
	
}





?>
