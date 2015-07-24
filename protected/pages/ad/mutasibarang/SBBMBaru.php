<?php
prado::using ('Application.MainPageAD');
class SBBMBaru extends MainPageAD {
    public static $totalQTY=0;
    public static $totalHARGA=0;
    public $datasbbm;
	public function onLoad($param) {			
		parent::onLoad($param);				
        $this->showSubMenuMutasiBarangMasuk = true;
		$this->showSBBMBaru = true;        
        $this->createObj('DMaster');
        $this->createObj('Obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {            
            if (isset($_SESSION['currentPageSBBMBaru']['datasbbm']['idsbbk'])) {
                $_SESSION['currentPageObat']['search']=false;                
                $this->detailProcess();                                
                $this->populateCart();
            }else {
                if (!isset($_SESSION['currentPageSBBMBaru'])||$_SESSION['currentPageSBBMBaru']['page_name']!='ad.mutasibarang.SBBMBaru') {
                    $_SESSION['currentPageSBBMBaru']=array('page_name'=>'ad.mutasibarang.SBBMBaru','page_num'=>0,'search'=>false,'datasbbm'=>array(),'idprodusen'=>'none','cart'=>array());												
                }
            }			
		}	        
	}
    public function checkBuatSBBMBaru ($sender,$param) {        
        $no_sbbk=$param->Value;		
        if ($no_sbbk != '') {
            try {            
                $idpuskesmas=$this->idpuskesmas;
                $str = "SELECT msb.idsbbk,msb.no_sbbk,msb.tanggal_sbbk,msb.idpuskesmas,msb.permintaan_dari,msb.idlpo,msb.no_lpo,msb.tanggal_lpo,msb.keperluan,nip_penerima,nama_penerima,tanggal_diterima,msb.no_spmb,msb.nip_ka_gudang,msb.nama_ka_gudang,msb.nip_pengemas,msb.nama_pengemas,msb.response_sbbk,msb.tahun FROM master_sbbk msb,master_lpo mlp WHERE mlp.idlpo=msb.idlpo AND msb.no_sbbk='$no_sbbk' AND mlp.response_lpo=3";
                $this->DB->setFieldTable(array('idsbbk','no_sbbk','tanggal_sbbk','idpuskesmas','permintaan_dari','idlpo','no_lpo','tanggal_lpo','keperluan','no_spmb','nip_penerima','nama_penerima','tanggal_diterima','nip_ka_gudang','nama_ka_gudang','nip_pengemas','nama_pengemas','response_sbbk','tahun'));
                $r=$this->DB->getRecord($str);                        
                if (isset($r[1])) {
                    $datasbbk=$r[1];
                    if ($idpuskesmas != $datasbbk['idpuskesmas'])throw new Exception ("Nomor SBBK ($no_sbbk) bukan untuk UPT Puskesmas {$datasbbk['nama_puskesmas']}.");		                    
                    if ($datasbbk['response_sbbk']==5)throw new Exception ("Nomor SBBK ($no_sbbk) telah selesai diproses (final).");		
                    if ($this->DB->checkRecordIsExist('idsbbk_gudang','master_sbbm_puskesmas',$datasbbk['idsbbk'])) {
                        throw new Exception ("Nomor SBBK ($no_sbbk) telah ada didatabase, untuk mengubahnya melalui menu Daftar SBBM.");		
                    }
                    $datasbbk['tanggal_diterima']=date('Y-m-d');
                    $_SESSION['currentPageSBBMBaru']['datasbbm']=$datasbbk;                         
                }else{
                    throw new Exception ("Nomor SBBK ($no_sbbk) tidak tersedia silahkan ganti dengan yang lain.");		
                }
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }
    }
    public function buatSBBM($sender,$param) {
        if ($this->IsValid) {  
            $datasbbk = $_SESSION['currentPageSBBMBaru']['datasbbm'];
            $idpuskesmas=$datasbbk['idpuskesmas'];
            $idsbbk_gudang=$datasbbk['idsbbk'];
            $tanggal_sbbk_gudang=$datasbbk['tanggal_sbbk'];
            $no_sbbk_gudang=$datasbbk['no_sbbk'];
            $no_spmb_gudang=$datasbbk['no_spmb'];
            $tanggal_sbbm_puskesmas=$datasbbk['tanggal_diterima'];
            $tahun_puskesmas=$datasbbk['tahun'];            
            $str = "INSERT INTO master_sbbm_puskesmas (idpuskesmas,idsbbk_gudang,tanggal_sbbk_gudang,no_sbbk_gudang,no_spmb_gudang,tanggal_sbbm_puskesmas,tahun_puskesmas,date_added,date_modified) VALUES ($idpuskesmas,$idsbbk_gudang,'$tanggal_sbbk_gudang','$no_sbbk_gudang','$no_spmb_gudang','$tanggal_sbbm_puskesmas',$tahun_puskesmas,NOW(),NOW())";
            $this->DB->insertRecord($str);
            $idsbbm_puskesmas=$this->DB->getLastInsertID ();
            $_SESSION['currentPageSBBMBaru']['datasbbm']['idsbbm_puskesmas']=$idsbbm_puskesmas;          
            $this->redirect('mutasibarang.SBBMBaru',true);
        }
    }    
    public function detailProcess() {
        $this->idProcess='add';
        $this->datasbbm = $_SESSION['currentPageSBBMBaru']['datasbbm'];                
        $this->cmbAddTanggalSBBM->Text=$this->TGL->tanggal('d-m-Y',$this->datasbbm['tanggal_diterima']);
        $this->txtAddNIPPenerima->Text=$this->datasbbm['nip_penerima'];
        $this->txtAddNamaPenerima->Text=$this->datasbbm['nama_penerima'];
        
        $status_puskesmas=$_SESSION['currentPageSBBMBaru']['datasbbm']['status_puskesmas'];
        if ($status_puskesmas == 'complete') {
            $this->btnSave->Enabled=false;
            $this->btnBatal->Enabled=false;
        }
    }
    public function renderCallback ($sender,$param) {
        $this->idProcess='add';
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageSBBMBaru']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageSBBMBaru']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageSBBMBaru']['search']=true;
        $this->populateData($_SESSION['currentPageSBBMBaru']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageSBBMBaru']['idprodusen']=$this->cmbFilterProdusen->Text;
        $this->populateData();
	}    
    protected function populateCart () {                     
        $idsbbk=$_SESSION['currentPageSBBMBaru']['datasbbm']['idsbbk'];
        $str = "SELECT dsb.iddetail_sbbk,dsb.kode_obat,dsb.nama_obat,dsb.harga,dsb.kemasan,dsb.stock_awal,dsb.penerimaan AS total_penerimaan,dsb.persediaan,dsb.pemakaian AS total_pemakaian,dsb.stock_akhir,dsb.permintaan,dsb.pemberian,dsb.ischecked FROM detail_sbbk dsb WHERE idsbbk='$idsbbk'";        
        $this->DB->setFieldTable(array('iddetail_sbbk','kode_obat','nama_obat','harga','kemasan','stock_awal','total_penerimaan','persediaan','total_pemakaian','stock_akhir','permintaan','pemberian','ischecked'));
        $r=$this->DB->getRecord($str);     

		$this->RepeaterCart->DataSource=$r;
		$this->RepeaterCart->dataBind();             
	}       
    public function itemCreatedCart($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {                        
            SBBMBaru::$totalQTY += $item->DataItem['pemberian'];
            SBBMBaru::$totalHARGA += $item->DataItem['harga'];      
            $status_puskesmas=$_SESSION['currentPageSBBMBaru']['datasbbm']['status_puskesmas'];
            $item->chkChecked->Checked=$item->DataItem['ischecked'];      
            if ($status_puskesmas == 'complete') {                
                $item->chkChecked->Enabled=!$item->DataItem['ischecked'];
            }            
        }
    }    
    public function batalSBBM ($sender,$param) {
		$id=$_SESSION['currentPageSBBMBaru']['datasbbm']['idsbbm_puskesmas'];        
        $this->DB->deleteRecord("master_sbbm_puskesmas WHERE idsbbm_puskesmas=$id");
        $idsbbk=$_SESSION['currentPageSBBMBaru']['datasbbm']['idsbbk'];        
        $str = "UPDATE master_sbbk SET tanggal_diterima='0000-00-00',nip_penerima='',nama_penerima='',date_modified=NOW() WHERE idsbbk=$idsbbk";
        $this->DB->updateRecord($str);
        unset($_SESSION['currentPageSBBMBaru']['datasbbm']);
        unset($_SESSION['currentPageSBBMBaru']['cart']);
        $this->redirect('mutasibarang.SBBMBaru',true);
	}
    public function saveData ($sender,$param) {
        if ($this->IsValid) {      
            $datasbbm=$_SESSION['currentPageSBBMBaru']['datasbbm'];
            $idsbbm_puskesmas=$datasbbm['idsbbm_puskesmas'];            
            $idsbbk_gudang=$datasbbm['idsbbk'];            
            $tanggal_sbbm = date('Y-m-d',$this->cmbAddTanggalSBBM->TimeStamp);            
            $nip_penerima=$this->txtAddNIPPenerima->Text;                                    
            $penerima=addslashes($this->txtAddNamaPenerima->Text);
            $_SESSION['currentPageSBBMBaru']['datasbbm']['tanggal_diterima']=$tanggal_sbbm;
            $_SESSION['currentPageSBBMBaru']['datasbbm']['nip_penerima']=$nip_penerima;
            $_SESSION['currentPageSBBMBaru']['datasbbm']['nama_penerima']=$penerima;
            $this->DB->query('BEGIN');
            $str = "UPDATE master_sbbm_puskesmas SET tanggal_sbbm_puskesmas='$tanggal_sbbm',nip_penerima_puskesmas='$nip_penerima',nama_penerima_puskesmas='$penerima',status_puskesmas='draft',date_modified=NOW() WHERE idsbbm_puskesmas=$idsbbm_puskesmas";
            if ($this->DB->updateRecord($str)) {    
                $str = "UPDATE master_sbbk SET tanggal_diterima='$tanggal_sbbm',nip_penerima='$nip_penerima',nama_penerima='$penerima',response_sbbk='3',date_modified=NOW() WHERE idsbbk=$idsbbk_gudang";
                $this->DB->updateRecord($str);
                $this->DB->deleteRecord("detail_sbbm_puskesmas WHERE idsbbm_puskesmas=$idsbbm_puskesmas");
                foreach ($this->RepeaterCart->Items as $inputan) {
                    $item=$inputan->chkChecked->getNamingContainer();
                    $iddetail_sbbk=$this->RepeaterCart->DataKeys[$item->getItemIndex()];                        
                    if ($inputan->chkChecked->Checked) {                           
                        $str = "INSERT INTO detail_sbbm_puskesmas (idsbbm_puskesmas,iddetail_sbbk_gudang,idprogram_gudang,idsumber_dana_gudang,idobat,idobat_puskesmas,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,no_batch,qty,tanggal_expire,barcode,date_added,date_modified) SELECT $idsbbm_puskesmas,dsb.iddetail_sbbk,msb.idprogram,msb.idsumber_dana,dsb.idobat,dsb.idobat_puskesmas,dsb.kode_obat,dsb.nama_obat,dsb.harga,dsb.idsatuan_obat,dsb.idgolongan,dsb.kemasan,dsb.no_batch,dsb.pemberian,dsb2.tanggal_expire,dsb2.barcode,NOW(),NOW() FROM detail_sbbk dsb, kartu_stock ks,detail_sbbm dsb2,master_sbbm msb WHERE dsb.iddetail_sbbk=ks.iddetail_sbbk AND dsb2.iddetail_sbbm=ks.iddetail_sbbm AND msb.idsbbm=ks.idsbbm AND dsb.iddetail_sbbk=$iddetail_sbbk LIMIT 1";
                        $this->DB->insertRecord($str);     
                        
                        $str = "UPDATE detail_sbbk SET ischecked=true WHERE iddetail_sbbk=$iddetail_sbbk";
                        $this->DB->updateRecord($str);                                                
                    }
                }
                $this->DB->query('COMMIT');
                $this->redirect('mutasibarang.SBBMBaru',true);
            }else{
                $this->DB->query('ROLLBACK');
            }            
        }
    }
    public function checkOut ($sender,$param) {
        if ($this->IsValid) {                       
            $datasbbm=$_SESSION['currentPageSBBMBaru']['datasbbm'];
            $idpuskesmas=$datasbbm['idpuskesmas'];
            $idsbbm_puskesmas=$datasbbm['idsbbm_puskesmas'];            
            $idsbbk_gudang=$datasbbm['idsbbk'];            
            $idlpo=$datasbbm['idlpo'];
            $nosbbk=$datasbbm['no_sbbk'];
            $tanggal_sbbm = date('Y-m-d',$this->cmbAddTanggalSBBM->TimeStamp);            
            $nip_penerima=$this->txtAddNIPPenerima->Text;                                    
            $penerima=addslashes($this->txtAddNamaPenerima->Text);            
            $_SESSION['currentPageSBBMBaru']['datasbbm']['tanggal_diterima']=$tanggal_sbbm;
            $_SESSION['currentPageSBBMBaru']['datasbbm']['nip_penerima']=$nip_penerima;
            $_SESSION['currentPageSBBMBaru']['datasbbm']['nama_penerima']=$penerima;
            $this->DB->query('BEGIN');
            $str = "UPDATE master_sbbm_puskesmas SET tanggal_sbbm_puskesmas='$tanggal_sbbm',nip_penerima_puskesmas='$nip_penerima',nama_penerima_puskesmas='$penerima',status_puskesmas='complete',date_modified=NOW() WHERE idsbbm_puskesmas=$idsbbm_puskesmas";            
            $userid=$this->Pengguna->getDataUser('userid');
            $username=$this->Pengguna->getDataUser('username');
            $nama=$this->Pengguna->getDataUser('nama');
            if ($this->DB->updateRecord($str)) {    
                $str = "UPDATE master_sbbk SET tanggal_diterima='$tanggal_sbbm',nip_penerima='$nip_penerima',nama_penerima='$penerima',response_sbbk='5',date_modified=NOW() WHERE idsbbk=$idsbbk_gudang";
                $this->DB->updateRecord($str);
                $this->DB->deleteRecord("detail_sbbm_puskesmas WHERE idsbbm_puskesmas=$idsbbm_puskesmas");
                $str = "UPDATE master_lpo SET response_lpo=5,date_modified=NOW() WHERE idlpo=$idlpo";
                $this->DB->updateRecord($str);                
                $bulan=$this->TGL->tanggal('m',$tanggal_sbbm);
                $tahun=$_SESSION['ta'];
                foreach ($this->RepeaterCart->Items as $inputan) {
                    $item=$inputan->chkChecked->getNamingContainer();
                    $iddetail_sbbk=$this->RepeaterCart->DataKeys[$item->getItemIndex()];                        
                    if ($inputan->chkChecked->Checked) {                                                
                        $str = "INSERT INTO detail_sbbm_puskesmas (idsbbm_puskesmas,iddetail_sbbk_gudang,idprogram_gudang,idsumber_dana_gudang,idobat,idobat_puskesmas,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,no_batch,qty,tanggal_expire,barcode,date_added,date_modified) SELECT $idsbbm_puskesmas,dsb.iddetail_sbbk,msb.idprogram,msb.idsumber_dana,dsb.idobat,dsb.idobat_puskesmas,dsb.kode_obat,dsb.nama_obat,dsb.harga,dsb.idsatuan_obat,dsb.idgolongan,dsb.kemasan,dsb.no_batch,dsb.pemberian,dsb2.tanggal_expire,dsb2.barcode,NOW(),NOW() FROM detail_sbbk dsb, kartu_stock ks,detail_sbbm dsb2,master_sbbm msb WHERE dsb.iddetail_sbbk=ks.iddetail_sbbk AND dsb2.iddetail_sbbm=ks.iddetail_sbbm AND msb.idsbbm=ks.idsbbm AND dsb.iddetail_sbbk=$iddetail_sbbk LIMIT 1";
                        $this->DB->insertRecord($str);
                        $iddetail_sbbm_puskesmas=$this->DB->getLastInsertID ();
                        $str = "UPDATE detail_sbbk SET ischecked=true WHERE iddetail_sbbk=$iddetail_sbbk";
                        $this->DB->updateRecord($str);                        
                        if ($inputan->chkChecked->Enabled) { // bagi yang sudah, tidak akan dijalankan perintah dalam blok ini.
                            $str = "INSERT INTO kartu_stock_puskesmas (idobat,idobat_puskesmas,idpuskesmas,idsbbm_puskesmas,iddetail_sbbm_puskesmas,tanggal_puskesmas,bulan_puskesmas,tahun_puskesmas,tanggal_expire_puskesmas,mode_puskesmas,isopname_puskesmas,islocked_puskesmas,date_added,date_modified) "
                                . "SELECT ks.idobat,dsb.idobat_puskesmas,$idpuskesmas,$idsbbm_puskesmas,$iddetail_sbbm_puskesmas,'$tanggal_sbbm','$bulan',$tahun,tanggal_expire,'masuk',0,1,NOW(),NOW() FROM kartu_stock ks,detail_sbbk dsb WHERE ks.iddetail_sbbk=dsb.iddetail_sbbk AND ks.iddetail_sbbk=$iddetail_sbbk";

                            $this->DB->insertRecord($str);                        

                            $str = "INSERT INTO kartu_stock_puskesmas_dinas (idobat,idobat_puskesmas,idpuskesmas,idsbbm_puskesmas,iddetail_sbbm_puskesmas,tanggal_puskesmas,bulan_puskesmas,tahun_puskesmas,tanggal_expire_puskesmas,mode_puskesmas,isopname_puskesmas,islocked_puskesmas,date_added,date_modified) "
                                . "SELECT ks.idobat,dsb.idobat_puskesmas,$idpuskesmas,$idsbbm_puskesmas,$iddetail_sbbm_puskesmas,'$tanggal_sbbm','$bulan',$tahun,tanggal_expire,'masuk',0,1,NOW(),NOW() FROM kartu_stock ks,detail_sbbk dsb WHERE ks.iddetail_sbbk=dsb.iddetail_sbbk AND ks.iddetail_sbbk=$iddetail_sbbk";

                            $this->DB->insertRecord($str);                        

                            $str = "INSERT INTO log_ks_puskesmas (idpuskesmas,idobat,idobat_puskesmas,idsbbm_puskesmas,iddetail_sbbm_puskesmas,tanggal_puskesmas,bulan_puskesmas,tahun_puskesmas,qty_puskesmas,sisa_stock_puskesmas,keterangan_puskesmas,mode_puskesmas,userid_puskesmas,username_puskesmas,nama_user_puskesmas,date_added) SELECT $idpuskesmas,dsp.idobat,dsp.idobat_puskesmas,dsp.idsbbm_puskesmas,dsp.iddetail_sbbm_puskesmas,'$tanggal_sbbm','$bulan',$tahun,dsp.qty,dsp.qty+mop.stock,CONCAT('Barang masuk dari gudang farmasi dengan no.sbbk ($nosbbk) sebanyak ',dsp.qty),'masuk',$userid,'$username','$nama',NOW() FROM detail_sbbm_puskesmas dsp,master_obat_puskesmas mop WHERE mop.idobat_puskesmas=dsp.idobat_puskesmas AND dsp.iddetail_sbbm_puskesmas=$iddetail_sbbm_puskesmas";
                            $this->DB->insertRecord($str);                        

                            $this->DB->updateRecord("UPDATE master_obat_puskesmas mop,(SELECT idobat_puskesmas,qty FROM detail_sbbm_puskesmas WHERE iddetail_sbbm_puskesmas=$iddetail_sbbm_puskesmas) AS temp SET stock=stock+temp.qty,stock_dinas=stock_dinas+temp.qty WHERE mop.idobat_puskesmas=temp.idobat_puskesmas");                                              
                        }                                               
                    }
                }
                $this->DB->query('COMMIT');
                $_SESSION['currentPageDaftarSBBM']['datasbbm']=$_SESSION['currentPageSBBMBaru']['datasbbm'];
                unset($_SESSION['currentPageSBBMBaru']['datasbbm']);
                unset($_SESSION['currentPageSBBMBaru']['cart']);
                $this->redirect('mutasibarang.DaftarSBBM',true);
            }else{
                $this->DB->query('ROLLBACK');
            }                          
        }
    }    
    public function closeSBBM ($sender,$param) {
        unset($_SESSION['currentPageSBBMBaru']['datasbbm']);
        unset($_SESSION['currentPageSBBMBaru']['cart']);
        $this->redirect('mutasibarang.SBBMBaru',true);
    }
    
}
?>