<?php
/**
 * @package Verbindung mit Wufoo
 */
/*
Plugin Name: Verbindung mit Wufoo
Plugin URI: https://palacios-online.de/
Description: This plugin connects via REST the Ultimate Member plugin with the Wufoo platform, for an exclusive form from cliente reqirements.
Version: 0.1
Author: Jose Manuel Rodriguez & Palacios Online
Author URI: https://palacios-online.de/
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2005-2015 Automattic, Inc.
*/

// Wufoo API Key: 14EZ-IL48-J3F3-LDNF 
// USER URL: https://kno.wufoo.com
// Main form HASH: m1mxmm630duq7yc
// Test form HASH: se52lup0q26hy8


// Make sure we don't expose any info if called directly
defined( 'ABSPATH' ) || exit;

/**
* Description for vmw_send_data_to_wufoo
* @data => Registered User ID
*/
function vmw_send_data_to_wufoo($registeredUserID){
    
    //$registeredUserID = 508;
    
    $wufoo_API_key = '14EZ-IL48-J3F3-LDNF';
    $wufoo_FORM_hash = 'p1vweylf0rf967b'; // for testing
    //$wufoo_FORM_hash = 'm1mxmm630duq7yc';
    
    
    /**
    * @current_registered_user => set current registered user ID from Ultimate Member action
    */
    $user_main_data = get_userdata( $registeredUserID );
    $user_meta_data = get_user_meta( $registeredUserID );
    
//    echo '<pre>';
//    print_r($user_main_data);
//    print_r($user_meta_data);
//    echo '</pre>';


    /* User main data */
    $gender                         =   get_user_meta( $registeredUserID, 'user_gender', true);
    $title                          =   get_user_meta( $registeredUserID, 'user_title', true);
    $firstName                      =   get_user_meta( $registeredUserID, 'first_name', true);
    $lastName                       =   get_user_meta( $registeredUserID, 'last_name', true);
    $address                        =   get_user_meta( $registeredUserID, 'user_addr_a', true);
    $city                           =   get_user_meta( $registeredUserID, 'user_city', true);
    $zipcode                        =   get_user_meta( $registeredUserID, 'user_zipcode', true);
    $country                        =   get_user_meta( $registeredUserID, 'country', true);
    $telefone                       =   get_user_meta( $registeredUserID, 'user_phone', true);
    $submited                       =   get_user_meta( $registeredUserID, 'submitted', true); // to get the email address
    
    // Assistenzarzt - Facharzt mit Möglichkeit zur operativen Ausbildung - Facharzt konservativ - Operateur
    $desired_positions              =   get_user_meta( $registeredUserID, 'desired_positions', true); 
    
    // 0 - 9
    $desired_postalcode             =   get_user_meta( $registeredUserID, 'desired_postalcode', true);
    
    // Angestellter Arzt - Honorararzt - Partner/Eigentümer
    $desired_job                    =   get_user_meta( $registeredUserID, 'desired_job', true);
    
    // Vollzeit - Teilzeit
    $desired_job_type               =   get_user_meta( $registeredUserID, 'desired_job_type', true);
    
    // Ländlich - Städtisch - Grossstädtisch
    $place_wished_residence         =   get_user_meta( $registeredUserID, 'place_wished_residence', true);
    
    // Desire anuall income - Single string
    $desired_annual_salary          =   get_user_meta( $registeredUserID, 'desired_annual_salary', true);
    
    // Einzelpraxis/Satellitenpraxis - Gemeinschaftspraxis/MVZ - Klinik
    $type_company                   =   get_user_meta( $registeredUserID, 'type_company', true);

    
    $user_specialization_exam_year  =   get_user_meta( $registeredUserID, 'user_specialization_exam_year', true); 
    
    
    // CREATE FIELDS STRING TO POST
    $fields = 'Field120='    .      $gender[0]                      // User title
            . '&Field115='   .      $title                          // User first name
            . '&Field116='   .      $firstName                      // User first name
            . '&Field117='   .      $lastName                       // User last name

            . '&Field3='     .      $address                        // User street address
            . '&Field5='     .      $city                           // User street address
            . '&Field7='     .      $zipcode                        // User ZIP code
            . '&Field8='     .      $country                        // User Country
            . '&Field9='     .      $submited['user_email']         // User Email
            . '&Field10='    .      $telefone                       // User phone number

            . '&Field1841='  .      $desired_annual_salary[0]           // User Desire Anuall Income
            . '&Field1023='  .      $user_specialization_exam_year[0]   // User Year of graduation
        ;

    // Desired Positions
    foreach( $desired_positions as $position ){
        $positions = array(
            '12' => 'Assistenzarzt/in',
            '13' => 'Facharzt/in',
            '14' => 'Operative Ausbildung',
            '15' => 'Operateur/in',
            '16' => 'Ärztliche Leitung',
        );  
        foreach($positions as $key => $position_name){
            if($position == strtoupper($position_name)){
                $fields .= '&Field' . $key . '=' . urlencode($position_name);
                break;
            }
        }                
    }
    
    //Desired postal codes
    $zip_codes = array();
    for($i = 1232; $i <= 1241; $i++){
        $zip_codes[$i] = $i;
    } 
    foreach( $desired_postalcode as $postalcode ){
        
        foreach($zip_codes as $apiID => $value){
            //echo $postalcode . ' - ' . $apiID . ' - ' . $value . '<br />';
            if ($postalcode == $value){
                $fields .= '&Field' . $apiID . '=' . $postalcode;
                break;
            }
        } 
        
    }
    
    // Desired Employments ratio
    foreach( $desired_job as $position ){
        $positions = array(
            '221' => 'Angestellt',
            '222' => 'Honorar',
            '223' => 'Selbstständig',
        );  
        foreach($positions as $key => $position_name){
            if($position == strtoupper($position_name)){
                $fields .= '&Field' . $key . '=' . urlencode($position_name);
                break;
            }
        }                
    }
    
    // Desired Employments ratio
    foreach( $desired_job_type as $position ){
        $positions = array(
            '2150' => '10',
            '2151' => '20',
            '2152' => '30',
            '2153' => '40'
        );  
        foreach($positions as $key => $position_name){
            if($position == strtoupper($position_name)){
                $fields .= '&Field' . $key . '=' . urlencode($position_name);
                break;
            }
        }                
    }
    
    // Desired locations
    foreach( $place_wished_residence as $position ){
        $positions = array(
            '321' => 'Land',
            '322' => 'Stadt',
            '323' => 'Großstadt'
        );  
        foreach($positions as $key => $position_name){
            if($position == strtoupper($position_name)){
                $fields .= '&Field' . $key . '=' . urlencode($position_name);
                break;
            }
        }                
    }
    
    // Desired Target content
    foreach( $type_company as $position ){
        $positions = array(
            '521' => 'Einzelpraxis',
            '522' => 'Großpraxis',
            '523' => 'Klinik'
        );  
        foreach($positions as $key => $position_name){
            if($position == strtoupper($position_name)){
                $fields .= '&Field' . $key . '=' . urlencode($position_name);
                break;
            }
        }                
    }
    
//    echo '<pre>';
//    echo $fields;
//    echo '</pre>';
//    die;
    
    /**
    * Returns an array of wufoo entries of current form Hash for POST procedure
    * @wufoocurl
    */
    $wufoocurl = curl_init('https://kno.wufoo.com/api/v3/forms/'.$wufoo_FORM_hash.'/entries.json');

    
    curl_setopt($wufoocurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($wufoocurl, CURLOPT_USERPWD, $wufoo_API_key .':knoptimed');
    curl_setopt($wufoocurl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($wufoocurl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($wufoocurl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($wufoocurl, CURLOPT_USERAGENT, 'Wufoo Sample Code');
                
    $response = curl_exec($wufoocurl);
    $resultStatus = curl_getinfo($wufoocurl);
    
    /**
    * Validar si logro conectarse para verificar si existe registro
    */
    if($resultStatus['http_code'] == 200) {

        /**
        * Variable inicializada en falso para establecer un estatus
        * si esta el correo ya en la base de datos
        * @duplicated 
        */
        $duplicated = false; 
        
        /**
        * Variable que almacena en objeto con arreglos de la respuesta
        * @entries 
        */
        $entries = json_decode($response);
        
        /**
        * Verificamos si el objeto tiene almenos 1 arreglo 
        */
        if (count ($entries->Entries) > 0 ){
            
            /**
            * Variable contadora para recorrer el objeto @entries
            * @i
            */
            $i = 0;
            
            /**
            * Recorremos el objeto @entries
            */
            foreach($entries->Entries as $entry){
                /**
                * Validar si en el campo del objeto esta el correo que
                * suministró el usuario. De existir el estado de 
                * @duplicated cambia a TRUE para no hacer el envio 
                * a Wuffoo
                */
                if ($entry->Field9 == $submited['user_email'])
                    $duplicated = true;
                $i++;
            }
        }
        
        /**
        * Validar si el estado de @duplicated sigue siendo FALSE
        * para hacer el envio a Wuffoo
        */
        if (!$duplicated){
        
            /**
            * Se setean las variables para hacer el POST
            */
            curl_setopt($wufoocurl, CURLOPT_POST, 1);
            curl_setopt($wufoocurl, CURLOPT_POSTFIELDS, $fields);
            
            /**
            * Se ejecuta el CURL
            */
            $response = curl_exec($wufoocurl);
            $json = json_decode($response);
            echo '<pre>';
            echo json_encode($json, JSON_PRETTY_PRINT);
            echo '</pre>';
            $resultStatus = curl_getinfo($wufoocurl);
            echo '<pre>';
            echo print_r($resultStatus);
            echo '</pre>';
            
        }
        
    }
    
}
add_action( 'um_after_save_registration_details', 'vmw_send_data_to_wufoo', 10, 2 );
?>
