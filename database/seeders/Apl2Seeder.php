<?php

namespace Database\Seeders;

use App\Models\APL2;
use App\Models\Skema;
use Illuminate\Database\Seeder;

class Apl2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get skema IDs
        $analisProgram = Skema::where('kode', 'AP')->first();
        $asistenPemrograman = Skema::where('kode', 'ASP')->first();
        $cyberSecurityAnalyst = Skema::where('kode', 'CSA')->first();
        $systemAnalyst = Skema::where('kode', 'SA')->first();
        $juniorWebProgrammer = Skema::where('kode', 'JWP')->first();

        // Analis Program (AP) - 20 soal
        if ($analisProgram) {
            $analisProgramSoal = [
                'Apa yang dimaksud dengan analisis program dalam siklus hidup pengembangan sistem?',
                'Sebutkan tiga tujuan utama dari analisis program!',
                'Apa perbedaan antara analisis program dan desain program?',
                'Jelaskan tahapan dalam proses analisis program!',
                'Apa yang dimaksud dengan kebutuhan fungsional?',
                'Apa yang dimaksud dengan kebutuhan non-fungsional?',
                'Sebutkan teknik yang digunakan dalam pengumpulan kebutuhan!',
                'Apa itu stakeholder dalam konteks analisis program?',
                'Jelaskan perbedaan antara kebutuhan eksplisit dan implisit!',
                'Apa yang dimaksud dengan feasibility study?',
                'Sebutkan aspek yang dianalisis dalam feasibility study!',
                'Apa itu use case dalam analisis program?',
                'Sebutkan contoh kebutuhan fungsional dari sebuah sistem e-commerce!',
                'Sebutkan contoh kebutuhan non-fungsional dari sistem informasi akademik!',
                'Apa yang dimaksud dengan prototyping dalam analisis program?',
                'Jelaskan manfaat dari dokumentasi kebutuhan!',
                'Apa itu requirement traceability?',
                'Sebutkan tools yang dapat digunakan dalam analisis program!',
                'Apa yang dimaksud dengan gap analysis?',
                'Jelaskan peran analis program dalam tim pengembangan!'
            ];

            foreach ($analisProgramSoal as $soal) {
                APL2::create([
                    'skema_id' => $analisProgram->id,
                    'question_text' => $soal,
                ]);
            }
        }

        // Asisten Pemrograman (ASP) - 20 soal
        if ($asistenPemrograman) {
            $asistenPemrogramanSoal = [
                'Apa yang dimaksud dengan pemrograman?',
                'Sebutkan tiga jenis tipe data dasar dalam bahasa pemrograman!',
                'Apa fungsi dari perintah "if" dalam pemrograman?',
                'Jelaskan perbedaan antara variabel dan konstanta!',
                'Apa perbedaan antara operator "==" dan "=" dalam pemrograman?',
                'Apa yang dimaksud dengan array dalam pemrograman?',
                'Apa perbedaan antara "while" dan "do while"?',
                'Jelaskan fungsi dari perintah "for" dalam pemrograman!',
                'Apa yang dimaksud dengan function dalam pemrograman?',
                'Sebutkan keuntungan menggunakan function!',
                'Apa yang dimaksud dengan parameter dalam function?',
                'Apa hasil dari "print(3 + 2 * 2)" di Python?',
                'Jelaskan perbedaan antara compiler dan interpreter!',
                'Apa yang dimaksud dengan debugging?',
                'Apa itu debugging dalam konteks pemrograman?',
                'Sebutkan tools debugging yang populer!',
                'Apa yang dimaksud dengan version control?',
                'Sebutkan dua praktik coding yang baik (clean code)!',
                'Apa peran dari Asisten Pemrograman dalam sebuah tim pengembangan perangkat lunak?',
                'Jelaskan pentingnya dokumentasi dalam pemrograman!'
            ];

            foreach ($asistenPemrogramanSoal as $soal) {
                APL2::create([
                    'skema_id' => $asistenPemrograman->id,
                    'question_text' => $soal,
                ]);
            }
        }

        // Cyber Security Analyst (CSA) - 20 soal
        if ($cyberSecurityAnalyst) {
            $cyberSecuritySoal = [
                'Apa yang dimaksud dengan cyber security?',
                'Sebutkan tiga pilar utama keamanan informasi (CIA Triad)!',
                'Apa perbedaan antara threat dan vulnerability?',
                'Apa yang dimaksud dengan malware?',
                'Sebutkan dua contoh jenis malware!',
                'Jelaskan apa itu serangan DDoS (Distributed Denial of Service)!',
                'Apa yang dimaksud dengan phishing?',
                'Apa itu firewall dan apa fungsinya?',
                'Apa fungsi dari antivirus?',
                'Sebutkan satu tool populer untuk analisis jaringan!',
                'Apa itu IDS dan apa fungsinya?',
                'Mengapa update/patching sistem penting?',
                'Apa yang dimaksud dengan network segmentation?',
                'Sebutkan protokol jaringan yang aman untuk pertukaran data!',
                'Apa yang dimaksud dengan enkripsi data?',
                'Apa perbedaan antara enkripsi simetris dan asimetris?',
                'Apa itu hashing dan apa fungsinya dalam keamanan?',
                'Apa yang dimaksud dengan incident response?',
                'Sebutkan satu contoh langkah awal dalam proses forensik digital!',
                'Apa peran Cyber Security Analyst dalam organisasi?'
            ];

            foreach ($cyberSecuritySoal as $soal) {
                APL2::create([
                    'skema_id' => $cyberSecurityAnalyst->id,
                    'question_text' => $soal,
                ]);
            }
        }

        // System Analyst (SA) - 20 soal
        if ($systemAnalyst) {
            $systemAnalystSoal = [
                'Apa peran utama seorang System Analyst dalam proyek pengembangan sistem informasi?',
                'Sebutkan tiga tanggung jawab utama System Analyst!',
                'Apa yang dimaksud dengan analisis kebutuhan sistem?',
                'Jelaskan perbedaan antara kebutuhan fungsional dan non-fungsional!',
                'Apa itu SRS (Software Requirements Specification)?',
                'Sebutkan dua jenis diagram yang digunakan dalam analisis sistem!',
                'Apa yang dimaksud dengan UML dalam analisis sistem?',
                'Jelaskan perbedaan antara use case dan user story!',
                'Apa itu stakeholder dalam konteks analisis sistem?',
                'Sebutkan teknik yang digunakan dalam pengumpulan kebutuhan!',
                'Apa yang dimaksud dengan feasibility study?',
                'Jelaskan aspek teknis dalam feasibility study!',
                'Apa itu prototyping dalam analisis sistem?',
                'Sebutkan jenis-jenis prototyping!',
                'Apa yang dimaksud dengan requirement traceability?',
                'Jelaskan pentingnya dokumentasi dalam analisis sistem!',
                'Apa itu UAT (User Acceptance Test)?',
                'Sebutkan tools yang digunakan System Analyst!',
                'Apa yang dimaksud dengan gap analysis?',
                'Jelaskan peran System Analyst dalam tim pengembangan!'
            ];

            foreach ($systemAnalystSoal as $soal) {
                APL2::create([
                    'skema_id' => $systemAnalyst->id,
                    'question_text' => $soal,
                ]);
            }
        }

        // Junior Web Programmer (JWP) - 20 soal
        if ($juniorWebProgrammer) {
            $juniorWebProgrammerSoal = [
                'Apa fungsi utama HTML dalam pengembangan web?',
                'Sebutkan tag HTML untuk membuat link!',
                'Apa perbedaan antara tag <div> dan <span> dalam HTML?',
                'Apa itu CSS dan apa fungsinya?',
                'Bagaimana cara menghubungkan file CSS eksternal ke HTML?',
                'Apa itu media query dalam CSS?',
                'Apa perbedaan antara padding dan margin?',
                'Bagaimana cara membuat teks berwarna biru menggunakan CSS?',
                'Apa itu JavaScript dalam konteks pemrograman web?',
                'Berikan contoh sintaks JavaScript untuk menampilkan alert pop-up!',
                'Apa itu DOM (Document Object Model)?',
                'Bagaimana cara mendapatkan elemen HTML berdasarkan ID menggunakan JavaScript?',
                'Apa fungsi addEventListener() dalam JavaScript?',
                'Apa fungsi utama bahasa server-side seperti PHP dalam pemrograman web?',
                'Berikan contoh kode PHP dasar untuk menampilkan "Hello PHP"!',
                'Apa itu database dan hubungannya dengan web?',
                'Apa peran localhost dalam pengembangan web lokal?',
                'Sebutkan tag HTML untuk membuat form input teks!',
                'Bagaimana cara memvalidasi form agar tidak ada field kosong menggunakan JavaScript?',
                'Sebutkan dua tool populer untuk mengembangkan dan menguji aplikasi web!'
            ];

            foreach ($juniorWebProgrammerSoal as $soal) {
                APL2::create([
                    'skema_id' => $juniorWebProgrammer->id,
                    'question_text' => $soal,
                ]);
            }
        }
    }
}
