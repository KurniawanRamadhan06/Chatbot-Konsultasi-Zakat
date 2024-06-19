<?php

use BotMan\BotMan\Facades\BotMan;
use Illuminate\Foundation\Inspiring;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
require_once __DIR__."/conversationZakatMaalEmas.php";

class conversationZakatEmasPerhitungan extends Conversation
{
    
    protected $jenisLogam;
    protected $berat;
    protected $hargaPerGram;
    protected $nilaiHarta;

    public function askJenisLogam()
    {
        $this->ask('Pilih jenis logam: Emas atau Perak?', function (Answer $message) {
            $jenis = strtolower($message->getText());
            if (!in_array($jenis, ['emas', 'perak'])) {
                $this->say('Pilihan tidak valid.');
                return $this->repeat();
            }
            $this->jenisLogam = $jenis;
            $this->askInput();
        });
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

    public function askInput()
    {
        $this->ask("Masukkan berat {$this->jenisLogam} (gram):", function (Answer $answer) {
            $weight = $this->convertToNumeric($answer->getText());
            $this->berat = (float) $weight;
            $this->fetchPrice();
        });
    }

    public function fetchPrice()
    {
        try {
            $client = new Client();
            if ($this->jenisLogam === 'emas') {
                $response = $client->request('GET', 'https://logam-mulia-api.vercel.app/prices/hargaemas-org');
                $data = json_decode($response->getBody(), true);
    
                if (isset($data['data'][0])) {
                    $goldData = $data['data'][0];
                    $buyPrice = $goldData['buy'];
                    $sellPrice = $goldData['sell'];
                    $type = $goldData['type'];
    
                    // $this->say("Harga beli: $buyPrice\nHarga jual: $sellPrice\nTipe: $type");
                    $this->hargaPerGram = $sellPrice;
                    // $this->calculateZakat();
                } else {
                    $this->say('Data harga emas tidak tersedia untuk website yang dipilih.');
                }
            } else if ($this->jenisLogam === 'perak') {
                // Harga perak ditentukan secara manual
                $this->hargaPerGram = 14766; // contoh harga perak per gram
                // $this->calculateZakat();
            }

            // Pengecekan apakah berat logam di bawah nisab
        $nisabe = 85; // Nisab emas dalam gram
        $nisabp = 595; // Nisab perak dalam gram
        if ($this->jenisLogam === 'emas' && $this->berat < $nisabe) {
            $this->say("Anda tidak wajib membayar zakat karena berat {$this->jenisLogam} Anda di bawah nisab.");
        } elseif ($this->jenisLogam === 'perak' && $this->berat < $nisabp) {
            $this->say("Anda tidak wajib membayar zakat karena berat {$this->jenisLogam} Anda di bawah nisab.");
        } else {
            $this->calculateZakat();
        }
        } catch (\Exception $e) {
            $this->say('Terjadi kesalahan dalam mengambil data harga ' . $this->jenisLogam . '.');
        }
    }
    public function calculateZakat()
    {
        function toUpperCase($string) {
            return strtoupper($string);
        }
        // Implementasikan logika perhitungan zakat di sini
        $totalHarta =  $this->berat * $this->hargaPerGram;
        $totalHarta = number_format($totalHarta, 0, ',', '.');
        $berat = number_format($this->berat, 0, ',', '.');
        $hargaPerGram = number_format($this->hargaPerGram, 0, ',', '.');
        $jenisLogam = toUpperCase($this->jenisLogam, 2);
        $zakat = $this->berat * $this->hargaPerGram * 0.025; // Contoh perhitungan zakat 2.5%
        $zakat = number_format($zakat, 0, ',', '.');
        $this->say("
Jenis Logam : {$jenisLogam} 
Berat: {$berat} gram
Harga per gram : Rp {$hargaPerGram}

Nisab zakat emas 85 gram = Rp 113.220.000

Jumlah zakat emas dan perak
= Total Harta x 2.5%
= (berat {$jenisLogam} x harga per gram) x 2.5%
= ({$berat} gram x Rp {$hargaPerGram}) x 2.5%
= Rp {$totalHarta} x 2.5%
= Rp {$zakat}

Dari Total Harta : Rp {$totalHarta}
Zakat yang harus dibayarkan : Rp {$zakat}"); 
// Instansiasi conversationZakatMaal
$conversation = new conversationZakatMaalEmas();

// Jalankan metode run jika perlu
$this->bot->startConversation($conversation);

// Langsung panggil metode finish
$conversation->askConfirm();
    }

    

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askJenisLogam();
    }
}
?>