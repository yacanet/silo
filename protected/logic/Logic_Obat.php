<?php
/**
*
* digunakan untuk memproses setup aplikasi
*
*/
prado::using ('Application.logic.Logic_Global');
class Logic_Obat extends Logic_Global {     
    /**
	* object setup;	
	*/
	public $setup;
    /**
	* object DMaster;	
	*/
    public $DMaster;
	public function __construct ($db) {
		parent::__construct ($db);	  
        $this->setup = $this->getLogic ('Setup');        
        $this->DMaster = $this->getLogic ('DMaster');
	}   
    /**
	* casting ke integer	
	*/
	public function toInteger ($stringNumeric) {
		return str_replace('.','',$stringNumeric);
	}
    /**
	* Untuk mendapatkan uang dalam format rupiah
	* @param angka	
	* @return string dalam rupiah
	*/
	public function toRupiah($angka,$tanpa_rp=true)  {
		if ($angka == '') {
			$angka=0;
		}
		$rupiah='';
		$rp=strlen($angka);
		while ($rp>3){
			$rupiah = ".". substr($angka,-3). $rupiah;
			$s=strlen($angka) - 3;
			$angka=substr($angka,0,$s);
			$rp=strlen($angka);
		}
		if ($tanpa_rp) {
			$rupiah = $angka . $rupiah;
		}else {
			$rupiah = "Rp. " . $angka . $rupiah;
		}
		return $rupiah;
	}
    /**
     * digunakan untuk mendapatkan info master obat
    */
    public function getInfoMasterObat ($idobat) {
        $str = "SELECT mo.idobat,mo.no_reg,mo.kode_obat,mo.nama_obat,mo.nama_merek,harga,idsatuan_obat,idgolongan,mo.idbentuk_sediaan,mo.nama_bentuk,mo.farmakologi,mo.kemasan,mo.komposisi,mo.idprodusen,p.nama_produsen,mo.stock,mo.min_stock,mo.max_stock,mo.status_obat,mo.date_added,mo.date_modified,mo.enabled FROM master_obat mo LEFT JOIN produsen p  ON (mo.idprodusen=p.idprodusen) WHERE idobat=$idobat";
        $this->db->setFieldTable(array('idobat','no_reg','kode_obat','nama_obat','nama_merek','harga','idsatuan_obat','idgolongan','idbentuk_sediaan','nama_bentuk','farmakologi','kemasan','komposisi','idprodusen','nama_produsen','stock','min_stock','max_stock','status_obat','date_added','date_modified','enabled'));
        $r=$this->db->getRecord($str);
        $dataobat=array();
        if (isset($r[1])) {
            $dataobat=$r[1];
            $dataobat['farmakologi']=json_decode($dataobat['farmakologi'],true);
        }
        return $dataobat;
    }   
    /**
     * digunakan untuk mendapatkan info master obat puskesmas
    */
    public function getInfoMasterObatPuskesmas ($idobat_puskesmas) {
        $str = "SELECT mo.idobat_puskesmas,mo.idobat,mo.no_reg,mo.kode_obat,mo.nama_obat,mo.nama_merek,harga,idsatuan_obat,idgolongan,mo.idbentuk_sediaan,mo.nama_bentuk,mo.farmakologi,mo.kemasan,mo.komposisi,mo.idprodusen,p.nama_produsen,mo.stock,mo.min_stock,mo.max_stock,mo.status_obat FROM master_obat_puskesmas mo LEFT JOIN produsen p  ON (mo.idprodusen=p.idprodusen) WHERE idobat_puskesmas=$idobat_puskesmas";
        $this->db->setFieldTable(array('idobat_puskesmas','idobat','no_reg','kode_obat','nama_obat','nama_merek','harga','idsatuan_obat','idgolongan','idbentuk_sediaan','nama_bentuk','farmakologi','kemasan','komposisi','idprodusen','nama_produsen','stock','min_stock','max_stock','status_obat'));
        $r=$this->db->getRecord($str);
        $dataobat=array();
        if (isset($r[1])) {
            $dataobat=$r[1];
            $dataobat['farmakologi']=json_decode($dataobat['farmakologi'],true);
        }
        return $dataobat;
    }
    /**
     * digunakan untuk mengetahui jumlah stock obat
     * 
     */
    public function getJumlahStockObat () {                
        $jumlah=$this->db->getSumRowsOfTable ('stock','master_obat');                
        return $jumlah;
    }
    /**
     * digunakan untuk mengetahui jumlah stock obat
     * 
     */
    public function getJumlahStockObatPuskesmas ($idpuskesmas) {                
        $jumlah=$this->db->getSumRowsOfTable ("stock","master_obat_puskesmas WHERE idpuskesmas=$idpuskesmas");                
        return $jumlah;
    }
    /**
     * digunakan untuk mengetahui jumlah master obat
     * 
     */
    public function getJumlahMasterObat () {                
        $jumlah=$this->db->getCountRowsOfTable ('master_obat','idobat');                
        return $jumlah;
    }
    /*
     * digunakan untuk mendapatkan stock obat dengan harga akhir pada bulan dan tahun tertentu
     */
    public function getFirstStock($idobat,$monthyear,$harga,$groupby=null) {   
        $tahun=explode('-',$monthyear);
        $tahun=$tahun[0];
        switch ($groupby) {
            case 'sumberdana' :
                $str = "SELECT idsumber_dana,SUM(qty) AS jumlah FROM master_sbbm msb,detail_sbbm dsb WHERE msb.idsbbm=dsb.idsbbm AND dsb.idobat=$idobat AND dsb.harga=$harga AND msb.tahun=$tahun AND status='complete' AND DATE_FORMAT(msb.tanggal_sbbm,'%Y-%m')<='$monthyear' GROUP BY idsumber_dana ORDER BY idsumber_dana ASC";                
                $this->db->setFieldTable(array('idsumber_dana','jumlah'));
                $r=$this->db->getRecord($str);  
                $awalstock=$this->DMaster->removeIdFromArray($this->DMaster->getListSumberDana (),'none');
                
                $awalstock_penerimaan=$awalstock;
                $awalstock_pengeluaran=$awalstock;
                $awalstock_penerimaan[1]=0;
                $awalstock_penerimaan[2]=0;
                $awalstock_penerimaan[3]=0;
                $awalstock_penerimaan[4]=0;
                foreach ($r as $k=>$v) {
                    $awalstock_penerimaan[$v['idsumber_dana']]=$v['jumlah'];
                }
                $str = "SELECT msb.idsumber_dana,COUNT(ks.idkartu_stock) AS jumlah FROM master_sbbk msk,detail_sbbk dsb,kartu_stock ks,master_sbbm msb WHERE msk.idsbbk=dsb.idsbbk AND ks.idobat=$idobat AND ks.iddetail_sbbk=dsb.iddetail_sbbk AND ks.idsbbm=msb.idsbbm AND dsb.harga=$harga AND msb.tahun=$tahun AND DATE_FORMAT(msk.tanggal_sbbk,'%Y-%m')<='$monthyear' AND ks.isdestroyed=0 GROUP BY msb.idsumber_dana ORDER BY msb.idsumber_dana ASC";                
                $this->db->setFieldTable(array('idsumber_dana','jumlah'));
                $r=$this->db->getRecord($str);  
                $awalstock_pengeluaran[1]=0;
                $awalstock_pengeluaran[2]=0;
                $awalstock_pengeluaran[3]=0;
                $awalstock_pengeluaran[4]=0;
                foreach ($r as $k=>$v) {
                    $awalstock_pengeluaran[$v['idsumber_dana']]=$v['jumlah'];
                }         
                $awalstock[1]=0;
                $awalstock[2]=0;
                $awalstock[3]=0;
                $awalstock[4]=0;
                for ($i=1;$i<=4;$i++) {
                    $awalstock[$i]=($awalstock_pengeluaran[$i] < $awalstock_penerimaan[$i]) ? $awalstock_penerimaan[$i] - $awalstock_pengeluaran[$i]:0;                    
                }                
            break;
            case 'sumberdanapuskesmas' :
                $str = "SELECT idsumber_dana_gudang,SUM(qty) AS jumlah FROM master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsb WHERE msb.idsbbm_puskesmas=dsb.idsbbm_puskesmas AND dsb.idobat_puskesmas=$idobat AND dsb.harga=$harga AND status_puskesmas='complete' AND msb.tahun_puskesmas=$tahun AND DATE_FORMAT(msb.tanggal_sbbm_puskesmas,'%Y-%m')<='$monthyear' GROUP BY idsumber_dana_gudang ORDER BY idsumber_dana_gudang ASC";                
                $this->db->setFieldTable(array('idsumber_dana_gudang','jumlah'));
                $r=$this->db->getRecord($str);  
                $awalstock=$this->DMaster->removeIdFromArray($this->DMaster->getListSumberDana (),'none');
                
                $awalstock_penerimaan=$awalstock;
                $awalstock_pengeluaran=$awalstock;
                $awalstock_penerimaan[1]=0;
                $awalstock_penerimaan[2]=0;
                $awalstock_penerimaan[3]=0;
                $awalstock_penerimaan[4]=0;
                foreach ($r as $k=>$v) {
                    $awalstock_penerimaan[$v['idsumber_dana_gudang']]=$v['jumlah'];
                }
                $str = "SELECT dsk.idsumber_dana_gudang,COUNT(ks.idkartu_stock_puskesmas) AS jumlah FROM master_sbbk_puskesmas msk,detail_sbbk_puskesmas dsb,kartu_stock_puskesmas ks,master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsk WHERE msk.idsbbk_puskesmas=dsb.idsbbk_puskesmas AND ks.idobat_puskesmas=$idobat AND ks.iddetail_sbbk_puskesmas=dsb.iddetail_sbbk_puskesmas AND ks.idsbbm_puskesmas=msb.idsbbm_puskesmas AND ks.iddetail_sbbm_puskesmas=dsk.iddetail_sbbm_puskesmas AND dsb.harga=$harga AND msb.tahun_puskesmas=$tahun AND DATE_FORMAT(msk.tanggal_sbbk_puskesmas,'%Y-%m')<='$monthyear' AND ks.isdestroyed=0 GROUP BY dsk.idsumber_dana_gudang ORDER BY dsk.idsumber_dana_gudang ASC";                
                $this->db->setFieldTable(array('idsumber_dana_gudang','jumlah'));
                $r=$this->db->getRecord($str);  
                $awalstock_pengeluaran[1]=0;
                $awalstock_pengeluaran[2]=0;
                $awalstock_pengeluaran[3]=0;
                $awalstock_pengeluaran[4]=0;
                foreach ($r as $k=>$v) {
                    $awalstock_pengeluaran[$v['idsumber_dana_gudang']]=$v['jumlah'];
                }         
                $awalstock[1]=0;
                $awalstock[2]=0;
                $awalstock[3]=0;
                $awalstock[4]=0;
                for ($i=1;$i<=4;$i++) {
                    $awalstock[$i]=($awalstock_pengeluaran[$i] < $awalstock_penerimaan[$i]) ? $awalstock_penerimaan[$i] - $awalstock_pengeluaran[$i]:0;                    
                }                
            break;
            case 'defaultpuskesmas' :
                $str_penerimaan="master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsb WHERE msb.idsbbm_puskesmas=dsb.idsbbm_puskesmas AND dsb.idobat_puskesmas=$idobat AND harga=$harga AND msb.tahun_puskesmas=$tahun AND status_puskesmas='complete' AND DATE_FORMAT(msb.tanggal_sbbm_puskesmas,'%Y-%m')<='$monthyear'";                
                $penerimaan=$this->db->getSumRowsOfTable('qty',$str_penerimaan);                                                            
                $pengeluaran=$this->db->getSumRowsOfTable('pemberian_puskesmas',"master_sbbk_puskesmas msk,detail_sbbk_puskesmas dsk WHERE msk.idsbbk_puskesmas=dsk.idsbbk_puskesmas AND dsk.idobat_puskesmas=$idobat AND harga=$harga AND msk.tahun_puskesmas=$tahun AND  DATE_FORMAT(msk.tanggal_sbbk_puskesmas,'%Y-%m')='$monthyear'");            
                $awalstock=($pengeluaran < $penerimaan) ? $penerimaan - $pengeluaran:0;
            break;
            case 'defaultpuskesmasdinas' :
                $str_penerimaan="master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsb WHERE msb.idsbbm_puskesmas=dsb.idsbbm_puskesmas AND dsb.idobat_puskesmas=$idobat AND harga=$harga AND msb.tahun_puskesmas=$tahun AND status_puskesmas='complete' AND DATE_FORMAT(msb.tanggal_sbbm_puskesmas,'%Y-%m')<='$monthyear'";                
                $penerimaan=$this->db->getSumRowsOfTable('qty',$str_penerimaan);                                                                            
                $pengeluaran=$this->db->getCountRowsOfTable("detail_sbbk_puskesmas dsk,kartu_stock_puskesmas_dinas kspd WHERE kspd.iddetail_sbbk_puskesmas=dsk.iddetail_sbbk_puskesmas AND dsk.idobat_puskesmas=$idobat AND harga=$harga AND DATE_FORMAT(kspd.tanggal_puskesmas,'%Y-%m')='$monthyear'",'kspd.idkartu_stock_puskesmas');
                $awalstock=($pengeluaran < $penerimaan) ? $penerimaan - $pengeluaran:0;                
            break;
            default :
                $penerimaan=$this->db->getSumRowsOfTable('qty',"master_sbbm msb,detail_sbbm dsb WHERE msb.idsbbm=dsb.idsbbm AND dsb.idobat=$idobat AND harga=$harga AND msb.tahun=$tahun AND status='complete' AND DATE_FORMAT(msb.tanggal_sbbm,'%Y-%m')<='$monthyear'");            
                $pengeluaran=$this->db->getSumRowsOfTable('pemberian',"master_sbbk msk,detail_sbbk dsk WHERE msk.idsbbk=dsk.idsbbk AND dsk.idobat=$idobat AND harga=$harga AND msk.tahun=$tahun AND msk.status='complete' AND  DATE_FORMAT(msk.tanggal_sbbk,'%Y-%m')='$monthyear'");            
                $awalstock=($pengeluaran < $penerimaan) ? $penerimaan - $pengeluaran:0;
        }
        return $awalstock;                    
    }
    /*
     * digunakan untuk mendapatkan stock obat per semester
     */
    public function getFirstStockSemester($idobat,$ta,$semester,$harga,$groupby=null) {   
        $tahun=$semester == 1?$ta-1:$ta;
        if ($semester == 1) {            
            $str_penerimaan = " AND msb.tanggal_sbbm <= '$tahun-12-31'";        
            $str_pengeluaran=" AND msk.tanggal_sbbk <= '$tahun-12-31'";        
        }else {
            $str_penerimaan = " AND msb.tanggal_sbbm < '$tahun-07-01'";        
            $str_pengeluaran=" AND msk.tanggal_sbbk < '$tahun-07-01'";        
        }        
        
        switch ($groupby) {
            case 'sumberdana' :
                $str = "SELECT idsumber_dana,SUM(qty) AS jumlah FROM master_sbbm msb,detail_sbbm dsb WHERE msb.idsbbm=dsb.idsbbm AND dsb.idobat=$idobat AND dsb.harga=$harga AND status='complete' $str_penerimaan GROUP BY idsumber_dana ORDER BY idsumber_dana ASC";                
                $this->db->setFieldTable(array('idsumber_dana','jumlah'));
                $r=$this->db->getRecord($str);  
                $awalstock=$this->DMaster->removeIdFromArray($this->DMaster->getListSumberDana (),'none');
                
                $awalstock_penerimaan=$awalstock;
                $awalstock_pengeluaran=$awalstock;
                $awalstock_penerimaan[1]=0;
                $awalstock_penerimaan[2]=0;
                $awalstock_penerimaan[3]=0;
                $awalstock_penerimaan[4]=0;
                foreach ($r as $k=>$v) {
                    $awalstock_penerimaan[$v['idsumber_dana']]=$v['jumlah'];
                }
                $str = "SELECT msb.idsumber_dana,COUNT(ks.idkartu_stock) AS jumlah FROM master_sbbk msk,detail_sbbk dsb,kartu_stock ks,master_sbbm msb WHERE msk.idsbbk=dsb.idsbbk AND ks.idobat=$idobat AND ks.iddetail_sbbk=dsb.iddetail_sbbk AND ks.idsbbm=msb.idsbbm AND dsb.harga=$harga $str_pengeluaran AND ks.isdestroyed=0 GROUP BY msb.idsumber_dana ORDER BY msb.idsumber_dana ASC";                
                $this->db->setFieldTable(array('idsumber_dana','jumlah'));
                $r=$this->db->getRecord($str);  
                $awalstock_pengeluaran[1]=0;
                $awalstock_pengeluaran[2]=0;
                $awalstock_pengeluaran[3]=0;
                $awalstock_pengeluaran[4]=0;
                foreach ($r as $k=>$v) {
                    $awalstock_pengeluaran[$v['idsumber_dana']]=$v['jumlah'];
                }         
                $awalstock[1]=0;
                $awalstock[2]=0;
                $awalstock[3]=0;
                $awalstock[4]=0;
                for ($i=1;$i<=4;$i++) {
                    $awalstock[$i]=($awalstock_pengeluaran[$i] < $awalstock_penerimaan[$i]) ? $awalstock_penerimaan[$i] - $awalstock_pengeluaran[$i]:0;                    
                }
            break;
            default :
                $penerimaan=$this->db->getSumRowsOfTable('qty',"master_sbbm msb,detail_sbbm dsb WHERE msb.idsbbm=dsb.idsbbm AND dsb.idobat=$idobat AND harga=$harga AND status='complete' $str_penerimaan");                    
                $pengeluaran=$this->db->getSumRowsOfTable('pemberian',"master_sbbk msk,detail_sbbk dsk WHERE msk.idsbbk=dsk.idsbbk AND dsk.idobat=$idobat AND harga=$harga AND msk.status='complete' $str_pengeluaran");            
                $awalstock=($pengeluaran < $penerimaan) ? $penerimaan - $pengeluaran:0;
        }
        return $awalstock;
    }
    /*
     * digunakan untuk mendapatkan stock obat dengan harga akhir pada tahun tertentu
     */
    public function getFirstStockTahunan($idobat,$year,$harga,$groupby=nulll) {        
        switch ($groupby) {
            case 'sumberdana' :
                $str = "SELECT idsumber_dana,SUM(qty) AS jumlah FROM master_sbbm msb,detail_sbbm dsb WHERE msb.idsbbm=dsb.idsbbm AND dsb.idobat=$idobat AND harga=$harga AND DATE_FORMAT(msb.tanggal_sbbm,'%Y')='$year' GROUP BY idsumber_dana ORDER BY idsumber_dana ASC";                                
                $this->db->setFieldTable(array('idsumber_dana','jumlah'));
                $r=$this->db->getRecord($str);  
                $awalstock=$this->DMaster->removeIdFromArray($this->DMaster->getListSumberDana (),'none');
                
                $awalstock_penerimaan=$awalstock;
                $awalstock_pengeluaran=$awalstock;
                $awalstock_penerimaan[1]=0;
                $awalstock_penerimaan[2]=0;
                $awalstock_penerimaan[3]=0;
                $awalstock_penerimaan[4]=0;
                foreach ($r as $k=>$v) {
                    $awalstock_penerimaan[$v['idsumber_dana']]=$v['jumlah'];
                }
                $str = "SELECT msb.idsumber_dana,COUNT(ks.idkartu_stock) AS jumlah FROM master_sbbk msk,detail_sbbk dsb,kartu_stock ks,master_sbbm msb WHERE msk.idsbbk=dsb.idsbbk AND ks.idobat=$idobat AND ks.iddetail_sbbk=dsb.iddetail_sbbk AND ks.idsbbm=msb.idsbbm AND dsb.harga=$harga AND DATE_FORMAT(msk.tanggal_sbbk,'%Y')='$year' AND ks.isdestroyed=0 GROUP BY msb.idsumber_dana ORDER BY msb.idsumber_dana ASC";                
                $this->db->setFieldTable(array('idsumber_dana','jumlah'));
                $r=$this->db->getRecord($str);  
                $awalstock_pengeluaran[1]=0;
                $awalstock_pengeluaran[2]=0;
                $awalstock_pengeluaran[3]=0;
                $awalstock_pengeluaran[4]=0;
                foreach ($r as $k=>$v) {
                    $awalstock_pengeluaran[$v['idsumber_dana']]=$v['jumlah'];
                }         
                $awalstock[1]=0;
                $awalstock[2]=0;
                $awalstock[3]=0;
                $awalstock[4]=0;
                for ($i=1;$i<=4;$i++) {
                    $awalstock[$i]=($awalstock_pengeluaran[$i] < $awalstock_penerimaan[$i]) ? $awalstock_penerimaan[$i] - $awalstock_pengeluaran[$i]:0;                    
                }                
            break;
            default :
                $penerimaan=$this->db->getSumRowsOfTable('qty',"master_sbbm msb,detail_sbbm dsb WHERE msb.idsbbm=dsb.idsbbm AND dsb.idobat=$idobat AND harga=$harga AND DATE_FORMAT(msb.tanggal_sbbm,'%Y')='$year'");            
                $pengeluaran=$this->db->getSumRowsOfTable('pemberian',"master_sbbk msk,detail_sbbk dsk WHERE msk.idsbbk=dsk.idsbbk AND dsk.idobat=$idobat AND harga=$harga AND DATE_FORMAT(msk.tanggal_sbbk,'%Y')='$year'");            
                $awalstock=($pengeluaran < $penerimaan) ? $penerimaan - $pengeluaran:0;
        }
        return $awalstock;                    
    }
    /*
     * digunakan untuk mendapatkan stock akhir pada bulan dan tahun tertentu
     */
    public function getLastStockFromStockCardPuskesmas($idobat_puskesmas,$monthyear) {
        $tanggal = explode('-',$monthyear);
        $tahun=$tanggal[0];
        $bulan=$tanggal[1];
        $str = "SELECT sisa_stock_puskesmas FROM log_ks_puskesmas ksp,(SELECT MAX(idlog_puskesmas) AS idlog FROM log_ks_puskesmas WHERE bulan_puskesmas='$bulan' AND tahun_puskesmas=$tahun AND idobat_puskesmas=$idobat_puskesmas) AS ksp2 WHERE ksp.idlog_puskesmas=ksp2.idlog";
        $this->db->setFieldTable(array('sisa_stock_puskesmas'));
        $r=$this->db->getRecord($str);        
        if (isset($r[1])) 
            return $r[1]['sisa_stock_puskesmas'];
        else
            return 0;
    }
    /*
     * digunakan untuk mendapatkan jumlah penerimaan dan pengeluaran pada bulan dan tahun tertentu
     */
    public function getTotalKeluarMasukFromStockCardPuskesmas($idobat_puskesmas,$monthyear,$table) {
        $tanggal = explode('-',$monthyear);
        $tahun=$tanggal[0];
        $bulan=$tanggal[1];
        $str = "SELECT mode_puskesmas,COUNT(idkartu_stock_puskesmas) AS qty FROM $table WHERE bulan_puskesmas='$bulan' AND tahun_puskesmas=$tahun AND idobat_puskesmas=$idobat_puskesmas AND isdestroyed=0 GROUP BY mode_puskesmas ASC";                
        $this->db->setFieldTable(array('mode_puskesmas','qty'));
        $r=$this->db->getRecord($str);            
        $masuk=0;
        $keluar=0;
        foreach ($r as $v){
            switch ($v['mode_puskesmas']) {
                case 'masuk' :
                    $masuk = $v['qty'];
                break;
                case 'keluar' :
                    $keluar = $v['qty'];
                break;
            }
        }
        $result=array('masuk'=>$masuk,'keluar'=>$keluar);
        return $result;
    }
    /**
     * digunakan untuk memperoleh jumlah permintaan obat
     */
    public function getJumlahLPO ($mode='all',$idpuskesmas=null) {
        $ta=$_SESSION['ta'];
        $str_idppuskesmas=$idpuskesmas==null?'':" AND idpuskesmas=$idpuskesmas";        
        $str = "SELECT response_lpo, COUNT(idlpo) AS jumlah_lpo FROM master_lpo WHERE tahun=$ta AND status='complete'$str_idppuskesmas GROUP BY response_lpo";                
        $this->db->setFieldTable(array('response_lpo','jumlah_lpo'));
        $r=$this->db->getRecord($str);        
        $result=0;
        while (list($k,$v)=each($r)) {
            $status=$v['response_lpo'];
            switch($mode) {
                case 'all' :
                    $result+=$v['jumlah_lpo'];
                break;
                case 'allwithoutcomplete' :
                    if ($status  != 5) {
                        $result+=$v['jumlah_lpo'];
                    }
                break;
                case 'array' :
                    $result[$status]=$v['jumlah_lpo'];
                break;
            }
            
        }
        return $result;            
    }
    /**
     * digunakan untuk memperoleh jumlah distribusi obat
     */
    public function getJumlahDistribusiObat ($tahun,$idpuskesmas=null) {
        if ($idpuskesmas == null) {
            $jumlah=$this->db->getCountRowsOfTable("kartu_stock WHERE mode='keluar' AND tahun=$tahun AND isdestroyed=0","idkartu_stock");
        }else{
            $jumlah=$this->db->getCountRowsOfTable("kartu_stock_puskesmas WHERE mode_puskesmas='keluar' AND tahun_puskesmas=$tahun AND isdestroyed=0","idkartu_stock_puskesmas");
        }
        return $jumlah;
    }
    /**
     * digunakan untuk memperoleh jumlah obat yang expire
     */
    public function getJumlahObatExpire () {
        $str = "SELECT COUNT(idobat) AS jumlah FROM kartu_stock WHERE isopname=0 AND mode='masuk' AND CURRENT_DATE > tanggal_expire AND isdestroyed=0";
        $this->db->setFieldTable(array('jumlah'));
        $r=$this->db->getRecord($str);  
        return $r[1]['jumlah'];        
    }
    /**
     * digunakan untuk memperoleh jumlah obat yang expire yang ada dipuskesmas
     */
    public function getJumlahObatExpirePuskesmas ($idpuskesmas) {        
        $str = "SELECT COUNT(idobat) AS jumlah FROM kartu_stock_puskesmas WHERE idpuskesmas=$idpuskesmas AND mode_puskesmas='masuk' AND CURRENT_DATE > tanggal_expire_puskesmas AND isdestroyed=0";
        $this->db->setFieldTable(array('jumlah'));
        $r=$this->db->getRecord($str);  
        return $r[1]['jumlah'];        
    }
}
?> 