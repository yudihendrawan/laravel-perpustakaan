<div class="form-group row">
    <label for="nama" class="col-sm-2 col-form-label">Nama</label>
    <div class="col-sm-10">
        <input type="text" disabled class="form-control" id="nama" value="{{ $anggota->nama }}">
    </div>
</div>

<div class="form-group row">
    <label for="nim" class="col-sm-2 col-form-label">NIM</label>
    <div class="col-sm-10">
        <input type="number" disabled class="form-control" id="nim" value="{{ $anggota->nim }}">
    </div>
</div>

<div class="form-group row">
    <label for="no_hp" class="col-sm-2 col-form-label">No HP</label>
    <div class="col-sm-10">
        <input type="number" disabled class="form-control" id="no_hp" value="{{ $anggota->no_hp }}">
    </div>
</div>

<div class="form-group row">
    <label for="tgl_lahir" class="col-sm-2 col-form-label">Tgl Lahir</label>
    <div class="col-sm-10">
        <input type="text" disabled class="form-control" id="tgl_lahir" value="{{ $anggota->tgl_lahir }}">
    </div>
</div>

<div class="form-group row">
    <label for="jurusan" class="col-sm-2 col-form-label">Jurusan</label>
    <div class="col-sm-10">
        <input type="text" disabled class="form-control" id="jurusan" value="{{ $anggota->jurusan }}">
    </div>
</div>

<div class="form-group row">
    <label for="jenis_kelamin" class="col-sm-2 col-form-label">Jenis Kelamin</label>
    <div class="col-sm-10">
        <input type="text" disabled class="form-control" id="jenis_kelamin" value="{{ $anggota->jenis_kelamin }}">
    </div>
</div>

<div class="form-group row">
    <label for="level" class="col-sm-2 col-form-label">Petugas</label>
    <div class="col-sm-10">
        <input type="text" disabled class="form-control" id="level" value="{{ $anggota->user->level }}">
    </div>
</div>
