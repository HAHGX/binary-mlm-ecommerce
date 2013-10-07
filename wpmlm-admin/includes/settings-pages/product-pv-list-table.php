<?php
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
// this code start for the table create for the product pv

class Product_Pv_List_Table extends WP_List_Table{
	
	function __construct(){
        global $status, $page;
                
	   //Set parent defaults
        parent::__construct( array(
            'singular'  => 'id',     //singular name of the listed records
            'plural'    => 'id',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
	
	function column_default($item, $column_name){
        switch($column_name){
			case 'product_name':
			case 'product_price':
			case 'pvset':
			 return $item[$column_name];
            default:
                return print_r($item,true);
        }
    }
	
	function get_columns(){
        $columns = array(
			'product_name'    	=> __('Product Name ','binary-mlm-pro'),
			'product_price'     	=> __('Product Price','binary-mlm-pro'),
			'pvset'     => __('Set Pv','binary-mlm-pro'),	
        );
        return $columns;
    }
	
	function get_sortable_columns() {
        $sortable_columns = array(
			
        );
        return $sortable_columns;
    }
	function prepare_items() {
        global $wpdb; 
		global $table_prefix;
		global $date_format;
                 $per_page = 30;
		
		$columns = $this->get_columns();
                $hidden = array();	
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable); 
		$listArr = array(); 
		$settings=get_option('wpmlm_general_settings');
               
                //print_r($settings);
                if(isset($settings['ecom_option'])&&$settings['ecom_option']=='wp-ecommerce')
                {
		$sql = "SELECT p.id as pid,p.post_name as name,pm.meta_value as price FROM {$table_prefix}posts as p INNER JOIN {$table_prefix}postmeta as pm ON p.id=pm.post_id
		WHERE p.post_type LIKE 'wpsc-product' AND p.post_status='publish' AND pm.meta_key LIKE '_wpsc_price'"; 
                }else if(isset($settings['ecom_option'])&&$settings['ecom_option']=='jigoshop')
                {
                 $sql = "SELECT p.id as pid,p.post_name as name,pm.meta_value as price FROM {$table_prefix}posts as p INNER JOIN {$table_prefix}postmeta as pm ON p.id=pm.post_id
		WHERE p.post_type ='product' AND p.post_status='publish' AND pm.meta_key LIKE 'regular_price'";   
                }
                else if(isset($settings['ecom_option'])&&$settings['ecom_option']=='woocommerce')
                {
                    $sql = "SELECT p.id as pid,p.post_name as name,pm.meta_value as price FROM {$table_prefix}posts as p INNER JOIN {$table_prefix}postmeta as pm  ON p.id=pm.post_id WHERE p.post_type ='product' AND p.post_status='publish' AND pm.meta_key LIKE '_regular_price' ";    
                }
		$rs = mysql_query($sql);
		$listArr = array();
                $path=WPMLM_FILE_PATH . '/wpmlm-admin/';
		if(mysql_num_rows($rs)>0){
                    $i=0;
		 	while($row = mysql_fetch_array($rs))
			{       
                                $pv_value = get_post_meta( $row['pid'], 'pv_value', TRUE );
                                $form='<form action="" id="pv_form_'.$row['pid'].'" method="POST">
                                    <input type="hidden" value="'.$row['pid'].'" name="pid">
                                    <input type="text" name="pv_value" id="pv_value_'.$row['pid'].'" value="'.$pv_value.'">
                                     <input type="submit" value="Save" id="update_'.$row['pid'].'"></form>';
				$listArr[$i]['product_name'] = $row['name'];
				$listArr[$i]['product_price'] = $row['price']; 
				$listArr[$i]['pvset'] = $form;
				$i++;
			}
		}
		
		
		$data = $listArr;
		$current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        $this->items = $data;
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($total_items/$per_page)  
        ) );
	}

}
?>