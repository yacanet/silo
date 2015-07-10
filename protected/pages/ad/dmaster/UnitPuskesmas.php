<?php
prado::using ('Application.MainPageAD');
class UnitPuskesmas extends MainPageAD {    
	public function onLoad($param) {		
		parent::onLoad($param);				        
		$this->showUnitPuskesmas=true;      
        $this->createObj('DMaster');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageUnitPuskesmas'])||$_SESSION['currentPageUnitPuskesmas']['page_name']!='ad.dmaster.UnitPuskesmas') {
				$_SESSION['currentPageUnitPuskesmas']=array('page_name'=>'ad.dmaster.UnitPuskesmas','page_num'=>0,'search'=>false);												
			}   
            $_SESSION['currentPageUnitPuskesmas']['search']=false;            
			$this->populateData ();	
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageUnitPuskesmas']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageUnitPuskesmas']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageUnitPuskesmas']['search']=true;
        $this->populateData($_SESSION['currentPageUnitPuskesmas']['search']);
	}
    protected function populateData ($search=false) {        
        $idpuskesmas=$this->idpuskesmas;
        $str = "SELECT idunitpuskesmas,nama_unit,notelpfax,idpuskesmas,nip_ka_unit,nama_ka_unit,enabled FROM unitpuskesmas WHERE idpuskesmas=$idpuskesmas";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {                
                case 'nama' :
                    $cluasa=" AND nama_unit LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("unitpuskesmas WHERE idpuskesmas='$idpuskesmas'$cluasa",'idunitpuskesmas');
                    $str = "$str $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ("unitpuskesmas WHERE idpuskesmas='$idpuskesmas'",'idunitpuskesmas');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageUnitPuskesmas']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageUnitPuskesmas']['page_num']=0;}
        $str = "$str ORDER BY nama_unit ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idunitpuskesmas','nama_unit','notelpfax','nip_ka_unit','nama_ka_unit','enabled'));
		$r=$this->DB->getRecord($str,$offset+1);                        
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}
    public function addProcess ($sender,$param) {
        $this->idProcess='add';                
    }
    public function checkId ($sender,$param) {
		$this->idProcess=$sender->getId()=='addKodeUnitPuskesmas'?'add':'edit';
        $kode_unit_puskesmas=$param->Value;		
        if ($kode_puskesmas != '') {
            try {   
                if ($this->hiddenid->Value!=$kode_unit_puskesmas) {                    
                    if ($this->DB->checkRecordIsExist('idunitpuskesmas','unitpuskesmas',$kode_unit_puskesmas)) {                                
                        throw new Exception ("Kode Unit Puskesmas ($kode_unit_puskesmas) sudah tidak tersedia silahkan ganti dengan yang lain.");		
                    }                               
                }                
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }	
    }    
    public function saveData ($sender,$param) {
        if ($this->IsValid) {
            $idunitpuskesmas=$this->txtAddKodeUnitPuskesmas->Text;            
            $nama_unit=addslashes(strtoupper($this->txtAddNamaUnit->Text));            
            $alamat=addslashes($this->txtAddAlamatUnit->Text);
            $idpuskesmas=$this->idpuskesmas;
            $notelpfax=addslashes($this->txtAddNoTelpFax->Text);            
            $kodepos=addslashes($this->txtAddKodePos->Text);                        
            $nip=addslashes($this->txtAddNIPKA->Text);
            $nama_ka_unit=addslashes($this->txtAddNamaKA->Text);
            $nip_pengelola=addslashes($this->txtAddNIPPengelola->Text);
            $nama_pengelola=addslashes($this->txtAddNamaPengelola->Text);
            $str = "INSERT INTO unitpuskesmas(idunitpuskesmas,nama_unit,alamat,idpuskesmas,notelpfax,kodepos,nip_ka_unit,nama_ka_unit,nip_pengelola_obat,nama_pengelola_obat) VALUES ($idunitpuskesmas,'$nama_unit','$alamat',$idpuskesmas,'$notelpfax','$kodepos','$nip','$nama_ka_unit','$nip_pengelola','$nama_pengelola')";            
            $this->DB->insertRecord($str);            
            
            if ($this->Application->Cache) {            
                $dataitem=$this->getList('unitpuskesmas WHERE enabled=1',array('idunitpuskesmas','nama_unit'),'nama_unit',null,1);
                $dataitem['none']='Daftar Poli Puskesmas';    
                $this->Application->Cache->set('listunitpuskesmas',$dataitem);                
            }
            $this->redirect('dmaster.UnitPuskesmas',true);            
        }
    }
    public function editRecord ($sender,$param) {
        $this->idProcess='edit';        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);        
		$this->hiddenid->Value=$id;        
        $str = "SELECT idunitpuskesmas,nama_unit,alamat,notelpfax,idpuskesmas,kodepos,nip_ka_unit,nama_ka_unit,nip_pengelola_obat,nama_pengelola_obat,enabled FROM unitpuskesmas WHERE idunitpuskesmas=$id";        
        $this->DB->setFieldTable(array('idunitpuskesmas','nama_unit','alamat','notelpfax','idpuskesmas','kodepos','nip_ka_unit','nama_ka_unit','nip_pengelola_obat','nama_pengelola_obat','enabled'));
        $r=$this->DB->getRecord($str);    
        $this->txtEditKodeUnitPuskesmas->Text=$r[1]['idunitpuskesmas'];        
        $this->txtEditNamaUnit->Text=$r[1]['nama_unit'];        
        $this->txtEditAlamatUnit->Text=$r[1]['alamat'];        
        $this->txtEditNoTelpFax->Text=$r[1]['notelpfax'];                      
        $this->txtEditKodePos->Text=$r[1]['kodepos'];
        $this->txtEditNIPKA->Text=$r[1]['nip_ka_unit'];
        $this->txtEditNamaKA->Text=$r[1]['nama_ka_unit'];
        $this->txtEditNIPPengelola->Text=$r[1]['nip_pengelola_obat'];
        $this->txtEditNamaPengelola->Text=$r[1]['nama_pengelola_obat'];
        $this->cmbEditStatus->Text=$r[1]['enabled'];        
        
    }
    public function updateData ($sender,$param) {
        if ($this->IsValid) {
            $id=$this->hiddenid->Value;
            $idunitpuskesmas=$this->txtEditKodeUnitPuskesmas->Text;
            $nama_unit=addslashes(strtoupper($this->txtEditNamaUnit->Text));                               
            $alamat=addslashes($this->txtEditAlamatUnit->Text);
            $notelpfax=addslashes($this->txtEditNoTelpFax->Text);                        
            $kodepos=addslashes($this->txtEditKodePos->Text);
            $nip=addslashes($this->txtEditNIPKA->Text);
            $nama_ka_unit=addslashes($this->txtEditNamaKA->Text);
            $nip_pengelola=addslashes($this->txtEditNIPPengelola->Text);
            $nama_pengelola=addslashes($this->txtEditNamaPengelola->Text);
            $enabled=$this->cmbEditStatus->Text;
            $str = "UPDATE unitpuskesmas SET idunitpuskesmas=$idunitpuskesmas,nama_unit='$nama_unit',alamat='$alamat',notelpfax='$notelpfax',kodepos='$kodepos',nip_ka_unit='$nip',nama_ka_unit='$nama_ka_unit',nip_pengelola_obat='$nip_pengelola',nama_pengelola_obat='$nama_pengelola',enabled=$enabled WHERE idunitpuskesmas=$id";
            $this->DB->updateRecord($str);        
            if ($this->Application->Cache) {            
                $dataitem=$this->getList('unitpuskesmas WHERE enabled=1',array('idunitpuskesmas','nama_unit'),'nama_unit',null,1);
                $dataitem['none']='Daftar Poli Puskesmas';    
                $this->Application->Cache->set('listunitpuskesmas',$dataitem);                
            }
            $this->redirect('dmaster.UnitPuskesmas',true);
            
        }
    }
    public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);        
        $this->DB->deleteRecord("unitpuskesmas WHERE idunitpuskesmas=$id");
        if ($this->Application->Cache) {            
            $dataitem=$this->getList('unitpuskesmas WHERE enabled=1',array('idunitpuskesmas','nama_unit'),'nama_unit',null,1);
            $dataitem['none']='Daftar Poli Puskesmas';    
            $this->Application->Cache->set('listunitpuskesmas',$dataitem);                
        }
        $this->redirect('dmaster.UnitPuskesmas',true);		        
	}
}