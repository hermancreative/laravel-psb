<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\CalonSiswa;
use App\Pembayaran;
use App\TesSeleksi;
use App\JadwalPendaftaran;
use App\TahunAjaran;
use App\DaftarUlang;
use App\QR;
use DB;
use Auth;

use CodeItNow\BarcodeBundle\Utils\QrCode;
use Image;
use Faker;

class PrintController extends Controller
{
	public $jenis;
	public $no_qr;
	public $no_pendf;

	private $user;
	// private $carbon;

	public function __construct(){
		$this->middleware(function ($request, $next) {
	        $this->user = Auth::user()->calonSiswa;

	        return $next($request);
	    });

	    // $this->carbon = Carbon::now();
	}

    public function index($jenis, $id){
    	$this->jenis = $jenis;
    	if ($this->jenis == 'pembayaran') {
    		$pembayaran = Pembayaran::find($id);
    		$this->no_pendf = $pembayaran->no_pendf;
    		$img_qr = $this->QrCode();
    		$no_qr = $this->no_qr;
			return view('pages.print.pembayaran', compact('no_qr','img_qr','pembayaran'));
    	} else if ($this->jenis == 'tesseleksi') {
    		$tesseleksi = TesSeleksi::find($id);
    		$this->no_pendf = $tesseleksi->no_pendf;
    		$img_qr = $this->QrCode();
    		$no_qr = $this->no_qr;
			return view('pages.print.tesseleksi', compact('no_qr','img_qr','tesseleksi'));
    	}  else if ($this->jenis == 'daftarulang') {
    		$daftarulang = DaftarUlang::find($id);
			$calonsiswa = CalonSiswa::find($daftarulang->no_pendf);
			$jadwalPendaf = JadwalPendaftaran::find($calonsiswa->id_jadwal);
			$tahunAjaran = TahunAjaran::find($jadwalPendaf->id_th_ajaran);
			// $tahunAjaran = DB::table('calon_siswas')
			// 				->join('jadwal_pendaftarans','jadwal_pendaftarans.id_jadwal','calon_siswas.id_jadwal')
			// 				->join('tahun_ajarans','tahun_ajarans.id_th_ajaran','jadwal_pendaftarans.id_th_ajaran')
			// 				->where('calon_siswas.no_pendf',$daftarulang->no_pendf)
			// 				->select('tahun_ajarans.th_ajaran','jadwal_pendaftarans.nm_jadwal')->get();
			// var_dump(json_encode($tahunAjaran));dd();
    		$this->no_pendf = $daftarulang->no_pendf;
    		$img_qr = $this->QrCode();
    		$no_qr = $this->no_qr;
			return view('pages.print.daftarulang', compact('no_qr','img_qr','daftarulang','jadwalPendaf','tahunAjaran'));
		}
    }

	public function cetak($jenis, $id){
    	$this->jenis = $jenis;
    	if ($this->jenis == 'pendaftaran') {
			$jadwal = JadwalPendaftaran::find($id);
			$calonsiswas = CalonSiswa::where('id_jadwal',$id)->get();
			return view('pages.print.pendaftaran', compact('calonsiswas','jadwal'));
    	} else if ($this->jenis == 'seleksi') {
			$jadwals = JadwalPendaftaran::find($id);
			$seleksis = DB::table('tes_seleksis')
								->join('calon_siswas','calon_siswas.no_pendf','tes_seleksis.no_pendf')
								->join('jadwal_pendaftarans','jadwal_pendaftarans.id_jadwal','calon_siswas.id_jadwal')
								->join('tahun_ajarans','tahun_ajarans.id_th_ajaran','jadwal_pendaftarans.id_th_ajaran')
								->join('jurusans','jurusans.kd_jurusan','calon_siswas.kd_jurusan')
								->where('jadwal_pendaftarans.id_jadwal',$jadwals->id_jadwal)        					
								->select('calon_siswas.no_pendf','calon_siswas.nm_cln_siswa','calon_siswas.alamat','calon_siswas.status_penerimaan','jurusans.nm_jurusan')
								->get();
			// var_dump(json_encode($seleksis));dd();
			return view('pages.print.seleksi', compact('seleksis','jadwals'));
		} else if ($this->jenis == 'siswa') {
			$tahunajarans = TahunAjaran::find($id);
			$siswas = DB::table('tahun_ajarans')
						->join('jadwal_pendaftarans','jadwal_pendaftarans.id_th_ajaran','tahun_ajarans.id_th_ajaran')
						->join('calon_siswas','calon_siswas.id_jadwal','jadwal_pendaftarans.id_jadwal')
						->join('jurusans','jurusans.kd_jurusan','calon_siswas.kd_jurusan')
						->join('kelas','kelas.kd_kelas','calon_siswas.kd_kelas')
						->where('tahun_ajarans.id_th_ajaran',$tahunajarans->id_th_ajaran)
						->select('jadwal_pendaftarans.nm_jadwal','calon_siswas.no_pendf','calon_siswas.nm_cln_siswa','jurusans.nm_jurusan','kelas.nm_kelas')
						->orderBy('calon_siswas.no_pendf')
						->get();
			return view('pages.print.siswa', compact('siswas','tahunajarans'));
    	}
    }

	public function cetakRegistrasi(){
		$pembayarans = Pembayaran::all();
		return view('pages.print.registrasi', compact('pembayarans'));
    }

	public function generateUUID(){
		$faker = Faker\Factory::create('id_ID');
		return $faker->uuid;
	}

	public function QrCode(){
    	$qrCode = new QrCode();
		$this->no_qr = $this->generateUUID();
		$qrCode->setText($this->no_qr)
				    ->setSize(400)
				    ->setPadding(10)
				    ->setErrorCorrection('high')
				    ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
				    ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
				    ->setLabel($this->no_qr)
				    ->setLabelFontSize(15)
				    ->setImageType(QrCode::IMAGE_TYPE_PNG);

		// Save image base64 to PNG
		$png_url = $this->no_qr . ".png";
		$path = 'img/qrcode/' . $png_url;
		
		$img_save = Image::make($qrCode->generate())->save($path);

		$image_data = 'data:' . $qrCode->getContentType() . ';base64,' . $qrCode->generate();

		$qr = new QR();
		$qr->qr_code = $this->no_qr;
		$qr->qr_code_image = $path;
		$qr->jenis = $this->jenis;
		$qr->no_pendf = $this->no_pendf;
		$qr->save();

		return $image_data;
    }
}