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
    protected $beratEmas;
    protected $beratPerak;
    protected $hargaEmasPerGram;
    protected $hargaPerakPerGram;
    protected $hargaPerGram;
    protected $nilaiHarta;

    public function askJenisLogam()
{
    $question = Question::create('Pilih jenis logam:')
        ->fallback('Pilihan tidak valid')
        ->callbackId('ask_jenis_logam')
        ->addButtons([
            Button::create('Emas')->value('emas'),
            Button::create('Perak')->value('perak'),
        ]);

    $this->ask($question, function (Answer $answer) {
        $jenis = strtolower($answer->getValue());
        if (!in_array($jenis, ['emas', 'perak'])) {
            $this->say('Pilihan tidak valid.');
            return $this->repeat();
        }
        $this->jenisLogam = $jenis;
        $this->askInput();
    });
}

public function askInput()
{
    $this->ask("Masukkan berat {$this->jenisLogam} (gram):", function (Answer $answer) {
        $weight = $this->convertToNumeric($answer->getText());
        $this->berat = (float) $weight;
        $this->fetchPrice();
    });
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

// public function askInputForBoth()
// {
//     $this->ask('Masukkan berat emas (gram):', function (Answer $answer) {
//         $this->beratEmas = (float) $this->convertToNumeric($answer->getText());
//         $this->ask('Masukkan berat perak (gram):', function (Answer $answer) {
//             $this->beratPerak = (float) $this->convertToNumeric($answer->getText());
//             $this->fetchPriceForBoth();
//         });
//     });
// }

public function fetchPrice()
{
    try {
        $client = new Client();
        if ($this->jenisLogam === 'emas') {
            $response = $client->request('GET', 'https://logam-mulia-api.vercel.app/prices/hargaemas-org');
            $data = json_decode($response->getBody(), true);

            if (isset($data['data'][0])) {
                $goldData = $data['data'][0];
                $this->hargaPerGram = $goldData['sell'];
            } else {
                $this->say('Data harga emas tidak tersedia untuk website yang dipilih.');
            }
        } else if ($this->jenisLogam === 'perak') {
            $this->hargaPerGram = 14766; // contoh harga perak per gram
        }

        $this->checkNisabAndCalculateZakat();
    } catch (\Exception $e) {
        $this->say('Terjadi kesalahan dalam mengambil data harga ' . $this->jenisLogam . '.');
    }
}

// public function fetchPriceForBoth()
// {
//     try {
//         $client = new Client();
//         $response = $client->request('GET', 'https://logam-mulia-api.vercel.app/prices/hargaemas-org');
//         $data = json_decode($response->getBody(), true);

//         if (isset($data['data'][0])) {
//             $goldData = $data['data'][0];
//             $this->hargaEmasPerGram = $goldData['sell'];
//         } else {
//             $this->say('Data harga emas tidak tersedia untuk website yang dipilih.');
//         }

//         $this->hargaPerakPerGram = 14766; // contoh harga perak per gram

//         $this->checkNisabAndCalculateZakatForBoth();
//     } catch (\Exception $e) {
//         $this->say('Terjadi kesalahan dalam mengambil data harga emas atau perak.');
//     }
// }

public function checkNisabAndCalculateZakat()
{
    $nisabe = 85; // Nisab emas dalam gram
    $nisabp = 595; // Nisab perak dalam gram
    $outputHitung = "### Kalkulator Zakat {$this->jenisLogam} ###\n\n";
    

    if ($this->jenisLogam === 'emas' && $this->berat < $nisabe) {
        $outputHitung .= " Nisab Zakat Emas adalah {$nisabe} gram \n\n";
        $this->say($outputHitung . "Anda tidak wajib membayar zakat karena berat {$this->jenisLogam} Anda {$this->berat} gram di bawah nisab. Anda tetap bisa menyempurnakan niat baik dengan bersedekah.");
        // Instansiasi conversationZakatMaal
        $conversation = new conversationZakatMaalEmas();
        $this->bot->startConversation($conversation);
        $conversation->askConfirmHitung();
    } elseif ($this->jenisLogam === 'perak' && $this->berat < $nisabp) {
        $outputHitung .= " Nisab Zakat Perak adalah {$nisabp} gram \n\n";
        $this->say($outputHitung . "Anda tidak wajib membayar zakat karena berat {$this->jenisLogam} Anda {$this->berat} gram di bawah nisab. Anda tetap bisa menyempurnakan niat baik dengan bersedekah.");
        // Instansiasi conversationZakatMaal
        $conversation = new conversationZakatMaalEmas();
        $this->bot->startConversation($conversation);
        $conversation->askConfirmHitung();
    } else {
        $this->calculateZakat();
    }
}

// public function checkNisabAndCalculateZakatForBoth()
// {
//     $nisabe = 85; // Nisab emas dalam gram
//     $nisabp = 595; // Nisab perak dalam gram

//     $belowNisabEmas = $this->beratEmas < $nisabe;
//     $belowNisabPerak = $this->beratPerak < $nisabp;

//     if ($belowNisabEmas && $belowNisabPerak) {
//         $this->say("Anda tidak wajib membayar zakat karena berat emas dan perak Anda di bawah nisab. Anda tetap bisa menyempurnakan niat baik dengan bersedekah.");
//     } else {
//         $this->calculateZakatForBoth();
//     }
// }

public function calculateZakat()
{
    $totalHarta = $this->berat * $this->hargaPerGram;
    $totalHarta = number_format($totalHarta, 0, ',', '.');
    $berat = number_format($this->berat, 0, ',', '.');
    $hargaPerGram = number_format($this->hargaPerGram, 0, ',', '.');
    $jenisLogam = strtoupper($this->jenisLogam);
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
    $this->bot->startConversation($conversation);
    $conversation->askConfirmHitung();
}

// public function calculateZakatForBoth()
// {
//     $totalHartaEmas = $this->beratEmas * $this->hargaEmasPerGram;
//     $totalHartaPerak = $this->beratPerak * $this->hargaPerakPerGram;

//     $totalHartaEmasFormatted = number_format($totalHartaEmas, 0, ',', '.');
//     $totalHartaPerakFormatted = number_format($totalHartaPerak, 0, ',', '.');
//     $beratEmasFormatted = number_format($this->beratEmas, 0, ',', '.');
//     $hargaEmasPerGramFormatted = number_format($this->hargaEmasPerGram, 0, ',', '.');
//     $beratPerakFormatted = number_format($this->beratPerak, 0, ',', '.');
//     $hargaPerakPerGramFormatted = number_format($this->hargaPerakPerGram, 0, ',', '.');

//     $zakatEmas = $totalHartaEmas * 0.025;
//     $zakatPerak = $totalHartaPerak * 0.025;

//     $zakatEmasFormatted = number_format($zakatEmas, 0, ',', '.');
//     $zakatPerakFormatted = number_format($zakatPerak, 0, ',', '.');

//     $this->say("
// Jenis Logam: EMAS 
// Berat: {$beratEmasFormatted} gram
// Harga per gram: Rp {$hargaEmasPerGramFormatted}

// Total Harta Emas: Rp {$totalHartaEmasFormatted}

// Zakat Emas (2.5%): Rp {$zakatEmasFormatted}

// Jenis Logam: PERAK 
// Berat: {$beratPerakFormatted} gram
// Harga per gram: Rp {$hargaPerakPerGramFormatted}

// Total Harta Perak: Rp {$totalHartaPerakFormatted}

// Zakat Perak (2.5%): Rp {$zakatPerakFormatted}

// Total Zakat yang harus dibayarkan: Rp " . number_format($zakatEmas + $zakatPerak, 0, ',', '.'));

//     // Instansiasi conversationZakatMaal
//     $conversation = new conversationZakatMaalEmas();
//     $this->bot->startConversation($conversation);
//     $conversation->askConfirmHitung();
// }

    

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askJenisLogam();
    }
}
?>