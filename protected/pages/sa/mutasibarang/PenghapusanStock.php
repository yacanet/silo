<?php
prado::using ('Application.MainPageSA');
class PenghapusanStock extends MainPageSA {    
    public static $totalQTY=0;
    public static $totalHARGA=0;
    public $datapenghapusan;    
	public function onLoad($param) {		
		parent::onLoad($param);		        
		$this->showPenghapusanStock=true;      
        $this->createObj('DMaster');
        $this->createObj('Obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {	            
            $this->Page->labelTahun->Text=$_SESSION['ta'];  
            if (isset($_SESSION['currentPagePenghapusanStock']['datapenghapusan']['process'])) {
                $this->detailProcess();
            }else{                
                if (!isset($_SESSION['currentPagePenghapusanStock'])||$_SESSION['currentPagePenghapusanStock']['page_name']!='sa.mutasibarang.PenghapusanStock') {
                    $_SESSION['currentPagePenghapusanStock']=array('page_name'=>'sa.mutasibarang.PenghapusanStock','page_num'=>0,'search'=>false,'datapenghapusan'=>array(),'cart'=>array());												
                }   
                $_SESSION['currentPagePenghapusanStock']['search']=false;                                    
                $this->populateData ();	            
            }
		}
	} 
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPagePenghapusanStock']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPagePenghapusanStock']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPagePenghapusanStock']['search']=true;
        $this->populateData($_SESSION['currentPagePenghapusanStock']['search']);
	}   
    public function itemCreated($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {            
            $status=$item->DataItem['status'];
            if ($status=='complete') {
                $item->btnEdit->Enabled=false;                
                $item->btnEdit->CssClass='table-link disabled';
                
                $item->btnDelete->Enabled=false;                
                $item->btnDelete->CssClass='table-link disabled';
            }else {
                $item->btnView->Enabled=false;                
                $item->btnView->CssClass='table-link disabled';
                
                $item->btnDelete->Attributes->OnClick="if(!confirm('Anda ingin menghapus data Penghapusan Stock ini ?')) return false;";
            }
        }
    }
    public function populateData ($search=false) {        
        $tahun=$_SESSION['ta'];        
        $str = "SELECT idpenghapusan_stock,no_berita_acara,tanggal_penghapusan,status FROM penghapusan_stock WHERE tahun=$tahun";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa="AND no_berita_acara='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("penghapusan_stock WHERE tahun=$tahun $cluasa",'idpenghapusan_stock');
                    $str = "$str $cluasa";
                break;                
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ("penghapusan_stock WHERE tahun=$tahun",'idpenghapusan_stock');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPagePenghapusanStock']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPagePenghapusanStock']['page_num']=0;}
        $str = "$str ORDER BY date_added DESC LIMIT $offset,$limit";            
		$this->DB->setFieldTable(array('idpenghapusan_stock','no_berita_acara','tanggal_penghapusan','status'));
		$r=$this->DB->getRecord($str,$offset+1);      
        $data_r=array();
        while (list($k,$v)=each($r)) {
            $id=$v['idpenghapusan_stock'];
            $str = "SELECT SUM(qty) AS jumlah_item,SUM(qty*harga) AS total_harga FROM detail_penghapusan_stock WHERE idpenghapusan_stock=$id";
            $this->DB->setFieldTable(array('jumlah_item','total_harga'));
            $r2=$this->DB->getRecord($str);  
            $v['jumlah_item']=$this->Obat->toRupiah($r2[1]['jumlah_item']);
            $v['total_harga']=$this->Obat->toRupiah($r2[1]['total_harga']);
            $data_r[$k]=$v;
        }
		$this->RepeaterS->DataSource=$data_r;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}
    public function addProcess ($sender,$param) {
        $this->idProcess='add';
        $_SESSION['currentPagePenghapusanStock']['datapenghapusan']['process']='add';
        $this->redirect('mutasibarang.PenghapusanStock',true);
    }
    public function detailProcess() {
        $this->idProcess='add';
        $datapenghapusan= $_SESSION['currentPagePenghapusanStock']['datapenghapusan']; 
        $this->txtAddNoBeritaAcara->Text=$datapenghapusan['no_berita_acara'];
        $this->cmbAddTanggalPenghapusanStock->Text=$this->TGL->tanggal('d-m-Y',$datapenghapusan['tanggal_penghapusan']); 
        $this->populateCart();
    } 
    public function itemCreatedCart($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {                                    
            $item->btnDelete->Attributes->OnClick="if(!confirm('Anda ingin menghapus item ini ?')) return false;";            
            
            PenghapusanStock::$totalQTY += $item->DataItem['sisa'];
            $harga=$item->DataItem['sisa']*$item->DataItem['harga']; 
            PenghapusanStock::$totalHARGA += $harga;                        
            
        }
    }
    protected function populateCart () {             
        $this->idProcess='add';        
        $cart = $_SESSION['currentPagePenghapusanStock']['cart'];                          
		$this->RepeaterCart->DataSource=$cart;
		$this->RepeaterCart->dataBind();             
	}
    public function viewRecord ($sender,$param) {                        
        $dataobat = $this->Obat->getInfoMasterObat($sender->CommandParameter);        
        $this->setInfoObat($dataobat);
        $this->modalInfoObat->show();
    }
    public function checkBarcode ($sender,$param) {
        $this->idProcess='add';
        $barcode=$param->Value;		
        if ($barcode != '') {
            try {                
                $tanggal_sekarang=date('Y-m-d');
                $str = "SELECT iddetail_sbbm,nama_obat,harga,qty FROM detail_sbbm dsb,master_sbbm msb WHERE dsb.idsbbm=msb.idsbbm AND barcode='$barcode' AND status='complete' AND dsb.tanggal_expire<'$tanggal_sekarang' AND isdestroyed=0";
                $this->DB->setFieldTable(array('iddetail_sbbm','nama_obat','harga','qty'));
                $r=$this->DB->getRecord($str);  
                if (isset($r[1])) {
                    $iddetail_sbbm=$r[1]['iddetail_sbbm'];
                    $nama_obat = $r[1]['nama_obat'];
                    $harga = $this->Obat->toRupiah($r[1]['harga']);
                    $qty=$r[1]['qty'];
                    $jumlah_keluar=$this->DB->getCountRowsOfTable ("kartu_stock WHERE iddetail_sbbm=$iddetail_sbbm AND mode='keluar' AND isdestroyed=0",'idkartu_stock');		
                    if ($jumlah_keluar >= $qty) {
                        throw new Exception ("Nama Obat ($nama_obat) yang harga $harga telah habis stocknya.");		
                    }
                }else {
                    throw new Exception ("Barcode ($barcode) tidak ada di database atau belum expire. Silahkan ganti dengan yang lain.");		
                }
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }
    }
    public function addObat ($sender,$param) {
        if ($this->IsValid) {
            $barcode=$this->txtAddBarcode->Text;
            $str = "SELECT dsb.iddetail_sbbm,dsb.idobat,dsb.kode_obat,dsb.nama_obat,dsb.harga,dsb.tanggal_expire,dsb.kemasan,dsb.qty FROM detail_sbbm dsb WHERE barcode='$barcode'";
            $this->DB->setFieldTable(array('iddetail_sbbm','idobat','kode_obat','nama_obat','harga','kemasan','tanggal_expire','qty'));
            $r=$this->DB->getRecord($str);
            $dataobat=$r[1];
            $iddetail_sbbm=$dataobat['iddetail_sbbm'];
            $jumlah_keluar=$this->DB->getCountRowsOfTable ("kartu_stock WHERE iddetail_sbbm=$iddetail_sbbm AND mode='keluar' AND isdestroyed=0",'idkartu_stock');		
            $stock=$dataobat['qty']-$jumlah_keluar;
            $_SESSION['currentPagePenghapusanStock']['cart'][$iddetail_sbbm]=array(
                                                                        'iddetail_sbbm'=>$iddetail_sbbm,
                                                                        'idobat'=>$dataobat['idobat'],
                                                                        'idobat_puskesmas'=>$dataobat['idobat_puskesmas'],
                                                                        'kode_obat'=>$dataobat['kode_obat'],
                                                                        'nama_obat'=>$dataobat['nama_obat'],
                                                                        'harga'=>$dataobat['harga'],                                                                    
                                                                        'tanggal_expire'=>$this->TGL->tanggal('d/m/Y',$dataobat['tanggal_expire']),
                                                                        'kemasan'=>$dataobat['kemasan'],
                                                                        'qty'=>$dataobat['qty'],
                                                                        'pemakaian'=>$jumlah_keluar,
                                                                        'sisa'=>$stock,
                                                                        );
            $this->redirect('mutasibarang.PenghapusanStock',true);
        }
    }
    public function deleteItem($sender,$param) {
        $id=$this->getDataKeyField($sender,$this->RepeaterCart);
        unset($_SESSION['currentPagePenghapusanStock']['cart'][$id]);        
        $this->populateCart();        
    }
    public function checkNoBeritaAcara ($sender,$param) {        
        $no_berita_acara=$param->Value;		
        if ($no_berita_acara != '') {
            try {                
                if ($_SESSION['currentPagePenghapusanStock']['datapenghapusan']['no_berita_acara'] != $no_berita_acara) {
                    if ($this->DB->checkRecordIsExist('no_berita_acara','penghapusan_stock',$no_berita_acara)) {                                
                        throw new Exception ("Nomor Berita Acara ($no_berita_acara) sudah ada, silahkan ganti dengan yang lain.");		
                    }
                }            
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }
    }
    public function saveData ($sender,$param) {
        if ($this->IsValid) {   
            $tahun=$_SESSION['ta'];
            $data= $_SESSION['currentPagePenghapusanStock']['datapenghapusan'];
            $no_berita_acara=addslashes($this->txtAddNoBeritaAcara->Text);
            $tanggal_penghapusan = date('Y-m-d',$this->cmbAddTanggalPenghapusanStock->TimeStamp); 
            $data['no_berita_acara']=$no_berita_acara;
            $data['tanggal_penghapusan']=$tanggal_penghapusan;                                    
            $cart=$_SESSION['currentPagePenghapusanStock']['cart'];            
            if (count($cart) > 0) {
                if ($data['process'] == 'add') {
                    $str = "INSERT INTO penghapusan_stock (idpenghapusan_stock,no_berita_acara,tanggal_penghapusan,tahun,status,date_added,date_modified) VALUES (NULL,'$no_berita_acara','$tanggal_penghapusan',$tahun,'draft',NOW(),NOW())";
                    $this->DB->query('BEGIN');
                    $cart=$_SESSION['currentPagePenghapusanStock']['cart'];                    
                    if ($this->DB->insertRecord($str)) {
                        $idpenghapusan_stock=$this->DB->getLastInsertID ();                        
                        foreach ($cart as $iddetail_sbbm=>$v) {      
                            $sisa=$v['sisa'];                                                       
                            $pemakaian=$v['pemakaian'];
                            $str = "INSERT INTO detail_penghapusan_stock (idpenghapusan_stock,iddetail_sbbm,idobat,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,tanggal_expire,qty,pemakaian,sisa,date_added,date_modified) SELECT $idpenghapusan_stock,$iddetail_sbbm,idobat,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,tanggal_expire,qty,$pemakaian,$sisa,NOW(),NOW() FROM detail_sbbm WHERE iddetail_sbbm=$iddetail_sbbm";                                                
                            $this->DB->insertRecord($str);                        
                        }
                        $data['idpenghapusan_stock']=$idpenghapusan_stock;
                        $data['process']='edit';
                        $_SESSION['currentPagePenghapusanStock']['datapenghapusan']=$data;
                        $this->DB->query('COMMIT');
                        $this->redirect('mutasibarang.PenghapusanStock',true);
                    }else{
                        $this->DB->query('ROLLBACK');
                    }
                }elseif ($data['process'] == 'edit') {            
                    $datapenghapusan=$_SESSION['currentPagePenghapusanStock']['datapenghapusan'];
                    $idpenghapusan_stock=$datapenghapusan['idpenghapusan_stock'];
                    
                    $str = "UPDATE penghapusan_stock SET no_berita_acara=$no_berita_acara,tanggal_penghapusan='$tanggal_penghapusan',date_modified=NOW() WHERE idpenghapusan_stock=$idpenghapusan_stock";
                    $this->DB->query('BEGIN');
                    $cart=$_SESSION['currentPagePenghapusanStock']['cart'];                    
                    if ($this->DB->updateRecord($str)) {
                        $this->DB->deleteRecord("detail_penghapusan_stock WHERE idpenghapusan_stock=$idpenghapusan_stock");                                             
                        foreach ($cart as $iddetail_sbbm=>$v) {      
                            $sisa=$v['sisa'];                                                       
                            $pemakaian=$v['pemakaian'];
                            $str = "INSERT INTO detail_penghapusan_stock (idpenghapusan_stock,iddetail_sbbm,idobat,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,tanggal_expire,qty,pemakaian,sisa,date_added,date_modified) SELECT $idpenghapusan_stock,$iddetail_sbbm,idobat,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,tanggal_expire,qty,$pemakaian,$sisa,NOW(),NOW() FROM detail_sbbm WHERE iddetail_sbbm=$iddetail_sbbm";                                                
                            $this->DB->insertRecord($str);                        
                        }                        
                        $data['process']='edit';
                        $_SESSION['currentPagePenghapusanStock']['datapenghapusan']=$data;
                        $this->DB->query('COMMIT');
                        $this->redirect('mutasibarang.PenghapusanStock',true);
                    }else{
                        $this->DB->query('ROLLBACK');
                    }
                }
            }
        }
    }
    public function checkOut ($sender,$param) {
        if ($this->IsValid) {   
            $tahun=$_SESSION['ta'];
            $data= $_SESSION['currentPagePenghapusanStock']['datapenghapusan'];
            $no_berita_acara=addslashes($this->txtAddNoBeritaAcara->Text);
            $tanggal_penghapusan = date('Y-m-d',$this->cmbAddTanggalPenghapusanStock->TimeStamp); 
            $data['no_berita_acara']=$no_berita_acara;
            $data['tanggal_penghapusan']=$tanggal_penghapusan;                                    
            $cart=$_SESSION['currentPagePenghapusanStock']['cart'];            
            if (count($cart) > 0) {
                if ($data['process'] == 'add') {
                    $str = "INSERT INTO penghapusan_stock (idpenghapusan_stock,no_berita_acara,tanggal_penghapusan,tahun,status,date_added,date_modified) VALUES (NULL,'$no_berita_acara','$tanggal_penghapusan',$tahun,'complete',NOW(),NOW())";
                    $this->DB->query('BEGIN');
                    $cart=$_SESSION['currentPagePenghapusanStock']['cart'];                    
                    if ($this->DB->insertRecord($str)) {
                        $idpenghapusan_stock=$this->DB->getLastInsertID ();                        
                        foreach ($cart as $iddetail_sbbm=>$v) {
                            $idobat=$v['idobat'];
                            $sisa=$v['sisa'];                                                       
                            $pemakaian=$v['pemakaian'];
                            $str = "INSERT INTO detail_penghapusan_stock (idpenghapusan_stock,iddetail_sbbm,idobat,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,tanggal_expire,qty,pemakaian,sisa,date_added,date_modified) SELECT $idpenghapusan_stock,$iddetail_sbbm,idobat,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,tanggal_expire,qty,$pemakaian,$sisa,NOW(),NOW() FROM detail_sbbm WHERE iddetail_sbbm=$iddetail_sbbm";                                                
                            $this->DB->insertRecord($str);                        
                            $jumlah_stock=$this->DB->getCountRowsOfTable ("kartu_stock WHERE iddetail_sbbm=$iddetail_sbbm AND mode='masuk'",'idkartu_stock');		                                                        
                            $this->DB->updateRecord("UPDATE kartu_stock SET isdestroyed=1 WHERE iddetail_sbbm=$iddetail_sbbm AND mode='masuk'");                            
                            $this->DB->updateRecord("UPDATE master_obat SET stock=stock-$jumlah_stock WHERE idobat=$idobat");                            
                            $this->DB->updateRecord("UPDATE detail_sbbm SET isdestroyed=1 WHERE iddetail_sbbm=$iddetail_sbbm");
                        }
                        $this->DB->query('COMMIT');
                        unset($_SESSION['currentPagePenghapusanStock']['datapenghapusan']);
                        unset($_SESSION['currentPagePenghapusanStock']['cart']);
                        $this->redirect('mutasibarang.PenghapusanStock',true);
                    }else{
                        $this->DB->query('ROLLBACK');
                    }
                }elseif ($data['process'] == 'edit') {            
                    $datapenghapusan=$_SESSION['currentPagePenghapusanStock']['datapenghapusan'];
                    $idpenghapusan_stock=$datapenghapusan['idpenghapusan_stock'];
                    
                    $str = "UPDATE penghapusan_stock SET no_berita_acara=$no_berita_acara,tanggal_penghapusan='$tanggal_penghapusan',tahun=$tahun,status='complete',date_modified=NOW() WHERE idpenghapusan_stock=$idpenghapusan_stock";
                    $this->DB->query('BEGIN');
                    $cart=$_SESSION['currentPagePenghapusanStock']['cart'];                    
                    if ($this->DB->updateRecord($str)) {
                        $this->DB->deleteRecord("detail_penghapusan_stock WHERE idpenghapusan_stock=$idpenghapusan_stock");                                             
                        foreach ($cart as $iddetail_sbbm=>$v) { 
                            $idobat=$v['idobat'];
                            $sisa=$v['sisa'];                                                       
                            $pemakaian=$v['pemakaian'];
                            $str = "INSERT INTO detail_penghapusan_stock (idpenghapusan_stock,iddetail_sbbm,idobat,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,tanggal_expire,qty,pemakaian,sisa,date_added,date_modified) SELECT $idpenghapusan_stock,$iddetail_sbbm,idobat,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,tanggal_expire,qty,$pemakaian,$sisa,NOW(),NOW() FROM detail_sbbm WHERE iddetail_sbbm=$iddetail_sbbm";                                                
                            $this->DB->insertRecord($str);                        
                            $jumlah_stock=$this->DB->getCountRowsOfTable ("kartu_stock WHERE iddetail_sbbm=$iddetail_sbbm AND mode='masuk'",'idkartu_stock');		                                                        
                            $this->DB->updateRecord("UPDATE kartu_stock SET isdestroyed=1 WHERE iddetail_sbbm=$iddetail_sbbm AND mode='masuk'");                            
                            $this->DB->updateRecord("UPDATE master_obat SET stock=stock-$jumlah_stock WHERE idobat=$idobat");                            
                            $this->DB->updateRecord("UPDATE detail_sbbm SET isdestroyed=1 WHERE iddetail_sbbm=$iddetail_sbbm");
                        }                        
                        $this->DB->query('COMMIT');
                        unset($_SESSION['currentPagePenghapusanStock']['datapenghapusan']);
                        unset($_SESSION['currentPagePenghapusanStock']['cart']);
                        $this->redirect('mutasibarang.PenghapusanStock',true);
                    }else{
                        $this->DB->query('ROLLBACK');
                    }
                }
            }
        }
    }
    public function closePenghapusan ($sender,$param) {
        unset($_SESSION['currentPagePenghapusanStock']['datapenghapusan']);        
        unset($_SESSION['currentPagePenghapusanStock']['cart']);        
        $this->redirect('mutasibarang.PenghapusanStock',true);
    }
    public function editRecord ($sender,$param) {
        $this->idProcess='edit';        
        $id=$this->getDataKeyField($sender,$this->RepeaterS);              
        $str = "SELECT idpenghapusan_stock,no_berita_acara,tanggal_penghapusan,status FROM penghapusan_stock WHERE idpenghapusan_stock=$id";
        $this->DB->setFieldTable(array('idpenghapusan_stock','no_berita_acara','tanggal_penghapusan','status'));
		$r=$this->DB->getRecord($str);
        $datapenghapusan=$r[1];
        $datapenghapusan['process']='edit';
        $_SESSION['currentPagePenghapusanStock']['datapenghapusan']=$datapenghapusan;
        
        $str = "SELECT iddetail_sbbm,idobat,kode_obat,nama_obat,harga,idsatuan_obat,idgolongan,kemasan,tanggal_expire,qty,pemakaian,sisa FROM detail_penghapusan_stock WHERE idpenghapusan_stock=$id";
        $this->DB->setFieldTable(array('iddetail_sbbm','idobat','kode_obat','nama_obat','harga','idsatuan_obat','idgolongan','kemasan','tanggal_expire','qty','pemakaian','sisa'));
        $r=$this->DB->getRecord($str);
        $cart=array();
        while (list($k,$v)=each($r)) {      
            $iddetail_sbbm=$v['iddetail_sbbm'];
            $jumlah_keluar=$this->DB->getCountRowsOfTable ("kartu_stock WHERE iddetail_sbbm=$iddetail_sbbm AND mode='keluar' AND isdestroyed=0",'idkartu_stock');		
            $stock=$v['qty']-$jumlah_keluar;
            $v['sisa']=$stock;
            $cart[$v['iddetail_sbbm']]=$v;
        }
        $_SESSION['currentPagePenghapusanStock']['cart']=$cart;
        $this->redirect('mutasibarang.PenghapusanStock',true);
    }
    public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);                
        $this->DB->deleteRecord("penghapusan_stock WHERE idpenghapusan_stock=$id");                         
        $this->redirect('mutasibarang.PenghapusanStock',true);
	}
}
