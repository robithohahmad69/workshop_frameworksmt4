@extends('layouts.apps')

@section('title', 'Select Kota')
@section('page-title', 'Select Kota')
@section('icon', 'mdi mdi-form-select')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/home">Home</a></li>
    <li class="breadcrumb-item active">Select 2</li>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #ced4da !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal !important;
        padding-left: 12px !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        top: 0 !important;
    }
</style>
@endsection

@section('content')
<div class="row">
<div class="col-lg-12 grid-margin stretch-card">
<div class="card">

    <div class="card-header">
        <h4 class="card-title mb-0">Select 2</h4>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function(){

    $('#selectKota').select2({
        placeholder: "Pilih Kota",
        width: '100%'
    });

    document.getElementById("btnTambah").addEventListener("click", function(){
        let kota = document.getElementById("kotaInput").value.trim();

        if(kota == ""){
            alert("Kota harus diisi");
            return;
        }

        let newOption = new Option(kota, kota, false, false);
        $('#selectKota').append(newOption).trigger('change');

        document.getElementById("kotaInput").value = "";
    });

    $('#selectKota').on('change', function(){
        let kota = $(this).val();
        $('#kotaTerpilih').text(kota);
    });

});
</script>
@endpush