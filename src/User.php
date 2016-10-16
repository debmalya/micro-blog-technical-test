<?php
class User
{
	/**
	 * @Id @Column(type="integer") @GeneratedValue 
         */
	protected $user_id;

	/**
	 * @Column(type="string")
         */
	protected $user_name;

	public function getUserId(){
		return $user_id;
	}

	public function getUserName(){
		return $user_name;
	}

	public function setUserName($user_name){
		$this->user_name = $user_name;
	}
}
