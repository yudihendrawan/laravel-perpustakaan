<?php

namespace App\Http\Controllers;

use App\Anggota;
use App\Buku;
use App\Transaksi;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\TryCatch;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Daftar Peminjaman';
        $transaksi = Transaksi::with('anggota','buku','user')->orderBy('created_at','desc')->paginate(6);
        return view('transaksi.index',compact('title','transaksi'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('transaksi.create',[
            'title' => 'Tambah Transaksi',
            'buku' => Buku::orderBy('judul','asc')->get(),
            
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = [
            'required' => 'atribute tidak boleh kosong',
            'unique' => 'atribute sudah ada',
            'numeric' => 'atribute harus angka',
        ];
        $request->validate(['nim' => 'required | numeric',
            'tgl_pinjam' => 'required',
            'tgl_kembali' => 'required',
        ], $message);

        // Ambil anggota berdasarkan NIM
        $anggota = Anggota::where('nim', $request->nim)->first();

        // Tangani kasus ketika anggota tidak ditemukan
        if (!$anggota) {
            session()->flash('fail', 'Anggota dengan NIM ' . $request->nim . ' tidak ditemukan.');
            return redirect('transaksi');
        }

        // Ambil ID anggota
        $anggota_id = $anggota->id;

        // Tolak jika anggota sudah meminjam buku
        if (Transaksi::where('anggota_id', $anggota_id)->where('status', 'pinjam')->exists()) {
            session()->flash('fail', 'Maaf, Anda masih memiliki buku yang belum dikembalikan.');
            return redirect('transaksi');
        }

        // Cek ketersediaan stok buku sebelum membuat transaksi
        $buku = Buku::find($request->buku_id);
        if (!$buku || $buku->jumlah_buku < 1) {
            session()->flash('fail', 'Maaf, stok buku tidak tersedia.');
            return redirect('transaksi');
        }

        // Membuat transaksi jika kondisi di atas terpenuhi
        $transaksi = Transaksi::create([
            'anggota_id' => $anggota_id,
            'kode_transaksi' => Str::random(10),
            'buku_id' => $request->buku_id,
            'tgl_pinjam' => $request->tgl_pinjam,
            'tgl_kembali' => $request->tgl_kembali,
            'status' => 'pinjam',
            'ket' => $request->ket,
            'user_id' => Auth::user()->id
        ]);

        // Mengurangkan jumlah_buku pada buku terkait
        $transaksi->buku->update(['jumlah_buku' => $buku->jumlah_buku - 1]);

        return redirect('transaksi')->with('success', 'Transaksi Anda berhasil!');
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaksi = Transaksi::with('buku','anggota','user')->find($id);
        return view('transaksi.show',compact('transaksi'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $transaksi = Transaksi::with('buku','anggota','user')->find($id);   
        $buku = Buku::get();
        return view('transaksi.edit',compact('transaksi','buku'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $message = ['required' => 'atribute tidak boleh kosong' ];
        $request->validate([
            // 'tgl_pinjam' => 'required',
            // 'tgl_kembali' => 'required',
            'status' => 'required',
        ],$message);

        //ambil anggota id
        $anggota = Anggota::where('nim',$request->nim)->get();
        foreach ($anggota as $val) {
            $anggota_id = $val->id;
        }
        //update transaksi
        $transaksi = Transaksi::find($id);
        $transaksi->update([
            'anggota_id' => $anggota_id ?? $transaksi->anggota_id,
            'kode_transaksi' => Str::random(10),
            'buku_id' => $request->buku_id ?? $transaksi->buku_id,
            'tgl_pinjam' => $request->tgl_pinjam ?? $transaksi->tgl_pinjam,
            'tgl_kembali' => $request->tgl_kembali ?? $transaksi->tgl_kembali,
            'status' => $request->status ?? $transaksi->status,
            'ket' => $request->ket ?? $transaksi->ket,
            'user_id' => Auth::user()->id
        ]);


          //jika transaksi dilakukan maka stock buku akan berkurang 
          $transaksi->buku->where('id',$transaksi->buku_id)->update(['jumlah_buku' => $transaksi->buku->jumlah_buku +1]);

        return redirect('transaksi')->with('success','transaksi berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Transaksi::find($id)->delete();
        return redirect('transaksi')->with('success','data berhasil dihapus');
    }

    public function search(Request $request){


        $request->validate([
            'q' => 'required'
        ]);
        //cari dengan kode_transaksi
        $cari = $request->q;

        $title = 'Daftar Transaksi';
       
        $transaksi = Transaksi::where('kode_transaksi','LIKE',"%$cari%")->paginate();
       
        return view('transaksi.index',compact('title','transaksi'));
        
    }
}
