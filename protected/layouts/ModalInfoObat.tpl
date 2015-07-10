<com:NModalPanel ID="modalInfoObat" CssClass="md-modal md-effect-1 md-show">
    <div class="md-content" style="background-color: #3a87ad;color:#fff">
        <div class="modal-header">                
            <h4 class="modal-title"><i class="fa fa-medkit"></i> <strong>Informasi Obat "<com:TActiveLabel ID="lblInfoNamaObat" />"</strong></h4>
        </div>
        <div class="modal-body">
             <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-3 control-label"><strong>Kode Obat / No.Reg.POM: </strong></label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><com:TActiveLabel ID="lblInfoKodeObat" /> [<com:TActiveLabel ID="lblInfoNoReg" />]</p>
                    </div>                            
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><strong>Merek Obat: </strong></label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><com:TActiveLabel ID="lblInfoMerekObat" /></p>
                    </div>                            
                </div> 
                <div class="form-group">
                    <label class="col-sm-3 control-label"><strong>Bentuk Sediaan: </strong></label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><com:TActiveLabel ID="lblInfoBentukSediaan" /></p>
                    </div>                            
                </div> 
                <div class="form-group">
                    <label class="col-sm-3 control-label"><strong>Farmakologi: </strong></label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><com:TActiveLabel ID="lblInfoFarmakologi" /></p>
                    </div>                            
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><strong>Kemasan: </strong></label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><com:TActiveLabel ID="lblInfoKemasan" /></p>
                    </div>                            
                </div>  
                <div class="form-group">
                    <label class="col-sm-3 control-label"><strong>Komposisi: </strong></label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><com:TActiveLabel ID="lblInfoKomposisi" /></p>
                    </div>                            
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><strong>Produsen: </strong></label>
                    <div class="col-sm-8">
                        <p class="form-control-static"><com:TActiveLabel ID="lblInfoProdusen" /></p>
                    </div>                            
                </div>
             </div>
        </div>
        <div class="modal-footer"  style="background-color: #696969">
            <div class="row">
                <div class="col-sm-10 text-left">
                    <strong>Status :</strong> <com:TActiveLabel ID="lblInfoStatus" />;
                    <com:TActiveLabel ID="lblInfoStock" />
                </div>
                <div class="col-sm-2">
                    <a OnClick="new Modal.Box('<%=$this->modalInfoObat->ClientID%>').hide();return false;" class="btn btn-default"><i class='icon-off'></i> Close</a>                              
                </div>
            </div>
            
        </div>     
    </div>      
</com:NModalPanel>