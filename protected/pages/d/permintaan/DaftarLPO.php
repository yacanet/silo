<?php
prado::using ('Application.MainPageSA');
class DaftarLPO extends MainPageSA {    
    public static $totalQTY=0;
    public static $totalHARGA=0;
    public $datalpo;
	public function onLoad($param) {		
		parent::onLoad($param);		        
		$this->showDaftarLPO=true;              
        $this->createObj('Obat');
        $this->createObj('DMaster');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            $this->Page->labelTahunDaftarLPO->Text=$_SESSION['ta'];                         
            if (isset($_SESSION['currentPageDaftarLPO']['datalpo']['no_lpo'])) {
                $this->detailProcess();
            }else {
                if (!isset($_SESSION['currentPageDaftarLPO'])||$_SESSION['currentPageDaftarLPO']['page_name']!='sa.permintaan.DaftarLPO') {
                    $_SESSION['currentPageDaftarLPO']=array('page_name'=>'sa.permintaan.DaftarLPO','page_num'=>0,'search'=>false,'datalpo'=>array(),'cart'=>array(),'response_lpo'=>1,'tanggal'=>date('Y-m-d'));												
                }   
                $_SESSION['currentPageDaftarLPO']['search']=false;              
                $daftar_lpo=$this->DMaster->removeIdFromArray($this->DMaster->getJenisResponseLPO(),'none');                
                $this->cmbResponseLPO->DataSource=$daftar_lpo;
                $this->cmbResponseLPO->Text=$_SESSION['currentPageDaftarLPO']['response_lpo'];            
                $this->cmbResponseLPO->dataBind();
                $this->populateData ();	
            }
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageDaftarLPO']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageDaftarLPO']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageDaftarLPO']['search']=true;
        $this->populateData($_SESSION['currentPageDaftarLPO']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageDaftarLPO']['response_lpo']=$this->cmbResponseLPO->Text;
        $this->populateData();
	}    
    public function itemCreated($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {            
            if ($item->DataItem['idsbbk'] != '') {
                $item->btnNewSBBK->Enabled=false;                
                $item->btnNewSBBK->CssClass='table-link disabled';                
            }
        }
    }
    public function populateData ($search=false) {        
        $status=$_SESSION['currentPageDaftarLPO']['response_lpo'];        
        $ta=$_SESSION['ta'];        
        if ($search) {                  
            $str = "SELECT mlp.idlpo,mlp.no_lpo,mlp.idpuskesmas,mlp.tanggal_lpo,mlp.nama_ka,mlp.jumlah_kunjungan_gratis,mlp.jumlah_kunjungan_bayar,mlp.jumlah_kunjungan_bpjs,mlp.response_lpo,msb.idsbbk,mlp.date_modified FROM master_lpo mlp LEFT JOIN master_sbbk msb ON (msb.idlpo=mlp.idlpo) WHERE mlp.status='complete'";                
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'nomor' :
                    $cluasa=" AND no_lpo='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_lpo WHERE status='complete' $cluasa",'no_lpo');
                    $str = "$str $cluasa";
                break;                
            }
        }else {
            $str = "SELECT mlp.idlpo,mlp.no_lpo,mlp.idpuskesmas,mlp.tanggal_lpo,mlp.nama_ka,mlp.jumlah_kunjungan_gratis,mlp.jumlah_kunjungan_bayar,mlp.jumlah_kunjungan_bpjs,mlp.response_lpo,msb.idsbbk,mlp.date_modified FROM master_lpo mlp LEFT JOIN master_sbbk msb ON (msb.idlpo=mlp.idlpo) WHERE mlp.status='complete' AND mlp.response_lpo='$status' AND mlp.tahun=$ta";                
            $jumlah_baris=$this->DB->getCountRowsOfTable ("master_lpo WHERE  status='complete' AND response_lpo='$status' AND tahun=$ta",'no_lpo');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageDaftarLPO']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageDaftarLPO']['page_num']=0;}
        $str = "$str ORDER BY date_modified DESC,no_lpo ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idlpo','no_lpo','idpuskesmas','tanggal_lpo','nama_ka','jumlah_kunjungan_gratis','jumlah_kunjungan_bayar','jumlah_kunjungan_bpjs','response_lpo','date_modified','idsbbk'));
		$r=$this->DB->getRecord($str,$offset+1);          
        
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}
    public function buatSBBK($sender,$param) {
        if ($this->IsValid) {
            $idlpo=$this->getDataKeyField($sender,$this->RepeaterS);           
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
            
            $this->DB->insertRecord("INSERT INTO master_sbbk (idlpo,no_lpo,tanggal_lpo,tanggal_sbbk,idpuskesmas,nama_puskesmas,nip_kadis,nama_kadis,nip_ka_gudang,nama_ka_gudang,nip_pengemas,nama_pengemas,tahun,date_added,date_modified) VALUES ($idlpo,'$no_lpo','$tanggal_lpo','$tanggal_sbbk',$idpuskesmas,'$nama_puskesmas','$nip_kadis','$nama_kadis','$nip_ka_gudang','$nama_ka_gudang','$nip_pengemas','$nama_pengemas',$ta,NOW(),NOW())");            
            $idsbbk=$this->DB->getLastInsertID ();
            $datasbbk=array('idsbbk'=>$idsbbk,'tanggal_sbbk'=>$tanggal_sbbk,'idpuskesmas'=>$idpuskesmas,'nama_puskesmas'=>$nama_puskesmas,'idlpo'=>$idlpo,'no_lpo'=>$no_lpo,'tanggal_lpo'=>$tanggal_lpo,'nip_ka_gudang'=>$nip_ka_gudang,'nama_ka_gudang'=>$nama_ka_gudang,'nip_pengemas'=>$nama_pengemas,'nama_pengemas'=>$nama_pengemas,'tahun'=>$ta,'mode'=>'buat','issaved'=>false);            
            $_SESSION['currentPageSBBKBaru']['datasbbk']=$datasbbk;            
            $_SESSION['currentPageSBBKBaru']['idprodusen']='none';
            $this->DB->updateRecord("UPDATE master_lpo SET response_lpo=2 WHERE idlpo=$idlpo");
            
            $str = "SELECT dlp.idobat,dlp.idobat_puskesmas,dlp.kode_obat,dlp.nama_obat,mo.harga,dlp.kemasan,mo.stock,dlp.stock_awal,dlp.penerimaan AS total_penerimaan,dlp.persediaan,dlp.pemakaian AS total_pemakaian,dlp.stock_akhir,dlp.permintaan FROM detail_lpo dlp LEFT JOIN master_obat mo ON (dlp.idobat=mo.idobat) WHERE dlp.idlpo='$idlpo' AND dlp.permintaan > 0";
            $this->DB->setFieldTable(array('idobat','idobat_puskesmas','kode_obat','nama_obat','harga','kemasan','stock','stock_awal','total_penerimaan','persediaan','total_pemakaian','stock_akhir','permintaan'));
            $r=$this->DB->getRecord($str);            
            print_r($r);
            $cart = array();
            while (list($k,$v)=each($r)) {
                $v['islpo']=true;
                $v['pemberian']=0;
                $cart[$v['idobat']]=$v;                
            }
            $_SESSION['currentPageSBBKBaru']['cart']=$cart;            
            $this->redirect('barangkeluar.SBBKBaru',true);
        }
    }
    public function viewRecord ($sender,$param) {
        $this->createObj('obat');
        $idlpo=$this->getDataKeyField($sender,$this->RepeaterS);           
        $str = "SELECT ml.idlpo,ml.no_lpo,ml.idpuskesmas,ml.tanggal_lpo,jumlah_kunjungan_gratis,jumlah_kunjungan_bayar,jumlah_kunjungan_bpjs,ml.nip_ka,ml.nama_ka,response_lpo FROM master_lpo ml WHERE ml.idlpo=$idlpo";            
        $this->DB->setFieldTable(array('idlpo','no_lpo','idpuskesmas','tanggal_lpo','jumlah_kunjungan_gratis','jumlah_kunjungan_bayar','jumlah_kunjungan_bpjs','nip_ka','nama_ka','response_lpo'));
        $r=$this->DB->getRecord($str);
        $_SESSION['currentPageDaftarLPO']['tanggal']=$r[1]['tanggal_lpo'];
        $_SESSION['currentPageDaftarLPO']['datalpo']=$r[1];
        $_SESSION['currentPageDaftarLPO']['datalpo']['issaved']=true;
        $_SESSION['currentPageDaftarLPO']['datalpo']['mode']='buat';
        $idlpo=$r[1]['idlpo'];
        $str = "SELECT idlpo,idobat,idobat_puskesmas,kode_obat,nama_obat,kemasan,stock_awal,penerimaan AS total_penerimaan,persediaan,pemakaian AS total_pemakaian,stock_akhir,permintaan AS qty FROM detail_lpo WHERE idlpo='$idlpo'";
        $this->DB->setFieldTable(array('idlpo','idobat','idobat_puskesmas','kode_obat','nama_obat','kemasan','stock_awal','total_penerimaan','persediaan','total_pemakaian','stock_akhir','qty'));
        $r=$this->DB->getRecord($str);            
        $cart = array();
        while (list($k,$v)=each($r)) {
            $cart[$v['idobat_puskesmas']]=$v;
        }        
        $_SESSION['currentPageDaftarLPO']['cart']=$cart;
        $this->redirect('permintaan.DaftarLPO',true);
    }
    public function detailProcess() {
        $this->datalpo = $_SESSION['currentPageDaftarLPO']['datalpo'];              
        $this->idProcess='view';                    
        $this->RepeaterCart->DataSource=$_SESSION['currentPageDaftarLPO']['cart'];
		$this->RepeaterCart->dataBind();             
    }
    public function itemCreatedCart($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {                        
            DaftarLPO::$totalQTY += $item->DataItem['qty'];            
        }
    }
    public function closeLPO ($sender,$param) {
        unset($_SESSION['currentPageDaftarLPO']['datalpo']);
        unset($_SESSION['currentPageDaftarLPO']['cart']);
        $this->redirect('permintaan.DaftarLPO',true);
    }
}
