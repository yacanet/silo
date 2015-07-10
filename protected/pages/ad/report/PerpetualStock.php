<?php
prado::using ('Application.MainPageAD');
class PerpetualStock extends MainPageAD {    
    public $dataobat;
	public function onLoad($param) {			
		parent::onLoad($param);				
        $this->showSubMenuReportStock = true;
		$this->showReportPerpetualStock = true;        
        $this->createObj('DMaster');
        $this->createObj('Obat');        
		if (!$this->IsPostBack&&!$this->IsCallBack) {                        
            if (isset($_SESSION['currentPagePerpetualStock']['dataobat']['idobat_puskesmas'])) {                
                $this->detailProcess();                
                $this->populateData();                
            }else {
                if (!isset($_SESSION['currentPagePerpetualStock'])||$_SESSION['currentPagePerpetualStock']['page_name']!='ad.report.PerpetualStock') {
                    $_SESSION['currentPagePerpetualStock']=array('page_name'=>'ad.report.PerpetualStock','page_num'=>0,'search'=>false,'dataobat'=>array());												
                }
            }			
		}	        
	}
    public function checkKodeObat ($sender,$param) {        
        $kode_obat=$param->Value;		
        if ($kode_obat != '') {
            try {                                 
                $idpuskesmas=$this->idpuskesmas;
                if (!$this->DB->checkRecordIsExist('kode_obat','master_obat_puskesmas',$kode_obat," AND idpuskesmas=$idpuskesmas")) {                                
                    throw new Exception ("Kode Obat ($kode_obat) tidak tersedia silahkan ganti dengan yang lain.");		
                }               
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }
    }
    public function viewPerpetuatStock($sender,$param) {
        if ($this->IsValid) {            
            $idpuskesmas=$this->idpuskesmas;
            $kode_obat=addslashes($this->txtKodeObat->Text);
            $str = "SELECT mo.idobat_puskesmas,mo.no_reg,mo.kode_obat,mo.nama_obat,mo.nama_merek,harga,idsatuan_obat,idgolongan,mo.idbentuk_sediaan,mo.nama_bentuk,mo.farmakologi,mo.kemasan,mo.komposisi,mo.idprodusen,p.nama_produsen,mo.stock,mo.min_stock,mo.max_stock,mo.status_obat FROM master_obat_puskesmas mo LEFT JOIN produsen p  ON (mo.idprodusen=p.idprodusen) WHERE kode_obat='$kode_obat' AND idpuskesmas=$idpuskesmas";
            $this->DB->setFieldTable(array('idobat_puskesmas','no_reg','kode_obat','nama_obat','nama_merek','harga','idsatuan_obat','idgolongan','idbentuk_sediaan','nama_bentuk','farmakologi','kemasan','komposisi','idprodusen','nama_produsen','stock','min_stock','max_stock','status_obat'));
            $r=$this->DB->getRecord($str);
            $data=  json_decode($r[1]['farmakologi']);            
            foreach ($data as $m) {
                $farmakologi="$farmakologi <span class=\"label label-info\" style=\"padding:3px;\">$m</span>";
            }
            $r[1]['farmakologi']=$farmakologi;
            $_SESSION['currentPagePerpetualStock']['dataobat']=$r[1];
            $this->redirect('report.PerpetualStock',true);
        }
    }     
    public function detailProcess() {
        $this->idProcess='add';
        $this->lblTahun->Text=$_SESSION['ta'];
        $this->dataobat = $_SESSION['currentPagePerpetualStock']['dataobat'];                      
    }       
    public function populateData ($search=false) {     
        $tahun=$_SESSION['ta'];
        $dataobat = $_SESSION['currentPagePerpetualStock']['dataobat']; 
        $idobat_puskesmas=$dataobat['idobat_puskesmas'];
        $str = "SELECT lksp.idobat_puskesmas,lksp.tanggal_puskesmas,lksp.qty_puskesmas,dsb.idsatuan_obat AS pembelian_idsatuan,dsb.harga AS pembelian_harga,dsb.harga*lksp.qty_puskesmas AS pembelian_jumlah,dsk.idsatuan_obat AS pengeluaran_idsatuan,dsk.harga AS pengeluaran_harga,dsk.harga*lksp.qty_puskesmas AS pengeluaran_jumlah,lksp.sisa_stock_puskesmas,lksp.mode_puskesmas,lksp.keterangan_puskesmas FROM log_ks_puskesmas lksp LEFT JOIN detail_sbbm_puskesmas dsb ON (lksp.iddetail_sbbm_puskesmas=dsb.iddetail_sbbm_puskesmas) LEFT JOIN detail_sbbk_puskesmas dsk ON (lksp.iddetail_sbbk_puskesmas=dsk.iddetail_sbbk_puskesmas) WHERE lksp.idobat_puskesmas=$idobat_puskesmas AND lksp.tahun_puskesmas=$tahun ORDER BY lksp.date_added ASC";
        $this->DB->setFieldTable(array('idobat_puskesmas','tanggal_puskesmas','qty_puskesmas','pembelian_idsatuan','pembelian_harga','pembelian_jumlah','pengeluaran_idsatuan','pengeluaran_harga','pengeluaran_jumlah','sisa_stock_puskesmas','mode_puskesmas','keterangan_puskesmas'));
        $r=$this->DB->getRecord($str);                  
        $data=array();        
        while (list($k,$v)=each($r)) {
            if ($v['mode_puskesmas']=='masuk') {
                $v['nama_satuan']=$this->DMaster->getNamaSatuanObat($v['pembelian_idsatuan']);
                $v['uraian']=$v['keterangan_puskesmas'];                                
                $v['harga']=$v['pembelian_harga'];
                $v['pembelian_qty']=$v['qty_puskesmas'];
                $v['saldo_jumlah']=$v['harga']*$v['sisa_stock_puskesmas'];                 
                $v['pengeluaran_qty']='-';                        
                $v['pengeluaran_jumlah']='-';
            }else {
                $v['nama_satuan']=$this->DMaster->getNamaSatuanObat($v['pengeluaran_idsatuan']);
                $v['uraian']=$v['keterangan_puskesmas'];
                $v['harga']=$v['pengeluaran_harga'];
                $v['pengeluaran_qty']=$v['qty_puskesmas'];
                $v['saldo_jumlah']=$v['harga']*$v['sisa_stock_puskesmas'];                                 
                $v['pembelian_qty']='-';                        
                $v['pembelian_jumlah']='-';                
            }            
            $data[$k]=$v;
        }
		$this->RepeaterS->DataSource=$data;
		$this->RepeaterS->dataBind();     
	} 
    public function printOut ($sender,$param) {        
        $this->createObj('report');     
        $tahun=$_SESSION['ta'];
        $this->report->setMode('excel2007'); 
        $dataobat=$_SESSION['currentPagePerpetualStock']['dataobat'];
        $dataReport['idobat_puskesmas']=$dataobat['idobat_puskesmas'];
        $dataReport['nama_obat']=$dataobat['nama_obat'];
        $dataReport['tahun']=$_SESSION['ta'];
        $dataReport['nama_puskesmas']=$this->Pengguna->getDataUser('nama_puskesmas');
        $dataReport['linkoutput']=$this->linkOutput; 
        $this->report->setDataReport($dataReport);        
        $this->report->printPerpetualStockPuskesmas ();          
        $this->lblPrintout->Text="Laporan Perpetual Stock Tahun $tahun";
        $this->modalPrintOut->show();
    }
    public function closePerpetualStock ($sender,$param) {
        unset($_SESSION['currentPagePerpetualStock']['dataobat']);        
        $this->redirect('report.PerpetualStock',true);
    }
    
}
?>