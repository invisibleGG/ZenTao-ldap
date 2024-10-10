<?php
$lang->user->expireBizWaring         = "<p style='color:yellow'> 您的軟件授權將在%s天后到期，為避免影響使用，請及時聯繫客戶經理續費。</p>";
$lang->user->expiryReminderToday     = "<p style='color:yellow'> 您的軟件授權將於今天到期，為避免影響使用，請及時聯繫客戶經理續費。</p>";
$lang->user->noticeUserLimit         = "研發用戶數已經達到授權的上限，不能繼續添加用戶！";
$lang->user->noticeFeedbackUserLimit = "運營管理界面用戶數已經達到授權的上限，不能繼續添加用戶！";

$lang->user->userAddWarning     = "研發用戶剩餘授權人數%d人，運營管理界面剩餘授權人數%d人，超過授權人數後新增人員將不會被保存";
$lang->user->rndUserAddWarning  = '研發用戶剩餘授權人數%d人，超過授權人數後新增人員將不會被保存';
$lang->user->liteUserAddWarning = '運營管理界面剩餘授權人數%d人，超過授權人數後新增人員將不會被保存';

if(!isset($lang->dept)) $lang->dept = new stdclass();
$lang->dept->manager = '部門經理';

$lang->user->isFeedback[0] = '研發用戶';
$lang->user->isFeedback[1] = '非研發用戶';
