<?php
prado::using ('Application.MainPageSA');
class ModalDaftarSBBM extends MainPageSA {    
    public static $totalQTY=0;
    public $datasbbm;    
    public function OnPreInit ($param) {	
		parent::onPreInit ($param);	
        $this->MasterClass='Application.layouts.ModalTemplate';				
	}
	public function onLoad($param) {		
		parent::onLoad($param);		        
        $this->createObj('DMaster');
        $this->createObj('Obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	            
            if (!isset($_SESSION['currentPageModalSBBM'])||$_SESSION['currentPageModalSBBM']['page_name']!='sa.mutasibarang.DetailSBBM') {
                $_SESSION['currentPageModalSBBM']=array('page_name'=>'sa.mutasibarang.DetailSBBM','page_num'=>0,'search'=>false);												
            }   
            $_SESSION['currentPageModalSBBM']['search']=false;                          
            $this->populateData ();	            
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageModalSBBM']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageModalSBBM']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageModalSBBM']['search']=true;
        $this->populateData($_SESSION['currentPageModalSBBM']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageModalSBBM']['status_sbbm']=$this->cmbFilterStatus->Text;
        $this->populateData();
	}
    protected function populateData ($search=false) {                
        $str = "SELECT tanggal_sbbm,no_sbbm,no_batch,nama_obat,harga,kemasan,nama_produsen,qty,tanggal_expire,nama_penyalur,barcode FROM master_sbbm ms,detail_sbbm ds WHERE ms.idsbbm=ds.idsbbm AND ms.status='complete' AND tanggal_sbbm <= NOW()";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa=" AND kode_obat='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbm ms,detail_sbbm ds WHERE ms.idsbbm=ds.idsbbm AND ms.status='complete' $cluasa",'iddetail_sbbm');
                    $str = "$str $cluasa";
                break;
                case 'nama' :
                    $cluasa=" AND nama_obat LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbm ms,detail_sbbm ds WHERE ms.idsbbm=ds.idsbbm AND ms.status='complete' $cluasa",'iddetail_sbbm');
                    $str = "$str $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbm ms,detail_sbbm ds WHERE ms.idsbbm=ds.idsbbm AND ms.status='complete'",'iddetail_sbbm');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageModalSBBM']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageModalSBBM']['page_num']=0;}
        $str = "$str ORDER BY ms.date_added DESC,no_sbbm ASC LIMIT $offset,$limit";            
		$this->DB->setFieldTable(array('tanggal_sbbm','no_sbbm','no_batch','nama_obat','harga','kemasan','nama_produsen','qty','tanggal_expire','nama_penyalur','barcode'));
		$r=$this->DB->getRecord($str,$offset+1);      
        
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}
}
