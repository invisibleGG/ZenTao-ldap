<?php

class myUser extends user
{
    public function login($referer = '', $from = '')
    {
        $this->config->notMd5Pwd = true;
        parent::login($referer = '', $from = '');
    }
}