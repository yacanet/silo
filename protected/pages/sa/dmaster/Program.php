<?php
prado::using ('Application.MainPageSA');
class Program extends MainPageSA {    
	public function onLoad($param) {		
		parent::onLoad($param);				        
		$this->showProgram=true;      
        $this->createObj('DMaster');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageProgram'])||$_SESSION['currentPageProgram']['page_name']!='sa.dmaster.Program') {
				$_SESSION['currentPageProgram']=array('page_name'=>'sa.dmaster.Program','page_num'=>0,'search'=>false);												
			}   
            $_SESSION['currentPageProgram']['search']=false;            
			$this->populateData ();	
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageProgram']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageProgram']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageProgram']['search']=true;
        $this->populateData($_SESSION['currentPageProgram']['search']);
	}
    public function itemCreated ($sender,$param) {
		$item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {	
            if ($item->DataItem['idprogram']==1){                
                $item->btnDelete->Enabled=false;                
                $item->btnDelete->CssClass='table-link disabled';
            }else{
                $item->btnDelete->Attributes->OnClick="if(!confirm('Anda ingin menghapus data User ini ?')) return false;";
            }
        }
    }
    protected function populateData ($search=false) {        
        $str = "SELECT idprogram,nama_program,tahun,enabled FROM program";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {                
                case 'nama' :
                    $cluasa="nama_program LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("program WHERE $cluasa",'idprogram');
                    $str = "$str WHERE $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ('program','idprogram');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageProgram']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageProgram']['page_num']=0;}
        $str = "$str ORDER BY nama_program ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idprogram','nama_program','tahun','enabled'));
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
            $nama_program=addslashes(strtoupper($this->txtAddNamaProgram->Text));                        
            $ta=$_SESSION['ta'];
            $str = "INSERT INTO program(idprogram,nama_program,tahun) VALUES (NULL,'$nama_program','$ta')";            
            $this->DB->insertRecord($str);
            if ($this->Application->Cache) {                                
                $dataitem=$this->DMaster->getList('program WHERE enabled=1',array('idprogram','nama_program'),'nama_program',null,1);
                $dataitem['none']='Seluruh Program';    
                $this->Application->Cache->set('listprogram',$dataitem);                    
            }
            $this->redirect('dmaster.Program',true);            
        }
    }
    public function editRecord ($sender,$param) {
        $this->idProcess='edit';        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);        
		$this->hiddenid->Value=$id;        
        $str = "SELECT idprogram,nama_program,enabled FROM program WHERE idprogram=$id";        
        $this->DB->setFieldTable(array('idprogram','nama_program','enabled'));
        $r=$this->DB->getRecord($str);   
        $this->txtEditNamaProgram->Text=$r[1]['nama_program'];                
        $this->cmbEditStatus->Text=$r[1]['enabled'];
        if ($id == 1) {            
            $this->cmbEditStatus->Enabled=false;
        }                
    }
    public function updateData ($sender,$param) {
        if ($this->IsValid) {                 
            $id=$this->hiddenid->Value;
            $nama_program=addslashes(strtoupper($this->txtEditNamaProgram->Text));                        
            $status=$this->cmbEditStatus->Text;
            $str = "UPDATE program SET nama_program='$nama_program',enabled=$status WHERE idprogram=$id";            
            $this->DB->updateRecord($str);
            if ($this->Application->Cache) {                                
                $dataitem=$this->DMaster->getList('program WHERE enabled=1',array('idprogram','nama_program'),'nama_program',null,1);
                $dataitem['none']='Seluruh Program';    
                $this->Application->Cache->set('listprogram',$dataitem);                    
            }
            $this->redirect('dmaster.Program',true);            
        }
    }    
    public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);        
        $this->DB->deleteRecord("program WHERE idprogram=$id");            
        if ($this->Application->Cache) {                                
            $dataitem=$this->DMaster->getList('program WHERE enabled=1',array('idprogram','nama_program'),'nama_program',null,1);
            $dataitem['none']='Seluruh Program';    
            $this->Application->Cache->set('listprogram',$dataitem);                    
        }
        $this->redirect('dmaster.Program',true);		        
	}
}