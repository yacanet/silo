<?php
prado::using ('Application.MainPageSA');
class Farmakologi extends MainPageSA {    
	public function onLoad($param) {		
		parent::onLoad($param);				        
        $this->showSubMenuSettingObatObatan=true;      
		$this->showFarmakologi=true;      
        $this->createObj('DMaster');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageFarmakologi'])||$_SESSION['currentPageFarmakologi']['page_name']!='sa.setting.Farmakologi') {
				$_SESSION['currentPageFarmakologi']=array('page_name'=>'sa.setting.Farmakologi','page_num'=>0,'search'=>false);												
			}   
            $_SESSION['currentPageFarmakologi']['search']=false;            
			$this->populateData ();	
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageFarmakologi']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageFarmakologi']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageFarmakologi']['search']=true;
        $this->populateData($_SESSION['currentPageFarmakologi']['search']);
	}
    protected function populateData ($search=false) {        
        $str = "SELECT idfarmakologi,nama_farmakologi,deskripsi,enabled FROM farmakologi";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {                
                case 'nama' :
                    $cluasa="nama_farmakologi LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("farmakologi WHERE $cluasa",'idfarmakologi');
                    $str = "$str WHERE $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ('farmakologi','idfarmakologi');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageFarmakologi']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageFarmakologi']['page_num']=0;}
        $str = "$str ORDER BY nama_farmakologi ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idfarmakologi','nama_farmakologi','deskripsi','enabled'));
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
            $nama_farmakologi=addslashes(strtoupper($this->txtAddNamaFarmakologi->Text));            
            $deskripsi=addslashes($this->txtAddDeskripsi->Text);            
            $str = "INSERT INTO farmakologi(idfarmakologi,nama_farmakologi,deskripsi) VALUES (NULL,'$nama_farmakologi','$deskripsi')";            
            $this->DB->insertRecord($str);
            if ($this->Application->Cache) {                                
                $dataitem=$this->DMaster->getList('farmakologi WHERE enabled=1',array('idfarmakologi','nama_farmakologi'),'nama_farmakologi',null,1);
                $dataitem['none']='Seluruh Farmakologi';    
                $this->Application->Cache->set('listfarmakologi',$dataitem);                    
            }
            $this->redirect('setting.Farmakologi',true);            
        }
    }
    public function editRecord ($sender,$param) {
        $this->idProcess='edit';        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);        
		$this->hiddenid->Value=$id;        
        $str = "SELECT idfarmakologi,nama_farmakologi,deskripsi,enabled FROM farmakologi WHERE idfarmakologi=$id";        
        $this->DB->setFieldTable(array('idfarmakologi','nama_farmakologi','deskripsi','enabled'));
        $r=$this->DB->getRecord($str);   
        $this->txtEditNamaFarmakologi->Text=$r[1]['nama_farmakologi'];        
        $this->txtEditDeskripsi->Text=$r[1]['deskripsi'];        
        $this->cmbEditStatus->Text=$r[1]['enabled'];
        
    }
    public function updateData ($sender,$param) {
        if ($this->IsValid) {                 
            $id=$this->hiddenid->Value;
            $nama_farmakologi=addslashes(strtoupper($this->txtEditNamaFarmakologi->Text));            
            $deskripsi=addslashes($this->txtEditDeskripsi->Text);                  
            $status=$this->cmbEditStatus->Text;
            $str = "UPDATE farmakologi SET nama_farmakologi='$nama_farmakologi',deskripsi='$deskripsi',enabled=$status WHERE idfarmakologi=$id";            
            $this->DB->updateRecord($str);
            if ($this->Application->Cache) {                                
                $dataitem=$this->DMaster->getList('farmakologi WHERE enabled=1',array('idfarmakologi','nama_farmakologi'),'nama_farmakologi',null,1);
                $dataitem['none']='Seluruh Farmakologi';    
                $this->Application->Cache->set('listfarmakologi',$dataitem);                    
            }
            $this->redirect('setting.Farmakologi',true);            
        }
    }    
    public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);        
        $this->DB->deleteRecord("farmakologi WHERE idfarmakologi=$id");
            
        if ($this->Application->Cache) {                                
            $dataitem=$this->DMaster->getList('farmakologi WHERE enabled=1',array('idfarmakologi','nama_farmakologi'),'nama_farmakologi',null,1);
            $dataitem['none']='Seluruh Farmakologi';    
            $this->Application->Cache->set('listfarmakologi',$dataitem);                    
        }
        $this->redirect('setting.Farmakologi',true);		        
	}
}