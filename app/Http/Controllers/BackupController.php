<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class BackupController extends Controller {

    public function backup_db(Request $request) {
        $backup_path = public_path('mongobackups/' . date('Y-m-d'));
        $dbname =  Config::get('database.connections.'.Config::get('database.default').'.database');
        shell_exec('mongodump -d '.$dbname.' --out ' . $backup_path);
    }

}
