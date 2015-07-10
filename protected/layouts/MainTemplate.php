<?php

class MainTemplate extends TTemplateControl {
    public function onLoad ($param) {
		parent::onLoad($param);		        
		if (!$this->Page->IsPostBack&&!$this->Page->IsCallback) {			
            $this->cmbSettingUmumTahun->Text=$_SESSION['ta'];       
            $this->labelUmumTahunKegiatan->Text=$_SESSION['ta']; 
            
            switch ($this->Page->getPagePath()) {
                case 'sa.mutasibarang.SBBMBaru' :                    
                    $datasbbm=$_SESSION['currentPageSBBMBaru']['datasbbm'];
                    $this->cmbSettingUmumTahun->Enabled=$datasbbm['mode']=='buat' || $datasbbm['mode']=='ubah' ? false:true;
                break;
            }
		}        
	}	   
    public function changeSettingUmumTahun($sender,$param) {
        $_SESSION['ta']=$this->cmbSettingUmumTahun->Text;        
        $this->labelUmumTahunKegiatan->Text=$_SESSION['ta'];   
        $this->populateLabelTahun();        
    }
    /**
     * digunakan untuk mengubah label tahun di beberapa halaman
     */
    private function populateLabelTahun () {
        switch ($this->Page->getPagePath()) {
            case 'sa.permintaan.DaftarLPO' :
                $this->Page->labelTahun->Text=$_SESSION['ta'];                         
                if ($this->Page->getDefaultProcess()) {
                    $this->Page->populateData();
                }
            break;
            case 'sa.permintaan.DaftarLPO' :
                $this->Page->labelTahun->Text=$_SESSION['ta'];                         
                if ($this->Page->getDefaultProcess()) {
                    $this->Page->populateData();
                }
            break;  
            case 'sa.permintaan.UsulanKebutuhan' :
                $this->Page->labelTahun->Text=$_SESSION['ta'];                         
                if ($this->Page->getDefaultProcess()) {
                    $this->Page->populateData();
                }
            break; 
            case 'sa.mutasibarang.PenghapusanStock' :
            case 'sa.mutasibarang.DaftarSBBM' :
                $this->Page->labelTahun->Text=$_SESSION['ta'];                         
                if ($this->Page->getDefaultProcess()) {
                    $this->Page->populateData();
                }
            break;            
            case 'sa.mutasibarang.DaftarSBBK' :
                $this->Page->labelTahunDaftarSBBK->Text=$_SESSION['ta'];                         
                if ($this->Page->getDefaultProcess()) {
                    $this->Page->populateData();
                }
            break;       
            case 'sa.report.MutasiObatBulanan' :
                $tanggal=date($_SESSION['ta'].'-m-01');
                $_SESSION['currentPageMutasiObatBulanan']['bulan']=$tanggal;
                $this->Page->lblBulanTahun->Text=$this->Page->TGL->tanggal('F Y',$_SESSION['currentPageMutasiObatBulanan']['bulan']);                                     
                if ($this->Page->getDefaultProcess()) {
                    $this->Page->populateData();
                }
            break;
            case 'sa.report.KeuanganPersediaan' :
            case 'sa.report.MutasiObatSemester' :
            case 'sa.report.MutasiObatTahunan' :
                $this->Page->lblTahun->Text=$_SESSION['ta'];                                     
                if ($this->Page->getDefaultProcess()) {
                    $this->Page->populateData();
                }
            break;
            case 'sa.report.AnalisaKetersediaan' :
                $this->Page->lblBulanTahun->Text=$this->Page->TGL->tanggal('F Y',$_SESSION['currentPageAnalisaKetersediaan']['bulan']);            
                if ($this->Page->getDefaultProcess()) {
                    $this->Page->populateData();
                }
            break;
            case 'sa.report.PerpetualStock' :                           
                if (isset($_SESSION['currentPagePerpetualStock']['dataobat']['idobat'])) {   
                    $this->Page->idProcess='add';
                    $this->Page->lblTahun->Text=$_SESSION['ta'];                 
                    $this->Page->populateData();                    
                }
            break;
            case 'sa.report.DinamikaLogistikObat' :                           
                $this->Page->lblTahun->Text=$_SESSION['ta'];                                     
                if ($this->Page->getDefaultProcess()) {
                    $this->Page->populateData();
                }
            break;
            case 'sa.report.DistribusiObat' :
                $tanggal=date($_SESSION['ta'].'-m-01');
                $_SESSION['currentPageDistribusiObat']['bulan']=$tanggal;                
                if ($this->Page->getDefaultProcess()) {
                    $this->Page->lblBulanTahun->Text=$this->Page->TGL->tanggal('F Y',$_SESSION['currentPageDistribusiObat']['bulan']);                                     
                    $this->Page->populateData();
                }elseif ($this->Page->getViewProcess(false)) {
                    $this->Page->lblDetailBulanTahun->Text=$this->Page->TGL->tanggal('F Y',$_SESSION['currentPageDistribusiObat']['bulan']);                                     
                    $this->populateDistribusiObat ();
                }
            break;            
            case 'ad.mutasibarang.PemakaianObatPOLI' :    
                $tanggal=date($_SESSION['ta'].'-m-01');
                $_SESSION['currentPagePemakaianObatPOLI']['bulan']=$tanggal;
                $this->Page->lblBulanTahun->Text=$this->Page->TGL->tanggal('F Y',$_SESSION['currentPagePemakaianObatPOLI']['bulan']);                                     
                if ($this->Page->getDefaultProcess()) {
                    $this->Page->populateData();
                }
            break;            
        }
    }
}
?>