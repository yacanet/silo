<?php
prado::using ('Application.MainPageSA');
class StockObatPuskesmas extends MainPageSA { 
    public static $totalQTY=0;
    public static $totalHARGA=0;
    public $datapuskesmas;
	public function onLoad($param) {		
		parent::onLoad($param);		
        $this->showSubMenuReportStock = true;
		$this->showReportStockObatPuskesmas=true;      
        $this->createObj('DMaster');
        $this->createObj('Obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {
            if (isset($_SESSION['currentPageStockObatPuskesmas']['datapuskesmas']['idpuskesmas'])) {
                $this->idProcess='view';
                $this->detailProcess();
            }else{
                if (!isset($_SESSION['currentPageStockObatPuskesmas'])||$_SESSION['currentPageStockObatPuskesmas']['page_name']!='sa.report.StockObatPuskesmas') {
                    $_SESSION['currentPageStockObatPuskesmas']=array('page_name'=>'sa.report.StockObatPuskesmas','page_num'=>0,'search'=>false,'bulan'=>date($_SESSION['ta'].'-m-01'),'datapuskesmas'=>array());												
                }   
                $_SESSION['currentPageStockObatPuskesmas']['search']=false;     
                $this->lblBulanTahun->Text=$this->TGL->tanggal('F Y',$_SESSION['currentPageStockObatPuskesmas']['bulan']);
                $this->cmbFilterBulan->Text=$this->TGL->tanggal('m',$_SESSION['currentPageStockObatPuskesmas']['bulan']);
                $this->populateData ();	
            }
		}
	}   
    public function filterRecord ($sender,$param) {        
        if ($sender->getId()=='btnFilterDetail') {
            $this->idProcess='view';
            $_SESSION['currentPageStockObatPuskesmas']['bulan']=$_SESSION['ta'].'-'.$this->cmbDetailFilterBulan->Text.'-01';
            $this->lblDetailBulanTahun->Text=$this->TGL->tanggal('F Y',$_SESSION['currentPageStockObatPuskesmas']['bulan']);
            $this->populateStockObatPuskesmas();
        }else{
            $_SESSION['currentPageStockObatPuskesmas']['bulan']=$_SESSION['ta'].'-'.$this->cmbFilterBulan->Text.'-01';
            $this->lblBulanTahun->Text=$this->TGL->tanggal('F Y',$_SESSION['currentPageStockObatPuskesmas']['bulan']);
            $this->populateData();
        }
	}
    public function itemCreated($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {                        
            StockObatPuskesmas::$totalQTY += $item->DataItem['sisa_obat'];                        
            StockObatPuskesmas::$totalHARGA += $item->DataItem['jumlah_rupiah'];            
        }
    }
    public function populateData ($search=false) {
        $listpuskesmas=$this->DMaster->getListPuskesmas ();        
        $bulan_sekarang=$this->TGL->tanggal('Y-m',$_SESSION['currentPageStockObatPuskesmas']['bulan']);        
        $datapuskesmas=array();
        $i=1;
        foreach($listpuskesmas as $k=>$v) {
            if ($k!='none') {                                
                $str = "SELECT COUNT(ks.idkartu_stock) AS jumlah_qty FROM kartu_stock ks, master_sbbk msk,detail_sbbk dsb WHERE ks.idsbbk=msk.idsbbk AND ks.iddetail_sbbk=dsb.iddetail_sbbk AND msk.idpuskesmas=$k AND msk.status='complete' AND DATE_FORMAT(msk.tanggal_sbbk,'%Y-%m')<='$bulan_sekarang'";                
                $this->DB->setFieldTable(array('jumlah_qty'));
                $r=$this->DB->getRecord($str);                
                $distribusi_obat=$r[1]['jumlah_qty']>0 ? $r[1]['jumlah_qty']:0;                
                
                $str = "SELECT mode_puskesmas,COUNT(idkartu_stock_puskesmas) AS jumlah_qty FROM kartu_stock_puskesmas_dinas WHERE idpuskesmas=$k AND DATE_FORMAT(date_added,'%Y-%m')<='$bulan_sekarang' GROUP BY mode_puskesmas ORDER BY mode_puskesmas ASC";
                $this->DB->setFieldTable(array('mode_puskesmas','jumlah_qty'));
                $r=$this->DB->getRecord($str);            
                $total_penerimaan=0;
                $total_pemakaian=0;
                foreach ($r as $m) {
                    if ($m['mode_puskesmas'] == 'keluar') {
                        $total_pemakaian+=$m['jumlah_qty'];
                    }
                    $total_penerimaan+=$m['jumlah_qty'];
                }
                $sisa_obat = $total_penerimaan - $total_pemakaian;                                             
                $jumlah_rupiah=$this->DB->getSumRowsOfTable('harga',"kartu_stock_puskesmas_dinas kspd,detail_sbbm_puskesmas dspp WHERE kspd.iddetail_sbbm_puskesmas=dspp.iddetail_sbbm_puskesmas AND kspd.mode_puskesmas='masuk' AND kspd.idpuskesmas=$k AND DATE_FORMAT(kspd.date_added,'%Y-%m')<='$bulan_sekarang'");
                
                $datapuskesmas[$i]=array('idpuskesmas'=>$k,'nama_puskesmas'=>$v,'distribusi_obat'=>$distribusi_obat,'total_penerimaan'=>$total_penerimaan,'total_pemakaian'=>$total_pemakaian,'sisa_obat'=>$sisa_obat,'jumlah_rupiah'=>$jumlah_rupiah);            
                $i+=1;
            }
        }
		$this->RepeaterS->DataSource=$datapuskesmas;
		$this->RepeaterS->dataBind();             
	} 
    
