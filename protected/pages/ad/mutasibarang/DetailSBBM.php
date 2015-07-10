<?php
prado::using ('Application.MainPageAD');
class DetailSBBM extends MainPageAD {    
    public static $totalQTY=0;
    public $datasbbk;    
	public function onLoad($param) {		
		parent::onLoad($param);		
        $this->showSubMenuMutasiBarangMasuk=true;
		$this->showDetailSBBM=true;      
        $this->createObj('DMaster');
        $this->createObj('Obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	            
            if (!isset($_SESSION['currentPageDetailSBBM'])||$_SESSION['currentPageDetailSBBM']['page_name']!='ad.mutasibarang.DetailSBBM') {
                $_SESSION['currentPageDetailSBBM']=array('page_name'=>'ad.mutasibarang.DetailSBBM','page_num'=>0,'search'=>false,'status_sbbk'=>'complete');												
            }   
            $_SESSION['currentPageDetailSBBM']['search']=false;              
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
		$_SESSION['currentPageDetailSBBM']['status_sbbk']=$this->cmbFilterStatus->Text;
        $this->populateData();
	}
    protected function populateData ($search=false) {
        $ta=$_SESSION['ta'];
        $idpuskesmas=$this->idpuskesmas;        
        $str = "SELECT tanggal_sbbm_puskesmas,no_sbbk_gudang,tanggal_sbbk_gudang,kode_obat,nama_obat,kemasan,harga,qty,tanggal_expire,barcode FROM master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsb WHERE msb.idsbbm_puskesmas=dsb.idsbbm_puskesmas AND msb.status_puskesmas='complete' AND msb.idpuskesmas=$idpuskesmas AND tahun_puskesmas=$ta";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa=" AND kode_obat='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsb WHERE msb.idsbbm_puskesmas=dsb.idsbbm_puskesmas AND msb.status_puskesmas='complete' AND msb.idpuskesmas=$idpuskesmas AND tahun_puskesmas=$ta $cluasa",'dsb.iddetail_sbbm_puskesmas');
                    $str = "$str $cluasa";
                break;
                case 'nama' :
                    $cluasa=" AND nama_obat LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsb WHERE msb.idsbbm_puskesmas=dsb.idsbbm_puskesmas AND msb.status_puskesmas='complete' AND msb.idpuskesmas=$idpuskesmas AND tahun_puskesmas=$ta $cluasa",'dsb.iddetail_sbbm_puskesmas');
                    $str = "$str $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsb WHERE msb.idsbbm_puskesmas=dsb.idsbbm_puskesmas AND msb.status_puskesmas='complete' AND msb.idpuskesmas=$idpuskesmas AND tahun_puskesmas=$ta",'dsb.iddetail_sbbm_puskesmas');		
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
        $str = "$str ORDER BY msb.tanggal_sbbm_puskesmas DESC,no_sbbk_gudang ASC LIMIT $offset,$limit";            
		$this->DB->setFieldTable(array('tanggal_sbbm_puskesmas','no_sbbk_gudang','tanggal_sbbk_gudang','kode_obat','nama_obat','kemasan','harga','qty','barcode'));
		$r=$this->DB->getRecord($str,$offset+1);          
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}
}
