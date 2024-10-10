<?php
$lang->user->expireBizWaring         = "<p style='color:yellow'>Your enterprise license will expire in %s days. Please renew it by contacting your designated account manager.</p>";
$lang->user->expiryReminderToday     = "<p style='color:yellow'>Your enterprise license will expire today. Please renew it by contacting your designated account manager.</p>";
$lang->user->noticeUserLimit         = "Number of Full Feature Interface users has reached the limit of the licensed. No more user can be added now!";
$lang->user->noticeFeedbackUserLimit = "Number of Operation Management Interface users has reached the limit of the licensed. No more user can be added now!";

$lang->user->userAddWarning     = "The left authorized number of Full Feature Interface integrated account is %d, and the left authorized number of Operation Management Interface account is %d. The new members will not be saved after exceeding the authorized number";
$lang->user->rndUserAddWarning  = 'The left authorized number of Full Feature Interface integrated account is %d, and the new members will not be saved after exceeding the authorized number';
$lang->user->liteUserAddWarning = 'The left authorized number of Operation Management Interface account is %d, and the new members will not be saved after exceeding the authorized number';

if(!isset($lang->dept)) $lang->dept = new stdclass();
$lang->dept->manager = 'Manager';

$lang->user->isFeedback[0] = 'Developer User';
$lang->user->isFeedback[1] = 'Feedback User';
