<?php

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
require_once __DIR__."/conversationZakatMaalPenghasilan.php";

class conversationZakatPenghasilanPerhitungan extends Conversation
{
    
    protected $penghasilan;
    protected $penghasilanBulanan;
    protected $periode;
    protected $bonuspenghasilanBulanan;

    public function KalkulatorZPenghasilan()
{
    $this->say('
    <b>Kalkulator Zakat Penghasilan</b>
    <br>Silahkan jawab pertanyaan berikut! Balas - (tanda hubung) untuk melewati atau mengosongkan pertanyaan', ['parse_mode'=>'HTML']);
    $this->ask('Apakah Anda ingin menghitung zakat penghasilan per bulan atau per tahun? (Balas dengan "bulan" atau "tahun")', function(Answer $answer) {
        $this->periode = strtolower($answer->getText());
        if ($this->periode == 'bulan' || $this->periode == 'tahun') {
            $this->askGaji();
        } else {
            $this->say('Jawaban tidak valid. Silakan mulai kembali dan balas dengan "bulan" atau "tahun".');
        }
    });
}

public function askGaji()
{
    $periodeTeks = ($this->periode == 'bulan') ? 'Perbulan' : 'Pertahun';
    $this->ask("Berapa Penghasilan Anda $periodeTeks?", function(Answer $answer) {
        $this->penghasilan = (float) str_replace(['Rp', '.', ','], '', $answer->getText());
        $this->askBonus();
    });
}

public function askBonus()
{
    $periodeTeks = ($this->periode == 'bulan') ? 'Perbulan' : 'Pertahun';
    $this->ask("Berapa Bonus Penghasilan Anda $periodeTeks?", function(Answer $answer) {
        $bonusPenghasilan = (float) str_replace(['Rp', '.', ','], '', $answer->getText());
        $totalPenghasilan = $this->penghasilan + $bonusPenghasilan;

        $zakatPenghasilan = $this->PerhitunganZakatPenghasilan($totalPenghasilan, $this->periode);
        if ($zakatPenghasilan > 0) {
            $this->say('Jumlah zakat penghasilan = 
(Penghasilan + bonus) x 2.5%
' . number_format($this->penghasilan, 0, ',', '.') . ' + ' . number_format($bonusPenghasilan, 0, ',', '.') . ' x 2.5%
= Rp' . number_format($zakatPenghasilan, 0, ',', '.') . '
                
Zakat penghasilan yang harus Anda bayarkan adalah: Rp ' . number_format($zakatPenghasilan, 0, ',', '.'));
            $this->niatZakat();
        } else {
            $this->say('Anda tidak wajib membayar zakat karena penghasilan Anda kurang dari nisab.');
            // Instansiasi conversationZakatMaal
            $conversation = new conversationZakatMaalPenghasilan();

            // Jalankan metode run jika perlu
            $this->bot->startConversation($conversation);

            // Langsung panggil metode finish
            $conversation->askConfirm();
        }
    });
}

function PerhitunganZakatPenghasilan($penghasilan, $periode)
{
    $nisab = 114665000; // Nisab pertahun
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
$conversation->askConfirm();
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