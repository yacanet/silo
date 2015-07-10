<?php
prado::using ('Application.MainPageAD');
class ObatPuskesmas extends MainPageAD{    
	public function onLoad($param) {		
		parent::onLoad($param);				        
		$this->showObatPuskesmas=true;      
        $this->createObj('DMaster');
        $this->createObj('Obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageObatPuskesmas'])||$_SESSION['currentPageObatPuskesmas']['page_name']!='ad.dmaster.ObatPuskesmas') {
				$_SESSION['currentPageObatPuskesmas']=array('page_name'=>'ad.dmaster.ObatPuskesmas','page_num'=>0,'search'=>false,'idprodusen'=>'none');												
			}   
            $_SESSION['currentPageObatPuskesmas']['search']=false;  
            $this->cmbFilterProdusen->DataSource=$this->DMaster->getListProdusen ();
            $this->cmbFilterProdusen->Text=$_SESSION['currentPageObatPuskesmas']['idprodusen'];
            $this->cmbFilterProdusen->DataBind();
			$this->populateData ();	
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageObatPuskesmas']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageObatPuskesmas']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageObatPuskesmas']['search']=true;
        $this->populateData($_SESSION['currentPageObatPuskesmas']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageObatPuskesmas']['idprodusen']=$this->cmbFilterProdusen->Text;
        $this->populateData();
	}    
    protected function populateData ($search=false) {        
        $idprodusen=$_SESSION['currentPageObatPuskesmas']['idprodusen'];
        $idpuskesmas=$this->idpuskesmas;
        $str_produsen=$idprodusen=='none' ?'':" AND idprodusen=$idprodusen";
        $str = "SELECT idobat,kode_obat,nama_obat,nama_bentuk,kemasan,harga,idprodusen,stock FROM master_obat_puskesmas WHERE idpuskesmas='$idpuskesmas'$str_produsen";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa=" AND kode_obat='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_obat_puskesmas WHERE idpuskesmas='$idpuskesmas' $cluasa",'idobat');
                    $str = "$str $cluasa";
                break;
                case 'nama' :
                    $cluasa=" AND nama_obat LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_obat_puskesmas WHERE idpuskesmas='$idpuskesmas' $cluasa",'idobat');
                    $str = "$str $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ("master_obat_puskesmas WHERE idpuskesmas='$idpuskesmas'",'idobat');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageObatPuskesmas']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageObatPuskesmas']['page_num']=0;}
        $str = "$str ORDER BY stock DESC,nama_obat ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idobat','kode_obat','nama_obat','nama_bentuk','kemasan','harga','idprodusen','stock'));
		$r=$this->DB->getRecord($str,$offset+1);     
        $data_r=array();
        while (list($k,$v)=each($r)) {            
            $v['nama_produsen']=$this->DMaster->getNamaProdusenByID($v['idprodusen']);            
            $data_r[$k]=$v;
        }
		$this->RepeaterS->DataSource=$data_r;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}
    public function viewRecord ($sender,$param) {
        $this->createObj('obat');
        $id=$this->getDataKeyField($sender,$this->RepeaterS);   
        $dataobat = $this->Obat->getInfoMasterObat($id);        
        $this->setInfoObat($dataobat);
        $this->modalInfoObat->show();
    }     
    public function editRecord ($sender,$param) {
        $this->idProcess='edit';        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);        
		$this->hiddenid->Value=$id;        
        $str = "SELECT min_stock,max_stock FROM master_obat_puskesmas WHERE idobat=$id";        
        $this->DB->setFieldTable(array('min_stock','max_stock','enabled'));
        $r=$this->DB->getRecord($str);   
                
        $this->txtEditMinimumStock->Text=$r[1]['min_stock'];        
        $this->txtEditMaksimumStock->Text=$r[1]['max_stock'];        
    }
    public function updateData ($sender,$param) {
        if ($this->IsValid) {     
            $idobat=$this->hiddenid->Value;           
            $minimum_stock=$this->txtEditMinimumStock->Text;
            $maximum_stock=$this->txtEditMaksimumStock->Text;            
            $str = "UPDATE master_obat_puskesmas SET min_stock=$minimum_stock,max_stock=$maximum_stock WHERE idobat=$idobat";            
            $this->DB->updateRecord($str);            
            $this->redirect('dmaster.ObatPuskesmas',true);              
        }
    }    
    public function copyMasterObat ($sender,$param) {
        $idpuskesmas=$this->idpuskesmas;
        $str = "INSERT INTO master_obat_puskesmas (idobat,idpuskesmas,kode_obat,no_reg,nama_obat,nama_merek,harga,idsatuan_obat,idgolongan,idbentuk_sediaan,nama_bentuk,farmakologi,komposisi,kemasan,idprodusen,stock,min_stock,max_stock,status_obat) SELECT mo.idobat,$idpuskesmas,mo.kode_obat,mo.no_reg,mo.nama_obat,mo.nama_merek,mo.harga,mo.idsatuan_obat,mo.idgolongan,mo.idbentuk_sediaan,mo.nama_bentuk,mo.farmakologi,mo.komposisi,mo.kemasan,mo.idprodusen,0,mo.min_stock,mo.max_stock,mo.status_obat FROM master_obat mo WHERE kode_obat NOT IN (SELECT kode_obat FROM master_obat_puskesmas WHERE idpuskesmas=$idpuskesmas)";            
        $this->DB->insertRecord($str);
        $this->redirect('dmaster.ObatPuskesmas',true);              
    }
    public function printOut ($sender,$param) {        
        $this->createObj('report');             
        $this->report->setMode('excel2007');        
        $dataReport['linkoutput']=$this->linkOutput; 
        $this->report->setDataReport($dataReport);        
        $this->report->printDaftarObat ($this->idpuskesmas);          
        $this->lblPrintout->Text="Laporan Daftar Obat";
        $this->modalPrintOut->show();
    }
}
?>