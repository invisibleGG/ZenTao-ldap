<?php
$lang->user->expireBizWaring         = "<p style='color:yellow'> 您的软件授权将在%s天后到期，为避免影响使用，请及时联系客户经理续费。</p>";
$lang->user->expiryReminderToday     = "<p style='color:yellow'> 您的软件授权将于今天到期，为避免影响使用，请及时联系客户经理续费。</p>";
$lang->user->noticeUserLimit         = "研发用户数已经达到授权的上限，不能继续添加用户！";
$lang->user->noticeFeedbackUserLimit = "运营管理界面用户数已经达到授权的上限，不能继续添加用户！";

$lang->user->userAddWarning     = "研发用户剩余授权人数%d人，运营管理界面剩余授权人数%d人，超过授权人数后新增人员将不会被保存";
$lang->user->rndUserAddWarning  = '研发用户剩余授权人数%d人，超过授权人数后新增人员将不会被保存';
$lang->user->liteUserAddWarning = '运营管理界面剩余授权人数%d人，超过授权人数后新增人员将不会被保存';

if(!isset($lang->dept)) $lang->dept = new stdclass();
$lang->dept->manager = '部门经理';

$lang->user->isFeedback[0] = '研发用户';
$lang->user->isFeedback[1] = '非研发用户';
