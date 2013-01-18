<?php
class Datadrivendesign_Knowledgebase_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();
    	$this->renderLayout();
    }
	
	public function getDocumentTreeAction()
	{		
		try {
			$id = $this->getRequest()->getParam('id');		
			$response['children'] = $this->getChildren($id);
			$response['status'] = 'OK';
		} catch (Exception $e) {
			$response['status'] = 'ERROR'.$e;
		}
		echo json_encode($response);
	}
	
	public function addToCartAction()
	{
		$productId = $this->getRequest()->getParam('id');
		$qty = $this->getRequest()->getParam('qty');
		$model = Mage::getModel('catalog/product');
		
		$product = $model->load($productId);

		$cart = Mage::getModel('checkout/cart');
    	$cart->init();
    	$cart->addProduct($product, array('qty' => $qty));
   		$cart->save();
    
   		Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
	}
	
	public function getDocumentAction()
	{
		$id = $this->getRequest()->getParam('id');
		$response = NULL;
		try {
			$document = Mage::getModel('knowledgebase/document')->load($id);
			if(!$document){
				$response['status'] = 'ERROR';
			} else {
				$response['data'] = array(
					'id'			=> $document->getData('entity_id'),
					'name'			=> $document->getData('title'),
					'published'		=> $document->getData('published'),
					'doc_id'		=> $document->getData('doc_id'),
					'access_key'	=> $document->getData('access_key'),
					'leaf'			=> 1
				);

				$response['status']	= 'OK';	
			}
		} catch (Exception $e) {
			$response['status'] = 'ERROR';
		}
		echo json_encode($response);
	}
	
	public function getProductInfoAction()
	{
		$query = $this->getRequest()->getParam('q');
		$query = preg_replace('/\s+/', '', $query);
		$response = NULL;
		try {
			$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$query);
			if(!$product){
				$response['status'] = 'ERROR';
			} else {
				$response['productId']	= $product->getId();
				$response['mpn']		= $product->getSku();
				$response['name']		= $product->getName();
				$response['price']		= $product->getFinalPrice();
				$response['image']		= (string)Mage::helper('catalog/image')->init($product, 'small_image')->resize(150);
				$response['status']		= 'OK';
			}			
		} catch (Exception $e) {
			$response['status'] = 'ERROR';	
		}		
		echo json_encode($response);
	}
	
	private function getChildren($parentId = NULL){
		$model = Mage::getModel('catalog/category');
		$response = NULL;
		
		if($parentId == 'root') {
			$parentId = Mage::getModel('catalog/category')->load(Mage::getStoreConfig('knowledgebase_options/general/root_id'))->getId();
		}
		
		if($model->getCategories($parentId)) {
			$categories = $model->getCategories($parentId);			
		
			foreach($categories as $category) {
				$response[] = array(
					'id'	=> $category->getId(),
					'name'	=> $category->getName(),
					'leaf'	=> 0
				);
			}
		}
		
		$documents = Mage::getModel('knowledgebase/document')->getCollection();
		$filter = '%"'.$parentId.'"%';
		$documents->addFieldToFilter('category_ids', array('like' => $filter));
		
		foreach($documents as $document) {
			$response[] = array(
				'id'			=> $document->getData('entity_id'),
				'name'			=> $document->getData('title'),
				'published'		=> $document->getData('published'),
				'doc_id'		=> $document->getData('doc_id'),
				'access_key'	=> $document->getData('access_key'),
				'leaf'			=> 1
			);
		}
		
		return $response;
		
	}
}