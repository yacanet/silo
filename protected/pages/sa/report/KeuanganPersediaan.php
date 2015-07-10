<?php
prado::using ('Application.MainPageSA');
class KeuanganPersediaan extends MainPageSA {        
	public function onLoad($param) {			
		parent::onLoad($param);				
        $this->showSubMenuReportStock = true;
		$this->showReportKeuanganPersediaan = true;        
        $this->createObj('DMaster');
        $this->createObj('Obat');        
		if (!$this->IsPostBack&&!$this->IsCallBack) {                                   
            if (!isset($_SESSION['currentPageKeuanganPersediaan'])||$_SESSION['currentPageKeuanganPersediaan']['page_name']!='sa.report.KeuanganPersediaan') {
                $_SESSION['currentPageKeuanganPersediaan']=array('page_name'=>'sa.report.KeuanganPersediaan','page_num'=>0,'search'=>false);												
            }   
            $this->lblTahun->Text=$_SESSION['ta'];            
            $this->populateData();
		}	        
	}   
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageKeuanganPersediaan']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageKeuanganPersediaan']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageKeuanganPersediaan']['search']=true;
        $this->populateData($_SESSION['currentPageKeuanganPersediaan']['search']);
	}
    public function populateData ($search=false) {
        $tahun=$_SESSION['ta'];        
        $str = "SELECT mo.idobat,dsb.iddetail_sbbm,mo.nama_obat AS mst_nama_obat,dsb.nama_obat,mo.idsatuan_obat AS mst_idsatuan_obat,dsb.idsatuan_obat,mo.kemasan AS mst_kemasan,dsb.kemasan,mo.harga AS mst_harga,dsb.harga FROM master_obat mo LEFT JOIN (SELECT iddetail_sbbm,idobat,nama_obat,harga,idsatuan_obat,kemasan,tanggal_expire,dsb.idprogram FROM master_sbbm msb,detail_sbbm dsb WHERE dsb.idsbbm=msb.idsbbm AND status='complete' AND msb.tahun=$tahun GROUP BY dsb.idobat,dsb.harga) dsb ON (mo.idobat=dsb.idobat)";
         if ($search) {
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa="WHERE mo.kode_obat='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("(SELECT mo.idobat FROM master_obat mo LEFT JOIN (SELECT iddetail_sbbm,idobat,nama_obat,harga,idsatuan_obat,kemasan,tanggal_expire,dsb.idprogram FROM master_sbbm msb,detail_sbbm dsb WHERE dsb.idsbbm=msb.idsbbm AND status='complete' AND msb.tahun=$tahun GROUP BY dsb.idobat,dsb.harga) dsb ON (mo.idobat=dsb.idobat) $cluasa) AS temp",'idobat');		
                    $str = "$str $cluasa";
                break;
                case 'nama' :
                    $cluasa="WHERE mo.nama_obat LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("(SELECT mo.idobat FROM master_obat mo LEFT JOIN (SELECT iddetail_sbbm,idobat,nama_obat,harga,idsatuan_obat,kemasan,tanggal_expire,dsb.idprogram FROM master_sbbm msb,detail_sbbm dsb WHERE dsb.idsbbm=msb.idsbbm AND status='complete' AND msb.tahun=$tahun GROUP BY dsb.idobat,dsb.harga) dsb ON (mo.idobat=dsb.idobat) $cluasa) AS temp",'idobat');		
                    $str = "$str $cluasa";
                break;
            }
        }else{
            $jumlah_baris=$this->DB->getCountRowsOfTable ("(SELECT mo.idobat FROM master_obat mo LEFT JOIN (SELECT iddetail_sbbm,idobat,nama_obat,harga,idsatuan_obat,kemasan,tanggal_expire,dsb.idprogram FROM master_sbbm msb,detail_sbbm dsb WHERE dsb.idsbbm=msb.idsbbm AND status='complete' AND msb.tahun=$tahun GROUP BY dsb.idobat,dsb.harga) dsb ON (mo.idobat=dsb.idobat)) AS temp",'idobat');		
        }
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageKeuanganPersediaan']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=30;$_SESSION['currentPageKeuanganPersediaan']['page_num']=0;}
        $str = "$str ORDER BY mo.nama_obat ASC LIMIT $offset,$limit";        
        $this->DB->setFieldTable(array('idobat','iddetail_sbbm','mst_nama_obat','nama_obat','mst_idsatuan_obat','idsatuan_obat','mst_kemasan','kemasan','mst_harga','harga'));
        $r=$this->DB->getRecord($str);
        $data=array();        
        while (list($k,$v)=each($r)) {
            $iddetail_sbbm=$v['iddetail_sbbm'];
            $keluar=0;
            $total=0;
            if ($iddetail_sbbm=='') {
                $v['nama_obat']=$v['mst_nama_obat'];
                $v['nama_satuan']=$this->DMaster->getNamaSatuanObat($v['mst_idsatuan_obat']);
                $v['kemasan']=$v['mst_kemasan'];
                $harga=$v['mst_harga'];
                $v['harga']=$this->Obat->toRupiah($harga);                
            }else {                
                $v['nama_satuan']=$this->DMaster->getNamaSatuanObat($v['idsatuan_obat']);                                
                $harga=$v['harga'];
                $v['harga']=$this->Obat->toRupiah($harga);
                
                $idobat=$v['idobat'];
                $str = "SELECT mode,COUNT(idkartu_stock) AS jumlah_qty FROM kartu_stock ks,detail_sbbm dsb WHERE ks.iddetail_sbbm=dsb.iddetail_sbbm AND dsb.idobat=$idobat AND dsb.harga=$harga GROUP BY mode";                
                $this->DB->setFieldTable(array('mode','jumlah_qty'));
                $m=$this->DB->getRecord($str);                
                foreach ($m as $y=>$z) {                    
                    if ($z['mode'] == 'keluar') {                        
                        $keluar+=$z['jumlah_qty'];
                    }
                    $total+=$z['jumlah_qty'];
                }                                
            }            
            $sisa_stock=$total-$keluar;
            $v['sisa_stock']=$sisa_stock;
            $v['sub_total']=$this->Obat->toRupiah($sisa_stock*$harga);
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
        $dataReport['nip_ka_gudang']=$this->setup->nipFormat($this->setup->getSettingValue('nip_ka_gudang'));
        $dataReport['nama_ka_gudang']=$this->setup->getSettingValue('nama_ka_gudang');
        $this->report->setMode('excel2007');        
        $dataReport['linkoutput']=$this->linkOutput; 
        $this->report->setDataReport($dataReport);        
        $this->report->printKeuanganPersediaan();          
        $this->lblPrintout->Text="Laporan Keuangan Persediaan Obat Tahun $tahun";
        $this->modalPrintOut->show();
    }
}
?>