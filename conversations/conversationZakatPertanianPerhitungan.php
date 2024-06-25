<?php

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Conversations\Conversation;
require_once __DIR__."/conversationZakatMaalPertanian.php";

class conversationZakatPertanianPerhitungan extends Conversation
{
    protected $jenisPertanian;
    protected $hasilPanen;
    protected $hargaKomoditas;
    protected $jenisAir;
    protected $perairanBerbayar;
    protected $nisab;

    public function askJenisPertanian()
    {
        $question = Question::create('Pilih jenis pertanian:')
            ->fallback('Pilihan tidak valid')
            ->callbackId('ask_jenis_pertanian')
            ->addButtons([
                Button::create('Beras')->value('beras'),
                Button::create('Gabah')->value('gabah'),
                Button::create('Sawit')->value('sawit'),
                Button::create('Jagung')->value('jagung'),
            ]);

        $this->ask($question, function (Answer $answer) {
            $this->jenisPertanian = strtolower($answer->getValue());
            if (!in_array($this->jenisPertanian, ['beras', 'gabah', 'sawit', 'jagung'])) {
                $this->say('Pilihan tidak valid.');
                return $this->repeat();
            }
            $this->askHasilPanen();
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

    public function askHasilPanen()
    {
        $this->ask('Masukkan hasil panen (dalam kilogram atau liter):', function (Answer $message) {
            $this->hasilPanen = $this->convertToNumeric($message->getText());
            $this->askHargaKomoditas();
        });
    }

    public function askHargaKomoditas()
    {
        $this->ask('Masukkan harga jual komoditas per kilogram atau liter (dalam Rupiah):', function (Answer $message) {
            $this->hargaKomoditas = $this->convertToNumeric($message->getText());
            $this->askUseAirBerbayar();
        });
    }
    public function askUseAirBerbayar()
    {
        $question = Question::create('Apakah menggunakan perairan berbayar?')
            ->fallback('Pilihan tidak valid')
            ->callbackId('ask_use_air_berbayar')
            ->addButtons([
                Button::create('Ya')->value('ya'),
                Button::create('Tidak')->value('tidak'),
            ]);

        $this->ask($question, function (Answer $answer) {
            $this->jenisAir = strtolower($answer->getValue());
            if (!in_array($this->jenisAir, ['ya', 'tidak'])) {
                $this->say('Pilihan tidak valid.');
                return $this->repeat();
            }
            $this->calculateZakat();
        });
    }

    public function calculateZakat()
{
    switch ($this->jenisPertanian) {
        case 'beras':
        case 'gabah':
        case 'sawit':
        case 'jagung':
            $nisab = 653; // Nisab dalam kilogram atau liter
            break;
        default:
            $this->say("Jenis pertanian tidak valid.");
            return;
    }

    if ($this->jenisAir == "ya") {
        $jenisAir = 0.05;
    } else {
        $jenisAir = 0.10;
    }

    $nisab = 653;
    $totalNilai = $this->hasilPanen * $this->hargaKomoditas * $jenisAir;
    $subtotal = $this->hasilPanen * $this->hargaKomoditas + $totalNilai;

    if ($this->hasilPanen < $nisab) {
        $outputRumus = "### Kalkulator Zakat Pertanian ###\n\n";
        $outputRumus .= "Jenis Pertanian: {$this->jenisPertanian}\n";
        $outputRumus .= "Hasil Panen: " . number_format($this->hasilPanen, 0, ',', '.') . " kg\n";
        $outputRumus .= "Harga jual komoditas: Rp " . number_format($this->hargaKomoditas, 0, ',', '.') . " per kg\n";
        $outputRumus .= "Total Nilai Harta: Rp " . number_format($subtotal, 0, ',', '.')." \n\n";
        
        $outputRumus .= "Nisab ".$this->jenisPertanian." adalah sebesar {$nisab} kg Sesuai ketentuan Baznas Kota Pekanbaru\n\n";

        $this->say($outputRumus ."Nilai Hasil Panen Anda belum mencapai nisab. Tetapi, Anda tetap bisa menyempurnakan niat baik dengan bersedekah.");
        // Instansiasi conversationZakatMaal
        $conversation = new conversationZakatMaalPertanian();

        // Jalankan metode run jika perlu
        $this->bot->startConversation($conversation);

        // Langsung panggil metode finish
        $conversation->askConfirmHitung();

        return;
    }

    $zakat = $subtotal * 0.025; // Zakat 2.5% dari total nilai harta
    $hasilpanen = $this->hasilPanen;
    $hargaKomoditas = $this->hargaKomoditas;
    $this->say("
### Kalkulator Zakat Pertanian ###

Jenis Pertanian: {$this->jenisPertanian}\nHasil Panen: " . number_format($this->hasilPanen, 0, ',', '.') . " kg\nHarga jual komoditas: Rp " . number_format($this->hargaKomoditas, 0, ',', '.') . " per kg\nTotal Nilai Harta: Rp " . number_format($subtotal, 0, ',', '.') ."

Nisab Zakat pertanian ". $this->jenisPertanian ." adalah sebesar {$nisab} kg Sesuai ketentuan Baznas Kota Pekanbaru.

Jumlah zakat pertanian 
= (Hasil panen x harga jual) + jenis peraian x 2.5%
= ".number_format($hasilpanen, 0, ',', '.')." x ".number_format($hargaKomoditas, 0, ',', '.')." + {$totalNilai} x 2.5%
= Rp ".number_format($zakat, 0, ',', '.')."

Karena hasil panen anda melebihi nisab, yaitu sebesar ".number_format($hasilpanen, 0, ',', '.')." kg
\nZakat yang harus dibayarkan: Rp " . number_format($zakat, 0, ',', '.'));
// Instansiasi conversationZakatMaal
$conversation = new conversationZakatMaalPertanian();

// Jalankan metode run jika perlu
$this->bot->startConversation($conversation);

// Langsung panggil metode finish
$conversation->askConfirmHitung();
}

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askJenisPertanian();
    }
}
?>