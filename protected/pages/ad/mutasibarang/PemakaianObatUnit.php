<?php
prado::using ('Application.MainPageAD');
class PemakaianObatUnit extends MainPageAD {    
    public static $totalQTY=0;
    public $datasbbk;    
	public function onLoad($param) {		
		parent::onLoad($param);		        
		$this->showPemakaianObatUnit=true;      
        $this->createObj('DMaster');
        $this->createObj('Obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	            
            if (!isset($_SESSION['currentPagePemakaianObatUnit'])||$_SESSION['currentPagePemakaianObatUnit']['page_name']!='sa.mutasibarang.PemakaianObatUnit') {
                $_SESSION['currentPagePemakaianObatUnit']=array('page_name'=>'sa.mutasibarang.PemakaianObatUnit','page_num'=>0,'search'=>false,'idunitpuskesmas'=>'none','bulan'=>date($_SESSION['ta'].'-m-01'));												
            }   
            $_SESSION['currentPagePemakaianObatUnit']['search']=false;                         
            $this->cmbFilterUnit->DataSource=$this->DMaster->getListUnitPuskesmas();
            $this->cmbFilterUnit->Text=$_SESSION['currentPagePemakaianObatUnit']['idunitpuskesmas'];
            $this->cmbFilterUnit->dataBind();
            
            $this->lblBulanTahun->Text=$this->TGL->tanggal('F Y',$_SESSION['currentPagePemakaianObatUnit']['bulan']);
            $this->cmbFilterBulan->Text=$this->TGL->tanggal('m',$_SESSION['currentPagePemakaianObatUnit']['bulan']);
            
            $this->populateData ();	            
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPagePemakaianObatUnit']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPagePemakaianObatUnit']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPagePemakaianObatUnit']['search']=true;
        $this->populateData($_SESSION['currentPagePemakaianObatUnit']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPagePemakaianObatUnit']['bulan']=$_SESSION['ta'].'-'.$this->cmbFilterBulan->Text.'-01';        
        $_SESSION['currentPagePemakaianObatUnit']['idunitpuskesmas']=$this->cmbFilterUnit->Text;
        $this->lblBulanTahun->Text=$this->TGL->tanggal('F Y',$_SESSION['currentPagePemakaianObatUnit']['bulan']);
        $this->populateData();
	}
    public function populateData ($search=false) {        
        $bulantahun=$this->TGL->tanggal('Y-m',$_SESSION['currentPagePemakaianObatUnit']['bulan']);
        $idpuskesmas=$this->idpuskesmas;        
        $str = "SELECT iddetail_sbbk_puskesmas,tanggal_sbbk_puskesmas,no_sbbk_puskesmas,kode_obat,nama_obat,kemasan,harga,pemberian_puskesmas,pemakaian_unit FROM master_sbbk_puskesmas ms,detail_sbbk_puskesmas ds WHERE ms.idsbbk_puskesmas=ds.idsbbk_puskesmas AND ms.idpuskesmas=$idpuskesmas AND ms.status_puskesmas='complete' AND DATE_FORMAT(ms.tanggal_sbbk_puskesmas,'%Y-%m')='$bulantahun'";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa=" AND kode_obat='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbk_puskesmas ms,detail_sbbk_puskesmas ds WHERE ms.idsbbk_puskesmas=ds.idsbbk_puskesmas AND ms.idpuskesmas=$idpuskesmas AND ms.status_puskesmas='complete' $cluasa",'ms.idsbbk_puskesmas');
                    $str = "$str $cluasa";
                break;
                case 'nama' :
                    $cluasa=" AND nama_obat LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbk_puskesmas ms,detail_sbbk_puskesmas ds WHERE ms.idsbbk_puskesmas=ds.idsbbk_puskesmas AND ms.idpuskesmas=$idpuskesmas AND ms.status_puskesmas='complete' $cluasa",'ms.idsbbk_puskesmas');
                    $str = "$str $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbk_puskesmas ms,detail_sbbk_puskesmas ds WHERE ms.idsbbk_puskesmas=ds.idsbbk_puskesmas AND ms.idpuskesmas=$idpuskesmas AND ms.status_puskesmas='complete' AND  DATE_FORMAT(ms.tanggal_sbbk_puskesmas,'%Y-%m')='$bulantahun'",'ms.idsbbk_puskesmas');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPagePemakaianObatUnit']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPagePemakaianObatUnit']['page_num']=0;}
        $str = "$str ORDER BY ms.date_added DESC,no_sbbk_puskesmas ASC LIMIT $offset,$limit";            
		$this->DB->setFieldTable(array('iddetail_sbbk_puskesmas','tanggal_sbbk_puskesmas','no_sbbk_puskesmas','kode_obat','nama_obat','kemasan','harga','pemberian_puskesmas','pemakaian_unit'));
		$r=$this->DB->getRecord($str,$offset+1);          
        $data = array();
        while (list($k,$v)=each($r)) {                
            $v['subtotal']=$this->Obat->toRupiah($v['harga']*$v['pemberian_puskesmas']);
            $data[$k]=$v;
        }        
		$this->RepeaterS->DataSource=$data;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}  
    
    public function checkOut ($sender,$param) {
        if ($this->IsValid) {  
            $this->DB->query('BEGIN');
            foreach ($this->RepeaterS->Items as $inputan) {
                $item=$inputan->txtQTYPemakaian->getNamingContainer();
                $iddetail_sbbk_puskesmas=$this->RepeaterS->DataKeys[$item->getItemIndex()];    
                $input_pemakaian_unit=$inputan->txtQTYPemakaian->Text;                
                
                $str = "SELECT dsb.idsbbk_puskesmas,dsb.iddetail_sbbm_puskesmas,dsb.idobat_puskesmas,dsb.pemakaian_unit,mop.stock_dinas,isuse_unit FROM master_obat_puskesmas mop,detail_sbbk_puskesmas dsb WHERE mop.idobat_puskesmas=dsb.idobat_puskesmas AND dsb.iddetail_sbbk_puskesmas=$iddetail_sbbk_puskesmas";
                $this->DB->setFieldTable(array('idsbbk_puskesmas','iddetail_sbbm_puskesmas','idobat_puskesmas','pemakaian_unit','stock_dinas','isuse_unit'));
                $r=$this->DB->getRecord($str);                 
                $stock_dinas=$r[1]['stock_dinas'];                
                
                if (($input_pemakaian_unit > 0) && ($input_pemakaian_unit <= ($stock_dinas+$r[1]['pemakaian_unit']))) {                              
                    $idsbbk_puskesmas=$r[1]['idsbbk_puskesmas'];
                    $iddetail_sbbm_puskesmas=$r[1]['iddetail_sbbm_puskesmas'];
                    $idobat_puskesmas=$r[1]['idobat_puskesmas'];                    
                    if ($r[1]['isuse_unit']) {                                                                        
                        if (($input_pemakaian_unit>=$r[1]['pemakaian_unit']) &&  ($input_pemakaian_unit<= ($r[1]['stock_dinas']+$r[1]['pemakaian_unit']))) {                            
                            $str = "UPDATE detail_sbbk_puskesmas SET pemakaian_unit=$input_pemakaian_unit,isuse_unit=1 WHERE iddetail_sbbk_puskesmas=$iddetail_sbbk_puskesmas";
                            $this->DB->updateRecord($str);
                            $pemakaian_unit=$input_pemakaian_unit-$r[1]['pemakaian_unit'];
                            $str = "UPDATE master_obat_puskesmas SET stock_dinas=stock_dinas-$pemakaian_unit WHERE idobat_puskesmas=$idobat_puskesmas";
                            $this->DB->updateRecord($str);                        
                            
                            $str = "UPDATE kartu_stock_puskesmas_dinas ks,(SELECT idkartu_stock_puskesmas FROM kartu_stock_puskesmas_dinas WHERE mode_puskesmas='masuk' AND iddetail_sbbm_puskesmas=$iddetail_sbbm_puskesmas LIMIT $pemakaian_unit) AS temp SET idsbbk_puskesmas=$idsbbk_puskesmas,iddetail_sbbk_puskesmas=$iddetail_sbbk_puskesmas,mode_puskesmas='keluar',date_modified=NOW() WHERE ks.idkartu_stock_puskesmas=temp.idkartu_stock_puskesmas";                        
                            $this->DB->updateRecord($str);
                        }
                    }else{
                        $str = "UPDATE detail_sbbk_puskesmas SET pemakaian_unit=$input_pemakaian_unit,isuse_unit=1 WHERE iddetail_sbbk_puskesmas=$iddetail_sbbk_puskesmas";
                        $this->DB->updateRecord($str);

                        $str = "UPDATE master_obat_puskesmas SET stock_dinas=stock_dinas-$input_pemakaian_unit WHERE idobat_puskesmas=$idobat_puskesmas";
                        $this->DB->updateRecord($str);

                        $str = "UPDATE kartu_stock_puskesmas_dinas ks,(SELECT idkartu_stock_puskesmas FROM kartu_stock_puskesmas_dinas WHERE mode_puskesmas='masuk' AND iddetail_sbbm_puskesmas=$iddetail_sbbm_puskesmas LIMIT $input_pemakaian_unit) AS temp SET idsbbk_puskesmas=$idsbbk_puskesmas,iddetail_sbbk_puskesmas=$iddetail_sbbk_puskesmas,mode_puskesmas='keluar',date_modified=NOW() WHERE ks.idkartu_stock_puskesmas=temp.idkartu_stock_puskesmas";                        
                        $this->DB->updateRecord($str);
                    }
                }       
            }
            $this->DB->query('COMMIT');
            $this->redirect('mutasibarang.PemakaianObatUnit',true);
        }
    }
}
