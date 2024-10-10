<?php

helper::importControl("user");
class myUser extends user
{
    public function importLDAP($type = "all", $param = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel("ldap");
        $type = strtolower($type);
        $queryID = $type == "bysearch" ? (int) $param : 0;
        
        if($this->config->edition != "open") {
            
            if($_POST) {
                foreach ($this->post->add as $i => $add) {
                    if(!$maxUsers) {
                        $userCount++;
                        if($this->config->vision == "rnd" && isset($properties["user"]) && $properties["user"]["value"] <= $userCount)
                        {
                            
                        }
                    }
                }
            }
        }
        
        // exit(var_dump(123));
        $this->view->title      = $this->lang->user->importLDAP;
        $this->view->users = 
        $this->display();
    }
}