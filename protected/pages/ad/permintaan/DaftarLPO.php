<?php
prado::using ('Application.MainPageAD');
class DaftarLPO extends MainPageAD {    
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
                if (!isset($_SESSION['currentPageDaftarLPO'])||$_SESSION['currentPageDaftarLPO']['page_name']!='ad.permintaan.DaftarLPO') {
                    $_SESSION['currentPageDaftarLPO']=array('page_name'=>'ad.permintaan.DaftarLPO','page_num'=>0,'search'=>false,'datalpo'=>array(),'cart'=>array(),'response_lpo'=>1,'status_lpo'=>'complete','tanggal_lpo'=>date('Y-m-d'));												
                }   
                $_SESSION['currentPageDaftarLPO']['search']=false;              
                $daftar_lpo=$this->DMaster->removeIdFromArray($this->DMaster->getJenisResponseLPO(),'none');                
                $this->cmbResponseLPO->DataSource=$daftar_lpo;
                $this->cmbResponseLPO->Text=$_SESSION['currentPageDaftarLPO']['response_lpo'];            
                $this->cmbResponseLPO->dataBind();
                
                $this->cmbFilterStatus->Text=$_SESSION['currentPageDaftarLPO']['status_lpo'];            
                
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
        $_SESSION['currentPageDaftarLPO']['status_lpo']=$this->cmbFilterStatus->Text;
        $this->populateData();
	}    
    public function itemCreated($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {            
            $status=$item->DataItem['status'];
            if ($status=='complete') {
                $item->btnEdit->Enabled=false;                
                $item->btnEdit->CssClass='table-link disabled';                                
                               
                $item->btnDelete->Enabled=false;                
                $item->btnDelete->CssClass='table-link disabled';
                
            }else {
                
                $item->btnView->Enabled=false;                
                $item->btnView->CssClass='table-link disabled';
                
                $item->btnDelete->Attributes->OnClick="if(!confirm('Anda ingin menghapus data LPO ini ?')) return false;";
            }
        }
    }
    public function populateData ($search=false) {        
        $status=$_SESSION['currentPageDaftarLPO']['status_lpo'];        
        $response_lpo=$_SESSION['currentPageDaftarLPO']['response_lpo'];        
        $ta=$_SESSION['ta'];
        $idpuskesmas=$this->idpuskesmas;        
        if ($search) {            
            $str = "SELECT ml.idlpo,ml.no_lpo,msk.no_sbbk,msk.tanggal_sbbk,ml.tanggal_lpo,ml.nama_ka,ml.jumlah_kunjungan_gratis,ml.jumlah_kunjungan_bayar,ml.jumlah_kunjungan_bpjs,ml.status,ml.response_lpo,ml.date_modified FROM master_lpo ml LEFT JOIN master_sbbk msk ON(ml.idlpo=msk.idlpo) WHERE ml.status='$status' AND ml.response_lpo='$response_lpo' AND ml.tahun=$ta AND ml.idpuskesmas='$idpuskesmas'";                
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa=" AND ml.no_lpo='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_lpo ml WHERE ml.tatus='$status' AND ml.response_lpo='$response_lpo' AND ml.tahun=$ta $cluasa",'idlpo');
                    $str = "$str $cluasa";
                break;
                case 'nomor' :
                    $cluasa=" AND ml.no_faktur='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_lpo ml WHERE ml.status='$status' AND ml.response_lpo='$response_lpo' AND ml.tahun=$ta $cluasa",'idlpo');
                    $str = "$str $cluasa";
                break;
            }
        }else {
            $str = "SELECT ml.idlpo,ml.no_lpo,msk.no_sbbk,msk.tanggal_sbbk,ml.tanggal_lpo,ml.nama_ka,ml.jumlah_kunjungan_gratis,ml.jumlah_kunjungan_bayar,ml.jumlah_kunjungan_bpjs,ml.status,ml.response_lpo,ml.date_modified FROM master_lpo ml LEFT JOIN master_sbbk msk ON(ml.idlpo=msk.idlpo) WHERE ml.status='$status' AND ml.idpuskesmas='$idpuskesmas' AND ml.response_lpo='$response_lpo' AND ml.tahun=$ta";                
            $jumlah_baris=$this->DB->getCountRowsOfTable ("master_lpo ml WHERE ml.status='$status' AND ml.response_lpo='$response_lpo' AND ml.tahun=$ta",'idlpo');		
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
        $str = "$str ORDER BY ml.date_modified DESC,ml.no_lpo ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idlpo','no_lpo','tanggal_lpo','no_sbbk','tanggal_sbbk','nama_ka','jumlah_kunjungan_gratis','jumlah_kunjungan_bayar','jumlah_kunjungan_bpjs','status','response_lpo','date_modified'));
		$r=$this->DB->getRecord($str,$offset+1);          
        $data=array();        
        while (list($k,$v)=each($r)) {
            $idlpo=$v['idlpo'];
            if ($response_lpo == 5) {
                $v['tanggal_sbbk']=$this->Page->TGL->tanggal('d/m/Y',$v['tanggal_sbbk']);                
            }else {
                $v['tanggal_sbbk'] = 'N.A';
                $v['no_sbbk'] = 'N.A';
            }            
            $v['total_permintaan']=$this->DB->getSumRowsOfTable ('permintaan',"detail_lpo WHERE idlpo=$idlpo");		  
            $data[$k]=$v;
        }
        $this->RepeaterS->DataSource=$data;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}    
    public function editRecord ($sender,$param) {
        $this->idProcess='edit';        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);        		        
        $str = "SELECT ml.idlpo,ml.no_lpo,ml.tanggal_lpo,jumlah_kunjungan_gratis,jumlah_kunjungan_bayar,jumlah_kunjungan_bpjs,ml.nip_ka,ml.nama_ka,ml.nip_pengelola_obat AS nip_pengelola,ml.nama_pengelola_obat AS nama_pengelola FROM master_lpo ml WHERE ml.idlpo=$id";            
        $this->DB->setFieldTable(array('idlpo','no_lpo','tanggal_lpo','jumlah_kunjungan_gratis','jumlah_kunjungan_bayar','jumlah_kunjungan_bpjs','nip_ka','nama_ka','nip_pengelola','nama_pengelola'));
        $r=$this->DB->getRecord($str);
        $_SESSION['currentPageLPOBaru']['idprodusen']='none';
        $_SESSION['currentPageLPOBaru']['tanggal_lpo']=$r[1]['tanggal_lpo'];
        $_SESSION['currentPageLPOBaru']['datalpo']=$r[1];
        $_SESSION['currentPageLPOBaru']['datalpo']['issaved']=true;
        $_SESSION['currentPageLPOBaru']['datalpo']['mode']='buat';        
        $str = "SELECT iddetail_lpo,idlpo,idobat,idobat_puskesmas,kode_obat,nama_obat,harga,kemasan,stock_awal,penerimaan AS total_penerimaan,persediaan,pemakaian AS total_pemakaian,stock_akhir,permintaan AS qty FROM detail_lpo WHERE idlpo='$id' AND permintaan > 0";
        $this->DB->setFieldTable(array('iddetail_lpo','idlpo','idobat','idobat_puskesmas','kode_obat','nama_obat','harga','kemasan','stock_awal','total_penerimaan','persediaan','total_pemakaian','stock_akhir','qty'));
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
    public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);        
        $this->DB->deleteRecord("master_lpo WHERE idlpo=$id");
        if ($_SESSION['currentPageLPOBaru']['datalpo']['idlpo']==$id) {
            unset($_SESSION['currentPageLPOBaru']['datalpo']);
            unset($_SESSION['currentPageLPOBaru']['cart']);   
        }        
        $this->redirect('permintaan.DaftarLPO',true);		        
	}
    public function viewRecord ($sender,$param) {
        $this->createObj('obat');
        $idlpo=$this->getDataKeyField($sender,$this->RepeaterS);           
        $str = "SELECT ml.idlpo,ml.no_lpo,ml.idpuskesmas,ml.tanggal_lpo,jumlah_kunjungan_gratis,jumlah_kunjungan_bayar,jumlah_kunjungan_bpjs,ml.nip_pengelola_obat AS nip_pengelola,ml.nama_pengelola_obat AS nama_pengelola,ml.nip_kadis,ml.nama_kadis,ml.nip_ka_gudang,ml.nama_ka_gudang,ml.nip_ka,ml.nama_ka,response_lpo FROM master_lpo ml WHERE ml.idlpo=$idlpo";            
        $this->DB->setFieldTable(array('idlpo','no_lpo','idpuskesmas','tanggal_lpo','jumlah_kunjungan_gratis','jumlah_kunjungan_bayar','jumlah_kunjungan_bpjs','nip_pengelola','nama_pengelola','nip_kadis','nama_kadis','nip_ka_gudang','nama_ka_gudang','nip_ka','nama_ka','response_lpo'));
        $r=$this->DB->getRecord($str);
        $_SESSION['currentPageDaftarLPO']['tanggal_lpo']=$r[1]['tanggal_lpo'];
        $_SESSION['currentPageDaftarLPO']['datalpo']=$r[1];
        $_SESSION['currentPageDaftarLPO']['datalpo']['issaved']=true;
        $_SESSION['currentPageDaftarLPO']['datalpo']['mode']='buat';
        $this->redirect('permintaan.DaftarLPO',true);
    }
    public function detailProcess() {
        $this->datalpo = $_SESSION['currentPageDaftarLPO']['datalpo'];              
        $this->idProcess='view';          
        $idlpo=$this->datalpo['idlpo'];
        $str = "SELECT iddetail_lpo,idlpo,idobat,idobat_puskesmas,kode_obat,nama_obat,harga,kemasan,stock_awal,penerimaan AS total_penerimaan,persediaan,pemakaian AS total_pemakaian,stock_akhir,permintaan AS qty FROM detail_lpo WHERE idlpo='$idlpo' ORDER BY nama_obat ASC,kode_obat ASC";
        $this->DB->setFieldTable(array('iddetail_lpo','idlpo','idobat','idobat_puskesmas','kode_obat','nama_obat','harga','kemasan','stock_awal','total_penerimaan','persediaan','total_pemakaian','stock_akhir','qty'));
        $r=$this->DB->getRecord($str);                    
        $this->RepeaterCart->DataSource=$r;
		$this->RepeaterCart->dataBind();             
    }
    public function itemCreatedCart($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {                        
            DaftarLPO::$totalQTY += $item->DataItem['qty'];   
            $harga=$item->DataItem['qty']*$item->DataItem['harga'];
            DaftarLPO::$totalHARGA += $harga;
        }
    }
    public function printOut ($sender,$param) {
        $this->idProcess='view';
        $this->createObj('report');
        $dataReport=$_SESSION['currentPageDaftarLPO']['datalpo'];        
        $dataReport['nama_puskesmas']=$this->Pengguna->getDataUser ('nama_puskesmas');
        $dataReport['nama_kecamatan']=$this->Pengguna->getDataUser ('nama_kecamatan');
        switch ($sender->getId()) {
            case 'btnPrintOutSuratPengantar' :                
                $this->report->setMode('pdf');                            
                $dataReport['linkoutput']=$this->linkOutput; 
                $this->report->setDataReport($dataReport);   
                $this->report->printSuratPengantarLPLPO();          
                $this->lblPrintout->Text='Surat Pengantar LPLPO';
            break;
            case 'btnPrintOutLembarLPO' :
                $this->report->setMode('excel2007');        
                $dataReport['linkoutput']=$this->linkOutput; 
                $this->report->setDataReport($dataReport);        
                $this->report->printDetailLPLPO ();          
                $this->lblPrintout->Text='Laporan Pemakaian dan Permintaan Obat';
            break;
        }        
        $this->modalPrintOut->show();
    }
    public function closeLPO ($sender,$param) {
        unset($_SESSION['currentPageDaftarLPO']['datalpo']);
        unset($_SESSION['currentPageDaftarLPO']['cart']);
        $this->redirect('permintaan.DaftarLPO',true);
    }
}
