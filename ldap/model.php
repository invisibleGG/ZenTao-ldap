<?php

class ldapModel extends model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function saveSettings()
    {
        $settings = fixer::input("post")->get();
        if (isset($settings->anonymous)) {
            $settings->admin = "";
            $settings->password = "";
        }
        foreach (explode(",", $this->config->ldap->set->requiredFields) as $requiredField) {
            if (!(isset($settings->anonymous) && $requiredField == "admin")) {
                if (empty($settings->{$requiredField})) {
                    dao::$errors[$requiredField] = sprintf($this->lang->ldap->error->noempty, $this->lang->ldap->{$requiredField});
                    return false;
                }
            }
        }
        
        // 如果开启需要检测连接状态
        if ($settings->turnon) {
            $ldapConn = $this->ldapConnect($settings);
            // 检测连接是否成功
            if (!$ldapConn) {
                dao::$errors[] = $this->lang->ldap->error->connect;
                return false;
            }
            
        }
        
        // 保存配置信息
        $this->loadModel('setting')->setItems('system.ldap', $settings);
        if(dao::isError()) return false;
        return true;
    }
    
    public function ldapConnect($settings)
    {
        try {
            $host = $settings->host;
            $port = $settings->port;
            $user = $settings->admin;
            $password = $settings->password;
            $version = $settings->version;
            $referrals = 0;
    
            $conn = ldap_connect($host, $port); //不要写成ldap_connect($host.':'.$port)的形式
            if ($conn) {
                //设置参数
                ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, $version); //声明使用版本3
                ldap_set_option($conn, LDAP_OPT_REFERRALS, $referrals); // Binding to ldap server
                
                if (isset($settings->anonymous)) {
                    $ldapBind = ldap_bind($conn);
                } else {
                    $ldapPassword = html_entity_decode(helper::decryptPassword($password));
                    $ldapBind = ldap_bind($conn, $user, $ldapPassword);
                }
                return  $conn;
            } else {
                return  false;
            }
        } catch (Exception $e) {
            dao::$errors[] = $this->lang->ldap->error->connect. $e->getMessage();
            return false;
        }
    }
    
    
    
    
}