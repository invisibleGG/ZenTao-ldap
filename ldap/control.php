<?php

class ldap extends control
{
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);
    }
    
    // 配置页面
    public function set()
    {
        if($_POST)
        {
            $this->ldap->saveSettings();
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browse')));
        }
        $ldapConfig = $this->config->ldap;
        $this->view->title      = $this->lang->ldap->common . $this->lang->colon . $this->lang->ldap->setting;
        $this->view->ldapConfig = $ldapConfig;
        $this->view->groups = $this->loadModel("group")->getPairs();
        $this->display();
    }
}