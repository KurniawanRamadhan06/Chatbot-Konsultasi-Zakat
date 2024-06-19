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
                $this->kalkulatorZakatMaal();
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

    protected $tabungan;
    protected $properti;
    protected $emas;
    protected $perak;
    protected $piutang;
    protected $hutang_properti;
    
    public function kalkulatorZakatMaal()
    {
    $this->ask('Masukkan nilai deposito/tabungan:', function($answer) {
        $this->tabungan = $this->convertToNumeric($answer->getText()); // Menggunakan fungsi konversi
        $this->askProperti();
    });
    }

    public function askProperti()
{
    $this->ask('Masukkan nilai properti/kendaraan:', function($answer) {
        $this->properti = $this->convertToNumeric($answer->getText()); // Menggunakan fungsi konversi
        $this->askEmasPerak();
    });
}

public function askEmasPerak()
{
    $this->ask('Pilih jenis harta yang akan dimasukkan (emas/perak):', function($answer) {
        $jenis_harta = strtolower($answer->getText());
        
        if ($jenis_harta === 'emas') {
            $this->ask('Masukkan nilai emas:', function($answer) {
                $this->emas = $this->convertToNumeric($answer->getText()); // Menggunakan fungsi konversi
                $this->askSaham();
            });
        } elseif ($jenis_harta === 'perak') {
            $this->ask('Masukkan nilai perak:', function($answer) {
                $this->perak = $this->convertToNumeric($answer->getText()); // Menggunakan fungsi konversi
                $this->askSaham();
            });
        } else {
            $this->say('Mohon maaf, pilihan yang Anda masukkan tidak valid.');
            $this->askEmasPerak();
        }
    });
}

// Fungsi untuk mengonversi input menjadi nilai numerik
public function convertToNumeric($input)
{
    // Menghapus karakter non-numeric
    $input = preg_replace("/[^0-9]/", "", $input);

    // Mengonversi kata menjadi angka
    $words = ['juta', 'ribu', 'jutaan', 'ribuan', 'miliar', 'milyar', 'miliaran', 'triliun', 'trilyun'];
    $replacements = ['1000000', '1000', '1000000', '1000', '1000000000', '1000000000', '1000000000', '1000000000000', '1000000000000'];

    $input = str_ireplace($words, $replacements, $input);

    return (float) $input;
}

    public function askSaham()
    {
    $this->ask('Masukkan nilai saham/piutang/surat berharga:', function($answer) {
        $this->piutang = $this->convertToNumeric($answer->getText()); // Menggunakan fungsi konversi
        $this->askHutang();
    });
    }

    public function askHutang()
    {
    $this->ask('Masukkan nilai hutang pribadi yang jatuh tempo tahun ini:', function($answer) {
        $this->hutang_properti = $this->convertToNumeric($answer->getText()); // Menggunakan fungsi konversi
        $this->calculateZakat();
    });
    }

    public function calculateZakat()
{
    $nisab_emas = 114665000; // Nisab dalam gram emas, bisa disesuaikan sesuai kebijakan yang berlaku
    $nisab_perak = 9732087; // Nisab dalam gram perak

    // Menyimpan data setiap jenis harta dalam array
    $jenis_harta = [
        'tabungan' => $this->tabungan,
        'properti' => max(0, $this->properti - $this->hutang_properti),
        'emas' => $this->emas,
        'perak' => $this->perak,
        'piutang' => $this->piutang
    ];

    // Menghitung total harta
    $total_harta = array_sum($jenis_harta);
    
    // Menghasilkan pesan detail perhitungan
    $detail_perhitungan = "Detail perhitungan:\n\n";
    foreach ($jenis_harta as $jenis => $nilai) {
        $detail_perhitungan .= ucfirst($jenis) . ": Rp " . number_format($nilai, 0, ',', '.') . "\n";
    }
    $detail_perhitungan .= "Total harta: Rp " . number_format($total_harta, 0, ',', '.') . "\n";

    // Mengecek apakah total harta melebihi nisab
    if ($jenis_harta['emas'] < $nisab_emas && $jenis_harta['perak'] < $nisab_perak) {
        $this->say($detail_perhitungan . "Anda tidak wajib membayar zakat maal karena total nilai emas dan perak belum mencapai nisab.");
        $this->askConfirm();
        return;
    }

    // Menghitung zakat untuk setiap jenis harta yang melebihi nisab
    $total_zakat = 0;
    $detail_perhitungan .= "\nPerhitungan zakat:\n\n";

    // Iterasi untuk menghitung zakat
    foreach ($jenis_harta as $jenis => $nilai) {
        $rate_zakat = ($jenis === 'piutang') ? 0.025 : 0.025 * ($jenis_harta['emas'] + $jenis_harta['perak'] >= $nisab_emas || $jenis_harta['emas'] + $jenis_harta['perak'] >= $nisab_perak);
        $zakat = $rate_zakat * $nilai;
        $total_zakat += $zakat;

        $detail_perhitungan .= ucfirst($jenis) . ": Rp " . number_format($nilai, 0, ',', '.') . " x " . ($rate_zakat * 100) . "% = Rp " . number_format($zakat, 0, ',', '.') . "\n\n";
    }

    $detail_perhitungan .= "Total zakat: Rp " . number_format($total_zakat, 0, ',', '.') . "\n";

    $this->say($detail_perhitungan . "\nTotal zakat yang harus Anda bayar adalah: Rp " . number_format($total_zakat, 0, ',', '.'));
    $this->askConfirm();
}
    
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
}
?>