<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class BookModel extends Eloquent {

    protected $collection = 'book';
    protected $fillable = array('title','subtitle','author','published_year','publisher','isbn','price',
        'codex_id','page', 'toc', 'cover', 'syllabus', 'keyword', 'organisation',
        'preview_url', 'preview_images', 'created_by', 'updated_by');

    public function create_book($insert_data, $main_id) {
        //$result = MainModel::create($insert_data);
        $result = BookModel::updateOrCreate(
                        ['_id' => $main_id], $insert_data
        );
        return $result->_id;
    }

    public function book_details($query_details = NULL) {
        $skip = 0;
        if (isset($query_details['skip'])) {
            $skip = $query_details['skip'];
        }
        $limit = config('constants.default_query_limit');
        if (isset($query_details['limit'])) {
            $limit = $query_details['limit'];
        }
        $search_key = "";
        if (isset($query_details['search_key'])) {
            $search_key = $query_details['search_key'];
        }
        $organisation = "";
        if (isset($query_details['organisation'])) {
            $organisation = $query_details['organisation'];
        }
        $codex_id = "";
        if (isset($query_details['codex_id'])) {
            $codex_id = $query_details['codex_id'];
        }
        $school_name = "";
        if (isset($query_details['school_name'])) {
            $school_name = $query_details['school_name'];
        }
        $title = $query_details['title'];
        $subtitle = $query_details['subtitle'];
        $level = $query_details['level'];
        $version = $query_details['version'];
        $subject = $query_details['subject'];
        $keywords_array = $query_details['keywords'];
        

        $sort_by = $query_details['sort_by'];
        if ($sort_by == "school_name") {
            $sort_by = "cover.school_name";
        }
        if ($sort_by == "level") {
            $sort_by = "cover.level";
        }
    
        $order_by = $query_details['order_by'];

        if ($search_key != "" || $organisation != "" || $codex_id != "" || $school_name != "" || $title != "" || $subtitle != "" || $level != "" || $version != "" || $subject != "" || count($keywords_array)>0) {
            $book_data = BookModel::
                    Where(function($filterByQuery)use ($query_details) {
                        if ($query_details['organisation'] != "") {
                            $filterByQuery->where('organisation', 'like', $query_details['organisation']);
                        }
                        if ($query_details['codex_id'] != "") {
                            $filterByQuery->where('codex_id', 'like', $query_details['codex_id']);
                        }
                        if ($query_details['school_name'] != "") {
                            $filterByQuery->where('cover.school_name', 'like', $query_details['school_name']);
                        }
                        if ($query_details['title'] != "") {
                            $filterByQuery->where('title', 'like', $query_details['title']);
                        }
                        if ($query_details['subtitle'] != "") {
                            $filterByQuery->where('subtitle', 'like', $query_details['subtitle']);
                        }
                        if ($query_details['level'] != "") {
                            $filterByQuery->where('cover.level', 'like', $query_details['level']);
                        }
                        if ($query_details['version'] != "") {
                            $filterByQuery->where('cover.version', 'like', $query_details['version']);
                        }
                        if ($query_details['subject'] != "") {
                            $filterByQuery->where('syllabus.subject', 'like', $query_details['subject']);
                        }
                        if (count($query_details['keywords'])>0) {
                            // use foreach for tags array to use like query for case insensitive search using orWhere
                            $filterByQuery->where('keyword','all',$query_details['keywords']);
                            
                        }
                    })
                    ->Where(function($filterSeaarchQuery)use ($search_key) {
                        if ($search_key != "") {
                            $filterSeaarchQuery->where('keyword', 'like', "%$search_key%");
                        }
                    })
                    ->skip($skip)
                    ->take($limit)
                    ->orderBy($sort_by, $order_by)
                    ->get();
        } else {
            $book_data = BookModel::skip($skip)->take($limit)->orderBy($sort_by, $order_by)->get();
        }
        return $book_data;
    }

    public function total_count($query_details) {
        if (isset($query_details['search_key'])) {
            $search_key = $query_details['search_key'];
        } else {
            $search_key = "";
        }
        $codex_id = "";
        if (isset($query_details['codex_id'])) {
            $codex_id = $query_details['codex_id'];
        }
        if (isset($query_details['organisation'])) {
            $organisation = $query_details['organisation'];
        } else {
            $organisation = "";
        }
        if (isset($query_details['school_name'])) {
            $school_name = $query_details['school_name'];
        } else {
            $school_name = "";
        }
        $title = $query_details['title'];
        $subtitle = $query_details['subtitle'];
        $level = $query_details['level'];
        $version = $query_details['version'];
        $subject = $query_details['subject'];
        $keywords_array = $query_details['keywords'];
        if ($search_key != "" || $organisation != "" || $codex_id != "" || $school_name != "" || $title != "" || $subtitle != "" || $level != "" || $version != "" || $subject != "" || count($keywords_array)>0) {
            $total_count = BookModel::
                    Where(function($filterByQuery)use ($query_details) {
                        if ($query_details['organisation'] != "") {
                            $filterByQuery->where('organisation',$query_details['organisation']);
                        }
                        if ($query_details['codex_id'] != "") {
                            $filterByQuery->where('codex_id', 'like', $query_details['codex_id']);
                        }
                        if ($query_details['school_name'] != "") {
                            $filterByQuery->where('cover.school_name','like', $query_details['school_name']);
                        }
                        if ($query_details['title'] != "") {
                            $filterByQuery->where('title', 'like', $query_details['title']);
                        }
                        if ($query_details['subtitle'] != "") {
                            $filterByQuery->where('subtitle', 'like', $query_details['subtitle']);
                        }
                        if ($query_details['level'] != "") {
                            $filterByQuery->where('cover.level', 'like', $query_details['level']);
                        }
                        if ($query_details['version'] != "") {
                            $filterByQuery->where('cover.version', 'like', $query_details['version']);
                        }
                        if ($query_details['subject'] != "") {
                            $filterByQuery->where('syllabus.subject', 'like', $query_details['subject']);
                        }
                        if (count($query_details['keywords'])>0) {
                            // use foreach for tags array to use like query for case insensitive search using orWhere
                            $filterByQuery->where('keyword','all',$query_details['keywords']);
                        }
                    })
                    ->Where(function($filterSeaarchQuery)use ($search_key) {
                        if ($search_key != "") {
                            $filterSeaarchQuery->where('keyword', 'like', "%$search_key%");
                        }
                    })
                    ->count();
        } else {
            $total_count = BookModel::count();
        }
        return $total_count;
    }

    public function find_book_details($book_id) {
        $book_data = BookModel::find($book_id);
        return $book_data;
    }

}
