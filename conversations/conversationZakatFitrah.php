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
        $this->say('Besarannya adalah beras atau makanan pokok seberat 2, 5 kg atau 3, 5 liter per jiwa. berdasarkan hadist Dari Ibn Umar RA, Rasulullah SAW bersabda, Rasulullah SAW, mewajibkan zakat fitrah dengan satu sha kurma atau satu sha gandum bagi setiap muslim yang merdeka maupun budak, laki-laki maupun perempuan, anak kecil maupun dewasa. Zakat tersebut diperintahkan dikeluarkan sebelum orang-orang keluar untuk melaksanakan sholat ied. (HR. Bukhari). ',['parse_mode' => 'HTML']);
        $this->kalkulatorZakatFitrah();
    }
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

    public function kalkulatorZakatFitrah(){
        $this->say('<b>Kalkulator Zakat Fitrah</b>', ['parse_mode' => 'html']);
        
        $this->ask('Berapa jumlah anggota keluarga yang akan Anda berikan zakat fitrah?', function ($answer, $conversation) {
            $jumlahAnggotaKeluarga = $this->convertToNumeric($answer->getText());
            
            $jenisBeras = ['pandan wangi', 'solok', 'ramos', 'top koki', 'belida', 'bulog'];
            $this->ask('Jenis beras (' . implode(', ', $jenisBeras) . ')?', function($answer, $conversation) use ($jumlahAnggotaKeluarga, $jenisBeras) {
                $jenisBerasInput = strtolower($answer->getText());
                if (!in_array($jenisBerasInput, $jenisBeras)) {
                    $conversation->repeat();
                    return;
                }
    
                $conversation->ask('Harga beras ' . ucfirst($jenisBerasInput) . ' per kg (pada wilayah anda)?', function($answer, $conversation) use ($jumlahAnggotaKeluarga, $jenisBerasInput) {
                    $hargaBerasPerKg = $this->convertToNumeric($answer->getText());
                    $jumlahZakatFitrah = $jumlahAnggotaKeluarga * 2.5; // Sesuai dengan standar makanan pokok yang digunakan untuk perhitungan zakat fitrah
                    $jumlahAnggotaKeluarga = number_format($jumlahAnggotaKeluarga, 0, ',', '.');
                    $hargaBayarSetara = $jumlahZakatFitrah * $hargaBerasPerKg;
                    $zakatRupiah = number_format($hargaBayarSetara, 0, ',', '.');
                    
                    $conversation->say("
Jumlah anggota keluarga : {$jumlahAnggotaKeluarga}
Jenis Beras : {$jenisBerasInput}
Harga Beras : Rp " . number_format($hargaBerasPerKg, 0, ',', '.'). "

Besaran zakat fitrah yang ditetapkan yakni sebesar 2,5 kg per jiwa. 

Jumlah Zakat Fitrah 
= Jumlah anggota x nisab
= {$jumlahAnggotaKeluarga} orang x 2.5
= {$jumlahZakatFitrah} Kg

Sehingga Jumlah<b> Zakat Fitrah</b> yang harus Anda bayar adalah <b> $jumlahZakatFitrah kg beras </b> atau uang sejumlah <b> Rp $zakatRupiah </b>", ['parse_mode' => 'html']);
                    
                    $this->askConfirm();
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