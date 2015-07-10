<?php
class UserManager extends TAuthManager {
	/**
	* Obj DB
	*/
	private $db;		
	/**
	* Username
	*/
	private $username;				
	/**
	* page
	*/
	private $page;
	/**
	* data user
	*/
	private $dataUser=array('data_user'=>array(),'hak_akses'=>array());
	
	public function __construct () {
		$this->db = $this->Application->getModule('db')->getLink();						
	}
		
	/**
	* digunakan untuk mengeset username serta mensplit username dan page	
	*/
	public function setUser ($username) {
		$username = explode('/',$username);
		$this->username=$username[0];
		$this->page=$username[1];				
	}    
	/**
	* get roles username	
	*/
	public function getDataUser () {				
		$username=$this->username;		
        $str = "SELECT u.userid,u.idpuskesmas,u.username,u.nip,u.nama,u.mobile_phone,u.email,u.page,u.isdeleted,foto FROM user u WHERE username='$username' AND active=1";
        $this->db->setFieldTable (array('userid','idpuskesmas','username','userpassword','salt','nip','nama','mobile_phone','email','page','isdeleted','foto'));							
        $r = $this->db->getRecord($str);			
        $dataUser=$r[1];
        switch ($dataUser['page']) {
            case 'ad' :
            case 'sp' :
                $idpuskesmas=$dataUser['idpuskesmas'];
                $str = "SELECT nama_puskesmas,alamat,notelpfax,p.idkecamatan,k.nama_kecamatan,kodepos,nip_ka,nama_ka,nip_pengelola_obat,nama_pengelola_obat FROM puskesmas p LEFT JOIN kecamatan k ON (k.idkecamatan=p.idkecamatan) WHERE idpuskesmas=$idpuskesmas";
                $this->db->setFieldTable (array('nama_puskesmas','alamat','notelpfax','idkecamatan','nama_kecamatan','kode_pos','nip_ka','nama_ka','nip_pengelola_obat','nama_pengelola_obat'));							
                $r = $this->db->getRecord($str);			
                $dataUser['nama_puskesmas']=$r[1]['nama_puskesmas'];
                $dataUser['alamat']=$r[1]['alamat'];
                $dataUser['idkecamatan']=$r[1]['idkecamatan'];
                $dataUser['nama_kecamatan']=$r[1]['nama_kecamatan'];
                $dataUser['notelpfax']=$r[1]['notelpfax'];
                $dataUser['kode_pos']=$r[1]['kodepos'];
                $dataUser['nip_ka']=$r[1]['nip_ka'];
                $dataUser['nama_ka']=$r[1]['nama_ka'];
                $dataUser['nip_pengelola_obat']=$r[1]['nip_pengelola_obat'];
                $dataUser['nama_pengelola_obat']=$r[1]['nama_pengelola_obat'];
            break;
        }
        $dataUser['logintime']=date('Y-m-d H:m:s');        
        $this->dataUser['data_user']=$dataUser;
		return $dataUser;
	}
	/**
	* digunakan untuk mendapatkan data user	
	*/
	public function getUser () {				
        $str = "SELECT u.userpassword,u.page,u.salt FROM user u WHERE username='{$this->username}' AND active=1";
        $this->db->setFieldTable (array('userpassword','salt','page'));							
        $r = $this->db->getRecord($str);				                
        $result=array();
        if (isset($r[1]) ) {
            $result=$r[1];            
        }        
        return $result;
	}	
}

?>