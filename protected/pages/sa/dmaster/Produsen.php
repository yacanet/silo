<?php
prado::using ('Application.MainPageSA');
class Produsen extends MainPageSA {    
	public function onLoad($param) {		
		parent::onLoad($param);				        
		$this->showProdusen=true;      
        $this->createObj('DMaster');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageProdusen'])||$_SESSION['currentPageProdusen']['page_name']!='sa.dmaster.Produsen') {
				$_SESSION['currentPageProdusen']=array('page_name'=>'sa.dmaster.Produsen','page_num'=>0,'search'=>false);												
			}   
            $_SESSION['currentPageProdusen']['search']=false;            
			$this->populateData ();	
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageProdusen']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageProdusen']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageProdusen']['search']=true;
        $this->populateData($_SESSION['currentPageProdusen']['search']);
	}
    protected function populateData ($search=false) {        
        $str = "SELECT idprodusen,nama_produsen,alamat,notelp,enabled FROM produsen";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {                
                case 'nama' :
                    $cluasa="nama_produsen LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("produsen WHERE $cluasa",'idprodusen');
                    $str = "$str WHERE $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ('produsen','idprodusen');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageProdusen']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageProdusen']['page_num']=0;}
        $str = "$str ORDER BY nama_produsen ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idprodusen','nama_produsen','alamat','notelp','enabled'));
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
            $nama_produsen=addslashes(strtoupper($this->txtAddNamaProdusen->Text));            
            $alamat=addslashes($this->txtAddAlamatProdusen->Text);
            $kota=addslashes($this->txtAddKota->Text);
            $notelp=addslashes($this->txtAddNoTelp->Text);                                                
            $email=addslashes($this->txtAddEmail->Text);                        
            $web=addslashes($this->txtAddWeb->Text);                        
            $str = "INSERT INTO produsen(idprodusen,nama_produsen,alamat,kota,notelp,email,web) VALUES (NULL,'$nama_produsen','$alamat','$kota','$notelp','$email','$web')";            
            $this->DB->insertRecord($str);
            if ($this->Application->Cache) {                                
                $dataitem=$this->DMaster->getList('produsen WHERE enabled=1',array('idprodusen','nama_produsen'),'nama_produsen',null,1);
                $dataitem['none']='Seluruh Produsen';    
                $this->Application->Cache->set('listprodusen',$dataitem);                    
            }
            $this->redirect('dmaster.Produsen',true);            
        }
    }
    public function editRecord ($sender,$param) {
        $this->idProcess='edit';        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);        
		$this->hiddenid->Value=$id;        
        $str = "SELECT idprodusen,nama_produsen,alamat,kota,notelp,email,web,enabled FROM produsen WHERE idprodusen=$id";        
        $this->DB->setFieldTable(array('idprodusen','nama_produsen','alamat','kota','notelp','email','web','enabled'));
        $r=$this->DB->getRecord($str);   
        $this->txtEditNamaProdusen->Text=$r[1]['nama_produsen'];        
        $this->txtEditAlamatProdusen->Text=$r[1]['alamat'];
        $this->txtEditKota->Text=$r[1]['kota'];        
        $this->txtEditNoTelp->Text=$r[1]['notelp'];                        
        $this->txtEditEmail->Text=$r[1]['email'];
        $this->txtEditWeb->Text=$r[1]['web'];
        $this->cmbEditStatus->Text=$r[1]['enabled'];
        
    }
    public function updateData ($sender,$param) {
        if ($this->IsValid) {                 
            $id=$this->hiddenid->Value;
            $nama_produsen=addslashes(strtoupper($this->txtEditNamaProdusen->Text));            
            $alamat=addslashes($this->txtEditAlamatProdusen->Text);
            $kota=addslashes($this->txtEditKota->Text);
            $notelp=addslashes($this->txtEditNoTelp->Text);                                           
            $email=addslashes($this->txtEditEmail->Text);                        
            $web=addslashes($this->txtEditWeb->Text);                        
            $status=$this->cmbEditStatus->Text;
            $str = "UPDATE produsen SET nama_produsen='$nama_produsen',alamat='$alamat',kota='$kota',notelp='$notelp',email='$email',web='$web',enabled=$status WHERE idprodusen=$id";            
            $this->DB->updateRecord($str);
            if ($this->Application->Cache) {                                
                $dataitem=$this->DMaster->getList('produsen WHERE enabled=1',array('idprodusen','nama_produsen'),'nama_produsen',null,1);
                $dataitem['none']='Seluruh Produsen';    
                $this->Application->Cache->set('listprodusen',$dataitem);                    
            }
            $this->redirect('dmaster.Produsen',true);            
        }
    }    
    public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);        
        $this->DB->deleteRecord("produsen WHERE idprodusen=$id");
            
        if ($this->Application->Cache) {                                
            $dataitem=$this->DMaster->getList('produsen WHERE enabled=1',array('idprodusen','nama_produsen'),'nama_produsen',null,1);
            $dataitem['none']='Seluruh Produsen';    
            $this->Application->Cache->set('listprodusen',$dataitem);                    
        }
        $this->redirect('dmaster.Produsen',true);		        
	}
}