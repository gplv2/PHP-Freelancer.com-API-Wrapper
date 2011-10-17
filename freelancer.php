<?php
/*******************************************************************************
 * $Id: freelancer.php 727 2010-05-05 11:57:51Z ndejong $
 * Copyright: Nicholas de Jong (me -at- nicholasdejong.com)
 * Web: http://www.nicholasdejong.com
 * License: GPLv3 & Commercial Use
 ******************************************************************************/

//
// NOTE: In order to use this class you MUST install the OAuth PHP class first.
//       Unfortunately in Debian/Ubuntu one can't do an `apt get install liboauth-php`
//       since that package does not include the required oauth.so library.
//
//       Seems the only way to obtain oauth.so is via building from the PEAR repo
//       which in turn demands you'll need C++ build tools and CURL development
//       libraries etc etc.
//
//       pecl install oauth-beta
//
//       A right royal ugly mess indeed - anyone got a cleaner workaround?
//

/**
 * @author Nicholas de Jong
 * @copyright Nicholas de Jong
 * @license GPLv3 & Commercial Use
 * */

class Freelancer {

    const API_TRANSPORT         = 'http';
    const API_HOST_PRODUCTION   = 'api.freelancer.com';
    const API_HOST_SANDBOX      = 'api.sandbox.freelancer.com';
    const AUTH_TOKEN_TIMEOUT    = 9999999999;
    const OAUTH_DEBUG           = FALSE;
    const USAGE_STATS           = TRUE;

    protected $oauth;
    protected $api_base;

    private $consumer_key;
    private $consumer_secret;

    private $ch;
    private $eol;

    private $debug=0;
    private $verbose=0;

    /**
     * Create an instance of the Freelancer class like this:-
     * $freelancer = new Freelancer(<consumer_key>,<consumer_secret>);
     *
     * By default this class will use the sandbox, ie api.sandbox.freelancer.com
     *
     * Switching to production is easily done by adding a boolean TRUE as the
     * third class instantiation parameter, ie:-
     * $freelancer = new Freelancer(<consumer_key>,<consumer_secret>,TRUE);
     *
     * @param string $consumer_key
     * @param string $consumer_secret
     * @param bool $is_production (optional)
     * @param string $transport (optional)
     */
    public function __construct($consumer_key, $consumer_secret, $is_production=FALSE,$transport=self::API_TRANSPORT) {
         /* test if we are called from the CLI */
         if (defined('STDIN')) {
            $this->eol="\n";
         } else {
            $this->eol="<BR/>";
         }

        // Assign the API users credentials in a place we can get at them
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;

        // Define the API host base
        if($is_production) {
            $this->api_base = $transport.'://'.self::API_HOST_PRODUCTION;
        } else {
            $this->api_base = $transport.'://'.self::API_HOST_SANDBOX;
        }

        // Define the consumer oauth
        // NOTE: read the oauth comments in upper section of this code if you are having problems
        $this->oauth = new OAuth($consumer_key,$consumer_secret,OAUTH_SIG_METHOD_HMACSHA1,OAUTH_AUTH_TYPE_URI);

        // OAuth debug
        if(self::OAUTH_DEBUG) {
            $this->oauth->enableDebug();
        }

    }

    /***************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************/

