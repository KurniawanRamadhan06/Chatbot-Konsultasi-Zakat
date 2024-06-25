<?php

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
require_once __DIR__."/conversationZakatMaalPenghasilan.php";

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;

class conversationZakatPenghasilanPerhitungan extends Conversation
{
    
    protected $penghasilan;
    protected $pengeluaran;
    protected $penghasilanBulanan;
    protected $periode;
    protected $hutang;
    protected $bonusPenghasilan;
    protected $bonuspenghasilanBulanan;

    public function KalkulatorZPenghasilan()
{
    $this->say('
    <b>Kalkulator Zakat Penghasilan</b>
    <br>Silahkan jawab pertanyaan berikut! Balas 0 jika ingin mengosongkan pertanyaan', ['parse_mode'=>'HTML']);

    $question = Question::create('Apakah Anda ingin menghitung zakat penghasilan per bulan atau per tahun?')
        ->fallback('Jawaban tidak valid')
        ->callbackId('ask_periode_zakat_penghasilan')
        ->addButtons([
            Button::create('Bulan')->value('bulan'),
            Button::create('Tahun')->value('tahun'),
        ]);

    $this->ask($question, function (Answer $answer) {
        $this->periode = strtolower($answer->getValue());
        if (in_array($this->periode, ['bulan', 'tahun'])) {
            $this->askGaji();
        } else {
            $this->say('Jawaban tidak valid. Silakan mulai kembali dan balas dengan "bulan" atau "tahun".');
            $this->repeat();
        }
    });
}

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

public function askGaji()
{
    $periodeTeks = ($this->periode == 'bulan') ? 'Perbulan' : 'Pertahun';
    $this->ask("Berapa Penghasilan Anda $periodeTeks?", function(Answer $answer) {
        $this->penghasilan = (float) $this->convertToNumeric($answer->getText());
        $this->askBonus();
    });
}

public function askBonus()
{
    $periodeTeks = ($this->periode == 'bulan') ? 'Perbulan' : 'Pertahun';
    $this->ask("Berapa Bonus Penghasilan Anda $periodeTeks?", function(Answer $answer) {
        $this->bonusPenghasilan = (float) $this->convertToNumeric($answer->getText());
        $this->askPengeluaran();
    });
}

public function askPengeluaran()
{
    $periodeTeks = ($this->periode == 'bulan') ? 'Perbulan' : 'Pertahun';
    $this->ask("Berapa jumlah pengeluaran yang harus Anda bayar?($periodeTeks)", function(Answer $answer) {
        $this->pengeluaran = (float) $this->convertToNumeric($answer->getText());
        $this->askHutang();
    });
}

public function askHutang()
{
    $this->ask("Berapa jumlah hutang yang harus Anda bayar?", function(Answer $answer) {
        $this->hutang = (float) $this->convertToNumeric($answer->getText());
        $this->calculateZakatPenghasilan();
    });
}

public function calculateZakatPenghasilan()
{
    $totalPenghasilan = $this->penghasilan + $this->bonusPenghasilan - $this->hutang - $this->pengeluaran;
    $zakatPenghasilan = $this->PerhitunganZakatPenghasilan($totalPenghasilan, $this->periode);
    if($this->periode =='bulan'){
        $sell = $this->getHargaEmas()*85;
        $nisab = $sell/12;
        $saynisab = "Nisab penghasilan bulanan yaitu sebesar Rp ". number_format($nisab, 0, ',', '.');
    } 
    else{
        $sell = $this->getHargaEmas()*85;
        $nisab = $sell;
        $saynisab = "Nisab penghasilan tahunan yaitu sebesar Rp ". number_format($nisab, 0, ',', '.');
    }
    if ($zakatPenghasilan > 0) {
        $this->say('
### Kalkulator Zakat Penghasilan ###

Jumlah zakat penghasilan = 
(Penghasilan + bonus - hutang - pengeluaran) x 2.5%
' . number_format($this->penghasilan, 0, ',', '.') . ' + ' . number_format($this->bonusPenghasilan, 0, ',', '.') . ' - ' . number_format($this->hutang, 0, ',', '.') . ' - ' . number_format($this->pengeluaran, 0, ',', '.') . ' x 2.5%
= Rp' . number_format($zakatPenghasilan, 0, ',', '.') . '

'.$saynisab.' (SK Ketua BAZNAS Nomor 1 Tahun 2024)

Karena total penghasilan bersih anda melebihi nisab sebesar Rp '.number_format($totalPenghasilan, 0, ',', '.').' 

Sesuai ketentuan berzakat 2,5% dari total penghasilan sesuai hadis [riwayat Abu Daud dari Ali bin Abi Thalib RA yang menyatakan kewajiban membayar zakat sebesar seperempat puluh (2,5%) dari penghasilan]. 

Zakat penghasilan yang harus Anda bayarkan adalah: Rp ' . number_format($zakatPenghasilan, 0, ',', '.'));
        $this->niatZakat();
    } else {
        $outputRumus = "### Kalkulator Zakat Penghasilan ###\n\n";
        $outputRumus.= "Jumlah penghasilan bersih";
        $outputRumus.= "\n= Penghasilan + bonus - hutang - pengeluaran";
        $outputRumus.= "\n= Rp". number_format($totalPenghasilan, 0, ',', '.')."\n\n";
        if($this->periode =='bulan'){
            $sell = $this->getHargaEmas()*85;
            $nisab = $sell/12;
            $saynisab = "Nisab penghasilan bulanan yaitu sebesar Rp ". number_format($nisab, 0, ',', '.');
        } 
        else{
            $sell = $this->getHargaEmas()*85;
            $nisab = $sell;
            $saynisab = "Nisab penghasilan tahunan yaitu sebesar Rp ". number_format($nisab, 0, ',', '.');
        }
        $this->say($outputRumus.'Anda tidak wajib membayar zakat karena total penghasilan bersih Anda kurang dari '.$saynisab.'. Anda tetap bisa menyempurnakan niat baik dengan bersedekah.');
        // Instansiasi conversationZakatMaal
        $conversation = new conversationZakatMaalPenghasilan();

        // Jalankan metode run jika perlu
        $this->bot->startConversation($conversation);

        // Langsung panggil metode finish
        $conversation->askConfirmHitung();
    }
}

function PerhitunganZakatPenghasilan($penghasilan, $periode)
{
    $sell = $this->getHargaEmas();
    $nisab = $sell*85; // Nisab pertahun
    if ($periode == 'bulan') {
        $nisab /= 12; // Nisab per bulan
    }
    $persentase = 2.5 / 100;
    $zakat = 0;
    if ($penghasilan >= $nisab) {
        $zakat = $penghasilan * $persentase;
    }
    return $zakat;
}
    
    public function niatZakat(){
// Instansiasi conversationZakatMaal
$conversation = new conversationZakatMaalPenghasilan();

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
        $this->KalkulatorZPenghasilan();
    }
}
?>