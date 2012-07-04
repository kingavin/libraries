<?php
class Class_Model_Address_Province_Collection extends Class_Model_Collection_Abstract
{
    public function __construct()
    {
        $this->_init();
        $this->setResourceName('area_province');
        $this->getSelect()
            ->from(array('main_table' => 'area_province'));
    }
}