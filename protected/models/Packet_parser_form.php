<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Packet_parser_form
 *
 * @author Moss
 */
class Packet_parser_form extends CFormModel{
	
	public $packet_type_id;
	
	public $packets_json;
	
	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('packet_type_id, packets_json', 'required'),
			array('packet_type_id', 'numerical', 'integerOnly'=>true),
		);
	}
	
}
