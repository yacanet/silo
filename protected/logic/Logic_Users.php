<?php

prado::using ('Application.logic.Logic_Global');
class Logic_Users extends Logic_Global {	
	/**
	* object Users
	*/
	private $U;	
	/**
	* Roles
	*/
	private $Roles;
	/**
	* Data User
	*/
	private $DataUser;	
	/**
     * list roles user     
     */
    private $ListUserRoles=array('none'=>'Keseluruhan Roles','sa'=>'GUDANG FARMASI','ad'=>'ADMIN PUSKESMAS','sp'=>'STAFF PUSKESMAS','d'=>'DINAS');
    
	public function __construct ($db) {
		parent::__construct ($db);	
		$this->U = $this->User;
		if (method_exists($this->U,'getRoles')) {
			$dataUser=$this->U->getName();	
			if ($dataUser != 'Guest') {
				$this->Roles=$this->U->getRoles();			                
				$this->DataUser=$dataUser;		                
            }		
		}				
	}
	/**
	* digunakan untuk membuat hash password
	* @return array
	*/
	public function createHashPassword($password,$salt='',$new=true) {
		if ($new) {
			$salt = substr(md5(uniqid(rand(), true)), 0, 6);	
			$password = hash('sha256', $salt . hash('sha256', $password));
			$data =array('salt'=>$salt,'password'=>$password);			
		}else {
			$data = hash('sha256', $salt . hash('sha256', $password));	
			$data =array('salt'=>$salt,'password'=>$password);		
		}
		return $data;
	}
    /**
	* digunakan untuk mendapatkan roles user
	*/		
	public function getRoles () {
		return $this->Roles[0];
	}
    /**
	* digunakan untuk mendapatkan name roles user
	*/		
	public function getRolesName () {
		return $this->ListUserRoles[$this->DataUser['page']];
	}
	/**
	* digunakan untuk mendapatkan tipe user
	*/		
	public function getTipeUser () {
		return $this->DataUser['page'];
	}		
	/**
	* digunakan untuk mendapatkan data user
	*
	* @return datauser
	*/
	public function getDataUser ($id='all') {						
		if ($id=='all')
			return $this->DataUser;
		else
			return $this->DataUser[$id];
	}	
	/**
	* untuk mendapatkan userid dari user
	*
	*/
	public function getUserid () {			
		return $this->DataUser['userid'];		
	}		
	/**
	* untuk mendapatkan username dari user
	*
	*/
	public function getUsername () {		
		return $this->DataUser['username'];
	}
    /**
	* untuk mendapatkan daftar roles
	*
	*/
	public function getListUserRoles($role=null) {		        
        if ($role===null)
            return $this->ListUserRoles;
        else
            return $this->ListUserRoles[$role];
	}	
}
?>