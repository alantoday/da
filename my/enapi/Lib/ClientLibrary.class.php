<?php
/**
 * Empower Network API
 * 
 * @author Nikolay Kolev
 * @author Sven Arild Helleland
 * @version 1.1
 * @package API
 */

namespace EmpowerNetwork\API;

/**
 * Client API Library
 * 
 * @author Nikolay Kolev
 * @author Sven Arild Helleland
 * @version 1.1
 * @package API
 * @subpackage ClientLibrary
 */
class ClientLibrary {
	
	/**
	 * The location of the API
	 * 
	 * Hardcoded to make integration easier on the client side.
	 * 
	 * @access protected
	 * @var string 
	 */
	protected $_domain = 'https://api.empowernetwork.com/';
	
	/**
	 * The username for the API
	 * 
	 * @access protected
	 * @var string 
	 */
	protected $_api_access_id;
	
	/**
	 * The key for the API
	 * 
	 * @access protected
	 * @var string 
	 */
	protected $_api_key;
	
	/**
	 * The version to use for the API
	 * 
	 * Hardcoded to make integration easier on the client side.
	 * 
	 * @access protected
	 * @var string 
	 */
	protected $_version = '1.1';

	/**
	 * The language you want the content in
	 *
	 * @access protected
	 * @var string
	 */
	protected $_language = 'en';
	
	/**
	 * How many seconds to wait before terminating the API call
	 * 
	 * @var int
	 */
	protected $_timeout = 20;
	
	/**
	 * The content from the response
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_response;
	
	/**
	 * The status code from the response
	 * 
	 * @access protected
	 * @var int
	 */
	protected $_status_code;
	
	/**
	 * The previous link from the response (if there is any)
	 * 
	 * @access protected
	 * @var string
	 */
	protected $_previous_link;
	
	/**
	 * The next link from the response (if there is any)
	 * 
	 * @access protected
	 * @var string
	 */
	protected $_next_link;
	
	/**
	 * The connection handle
	 * 
	 * @access protected
	 * @var mixed
	 */
	protected $_curl_handle;
	
	/**
	 * If we should verify the SSL certificate on the API server
	 * 
	 * Note. 
	 * 
	 * @var bool
	 */
	protected $_ssl_verify_peer = false;

	/**
	 * Constructor
	 * 
	 * @param string $accessId			The username we want to connect with
	 * @param string $apiKey				The API key we want to connect with
	 */
	public function __construct($accessId, $apiKey) {
		$this->_api_key = $apiKey;
		$this->_api_access_id = $accessId;
		
		$this->resetApi();
		}	
		
		
	/**
	 * Reset the library for another API call
	 * 
	 * @return void 
	 */
	public function resetApi() {
		$this->_response = false;
		$this->_next_link = false;
		$this->_previous_link = false;
		$this->_status_code = false;
		$this->_curl_handle = null;
		}
		
		
	/**
	 * Set API Version
	 * 
	 * @param string $version		Which version we want to retrieve data from
	 * @return void
	 */
	public function setVersion($version) {
		$this->_version = $version;
		}	
		
	
	/**
	 * Set if we should verify the ssl peer's certificate
	 * 
	 * @param bool $option	If you want to verify it or not
	 * @return bool					True on success, false on failure
	 */	
	public function sslVerifyPeer($option) {
		if (!is_bool($option)) {
			return false;
			}
			
		$this->_ssl_verify_peer = $option;
		
		return true;
		}
		
	/**
	 * Returns overview information for the member leads
	 * 
	 * @return bool		True on success, false on failure
	 */
	public function prepareLeadsOverview() {
		
		return $this->_sendRequest($this->_domain.'Leads/leadsOverview', 'GET');
		}


	/**
	 * Returns the leads
	 *
	 * @param int $recordsNum			How many leads per page
	 * @param int $pagesNum				How many pages to return
	 * @param int|string $id			Which id to get the data from or (first|last)
	 * @param int $page						Which page do we want to show
	 * @param string $hash				The security hash
	 * @return bool								Whether the operation was a success
	 */
	public function prepareLeads($recordsNum, $pagesNum, $id=null, $page=null, $hash=null) {
		$url = $this->_domain.'Leads/leads';

		$url .= '/num/'.(int) $recordsNum.'/show_pages/'.(int)$pagesNum;

		if (!empty($id)) {
			$url .= '/id/'.$id;
			}

		if (!empty($page)) {
			$url .= '/page/'.(int) $page;
			}

		if (!empty($hash)) {
			$url .= '/hash/'.$hash;
			}

		return $this->_sendRequest($url, 'GET');
		}
		
		
	/**
	 * Returns information for a lead
	 * 
	 * @param int $leadId		The lead id we want more information about
	 * @return bool					True on success, false on failure
	 */
	public function prepareLead($leadId) {
		
		return $this->_sendRequest($this->_domain.'Leads/lead/id/'.(int) $leadId, 'GET');
		}
		
		
	/**
	 * Returns overview information for the downline
	 * 
	 * @return bool		True on success, false on failure
	 */
	public function prepareDownlineOverview() {
		
		return $this->_sendRequest($this->_domain.'Downline/downlineOverview', 'GET');
		}

