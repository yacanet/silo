<?php
prado::using ('Application.MainPageSA');
class MutasiObatSemester extends MainPageSA {     
	public function onLoad($param) {		
		parent::onLoad($param);		
        $this->showSubMenuReportMutasiObat=true;
		$this->showReportMutasiObatSemester=true;              
        $this->createObj('DMaster');
        $this->createObj('Obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageMutasiObatSemester'])||$_SESSION['currentPageMutasiObatSemester']['page_name']!='sa.report.MutasiObatSemester') {
                $_SESSION['currentPageMutasiObatSemester']=array('page_name'=>'sa.report.MutasiObatSemester','page_num'=>0,'search'=>false,'semester'=>1);												
            }  
            $this->lblSemester->Text=$_SESSION['currentPageMutasiObatSemester']['semester'] == 1 ? 'I (Januari - Juni)' : 'II (Juli - Desember)';
            $this->cmbFilterSemester->Text=$_SESSION['currentPageMutasiObatSemester']['semester'];
            $this->lblTahun->Text=$_SESSION['ta'];
            $this->populateData();            
            
		}
	}
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageMutasiObatSemester']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageMutasiObatSemester']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageMutasiObatSemester']['search']=true;
        $this->populateData($_SESSION['currentPageMutasiObatSemester']['search']);
	}
    public function filterRecord ($sender,$param) {        
		$_SESSION['currentPageMutasiObatSemester']['semester']=$this->cmbFilterSemester->Text;
        $this->lblSemester->Text=$_SESSION['currentPageMutasiObatSemester']['semester'] == 1 ? 'I (Januari - Juni)' : 'II (Juli - Desember)';
        $this->populateData();
	}    
    public function populateData ($search=false) {        
        $ta=$_SESSION['ta'];
        $semester=$_SESSION['currentPageMutasiObatSemester']['semester'];        
        $str_sbbm=$semester == 1 ? " msb.tanggal_sbbm >= '$ta-01-01' AND msb.tanggal_sbbm < '$ta-07-01'" :  " msb.tanggal_sbbm >= '$ta-07-01' AND  msb.tanggal_sbbm <= '$ta-12-31'";        
        $str_sbbk=$semester == 1 ? " msk.tanggal_sbbk >= '$ta-01-01' AND msk.tanggal_sbbk < '$ta-07-01'" :  " msk.tanggal_sbbk >= '$ta-07-01' AND  msk.tanggal_sbbk <= '$ta-12-31'";        
        $str = "SELECT mo.idobat,dsb.iddetail_sbbm,mo.kode_obat AS mst_kode_obat,dsb.kode_obat,mo.nama_obat AS mst_nama_obat,dsb.nama_obat,mo.idsatuan_obat AS mst_idsatuan_obat,dsb.idsatuan_obat,mo.harga AS mst_harga,dsb.harga FROM master_obat mo LEFT JOIN detail_sbbm dsb ON (dsb.idobat=mo.idobat)";        
        if ($search) {
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa="WHERE mo.kode_obat='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("(SELECT mo.idobat FROM master_obat mo LEFT JOIN detail_sbbm dsb ON (dsb.idobat=mo.idobat) $cluasa GROUP BY mo.idobat,dsb.harga) AS temp",'idobat');		
                    $str = "$str $cluasa";
                break;
                case 'nama' :
                    $cluasa="WHERE mo.nama_obat LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("(SELECT mo.idobat FROM master_obat mo LEFT JOIN detail_sbbm dsb ON (dsb.idobat=mo.idobat) $cluasa GROUP BY mo.idobat,dsb.harga) AS temp",'idobat');		
                    $str = "$str $cluasa";
                break;
            }
        }else{
            $jumlah_baris=$this->DB->getCountRowsOfTable ("(SELECT mo.idobat FROM master_obat mo LEFT JOIN detail_sbbm dsb ON (dsb.idobat=mo.idobat) GROUP BY mo.idobat,dsb.harga) AS temp",'idobat');		
        }
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageMutasiObatSemester']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=30;$_SESSION['currentPageMutasiObatSemester']['page_num']=0;}
        $str = "$str GROUP BY mo.idobat,dsb.harga ORDER BY ISNULL(dsb.tanggal_expire),dsb.tanggal_expire ASC,mo.nama_obat ASC LIMIT $offset,$limit";        
        $this->DB->setFieldTable(array('idobat','mst_kode_obat','kode_obat','mst_nama_obat','nama_obat','mst_idsatuan_obat','idsatuan_obat','mst_harga','harga'));
		$r=$this->DB->getRecord($str,$offset+1);          
        $data=array();        
        while (list($k,$v)=each($r)) {
            $idobat=$v['idobat'];            
            if ($v['kode_obat']=='') {
                $v['kode_obat']=$v['mst_kode_obat'];
                $v['nama_obat']=$v['mst_nama_obat'];
                $v['nama_satuan']=$this->DMaster->getNamaSatuanObat($v['mst_idsatuan_obat']);
                $v['harga']=$v['mst_harga'];
                $v['stock_awal']=0;
                $v['harga_stock_awal']=0;
                $v['penerimaan']=0;
                $v['harga_penerimaan']=0;
                $v['pengeluaran']=0;
                $v['harga_pengeluaran']=0;
                $v['stock_akhir']=0;
                $v['harga_stock_akhir']=0;
            }else{
                $v['nama_satuan']=$this->DMaster->getNamaSatuanObat($v['idsatuan_obat']);
                $harga=$v['harga'];                                
                $stock_awal=$this->Obat->getFirstStockSemester($idobat,$ta,$semester,$harga);
                $v['stock_awal']=$stock_awal;                
                $v['harga_stock_awal']=$this->Obat->toRupiah($stock_awal*$harga);                                
                $penerimaan=$this->DB->getSumRowsOfTable('qty',"master_sbbm msb,detail_sbbm dsb WHERE msb.idsbbm=dsb.idsbbm AND dsb.idobat=$idobat AND harga=$harga AND status='complete' AND $str_sbbm");
                $v['penerimaan']=$penerimaan;
                $v['harga_penerimaan']=$this->Obat->toRupiah($penerimaan*$harga);                
                $pengeluaran=$this->DB->getSumRowsOfTable('pemberian',"master_sbbk msk,detail_sbbk dsk WHERE msk.idsbbk=dsk.idsbbk AND dsk.idobat=$idobat AND harga=$harga AND status='complete' AND $str_sbbk");
                $v['pengeluaran']=$pengeluaran;
                $v['harga_pengeluaran']=$this->Obat->toRupiah($pengeluaran*$harga);                
                $stock_akhir=($stock_awal+$penerimaan)-$pengeluaran;
                $v['stock_akhir']=$stock_akhir;
                $v['harga_stock_akhir']=$this->Obat->toRupiah($stock_akhir*$harga);
            }
            $data[$k]=$v;
        }
        $this->RepeaterS->DataSource=$data;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
    }
    public function printOut ($sender,$param) {        
        $this->createObj('report');             
        $ta=$_SESSION['ta'];
        $dataReport['semester']=$_SESSION['currentPageMutasiObatSemester']['semester'];        
        $dataReport['tahun']=$ta;        
        $dataReport['nip_ka_gudang']=$this->setup->nipFormat($this->setup->getSettingValue('nip_ka_gudang'));
        $dataReport['nama_ka_gudang']=$this->setup->getSettingValue('nama_ka_gudang');
        $this->report->setMode('excel2007');        
        $dataReport['linkoutput']=$this->linkOutput; 
        $this->report->setDataReport($dataReport);        
        $this->report->printMutasiObatSemester ();          
        $semester=$_SESSION['currentPageMutasiObatSemester']['semester'] == 1 ? 'I (Januari - Juni)' : 'II (Juli - Des)';
        $this->lblPrintout->Text="Laporan Mutasi Obat Semester $semester Thn $ta";
        $this->modalPrintOut->show();
    }
}
