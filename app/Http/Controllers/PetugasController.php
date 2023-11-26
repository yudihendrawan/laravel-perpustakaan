<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PetugasController extends Controller
{
 public function index()
 {
  return view('petugas.index', [
   'title' => 'Daftar Petugas',
   'petugas' => User::orderBy('name', 'asc')->paginate(4),
  ]);
 }

 public function store(Request $request)
 {
  $request->validate([
   'name' => 'required|max:255',
   'email' => 'required|email:dns|unique:users',
   'password' => 'required|min:5|max:255',
   'level' => 'required',
  ]);

  $validatedData = $request->all();
  $validatedData['password'] = Hash::make($validatedData['password']);

  User::create($validatedData);

  return redirect('petugas')->with('success', 'Petugas berhasil ditambahkan');
 }

 public function show($id)
 {
  $petugas = User::with('petugas')->find($id);
  return view('petugas.show', compact('petugas'));
 }

 public function edit($id)
 {
  $petugas = User::find($id);
  return view('petugas.edit', compact('petugas'));
 }

 public function update(Request $request, $id)
 {
  $request->validate([
   'name' => 'required',
   'email' => 'required',
   'password' => 'required|min:6',
  ]);

  $user = User::find($id);
  $user->update([
   'name' => $request->name ?? $user->name,
   'email' => $request->email ?? $user->email,
   'password' => Hash::make($request->password) ?? $user->password,
   'level' => $request->level ?? $user->level,
   'updated_at' => Carbon::now(),
  ]);

  return back()->with('success', 'Data berhasil diedit');
 }

 public function destroy($id)
 {
  User::find($id)->delete();
  return redirect('petugas')->with('success', 'Petugas berhasil dihapus');
 }

 public function search(Request $request)
 {
  $cari = $request->get('q');
  $petugas = User::where('email', 'LIKE', "%$cari%")->orWhere('name', 'LIKE', "%$cari%")->paginate();
  return view('petugas.index', [
   'title' => 'Daftar Petugas',
   'petugas' =>  $petugas,
  ]);
 }
}
