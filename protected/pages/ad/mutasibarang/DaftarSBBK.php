<?php
prado::using ('Application.MainPageAD');
class DaftarSBBK extends MainPageAD {    
    public static $totalQTY=0;
    public static $totalHARGA=0;
    public $datasbbk;
	public function onLoad($param) {		
		parent::onLoad($param);		
        $this->showSubMenuMutasiBarangKeluar=true;
		$this->showDaftarSBBK=true;              
        $this->createObj('Obat');
        $this->createObj('DMaster');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            $this->Page->labelTahunDaftarSBBK->Text=$_SESSION['ta'];                         
            if (isset($_SESSION['currentPageDaftarSBBK']['datasbbk']['no_sbbk_puskesmas'])) {
                $this->detailProcess();
            }else {
                if (!isset($_SESSION['currentPageDaftarSBBK'])||$_SESSION['currentPageDaftarSBBK']['page_name']!='sa.mutasibarang.DaftarSBBK') {
                    $_SESSION['currentPageDaftarSBBK']=array('page_name'=>'sa.mutasibarang.DaftarSBBK','page_num'=>0,'search'=>false,'datasbbk'=>array(),'cart'=>array(),'status_sbbk'=>'complete');												
                }   
                $_SESSION['currentPageDaftarSBBK']['search']=false;              
                $this->cmbFilterStatus->Text=$_SESSION['currentPageDaftarSBBK']['status_sbbk'];            
                $this->populateData ();	
            }
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageDaftarSBBK']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageDaftarSBBK']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageDaftarSBBK']['search']=true;
        $this->populateData($_SESSION['currentPageDaftarSBBK']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageDaftarSBBK']['status_sbbk']=$this->cmbFilterStatus->Text;
        $this->populateData();
	}    
    public function itemCreated($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {            
            $status=$item->DataItem['status_puskesmas'];
            if ($status=='complete') {
                $item->btnEdit->Enabled=false;                
                $item->btnEdit->CssClass='table-link disabled';
                
                $item->btnDelete->Enabled=false;                
                $item->btnDelete->CssClass='table-link disabled';
            }else {
                $item->btnView->Enabled=false;                
                $item->btnView->CssClass='table-link disabled';
                
                $item->btnDelete->Attributes->OnClick="if(!confirm('Anda ingin menghapus data SBBK ini ?')) return false;";
            }
        }
    }
    public function populateData ($search=false) {        
        $idpuskesmas=$this->idpuskesmas;
        $status=$_SESSION['currentPageDaftarSBBK']['status_sbbk'];        
        $ta=$_SESSION['ta'];
        $str = "SELECT ms.idsbbk_puskesmas,ms.no_sbbk_puskesmas,ms.tanggal_sbbk_puskesmas,idlpo_unit,no_lpo_unit,tanggal_lpo_unit,nama_unit,keperluan_unit,status_puskesmas,response_sbbk_puskesmas,date_modified FROM master_sbbk_puskesmas ms WHERE idpuskesmas=$idpuskesmas AND status_puskesmas='$status' AND tahun_puskesmas=$ta";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa=" AND no_sbbk='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbk_puskesmas WHERE idpuskesmas=$idpuskesmas AND $cluasa",'idsbbk_puskesmas');
                    $str = "$str $cluasa";
                break;                
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbk_puskesmas WHERE idpuskesmas=$idpuskesmas AND status_puskesmas='$status'",'idsbbk_puskesmas');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageDaftarSBBK']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageDaftarSBBK']['page_num']=0;}
        $str = "$str ORDER BY date_added DESC,no_sbbk_puskesmas ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idsbbk_puskesmas','no_sbbk_puskesmas','tanggal_sbbk_puskesmas','idlpo_unit','no_lpo_unit','tanggal_lpo_unit','nama_unit','keperluan_unit','status_puskesmas','response_sbbk_puskesmas','date_modified'));
		$r=$this->DB->getRecord($str,$offset+1);          
        $data_r=array();
        while (list($k,$v)=each($r)) {
            if ($v['status_puskesmas'] == 'none') {
                $v['no_sbbk_puskesmas']='-';
                $v['tanggal_sbbk_puskesmas']='-';                                   
                $v['tanggal_lpo_unit']='-';
                $v['nama_unit']='-';                                                
                $v['keperluan_unit']='-';
            }else {
                $v['tanggal_sbbk_puskesmas']=$this->TGL->tanggal('d/m/Y',$v['tanggal_sbbk_puskesmas']);                
                $v['tanggal_lpo_unit']=$this->TGL->tanggal('d/m/Y',$v['tanggal_lpo_unit']);                
            }
            $data_r[$k]=$v;
        }
		$this->RepeaterS->DataSource=$data_r;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}    
    public function editRecord ($sender,$param) {
        $this->idProcess='edit';        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);        		
        $str = "SELECT idsbbk_puskesmas,no_sbbk_puskesmas,tanggal_sbbk_puskesmas,idpuskesmas,idunitpuskesmas,nama_unit,idlpo_unit,no_lpo_unit,tanggal_lpo_unit,keperluan_unit,no_spmb_puskesmas,nip_ka_gudang_puskesmas,nama_ka_gudang_puskesmas,nip_pengemas_puskesmas,nama_pengemas_puskesmas FROM master_sbbk_puskesmas WHERE idsbbk_puskesmas=$id";
        $this->DB->setFieldTable(array('idsbbk_puskesmas','no_sbbk_puskesmas','tanggal_sbbk_puskesmas','idpuskesmas','idunitpuskesmas','nama_unit','idlpo_unit','no_lpo_unit','tanggal_lpo_unit','keperluan_unit','no_spmb_puskesmas','nip_ka_gudang_puskesmas','nama_ka_gudang_puskesmas','nip_pengemas_puskesmas','nama_pengemas_puskesmas'));
        $datasbbk=$this->DB->getRecord($str);        
        
        $_SESSION['currentPageSBBKBaru']['idprodusen']='none';
        $_SESSION['currentPageSBBKBaru']['datasbbk']=$datasbbk[1];
        $_SESSION['currentPageSBBKBaru']['datasbbk']['issaved']=true;
        $_SESSION['currentPageSBBKBaru']['datasbbk']['mode']='buat';        
        
        $idsbbk_puskesmas=$datasbbk[1]['idsbbk_puskesmas'];
        $str = "SELECT dsb.iddetail_sbbm_puskesmas,dsb.idobat,dsb.idobat_puskesmas,dsb.kode_obat,dsb.nama_obat,dsb.harga,dsb.kemasan,mop.stock,dsb.stock_awal_unit,dsb.penerimaan_unit AS total_penerimaan,dsb.persediaan_unit,dsb.pemakaian_unit AS total_pemakaian,dsb.stock_akhir_unit,dsb.permintaan_unit,dsb.pemberian_puskesmas FROM detail_sbbk_puskesmas dsb LEFT JOIN master_obat_puskesmas mop ON (mop.idobat_puskesmas=dsb.idobat_puskesmas) WHERE idsbbk_puskesmas=$idsbbk_puskesmas";        
        $this->DB->setFieldTable(array('iddetail_sbbm_puskesmas','dsb.idobat','idobat_puskesmas','kode_obat','nama_obat','harga','kemasan','stock','stock_awal_unit','total_penerimaan','persediaan_unit','stock_akhir_unit','total_pemakaian','permintaan_unit','pemberian_puskesmas'));
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
    public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);                
        $this->DB->deleteRecord("master_sbbk_puskesmas WHERE idsbbk_puskesmas=$id");                 
        if ($_SESSION['currentPageSBBKBaru']['datasbbk']['idsbbk']==$id) {
            unset($_SESSION['currentPageSBBKBaru']['datasbbk']);
            unset($_SESSION['currentPageSBBKBaru']['cart']);
        }
        $this->redirect('mutasibarang.DaftarSBBK',true);		        
	}
    public function viewRecord ($sender,$param) {        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);           
        $str = "SELECT idsbbk_puskesmas,no_sbbk_puskesmas,tanggal_sbbk_puskesmas,nama_unit,idlpo_unit,no_lpo_unit,tanggal_lpo_unit,keperluan_unit,no_spmb_puskesmas,nip_ka_gudang_puskesmas,nama_ka_gudang_puskesmas,nip_pengemas_puskesmas,nama_pengemas_puskesmas FROM master_sbbk_puskesmas WHERE idsbbk_puskesmas=$id";
        $this->DB->setFieldTable(array('idsbbk_puskesmas','no_sbbk_puskesmas','tanggal_sbbk_puskesmas','nama_unit','idlpo_unit','no_lpo_unit','tanggal_lpo_unit','keperluan_unit','no_spmb_puskesmas','nip_ka_gudang_puskesmas','nama_ka_gudang_puskesmas','nip_pengemas_puskesmas','nama_pengemas_puskesmas'));
        $datasbbk=$this->DB->getRecord($str);        
        $_SESSION['currentPageDaftarSBBK']['datasbbk']=$datasbbk[1];                
        
        $idsbbk_puskesmas=$datasbbk[1]['idsbbk_puskesmas'];
        $str = "SELECT dsb.iddetail_sbbk_puskesmas,dsb.kode_obat,dsb.nama_obat,dsb.harga,dsb.kemasan,dsb.stock_awal_unit,dsb.penerimaan_unit AS total_penerimaan,dsb.persediaan_unit,dsb.pemakaian_unit AS total_pemakaian,dsb.stock_akhir_unit,dsb.permintaan_unit,dsb.pemberian_puskesmas,dsb.islpo_unit FROM detail_sbbk_puskesmas dsb WHERE idsbbk_puskesmas='$idsbbk_puskesmas'";        
        $this->DB->setFieldTable(array('iddetail_sbbk_puskesmas','kode_obat','nama_obat','harga','kemasan','stock_awal_unit','total_penerimaan','persediaan_unit','stock_akhir_unit','total_pemakaian','permintaan_unit','pemberian_puskesmas','islpo_unit'));
        $r=$this->DB->getRecord($str);               
        $cart = array();
        while (list($k,$v)=each($r)) {                
            $cart[$v['iddetail_sbbk_puskesmas']]=$v;
        }                        
        $_SESSION['currentPageDaftarSBBK']['cart']=$cart;                    
        
        $this->redirect('mutasibarang.DaftarSBBK',true);
    }  
    public function itemCreatedCart($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {                        
            DaftarSBBK::$totalQTY += $item->DataItem['pemberian_puskesmas'];            
            $harga=$item->DataItem['pemberian_puskesmas']*$item->DataItem['harga'];
            $obat=$this->getLogic('Obat');
            $item->literalSubTotal->Text=$obat->toRupiah($harga);
            DaftarSBBK::$totalHARGA += $harga;
        }
    }   
    public function detailProcess() {
        $this->datasbbk = $_SESSION['currentPageDaftarSBBK']['datasbbk'];              
        $this->idProcess='view';                    
                
        $this->RepeaterCart->DataSource=$_SESSION['currentPageDaftarSBBK']['cart'];
		$this->RepeaterCart->dataBind();             
    }
    public function printOut ($sender,$param) {
        $this->idProcess='view';
        $this->createObj('report');
        $dataReport=$_SESSION['currentPageDaftarSBBK']['datasbbk'];                  
        $ka_puskesmas=$this->DMaster->getNamaKAPuskesmasByID ($dataReport['idpuskesmas']);                        
        $dataReport['nip_ka']=$ka_puskesmas['nip_ka'];
        $dataReport['nama_ka']=$ka_puskesmas['nama_ka'];
        switch ($sender->getId()) {
            case 'btnPrintOutSuratPengantar' :                
                $this->report->setMode('pdf');                            
                $dataReport['linkoutput']=$this->linkOutput; 
                $this->report->setDataReport($dataReport);   
                $this->report->printSuratPengantarSBBK();          
                $this->lblPrintout->Text='Surat Pengantar SBBK';
            break;
            case 'btnPrintOutLembarSBBK' :
                $this->report->setMode('excel2007');        
                $dataReport['linkoutput']=$this->linkOutput;                 
                $this->report->setDataReport($dataReport);        
                $this->report->printDetailSBBK();          
                $this->lblPrintout->Text='Laporan SBBK';
            break;
        }        
        $this->modalPrintOut->show();
    }
    public function closeSBBK ($sender,$param) {        
        unset($_SESSION['currentPageDaftarSBBK']['datasbbk']);
        unset($_SESSION['currentPageDaftarSBBK']['cart']);
        $this->redirect('mutasibarang.DaftarSBBK',true);
    }
}
