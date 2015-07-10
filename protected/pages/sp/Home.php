<?php
prado::using ('Application.MainPageSP');
class Home extends MainPageSP {
	public function onLoad($param) {		
		parent::onLoad($param);		            
        $this->showDashboard=true;
        $this->createObj('dmaster');
        $this->createObj('obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {              
            if (!isset($_SESSION['currentPageHome'])||$_SESSION['currentPageHome']['page_name']!='sp.Home') {                                
                
            }                
		}        
	} 
    
}
?>