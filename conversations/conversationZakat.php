<?php

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
// require_once __DIR__."/conversationZakatFitrahDalil.php";

class conversationZakat extends Conversation
{
    public function askReason()
    {
        $question = Question::create('Pilih Zakat apa yang anda maksud?')
		->addButtons([
			Button::create('Zakat Fitrah')->value('0-'),
			Button::create('Zakat Maal')->value('1-'),
            Button::create('Zakat Emas dan Perak')->value('2-'),
			Button::create('Zakat Penghasilan')->value('3-'),
			Button::create('Zakat Peternakan')->value('4-'),
			Button::create('Zakat Pertanian')->value('5-'),
			Button::create('Batalkan')->value('cancel'),
		]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->getValue() === '0-') {
                // Instansiasi conversationZakatMaal
                $conversation = new conversationZakatFitrah();

                // Jalankan metode run jika perlu
                $this->bot->startConversation($conversation);

                // Langsung panggil metode finish
                $conversation->askPilihan();
            }
            elseif ($answer->getValue() === '1-') {
                // Instansiasi conversationZakatMaal
                $conversation = new conversationZakatMaal();

                // Jalankan metode run jika perlu
                $this->bot->startConversation($conversation);

                // Langsung panggil metode finish
                $conversation->askPilihan();
            }
            elseif ($answer->getValue() === '2-') {
                // Instansiasi conversationZakatMaal
                $conversation = new conversationZakatMaalEmas();

                // Jalankan metode run jika perlu
                $this->bot->startConversation($conversation);

                // Langsung panggil metode finish
                $conversation->askPilihan();
            }
            elseif ($answer->getValue() === '3-') {
                // Instansiasi conversationZakatMaal
                $conversation = new conversationZakatMaalPenghasilan();

                // Jalankan metode run jika perlu
                $this->bot->startConversation($conversation);

                // Langsung panggil metode finish
                $conversation->askPilihan();
            }
            elseif ($answer->getValue() === '4-') {
                // Instansiasi conversationZakatMaal
                $conversation = new conversationZakatMaalPeternakan();

                // Jalankan metode run jika perlu
                $this->bot->startConversation($conversation);

                // Langsung panggil metode finish
                $conversation->askPilihan();
            }
            elseif ($answer->getValue() === '5-') {
                // Instansiasi conversationZakatMaal
                $conversation = new conversationZakatMaalPertanian();

                // Jalankan metode run jika perlu
                $this->bot->startConversation($conversation);

                // Langsung panggil metode finish
                $conversation->askPilihan();
            }
            else
            {
                $this->say('Terima kasih, Proses Dibatalkan.  <i>Jika anda berubah pikiran, silahkan ketik </i> -> <b>/zakat </b>', ['parse_mode'=>'HTML']);
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