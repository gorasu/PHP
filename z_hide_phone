function z_hide_phone($phone,$replace_symbol ='X',$numeric_count = 4){

$phone = preg_replace('/[^0-9]*/i','',$phone);
if(strlen($phone) <= $numeric_count){
	$numeric_count = ceil(strlen($phone)/2);
}
preg_match('/(.*)([0-9]{'.$numeric_count.'})$/i',$phone,$out);
$return = '';
$count_replace = strlen($out[1]);
	for($i = 0; $i < $count_replace; $i++ ) {
		$return .=$replace_symbol;
	}
return 	$return.$out[2];
}
