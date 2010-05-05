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

    public $auth;

    protected $oauth;
    protected $api_base;

    private $consumer_key;
    private $consumer_secret;

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
    public function __construct($consumer_key,$consumer_secret,$is_production=FALSE,$transport=self::API_TRANSPORT) {

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
            echo 'ERROR: Requested token does not match returned token - check the token details are still valid in the token file'."\n";
            die(__METHOD__);
        }

        // Setup the OAuth instance with this token and token secret
        $this->oauth->setToken($auth['oauth_token'],$auth['oauth_token_secret']);

        // Place this auth data in the publicly acessable $this->auth so others can see that data if required.
        $this->auth = $auth;

        // Return the request token that this authorization is for
        return $auth['oauth_token'];
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
            echo 'ERROR: Developer has not yet called authorize() before making Freelancer API calls'."\n\n";
            die(__METHOD__);
        }

        try {
            $this->oauth->fetch($url,$params);
        } catch (Exception $e) {
            echo 'ERROR: unable to perform oauth\'d fetch from :- '.$url."\n\n";
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

        echo "\n";
        echo 'You MUST visit the URL below to authorize the request token:-'."\n";
        echo str_replace('api.','www.',$this->api_base).'/users/api-token/auth.php?oauth_token='.$request_token."\n\n";
        echo 'Press ENTER when done'."\n";
        $in = fread(STDIN,1);

        try {
            $this->oauth->fetch($call_url,array('oauth_token'=>$request_token));
        } catch (Exception $e) {
            echo 'ERROR: unable to authorize the request token:- '.$request_token."\n\n";
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
            echo 'ERROR: unable to establish an access token with verifier:- '.$oauth_verifier."\n\n";
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
}
?>