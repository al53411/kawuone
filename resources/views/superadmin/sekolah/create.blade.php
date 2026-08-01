@extends('layouts.app') {{-- Sesuaikan dengan layout utama projectmu --}}

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            {{-- Alert Sukses --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-lg rounded-4" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                <div class="card-header bg-success text-white py-3 rounded-top-4 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-building-add me-2"></i> Tambah Data Sekolah Baru</h5>
                </div>
                <div class="card-body p-4">
                    
                    <form action="{{ route('superadmin.sekolah.store') }}" method="POST">
                        @csrf

                        {{-- Section 1: Identitas Sekolah --}}
                        <h6 class="fw-bold text-success border-bottom pb-2 mb-3"><i class="bi bi-info-circle me-1"></i> Identitas Utama Sekolah</h6>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">NPSN <span class="text-danger">*</span></label>
                                <input type="number" name="npsn" class="form-control @error('npsn') is-invalid @enderror" placeholder="Contoh: 20501234" value="{{ old('npsn') }}" required>
                                @error('npsn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Sekolah <span class="text-danger">*</span></label>
                                <input type="text" name="nama_sekolah" class="form-control @error('nama_sekolah') is-invalid @enderror" placeholder="Contoh: SDN Kawu 1" value="{{ old('nama_sekolah') }}" required>
                                @error('nama_sekolah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jenjang Sekolah <span class="text-danger">*</span></label>
                                <select name="jenjang" class="form-select @error('jenjang') is-invalid @enderror" required>
                                    <option value="" selected disabled>-- Pilih Jenjang --</option>
                                    <option value="SD" {{ old('jenjang') == 'SD' ? 'selected' : '' }}>SD / MI</option>
                                    <option value="SMP" {{ old('jenjang') == 'SMP' ? 'selected' : '' }}>SMP / MTs</option>
                                    <option value="SMA" {{ old('jenjang') == 'SMA' ? 'selected' : '' }}>SMA / MA</option>
                                    <option value="SMK" {{ old('jenjang') == 'SMK' ? 'selected' : '' }}>SMK</option>
                                </select>
                                @error('jenjang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status Sekolah <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="" selected disabled>-- Pilih Status --</option>
                                    <option value="Negeri" {{ old('status') == 'Negeri' ? 'selected' : '' }}>Negeri</option>
                                    <option value="Swasta" {{ old('status') == 'Swasta' ? 'selected' : '' }}>Swasta</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Section 2: Alamat Lengkap --}}
                        <h6 class="fw-bold text-success border-bottom pb-2 mb-3 mt-4"><i class="bi bi-geo-alt me-1"></i> Alamat & Lokasi</h6>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Alamat Jalan / RT / RW <span class="text-danger">*</span></label>
                                <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="2" placeholder="Jl. Raya Kawu No. 01, RT 02/RW 01" required>{{ old('alamat') }}</textarea>
                                @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Desa / Kelurahan</label>
                                <input type="text" name="desa_kelurahan" class="form-control" placeholder="Kawu" value="{{ old('desa_kelurahan') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Kecamatan</label>
                                <input type="text" name="kecamatan" class="form-control" placeholder="Kedungalar" value="{{ old('kecamatan') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Kabupaten / Kota</label>
                                <input type="text" name="kabupaten_kota" class="form-control" placeholder="Ngawi" value="{{ old('kabupaten_kota') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Provinsi</label>
                                <input type="text" name="provinsi" class="form-control" placeholder="Jawa Timur" value="{{ old('provinsi') }}">
                            </div>
                        </div>

                        {{-- Section 3: Kontak & Penanggung Jawab --}}
                        <h6 class="fw-bold text-success border-bottom pb-2 mb-3 mt-4"><i class="bi bi-person-badge me-1"></i> Penanggung Jawab & Kontak</h6>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Kepala Sekolah & Gelar</label>
                                <input type="text" name="nama_kepsek" class="form-control" placeholder="Drs. Ahmad Dahlan, M.Pd" value="{{ old('nama_kepsek') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">NIP Kepala Sekolah (Opsional)</label>
                                <input type="text" name="nip_kepsek" class="form-control" placeholder="19800101 200501 1 001" value="{{ old('nip_kepsek') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No. Telepon / WhatsApp Sekolah</label>
                                <input type="text" name="telepon" class="form-control" placeholder="081234567890" value="{{ old('telepon') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Resmi Sekolah</label>
                                <input type="email" name="email" class="form-control" placeholder="sdnkawu1@sch.id" value="{{ old('email') }}">
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <button type="reset" class="btn btn-light px-4 rounded-3 fw-semibold">Reset Form</button>
                            <button type="submit" class="btn btn-success px-4 rounded-3 fw-semibold"><i class="bi bi-save me-1"></i> Simpan Data Sekolah</button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection