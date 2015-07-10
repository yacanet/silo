<?php
prado::using ('Application.MainPageSA');
class SBBKBebas extends MainPageSA {
    public static $totalQTY=0;
    public static $totalHARGA=0;
    public $datasbbk;
	public function onLoad($param) {			
		parent::onLoad($param);				
        $this->showSubMenuMutasiBarangKeluar = true;
		$this->showSBBKBebas = true;        
        $this->createObj('DMaster');
        $this->createObj('Obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {                        
            if (!isset($_SESSION['currentPageSBBKBebas'])||$_SESSION['currentPageSBBKBebas']['page_name']!='sa.mutasibarang.SBBKBebas') {
                $_SESSION['currentPageSBBKBebas']=array('page_name'=>'sa.mutasibarang.SBBKBebas','page_num'=>0,'search'=>false,'datasbbk'=>array(),'cart'=>array());												
            }
            $this->detailProcess();		
		}	        
	}     
    public function viewRecord ($sender,$param) {                
        $id=$sender->CommandParameter;   
        $dataobat = $this->Obat->getInfoMasterObat($id);        
        $this->setInfoObat($dataobat);
        $this->modalInfoObat->show();
    }
    public function detailProcess() {
        $this->datasbbk = $_SESSION['currentPageSBBKBebas']['datasbbk'];          
        if ($this->datasbbk['mode'] == 'ubah') {                                             
            $this->cmbAddTanggalSBBK->Text=$this->TGL->tanggal('d-m-Y',$this->datasbbk['tanggal_sbbk']);                            
            $this->txtAddNoSBBK->Text=$this->datasbbk['no_sbbk'];                
            $this->txtAddNoPermintaan->Text=$this->datasbbk['no_lpo'];                            
            $this->txtAddPermintaan->Text=$this->datasbbk['permintaan_dari'];                            
            $this->txtAddKeperluan->Text=$this->datasbbk['keperluan'];
            $this->txtAddNoSPMB->Text=$this->datasbbk['no_spmb'];            
            $this->txtAddNIPPengemas->Text=$this->datasbbk['nip_pengemas'];            
            $this->txtAddNamaPengemas->Text=$this->datasbbk['nama_pengemas'];            
            $this->hiddenno_sbbk->Value=$this->datasbbk['no_sbbk'];
            $this->hiddenno_spmb->Value=$this->datasbbk['no_spmb'];            
        }      
        $this->populateCart();
    }
    public function getQTYFromCart($idobat){
        $cart=$_SESSION['currentPageSBBKBebas']['cart'];
        if (isset($cart[$idobat])) {
            return $cart[$idobat]['pemberian'];
        }else{
            return 0;
        }
        
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
                SBBKBebas::$totalQTY += $item->DataItem['pemberian'];
                $harga=$item->DataItem['pemberian']*$item->DataItem['harga']; 
                SBBKBebas::$totalHARGA += $harga;            
            }
            
        }
    }
    public function deleteItem($sender,$param) {
        $id=$this->getDataKeyField($sender,$this->RepeaterCart);
        unset($_SESSION['currentPageSBBKBebas']['cart'][$id]);        
        $this->populateCart();        
    }
    protected function populateCart () {                     
        $cart = $_SESSION['currentPageSBBKBebas']['cart'];                  
		$this->RepeaterCart->DataSource=$cart;
		$this->RepeaterCart->dataBind();             
	}
    public function checkBarcode ($sender,$param) {
        $this->idProcess='add';
        $barcode=$param->Value;		
        if ($barcode != '') {
            try {                
                $str = "SELECT iddetail_sbbm,nama_obat,harga,qty FROM detail_sbbm dsb,master_sbbm msb WHERE dsb.idsbbm=msb.idsbbm AND barcode='$barcode' AND status='complete'";
                $this->DB->setFieldTable(array('iddetail_sbbm','nama_obat','harga','qty'));
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
                $_SESSION['currentPageSBBKBebas']['cart'][$iddetail_sbbm]['stock']=$stock;
                $_SESSION['currentPageSBBKBebas']['cart'][$iddetail_sbbm]['pemberian']=$pemberian;                
                $this->redirect('mutasibarang.SBBKBebas',true);
            }else{
                $this->labelMessageError->Text="Jumlah pemberian ($pemberian) harus lebih kecil atau sama dengan jumlah stock ($qty)";
                $this->modalMessageError->show();
            }                      
        }
    }
    public function addObat ($sender,$param) {
        if ($this->IsValid) {
            $barcode=$this->txtAddBarcode->Text;
            $str = "SELECT dsb.iddetail_sbbm,dsb.idobat,dsb.kode_obat,dsb.nama_obat,dsb.harga,dsb.kemasan,dsb.qty FROM detail_sbbm dsb WHERE barcode='$barcode'";
            $this->DB->setFieldTable(array('iddetail_sbbm','idobat','kode_obat','nama_obat','harga','kemasan','qty'));
            $r=$this->DB->getRecord($str);            
            $dataobat=$r[1];
            $iddetail_sbbm=$dataobat['iddetail_sbbm'];
            $jumlah_keluar=$this->DB->getCountRowsOfTable ("kartu_stock WHERE iddetail_sbbm=$iddetail_sbbm AND mode='keluar' AND isdestroyed=0",'idkartu_stock');		
            $stock=$dataobat['qty']-$jumlah_keluar;
            $_SESSION['currentPageSBBKBebas']['cart'][$iddetail_sbbm]=array(
                                                                        'iddetail_sbbm'=>$iddetail_sbbm,
                                                                        'idobat'=>$dataobat['idobat'],
                                                                        'idobat_puskesmas'=>$dataobat['idobat_puskesmas'],
                                                                        'kode_obat'=>$dataobat['kode_obat'],
                                                                        'nama_obat'=>$dataobat['nama_obat'],
                                                                        'harga'=>$dataobat['harga'],                                                                    
                                                                        'kemasan'=>$dataobat['kemasan'],
                                                                        'stock'=>$stock,                                                                        
                                                                        'pemberian'=>$stock);
            $this->redirect('mutasibarang.SBBKBebas',true);
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
            $ta=$_SESSION['ta'];
            $nosbbk=addslashes($this->txtAddNoSBBK->Text);
            $tanggal_sbbk = date('Y-m-d',$this->cmbAddTanggalSBBK->TimeStamp);            
            $no_lpo=addslashes($this->txtAddNoPermintaan->Text);
            $permintaan_dari = addslashes($this->txtAddPermintaan->Text);
            $keperluan=addslashes($this->txtAddKeperluan->Text);
            $nospmb=addslashes($this->txtAddNoSPMB->Text);
            
            $nip_kadis=$this->setup->getSettingValue('nip_kadis');        
            $nama_kadis=$this->setup->getSettingValue('nama_kadis');        
            
            $nip_ka_gudang=$this->setup->getSettingValue('nip_ka_gudang');        
            $nama_ka_gudang=$this->setup->getSettingValue('nama_ka_gudang');                                     
            
            $nip_pengemas=addslashes($this->txtAddNIPPengemas->Text);
            $nama_pengemas=addslashes($this->txtAddNamaPengemas->Text);
            
            $this->DB->query('BEGIN');
            $datasbbk=$_SESSION['currentPageSBBKBebas']['datasbbk'];            
            if (isset($datasbbk['idsbbk'])) {
                $idsbbk=$datasbbk['idsbbk'];            
                $str = "UPDATE master_sbbk SET no_sbbk='$nosbbk',no_spmb='$nospmb',tanggal_sbbk='$tanggal_sbbk',no_lpo='$no_lpo',permintaan_dari='$permintaan_dari',keperluan='$keperluan',nip_kadis='$nip_kadis',nama_kadis='$nama_kadis',nip_ka_gudang='$nip_ka_gudang',nama_ka_gudang='$nama_ka_gudang',nip_pengemas='$nip_pengemas',nama_pengemas='$nama_pengemas',status='draft',date_modified=NOW() WHERE idsbbk=$idsbbk";
                if ($this->DB->updateRecord($str)) {                                                        
                    $datasbbk['no_sbbk']=$nosbbk;
                    $datasbbk['tanggal_sbbk']=$tanggal_sbbk;
                    $datasbbk['no_lpo']=$no_lpo;
                    $datasbbk['permintaan_dari']=$permintaan_dari;
                    $datasbbk['keperluan']=$keperluan;
                    $datasbbk['no_spmb']=$nospmb;                
                    $datasbbk['nip_pengemas']=$nip_pengemas;
                    $datasbbk['nama_pengemas']=$nama_pengemas;                
                    $_SESSION['currentPageSBBKBebas']['datasbbk']=$datasbbk;
                    $cart=$_SESSION['currentPageSBBKBebas']['cart'];
                    if (count($cart) > 0) {                        
                        $this->DB->deleteRecord("detail_sbbk WHERE idsbbk=$idsbbk");
                        foreach ($cart as $iddetail_sbbm=>$v) {                                                                  
                            $qty=$v['pemberian'];    
                            $str = "INSERT INTO detail_sbbk (iddetail_sbbm,idsbbk,idobat,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,pemberian,islpo,date_added,date_modified) SELECT $iddetail_sbbm,$idsbbk,idobat,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,'$qty',0,NOW(),NOW() FROM detail_sbbm WHERE iddetail_sbbm=$iddetail_sbbm";                                                
                            $this->DB->insertRecord($str); 
                        }
                    }
                    $this->DB->query('COMMIT');                    
                }else{
                    $this->DB->query('ROLLBACK');
                }
            }else{
                $str = "INSERT INTO master_sbbk (no_sbbk,no_spmb,tanggal_sbbk,no_lpo,permintaan_dari,keperluan,status,tahun,nip_kadis,nama_kadis,nip_ka_gudang,nama_ka_gudang,nip_pengemas,nama_pengemas,date_added,date_modified) VALUES ('$nosbbk','$nospmb','$tanggal_sbbk','$no_lpo','$permintaan_dari','$keperluan','draft',$ta,'$nip_kadis','$nama_kadis','$nip_ka_gudang','$nama_ka_gudang','$nip_pengemas','$nama_pengemas',NOW(),NOW())";
                if ($this->DB->insertRecord($str)) {
                    $idsbbk=$this->DB->getLastInsertID ();               
                    
                    $datasbbk=array('idsbbk'=>$idsbbk,'no_sbbk'=>$nosbbk,'tanggal_sbbk'=>$tanggal_sbbk,'no_lpo'=>$no_lpo,'permintaan_dari'=>$permintaan_dari,'keperluan'=>$keperluan,'no_spmb'=>$nospmb,'nip_ka_gudang'=>$nip_ka_gudang,'nama_ka_gudang'=>$nama_ka_gudang,'nip_pengemas'=>$nama_pengemas,'nama_pengemas'=>$nama_pengemas,'tahun'=>$ta,'mode'=>'ubah');            
                    $_SESSION['currentPageSBBKBebas']['datasbbk']=$datasbbk; 

                    $cart=$_SESSION['currentPageSBBKBebas']['cart'];
                    if (count($cart) > 0) {
                        foreach ($cart as $iddetail_sbbm=>$v) {
                            $qty=$v['pemberian'];    
                            $str = "INSERT INTO detail_sbbk (iddetail_sbbm,idsbbk,idobat,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,pemberian,islpo,date_added,date_modified) SELECT $iddetail_sbbm,$idsbbk,idobat,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,'$qty',0,NOW(),NOW() FROM detail_sbbm WHERE iddetail_sbbm=$iddetail_sbbm";                                                
                            $this->DB->insertRecord($str);                        
                        }
                    }               
                    $this->DB->query('COMMIT'); 
                }else{
                    $this->DB->query('ROLLBACK');
                }
            }
            $this->redirect('mutasibarang.SBBKBebas',true);
        }
    }
    public function checkOut ($sender,$param) {
        if ($this->IsValid) {           
            $datasbbk=$_SESSION['currentPageSBBKBebas']['datasbbk'];
            $idsbbk=$datasbbk['idsbbk']; 
            $no_lpo=addslashes($this->txtAddNoPermintaan->Text);
            $permintaan_dari=  addslashes($this->txtAddPermintaan->Text);
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
            $userid=$this->Pengguna->getDataUser('userid');
            $username=$this->Pengguna->getDataUser('username');
            $nama=$this->Pengguna->getDataUser('nama');
            if (isset($datasbbk['idsbbk'])) {            
                $str = "UPDATE master_sbbk SET no_sbbk='$nosbbk',no_spmb='$nospmb',tanggal_sbbk='$tanggal_sbbk',no_lpo='$no_lpo',permintaan_dari='$permintaan_dari',keperluan='$keperluan',status='complete',nip_kadis='$nip_kadis',nama_kadis='$nama_kadis',nip_ka_gudang='$nip_ka_gudang',nama_ka_gudang='$nama_ka_gudang',nip_pengemas='$nip_pengemas',nama_pengemas='$nama_pengemas',response_sbbk=5,date_modified=NOW() WHERE idsbbk=$idsbbk";

                if ($this->DB->updateRecord($str)) {    
                    $datasbbk['issaved']=true;                                        
                    $datasbbk['no_sbbk']=$nosbbk;
                    $datasbbk['tanggal_sbbk']=$tanggal_sbbk;
                    $datasbbk['permintaan_dari']=$permintaan_dari;
                    $datasbbk['keperluan']=$keperluan;
                    $datasbbk['no_spmb']=$nospmb;                
                    $_SESSION['currentPageSBBKBebas']['datasbbk']=$datasbbk;
                    $cart=$_SESSION['currentPageSBBKBebas']['cart'];
                    if (count($cart) > 0) {                    
                        $this->DB->deleteRecord("detail_sbbk WHERE idsbbk=$idsbbk");
                        $bulan=$this->TGL->tanggal('m',$tanggal_sbbk);
                        $tahun=$_SESSION['ta'];
                        foreach ($cart as $iddetail_sbbm=>$v) {
                            $idobat=$v['idobat'];                                
                            $qty=$v['pemberian'];    
                            $str = "INSERT INTO detail_sbbk (iddetail_sbbm,idsbbk,idobat,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,pemberian,islpo,date_added,date_modified) SELECT $iddetail_sbbm,$idsbbk,idobat,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,'$qty',0,NOW(),NOW() FROM detail_sbbm WHERE iddetail_sbbm=$iddetail_sbbm";                                                                                
                            $this->DB->insertRecord($str);
                            $iddetail_sbbk=$this->DB->getLastInsertID ();
                            $this->DB->updateRecord("UPDATE master_obat SET stock=stock-$qty WHERE idobat=$idobat");
                            $str = "UPDATE kartu_stock ks,(SELECT idkartu_stock FROM kartu_stock WHERE mode='masuk' AND iddetail_sbbm=$iddetail_sbbm AND isdestroyed=0 LIMIT $qty) AS temp SET idsbbk=$idsbbk,iddetail_sbbk=$iddetail_sbbk,mode='keluar',date_modified=NOW() WHERE ks.idkartu_stock=temp.idkartu_stock";                        
                            $this->DB->updateRecord($str);
                            $str = "INSERT INTO log_ks (idobat,idsbbk,iddetail_sbbk,tanggal,bulan,tahun,qty,sisa_stock,keterangan,mode,userid,username,nama,date_added) SELECT $idobat,$idsbbk,$iddetail_sbbk,'$tanggal_sbbk','$bulan',$tahun,$qty,sisa_stock-$qty,'Barang keluar dengan no.sbbk ($nosbbk) dikirim ke $permintaan_dari sebanyak $qty','keluar',$userid,'$username','$nama',NOW() FROM log_ks WHERE idobat=$idobat ORDER BY idlog DESC LIMIT 1";
                            $this->DB->insertRecord($str);
                        }
                    }             
                    $_SESSION['currentPageDaftarSBBK']['datasbbk']=$datasbbk;
                    $_SESSION['currentPageDaftarSBBK']['cart']=$cart;
                    $this->DB->query('COMMIT');
                    unset($_SESSION['currentPageSBBKBebas']['datasbbk']);
                    unset($_SESSION['currentPageSBBKBebas']['cart']);
                    $this->redirect('mutasibarang.DaftarSBBK',true);
                }else{
                    $this->DB->query('ROLLBACK');
                }
            }else{
                $ta=$_SESSION['ta'];                
                $str = "INSERT INTO master_sbbk (no_sbbk,no_spmb,tanggal_sbbk,no_lpo,permintaan_dari,keperluan,status,tahun,nip_kadis,nama_kadis,nip_ka_gudang,nama_ka_gudang,nip_pengemas,nama_pengemas,response_sbbk,date_added,date_modified) VALUES ('$nosbbk','$nospmb','$tanggal_sbbk','$no_lpo','$permintaan_dari','$keperluan','complete',$ta,'$nip_kadis','$nama_kadis','$nip_ka_gudang','$nama_ka_gudang','$nip_pengemas','$nama_pengemas',5,NOW(),NOW())";
                if ($this->DB->insertRecord($str)) {
                    $idsbbk=$this->DB->getLastInsertID ();                    
                    
                    $cart=$_SESSION['currentPageSBBKBebas']['cart'];
                    if (count($cart) > 0) {                    
                        $this->DB->deleteRecord("detail_sbbk WHERE idsbbk=$idsbbk");
                        $bulan=$this->TGL->tanggal('m',$tanggal_sbbk);
                        $tahun=$_SESSION['ta'];
                        foreach ($cart as $iddetail_sbbm=>$v) {
                            $idobat=$v['idobat'];                                
                            $qty=$v['pemberian'];    
                            $str = "INSERT INTO detail_sbbk (iddetail_sbbm,idsbbk,idobat,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,pemberian,islpo,date_added,date_modified) SELECT $iddetail_sbbm,$idsbbk,idobat,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,'$qty',0,NOW(),NOW() FROM detail_sbbm WHERE iddetail_sbbm=$iddetail_sbbm";                                                                                
                            $this->DB->insertRecord($str);
                            $iddetail_sbbk=$this->DB->getLastInsertID ();
                            $this->DB->updateRecord("UPDATE master_obat SET stock=stock-$qty WHERE idobat=$idobat");
                            $str = "UPDATE kartu_stock ks,(SELECT idkartu_stock FROM kartu_stock WHERE mode='masuk' AND iddetail_sbbm=$iddetail_sbbm AND isdestroyed=0 LIMIT $qty) AS temp SET idsbbk=$idsbbk,iddetail_sbbk=$iddetail_sbbk,mode='keluar',date_modified=NOW() WHERE ks.idkartu_stock=temp.idkartu_stock";                        
                            $this->DB->updateRecord($str);
                            $str = "INSERT INTO log_ks (idobat,idsbbk,iddetail_sbbk,tanggal,bulan,tahun,qty,sisa_stock,keterangan,mode,userid,username,nama,date_added) SELECT $idobat,$idsbbk,$iddetail_sbbk,'$tanggal_sbbk','$bulan',$tahun,$qty,sisa_stock-$qty,'Barang keluar dengan no.sbbk ($nosbbk) dikirim ke $permintaan_dari sebanyak $qty','keluar',$userid,'$username','$nama',NOW() FROM log_ks WHERE idobat=$idobat ORDER BY idlog DESC LIMIT 1";                                    
                            $this->DB->insertRecord($str);
                        }
                    }
                    $_SESSION['currentPageDaftarSBBK']['datasbbk']=$datasbbk;
                    $_SESSION['currentPageDaftarSBBK']['cart']=$cart;
                    $this->DB->query('COMMIT');
                    unset($_SESSION['currentPageSBBKBebas']['datasbbk']);
                    unset($_SESSION['currentPageSBBKBebas']['cart']);
                    $this->redirect('mutasibarang.DaftarSBBK',true);
                }else{
                    $this->DB->query('ROLLBACK');
                }
            }
        }
    }    
    public function batalSBBK ($sender,$param) {
		$id=$_SESSION['currentPageSBBKBebas']['datasbbk']['idsbbk'];        
        $this->DB->deleteRecord("master_sbbk WHERE idsbbk='$id'");
        unset($_SESSION['currentPageSBBKBebas']['datasbbk']);
        unset($_SESSION['currentPageSBBKBebas']['cart']);
        $this->redirect('mutasibarang.SBBKBebas',true);
	}
    public function closeSBBK ($sender,$param) {
        unset($_SESSION['currentPageSBBKBebas']['datasbbk']);
        unset($_SESSION['currentPageSBBKBebas']['cart']);
        $this->redirect('mutasibarang.SBBKBebas',true);
    }
    
}
?>