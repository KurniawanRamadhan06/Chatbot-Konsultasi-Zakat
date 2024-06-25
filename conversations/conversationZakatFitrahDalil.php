<?php


use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
require_once __DIR__."/conversationZakatFitrah.php";

class conversationZakatFitrahDalil extends Conversation
{

    public function sayDalilFitrah()
    {
        $this->say("<b>Dalil dan Hadis Zakat Fitrah</b>", ['parse_mode' => 'HTML']);
        $this->say('Dalil zakat fitrah salah satunya adalah Surah At-Taubah ayat 60, Artinya: Sesungguhnya zakat itu hanyalah untuk orang-orang fakir, orang-orang miskin, para amil zakat, orang-orang yang dilunakkan hatinya (mualaf), untuk (memerdekakan) para hamba sahaya, untuk (membebaskan) orang-orang yang berutang, untuk jalan Allah dan untuk orang-orang yang sedang dalam perjalanan (yang memerlukan pertolongan), sebagai kewajiban dari Allah. Allah Maha Mengetahui lagi Maha Bijaksana.
        
Hadis dari Ibn Abbas (RA):
Rasulullah SAW mewajibkan zakat fitrah sebagai penyucian bagi orang yang berpuasa dari perkataan yang sia-sia dan perbuatan dosa, serta untuk memberi makan kepada orang miskin. (HR. Abu Daud dan Ibn Majah)
        ',['parse_mode' => 'HTML']);
        // $this->askConfirm();
        
        // Instansiasi conversationZakatMaal
        $conversation = new conversationZakatFitrah();

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
        $this->sayDalilFitrah();
    }
}
?>