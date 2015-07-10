<?php
prado::using ('Application.MainPageSA');
class SatuanObat extends MainPageSA {    
	public function onLoad($param) {		
		parent::onLoad($param);				        
        $this->showSubMenuSettingObatObatan=true;      
		$this->showSatuanObat=true;      
        $this->createObj('DMaster');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageSatuanObat'])||$_SESSION['currentPageSatuanObat']['page_name']!='sa.setting.SatuanObat') {
				$_SESSION['currentPageSatuanObat']=array('page_name'=>'sa.setting.SatuanObat','page_num'=>0,'search'=>false);												
			}   
            $_SESSION['currentPageSatuanObat']['search']=false;            
			$this->populateData ();	
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageSatuanObat']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageSatuanObat']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageSatuanObat']['search']=true;
        $this->populateData($_SESSION['currentPageSatuanObat']['search']);
	}
    protected function populateData ($search=false) {        
        $str = "SELECT idsatuan_obat,nama_satuan,deskripsi,enabled FROM satuanobat";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {                
                case 'nama' :
                    $cluasa="nama_satuan LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("satuanobat WHERE $cluasa",'idsatuan_obat');
                    $str = "$str WHERE $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ('satuanobat','idsatuan_obat');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageSatuanObat']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageSatuanObat']['page_num']=0;}
        $str = "$str ORDER BY nama_satuan ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idsatuan_obat','nama_satuan','deskripsi','enabled'));
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
            $nama_satuan=addslashes(strtoupper($this->txtAddNamaSatuan->Text));            
            $deskripsi=addslashes($this->txtAddDeskripsi->Text);            
            $str = "INSERT INTO satuanobat(idsatuan_obat,nama_satuan,deskripsi) VALUES (NULL,'$nama_satuan','$deskripsi')";            
            $this->DB->insertRecord($str);
            if ($this->Application->Cache) {                                
                $dataitem=$this->DMaster->getList('satuanobat WHERE enabled=1',array('idsatuan_obat','nama_satuan'),'nama_satuan',null,1);
                $dataitem['none']='Seluruh Satuan Obat';    
                $this->Application->Cache->set('listsatuanobat',$dataitem);                    
            }
            $this->redirect('setting.SatuanObat',true);            
        }
    }
    public function editRecord ($sender,$param) {
        $this->idProcess='edit';        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);        
		$this->hiddenid->Value=$id;        
        $str = "SELECT idsatuan_obat,nama_satuan,deskripsi,enabled FROM satuanobat WHERE idsatuan_obat=$id";        
        $this->DB->setFieldTable(array('idsatuan_obat','nama_satuan','deskripsi','enabled'));
        $r=$this->DB->getRecord($str);   
        $this->txtEditNamaSatuan->Text=$r[1]['nama_satuan'];        
        $this->txtEditDeskripsi->Text=$r[1]['deskripsi'];        
        $this->cmbEditStatus->Text=$r[1]['enabled'];
        
    }
    public function updateData ($sender,$param) {
        if ($this->IsValid) {                 
            $id=$this->hiddenid->Value;
            $nama_satuan=addslashes(strtoupper($this->txtEditNamaSatuan->Text));            
            $deskripsi=addslashes($this->txtEditDeskripsi->Text);                  
            $status=$this->cmbEditStatus->Text;
            $str = "UPDATE satuanobat SET nama_satuan='$nama_satuan',deskripsi='$deskripsi',enabled=$status WHERE idsatuan_obat=$id";            
            $this->DB->updateRecord($str);
            if ($this->Application->Cache) {                                
                $dataitem=$this->DMaster->getList('satuanobat WHERE enabled=1',array('idsatuan_obat','nama_satuan'),'nama_satuan',null,1);
                $dataitem['none']='Seluruh Satuan Obat';    
                $this->Application->Cache->set('listsatuanobat',$dataitem);                    
            }
            $this->redirect('setting.SatuanObat',true);            
        }
    }    
    public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);        
        $this->DB->deleteRecord("satuanobat WHERE idsatuan_obat=$id");
            
        if ($this->Application->Cache) {                                
            $dataitem=$this->DMaster->getList('satuanobat WHERE enabled=1',array('idsatuan_obat','nama_satuan'),'nama_satuan',null,1);
            $dataitem['none']='Seluruh SatuanObat';    
            $this->Application->Cache->set('listsatuanobat',$dataitem);                    
        }
        $this->redirect('setting.SatuanObat',true);		        
	}
}