<?php
/**
*
* digunakan untuk memproses tanggal
*
*/
prado::using ('Application.logic.Logic_Global');
class Logic_Penanggalan extends Logic_Global {
    /*
     * nama hari dalam bahasa ingris
     */
    private $dayName = array('Sunday', 'Monday', 'Tuesday','Wednesday', 'Thursday', 'Friday', 'Saturday');
    /*
     * nama hari dalam bahasa indonesia
     */
    private $namaHari = array('Minggu', 'Senin', 'Selasa','Rabu', 'Kamis', 'Jumat', 'Sabtu');
    /*
     * nama bulan dalam bahasa ingris
     */
    private $monthName = array('January', 'February', 'March', 'April', 'May','June', 'July', 'August', 'September', 'October', 'November' , 'December');
    /*
     * nama bulan dalam bahasa indonesia
     */
    private $namaBulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei','Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    /**
     * digunakan untuk memformat tanggal
     * @param type $format
     * @param type $date
     * @return type date
     */
    public function tanggal($format, $date=null) {
        if (is_object($date)){            
            $tgl=$date;
        }else {
            if ($date === null) {
                $tgl = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
            }else {
                $tgl = new DateTime($date, new DateTimeZone('Asia/Jakarta'));
            }
        }		
        $result = str_replace($this->dayName, $this->namaHari, $tgl->format ($format));
        return str_replace($this->monthName, $this->namaBulan, $result);
	}   
    /**
     * digunakan untuk mendapatkan tanggal pada tahun depan
     */
    public function getDateNextYear ($currentdate,$sumyear,$format) {
        $date = new DateTime($currentdate,new DateTimeZone('Asia/Jakarta'));
        $date->add(new DateInterval("P{$sumyear}Y"));        
        $result = str_replace($this->dayName, $this->namaHari, $date->format ($format));
        return str_replace($this->monthName, $this->namaBulan, $result);        
    }
    /**
	* digunakan untuk mengetahui perbedaan waktu ke waktu
	*/
	public function relativeTimeBasic($date1,$date2,$mode='all')	{		      
        $datetime1 = new DateTime($date1, new DateTimeZone('Asia/Jakarta'));
        $datetime2 = new DateTime($date2, new DateTimeZone('Asia/Jakarta'));
        $interval = $datetime1->diff($datetime2);   
		$tanggal=array();
		switch ($mode) {
			case 'tahun' :
				$tanggal=$interval->y;
			break;						
			case 'bulan' :
				$tanggal=$interval->format("%m");
			break;
			case 'tahunbulan' :
				$tanggal=array('tahun'=>$interval->y,'bulan'=>$interval->m);
			break;
		}        
        return $tanggal;
	}
    /**
     * digunakan untuk mendapatkan bulan dan tahun sebelumnya
     * @return type date
     */
    public function getMonthAndYearBefore ($tanggal_saat_ini) {
          $month = $this->tanggal('m',$tanggal_saat_ini);
          $year = $this->tanggal('Y',$tanggal_saat_ini);
          $last_month = $month-1%12;
          if ($last_month==0) {
              $lastmonthyear=($year-1).'-12';
          }else{
              $lastmonth=$last_month > 9 ? $last_month : "0$last_month";
              $lastmonthyear="$year-$lastmonth";
          }
          return $lastmonthyear;
    }
}
?>