<?php
class Datadrivendesign_Knowledgebase_Block_Adminhtml_Knowledgebase_Edit_Tab_Categories extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
	protected $_categoryIds;
    protected $_selectedNodes = null;
	
	public function __construct() {
		parent::__construct();
		$this->setTemplate('knowledgebase/edit/tab/categories.phtml');
	}

    /**
     * Retrieve currently edited product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
		return false;
    }

    protected function getCategoryIds()
    {
		$document = Mage::getModel('knowledgebase/document')->load($this->getRequest()->getParam('id'));
		if($document->getData('category_ids') == "") {
			return array();
		} else {
			return json_decode($document->getData('category_ids'), true);	
		}
    }

    public function getIdsString()
    {
		$document = Mage::getModel('knowledgebase/document')->load($this->getRequest()->getParam('id'));
		return $document->getData('category_ids');
    }

    public function getRootNode()
    {
        $root = $this->getRoot();
        if ($root && in_array($root->getId(), $this->getCategoryIds())) {
            $root->setChecked(true);
        }
        return $root;
    }

    public function getRoot($parentNodeCategory=null, $recursionLevel=3)
    {
		if(is_null($parentNodeCategory)) {
			$parentNodeCategory = Mage::getModel('catalog/category')->load(Mage::getStoreConfig('knowledgebase_options/general/root_id'));
		}		
		
		$root = $this->getNode($parentNodeCategory, $recursionLevel);
        $root->setIsVisible(false);

        return $root;
    }

    protected function _getNodeJson($node, $level=1)
    {
        $item = parent::_getNodeJson($node, $level);

        $isParent = $this->_isParentSelectedCategory($node);

        if ($isParent) {
            $item['expanded'] = true;
        }

        if (in_array($node->getId(), $this->getCategoryIds())) {
            $item['checked'] = true;
        }

        if ($this->isReadonly()) {
            $item['disabled'] = true;
        }
        return $item;
    }

    protected function _isParentSelectedCategory($node)
    {
        foreach ($this->_getSelectedNodes() as $selected) {
            if ($selected) {
                $pathIds = explode('/', $selected->getPathId());
                if (in_array($node->getId(), $pathIds)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function _getSelectedNodes()
    {
        if ($this->_selectedNodes === null) {
            $this->_selectedNodes = array();
            $root = $this->getRoot();
            foreach ($this->getCategoryIds() as $categoryId) {
                if ($root) {
                    $this->_selectedNodes[] = $root->getTree()->getNodeById($categoryId);
                }
            }
        }

        return $this->_selectedNodes;
    }

    public function getCategoryChildrenJson($categoryId)
    {
        $category = Mage::getModel('catalog/category')->load($categoryId);
        $node = $this->getRoot($category, 1)->getTree()->getNodeById($categoryId);

        if (!$node || !$node->hasChildren()) {
            return '[]';
        }

        $children = array();
        foreach ($node->getChildren() as $child) {
            $children[] = $this->_getNodeJson($child);
        }

        return Mage::helper('core')->jsonEncode($children);
    }

    public function getLoadTreeUrl($expanded=null)
    {
        return $this->getUrl('*/*/categoriesJson', array('_current'=>true));
    }

    /**
     * Return distinct path ids of selected categories
     *
     * @param int $rootId Root category Id for context
     * @return array
     */
    public function getSelectedCategoriesPathIds($rootId = false)
    {
        $ids = array();
        $collection = Mage::getModel('catalog/category')->getCollection();

        if ($rootId) {
            $collection->addFieldToFilter('parent_id', $rootId);
        } else {
            $collection->addFieldToFilter('entity_id', array('in'=>$this->getCategoryIds()));
        }

        foreach ($collection as $item) {
            if ($rootId && !in_array($rootId, $item->getPathIds())) {
                continue;
            }
            foreach ($item->getPathIds() as $id) {
                if (!in_array($id, $ids)) {
                    $ids[] = $id;
                }
            }
        }
        return $ids;
    }
}