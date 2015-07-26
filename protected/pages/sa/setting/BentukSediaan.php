<?php
prado::using ('Application.MainPageSA');
class BentukSediaan extends MainPageSA {    
	public function onLoad($param) {		
		parent::onLoad($param);				        
        $this->showSubMenuSettingObatObatan=true;      
		$this->showBentukSediaan=true;      
        $this->createObj('DMaster');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageBentukSediaan'])||$_SESSION['currentPageBentukSediaan']['page_name']!='sa.setting.BentukSediaan') {
				$_SESSION['currentPageBentukSediaan']=array('page_name'=>'sa.setting.BentukSediaan','page_num'=>0,'search'=>false);												
			}   
            $_SESSION['currentPageBentukSediaan']['search']=false;            
			$this->populateData ();	
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageBentukSediaan']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageBentukSediaan']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageBentukSediaan']['search']=true;
        $this->populateData($_SESSION['currentPageBentukSediaan']['search']);
	}
    protected function populateData ($search=false) {        
        $str = "SELECT idbentuk_sediaan,nama_bentuk,deskripsi,enabled FROM bentuksediaan";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {                
                case 'nama' :
                    $cluasa="nama_bentuk LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("bentuksediaan WHERE $cluasa",'idbentuk_sediaan');
                    $str = "$str WHERE $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ('bentuksediaan','idbentuk_sediaan');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageBentukSediaan']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageBentukSediaan']['page_num']=0;}
        $str = "$str ORDER BY nama_bentuk ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idbentuk_sediaan','nama_bentuk','deskripsi','enabled'));
		$r=$this->DB->getRecord($str,$offset+1);                        
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}
    public function addProcess ($sender,$param) {
        $this->idProcess='add';        
    }    
    public function saveData ($sender,$param) {
        if ($this->IsValid) {                       
            $nama_bentuk=addslashes(strtoupper($this->txtAddNamaBentuk->Text));            
            $deskripsi=addslashes($this->txtAddDeskripsi->Text);            
            $str = "INSERT INTO bentuksediaan(idbentuk_sediaan,nama_bentuk,deskripsi) VALUES (NULL,'$nama_bentuk','$deskripsi')";            
            $this->DB->insertRecord($str);
            if ($this->Application->Cache) {                                
                $dataitem=$this->DMaster->getList('bentuksediaan WHERE enabled=1',array('idbentuk_sediaan','nama_bentuk'),'nama_bentuk',null,1);
                $dataitem['none']='Seluruh Bentuk Sedian';    
                $this->Application->Cache->set('listbentuksediaan',$dataitem);                    
            }
            $this->redirect('setting.BentukSediaan',true);            
        }
    }
    public function editRecord ($sender,$param) {
        $this->idProcess='edit';        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);        
		$this->hiddenid->Value=$id;        
        $str = "SELECT idbentuk_sediaan,nama_bentuk,deskripsi,enabled FROM bentuksediaan WHERE idbentuk_sediaan=$id";        
        $this->DB->setFieldTable(array('idbentuk_sediaan','nama_bentuk','deskripsi','enabled'));
        $r=$this->DB->getRecord($str);   
        $this->txtEditNamaBentuk->Text=$r[1]['nama_bentuk'];        
        $this->txtEditDeskripsi->Text=$r[1]['deskripsi'];        
        $this->cmbEditStatus->Text=$r[1]['enabled'];
        
    }
    public function updateData ($sender,$param) {
        if ($this->IsValid) {                 
            $id=$this->hiddenid->Value;
            $nama_bentuk=addslashes(strtoupper($this->txtEditNamaBentuk->Text));            
            $deskripsi=addslashes($this->txtEditDeskripsi->Text);                  
            $status=$this->cmbEditStatus->Text;
            $str = "UPDATE bentuksediaan SET nama_bentuk='$nama_bentuk',deskripsi='$deskripsi',enabled=$status WHERE idbentuk_sediaan=$id";            
            $this->DB->updateRecord($str);
            if ($this->Application->Cache) {                                
                $dataitem=$this->DMaster->getList('bentuksediaan WHERE enabled=1',array('idbentuk_sediaan','nama_bentuk'),'nama_bentuk',null,1);
                $dataitem['none']='Seluruh Bentuk Sediaan';    
                $this->Application->Cache->set('listbentuksediaan',$dataitem);                    
            }
            $this->redirect('setting.BentukSediaan',true);            
        }
    }    
    public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);                
        if ($this->DB->checkRecordIsExist('idbentuk_sediaan','master_obat',$id)) {                                
            $this->lblPrintout->Text='Gagal menghapus bentuk sediaan';
            $this->labelMessageError->Text = "ID bentuk sediaan ($id) sedang digunakan di master obat jadi tidak bisa dihapus.";
            $this->modalMessageError->show();
        }else{
            $this->DB->deleteRecord("bentuksediaan WHERE idbentuk_sediaan=$id");
            if ($this->Application->Cache) {                                
                $dataitem=$this->DMaster->getList('bentuksediaan WHERE enabled=1',array('idbentuk_sediaan','nama_bentuk'),'nama_bentuk',null,1);
                $dataitem['none']='Seluruh Bentuk Sediaan';    
                $this->Application->Cache->set('listbentuksediaan',$dataitem);                    
            }
            $this->redirect('setting.BentukSediaan',true);		        
        }
	}
}