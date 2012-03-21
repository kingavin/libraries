<?php
class App_Cart_General
{
	protected $_sessionId;
	protected $_userId;
	
	protected $_cartDoc = null;
	protected $_itemList = null;
	
	public function __construct()
	{
		$this->_sessionId = App_Cart::getSessionId();
		$csu = Class_Session_User::getInstance();
		$this->userId = $csu->getUserId();
		$this->_load();
	}
	
	protected function _load()
	{
		$cartCo = new App_Mongo_Cart_Co();
		$this->_cartDoc = $cartCo->fetchOne(array('sessionId' => $this->_sessionId));
		if(!is_null($this->_cartDoc)) {
			$this->_itemList = $this->_cartDoc->itemList;
		} else {
			$this->_cartDoc = $cartCo->create();
			$this->_cartDoc->sessionId = $this->_sessionId;
			$this->_itemList = array();
		}
	}
	
	public function isEmpty()
	{
		if(count($this->_itemList) == 0) {
			return true;
		}
		return false;
	}
	
	public function getItemList()
	{
		return $this->_itemList;
	}
	
	public function addItem($id, $qty, $price, array $info = array())
	{
		if(is_null($id) || is_null($qty) || is_null($price)) {
            throw new Exception('product qty and price needed for inserting to cart!!');
        }
		
		if(!array_key_exists($id, $this->_itemList)) {
            $this->_itemList[$id] = array();
            $this->_itemList[$id]['id'] = $id;
            $this->_itemList[$id]['qty'] = 0;
            $this->_itemList[$id]['price'] = $price;
            $this->_itemList[$id]['info'] = $info;
        }
        
        $this->_itemList[$id]['qty']+= $qty;
        
        return $this;
	}
	
	public function updateItem($id, $qty)
	{
		if(!array_key_exists($id, $this->_itemList)) {
            throw new Exception('product not in cart!!');
        }
        $this->_itemList[$id]['qty'] = $qty;
        
        return $this;
	}

	public function removeItem($id)
	{
		if(array_key_exists($id, $this->_itemList)) {
			unset($this->_itemList[$id]);
		}
		return $this;
	}
	
	public function getSubtotal()
	{
		$subtotal = 0;
		foreach($this->_itemList as $item) {
			$subtotal += $item['price'] * $item['qty'];
		}
		return $subtotal;
	}
	
	public function getUserId()
	{
		return $this->_userId;
	}
	
	public function setAddress($addressId, $fullAddress)
	{
		$this->_cartDoc->addressId = $addressId;
		$this->_cartDoc->fullAddress = $fullAddress;
		return $this;
	}
	
	public function getFullAddress()
	{
		return $this->_cartDoc->fullAddress;
	}
	
	public function getAddressId()
	{
		return $this->_cartDoc->addressId;
	}
	
	public function clear()
	{
		$this->_cartDoc = null;
		$this->_itemList = null;
	}
	
	public function save()
	{
		$this->_cartDoc->itemList = $this->getItemList();
		$this->_cartDoc->save();
	}
	
	public function remove()
	{
		$this->_cartDoc->delete();
		$this->clear();
	}
	
	public function isValid()
	{
		if(count($this->_itemList) > 0 &&
			!empty($this->_cartDoc->fullAddress) &&
			!empty($this->_cartDoc->addressId) &&
			!empty($this->_userId)
		) {
			return true;
		} else {
			return false;
		}
	}
}