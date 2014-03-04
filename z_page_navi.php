<?
/**
 *          $a_default = array(
                               'current_page'=>1, // Текущая страница
                               'count_preview_left'=>10, // Кол-во страниц слева от разделителя
                               'count_preview_right'=>2, // Кол-во страниц справа от разделителя
                               'count_preview_center_left'=>5, // Кол-во страниц слева от активной страницы
                               'count_preview_center_right'=>5,// Кол-во страниц справа от активной страницы
                               'anchor'=>$this->_anchor, // Якорь вместо которого подставляется номер страницы
                               'clean_url'=>false, // Чистая ссылка для  генрации первой страницы
                               'hook_url_prepare'=>false, // Хук который получает Url для его последующей 
                               
                               );
            $a_required = array(
                'current_page', 
                'template_links_page_navi', // Url с якороем который будет в последующем заменен на число
                'count_posts_on_page', // Кол-во контакнета на странице
                'all_posts_count' // Общее кол-во контента
            );
			
			
			
			$a_data =array(
			'template_links_page_navi'=>'http://'.$_SERVER['SERVER_NAME'].'page/'.$navi->anchor(),	       
			'count_posts_on_page'=>10,
			'all_posts_count'=>10000,
			'current_page'=>!empty($_GET['page']) ? $_GET['page'] : 105,
                        'clean_url'=>'http://'.$_SERVER['SERVER_NAME'],
			);
			$a_pages = $navi->navi($a_data);
 *
 *
 */


    class page_navi {
            private $_a_params = array();
            private $_anchor = '#p_num#';

	function navi($a_data){
            
            $a_default = array(
                               'current_page'=>1, // Текущая страница
                               'count_preview_left'=>10, // Кол-во страниц слева от разделителя
                               'count_preview_right'=>2, // Кол-во страниц справа от разделителя
                               'count_preview_center_left'=>5, // Кол-во страниц слева от активной страницы
                               'count_preview_center_right'=>5,// Кол-во страниц справа от активной страницы
                               'anchor'=>$this->_anchor, // Якорь вместо которого подставляется номер страницы
                               'clean_url'=>false, // Чистая ссылка для  генрации первой страницы
                               'hook_url_prepare'=>false, // Чистая ссылка для  генрации первой страницы
                               
                               );
            $a_required = array('current_page','template_links_page_navi','count_posts_on_page','all_posts_count');
            
            $a_data = array_merge($a_default,$a_data);
		/*Проверка на обязательные параметры*/
		foreach($a_required as $value){
			if(!isset($a_data[$value])) {
				echo ('<span style="border:1px solid red; background:#FFC6C6;color:#6D0000;padding:5px;">Error: не указан параметр '.$value.'</span>');
			return ;
			}
	    }
            $this->_anchor = $a_data['anchor'];
            $a_data['current_page'] = $a_data['current_page']*1;
            if($a_data['current_page'] <= 0) {
		$a_data['current_page'] = 1;
            }
            $last_page_num =  ceil($a_data['all_posts_count']/$a_data['count_posts_on_page']);
            $a_data['count_pages'] = $last_page_num;
            
            $this->set_params($a_data);
            
            $a_return['left_page'] = $this->left_pages();
            $a_return['right_page'] = $this->right_pages();
            $a_return['center_page'] = $this->center_pages();
    
            return $a_return;

            
        }
	

	function anchor_replace($p_num){
            $tamplate = $this->get_param('template_links_page_navi');
            if($this->get_param('clean_url') && ( $p_num == 0 || $p_num == 1) ){
                return $this->get_param('clean_url');
            }
	    $url =  str_replace($this->anchor(),$p_num,$tamplate);
            if($hook_url_prepare = $this->get_param('hook_url_prepare')){
                $url = call_user_func_array($hook_url_prepare,array($url));
            }
            return $url;
	}
        function anchor(){
            return $this->_anchor;
        }
        
        function next_page(){
            if($this->get_param('count_pages') > $this->get_param('current_page')){
                return $this->anchor_replace($this->get_param('current_page')+1);
            }
            return false;
        }
        function prew_page(){
            if($prew =$this->get_param('current_page')-1){
                return $this->anchor_replace($prew);
            }
            return false;
        }
        
        function left_pages(){
            $a_return = array();
            $left_iteration = $this->get_param('current_page') - $this->get_param('count_preview_center_left')-1;
            if($left_iteration <= 0){
                $left_iteration = 0;
            }
            else if($this->get_param('count_preview_left') < $left_iteration){
                $left_iteration = $this->get_param('count_preview_left');
            }
            for($i = 1; $i <=$left_iteration; $i++){
                $a_return[$i] = $this->anchor_replace($i);	
            }
            return $a_return;
        }
        function right_pages(){
            $center_end = $this->get_param('current_page') + $this->get_param('count_preview_center_right');
            $right_iteration = $this->get_param('count_pages') - $this->get_param('count_preview_right');
            if($center_end > $right_iteration){
                $right_iteration = $center_end;
            }

             if($right_iteration > $this->get_param('count_pages')){
                    $right_iteration = $this->get_param('count_pages');
             }
            for($i = $right_iteration+1; $i <= $this->get_param('count_pages'); $i++){
                $a_return[$i] = $this->anchor_replace($i);	
            }
            return $a_return;
        }
        
        function center_pages(){
          $a_return  = array();
         
          
          $left_iteration =  $this->get_param('current_page') - $this->get_param('count_preview_center_left');
          if($left_iteration <= 0){
            $left_iteration = 1;
          }
          for($i = $left_iteration;  $i < $this->get_param('current_page'); $i++){
            $a_return[$i] = $this->anchor_replace($i);	
          }
            $a_return[$this->get_param('current_page')] = $this->anchor_replace($this->get_param('current_page'));	
            $right_iteration = $this->get_param('current_page') + $this->get_param('count_preview_center_right');
            if($right_iteration > $this->get_param('count_pages')){
                $right_iteration = $this->get_param('count_pages');
            }
            for($i = $this->get_param('current_page'); $i <= $right_iteration; $i++){
                $a_return[$i] = $this->anchor_replace($i);
                
            }
            return $a_return;

        }
        
        function is_current_page($num_or_str){
            if(is_numeric($num_or_str)){
                return $num_or_str == $this->get_param('current_page');
            }
            return $num_or_str == $this->anchor_replace($this->get_param('current_page'));
            
        }
        
        
        function set_params($a_data){
            $this->_a_params = $a_data;
        }
        function get_param($name){
            return $this->_a_params[$name];
        }        
        

		
}
?>
