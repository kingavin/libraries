<?php
abstract class App_Mongo_Tree_Doc extends App_Mongo_Db_Document
{
	protected $_head = null;
	protected $_leafs;
	
	protected $_trail = null;
	
	abstract protected function _getIndex();
	abstract protected function _getReadLeafCollection();
	
//	public function render()
//	{
//		$str = "<ul>";
//		$index = $this->_getIndex();
//		
//		//Zend_Debug::dump($index);
//		
//		foreach($index as $key => $val) {
//			$str.= $this->_renderItem($val);
//			
//			
//		}
//		return $str.'</ul>';
//	}
//	
//	protected function _renderItem($item)
//	{
//		$tmpStr = '<li>'.$item['label'].'<ul>';
//		if(isset($item['children']) && count($item['children']) > 0) {
//			foreach($item['children'] as $cItem) {
//				$tmpStr.= $this->_renderItem($cItem);
//			}
//		}
//		return $tmpStr.'</ul></li>';
//	}
	
	
//	
//	
//public function toMultiOptions()
//	{
//		$arr = array();
//		foreach($this->_head->getChildren() as $cLink) {
//			$this->_getChildrenAsMultiOptions($cLink, $arr, '');
//		}
//		return $arr;
//	}
//	
//	
//	protected function _getChildrenAsMultiOptions(Class_Link $link, &$arr, $prefix)
//	{
//		$arr[$link->getId()] = $prefix.$link->label;
//		if($link->hasChildren()) {
//			foreach($link->getChildren() as $cLink) {
//				$this->_getChildrenAsMultiOptions($cLink, $arr, $prefix.'--');
//			}
//		}
//		return ;
//	}
	
	
	
	public function getTrail($id)
    {
    	if(is_null($this->_trail)) {
    		$this->_trail = array();
    		$index = $this->_getIndex();
    	
    		$this->_searchChildren($this->_trail, $id, $index, 0);
    		ksort($this->_trail);
    	}
    	
    	return $this->_trail;
    }
	
    public function _searchChildren(&$trail, $needle, $haystack, $lv)
    {
    	$lv++;
    	foreach($haystack as $hay) {
    		if($hay['id'] == $needle) {
    			$trail[$lv] = $hay;
    			$trail[$lv]['children'] = null;
    			return true;
    		}
    		if(isset($hay['children'])) {
    			$result = $this->_searchChildren($trail, $needle, $hay['children'], $lv);
    			if($result) {
    				$trail[$lv] = $hay;
    				$trail[$lv]['children'] = null;
    				return true;
    			}
    		}
    	}
    	return false;
    }
    
    public function getLevelOneTree($leafId)
    {
    	$trail = $this->getTrail($leafId);
    	if(empty($trail)) {
    		$levelOneBranchId = 0;
    	} else {
    		$levelOneBranchId = $trail[1]['id'];
    	}
    	
    	$index = $this->_getIndex();
    	foreach($index as $k => $v) {
    		if($v['id'] == $levelOneBranchId) {
    			return $v;
    		}
    	}
    }
    
	public function toMultioptions($htmlField = 'label')
	{
		$index = $this->_getIndex();
		
		if(is_null($index)) {
			$index = array();
		}
		
		$multioptions = array();
		foreach($index as $indexChild) {
			$this->_getChildrenAsMultiOptions($htmlField, $indexChild, $multioptions, '');
		}
		return $multioptions;
	}
	
	protected function _getChildrenAsMultiOptions($htmlField, $val, &$arr, $prefix)
	{
		$arr[$val['id']] = $prefix.$val[$htmlField];
		if(isset($val['children'])) {
			foreach($val['children'] as $v) {
				$this->_getChildrenAsMultiOptions($htmlField, $v, $arr, $prefix.'--');
			}
		}
		return ;
	}
	
	public function readLeafs()
	{
		if(!is_null($this->_head)) {
			throw new Exception('leafs already generated!');
		}
		
		$co = $this->_getReadLeafCollection();
		$this->_head = $co->create(array('label' => 'ROOT', 'parentId' => null));
		
		$leafDocs = $co->setFields(array('label', 'parentId', 'sort', 'link'))
			->sort('sort', 1)
			->fetchDoc();
			
		$this->_leafs = $leafDocs;
		$this->_buildConnection($this->_head);
		$this->_head->sortChildren();
		return $this;
	}
	
	public function setLeafs($leafDocs)
	{
		if(!is_null($this->_head)) {
			throw new Exception('leafs already generated!');
		}
		
		$co = $this->_getReadLeafCollection();
		$this->_head = $co->create(array('label' => 'ROOT', 'parentId' => null));
		
		$this->_leafs = $leafDocs;
		
		$this->_buildConnection($this->_head);
		$this->_head->sortChildren();
		return $this;
	}
	
	protected function _buildConnection($parent)
	{
		$count = count($this->_leafs);
        if($count > 0) {
            foreach($this->_leafs as $i => $parent) {
            	if($parent->parentId == 0) {
            		$this->_head->appendChild($parent);
            	}
                foreach($this->_leafs as $j => $child) {
            		if($parent->getId() == $child->parentId) {
            			$parent->appendChild($child);
            			continue;
            		}
                }
            }
        }
        return;
	}
	
	public function buildIndex()
	{
		$leafArr = array();
		$childrenPage = $this->_head->buildArr($leafArr);
		
		return $leafArr;
	}
	
	public function renderPages()
	{
        ob_start();
        $this->_head->render();
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
	}
}