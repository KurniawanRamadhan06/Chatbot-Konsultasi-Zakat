<?php

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

require_once __DIR__."/conversationZakatFitrahniat.php";
require_once __DIR__."/conversationZakatEmasniat.php";
require_once __DIR__."/conversationZakatPenghasilanNiat.php";
require_once __DIR__."/conversationZakatPertanianNiat.php";
require_once __DIR__."/conversationZakatPeternakanNiat.php";


class conversationNiat extends Conversation
{
    public function askReason()
    {
        $question = Question::create('Pilih Niat apa yang anda maksud?')
		->addButtons([
			Button::create('Niat Zakat Fitrah')->value('0'),
			Button::create('Niat Zakat Emas dan Perak')->value('1'),
			Button::create('Niat Zakat Penghasilan')->value('2'),
			Button::create('Niat Zakat Pertanian')->value('3'),
			Button::create('Niat Zakat Peternakan')->value('4'),
			Button::create('Batalkan')->value('5'),
		]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->getValue() === '0') {
                $this->bot->startConversation(new conversationZakatFitrahniat);
            }
            elseif ($answer->getValue() === '1') {
                $this->bot->startConversation(new conversationZakatEmasniat);
            }
            elseif ($answer->getValue() === '2') {
                $this->bot->startConversation(new conversationZakatPenghasilanNiat);
            }
            elseif ($answer->getValue() === '3') {
                $this->bot->startConversation(new conversationZakatPertanianNiat);
            }
            elseif ($answer->getValue() === '4') {
                $this->bot->startConversation(new conversationZakatPeternakanNiat);
            }
            else
            {
                $this->say('Terima kasih, Proses Dibatalkan.  <i>Jika anda berubah pikiran, silahkan ketik </i> -> <b>/dalil </b>', ['parse_mode'=>'HTML']);
            }
        });
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askReason();
    }
}
?>