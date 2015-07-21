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
                $this->idProcess='add';
                $_SESSION['currentPageObat']['search']=false;                
                $this->detailProcess();                
                $this->populateData();
                $this->populateCart();
            }else {
                if (!isset($_SESSION['currentPageLPOBaru'])||$_SESSION['currentPageLPOBaru']['page_name']!='ad.permintaan.LPOBaru') {
                    $_SESSION['currentPageLPOBaru']=array('page_name'=>'ad.permintaan.LPOBaru','page_num'=>0,'search'=>false,'datalpo'=>array(),'cart'=>array(),'tanggal_lpo'=>date('Y-m-d'),'status_lpo'=>'complete');												
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
            $tanggal_sebelumnya=$this->TGL->getMonthAndYearBefore ($tanggal_lpo);        
            $str = "INSERT INTO master_lpo (no_lpo,idpuskesmas,tanggal_lpo,nip_ka,nama_ka,nip_pengelola_obat,nama_pengelola_obat,nip_kadis,nama_kadis,nip_ka_gudang,nama_ka_gudang,tahun,date_added,date_modified) VALUES ('$nolpo',$idpuskesmas,'$tanggal_lpo','$nip_ka','$nama_ka','$nip_pengelola','$nama_pengelola','$nip_kadis','$nama_kadis','$nip_ka_gudang','$nama_ka_gudang',$ta,NOW(),NOW())";
            $this->DB->query('BEGIN');
            if ($this->DB->insertRecord($str) ) {
                $idlpo=$this->DB->getLastInsertID ();
                $tanggal_sebelumnya=$this->TGL->getMonthAndYearBefore ($tanggal_lpo);        
                $str = "SELECT dsb.iddetail_sbbm_puskesmas,mo.idobat_puskesmas AS mst_idobat_puskesmas,dsb.idobat_puskesmas,mo.idobat AS mst_idobat,dsb.idobat,mo.kode_obat AS mst_kode_obat,dsb.kode_obat,mo.nama_obat AS mst_nama_obat,dsb.nama_obat,mo.idsatuan_obat AS mst_idsatuan_obat,dsb.idsatuan_obat,mo.harga AS mst_harga,dsb.harga,mo.idgolongan AS mst_idgolongan,dsb.idgolongan,mo.idgolongan AS mst_idgolongan,dsb.idgolongan,mo.kemasan AS mst_kemasan,dsb.kemasan FROM master_obat_puskesmas mo LEFT JOIN (SELECT temp2.iddetail_sbbm_puskesmas,temp2.idobat,temp2.idobat_puskesmas,temp2.kode_obat,temp2.nama_obat,temp2.harga,temp2.idsatuan_obat,temp2.idgolongan,temp2.kemasan,temp2.tanggal_expire FROM master_sbbm_puskesmas temp1,detail_sbbm_puskesmas temp2 WHERE temp1.idsbbm_puskesmas=temp2.idsbbm_puskesmas AND DATE_FORMAT(temp1.tanggal_sbbm_puskesmas,'%Y-%m')='$tanggal_sebelumnya') AS dsb ON (dsb.idobat_puskesmas=mo.idobat_puskesmas) WHERE mo.idpuskesmas=$idpuskesmas GROUP BY mo.idobat_puskesmas,dsb.harga ORDER BY ISNULL(dsb.tanggal_expire),dsb.tanggal_expire ASC,mo.nama_obat ASC";
                $this->DB->setFieldTable(array('iddetail_sbbm_puskesmas','mst_idobat_puskesmas','idobat_puskesmas','mst_idobat','idobat','mst_kode_obat','kode_obat','mst_nama_obat','nama_obat','mst_idsatuan_obat','idsatuan_obat','mst_harga','harga','mst_idgolongan','idgolongan','mst_kemasan','kemasan'));
                $r=$this->DB->getRecord($str);          
                
                while (list($k,$v)=each($r)) {                
                    $iddetail_sbbm_puskesmas=$v['iddetail_sbbm_puskesmas'];
                    if ($iddetail_sbbm_puskesmas=='') {
                        $idobat_puskesmas=$v['mst_idobat_puskesmas'];            
                        $idobat=$v['mst_idobat'];            
                        $kode_obat=$v['mst_kode_obat'];
                        $nama_obat=$v['mst_nama_obat'];
                        $idsatuan_obat=$v['mst_idsatuan_obat'];
                        $harga=$v['mst_harga'];                    
                        $idgolongan=$v['mst_idgolongan'];                    
                        $kemasan=$v['mst_kemasan'];                    
                    }else{
                        $idobat_puskesmas=$v['idobat_puskesmas'];            
                        $idobat=$v['idobat'];            
                        $kode_obat=$v['kode_obat'];
                        $nama_obat=$v['nama_obat'];
                        $idsatuan_obat=$v['idsatuan_obat'];
                        $harga=$v['harga'];                                    
                        $idgolongan=$v['idgolongan'];                    
                        $kemasan=$v['kemasan'];                    
                    }
                    $str = "INSERT INTO detail_lpo (idlpo,idobat,idobat_puskesmas,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,date_added,date_modified) VALUES "
                    . "($idlpo,$idobat,$idobat_puskesmas,'$kode_obat','$nama_obat',$harga,$idsatuan_obat,$idgolongan,'$kemasan',NOW(),NOW())";
                    $this->DB->insertRecord($str);
                }                
                $this->DB->query('COMMIT');
                $_SESSION['currentPageLPOBaru']['datalpo']=array('idlpo'=>$idlpo,'no_lpo'=>$nolpo,'idpuskesmas'=>$idpuskesmas,'nip_ka'=>$nip_ka,'nama_ka'=>$nama_ka,'nip_pengelola'=>$nip_pengelola,'nama_pengelola'=>$nama_pengelola,'nip_kadis'=>$nip_kadis,'nama_kadis'=>$nama_kadis,'nip_ka_gudang'=>$nip_ka_gudang,'nama_ka_gudang'=>$nama_ka_gudang,'mode'=>'buat','issaved'=>false,'response_lpo'=>1);
            }else{
                $this->DB->query('ROLLBACK');
            }            
            $this->redirect('permintaan.LPOBaru',true);
        }
    }
    public function checkUbahLPOBaru ($sender,$param) {        
        $no_lpo=$param->Value;		
        $idpuskesmas=$this->idpuskesmas;
        if ($no_lpo != '') {
            try {                                 
                $str = "SELECT status FROM master_lpo WHERE no_lpo='$no_lpo' AND idpuskesmas=$idpuskesmas";
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
            $str = "SELECT ml.idlpo,ml.no_lpo,ml.idpuskesmas,ml.tanggal_lpo,jumlah_kunjungan_gratis,jumlah_kunjungan_bayar,jumlah_kunjungan_bpjs,ml.nip_ka,ml.nama_ka,ml.nip_pengelola_obat AS nip_pengelola,ml.nama_pengelola_obat AS nama_pengelola,response_lpo FROM master_lpo ml WHERE ml.no_lpo='$no_lpo'";            
            $this->DB->setFieldTable(array('idlpo','no_lpo','idpuskesmas','tanggal_lpo','jumlah_kunjungan_gratis','jumlah_kunjungan_bayar','jumlah_kunjungan_bpjs','nip_ka','nama_ka','response_lpo','nip_pengelola','nama_pengelola'));
            $r=$this->DB->getRecord($str);
            $datalpo=$r[1];
            $_SESSION['currentPageLPOBaru']['tanggal_lpo']=$datalpo['tanggal_lpo'];                        
            
            $datalpo['nip_ka'] = $this->Pengguna->getDataUser('nip_ka');
            $datalpo['nama_ka'] = $this->Pengguna->getDataUser('nama_ka');
            $datalpo['nip_pengelola'] = $this->Pengguna->getDataUser('nip_pengelola_obat');
            $datalpo['nama_pengelola'] = $this->Pengguna->getDataUser('nama_pengelola_obat');

            $datalpo['nip_kadis']=$this->setup->getSettingValue('nip_kadis');        
            $datalpo['nama_kadis']=$this->setup->getSettingValue('nama_kadis');        
            $datalpo['nip_ka_gudang']=$this->setup->getSettingValue('nip_ka_gudang');        
            $datalpo['nama_ka_gudang']=$this->setup->getSettingValue('nama_ka_gudang');      
            $datalpo['issaved']=true;
            $datalpo['mode']='buat';
            $_SESSION['currentPageLPOBaru']['datalpo']=$datalpo;
            
            $idlpo=$datalpo['idlpo'];
            $str = "SELECT iddetail_lpo,idlpo,idobat,idobat_puskesmas,kode_obat,nama_obat,harga,kemasan,stock_awal,penerimaan AS total_penerimaan,persediaan,pemakaian AS total_pemakaian,stock_akhir,permintaan AS qty FROM detail_lpo WHERE idlpo='$idlpo' AND permintaan > 0";
            $this->DB->setFieldTable(array('iddetail_lpo','idobat','idobat_puskesmas','kode_obat','nama_obat','harga','kemasan','stock_awal','total_penerimaan','persediaan','total_pemakaian','stock_akhir','qty'));
            $r=$this->DB->getRecord($str);            
            $cart = array();
            $tanggal_lpo=$_SESSION['currentPageLPOBaru']['tanggal_lpo'];
            $bulantahun=$this->TGL->tanggal('Y-m',$tanggal_lpo);
            $bulan_sebelumnya=$this->TGL->getMonthAndYearBefore ($tanggal_lpo);        
            while (list($k,$v)=each($r)) {
                $idobat_puskesmas=$v['idobat_puskesmas'];
                $harga=$v['harga'];
                $stock_awal=$this->Obat->getFirstStock($idobat_puskesmas,$bulan_sebelumnya,$harga,'defaultpuskesmasdinas');
                $penerimaan=$this->DB->getSumRowsOfTable('qty',"master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsb WHERE msb.idsbbm_puskesmas=dsb.idsbbm_puskesmas AND dsb.idobat_puskesmas=$idobat_puskesmas AND harga=$harga AND DATE_FORMAT(msb.tanggal_sbbm_puskesmas,'%Y-%m')='$bulantahun'");                                
                $persediaan=$stock_awal+$penerimaan;                    
                $pemakaian=$this->DB->getCountRowsOfTable("detail_sbbk_puskesmas dsk,kartu_stock_puskesmas_dinas kspd WHERE kspd.iddetail_sbbk_puskesmas=dsk.iddetail_sbbk_puskesmas AND dsk.idobat_puskesmas=$idobat_puskesmas AND harga=$harga AND DATE_FORMAT(kspd.tanggal_puskesmas,'%Y-%m')='$bulantahun'",'kspd.idkartu_stock_puskesmas');                    
                $stock_akhir=$persediaan-$pemakaian;
                
                $v['stock_awal']=$stock_awal;
                $v['total_penerimaan']=$penerimaan;
                $v['persediaan']=$penerimaan;
                $v['total_pemakaian']=$pemakaian;
                $v['stock_akhir']=$stock_akhir;
                
                $cart[$v['iddetail_lpo']]=$v;
            }
            $_SESSION['currentPageLPOBaru']['cart']=$cart;
            $this->redirect('permintaan.LPOBaru',true);
        }
    }
    public function changeTanggalLPO($sender,$param) {
        $this->idProcess='add';
        $tanggal_lpo=date('Y-m-d',$sender->TimeStamp);
        $_SESSION['currentPageLPOBaru']['tanggal_lpo']=$tanggal_lpo;
        $this->labelBulanPermintaanObat->Text=$this->TGL->tanggal('F Y',$tanggal_lpo);        
        $this->labelBulanPenggunaanObat->Text=$this->TGL->tanggal('F Y',$tanggal_lpo);
        $this->populateData();
    }
    public function detailProcess() {
        $this->datalpo = $_SESSION['currentPageLPOBaru']['datalpo'];         
        if ($this->datalpo['mode'] == 'buat') {
            $this->idProcess='add';            
            $tanggal_lpo=$_SESSION['currentPageLPOBaru']['tanggal_lpo'];                        
            $this->labelBulanPermintaanObat->Text=$this->TGL->tanggal('F Y',$tanggal_lpo);            
            $this->labelBulanPenggunaanObat->Text=$this->TGL->tanggal('F Y',$tanggal_lpo);
            $this->txtAddNoLPO->Text=$this->datalpo['no_lpo'];
            $this->hiddenno_lpo->Value=$this->datalpo['no_lpo'];            
            $this->txtAddNIPKA->Text=$this->datalpo['nip_ka'];            
            $this->txtAddNamaKA->Text=$this->datalpo['nama_ka'];            
            $this->txtAddNIPPengelola->Text=$this->datalpo['nip_pengelola'];            
            $this->txtAddNamaPengelola->Text=$this->datalpo['nama_pengelola'];            
            if ($this->datalpo['issaved']) {                                              
                $this->cmbAddTanggalLPO->Text=$this->TGL->tanggal('d-m-Y',$tanggal_lpo);                
                $this->txtAddJumlahKunjunganBayar->Text=$this->datalpo['jumlah_kunjungan_bayar'];
                $this->txtAddJumlahKunjunganGratis->Text=$this->datalpo['jumlah_kunjungan_gratis'];            
                $this->txtAddJumlahKunjunganBPJS->Text=$this->datalpo['jumlah_kunjungan_bpjs'];            
            }else{
                $this->cmbAddTanggalLPO->Text=$this->TGL->tanggal('d-m-Y',$tanggal_lpo);
            }            
        }elseif ($this->datalpo['mode'] == 'ubah') {
            $this->idProcess='edit';
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
    protected function populateCart () {                    
		$this->RepeaterCart->DataSource=$_SESSION['currentPageLPOBaru']['cart'];
		$this->RepeaterCart->dataBind();             
	}    
    public function deleteItem($sender,$param) {
        $id=$this->getDataKeyField($sender,$this->RepeaterCart);
        unset($_SESSION['currentPageLPOBaru']['cart'][$id]);        
        $this->populateCart();
        $this->populateData();
    }    
    public function renderCallback ($sender,$param) {
        $this->idProcess='add';
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
        $this->idProcess='add';
		$_SESSION['currentPageLPOBaru']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageLPOBaru']['search']);
	}
    public function searchRecord ($sender,$param) {
        $this->idProcess='add';
		$_SESSION['currentPageLPOBaru']['search']=true;
        $this->populateData($_SESSION['currentPageLPOBaru']['search']);
	}    
    protected function populateData ($search=false) {     
        $idlpo=$_SESSION['currentPageLPOBaru']['datalpo']['idlpo'];                
        $str = "SELECT dlp.iddetail_lpo,dlp.idobat_puskesmas,dlp.kode_obat,dlp.nama_obat,dlp.harga,dlp.kemasan FROM detail_lpo dlp LEFT JOIN master_obat_puskesmas mop ON (mop.idobat_puskesmas=dlp.idobat_puskesmas) WHERE dlp.idlpo=$idlpo";                       
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa=" AND dlp.kode_obat='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("detail_lpo dlp WHERE idlpo=$idlpo $cluasa",'iddetail_lpo');
                    $str = "$str $cluasa";
                break;
                case 'nama' :
                    $cluasa=" AND dlp.nama_obat LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("detail_lpo dlp WHERE idlpo=$idlpo $cluasa",'iddetail_lpo');
                    $str = "$str $cluasa";
                break;  
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ("detail_lpo WHERE idlpo=$idlpo",'iddetail_lpo');		
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
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageLPOBaru']['page_num']=0;}
        $str = "$str ORDER BY mop.stock DESC,dlp.nama_obat ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('iddetail_lpo','idobat_puskesmas','kode_obat','nama_obat','harga','kemasan'));
		$r=$this->DB->getRecord($str,$offset+1);             
        $result=array();        
        $tanggal_lpo=$_SESSION['currentPageLPOBaru']['tanggal_lpo'];
        $bulantahun=$this->TGL->tanggal('Y-m',$tanggal_lpo);
        $bulan_sebelumnya=$this->TGL->getMonthAndYearBefore ($tanggal_lpo);        
        while (list($k,$v)=each($r)) {
            $idobat_puskesmas=$v['idobat_puskesmas'];            
            $harga=$v['harga'];
            $stock_awal=$this->Obat->getFirstStock($idobat_puskesmas,$bulan_sebelumnya,$harga,'defaultpuskesmasdinas');
            $penerimaan=$this->DB->getSumRowsOfTable('qty',"master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsb WHERE msb.idsbbm_puskesmas=dsb.idsbbm_puskesmas AND dsb.idobat_puskesmas=$idobat_puskesmas AND harga=$harga AND DATE_FORMAT(msb.tanggal_sbbm_puskesmas,'%Y-%m')='$bulantahun'");            
            $v['awal_stock']=$stock_awal;
            $v['total_penerimaan']=$penerimaan;
            $persediaan=$stock_awal+$penerimaan;
            $v['persediaan']=$persediaan;                        
            $pemakaian=$this->DB->getCountRowsOfTable("detail_sbbk_puskesmas dsk,kartu_stock_puskesmas_dinas kspd WHERE kspd.iddetail_sbbk_puskesmas=dsk.iddetail_sbbk_puskesmas AND dsk.idobat_puskesmas=$idobat_puskesmas AND harga=$harga AND DATE_FORMAT(kspd.tanggal_puskesmas,'%Y-%m')='$bulantahun'",'kspd.idkartu_stock_puskesmas');
            $v['total_pemakaian']=$pemakaian;
            $v['stock_akhir']=$persediaan-$pemakaian;
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
        $idpuskesmas=$this->idpuskesmas;
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
    public function addProductRepeater($sender,$param) {        
        if ($this->IsValid) {          
            $repeater=$sender->getId()=='btnSimpanQtyRepeaterCart'?$this->RepeaterCart:$this->RepeaterS;
            $iddetail_lpo=$this->getDataKeyField($sender,$repeater); 
            $str = "SELECT dlp.idobat_puskesmas,dlp.kode_obat,dlp.nama_obat,dlp.harga,dlp.kemasan FROM detail_lpo dlp WHERE dlp.iddetail_lpo=$iddetail_lpo";                       
            $this->DB->setFieldTable(array('idobat_puskesmas','kode_obat','nama_obat','harga','kemasan'));
            $r=$this->DB->getRecord($str);             
            
            $dataobat=$r[1];
            $idobat_puskesmas=$dataobat['idobat_puskesmas'];
            $harga=$dataobat['harga'];
            $tanggal_lpo=$_SESSION['currentPageLPOBaru']['tanggal_lpo'];
            $bulantahun=$this->TGL->tanggal('Y-m',$tanggal_lpo);
            $bulan_sebelumnya=$this->TGL->getMonthAndYearBefore ($tanggal_lpo);        
            $stock_awal=$this->Obat->getFirstStock($idobat_puskesmas,$bulan_sebelumnya,$harga,'defaultpuskesmas');
            $total_penerimaan=$this->DB->getSumRowsOfTable('qty',"master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsb WHERE msb.idsbbm_puskesmas=dsb.idsbbm_puskesmas AND dsb.idobat_puskesmas=$idobat_puskesmas AND harga=$harga AND DATE_FORMAT(msb.tanggal_sbbm_puskesmas,'%Y-%m')='$bulantahun'");            
            $persediaan=$stock_awal+$total_penerimaan;            
            $total_pemakaian=$this->DB->getCountRowsOfTable("detail_sbbk_puskesmas dsk,kartu_stock_puskesmas_dinas kspd WHERE kspd.iddetail_sbbk_puskesmas=dsk.iddetail_sbbk_puskesmas AND dsk.idobat_puskesmas=$idobat_puskesmas AND harga=$harga AND DATE_FORMAT(kspd.tanggal_puskesmas,'%Y-%m')='$bulantahun'",'kspd.idkartu_stock_puskesmas');
            $stock_akhir=$persediaan-$total_pemakaian;

            $obj=$sender->getNamingContainer();
            $qty=$obj->txtQTY->getText();                                    

            $_SESSION['currentPageLPOBaru']['cart'][$iddetail_lpo]=array('iddetail_lpo'=>$iddetail_lpo,
                                                                    'idobat_puskesmas'=>$idobtat_puskesmas,
                                                                    'idobat'=>$dataobat['idobat'],
                                                                    'kode_obat'=>$dataobat['kode_obat'],
                                                                    'nama_obat'=>$dataobat['nama_obat'],
                                                                    'harga'=>$dataobat['harga'],
                                                                    'idsatuan_obat'=>$dataobat['idsatuan_obat'],
                                                                    'idgolongan'=>$dataobat['idgolongan'],
                                                                    'kemasan'=>$dataobat['kemasan'], 
                                                                    'awal_stock'=>$stock_awal,
                                                                    'total_penerimaan'=>$total_penerimaan,
                                                                    'total_pemakaian'=>$total_pemakaian,
                                                                    'persediaan'=>$persediaan,
                                                                    'stock_akhir'=>$stock_akhir,
                                                                    'qty'=>$qty);
                                                                    
            $this->redirect('permintaan.LPOBaru',true);
        }
        
    }
    public function addAllProductRepeater($sender,$param) {        
        if ($this->IsValid) {          
            foreach ($this->RepeaterS->Items as $inputan) {
                $item=$inputan->txtQTY->getNamingContainer();
                $iddetail_lpo = $this->RepeaterS->DataKeys[$item->getItemIndex()];    
                $qty=$inputan->txtQTY->Text;                
                
                if ($qty > 0) {
                    $str = "SELECT dlp.idobat_puskesmas,dlp.kode_obat,dlp.nama_obat,dlp.harga,dlp.kemasan FROM detail_lpo dlp WHERE dlp.iddetail_lpo=$iddetail_lpo";                       
                    $this->DB->setFieldTable(array('idobat_puskesmas','kode_obat','nama_obat','harga','kemasan'));
                    $r=$this->DB->getRecord($str);             
                    
                    $dataobat=$r[1];
                    $idobat_puskesmas=$dataobat['idobat_puskesmas'];
                    $harga=$dataobat['harga'];
                    $tanggal_lpo=$_SESSION['currentPageLPOBaru']['tanggal_lpo'];
                    $bulantahun=$this->TGL->tanggal('Y-m',$tanggal_lpo);
                    $bulan_sebelumnya=$this->TGL->getMonthAndYearBefore ($tanggal_lpo);        
                    $stock_awal=$this->Obat->getFirstStock($idobat_puskesmas,$bulan_sebelumnya,$harga,'defaultpuskesmas');
                    $total_penerimaan=$this->DB->getSumRowsOfTable('qty',"master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsb WHERE msb.idsbbm_puskesmas=dsb.idsbbm_puskesmas AND dsb.idobat_puskesmas=$idobat_puskesmas AND harga=$harga AND DATE_FORMAT(msb.tanggal_sbbm_puskesmas,'%Y-%m')='$bulantahun'");            
                    $persediaan=$stock_awal+$total_penerimaan;            
                    $total_pemakaian=$this->DB->getCountRowsOfTable("detail_sbbk_puskesmas dsk,kartu_stock_puskesmas_dinas kspd WHERE kspd.iddetail_sbbk_puskesmas=dsk.iddetail_sbbk_puskesmas AND dsk.idobat_puskesmas=$idobat_puskesmas AND harga=$harga AND DATE_FORMAT(kspd.tanggal_puskesmas,'%Y-%m')='$bulantahun'",'kspd.idkartu_stock_puskesmas');
                    $stock_akhir=$persediaan-$total_pemakaian;
                    
                    $_SESSION['currentPageLPOBaru']['cart'][$iddetail_lpo]=array('iddetail_lpo'=>$iddetail_lpo,
                                                                    'idobat_puskesmas'=>$idobtat_puskesmas,
                                                                    'idobat'=>$dataobat['idobat'],
                                                                    'kode_obat'=>$dataobat['kode_obat'],
                                                                    'nama_obat'=>$dataobat['nama_obat'],
                                                                    'harga'=>$dataobat['harga'],
                                                                    'idsatuan_obat'=>$dataobat['idsatuan_obat'],
                                                                    'idgolongan'=>$dataobat['idgolongan'],
                                                                    'kemasan'=>$dataobat['kemasan'], 
                                                                    'awal_stock'=>$stock_awal,
                                                                    'total_penerimaan'=>$total_penerimaan,
                                                                    'total_pemakaian'=>$total_pemakaian,
                                                                    'persediaan'=>$persediaan,
                                                                    'stock_akhir'=>$stock_akhir,
                                                                    'qty'=>$qty);                                                                    
                    
                }
            }
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
            
            $nip_ka=$datalpo['nip_ka'];
            $nama_ka=$datalpo['nama_ka'];
            $nip_pengelola=$datalpo['nip_pengelola'];
            $nama_pengelola=$datalpo['nama_pengelola'];

            $nip_kadis=$datalpo['nip_kadis'];        
            $nama_kadis=$datalpo['nama_kadis'];        
            $nip_ka_gudang=$datalpo['nip_ka_gudang'];        
            $nama_ka_gudang=$datalpo['nama_ka_gudang'];      
            
            $this->DB->query('BEGIN');            
            $str = "UPDATE master_lpo SET no_lpo='$no_lpo',tanggal_lpo='$tanggal_lpo',nip_ka='$nip_ka',nama_ka='$nama_ka',nip_pengelola_obat='$nip_pengelola',nama_pengelola_obat='$nama_pengelola',nip_kadis='$nip_kadis',nama_kadis='$nama_kadis',nip_ka_gudang='$nip_ka_gudang',nama_ka_gudang='$nama_ka_gudang',jumlah_kunjungan_gratis='$jmlh_kunjungan_gratis',jumlah_kunjungan_bayar='$jmlh_kunjungan_bayar',jumlah_kunjungan_bpjs='$jmlh_kunjungan_bpjs',status='draft',date_modified=NOW() WHERE idlpo=$idlpo";
            if ($this->DB->updateRecord($str)) {    
                $datalpo['issaved']=true;                                                                        
                $datalpo['no_lpo']=$no_lpo;                
                $datalpo['tanggal_lpo']=$tanggal_lpo;
                $datalpo['jumlah_kunjungan_gratis']=$jmlh_kunjungan_gratis;
                $datalpo['jumlah_kunjungan_bayar']=$jmlh_kunjungan_bayar;
                $datalpo['jumlah_kunjungan_bpjs']=$jmlh_kunjungan_bpjs;                
                $_SESSION['currentPageLPOBaru']['datalpo']=$datalpo;  
                
                $cart = $_SESSION['currentPageLPOBaru']['cart'];
                while (list($iddetail_lpo,$v)=each($cart)) {
                    $stock_awal=$v['stock_awal'];
                    $total_penerimaan=$v['total_penerimaan'];
                    $total_pemakaian=$v['total_pemakaian'];
                    $persediaan=$v['persediaan'];
                    $stock_akhir=$v['stock_akhir'];
                    $permintaan=$v['qty'];
                    $str = "UPDATE detail_lpo SET stock_awal='$stock_awal',penerimaan='$total_penerimaan',persediaan='$persediaan',pemakaian='$total_pemakaian',stock_akhir='$stock_akhir',permintaan='$permintaan',date_modified=NOW() WHERE iddetail_lpo=$iddetail_lpo";                                                   
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
            
            $nip_ka=$datalpo['nip_ka'];
            $nama_ka=$datalpo['nama_ka'];
            $nip_pengelola=$datalpo['nip_pengelola'];
            $nama_pengelola=$datalpo['nama_pengelola'];

            $nip_kadis=$datalpo['nip_kadis'];        
            $nama_kadis=$datalpo['nama_kadis'];        
            $nip_ka_gudang=$datalpo['nip_ka_gudang'];        
            $nama_ka_gudang=$datalpo['nama_ka_gudang'];    
            
            $this->DB->query('BEGIN');            
            $str = "UPDATE master_lpo SET no_lpo='$no_lpo',tanggal_lpo='$tanggal_lpo',nip_ka='$nip_ka',nama_ka='$nama_ka',nip_pengelola_obat='$nip_pengelola',nama_pengelola_obat='$nama_pengelola',nip_kadis='$nip_kadis',nama_kadis='$nama_kadis',nip_ka_gudang='$nip_ka_gudang',nama_ka_gudang='$nama_ka_gudang',jumlah_kunjungan_gratis='$jmlh_kunjungan_gratis',jumlah_kunjungan_bayar='$jmlh_kunjungan_bayar',jumlah_kunjungan_bpjs='$jmlh_kunjungan_bpjs',status='complete',date_modified=NOW() WHERE idlpo=$idlpo";
            if ($this->DB->updateRecord($str)) {    
                $datalpo['idpuskesmas']=$this->idpuskesmas;                                                                        
                $datalpo['issaved']=true;                                                                        
                $datalpo['no_lpo']=$no_lpo;                
                $datalpo['tanggal_lpo']=$tanggal_lpo;
                $datalpo['jumlah_kunjungan_gratis']=$jmlh_kunjungan_gratis;
                $datalpo['jumlah_kunjungan_bayar']=$jmlh_kunjungan_bayar;
                $datalpo['jumlah_kunjungan_bpjs']=$jmlh_kunjungan_bpjs;                
                $datalpo['response_lpo']=1;                
                $_SESSION['currentPageLPOBaru']['datalpo']=$datalpo;                 
                $str = "SELECT idobat_puskesmas,iddetail_lpo,harga FROM detail_lpo WHERE idlpo=$idlpo";
                $this->DB->setFieldTable(array('idobat_puskesmas','iddetail_lpo','harga'));
                $r=$this->DB->getRecord($str);
                
                $tanggal_lpo=$_SESSION['currentPageLPOBaru']['tanggal_lpo'];
                $bulantahun=$this->TGL->tanggal('Y-m',$tanggal_lpo);
                $bulan_sebelumnya=$this->TGL->getMonthAndYearBefore ($tanggal_lpo);        
                while (list($k,$v)=each($r)) {
                    $iddetail_lpo=$v['iddetail_lpo'];
                    $idobat_puskesmas=$v['idobat_puskesmas'];
                    $harga=$v['harga'];
                    $stock_awal=$this->Obat->getFirstStock($idobat_puskesmas,$bulan_sebelumnya,$harga,'defaultpuskesmas');
                    $total_penerimaan=$this->DB->getSumRowsOfTable('qty',"master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsb WHERE msb.idsbbm_puskesmas=dsb.idsbbm_puskesmas AND dsb.idobat_puskesmas=$idobat_puskesmas AND harga=$harga AND DATE_FORMAT(msb.tanggal_sbbm_puskesmas,'%Y-%m')='$bulantahun'");            
                    $persediaan=$stock_awal+$total_penerimaan;            
                    $total_pemakaian=$this->DB->getCountRowsOfTable("detail_sbbk_puskesmas dsk,kartu_stock_puskesmas_dinas kspd WHERE kspd.iddetail_sbbk_puskesmas=dsk.iddetail_sbbk_puskesmas AND dsk.idobat_puskesmas=$idobat_puskesmas AND harga=$harga AND DATE_FORMAT(kspd.tanggal_puskesmas,'%Y-%m')='$bulantahun'",'kspd.idkartu_stock_puskesmas');
                    $stock_akhir=$persediaan-$total_pemakaian;                    
                    $permintaan=$_SESSION['currentPageLPOBaru']['cart'][$iddetail_lpo]['qty'];                    
                    $str = "UPDATE detail_lpo SET stock_awal='$stock_awal',penerimaan='$total_penerimaan',persediaan='$persediaan',pemakaian='$total_pemakaian',stock_akhir='$stock_akhir',permintaan='$permintaan',date_added=NOW(),date_modified=NOW() WHERE iddetail_lpo=$iddetail_lpo";                                                   
                    $this->DB->updateRecord($str);
                }                
                $_SESSION['currentPageDaftarLPO']['datalpo']=$datalpo;                
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
