<?php

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
require_once __DIR__."/conversationZakatFitrahDalil.php";


class conversationDalil extends Conversation
{
    public function askReason()
    {
        $question = Question::create('Pilih Dalil mana yang anda maksud?')
		->addButtons([
			Button::create('Dalil zakat Fitrah')->value('0'),
			Button::create('Dalil zakat Maal')->value('1'),
			Button::create('Dalil zakat Emas dan Perak')->value('2'),
			Button::create('Dalil zakat Penghasilan')->value('3'),
			Button::create('Dalil zakat Pertanian')->value('4'),
			Button::create('Dalil zakat Peternakan')->value('5'),
			Button::create('Batalkan')->value('6'),
		]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->getValue() === '0') {
                $this->bot->startConversation(new conversationZakatFitrahDalil());
            }
            elseif ($answer->getValue() === '1') {
                $this->bot->startConversation(new conversationZakatMaalDalil());
            }
            elseif ($answer->getValue() === '2') {
                $this->bot->startConversation(new conversationZakatEmasDalil());
            }
            elseif ($answer->getValue() === '3') {
                $this->bot->startConversation(new conversationZakatPenghasilanDalil());
            }
            elseif ($answer->getValue() === '4') {
                $this->bot->startConversation(new conversationZakatPertanianDalil());
            }
            elseif ($answer->getValue() === '5') {
                $this->bot->startConversation(new conversationZakatPeternakanDalil());
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