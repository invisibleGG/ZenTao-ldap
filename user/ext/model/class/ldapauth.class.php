<?php

class ldapauthUser extends userModel
{
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);
    }
    
    public function identify($account, $password, $user = NULL)
    {
        # 判断输入账户
        if (empty($account)) {
            return $user;
        }

        $isLink = false;
        if (empty($user)) {
            $ldap = $this->dao->select("account")->from(TABLE_USER)->where("ldap")->eq($account)->fetch("account");
            if (!empty($ldap)) {
                $isLink = true;
                $user = parent::identify($ldap, $password);
            }
        }
        
        if (empty($password)) {
            return $user;
        }
        // exit(var_dump(123));
        $ldapConfig = $this->getLDAPConfig();
        if (empty($ldapConfig)) {
            return $user;
        }
        if (empty($ldapConfig->turnon)) {
            return $user;
        }
        // 使用配置连接ldap服务器
        $ldapConn = $this->loadModel('ldap')->ldapConnect($ldapConfig);
        
        // 验证
        $ldap_user = $this->getLDAPUser($account, $password);
        // exit(var_dump($ldap_user[$ldapConfig->realname][0]));
        if($ldap_user){
            // ldap校验账号密码成功
            if(!$user){
                // 创建账户再返回相关信息
                $userInfo = [
                    "account"   => $account,
                    "password"  => $password,
                    "dept"      => $ldapConfig->group,
                    "realname"  => $ldap_user[$ldapConfig->realname][0],
                    "email"     => $ldap_user[$ldapConfig->email][0],
                    "mobile"    => $ldap_user[$ldapConfig->mobile][0],
                    "phone"     => $ldap_user[$ldapConfig->phone][0],
                ];
                $uid = $this->createUser($userInfo);
                if($uid){
                    // 创建成功
                    return $this->loadModel('user')->getById($uid);
                }
            }
            return $user;
        }
        return false;
        
    }
    
    // 获取系统中的LDAP配置信息
    public function getLDAPConfig()
    {
        $ldap = $this->loadModel('ldap');
        return $this->config->ldap;
    }
    
    public function getLDAPUser($account, $password)
    {
        $user_info = []; // 用户信息
        
        // 1.获取员工
        try {
            $ldapConfig = $this->getLDAPConfig();
            $uidFiled = $ldapConfig->account;
            $baseDn = $ldapConfig->baseDN;
            
            $user_info = []; // 用户信息
    
            // 连接ldap
            $conn = $this->loadModel('ldap')->ldapConnect($ldapConfig);

            // $dn = "$uidFiled=$account,$baseDn";
    
            // ===========读取===========
            $search_filter = "($uidFiled=$account)"; //设置uid过滤  
            // $justthese = array('dn', 'o'); //设置输出属性 , 不传查询所有

            $search_id = ldap_search($conn, $baseDn, $search_filter);
            $res = ldap_get_entries($conn, $search_id); //结果
            
            if (!$res[0]) {
                return false;
            }
            
            $userDN = $res[0]["name"][0];
  
            $ldapBindUser = ldap_bind($conn, $userDN, $password);

            if($ldapBindUser){
                $user_info = $res["0"];
                ldap_unbind($conn);
            }else{
                return false;
            }
            
            ldap_close($conn);
        } catch (\Throwable $th) {
            ldap_close($conn);
        }
    
        return $user_info;
    }
    
    public function createUser($userInfo)
    {

        $user = fixer::input('post')
            ->setDefault('join', '0000-00-00')
            ->setDefault('type', 'inside')
            ->setDefault('company', 0)
            ->setDefault('visions', 'rnd')
            ->setDefault('account', $userInfo['account'])
            ->setDefault('password', $userInfo['password'])
            ->setDefault('dept', $userInfo['dept'])
            ->setDefault('realname', $userInfo['realname'])
            ->setDefault('ldap', $userInfo['account'])
            ->setIF(!empty($userInfo['email']), 'email', $userInfo['email'])
            ->setIF(!empty($userInfo['mobile']), 'mobile', $userInfo['mobile'])
            ->setIF(!empty($userInfo['phone']), 'phone', $userInfo['phone'])
            // ->setDefault('role', $role)
            // ->setDefault('commiter', $commiter)
            ->setDefault('gender', "f")
            ->join('visions', ',')
            ->remove('new, actor, referer, verifyRand, keepLogin, captcha, group, verifyPassword, passwordStrength, passwordLength')
            ->get();
            $user->password = md5($user->password);
// exit(var_dump($user));
        $status = $this->dao->insert(TABLE_USER)->data($user)
            ->autoCheck()
            ->batchCheck($this->config->user->create->requiredFields, 'notempty')
            ->check('saccount', 'unique')
            ->check('account', 'account')
            ->checkIF(!empty($userInfo['email']), 'email', 'email')
            ->exec();
// exit(var_dump($status));
        if(!dao::isError())
        {
            $userID = $this->dao->lastInsertID();

            $this->computeUserView($user->account);
            // $this->loadModel('action')->create('user', $userID, 'Created');
            $this->loadModel('mail');
            if($this->config->mail->mta == 'sendcloud' and !empty($user->email)) $this->mail->syncSendCloud('sync', $user->email, $user->realname);

            return $userID;
        }else{
            // exit(var_dump(dao::getError()));
            return false;
        }
    }
}