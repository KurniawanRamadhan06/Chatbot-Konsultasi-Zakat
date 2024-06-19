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
        $this->ask('Pilih jenis pertanian: Beras atau Gabah?', function (Answer $message) {
            $this->jenisPertanian = strtolower($message->getText());
            if (!in_array($this->jenisPertanian, ['beras', 'gabah'])) {
                $this->say('Pilihan tidak valid.');
                return $this->repeat();
            }
            $this->askHasilPanen();
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
        $this->ask('Apakah menggunakan perairan berbayar [ya atau tidak]?', function (Answer $message) {
            $jenis = $message->getText();
            $this->jenisAir = strtolower($jenis);
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

    $totalNilai = $this->hasilPanen * $this->hargaKomoditas * $jenisAir;
    $subtotal = $this->hasilPanen * $this->hargaKomoditas + $totalNilai;

    if ($this->hasilPanen < $nisab) {
        $this->say("Anda tidak wajib membayar zakat karena total nilai harta kurang dari nisab.");
        return;
    }

    $zakat = $subtotal * 0.025; // Zakat 2.5% dari total nilai harta
    $hasilpanen = $this->hasilPanen;
    $hargaKomoditas = $this->hargaKomoditas;
    $this->say("Jenis Pertanian: {$this->jenisPertanian}\nHasil Panen: " . number_format($this->hasilPanen, 0, ',', '.') . " kg\nHarga jual komoditas: Rp " . number_format($this->hargaKomoditas, 0, ',', '.') . " per kg\nTotal Nilai Harta: Rp " . number_format($subtotal, 0, ',', '.') . 
"

Jumlah zakat pertanian 
= (Hasil panen x harga jual) + jenis peraian x 2.5%
= ".number_format($hasilpanen, 0, ',', '.')." x ".number_format($hargaKomoditas, 0, ',', '.')." + {$totalNilai} x 2.5%
= Rp ".number_format($zakat, 0, ',', '.')."
\nZakat yang harus dibayarkan: Rp " . number_format($zakat, 0, ',', '.'));
// Instansiasi conversationZakatMaal
$conversation = new conversationZakatMaalPertanian();

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
        $this->askJenisPertanian();
    }
}
?>