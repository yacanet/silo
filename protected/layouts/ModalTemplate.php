<?php

class ModalTemplate extends TTemplateControl {
    public function onLoad ($param) {
		parent::onLoad($param);		        
		if (!$this->Page->IsPostBack&&!$this->Page->IsCallback) {			
           
		}        
	}
}
?>