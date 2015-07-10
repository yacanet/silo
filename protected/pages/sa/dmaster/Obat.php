<?php
prado::using ('Application.MainPageSA');
class Obat extends MainPageSA {    
	public function onLoad($param) {		
		parent::onLoad($param);				        
		$this->showObat=true;      
        $this->createObj('DMaster');
        $this->createObj('Obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageObat'])||$_SESSION['currentPageObat']['page_name']!='sa.dmaster.Obat') {
				$_SESSION['currentPageObat']=array('page_name'=>'sa.dmaster.Obat','page_num'=>0,'search'=>false,'idprodusen'=>'none');												
			}   
            $_SESSION['currentPageObat']['search']=false;  
            $this->cmbFilterProdusen->DataSource=$this->DMaster->getListProdusen ();
            $this->cmbFilterProdusen->Text=$_SESSION['currentPageObat']['idprodusen'];
            $this->cmbFilterProdusen->DataBind();
			$this->populateData ();	
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageObat']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageObat']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageObat']['search']=true;
        $this->populateData($_SESSION['currentPageObat']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageObat']['idprodusen']=$this->cmbFilterProdusen->Text;
        $this->populateData();
	}
    public function itemCreated($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {            
            $stock=$item->DataItem['stock'];
            if ($stock > 0) {
                $item->btnDelete->Enabled=false;                
                $item->btnDelete->CssClass='table-link disabled';
            }else {
                $item->btnDelete->Attributes->OnClick="if(!confirm('Anda ingin menghapus data Obat ini ?')) return false";
            }
        }
    }
    protected function populateData ($search=false) {        
        $idprodusen=$_SESSION['currentPageObat']['idprodusen'];
        $str_produsen=$idprodusen=='none' ?'':" WHERE idprodusen=$idprodusen";
        $str = "SELECT idobat,kode_obat,nama_obat,nama_bentuk,kemasan,idprodusen,harga,stock FROM master_obat$str_produsen";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa=$idprodusen=='none' ?" WHERE kode_obat='$txtsearch'":" AND kode_obat='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_obat $cluasa",'idobat');
                    $str = "$str $cluasa";
                break;
                case 'nama' :
                    $cluasa=$idprodusen=='none' ?" WHERE nama_obat LIKE '%$txtsearch%'" :" AND nama_obat LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_obat $cluasa",'idobat');
                    $str = "$str $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ('master_obat','idobat');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageObat']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageObat']['page_num']=0;}
        $str = "$str ORDER BY stock DESC,nama_obat ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idobat','kode_obat','nama_obat','nama_bentuk','kemasan','idprodusen','harga','stock'));
		$r=$this->DB->getRecord($str,$offset+1);     
        $data_r=array();
        while (list($k,$v)=each($r)) {            
            $v['nama_produsen']=$this->DMaster->getNamaProdusenByID($v['idprodusen']);            
            $data_r[$k]=$v;
        }
		$this->RepeaterS->DataSource=$data_r;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}
    public function viewRecord ($sender,$param) {        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);   
        $dataobat = $this->Obat->getInfoMasterObat($id);        
        $this->setInfoObat($dataobat);
        $this->modalInfoObat->show();
    }
    public function addProcess ($sender,$param) {
        $this->idProcess='add';   
        $this->cmbAddSatuanObat->DataSource=$this->DMaster->getListSatuanObat();
        $this->cmbAddSatuanObat->DataBind();
        
        $this->cmbAddGolonganObat->DataSource=$this->DMaster->getGolonganObat();
        $this->cmbAddGolonganObat->DataBind();
        
        $this->cmbAddBentukSediaan->DataSource=$this->DMaster->getListBentukSediaan ();
        $this->cmbAddBentukSediaan->DataBind();
        
        $this->cmbAddProdusen->DataSource=$this->DMaster->getListProdusen ();
        $this->cmbAddProdusen->DataBind();
        
        $this->listAddFarmakologi->DataSource=$this->DMaster->removeIdFromArray($this->DMaster->getListFarmakologi(),'none');
        $this->listAddFarmakologi->DataBind();

    }     
    public function checkKodeObat ($sender,$param) {
		$this->idProcess=$sender->getId()=='addKodeObat'?'add':'edit';
        $kode_obat=$param->Value;		
        if ($kode_obat != '') {
            try {   
                if ($this->hiddenkode_obat->Value!=$kode_obat) {                    
                    if ($this->DB->checkRecordIsExist('kode_obat','master_obat',$kode_obat)) {                                
                        throw new Exception ("Kode Obat ($kode_obat) sudah tidak tersedia silahkan ganti dengan yang lain.");		
                    }                               
                }                
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }	
    } 
    public function checkNoRegPOM ($sender,$param) {
		$this->idProcess=$sender->getId()=='addNoRegPOM'?'add':'edit';
        $no_reg=$param->Value;		
        if ($no_reg != '') {
            try {   
                if ($this->hiddenno_reg->Value!=$no_reg) {                    
                    if ($this->DB->checkRecordIsExist('no_reg','master_obat',$no_reg)) {                                
                        throw new Exception ("Nomor registrasi obat dari POM ($no_reg) sudah tidak tersedia silahkan ganti dengan yang lain.");		
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
            $noreg=addslashes($this->txtAddNoRegPOM->Text);
            $kode_obat=addslashes($this->txtAddKodeObat->Text);
            $nama_obat=addslashes(strtoupper($this->txtAddNamaObat->Text));            
            $merek_obat=addslashes($this->txtAddMerekObat->Text);
            $harga=$this->Obat->toInteger($this->txtAddHargaObat->Text);
            $idsatuanobat=$this->cmbAddSatuanObat->Text;            
            $idgolonganobat=$this->cmbAddGolonganObat->Text;            
            $idbentuk_sediaan=$this->cmbAddBentukSediaan->Text;            
            $nama_bentuk=addslashes($this->txtAddBentukSediaan->Text);            
            $idprodusen=$this->cmbAddProdusen->Text;                        
            $indices=$this->listAddFarmakologi->SelectedIndices;            
            $data_farmakologi=array();
            foreach($indices as $index) {
                $item=$this->listAddFarmakologi->Items[$index];
                $data_farmakologi[$item->Value]=$item->Text;                
            }
            $famakologi=json_encode($data_farmakologi);
            $komposisi=addslashes($this->txtAddKomposisi->Text);
            $kemasan=addslashes($this->txtAddKemasan->Text);
            $minimum_stock=$this->txtAddMinimumStock->Text;
            $maximum_stock=$this->txtAddMaksimumStock->Text;
            $str = "INSERT INTO master_obat(idobat,kode_obat,no_reg,nama_obat,nama_merek,harga,idsatuan_obat,idgolongan,idbentuk_sediaan,nama_bentuk,farmakologi,komposisi,kemasan,idprodusen,min_stock,max_stock,date_added,date_modified) VALUES (NULL,'$kode_obat','$noreg','$nama_obat','$merek_obat','$harga','$idsatuanobat','$idgolonganobat','$idbentuk_sediaan','$nama_bentuk','$famakologi','$komposisi','$kemasan','$idprodusen',$minimum_stock,$maximum_stock,NOW(),NOW())";            
            $this->DB->query('BEGIN');
            if ($this->DB->insertRecord($str)) {
                $idobat=$this->DB->getLastInsertID ();
                $puskesmas=$this->DMaster->removeIdFromArray($this->DMaster->getListPuskesmas (),'none');
                $jumlah_puskesmas=count($puskesmas);
                $i=0;
                foreach ($puskesmas as $k=>$v) {
                    $idpuskesmas=$k;
                    if ($jumlah_puskesmas > ($i+1))
                        $values = "$values (NULL,$idobat,$idpuskesmas,'$kode_obat','$noreg','$nama_obat','$merek_obat','$harga','$idsatuanobat','$idgolonganobat','$idbentuk_sediaan','$nama_bentuk','$famakologi','$komposisi','$kemasan','$idprodusen',$minimum_stock,$maximum_stock),";            
                    else
                        $values = "$values (NULL,$idobat,$idpuskesmas,'$kode_obat','$noreg','$nama_obat','$merek_obat','$harga','$idsatuanobat','$idgolonganobat','$idbentuk_sediaan','$nama_bentuk','$famakologi','$komposisi','$kemasan','$idprodusen',$minimum_stock,$maximum_stock)";            
                    $i+=1;
                }    
                $str = "INSERT INTO master_obat_puskesmas(idobat_puskesmas,idobat,idpuskesmas,kode_obat,no_reg,nama_obat,nama_merek,harga,idsatuan_obat,idgolongan,idbentuk_sediaan,nama_bentuk,farmakologi,komposisi,kemasan,idprodusen,min_stock,max_stock) VALUES $values";
                $this->DB->insertRecord($str);
                $this->DB->query('COMMIT');
                $this->redirect('dmaster.Obat',true);    
            }else{
                $this->DB->query('ROLLBACK');
            }
                    
        }
    }    
    public function editRecord ($sender,$param) {
        $this->idProcess='edit';        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);        
		$this->hiddenid->Value=$id;        
        $str = "SELECT idobat,kode_obat,no_reg,nama_obat,nama_merek,harga,idsatuan_obat,idgolongan,idbentuk_sediaan,nama_bentuk,farmakologi,komposisi,kemasan,idprodusen,min_stock,max_stock,enabled FROM master_obat WHERE idobat=$id";        
        $this->DB->setFieldTable(array('idobat','kode_obat','no_reg','nama_obat','nama_merek','harga','idsatuan_obat','idgolongan','idbentuk_sediaan','nama_bentuk','farmakologi','komposisi','kemasan','idprodusen','min_stock','max_stock','enabled'));
        $r=$this->DB->getRecord($str);   
        
        $this->txtEditKodeObat->Text=$r[1]['kode_obat'];
        $this->hiddenkode_obat->Value=$r[1]['kode_obat'];
        $this->txtEditNoRegPOM->Text=$r[1]['no_reg'];        
        $this->hiddenno_reg->Value=$r[1]['no_reg'];
        $this->txtEditNamaObat->Text=$r[1]['nama_obat'];            
        $this->txtEditMerekObat->Text=$r[1]['nama_merek'];   
        $this->txtEditHargaObat->Text=$this->Obat->toRupiah($r[1]['harga']);   
            
        $this->cmbEditSatuanObat->DataSource=$this->DMaster->getListSatuanObat();
        $this->cmbEditSatuanObat->Text=$r[1]['idsatuan_obat'];                                
        $this->cmbEditSatuanObat->DataBind();
        
        $this->cmbEditGolonganObat->DataSource=$this->DMaster->getGolonganObat();
        $this->cmbEditGolonganObat->Text=$r[1]['idgolongan'];                                            
        $this->cmbEditGolonganObat->DataBind();
        
        $this->cmbEditBentukSediaan->DataSource=$this->DMaster->getListBentukSediaan ();
        $this->cmbEditBentukSediaan->Text=$r[1]['idbentuk_sediaan'];
        $this->cmbEditBentukSediaan->DataBind();
        
        $this->txtEditBentukSediaan->Text=$r[1]['nama_bentuk'];
        
        $this->cmbEditProdusen->DataSource=$this->DMaster->getListProdusen ();
        $this->cmbEditProdusen->Text=$r[1]['idprodusen'];        
        $this->cmbEditProdusen->DataBind();
        
        $this->txtEditMinimumStock->Text=$r[1]['min_stock'];        
        $this->txtEditMaksimumStock->Text=$r[1]['max_stock'];        
        
        $this->listEditFarmakologi->DataSource=$this->DMaster->removeIdFromArray($this->DMaster->getListFarmakologi(),'none');        
        $this->listEditFarmakologi->DataBind();                
        $farmakologi=json_decode($r[1]['farmakologi'],true);
        $items=$this->listEditFarmakologi->Items;
        foreach($items as $item) {
            $item->selected=  array_key_exists($item->value,$farmakologi);
        }                
        $this->txtEditKomposisi->Text=$r[1]['komposisi'];
        $this->txtEditKemasan->Text=$r[1]['kemasan'];
        
        $this->cmbEditStatus->Text=$r[1]['enabled'];
        
    }
    public function updateData ($sender,$param) {
        if ($this->IsValid) {     
            $idobat=$this->hiddenid->Value;
            $noreg=addslashes($this->txtEditNoRegPOM->Text);
            $kode_obat=addslashes($this->txtEditKodeObat->Text);
            $nama_obat=addslashes(strtoupper($this->txtEditNamaObat->Text));            
            $merek_obat=addslashes($this->txtEditMerekObat->Text);
            $harga=$this->Obat->toInteger($this->txtEditHargaObat->Text);
            $idbentuk_sediaan=$this->cmbEditBentukSediaan->Text;            
            $idsatuanobat=$this->cmbEditSatuanObat->Text;            
            $idgolonganobat=$this->cmbEditGolonganObat->Text;            
            $nama_bentuk=addslashes($this->txtEditBentukSediaan->Text);            
            $idprodusen=$this->cmbEditProdusen->Text;                        
            $indices=$this->listEditFarmakologi->SelectedIndices;            
            $data_farmakologi=array();
            foreach($indices as $index) {
                $item=$this->listEditFarmakologi->Items[$index];
                $data_farmakologi[$item->Value]=$item->Text;                
            }
            $famakologi=json_encode($data_farmakologi);
            $komposisi=addslashes($this->txtEditKomposisi->Text);
            $kemasan=addslashes($this->txtEditKemasan->Text);
            $minimum_stock=$this->txtEditMinimumStock->Text;
            $maximum_stock=$this->txtEditMaksimumStock->Text;
            $status= $this->cmbEditStatus->Text;
            $str = "UPDATE master_obat SET kode_obat='$kode_obat',no_reg='$noreg',nama_obat='$nama_obat',nama_merek='$merek_obat',harga='$harga',idsatuan_obat='$idsatuanobat',idgolongan='$idgolonganobat',idbentuk_sediaan='$idbentuk_sediaan',nama_bentuk='$nama_bentuk',farmakologi='$famakologi',komposisi='$komposisi',kemasan='$kemasan',idprodusen='$idprodusen',min_stock=$minimum_stock,max_stock=$maximum_stock,date_modified=NOW(),enabled=$status WHERE idobat=$idobat";            
            $this->DB->updateRecord($str);
            $this->DB->query('BEGIN');
            if ($this->DB->updateRecord($str)) {
                $str = "UPDATE master_obat_puskesmas SET kode_obat='$kode_obat',no_reg='$noreg',nama_obat='$nama_obat',nama_merek='$merek_obat',harga='$harga',idsatuan_obat='$idsatuanobat',idgolongan='$idgolonganobat',idbentuk_sediaan='$idbentuk_sediaan',nama_bentuk='$nama_bentuk',farmakologi='$famakologi',komposisi='$komposisi',kemasan='$kemasan',idprodusen='$idprodusen' WHERE idobat=$idobat";            
                $this->DB->updateRecord($str);
                $this->DB->query('COMMIT');
                $this->redirect('dmaster.Obat',true);
            }else{
                $this->DB->query('ROLLBACK');
            }           
        }
    }    
    public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);        
        $this->DB->deleteRecord("master_obat WHERE idobat=$id");
        $this->redirect('dmaster.Obat',true);		        
	}
    public function printOut ($sender,$param) {        
        $this->createObj('report');             
        $this->report->setMode('excel2007');        
        $dataReport['linkoutput']=$this->linkOutput; 
        $this->report->setDataReport($dataReport);        
        $this->report->printDaftarObat ();          
        $this->lblPrintout->Text="Laporan Daftar Obat";
        $this->modalPrintOut->show();
    }
}
?>
