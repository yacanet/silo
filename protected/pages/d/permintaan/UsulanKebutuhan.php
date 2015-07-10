<?php
prado::using ('Application.MainPageSA');
class UsulanKebutuhan extends MainPageSA {    
    public static $totalQTY=0;
    public static $totalHARGA=0;
    public $datalpo;
	public function onLoad($param) {		
		parent::onLoad($param);		        
		$this->showUsulanKebutuhan=true;              
        $this->createObj('Obat');
        $this->createObj('DMaster');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	        
            if (!isset($_SESSION['currentPageUsulanKebutuhan'])||$_SESSION['currentPageUsulanKebutuhan']['page_name']!='sa.permintaan.UsulanKebutuhan') {
                $_SESSION['currentPageUsulanKebutuhan']=array('page_name'=>'sa.permintaan.UsulanKebutuhan','page_num'=>0,'search'=>false,'datalpo'=>array(),'cart'=>array(),'response_lpo'=>1,'tanggal'=>date('Y-m-d'));												
            }   
            $this->labelTahun->Text=$_SESSION['ta'];
            $_SESSION['currentPageUsulanKebutuhan']['search']=false;              
            $this->populateData ();	            
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageUsulanKebutuhan']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageUsulanKebutuhan']['search']);
	}   
    public function populateData ($search=false) {                                
        if ($search) {                  
            $str = "SELECT mlp.idlpo,mlp.no_lpo,mlp.idpuskesmas,mlp.tanggal_lpo,mlp.nama_ka,mlp.jumlah_kunjungan_gratis,mlp.jumlah_kunjungan_bayar,mlp.jumlah_kunjungan_bpjs,mlp.response_lpo,msb.idsbbk,mlp.date_modified FROM master_lpo mlp LEFT JOIN master_sbbk msb ON (msb.idlpo=mlp.idlpo) WHERE mlp.status='complete'";                
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'nomor' :
                    $cluasa=" AND no_lpo='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_lpo WHERE status='complete' $cluasa",'no_lpo');
                    $str = "$str $cluasa";
                break;                
            }
        }else {
            $str = 'SELECT idobat,kode_obat,nama_obat,idsatuan_obat,harga FROM master_obat';                
            $jumlah_baris=$this->DB->getCountRowsOfTable ('master_obat','idobat');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageUsulanKebutuhan']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageUsulanKebutuhan']['page_num']=0;}
        $str = "$str ORDER BY nama_obat ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idobat','kode_obat','nama_obat','idsatuan_obat','harga'));
        $r=$this->DB->getRecord($str,$offset+1);                 
		$data=array();  
        $tahun_lalu=$_SESSION['ta']-1;        
        while (list($k,$v)=each($r)) {
            $idobat=$v['idobat'];
            $harga=$v['harga'];
            $v['harga']=$this->Obat->toRupiah($harga);
            $v['nama_satuan']=$this->DMaster->getNamaSatuanObat($v['idsatuan_obat']);
            $str = "SELECT sisa_stock FROM log_ks ksp,(SELECT MAX(idlog) AS idlog FROM log_ks WHERE tahun=$tahun_lalu AND idobat=$idobat) AS ksp2 WHERE ksp.idlog=ksp2.idlog";            
            $this->DB->setFieldTable(array('sisa_stock'));
            $r1=$this->DB->getRecord($str);        
            $sisa_stock=isset($r1[1]) ? $r1[1]['sisa_stock'] : 0;
            $v['sisa_stock_tahun_lalu']=$sisa_stock;
            $str = "SELECT SUM(pemberian)/12 AS rata2 FROM master_sbbk msb, detail_sbbk dsb WHERE msb.idsbbk=dsb.idsbbk AND msb.status='complete' AND  msb.tahun=$tahun_lalu AND dsb.idobat=$idobat";
            $this->DB->setFieldTable(array('rata2'));
            $r2=$this->DB->getRecord($str);                    
            $rata2=$r2[1]['rata2'] > 0 ? number_format($r2[1]['rata2'],2) : 0;
            $v['rata2_tahun_lalu']=$rata2;            
            $usulan = ($rata2*18)-$sisa_stock;
            $v['usulan']=$usulan;
            $sub_total=$usulan*$harga;
            $v['sub_total']=$this->Obat->toRupiah($sub_total);
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
        $this->report->printUsulanKebutuhanObat();          
        $this->lblPrintout->Text="Laporan Usulan Kebutuhan Obat Tahun $tahun";
        $this->modalPrintOut->show();
    }
}
