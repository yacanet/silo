<?php
class Login extends MainPage { 
    public function OnPreInit ($param) {	
		parent::onPreInit ($param);	
		$this->MasterClass="Application.layouts.LoginTemplate";				
	}
	public function onLoad($param) {		
		parent::onLoad($param);				
		if (!$this->IsPostBack&&!$this->IsCallBack) {            
		}
	}
	public function checkUsernameAndPassword($sender,$param) {		
        $username=$param->Value;
        if ($username != '') {
            try {  
                $auth = $this->Application->getModule ('auth');
                $username=addslashes(trim($this->txtUsername->Text));
                $userpassword=addslashes(trim($this->txtPassword->Text));
                if (!$auth->login ($username,$userpassword)){			                    
                    throw new Exception ('Username atau password salah!.Silahkan ulangi kembali');						
                }
            }catch (Exception $e) {		
                $message='<br /><div class="alert alert-danger">
                    <strong>Error!</strong>
                    '.$e->getMessage().'</div>';
				$sender->ErrorMessage=$message;					
				$param->IsValid=false;		
			}
        }							
		
	}
    
    public function doLogin ($sender,$param) {
        if ($this->IsValid) {                        
            $pengguna=$this->getLogic('Users');
            $_SESSION['ta']=date('Y');
            $_SESSION['foto']= $pengguna->getDataUser('foto');
            $userid=$pengguna->getDataUser('userid');
            $this->DB->updateRecord("UPDATE user SET logintime=NOW() WHERE userid=$userid");                                    
            $this->redirect('Home',true);
        }
    }
    
}
?>