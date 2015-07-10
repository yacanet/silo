<?php
prado::using ('Application.MainPageSA');
class PerpetualStock extends MainPageSA {    
    public $dataobat;
	public function onLoad($param) {			
		parent::onLoad($param);				
        $this->showSubMenuReportStock = true;
		$this->showReportPerpetualStock = true;        
        $this->createObj('DMaster');
        $this->createObj('Obat');        
		if (!$this->IsPostBack&&!$this->IsCallBack) {                        
            if (isset($_SESSION['currentPagePerpetualStock']['dataobat']['idobat'])) {                
                $this->detailProcess();                
                $this->populateData();                
            }else {
                if (!isset($_SESSION['currentPagePerpetualStock'])||$_SESSION['currentPagePerpetualStock']['page_name']!='sa.report.PerpetualStock') {
                    $_SESSION['currentPagePerpetualStock']=array('page_name'=>'sa.report.PerpetualStock','page_num'=>0,'search'=>false,'dataobat'=>array());												
                }
            }			
		}	        
	}
    public function checkKodeObat ($sender,$param) {        
        $kode_obat=$param->Value;		
        if ($kode_obat != '') {
            try {                                 
                if (!$this->DB->checkRecordIsExist('kode_obat','master_obat',$kode_obat)) {                                
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
            $kode_obat=addslashes($this->txtKodeObat->Text);
            $str = "SELECT mo.idobat,mo.no_reg,mo.kode_obat,mo.nama_obat,mo.nama_merek,harga,idsatuan_obat,idgolongan,mo.idbentuk_sediaan,mo.nama_bentuk,mo.farmakologi,mo.kemasan,mo.komposisi,mo.idprodusen,p.nama_produsen,mo.stock,mo.min_stock,mo.max_stock,mo.status_obat,mo.date_added,mo.date_modified,mo.enabled FROM master_obat mo LEFT JOIN produsen p  ON (mo.idprodusen=p.idprodusen) WHERE kode_obat='$kode_obat'";
            $this->DB->setFieldTable(array('idobat','no_reg','kode_obat','nama_obat','nama_merek','harga','idsatuan_obat','idgolongan','idbentuk_sediaan','nama_bentuk','farmakologi','kemasan','komposisi','idprodusen','nama_produsen','stock','min_stock','max_stock','status_obat','date_added','date_modified','enabled'));
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
        $idobat=$dataobat['idobat'];
        $str = "SELECT ks.idobat,ks.tanggal,ks.qty,dsb.idsatuan_obat AS pembelian_idsatuan,dsb.harga AS pembelian_harga,dsb.harga*ks.qty AS pembelian_jumlah,dsk.idsatuan_obat AS pengeluaran_idsatuan,dsk.harga AS pengeluaran_harga,dsk.harga*ks.qty AS pengeluaran_jumlah,ks.sisa_stock,ks.mode,ks.keterangan FROM log_ks ks LEFT JOIN detail_sbbm dsb ON (ks.iddetail_sbbm=dsb.iddetail_sbbm) LEFT JOIN detail_sbbk dsk ON (ks.iddetail_sbbk=dsk.iddetail_sbbk) WHERE ks.idobat=$idobat AND ks.tahun=$tahun ORDER BY ks.tanggal ASC";
        $this->DB->setFieldTable(array('idobat','tanggal','qty','pembelian_idsatuan','pembelian_harga','pembelian_jumlah','pengeluaran_idsatuan','pengeluaran_harga','pengeluaran_jumlah','sisa_stock','mode','keterangan'));
        $r=$this->DB->getRecord($str);          
        $data=array();
        
        while (list($k,$v)=each($r)) {
            if ($v['mode']=='masuk') {
                $v['nama_satuan']=$this->DMaster->getNamaSatuanObat($v['pembelian_idsatuan']);
                $v['uraian']=$v['keterangan'];                                
                $v['harga']=$v['pembelian_harga'];
                $v['pembelian_qty']=$v['qty'];
                $v['saldo_jumlah']=$v['harga']*$v['pembelian_qty'];                 
                $v['pengeluaran_qty']='-';                        
                $v['pengeluaran_jumlah']='-';
            }else {
                $v['nama_satuan']=$this->DMaster->getNamaSatuanObat($v['pengeluaran_idsatuan']);
                $v['uraian']=$v['keterangan'];
                $v['harga']=$v['pengeluaran_harga'];
                $v['pengeluaran_qty']=$v['qty'];
                $v['saldo_jumlah']=$v['harga']*$v['pengeluaran_qty'];                                 
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
        $dataReport['idobat']=$dataobat['idobat'];
        $dataReport['nama_obat']=$dataobat['nama_obat'];
        $dataReport['tahun']=$_SESSION['ta'];
        $dataReport['linkoutput']=$this->linkOutput; 
        $this->report->setDataReport($dataReport);        
        $this->report->printPerpetualStock ();          
        $this->lblPrintout->Text="Laporan Perpetual Stock Tahun $tahun";
        $this->modalPrintOut->show();
    }
    public function closePerpetualStock ($sender,$param) {
        unset($_SESSION['currentPagePerpetualStock']['dataobat']);        
        $this->redirect('report.PerpetualStock',true);
    }
    
}
?>