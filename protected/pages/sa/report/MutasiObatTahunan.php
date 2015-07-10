<?php
prado::using ('Application.MainPageSA');
class MutasiObatTahunan extends MainPageSA {     
	public function onLoad($param) {		
		parent::onLoad($param);		
        $this->showSubMenuReportMutasiObat=true;
		$this->showReportMutasiObatTahunan=true;              
        $this->createObj('DMaster');
        $this->createObj('Obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageMutasiObatTahunan'])||$_SESSION['currentPageMutasiObatTahunan']['page_name']!='sa.report.MutasiObatTahunan') {
                $_SESSION['currentPageMutasiObatTahunan']=array('page_name'=>'sa.report.MutasiObatTahunan','page_num'=>0,'search'=>false);												
            }  
            $this->lblTahun->Text=$_SESSION['ta'];            
            $this->populateData();            
            
		}
	}
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageMutasiObatTahunan']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageMutasiObatTahunan']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageMutasiObatTahunan']['search']=true;
        $this->populateData($_SESSION['currentPageMutasiObatTahunan']['search']);
	}    
    public function itemCreated($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {            
            
        }
    }
    public function populateData ($search=false) {
        $tahun=$_SESSION['ta'];
        $tahun_sebelumnya=$_SESSION['ta']-1;        
        $str = "SELECT mo.idobat,mo.kode_obat AS mst_kode_obat,dsb.kode_obat,mo.nama_obat AS mst_nama_obat,dsb.nama_obat,mo.idsatuan_obat AS mst_idsatuan_obat,dsb.idsatuan_obat,mo.harga AS mst_harga,dsb.harga FROM master_obat mo LEFT JOIN detail_sbbm dsb ON (dsb.idobat=mo.idobat) ";        
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
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageMutasiObatTahunan']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=30;$_SESSION['currentPageMutasiObatTahunan']['page_num']=0;}
        $str = "$str GROUP BY mo.idobat,dsb.harga ORDER BY ISNULL(dsb.tanggal_expire),dsb.tanggal_expire ASC, mo.nama_obat ASC LIMIT $offset,$limit";        
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
                $stock_awal=$this->Obat->getFirstStockTahunan($idobat,$tahun_sebelumnya,$harga);
                $v['stock_awal']=$stock_awal;                
                $v['harga_stock_awal']=$this->Obat->toRupiah($stock_awal*$harga);                
                $penerimaan=$this->DB->getSumRowsOfTable('qty',"master_sbbm msb,detail_sbbm dsb WHERE msb.idsbbm=dsb.idsbbm AND dsb.idobat=$idobat AND harga=$harga AND DATE_FORMAT(msb.tanggal_sbbm,'%Y')='$tahun'");
                $v['penerimaan']=$penerimaan;
                $v['harga_penerimaan']=$this->Obat->toRupiah($penerimaan*$harga);                
                $pengeluaran=$this->DB->getSumRowsOfTable('pemberian',"master_sbbk msk,detail_sbbk dsk WHERE msk.idsbbk=dsk.idsbbk AND dsk.idobat=$idobat AND harga=$harga AND DATE_FORMAT(msk.tanggal_sbbk,'%Y')='$tahun'");
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
        $tahun=$_SESSION['ta'];                
        $dataReport['tahun']=$tahun;
        $dataReport['tahunsebelumnya']=$tahun-1;
        $dataReport['nip_ka_gudang']=$this->setup->nipFormat($this->setup->getSettingValue('nip_ka_gudang'));
        $dataReport['nama_ka_gudang']=$this->setup->getSettingValue('nama_ka_gudang');
        $this->report->setMode('excel2007');        
        $dataReport['linkoutput']=$this->linkOutput; 
        $this->report->setDataReport($dataReport);        
        $this->report->printMutasiObatTahunan ();          
        $this->lblPrintout->Text="Laporan Mutasi Obat Tahun $tahun";
        $this->modalPrintOut->show();
    }
}
