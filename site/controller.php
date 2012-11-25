<?php
/**
 * @package    SkroutzEasy component for Joomla 1.5.x and 1.6.x
 * @copyright  Copyright (c) 2012 Skroutz S.A. - http://www.skroutz.gr
 * @link       http://developers.skroutz.gr/oauth2
 * @license    MIT
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class SkroutzEasyController extends JController
{
	/**
	 * Default constructor
	 *
	 * @access public
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Method to display the view
	 *
	 * @access public
	 */
	function display()
	{
		parent::display();
	}

	/**
	 * Redirect user for oAuth authorization
	 *
	 * @access public
	 */
	function login()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// More info: http://docs.joomla.org/Component_parameters
		// access $params and construct the redirect URL for authorization
		$params = &JComponentHelper::getParams('com_skroutzeasy');

		// Url for redirection
		$url = $this->getAuthorizationUrl($params);

		// Redirect
		$this->setRedirect($url);
	}

	/**
	 * This is the callback
	 *
	 * @access public
	 */
	function callback()
	{
		$mainframe =& JFactory::getApplication();

		$code = JRequest::getString('code');
		$error = JRequest::getString('error');

		// Load appropriate VM code
		$this->loadVm();

		if ($code) {

			// More info: http://docs.joomla.org/Component_parameters
			// access $params and construct the redirect URL for authorization
			$params = &JComponentHelper::getParams('com_skroutzeasy');

			// Get the access token
			$access_token = $this->getAccessToken($params, $code);

			// Get the address
			$address = $this->getAddress($params, $access_token);

			// Create the data
			$data = $this->mapUserData($params, $address);

			// Try to find the user
			if ($user = $this->findUser($data)) {
				// Login the user
				$this->loginUser($data);

				// Update the user
				$result = $this->updateUser($data);
			} else {
				// Create the user
				$result = $this->registerUser($data);

				// Activate the user
				$this->activateUser($result['user']);

				// Login the user
				$this->loginUser($data);
			}

			// Respect return option for redirects
			if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
				$return = base64_decode($return);
				if (!JURI::isInternal($return)) {
					$return = '';
				}
			} else {
				$return = 'index.php?option=com_virtuemart';
			}

			// Redirect
			$this->setRedirect($return, $result['message']);
		} elseif ($error) {
			// Get the error message from the error description parameter
			$message = $mainframe->enqueueMessage(JText::_(JFilter::clean(JRequest::getString('error_description')), false));

			// Redirect to index and display the message
			$this->setRedirect('index.php?option=com_virtuemart', $message);
		}
	}

	/**
	 * If you have customized your Joomla or Virtuemart installation
	 * this is the only function you will most likely need to update.
	 *
	 * This function maps the JSON data to the locally used form fields.
	 *
	 * Note that the most common fields are mapped directly and non standard ones
	 * can be customized in the Administration interface (Global component parameters)
	 *
	 * @param $params
	 * @param $json
	 *

	 * @return array
	 * @access public
	 */
	private function mapUserData($params, $json)
	{
		// Import JUserHelper
		jimport('joomla.user.helper');

		// Find the state
		$billing_state = $this->findState($json->region);

		// Populate data
		$data['name'] = $json->first_name . " " . $json->last_name;
		$data['username'] = $json->email; // Using the email as the username
		$data['password'] = $data['password2'] = JUserHelper::genRandomPassword(); // Generate random password
		$data['title'] = "";
		$data['email'] = $json->email;
		$data['first_name'] = $json->first_name;
		$data['middle_name'] = "";
		$data['last_name'] = $json->last_name;
		$data['address_1'] = $json->address;
		$data['address_2'] = "";
		$data['zip'] = $json->zip;
		$data['city'] = $json->city;

		if ($this->isVmVersion("2.0")) {
			$data['virtuemart_country_id'] = $billing_state->virtuemart_country_id;
			$data['virtuemart_state_id'] = $billing_state->virtuemart_state_id;
		} else if ($this->isVmVersion("1.1")) {
			$data['country'] = $billing_state->country;
			$data['state'] = $billing_state->state;
		}

		$data['phone_1'] = $json->phone;
		$data['phone_2'] = $json->mobile;
		$data['agreed'] = "1"; // Well, I guess everybody does!
		$data['address_type'] = 'BT';
		$data[$params->get('invoice_type_field')] = $params->get('invoice_type_retail_value');

		if ($json->invoice) {
			$data[$params->get('company_field')] = $json->company;
			$data[$params->get('profession_field')] = $json->profession;
			$data[$params->get('afm_field')] = $json->afm;
			$data[$params->get('doy_field')] = $json->doy;
			$data[$params->get('company_phone_field')] = $json->company_phone;
			$date[$params->get('invoice_type_field')] = $params->get('invoice_type_invoice_value');
		}

		if (isset($json->shipping_address)) {
			// Find the state
			$shipping_state = $this->findState($data->shipping_address->region);

			// Set the correct prefix
			if ($this->isVmVersion("2.0")) {
				$shipping_prefix = 'shipto_';
			} else if ($this->isVmVersion("1.1")) {
				$shipping_prefix = 'shipping_';
			} else {
				// TODO: Support other VirtueMart versions
				$shipping_prefix = '';
			}

			// Populate data
			$data[$shipping_prefix.'address_type_name'] = 'Shipping Address';
			$data[$shipping_prefix.'company'] = '';

			$data[$shipping_prefix.'first_name'] = $json->shipping_address->first_name;
			$data[$shipping_prefix.'middle_name'] = "";
			$data[$shipping_prefix.'last_name'] = $json->shipping_address->last_name;
			$data[$shipping_prefix.'address_1'] = $json->shipping_address->address;
			$data[$shipping_prefix.'address_2'] = "";
			$data[$shipping_prefix.'zip'] = $json->shipping_address->zip;
			$data[$shipping_prefix.'city'] = $json->shipping_address->city;

			if ($this->isVmVersion("2.0")) {
				$data[$shipping_prefix.'virtuemart_country_id'] = $shipping_state->virtuemart_country_id;
				$data[$shipping_prefix.'virtuemart_state_id'] = $shipping_state->virtuemart_state_id;
			} else if ($this->isVmVersion("1.1")) {
				$data[$shipping_prefix.'country'] = $shipping_state->country;
				$data[$shipping_prefix.'state'] = $shipping_state->state;
			}

			$data[$shipping_prefix.'phone_1'] = $json->shipping_address->phone;
			$data[$shipping_prefix.'phone_2'] = $json->shipping_address->mobile;
		}

		// Return the data
		return $data;
	}

	/**
	 * Given a region this function tries to fetch the state
	 *
	 * @param $region
	 *
	 * @access private
	 */
	private function findState($region)
	{
		if ($this->isVmVersion("2.0")) {
			return $this->findStateVm2($region);
		} else if ($this->isVmVersion("1.1")) {
			return $this->findStateVm1($region);
		}
	}

	/**
	 * Given a region this function tries to fetch the equivalent VmState
	 *
	 * This is the VirtueMart 2 version.
	 *
	 * @param $region
	 *
	 * @access private
	 */
	private function findStateVm2($region)
	{
		// Load VirtueMart country and state models
		if (!class_exists('VirtueMartModelCountry')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'country.php');
		if (!class_exists('VirtueMartModelState')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'state.php');

		// Load countries
		$countryModel = VmModel::getModel('country');
		// XXX: Hardcoded country
		$countries = $countryModel->getCountries(true, 1, "Greece");
		$country = $countries[0];
		// NOTE: Alternative but the (int) cast in getCountryByCode breaks this
		//$country = $countryModel->getCountryByCode('GR');

		// Load states for this country
		$stateModel = VmModel::getModel('state');
		$states = $stateModel->getStates($country->virtuemart_country_id);

		// Normalize provided region
		$region = $this->normalizeRegion($region);

		// Return the state
		foreach ($states as $state) {
			if ($state->state_name == $region) {
				return $state;
			}
		}

		vmWarn('Could not map region ' . $region);
	}

	/**
	 * Given a region this function tries to fetch the equivalent state row
	 *
	 * This is the VirtueMart 1.1 version.
	 *
	 * @param $region
	 *
	 * @access private
	 */
	private function findStateVm1($region)
	{
		// Load VirtueMart country model
		//
		// Note: unfortunately there is no state model so we query the DB directly :(
		if (!class_exists('ps_country')) require(CLASSPATH.'ps_country.php');

		// Load countries
		$countryModel = new ps_country();
		// XXX: Hardcoded country
		$country_code = "GRC";
		$country = $countryModel->get_country_by_code($country_code);

		// Load states for this country from the DB
		$db = new ps_DB;
		$q = 'SELECT state_name, state_2_code FROM #__{vm}_state s WHERE s.country_id='.$country->f("country_id");
		$db->query($q);

		// Normalize provided region
		$region = $this->normalizeRegion($region);

		// Return the state
		while ($db->next_record()) {
			if ($db->f("state_name") == $region) {
				// Create an anonymous class
				$state = new stdClass;
				$state->country = $country_code;
				$state->state = $db->f("state_2_code");

				return $state;
			}
		}

	}

	/**
	 * Normalizes a region
	 *
	 * Removes intonation, capitalizes the region
	 * and performs necessary mappings for the region
	 *
	 * @param $region
	 *
	 * @return string
	 * @access private
	 */
	private function normalizeRegion($region)
	{
		$normalizeChars = array( 
			'Ά'=>'Α', 'Έ'=>'Ε', 'Ή'=>'Η', 'Ί'=>'Ι', 'Ϊ'=>'Ι', 'Ό'=>'Ο', 'Ύ'=>'Υ', 'Ώ'=>'Ω',
			'ά'=>'α', 'έ'=>'ε', 'ή'=>'η', 'ί'=>'ι', 'ϊ'=>'ι', 'ΐ'=> 'ι', 'ό'=>'ο', 'ύ'=>'υ', 'ώ'=>'ω'
		);

		// Normalize intonation
		$region = strtr($region, $normalizeChars);

		// Capitalize
		$region = mb_strtoupper($region, "utf-8");

		// Remove custom regions (parentheses)
		//$region = mb_ereg_replace('\s\(.*\)$', '', $region);

		// Substitute 'ΠΕΛΛΑΣ' with 'ΠΕΛΛΗΣ'
		if ($region == 'ΠΕΛΛΑΣ') $region = 'ΠΕΛΛΗΣ';

		return $region;
	}

	/**
	 * Returns the access token for a given code
	 *
	 * @param $params
	 * @param $code
	 *
	 * @access private
	 */
	private function getAccessToken($params, $code)
	{
		$fields = array(
			'code'          => $code,
			'client_id'     => $params->get('client_id'),

			'client_secret' => $params->get('client_secret'),
			'redirect_uri'  => $params->get('redirect_uri') . $this->getCallbackPath(),
			'grant_type'    => 'authorization_code'
		);

		$result = $this->doPostRequest($params->get('site') . $params->get('token_url'), $fields);

		// Decode and return the result
		return json_decode($result)->access_token;
	}

	/**
	 * Returns the address JSON
	 *
	 * @param $params
	 * @param $access_token
	 *
	 * @return mixed
	 * @access private
	 */
	private function getAddress($params, $access_token)
	{
		$query = "?oauth_token=" . urlencode($access_token);

		$result = $this->doGetRequest($params->get('site') . $params->get('address_url') . $query);

		// Decode and return the result

		return json_decode($result);
	}

	/**
	 * Finds a user given an array with a 'username' key
	 *
	 * Returns the user or false in case the user is not found
	 *
	 * @param $data
	 *
	 * @return mixed
	 * @access private
	 */
	private function findUser($data)
	{
		if ($this->isVmVersion("2.0")) {
			return $this->findUserVm2($data);
		} else if ($this->isVmVersion("1.1")) {
			return $this->findUserVm1($data);
		}
	}

	/**
	 * Finds a user given an array with a 'username' key
	 *
	 * This is the VirtueMart 2 version.
	 *
	 * @param $data
	 *
	 * @return mixed
	 * @access private
	 */
	private function findUserVm2($data)
	{
		$currentUser = JFactory::getUser();

		if ($currentUser->id != 0) {
			return $currentUser;
		} else {
			// This doesn't work on emails due to getWord() striping
			// characters besides [A-Aa-z_].
			//JRequest::setVar('search', $data['username'], 'get');

			$this->addModelPath(JPATH_VM_ADMINISTRATOR.DS.'models');
			$userModel = VmModel::getModel('user');

			// This is a done to bypass a bug in getUserList()
			$userModel->_selectedOrdering = 'vmu.virtuemart_user_id';
			$users = $userModel->getUserList();

			//JRequest::setVar('search', null);

			foreach ($users as $user) {
				if ($user->username == $data['username']) {
					return $user;
				}
			}

			return false;
		}
	}

	/**
	 * Finds a user given an array with a 'username' key
	 *
	 * This is the VirtueMart 1.1 version.
	 *
	 * @param $data
	 *
	 * @return mixed
	 * @access private
	 */
	private function findUserVm1($data)
	{
		$currentUser = JFactory::getUser();

		if ($currentUser->id != 0) {
			return $currentUser;
		} else {
			// Get the id of the user from the database
			$userId = JUserHelper::getUserId($data['username']);

			// Return false if not found
			if ($userId == 0) {
				return false;
			}

			$user =& JFactory::getUser();
			$user->load($userId);

			return $user;
		}
	}

	/**
	 * Login a User given an array with a username and a password key / value
	 *
	 * @param $data
	 */
	private function loginUser($data)
	{
		$mainframe =& JFactory::getApplication();

		// Username and password must be passed in an array
		$credentials = array('username' => $data['username'], 'password' => $data['password']);
		$options = array('oauth_login' => true);
		$mainframe->login($credentials, $options);
	}

	/**
	 * Registers a user
	 *
	 * @param $data
	 *
	 * @return string
	 * @access private
	 */
	private function registerUser($data)
	{
		// Add a token to the request to bypass CSRF
		JRequest::setVar(JUtility::getToken(), 1, 'post');

		// Add the name and the email in the request to bypass bugs in VirtueMart
		// administrator/components/com_virtuemart/models/user.php:526
		JRequest::setVar('name', $data['name'], 'post');
		JRequest::setVar('email', $data['email'], 'post');

		return $this->updateUser($data);
	}

	/**
	 * Updates user info
	 *
	 * Returns an array with the result
	 *
	 * @param $data
	 * @return mixed
	 */
	private function updateUser($data)
	{
		if ($this->isVmVersion("2.0")) {
			return $this->updateUserVm2($data);
		} else if ($this->isVmVersion("1.1")) {
			return $this->updateUserVm1($data);
		}
	}

	/**
	 * Updates user info
	 *
	 * This is the VirtueMart 2 version.
	 *
	 * Returns an array with the result
	 *
	 * @param $data
	 * @return mixed
	 */
	private function updateUserVm2($data)
	{
		// Add a token to the request to bypass CSRF
		JRequest::setVar(JUtility::getToken(), 1, 'post');

		$this->addModelPath(JPATH_VM_ADMINISTRATOR.DS.'models');
		$userModel = VmModel::getModel('user');

		// Store the data
		$result = $userModel->store($data);

		// Association the address and the user with the cart
		$this->saveToCart($data);
		return $result;
	}

	/**
	 * Updates user info
	 *
	 * This is the VirtueMart 1.1 version.
	 *
	 * Returns an array with the result
	 *
	 * @param $data
	 * @return mixed
	 */
	private function updateUserVm1($data)
	{
		// Add a token to the request to bypass CSRF
		JRequest::setVar(JUtility::getToken(), 1, 'post');

		// Load VirtueMart shopper model
		if (!class_exists('ps_shopper')) require(CLASSPATH.'ps_shopper.php');
		$shopperModel = new ps_shopper();

		// The shopper model checks only for POST data
		$_POST = array_merge($_POST, $data);

		// Update the shopper
		$result = $shopperModel->update($data);

		if (isset($data['shipping_address_type_name'])) {
			// Load VirtueMart user address model
			if (!class_exists('ps_user_address')) require(CLASSPATH.'ps_user_address.php');
			$userAddressModel = new ps_user_address();

			// Update the address
			$userAddressModel->update($data);
		}

		// Associate the address and the user with the cart
		$this->saveToCart($data);

		return $result;
	}

	/**
	 * Activates a user
	 *
	 * @param $user
	 * @return bool
	 */
	private function activateUser($user)
	{
		jimport('joomla.user.helper');

		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		$userActivation = $usersConfig->get('useractivation');

		if ($userActivation) {
			$user->set('block', '0');
			$user->set('activation', '');

			if (!$user->save()) {
				JError::raiseWarning( "SOME_ERROR_CODE", $user->getError() );
				return false;
			}
		}
	}

	/**
	 * Associates an address with a cart
	 *
	 * @param $data
	 *
	 * @access private
	 */
	private function saveToCart($data){
		if ($this->isVmVersion("2.0")) {
			return $this->saveToCartVm2($data);
		} else if ($this->isVmVersion("1.1")) {
			return $this->saveToCartVm1($data);
		}
	}

	/**
	 * Associates an address with a cart
	 *
	 * This is the VirtueMart 2 version.
	 *
	 * @param $data
	 *
	 * @access private
	 */
	private function saveToCartVm2($data){
		if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DS.'helpers'.DS.'cart.php');
		$cart = VirtueMartCart::getCart();
		$cart->saveAddressInCart($data, $data['address_type']);
	}

	/**
	 * Associates an address with a cart
	 *
	 * This is the VirtueMart 1.1 version.
	 *
	 * @param $data
	 *
	 * @access private
	 */
	private function saveToCartVm1($data){
		// Load VirtueMart cart model
		if (!class_exists('ps_cart')) require(CLASSPATH.'ps_cart.php');
		$cartModel = new ps_cart();
		$cartModel->saveCart();
	}

	/**
	 * Returns the authorization URL
	 *
	 * @param $params
	 *
	 * @return string
	 * @access private
	 */
	private function getAuthorizationUrl($params)
	{
		$site = $params->get('site');
		$authorization_url = $params->get('authorization_url');

		$url = $site . $authorization_url;

		$client_id = urlencode($params->get('client_id'));
		$redirect_uri = urlencode($params->get('redirect_uri') . $this->getCallbackPath());

		return $url . "?client_id=" . $client_id . "&redirect_uri=" . $redirect_uri . "&response_type=code";
	}

	/**
	 * Returns the callback absolute URL
	 *
	 * @access private
	 */
	private function getCallbackPath()
	{
		// Create absolute URL for callback
		// http://docs.joomla.org/JRoute::_/1.5
		// https://groups.google.com/forum/?fromgroups#!topic/joomla-dev-general/u_DhbVorMFY/discussion
		return JRoute::_('index.php?option=com_skroutzeasy&task=callback', true, -1);
	}

	/**
	 * Performs a POST request
	 *
	 * @param $url
	 * @param $fields
	 *
	 * @return mixed
	 * @access private
	 */
	private function doPostRequest($url, $fields)
	{
		// Do the request
		return $this->doRequest($url, true, $fields);
	}

	/**
	 * Performs a GET request
	 *
	 * @param $url
	 *
	 * @return mixed
	 * @access private
	 */
	private function doGetRequest($url)
	{
		// Do the request
		return $this->doRequest($url);
	}

	/**
	 * Performs an HTTP request using CURL
	 *
	 * @param $url
	 * @param $post = false
	 * @param $fields = null
	 *
	 * @return mixed
	 * @access private
	 */
	private function doRequest($url, $post = false, $fields = null)
	{
		// Open connection
		$ch = curl_init();

		if ($post) {
			// Set POST options
			curl_setopt_array($ch, array(
					CURLOPT_URL => $url,
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS => http_build_query($fields),
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_FOLLOWLOCATION => 1,
					CURLOPT_RETURNTRANSFER => 1
				)
			);
		} else {
			// Set GET options
			curl_setopt_array($ch, array(
					CURLOPT_URL => $url,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_RETURNTRANSFER => 1
				)
			);
		}

		// Execute the request
		$result = curl_exec($ch);

		// Close the connection, free up system resources
		curl_close($ch);

		// Error handling
		if (!$result) {
			// TODO
		}

		// Return the result
		return $result;
	}

	private function isVmVersion($version)
	{
		global $VMVERSION, $VIRTUEMART_VERSION;

		if (!class_exists('vmVersion')) {
			require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'version.php');

			if (isset($VMVERSION)) {
				$VIRTUEMART_VERSION = $VMVERSION->RELEASE;
			} else {
				$VMVERSION = new vmVersion();
				$VIRTUEMART_VERSION = $VMVERSION::$RELEASE;
			}
		}

		if (substr($VIRTUEMART_VERSION, 0, 3) == $version) {
			return true;
		} else {
			return false;
		}
	}

	private function loadVm() {
		if ($this->isVmVersion("2.0")) {
			if (!class_exists('VmConfig')) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
		} else if ($this->isVmVersion("1.1")) {
			if (!defined('_VM_PARSER_LOADED')) require(JPATH_BASE.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart_parser.php');
		}
	}
}
