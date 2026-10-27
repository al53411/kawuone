<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $siswa_id
 * @property int|null $kelas_id
 * @property int|null $guru_id
 * @property \Illuminate\Support\Carbon $tanggal
 * @property string $status
 * @property string $tipe_absen
 * @property string|null $mapel
 * @property string|null $jam_ke
 * @property string|null $catatan
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $guru
 * @property-read \App\Models\Kelas|null $kelas
 * @property-read \App\Models\Siswa $siswa
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereGuruId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereJamKe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereKelasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereMapel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereSiswaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereTipeAbsen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAbsensi {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $sekolah_id
 * @property int|null $guru_id
 * @property int $siswa_id
 * @property int $kelas_id
 * @property int|null $mapel_id
 * @property string $tanggal
 * @property int $pertemuan_ke
 * @property string $status
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Siswa $siswa
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiMapel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiMapel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiMapel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiMapel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiMapel whereGuruId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiMapel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiMapel whereKelasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiMapel whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiMapel whereMapelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiMapel wherePertemuanKe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiMapel whereSekolahId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiMapel whereSiswaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiMapel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiMapel whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiMapel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAbsensiMapel {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $sekolah_id
 * @property string $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereSekolahId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperActivityLog {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $sekolah_id
 * @property int $tujuan_pembelajaran_id
 * @property int $mapel_id
 * @property int|null $kelas_id
 * @property string $kode_atp
 * @property string $semester
 * @property int $urutan
 * @property int $alokasi_jp
 * @property string $rincian_materi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Kelas|null $kelas
 * @property-read \App\Models\Mapel $mapel
 * @property-read \App\Models\TujuanPembelajaran $tujuanPembelajaran
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlurTujuanPembelajaran newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlurTujuanPembelajaran newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlurTujuanPembelajaran query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlurTujuanPembelajaran whereAlokasiJp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlurTujuanPembelajaran whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlurTujuanPembelajaran whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlurTujuanPembelajaran whereKelasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlurTujuanPembelajaran whereKodeAtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlurTujuanPembelajaran whereMapelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlurTujuanPembelajaran whereRincianMateri($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlurTujuanPembelajaran whereSekolahId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlurTujuanPembelajaran whereSemester($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlurTujuanPembelajaran whereTujuanPembelajaranId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlurTujuanPembelajaran whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlurTujuanPembelajaran whereUrutan($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAlurTujuanPembelajaran {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $modul_ajar_id
 * @property string $jenis_asesmen
 * @property string $teknik_penilaian
 * @property string|null $instrumen_penilaian
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asesmen newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asesmen newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asesmen query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asesmen whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asesmen whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asesmen whereInstrumenPenilaian($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asesmen whereJenisAsesmen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asesmen whereModulAjarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asesmen whereTeknikPenilaian($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asesmen whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAsesmen {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $sekolah_id
 * @property int $mapel_id
 * @property string $fase
 * @property string $elemen
 * @property string $deskripsi_cp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Mapel $mapel
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ModulAjar> $modulAjars
 * @property-read int|null $modul_ajars_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TujuanPembelajaran> $tujuanPembelajarans
 * @property-read int|null $tujuan_pembelajarans_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapaianPembelajaran newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapaianPembelajaran newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapaianPembelajaran query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapaianPembelajaran whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapaianPembelajaran whereDeskripsiCp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapaianPembelajaran whereElemen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapaianPembelajaran whereFase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapaianPembelajaran whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapaianPembelajaran whereMapelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapaianPembelajaran whereSekolahId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CapaianPembelajaran whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperCapaianPembelajaran {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $sekolah_id
 * @property string $nik
 * @property string $nama_lengkap
 * @property string $tempat_lahir
 * @property \Illuminate\Support\Carbon $tanggal_lahir
 * @property string $jenis_kelamin
 * @property string $nama_ibu_kandung
 * @property string|null $nip
 * @property string $status_kepegawaian
 * @property string|null $golongan
 * @property string|null $jabatan
 * @property string|null $jenis_guru
 * @property string|null $mata_pelajaran
 * @property \Illuminate\Support\Carbon|null $tmt_sk
 * @property int $mkg_tahun
 * @property int $mkg_bulan
 * @property string $pendidikan_terakhir
 * @property string|null $nuptk
 * @property string|null $no_serdik
 * @property string|null $nrg
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Kelas> $kelas
 * @property-read int|null $kelas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Kelas> $kelasWali
 * @property-read int|null $kelas_wali_count
 * @property-read \App\Models\Sekolah|null $sekolah
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereGolongan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereJabatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereJenisGuru($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereJenisKelamin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereMataPelajaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereMkgBulan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereMkgTahun($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereNamaIbuKandung($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereNamaLengkap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereNik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereNip($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereNoSerdik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereNrg($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereNuptk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru wherePendidikanTerakhir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereSekolahId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereStatusKepegawaian($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereTanggalLahir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereTempatLahir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereTmtSk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guru whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperGuru {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $guru_id
 * @property int|null $kelas_id
 * @property string $hari
 * @property string $tanggal
 * @property string $jam_ke
 * @property string $mapel
 * @property string $materi
 * @property string $kegiatan
 * @property string|null $keterangan
 * @property string $status_validasi
 * @property string|null $catatan_kepsek
 * @property string|null $tanggal_validasi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $guru
 * @property-read \App\Models\Kelas|null $kelas
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JurnalGuru newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JurnalGuru newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JurnalGuru query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JurnalGuru whereCatatanKepsek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JurnalGuru whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JurnalGuru whereGuruId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JurnalGuru whereHari($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JurnalGuru whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JurnalGuru whereJamKe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JurnalGuru whereKegiatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JurnalGuru whereKelasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JurnalGuru whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JurnalGuru whereMapel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JurnalGuru whereMateri($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JurnalGuru whereStatusValidasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JurnalGuru whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JurnalGuru whereTanggalValidasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JurnalGuru whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperJurnalGuru {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $sekolah_id
 * @property int|null $guru_id
 * @property string $nama_kelas
 * @property string|null $wali_kelas
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Guru|null $guru
 * @property-read \App\Models\Guru|null $gurus
 * @property-read \App\Models\Sekolah|null $sekolah
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Siswa> $siswas
 * @property-read int|null $siswas_count
 * @property-read \App\Models\Guru|null $waliKelas
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereGuruId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereNamaKelas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereSekolahId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kelas whereWaliKelas($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperKelas {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $modul_ajar_id
 * @property int $pertemuan_ke
 * @property string $kegiatan_awal
 * @property string $kegiatan_inti
 * @property string $kegiatan_penutup
 * @property int|null $alokasi_menit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LangkahPembelajaran newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LangkahPembelajaran newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LangkahPembelajaran query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LangkahPembelajaran whereAlokasiMenit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LangkahPembelajaran whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LangkahPembelajaran whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LangkahPembelajaran whereKegiatanAwal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LangkahPembelajaran whereKegiatanInti($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LangkahPembelajaran whereKegiatanPenutup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LangkahPembelajaran whereModulAjarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LangkahPembelajaran wherePertemuanKe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LangkahPembelajaran whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLangkahPembelajaran {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $sekolah_id
 * @property string|null $kode_mapel
 * @property string $nama_mapel
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Kelas> $kelases
 * @property-read int|null $kelases_count
 * @property-read \App\Models\Sekolah|null $sekolah
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel whereKodeMapel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel whereNamaMapel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel whereSekolahId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mapel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMapel {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $mapel_id
 * @property int|null $cp_id
 * @property string $judul
 * @property string $fase
 * @property \App\Models\Kelas|null $kelas
 * @property string $alokasi_waktu
 * @property string $tahun_ajaran
 * @property string $semester
 * @property string|null $kompetensi_awal
 * @property array<array-key, mixed>|null $profil_pelajar_pancasila
 * @property string|null $sarana_prasarana
 * @property string $target_peserta_didik
 * @property string|null $model_pembelajaran
 * @property string|null $pemahaman_bermakna
 * @property string|null $pertanyaan_pemantik
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AlurTujuanPembelajaran|null $alurTujuanPembelajaran
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asesmen> $asesmens
 * @property-read int|null $asesmens_count
 * @property-read \App\Models\CapaianPembelajaran|null $capaianPembelajaran
 * @property-read mixed $deskripsi_cp
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ModulLampiran> $lampirans
 * @property-read int|null $lampirans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LangkahPembelajaran> $langkahPembelajarans
 * @property-read int|null $langkah_pembelajarans_count
 * @property-read \App\Models\Mapel $mapel
 * @property-read \App\Models\Sekolah|null $sekolah
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TujuanPembelajaran> $tujuanPembelajarans
 * @property-read int|null $tujuan_pembelajarans_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar whereAlokasiWaktu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar whereCpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar whereFase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar whereJudul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar whereKelas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar whereKompetensiAwal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar whereMapelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar whereModelPembelajaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar wherePemahamanBermakna($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar wherePertanyaanPemantik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar whereProfilPelajarPancasila($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar whereSaranaPrasarana($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar whereSemester($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar whereTahunAjaran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar whereTargetPesertaDidik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulAjar whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperModulAjar {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $modul_ajar_id
 * @property string $tipe
 * @property string $judul_lampiran
 * @property string|null $konten_teks
 * @property string|null $file_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulLampiran newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulLampiran newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulLampiran query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulLampiran whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulLampiran whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulLampiran whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulLampiran whereJudulLampiran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulLampiran whereKontenTeks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulLampiran whereModulAjarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulLampiran whereTipe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModulLampiran whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperModulLampiran {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $sekolah_id
 * @property int $kelas_id
 * @property int $mapel_id
 * @property int $tp_id
 * @property int $siswa_id
 * @property numeric $nilai Skala Nilai 0 - 100
 * @property string|null $catatan Deskripsi/Catatan Capaian TP Siswa
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Kelas $kelas
 * @property-read \App\Models\Mapel $mapel
 * @property-read \App\Models\Sekolah|null $sekolah
 * @property-read \App\Models\Siswa $siswa
 * @property-read \App\Models\TujuanPembelajaran $tujuanPembelajaran
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NilaiFormatif newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NilaiFormatif newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NilaiFormatif query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NilaiFormatif whereCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NilaiFormatif whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NilaiFormatif whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NilaiFormatif whereKelasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NilaiFormatif whereMapelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NilaiFormatif whereNilai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NilaiFormatif whereSekolahId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NilaiFormatif whereSiswaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NilaiFormatif whereTpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NilaiFormatif whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperNilaiFormatif {}
}

namespace App\Models{
/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilSekolah newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilSekolah newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilSekolah query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilSekolah whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilSekolah whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilSekolah whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProfilSekolah {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $npsn
 * @property string $nama_sekolah
 * @property string $jenjang
 * @property string $status
 * @property string|null $alamat
 * @property string|null $kecamatan
 * @property string|null $kabupaten_kota
 * @property string|null $provinsi
 * @property string|null $kode_pos
 * @property string|null $email
 * @property string|null $telepon
 * @property string|null $nama_kepsek
 * @property string|null $nip_kepsek
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Guru> $gurus
 * @property-read int|null $gurus_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JurnalGuru> $jurnals
 * @property-read int|null $jurnals_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Kelas> $kelases
 * @property-read int|null $kelases_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Siswa> $siswas
 * @property-read int|null $siswas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sekolah newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sekolah newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sekolah query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sekolah whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sekolah whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sekolah whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sekolah whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sekolah whereJenjang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sekolah whereKabupatenKota($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sekolah whereKecamatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sekolah whereKodePos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sekolah whereNamaKepsek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sekolah whereNamaSekolah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sekolah whereNipKepsek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sekolah whereNpsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sekolah whereProvinsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sekolah whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sekolah whereTelepon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sekolah whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSekolah {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $kelas_id
 * @property int|null $sekolah_id
 * @property string|null $nisn
 * @property string $nama_siswa
 * @property string $jenis_kelamin
 * @property string|null $alamat
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Kelas|null $kelas
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereJenisKelamin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereKelasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereNamaSiswa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereNisn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereSekolahId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Siswa whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSiswa {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $sekolah_id
 * @property string $tahun
 * @property string $semester
 * @property bool $is_aktif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran whereIsAktif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran whereSekolahId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran whereSemester($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran whereTahun($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TahunAjaran whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTahunAjaran {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $cp_id
 * @property int|null $modul_ajar_id
 * @property int|null $sekolah_id
 * @property int $mapel_id
 * @property int|null $kelas_id
 * @property string|null $fase
 * @property string|null $elemen
 * @property string|null $kode_tp
 * @property string $deskripsi_tp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Kelas|null $kelas
 * @property-read \App\Models\Mapel $mapel
 * @property-read \App\Models\Sekolah|null $sekolah
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TujuanPembelajaran newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TujuanPembelajaran newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TujuanPembelajaran query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TujuanPembelajaran whereCpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TujuanPembelajaran whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TujuanPembelajaran whereDeskripsiTp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TujuanPembelajaran whereElemen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TujuanPembelajaran whereFase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TujuanPembelajaran whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TujuanPembelajaran whereKelasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TujuanPembelajaran whereKodeTp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TujuanPembelajaran whereMapelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TujuanPembelajaran whereModulAjarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TujuanPembelajaran whereSekolahId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TujuanPembelajaran whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTujuanPembelajaran {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $sekolah_id
 * @property string $name
 * @property string|null $nip
 * @property string|null $email
 * @property string $password
 * @property string $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Guru|null $guru
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JurnalGuru> $jurnals
 * @property-read int|null $jurnals_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNip($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSekolahId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

