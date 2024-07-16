<?php

public function identify($account, $password)
{
    /* If ionCube is not loaded, jump to loader-wizard.php. */
    $user = parent::identify($account, $password);
    
    if($user)
    {
        $user->rights = $this->authorize($account);
        $user->groups = $this->getGroups($account);
        $user->admin  = strpos($this->app->company->admins, ",{$user->account},") !== false;
        $this->session->set('user', $user);

        return $user;
    }
    //exit(var_dump($this->loadModel('ldap')->identify($account, $password, $user)));
    // return $this->loadModel('ldap')->identify($account, $password, $user);
    return $this->loadExtension('ldapauth')->identify($account, $password, $user);
}

public function getLDAPConfig()
{
    return $this->loadExtension('ldapauth')->getLDAPConfig();
}

public function getLDAPUser($type = 'all', $queryID = 0)
{
    return $this->loadExtension('ldapauth')->getLDAPUser($type, $queryID);
}

public function importLDAP($type = 'all', $queryID = 0)
{
    return $this->loadExtension('ldapauth')->importLDAP($type, $queryID);
}

public function getUserWithoutLDAP()
{
    return $this->loadExtension('ldapauth')->getUserWithoutLDAP();
}
