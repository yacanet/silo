<?php
prado::using ('Application.MainPageAD');
class DetailSBBK extends MainPageAD {    
    public static $totalQTY=0;
    public $datasbbk;    
	public function onLoad($param) {		
		parent::onLoad($param);		
        $this->showSubMenuMutasiBarangKeluar=true;
		$this->showDetailSBBK=true;      
        $this->createObj('DMaster');
        $this->createObj('Obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	            
            if (!isset($_SESSION['currentPageDetailSBBK'])||$_SESSION['currentPageDetailSBBK']['page_name']!='sa.mutasibarang.DetailSBBK') {
                $_SESSION['currentPageDetailSBBK']=array('page_name'=>'sa.mutasibarang.DetailSBBK','page_num'=>0,'search'=>false,'status_sbbk'=>'complete');												
            }   
            $_SESSION['currentPageDetailSBBK']['search']=false;              
            $this->cmbFilterStatus->Text=$_SESSION['currentPageDetailSBBK']['status_sbbk'];            
            $this->populateData ();	            
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageDetailSBBK']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageDetailSBBK']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageDetailSBBK']['search']=true;
        $this->populateData($_SESSION['currentPageDetailSBBK']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageDetailSBBK']['status_sbbk']=$this->cmbFilterStatus->Text;
        $this->populateData();
	}
    protected function populateData ($search=false) {        
        $idpuskesmas=$this->idpuskesmas;
        $status=$_SESSION['currentPageDetailSBBK']['status_sbbk'];        
        $str = "SELECT tanggal_sbbk_puskesmas,no_sbbk_puskesmas,kode_obat,nama_obat,kemasan,harga,pemberian_puskesmas FROM master_sbbk_puskesmas ms,detail_sbbk_puskesmas ds WHERE ms.idsbbk_puskesmas=ds.idsbbk_puskesmas AND ms.idpuskesmas=$idpuskesmas AND ms.status_puskesmas='$status'";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa=" AND kode_obat='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbk_puskesmas ms,detail_sbbk_puskesmas ds WHERE ms.idsbbk_puskesmas=ds.idsbbk_puskesmas AND ms.idpuskesmas=$idpuskesmas $cluasa",'ms.idsbbk_puskesmas');
                    $str = "$str $cluasa";
                break;
                case 'nama' :
                    $cluasa=" AND nama_obat LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbk_puskesmas ms,detail_sbbk_puskesmas ds WHERE ms.idsbbk_puskesmas=ds.idsbbk_puskesmas AND ms.idpuskesmas=$idpuskesmas $cluasa",'ms.idsbbk_puskesmas');
                    $str = "$str $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbk_puskesmas ms,detail_sbbk_puskesmas ds WHERE ms.idsbbk_puskesmas=ds.idsbbk_puskesmas AND ms.idpuskesmas=$idpuskesmas AND ms.status_puskesmas='$status'",'ms.idsbbk_puskesmas');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageDetailSBBK']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageDetailSBBK']['page_num']=0;}
        $str = "$str ORDER BY ms.date_added DESC,no_sbbk_puskesmas ASC LIMIT $offset,$limit";            
		$this->DB->setFieldTable(array('tanggal_sbbk_puskesmas','no_sbbk_puskesmas','kode_obat','nama_obat','kemasan','harga','pemberian_puskesmas'));
		$r=$this->DB->getRecord($str,$offset+1);          
        $data = array();
        while (list($k,$v)=each($r)) {                
            $v['subtotal']=$this->Obat->toRupiah($v['harga']*$v['pemberian_puskesmas']);
            $data[$k]=$v;
        }
		$this->RepeaterS->DataSource=$data;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}
}
