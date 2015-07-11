<?php
prado::using ('Application.MainPageSA');
class SBBKBaru extends MainPageSA {
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
            if (isset($_SESSION['currentPageSBBKBaru']['datasbbk']['idsbbk'])) {                                
                $this->detailProcess();                                
                $this->populateCart();
            }else {
                if (!isset($_SESSION['currentPageSBBKBaru'])||$_SESSION['currentPageSBBKBaru']['page_name']!='sa.mutasibarang.SBBKBaru') {
                    $_SESSION['currentPageSBBKBaru']=array('page_name'=>'sa.mutasibarang.SBBKBaru','page_num'=>0,'search'=>false,'datasbbk'=>array(),'cart'=>array());												
                }
            }			
		}	        
	}
    public function checkBuatSBBKBaru ($sender,$param) {        
        $no_lpo=$param->Value;		
        if ($no_lpo != '') {
            try {
                if (!$this->DB->checkRecordIsExist('no_lpo','master_lpo',$no_lpo," AND status='complete'")) {                                
                    throw new Exception ("Nomor LPO ($no_lpo) tidak ada di database silahkan ganti dengan yang lain.");		
                }               
                if ($this->DB->checkRecordIsExist('no_lpo','master_sbbk',$no_lpo)) {                                
                    throw new Exception ("Nomor LPO ($no_lpo) sudah dibuatkan SBBK-nya, silahkan dilihat di Daftar SBBK.");		
                }
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }
    }
    public function buatSBBK($sender,$param) {
        if ($this->IsValid) {
            $no_lpo = addslashes(trim($this->txtNoLPOSBBKBaru->Text));      
            $ta=$_SESSION['ta'];
            $tanggal_sbbk=Date("$ta-m-d");
            $str = "SELECT idlpo,no_lpo,tanggal_lpo,idpuskesmas FROM master_lpo WHERE no_lpo='$no_lpo'";
            $this->DB->setFieldTable(array('idlpo','no_lpo','tanggal_lpo','idpuskesmas'));
            $r=$this->DB->getRecord($str);
            $idlpo=$r[1]['idlpo'];
            $tanggal_lpo=$r[1]['tanggal_lpo'];
            $idpuskesmas=$r[1]['idpuskesmas'];
            $permintaan_dari=$this->DMaster->getNamaPuskesmasByID($idpuskesmas);
            
            $nip_kadis=$this->setup->getSettingValue('nip_kadis');        
            $nama_kadis=$this->setup->getSettingValue('nama_kadis');        
            $nip_ka_gudang=$this->setup->getSettingValue('nip_ka_gudang');        
            $nama_ka_gudang=$this->setup->getSettingValue('nama_ka_gudang');             
            
            $this->DB->insertRecord("INSERT INTO master_sbbk (idlpo,no_lpo,tanggal_lpo,tanggal_sbbk,idpuskesmas,permintaan_dari,nip_kadis,nama_kadis,nip_ka_gudang,nama_ka_gudang,tahun,date_added,date_modified) VALUES ($idlpo,'$no_lpo','$tanggal_lpo','$tanggal_sbbk',$idpuskesmas,'$permintaan_dari','$nip_kadis','$nama_kadis','$nip_ka_gudang','$nama_ka_gudang',$ta,NOW(),NOW())");            
            $idsbbk=$this->DB->getLastInsertID ();
            $datasbbk=array('idsbbk'=>$idsbbk,'tanggal_sbbk'=>$tanggal_sbbk,'idpuskesmas'=>$idpuskesmas,'permintaan_dari'=>$permintaan_dari,'idlpo'=>$idlpo,'no_lpo'=>$no_lpo,'tanggal_lpo'=>$tanggal_lpo,'nip_ka_gudang'=>$nip_ka_gudang,'nama_ka_gudang'=>$nama_ka_gudang,'nip_pengemas'=>$nama_pengemas,'nama_pengemas'=>$nama_pengemas,'tahun'=>$ta,'mode'=>'buat','issaved'=>false);            
            $_SESSION['currentPageSBBKBaru']['datasbbk']=$datasbbk;            
            
            $this->DB->updateRecord("UPDATE master_lpo SET response_lpo=2 WHERE idlpo=$idlpo");
            $_SESSION['currentPageSBBKBaru']['cart']=array();            
            $this->redirect('mutasibarang.SBBKBaru',true);
        }
    }
    public function checkUbahSBBKBaru ($sender,$param) {        
        $no_sbbk=$param->Value;		
        if ($no_sbbk != '') {
            try {                                         
                $str = "SELECT status FROM master_sbbk WHERE no_sbbk='$no_sbbk'";
                $this->DB->setFieldTable(array('status'));
                $r=$this->DB->getRecord($str); 
                if (isset($r[1]) ){
                    if ($r[1]['status'] == 'complete')
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
            $str = "SELECT idlpo,idsbbk,no_sbbk,tanggal_sbbk,idpuskesmas,permintaan_dari,idlpo,no_lpo,tanggal_lpo,keperluan,no_spmb,nip_ka_gudang,nama_ka_gudang,nip_pengemas,nama_pengemas FROM master_sbbk WHERE no_sbbk='$no_sbbk'";
            $this->DB->setFieldTable(array('idlpo','idsbbk','no_sbbk','tanggal_sbbk','idpuskesmas','permintaan_dari','idlpo','no_lpo','tanggal_lpo','keperluan','no_spmb','nip_ka_gudang','nama_ka_gudang','nip_pengemas','nama_pengemas'));
            $datasbbk=$this->DB->getRecord($str);        

            $_SESSION['currentPageSBBKBaru']['idprodusen']='none';
            $_SESSION['currentPageSBBKBaru']['datasbbk']=$datasbbk[1];
            $_SESSION['currentPageSBBKBaru']['datasbbk']['issaved']=true;
            $_SESSION['currentPageSBBKBaru']['datasbbk']['mode']='buat';        

            $idsbbk=$datasbbk[1]['idsbbk'];
            $str = "SELECT dsb.iddetail_sbbm,dsb.idobat,dsb.idobat_puskesmas,dsb.kode_obat,dsb.nama_obat,mo.harga,dsb.kemasan,mo.stock,dsb.stock_awal,dsb.penerimaan AS total_penerimaan,dsb.persediaan,dsb.pemakaian AS total_pemakaian,dsb.stock_akhir,dsb.permintaan,dsb.pemberian,dsb.islpo FROM detail_sbbk dsb LEFT JOIN master_obat mo ON (dsb.idobat=mo.idobat) WHERE idsbbk='$idsbbk'";        
            $this->DB->setFieldTable(array('iddetail_sbbm','idobat','idobat_puskesmas','kode_obat','nama_obat','harga','kemasan','stock','stock_awal','total_penerimaan','persediaan','total_pemakaian','stock_akhir','permintaan','pemberian','islpo'));
            $r=$this->DB->getRecord($str);               
            $cart = array();
            if (isset($r[1])) {
                while (list($k,$v)=each($r)) {
                    $cart[$v['iddetail_sbbm']]=$v;
                }
            }            
            $_SESSION['currentPageSBBKBaru']['cart']=$cart;            
            $this->redirect('mutasibarang.SBBKBaru',true);
        }
    }
    public function detailProcess() {
        $this->datasbbk = $_SESSION['currentPageSBBKBaru']['datasbbk'];         
        if ($this->datasbbk['mode'] == 'buat') {
            $this->idProcess='add';                                    
            $this->cmbAddTanggalSBBK->Text=$this->TGL->tanggal('d-m-Y',$this->datasbbk['tanggal_sbbk']);
            if ($this->datasbbk['issaved']) {                
                $this->txtAddNoSBBK->Text=$this->datasbbk['no_sbbk'];                
                $this->txtAddKeperluan->Text=$this->datasbbk['keperluan'];
                $this->txtAddNoSPMB->Text=$this->datasbbk['no_spmb'];            
                $this->txtAddNIPPengemas->Text=$this->datasbbk['nip_pengemas'];            
                $this->txtAddNamaPengemas->Text=$this->datasbbk['nama_pengemas'];            
                $this->hiddenno_sbbk->Value=$this->datasbbk['no_sbbk'];
                $this->hiddenno_spmb->Value=$this->datasbbk['no_spmb'];
            }
        }      
    }
    public function getQTYFromCart($idobat){
        $cart=$_SESSION['currentPageSBBKBaru']['cart'];
        if (isset($cart[$idobat])) {
            return $cart[$idobat]['pemberian'];
        }else{
            return 0;
        }
        
    }
    public function viewRecord ($sender,$param) {                
        $id=$sender->CommandParameter;   
        $dataobat = $this->Obat->getInfoMasterObat($id);        
        $this->setInfoObat($dataobat);
        $this->modalInfoObat->show();
    }
    public function itemCreatedCart($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {                        
            $item->txtQTY->Enabled=$item->DataItem['stock'] > 0;
            if ($item->DataItem['islpo']) {                
                $item->btnDelete->Enabled=false;
                $item->btnDelete->CssClass='table-link disabled';
            }else{
                $item->btnDelete->Attributes->OnClick="if(!confirm('Anda ingin menghapus item ini ?')) return false;";
            }            
            if ($item->DataItem['pemberian'] > 0) {
                SBBKBaru::$totalQTY += $item->DataItem['pemberian'];
                $harga=$item->DataItem['pemberian']*$item->DataItem['harga']; 
                SBBKBaru::$totalHARGA += $harga;            
            }
            
        }
    }
    public function deleteItem($sender,$param) {
        $id=$this->getDataKeyField($sender,$this->RepeaterCart);
        unset($_SESSION['currentPageSBBKBaru']['cart'][$id]);        
        $this->populateCart();        
    }
    protected function populateCart () {             
        $this->idProcess='add';        
        $cart = $_SESSION['currentPageSBBKBaru']['cart'];                  
		$this->RepeaterCart->DataSource=$cart;
		$this->RepeaterCart->dataBind();             
	}
    public function checkBarcode ($sender,$param) {
        $this->idProcess='add';
        $barcode=$param->Value;		
        if ($barcode != '') {
            try {                
                $str = "SELECT iddetail_sbbm,nama_obat,harga,qty,tanggal_sbbm FROM detail_sbbm dsb,master_sbbm msb WHERE dsb.idsbbm=msb.idsbbm AND barcode='$barcode' AND status='complete'";
                $this->DB->setFieldTable(array('iddetail_sbbm','nama_obat','harga','qty','tanggal_sbbm'));
                $r=$this->DB->getRecord($str);  
                if (isset($r[1])) {
                    $iddetail_sbbm=$r[1]['iddetail_sbbm'];
                    $nama_obat = $r[1]['nama_obat'];
                    $harga = $this->Obat->toRupiah($r[1]['harga']);
                    $qty=$r[1]['qty'];
                    $jumlah_keluar=$this->DB->getCountRowsOfTable ("kartu_stock WHERE iddetail_sbbm=$iddetail_sbbm AND mode='keluar' AND isdestroyed=0",'idkartu_stock');		
                    if ($jumlah_keluar >= $qty) {
                        throw new Exception ("Nama Obat ($nama_obat) yang harga $harga telah habis stocknya.");		
                    }
                    if ($this->TGL->compareDate($r[1]['tanggal_sbbm'],date('Y-m-d'),'greaterthan')) {                        
                        throw new Exception ("Tanggal SBBM dari barcode ($barcode) melampaui tanggal hari ini, mohon diubah di Daftar SBBM menjadi tanggal sebelumnya.");		
                    }
                }else {
                    throw new Exception ("Barcode ($barcode) tidak ada di database silahkan ganti dengan yang lain.");		
                }
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }
    }
    public function simpanProductRepeater($sender,$param) {        
        if ($this->IsValid) {                        
            $iddetail_sbbm=$this->getDataKeyField($sender,$this->RepeaterCart);                                    
            $obj=$sender->getNamingContainer();
            $pemberian=$obj->txtQTY->getText();                                              
            
            $str = "SELECT qty FROM detail_sbbm dsb WHERE iddetail_sbbm=$iddetail_sbbm";
            $this->DB->setFieldTable(array('qty'));
            $r=$this->DB->getRecord($str); 
            
            $qty=$r[1]['qty'];
            $jumlah_keluar=$this->DB->getCountRowsOfTable ("kartu_stock WHERE iddetail_sbbm=$iddetail_sbbm AND mode='keluar' AND isdestroyed=0",'idkartu_stock');		
            $stock=$qty - $jumlah_keluar;
            if ($pemberian <= $stock) {
                $_SESSION['currentPageSBBKBaru']['cart'][$iddetail_sbbm]['stock']=$stock;
                $_SESSION['currentPageSBBKBaru']['cart'][$iddetail_sbbm]['pemberian']=$pemberian;                
                $this->redirect('mutasibarang.SBBKBaru',true);
            }else{
                $this->labelMessageError->Text="Jumlah pemberian ($pemberian) harus lebih kecil atau sama dengan jumlah stock ($qty)";
                $this->modalMessageError->show();
            }                      
        }
    }
    public function addObat ($sender,$param) {
        if ($this->IsValid) {
            $barcode=$this->txtAddBarcode->Text;
            $str = "SELECT dsb.iddetail_sbbm,dsb.idobat,dsb.kode_obat,dsb.nama_obat,dsb.harga,dsb.kemasan,dsb.qty,dlp.idobat_puskesmas,dlp.stock_awal,dlp.penerimaan,dlp.persediaan,dlp.pemakaian,dlp.permintaan,dlp.stock_akhir FROM detail_sbbm dsb LEFT JOIN detail_lpo dlp ON (dsb.idobat=dlp.idobat) WHERE barcode='$barcode' ORDER BY dlp.iddetail_lpo DESC LIMIT 1";
            $this->DB->setFieldTable(array('iddetail_sbbm','idobat','kode_obat','nama_obat','harga','kemasan','qty','idobat_puskesmas','stock_awal','penerimaan','persediaan','pemakaian','permintaan','stock_akhir'));
            $r=$this->DB->getRecord($str);
            $dataobat=$r[1];
            $iddetail_sbbm=$dataobat['iddetail_sbbm'];
            $jumlah_keluar=$this->DB->getCountRowsOfTable ("kartu_stock WHERE iddetail_sbbm=$iddetail_sbbm AND mode='keluar' AND isdestroyed=0",'idkartu_stock');		
            $stock=$dataobat['qty']-$jumlah_keluar;
            $_SESSION['currentPageSBBKBaru']['cart'][$iddetail_sbbm]=array(
                                                                        'iddetail_sbbm'=>$iddetail_sbbm,
                                                                        'idobat'=>$dataobat['idobat'],
                                                                        'idobat_puskesmas'=>$dataobat['idobat_puskesmas'],
                                                                        'kode_obat'=>$dataobat['kode_obat'],
                                                                        'nama_obat'=>$dataobat['nama_obat'],
                                                                        'harga'=>$dataobat['harga'],                                                                    
                                                                        'kemasan'=>$dataobat['kemasan'],
                                                                        'stock'=>$stock,
                                                                        'stock_awal'=>$dataobat['stock_awal'],
                                                                        'penerimaan'=>$dataobat['penerimaan'],
                                                                        'persediaan'=>$dataobat['persediaan'],
                                                                        'total_pemakaian'=>$dataobat['pemakaian'],                    
                                                                        'permintaan'=>$dataobat['permintaan'],     
                                                                        'stock_akhir'=>$dataobat['stock_akhir'],                                                  
                                                                        'islpo'=>$dataobat['permintaan'] > 0,
                                                                        'pemberian'=>$stock);
            $this->redirect('mutasibarang.SBBKBaru',true);
        }
    }
    public function checkNoSBBK ($sender,$param) {
		$this->idProcess=$sender->getId()=='addNoSBBK'?'add':'edit';
        $no_sbbk=$param->Value;		
        if ($no_sbbk != '') {
            try {   
                if ($this->hiddenno_sbbk->Value!=$no_sbbk) {
                    if ($this->DB->checkRecordIsExist('no_sbbk','master_sbbk',$no_sbbk)) {                                
                        throw new Exception ("Nomor SBBK ($no_sbbk) sudah tidak tersedia silahkan ganti dengan yang lain.");		
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
        $no_spmb=$param->Value;		
        if ($no_spmb != '') {
            try {   
                if ($this->hiddenno_spmb->Value!=$no_spmb) {
                    if ($this->DB->checkRecordIsExist('no_spmb','master_sbbk',$no_spmb)) {                                
                        throw new Exception ("Nomor SPMB ($no_spmb) sudah tidak tersedia silahkan ganti dengan yang lain.");		
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
            $idsbbk=$datasbbk['idsbbk'];            
            $nosbbk=addslashes($this->txtAddNoSBBK->Text);
            $tanggal_sbbk = date('Y-m-d',$this->cmbAddTanggalSBBK->TimeStamp);            
            $keperluan=addslashes($this->txtAddKeperluan->Text);
            $nospmb=addslashes($this->txtAddNoSPMB->Text);
            
            $nip_kadis=$this->setup->getSettingValue('nip_kadis');        
            $nama_kadis=$this->setup->getSettingValue('nama_kadis');        

            $nip_ka_gudang=$this->setup->getSettingValue('nip_ka_gudang');        
            $nama_ka_gudang=$this->setup->getSettingValue('nama_ka_gudang');                                                         
                                
            $nip_pengemas=addslashes($this->txtAddNIPPengemas->Text);
            $nama_pengemas=addslashes($this->txtAddNamaPengemas->Text);
            
            $this->DB->query('BEGIN');
            $str = "UPDATE master_sbbk SET no_sbbk='$nosbbk',no_spmb='$nospmb',tanggal_sbbk='$tanggal_sbbk',keperluan='$keperluan',status='draft',nip_kadis='$nip_kadis',nama_kadis='$nama_kadis',nip_ka_gudang='$nip_ka_gudang',nama_ka_gudang='$nama_ka_gudang',nip_pengemas='$nip_pengemas',nama_pengemas='$nama_pengemas',date_modified=NOW() WHERE idsbbk=$idsbbk";
            if ($this->DB->updateRecord($str)) {    
                $datasbbk['issaved']=true;                                        
                $datasbbk['no_sbbk']=$nosbbk;
                $datasbbk['tanggal_sbbk']=$tanggal_sbbk;
                $datasbbk['keperluan']=$keperluan;
                $datasbbk['no_spmb']=$nospmb;                
                $datasbbk['nip_pengemas']=$nip_pengemas;
                $datasbbk['nama_pengemas']=$nama_pengemas;                
                $_SESSION['currentPageSBBKBaru']['datasbbk']=$datasbbk;
                $cart=$_SESSION['currentPageSBBKBaru']['cart'];
                if (count($cart) > 0) {
                    $idlpo=$datasbbk['idlpo'];
                    $this->DB->deleteRecord("detail_sbbk WHERE idsbbk=$idsbbk");
                    foreach ($cart as $iddetail_sbbm=>$v) {                                                                  
                        $idobat_puskesmas=$v['idobat_puskesmas'];                                                              
                        $stock_awal=$v['stock_awal'];
                        $penerimaan=$v['penerimaan'];
                        $persediaan=$v['persediaan'];
                        $pemakaian=$v['total_pemakaian'];
                        $permintaan =$v['permintaan'];
                        $stock_akhir=$v['stock_akhir'];                        
                        $qty=$v['pemberian'];    
                        $str = "INSERT INTO detail_sbbk (idsbbk,idsbbm,iddetail_sbbm,idobat,idobat_puskesmas,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,no_batch,stock_awal,penerimaan,persediaan,pemakaian,stock_akhir,permintaan,pemberian,islpo,date_added,date_modified) SELECT $idsbbk,idsbbm,$iddetail_sbbm,idobat,$idobat_puskesmas,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,no_batch,$stock_awal,$penerimaan,$persediaan,$pemakaian,$stock_akhir,$permintaan,'$qty',1,NOW(),NOW() FROM detail_sbbm WHERE iddetail_sbbm=$iddetail_sbbm";                                                
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
            $idsbbk=$datasbbk['idsbbk']; 
            $permintaan_dari=$this->DMaster->getNamaPuskesmasByID($datasbbk['idpuskesmas']);            
            $nosbbk=addslashes($this->txtAddNoSBBK->Text);
            $tanggal_sbbk = date('Y-m-d',$this->cmbAddTanggalSBBK->TimeStamp);            
            $keperluan=addslashes($this->txtAddKeperluan->Text);
            $nospmb=addslashes($this->txtAddNoSPMB->Text);
            
            $nip_kadis=$this->setup->getSettingValue('nip_kadis');        
            $nama_kadis=$this->setup->getSettingValue('nama_kadis');        

            $nip_ka_gudang=$this->setup->getSettingValue('nip_ka_gudang');        
            $nama_ka_gudang=$this->setup->getSettingValue('nama_ka_gudang');                                                         
                                
            $nip_pengemas=addslashes($this->txtAddNIPPengemas->Text);
            $nama_pengemas=addslashes($this->txtAddNamaPengemas->Text);
            
            $this->DB->query('BEGIN');
            $str = "UPDATE master_sbbk SET no_sbbk='$nosbbk',no_spmb='$nospmb',tanggal_sbbk='$tanggal_sbbk',keperluan='$keperluan',status='complete',response_sbbk=1,nip_kadis='$nip_kadis',nama_kadis='$nama_kadis',nip_ka_gudang='$nip_ka_gudang',nama_ka_gudang='$nama_ka_gudang',nip_pengemas='$nip_pengemas',nama_pengemas='$nama_pengemas',date_modified=NOW() WHERE idsbbk=$idsbbk";
            
            $userid=$this->Pengguna->getDataUser('userid');
            $username=$this->Pengguna->getDataUser('username');
            $nama=$this->Pengguna->getDataUser('nama');
            
            if ($this->DB->updateRecord($str)) {    
                $datasbbk['issaved']=true;                                        
                $datasbbk['no_sbbk']=$nosbbk;
                $datasbbk['tanggal_sbbk']=$tanggal_sbbk;
                $datasbbk['keperluan']=$keperluan;
                $datasbbk['no_spmb']=$nospmb;                
                $_SESSION['currentPageSBBKBaru']['datasbbk']=$datasbbk;
                $cart=$_SESSION['currentPageSBBKBaru']['cart'];
                if (count($cart) > 0) {
                    $idlpo=$datasbbk['idlpo'];
                    $this->DB->deleteRecord("detail_sbbk WHERE idsbbk=$idsbbk");
                    $bulan=$this->TGL->tanggal('m',$tanggal_sbbk);
                    $tahun=$_SESSION['ta'];
                    foreach ($cart as $iddetail_sbbm=>$v) {
                        $idobat=$v['idobat'];        
                        $idobat_puskesmas=$v['idobat_puskesmas']; 
                        $stock_awal=$v['stock_awal'];
                        $penerimaan=$v['penerimaan'];
                        $persediaan=$v['persediaan'];
                        $pemakaian=$v['total_pemakaian'];
                        $permintaan =$v['permintaan'];
                        $stock_akhir=$v['stock_akhir'];                        
                        $qty=$v['pemberian'];    
                        $str = "INSERT INTO detail_sbbk (idsbbk,idsbbm,iddetail_sbbm,idobat,idobat_puskesmas,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,no_batch,stock_awal,penerimaan,persediaan,pemakaian,stock_akhir,permintaan,pemberian,islpo,date_added,date_modified) SELECT $idsbbk,idsbbm,$iddetail_sbbm,idobat,$idobat_puskesmas,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,no_batch,$stock_awal,$penerimaan,$persediaan,$pemakaian,$stock_akhir,$permintaan,'$qty',1,NOW(),NOW() FROM detail_sbbm WHERE iddetail_sbbm=$iddetail_sbbm";                                                                                
                        $this->DB->insertRecord($str);
                        $iddetail_sbbk=$this->DB->getLastInsertID ();
                        $this->DB->updateRecord("UPDATE master_obat SET stock=stock-$qty WHERE idobat=$idobat");
                        $str = "UPDATE kartu_stock ks,(SELECT idkartu_stock FROM kartu_stock WHERE mode='masuk' AND iddetail_sbbm=$iddetail_sbbm AND isdestroyed=0 LIMIT $qty) AS temp SET idsbbk=$idsbbk,iddetail_sbbk=$iddetail_sbbk,mode='keluar',date_modified=NOW() WHERE ks.idkartu_stock=temp.idkartu_stock";                        
                        $this->DB->updateRecord($str);
                        $str = "INSERT INTO log_ks (idobat,idsbbk,iddetail_sbbk,tanggal,bulan,tahun,qty,sisa_stock,keterangan,mode,userid,username,nama,date_added) SELECT $idobat,$idsbbk,$iddetail_sbbk,'$tanggal_sbbk','$bulan',$tahun,$qty,sisa_stock-$qty,'Barang keluar dengan no.sbbk ($nosbbk) dikirim ke puskesmas $permintaan_dari sebanyak $qty','keluar',$userid,'$username','$nama',NOW() FROM log_ks WHERE idobat=$idobat ORDER BY idlog DESC LIMIT 1";
                        $this->DB->insertRecord($str);
                    }
                }        
                $this->DB->updateRecord("UPDATE master_lpo SET response_lpo=3 WHERE idlpo=$idlpo");                
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
		$id=$_SESSION['currentPageSBBKBaru']['datasbbk']['idsbbk'];        
        $this->DB->deleteRecord("master_sbbk WHERE idsbbk=$id");
        $idlpo=$_SESSION['currentPageSBBKBaru']['datasbbk']['idlpo'];        
        $this->DB->updateRecord("UPDATE master_lpo SET response_lpo=1 WHERE idlpo=$idlpo");
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