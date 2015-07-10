<?php
prado::using ('Application.MainPageAD');
class ExpireObat extends MainPageAD {         
	public function onLoad($param) {			
		parent::onLoad($param);				
        $this->showSubMenuReportStock = true;
		$this->showReportExpireObat = true;        
        $this->createObj('DMaster');
        $this->createObj('Obat');        
		if (!$this->IsPostBack&&!$this->IsCallBack) {                                   
            if (!isset($_SESSION['currentPageExpireObat'])||$_SESSION['currentPageExpireObat']['page_name']!='ad.report.ExpireObat') {
                $_SESSION['currentPageExpireObat']=array('page_name'=>'ad.report.ExpireObat','page_num'=>0,'idprogram'=>'none','waktuexpires'=>1,'modeexpires'=>'bulankedepan');												
            }   
            $listprogram=$this->DMaster->getListProgram ();
            $listprogram['none']='Keseluruhan Program';
            $this->cmbFilterProgram->DataSource=$listprogram;
            $this->cmbFilterProgram->Text=$_SESSION['currentPageExpireObat']['idprogram'];
            $this->cmbFilterProgram->DataBind();
            $this->txtFilterWaktuExpires->Text=$_SESSION['currentPageExpireObat']['waktuexpires'];
            $this->cmbModeExpires->Text=$_SESSION['currentPageExpireObat']['modeexpires'];
            
            $this->setLabelJangkaWaktu();
            $this->populateData();
		}	        
	}  
    private function setLabelJangkaWaktu () {        
        switch ($_SESSION['currentPageExpireObat']['modeexpires']) {            
            case 'harikedepan' :                
                $waktu = ' Hari ke Depan';
            break;
            case 'minggukedepan' :                
                $waktu = ' Minggu ke Depan';
            break;
            case 'bulankedepan' :                
                $waktu = ' Bulan ke Depan';
            break;                    
            case 'harikebelakang' :                
                $waktu = ' Hari ke Belakang';
            break;
            case 'minggukebelakang' :                
                $waktu = ' Minggu ke Belakang';
            break;
            case 'bulankebelakang' :                
                $waktu = ' Bulan ke Belakang';
            break;   
        }
        $this->lblJangkaWaktu->Text=$_SESSION['currentPageExpireObat']['waktuexpires'] ." $waktu";
    }
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageExpireObat']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageExpireObat']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageExpireObat']['search']=true;
        $this->populateData($_SESSION['currentPageExpireObat']['search']);
	}   
    public function filterRecord ($sender,$param) {        
        $_SESSION['currentPageExpireObat']['idprogram']=$this->cmbFilterProgram->Text;
		$_SESSION['currentPageExpireObat']['waktuexpires']=$this->txtFilterWaktuExpires->Text;
        $_SESSION['currentPageExpireObat']['modeexpires']=$this->cmbModeExpires->Text;        
        $this->setLabelJangkaWaktu();
        $this->populateData();
	}
    public function itemCreated($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {            
            
        }
    }
    public function populateData ($search=false) {    
        $idpuskesmas=$this->idpuskesmas;
        $waktuexpires=$_SESSION['currentPageExpireObat']['waktuexpires'];
        $modeexpires=$_SESSION['currentPageExpireObat']['modeexpires'];
        switch($modeexpires) {
            case 'harikedepan' :                
                $str_mode_expires = " AND tanggal_expire>=DATE(NOW()) AND tanggal_expire <= DATE_ADD(DATE(NOW()),INTERVAL $waktuexpires DAY)";
            break;
            case 'minggukedepan' :                
                $str_mode_expires = " AND tanggal_expire>=DATE(NOW()) AND tanggal_expire <= DATE_ADD(DATE(NOW()),INTERVAL $waktuexpires WEEK)";
            break;
            case 'bulankedepan' :                
                $str_mode_expires = " AND tanggal_expire>=DATE(NOW()) AND tanggal_expire <= DATE_ADD(DATE(NOW()),INTERVAL $waktuexpires MONTH)";
            break;          
            case 'harikebelakang' :                
                $str_mode_expires = " AND YEAR(tanggal_expire) = YEAR(CURRENT_DATE - INTERVAL $waktuexpires MONTH) AND MONTH(tanggal_expire) = MONTH(CURRENT_DATE - INTERVAL $waktuexpires DAY)";
            break;
            case 'minggukebelakang' :                
                $str_mode_expires = " AND YEAR(tanggal_expire) = YEAR(CURRENT_DATE - INTERVAL $waktuexpires MONTH) AND MONTH(tanggal_expire) = MONTH(CURRENT_DATE - INTERVAL $waktuexpires WEEK)";
            break;
            case 'bulankebelakang' :                
                $str_mode_expires = " AND YEAR(tanggal_expire) = YEAR(CURRENT_DATE - INTERVAL $waktuexpires MONTH) AND MONTH(tanggal_expire) = MONTH(CURRENT_DATE - INTERVAL $waktuexpires MONTH)";
            break;              
        }        
        $str = "SELECT iddetail_sbbm_puskesmas,nama_obat,harga,idsatuan_obat,kemasan,tanggal_expire,dsb.idprogram_gudang FROM master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsb WHERE dsb.idsbbm_puskesmas=msb.idsbbm_puskesmas AND msb.idpuskesmas=$idpuskesmas AND status_puskesmas='complete'$str_mode_expires";
        if ($search) {
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa=" AND kode_obat='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("(SELECT iddetail_sbbm_puskesmas FROM master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsb WHERE dsb.idsbbm_puskesmas=msb.idsbbm_puskesmas AND msb.idpuskesmas=$idpuskesmas AND  status_puskesmas='complete'$str_mode_expires $cluasa GROUP BY idobat_puskesmas,harga,tanggal_expire) temp",'iddetail_sbbm_puskesmas');		
                    $str = "$str $cluasa";
                break;
                case 'nama' :
                    $cluasa=" AND nama_obat LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("(SELECT iddetail_sbbm_puskesmas FROM master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsb WHERE dsb.idsbbm_puskesmas=msb.idsbbm_puskesmas AND msb.idpuskesmas=$idpuskesmas AND  status_puskesmas='complete'$str_mode_expires $cluasa GROUP BY idobat_puskesmas,harga,tanggal_expire) temp",'iddetail_sbbm_puskesmas');		
                    $str = "$str $cluasa";
                break;
            }
        }else{
            $idprogram=$_SESSION['currentPageExpireObat']['idprogram'];
            $str = $idprogram == 'none' ?$str:" $str AND dsb.idprogram_gudang=$idprogram";            
            $jumlah_baris=$this->DB->getCountRowsOfTable ("(SELECT iddetail_sbbm_puskesmas FROM master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsb WHERE dsb.idsbbm_puskesmas=msb.idsbbm_puskesmas AND msb.idpuskesmas=$idpuskesmas AND  status_puskesmas='complete'$str_mode_expires GROUP BY idobat_puskesmas,harga,tanggal_expire) temp",'iddetail_sbbm_puskesmas');		
        }        
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageExpireObat']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=30;$_SESSION['currentPageExpireObat']['page_num']=0;}
        $str = "$str GROUP BY idobat_puskesmas,harga,tanggal_expire ORDER BY tanggal_expire ASC,nama_obat ASC LIMIT $offset,$limit";        
        $this->DB->setFieldTable(array('iddetail_sbbm_puskesmas','nama_obat','harga','idsatuan_obat','kemasan','tanggal_expire','idprogram_gudang'));
        $r=$this->DB->getRecord($str);
        $data=array();        
        while (list($k,$v)=each($r)) {
            $harga=$v['harga'];
            $v['harga']=$this->Obat->toRupiah($harga);
            $v['nama_program']=$this->DMaster->getNamaProgramByID($v['idprogram_gudang']);
            $v['tanggal_expire']=$this->TGL->tanggal('d/m/Y',$v['tanggal_expire']);
            $iddetail_sbbm_puskesmas=$v['iddetail_sbbm_puskesmas'];
            $volume=$this->DB->getCountRowsOfTable ("kartu_stock_puskesmas WHERE iddetail_sbbm_puskesmas=$iddetail_sbbm_puskesmas AND mode_puskesmas='masuk'",'iddetail_sbbm_puskesmas');		
            $v['volume']=$volume;
            $subtotal=$volume*$harga;
            $v['subtotal']=$this->Obat->toRupiah($subtotal);
            $data[$k]=$v;
        }
        $this->RepeaterS->DataSource=$data;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
    }
    public function printOut ($sender,$param) {        
        $this->createObj('report');             
        $idprogram=$_SESSION['currentPageExpireObat']['idprogram'];
        $dataReport['idprogram']=$idprogram;
        $dataReport['waktuexpires']=$_SESSION['currentPageExpireObat']['waktuexpires'];
        $dataReport['modeexpires']=$_SESSION['currentPageExpireObat']['modeexpires'];        
        $dataReport['nama_program']=$idprogram=='none'?'':' PROGRAM '. $this->DMaster->getNamaProgramByID($idprogram);        
        $dataReport['nama_puskesmas']=$this->Pengguna->getDataUser('nama_puskesmas');
        $this->report->setMode('excel2007');        
        $dataReport['linkoutput']=$this->linkOutput; 
        $this->report->setDataReport($dataReport);        
        $this->report->printExpireObatPuskesmas($this->idpuskesmas);          
        $this->lblPrintout->Text="Laporan Expire Obat ".$dataReport['nama_program'];
        $this->modalPrintOut->show();
    }
}
?>