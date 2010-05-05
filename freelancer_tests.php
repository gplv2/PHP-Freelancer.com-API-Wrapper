#!/usr/bin/php
<?php
/*******************************************************************************
 * $Id: freelancer_tests.php 727 2010-05-05 11:57:51Z ndejong $
 * Copyright: Nicholas de Jong (me -at- nicholasdejong.com)
 * Web: http://www.nicholasdejong.com
 * License: GPLv3 & Commercial Use
 ******************************************************************************/

include_once('freelancer.php');

// Set the Freelancer API credentials
$credential = getCredentials($argv);
$freelancer = new Freelancer($credential['key'],$credential['secret']);

// We must first make sure we are authorized before we can make any Freelancer API call
$token = $freelancer->authorize();
echo 'Using authorized token: '.$token."\n";

// getUsersBySearch
//$data_getUsersBySearch = $freelancer->getUsersBySearch(array('country_csv'=>'Australia'));
//print_r($data_getUsersBySearch);

// getUserFeedbacks
//$data_getUserFeedbacks = $freelancer->getUserFeedbacks(array('username'=>$data_getUsersBySearch['items'][0]['username']));
//print_r($data_getUserFeedbacks);

// getUserDetails
//$data_getUserDetails = $freelancer->getUserDetails(array('username'=>$data_getUsersBySearch['items'][0]['username']));
//print_r($data_getUserDetails);

// getJobList
//$data_getJobList = $freelancer->getJobList();
//print_r($data_getJobList);

// getAccountDetails
$data_getAccountDetails = $freelancer->getAccountDetails();
print_r($data_getAccountDetails);

// getProfileInfo
//$data_getProfileInfo = $freelancer->getProfileInfo(array('userid'=>$data_getUsersBySearch['items'][0]['userid']));
//print_r($data_getProfileInfo);

// setProfileInfo
//$data_setProfileInfo = $freelancer->setProfileInfo(array('profiletext'=>'hello world'));
//print_r($data_setProfileInfo);

// postNewProject
//$data_postNewProject = $freelancer->postNewProject(array('projectname'=>'Test Project '.time(),'projectdesc'=>'My project description '.time(),'jobtypecsv'=>'YouTube,XXX','budget'=>'100','duration'=>'30'));
//print_r($data_postNewProject);

// postNewTrialProject
//$data_postNewTrialProject = $freelancer->postNewTrialProject(array('projectname'=>'Test Project '.time(),'projectdesc'=>'My project description '.time(),'jobtypecsv'=>'YouTube,XXX','budget'=>'100','duration'=>'30'));
//print_r($data_postNewTrialProject);

// postNewDraftProject
//$data_postNewDraftProject = $freelancer->postNewDraftProject(array('projectname'=>'Test Project '.time(),'projectdesc'=>'My project description '.time(),'jobtypecsv'=>'YouTube,XXX','budget'=>'100','duration'=>'30'));
//print_r($data_postNewDraftProject);

// chooseWinnerForProject
//$data_chooseWinnerForProject = $freelancer->chooseWinnerForProject(array('projectid'=>'1','useridcsv'=>'12345'));
//print_r($data_chooseWinnerForProject);

// getPostedProjectList
//$data_getPostedProjectList = $freelancer->getPostedProjectList(array('status'=>'1'));
//print_r($data_getPostedProjectList);

// inviteUserForProject
//$data_inviteUserForProject = $freelancer->inviteUserForProject(array('projectid'=>'1','useridcsv'=>$data_getUsersBySearch['items'][0]['userid']));
//print_r($data_inviteUserForProject);

// updateProjectDetails
//$data_updateProjectDetails = $freelancer->updateProjectDetails(array('projectid'=>'1','projectdesc'=>'Some new description '.time()));
//print_r($data_updateProjectDetails);

// getProjectListForPlacedBids
//$data_getProjectListForPlacedBids = $freelancer->getProjectListForPlacedBids(array('status'=>'1'));
//print_r($data_getProjectListForPlacedBids);

// placeBidOnProject
//$data_placeBidOnProject = $freelancer->placeBidOnProject(array('amount'=>'100','days'=>'5','description'=>'My bid description','projectid'=>'1','notificationStatus'=>'1'));
//print_r($data_placeBidOnProject);

