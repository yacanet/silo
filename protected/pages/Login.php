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
            $foto = $pengguna->getDataUser('foto');
            $lokasi = BASEPATH . $this->setup->getSettingValue('dir_userimages');            
            if (!is_file("$lokasi/$foto")) {
                $foto='no_photo.png';
            }
            $_SESSION['foto']= $foto;
            $userid=$pengguna->getDataUser('userid');
            $this->DB->updateRecord("UPDATE user SET logintime=NOW() WHERE userid=$userid");                                    
            
            $_SESSION['awal_tahun_sistem']=$this->setup->getSettingValue('awal_tahun_sistem');
            $this->redirect('Home',true);
        }
    }
    
}
?>