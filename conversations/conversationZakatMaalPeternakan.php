<?php

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
require_once __DIR__."/conversationZakatPeternakanDalil.php";
require_once __DIR__."/conversationZakatPeternakanPerhitungan.php";

class conversationZakatMaalPeternakan extends Conversation
{
    public function askPilihan()
    {
        $question = Question::create('Apa yang ingin anda tanyakan terkait Zakat Peternakan?')
		->addButtons([
			Button::create('Lihat Penjelasan')->value('0'),
			Button::create('Lihat Dalil')->value('1'),
			Button::create('Lihat Niat')->value('niat'),
			Button::create('Lihat Perhitungan')->value('2'),
			Button::create('Selesai')->value('3'),
		]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->getValue() === '0') {
                $this->pengertianZPeternakan();
            }
            elseif ($answer->getValue() === '1') {
                $this->bot->startConversation(new conversationZakatPeternakanDalil());

            }
            elseif ($answer->getValue() === '2') {
                $this->bot->startConversation(new conversationZakatPeternakanPerhitungan());
                
            }
            elseif ($answer->getValue() === 'niat') {
                $this->bot->startConversation(new conversationZakatPeternakanNiat());
                
            }
            else
            {
                $this->finish();
            }
        });
    }
    public function pengertianZPeternakan()
    {
        $this->say("<b>Pengertian Zakat Peternakan</b>", ['parse_mode'=>'HTML']);
        $this->say("<b>Zakat Peternakan</b> Adalah zakat yang dikenakan atas hasil pertanian, perkebunan dan hasil hutan pada saat panen.",['parse_mode'=>'HTML']);
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