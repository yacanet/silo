<?php
prado::using ('Application.MainPageSA');
class Penyalur extends MainPageSA {    
	public function onLoad($param) {		
		parent::onLoad($param);				        
		$this->showPenyalur=true;      
        $this->createObj('DMaster');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPagePenyalur'])||$_SESSION['currentPagePenyalur']['page_name']!='sa.dmaster.Penyalur') {
				$_SESSION['currentPagePenyalur']=array('page_name'=>'sa.dmaster.Penyalur','page_num'=>0,'search'=>false);												
			}   
            $_SESSION['currentPagePenyalur']['search']=false;            
			$this->populateData ();	
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPagePenyalur']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPagePenyalur']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPagePenyalur']['search']=true;
        $this->populateData($_SESSION['currentPagePenyalur']['search']);
	}
    protected function populateData ($search=false) {        
        $str = "SELECT idpenyalur,nama_penyalur,contactperson,notelp,nohp,enabled FROM penyalur";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {                
                case 'nama' :
                    $cluasa="nama_penyalur LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("penyalur WHERE $cluasa",'idpenyalur');
                    $str = "$str WHERE $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ('penyalur','idpenyalur');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPagePenyalur']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPagePenyalur']['page_num']=0;}
        $str = "$str ORDER BY nama_penyalur ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idpenyalur','nama_penyalur','contactperson','notelp','nohp','enabled'));
		$r=$this->DB->getRecord($str,$offset+1);                        
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}
    public function addProcess ($sender,$param) {
        $this->idProcess='add';        
    }    
    public function saveData ($sender,$param) {
        if ($this->IsValid) {                       
            $nama_penyalur=addslashes(strtoupper($this->txtAddNamaPenyalur->Text));            
            $alamat=addslashes($this->txtAddAlamatPenyalur->Text);
            $kota=addslashes($this->txtAddKota->Text);
            $notelp=addslashes($this->txtAddNoTelp->Text);            
            $nohp=addslashes($this->txtAddNoHP->Text);            
            $contactperson=addslashes($this->txtAddContactPerson->Text);                        
            $email=addslashes($this->txtAddEmail->Text);                        
            $web=addslashes($this->txtAddWeb->Text);                        
            $str = "INSERT INTO penyalur(idpenyalur,nama_penyalur,alamat,kota,notelp,nohp,contactperson,email,web) VALUES (NULL,'$nama_penyalur','$alamat','$kota','$notelp','$nohp','$contactperson','$email','$web')";            
            $this->DB->insertRecord($str);
            if ($this->Application->Cache) {                                
                $dataitem=$this->DMaster->getList('penyalur WHERE enabled=1',array('idpenyalur','nama_penyalur'),'nama_penyalur',null,1);
                $dataitem['none']='Seluruh Penyalur';    
                $this->Application->Cache->set('listpenyalur',$dataitem);                    
            }
            $this->redirect('dmaster.Penyalur',true);            
        }
    }
    public function editRecord ($sender,$param) {
        $this->idProcess='edit';        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);        
		$this->hiddenid->Value=$id;        
        $str = "SELECT idpenyalur,nama_penyalur,alamat,kota,notelp,nohp,contactperson,email,web,enabled FROM penyalur WHERE idpenyalur=$id";        
        $this->DB->setFieldTable(array('idpenyalur','nama_penyalur','alamat','kota','notelp','nohp','contactperson','email','web','enabled'));
        $r=$this->DB->getRecord($str);   
        $this->txtEditNamaPenyalur->Text=$r[1]['nama_penyalur'];        
        $this->txtEditAlamatPenyalur->Text=$r[1]['alamat'];
        $this->txtEditKota->Text=$r[1]['kota'];        
        $this->txtEditNoTelp->Text=$r[1]['notelp'];                
        $this->txtEditNoHP->Text=$r[1]['nohp'];
        $this->txtEditContactPerson->Text=$r[1]['contactperson'];
        $this->txtEditEmail->Text=$r[1]['email'];
        $this->txtEditWeb->Text=$r[1]['web'];
        $this->cmbEditStatus->Text=$r[1]['enabled'];
        
    }
    public function updateData ($sender,$param) {
        if ($this->IsValid) {                 
            $id=$this->hiddenid->Value;
            $nama_penyalur=addslashes(strtoupper($this->txtEditNamaPenyalur->Text));            
            $alamat=addslashes($this->txtEditAlamatPenyalur->Text);
            $kota=addslashes($this->txtEditKota->Text);
            $notelp=addslashes($this->txtEditNoTelp->Text);            
            $nohp=addslashes($this->txtEditNoHP->Text);            
            $contactperson=addslashes($this->txtEditContactPerson->Text);                        
            $email=addslashes($this->txtEditEmail->Text);                        
            $web=addslashes($this->txtEditWeb->Text);                        
            $status=$this->cmbEditStatus->Text;
            $str = "UPDATE penyalur SET nama_penyalur='$nama_penyalur',alamat='$alamat',kota='$kota',notelp='$notelp',nohp='$nohp',contactperson='$contactperson',email='$email',web='$web',enabled=$status WHERE idpenyalur=$id";            
            $this->DB->updateRecord($str);
            if ($this->Application->Cache) {                                
                $dataitem=$this->DMaster->getList('penyalur WHERE enabled=1',array('idpenyalur','nama_penyalur'),'nama_penyalur',null,1);
                $dataitem['none']='Seluruh Penyalur';    
                $this->Application->Cache->set('listpenyalur',$dataitem);                    
            }
            $this->redirect('dmaster.Penyalur',true);            
        }
    }    
    public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);        
        $this->DB->deleteRecord("penyalur WHERE idpenyalur=$id");
            
        if ($this->Application->Cache) {                                
            $dataitem=$this->DMaster->getList('penyalur WHERE enabled=1',array('idpenyalur','nama_penyalur'),'nama_penyalur',null,1);
            $dataitem['none']='Seluruh Penyalur';    
            $this->Application->Cache->set('listpenyalur',$dataitem);                    
        }
        $this->redirect('dmaster.Penyalur',true);		        
	}
}