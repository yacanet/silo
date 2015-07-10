<?php
prado::using ('Application.MainPageAD');
class UserPuskesmas extends MainPageAD {
	public function onLoad ($param) {		
		parent::onLoad ($param);    
        $this->showSubMenuSettingSistem = true;
        $this->showUserPuskesmas = true;
        $this->createObj('DMaster');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageUserPuskesmas'])||$_SESSION['currentPageUserPuskesmas']['page_name']!='ad.setting.UserPuskesmas') {
                $_SESSION['currentPageUserPuskesmas']=array('page_name'=>'ad.setting.UserPuskesmas','page_num'=>0,'search'=>false,'idpuskesmas');												
			}     
            $_SESSION['currentPageUserPuskesmas']['search']=false;                        
			$this->populateData ();			
		}
	}
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageUserPuskesmas']['page_num']=$param->NewPageIndex;
		$this->populateData($_SESSION['currentPageUserPuskesmas']['search']);
	} 
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageUserPuskesmas']['search']=true;
        $this->populateData($_SESSION['currentPageUserPuskesmas']['search']);
	}
	protected function populateData ($search=false) {      
        $idpuskesmas=$this->Pengguna->getDataUser('idpuskesmas');
        if ($search) {
            $txtsearch=$this->txtKriteria->Text;
            $str = "SELECT userid,username,page,nama,mobile_phone,isdeleted,page,active FROM user WHERE u.idpuskesmas=$idpuskesmas";
            switch ($this->cmbKriteria->Text) {
                case 'username' :
                    $cluasa=" AND username LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("user u  WHERE u.idpuskesmas=$idpuskesmas $cluasa",'userid');
                    $str = "$str $cluasa";
                break;
                case 'nama' :
                    $cluasa=" AND nama LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("user u WHERE u.idpuskesmas=$idpuskesmas $cluasa",'userid');
                    $str = "$str $cluasa";
                break;
            }
        }else {            
            $str = "SELECT userid,username,page,nama,mobile_phone,isdeleted,page,active,foto FROM user u  WHERE u.idpuskesmas=$idpuskesmas";
            $jumlah_baris=$this->DB->getCountRowsOfTable ("user u WHERE u.idpuskesmas=$idpuskesmas",'userid');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageUserPuskesmas']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageUserPuskesmas']['page_num']=0;}
        $str = "$str LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('userid','username','page','nama','mobile_phone','isdeleted','page','active','foto'));
		$r=$this->DB->getRecord($str,$offset+1);        
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();    
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);        
	}
    public function addProcess ($sender,$param) {
        $this->idProcess='add';            
    }
    public function dataBound ($sender,$param) {
		$item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {	
            if ($item->DataItem['isdeleted']==1){                
                $item->btnDelete->Attributes->OnClick="if(!confirm('Anda ingin menghapus data User ini ?')) return false;";                
            }else{
                $item->btnDelete->Enabled=false;                
                $item->btnDelete->CssClass='table-link disabled';
            }
        }
    }
    public function checkUsername ($sender,$param) {        
		$this->idProcess=$sender->getId()=='addUsername'?'add':'edit';
        $username=$param->Value;		
        if ($username != '') {
            try {   
                if ($this->hiddenusername->Value!=$username) {                                                                                                           
                    if ($this->DB->checkRecordIsExist('username','user',$username)) {                                
                        throw new Exception ("Username ($username) sudah tidak tersedia silahkan ganti dengan yang lain.");                    
                    }                    
                }                
                
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }	
    }
    public function checkEmail ($sender,$param) {        
		$this->idProcess=$sender->getId()=='addEmail'?'add':'edit';
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
    public function saveData($sender,$param) {		
        if ($this->Page->IsValid) {		
            $username=addslashes($this->txtAddUsername->Text);            
            $alamatemail=addslashes($this->txtAddEmail->Text);            
            $page=$this->cmbAddRoles->Text;
            $data=$this->Pengguna->createHashPassword(addslashes($this->txtAddPassword->Text));
            $salt=$data['salt'];
            $password=$data['password'];                        
            $idpuskesmas=$this->Pengguna->getDataUser('idpuskesmas');            
            $foto=$this->setup->getSettingValue('dir_userimages').'/no_photo.png';
            $str = "INSERT INTO user (userid,idpuskesmas,username,userpassword,salt,page,email,active,foto) VALUES (NULL,'$idpuskesmas','$username','$password','$salt','$page','$alamatemail',1,'$foto')";
            $this->DB->insertRecord($str);
            $this->redirect('setting.UserPuskesmas',true);                     
        }
	}
    public function editRecord ($sender,$param) {		
		$this->idProcess='edit';
		$id=$this->getDataKeyField($sender,$this->RepeaterS);        
		$this->hiddenuserid->Value=$id;
        $str = "SELECT idpuskesmas,username,email,active FROM user WHERE userid='$id'";
        $this->DB->setFieldTable(array('idpuskesmas','username','email','active'));
        $r=$this->DB->getRecord($str);    
		$result = $r[1];        				
        $this->hiddenusername->Value=$result['username'];
        $this->hiddenemail->Value=$result['email'];
		$this->txtEditUsername->Text=$result['username'];		
        $this->txtEditUsername->Enabled=$result['page']=='p'?false:true;
		$this->txtEditEmail->Text=$result['email'];		        		        
        $this->cmbEditRoles->DataSource=$result['page'];        
        $this->cmbEditStatus->Text=$result['active'];
	}
    public function updateData($sender,$param) {		
        if ($this->Page->IsValid) {		
            $id=$this->hiddenuserid->Value;
            $username= addslashes($this->txtEditUsername->Text);            
            $alamatemail=addslashes($this->txtEditEmail->Text);                        
            $page=$this->cmbEditRoles->Text;            
            $active=$this->cmbEditStatus->Text;
            if ($this->txtEditPassword->Text == '') {
                $str = "UPDATE user SET page='$page',username='$username',email='$alamatemail',active='$active' WHERE userid=$id";
            }else {
                $data=$this->Pengguna->createHashPassword(addslashes($this->txtEditPassword->Text));
                $salt=$data['salt'];
                $password=$data['password'];
                $str = "UPDATE user SET  page='$page',username='$username',userpassword='$password',salt='$salt',email='$alamatemail',active='$active' WHERE userid=$id";
            }
            $this->DB->updateRecord($str);           
            $this->redirect('setting.UserPuskesmas',true);
        }
	}
    public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);
		$this->DB->deleteRecord("user WHERE userid=$id");		
		$this->redirect('setting.UserPuskesmas',true);		
	}
}

?>