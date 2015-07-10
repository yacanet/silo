<?php
prado::using ('Application.MainPageSA');
class ModalDaftarLPO extends MainPageSA {    
    public static $totalQTY=0;
    public static $totalHARGA=0;    
    public function OnPreInit ($param) {	
		parent::onPreInit ($param);	
        $this->MasterClass='Application.layouts.ModalTemplate';				
	}
	public function onLoad($param) {		
		parent::onLoad($param);		        		            
        $this->createObj('Obat');
        $this->createObj('DMaster');        
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageModalDaftarLPO'])||$_SESSION['currentPageModalDaftarLPO']['page_name']!='sa.permintaan.ModalDaftarLPO') {
                $_SESSION['currentPageModalDaftarLPO']=array('page_name'=>'sa.permintaan.ModalDaftarLPO','page_num'=>0,'search'=>false);												
            }   
            $_SESSION['currentPageModalDaftarLPO']['search']=false;                                          
            $this->populateData ();	            
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageModalDaftarLPO']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageModalDaftarLPO']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageModalDaftarLPO']['search']=true;
        $this->populateData($_SESSION['currentPageModalDaftarLPO']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageModalDaftarLPO']['response_lpo']=$this->cmbResponseLPO->Text;
        $this->populateData();
	}       
    public function populateData ($search=false) {     
        $idlpo=$this->REQUEST['id'];        
        if ($search) {            
            $str = "SELECT idlpo,idobat,idobat_puskesmas,kode_obat,nama_obat,kemasan,stock_awal,penerimaan AS total_penerimaan,persediaan,pemakaian AS total_pemakaian,stock_akhir,permintaan AS qty FROM detail_lpo WHERE idlpo='$idlpo'";
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa=" AND kode_obat='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("detail_lpo WHERE idlpo='$idlpo'$cluasa",'iddetail_lpo');
                    $str = "$str $cluasa";
                break;
                case 'nama' :
                    $cluasa=" AND nama_obat LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("detail_lpo WHERE idlpo='$idlpo'$cluasa",'iddetail_lpo');
                    $str = "$str $cluasa";
                break;
            }
        }else {
            $str = "SELECT idlpo,idobat,idobat_puskesmas,kode_obat,nama_obat,kemasan,stock_awal,penerimaan AS total_penerimaan,persediaan,pemakaian AS total_pemakaian,stock_akhir,permintaan AS qty FROM detail_lpo WHERE idlpo='$idlpo'";
            $jumlah_baris=$this->DB->getCountRowsOfTable ("detail_lpo WHERE idlpo='$idlpo'",'iddetail_lpo');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageModalDaftarLPO']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageModalDaftarLPO']['page_num']=0;}
        $str = "$str ORDER BY permintaan DESC,nama_obat ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idlpo','idobat','idobat_puskesmas','kode_obat','nama_obat','kemasan','stock_awal','total_penerimaan','persediaan','total_pemakaian','stock_akhir','qty'));
		$r=$this->DB->getRecord($str,$offset+1);          
                
        $this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}   
    
}
