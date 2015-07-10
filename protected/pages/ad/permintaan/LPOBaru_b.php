<?php
prado::using ('Application.MainPageAD');
class LPOBaru extends MainPageAD {
    public static $totalQTY=0;
    public static $totalHARGA=0;
    public $datalpo;
	public function onLoad($param) {			
		parent::onLoad($param);				        
		$this->showLPOBaru = true;        
        $this->createObj('DMaster');
        $this->createObj('Obat');                
		if (!$this->IsPostBack&&!$this->IsCallBack) {            
            if (isset($_SESSION['currentPageLPOBaru']['datalpo']['no_lpo'])) {
                $_SESSION['currentPageObat']['search']=false;
                $this->cmbFilterProdusen->DataSource=$this->DMaster->getListProdusen ();
                $this->cmbFilterProdusen->Text=$_SESSION['currentPageObat']['idprodusen'];
                $this->cmbFilterProdusen->DataBind();                
                $this->detailProcess();                
                $this->populateData();
                $this->populateCart();
            }else {
                if (!isset($_SESSION['currentPageLPOBaru'])||$_SESSION['currentPageLPOBaru']['page_name']!='ad.permintaan.LPOBaru') {
                    $_SESSION['currentPageLPOBaru']=array('page_name'=>'ad.permintaan.LPOBaru','page_num'=>0,'search'=>false,'datalpo'=>array(),'idprodusen'=>'none','cart'=>array(),'tanggal'=>date('Y-m-d'),'status_lpo'=>'complete');												
                }
            }			
		}	        
	}    
    public function checkBuatLPOBaru ($sender,$param) {        
        $no_lpo=$param->Value;		
        $idpuskesmas=$this->idpuskesmas;
        if ($no_lpo != '') {
            try {                                 
                if ($this->DB->checkRecordIsExist('no_lpo','master_lpo',$no_lpo," AND idpuskesmas=$idpuskesmas")) {                                
                    throw new Exception ("Nomor LPO ($no_lpo) sudah tidak tersedia silahkan ganti dengan yang lain.");		
                }               
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }
    }
    public function buatLPO($sender,$param) {
        if ($this->IsValid) {
            $nolpo = addslashes(trim($this->txtNoLPOBaru->Text));      
            $idpuskesmas = $this->idpuskesmas;
            $nip_ka = $this->Pengguna->getDataUser('nip_ka');
            $nama_ka = $this->Pengguna->getDataUser('nama_ka');
            $nip_pengelola = $this->Pengguna->getDataUser('nip_pengelola_obat');
            $nama_pengelola = $this->Pengguna->getDataUser('nama_pengelola_obat');
            
            $nip_kadis=$this->setup->getSettingValue('nip_kadis');        
            $nama_kadis=$this->setup->getSettingValue('nama_kadis');        
            $nip_ka_gudang=$this->setup->getSettingValue('nip_ka_gudang');        
            $nama_ka_gudang=$this->setup->getSettingValue('nama_ka_gudang');        
            
            $ta=$_SESSION['ta'];
            $tanggal_lpo=Date("$ta-m-d");
            $this->DB->insertRecord("INSERT INTO master_lpo (no_lpo,idpuskesmas,tanggal_lpo,nip_ka,nama_ka,nip_pengelola_obat,nama_pengelola_obat,nip_kadis,nama_kadis,nip_ka_gudang,nama_ka_gudang,tahun,date_added,date_modified) VALUES ('$nolpo',$idpuskesmas,'$tanggal_lpo','$nip_ka','$nama_ka','$nip_pengelola','$nama_pengelola','$nip_kadis','$nama_kadis','$nip_ka_gudang','$nama_ka_gudang',$ta,NOW(),NOW())");            
            $idlpo=$this->DB->getLastInsertID ();            
            $_SESSION['currentPageLPOBaru']['datalpo']=array('idlpo'=>$idlpo,'no_lpo'=>$nolpo,'idpuskesmas'=>$idpuskesmas,'nip_ka'=>$nip_ka,'nama_ka'=>$nama_ka,'nip_pengelola'=>$nip_pengelola,'nama_pengelola'=>$nama_pengelola,'nip_kadis'=>$nip_kadis,'nama_kadis'=>$nama_kadis,'nip_ka_gudang'=>$nip_ka_gudang,'nama_ka_gudang'=>$nama_ka_gudang,'mode'=>'buat','issaved'=>false,'response_lpo'=>1);
            $str= "INSERT INTO detail_lpo (idlpo,idobat,idobat_puskesmas,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan) SELECT $idlpo,idobat,idobat_puskesmas,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan FROM master_obat_puskesmas WHERE idpuskesmas=$idpuskesmas";
            $this->DB->insertRecord($str);
            $this->redirect('permintaan.LPOBaru',true);
        }
    }
    public function checkUbahLPOBaru ($sender,$param) {        
        $no_lpo=$param->Value;		
        $idpuskeskmas=$this->idpuskesmas;
        if ($no_lpo != '') {
            try {                                 
                if (!$this->DB->checkRecordIsExist('no_lpo','master_lpo',$no_lpo," AND idpuskesmas=$idpuskesmas")) {                                
                    
                } 
                $str = "SELECT status FROM master_lpo WHERE no_lpo='$no_lpo' AND idpuskesmas=$idpuskeskmas";
                $this->DB->setFieldTable(array('status'));
                $r=$this->DB->getRecord($str); 
                if (isset($r[1]) ){
                    if ($r[1]['status'] == 'complete')
                        throw new Exception ("Nomor LPO ($no_lpo) statusnya sudah complete, jadi datanya tidak bisa diubah.");		
                }else{
                    throw new Exception ("Nomor LPO ($no_lpo) tidak ada di database silahkan ganti dengan yang lain.");		
                }
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }
    }
    public function ubahLPO($sender,$param) {
        if ($this->IsValid) {
            $no_lpo=addslashes($this->txtNoLPOBaru->Text);
            $str = "SELECT ml.idlpo,ml.no_lpo,ml.idpuskesmas,ml.tanggal_lpo,jumlah_kunjungan_gratis,jumlah_kunjungan_bayar,jumlah_kunjungan_bpjs,ml.nip_ka,ml.nama_ka,response_lpo FROM master_lpo ml WHERE ml.no_lpo='$no_lpo'";            
            $this->DB->setFieldTable(array('idlpo','no_lpo','idpuskesmas','tanggal_lpo','jumlah_kunjungan_gratis','jumlah_kunjungan_bayar','jumlah_kunjungan_bpjs','nip_ka','nama_ka','response_lpo'));
            $r=$this->DB->getRecord($str);
            $_SESSION['currentPageLPOBaru']['tanggal']=$r[1]['tanggal_lpo'];
            $_SESSION['currentPageLPOBaru']['datalpo']=$r[1];
            $_SESSION['currentPageLPOBaru']['datalpo']['issaved']=true;
            $_SESSION['currentPageLPOBaru']['datalpo']['mode']='buat';
            $idlpo=$r[1]['idlpo'];
            $str = "SELECT idlpo,idobat,idobat_puskesmas,kode_obat,nama_obat,harga,kemasan,stock_awal,penerimaan AS total_penerimaan,persediaan,pemakaian AS total_pemakaian,stock_akhir,permintaan AS qty FROM detail_lpo WHERE idlpo='$idlpo'";
            $this->DB->setFieldTable(array('idlpo','idobat','idobat_puskesmas','kode_obat','nama_obat','harga','kemasan','stock_awal','total_penerimaan','persediaan','total_pemakaian','stock_akhir','qty'));
            $r=$this->DB->getRecord($str);            
            $cart = array();
            while (list($k,$v)=each($r)) {
                $cart[$v['idobat_puskesmas']]=$v;
            }
            $_SESSION['currentPageLPOBaru']['cart']=$cart;
            $this->redirect('permintaan.LPOBaru',true);
        }
    }
    public function changeTanggalLPO($sender,$param) {
        $this->idProcess='add';
        $tanggal_saat_ini=date('Y-m-d',$sender->TimeStamp);
        $_SESSION['currentPageLPOBaru']['tanggal']=$tanggal_saat_ini;
        $this->labelBulanPermintaanObat->Text=$this->TGL->tanggal('F Y',$tanggal_saat_ini);
        $tanggal_sebelumnya=$this->TGL->getMonthAndYearBefore ($tanggal_saat_ini).'-01';
        $this->labelBulanPenggunaanObat->Text=$this->TGL->tanggal('F Y',$tanggal_sebelumnya);
        $this->populateData();
    }
    public function detailProcess() {
        $this->datalpo = $_SESSION['currentPageLPOBaru']['datalpo'];         
        if ($this->datalpo['mode'] == 'buat') {
            $this->idProcess='add';            
            $tanggal_saat_ini=$_SESSION['currentPageLPOBaru']['tanggal'];                        
            $this->labelBulanPermintaanObat->Text=$this->TGL->tanggal('F Y',$tanggal_saat_ini);
            $tanggal_sebelumnya=$this->TGL->getMonthAndYearBefore ($tanggal_saat_ini).'-01';
            $this->labelBulanPenggunaanObat->Text=$this->TGL->tanggal('F Y',$tanggal_sebelumnya);
            $this->txtAddNoLPO->Text=$this->datalpo['no_lpo'];
            $this->hiddenno_lpo->Value=$this->datalpo['no_lpo'];            
            $this->txtAddNIPKA->Text=$this->datalpo['nip_ka'];            
            $this->txtAddNamaKA->Text=$this->datalpo['nama_ka'];            
            $this->txtAddNIPPengelola->Text=$this->datalpo['nip_pengelola'];            
            $this->txtAddNamaPengelola->Text=$this->datalpo['nama_pengelola'];            
            if ($this->datalpo['issaved']) {                
                $this->cmbAddTanggalLPO->Text=$this->TGL->tanggal('d-m-Y',$this->datalpo['tanggal_lpo']);                
                $this->txtAddJumlahKunjunganBayar->Text=$this->datalpo['jumlah_kunjungan_bayar'];
                $this->txtAddJumlahKunjunganGratis->Text=$this->datalpo['jumlah_kunjungan_gratis'];            
                $this->txtAddJumlahKunjunganBPJS->Text=$this->datalpo['jumlah_kunjungan_bpjs'];            
            }else{
                $this->cmbAddTanggalLPO->Text=$this->TGL->tanggal('d-m-Y',$tanggal_saat_ini);
            }            
        }elseif ($this->datalpo['mode'] == 'ubah') {
            $this->idProcess='edit';
        }        
    }
    public function renderCallback ($sender,$param) {
        $this->idProcess=$_SESSION['currentPageLPOBaru']['datalpo']['mode']=='buat'?'add':'edit';
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageLPOBaru']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageLPOBaru']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageLPOBaru']['search']=true;
        $this->populateData($_SESSION['currentPageLPOBaru']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageLPOBaru']['idprodusen']=$this->cmbFilterProdusen->Text;
        $this->populateData();
	}    
    protected function populateCart () {             
        $this->idProcess='add';
		$this->RepeaterCart->DataSource=$_SESSION['currentPageLPOBaru']['cart'];
		$this->RepeaterCart->dataBind();             
	}    
    public function deleteItem($sender,$param) {
        $id=$this->getDataKeyField($sender,$this->RepeaterCart);
        unset($_SESSION['currentPageLPOBaru']['cart'][$id]);        
        $this->populateCart();
        $this->populateData();
    }
    public function itemCreated($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {            
            
        }
    }
    public function itemCreatedCart($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {                        
            LPOBaru::$totalQTY += $item->DataItem['qty'];  
            $harga=$item->DataItem['qty']*$item->DataItem['harga'];
            LPOBaru::$totalHARGA += $harga;
        }
    }
    protected function populateData ($search=false) {     
        $idlpo=$_SESSION['currentPageLPOBaru']['datalpo']['idlpo'];        
        $this->idProcess=$_SESSION['currentPageLPOBaru']['datalpo']['mode']=='buat'?'add':'edit';
        $idprodusen=$_SESSION['currentPageLPOBaru']['idprodusen'];
        $str_produsen=$idprodusen=='none' || $idprodusen=='' ?'':" AND idprodusen=$idprodusen";
        $str = "SELECT dlp.iddetail_lpo,dlp.idobat_puskesmas,dlp.kode_obat,dlp.nama_obat,dlp.harga,dlp.kemasan FROM detail_lpo dlp LEFT JOIN master_obat_puskesmas mop ON (mop.idobat_puskesmas=dlp.idobat_puskesmas) WHERE idlpo=$idlpo $str_produsen";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa=$idprodusen=='none' ?" AND kode_obat='$txtsearch'":" AND kode_obat='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("detail_lpo WHERE idlpo=$idlpo $cluasa",'iddetail_lpo');
                    $str = "$str $cluasa";
                break;
                case 'nama' :
                    $cluasa=$idprodusen=='none' ?" AND nama_obat LIKE '%$txtsearch%'" :" AND nama_obat LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("detail_lpo WHERE idlpo=$idlpo $cluasa",'iddetail_lpo');
                    $str = "$str $cluasa";
                break;  
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ("detail_lpo WHERE idlpo=$idlpo $str_produsen",'iddetail_lpo');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageLPOBaru']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=50;$_SESSION['currentPageLPOBaru']['page_num']=0;}
        $str = "$str ORDER BY nama_obat ASC,stock ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('iddetail_lpo','idobat_puskesmas','kode_obat','nama_obat','harga','kemasan'));
		$r=$this->DB->getRecord($str,$offset+1);             
        $result=array();        
        $tanggal_sebelumnya=$this->TGL->getMonthAndYearBefore ($_SESSION['currentPageLPOBaru']['tanggal']);        
        while (list($k,$v)=each($r)) {
            $idobat_puskesmas=$v['idobat_puskesmas'];
            $stock_awal=$this->Obat->getLastStockFromStockCardPuskesmas($idobat_puskesmas,$tanggal_sebelumnya);
            $masukkeluar=$this->Obat->getTotalKeluarMasukFromStockCardPuskesmas($idobat_puskesmas,$tanggal_sebelumnya);
            $persediaan=$stock_awal+$masukkeluar['masuk'];
            $v['awal_stock']=$stock_awal;
            $v['total_penerimaan']=$masukkeluar['masuk'];
            $v['total_pemakaian']=$masukkeluar['keluar'];
            $v['persediaan']=$persediaan;
            $v['stock_akhir']=$persediaan-$masukkeluar['keluar'];
            $result[$k]=$v;
        }        
		$this->RepeaterS->DataSource=$result;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}
    public function viewRecord ($sender,$param) {                
        $id=$sender->CommandParameter;   
        $dataobat = $this->Obat->getInfoMasterObatPuskesmas($id);        
        $this->setInfoObat($dataobat);
        $this->modalInfoObat->show();
    }
    public function checkNoLPO ($sender,$param) {
		$this->idProcess=$sender->getId()=='addNoLPO'?'add':'edit';
        $no_lpo=$param->Value;		
        $idpuskeskmas=$this->idpuskesmas;
        if ($no_lpo != '') {
            try {   
                if ($this->hiddenno_lpo->Value!=$no_lpo) {
                    if ($this->DB->checkRecordIsExist('no_lpo','master_lpo',$no_lpo," AND idpuskesmas=$idpuskesmas")) {                                
                        throw new Exception ("Nomor LPO ($no_lpo) sudah tidak tersedia silahkan ganti dengan yang lain.");		
                    }                               
                }
                                
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }	
    }    
    public function simpanProductRepeater($sender,$param) {        
        if ($this->IsValid) {                        
            $iddetail_lpo=$this->getDataKeyField($sender,$this->RepeaterS); 
            $idobat_puskesmas=$sender->CommandParameter;
            $dataobat=$this->Obat->getInfoMasterObatPuskesmas($idobat_puskesmas);            
            $obj=$sender->getNamingContainer();
            $qty=$obj->txtQTY->getText();                        
            $tanggal_sebelumnya=$this->TGL->getMonthAndYearBefore ($_SESSION['currentPageLPOBaru']['tanggal']);        
            $stock_awal=$this->Obat->getLastStockFromStockCardPuskesmas($idobat_puskesmas,$tanggal_sebelumnya);
            $masukkeluar=$this->Obat->getTotalKeluarMasukFromStockCardPuskesmas($idobat_puskesmas,$tanggal_sebelumnya);
            $persediaan=$stock_awal+$masukkeluar['masuk'];            
            $stock_akhir=$persediaan-$masukkeluar['keluar'];            
            $_SESSION['currentPageLPOBaru']['cart'][$iddetail_lpo]=array('iddetail_lpo'=>$iddetail_lpo,
                                                                    'idobat_puskesmas'=>$idobat_puskesmas,
                                                                    'idobat'=>$dataobat['idobat'],
                                                                    'kode_obat'=>$dataobat['kode_obat'],
                                                                    'nama_obat'=>$dataobat['nama_obat'],
                                                                    'harga'=>$dataobat['harga'],
                                                                    'idsatuan_obat'=>$dataobat['idsatuan_obat'],
                                                                    'idgolongan'=>$dataobat['idgolongan'],
                                                                    'kemasan'=>$dataobat['kemasan'], 
                                                                    'awal_stock'=>$stock_awal,
                                                                    'total_penerimaan'=>$masukkeluar['masuk'],
                                                                    'total_pemakaian'=>$masukkeluar['keluar'],
                                                                    'persediaan'=>$persediaan,
                                                                    'stock_akhir'=>$stock_akhir,
                                                                    'qty'=>$qty);
                                                                    
            $this->redirect('permintaan.LPOBaru',true);
        }
    }
    public function getQTYFromCart($iddetail_lpo){
        $cart=$_SESSION['currentPageLPOBaru']['cart'];
        if (isset($cart[$iddetail_lpo])) {
            return $cart[$iddetail_lpo]['qty'];
        }else{
            return 0;
        }
        
    }
    public function saveData ($sender,$param) {
        if ($this->IsValid) {      
            $datalpo=$_SESSION['currentPageLPOBaru']['datalpo'];
            $idlpo=$datalpo['idlpo'];            
            $tanggal_lpo = date('Y-m-d',$this->cmbAddTanggalLPO->TimeStamp);            
            $no_lpo=addslashes($this->txtAddNoLPO->Text);
            $jmlh_kunjungan_bayar=$this->txtAddJumlahKunjunganBayar->Text;
            $jmlh_kunjungan_gratis=$this->txtAddJumlahKunjunganGratis->Text;            
            $jmlh_kunjungan_bpjs=$this->txtAddJumlahKunjunganBPJS->Text;            
            $this->DB->query('BEGIN');            
            $str = "UPDATE master_lpo SET no_lpo='$no_lpo',tanggal_lpo='$tanggal_lpo',jumlah_kunjungan_gratis='$jmlh_kunjungan_gratis',jumlah_kunjungan_bayar='$jmlh_kunjungan_bayar',jumlah_kunjungan_bpjs='$jmlh_kunjungan_bpjs',status='draft',date_modified=NOW() WHERE idlpo=$idlpo";
            if ($this->DB->updateRecord($str)) {    
                $datalpo['issaved']=true;                                                                        
                $datalpo['no_lpo']=$no_lpo;                
                $datalpo['tanggal_lpo']=$tanggal_lpo;
                $datalpo['jumlah_kunjungan_gratis']=$jmlh_kunjungan_gratis;
                $datalpo['jumlah_kunjungan_bayar']=$jmlh_kunjungan_bayar;
                $datalpo['jumlah_kunjungan_bpjs']=$jmlh_kunjungan_bpjs;                
                $_SESSION['currentPageLPOBaru']['datalpo']=$datalpo;                
                $tanggal_sebelumnya=$this->TGL->getMonthAndYearBefore ($_SESSION['currentPageLPOBaru']['tanggal']);        
                foreach ($this->RepeaterS->Items as $inputan) {                    
                    $item=$inputan->txtQTY->getNamingContainer();                    
                    $iddetail_lpo=$this->RepeaterS->DataKeys[$item->getItemIndex()];                        
                    $qty=$inputan->txtQTY->Text;                        
                    $idobat_puskesmas=$inputan->btnAddProductRepeater->CommandParameter;
                    $stock_awal=$this->Obat->getLastStockFromStockCardPuskesmas($idobat_puskesmas,$tanggal_sebelumnya);
                    $masukkeluar=$this->Obat->getTotalKeluarMasukFromStockCardPuskesmas($idobat_puskesmas,$tanggal_sebelumnya);
                    $total_penerimaan=$masukkeluar['masuk'];
                    $total_pemakaian=$masukkeluar['keluar'];
                    $persediaan=$stock_awal+$masukkeluar['masuk'];            
                    $stock_akhir=$persediaan-$masukkeluar['keluar'];    
                    if ($stock > 0) {
                        $_SESSION['currentPageLPOBaru']['cart'][$iddetail_lpo]['awal_stock']=$stock_awal;
                        $_SESSION['currentPageLPOBaru']['cart'][$iddetail_lpo]['total_penerimaan']=$total_penerimaan;
                        $_SESSION['currentPageLPOBaru']['cart'][$iddetail_lpo]['total_pemakaian']=$total_pemakaian;
                        $_SESSION['currentPageLPOBaru']['cart'][$iddetail_lpo]['persediaan']=$persediaan;
                        $_SESSION['currentPageLPOBaru']['cart'][$iddetail_lpo]['stock_akhir']=$stock_akhir;
                    }                    
                    $str = "UPDATE detail_lpo SET stock_awal='$stock_awal',penerimaan='$total_penerimaan',persediaan='$persediaan',pemakaian='$total_pemakaian',stock_akhir='$stock_akhir',permintaan='$qty',date_added=NOW(),date_modified=NOW() WHERE iddetail_lpo=$iddetail_lpo";                                                   
                    $this->DB->updateRecord($str);                        
                }
                $this->DB->query('COMMIT');
                $this->redirect('permintaan.LPOBaru',true);
            }else{
                $this->DB->query('ROLLBACK');
            }            
        }
    }
    public function checkOut ($sender,$param) {
        if ($this->IsValid) {      
            $datalpo=$_SESSION['currentPageLPOBaru']['datalpo'];
            $idlpo=$datalpo['idlpo'];            
            $tanggal_lpo = date('Y-m-d',$this->cmbAddTanggalLPO->TimeStamp);            
            $no_lpo=addslashes($this->txtAddNoLPO->Text);
            $jmlh_kunjungan_bayar=$this->txtAddJumlahKunjunganBayar->Text;
            $jmlh_kunjungan_gratis=$this->txtAddJumlahKunjunganGratis->Text;            
            $jmlh_kunjungan_bpjs=$this->txtAddJumlahKunjunganBPJS->Text;            
            $this->DB->query('BEGIN');            
            $str = "UPDATE master_lpo SET no_lpo='$no_lpo',tanggal_lpo='$tanggal_lpo',jumlah_kunjungan_gratis='$jmlh_kunjungan_gratis',jumlah_kunjungan_bayar='$jmlh_kunjungan_bayar',jumlah_kunjungan_bpjs='$jmlh_kunjungan_bpjs',status='complete',date_modified=NOW() WHERE idlpo=$idlpo";
            if ($this->DB->updateRecord($str)) {    
                $datalpo['idpuskesmas']=$this->idpuskesmas;                                                                        
                $datalpo['issaved']=true;                                                                        
                $datalpo['no_lpo']=$no_lpo;                
                $datalpo['tanggal_lpo']=$tanggal_lpo;
                $datalpo['jumlah_kunjungan_gratis']=$jmlh_kunjungan_gratis;
                $datalpo['jumlah_kunjungan_bayar']=$jmlh_kunjungan_bayar;
                $datalpo['jumlah_kunjungan_bpjs']=$jmlh_kunjungan_bpjs;                
                $datalpo['response_lpo']=1;                
                $cart=$_SESSION['currentPageLPOBaru']['cart'];
                if (count($cart) > 0) {                    
                    $tanggal_sebelumnya=$this->TGL->getMonthAndYearBefore ($_SESSION['currentPageLPOBaru']['tanggal']);        
                    foreach ($cart as $v) {
                        $iddetail_lpo=$v['iddetail_lpo'];                                   
                        $idobat_puskesmas=$v['idobat_puskesmas'];                                             
                        $stock_awal=$this->Obat->getLastStockFromStockCardPuskesmas($idobat_puskesmas,$tanggal_sebelumnya);
                        $masukkeluar=$this->Obat->getTotalKeluarMasukFromStockCardPuskesmas($idobat_puskesmas,$tanggal_sebelumnya);
                        $total_penerimaan=$masukkeluar['masuk'];
                        $total_pemakaian=$masukkeluar['keluar'];
                        $persediaan=$stock_awal+$masukkeluar['masuk'];            
                        $stock_akhir=$persediaan-$masukkeluar['keluar'];      
                        $_SESSION['currentPageLPOBaru']['cart'][$iddetail_lpo]['awal_stock']=$stock_awal;
                        $_SESSION['currentPageLPOBaru']['cart'][$iddetail_lpo]['total_penerimaan']=$total_penerimaan;
                        $_SESSION['currentPageLPOBaru']['cart'][$iddetail_lpo]['total_pemakaian']=$total_pemakaian;
                        $_SESSION['currentPageLPOBaru']['cart'][$iddetail_lpo]['persediaan']=$persediaan;
                        $_SESSION['currentPageLPOBaru']['cart'][$iddetail_lpo]['stock_akhir']=$stock_akhir;
                        $qty=$v['qty'];                        
                        $str = "UPDATE detail_lpo SET stock_awal='$stock_awal',penerimaan='$total_penerimaan',persediaan='$persediaan',pemakaian='$total_pemakaian',stock_akhir='$stock_akhir',permintaan='$qty',date_added=NOW(),date_modified=NOW() WHERE iddetail_lpo=$iddetail_lpo";                                                
                        $this->DB->updateRecord($str);                        
                    }
                }                
                $_SESSION['currentPageDaftarLPO']['datalpo']=$datalpo;
                $_SESSION['currentPageDaftarLPO']['cart']=$cart;
                $this->DB->query('COMMIT');
                unset($_SESSION['currentPageLPOBaru']['datalpo']);
                unset($_SESSION['currentPageLPOBaru']['cart']);
                $this->redirect('permintaan.DaftarLPO',true);
            }else{
                $this->DB->query('ROLLBACK');
            }            
        }
    }
    public function clearCart ($sender,$param) {
        $_SESSION['currentPageLPOBaru']['cart']=array();
        $this->redirect('permintaan.LPOBaru',true);
    }
    public function batalLPO ($sender,$param) {
		$id=$_SESSION['currentPageLPOBaru']['datalpo']['idlpo'];        
        $this->DB->deleteRecord("master_lpo WHERE idlpo=$id");
        unset($_SESSION['currentPageLPOBaru']['datalpo']);
        unset($_SESSION['currentPageLPOBaru']['cart']);
        $this->redirect('permintaan.LPOBaru',true);
	}
    public function closeLPO ($sender,$param) {
        unset($_SESSION['currentPageLPOBaru']['datalpo']);
        unset($_SESSION['currentPageLPOBaru']['cart']);
        $this->redirect('permintaan.LPOBaru',true);
    }
    
}
?>
