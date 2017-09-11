<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

return [
    'first_name_required' => array('error_code'=>'E101','error_message' =>"First Name is required."),
    'last_name_required' => array('error_code'=>'E102','error_message' =>"Last Name is required."),
    'password_required' => array('error_code'=>'E103','error_message' =>"Password is required."),
    'email_vaild' => array('error_code'=>'E104','error_message' =>"Email is invalid."),
    'email_required' => array('error_code'=>'E105','error_message' =>"Email is required."),
    'email_already_taken' => array('error_code'=>'E106','error_message' =>"Email is already taken."),
    'login_user_name_required' => array('error_code'=>'E107','error_message' =>"User name is required."),
    'login_password_required' =>  array('error_code'=>'E108','error_message' =>"Password is required."),
    'login_invalid' => array('error_code'=>'E109','error_message' =>"User name is required."),
    'invalid_user_id' => array('error_code'=>'E110','error_message' =>"Invalid user id."),
    'username_required' => array('error_code'=>'E111','error_message' =>"Username is required."),
    'activation_key_required' => array('error_code'=>'E112','error_message' =>"Activation key is required."),
    'activation_key_exists' => array('error_code'=>'E113','error_message' =>"Activation key is invalid."),
    
    'media_type_required' => array('error_code'=>'E301','error_message' =>"Media type is missing."),
    'media_extension_required' => array('error_code'=>'E302','error_message' =>"Media extension is required."),
    'media_file_required' => array('error_code'=>'E303','error_message' =>"Media file is required."),
    'invalid_media_file_mime' => array('error_code'=>'E304','error_message' =>"Invalid MIME type."),
    'media_owner_required' => array('error_code'=>'E305','error_message' =>"Owner is required."),
    'media_usage_required' => array('error_code'=>'E306','error_message' =>"Usage is required."),
    'media_created_by_required' => array('error_code'=>'E307','error_message' =>"Created by is required."),
    'invalid_media_created_by' => array('error_code'=>'E308','error_message' =>"Created by has invalid id."),
    'invalid_media_id' =>array('error_code'=>'E309','error_message' =>"Invalid Media id.") ,
    'media_deleted_success' => array('error_code'=>'E310','error_message' =>"Media deleted successfully."),
    
    'invalid_page_group_id' => array('error_code'=>'E401','error_message' =>"Invalid Page Group id."),
    
    'invalid_page_id' =>  array('error_code'=>'E501','error_message' =>"Invalid Page id."),
    'page_id_required' =>  array('error_code'=>'E502','error_message' =>"Page id is required"),
    'page_id_exists' =>  array('error_code'=>'E503','error_message' =>"Page id doesn't exists"),
    
    
    'invalid_section_id' => array('error_code'=>'E601','error_message' =>"Invalid Section id."),
    
    'invalid_question_id' => array('error_code'=>'E701','error_message' =>"Invalid Question id."),
    
    'question_type_required' => array('error_code'=>'E801','error_message' =>"Question type is required."),
    'block_required' => array('error_code'=>'E802','error_message' =>"Block is required."),
    'question_type_id_required' => array('error_code'=>'E803','error_message' =>"Question type id is required."),
    'invalid_question_type_id' => array('error_code'=>'E804','error_message' =>"Invalid Question type id."),
    
    'organization_name_required' => array('error_code'=>'E901','error_message' =>"Organization Name is required."),
    'organization_email_required' => array('error_code'=>'E902','error_message' =>"Organization email is required."),
    'organization_contact_person_required' => array('error_code'=>'E903','error_message' =>"Organization contact person is required."),
    'organization_type_required' => array('error_code'=>'E904','error_message' =>"Organization type is required."),
    'organization_remark_required' => array('error_code'=>'E905','error_message' =>"Organization remark is required."),
    'organization_consultant_required' => array('error_code'=>'E906','error_message' =>"Organization consultant is required."),
    'organization_role_required' => array('error_code'=>'E907','error_message' =>"Organization role is required."),
    'invalid_organization_id' => array('error_code'=>'E908','error_message' =>"Invalid organization id.") ,
    'organization_id_required' => array('error_code'=>'E909','error_message' =>"Organization id is required."),
    'organization_doesnot_exist' => array('error_code'=>'E910','error_message' =>"Organization doesnt exists."),
    'organization_deleted_success' => array('error_code'=>'E911','error_message' =>"Organization deleted successfully."),
    
    
    
   
    
    'invalid_layout_id' => array('error_code'=>'E1301','error_message' =>"Invalid layout id."),
    
    'book_id_required' => array('error_code'=>'E1401','error_message' =>"Book ID is Required."),
    'book_page_required' => array('error_code'=>'E1402','error_message' =>"Book Page is Required."),
    'book_toc_required' => array('error_code'=>'E1403','error_message' =>"Book TOC is Required."),
    'book_cover_required' => array('error_code'=>'E1404','error_message' =>"Book Cover is Required."),
    'book_syllabus_required' => array('error_code'=>'E1405','error_message' =>"Book Syllabus is Required."),
    'book_keyword_required' => array('error_code'=>'E1406','error_message' =>"Book Keyword is Required."),
    'book_organisation_required' => array('error_code'=>'E1407','error_message' =>"Organisation is Required."),
    'invalid_book_id' => array('error_code'=>'E1408','error_message' =>"Invalid Book ID."),
    
    'subject_id_required' => array('error_code'=>'E1501','error_message' =>"Subject ID is Required."),
    'subject_code_required' => array('error_code'=>'E1502','error_message' =>"Subject Code is Required."),
    'subject_title_required' => array('error_code'=>'E1503','error_message' =>"Subject title is Required."),
    'subject_domain_required' => array('error_code'=>'E1504','error_message' =>"Subject Domain is Required."),
    'invalid_subject_id' => array('error_code'=>'E1505','error_message' =>"Invalid subject id."),
    
    'resource_title_required' => array('error_code'=>'E1601','error_message' =>"Invalid subject id."),
    'resource_description_required' => array('error_code'=>'E1602','error_message' =>"Invalid subject id."),
    'resource_type_required' => array('error_code'=>'E1603','error_message' =>"Invalid subject id."),
    'resource_url_required' => array('error_code'=>'E1604','error_message' =>"Invalid subject id."),
    'resource_id_required' => array('error_code'=>'E1605','error_message' =>"Invalid subject id."),
    'invalid_resource_id' => array('error_code'=>'E1606','error_message' =>"Invalid subject id."),
    
    'class_name_required' => array('error_code'=>'E1701','error_message' =>"Class name is required"),
    'class_name_unique' => array('error_code'=>'E1702','error_message' =>"Class name already exists"),
    'class_id_required' => array('error_code'=>'E1703','error_message' =>"Class id is required"),
    'class_id_invalid' => array('error_code'=>'E1704','error_message' =>"Class id is invalid"),
    
    'class_flow_title_required' => array('error_code'=>'E1801','error_message' =>"Class flow title is required"),
    'class_flow_id_required' => array('error_code'=>'E1803','error_message' =>"Class flow id is required"),
    'class_flow_id_invalid' => array('error_code'=>'E1804','error_message' =>"Class flow id is invalid"),
    
    
];