	/**
	 * Returns the username of the logged in member
	 *
	 * @return bool		True on success, false on failure
	 */
	public function prepareUsername() {

		return $this->_sendRequest($this->_domain.'Downline/username', 'GET');
		}
		
		
	/**
	 * Returns the downline
	 *
	 * @param int $program			The program id we want info for
	 * @param string $status		The status (paid/unpaid)
	 * @param int $page					Which page do we want
	 * @param int $count				How many records to get
	 * @param string $type			The type of downline
	 * @return bool							True on success, false on failure
	 */
	public function prepareDownline($program=null, $status=null, $page=null, $count=null, $type=null) {
		$url = $this->_domain.'Downline/downline';
		
		if ($program !== false) {
			$url .= '/program/'.(int) $program;
			}
			
		if ($status !== false) {
			$url .= '/status/'.trim($status);
			}
		
		if ($page !== false) {
			$url .= '/page/'.(int) $page;
			}
			
		if ($count !== false) {
			$url .= '/num/'. (int) $count;
			}

		if ($type !== false) {
			$url .= '/type/'.$type;
			}
		
		return $this->_sendRequest($url, 'GET');
		}
		
		
	/**
	 * Returns payment statistics
	 * 
	 * @return bool		True on success, false on failure
	 */
	public function preparePaymentStats() {
		
		return $this->_sendRequest($this->_domain.'Payments/paymentsStats', 'GET');
		}


