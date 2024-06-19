<?php

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
require_once __DIR__."/conversationZakatPenghasilanDalil.php";
require_once __DIR__."/conversationZakatPenghasilanPerhitungan.php";

class conversationZakatMaalPenghasilan extends Conversation
{
    public function askPilihan()
    {
        $question = Question::create('Apa yang ingin anda tanyakan terkait Zakat Penghasilan?')
		->addButtons([
			Button::create('Lihat Penjelasan')->value('0'),
			Button::create('Lihat Dalil')->value('1'),
			Button::create('Lihat Niat')->value('niat'),
			Button::create('Lihat Perhitungan')->value('2'),
			Button::create('Selesai')->value('3'),
		]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->getValue() === '0') {
                $this->pengertianZPenghasilan();
            }
            elseif ($answer->getValue() === '1') {
                $this->bot->startConversation(new conversationZakatPenghasilanDalil());

            }
            elseif ($answer->getValue() === '2') {
                $this->bot->startConversation(new conversationZakatPenghasilanPerhitungan());
                
            }
            elseif ($answer->getValue() === 'niat') {
                $this->bot->startConversation(new conversationZakatPenghasilanNiat());
                
            }
            else
            {
                $this->finish();
            }
        });
    }
    public function pengertianZPenghasilan()
    {
        $this->say("<b>Pengertian Zakat Penghasilan</b>", ['parse_mode'=>'HTML']);
        $this->say(" <b>Zakat Penghasilan</b> Adalah zakat yang dikeluarkan dari penghasilan yang diperoleh dari hasil profesi pada saat menerima pembayaran, zakat ini dikenal juga sebagai zakat profesi atau zakat penghasilan.", ['parse_mode'=>'HTML']);
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