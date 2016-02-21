<?php


class Test_identity_map{
	
	public static function test1(){
		echo "<pre>";
		
		$category1 = Category::model()->findByPk(1);
		$category2 = Category::model()->findByPk(1);
		$ok = $category1 === $category2;
		echo '($category1 === $category2)... '.($ok?'PASSED':'FAILED').'<br>';
		
		$category_child_arr = $category1->categories();
		$categoy_child = $category_child_arr[0];
		$ok = $categoy_child->parent === $category1;
		echo '($categoy_child->parent === $category1)... '.($ok?'PASSED':'FAILED').'<br>';
		
		$purchase = new Purchase();
		$purchase->delivery_cost = -1;
		$purchase->save();
		$purchase_id = $purchase->id;
		
		$purchase_loaded_by_pk = Purchase::model()->findByPk((int)$purchase_id);
		$ok = $purchase_loaded_by_pk === $purchase;
		echo 'after create, ($purchase_loaded_by_pk === $purchase)... '.($ok?'PASSED':'FAILED').'<br>';				
		
		
		$purchase->delete();
		$ok = Identity_map::get_record(get_class($purchase), $purchase->id) === null;
		echo 'after delete, (Identity_map::get_record(get_class($purchase), $purchase->id) === null)... '.($ok?'PASSED':'FAILED').'<br>';				
		
		$c = Customer::model()->findByPk(21);
		$c->birth_date = '1-7-80';
		$c->save();
		
		
		echo "</pre>";		
	}
}