	/**
	 * Returns information about all payments
	 *
	 * @param int $records		How many pages to return
	 * @param int $pages			How many records to get per page
	 * @param int $id					From which record to get the data
	 * @param int $page				Which page we want
	 * @param string $hash		The security hash to validate the sent page and id
	 * @return bool
	 */
	public function preparePaymentList($records, $pages, $id=null, $page=null, $hash=null) {
		$url = $this->_domain.'Payments/getCommissionHistory/num/'.(int)$records.'/show_pages/'.(int)$pages;

		if (!empty($page)) {
			$url .= '/page/'.(int) $page;
			}
		
		if (!empty($id)) {
			$url .= '/id/'. $id;
			}

		if (!empty($hash)) {
			$url .= '/hash/'.$hash;
			}

		return $this->_sendRequest($url, 'GET');
		}
		
		
	/**
	 * Returns payment info
	 *
	 * @param int $id		The payment record id
	 * @return bool			True on success, false on failure
	 */
	public function preparePaymentInfo($id) {
				
		return $this->_sendRequest($this->_domain.'Payments/paymentInfo/id/'.(int) $id, 'GET');
		}
		
		
	/**
	 * Returns information about the email subscription. The different statuses
	 * for the different lists and the global statuses
	 *
	 * @return bool		True on success, false on failure
	 */
	public function prepareEmailSubscriptionInfo() {
		
		return $this->_sendRequest($this->_domain.'EmailSubscription/info', 'GET');
		}
		
		
	/**
	 * Returns information about a subscription for a specific list together with
	 * all api calls made from our system and their status
	 *
	 * @param int $listId		The id of the list
	 * @return bool					True on success, false on failure
	 */
	public function prepareEmailSubscriptionRecord($listId) {
		
		return $this->_sendRequest($this->_domain.'EmailSubscription/record/id/'.(int) $listId, 'GET');
		}
		
		
	/**
	 * Subscribe or unsubscribe to a list
	 *
	 * @param int $id						The id of the list
	 * @param bool $subscribe		Whether to subscribe or unsubscribe to the list
	 * @return bool
	 */
	public function prepareEmailSubscriptionChange($id, $subscribe) {
		$data = array('listid' => (int) $id
									, 'subscribe' => (($subscribe === true)?1:0));
				
		return $this->_sendRequest($this->_domain.'EmailSubscription/subscription', 'PUT', $data);
		}
		
		
	/**
	 * Returns information about the statuses for the different programs for the member
	 *
	 * @return bool		True on success, false on failure
	 */
	public function prepareProgramStats() {
		
		return $this->_sendRequest($this->_domain.'Program/programStats', 'GET');
		}
		
		
	/**
	 * Returns information about the name and member status for that program
	 *
	 * @param int $programId		The program id we want info for
	 * @return bool							True on success, false on failure
	 */
	public function prepareProgram($programId) {
				
		return $this->_sendRequest($this->_domain.'Program/program/program/'.(int) $programId, 'GET');
		}
		
		
	/**
	 * Returns information about a member in the downline
	 *
	 * @param int $id						The id we want member info for
	 * @param bool $isUsername	If the id is a username or not (Default: false)
	 * @return bool							True on success, false on failure
	 */
	public function prepareDownlineMember($id, $isUsername=false) {
		$url = $this->_domain.'Downline/member/';
		
		if ($isUsername === false) {
			$url .= 'id/'.(int) $id;
			}
		else {
			$url .= 'username/'.trim($id);
			}

		return $this->_sendRequest($url, 'GET');
		}
		
		
	/**
	 * The response returned from the API server
	 * 
	 * @return array	An array with the correct information according to what method called
	 */
	public function getResponse() {
		return json_decode($this->_response, true);
		}
		
		
	/**
	 * The last returned status code
	 * 
	 * @return int
	 */
	public function getStatusCode() {
		return $this->_status_code;
		}
		
		
	/**
	 * The next page link
	 * 
	 * @return string
	 */
	public function getNextLink() {
		return $this->_next_link;
		}
		
		
	/**
	 * The previous page link
	 * 
	 * @return string
	 */
	public function getPrevLink() {
		return $this->_previous_link;
		}
		
		
	/**
	 * Sends an request to the API and returns the data and the response code
	 * 
	 * @access protected
	 * @param string $url		The url we will send the response to
	 * @param string $type	What the type of request it is (GET, POST, PUT, DELETE)
	 * @param array $data		Any data sent over if POST or PUT in an array
	 * @return boolean			True on success, false on failure
	 */	
	protected function _sendRequest($url, $type, $data=null) {
		
		$this->_curl_handle = curl_init();
		
		curl_setopt($this->_curl_handle, CURLOPT_URL, $url);
		
		curl_setopt($this->_curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->_curl_handle, CURLOPT_VERBOSE, 1);
		curl_setopt($this->_curl_handle, CURLOPT_HEADER, 1);
		curl_setopt($this->_curl_handle, CURLOPT_HTTPHEADER, $this->_createHeaders($type));
		curl_setopt($this->_curl_handle, CURLOPT_TIMEOUT, $this->_timeout);
		curl_setopt($this->_curl_handle, CURLOPT_SSL_VERIFYPEER, $this->_ssl_verify_peer);
		
		if ($type == 'POST') {
			curl_setopt($this->_curl_handle, CURLOPT_POST, 1);
			}
		elseif (in_array($type, array('PUT', 'DELETE'))) {
			curl_setopt($this->_curl_handle, CURLOPT_CUSTOMREQUEST, $type);
			}
		
		if (!empty($data)) {
			curl_setopt($this->_curl_handle, CURLOPT_POSTFIELDS, http_build_query($data));
			}
		
		$response = curl_exec($this->_curl_handle);

		if ($response === false) {
			curl_close($this->_curl_handle);
			
			return false;
			}
			
		//Get the returned http status code
		$this->_status_code = curl_getinfo($this->_curl_handle, CURLINFO_HTTP_CODE);
		
		//Get the header size
		$header_size = curl_getinfo($this->_curl_handle, CURLINFO_HEADER_SIZE);
				
		curl_close($this->_curl_handle);
		
		$this->_parseResponse($response, $header_size);
		
		return true;
		}
		
		
	/**
	 * Parse The Response From The API
	 * 
	 * @access protected
	 * @param string $response	The response from the API call
	 * @param int $headerSize		The reported header size from the API call
	 * @return void
	 */	
	protected function _parseResponse($response, $headerSize) {
		$header = substr($response, 0, $headerSize);
		$body = substr($response, $headerSize);
		
		//Parse the headers
		$headers = explode("\n", str_replace("\r", '', $header));
	
    foreach($headers as $value){
			$header = explode(": ",$value);
			
			if ($header[0] == 'Link' && preg_match('/<([^<]+)>, rel="([^"]+)"/i', $header[1], $matches)) {
				
				if ($matches[2] == "next") {
					$this->_next_link = $matches[1];
					}
				elseif ($matches[2] == "prev") {
					$this->_previous_link = $matches[1];
					}
				}
			}
			
		//Set the inner properties
		if (!empty($body)) {
			$this->_response = $body;
			}
		}		
		
		
	/**
	 * Create HTTP Header's
	 *
	 * @access protected
	 * @param string $type 	The form method
	 * @return array				Returns the http headers
	 */
	protected function _createHeaders($type) {
		$header = array();

		switch (strtoupper($type)) {
			case 'DELETE':
			case 'GET':
				//You dont set a Content-Type for these calls.
				break 1;
			case 'PUT':
			case 'POST':
				$header[] = "Content-Type: application/x-www-form-urlencoded;charset=UTF-8";
			break 1;
			}
		
		$header[] = "Accept: application/json;charset=UTF-8;version=".$this->_version;
		$header[] = "Accept-Language: ".$this->_language;
		$header[] = "User-Agent: Kaizen-Web; Client API (1.1)";
		$header[] = "Login: ".base64_encode($this->_api_access_id.':'.$this->_api_key);
		
		return $header;
		}	
	}