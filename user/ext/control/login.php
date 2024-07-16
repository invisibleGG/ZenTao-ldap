<?php

class myUser extends user
{
    public function login($referer = '', $from = '')
    {
        /* Check if you can operating on the folder. */
        $canModifyDIR = true;
        if($this->user->checkTmp() === false)
        {
            $canModifyDIR = false;
            $floderPath   = $this->app->tmpRoot;
        }
        elseif(!is_dir($this->app->dataRoot) or substr(base_convert(@fileperms($this->app->dataRoot), 10, 8), -4) != '0777')
        {
            $canModifyDIR = false;
            $floderPath   = $this->app->dataRoot;
        }

        if(!$canModifyDIR)
        {
            if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            {
                return print(sprintf($this->lang->user->mkdirWin, $floderPath, $floderPath));
            }
            else
            {
                return print(sprintf($this->lang->user->mkdirLinux, $floderPath, $floderPath, $floderPath, $floderPath));
            }
        }

        $this->setReferer($referer);

        $loginLink = $this->createLink('user', 'login');
        $denyLink  = $this->createLink('user', 'deny');

        /* Reload lang by lang of get when viewType is json. */
        if($this->app->getViewType() == 'json' and $this->get->lang and $this->get->lang != $this->app->getClientLang())
        {
            $this->app->setClientLang($this->get->lang);
            $this->app->loadLang('user');
        }

        /* If user is logon, back to the rerferer. */
        if($this->user->isLogon())
        {
            if($this->app->getViewType() == 'json')
            {
                $data = $this->user->getDataInJSON($this->app->user);
                return print(helper::removeUTF8Bom(json_encode(array('status' => 'success') + $data)));
            }

            $response['result'] = 'success';
            if(strpos($this->referer, $loginLink) === false and
               strpos($this->referer, $denyLink)  === false and
               strpos($this->referer, 'ajax') === false and
               strpos($this->referer, 'block')  === false and $this->referer
            )
            {
                $response['locate'] = $this->referer;
                if(helper::isWithTID() and strpos($response['locate'], 'tid=') === false) $response['locate'] .= (strpos($response['locate'], '?') === false ? '?' : '&') . "tid={$this->get->tid}";
                return $this->send($response);
            }
            else
            {
                $response['locate'] = $this->config->webRoot . (helper::isWithTID() ? "?tid={$this->get->tid}" : '');
                return $this->send($response);
            }
        }

        /* Passed account and password by post or get. */
        if(!empty($_POST) or (isset($_GET['account']) and isset($_GET['password'])))
        {
            $account  = '';
            $password = '';
            if($this->post->account)  $account  = $this->post->account;
            if($this->get->account)   $account  = $this->get->account;
            if($this->post->password) $password = $this->post->password;
            if($this->get->password)  $password = $this->get->password;

            $account = trim($account);
            if($this->user->checkLocked($account))
            {
                $response['result']  = 'fail';
                $response['message'] = sprintf($this->lang->user->loginLocked, $this->config->user->lockMinutes);
                if($this->app->getViewType() == 'json') return print(helper::removeUTF8Bom(json_encode(array('status' => 'failed', 'reason' => $response['message']))));
                return $this->send($response);
            }

            if((!empty($this->config->safe->loginCaptcha) and strtolower($this->post->captcha) != strtolower($this->session->captcha) and $this->app->getViewType() != 'json'))
            {
                $response['result']  = 'fail';
                $response['message'] = $this->lang->user->errorCaptcha;
                return $this->send($response);
            }

            $user = $this->user->identify($account, $password);

            if($user)
            {
                /* Set user group, rights, view and aword login score. */
                $user = $this->user->login($user);

                /* Go to the referer. */
                if($this->post->referer and strpos($this->post->referer, $loginLink) === false and strpos($this->post->referer, $denyLink) === false and strpos($this->post->referer, 'block') === false)
                {
                    if($this->app->getViewType() == 'json')
                    {
                        $data = $this->user->getDataInJSON($user);
                        return print(helper::removeUTF8Bom(json_encode(array('status' => 'success') + $data)));
                    }

                    /* Get the module and method of the referer. */
                    $module = $this->config->default->module;
                    $method = $this->config->default->method;
                    if($this->config->requestType == 'PATH_INFO')
                    {
                        $requestFix = $this->config->requestFix;

                        $path = substr($this->post->referer, strrpos($this->post->referer, '/') + 1);
                        $path = rtrim($path, '.html');
                        if($path and strpos($path, $requestFix) !== false) list($module, $method) = explode($requestFix, $path);
                    }
                    else
                    {
                        $url   = html_entity_decode($this->post->referer);
                        $param = substr($url, strrpos($url, '?') + 1);

                        if(strpos($param, '&') !== false) list($module, $method) = explode('&', $param);
                        $module = str_replace('m=', '', $module);
                        $method = str_replace('f=', '', $method);
                    }

                    /* Check parsed name of module and method from referer. */
                    if(empty($module) or !$this->app->checkModuleName($module, $exit = false) or
                       empty($method) or !$this->app->checkMethodName($module, $exit = false))
                    {
                        $module = $this->config->default->module;
                        $method = $this->config->default->method;
                    }

                    $response['result']  = 'success';
                    if(common::hasPriv($module, $method))
                    {
                        $response['locate'] = $this->post->referer;
                        if(helper::isWithTID() and strpos($response['locate'], 'tid=') === false) $response['locate'] .= (strpos($response['locate'], '?') === false ? '?' : '&') . "tid={$this->get->tid}";
                        return $this->send($response);
                    }
                    else
                    {
                        $response['locate'] = $this->config->webRoot . (helper::isWithTID() ? "?tid={$this->get->tid}" : '');
                        return $this->send($response);
                    }
                }
                else
                {
                    if($this->app->getViewType() == 'json')
                    {
                        $data = $this->user->getDataInJSON($user);
                        return print(helper::removeUTF8Bom(json_encode(array('status' => 'success') + $data)));
                    }

                    $response['locate']  = $this->config->webRoot . (helper::isWithTID() ? "?tid={$this->get->tid}" : '');
                    $response['result']  = 'success';
                    return $this->send($response);
                }
            }
            else
            {
                $response['result']  = 'fail';
                $fails = $this->user->failPlus($account);
                if($this->app->getViewType() == 'json') return print(helper::removeUTF8Bom(json_encode(array('status' => 'failed', 'reason' => $this->lang->user->loginFailed))));
                $remainTimes = $this->config->user->failTimes - $fails;
                if($remainTimes <= 0)
                {
                    $response['message'] = sprintf($this->lang->user->loginLocked, $this->config->user->lockMinutes);
                    return $this->send($response);
                }
                else if($remainTimes <= 3)
                {
                    $response['message'] = sprintf($this->lang->user->lockWarning, $remainTimes);
                    return $this->send($response);
                }

                $response['message'] = $this->lang->user->loginFailed;
                if(dao::isError()) $response['message'] = dao::getError();
                return $this->send($response);
            }
        }
        else
        {
            setcookie('tab', false, time(), $this->config->webRoot);
            $loginExpired = !(preg_match("/(m=|\/)(index)(&f=|-)(index)(&|-|\.)?/", strtolower($this->referer), $output) or $this->referer == $this->config->webRoot or empty($this->referer) or preg_match("/\/www\/$/", strtolower($this->referer), $output));
            $this->config->notMd5Pwd = true;
            $this->loadModel('misc');
            $this->loadModel('extension');
            $this->view->noGDLib       = sprintf($this->lang->misc->noGDLib, common::getSysURL() . $this->config->webRoot, '', false, true);
            $this->view->title         = $this->lang->user->login;
            $this->view->referer       = $this->referer;
            $this->view->s             = zget($this->config->global, 'sn', '');
            $this->view->keepLogin     = $this->cookie->keepLogin ? $this->cookie->keepLogin : 'off';
            $this->view->rand          = $this->user->updateSessionRandom();
            $this->view->unsafeSites   = $this->misc->checkOneClickPackage();
            $this->view->plugins       = $this->extension->getExpiringPlugins(true);
            $this->view->loginExpired  = $loginExpired;
            $this->display();
        }
    }
}