<?php
prado::using ('Application.MainPageAD');
class MutasiObatBulanan extends MainPageAD {     
	public function onLoad($param) {		
		parent::onLoad($param);		
        $this->showSubMenuReportMutasiObat=true;
		$this->showReportMutasiObatBulanan=true;              
        $this->createObj('DMaster');
        $this->createObj('Obat');        
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageMutasiObatBulanan'])||$_SESSION['currentPageMutasiObatBulanan']['page_name']!='ad.report.MutasiObatBulanan') {
                $_SESSION['currentPageMutasiObatBulanan']=array('page_name'=>'ad.report.MutasiObatBulanan','page_num'=>0,'search'=>false,'bulan'=>date($_SESSION['ta'].'-m-01'));												
            }  
            $this->lblBulanTahun->Text=$this->TGL->tanggal('F Y',$_SESSION['currentPageMutasiObatBulanan']['bulan']);
            $this->cmbFilterBulan->Text=$this->TGL->tanggal('m',$_SESSION['currentPageMutasiObatBulanan']['bulan']);
            $this->populateData();            
            
		}
	}
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageMutasiObatBulanan']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageMutasiObatBulanan']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageMutasiObatBulanan']['search']=true;
        $this->populateData($_SESSION['currentPageMutasiObatBulanan']['search']);
	}
    public function filterRecord ($sender,$param) {        
		$_SESSION['currentPageMutasiObatBulanan']['bulan']=$_SESSION['ta'].'-'.$this->cmbFilterBulan->Text.'-01';
        $this->lblBulanTahun->Text=$this->TGL->tanggal('F Y',$_SESSION['currentPageMutasiObatBulanan']['bulan']);
        $this->populateData();
	}
    public function itemCreated($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {            
            
        }
    }
    public function populateData ($search=false) {
        $idpuskesmas=$this->idpuskesmas;
        $bulantahun=$this->TGL->tanggal('Y-m',$_SESSION['currentPageMutasiObatBulanan']['bulan']);
        $bulan_sebelumnya=$this->TGL->getMonthAndYearBefore ($_SESSION['currentPageMutasiObatBulanan']['bulan']);        
        $str = "SELECT mo.idobat_puskesmas,dsb.iddetail_sbbm_puskesmas,mo.kode_obat AS mst_kode_obat,dsb.kode_obat,mo.nama_obat AS mst_nama_obat,dsb.nama_obat,mo.idsatuan_obat AS mst_idsatuan_obat,dsb.idsatuan_obat,mo.harga AS mst_harga,dsb.harga FROM master_obat_puskesmas mo LEFT JOIN detail_sbbm_puskesmas dsb ON (dsb.idobat_puskesmas=mo.idobat_puskesmas) WHERE mo.idpuskesmas=$idpuskesmas";        
        if ($search) {
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa=" AND mo.kode_obat='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("(SELECT mo.idobat_puskesmas FROM master_obat mo LEFT JOIN detail_sbbm_puskesmas dsb ON (dsb.idobat_puskesmas=mo.idobat_puskesmas) WHERE mo.idpuskesmas=$idpuskesmas $cluasa GROUP BY mo.idobat,dsb.harga) AS temp",'idobat_puskesmas');		
                    $str = "$str $cluasa";
                break;
                case 'nama' :
                    $cluasa=" AND mo.nama_obat LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("(SELECT mo.idobat_puskesmas FROM master_obat mo LEFT JOIN detail_sbbm_puskesmas dsb ON (dsb.idobat_puskesmas=mo.idobat_puskesmas) WHERE mo.idpuskesmas=$idpuskesmas $cluasa GROUP BY mo.idobat,dsb.harga) AS temp",'idobat_puskesmas');		
                    $str = "$str $cluasa";
                break;
            }
        }else{
            $jumlah_baris=$this->DB->getCountRowsOfTable ("(SELECT mo.idobat_puskesmas FROM master_obat_puskesmas mo LEFT JOIN detail_sbbm_puskesmas dsb ON (dsb.idobat_puskesmas=mo.idobat_puskesmas) WHERE mo.idpuskesmas=$idpuskesmas GROUP BY mo.idobat,dsb.harga) AS temp",'idobat_puskesmas');		
        }
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageMutasiObatBulanan']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=30;$_SESSION['currentPageMutasiObatBulanan']['page_num']=0;}
        $str = "$str GROUP BY mo.idobat_puskesmas,dsb.harga ORDER BY ISNULL(dsb.tanggal_expire),dsb.tanggal_expire ASC,mo.nama_obat ASC LIMIT $offset,$limit";        
        $this->DB->setFieldTable(array('idobat_puskesmas','mst_kode_obat','kode_obat','mst_nama_obat','nama_obat','mst_idsatuan_obat','idsatuan_obat','mst_harga','harga'));
		$r=$this->DB->getRecord($str,$offset+1);          
        $data=array();        
        while (list($k,$v)=each($r)) {
            $idobat_puskesmas=$v['idobat_puskesmas'];            
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
                $stock_awal=$this->Obat->getFirstStock($idobat_puskesmas,$bulan_sebelumnya,$harga,'defaultpuskesmas');
                $v['stock_awal']=$stock_awal;                
                $v['harga_stock_awal']=$this->Obat->toRupiah($stock_awal*$harga);                
                $penerimaan=$this->DB->getSumRowsOfTable('qty',"master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsb WHERE msb.idsbbm_puskesmas=dsb.idsbbm_puskesmas AND dsb.idobat_puskesmas=$idobat_puskesmas AND harga=$harga AND DATE_FORMAT(msb.tanggal_sbbm_puskesmas,'%Y-%m')='$bulantahun'");
                $v['penerimaan']=$penerimaan;
                $v['harga_penerimaan']=$this->Obat->toRupiah($penerimaan*$harga);                
                $pengeluaran=$this->DB->getSumRowsOfTable('pemberian_puskesmas',"master_sbbk_puskesmas msk,detail_sbbk_puskesmas dsk WHERE msk.idsbbk_puskesmas=dsk.idsbbk_puskesmas AND dsk.idobat_puskesmas=$idobat_puskesmas AND harga=$harga AND DATE_FORMAT(msk.tanggal_sbbk_puskesmas,'%Y-%m')='$bulantahun'");
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
        $bulantahun=$this->TGL->tanggal('F Y',$_SESSION['currentPageMutasiObatBulanan']['bulan']);
        $bulan_sebelumnya=$this->TGL->getMonthAndYearBefore ($_SESSION['currentPageMutasiObatBulanan']['bulan']);        
        $dataReport['bulantahun']=$_SESSION['currentPageMutasiObatBulanan']['bulan'];
        $dataReport['bulansebelumnya']=$bulan_sebelumnya;
        $dataReport['nama_puskesmas']=$this->nama_puskesmas;
        $dataReport['nip_ka_puskesmas']=$this->setup->nipFormat($this->Pengguna->getDataUser('nip_ka'));
        $dataReport['nama_ka_puskesmas']=$this->Pengguna->getDataUser('nama_ka');
        $this->report->setMode('excel2007');        
        $dataReport['linkoutput']=$this->linkOutput; 
        $this->report->setDataReport($dataReport);        
        $this->report->printMutasiObatBulananPuskesmas ($this->idpuskesmas);          
        $this->lblPrintout->Text="Laporan Mutasi Obat Bulan $bulantahun";
        $this->modalPrintOut->show();
    }
}