    public function viewRecord ($sender,$param) {        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);        		
        
//        $bulan_sekarang=$this->TGL->tanggal('Y-m',$_SESSION['currentPageStockObatPuskesmas']['bulan']);
//        $tanggal_sebelumnya=$this->TGL->getMonthAndYearBefore ($_SESSION['currentPageStockObatPuskesmas']['bulan']);        
//        $tanggal_sebelumnya = explode('-',$tanggal_sebelumnya);
//        $tahun_sebelumnya=$tanggal_sebelumnya[0];
//        $bulan_sebelumnya=$tanggal_sebelumnya[1];
//                
//        $str = "SELECT sisa_stock FROM log_ks_puskesmas ksp,(SELECT idobat_puskesmas,MAX(idlog) AS idlog FROM log_ks_puskesmas WHERE bulan='$bulan_sebelumnya' AND tahun='$tahun_sebelumnya' AND idpuskesmas=$id GROUP BY idobat_puskesmas) AS ksp2 WHERE ksp.idlog=ksp2.idlog AND ksp.idobat_puskesmas=ksp2.idobat_puskesmas AND idpuskesmas=$id";
//        $this->DB->setFieldTable(array('sisa_stock'));
//        $r=$this->DB->getRecord($str);
//        $awal_stock=isset($r[1]) ? $r[1]['sisa_stock']:0;
//
//        $str = "SELECT SUM(dsb.pemberian) AS jumlah FROM master_lpo mlp,master_sbbk msk,detail_sbbk dsb WHERE mlp.idlpo=msk.idlpo AND dsb.idsbbk=msk.idsbbk AND msk.idpuskesmas=$id AND msk.response_sbbk=5 AND mlp.response_lpo=5 AND DATE_FORMAT(msk.tanggal_diterima,'%Y-%m')='$bulan_sekarang'";
//        $this->DB->setFieldTable(array('jumlah'));
//        $r=$this->DB->getRecord($str);
//        $penerimaan=$r[1]['jumlah']>0 ? $r[1]['jumlah']:0;
//
//        $persediaan=$awal_stock+$penerimaan;                
//
//        $str = "SELECT SUM(dsb.pemberian) AS jumlah FROM master_sbbk_puskesmas msk,detail_sbbk_puskesmas dsb WHERE dsb.idsbbk=msk.idsbbk AND msk.idpuskesmas=$id AND msk.response_sbbk=5 AND DATE_FORMAT(msk.tanggal_diterima,'%Y-%m')='$bulan_sekarang'";
//        $this->DB->setFieldTable(array('jumlah'));
//        $r=$this->DB->getRecord($str);
//        $pengeluaran=$r[1]['jumlah']>0 ? $r[1]['jumlah']:0;

//        $sisa_stock=$persediaan-$pengeluaran;
        $nama_puskesmas=$this->DMaster->getNamaPuskesmasByID($id);
        $nama_ka=$this->DMaster->getNamaKAPuskesmasByID($id);        
        $datapuskesmas=array('idpuskesmas'=>$id,'nama_puskesmas'=>$nama_puskesmas,'nip_ka'=>$nama_ka['nip_ka'],'nama_ka'=>$nama_ka['nama_ka'],'awal_stock'=>$awal_stock,'penerimaan'=>$penerimaan,'persediaan'=>$persediaan,'pengeluaran'=>$pengeluaran,'sisa_stock'=>$sisa_stock);            
        
