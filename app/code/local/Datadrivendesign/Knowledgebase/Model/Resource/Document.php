<?php
class Datadrivendesign_Knowledgebase_Model_Resource_Document extends Mage_Core_Model_Resource_Db_Abstract 
{
	protected function _construct()
	{
		$this->_init('knowledgebase/document', 'entity_id');
	}
}