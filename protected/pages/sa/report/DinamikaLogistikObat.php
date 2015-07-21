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
        $str = "SELECT mo.idobat,mo.nama_obat,mo.idsatuan_obat,mo.kemasan FROM master_obat mo";
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
        $this->DB->setFieldTable(array('idobat','nama_obat','idsatuan_obat','kemasan'));
        $r=$this->DB->getRecord($str);
        $data=array();        
        while (list($k,$v)=each($r)) {
            $idobat=$v['idobat'];
            $penerimaan=$this->DB->getSumRowsOfTable('qty',"master_sbbm msb,detail_sbbm dsb WHERE msb.idsbbm=dsb.idsbbm AND dsb.idobat=$idobat AND msb.status='complete' AND DATE_FORMAT(msb.tanggal_sbbm,'%Y')<='$tahun_sebelumnya'");            
            $pengeluaran=$this->DB->getSumRowsOfTable('pemberian',"master_sbbk msk,detail_sbbk dsk WHERE msk.idsbbk=dsk.idsbbk AND dsk.idobat=$idobat AND msk.status='complete' AND DATE_FORMAT(msk.tanggal_sbbk,'%Y')<='$tahun_sebelumnya'");            
            $stock_awal=($pengeluaran < $penerimaan) ? $penerimaan - $pengeluaran:0;            
            $v['stock_awal']=$stock_awal;                
            $penerimaan2=$this->DB->getSumRowsOfTable('qty',"master_sbbm msb,detail_sbbm dsb WHERE msb.idsbbm=dsb.idsbbm AND dsb.idobat=$idobat AND msb.status='complete' AND DATE_FORMAT(msb.tanggal_sbbm,'%Y')='$tahun'");
            $v['penerimaan']=$penerimaan2;
            $pengeluaran2=$this->DB->getSumRowsOfTable('pemberian',"master_sbbk msk,detail_sbbk dsk WHERE msk.idsbbk=dsk.idsbbk AND dsk.idobat=$idobat AND msk.status='complete' AND DATE_FORMAT(msk.tanggal_sbbk,'%Y')='$tahun'");
            $v['pengeluaran']=$pengeluaran2;
            $stock_akhir=($stock_awal+$penerimaan2)-$pengeluaran2;
            $v['stock_akhir']=$stock_akhir;
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