        $_SESSION['currentPageStockObatPuskesmas']['datapuskesmas']=$datapuskesmas;
        $this->redirect('report.StockObatPuskesmas',true);
    }
    private function detailProcess() {
        $this->datapuskesmas=$_SESSION['currentPageStockObatPuskesmas']['datapuskesmas'];
        $this->lblDetailBulanTahun->Text=$this->TGL->tanggal('F Y',$_SESSION['currentPageStockObatPuskesmas']['bulan']);
        $this->cmbDetailFilterBulan->Text=$this->TGL->tanggal('m',$_SESSION['currentPageStockObatPuskesmas']['bulan']);        
        $this->populateStockObatPuskesmas ();
    }   
    public function populateStockObatPuskesmas () {
        $idpuskesmas=$_SESSION['currentPageStockObatPuskesmas']['datapuskesmas']['idpuskesmas'];                
        $bulan_sekarang=$this->TGL->tanggal('Y-m',$_SESSION['currentPageStockObatPuskesmas']['bulan']);
        $str = "SELECT dsb.iddetail_sbbk,mop.idobat_puskesmas,mop.nama_obat,mop.idsatuan_obat AS mst_idsatuan_obat,dsb.idsatuan_obat,mop.harga AS mst_harga,dsb.harga FROM master_obat_puskesmas mop LEFT JOIN (SELECT iddetail_sbbk,idobat_puskesmas,idsatuan_obat,harga FROM master_sbbk msk,detail_sbbk dsb1 WHERE msk.idsbbk=dsb1.idsbbk AND msk.status='complete' AND DATE_FORMAT(msk.tanggal_sbbk,'%Y-%m')='$bulan_sekarang') AS dsb ON (mop.idobat_puskesmas=dsb.idobat_puskesmas) WHERE mop.idpuskesmas=$idpuskesmas GROUP BY mop.idobat_puskesmas,dsb.harga ORDER BY mop.nama_obat ASC";
        $this->DB->setFieldTable(array('iddetail_sbbk','idobat_puskesmas','nama_obat','mst_idsatuan_obat','idsatuan_obat','mst_harga','harga'));
		$r=$this->DB->getRecord($str);                         
        $data=array();
        while (list($k,$v)=each($r)) {                       
            $iddetail_sbbk=$v['iddetail_sbbk'];
            if ($iddetail_sbbk=='') {
                $v['nama_satuan']=$this->DMaster->getNamaSatuanObat($v['mst_idsatuan_obat']);
                $v['harga']=$v['mst_harga'];
                $v['pengeluaran']=0;                
                $v['jumlah_rupiah']=0;
            }else {                
                $v['nama_satuan']=$this->DMaster->getNamaSatuanObat($v['idsatuan_obat']);                
                $str = "SELECT COUNT(ks.idkartu_stock) AS jumlah_qty,SUM(dsb.harga) AS jumlah_rupiah FROM detail_sbbk dsb,kartu_stock ks WHERE ks.iddetail_sbbk=dsb.iddetail_sbbk AND ks.iddetail_sbbk=$iddetail_sbbk";                
                $this->DB->setFieldTable(array('jumlah_qty','jumlah_rupiah'));
                $r1=$this->DB->getRecord($str);
                $v['pengeluaran']=$r1[1]['jumlah_qty']>0 ? $r1[1]['jumlah_qty']:0;                
                $v['jumlah_rupiah']=$r1[1]['jumlah_rupiah']>0 ? $r1[1]['jumlah_rupiah']:0;
            }
            $data[$k]=$v;
        }
        $this->RepeaterStockObatPuskesmas->DataSource=$data;
        $this->RepeaterStockObatPuskesmas->dataBind();
    }
    public function printOut ($sender,$param) {        
        $this->createObj('report');     
        $dataReport=$_SESSION['currentPageStockObatPuskesmas']['datapuskesmas'];
        $bulantahun=$this->TGL->tanggal('F Y',$_SESSION['currentPageStockObatPuskesmas']['bulan']);
        $bulan_sebelumnya=$this->TGL->getMonthAndYearBefore ($_SESSION['currentPageStockObatPuskesmas']['bulan']);        
        $dataReport['bulantahun']=$_SESSION['currentPageStockObatPuskesmas']['bulan'];
        $dataReport['bulansebelumnya']=$bulan_sebelumnya;
        $dataReport['nip_ka_gudang']=$this->setup->nipFormat($this->setup->getSettingValue('nip_ka_gudang'));
        $dataReport['nama_ka_gudang']=$this->setup->getSettingValue('nama_ka_gudang');
        $this->report->setMode('excel2007');        
        $dataReport['linkoutput']=$this->linkOutput; 
        $this->report->setDataReport($dataReport);  
        if ($sender->getId()=='btnPrintOutDetailStockObatPuskesmas') {
            $this->report->printDetailStockObatPuskesmas ();          
            $this->lblPrintout->Text="Laporan Stock Obat Puskesmas s.d Bulan $bulantahun";
        }else{
            $this->report->printRekapitulasiStockObatPuskesmas ();          
            $this->lblPrintout->Text="Laporan Stock Obat Puskesmas s.d Bulan $bulantahun";
        }        
        $this->modalPrintOut->show();
    }
    public function closeStockObatPuskesmas ($sender,$param) {
        unset($_SESSION['currentPageStockObatPuskesmas']['datapuskesmas']);        
        $this->redirect('report.StockObatPuskesmas',true);
    }
}
?>