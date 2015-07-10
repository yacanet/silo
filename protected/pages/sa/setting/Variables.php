<?php
prado::using ('Application.MainPageSA');
class Variables extends MainPageSA {    
	public function onLoad($param) {		
		parent::onLoad($param);				        
		$this->showVariable=true;              
		if (!$this->IsPostBack&&!$this->IsCallBack) {	           
            if (!isset($_SESSION['currentPageVariables'])||$_SESSION['currentPageVariables']['page_name']!='m.setting.Variables') {
				$_SESSION['currentPageVariables']=array('page_name'=>'m.setting.Variables','page_num'=>0);												
			}            
            $this->populateData (); 
		}
	}    
    public function populateData () {       
        $this->txtNIPKadis->Text=$this->setup->getSettingValue('nip_kadis');        
        $this->txtNamaKadis->Text=$this->setup->getSettingValue('nama_kadis');        
        $this->txtNIPKAGudang->Text=$this->setup->getSettingValue('nip_ka_gudang');        
        $this->txtNamaKAGudang->Text=$this->setup->getSettingValue('nama_ka_gudang');                
    }
    public function saveData ($sender,$param) {
        if ($this->IsValid) {
            $nipkadis=  addslashes($this->txtNIPKadis->Text);
            $str = "UPDATE setting SET value='$nipkadis' WHERE setting_id=5";            
            $this->DB->updateRecord($str);
            $namakadis=  addslashes($this->txtNamaKadis->Text);
            $str = "UPDATE setting SET value='$namakadis' WHERE setting_id=6";            
            $this->DB->updateRecord($str);
            
            $nipkagudang=  addslashes($this->txtNIPKAGudang->Text);
            $str = "UPDATE setting SET value='$nipkagudang' WHERE setting_id=7";            
            $this->DB->updateRecord($str);
            
            $namakagudang=  addslashes($this->txtNamaKAGudang->Text);
            $str = "UPDATE setting SET value='$namakagudang' WHERE setting_id=8";            
            $this->DB->updateRecord($str);
            
            $this->setup->loadSetting(true);            
            $this->redirect('setting.Variables',true);
        }
    }
}