<?php
prado::using ('Application.MainPageAD');
class SBBKBaru extends MainPageAD {
    public static $totalQTY=0;
    public static $totalHARGA=0;
    public $datasbbk;
	public function onLoad($param) {			
		parent::onLoad($param);				
        $this->showSubMenuMutasiBarangKeluar = true;
		$this->showSBBKBaru = true;        
        $this->createObj('DMaster');
        $this->createObj('Obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {            
            if (isset($_SESSION['currentPageSBBKBaru']['datasbbk']['idsbbk_puskesmas'])) {
                $_SESSION['currentPageObat']['search']=false;                
                $this->detailProcess();                
                $this->populateData();
                $this->populateCart();
            }else {
                if (!isset($_SESSION['currentPageSBBKBaru'])||$_SESSION['currentPageSBBKBaru']['page_name']!='ad.mutasibarang.SBBKBaru') {
                    $_SESSION['currentPageSBBKBaru']=array('page_name'=>'ad.mutasibarang.SBBKBaru','page_num'=>0,'search'=>false,'datasbbk'=>array(),'cart'=>array());												
                }
            }			
		}	        
	}
    public function checkBuatSBBKBaru ($sender,$param) {        
        $no_lpo_unit=$param->Value;		
        $idpuskesmas=$this->idpuskesmas;
        if ($no_lpo_unit != '') {
            try {             
                if ($this->DB->checkRecordIsExist('no_lpo_unit','master_sbbk_puskesmas',$no_lpo_unit," AND idpuskesmas=$idpuskesmas")) {                                
                    throw new Exception ("Nomor LPO ($no_lpo_unit) sudah dibuatkan SBBK-nya, silahkan dilihat di Daftar SBBK.");		
                }
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }
    }
    public function buatSBBK($sender,$param) {
        if ($this->IsValid) {
            $no_lpo_unit = addslashes(trim($this->txtNoLPOSBBKBaru->Text));      
            $ta=$_SESSION['ta'];
            $tanggal_sbbk_puskesmas=Date("$ta-m-d");            
            $idpuskesmas=$this->idpuskesmas;            
            
            $this->DB->insertRecord("INSERT INTO master_sbbk_puskesmas (no_lpo_unit,tanggal_sbbk_puskesmas,idpuskesmas,tahun_puskesmas,date_added,date_modified) VALUES ('$no_lpo_unit','$tanggal_sbbk_puskesmas',$idpuskesmas,$ta,NOW(),NOW())");            
            
            $idsbbk_puskesmas=$this->DB->getLastInsertID ();
            $datasbbk=array('idsbbk_puskesmas'=>$idsbbk_puskesmas,'tanggal_sbbk_puskesmas'=>$tanggal_sbbk_puskesmas,'no_lpo_unit'=>$no_lpo_unit,'tahun_puskesmas'=>$ta,'mode'=>'buat','issaved'=>false);            
            $_SESSION['currentPageSBBKBaru']['datasbbk']=$datasbbk;  

            $this->redirect('mutasibarang.SBBKBaru',true);
        }
    }
    public function checkUbahSBBKBaru ($sender,$param) {        
        $no_sbbk=$param->Value;		
        if ($no_sbbk != '') {
            try {                                         
                $str = "SELECT status_puskesmas FROM master_sbbk_puskesmas WHERE no_sbbk_puskesmas='$no_sbbk'";
                $this->DB->setFieldTable(array('status_puskesmas'));
                $r=$this->DB->getRecord($str); 
                if (isset($r[1]) ){
                    if ($r[1]['status_puskesmas'] == 'complete')
                        throw new Exception ("Nomor SBBK ($no_sbbk) statusnya sudah complete, jadi datanya tidak bisa diubah.");		
                }else{
                    throw new Exception ("Nomor SBBK ($no_sbbk) tidak ada di database silahkan ganti dengan yang lain.");		
                }
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }
    }
    public function ubahSBBK($sender,$param) {
        if ($this->IsValid) {
            $no_sbbk=addslashes($this->txtNoLPOSBBKBaru->Text);
            $str = "SELECT idsbbk_puskesmas,no_sbbk_puskesmas,tanggal_sbbk_puskesmas,idpuskesmas,idunitpuskesmas,nama_unit,idlpo_unit,no_lpo_unit,tanggal_lpo_unit,keperluan_unit,no_spmb_puskesmas,nip_ka_gudang_puskesmas,nama_ka_gudang_puskesmas,nip_pengemas_puskesmas,nama_pengemas_puskesmas FROM master_sbbk_puskesmas WHERE no_sbbk_puskesmas='$no_sbbk'";            
            $this->DB->setFieldTable(array('idsbbk_puskesmas','no_sbbk_puskesmas','tanggal_sbbk_puskesmas','idpuskesmas','idunitpuskesmas','nama_unit','idlpo_unit','no_lpo_unit','tanggal_lpo_unit','keperluan_unit','no_spmb_puskesmas','nip_ka_gudang_puskesmas','nama_ka_gudang_puskesmas','nip_pengemas_puskesmas','nama_pengemas_puskesmas'));
            $datasbbk=$this->DB->getRecord($str);        

            $_SESSION['currentPageSBBKBaru']['idprodusen']='none';
            $_SESSION['currentPageSBBKBaru']['datasbbk']=$datasbbk[1];
            $_SESSION['currentPageSBBKBaru']['datasbbk']['issaved']=true;
            $_SESSION['currentPageSBBKBaru']['datasbbk']['mode']='buat';        

            $idsbbk_puskesmas=$datasbbk[1]['idsbbk_puskesmas'];            
            $str = "SELECT dsb.iddetail_sbbm_puskesmas,dsb.idobat,dsb.idobat_puskesmas,dsb.kode_obat,dsb.nama_obat,dsb.harga,dsb.kemasan,mop.stock,dsb.stock_awal_unit,dsb.penerimaan_unit AS total_penerimaan,dsb.persediaan_unit,dsb.pemakaian_unit AS total_pemakaian,dsb.stock_akhir_unit,dsb.permintaan_unit,dsb.pemberian_puskesmas FROM detail_sbbk_puskesmas dsb LEFT JOIN master_obat_puskesmas mop ON (mop.idobat_puskesmas=dsb.idobat_puskesmas) WHERE idsbbk_puskesmas=$idsbbk_puskesmas";        
            $this->DB->setFieldTable(array('iddetail_sbbm_puskesmas','idobat','idobat_puskesmas','kode_obat','nama_obat','harga','kemasan','stock','stock_awal_unit','total_penerimaan','persediaan_unit','stock_akhir_unit','total_pemakaian','permintaan_unit','pemberian_puskesmas'));
            $r=$this->DB->getRecord($str);               
            $cart = array();
            if (isset($r[1])) {
                while (list($k,$v)=each($r)) {
                    $cart[$v['iddetail_sbbm_puskesmas']]=$v;
                }
            }
            $_SESSION['currentPageSBBKBaru']['cart']=$cart;            
            $this->redirect('mutasibarang.SBBKBaru',true);
        }
    }
    public function detailProcess() {        
        $idpuskesmas=$this->idpuskesmas;
        $this->datasbbk = $_SESSION['currentPageSBBKBaru']['datasbbk'];         
        if ($this->datasbbk['mode'] == 'buat') {
            $this->idProcess='add';                                    
            $this->cmbAddTanggalSBBK->Text=$this->TGL->tanggal('d-m-Y',$this->datasbbk['tanggal_sbbk_puskesmas']);
            $daftar_unit=$this->DMaster->getList("unitpuskesmas WHERE idpuskesmas=$idpuskesmas AND enabled=1",array('idunitpuskesmas','nama_unit'),'nama_unit',null,1);
            $daftar_unit['none']='Daftar Unit Puskesmas';
            $this->cmbAddUnit->DataSource=$daftar_unit;
            $this->cmbAddUnit->DataBind();
            $this->txtAddNoLPO->Text=$this->datasbbk['no_lpo_unit'];
            $this->hiddenno_lpo_unit->Value=$this->datasbbk['no_lpo_unit'];
            if ($this->datasbbk['issaved']) {                
                $this->txtAddNoSBBK->Text=$this->datasbbk['no_sbbk_puskesmas'];                
                $this->cmbAddUnit->Text=$this->datasbbk['idunitpuskesmas'];                
                $this->txtAddKeperluan->Text=$this->datasbbk['keperluan_unit'];
                $this->txtAddNoSPMB->Text=$this->datasbbk['no_spmb_puskesmas'];  
                $this->cmbAddTanggalLPO->Text=$this->TGL->tanggal('d-m-Y',$this->datasbbk['tanggal_lpo_unit']);                
                $this->hiddenno_sbbk->Value=$this->datasbbk['no_sbbk_puskesmas'];                
                $this->hiddenno_spmb->Value=$this->datasbbk['no_spmb_puskesmas'];
            }
        }      
    }
    public function renderCallback ($sender,$param) {
        $this->idProcess=$_SESSION['currentPageSBBKBaru']['datasbbk']['mode']=='buat'?'add':'edit';
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageSBBKBaru']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageSBBKBaru']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageSBBKBaru']['search']=true;
        $this->populateData($_SESSION['currentPageSBBKBaru']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageSBBKBaru']['idprodusen']=$this->cmbFilterProdusen->Text;
        $this->populateData();
	}    
    protected function populateCart () {             
        $this->idProcess='add';        
        $cart = $_SESSION['currentPageSBBKBaru']['cart'];          
		$this->RepeaterCart->DataSource=$cart;
		$this->RepeaterCart->dataBind();             
	}
    public function simpanProductRepeater($sender,$param) {        
        if ($this->IsValid) {                        
            $iddetail_sbbm_puskesmas=$this->getDataKeyField($sender,$this->RepeaterCart);                        
            $idobat_puskesmas=$sender->CommandParameter;
            $dataobat=$this->Obat->getInfoMasterObatPuskesmas($idobat_puskesmas);            
            $obj=$sender->getNamingContainer();
            $qty=$obj->txtQTY->getText();                                  
            $stock=$dataobat['stock'];
            if ($qty <= $stock) {                                
                $_SESSION['currentPageSBBKBaru']['cart'][$iddetail_sbbm_puskesmas]['stock']=$stock;
                $_SESSION['currentPageSBBKBaru']['cart'][$iddetail_sbbm_puskesmas]['pemberian_puskesmas']=$qty;                
            }          
            $this->redirect('mutasibarang.SBBKBaru',true);
        }
    }
    public function addProductRepeater($sender,$param) {        
        if ($this->IsValid) {         
            $iddetail_sbbm_puskesmas = $this->getDataKeyField($sender,$this->RepeaterS);
            $str = "SELECT dsp.iddetail_sbbm_puskesmas,idobat_puskesmas,idobat,kode_obat,nama_obat,harga,kemasan,tanggal_expire,(dsp.qty-IFNULL(ksp_keluar.jumlah_keluar,0)) AS stock FROM detail_sbbm_puskesmas dsp LEFT JOIN (SELECT iddetail_sbbm_puskesmas,COUNT(idkartu_stock_puskesmas) AS jumlah_keluar FROM kartu_stock_puskesmas WHERE mode_puskesmas='keluar' AND isdestroyed=0 GROUP BY iddetail_sbbm_puskesmas) AS ksp_keluar ON (ksp_keluar.iddetail_sbbm_puskesmas=dsp.iddetail_sbbm_puskesmas) WHERE (dsp.qty-IFNULL(ksp_keluar.jumlah_keluar,0)) > 0 AND dsp.iddetail_sbbm_puskesmas=$iddetail_sbbm_puskesmas";        
            $this->DB->setFieldTable(array('iddetail_sbbm_puskesmas','idobat_puskesmas','idobat','kode_obat','nama_obat','harga','kemasan','tanggal_expire','stock'));            
            $r=$this->DB->getRecord($str); 
            $dataobat=$r[1];
            $obj=$sender->getNamingContainer();
            $qty=$obj->txtQTY->getText();                        
            $stock=$dataobat['stock'];                        
            if ($qty <= $stock) {
                $_SESSION['currentPageSBBKBaru']['cart'][$iddetail_sbbm_puskesmas]=array(
                                                                        'iddetail_sbbm_puskesmas'=>$iddetail_sbbm_puskesmas,                                                                        
                                                                        'idobat_puskesmas'=>$dataobat['idobat_puskesmas'],
                                                                        'idobat'=>$dataobat['idobat'],
                                                                        'kode_obat'=>$dataobat['kode_obat'],
                                                                        'nama_obat'=>$dataobat['nama_obat'],
                                                                        'harga'=>$dataobat['harga'],                                                                    
                                                                        'kemasan'=>$dataobat['kemasan'],
                                                                        'stock'=>$stock,
                                                                        'total_pemakaian'=>0,                    
                                                                        'permintaan_unit'=>0,     
                                                                        'stock_akhir_unit'=>0,                                                  
                                                                        'islpo_unit'=>false,
                                                                        'pemberian_puskesmas'=>$qty);
                $this->redirect('mutasibarang.SBBKBaru',true);
            }
        }
    }
    public function getQTYFromCart($idddetail_sbbm_puskesmas){
        $cart=$_SESSION['currentPageSBBKBaru']['cart'];
        if (isset($cart[$idddetail_sbbm_puskesmas])) {
            return $cart[$idddetail_sbbm_puskesmas]['pemberian_puskesmas'];
        }else{
            return 0;
        }
        
    }
    public function clearCart ($sender,$param) {
        $_SESSION['currentPageSBBKBaru']['cart']=array();
        $this->redirect('mutasibarang.SBBKBaru',true);
    }
    public function deleteItem($sender,$param) {
        $id=$this->getDataKeyField($sender,$this->RepeaterCart);
        unset($_SESSION['currentPageSBBKBaru']['cart'][$id]);        
        $this->populateCart();
        $this->populateData();
    }
    public function itemCreated($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {            
            if (!($item->DataItem['idobat_puskesmas'] > 0)) {
                $item->btnAddProductRepeater->Enabled=false;                
                $item->btnAddProductRepeater->CssClass='table-link disabled';
            }
        }
    }
    public function itemCreatedCart($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {                        
            if ($item->DataItem['islpo_unit']) {                
                $item->btnDelete->Enabled=false;
                $item->btnDelete->CssClass='table-link disabled';
            }else{
                $item->btnDelete->Attributes->OnClick="if(!confirm('Anda ingin menghapus item ini ?')) return false;";
            }            
            if ($item->DataItem['pemberian_puskesmas'] > 0) {
                SBBKBaru::$totalQTY += $item->DataItem['pemberian_puskesmas'];
                $harga=$item->DataItem['pemberian_puskesmas']*$item->DataItem['harga']; 
                SBBKBaru::$totalHARGA += $harga;            
            }
            
        }
    }
    protected function populateData ($search=false) {             
        $str = "SELECT dsp.iddetail_sbbm_puskesmas,idobat_puskesmas,kode_obat,nama_obat,harga,kemasan,tanggal_expire,(dsp.qty-IFNULL(ksp_keluar.jumlah_keluar,0)) AS stock FROM detail_sbbm_puskesmas dsp LEFT JOIN (SELECT iddetail_sbbm_puskesmas,COUNT(idkartu_stock_puskesmas) AS jumlah_keluar FROM kartu_stock_puskesmas WHERE mode_puskesmas='keluar' AND isdestroyed=0 GROUP BY iddetail_sbbm_puskesmas) AS ksp_keluar ON (ksp_keluar.iddetail_sbbm_puskesmas=dsp.iddetail_sbbm_puskesmas) WHERE (dsp.qty-IFNULL(ksp_keluar.jumlah_keluar,0)) > 0";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa=" AND kode_obat='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("detail_sbbm_puskesmas dsp LEFT JOIN (SELECT iddetail_sbbm_puskesmas,COUNT(idkartu_stock_puskesmas) AS jumlah_keluar FROM kartu_stock_puskesmas WHERE mode_puskesmas='keluar' AND isdestroyed=0 GROUP BY iddetail_sbbm_puskesmas) AS ksp_keluar ON (ksp_keluar.iddetail_sbbm_puskesmas=dsp.iddetail_sbbm_puskesmas) WHERE (dsp.qty-IFNULL(ksp_keluar.jumlah_keluar,0)) > 0 $cluasa",'dsp.iddetail_sbbm_puskesmas');
                    $str = "$str $cluasa";
                break;
                case 'nama' :
                    $cluasa=" AND nama_obat LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("detail_sbbm_puskesmas dsp LEFT JOIN (SELECT iddetail_sbbm_puskesmas,COUNT(idkartu_stock_puskesmas) AS jumlah_keluar FROM kartu_stock_puskesmas WHERE mode_puskesmas='keluar' AND isdestroyed=0 GROUP BY iddetail_sbbm_puskesmas) AS ksp_keluar ON (ksp_keluar.iddetail_sbbm_puskesmas=dsp.iddetail_sbbm_puskesmas) WHERE (dsp.qty-IFNULL(ksp_keluar.jumlah_keluar,0)) > 0 $cluasa",'dsp.iddetail_sbbm_puskesmas');
                    $str = "$str $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ("detail_sbbm_puskesmas dsp LEFT JOIN (SELECT iddetail_sbbm_puskesmas,COUNT(idkartu_stock_puskesmas) AS jumlah_keluar FROM kartu_stock_puskesmas WHERE mode_puskesmas='keluar' AND isdestroyed=0 GROUP BY iddetail_sbbm_puskesmas) AS ksp_keluar ON (ksp_keluar.iddetail_sbbm_puskesmas=dsp.iddetail_sbbm_puskesmas) WHERE (dsp.qty-IFNULL(ksp_keluar.jumlah_keluar,0)) > 0",'dsp.iddetail_sbbm_puskesmas');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageSBBKBaru']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageSBBKBaru']['page_num']=0;}
        $str = "$str ORDER BY tanggal_expire ASC,nama_obat ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('iddetail_sbbm_puskesmas','idobat_puskesmas','kode_obat','nama_obat','harga','kemasan','tanggal_expire','stock'));
		$r=$this->DB->getRecord($str,$offset+1);                     
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}
    public function viewRecord ($sender,$param) {                
        $id=$sender->CommandParameter;   
        $dataobat = $this->Obat->getInfoMasterObat($id);        
        $this->setInfoObat($dataobat);
        $this->modalInfoObat->show();
    }
    public function checkNoSBBK ($sender,$param) {
		$this->idProcess=$sender->getId()=='addNoSBBK'?'add':'edit';
        $idpuskesmas=$this->idpuskesmas;
        $no_sbbk_puskesmas=$param->Value;		
        if ($no_sbbk_puskesmas != '') {
            try {   
                if ($this->hiddenno_sbbk->Value!=$no_sbbk_puskesmas) {
                    if ($this->DB->checkRecordIsExist('no_sbbk_puskesmas','master_sbbk_puskesmas',$no_sbbk_puskesmas," AND idpuskesmas=$idpuskesmas")) {                                
                        throw new Exception ("Nomor SBBK ($no_sbbk_puskesmas) sudah tidak tersedia silahkan ganti dengan yang lain.");		
                    }                               
                }
                                
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }	
    }
    public function checkNoLPO ($sender,$param) {   
        $this->idProcess=$sender->getId()=='addNoLPO'?'add':'edit';
        $no_lpo_unit=$param->Value;		
        $idpuskesmas=$this->idpuskesmas;
        if ($no_lpo_unit != '') {
            try {   
                if ($this->hiddenno_lpo_unit->Value!=$no_lpo_unit) {
                    if ($this->DB->checkRecordIsExist('no_lpo_unit','master_sbbk_puskesmas',$no_lpo_unit," AND idpuskesmas=$idpuskesmas")) {                                
                        throw new Exception ("Nomor LPO ($no_lpo_unit) sudah dibuatkan SBBK-nya, silahkan dilihat di Daftar SBBK.");		
                    }
                }
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }
    }
    public function checkNoSPMB ($sender,$param) {
		$this->idProcess=$sender->getId()=='addNoSPMB'?'add':'edit';
        $no_spmb_puskesmas=$param->Value;		
        $idpuskesmas=$this->idpuskesmas;
        if ($no_spmb_puskesmas != '') {
            try {   
                if ($this->hiddenno_spmb->Value!=$no_spmb_puskesmas) {
                    if ($this->DB->checkRecordIsExist('no_spmb_puskesmas','master_sbbk_puskesmas',$no_spmb_puskesmas," AND idpuskesmas=$idpuskesmas")) {                                
                        throw new Exception ("Nomor SPMB ($no_spmb_puskesmas) sudah tidak tersedia silahkan ganti dengan yang lain.");		
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
            $datasbbk=$_SESSION['currentPageSBBKBaru']['datasbbk'];
            $idsbbk_puskesmas=$datasbbk['idsbbk_puskesmas'];            
            $nosbbk_puskesmas=addslashes($this->txtAddNoSBBK->Text);
            $tanggal_sbbk_puskesmas = date('Y-m-d',$this->cmbAddTanggalSBBK->TimeStamp);  
            $idunitpuskesmas = $this->cmbAddUnit->Text;
            $daftar_unit=$this->DMaster->getList("unitpuskesmas WHERE idunitpuskesmas=$idunitpuskesmas",array('nama_unit'));
            $nama_unit=$daftar_unit[1]['nama_unit'];
            $tanggal_lpo_unit = date('Y-m-d',$this->cmbAddTanggalLPO->TimeStamp);            
            $nolpo_unit=$this->txtAddNoLPO->Text;
            $keperluan_unit=addslashes($this->txtAddKeperluan->Text);
            $nospmb_puskesmas=addslashes($this->txtAddNoSPMB->Text);
            
            $this->DB->query('BEGIN');
            $str = "UPDATE master_sbbk_puskesmas SET no_sbbk_puskesmas='$nosbbk_puskesmas',no_spmb_puskesmas='$nospmb_puskesmas',tanggal_sbbk_puskesmas='$tanggal_sbbk_puskesmas',idunitpuskesmas=$idunitpuskesmas,nama_unit='$nama_unit',tanggal_lpo_unit='$tanggal_lpo_unit',no_lpo_unit='$nolpo_unit',keperluan_unit='$keperluan_unit',status_puskesmas='draft',date_modified=NOW() WHERE idsbbk_puskesmas=$idsbbk_puskesmas";
            if ($this->DB->updateRecord($str)) {    
                $datasbbk['issaved']=true;                                        
                $datasbbk['no_sbbk_puskesmas']=$nosbbk_puskesmas;
                $datasbbk['tanggal_sbbk_puskesmas']=$tanggal_sbbk_puskesmas;
                $datasbbk['idunitpuskesmas']=$idunitpuskesmas;
                $datasbbk['tanggal_lpo_unit']=$tanggal_lpo_unit;
                $datasbbk['no_lpo_unit_unit']=$nolpo_unit;        
                $datasbbk['keperluan_unit']=$keperluan_unit;
                $datasbbk['no_spmb_puskesmas']=$nospmb_puskesmas;                
                $_SESSION['currentPageSBBKBaru']['datasbbk']=$datasbbk;
                $cart=$_SESSION['currentPageSBBKBaru']['cart'];
                if (count($cart) > 0) {                    
                    $this->DB->deleteRecord("detail_sbbk_puskesmas WHERE idsbbk_puskesmas=$idsbbk_puskesmas");
                    foreach ($cart as $iddetail_sbbm_puskesmas=>$v) {                                                
                        $qty=$v['pemberian_puskesmas'];                        
                        $str = "INSERT INTO detail_sbbk_puskesmas (idsbbk_puskesmas,iddetail_sbbm_puskesmas,idobat,idobat_puskesmas,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,no_batch,pemberian_puskesmas,islpo_unit,date_added,date_modified) SELECT $idsbbk_puskesmas,$iddetail_sbbm_puskesmas,idobat,idobat_puskesmas,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,no_batch,'$qty',0,NOW(),NOW() FROM detail_sbbm_puskesmas WHERE iddetail_sbbm_puskesmas=$iddetail_sbbm_puskesmas";                                                
                        $this->DB->insertRecord($str);                        
                    }
                }
                $this->DB->query('COMMIT');
                $this->redirect('mutasibarang.SBBKBaru',true);
            }else{
                $this->DB->query('ROLLBACK');
            }            
        }
    }
    public function checkOut ($sender,$param) {
        if ($this->IsValid) {                       
            $datasbbk=$_SESSION['currentPageSBBKBaru']['datasbbk'];
            $idsbbk_puskesmas=$datasbbk['idsbbk_puskesmas'];            
            $nosbbk_puskesmas=addslashes($this->txtAddNoSBBK->Text);
            $tanggal_sbbk_puskesmas = date('Y-m-d',$this->cmbAddTanggalSBBK->TimeStamp);  
            $idunitpuskesmas = $this->cmbAddUnit->Text;
            $daftar_unit=$this->DMaster->getList("unitpuskesmas WHERE idunitpuskesmas=$idunitpuskesmas",array('nama_unit'));
            $nama_unit=$daftar_unit[1]['nama_unit'];
            $tanggal_lpo_unit = date('Y-m-d',$this->cmbAddTanggalLPO->TimeStamp);            
            $nolpo_unit=$this->txtAddNoLPO->Text;
            $keperluan_unit=addslashes($this->txtAddKeperluan->Text);
            $nospmb_puskesmas=addslashes($this->txtAddNoSPMB->Text);
            
            $this->DB->query('BEGIN');
            $str = "UPDATE master_sbbk_puskesmas SET no_sbbk_puskesmas='$nosbbk_puskesmas',no_spmb_puskesmas='$nospmb_puskesmas',tanggal_sbbk_puskesmas='$tanggal_sbbk_puskesmas',idunitpuskesmas=$idunitpuskesmas,nama_unit='$nama_unit',tanggal_lpo_unit='$tanggal_lpo_unit',no_lpo_unit='$nolpo_unit',keperluan_unit='$keperluan_unit',nip_penerima_unit='1991920513',nama_penerima_unit='sistem',tanggal_diterima_unit='$tanggal_sbbk_puskesmas',status_puskesmas='complete',response_sbbk_puskesmas=5,date_modified=NOW() WHERE idsbbk_puskesmas=$idsbbk_puskesmas";
                        
            $userid=$this->Pengguna->getDataUser('userid');
            $username=$this->Pengguna->getDataUser('username');
            $nama=$this->Pengguna->getDataUser('nama');
            
            if ($this->DB->updateRecord($str)) {    
                $datasbbk['issaved']=true;                                        
                $datasbbk['no_sbbk_puskesmas']=$nosbbk_puskesmas;
                $datasbbk['tanggal_sbbk_puskesmas']=$tanggal_sbbk_puskesmas;
                $datasbbk['keperluan_unit']=$keperluan_unit;
                $datasbbk['no_spmb_puskesmas']=$nospmb_puskesmas;                
                $_SESSION['currentPageSBBKBaru']['datasbbk']=$datasbbk;
                $cart=$_SESSION['currentPageSBBKBaru']['cart'];
                if (count($cart) > 0) {                    
                    $this->DB->deleteRecord("detail_sbbk_puskesmas WHERE idsbbk_puskesmas=$idsbbk_puskesmas");
                    $bulan=$this->TGL->tanggal('m',$tanggal_sbbk_puskesmas);
                    $tahun=$_SESSION['ta'];
                    foreach ($cart as $iddetail_sbbm_puskesmas=>$v) {                               
                        $idobat_puskesmas=$v['idobat_puskesmas']; 
                        $qty=$v['pemberian_puskesmas'];                       
                        $str = "INSERT INTO detail_sbbk_puskesmas (idsbbk_puskesmas,iddetail_sbbm_puskesmas,idobat,idobat_puskesmas,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,no_batch,pemberian_puskesmas,islpo_unit,date_added,date_modified) SELECT $idsbbk_puskesmas,$iddetail_sbbm_puskesmas,idobat,idobat_puskesmas,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,no_batch,'$qty',0,NOW(),NOW() FROM detail_sbbm_puskesmas WHERE iddetail_sbbm_puskesmas=$iddetail_sbbm_puskesmas";                                                
                        $this->DB->insertRecord($str);
                        $iddetail_sbbk_puskesmas=$this->DB->getLastInsertID ();
                        $this->DB->updateRecord("UPDATE master_obat_puskesmas SET stock=stock-$qty WHERE idobat_puskesmas=$idobat_puskesmas");
                        $str = "UPDATE kartu_stock_puskesmas ks,(SELECT idkartu_stock_puskesmas FROM kartu_stock_puskesmas WHERE mode_puskesmas='masuk' AND isdestroyed=0 AND iddetail_sbbm_puskesmas=$iddetail_sbbm_puskesmas LIMIT $qty) AS temp SET idsbbk_puskesmas=$idsbbk_puskesmas,iddetail_sbbk_puskesmas=$iddetail_sbbk_puskesmas,mode_puskesmas='keluar',date_modified=NOW() WHERE ks.idkartu_stock_puskesmas=temp.idkartu_stock_puskesmas";                        
                        $this->DB->updateRecord($str);
                        $str = "INSERT INTO log_ks_puskesmas (idpuskesmas,idobat,idobat_puskesmas,idsbbk_puskesmas,iddetail_sbbk_puskesmas,tanggal_puskesmas,bulan_puskesmas,tahun_puskesmas,qty_puskesmas,sisa_stock_puskesmas,keterangan_puskesmas,mode_puskesmas,userid_puskesmas,username_puskesmas,nama_user_puskesmas,date_added) SELECT idpuskesmas,idobat,idobat_puskesmas,$idsbbk_puskesmas,$iddetail_sbbk_puskesmas,'$tanggal_sbbk_puskesmas','$bulan',$tahun,$qty,sisa_stock_puskesmas-$qty,'Barang keluar dengan no.sbbk ($nosbbk_puskesmas) dikirim ke puskesmas $nama_unit sebanyak $qty','keluar',$userid,'$username','$nama',NOW() FROM log_ks_puskesmas WHERE idobat_puskesmas=$idobat_puskesmas ORDER BY idlog_puskesmas DESC LIMIT 1";
                        $this->DB->insertRecord($str);
                    }
                }        
                $_SESSION['currentPageDaftarSBBK']['datasbbk']=$datasbbk;
                $_SESSION['currentPageDaftarSBBK']['cart']=$cart;
                $this->DB->query('COMMIT');
                unset($_SESSION['currentPageSBBKBaru']['datasbbk']);
                unset($_SESSION['currentPageSBBKBaru']['cart']);
                $this->redirect('mutasibarang.DaftarSBBK',true);
            }else{
                $this->DB->query('ROLLBACK');
            }            
        }
    }    
    public function batalSBBK ($sender,$param) {
		$id=$_SESSION['currentPageSBBKBaru']['datasbbk']['idsbbk_puskesmas'];        
        $this->DB->deleteRecord("master_sbbk_puskesmas WHERE idsbbk_puskesmas=$id");
        unset($_SESSION['currentPageSBBKBaru']['datasbbk']);
        unset($_SESSION['currentPageSBBKBaru']['cart']);
        $this->redirect('mutasibarang.SBBKBaru',true);
	}
    public function closeSBBK ($sender,$param) {
        unset($_SESSION['currentPageSBBKBaru']['datasbbk']);
        unset($_SESSION['currentPageSBBKBaru']['cart']);
        $this->redirect('mutasibarang.SBBKBaru',true);
    }
    
}
?>
