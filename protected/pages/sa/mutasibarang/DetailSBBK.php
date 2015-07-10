<?php
prado::using ('Application.MainPageSA');
class DetailSBBK extends MainPageSA {    
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
        $status=$_SESSION['currentPageDetailSBBK']['status_sbbk'];        
        $str = "SELECT tanggal_sbbk,no_sbbk,kode_obat,nama_obat,kemasan,harga,permintaan,pemberian FROM master_sbbk ms,detail_sbbk ds WHERE ms.idsbbk=ds.idsbbk AND ms.status='$status'";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'nomor_sbbk' :
                    $cluasa="AND no_sbbk='%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbk ms,detail_sbbk ds WHERE ms.idsbbk=ds.idsbbk AND ms.status='$status'$cluasa",'ms.idsbbk');
                    $str = "$str $cluasa";
                break;
                case 'nomor_spmb' :
                    $cluasa=" AND no_spmb LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbk ms,detail_sbbk ds WHERE ms.idsbbk=ds.idsbbk AND ms.status='$status'$cluasa",'ms.idsbbk');
                    $str = "$str $cluasa";
                break;
                case 'kode_obat' :
                    $cluasa=" AND kode_obat LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbk ms,detail_sbbk ds WHERE ms.idsbbk=ds.idsbbk AND ms.status='$status'$cluasa",'ms.idsbbk');
                    $str = "$str $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbk ms,detail_sbbk ds WHERE ms.idsbbk=ds.idsbbk AND ms.status='$status'",'ms.idsbbk');		
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
        $str = "$str ORDER BY ms.date_added DESC,no_sbbk ASC LIMIT $offset,$limit";            
		$this->DB->setFieldTable(array('tanggal_sbbk','no_sbbk','kode_obat','nama_obat','kemasan','harga','permintaan','pemberian'));
		$r=$this->DB->getRecord($str,$offset+1);     
        $data=array();
        while (list($k,$v)=each($r)) {
            $v['subtotal']=$this->Obat->toRupiah($v['harga']*$v['pemberian']);
            $data[$k]=$v;
        }
		$this->RepeaterS->DataSource=$data;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}
}
