<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Code;

class CodesController extends Controller
{
    public function index(){
        $codes = Code::orderBy('id', 'desc')->paginate(15);
        return view('admin.pages.codes.index', compact('codes'));
    }
}
