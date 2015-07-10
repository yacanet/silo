<?php
prado::using ('Application.MainPageSA');
class DinamikaLogistikObat extends MainPageSA {        
	public function onLoad($param) {			
		parent::onLoad($param);				
        $this->showSubMenuReportStock = true;
		$this->showReportDinamikaLogistikObat = true;        
        $this->createObj('DMaster');
        $this->createObj('Obat');        
		if (!$this->IsPostBack&&!$this->IsCallBack) {                                   
            if (!isset($_SESSION['currentPageDinamikaLogistikObat'])||$_SESSION['currentPageDinamikaLogistikObat']['page_name']!='sa.report.DinamikaLogistikObat') {
                $_SESSION['currentPageDinamikaLogistikObat']=array('page_name'=>'sa.report.DinamikaLogistikObat','page_num'=>0,'search'=>false);												
            }   
            $this->lblTahun->Text=$_SESSION['ta'];            
            $this->populateData();
		}	        
	}   
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageDinamikaLogistikObat']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageDinamikaLogistikObat']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageDinamikaLogistikObat']['search']=true;
        $this->populateData($_SESSION['currentPageDinamikaLogistikObat']['search']);
	}    
    public function itemCreated($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {            
            
        }
    }
    public function populateData ($search=false) {
        $tahun=$_SESSION['ta'];
        $tahun_sebelumnya=$_SESSION['ta']-1;
        $str = "SELECT mo.idobat,mo.nama_obat,mo.idsatuan_obat,mo.kemasan,COALESCE(penerimaan_jumlah_tahun_lalu-pengeluaran_jumlah_tahun_lalu,0) AS penerimaan_jumlah_tahun_lalu,COALESCE(penerimaan_jumlah,0) penerimaan_jumlah,COALESCE(pengeluaran_jumlah,0) pengeluaran_jumlah FROM master_obat mo LEFT JOIN (SELECT idobat,SUM(qty) AS penerimaan_jumlah_tahun_lalu FROM master_sbbm msb,detail_sbbm dsb WHERE msb.idsbbm=dsb.idsbbm AND msb.tahun=$tahun_sebelumnya GROUP BY dsb.idobat) penerimaan_tahun_lalu ON (penerimaan_tahun_lalu.idobat=mo.idobat) LEFT JOIN (SELECT idobat,SUM(pemberian) AS pengeluaran_jumlah_tahun_lalu FROM master_sbbk msk,detail_sbbk dsk WHERE msk.idsbbk=dsk.idsbbk AND msk.tahun=$tahun_sebelumnya GROUP BY idobat) pengeluaran_tahun_lalu ON (pengeluaran_tahun_lalu.idobat=mo.idobat) LEFT JOIN (SELECT idobat,SUM(qty) AS penerimaan_jumlah FROM master_sbbm msb,detail_sbbm dsb WHERE msb.idsbbm=dsb.idsbbm AND msb.tahun=$tahun GROUP BY dsb.idobat) penerimaan ON (penerimaan.idobat=mo.idobat) LEFT JOIN (SELECT idobat,SUM(pemberian) AS pengeluaran_jumlah FROM master_sbbk msk,detail_sbbk dsk WHERE msk.idsbbk=dsk.idsbbk AND msk.tahun=$tahun GROUP BY idobat) pengeluaran ON (pengeluaran.idobat=mo.idobat)";
         if ($search) {
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa="WHERE kode_obat='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_obat $cluasa",'idobat');		
                    $str = "$str $cluasa";
                break;
                case 'nama' :
                    $cluasa="WHERE nama_obat LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_obat $cluasa",'idobat');		
                    $str = "$str $cluasa";
                break;
            }
        }else{
            $jumlah_baris=$this->DB->getCountRowsOfTable ('master_obat','idobat');		
        }
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageDinamikaLogistikObat']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=30;$_SESSION['currentPageDinamikaLogistikObat']['page_num']=0;}
        $str = "$str ORDER BY mo.nama_obat ASC LIMIT $offset,$limit";        
        $this->DB->setFieldTable(array('idobat','nama_obat','idsatuan_obat','kemasan','penerimaan_jumlah_tahun_lalu','penerimaan_jumlah','pengeluaran_jumlah'));
        $r=$this->DB->getRecord($str);
        $data=array();        
        while (list($k,$v)=each($r)) {
            $v['sisa_stock']=($v['penerimaan_jumlah_tahun_lalu']+$v['penerimaan_jumlah'])-$v['pengeluaran_jumlah'];
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
        $this->report->printDinamikaLogistikObat();          
        $this->lblPrintout->Text="Laporan Dinamika Logistik Obat Tahun $tahun";
        $this->modalPrintOut->show();
    }
}
?>