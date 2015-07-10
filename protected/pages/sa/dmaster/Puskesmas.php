<?php
/**
 * Puskesmas
 *  
 * @category   Super Admin
 * @package    DataMaster 
 * @version    0.1 2015-05-16
 */
prado::using ('Application.MainPageSA');
class Puskesmas extends MainPageSA {    
	public function onLoad($param) {		
		parent::onLoad($param);				        
		$this->showPuskesmas=true;      
        $this->createObj('DMaster');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPagePuskesmas'])||$_SESSION['currentPagePuskesmas']['page_name']!='sa.dmaster.Puskesmas') {
				$_SESSION['currentPagePuskesmas']=array('page_name'=>'sa.dmaster.Puskesmas','page_num'=>0,'search'=>false);												
			}   
            $_SESSION['currentPagePuskesmas']['search']=false;            
			$this->populateData ();	            
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPagePuskesmas']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPagePuskesmas']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPagePuskesmas']['search']=true;
        $this->populateData($_SESSION['currentPagePuskesmas']['search']);
	}
    protected function populateData ($search=false) {        
        $str = "SELECT idpuskesmas,nama_puskesmas,notelpfax,idkecamatan,nip_ka,nama_ka,enabled FROM puskesmas";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {                
                case 'nama' :
                    $cluasa="nama_puskesmas LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("puskesmas WHERE $cluasa",'idpuskesmas');
                    $str = "$str WHERE $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ('puskesmas','idpuskesmas');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPagePuskesmas']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPagePuskesmas']['page_num']=0;}
        $str = "$str ORDER BY nama_puskesmas ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idpuskesmas','nama_puskesmas','notelpfax','idkecamatan','nip_ka','nama_ka','enabled'));
		$r=$this->DB->getRecord($str,$offset+1);                        
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}
    public function addProcess ($sender,$param) {
        $this->idProcess='add';        
        $this->cmbAddKecamatan->DataSource=$this->DMaster->getListKecamatan();        
        $this->cmbAddKecamatan->DataBind();        
    }
    public function checkId ($sender,$param) {
		$this->idProcess=$sender->getId()=='addKodePuskesmas'?'add':'edit';
        $kode_puskesmas=$param->Value;		
        if ($kode_puskesmas != '') {
            try {   
                if ($this->hiddenid->Value!=$kode_puskesmas) {                    
                    if ($this->DB->checkRecordIsExist('idpuskesmas','puskesmas',$kode_puskesmas)) {                                
                        throw new Exception ("Kode Puskesmas ($kode_puskesmas) sudah tidak tersedia silahkan ganti dengan yang lain.");		
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
            $idpuskesmas=$this->txtAddKodePuskesmas->Text;            
            $nama_puskesmas=addslashes(strtoupper($this->txtAddNamaPuskesmas->Text));            
            $alamat=addslashes($this->txtAddAlamatPuskesmas->Text);
            $idkecamatan=$this->cmbAddKecamatan->Text;
            $notelpfax=addslashes($this->txtAddNoTelpFax->Text);            
            $kodepos=addslashes($this->txtAddKodePos->Text);                        
            $nip=addslashes($this->txtAddNIPKA->Text);
            $nama_ka=addslashes($this->txtAddNamaKA->Text);
            $nip_pengelola=addslashes($this->txtAddNIPPengelola->Text);
            $nama_pengelola=addslashes($this->txtAddNamaPengelola->Text);
            $str = "INSERT INTO puskesmas(idpuskesmas,nama_puskesmas,alamat,idkecamatan,notelpfax,kodepos,nip_ka,nama_ka,nip_pengelola_obat,nama_pengelola_obat) VALUES ($idpuskesmas,'$nama_puskesmas','$alamat',$idkecamatan,'$notelpfax','$kodepos','$nip','$nama_ka','$nip_pengelola','$nama_pengelola')";            
            $this->DB->insertRecord($str);
            if ($this->Application->Cache) {                                
                $dataitem=$this->DMaster->getList('puskesmas WHERE enabled=1',array('idpuskesmas','nama_puskesmas'),'nama_puskesmas',null,1);
                $dataitem['none']='Daftar Puskesmas';    
                $this->Application->Cache->set('listpuskesmas',$dataitem);     
                
                $dataitem=$this->DMaster->getList('puskesmas WHERE enabled=1',array('idpuskesmas','nip_ka','nama_ka'),'nama_puskesmas',null,2);
                $dataitem['none']='Daftar KA Puskesmas';    
                $this->Application->Cache->set('listkapuskesmas',$dataitem);
            }
            $this->redirect('dmaster.Puskesmas',true);            
        }
    }
    public function editRecord ($sender,$param) {
        $this->idProcess='edit';        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);        
		$this->hiddenid->Value=$id;        
        $str = "SELECT idpuskesmas,nama_puskesmas,alamat,notelpfax,idkecamatan,kodepos,nip_ka,nama_ka,nip_pengelola_obat,nama_pengelola_obat,enabled FROM puskesmas WHERE idpuskesmas=$id";        
        $this->DB->setFieldTable(array('idpuskesmas','nama_puskesmas','alamat','notelpfax','idkecamatan','kodepos','nip_ka','nama_ka','nip_pengelola_obat','nama_pengelola_obat','enabled'));
        $r=$this->DB->getRecord($str);    
        $this->txtEditKodePuskesmas->Text=$r[1]['idpuskesmas'];        
        $this->txtEditNamaPuskesmas->Text=$r[1]['nama_puskesmas'];        
        $this->txtEditAlamatPuskesmas->Text=$r[1]['alamat'];        
        $this->txtEditNoTelpFax->Text=$r[1]['notelpfax'];        
        $this->cmbEditKecamatan->DataSource=$this->DMaster->getListKecamatan();
        $this->cmbEditKecamatan->Text=$r[1]['idkecamatan'];        
        $this->cmbEditKecamatan->DataBind();        
        $this->txtEditKodePos->Text=$r[1]['kodepos'];
        $this->txtEditNIPKA->Text=$r[1]['nip_ka'];
        $this->txtEditNamaKA->Text=$r[1]['nama_ka'];
        $this->txtEditNIPPengelola->Text=$r[1]['nip_pengelola_obat'];
        $this->txtEditNamaPengelola->Text=$r[1]['nama_pengelola_obat'];
        $this->cmbEditStatus->Text=$r[1]['enabled'];        
        
    }
    public function updateData ($sender,$param) {
        if ($this->IsValid) {
            $id=$this->hiddenid->Value;
            $idpuskesmas=$this->txtEditKodePuskesmas->Text;
            $nama_puskesmas=addslashes(strtoupper($this->txtEditNamaPuskesmas->Text));                               
            $alamat=addslashes($this->txtEditAlamatPuskesmas->Text);
            $notelpfax=addslashes($this->txtEditNoTelpFax->Text);            
            $idkecamatan=$this->cmbEditKecamatan->Text;
            $kodepos=addslashes($this->txtEditKodePos->Text);
            $nip=addslashes($this->txtEditNIPKA->Text);
            $nama_ka=addslashes($this->txtEditNamaKA->Text);
            $nip_pengelola=addslashes($this->txtEditNIPPengelola->Text);
            $nama_pengelola=addslashes($this->txtEditNamaPengelola->Text);
            $enabled=$this->cmbEditStatus->Text;
            $str = "UPDATE puskesmas SET idpuskesmas=$idpuskesmas,nama_puskesmas='$nama_puskesmas',alamat='$alamat',notelpfax='$notelpfax',idkecamatan=$idkecamatan,kodepos='$kodepos',nip_ka='$nip',nama_ka='$nama_ka',nip_pengelola_obat='$nip_pengelola',nama_pengelola_obat='$nama_pengelola',enabled=$enabled WHERE idpuskesmas=$id";
            $this->DB->updateRecord($str);            
            $this->DB->updateRecord("UPDATE user SET idpuskesmas=$idpuskesmas WHERE idpuskesmas=$id");
            if ($this->Application->Cache) {                                
                $dataitem=$this->DMaster->getList('puskesmas WHERE enabled=1',array('idpuskesmas','nama_puskesmas'),'nama_puskesmas',null,1);
                $dataitem['none']='Daftar Puskesmas';    
                $this->Application->Cache->set('listpuskesmas',$dataitem);  
                
                $dataitem=$this->DMaster->getList('puskesmas WHERE enabled=1',array('idpuskesmas','nip_ka','nama_ka'),'nama_puskesmas',null,2);
                $dataitem['none']='Daftar KA Puskesmas';    
                $this->Application->Cache->set('listkapuskesmas',$dataitem);
            }
            $this->redirect('dmaster.Puskesmas',true);
            
        }
    }
    public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);      
        if ($this->DB->checkRecordIsExist('idpuskesmas','master_obat_puskesmas',$id)) {                                
            $this->labelMessageError->Text="ID Puskesmas ($id), sedang digunakan di Master Obat Puskesmas jadi tidak bisa dihapus. Alternatifnya Anda bisa menonaktifkan puskesmas ini.";
            $this->modalMessageError->show();                        
        }else{            
            $this->DB->deleteRecord("puskesmas WHERE idpuskesmas=$id");
            $this->DB->deleteRecord("user WHERE idpuskesmas=$id");
            if ($this->Application->Cache) {                                
                $dataitem=$this->DMaster->getList('puskesmas WHERE enabled=1',array('idpuskesmas','nama_puskesmas'),'nama_puskesmas',null,1);
                $dataitem['none']='Daftar Puskesmas';    
                $this->Application->Cache->set('listpuskesmas',$dataitem);         

                $dataitem=$this->DMaster->getList('puskesmas WHERE enabled=1',array('idpuskesmas','nip_ka','nama_ka'),'nama_puskesmas',null,2);
                $dataitem['none']='Daftar KA Puskesmas';    
                $this->Application->Cache->set('listkapuskesmas',$dataitem);
            }            
            $this->redirect('dmaster.Puskesmas',true);		        
        }          
	}
}