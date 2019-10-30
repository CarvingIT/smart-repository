<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportsController extends Controller
{

    public function index(){
        return view('reports-index');
    }

    public function downloads(){
        $hits = \DB::table('document_downloads')
            ->select(\DB::raw('DATE(added_on) as date'), \DB::raw('count(id) as cnt'))
            ->groupBy('date')->get();
        return view('report-date-count',['hits'=>$hits, 'name'=>'Downloads']);
    }

    public function uploads(){
        $hits = \DB::table('document_revisions')
            ->select(\DB::raw('DATE(created_at) as date'), \DB::raw('count(id) as cnt'))
            ->groupBy('date')->get();
        return view('report-date-count',['hits'=>$hits, 'name'=>'Uploads']);
    }

}