// retractBidFromProject
//$data_retractBidFromProject = $freelancer->retractBidFromProject(array('projectid'=>'1'));
//print_r($data_retractBidFromProject);

// acceptBidWon
//$data_acceptBidWon = $freelancer->acceptBidWon(array('projectid'=>'1','state'=>'0'));
//print_r($data_acceptBidWon);

// requestCancelProject
//$data_requestCancelProject = $freelancer->requestCancelProject(array('projectid'=>'1','commenttext'=>'Hello world','reasoncancellation'=>'6','followedguidelinesstatus'=>'0'));
//print_r($data_requestCancelProject);

// postFeedback
//$data_postFeedback = $freelancer->postFeedback(array('projectid'=>'1','rating'=>'5','feedbacktext'=>'Some feedback','userid'=>'12345'));
//print_r($data_postFeedback);

// postReplyForFeedback
//$data_postReplyForFeedback = $freelancer->postReplyForFeedback(array('projectid'=>'1','feedbacktext'=>'Some reply feedback','userid'=>'12345'));
//print_r($data_postReplyForFeedback);

// requestWithdrawFeedback
//$data_requestWithdrawFeedback = $freelancer->requestWithdrawFeedback(array('projectid'=>'1','userid'=>'12345'));
//print_r($data_requestWithdrawFeedback);

// getNotification
//$data_getNotification = $freelancer->getNotification();
//print_r($data_getNotification);

// getNews
//$data_getNews = $freelancer->getNews();
//print_r($data_getNews);

// searchProjects
//$data_searchProjects = $freelancer->searchProjects(array('istrial'=>'1'));
//print_r($data_searchProjects);

// getProjectFees
//$data_getProjectFees = $freelancer->getProjectFees();
//print_r($data_getProjectFees);

// getProjectDetails
//$data_getProjectDetails = $freelancer->getProjectDetails(array('projectid'=>'7'));
//print_r($data_getProjectDetails);

// getBidsDetails
//$data_getBidsDetails = $freelancer->getBidsDetails(array('projectid'=>'7'));
//print_r($data_getBidsDetails);

// getPublicMessages
//$data_getPublicMessages = $freelancer->getPublicMessages(array('projectid'=>'5'));
//print_r($data_getPublicMessages);

// postPublicMessage
//$data_postPublicMessage = $freelancer->postPublicMessage(array('projectid'=>'5','messagetext'=>'A test public message'));
//print_r($data_postPublicMessage);

// getInboxMessages
//$data_getInboxMessages = $freelancer->getInboxMessages(array('count'=>'5'));
//print_r($data_getInboxMessages);

// getSentMessages
//$data_getSentMessages = $freelancer->getSentMessages(array('count'=>'5'));
//print_r($data_getSentMessages);

// getUnreadCount
//$data_getUnreadCount = $freelancer->getUnreadCount();
//print_r($data_getUnreadCount);

// sendMessage
//$data_sendMessage = $freelancer->sendMessage(array('projectid'=>'5','messagetext'=>'Message text:'.time(),'username'=>'nicholasdejong'));
//print_r($data_sendMessage);


/**
 * getCredentials()
 *
 * Obtains the Freelancer API credentials either from file or command line args
 */
function getCredentials($argv) {
    
    // If command line args exist try and use them first
    if(!empty($argv[1]) && (strlen($argv[1]) == 40) && !empty($argv[2]) && strlen($argv[2])==40) {
        return array('key'=>$argv[1],'secret'=>$argv[2]);
    }
    else {
        if(file_exists('freelancer_tests_credentials')) {
            $credential = array();
            foreach(explode("\n",file_get_contents('freelancer_tests_credentials')) as $line) {
                $line = str_replace(' ','',$line);
                if(preg_match("/CONSUMER_KEY:(.*?)$/",$line,$matches)) {
                    $credential['key'] = $matches[1];
                }
                if(preg_match("/CONSUMER_SECRET:(.*?)$/",$line,$matches)) {
                    $credential['secret'] = $matches[1];
                }
            }
            return $credential;
        } else {
            usage();
        }
    }
}

/**
 * usage()
 */
function usage() {
    echo "\n";
    echo './freelancer_tests.php <CONSUMER_KEY> <CONSUMER_SECRET>'."\n";
    echo "\n";
}

?>
