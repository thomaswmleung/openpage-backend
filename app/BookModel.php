<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class BookModel extends Eloquent {

    protected $collection = 'book';
    protected $fillable = array('page', 'toc', 'cover', 'syllabus', 'keyword', 'organisation', 'preview_url', 'preview_images', 'created_by', 'updated_by');

    public function create_book($insert_data, $main_id) {
        //$result = MainModel::create($insert_data);
        $result = BookModel::updateOrCreate(
                        ['_id' => $main_id], $insert_data
        );
        return $result->_id;
    }

    public function book_details($query_details = NULL) {
        if ($query_details == NULL) {
            $skip = 0;
            $limit = config('constants.default_query_limit');
            $search_key = "";
        } else {
            if (isset($query_details['skip'])) {
                $skip = $query_details['skip'];
            } else {
                $skip = 0;
            }
            if (isset($query_details['limit'])) {
                $limit = $query_details['limit'];
            } else {
                $limit = config('constants.default_query_limit');
            }
            if (isset($query_details['search_key'])) {
                $search_key = $query_details['search_key'];
            } else {
                $search_key = "";
            }
            if (isset($query_details['organisation'])) {
                $organisation = $query_details['organisation'];
            } else {
                $organisation = "";
            }
        }

        if ($search_key != "" || $organisation != "") {
            $book_data = BookModel::
                    Where(function($organisationIdQuery)use ($organisation) {
                        if ($organisation != "") {
                            $organisationIdQuery->where('organisation', $organisation);
                        }
                    })
                    ->Where(function($filterSeaarchQuery)use ($search_key) {
                        if ($search_key != "") {
                            $filterSeaarchQuery->where('keyword', 'like', "%$search_key%");
                        }
                    })
                    ->skip($skip)
                    ->take($limit)
                    ->get();
        } else {
            $book_data = BookModel::skip($skip)->take($limit)->get();
        }
        return $book_data;
    }

    public function total_count($query_details) {
        $search_key = $query_details['search_key'];
        $organisation = $query_details['organisation'];
        if ($search_key != "" || $organisation != "") {
            $total_count = BookModel::
                    Where(function($organisationIdQuery)use ($organisation) {
                        if ($organisation != "") {
                            $organisationIdQuery->where('organisation', $organisation);
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
