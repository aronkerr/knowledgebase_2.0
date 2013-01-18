<?php
 //Mage::getStoreConfig('knowledgebase_options/general/scribd_api');

/* Unofficial Scribd PHP Class library */

class Datadrivendesign_Knowledgebase_Helper_Scribd extends Mage_Core_Helper_Abstract
{
	//public $api_key;
	//public $secret;
	private $url;
	public $session_key;
  	public $my_user_id;
	private $error;

	public function __construct() {
		$this->url = Mage::getStoreConfig('knowledgebase_options/general/scribd_url').'api_key='.Mage::getStoreConfig('knowledgebase_options/general/scribd_api');
	 }

  /**
   * Upload a document from a file
   * @param string $file : relative path to file
   * @param string $doc_type : PDF, DOC, TXT, PPT, etc.
   * @param string $access : public or private. Default is Public.
   * @param int $rev_id : id of file to modify
   * @return array containing doc_id, access_key, and secret_password if nessesary.
   */
	public function upload($file, $doc_type = null, $access = null, $rev_id = null){
		$method = "docs.upload";
		$params['doc_type'] = $doc_type;
		$params['access'] = $access;
		$params['rev_id'] = $rev_id;
		$params['file'] = "@".$file;

		$result = $this->postRequest($method, $params);
		return $result;
	}

	/**
	* Get settings of a document
	* @return array containing doc_id, title , description , access, tags, show_ads, license, access_key, secret_password
	*/
	public function getSettings($doc_id){
		$method = "docs.getSettings";
		$params['doc_id'] = $doc_id;

		$result = $this->postRequest($method, $params);
		return $result;
	}
  /**
   * Change settings of a document
   * @param array $doc_ids : document id
   * @param string $title : title of document
   * @param string $description : description of document
   * @param string $access : private, or public
   * @param string $license : "by", "by-nc", "by-nc-nd", "by-nc-sa", "by-nd", "by-sa", "c" or "pd"
   * @param string $access : private, or public
   * @param array $show_ads : default, true, or false
   * @param array $tags : list of tags
   * @return string containing DISPLAYABLE", "DONE", "ERROR", or "PROCESSING" for the current document.
   */
	public function changeSettings($doc_ids, $title = null, $description = null, $access = null, $license = null, $parental_advisory = null, $show_ads = null, $tags = null){
		$method = "docs.changeSettings";
		$params['doc_ids'] = $doc_ids;
		$params['title'] = $title;
		$params['description'] = $description;
		$params['access'] = $access;
		$params['license'] = $license;
		$params['show_ads'] = $show_ads;
		$params['tags'] = $tags;

		$result = $this->postRequest($method, $params);
		return $result;
	}
  /**
   * Delete a document
   * @param int $doc_id : document id
   * @return 1 on success;
   */
	public function delete($doc_id){
		$method = "docs.delete";
		$params['doc_id'] = $doc_id;

		$result = $this->postRequest($method, $params);
		return $result;
	}
	  /**
   * Get document ready state. See http://www.scribd.com/developers/api?method_name=docs.getConversionStatus.
   * @param int $doc_id : document id
   * @return array(). ["DISPLAYABLE", "DONE", "ERROR", "PROCESSING"]
   */
	public function getReadyState($doc_id){
		$method = "docs.getConversionStatus";
		$params['doc_id'] = $doc_id;

		$result = $this->postRequest($method, $params);
		return $result;
	}
	  /**
   * Search the Scribd database
   * @param string $query : search query
   * @param int $num_results : number of results to return (10 default, 1000 max)
   * @param int $num_start : number to start from
   * @param string $scope : scope of search, "all" or "user"
   * @return array of results, each of which contain doc_id, secret password, access_key, title, and description
   */
	public function search($query, $num_results = null, $num_start = null, $scope = null){
		$method = "docs.search";
		$params['query'] = $query;
		$params['num_results'] = $num_results;
		$params['num_start'] = $num_start;
		$params['scope'] = $scope;

		$result = $this->postRequest($method, $params);

		return $result['result_set'];
	}
	
	 /**
   * Get thumbnail for document. See http://www.scribd.com/developers/api?method_name=thumbnail.get
   * @param int $doc_id : document id
   * @param int $width : (optional) Width in px of the desired image. If not included, will use the default thumb size.
   * @param int $height : (optional) Height in px of the desired image. If not included, will use the default thumb size.
   * @return array containing thumbnail_url.
   */
   	public function getThumbnail($doc_id, $width = NULL, $height = NULL){
		$method = "thumbnail.get";
		$params['doc_id'] = $doc_id;
		$params['width'] = $width;
		$params['height'] = $height;

		$result = $this->postRequest($method, $params);
		return $result;
	}

	/**
   * Get download url for document. See http://www.scribd.com/developers/api?method_name=thumbnail.get
   * @param int $doc_id : document id
   * @param text $doc_type : (required) The type of file to download. If "original", will get a link to the original file uploaded, regardless of its extension. options[pdf, txt, original]
   * @return array containing download_link.
   */
   public function getDownloadUrl($doc_id, $doc_type = 'original'){
		$method = "docs.getDownloadUrl";
		$params['doc_id'] = $doc_id;
		$params['doc_type'] = $doc_type;

		$result = $this->postRequest($method, $params);
		return $result;
	}
   
	private function postRequest($method, $params){
		$params['method'] = $method;
		$params['session_key'] = $this->session_key;
    	$params['my_user_id'] = $this->my_user_id;


		$post_params = array();
		foreach ($params as $key => &$val) {
			if(!empty($val)){

				if (is_array($val)) $val = implode(',', $val);
				if($key != 'file' && substr($val, 0, 1) == "@"){
					$val = chr(32).$val;
				}

				$post_params[$key] = $val;
			}
		}    
		
		$request_url = $this->url;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $request_url );       
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1 );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params );
		$xml = curl_exec( $ch );
		$result = simplexml_load_string($xml); 
		curl_close($ch);

			if($result['stat'] == 'fail'){
				$this->error = $result->error->attributes()->code;
				throw new Exception($result->error->attributes()->message, $result->error->attributes()->code);
				return 0;

			}
			if($result['stat'] == "ok"){				
				return $result;
			}
	}
}
?>