    /**
     * getUsersBySearch()
     * 
     * @link http://developer.freelancer.com/GetUsersBySearch
     * @param array $params
     * @return array
     */
    public function getUsersBySearch($params) {
        $call_url = $this->api_base.'/User/getUsersBySearch.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * getUserFeedbacks()
     *
     * @link http://developer.freelancer.com/GetUserFeedbacks
     * @param array $params
     * @return array
     */
    public function getUserFeedbacks($params) {
        $call_url = $this->api_base.'/User/getUserFeedbacks.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['user'])) {
            return $data['user'];
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * getUserDetails()
     *
     * @link http://developer.freelancer.com/GetUserDetails
     * @return array
     */
    public function getUserDetails($params) {
        $call_url = $this->api_base.'/User/getUserDetails.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['user'])) {
            return $data['user'];
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * getJobList()
     *
     * @link http://developer.freelancer.com/GetJobList
     * @return array
     */
    public function getJobList() {
        $call_url = $this->api_base.'/Job/getJobList.json';

        $data = $this->oFetch($call_url);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * getAccountDetails()
     *
     * @link http://developer.freelancer.com/GetAccountDetails
     * @return array
     */
    public function getAccountDetails() {
        $call_url = $this->api_base.'/Profile/getAccountDetails.json';

        $data = $this->oFetch($call_url);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * getProfileInfo()
     *
     * @link http://developer.freelancer.com/GetProfileInfo
     * @param array $params
     * @return array
     */
    public function getProfileInfo($params) {
        $call_url = $this->api_base.'/Profile/getProfileInfo.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * setProfileInfo()
     *
     * @link http://developer.freelancer.com/SetProfileInfo
     * @param array $params
     * @return mixed
     */
    public function setProfileInfo($params) {
        $call_url = $this->api_base.'/Profile/setProfileInfo.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['statusconfirmation']) && '1'==$data['statusconfirmation']) {
            return TRUE;
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * postNewProject()
     *
     * @link http://developer.freelancer.com/PostNewProject
     * @param array $params
     * @return mixed
     */
    public function postNewProject($params) {
        $call_url = $this->api_base.'/Employer/postNewProject.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * postNewTrialProject()
     *
     * @link http://developer.freelancer.com/PostNewTrialProject
     * @param array $params
     * @return array
     */
    public function postNewTrialProject($params) {
        $call_url = $this->api_base.'/Employer/postNewTrialProject.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * postNewDraftProject()
     *
     * @link http://developer.freelancer.com/PostNewDraftProject
     * @param array $params
     * @return array
     */
    public function postNewDraftProject($params) {
        $call_url = $this->api_base.'/Employer/postNewDraftProject.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * chooseWinnerForProject()
     *
     * @link http://developer.freelancer.com/ChooseWinnerForProject
     * @param array $params
     * @return array
     */
    public function chooseWinnerForProject($params) {
        $call_url = $this->api_base.'/Employer/chooseWinnerForProject.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];   // TODO: confirm this response is an xml-result style
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * getPostedProjectList()
     *
     * @link http://developer.freelancer.com/GetPostedProjectList
     * @param array $params
     * @return array
     */
    public function getPostedProjectList($params) {
        $call_url = $this->api_base.'/Employer/getPostedProjectList.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * inviteUserForProject()
     *
     * @link http://developer.freelancer.com/InviteUserForProject
     * @param array $params
     * @return array
     */
    public function inviteUserForProject($params) {
        $call_url = $this->api_base.'/Employer/inviteUserForProject.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];   // TODO: confirm this response is an xml-result style
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * updateProjectDetails()
     *
     * @link http://developer.freelancer.com/UpdateProjectDetails
     * @param array $params
     * @return array
     */
    public function updateProjectDetails($params) {
        $call_url = $this->api_base.'/Employer/updateProjectDetails.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];   // TODO: confirm this response is an xml-result style
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * getProjectListForPlacedBids()
     *
     * @link http://developer.freelancer.com/GetProjectListForPlacedBids
     * @param array $params
     * @return array
     */
    public function getProjectListForPlacedBids($params) {
        $call_url = $this->api_base.'/Freelancer/getProjectListForPlacedBids.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * placeBidOnProject()
     *
     * @link http://developer.freelancer.com/PlaceBidOnProject
     * @param array $params
     * @return array
     */
    public function placeBidOnProject($params) {
        $call_url = $this->api_base.'/Freelancer/placeBidOnProject.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];   // TODO: confirm this response is an xml-result style
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * retractBidFromProject()
     *
     * @link http://developer.freelancer.com/RetractBidFromProject
     * @param array $params
     * @return array
     */
    public function retractBidFromProject($params) {
        $call_url = $this->api_base.'/Freelancer/retractBidFromProject.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];   // TODO: confirm this response is an xml-result style
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * acceptBidWon()
     *
     * @link http://developer.freelancer.com/AcceptBidWon
     * @param array $params
     * @return array
     */
    public function acceptBidWon($params) {
        $call_url = $this->api_base.'/Freelancer/acceptBidWon.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];   // TODO: confirm this response is an xml-result style
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * requestCancelProject()
     *
     * @link http://developer.freelancer.com/RequestCancelProject
     * @param array $params
     * @return array
     */
    public function requestCancelProject($params) {
        $call_url = $this->api_base.'/Common/requestCancelProject.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['statusconfirmation']) && '1'==$data['statusconfirmation']) {
            return TRUE;
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * postFeedback()
     *
     * @link http://developer.freelancer.com/PostFeedback
     * @param array $params
     * @return array
     */
    public function postFeedback($params) {
        $call_url = $this->api_base.'/Common/postFeedback.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];   // TODO: confirm this response is an xml-result style
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * postReplyForFeedback()
     *
     * @link http://developer.freelancer.com/PostReplyForFeedback
     * @param array $params
     * @return array
     */
    public function postReplyForFeedback($params) {
        $call_url = $this->api_base.'/Common/postReplyForFeedback.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];   // TODO: confirm this response is an xml-result style
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * requestWithdrawFeedback()
     *
     * @link http://developer.freelancer.com/RequestWithdrawFeedback
     * @param array $params
     * @return array
     */
    public function requestWithdrawFeedback($params) {
        $call_url = $this->api_base.'/Common/requestWithdrawFeedback.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];   // TODO: confirm this response is an xml-result style
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * PAYMENT METHODS YET TO BE WRITTEN !!
     */

    /**
     * getNotification()
     *
     * @link http://developer.freelancer.com/GetNotification
     * @return array
     */
    public function getNotification() {
        $call_url = $this->api_base.'/Notification/getNotification.json';

        $data = $this->oFetch($call_url);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];
        } else {
            return $data; // ie return error data
        }
    }
    
    /**
     * getNews()
     *
     * @link http://developer.freelancer.com/GetNews
     * @return array
     */
    public function getNews() {
        $call_url = $this->api_base.'/Notification/getNews.json';

        $data = $this->oFetch($call_url);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * searchProjects()
     *
     * @link http://developer.freelancer.com/SearchProjects
     * @param array $params
     * @return array
     */
    public function searchProjects($params) {
        $call_url = $this->api_base.'/Project/searchProjects.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * getProjectFees()
     *
     * @link http://developer.freelancer.com/GetProjectFees
     * @return array
     */
    public function getProjectFees() {
        $call_url = $this->api_base.'/Project/getProjectFees.json';

        $data = $this->oFetch($call_url);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * getProjectDetails()
     *
     * @link http://developer.freelancer.com/GetProjectDetails
     * @param array $params
     * @return array
     */
    public function getProjectDetails($params) {
        $call_url = $this->api_base.'/Project/getProjectDetails.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * getBidsDetails()
     *
     * @link http://developer.freelancer.com/GetBidsDetails
     * @param array $params
     * @return array
     */
    public function getBidsDetails($params) {
        $call_url = $this->api_base.'/Project/getBidsDetails.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];   // TODO: confirm this response is an xml-result style
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * getPublicMessages()
     *
     * @link http://developer.freelancer.com/GetPublicMessages
     * @param array $params
     * @return array
     */
    public function getPublicMessages($params) {
        $call_url = $this->api_base.'/Project/getPublicMessages.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];   // TODO: confirm this response is an xml-result style
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * postPublicMessage()
     *
     * @link http://developer.freelancer.com/PostPublicMessage
     * @param array $params
     * @return array
     */
    public function postPublicMessage($params) {
        $call_url = $this->api_base.'/Project/postPublicMessage.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['statusconfirmation']) && '1'==$data['statusconfirmation']) {
            return TRUE;
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * getInboxMessages()
     *
     * @link http://developer.freelancer.com/GetInboxMessages
     * @param array $params
     * @return array
     */
    public function getInboxMessages($params) {
        $call_url = $this->api_base.'/Message/getInboxMessages.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];   // TODO: confirm this response is an xml-result style
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * getSentMessages()
     *
     * @link http://developer.freelancer.com/GetSentMessages
     * @param array $params
     * @return array
     */
    public function getSentMessages($params) {
        $call_url = $this->api_base.'/Message/getSentMessages.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result'])) {
            return $data['xml-result'];   // TODO: confirm this response is an xml-result style
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * getUnreadCount()
     *
     * @link http://developer.freelancer.com/GetUnreadCount
     * @return array
     */
    public function getUnreadCount() {
        $call_url = $this->api_base.'/Message/getUnreadCount.json';

        $data = $this->oFetch($call_url);

        if(isset($data['xml-result']['items']['unreadcount'])) {
            return $data['xml-result']['items']['unreadcount'];
        } else {
            return $data; // ie return error data
        }
    }

    /**
     * sendMessage()
     *
     * @link http://developer.freelancer.com/SendMessage
     * @param array $params
     * @return array
     */
    public function sendMessage($params) {
        $call_url = $this->api_base.'/Message/sendMessage.json';

        $data = $this->oFetch($call_url,$params);

        if(isset($data['xml-result']['statusconfirmation']) && '1'==$data['xml-result']['statusconfirmation']) {
            return TRUE;
        } else {
            return $data; // ie return error data
        }
    }


     /**
      * authorize()
      *
      * @param string $request_token (optional)
      * @param string $token_filename (optional)
      * @return string
      */
    public function authorize($request_token=null,$token_filename=null) {
        $this->debug(__METHOD__, "call",5);

        $auth = array();

        // Name the auth token data filename
        if(is_null($token_filename)) {
            $token_filename = sys_get_temp_dir().'/freelancer_api_'.md5($this->consumer_key.$this->consumer_secret.$this->api_base.$request_token).'.token';
        }

        // Check if the cache file exists
        if(file_exists($token_filename)) {
            $auth = json_decode(file_get_contents($token_filename),TRUE);
        }

        // Check if the token data file data has timed out
        if(!isset($auth['timestamp']) or (time() - $auth['timestamp'] > self::AUTH_TOKEN_TIMEOUT)) {

            $auth['timestamp'] = time();
            $auth['request_token'] = $this->requestRequestToken();
            $auth['oauth_verifier'] = $this->getRequestTokenVerifier($auth['request_token']);
            $auth = array_merge($auth,$this->requestAccessToken($auth['oauth_verifier'],$auth['request_token']));
            file_put_contents($token_filename,json_encode($auth));

            $this->usageStats();
        }

        // If $request_token is not null make sure it matches $auth['oauth_token']
        if(!is_null($request_token) && $request_token != $auth['oauth_token']) {
            $this->debug( __METHOD__, "simple", 0, sprintf("Requested token does not match returned token - check the token details are still valid in the token file"));
            die(__METHOD__);
        }

        // Setup the OAuth instance with this token and token secret
        $this->oauth->setToken($auth['oauth_token'],$auth['oauth_token_secret']);

        // Place this auth data in the publicly acessable $this->auth so others can see that data if required.
        $this->auth = $auth;

        // Return the request token that this authorization is for
        return $auth['oauth_token'];
        $this->debug(__METHOD__, "hangup",5);
    }

    /***************************************************************************
     * PROTECTED FUNCTIONS
     **************************************************************************/

    /**
     * oFetch()
     *
     * @param string $url
     * @param array $params
     * @return array
     */
    protected function oFetch($url,$params=array()) {

        // Check that an authorization call has been made before we get here
        if(!isset($this->auth['timestamp'])) {
            $this->debug( __METHOD__, "simple", 0, sprintf("Failed to call authorize() before making Freelancer API calls."));
            die(__METHOD__);
        }

        try {
            $this->oauth->fetch($url,$params);
        } catch (Exception $e) {
            $this->debug( __METHOD__, "simple", 0, sprintf("Unable to perform oauth'd fetch from : - %s",$url));
            die(__METHOD__);
        }

        return json_decode($this->oauth->getLastResponse(),true);
    }

    /**
     * requestRequestToken()
     *
     * @return string
     */
    protected function requestRequestToken() {
        $call_url = $this->api_base.'/RequestRequestToken/requestRequestToken.json';
        
        $request_token = array();

        try {
            $request_token = $this->oauth->getRequestToken($call_url);
        } catch (Exception $e) {
            print_r($this->oauth->getLastResponseInfo());
            die(__METHOD__);
        }

        return $request_token['oauth_token'];
    }

    /**
     * getRequestTokenVerifier()
     * 
     * @param string $consumer_key
     * @return string
     */
    protected function getRequestTokenVerifier($request_token) {
        $call_url = $this->api_base.'/RequestAccessToken/getRequestTokenVerifier.json';

        // echo 'You MUST visit the URL below to authorize the request token:-'."\n";
        // echo str_replace('api.','www.',$this->api_base).'/users/api-token/auth.php?oauth_token='.$request_token."\n\n";
        // echo 'Press ENTER when done'."\n";
        // $in = fread(STDIN,1);

        $token_url = str_replace('api.','www.',$this->api_base).'/users/api-token/auth.php?oauth_token='.$request_token."\n\n";
        $this->debug( __METHOD__, "simple", 3, sprintf("Fetching : %s",$request_token));
        $this->ch = curl_init($token_url);

        // set user agent
        $useragent="PHP_Curl_freelance_oauth";
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array('HTTP_ACCEPT_LANGUAGE: UTF-8')); // request utf8 here

        $server_output = curl_exec($this->ch);
        $curlinfo = curl_getinfo($this->ch);

        curl_close($this->ch);

        // In case you want to post stuff
        // $post_arr = array('blah' => $this_bar , 'booh' => $this_foo, 'time' => time(),'pid' => posix_getpid());
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $post_arr);

        try {
            $this->oauth->fetch($call_url,array('oauth_token'=>$request_token));
        } catch (Exception $e) {
            $this->debug( __METHOD__, "simple", 0, sprintf("Unable to authorize the request token:- %s",$request_token));
            die(__METHOD__);
        }

        // verifier=be42e092fa6c0968066dbccce39f60c3c734509
        if(preg_match("/verifier=(.*?)$/",$this->oauth->getLastResponse(),$matches)) {
            return $matches[1];
        } else {
            return FALSE;
        }
    }

    /**
     * requestAccessToken()
     * 
     * @param string $oauth_verifier
     * @return array
     */
    protected function requestAccessToken($oauth_verifier,$request_token) {
        $call_url = $this->api_base.'/RequestAccessToken/requestAccessToken.json';

        try {
            $this->oauth->fetch($call_url,array('oauth_verifier'=>$oauth_verifier,'oauth_token'=>$request_token));
        } catch (Exception $e) {
            $this->debug( __METHOD__, "simple", 0, sprintf("Unable to establish an access token with verifier :- %s", $oauth_verifier));
            die(__METHOD__);
        }

        // oauth_token=385ee1dc46e86c6b43b8b84f66d5d91172234c29&oauth_token_secret=97a56ef5ce212414ff5598cc907f12eca876c908
        if(preg_match("/oauth_token=(.*?)&oauth_token_secret=(.*?)$/",$this->oauth->getLastResponse(),$matches)) {
            return array('oauth_token'=>$matches[1],'oauth_token_secret'=>$matches[2]);
        } else {
            return FALSE;
        }
    }

    /**
     * usageStats()
     *
     * Provides anonymous usage stats
     */
    private function usageStats() {
        if(self::USAGE_STATS) {
            try {
                $er = error_reporting(); error_reporting(0);
                file_get_contents('http://stats.nicholasdejong.com/usage.php?t=class&n='.__CLASS__,FALSE,stream_context_create(array('http'=>array('timeout'=>2))));
                error_reporting($er);
            } catch (Exception $e) { }
        }
    }

   /**
    * \fn function setVerbose($level)
    * \brief switches the verbosity level
    * \param $level: Level to switch to
    */
   public function setVerbose($level) {
      $this->debug(__METHOD__, "call",2);
      if(!isset($level)) { return $this ;}

      // $args=func_get_args();
      // $this->debug(__METHOD__, "action" , 5, "Arguments: " . print_r($args, true));
      if ($level>=0) {
         $this->verbose = sprintf("%d",$level);
         // $this->debug(__METHOD__, "info",1, "Switched to verbose level " . $this->verbose);
      } else {
         $this->verbose = 0;
      }
      return $this;
   }

   /**
    * \fn function setDebug($state = 1/0)
    * \brief Enables or disables the internal debugger
    * \param $state: Enable or disable the debugger
    *
    * This function enables or disables the extended debug information
    * on screen. Dumps the called function and their arguments, and also
    * their internal calls.
    */
   public function setDebug($state = 1) {
      $this->debug(__METHOD__, "call",5);
      if(!isset($state)) { return $this ;}

      // $this->debug(__METHOD__, "action" , 4, print_r($args, true));
      $args=func_get_args();
      if ($state > 0) {
         $this->debug = 1;
      } else {
         $this->debug = 0;
      }

      $this->debug(__METHOD__, "hangup",5);
      return $this;
   }

   public function debug($func, $type="simple", $level, $message = "", $pad_me = 0) {
      /* If the debugger is disabled, retuns without doing anything */
      if (!$this->debug or !isset($func) or !isset($level)) {
         return 0;
      }

      $pre="";

      if (strlen($func)==0) {
         $func="Main";
      }

      // Some make-up (type can be stdout)
      switch($type){
         case "call":
            $message=""; 
         $pre = sprintf("[%s()] - Called", $func);
         break;
         case "hangup":
            $message=""; 
         $pre = sprintf("[%s()] - Done", $func);
         break;
         case "simple":
            $pre = sprintf("[%s()]", $func);
         break;
         default :
            $pre = sprintf("[%s()]", $func);
         break;
      }

      $DateTime=@date('Y-m-d H:i:s', time());

      if ( $level <= $this->verbose ) {
         $mylvl=NULL;
         switch($level) {
            case 0:   $mylvl ="error"; break;
            case 1:   $mylvl ="core "; break;
            case 2:   $mylvl ="info "; break;
            case 3:   $mylvl ="notic"; break;
            case 4:   $mylvl ="verbs"; break;
            case 5:   $mylvl ="dtail"; break;
            case 6:   $mylvl ="trace"; break;
            default : $mylvl = $level ; break;
         }

         $nested=0;
         if (is_array($message)) {
            $pad_length=0;
            foreach ($message as $key=>$val) {
               if(!is_array($val)){
                  $pad_length = (strlen($key) >= $pad_length) ? strlen($key) : $pad_length;
               } else {
                  $nested=0;
               }
            }

            foreach ($message as $key=>$val) {
               if (!is_array($val)) {
                  /* does the array needs some padding */
                  if($pad_me == 1) {
                     $padded_eq=str_pad($key, $pad_length, ' ' ,STR_PAD_RIGHT);
                     $key_val = sprintf("%s = %s",$padded_eq, $val);
                  } elseif ($pad_me == 2) {
                     $padded_key=str_pad($key, $pad_length, ' ' ,STR_PAD_LEFT);
                     $key_val = sprintf("%s = %s",$padded_key,$val);
                  } else {
                     $key_val = sprintf("%s = %s",$key,$val);
                  }

                  $content = sprintf("%s:[%s]- %s %s%s", $DateTime, $mylvl, $pre, $key_val , $this->eol);
                  if ($type == "stderr") {
                     // or see http://dren.ch/php-print-to-stderr/ and try this below when this doesn't work for YOUR php version
                     // $STDERR = fopen('php://stderr', 'w+');
                     fwrite(STDERR, $content); 
                  } else {
                     fwrite(STDOUT, $content); 
                  }
               }
            }
         } else {
            $lines = explode("\n", trim($message));
            $no_lines = count($lines);

            /*
               if ($no_lines==0) {
               fwrite(STDERR, "\n"); 
               }
             */

            foreach ($lines as $line) {
               $content = sprintf("%s:[%s]- %s %s%s", $DateTime, $mylvl, $pre, $line , $this->eol);
               /* Finally we dump this to stderr or the stdout */
               if ($type == "stderr") {
                  fwrite(STDERR, $content); 
               } else {
                  fwrite(STDOUT, $content); 
               }
            }
         }
      }
      return 1;
   }
}
?>
