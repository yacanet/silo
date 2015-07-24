<?php
prado::using ('Application.MainPageSA');
class DaftarSBBK extends MainPageSA {    
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
            if (isset($_SESSION['currentPageDaftarSBBK']['datasbbk']['no_sbbk'])) {
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
            $status=$item->DataItem['status'];
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
        $status=$_SESSION['currentPageDaftarSBBK']['status_sbbk'];        
        $ta=$_SESSION['ta'];
        $str = "SELECT ms.idsbbk,ms.no_sbbk,ms.tanggal_sbbk,idlpo,no_lpo,tanggal_lpo,permintaan_dari,keperluan,status,response_sbbk,date_modified FROM master_sbbk ms WHERE status='$status' AND tahun=$ta";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa=" AND no_sbbk='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbk WHERE $cluasa",'no_sbbk');
                    $str = "$str $cluasa";
                break;
                case 'nomor' :
                    $cluasa=" AND no_faktur='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbk WHERE $cluasa",'no_sbbk');
                    $str = "$str $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbk WHERE status='$status'",'no_sbbk');		
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
        $str = "$str ORDER BY date_added DESC,no_sbbk ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idsbbk','no_sbbk','tanggal_sbbk','idlpo','no_lpo','tanggal_lpo','permintaan_dari','keperluan','status','response_sbbk','date_modified'));
		$r=$this->DB->getRecord($str,$offset+1);          
        $data_r=array();
        while (list($k,$v)=each($r)) {
            if ($v['idlpo'] == 0) {
                $v['no_lpo']='-';
                $v['tanggal_lpo']='-';
            }else{
                $v['tanggal_lpo']=$this->TGL->tanggal('d/m/Y',$v['tanggal_lpo']);
                if ($v['status'] == 'none') {
                    $v['no_sbbk']='-';
                    $v['tanggal_sbbk']='-';                                   
                    $v['nama_penyalur']='-';
                    $v['no_faktur']='-';
                    $v['tanggal_faktur']='-';
                    $v['keperluan']='-';
                }else {
                    $v['tanggal_sbbk']=$this->TGL->tanggal('d/m/Y',$v['tanggal_sbbk']);
                    $v['tanggal_faktur']=$this->TGL->tanggal('d/m/Y',$v['tanggal_faktur']);;
                }
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
        $idlpo=$sender->CommandParameter;
        if ($idlpo==0) {
            $str = "SELECT idsbbk,no_sbbk,tanggal_sbbk,no_lpo,permintaan_dari,keperluan,no_spmb,nip_ka_gudang,nama_ka_gudang,nip_pengemas,nama_pengemas FROM master_sbbk WHERE idsbbk=$id";
            $this->DB->setFieldTable(array('idsbbk','no_sbbk','tanggal_sbbk','no_lpo','permintaan_dari','keperluan','no_spmb','nip_ka_gudang','nama_ka_gudang','nip_pengemas','nama_pengemas'));
            $datasbbk=$this->DB->getRecord($str);        
                        
            $_SESSION['currentPageSBBKBebas']['datasbbk']=$datasbbk[1];            
            $_SESSION['currentPageSBBKBebas']['datasbbk']['mode']='ubah';        

            $idsbbk=$datasbbk[1]['idsbbk'];
            $str = "SELECT dsb.iddetail_sbbm,dsb.idobat,dsb.idobat_puskesmas,dsb.kode_obat,dsb.nama_obat,mo.harga,dsb.kemasan,mo.stock,dsb.pemberian FROM detail_sbbk dsb LEFT JOIN master_obat mo ON (dsb.idobat=mo.idobat) WHERE idsbbk='$idsbbk'";        
            $this->DB->setFieldTable(array('iddetail_sbbm','idobat','idobat_puskesmas','kode_obat','nama_obat','harga','kemasan','stock','pemberian'));
            $r=$this->DB->getRecord($str);               
            $cart = array();
            if (isset($r[1])) {
                while (list($k,$v)=each($r)) {
                    $cart[$v['iddetail_sbbm']]=$v;
                }
            }             
            $_SESSION['currentPageSBBKBebas']['cart']=$cart;
            
            $this->redirect('mutasibarang.SBBKBebas',true);
        }else{
            $str = "SELECT idlpo,idsbbk,no_sbbk,tanggal_sbbk,idpuskesmas,permintaan_dari,idlpo,no_lpo,tanggal_lpo,keperluan,no_spmb,nip_ka_gudang,nama_ka_gudang,nip_pengemas,nama_pengemas FROM master_sbbk WHERE idsbbk=$id";
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
    public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);                
        $idlpo=$sender->CommandParameter;
        $this->DB->deleteRecord("master_sbbk WHERE idsbbk=$id");
        $this->DB->updateRecord("UPDATE master_lpo SET response_lpo=1 WHERE idlpo=$idlpo");                
        if ($_SESSION['currentPageSBBKBaru']['datasbbk']['idsbbk']==$id) {
            unset($_SESSION['currentPageSBBKBaru']['datasbbk']);
            unset($_SESSION['currentPageSBBKBaru']['cart']);
        }
        $this->redirect('mutasibarang.DaftarSBBK',true);		        
	}
    public function viewRecord ($sender,$param) {        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);           
        $str = "SELECT idlpo,idsbbk,no_sbbk,tanggal_sbbk,permintaan_dari,idlpo,no_lpo,tanggal_lpo,keperluan,no_spmb,nip_ka_gudang,nama_ka_gudang,nip_pengemas,nama_pengemas,response_sbbk FROM master_sbbk WHERE idsbbk=$id";
        $this->DB->setFieldTable(array('idlpo','idsbbk','no_sbbk','tanggal_sbbk','permintaan_dari','idlpo','no_lpo','tanggal_lpo','keperluan','no_spmb','nip_ka_gudang','nama_ka_gudang','nip_pengemas','nama_pengemas','response_sbbk'));
        $datasbbk=$this->DB->getRecord($str);        
        $_SESSION['currentPageDaftarSBBK']['datasbbk']=$datasbbk[1]; 
        
        $this->redirect('mutasibarang.DaftarSBBK',true);
    }  
    public function itemCreatedCart($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {                        
            DaftarSBBK::$totalQTY += $item->DataItem['pemberian'];            
            $harga=$item->DataItem['pemberian']*$item->DataItem['harga'];
            DaftarSBBK::$totalHARGA += $harga;
        }
    }    
    public function detailProcess() {
        $this->datasbbk = $_SESSION['currentPageDaftarSBBK']['datasbbk'];              
        $this->idProcess='view'; 
        
        $idsbbk=$this->datasbbk['idsbbk'];
        $str = "SELECT dsb.idobat,dsb.no_batch,dsb.nama_obat,dsb.harga,dsb.kemasan,dsb.stock_awal,dsb.penerimaan AS total_penerimaan,dsb.persediaan,dsb.pemakaian AS total_pemakaian,dsb.stock_akhir,dsb.permintaan,dsb.pemberian,dsb.islpo,dsb.ischecked FROM detail_sbbk dsb WHERE idsbbk='$idsbbk'";        
        $this->DB->setFieldTable(array('idobat','no_batch','nama_obat','harga','kemasan','stock_awal','total_penerimaan','persediaan','total_pemakaian','stock_akhir','permintaan','pemberian','islpo','ischecked'));
        $r=$this->DB->getRecord($str);               
        
        $this->RepeaterCart->DataSource=$r;
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
