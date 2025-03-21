<?php include $app->getModuleRoot() . 'common/view/header.html.php';?>
<div id='mainContent' class='main-content'>
  <?php if(!extension_loaded('ldap')):?>
  <div class='center-block alert alert-danger'>
    <h4><?php echo $lang->ldap->noldap->header?></h4>
    <hr />
    <div class='box-content'><?php echo $lang->ldap->noldap->content?></div>
  </div>
  <?php else:?>
  <div class='center-block'>
    <form method="post" class="main-form form-ajax" enctype="multipart/form-data" id="ldapForm">
        <div class='detail-title'><?php echo $lang->ldap->base?></div>
        <table class='table table-form'>
          <tr>
            <th class='thWidth'><?php echo $lang->ldap->turnon?></th>
            <td class='w-400px'><?php echo html::select('turnon', $lang->ldap->turnonList, empty($ldapConfig->turnon) ? '' : $ldapConfig->turnon, "class='form-control'")?></td>
            <td></td>
          </tr>
          <tr>
            <th><?php echo $lang->ldap->type?></th>
            <td><?php echo html::select('type', $lang->ldap->typeList, empty($ldapConfig->type) ? '' : $ldapConfig->type, "class='form-control'")?></td>
            <td>
              <?php $checked = isset($ldapConfig->anonymous) ? 'checked' : '';?>
              <div class='checkbox-primary'>
                <input type="checkbox" id="anonymous" name="anonymous" value="1" <?php echo $checked?>>
                <label for='anonymous'><?php echo $lang->ldap->anonymous?></label>
              </div>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->ldap->host?></th>
            <td><?php echo html::input('host', empty($ldapConfig->host) ? '' : $ldapConfig->host, "class='form-control' autocomplete='off'")?></td>
            <td><?php echo $lang->ldap->example . 'ldap.test.com'?></td>
          </tr>
          <tr>
            <th><?php echo $lang->ldap->port?></th>
            <td><?php echo html::input('port', empty($ldapConfig->port) ? '389' : $ldapConfig->port, "class='form-control' autocomplete='off'")?></td>
          </tr>
          <tr>
            <th><?php echo $lang->ldap->version?></th>
            <td><?php echo html::select('version',$lang->ldap->versionList, empty($ldapConfig->version) ? '3' : $ldapConfig->version, "class='form-control'")?></td>
          </tr>
          <tr class='adshow'>
            <th><?php echo $lang->ldap->admin?></th>
            <td><?php echo html::input('admin', empty($ldapConfig->admin) ? '' : $ldapConfig->admin, "class='form-control' autocomplete='off'")?></td>
            <td><?php echo $lang->ldap->example . 'cn=admin,dc=test,dc=com'?></td>
          </tr>
          <tr class='adshow'>
            <th><?php echo $lang->ldap->password?></th>
            <td>
              <input type='password' style="display:none"> <!-- for disable autocomplete all browser -->
              <?php echo html::password('password', empty($ldapConfig->password) ? '' : helper::decryptPassword($ldapConfig->password), "class='form-control' autocomplete='off'")?>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->ldap->baseDN?></th>
            <td><?php echo html::input('baseDN', empty($ldapConfig->baseDN) ? '' : $ldapConfig->baseDN, "class='form-control' autocomplete='off'")?></td>
            <td><?php echo $lang->ldap->example . 'dc=test,dc=com'?></td>
          </tr>
          <tr>
            <th><?php echo $lang->ldap->charset?></th>
            <td><?php echo html::input('charset', empty($ldapConfig->charset) ? 'utf-8' : $ldapConfig->charset, "class='form-control' autocomplete='off'")?></td>
          </tr>
        </table>
        <div class='detail-title'><?php echo $lang->ldap->attr?></div>
        <table class='table table-form'>
          <tr>
            <th class='thWidth'><?php echo $lang->ldap->account?></th>
            <td class='w-400px'><?php echo html::input('account', empty($ldapConfig->account) ? 'samaccountname' : $ldapConfig->account, "class='form-control' autocomplete='off'")?></td>
            <td><?php echo $lang->ldap->accountPS?></td>
          </tr>
          <tr>
            <th><?php echo $lang->ldap->defaultGroup;?></th>
            <td><?php echo html::select('group', $groups, empty($ldapConfig->group) ? '' : $ldapConfig->group, "class='form-control'");?></td>
          </tr>
          <tr>
            <th><?php echo $lang->ldap->realname?></th>
            <td><?php echo html::input('realname', empty($ldapConfig->realname) ? 'name' : $ldapConfig->realname, "class='form-control' autocomplete='off'")?></td>
          </tr>
          <tr>
            <th><?php echo $lang->ldap->email?></th>
            <td><?php echo html::input('email', empty($ldapConfig->email) ? 'Email' : $ldapConfig->email, "class='form-control' autocomplete='off'")?></td>
          </tr>
          <tr>
            <th><?php echo $lang->ldap->mobile?></th>
            <td><?php echo html::input('mobile', empty($ldapConfig->mobile) ? 'mobile' : $ldapConfig->mobile, "class='form-control' autocomplete='off'")?></td>
          </tr>
          <tr>
            <th><?php echo $lang->ldap->phone?></th>
            <td><?php echo html::input('phone', empty($ldapConfig->phone) ? 'telephonenumber' : $ldapConfig->phone, "class='form-control' autocomplete='off'")?></td>
          </tr>
        </table>
        <div class='detail-title'><?php echo $lang->ldap->other?></div>
        <table class='table table-form'>
          <tr>
            <th class='thWidth'><?php echo $lang->ldap->repeatPolicy?></th>
            <td class='w-400px repeatPolicy'><?php echo html::radio('repeatPolicy', $lang->ldap->repeatPolicyList, empty($ldapConfig->repeatPolicy) ? 'number' : $ldapConfig->repeatPolicy)?></td>
            <td></td>
          </tr>
          <tr>
            <th><?php echo $lang->ldap->autoCreate?></th>
            <td><?php echo html::radio('autoCreate', $lang->ldap->autoCreateList, zget($ldapConfig, 'autoCreate', 1));?></td>
            <td></td>
          </tr>
          <tr>
          <?php $disabled = empty($ldapConfig->turnon) ? 'disabled' : '';?>
            <td colspan='3' class='text-center form-actions'>
              <?php echo html::submitButton($lang->save, '', 'btn btn-secondary btn-wide');?>
              <?php if(common::hasPriv('user', 'importldap')) echo ' ' . html::a($this->createLink('user', 'importLDAP'), $lang->ldap->import, '', "class='btn btn-primary' $disabled");?></td>
            </td>
          </tr>
        </table>
    </form>
  </div>
  <?php endif;?>
</div>
<?php js::set('importTipText', $lang->ldap->importTip)?>
<?php js::set('goImportText',  $lang->ldap->goImport)?>
<?php include $app->getModuleRoot() . 'common/view/footer.html.php';?>
