<?php

require_once __DIR__."/conversationZakatMaalDalil.php";
require_once __DIR__."/conversationZakatMaalEmas.php";
require_once __DIR__."/conversationZakatMaalPenghasilan.php";
require_once __DIR__."/conversationZakatMaalPeternakan.php";
require_once __DIR__."/conversationZakatMaalPertanian.php";

use BotMan\BotMan\BotMan;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;

//
    class conversationZakatMaal extends Conversation
    {
        public function askPilihan()
        {
            $question = Question::create('Apa yang ingin anda tanyakan terkait Zakat Maal?')
            ->addButtons([
                Button::create('Lihat Penjelasan')->value('0'),
                Button::create('Lihat Dalil')->value('1'),
                Button::create('Perhitungan Zakat Maal')->value('3'),
                Button::create('Selesai')->value('4'),
                Button::create('Kembali')->value('5'),
            ]);

            $this->ask($question, function (Answer $answer) {
                if ($answer->getValue() === '0') {
                    $this->pengertianZMaal();
                }
                elseif ($answer->getValue() === '1') {
                    $this->bot->startConversation(new conversationZakatMaalDalil());

                }
                elseif ($answer->getValue() === '3') {
                    $this->askTabungan();
                }
                elseif ($answer->getValue() === '5') {
                    $this->bot->startConversation(new conversationZakat);
                }
                else
                {
                    $this->finish();
                }
            });
        }
        public function pengertianZMaal()
        {
            $this->say("<b>Pengertian Zakat Maal</b>", ['parse_mode'=>'HTML']);
            $this->say(" Zakat mal adalah zakat yang dikenakan atas segala jenis harta, yang secara zat maupun substansi perolehannya, tidak bertentangan dengan ketentuan agama. Sebagai contoh, zakat mal terdiri atas uang, emas, surat berharga, penghasilan profesi, dan lain-lain, sebagaimana yang terdapat dalam UU No. 23/2011 tentang Pengelolaan Zakat, Peraturan Menteri Agama No. 52 Tahun 2014 yang telah diubah dua kali dengan perubahan kedua adalah Peraturan Menteri Agama No. 31/2019, dan pendapat Syaikh Dr. Yusuf Al-Qardhawi serta para ulama lainnya.");        
            $this->askConfirm();
        }
//

public function getHargaEmas() {
    $client = new Client();
    try {
        $response = $client->request('GET', 'https://logam-mulia-api.vercel.app/prices/hargaemas-org');
        $data = json_decode($response->getBody(), true);

        if (isset($data['data']['0'])) {
            $goldData = $data['data'][0];
            return $goldData['sell'];
        } else {
            throw new Exception("Data 'sell' tidak ditemukan dalam respons.");
        }
    } catch (ConnectException $e) {
        // Tangani kesalahan koneksi, misalnya server tidak dapat dijangkau
        return 'Kesalahan koneksi: ' . $e->getMessage();
    } catch (RequestException $e) {
        // Tangani kesalahan request, misalnya status code 4xx atau 5xx
        return 'Kesalahan permintaan: ' . $e->getMessage();
    } catch (Exception $e) {
        // Tangani kesalahan umum lainnya
        return 'Kesalahan: ' . $e->getMessage();
    }
}

// Fungsi untuk menghitung zakat maal
protected $input = [
    'tabungan' => 0,
    'properti' => 0,
    'surat_berharga' => 0,
    'emas' => 0,
    'perak' => 0,
    'hutang' => 0
];

public function askTabungan()
{
    $this->ask('Masukkan nilai tabungan Anda (dalam Rupiah):', function(Answer $answer) {
        $this->input['tabungan'] = $this->convertToNumeric($answer->getText());
        $this->askProperti();
    });
}

public function askProperti()
{
    $this->ask('Masukkan nilai properti Anda (dalam Rupiah):', function(Answer $answer) {
        $this->input['properti'] = $this->convertToNumeric($answer->getText());
        $this->askSuratBerharga();
    });
}

public function askSuratBerharga()
{
    $this->ask('Masukkan nilai surat berharga Anda (dalam Rupiah):', function(Answer $answer) {
        $this->input['surat_berharga'] = $this->convertToNumeric($answer->getText());
        $this->askEmas();
    });
}

public function askEmas()
{
    $this->ask('Masukkan berat emas Anda (dalam gram):', function(Answer $answer) {
        $this->input['emas'] = (float) $answer->getText();
        $this->askPerak();
    });
}

public function askPerak()
{
    $this->ask('Masukkan berat perak Anda (dalam gram):', function(Answer $answer) {
        $this->input['perak'] = (float) $answer->getText();
        $this->askHutang();
    });
}

public function askHutang()
{
    $this->ask('Masukkan nilai hutang Anda (dalam Rupiah, jika tidak ada ketik 0):', function(Answer $answer) {
        $this->input['hutang'] = $this->convertToNumeric($answer->getText());
        $this->prosesHasil();
    });
}

public function prosesHasil()
{
    // Hitung zakat maal berdasarkan input yang sudah diterima
    $tabungan = $this->input['tabungan'];
    $properti = $this->input['properti'];
    $suratBerharga = $this->input['surat_berharga'];
    $gramEmas = $this->input['emas'];
    $gramPerak = $this->input['perak'];
    $hutang = $this->input['hutang'];

    // Harga emas dan perak dalam Rupiah per gram
    $sell = $this->getHargaEmas();
    $hargaEmas = $sell;
    $hargaPerak = 14766; // Harga perak per gram

    // Menghitung nilai emas dan perak dalam Rupiah
    $nilaiEmas = $gramEmas * $hargaEmas;
    $nilaiPerak = $gramPerak * $hargaPerak;

    // Menghitung total kekayaan
    $totalKekayaan = $tabungan + $properti + $suratBerharga + $nilaiEmas + $nilaiPerak - $hutang;

    // Nilai nisab adalah 85 gram emas atau setara dengan 120 juta
    $nisab = $sell * 85;

    // Pesan output
    $output = "### Hasil Perhitungan Zakat Maal ###\n\n";
    $output .= "Nilai Tabungan: " . number_format($tabungan, 0, ',', '.') . " Rupiah\n";
    $output .= "Nilai Properti: " . number_format($properti, 0, ',', '.') . " Rupiah\n";
    $output .= "Nilai Surat Berharga: " . number_format($suratBerharga, 0, ',', '.') . " Rupiah\n";
    $output .= "Nilai Emas: " . $gramEmas . " gram x " . number_format($hargaEmas, 0, ',', '.') . " Rupiah/gram = " . number_format($nilaiEmas, 0, ',', '.') . " Rupiah\n";
    $output .= "Nilai Perak: " . $gramPerak . " gram x " . number_format($hargaPerak, 0, ',', '.') . " Rupiah/gram = " . number_format($nilaiPerak, 0, ',', '.') . " Rupiah\n";
    $output .= "Nilai Hutang: " . number_format($hutang, 0, ',', '.') . " Rupiah\n\n";

    $output .= "Total Kekayaan: " . number_format($totalKekayaan, 0, ',', '.') . " Rupiah\n\n";
    $output .= "Nisab: " . number_format($nisab, 0, ',', '.') . " Rupiah (SK Ketua BAZNAS Nomor 1 Tahun 2024 Tentang Nilai Nisab 2024)\n\n";
    $output .= "Kadar zakat sebesar 2,5% ini didasarkan pada hadis riwayat Abu Daud dari Ali bin Abi Thalib RA yang menyatakan kewajiban membayar zakat sebesar seperempat puluh dari penghasilan.\n\n";

    // Memeriksa apakah total kekayaan mencapai nisab
    if ($totalKekayaan < $nisab) {
        // Jika tidak mencapai nisab
        $output .= "Penghasilan Anda belum mencapai nisab. Anda tetap bisa menyempurnakan niat baik dengan bersedekah.";
    } else {
        // Jika mencapai atau melebihi nisab, hitung zakat
        $zakat = 0.025 * $totalKekayaan; // Zakat 2.5% dari total kekayaan
        $output .= "Kekayaan Anda mencapai atau melebihi nisab.\n";
        $output .= "Rumus Perhitungan Zakat Maal:\n";
        $output .= "Zakat = 2.5% x Total Kekayaan\n";
        $output .= "Zakat = 0.025 x " . number_format($totalKekayaan, 0, ',', '.') . " Rupiah\n";
        $output .= "Zakat = " . number_format($zakat, 0, ',', '.') . " Rupiah\n\n";
        $output .= "Jumlah zakat maal yang harus Anda bayar adalah: " . number_format($zakat, 0, ',', '.') . " Rupiah.";
    }

    // Menampilkan output kepada pengguna
    $this->say($output,['parse_mode'=>'HTML']);
    $this->askConfirmHitung();
}


public function convertToNumeric($input) {
    // Mengganti titik dengan kosong agar "5.000" menjadi "5000"
    $input = str_replace('.', '', $input);

    // Menangani berbagai kombinasi angka dan kata-kata
    $wordsToNumbers = [
        'juta' => 1000000,
        'jutaan' => 1000000,
        'ribu' => 1000,
        'ribuan' => 1000,
        'miliar' => 1000000000,
        'milyar' => 1000000000,
        'miliaran' => 1000000000,
        'triliun' => 1000000000000,
        'trilyun' => 1000000000000,
    ];

    // Proses input untuk menggantikan kata-kata besar dengan angka
    foreach ($wordsToNumbers as $word => $value) {
        if (stripos($input, $word) !== false) {
            if (preg_match('/(\d+)\s*' . $word . '/i', $input, $matches)) {
                $input = str_ireplace($matches[0], $matches[1] * $value, $input);
            }
        }
    }

    // Menghapus semua karakter non-numeric kecuali titik desimal dan angka
    $input = preg_replace("/[^0-9.]/", "", $input);

    return (float) $input;
}





//     protected $tabungan;
//     protected $properti;
//     protected $emas;
//     protected $perak;
//     protected $piutang;
//     protected $hutang_properti;
    
//     public function kalkulatorZakatMaal()
//     {
//     $this->ask('Masukkan nilai deposito/tabungan: (Rp)', function($answer) {
//         $this->tabungan = $this->convertToNumeric($answer->getText()); // Menggunakan fungsi konversi
//         $this->askProperti();
//     });
//     }

//     public function askProperti()
// {
//     $this->ask('Masukkan nilai properti/kendaraan: (Rp)', function($answer) {
//         $this->properti = $this->convertToNumeric($answer->getText()); // Menggunakan fungsi konversi
//         $this->askEmasPerak();
//     });
// }

// public function askEmasPerak()
// {
//     $question = Question::create('Pilih jenis harta yang akan dimasukkan:')
//         ->fallback('Pilihan tidak valid')
//         ->callbackId('ask_emas_perak')
//         ->addButtons([
//             Button::create('Emas')->value('emas'),
//             Button::create('Perak')->value('perak'),
//         ]);

//     $this->ask($question, function (Answer $answer) {
//         $jenis_harta = strtolower($answer->getValue());

//         if (!in_array($jenis_harta, ['emas', 'perak'])) {
//             $this->say('Pilihan tidak valid.');
//             return $this->repeat();
//         }

//         if ($jenis_harta === 'emas') {
//             $this->ask('Masukkan berat emas dalam gram:', function(Answer $answer) {
//                 $this->emas = (float) $answer->getText(); // Simpan berat emas dalam gram
//                 $this->askSaham(); // Ambil harga emas dari API setelah mendapatkan berat emas
//             });
//         } elseif ($jenis_harta === 'perak') {
//             $this->ask('Masukkan berat perak dalam gram:', function(Answer $answer) {
//                 $this->perak = (float) $answer->getText(); // Simpan berat perak dalam gram
//                 $this->askSaham(); // Ambil harga emas dari API setelah mendapatkan berat perak
//             });
//         }
//     });
// }



// // Fungsi untuk mengonversi input menjadi nilai numerik
// public function convertToNumeric($input)
// {
//     // Menghapus karakter non-numeric
//     $input = preg_replace("/[^0-9]/", "", $input);

//     // Mengonversi kata menjadi angka
//     $words = ['juta', 'ribu', 'jutaan', 'ribuan', 'miliar', 'milyar', 'miliaran', 'triliun', 'trilyun'];
//     $replacements = ['1000000', '1000', '1000000', '1000', '1000000000', '1000000000', '1000000000', '1000000000000', '1000000000000'];

//     $input = str_ireplace($words, $replacements, $input);

//     return (float) $input;
// }

//     public function askSaham()
//     {
//     $this->ask('Masukkan nilai saham/piutang/surat berharga: (Rp)', function($answer) {
//         $this->piutang = $this->convertToNumeric($answer->getText()); // Menggunakan fungsi konversi
//         $this->askHutang();
//     });
//     }

//     public function askHutang()
//     {
//     $this->ask('Masukkan nilai hutang pribadi yang jatuh tempo tahun ini: (Rp)', function($answer) {
//         $this->hutang_properti = $this->convertToNumeric($answer->getText()); // Menggunakan fungsi konversi
//         $this->calculateZakat();
//     });
//     }

//     public function calculateZakat()
// {
//     $nisab_emas = 114665000; // Nisab dalam gram emas
//     $nisab_perak = 9732087; // Nisab dalam gram perak

//     // Simpan nilai konversi dari gram ke IDR (contoh sederhana, bisa disesuaikan)
//     $harga_emas_per_gram = 1000000; // Misalnya harga emas per gram dalam IDR
//     $harga_perak_per_gram = 10000;  // Misalnya harga perak per gram dalam IDR

//     // Menyimpan data setiap jenis harta dalam array
//     $jenis_harta = [
//         'tabungan' => $this->tabungan,
//         'properti' => max(0, $this->properti - $this->hutang_properti),
//         'emas' => $this->emas * $harga_emas_per_gram, // Konversi berat emas ke IDR
//         'perak' => $this->perak * $harga_perak_per_gram, // Konversi berat perak ke IDR
//         'piutang' => $this->piutang
//     ];

//     // Menghitung total harta
//     $total_harta = array_sum($jenis_harta);

//     // Menghasilkan pesan detail perhitungan
//     $detail_perhitungan = "Detail perhitungan:\n\n";
//     foreach ($jenis_harta as $jenis => $nilai) {
//         $detail_perhitungan .= ucfirst($jenis) . ": Rp " . number_format($nilai, 0, ',', '.') . "\n";
//     }
//     $detail_perhitungan .= "Total harta: Rp " . number_format($total_harta, 0, ',', '.') . "\n";

//     // Mengecek apakah total harta melebihi nisab
//     if ($jenis_harta['emas'] < $nisab_emas && $jenis_harta['perak'] < $nisab_perak) {
//         $this->say($detail_perhitungan . "Anda tidak wajib membayar zakat maal karena total nilai emas dan perak belum mencapai nisab.");
//         $this->askConfirm();
//         return;
//     }

//     // Menghitung zakat untuk setiap jenis harta yang melebihi nisab
//     $total_zakat = 0;
//     $detail_perhitungan .= "\nPerhitungan zakat:\n\n";

//     // Iterasi untuk menghitung zakat
//     foreach ($jenis_harta as $jenis => $nilai) {
//         $rate_zakat = ($jenis === 'piutang') ? 0.025 : 0.025 * ($jenis_harta['emas'] + $jenis_harta['perak'] >= $nisab_emas || $jenis_harta['emas'] + $jenis_harta['perak'] >= $nisab_perak);
//         $zakat = $rate_zakat * $nilai;
//         $total_zakat += $zakat;

//         $detail_perhitungan .= ucfirst($jenis) . ": Rp " . number_format($nilai, 0, ',', '.') . " x " . ($rate_zakat * 100) . "% = Rp " . number_format($zakat, 0, ',', '.') . "\n\n";
//     }

//     $detail_perhitungan .= "Total zakat: Rp " . number_format($total_zakat, 0, ',', '.') . "\n";

//     $this->say($detail_perhitungan . "\nTotal zakat yang harus Anda bayar adalah: Rp " . number_format($total_zakat, 0, ',', '.'));
//     $this->askConfirm();
// }
    
//

    public function perhitunganZMaal()
    {
        $this->say("<b>Perhitungan Zakat Maal</b>", ['parse_mode'=>'HTML']);
        $this->say('Besarannya adalah beras atau makanan pokok seberat 2, 5 kg atau 3, 5 liter per jiwa. berdasarkan hadist Dari Ibn Umar RA, Rasulullah SAW bersabda, Rasulullah SAW, mewajibkan zakat fitrah dengan satu sha kurma atau satu sha gandum bagi setiap muslim yang merdeka maupun budak, laki-laki maupun perempuan, anak kecil maupun dewasa. Zakat tersebut diperintahkan dikeluarkan sebelum orang-orang keluar untuk melaksanakan sholat ied. (HR. Bukhari). ',['parse_mode' => 'HTML']);
        $this->askConfirm();
    }

    public function finish() 
    {
        $this->say("<b>Selesai</b>", ['parse_mode'=>'HTML']);
        $this->say('Terima kasih sudah berkonsultasi.');
    }

    public function askConfirmHitung()
    {
        $question = Question::create('Apakah ada hal lain yang ingin Anda tanyakan?')
		->addButtons([
			Button::create('Ya')->value('0'),
			Button::create('Tidak')->value('1'),
            Button::create('Hitung Ulang')->value('2'),
		]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->getValue() === '0') {
                $this->askPilihan();
            }
            elseif($answer->getValue() === '2') {
                $this->askTabungan();
            }
            else
            {
                $this->finish();
            }
        });

    }

    public function askConfirm()
    {
        $question = Question::create('Apakah ada hal lain yang ingin Anda tanyakan?')
		->addButtons([
			Button::create('Ya')->value('0'),
			Button::create('Tidak')->value('1'),
		]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->getValue() === '0') {
                $this->askPilihan();
            }
            else
            {
                $this->finish();
            }
        });

    }

    /**
     * Start the conversation
     */
    public function run()
    {
        
    }
//
}

?>