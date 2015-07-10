<?php
prado::using ('Application.MainPageSA');
class Profiles extends MainPageSA {
	public function onLoad($param) {		
		parent::onLoad($param);		        
		if (!$this->IsPostBack&&!$this->IsCallBack) {
            if (!isset($_SESSION['currentPageProfile'])||$_SESSION['currentPageProfile']['page_name']!='Profiles') {
                $_SESSION['currentPageProfile']=array('page_name'=>'Profiles','page_num'=>0,'roles'=>'none','search'=>false);												
			}
            $this->userPhoto->ImageUrl=$this->setup->getAddress().'/'.$_SESSION['foto'];
            $this->path_userimages->Value=$_SESSION['foto'];
			$this->populateData ();			
		}
	}   
    public function populateData ($sender,$param) {				
		$id=$this->Pengguna->getDataUser('userid');      
		$this->hiddenuserid->Value=$id;
        $str = "SELECT username,nip,nama,mobile_phone,email,active FROM user WHERE userid='$id'";
        $this->DB->setFieldTable(array('username','nip','nama','mobile_phone','email','active'));
        $r=$this->DB->getRecord($str);    
		$result = $r[1];        				
        $this->hiddenusername->Value=$result['username'];
        $this->hiddenemail->Value=$result['email'];
		$this->txtUsername->Text=$result['username'];		        
        $this->txtNama->Text=$result['nama'];
        $this->txtNIP->Text=$result['nip'];
        $this->txtAlamatEmail->Text=$result['email'];
		$this->txtNoHP->Text=$result['mobile_phone'];		                       
	}
    public function checkEmail ($sender,$param) {
		$this->idProcess=$sender->getId()=='CustomAddEmailValidator'?'add':'edit';
        $email=$param->Value;		
        if ($email != '') {
            try {   
                if ($this->hiddenemail->Value!=$email) {                    
                    if ($this->DB->checkRecordIsExist('email','user',$email)) {                                
                        throw new Exception ("<span class='error'>Email ($email) sudah tidak tersedia silahkan ganti dengan yang lain.</span>");		
                    }                               
                }                
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }	
    }  
    public function saveData ($sender,$param) {		
        if ($this->Page->IsValid) {          
            $id=$this->hiddenuserid->Value;       
            $nama=addslashes($this->txtNama->Text);
            $nip=addslashes($this->txtNIP->Text);
            $hp=addslashes($this->txtNoHP->Text);
            $alamatemail=$this->txtAlamatEmail->Text;        
            $userimages=$this->path_userimages->Value;
            $_SESSION['foto']=$userimages;
            if ($this->txtPassword->Text == '') {
                $str = "UPDATE user SET nip='$nip',nama='$nama',mobile_phone='$hp',email='$alamatemail',foto='$userimages' WHERE userid=$id";
            }else {
                $data=$this->Pengguna->createHashPassword($this->txtPassword->Text);
                $salt=$data['salt'];
                $password=$data['password'];
                $str = "UPDATE user SET userpassword='$password',salt='$salt',nip='$nip',nama='$nama',mobile_phone='$hp',email='$alamatemail',foto='$userimages' WHERE userid=$id";
            }
            $this->DB->updateRecord($str);           
            $this->redirect('setting.Profiles',true);
        }
	}    
    public function onImgUpload($sender, $param) {
        if ($sender->getHasFile()) {
            $mime=$sender->getFileType();
            if($mime!="image/png" && $mime!="image/jpg" && $mime!="image/jpeg"){
                $error =  '<div class="alert alert-warning">                
                            <p><strong>Error:</strong>the file is not an image</p>
                        </div>'; 
                $this->errorUpload->Text=$error;
                return;
            }           

            if($mime=="image/png")	{
                if(!(imagetypes() & IMG_PNG)) {
                    $error =  '<div class="alert alert-warning">                
                            <p><strong>Error:</strong>missing png support in gd library.</p>
                        </div>'; 
                    $this->errorUpload->Text=$error;                    
                    return;
                }
            }
            if(($mime=="image/jpg" || $mime=="image/jpeg")){
                if(!(imagetypes() & IMG_JPG)){                    
                    $error =  '<div class="alert alert-warning">                
                            <p><strong>Error:</strong>missing jpeg support in gd library.</p>
                        </div>'; 
                    $this->errorUpload->Text=$error;
                    return;
                }
            }
            $this->userPhoto->ImageUrl=$this->setup->resizeImage($sender->LocalName,null,100,100,false,'stream',false,false);
            $filename=substr(hash('sha512',rand()),0,8);
            $name=$sender->FileName;
            $part=$this->setup->cleanFileNameString($name);
            $path=$this->setup->getSettingValue('dir_userimages')."/$filename-$part";
            $this->path_userimages->Value=$path;
            $this->setup->resizeImage($sender->LocalName,null,100,100,false,BASEPATH."/$path",false,false);            
            chmod(BASEPATH."/$path",0644);             
        }else {                    
            //error handling
            switch ($sender->ErrorCode){
                case 1:
                    $err="file size too big (php.ini).";
                break;
                case 2:
                    $err="file size too big (form).";
                break;
                case 3:
                    $err="file upload interrupted.";
                break;
                case 4:
                    $err="no file chosen.";
                break;
                case 6:
                    $err="internal problem (missing temporary directory).";
                break;
                case 7:
                    $err="unable to write file on disk.";
                break;
                case 8:
                    $err="file type not accepted.";
                break;
            }
            $error =  '<div class="alert alert-warning">                
                            <p><strong>Error:</strong>'.$err.'</p>
                        </div>';   
            $this->errorUpload->Text=$error;
            return;        
        }        
    }
}