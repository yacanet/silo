<?php
prado::using ('Application.MainPageSA');
class DaftarSBBM extends MainPageSA {    
    public static $totalQTY=0;
    public static $totalHARGA=0;
    public $datasbbm;
	public function onLoad($param) {		
		parent::onLoad($param);		
        $this->showSubMenuMutasiBarangMasuk=true;
		$this->showDaftarSBBM=true;              
        $this->createObj('Obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            $this->Page->labelTahun->Text=$_SESSION['ta'];                         
            if (isset($_SESSION['currentPageDaftarSBBM']['datasbbm']['no_sbbm'])) {
                $this->detailProcess();                
            }else {
                if (!isset($_SESSION['currentPageDaftarSBBM'])||$_SESSION['currentPageDaftarSBBM']['page_name']!='sa.mutasibarang.DaftarSBBM') {
                    $_SESSION['currentPageDaftarSBBM']=array('page_name'=>'sa.mutasibarang.DaftarSBBM','page_num'=>0,'search'=>false,'datasbbm'=>array(),'cart'=>array(),'status_sbbm'=>'complete');												
                }   
                $_SESSION['currentPageDaftarSBBM']['search']=false;              
                $this->cmbFilterStatus->Text=$_SESSION['currentPageDaftarSBBM']['status_sbbm'];            
                $this->populateData ();	
            }
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageDaftarSBBM']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageDaftarSBBM']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageDaftarSBBM']['search']=true;
        $this->populateData($_SESSION['currentPageDaftarSBBM']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageDaftarSBBM']['status_sbbm']=$this->cmbFilterStatus->Text;
        $this->populateData();
	}    
    public function itemCreated($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {            
            $status=$item->DataItem['status'];
            if ($status=='complete') {                
                if ($this->DB->checkRecordIsExist('idsbbm','detail_sbbk',$item->DataItem['idsbbm'],' LIMIT 1')) {
                    $item->btnDelete->Enabled=false;                
                    $item->btnDelete->CssClass='table-link disabled';
                }else {
                    $item->btnDelete->Attributes->OnClick="if(!confirm('Anda ingin menghapus data SBBM ini ?')) return false;";
                }
            }else {
                $item->btnView->Enabled=false;                
                $item->btnView->CssClass='table-link disabled';
                
                $item->btnDelete->Attributes->OnClick="if(!confirm('Anda ingin menghapus data SBBM ini ?')) return false;";
            }
        }
    }
    public function populateData ($search=false) {        
        $status=$_SESSION['currentPageDaftarSBBM']['status_sbbm'];        
        $ta=$_SESSION['ta'];        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            $str = "SELECT ms.idsbbm,ms.no_sbbm,ms.tanggal_sbbm,ms.sumber_dana,ps.nama_penyalur,ms.no_faktur,penerima,status,date_modified FROM master_sbbm ms LEFT JOIN penyalur_sbbm ps ON (ms.idsbbm=ps.idsbbm)";        
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa=" WHERE no_sbbm='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbm $cluasa",'no_sbbm');
                    $str = "$str $cluasa";
                break;
                case 'nomor' :
                    $cluasa=" WHERE no_faktur='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbm $cluasa",'no_sbbm');
                    $str = "$str $cluasa";
                break;
            }
        }else {
            $str = "SELECT ms.idsbbm,ms.no_sbbm,ms.tanggal_sbbm,ms.sumber_dana,ps.nama_penyalur,ms.no_faktur,penerima,status,date_modified FROM master_sbbm ms LEFT JOIN penyalur_sbbm ps ON (ms.idsbbm=ps.idsbbm) WHERE status='$status' AND tahun=$ta";        
            $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbm WHERE status='$status' AND tahun=$ta",'no_sbbm');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageDaftarSBBM']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageDaftarSBBM']['page_num']=0;}
        $str = "$str ORDER BY date_added DESC,no_sbbm ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idsbbm','no_sbbm','tanggal_sbbm','sumber_dana','nama_penyalur','no_faktur','tanggal_faktur','penerima','status','date_modified'));
		$r=$this->DB->getRecord($str,$offset+1);          
        $data_r=array();
        while (list($k,$v)=each($r)) {
            if ($v['status'] == 'none') {
                $v['tanggal_sbbm']='-';
                $v['sumber_dana']='-';                    
                $v['nama_penyalur']='-';
                $v['no_faktur']='-';
                $v['tanggal_faktur']='-';
                $v['penerima']='-';
            }else {
                $v['tanggal_sbbm']=$this->TGL->tanggal('d/m/Y',$v['tanggal_sbbm']);
                $v['tanggal_faktur']=$this->TGL->tanggal('d/m/Y',$v['tanggal_faktur']);;
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
        $str = "SELECT idsbbm,no_sbbm,tanggal_sbbm,idsumber_dana,sumber_dana,idprogram,idpenyalur,no_faktur,tanggal_faktur,penerima,status FROM master_sbbm WHERE idsbbm=$id";
        $this->DB->setFieldTable(array('idsbbm','no_sbbm','tanggal_sbbm','idsumber_dana','sumber_dana','idprogram','idpenyalur','no_faktur','tanggal_faktur','penerima','status'));
        $r=$this->DB->getRecord($str);
        $_SESSION['currentPageSBBMBaru']['idprodusen']='none';
        $_SESSION['currentPageSBBMBaru']['datasbbm']=$r[1];
        $_SESSION['currentPageSBBMBaru']['datasbbm']['issaved']=true;        
        $_SESSION['currentPageSBBMBaru']['datasbbm']['mode']=$r[1]['status']=='complete'?'ubah':'buat';
        $str = "SELECT idsbbm,idobat,kode_obat,no_reg,nama_obat,nama_merek,harga,idbentuk_sediaan,nama_bentuk,farmakologi,komposisi,kemasan,idprodusen,nama_produsen,no_batch,qty,tanggal_expire,status_obat,date_added,date_modified FROM detail_sbbm WHERE idsbbm='$id'";
        $this->DB->setFieldTable(array('idsbbm','idobat','kode_obat','no_reg','nama_obat','nama_merek','harga','idbentuk_sediaan','nama_bentuk','farmakologi','komposisi','kemasan','idprodusen','nama_produsen','no_batch','qty','tanggal_expire','status_obat','date_added','date_modified'));
        $r=$this->DB->getRecord($str);                    
        $result=array();
        while (list($k,$v)=each($r)) {
            $v['idcart']=$k;
            $result[$k]=$v;
        }
        $_SESSION['currentPageSBBMBaru']['cart']=$result;
        $this->redirect('mutasibarang.SBBMBaru',true);
        
    }     
    public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);        
        $str = "SELECT dsb1.idobat,msb.status,dsb1.qty,dsb2.iddetail_sbbk FROM master_sbbm msb JOIN detail_sbbm dsb1 ON (msb.idsbbm=dsb1.idsbbm)  LEFT JOIN detail_sbbk dsb2 ON (dsb1.idsbbm=dsb2.idsbbm) WHERE dsb1.idsbbm=$id LIMIT 1";
        $this->DB->setFieldTable(array('idobat','status','qty','iddetail_sbbk'));
        $r=$this->DB->getRecord($str);                    
        if ($r[1]['status']=='complete') {
            if ($r[1]['iddetail_sbbk']=='') {                
                $qty = $r[1]['qty'];
                $idobat = $r[1]['idobat'];
                $this->DB->query('BEGIN');
                $this->DB->deleteRecord("master_sbbm WHERE idsbbm=$id");                
                $this->DB->deleteRecord("kartu_stock WHERE idsbbm=$id");                
                $this->DB->deleteRecord("log_ks WHERE idsbbm=$id");                
                $this->DB->updateRecord("UPDATE master_obat SET stock=stock-$qty WHERE idobat=$idobat");
                $this->DB->query('COMMIT');
            }
        }else{
            $this->DB->deleteRecord("master_sbbm WHERE idsbbm=$id");            
        }        
        if ($_SESSION['currentPageSBBMBaru']['datasbbm']['idsbbm']==$id) {
            unset($_SESSION['currentPageSBBMBaru']['datasbbm']);
            unset($_SESSION['currentPageSBBMBaru']['cart']);
        }
        $this->redirect('mutasibarang.DaftarSBBM',true);		        
	}
    public function viewRecord ($sender,$param) {        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);   
        $str = "SELECT ms.idsbbm,ms.no_sbbm,ms.tanggal_sbbm,ms.sumber_dana,ps.nama_penyalur,ps.alamat,ps.kota,ms.no_faktur,ms.tanggal_faktur,ms.penerima FROM master_sbbm ms,penyalur_sbbm ps WHERE ps.idsbbm=ms.idsbbm AND ms.idsbbm=$id";
        $this->DB->setFieldTable(array('idsbbm','no_sbbm','tanggal_sbbm','sumber_dana','nama_penyalur','alamat','kota','no_faktur','tanggal_faktur','penerima'));
        $r=$this->DB->getRecord($str);
        $_SESSION['currentPageDaftarSBBM']['datasbbm']=$r[1];      
        $str = "SELECT iddetail_sbbm,idsbbm,idobat,kode_obat,no_reg,nama_obat,nama_merek,harga,idbentuk_sediaan,nama_bentuk,farmakologi,komposisi,kemasan,idprodusen,nama_produsen,no_batch,qty,tanggal_expire,status_obat,date_added,date_modified FROM detail_sbbm WHERE idsbbm='$id'";
        $this->DB->setFieldTable(array('idsbbm','idobat','kode_obat','no_reg','nama_obat','nama_merek','harga','idbentuk_sediaan','nama_bentuk','farmakologi','komposisi','kemasan','idprodusen','nama_produsen','no_batch','qty','tanggal_expire','status_obat','date_added','date_modified'));
        $r=$this->DB->getRecord($str);                            
        $_SESSION['currentPageDaftarSBBM']['cart']=$r;
        $this->redirect('mutasibarang.DaftarSBBM',true);
    }  
    public function itemCreatedCart($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {                        
            DaftarSBBM::$totalQTY += $item->DataItem['qty'];
            $harga=$item->DataItem['qty']*$item->DataItem['harga'];
            DaftarSBBM::$totalHARGA += $harga;
        }
    }    
    public function detailProcess() {
        $this->datasbbm = $_SESSION['currentPageDaftarSBBM']['datasbbm'];              
        $this->idProcess='view';                    
        $this->RepeaterCart->DataSource=$_SESSION['currentPageDaftarSBBM']['cart'];
		$this->RepeaterCart->dataBind();             
    }
    public function printOut ($sender,$param) {        
        $this->createObj('report');             
        $dataReport=$_SESSION['currentPageDaftarSBBM']['datasbbm'];                
        $this->report->setMode('pdf');        
        $dataReport['linkoutput']=$this->linkOutput; 
        $this->report->setDataReport($dataReport);        
        $this->report->printBarcode ();          
        $this->lblPrintout->Text="Cetak Barcode SBBM";
        $this->modalPrintOut->show();
    }
    public function closeSBBM ($sender,$param) {
        unset($_SESSION['currentPageDaftarSBBM']['datasbbm']);
        unset($_SESSION['currentPageDaftarSBBM']['cart']);
        $this->redirect('mutasibarang.DaftarSBBM',true);
    }
}
