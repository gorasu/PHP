<?
/** inject_to_array_fast
*
* Добавляет в массив значения после заданного ключа, массив возвращается по ссылке.
* 
* @param $main_array - Главный массив в который будет добавлен новый элемент
* @param $key_sparator - нименование ключа разделителя после которого будет добавлен элемент !Требует передачу строго типизированного значения
* @param $a_add - массив элементов которые будут добавлены к основному массиву
*
* @todo  Сделать возможность добавления нового эелмента до ключа массив
**/
function inject_to_array_fast(&$main_array,$key_sparator,$a_add){
    if(!key_exists($key_sparator,$main_array)){
        return false;
    }
    $a_return  = array();
    foreach($main_array as $a_key => $a_val){
        $a_return[$a_key]= $a_val;
        if($a_key == $key_sparator){
            foreach($a_add as $add_key => $add_val){
                
                $a_return[$add_key] = $add_val;
                
            }
        }
    }
    $main_array = $a_return;

}
/** inject_to_array_slow
*
* Добавляет в массив значения после заданного ключа, массив возвращается по ссылке.
* В отличие от inject_to_array_fast имеет возможность добавления значения как после определенного ключа так и до.
* Работает  ~1.5 раза медленнее чем inject_to_array_fast
*
* @param $main_array - Главный массив в который будет добавлен новый элемент
* @param $key_sparator - нименование ключа разделителя до/после которого будет добавлен элемент !Требует передачу строго типизированного значения
* @param $a_add - массив элементов которые будут добавлены к основному массиву
**/
function inject_to_array_slow(&$main_array,$key_sparator,$a_add,$is_after = true){
    if(!key_exists($key_sparator,$main_array)){
        return false;
    }
    
    $main_keys = array_keys($main_array);
    $main_vals = array_values($main_array);
    $add_num = array_search($key_sparator, $main_keys,true) ;
    if($is_after){
       $add_num++; 
    }
    $main_keys2 = array_splice($main_keys, $add_num);
    $main_vals2 = array_splice($main_vals, $add_num);
    
    
    $add_keys = array_keys($a_add);
    $add_vals = array_values($a_add);
    $main_keys = array_merge($main_keys,$add_keys);
    $main_vals = array_merge($main_vals,$add_vals);
    $main_array =   array_merge(array_combine($main_keys, $main_vals), array_combine($main_keys2, $main_vals2));
    return true;
}



$arr = array();
for($i = 0; $i < 1000; $i++){
    $arr['mykey_'.$i] = 'my_value_'.$i;
}

$a_fast = $arr;
$a_slow_after = $arr;
$a_slow_before = $arr;

inject_to_array_fast($a_fast,'mykey_823',array('MyKey'=>'MyValue'));
inject_to_array_slow($a_slow_after,'mykey_823',array('MyKey'=>'MyValue'));
inject_to_array_slow($a_slow_before,'mykey_823',array('MyKey'=>'MyValue'),false);

print_r($a_fast); // ... [mykey_823]=>'my_value_823', [MyKey]=>'MyValue', [mykey_824]=>'my_value_824' ...
print_r($a_slow_after); // ... [mykey_823]=>'my_value_823', [MyKey]=>'MyValue', [mykey_824]=>'my_value_824' ...
print_r($a_slow_before); // ... [mykey_822]=>'my_value_822', [MyKey]=>'MyValue', [mykey_824]=>'my_value_824' ...




?>
