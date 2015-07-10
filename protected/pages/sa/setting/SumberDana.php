<?php
prado::using ('Application.MainPageSA');
class SumberDana extends MainPageSA {    
	public function onLoad($param) {		
		parent::onLoad($param);				        
        $this->showSubMenuSettingObatObatan=true;      
		$this->showSumberDana=true;      
        $this->createObj('DMaster');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageSumberDana'])||$_SESSION['currentPageSumberDana']['page_name']!='sa.setting.SumberDana') {
				$_SESSION['currentPageSumberDana']=array('page_name'=>'sa.setting.SumberDana','page_num'=>0,'search'=>false);												
			}   
            $_SESSION['currentPageSumberDana']['search']=false;            
			$this->populateData ();	
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageSumberDana']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageSumberDana']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageSumberDana']['search']=true;
        $this->populateData($_SESSION['currentPageSumberDana']['search']);
	}
    protected function populateData ($search=false) {        
        $str = "SELECT idsumber_dana,nama_sumber,enabled FROM sumber_dana";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {                
                case 'nama' :
                    $cluasa="nama_sumber LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("sumber_dana WHERE $cluasa",'idsumber_dana');
                    $str = "$str WHERE $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ('sumber_dana','idsumber_dana');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageSumberDana']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageSumberDana']['page_num']=0;}
        $str = "$str ORDER BY nama_sumber ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idsumber_dana','nama_sumber','enabled'));
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
            $nama_sumber=addslashes(strtoupper($this->txtAddNamaSumber->Text));                              
            $str = "INSERT INTO sumber_dana(idsumber_dana,nama_sumber) VALUES (NULL,'$nama_sumber')";            
            $this->DB->insertRecord($str);
            if ($this->Application->Cache) {                                
                $dataitem=$this->DMaster->getList('sumber_dana WHERE enabled=1',array('idsumber_dana','nama_sumber'),'nama_sumber',null,1);
                $dataitem['none']='Seluruh Sumber Dana';    
                $this->Application->Cache->set('listsumber_dana',$dataitem);                    
            }
            $this->redirect('setting.SumberDana',true);            
        }
    }
    public function editRecord ($sender,$param) {
        $this->idProcess='edit';        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);        
		$this->hiddenid->Value=$id;        
        $str = "SELECT idsumber_dana,nama_sumber,enabled FROM sumber_dana WHERE idsumber_dana=$id";        
        $this->DB->setFieldTable(array('idsumber_dana','nama_sumber','enabled'));
        $r=$this->DB->getRecord($str);   
        $this->txtEditNamaSumber->Text=$r[1]['nama_sumber'];                        
        $this->cmbEditStatus->Text=$r[1]['enabled'];        
    }
    public function updateData ($sender,$param) {
        if ($this->IsValid) {                 
            $id=$this->hiddenid->Value;
            $nama_sumber=addslashes(strtoupper($this->txtEditNamaSumber->Text));                        
            $status=$this->cmbEditStatus->Text;
            $str = "UPDATE sumber_dana SET nama_sumber='$nama_sumber',enabled=$status WHERE idsumber_dana=$id";            
            $this->DB->updateRecord($str);
            if ($this->Application->Cache) {                                
                $dataitem=$this->DMaster->getList('sumber_dana WHERE enabled=1',array('idsumber_dana','nama_sumber'),'nama_sumber',null,1);
                $dataitem['none']='Seluruh Sumber Dana';    
                $this->Application->Cache->set('listsumber_dana',$dataitem);                    
            }
            $this->redirect('setting.SumberDana',true);            
        }
    }    
    public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);        
        $this->DB->deleteRecord("sumber_dana WHERE idsumber_dana=$id");
            
        if ($this->Application->Cache) {                                
            $dataitem=$this->DMaster->getList('sumber_dana WHERE enabled=1',array('idsumber_dana','nama_sumber'),'nama_sumber',null,1);
            $dataitem['none']='Seluruh SumberDana';    
            $this->Application->Cache->set('listsumber_dana',$dataitem);                    
        }
        $this->redirect('setting.SumberDana',true);		        
	}
}