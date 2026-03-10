@extends('layouts.apps')

@section('title', 'Select Kota')
@section('page-title', 'Select Kota')
@section('icon', 'mdi mdi-form-select')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/home">Home</a></li>
    <li class="breadcrumb-item active">Select</li>
@endsection


@section('content')

<div class="row">
<div class="col-lg-12 grid-margin stretch-card">
<div class="card">

    <div class="card-header">
        <h4 class="card-title mb-0">Select</h4>
    </div>

    <div class="card-body">

        <div class="form-group">
            <label>Kota:</label>
            <input type="text" id="kotaInput" class="form-control">
        </div>

        <button id="btnTambah" class="btn btn-success mt-2">
        Tambahkan
        </button>

        <br><br>

        <div class="form-group">
            <label>Select Kota:</label>
            <select id="selectKota" class="form-control">
            <option value="">-- pilih kota --</option>
            </select>
        </div>

        

        <div>
        <h5>Kota Terpilih:</h5>
        <p id="kotaTerpilih"></p>
        </div>

    </div>
</div>
</div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById("btnTambah").addEventListener("click", function(){
    let kota = document.getElementById("kotaInput").value.trim();

    if(kota == ""){
        alert("Kota harus diisi");
        return;
    }

    let select = document.getElementById("selectKota");

    let option = document.createElement("option");
    option.value = kota;
    option.text  = kota;
    select.appendChild(option);

    document.getElementById("kotaInput").value = "";
});

document.getElementById("selectKota").addEventListener("change", function(){
    let kota = this.value;
    document.getElementById("kotaTerpilih").innerText = kota;
});
</script>
@endpush

