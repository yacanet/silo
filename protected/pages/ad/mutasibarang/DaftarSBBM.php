<?php
prado::using ('Application.MainPageAD');
class DaftarSBBM extends MainPageAD {    
    public static $totalQTY=0;
    public static $totalHARGA=0;
    public $datasbbm;
	public function onLoad($param) {		
		parent::onLoad($param);		
        $this->showSubMenuMutasiBarangMasuk=true;
		$this->showDaftarSBBM=true;              
        $this->createObj('Obat');
        $this->createObj('DMaster');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            $this->Page->labelTahunDaftarSBBM->Text=$_SESSION['ta'];                         
            if (isset($_SESSION['currentPageDaftarSBBM']['datasbbm']['idsbbm_puskesmas'])) {
                $this->detailProcess();
            }else {
                if (!isset($_SESSION['currentPageDaftarSBBM'])||$_SESSION['currentPageDaftarSBBM']['page_name']!='ad.mutasibarang.DaftarSBBM') {
                    $_SESSION['currentPageDaftarSBBM']=array('page_name'=>'ad.mutasibarang.DaftarSBBM','page_num'=>0,'search'=>false,'datasbbm'=>array(),'cart'=>array());												
                }   
                $_SESSION['currentPageDaftarSBBM']['search']=false;                              
                $this->populateData ();	
            }
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageDaftarSBBM']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageDaftarSBBM']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageDaftarSBBM']['search']=true;
        $this->populateData($_SESSION['currentPageDaftarSBBM']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageDaftarSBBM']['status_sbbk']=$this->cmbFilterStatus->Text;
        $this->populateData();
	}    
    public function itemCreated($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {   
            $status=$item->DataItem['status_puskesmas'];
            if ($status=='complete') {                
                $item->btnDelete->Enabled=false;                
                $item->btnDelete->CssClass='table-link disabled';
            }else {
                $item->btnView->Enabled=false;                
                $item->btnView->CssClass='table-link disabled';
                
                $item->btnDelete->Attributes->OnClick="if(!confirm('Anda ingin menghapus data SBBM ini ?')) return false;";
            }
        }
    }
    public function populateData ($search=false) {                
        $ta=$_SESSION['ta'];
        $idpuskesmas=$this->idpuskesmas;
        $str = "SELECT msp.idsbbm_puskesmas,msp.tanggal_sbbm_puskesmas,msp.no_sbbk_gudang,msp.tanggal_sbbk_gudang,msb.no_lpo,msb.tanggal_lpo,msp.nip_penerima_puskesmas,msp.nama_penerima_puskesmas,msp.status_puskesmas FROM master_sbbm_puskesmas msp,master_sbbk msb WHERE msp.idsbbk_gudang=msb.idsbbk AND msp.tahun_puskesmas=$ta AND msp.idpuskesmas=$idpuskesmas";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'nomor_sbbk' :
                    $cluasa=" AND msp.no_sbbk_gudang='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbm_puskesmas msp,master_sbbk msb WHERE msp.idsbbk_gudang=msb.idsbbk AND msp.tahun_puskesmas=$ta AND msp.idpuskesmas=$idpuskesmas $cluasa",'msp.idsbbm_puskesmas');
                    $str = "$str $cluasa";
                break;                
                case 'nomor_lpo' :
                    $cluasa=" AND msb.no_lpo='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbm_puskesmas msp,master_sbbk msb WHERE msp.idsbbk_gudang=msb.idsbbk AND msp.tahun_puskesmas=$ta AND msp.idpuskesmas=$idpuskesmas $cluasa",'msp.idsbbm_puskesmas');
                    $str = "$str $cluasa";
                break;                
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ("master_sbbm_puskesmas msp,master_sbbk msb WHERE msp.idsbbk_gudang=msb.idsbbk AND msp.tahun_puskesmas=$ta AND msp.idpuskesmas=$idpuskesmas",'msp.idsbbm_puskesmas');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageDaftarSBBM']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageDaftarSBBM']['page_num']=0;}
        $str = "$str ORDER BY tanggal_sbbm_puskesmas DESC,no_sbbk_gudang ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idsbbm_puskesmas','tanggal_sbbm_puskesmas','no_sbbk_gudang','tanggal_sbbk_gudang','no_lpo','tanggal_lpo','nip_penerima_puskesmas','nama_penerima_puskesmas','status_puskesmas'));
		$r=$this->DB->getRecord($str,$offset+1);          
        $data_r=array();
        while (list($k,$v)=each($r)) {                
            $v['tanggal_sbbk_gudang']=$this->TGL->tanggal('d/m/Y',$v['tanggal_sbbk_gudang']);
            $v['tanggal_lpo']=$this->TGL->tanggal('d/m/Y',$v['tanggal_lpo']);
            if ($v['status_puskesmas'] == 'none') {
                $v['tanggal_sbbm_puskesmas']='N.A';
                $v['nip_penerima_puskesmas']='N.A';
                $v['nama_penerima_puskesmas']='N.A';
            }else{
                $v['tanggal_sbbm_puskesmas']=$this->TGL->tanggal('d/m/Y',$v['tanggal_sbbm_puskesmas']);
                $v['nip_penerima_puskesmas']=$this->setup->nipFormat($v['nip_penerima_puskesmas']);
            }
            $data_r[$k]=$v;
        }
		$this->RepeaterS->DataSource=$data_r;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}    
    public function editRecord ($sender,$param) {
        $this->idProcess='edit';        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);        		        
        $str = "SELECT msp.idsbbm_puskesmas,msb.idsbbk,msb.no_sbbk,msb.tanggal_sbbk,msb.idpuskesmas,msb.permintaan_dari,msb.idlpo,msb.no_lpo,msb.tanggal_lpo,msb.keperluan,nip_penerima,nama_penerima,tanggal_diterima,msb.no_spmb,msb.nip_ka_gudang,msb.nama_ka_gudang,msb.nip_pengemas,msb.nama_pengemas,msb.response_sbbk,msb.tahun,msp.status_puskesmas FROM master_sbbm_puskesmas msp,master_sbbk msb,master_lpo mlp WHERE msp.idsbbk_gudang=msb.idsbbk AND mlp.idlpo=msb.idlpo AND msp.idsbbm_puskesmas=$id";
        $this->DB->setFieldTable(array('idsbbm_puskesmas','idsbbk','no_sbbk','tanggal_sbbk','idpuskesmas','permintaan_dari','idlpo','no_lpo','tanggal_lpo','keperluan','no_spmb','nip_penerima','nama_penerima','tanggal_diterima','nip_ka_gudang','nama_ka_gudang','nip_pengemas','nama_pengemas','response_sbbk','tahun','status_puskesmas'));
        $datasbbm=$this->DB->getRecord($str);                                
        
        $_SESSION['currentPageSBBMBaru']['datasbbm']=$datasbbm[1];
        $_SESSION['currentPageSBBMBaru']['datasbbm']['issaved']=true;
        $_SESSION['currentPageSBBMBaru']['datasbbm']['mode']=$datasbbm[1]['status_puskesmas']==1?'buat':'ubah';               
              
        $this->redirect('mutasibarang.SBBMBaru',true);
        
    }         
    public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);                
        $this->DB->deleteRecord("master_sbbm_puskesmas WHERE idsbbm_puskesmas=$id");
        $str = "UPDATE master_sbbk,(SELECT idsbbk_gudang FROM master_sbbm_puskesmas WHERE idsbbm_puskesmas=$id) AS temp SET tanggal_diterima='0000-00-00',nip_penerima='',nama_penerima='',date_modified=NOW() WHERE idsbbk=temp.idsbbk_gudang";
        $this->DB->updateRecord($str);
        if ($_SESSION['currentPageSBBMBaru']['datasbbm']['idsbbm_puskesmas']==$id) {
            unset($_SESSION['currentPageSBBMBaru']['datasbbm']);
            unset($_SESSION['currentPageSBBMBaru']['cart']);
        }
        $this->redirect('mutasibarang.DaftarSBBM',true);		        
	}
    public function viewRecord ($sender,$param) {        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);           
        $str = "SELECT msp.idsbbm_puskesmas,msp.tanggal_sbbm_puskesmas,msp.no_sbbk_gudang,msp.tanggal_sbbk_gudang,msb.no_lpo,msb.tanggal_lpo,msp.nama_penerima_puskesmas,msp.tanggal_sbbm_puskesmas,msp.status_puskesmas FROM master_sbbm_puskesmas msp,master_sbbk msb WHERE msp.idsbbk_gudang=msb.idsbbk AND idsbbm_puskesmas=$id";
        $this->DB->setFieldTable(array('idsbbm_puskesmas','tanggal_sbbm_puskesmas','no_sbbk_gudang','tanggal_sbbk_gudang','no_lpo','tanggal_lpo','nama_penerima_puskesmas','tanggal_sbbm_puskesmas','status_puskesmas'));
        $datasbbm=$this->DB->getRecord($str);                
        $_SESSION['currentPageDaftarSBBM']['datasbbm']=$datasbbm[1];        
                            
        $this->redirect('mutasibarang.DaftarSBBM',true);
    }  
    public function itemCreatedCart($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {                        
            DaftarSBBM::$totalQTY += $item->DataItem['qty'];            
            DaftarSBBM::$totalHARGA += $item->DataItem['harga'];
        }
    }  
    public function detailProcess() {
        $this->datasbbm = $_SESSION['currentPageDaftarSBBM']['datasbbm'];                      
        $this->idProcess='view';          
        
        $idsbbm_puskesmas=$this->datasbbm['idsbbm_puskesmas'];
        $str = "SELECT dsb.iddetail_sbbk_gudang,dsb.kode_obat,dsb.nama_obat,dsb.harga,dsb.kemasan,qty,tanggal_expire,barcode,idsumber_dana_gudang FROM detail_sbbm_puskesmas dsb WHERE idsbbm_puskesmas='$idsbbm_puskesmas'";        
        $this->DB->setFieldTable(array('iddetail_sbbk_gudang','kode_obat','nama_obat','harga','kemasan','qty','tanggal_expire','barcode','idsumber_dana_gudang'));
        $r=$this->DB->getRecord($str);               
        $cart = array();
        while (list($k,$v)=each($r)) {    
            $v['nama_sumber']=$this->DMaster->getNamaSumberDanaByID($v['idsumber_dana_gudang']);
            $cart[$v['idobat']]=$v;
        }
        $this->RepeaterCart->DataSource=$cart;
		$this->RepeaterCart->dataBind();             
    }    
    public function closeSBBM ($sender,$param) {
        unset($_SESSION['currentPageDaftarSBBM']['datasbbm']);
        unset($_SESSION['currentPageDaftarSBBM']['cart']);
        $this->redirect('mutasibarang.DaftarSBBM',true);
    }
}
