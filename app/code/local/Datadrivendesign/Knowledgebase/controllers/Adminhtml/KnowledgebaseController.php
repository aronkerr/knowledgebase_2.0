<?php

class Datadrivendesign_Knowledgebase_Adminhtml_KnowledgebaseController extends Mage_Adminhtml_Controller_action
{
	protected $file;
	protected $doc_id;
	protected $rev_id;
	protected $access;
	
	protected function _initAction() {		
		$this->loadLayout()
			->_setActiveMenu('datadrivendesign/knowledgebase')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Knowledge Base'), Mage::helper('adminhtml')->__('Knowledge Base'));
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction();
		$this->_addContent($this->getLayout()->createBlock('knowledgebase/adminhtml_knowledgebase'));
		$this->renderLayout();
	}

	public function editAction() {	
		$id = $this->getRequest()->getParam('id');
		$document = Mage::getModel('knowledgebase/document')->load($id);
		
		if ($document->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$document->setData($data);
			}

			Mage::register('knowledgebase_data', $document);

			$this->loadLayout();
			$this->_setActiveMenu('knowledgebase/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('knowledgebase/adminhtml_knowledgebase_edit'))
				->_addLeft($this->getLayout()->createBlock('knowledgebase/adminhtml_knowledgebase_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('knowledgebase')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
	
	public function deleteAction() {	
		$id = $this->getRequest()->getParam('id');
		$scribd = Mage::helper('knowledgebase/scribd');
		$document = Mage::getModel('knowledgebase/document')->load($id);
		
		if( $id > 0 ) {
			try {
				// Delete doc_id from Scribd. See http://www.scribd.com/developers/api?method_name=docs.delete
				if($document->getDoc_id()) {
					$scribd->delete($document->getDoc_id());
				}

				// If successfully deleted from Scribd delete reference in Magento database.
				$document->delete();							

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
 	
	public function massDeleteAction() {
        $knowledgebaseIds = $this->getRequest()->getParam('knowledgebase');
		$scribd = Mage::helper('knowledgebase/scribd');
		
        if(!is_array($knowledgebaseIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($knowledgebaseIds as $knowledgebaseId) {
                    $document = Mage::getModel('knowledgebase/document')->load($knowledgebaseId);

					// Delete doc_id from Scribd. See http://www.scribd.com/developers/api?method_name=docs.delete
					if($document->getDoc_id()) {
						$scribd->delete($document->getDoc_id());
					}	

					// If successfully deleted from Scribd delete reference in Magento database.
                    $document->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($knowledgebaseIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
	public function saveAction() {
		$doc_type = 'pdf';
		$access = 'private';
		$rev_id = NULL;
		$file = NULL;
		
		$model = Mage::getModel('knowledgebase/document');
		$scribd = Mage::helper('knowledgebase/scribd');
		
		if ($data = $this->getRequest()->getPost()) {		
			//Check if we are updating the document. If we are assign the rev_id.
			if($this->getRequest()->getParam('id')){
				$rev_id = $model->getData('doc_id');
			}						
			
			//If file field is set upload file to scribd
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {	
				try {					
					$file = $_FILES['filename']['tmp_name'];					
					$result = $scribd->upload($file, $doc_type, $access, $rev_id);
					
					// Store returned Scribd file information in $data array() for writing to database.
					$data['doc_id'] = (string)$result->doc_id;
					$data['doc_type'] = $doc_type;
					$data['access_key'] = (string)$result->access_key;
					$data['secret_password'] = (string)$result->secret_password;
					
				} catch (Exception $e) {
					Mage::log($e);
				}				
			}
			
			//Save document
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'))
				->save();
			
			try {				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('knowledgebase')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $this->model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
		//If no post data found show error message
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('knowledgebase')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
	
    public function massStatusAction() {
        $knowledgebaseIds = $this->getRequest()->getParam('knowledgebase');
        if(!is_array($knowledgebaseIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($knowledgebaseIds as $knowledgebaseId) {
                    $knowledgebase = $this->model->load($knowledgebaseId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($knowledgebaseIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction() {
        $fileName   = 'knowledgebase.csv';
        $content    = $this->getLayout()->createBlock('knowledgebase/adminhtml_knowledgebase_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName   = 'knowledgebase.xml';
        $content    = $this->getLayout()->createBlock('knowledgebase/adminhtml_knowledgebase_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
	
	public function categoriesAction() {
		$block = $this->getLayout()->createBlock('knowledgebase/adminhtml_knowledgebase_edit_tab_categories')->toHtml();
		$this->getResponse()->setBody($block);
	}
	
	public function categoriesJsonAction() {
		$id = $this->getRequest()->getParam('category');
		$block = $this->getLayout()->createBlock('knowledgebase/adminhtml_knowledgebase_edit_tab_categories');
		
		$categoryId = $block->getCategoryChildrenJson($id);
		$this->getResponse()->setBody($categoryId);
	}
}