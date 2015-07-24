<?php
prado::using ('Application.MainPageSA');
class Home extends MainPageSA {
	public function onLoad($param) {		
		parent::onLoad($param);		            
        $this->showDashboard=true;
        $this->createObj('dmaster');
        $this->createObj('obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {              
            if (!isset($_SESSION['currentPageHome'])||$_SESSION['currentPageHome']['page_name']!='sa.Home') {                                
                $str = "SELECT dsb.idobat,dsb.nama_obat,dsb.harga,dsb.idsatuan_obat,dsb.kemasan,dsb.tanggal_expire,dsb.idprogram,COUNT(kartu_stock.idobat) AS volume FROM master_sbbm msb JOIN detail_sbbm dsb ON (dsb.idsbbm=msb.idsbbm) LEFT JOIN (SELECT ks.idobat,dsb.harga FROM kartu_stock ks,detail_sbbm dsb WHERE dsb.iddetail_sbbm=ks.iddetail_sbbm AND ks.mode='masuk' AND ks.isdestroyed=0) AS kartu_stock ON (kartu_stock.idobat=dsb.idobat AND kartu_stock.harga=dsb.harga) WHERE msb.status='complete' AND dsb.tanggal_expire>=DATE(NOW()) AND dsb.tanggal_expire <= DATE_ADD(DATE(NOW()),INTERVAL 5 MONTH) GROUP BY dsb.idobat,dsb.harga HAVING COUNT(kartu_stock.idobat) > 0 ORDER BY dsb.tanggal_expire ASC,dsb.nama_obat ASC LIMIT 10";
                $this->DB->setFieldTable(array('idobat','nama_obat','harga','idsatuan_obat','kemasan','tanggal_expire','idprogram','volume'));
                $r=$this->DB->getRecord($str);
                $dataobatexpires=array();        
                while (list($k,$v)=each($r)) {
                    $harga=$v['harga'];
                    $v['harga']=$this->Obat->toRupiah($harga);
                    $v['nama_program']=$this->DMaster->getNamaProgramByID($v['idprogram']);
                    $v['tanggal_expire']=$this->TGL->tanggal('d/m/Y',$v['tanggal_expire']);                    
                    $subtotal=$v['volume']*$harga;
                    $v['subtotal']=$this->Obat->toRupiah($subtotal);
                    $dataobatexpires[$k]=$v;
                }                
                $_SESSION['currentPageHome']=array('page_name'=>'sa.Home',
                                                   'stockobatall'=>$this->Obat->getJumlahStockObat (true),
                                                   'stockobat'=>$this->Obat->getJumlahStockObat (),
                                                   'obatexpires'=>$this->Obat->getJumlahObatExpire(),
                                                   'lembarpo'=>$this->Obat->getJumlahLPO ('allwithoutcomplete'),
                                                   'distribusiobat'=>$this->Obat->getJumlahDistribusiObat($_SESSION['ta']),
                                                   'dataobatexpires'=>$dataobatexpires);
            }   
            $this->RepeaterObatKadaluarsa->DataSource=$_SESSION['currentPageHome']['dataobatexpires'];
            $this->RepeaterObatKadaluarsa->DataBind();
		}
        $this->populateLPO();
	}
    public function refreshPage ($sender,$param) {
        unset($_SESSION['currentPageHome']);
        $this->redirect('sa.Home');
    }
    public function clickExpiredObat  ($sender,$param) {
        $_SESSION['currentPageExpireObat']=array('page_name'=>'sa.report.ExpireObat','page_num'=>0,'idprogram'=>'none','waktuexpires'=>1,'modeexpires'=>'telahexpire');
        $this->redirect('report.ExpireObat',true);
    }
    public function clickDetailObatExpire  ($sender,$param) {
        $_SESSION['currentPageExpireObat']=array('page_name'=>'sa.report.ExpireObat','page_num'=>0,'idprogram'=>'none','waktuexpires'=>5,'modeexpires'=>'bulan');												        
        $this->redirect('report.ExpireObat',true);
    }
    public function itemCreatedLPO($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {            
            if ($item->DataItem['idsbbk'] != '') {
                $item->btnNewSBBK->Enabled=false;                
                $item->btnNewSBBK->CssClass='table-link disabled';                
            }
        }
    }
    public function populateLPO () {
        $ta=$_SESSION['ta'];        
        $str = "SELECT mlp.idlpo,mlp.no_lpo,mlp.idpuskesmas,mlp.tanggal_lpo,mlp.nama_ka,mlp.jumlah_kunjungan_gratis,mlp.jumlah_kunjungan_bayar,mlp.jumlah_kunjungan_bpjs,mlp.response_lpo,msb.idsbbk,mlp.date_modified FROM master_lpo mlp LEFT JOIN master_sbbk msb ON (msb.idlpo=mlp.idlpo) WHERE mlp.status='complete' AND mlp.response_lpo=1 AND mlp.tahun=$ta ORDER BY date_modified DESC,no_lpo ASC LIMIT 5";                                
		$this->DB->setFieldTable(array('idlpo','no_lpo','idpuskesmas','tanggal_lpo','nama_ka','jumlah_kunjungan_gratis','jumlah_kunjungan_bayar','jumlah_kunjungan_bpjs','response_lpo','date_modified','idsbbk'));
		$r=$this->DB->getRecord($str);          
        $data=array();                
        while (list($k,$v)=each($r)) {
            $idlpo=$v['idlpo'];                        
            $v['total_permintaan']=$this->DB->getSumRowsOfTable ('permintaan',"detail_lpo WHERE idlpo=$idlpo");		  
            $data[$k]=$v;
        }
        $this->RepeaterLPO->DataSource=$data;
		$this->RepeaterLPO->dataBind();             
    }
    public function buatSBBK($sender,$param) {
        if ($this->IsValid) {
            $idlpo=$this->getDataKeyField($sender,$this->RepeaterLPO);           
            $ta=$_SESSION['ta'];
            $tanggal_sbbk=Date("$ta-m-d");
            $str = "SELECT no_lpo,tanggal_lpo,idpuskesmas FROM master_lpo WHERE idlpo='$idlpo'";
            $this->DB->setFieldTable(array('no_lpo','tanggal_lpo','idpuskesmas'));
            $r=$this->DB->getRecord($str);
            $no_lpo=$r[1]['no_lpo'];
            $tanggal_lpo=$r[1]['tanggal_lpo'];
            $idpuskesmas=$r[1]['idpuskesmas'];
            $nama_puskesmas=$this->DMaster->getNamaPuskesmasByID($idpuskesmas);
            
            $nip_kadis=$this->setup->getSettingValue('nip_kadis');        
            $nama_kadis=$this->setup->getSettingValue('nama_kadis');        
            $nip_ka_gudang=$this->setup->getSettingValue('nip_ka_gudang');        
            $nama_ka_gudang=$this->setup->getSettingValue('nama_ka_gudang'); 
            $nip_pengemas=$this->setup->getSettingValue('nip_pengemas');        
            $nama_pengemas=$this->setup->getSettingValue('nama_pengemas'); 
            
            $this->DB->insertRecord("INSERT INTO master_sbbk (idlpo,no_lpo,tanggal_lpo,tanggal_sbbk,idpuskesmas,permintaan_dari,nip_kadis,nama_kadis,nip_ka_gudang,nama_ka_gudang,nip_pengemas,nama_pengemas,tahun,date_added,date_modified) VALUES ($idlpo,'$no_lpo','$tanggal_lpo','$tanggal_sbbk',$idpuskesmas,'$nama_puskesmas','$nip_kadis','$nama_kadis','$nip_ka_gudang','$nama_ka_gudang','$nip_pengemas','$nama_pengemas',$ta,NOW(),NOW())");            
            $idsbbk=$this->DB->getLastInsertID ();
            $datasbbk=array('idsbbk'=>$idsbbk,'tanggal_sbbk'=>$tanggal_sbbk,'idpuskesmas'=>$idpuskesmas,'permintaan_dari'=>$nama_puskesmas,'idlpo'=>$idlpo,'no_lpo'=>$no_lpo,'tanggal_lpo'=>$tanggal_lpo,'nip_ka_gudang'=>$nip_ka_gudang,'nama_ka_gudang'=>$nama_ka_gudang,'nip_pengemas'=>$nama_pengemas,'nama_pengemas'=>$nama_pengemas,'tahun'=>$ta,'mode'=>'buat','issaved'=>false);            
            $_SESSION['currentPageSBBKBaru']['datasbbk']=$datasbbk;            
            $_SESSION['currentPageSBBKBaru']['idprodusen']='none';
            $this->DB->updateRecord("UPDATE master_lpo SET response_lpo=2 WHERE idlpo=$idlpo");
            
            $_SESSION['currentPageSBBKBaru']['cart']=array();            
            $this->redirect('mutasibarang.SBBKBaru',true);
        }
    }
    public function viewRecordLPO ($sender,$param) {
        $this->createObj('obat');
        $idlpo=$this->getDataKeyField($sender,$this->RepeaterLPO);           
        $str = "SELECT ml.idlpo,ml.no_lpo,ml.idpuskesmas,p.nama_puskesmas,k.nama_kecamatan,ml.tanggal_lpo,jumlah_kunjungan_gratis,jumlah_kunjungan_bayar,jumlah_kunjungan_bpjs,ml.nip_ka,ml.nama_ka,response_lpo FROM master_lpo ml LEFT JOIN puskesmas p ON (ml.idpuskesmas=p.idpuskesmas) LEFT JOIN kecamatan k ON (p.idkecamatan=k.idkecamatan) WHERE ml.idlpo=$idlpo";            
        $this->DB->setFieldTable(array('idlpo','no_lpo','idpuskesmas','nama_puskesmas','nama_kecamatan','tanggal_lpo','jumlah_kunjungan_gratis','jumlah_kunjungan_bayar','jumlah_kunjungan_bpjs','nip_ka','nama_ka','response_lpo'));
        $r=$this->DB->getRecord($str);
        $_SESSION['currentPageDaftarLPO']['tanggal_lpo']=$r[1]['tanggal_lpo'];
        $_SESSION['currentPageDaftarLPO']['datalpo']=$r[1];
        $_SESSION['currentPageDaftarLPO']['datalpo']['issaved']=true;
        $_SESSION['currentPageDaftarLPO']['datalpo']['mode']='buat';
        $idlpo=$r[1]['idlpo'];
        $str = "SELECT iddetail_lpo,idobat,idobat_puskesmas,kode_obat,nama_obat,kemasan,stock_awal,penerimaan AS total_penerimaan,persediaan,pemakaian AS total_pemakaian,stock_akhir,permintaan AS qty FROM detail_lpo WHERE idlpo='$idlpo' ORDER BY nama_obat ASC";
        $this->DB->setFieldTable(array('iddetail_lpo','idobat','idobat_puskesmas','kode_obat','nama_obat','kemasan','stock_awal','total_penerimaan','persediaan','total_pemakaian','stock_akhir','qty'));
        $r=$this->DB->getRecord($str);            
        $cart = array();
        while (list($k,$v)=each($r)) {
            $cart[$v['iddetail_lpo']]=$v;
        }        
        $_SESSION['currentPageDaftarLPO']['cart']=$cart;
        $this->redirect('permintaan.DaftarLPO',true);
    }
}
?>