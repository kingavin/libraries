<?php
class App_Mongo_Tree_Leaf_Doc extends App_Mongo_Db_Document
{	
	protected $_parent = array();
	protected $_children = array();
	protected $_hasChildren = false;
	
	public function appendChild(App_Mongo_Tree_Leaf_Doc $pageDoc)
	{
		$thisId = is_null($this->getId()) ? 0 : $this->getId();
		if($pageDoc->parentId == $thisId) {
			$pageDoc->_parent = $this;
			$this->_children[] = $pageDoc;
			$this->_hasChildren = true;
		}
		return $this;
	}
	
	public function sortChildren()
    {
        $this->_bubbleSort($this->_children);
        return $this;
    }
    
    protected function _bubbleSort(&$array)
    {
		$count = count($array);
		if ($count <= 0)
			return false;
		for($i = 0; $i < $count; $i++) {
			for($j = $count - 1; $j > $i; $j--)	{
				$jKey = intval($array[$j]->sort);
				$jmKey = intval($array[$j-1]->sort);
				if($jKey < $jmKey) {
					$tmp = $array[$j];
					$array[$j] = $array[$j-1];
					$array[$j-1] = $tmp;
				}
			}
		}
    }
    
    public function buildArr(&$arr)
    {
    	foreach($this->_children as $c) {
    		$tempArr = $c->getData();
    		unset($tempArr['sort']);
    		unset($tempArr['parentId']);
    		$tempArr['id'] = $c->getId();
    		if($c->_hasChildren) {
    			$tempChildrenArr = array();
    			$c->buildArr($tempChildrenArr);
    			$tempArr['children'] = $tempChildrenArr;
    		}
    		$arr[] = $tempArr;
    	}
    	return $arr;
    }
    
    public function render()
    {
    	echo "<ul>";
    	echo "<li>".$this->label."</li>";
    	if($this->_hasChildren) {
    		foreach($this->_children as $c) {
    			$c->render();
    		}
    	}
    	echo "</ul>";
    }
}