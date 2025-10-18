@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="row">
  <div class="col-md-2 stretch-card grid-margin">
    <div class="card bg-gradient-danger card-img-holder text-white">
      <div class="card-body">
        <h4 class="font-weight-normal mb-3">Total Kuota</h4>
        <h2 class="mb-5">{{ $totalKuota }}</h2>
        <h6 class="card-text">Kuota per hari</h6>
      </div>
    </div>
  </div>
  <div class="col-md-5 stretch-card grid-margin">
    <div class="card bg-gradient-danger card-img-holder text-white">
      <div class="card-body">
        <h4 class="font-weight-normal mb-3">Sisa Kuota</h4>
        <h2 class="mb-5">{{ $sisaKuota }}</h2>
        <h6 class="card-text">Kuota tersisa hari ini</h6>
      </div>
    </div>
  </div>

  <div class="col-md-5 stretch-card grid-margin">
    <div class="card bg-gradient-info card-img-holder text-white">
      <div class="card-body">
        <h4 class="font-weight-normal mb-3">Kuota Terpakai</h4>
        <h2 class="mb-5">{{ $kuotaTerpakai }}</h2>
        <h6 class="card-text">Hari ini ({{ now()->format('d M Y') }})</h6>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4 stretch-card grid-margin">
    <div class="card bg-gradient-success card-img-holder text-white">
      <div class="card-body">
        <h4 class="font-weight-normal mb-3">Permohonan Surat Sehat</h4>
        <h2 class="mb-5">{{ $suratSehat }}</h2>
        <h6 class="card-text">Diterima hari ini</h6>
      </div>
    </div>
  </div>
  <div class="col-md-4 stretch-card grid-margin">
    <div class="card bg-gradient-primary card-img-holder text-white">
      <div class="card-body">
        <h4 class="font-weight-normal mb-3">Permohonan Surat Kejiwaan</h4>
        <h2 class="mb-5">{{ $suratKejiwaan }}</h2>
        <h6 class="card-text">Diterima hari ini</h6>
      </div>
    </div>
  </div>
  <div class="col-md-4 stretch-card grid-margin">
    <div class="card bg-gradient-warning card-img-holder text-white">
      <div class="card-body">
        <h4 class="font-weight-normal mb-3">Permohonan Surat Narkoba</h4>
        <h2 class="mb-5">{{ $suratNarkoba }}</h2>
        <h6 class="card-text">Diterima hari ini</h6>
      </div>
    </div>
  </div>
</div>
@endsection
