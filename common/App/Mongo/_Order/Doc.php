<?php
class App_Mongo_Order_Doc extends App_Mongo_Db_Document
{
	public function setFromCart($cart)
	{
		$this->itemList = $cart->getItemList();
		$this->fullAddress = $cart->getFullAddress();
		$this->userId = $cart->getUserId();
		return $this;
	}
}