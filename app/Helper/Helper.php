<?php

use Illuminate\Support\Facades\Route;

    function day_id($tanggal){
		$day = date('D', strtotime($tanggal));
		$dayList = array(
			'Sun' => 'Minggu',
			'Mon' => 'Senin',
			'Tue' => 'Selasa',
			'Wed' => 'Rabu',
			'Thu' => 'Kamis',
			'Fri' => 'Jumat',
			'Sat' => 'Sabtu'
		);

		return $dayList[$day];
	}
	
    
	function date_db($tgl){
		if(trim($tgl) != ""){
			$year   = substr($tgl, 6, 4);
			$month  = substr($tgl, 3, 2);
			$day    = substr($tgl, 0, 2);
			
			$new_tgl = $year.'-'.$month.'-'.$day;

			return ($new_tgl);
		}else{
			return NULL;
		}
	}

	function date_id($tgl){
		if(trim($tgl) != ""){
			$year   = substr($tgl, 0, 4);
			$month  = substr($tgl, 5, 2);
			$day    = substr($tgl, 8, 2);
			
			$new_tgl = $day.'/'.$month.'/'.$year;

			return ($new_tgl);
		}else{
			return NULL;
		}
	}

	function date_id_simple($tgl){
		if(trim($tgl) != ""){
			$year   = substr($tgl, 2, 2);
			$month  = substr($tgl, 5, 2);
			$day    = substr($tgl, 8, 2);
			
			$new_tgl = $day.'-'.$month.'-'.$year;

			return ($new_tgl);
		}else{
			return NULL;
		}
	}

	function date_id_full($string){
		$bulanIndo = [
			'', 
			'Januari', 
			'Februari', 
			'Maret', 
			'April', 
			'Mei', 
			'Juni', 
			'Juli', 
			'Agustus', 
			'September' , 
			'Oktober', 
			'November', 
			'Desember'
		];
	
		$hari = day_id($string);
		$tanggal = explode("-", $string)[2];
		$bulan = explode("-", $string)[1];
		$tahun = explode("-", $string)[0];
	
		return  $hari. ', '. $tanggal . " " . $bulanIndo[abs($bulan)] . " " . $tahun;
	}

	function date_en_full($tgl){
		if(trim($tgl) != ""){
			$year   = substr($tgl, 0, 4);
			$new_tgl = date('F jS, Y', strtotime($tgl));

			return ($new_tgl);
		}else{
			return NULL;
		}
	}

	function time_id($time){
		if(trim($time) != ""){
			return(substr($time, 0, 5));
		}else{
			return NULL;
		}
	}

    function imploadValue($types){
        $strTypes = implode(",", $types);
        return $strTypes;
    }
    
    function explodeValue($types){
        $strTypes = explode(",", $types);
        return $strTypes;
    }
    
    function set_transaksi_no(){
    
        return 'B'.date('m').date('d').str_pad(rand(1111, 9999), 3, "0", STR_PAD_LEFT);
    }
    
    function remove_special_char($text) {
    
            $t = $text;
    
            $specChars = array(
                ' ' => '-',    '!' => '',    '"' => '',
                '#' => '',    '$' => '',    '%' => '',
                '&amp;' => '',    '\'' => '',   '(' => '',
                ')' => '',    '*' => '',    '+' => '',
                ',' => '',    '₹' => '',    '.' => '',
                '/-' => '',    ':' => '',    ';' => '',
                '<' => '',    '=' => '',    '>' => '',
                '?' => '',    '@' => '',    '[' => '',
                '\\' => '',   ']' => '',    '^' => '',
                '_' => '',    '`' => '',    '{' => '',
                '|' => '',    '}' => '',    '~' => '',
                '-----' => '-',    '----' => '-',    '---' => '-',
                '/' => '',    '--' => '-',   '/_' => '-',   
                
            );
    
            foreach ($specChars as $k => $v) {
                $t = str_replace($k, $v, $t);
            }
    
            return $t;
	}

	function set_active($uri, $output = "mm-active")
	{
		if( is_array($uri) ) {
			foreach ($uri as $u) {
				if (Route::is($u)) {
					return $output;
				}
			}
		} else {
			if (Route::is($uri)){
				return $output;
			}
		}
	}
	

	
