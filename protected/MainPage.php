<?php

class MainPage extends TPage {   
	/**
	* id process
	*/
	public $idProcess;	
	/**
	* Object Variable "Database"
	*
	*/
	public $DB;		
	/**
	* Object Variable "Setup"
	*
	*/
	public $setup;		
	/**
	* Object Variable "Tanggal"
	*
	*/
	public $TGL;	  
    /**
	* Object Variable "Report"
	*
	*/
	public $report;
    /**
	* Object Variable "User"	
	*/
	public $Pengguna;  
    /**
	* Object Variable "Data Master"	
	*/
	public $DMaster;      
    /**
	* Object Variable "Obat"	
	*/
	public $Obat;  
    /**     
     * show page dashboard
     */
    public $showDashboard=false;    
    /**
	* globar properti info obat
	*/
	public $infoObat;
	public function OnPreInit ($param) {	
		parent::onPreInit ($param);
		//instantiasi database		
		$this->DB = $this->Application->getModule ('db')->getLink();		
        //instantiasi fungsi setup
        $this->setup = $this->getLogic('Setup');                        
        //setting templaces yang aktif
        $this->MasterClass='Application.layouts.MainTemplate';		
		$this->Theme='cube';
	}
	public function onLoad ($param) {		
		parent::onLoad($param);				
		//instantiasi user
		$this->Pengguna = $this->getLogic('Users');
        //mengecek akses user terhadap halaman tertentu
        $datauser = $this->Pengguna->getDataUser();        
        if (isset($datauser['page'])) {	
            $currentPage=$this->Page->getPagePath();
            if ($currentPage != 'Logout') {
                $page=$datauser['page'];
                $currentPage=explode('.',$currentPage);	                
                if ($currentPage[0] != $page) {					                                                 
                    $this->redirect("$page.Home");
                }
            }
		}		    
		//instantiasi fungsi tanggal
		$this->TGL = $this->getLogic ('Penanggalan');        
	}
	/**
	* mendapatkan lo object
	* @return obj	
	*/
	public function getLogic ($_class=null) {
		if ($_class === null)
			return $this->Application->getModule ('logic');
		else 
			return $this->Application->getModule ('logic')->getInstanceOfClass($_class);	
	}
	/**
	* id proses tambah, delete, update,show
	*/
	protected function setIdProcess ($sender,$param) {		
		$this->idProcess=$sender->getId();
	}
	
	/**
	* add panel
	* @return boolean
	*/
	public function getAddProcess ($disabletoolbars=true) {
		if ($this->idProcess == 'add') {			
			if ($disabletoolbars)$this->disableToolbars();
			return true;
		}else {
			return false;
		}
	}
	
	/**
	* edit panel
	* @return boolean
	*/
	public function getEditProcess ($disabletoolbars=true) {
		if ($this->idProcess == 'edit') {			
			if ($disabletoolbars)$this->disableToolbars();
			return true;
		}else {
			return false;
		}

	}
	
	/**
	* view panel
	* @return boolean
	*/
	public function getViewProcess ($disabletoolbars=true) {
		if ($this->idProcess == 'view') {
			if ($disabletoolbars)$this->disableToolbars();			
			return true;
		}else {
			return false;
		}

	}
	
	/**
	* default panel
	* @return boolean
	*/
	public function getDefaultProcess () {
		if ($this->idProcess == 'add' || $this->idProcess == 'edit'|| $this->idProcess == 'view') {
			return false;
		}else {
			return true;
		}
	}	
	/**
	* digunakan untuk mendapatkan sebuah data key dari repeater
	* @return data key
	*/
	protected function getDataKeyField($sender,$repeater) {
		$item=$sender->getNamingContainer();
		return $repeater->DataKeys[$item->getItemIndex()];
	}    
    /**
	* Redirect
	*/
	protected function redirect ($page,$automaticpage=false,$param=array()) {
		$this->Response->Redirect($this->constructUrl($page,$automaticpage,$param));	
	}	  
    /**
     * digunakan untuk membuat url
     */
    public function constructUrl($page,$automaticpage=false,$param=array()) {              
        $url=$page;
        if ($automaticpage) {
            $this->Pengguna = $this->getLogic('Users');
            $tipeuser=$this->Pengguna->getTipeUser();    
            $url="$tipeuser.$url";
        }        
        return $this->Service->constructUrL($url,$param);
    }
    /**
     * digunakan untuk mendapatkan informasi paging
     */
    public function getInfoPaging ($repeater) {
        $str='';
        if ($repeater->Items->Count() > 0) {
            $jumlah_baris=$repeater->VirtualItemCount;
            $currentPage=$repeater->CurrentPageIndex;
            $offset=$currentPage*$repeater->PageSize;
            $awal=$offset+1;        
            $akhir=$repeater->Items->Count()+$offset;
            $str="Menampilkan $awal hingga $akhir dari $jumlah_baris";        
        }
        return $str;
    }
    /**
     * digunakan untuk mendapatkan informasi paging
     */
    public function setInfoObat ($dataobat) {
        $status_obat=$dataobat['status_obat'];
        $color = $status_obat == 'verified' ? 'label-primary' : 'label-warning';
        $this->lblInfoNoReg->Text=$dataobat['no_reg'] . " <span class=\"label $color\" style=\"padding:3px;\">$status_obat</span>";
        $this->lblInfoKodeObat->Text=$dataobat['kode_obat'];
        $this->lblInfoNamaObat->Text=$dataobat['nama_obat'];
        $this->lblInfoMerekObat->Text=$dataobat['nama_merek'];
        $this->lblInfoBentukSediaan->Text=$dataobat['nama_bentuk'];
        $data=$dataobat['farmakologi'];
        foreach ($data as $m) {
            $farmakologi="$farmakologi <span class=\"label label-info\" style=\"padding:3px;\">$m</span>";
        }
        $this->lblInfoFarmakologi->Text=$farmakologi;
        $this->lblInfoKemasan->Text=$dataobat['kemasan'];
        $this->lblInfoKomposisi->Text=$dataobat['komposisi'];
        $this->lblInfoProdusen->Text=$dataobat['nama_produsen'];
        $status=$dataobat['enabled']==true?'<span class="label label-success">Active</span>':'<span class="label label-warning">Inactive</span>';
        $this->lblInfoStatus->Text=$status;
        $str_stock = '<strong>Min. Stock:</strong> '.$dataobat['min_stock'].'; ';
        $str_stock .= '<strong>Max. Stock :</strong> '.$dataobat['max_stock'].'; ';
        $str_stock .= '<strong>Stock :</strong> '.$dataobat['stock'];
        $this->lblInfoStock->Text=$str_stock;
    }
    /**
     * digunakan untuk membuat berbagai macam object
     */
    public function createObj ($nama_object) {
        switch (strtolower($nama_object)) {
            case 'dmaster' :
                $this->DMaster = $this->getLogic('DMaster');
            break;            
            case 'obat' :
                $this->Obat = $this->getLogic('Obat');
            break;
            case 'report' :
                $this->report = $this->getLogic('Report');
            break;
        }
    }    
}
?>