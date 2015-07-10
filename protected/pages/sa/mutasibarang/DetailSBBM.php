<?php
prado::using ('Application.MainPageSA');
class DetailSBBM extends MainPageSA {    
    public static $totalQTY=0;
    public $datasbbm;    
	public function onLoad($param) {		
		parent::onLoad($param);		
        $this->showSubMenuMutasiBarangMasuk=true;
		$this->showDetailSBBM=true;      
        $this->createObj('DMaster');
        $this->createObj('Obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	            
            if (!isset($_SESSION['currentPageDetailSBBM'])||$_SESSION['currentPageDetailSBBM']['page_name']!='sa.mutasibarang.DetailSBBM') {
                $_SESSION['currentPageDetailSBBM']=array('page_name'=>'sa.mutasibarang.DetailSBBM','page_num'=>0,'search'=>false,'status_sbbm'=>'complete');												
            }   
            $_SESSION['currentPageDetailSBBM']['search']=false;              
            $this->cmbFilterStatus->Text=$_SESSION['currentPageDetailSBBM']['status_sbbm'];            
            $this->populateData ();	            
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageDetailSBBM']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageDetailSBBM']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageDetailSBBM']['search']=true;
        $this->populateData($_SESSION['currentPageDetailSBBM']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageDetailSBBM']['status_sbbm']=$this->cmbFilterStatus->Text;
        $this->populateData();
	}
    protected function populateData ($search=false) {        
        $status=$_SESSION['currentPageDetailSBBM']['status_sbbm'];        
        $str = "SELECT tanggal_sbbm,no_sbbm,kode_obat,nama_obat,harga,kemasan,nama_produsen,qty,tanggal_expire,nama_penyalur,barcode FROM master_sbbm ms,detail_sbbm ds WHERE ms.idsbbm=ds.idsbbm AND ms.status='$status'";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'nomor_sbbm' :
                    $cluasa=" AND no_sbbm LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbm ms,detail_sbbm ds WHERE ms.idsbbm=ds.idsbbm AND ms.status='$status'$cluasa",'ms.idsbbm');
                    $str = "$str $cluasa";
                break;
                case 'nomor_faktur' :
                    $cluasa=" AND nomor_faktur LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbm ms,detail_sbbm ds WHERE ms.idsbbm=ds.idsbbm AND ms.status='$status'$cluasa",'ms.idsbbm');
                    $str = "$str $cluasa";
                break;
                case 'kode_obat' :
                    $cluasa=" AND kode_obat='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbm ms,detail_sbbm ds WHERE ms.idsbbm=ds.idsbbm AND ms.status='$status'$cluasa",'ms.idsbbm');
                    echo $str = "$str $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbm ms,detail_sbbm ds WHERE ms.idsbbm=ds.idsbbm AND ms.status='$status'",'ms.idsbbm');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageDetailSBBM']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageDetailSBBM']['page_num']=0;}
        $str = "$str ORDER BY ms.date_added DESC,no_sbbm ASC LIMIT $offset,$limit";            
		$this->DB->setFieldTable(array('tanggal_sbbm','no_sbbm','kode_obat','nama_obat','harga','kemasan','nama_produsen','qty','tanggal_expire','nama_penyalur','barcode'));
		$r=$this->DB->getRecord($str,$offset+1);      
        
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}
}
