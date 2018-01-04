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
    'login_invalid' => array('error_code'=>'E109','error_message' =>"Username/password is invalid"),
    'invalid_user_id' => array('error_code'=>'E110','error_message' =>"Invalid user id."),
    'username_required' => array('error_code'=>'E111','error_message' =>"Username is required."),
    'activation_key_required' => array('error_code'=>'E112','error_message' =>"Activation key is required."),
    'activation_key_exists' => array('error_code'=>'E113','error_message' =>"Activation key is invalid."),
    'profile_image_invalid_format' => array('error_code'=>'E114','error_message' =>"Invalid profile image format."),
    'username_verification_required' => array('error_code'=>'E115','error_message' =>"User email verification required"),
    
    'media_type_required' => array('error_code'=>'E301','error_message' =>"Media type is missing."),
    'media_extension_required' => array('error_code'=>'E302','error_message' =>"Media extension is required."),
    'media_file_required' => array('error_code'=>'E303','error_message' =>"Media file is required."),
    'invalid_media_file_mime' => array('error_code'=>'E304','error_message' =>"Invalid MIME type."),
    'media_owner_required' => array('error_code'=>'E305','error_message' =>"Owner is required."),
    'media_usage_required' => array('error_code'=>'E306','error_message' =>"Usage is required."),
    'media_created_by_required' => array('error_code'=>'E307','error_message' =>"Created by is required."),
    'invalid_media_created_by' => array('error_code'=>'E308','error_message' =>"Created by has invalid id."),
    'invalid_media_id' =>array('error_code'=>'E309','error_message' =>"Invalid Media id.") ,
    'file_limit_exceeded' => array('error_code'=>'E310','error_message' =>"Media file size is limited to 10 MB"),
    
    'invalid_page_group_id' => array('error_code'=>'E401','error_message' =>"Invalid Page Group id."),
    'invalid_archive_file' => array('error_code'=>'E402','error_message' =>"Invalid Archive file"),
    'invalid_meta_data_file' => array('error_code'=>'E403','error_message' =>"Invalid Meta data file"),
    'archive_file_required' => array('error_code'=>'E404','error_message' =>"Archive file is required"),
    'meta_data_file_required' => array('error_code'=>'E405','error_message' =>"Meta data file is required"),
    
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
    'book_title_required' => array('error_code'=>'E1409','error_message' =>"Book title is Required."),
    'book_author_required' => array('error_code'=>'E1410','error_message' =>"Book author is Required."),
    'book_published_year_required' => array('error_code'=>'E1411','error_message' =>"Book Published year is Required."),
    'book_publisher_required' => array('error_code'=>'E1412','error_message' =>"Book Publisher is Required."),
    'book_isbn_required' => array('error_code'=>'E1413','error_message' =>"Book ISBN is Required."),
    'book_price_required' => array('error_code'=>'E1414','error_message' =>"Book Price is Required."),
    
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
    'keyword_required' =>array('error_code'=>'E1607','error_message' =>"Keyword is Required"),
    'keyword_ID_required' =>array('error_code'=>'E1608','error_message' =>"Keyword ID is Required"),
    'invalid_keyword_id' => array('error_code'=>'E1609','error_message' =>"Invalid Keyword ID."),
    'resource_category_required' => array('error_code'=>'E1610','error_message' =>"Resource category id is required"),
    'resource_category_exists' => array('error_code'=>'E1610','error_message' =>"Resource category id is invalid"),
    
    'class_name_required' => array('error_code'=>'E1701','error_message' =>"Class name is required"),
    'class_name_unique' => array('error_code'=>'E1702','error_message' =>"Class name already exists"),
    'class_id_required' => array('error_code'=>'E1703','error_message' =>"Class id is required"),
    'class_id_invalid' => array('error_code'=>'E1704','error_message' =>"Class id is invalid"),
    
    'class_flow_title_required' => array('error_code'=>'E1801','error_message' =>"Class flow title is required"),
    'class_flow_resource_required' => array('error_code'=>'E1802','error_message' =>"Class flow resource is required"),
    'class_flow_id_required' => array('error_code'=>'E1803','error_message' =>"Class flow id is required"),
    'class_flow_id_invalid' => array('error_code'=>'E1804','error_message' =>"Class flow id is invalid"),
    
    'keyword_id_invalid' => array('error_code'=>'E1901','error_message' =>"Keyword id is invalid"),
    'keyword_required' => array('error_code'=>'E1902','error_message' =>"Keyword is required"),
    
    'domain_id_invalid' => array('error_code'=>'E2001','error_message' =>"Domain id is invalid"),
    'code_required' => array('error_code'=>'E2002','error_message' =>"Domain code is required"),
    'domain_title_required' => array('error_code'=>'E2003','error_message' =>"Domain title is required"),
    
    'sub_domain_id_invalid' => array('error_code'=>'E2101','error_message' =>"Sub Domain id is invalid"),
    'sub_code_required' => array('error_code'=>'E2102','error_message' =>"Sub Domain code is required"),
    'sub_domain_title_required' => array('error_code'=>'E2103','error_message' =>"Sub Domain title is required"),
    
    'knowledge_unit_id_invalid' => array('error_code'=>'E2201','error_message' =>"knowledge unit id is invalid"),
    'knowledge_unit_code_required' => array('error_code'=>'E2202','error_message' =>"knowledge unit code is required"),
    'knowledge_unit_title_required' => array('error_code'=>'E2203','error_message' =>"knowledge unit title is required"),
    
    'particular_id_invalid' => array('error_code'=>'E2301','error_message' =>"particular unit id is invalid"),
    'particular_detail_required' => array('error_code'=>'E2302','error_message' =>"particular detail is required"),
    'particular_title_required' => array('error_code'=>'E2303','error_message' =>"particular title is required"),
    
    'resource_category_name_required' => array('error_code'=>'E2401','error_message' =>"Resource Category name is required"),
    'resource_category_name_unique' => array('error_code'=>'E2402','error_message' =>"Resource Category name already exists"),
    'resource_category_id_invalid' => array('error_code'=>'E2403','error_message' =>"Resource Category id is invalid"),
    
    'codex_name_required' => array('error_code'=>'E2501','error_message' =>"Codex name is required"),
    'codex_file_required' => array('error_code'=>'E2502','error_message' =>"Codex file is required."),
    'codex_file_limit_exceeded' => array('error_code'=>'E2503','error_message' =>"Codex file size is limited to 10 MB."),
    'invalid_codex_file_mime' => array('error_code'=>'E2504','error_message' =>"Invalid codex image file type."),
    'codex_created_by_required' => array('error_code'=>'E2507','error_message' =>"Created by is required."),
    'invalid_codex_created_by' => array('error_code'=>'E2508','error_message' =>"Created by has invalid id."),
    'invalid_codex_id' =>array('error_code'=>'E2509','error_message' =>"Invalid codex id.") ,
    'codex_id_required' => array('error_code'=>'E2510','error_message' =>"Codex id is required"),
    
    'static_html_page_code_required' => array('error_code'=>'E2601','error_message' =>"Static page code is required"),
    'static_html_page_code_unique' => array('error_code'=>'E2602','error_message' =>"Static page code already exists"),
    'static_html_page_id_invalid' => array('error_code'=>'E2603','error_message' =>"Static page id is invalid"),
    'static_html_page_content_required' => array('error_code'=>'E2701','error_message' =>"Static page content is required"),

];
