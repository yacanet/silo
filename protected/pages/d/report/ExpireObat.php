<?php
prado::using ('Application.MainPageSA');
class ExpireObat extends MainPageSA {         
	public function onLoad($param) {			
		parent::onLoad($param);				
        $this->showSubMenuReportStock = true;
		$this->showReportExpireObat = true;        
        $this->createObj('DMaster');
        $this->createObj('Obat');        
		if (!$this->IsPostBack&&!$this->IsCallBack) {                                   
            if (!isset($_SESSION['currentPageExpireObat'])||$_SESSION['currentPageExpireObat']['page_name']!='sa.report.ExpireObat') {
                $_SESSION['currentPageExpireObat']=array('page_name'=>'sa.report.ExpireObat','page_num'=>0,'idprogram'=>'none','waktuexpires'=>1,'modeexpires'=>'bulan');												
            }   
            $listprogram=$this->DMaster->getListProgram ();
            $listprogram['none']='Keseluruhan Program';
            $this->cmbFilterProgram->DataSource=$listprogram;
            $this->cmbFilterProgram->Text=$_SESSION['currentPageExpireObat']['idprogram'];
            $this->cmbFilterProgram->DataBind();
            $this->txtFilterWaktuExpires->Text=$_SESSION['currentPageExpireObat']['waktuexpires'];
            $this->cmbModeExpires->Text=$_SESSION['currentPageExpireObat']['modeexpires'];
            
            $this->lblJangkaWaktu->Text=$_SESSION['currentPageExpireObat']['waktuexpires']. ' '.$_SESSION['currentPageExpireObat']['modeexpires'];
            $this->populateData();
		}	        
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
        $this->lblJangkaWaktu->Text=$_SESSION['currentPageExpireObat']['waktuexpires']. ' '.$_SESSION['currentPageExpireObat']['modeexpires'];
        $this->populateData();
	}
    public function itemCreated($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {            
            
        }
    }
    public function populateData ($search=false) {        
        $waktuexpires=$_SESSION['currentPageExpireObat']['waktuexpires'];
        $modeexpires=$_SESSION['currentPageExpireObat']['modeexpires'];
        switch($modeexpires) {
            case 'hari' :                
                $str_mode_expires = " AND tanggal_expire>=DATE(NOW()) AND tanggal_expire <= DATE_ADD(DATE(NOW()),INTERVAL $waktuexpires DAY)";
            break;
            case 'minggu' :                
                $str_mode_expires = " AND tanggal_expire>=DATE(NOW()) AND tanggal_expire <= DATE_ADD(DATE(NOW()),INTERVAL $waktuexpires WEEK)";
            break;
            case 'bulan' :                
                $str_mode_expires = " AND tanggal_expire>=DATE(NOW()) AND tanggal_expire <= DATE_ADD(DATE(NOW()),INTERVAL $waktuexpires MONTH)";
            break;            
        }        
        $str = "SELECT iddetail_sbbm,nama_obat,harga,idsatuan_obat,kemasan,tanggal_expire,dsb.idprogram FROM master_sbbm msb,detail_sbbm dsb WHERE dsb.idsbbm=msb.idsbbm AND status='complete'$str_mode_expires";
        if ($search) {
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa=" AND kode_obat='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("(SELECT iddetail_sbbm FROM master_sbbm msb,detail_sbbm dsb WHERE dsb.idsbbm=msb.idsbbm AND status='complete'$str_mode_expires $cluasa GROUP BY idobat,harga,tanggal_expire) temp",'iddetail_sbbm');		
                    $str = "$str $cluasa";
                break;
                case 'nama' :
                    $cluasa=" AND nama_obat LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("(SELECT iddetail_sbbm FROM master_sbbm msb,detail_sbbm dsb WHERE dsb.idsbbm=msb.idsbbm AND status='complete'$str_mode_expires $cluasa GROUP BY idobat,harga,tanggal_expire) temp",'iddetail_sbbm');		
                    $str = "$str $cluasa";
                break;
            }
        }else{
            $idprogram=$_SESSION['currentPageExpireObat']['idprogram'];
            $str = $idprogram == 'none' ?$str:" $str AND dsb.idprogram=$idprogram";            
            $jumlah_baris=$this->DB->getCountRowsOfTable ("(SELECT iddetail_sbbm FROM master_sbbm msb,detail_sbbm dsb WHERE dsb.idsbbm=msb.idsbbm AND status='complete'$str_mode_expires GROUP BY idobat,harga,tanggal_expire) temp",'iddetail_sbbm');		
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
        $str = "$str GROUP BY idobat,harga,tanggal_expire ORDER BY tanggal_expire ASC,nama_obat ASC LIMIT $offset,$limit";        
        $this->DB->setFieldTable(array('iddetail_sbbm','nama_obat','harga','idsatuan_obat','kemasan','tanggal_expire','idprogram'));
        $r=$this->DB->getRecord($str);
        $data=array();        
        while (list($k,$v)=each($r)) {
            $harga=$v['harga'];
            $v['harga']=$this->Obat->toRupiah($harga);
            $v['nama_program']=$this->DMaster->getNamaProgramByID($v['idprogram']);
            $v['tanggal_expire']=$this->TGL->tanggal('d/m/Y',$v['tanggal_expire']);
            $iddetail_sbbm=$v['iddetail_sbbm'];
            $volume=$this->DB->getCountRowsOfTable ("kartu_stock WHERE iddetail_sbbm=$iddetail_sbbm AND mode='masuk'",'iddetail_sbbm');		
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
        $dataReport['nip_ka_gudang']=$this->setup->nipFormat($this->setup->getSettingValue('nip_ka_gudang'));
        $dataReport['nama_ka_gudang']=$this->setup->getSettingValue('nama_ka_gudang');
        $this->report->setMode('excel2007');        
        $dataReport['linkoutput']=$this->linkOutput; 
        $this->report->setDataReport($dataReport);        
        $this->report->printExpireObat();          
        $this->lblPrintout->Text="Laporan Expire Obat ".$dataReport['nama_program'];
        $this->modalPrintOut->show();
    }
}
?>