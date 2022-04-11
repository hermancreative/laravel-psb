<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\TahunAjaran;
use Auth;

class TahunAjaranController extends Controller
{
    public function index(){
    	$tahunajarans = TahunAjaran::paginate(10);
		$admin = Auth::user()->username;
    	return view('pages.admin.tahunajaran.index',compact('tahunajarans','admin'));
    }

    public function postAdd(Request $req){
    	$tahunajaran = new TahunAjaran();
    	$tahunajaran->tahun_ajaran = $req->tahun_ajaran;
    	if ($tahunajaran->save()) {
    		flash('Tahun Ajaran Berhasil Ditambahkan')->success();
    	} else {
			flash('Tahun Ajaran Tidak Berhasil Ditambahkan')->error();
    	}

    	return redirect(route('indexTahunAjaranAdmin'));
    }

    public function delete($id){
    	if ($tahunajaran = TahunAjaran::find($id)->delete()) {
    		flash('Tahun Ajaran Berhasil Dihapus')->success();
    	} else {
			flash('Tahun Ajaran Tidak Berhasil Dihapus')->error();
    	}

    	return redirect(route('indexTahunAjaranAdmin'));
    }

    // public function search($id){

    	
    // 	return view('pages.admin.tahunajaran.index',compact('tahunajarans'));
    // }   
}