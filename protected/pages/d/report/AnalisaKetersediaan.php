<?php
prado::using ('Application.MainPageSA');
class AnalisaKetersediaan extends MainPageSA {     
	public function onLoad($param) {		
		parent::onLoad($param);		        
		$this->showReportAnalisaKetersediaan=true;              
        $this->createObj('DMaster');
        $this->createObj('Obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageAnalisaKetersediaan'])||$_SESSION['currentPageAnalisaKetersediaan']['page_name']!='sa.report.AnalisaKetersediaan') {
                $_SESSION['currentPageAnalisaKetersediaan']=array('page_name'=>'sa.report.AnalisaKetersediaan','page_num'=>0,'search'=>false);												
            }  
            $this->lblTahun->Text=$_SESSION['ta'];            
            $this->populateData();            
            
		}
	}
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageAnalisaKetersediaan']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageAnalisaKetersediaan']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageAnalisaKetersediaan']['search']=true;
        $this->populateData($_SESSION['currentPageAnalisaKetersediaan']['search']);
	}
    public function populateData ($search=false) {
        $tahun=$_SESSION['ta'];
        $tahun_sebelumnya=$_SESSION['ta']-1;        
        $str = 'SELECT mo.idobat,mo.nama_obat,mo.idsatuan_obat,mo.kemasan FROM master_obat mo';        
        if ($search) {
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa="WHERE mo.kode_obat='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_obat mo $cluasa",'idobat');		
                    $str = "$str $cluasa";
                break;
                case 'nama' :
                    $cluasa="WHERE mo.nama_obat LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_obat mo $cluasa",'idobat');		
                    $str = "$str $cluasa";
                break;
            }
        }else{
            $jumlah_baris=$this->DB->getCountRowsOfTable ('master_obat','idobat');		
        }
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageAnalisaKetersediaan']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=30;$_SESSION['currentPageAnalisaKetersediaan']['page_num']=0;}
        $str = "$str ORDER BY mo.nama_obat ASC LIMIT $offset,$limit";        
        $this->DB->setFieldTable(array('idobat','nama_obat','idsatuan_obat','kemasan'));
		$r=$this->DB->getRecord($str,$offset+1);          
        $data=array();        
        $tahun_lalu=$_SESSION['ta']-1;        
        while (list($k,$v)=each($r)) {
            $idobat=$v['idobat'];                        
            $v['nama_satuan']=$this->DMaster->getNamaSatuanObat($v['idsatuan_obat']);   
            
            $str = "SELECT sisa_stock FROM log_ks ksp,(SELECT MAX(idlog) AS idlog FROM log_ks WHERE tahun=$tahun_lalu AND idobat=$idobat) AS ksp2 WHERE ksp.idlog=ksp2.idlog";            
            $this->DB->setFieldTable(array('sisa_stock'));
            $r1=$this->DB->getRecord($str);        
            $sisa_stock_gf=isset($r1[1]) ? $r1[1]['sisa_stock'] : 0;
            
            $str = "SELECT sisa_stock FROM log_ks_puskesmas ksp,(SELECT MAX(idlog) AS idlog FROM log_ks_puskesmas WHERE tahun=$tahun_lalu AND idobat=$idobat) AS ksp2 WHERE ksp.idlog=ksp2.idlog";                                    
            $r1=$this->DB->getRecord($str);                    
            $sisa_stock_pm=isset($r1[1]) ? $r1[1]['sisa_stock'] : 0;            
            
            $v['sisa_stock_tahun_lalu']=$sisa_stock_gf+$sisa_stock_pm;
            
            $str = "SELECT SUM(pemberian)/12 AS rata2 FROM master_sbbk msb, detail_sbbk dsb WHERE msb.idsbbk=dsb.idsbbk AND msb.status='complete' AND  msb.tahun=$tahun_lalu AND dsb.idobat=$idobat";
            $this->DB->setFieldTable(array('rata2'));
            $r2=$this->DB->getRecord($str);                    
            $rata2_gf=$r2[1]['rata2'] > 0 ? number_format($r2[1]['rata2'],2) : 0;
            
            $str = "SELECT SUM(pemberian)/12 AS rata2 FROM master_sbbk_puskesmas msb, detail_sbbk_puskesmas dsb WHERE msb.idsbbk=dsb.idsbbk AND msb.status='complete' AND  msb.tahun=$tahun_lalu AND dsb.idobat=$idobat";            
            $r2=$this->DB->getRecord($str);                    
            $rata2_pm=$r2[1]['rata2'] > 0 ? number_format($r2[1]['rata2'],2) : 0;
            
            $v['rata2_tahun_lalu']=$rata2_gf+$rata2_pm;
            $v['ketersediaan_tahun_lalu']=number_format($v['sisa_stock_tahun_lalu']/$v['rata2_tahun_lalu'],2);
            $v['kebutuhan_tahun_selanjutnya']=$v['rata2_tahun_lalu']*18;
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
        $this->report->printAnalisaKetersediaanObat ();          
        $this->lblPrintout->Text="Laporan Analisa Ketersediaan Obat Tahun $tahun";
        $this->modalPrintOut->show();
    }
}
