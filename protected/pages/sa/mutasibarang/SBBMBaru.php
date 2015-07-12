<?php
prado::using ('Application.MainPageSA');
class SBBMBaru extends MainPageSA {
    public static $totalQTY=0;
    public static $totalHARGA=0;
    public $datasbbm;
	public function onLoad($param) {			
		parent::onLoad($param);				
        $this->showSubMenuMutasiBarangMasuk = true;
		$this->showSBBMBaru = true;        
        $this->createObj('DMaster');
        $this->createObj('Obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {            
            $_SESSION['currentPageSBBMBaru']['search']=false;
            if (isset($_SESSION['currentPageSBBMBaru']['datasbbm']['no_sbbm'])) {                                
                $this->detailProcess();                                
            }else {
                if (!isset($_SESSION['currentPageSBBMBaru'])||$_SESSION['currentPageSBBMBaru']['page_name']!='sa.mutasibarang.SBBMBaru') {
                    $_SESSION['currentPageSBBMBaru']=array('page_name'=>'sa.mutasibarang.SBBMBaru','page_num'=>0,'search'=>false,'datasbbm'=>array(),'idprodusen'=>'none','cart'=>array());												
                }                
            }			
		}	        
	}
    public function checkBuatSBBMBaru ($sender,$param) {        
        $no_sbbm=$param->Value;		
        if ($no_sbbm != '') {
            try {                                 
                if ($this->DB->checkRecordIsExist('no_sbbm','master_sbbm',$no_sbbm)) {                                
                    throw new Exception ("Nomor SBBM ($no_sbbm) sudah tidak tersedia silahkan ganti dengan yang lain.");		
                }               
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }
    }
    public function buatSBBM($sender,$param) {
        if ($this->IsValid) {
            $nosbbm = addslashes(trim($this->txtNoSBBMBaru->Text));      
            $ta=$_SESSION['ta'];
            $tanggal_sbbm=Date("$ta-m-d");
            $this->DB->insertRecord("INSERT INTO master_sbbm (no_sbbm,tanggal_sbbm,tahun,date_added,date_modified) VALUES ('$nosbbm',$tanggal_sbbm,$ta,NOW(),NOW())");            
            $idsbbm=$this->DB->getLastInsertID ();
            $_SESSION['currentPageSBBMBaru']['datasbbm']=array('idsbbm'=>$idsbbm,'no_sbbm'=>$nosbbm,'mode'=>'buat','issaved'=>false);
            $this->redirect('mutasibarang.SBBMBaru',true);
        }
    }
    public function checkUbahSBBMBaru ($sender,$param) {        
        $no_sbbm=$param->Value;		
        if ($no_sbbm != '') {
            try {                                                 
                $str = "SELECT status FROM master_sbbm WHERE no_sbbm='$no_sbbm'";
                $this->DB->setFieldTable(array('status'));
                $r=$this->DB->getRecord($str); 
                if (!isset($r[1]) ){                    
                    throw new Exception ("Nomor SBBM ($no_sbbm) tidak ada di database silahkan ganti dengan yang lain.");		
                }
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }
    }
    public function ubahSBBM($sender,$param) {
        if ($this->IsValid) {
            $no_sbbm=addslashes($this->txtNoSBBMBaru->Text);
            $str = "SELECT ms.idsbbm,ms.no_sbbm,ms.tanggal_sbbm,ms.idsumber_dana,idprogram,ms.idpenyalur,ps.nama_penyalur,ps.alamat,ps.kota,ms.no_faktur,ms.tanggal_faktur,ms.penerima,ms.status FROM master_sbbm ms LEFT JOIN penyalur_sbbm ps ON (ps.idsbbm=ms.idsbbm) WHERE ms.no_sbbm='$no_sbbm'";
            $this->DB->setFieldTable(array('idsbbm','no_sbbm','tanggal_sbbm','idsumber_dana','idprogram','idpenyalur','nama_penyalur','alamat','kota','no_faktur','tanggal_faktur','penerima','status'));
            $r=$this->DB->getRecord($str);
            $_SESSION['currentPageSBBMBaru']['datasbbm']=$r[1];
            $_SESSION['currentPageSBBMBaru']['datasbbm']['issaved']=true;
            $_SESSION['currentPageSBBMBaru']['datasbbm']['mode']=$r[1]['status']=='complete'?'ubah':'buat';
            $idsbbm=$r[1]['idsbbm'];
            $str = "SELECT idsbbm,idobat,kode_obat,no_reg,nama_obat,nama_merek,harga,idbentuk_sediaan,nama_bentuk,farmakologi,komposisi,kemasan,idprodusen,nama_produsen,no_batch,qty,tanggal_expire,status_obat,date_added,date_modified FROM detail_sbbm WHERE idsbbm='$idsbbm'";
            $this->DB->setFieldTable(array('idsbbm','idobat','kode_obat','no_reg','nama_obat','nama_merek','harga','idbentuk_sediaan','nama_bentuk','farmakologi','komposisi','kemasan','idprodusen','nama_produsen','no_batch','qty','tanggal_expire','status_obat','date_added','date_modified'));
            $r=$this->DB->getRecord($str);            
            $cart = array();
            while (list($k,$v)=each($r)) {
                $v['idcart']=$k;
                $cart[$k]=$v;
            }
            $_SESSION['currentPageSBBMBaru']['cart']=$cart;
            $this->redirect('mutasibarang.SBBMBaru',true);
        }
    }
    public function detailProcess() {
        $this->datasbbm = $_SESSION['currentPageSBBMBaru']['datasbbm'];              
        $this->cmbFilterProdusen->DataSource=$this->DMaster->getListProdusen ();
        $this->cmbFilterProdusen->Text=$this->datasbbm['idprodusen'];
        $this->cmbFilterProdusen->DataBind();
        if ($this->datasbbm['mode'] == 'buat') {
            $this->idProcess='add';            
            $this->cmbAddPenyalur->DataSource=$this->DMaster->getListPenyalur();
            $this->cmbAddPenyalur->DataBind();
            
            $this->cmbAddSumberDana->DataSource=$this->DMaster->getListSumberDana();            
            $this->cmbAddSumberDana->DataBind();
            
            $this->cmbAddProgram->DataSource=$this->DMaster->getListProgram();            
            $this->cmbAddProgram->DataBind();
            
            if ($this->datasbbm['issaved']) {                
                $this->cmbAddTanggalSBBM->Text=$this->TGL->tanggal('d-m-Y',$this->datasbbm['tanggal_sbbm']);
                $this->cmbAddSumberDana->Text=$this->datasbbm['idsumber_dana'];
                $this->cmbAddProgram->Text=$this->datasbbm['idprogram'];                
                $this->cmbAddPenyalur->Text=$this->datasbbm['idpenyalur'];
                $this->txtAddNoFaktur->Text=$this->datasbbm['no_faktur'];
                $this->hiddenno_faktur->Value=$this->datasbbm['no_faktur'];
                $this->cmbAddTanggalFaktur->Text=$this->TGL->tanggal('d-m-Y',$this->datasbbm['tanggal_faktur']);
                $this->txtAddPenerima->Text=$this->datasbbm['penerima'];;
            }            
            $this->populateData();
            $this->populateCart();
        }elseif ($this->datasbbm['mode'] == 'ubah') {
            $this->idProcess='edit';
            
            $this->cmbEditPenyalur->DataSource=$this->DMaster->getListPenyalur();
            $this->cmbEditPenyalur->DataBind();
            
            $this->cmbEditTanggalSBBM->Text=$this->TGL->tanggal('d-m-Y',$this->datasbbm['tanggal_sbbm']);    
            $tahunCMB = $this->TGL->tanggal('Y',$this->datasbbm['tanggal_sbbm'])+1;
            $this->cmbEditTanggalSBBM->FromYear=$_SESSION['awal_tahun_sistem'];
            $this->cmbEditTanggalSBBM->UpToYear=$tahunCMB;            
            $this->cmbEditPenyalur->Text=$this->datasbbm['idpenyalur'];
            $this->txtEditNoFaktur->Text=$this->datasbbm['no_faktur'];
            $this->hiddenno_faktur->Value=$this->datasbbm['no_faktur'];
            $this->cmbEditTanggalFaktur->Text=$this->TGL->tanggal('d-m-Y',$this->datasbbm['tanggal_faktur']);
            $this->cmbEditTanggalFaktur->UpToYear=$tahunCMB;
            $this->cmbEditTanggalFaktur->FromYear=$_SESSION['awal_tahun_sistem'];
            $this->txtEditPenerima->Text=$this->datasbbm['penerima'];;
        }        
    }
    public function renderCallback ($sender,$param) {
        $this->idProcess=$_SESSION['currentPageSBBMBaru']['datasbbm']['mode']=='buat'?'add':'edit';
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {     
		$_SESSION['currentPageSBBMBaru']['page_num']=$param->NewPageIndex;        
		$this->populateData($_SESSION['currentPageSBBMBaru']['search']);
	}
    public function searchRecord ($sender,$param) {
		$_SESSION['currentPageSBBMBaru']['search']=true;
        $this->populateData($_SESSION['currentPageSBBMBaru']['search']);
	}
    public function filterRecord ($sender,$param) {
		$_SESSION['currentPageSBBMBaru']['idprodusen']=$this->cmbFilterProdusen->Text;
        $this->populateData();
	}    
    public function addProductRepeater($sender,$param) {        
        if ($this->IsValid) {                        
            $idobat=$this->getDataKeyField($sender,$this->RepeaterS);            
            $dataobat=$this->Obat->getInfoMasterObat($idobat);            
            $obj=$sender->getNamingContainer();
            $qty=$obj->txtQTY->getText();            
            $no_batch=addslashes($obj->txtNoBatch->getText());            
            $tangal_expire = date('Y-m-d',$obj->cmbAddExpire->TimeStamp);
            $cart = $_SESSION['currentPageSBBMBaru']['cart'];
            $bool_exist=false;
            foreach ($cart as $k=>$v) {
                if ($idobat == $v['idobat'] && $tangal_expire == $v['tanggal_expire']) {
                    $bool_exist=true;
                    $idcart=$k;
                    break;
                }
            }
            if ($bool_exist) {                
                $_SESSION['currentPageSBBMBaru']['cart'][$idcart]['qty']=$qty;
                $_SESSION['currentPageSBBMBaru']['cart'][$idcart]['no_batch']=$no_batch;
                $_SESSION['currentPageSBBMBaru']['cart'][$idcart]['tanggal_expire']=$tangal_expire;
            }else{
                $idcart = count($cart)+1;
                $_SESSION['currentPageSBBMBaru']['cart'][$idcart]=array(
                                                            'idcart'=>$idcart,
                                                            'idobat'=>$idobat,
                                                            'kode_obat'=>$dataobat['kode_obat'],
                                                            'nama_obat'=>$dataobat['nama_obat'],
                                                            'harga'=>$dataobat['harga'],
                                                            'nama_bentuk'=>$dataobat['nama_bentuk'],
                                                            'kemasan'=>$dataobat['kemasan'],
                                                            'idprodusen'=>$dataobat['idprodusen'],
                                                            'nama_produsen'=>$dataobat['nama_produsen'],                                                                    
                                                            'qty'=>$qty,
                                                            'no_batch'=>$no_batch,
                                                            'tanggal_expire'=>$tangal_expire);
            }
            $this->redirect('mutasibarang.SBBMBaru',true);
        }
    }
    public function itemCreatedCart($sender,$param) {
        $item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {            
            $this->TGL = $this->getLogic('Penanggalan');
            SBBMBaru::$totalQTY += $item->DataItem['qty'];
            $harga=$item->DataItem['qty']*$item->DataItem['harga'];            
            SBBMBaru::$totalHARGA += $harga;
            $item->cmbAddExpire->Text=$this->TGL->tanggal('d-m-Y',$_SESSION['currentPageSBBMBaru']['cart'][$item->DataItem['idcart']]['tanggal_expire']);            
        }
    }
    protected function populateCart () {             
        $this->idProcess='add';
		$this->RepeaterCart->DataSource=$_SESSION['currentPageSBBMBaru']['cart'];
		$this->RepeaterCart->dataBind();             
	}    
    public function saveItem($sender,$param) {        
        if ($this->IsValid) {                        
            $idcart=$this->getDataKeyField($sender,$this->RepeaterCart);            
            $obj=$sender->getNamingContainer();
            $qty=$obj->txtQTY->getText();            
            $no_batch=addslashes($obj->txtNoBatch->getText());            
            $tangal_expire = date('Y-m-d',$obj->cmbAddExpire->TimeStamp);            
            $_SESSION['currentPageSBBMBaru']['cart'][$idcart]['qty']=$qty;
            $_SESSION['currentPageSBBMBaru']['cart'][$idcart]['no_batch']=$no_batch;
            $_SESSION['currentPageSBBMBaru']['cart'][$idcart]['tanggal_expire']=$tangal_expire;
            $this->redirect('mutasibarang.SBBMBaru',true);
        }
    }
    public function deleteItem($sender,$param) {
        $id=$this->getDataKeyField($sender,$this->RepeaterCart);
        unset($_SESSION['currentPageSBBMBaru']['cart'][$id]);        
        $this->populateCart();
        $this->populateData();
    }
    public function getQTYFromCart($idobat){
        $cart=$_SESSION['currentPageSBBMBaru']['cart'];
        if (isset($cart[$idobat])) {
            return $cart[$idobat]['qty'];
        }else{
            return 0;
        }
        
    }
    public function clearCart ($sender,$param) {
        $_SESSION['currentPageSBBMBaru']['cart']=array();
        $this->redirect('mutasibarang.SBBMBaru',true);
    }
    protected function populateData ($search=false) {     
        $this->idProcess=$_SESSION['currentPageSBBMBaru']['datasbbm']['mode']=='buat'?'add':'edit';
        $idprodusen=$_SESSION['currentPageSBBMBaru']['idprodusen'];
        $str_produsen=$idprodusen=='none' ?'':" WHERE idprodusen=$idprodusen";
        $str = "SELECT idobat,kode_obat,nama_obat,harga,nama_bentuk,kemasan,stock FROM master_obat$str_produsen";        
        if ($search) {            
            $txtsearch=$this->txtKriteria->Text;
            switch ($this->cmbKriteria->Text) {
                case 'kode' :
                    $cluasa=$idprodusen=='none' ?" WHERE kode_obat='$txtsearch'":" AND kode_obat='$txtsearch'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_obat $cluasa",'idobat');
                    $str = "$str $cluasa";
                break;
                case 'nama' :
                    $cluasa=$idprodusen=='none' ?" WHERE nama_obat LIKE '%$txtsearch%'" :" AND nama_obat LIKE '%$txtsearch%'";
                    $jumlah_baris=$this->DB->getCountRowsOfTable ("master_obat $cluasa",'idobat');
                    $str = "$str $cluasa";
                break;
            }
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ("master_obat$str_produsen",'idobat');		
        }		
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageSBBMBaru']['page_num'];
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageSBBMBaru']['page_num']=0;}
        $str = "$str ORDER BY nama_obat ASC LIMIT $offset,$limit";        
		$this->DB->setFieldTable(array('idobat','kode_obat','nama_obat','harga','nama_bentuk','kemasan','stock'));
		$r=$this->DB->getRecord($str,$offset+1);             
        
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();     
        $this->paginationInfo->Text=$this->getInfoPaging($this->RepeaterS);
	}
    public function viewRecord ($sender,$param) {                
        $id=$this->getDataKeyField($sender,$this->RepeaterS);   
        $dataobat = $this->Obat->getInfoMasterObat($id);        
        $this->setInfoObat($dataobat);
        $this->modalInfoObat->show();
    }
    public function checkNoFaktur ($sender,$param) {
		$this->idProcess=$sender->getId()=='addNoFaktur'?'add':'edit';
        $no_faktur=$param->Value;		
        if ($no_faktur != '') {
            try {   
                if ($this->hiddenno_faktur->Value!=$no_faktur) {
                    if ($this->DB->checkRecordIsExist('no_faktur','master_sbbm',$no_faktur)) {                                
                        throw new Exception ("Nomor Faktur ($no_faktur) sudah tidak tersedia silahkan ganti dengan yang lain.");		
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
            $datasbbm=$_SESSION['currentPageSBBMBaru']['datasbbm'];
            $idsbbm=$datasbbm['idsbbm'];            
            $tanggal_sbbm = date('Y-m-d',$this->cmbAddTanggalSBBM->TimeStamp);
            $idsumber_dana=$this->cmbAddSumberDana->Text;
            $sumber_dana=$this->DMaster->getNamaSumberDanaByID($idsumber_dana);
            $idprogram=$this->cmbAddProgram->Text;
            $nama_program=$this->DMaster->getNamaProgramByID($idprogram);
            $idpenyalur=$this->cmbAddPenyalur->Text;
            $nama_penyalur=$this->DMaster->getNamaPenyalurByID($idpenyalur);
            $no_faktur=addslashes($this->txtAddNoFaktur->Text);
            $tanggal_faktur = date('Y-m-d',$this->cmbAddTanggalFaktur->TimeStamp);
            $penerima=addslashes($this->txtAddPenerima->Text);
            
            $this->DB->query('BEGIN');
            $str = "UPDATE master_sbbm SET tanggal_sbbm='$tanggal_sbbm',idsumber_dana='$idsumber_dana',sumber_dana='$sumber_dana',idprogram=$idprogram,nama_program='$nama_program',idpenyalur=$idpenyalur,nama_penyalur='$nama_penyalur',no_faktur='$no_faktur',tanggal_faktur='$tanggal_faktur',penerima='$penerima',status='draft',date_modified=NOW() WHERE idsbbm=$idsbbm";
            if ($this->DB->updateRecord($str)) {    
                $datasbbm['issaved']=true;                                        
                
                $this->DB->deleteRecord("penyalur_sbbm WHERE idsbbm=$idsbbm");                
                $str = "INSERT INTO penyalur_sbbm (idsbbm,idpenyalur,nama_penyalur,alamat,kota,notelp,nohp,contactperson,email,web) SELECT $idsbbm,idpenyalur,nama_penyalur,alamat,kota,notelp,nohp,contactperson,email,web FROM penyalur WHERE idpenyalur=$idpenyalur";
                $this->DB->insertRecord($str);                                    

                $str = "SELECT nama_penyalur,alamat,kota FROM penyalur_sbbm WHERE idsbbm=$idsbbm";
                $this->DB->setFieldTable(array('nama_penyalur','alamat','kota'));
                $r=$this->DB->getRecord($str);

                $datasbbm['nama_penyalur']=$r[1]['nama_penyalur'];
                $datasbbm['alamat']=$r[1]['alamat'];
                $datasbbm['kota']=$r[1]['kota'];
                
                $datasbbm['tanggal_sbbm']=$tanggal_sbbm;
                $datasbbm['idsumber_dana']=$idsumber_dana;
                $datasbbm['sumber_dana']=$sumber_dana;
                $datasbbm['idprogram']=$idprogram;
                $datasbbm['nama_program']=$nama_program;
                $datasbbm['idpenyalur']=$idpenyalur;
                $datasbbm['no_faktur']=$no_faktur;
                $datasbbm['tanggal_faktur']=$tanggal_faktur;
                $datasbbm['penerima']=$penerima;
                $_SESSION['currentPageSBBMBaru']['datasbbm']=$datasbbm;
                $cart=$_SESSION['currentPageSBBMBaru']['cart'];
                if (count($cart) > 0) {
                    $this->DB->deleteRecord("detail_sbbm WHERE idsbbm=$idsbbm");
                    foreach ($cart as $v) {
                        $idobat=$v['idobat'];
                        $idprodusen=$v['idprodusen'];
                        $nama_produsen=$v['nama_produsen'];
                        $qty=$v['qty'];
                        $no_batch=$v['no_batch'];
                        $tanggal_expire=$v['tanggal_expire'];
                        $str = "INSERT INTO detail_sbbm (idprogram,idsbbm,idobat,kode_obat,no_reg,nama_obat,nama_merek,harga,idsatuan_obat,idgolongan,idbentuk_sediaan,nama_bentuk,farmakologi,komposisi,kemasan,idprodusen,nama_produsen,no_batch,qty,tanggal_expire,status_obat,date_added,date_modified) SELECT $idprogram,$idsbbm,idobat,kode_obat,no_reg,nama_obat,nama_merek,harga,idsatuan_obat,idgolongan,idbentuk_sediaan,nama_bentuk,farmakologi,komposisi,kemasan,'$idprodusen','$nama_produsen','$no_batch','$qty','$tanggal_expire',status_obat,NOW(),NOW() FROM master_obat WHERE idobat=$idobat";                                                
                        $this->DB->insertRecord($str);                        
                    }
                }
                $this->DB->query('COMMIT');
                $this->redirect('mutasibarang.SBBMBaru',true);
            }else{
                $this->DB->query('ROLLBACK');
            }
            
        }
    }
    public function checkOut ($sender,$param) {
        if ($this->IsValid) {           
            $datasbbm=$_SESSION['currentPageSBBMBaru']['datasbbm'];
            $idsbbm=$datasbbm['idsbbm'];
            $tanggal_sbbm = date('Y-m-d',$this->cmbAddTanggalSBBM->TimeStamp);
            $idsumber_dana=$this->cmbAddSumberDana->Text;
            $sumber_dana=$this->DMaster->getNamaSumberDanaByID($idsumber_dana);
            $idprogram=$this->cmbAddProgram->Text;
            $nama_program=$this->DMaster->getNamaProgramByID($idprogram);
            $idpenyalur=$this->cmbAddPenyalur->Text;
            $nama_penyalur=$this->DMaster->getNamaPenyalurByID($idpenyalur);
            $no_faktur=addslashes($this->txtAddNoFaktur->Text);
            $tanggal_faktur = date('Y-m-d',$this->cmbAddTanggalFaktur->TimeStamp);
            $penerima=addslashes($this->txtAddPenerima->Text);
            
            $this->DB->query('BEGIN');            
            $str = "UPDATE master_sbbm SET tanggal_sbbm='$tanggal_sbbm',idsumber_dana='$idsumber_dana',idprogram=$idprogram,nama_program='$nama_program',sumber_dana='$sumber_dana',idpenyalur=$idpenyalur,nama_penyalur='$nama_penyalur',no_faktur='$no_faktur',tanggal_faktur='$tanggal_faktur',penerima='$penerima',status='complete',date_modified=NOW() WHERE idsbbm=$idsbbm";
            if ($this->DB->updateRecord($str)) {
                $datasbbm['issaved']=true;                                        
                
                $this->DB->deleteRecord("penyalur_sbbm WHERE idsbbm=$idsbbm");                
                $str = "INSERT INTO penyalur_sbbm (idsbbm,idpenyalur,nama_penyalur,alamat,kota,notelp,nohp,contactperson,email,web) SELECT $idsbbm,idpenyalur,nama_penyalur,alamat,kota,notelp,nohp,contactperson,email,web FROM penyalur WHERE idpenyalur=$idpenyalur";
                $this->DB->insertRecord($str);                                    

                $str = "SELECT nama_penyalur,alamat,kota FROM penyalur_sbbm WHERE idsbbm=$idsbbm";
                $this->DB->setFieldTable(array('nama_penyalur','alamat','kota'));
                $r=$this->DB->getRecord($str);

                $datasbbm['nama_penyalur']=$r[1]['nama_penyalur'];
                $datasbbm['alamat']=$r[1]['alamat'];
                $datasbbm['kota']=$r[1]['kota'];
                
                $datasbbm['tanggal_sbbm']=$tanggal_sbbm;
                $datasbbm['idsumber_dana']=$idsumber_dana;
                $datasbbm['sumber_dana']=$sumber_dana;
                $datasbbm['idprogram']=$idprogram;
                $datasbbm['nama_program']=$nama_program;
                $datasbbm['idpenyalur']=$idpenyalur;
                $datasbbm['no_faktur']=$no_faktur;
                $datasbbm['tanggal_faktur']=$tanggal_faktur;
                $datasbbm['penerima']=$penerima;
                
                $no_sbbm = $datasbbm['no_sbbm'];                
                $tanggal_sbbm=$datasbbm['tanggal_sbbm'];                
                $bulan=$this->TGL->tanggal('m',$tanggal_sbbm);
                $tahun=$_SESSION['ta'];
                $cart=$_SESSION['currentPageSBBMBaru']['cart'];
                $this->DB->deleteRecord("detail_sbbm WHERE idsbbm=$idsbbm");
                
                $userid=$this->Pengguna->getDataUser('userid');
                $username=$this->Pengguna->getDataUser('username');
                $nama=$this->Pengguna->getDataUser('nama');
                foreach ($cart as $v) {
                    $idobat=$v['idobat'];
                    $idprodusen=$v['idprodusen'];
                    $nama_produsen=$v['nama_produsen'];
                    $qty=$v['qty'];
                    $no_batch=$v['no_batch'];
                    $tanggal_expire=$v['tanggal_expire'];
                    $harga=$v['harga'];                    
                    $barcode=strtoupper($idsumber_dana.$no_batch.substr(md5(uniqid(rand(), true)), 1, 3));
                    $str = "INSERT INTO detail_sbbm (idprogram,idsbbm,idobat,kode_obat,no_reg,nama_obat,nama_merek,harga,idsatuan_obat,idgolongan,idbentuk_sediaan,nama_bentuk,farmakologi,komposisi,kemasan,idprodusen,nama_produsen,no_batch,qty,tanggal_expire,barcode,status_obat,date_added,date_modified) SELECT $idprogram,$idsbbm,idobat,kode_obat,no_reg,nama_obat,nama_merek,$harga,idsatuan_obat,idgolongan,idbentuk_sediaan,nama_bentuk,farmakologi,komposisi,kemasan,'$idprodusen','$nama_produsen','$no_batch','$qty','$tanggal_expire','$barcode',status_obat,NOW(),NOW() FROM master_obat WHERE idobat=$idobat";                        
                    $this->DB->insertRecord($str);  
                    $str = "SELECT stock FROM master_obat WHERE idobat=$idobat";
                    $this->DB->setFieldTable(array('stock'));
                    $r=$this->DB->getRecord($str);
                    $stock=$r[1]['stock']+$qty;
                    $iddetail_sbbm=$this->DB->getLastInsertID ();
                    $values='';
                    for ($i=0;$i < $qty;$i++) {
                        if ($qty <= ($i+1)) {
                            $values="$values ($idobat,$idsbbm,$iddetail_sbbm,'$tanggal_sbbm','$bulan',$tahun,'$tanggal_expire','masuk',0,1,NOW(),NOW())";
                        }else {
                            $values="$values ($idobat,$idsbbm,$iddetail_sbbm,'$tanggal_sbbm','$bulan',$tahun,'$tanggal_expire','masuk',0,1,NOW(),NOW()),";
                        }
                    }
                    $str = "INSERT INTO kartu_stock (idobat,idsbbm,iddetail_sbbm,tanggal,bulan,tahun,tanggal_expire,mode,isopname,islocked,date_added,date_modified) VALUES $values";                         
                    $this->DB->insertRecord($str);
                    $str = "UPDATE master_obat SET stock=stock+$qty WHERE idobat=$idobat";
                    $this->DB->updateRecord($str);                      
                    $str = "INSERT INTO log_ks (idobat,idsbbm,iddetail_sbbm,tanggal,bulan,tahun,qty,sisa_stock,keterangan,mode,userid,username,nama,date_added) VALUES ($idobat,$idsbbm,$iddetail_sbbm,'$tanggal_sbbm','$bulan',$tahun,$qty,$stock,'Barang masuk dengan no.sbbm ($no_sbbm) sebanyak $qty','masuk',$userid,'$username','$nama',NOW())";
                    $this->DB->insertRecord($str);
                }                
                $_SESSION['currentPageDaftarSBBM']['datasbbm']=$datasbbm;
                $_SESSION['currentPageDaftarSBBM']['cart']=$cart;
                $this->DB->query('COMMIT');
                unset($_SESSION['currentPageSBBMBaru']['datasbbm']);
                unset($_SESSION['currentPageSBBMBaru']['cart']);
                $this->redirect('mutasibarang.DaftarSBBM',true);
            }else{
                $this->DB->query('ROLLBACK');
            }            
        }
    }
    public function updateData ($sender,$param) {
        if ($this->IsValid) {      
            $datasbbm=$_SESSION['currentPageSBBMBaru']['datasbbm'];
            $idsbbm=$datasbbm['idsbbm'];            
            $tanggal_sbbm = date('Y-m-d',$this->cmbEditTanggalSBBM->TimeStamp);            
            $tahun=date('Y',$this->cmbEditTanggalSBBM->TimeStamp);            
            $idpenyalur=$this->cmbEditPenyalur->Text;
            $nama_penyalur=$this->DMaster->getNamaPenyalurByID($idpenyalur);
            $no_faktur=addslashes($this->txtEditNoFaktur->Text);
            $tanggal_faktur = date('Y-m-d',$this->cmbEditTanggalFaktur->TimeStamp);
            $penerima=addslashes($this->txtEditPenerima->Text);
            
            $str = "UPDATE master_sbbm SET tanggal_sbbm='$tanggal_sbbm',idpenyalur=$idpenyalur,nama_penyalur='$nama_penyalur',no_faktur='$no_faktur',tanggal_faktur='$tanggal_faktur',penerima='$penerima',tahun=$tahun,date_modified=NOW() WHERE idsbbm=$idsbbm";
            $this->DB->updateRecord($str);
            
            unset($_SESSION['currentPageSBBMBaru']['datasbbm']);
            unset($_SESSION['currentPageSBBMBaru']['cart']);
            $this->redirect('mutasibarang.DaftarSBBM',true);
        }
    }
    public function batalSBBM ($sender,$param) {
		$id=$_SESSION['currentPageSBBMBaru']['datasbbm']['idsbbm'];        
        $this->DB->deleteRecord("master_sbbm WHERE idsbbm=$id");
        
        unset($_SESSION['currentPageSBBMBaru']['datasbbm']);
        unset($_SESSION['currentPageSBBMBaru']['cart']);
        $this->redirect('mutasibarang.SBBMBaru',true);
	}
    public function closeSBBM ($sender,$param) {
        unset($_SESSION['currentPageSBBMBaru']['datasbbm']);
        unset($_SESSION['currentPageSBBMBaru']['cart']);
        $this->redirect('mutasibarang.SBBMBaru',true);
    }
    
}
?>