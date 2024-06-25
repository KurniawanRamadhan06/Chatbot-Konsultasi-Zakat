<?php

require_once __DIR__."/conversationZakatFitrahDalil.php";
require_once __DIR__."/conversationZakatFitrahniat.php";

use BotMan\BotMan\BotMan;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class conversationZakatFitrah extends Conversation
{
    
    public function askPilihan()
    {
        $question = Question::create('Apa yang ingin anda tanyakan terkait Zakat Fitrah?')
		->addButtons([
			Button::create('Lihat Penjelasan')->value('0'),
			Button::create('Lihat Dalil')->value('1'),
			Button::create('Lihat Niat')->value('5'),
			Button::create('Lihat Perhitungan')->value('2'),
			Button::create('Selesai')->value('3'),

		]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->getValue() === '0') {
                $this->pengertianZFitrah();
            }
            elseif ($answer->getValue() === '1') {
                $this->bot->startConversation(new conversationZakatFitrahDalil());

            }
            elseif ($answer->getValue() === '2') {
                $this->perhitunganZFitrah();
            }
            elseif ($answer->getValue() === '5') {
                $this->niatZakatFitrah();
            }
            else
            {
                $this->finish();
            }
        });
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
            }else if ($answer->getValue() === '2') {
                $this->perhitunganZFitrah();
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
    
    public function niatZakatFitrah()
    {
        $this->bot->startConversation(new conversationZakatFitrahniat());
    
    }
    public function pengertianZFitrah()
    {
        $this->say("<b>Pengertian Zakat Fitrah</b>", ['parse_mode'=>'HTML']);
        $this->say(" Zakat fitrah adalah zakat yang wajib dikeluarkan oleh setiap Muslim menjelang idul fitri pada bulan suci Ramadhan. Zakat fitrah wajib ditunaikan bagi setiap jiwa, dengan syarat beragama Islam, menemui sebagian dari bulan Ramadan dan sebagian dari awalnya bulan Syawal (malam hari raya), dan memiliki kelebihan rezeki atau kebutuhan pokok untuk malam dan Hari Raya Idul Fitri. Besarannya adalah beras atau makanan pokok seberat 2, 5 kg atau 3, 5 liter per jiwa ");
        $this->askConfirm();

        
    }
    public function perhitunganZFitrah()
    {
        $this->say("<b>Perhitungan Zakat Fitrah</b>", ['parse_mode'=>'HTML']);
        $this->say('Besarannya adalah beras atau makanan pokok seberat 2,5 kg atau 3,5 liter per jiwa. berdasarkan hadist Dari Ibn Umar RA, Rasulullah SAW bersabda, Rasulullah SAW, mewajibkan zakat fitrah dengan satu sha kurma atau satu sha gandum bagi setiap muslim yang merdeka maupun budak, laki-laki maupun perempuan, anak kecil maupun dewasa. Zakat tersebut diperintahkan dikeluarkan sebelum orang-orang keluar untuk melaksanakan sholat ied. (HR. Bukhari). ',['parse_mode' => 'HTML']);
        $this->kalkulatorZakatFitrah();
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

    public function kalkulatorZakatFitrah()
{
    $this->say('<b>Kalkulator Zakat Fitrah</b>', ['parse_mode' => 'html']);

    $this->ask('Berapa jumlah anggota keluarga yang akan Anda berikan zakat fitrah?(berapa orang)', function ($answer, $conversation) {
        $jumlahAnggotaKeluarga = $this->convertToNumeric($answer->getText());
        
        $question = Question::create('Pilih jenis beras:')
            ->fallback('Pilihan tidak valid')
            ->callbackId('ask_jenis_beras')
            ->addButtons([
                Button::create('Pandan Wangi')->value('pandan wangi'),
                Button::create('Solok')->value('solok'),
                Button::create('Ramos')->value('ramos'),
                Button::create('Top Koki')->value('top koki'),
                Button::create('Belida')->value('belida'),
                Button::create('Bulog')->value('bulog'),
            ]);

        $this->ask($question, function ($answer, $conversation) use ($jumlahAnggotaKeluarga) {
            $jenisBerasInput = strtolower($answer->getValue());
            if (!in_array($jenisBerasInput, ['pandan wangi', 'solok', 'ramos', 'top koki', 'belida', 'bulog'])) {
                $this->say('Pilihan tidak valid.');
                return $this->repeat();
            }

            $this->ask('Harga beras ' . ucfirst($jenisBerasInput) . ' per kg (pada wilayah anda)?', function ($answer, $conversation) use ($jumlahAnggotaKeluarga, $jenisBerasInput) {
                $hargaBerasPerKg = $this->convertToNumeric($answer->getText());
                $jumlahZakatFitrah = $jumlahAnggotaKeluarga * 2.5; // Sesuai dengan standar makanan pokok yang digunakan untuk perhitungan zakat fitrah
                $jumlahAnggotaKeluargaFormatted = number_format($jumlahAnggotaKeluarga, 0, ',', '.');
                $hargaBayarSetara = $jumlahZakatFitrah * $hargaBerasPerKg;
                $zakatRupiah = number_format($hargaBayarSetara, 0, ',', '.');

                $this->say("
Jumlah anggota keluarga : {$jumlahAnggotaKeluargaFormatted} orang
Jenis Beras : {$jenisBerasInput}
Harga Beras : Rp " . number_format($hargaBerasPerKg, 0, ',', '.') . "

Besaran zakat fitrah yang ditetapkan yakni sebesar 2,5 kg per jiwa. 

Jumlah Zakat Fitrah 
= Jumlah anggota x nisab
= {$jumlahAnggotaKeluarga} orang x 2.5
= {$jumlahZakatFitrah} Kg

Sehingga Jumlah <b>Zakat Fitrah</b> yang harus Anda bayar adalah <b>{$jumlahZakatFitrah} kg beras</b> atau uang sejumlah <b>Rp {$zakatRupiah}</b>", ['parse_mode' => 'html']);
                
                $this->askConfirmHitung();
            });
        });
    });
}

    public function finish() 
    {
        $this->say("<b>Selesai</b>", ['parse_mode'=>'HTML']);
        $this->say('Terima kasih sudah berkonsultasi.');
    }
    public function run()
    {

    }
}
?>