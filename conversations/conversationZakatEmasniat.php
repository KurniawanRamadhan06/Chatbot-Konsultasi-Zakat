<?php

require_once __DIR__."/conversationZakatFitrahDalil.php";

use BotMan\BotMan\BotMan;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class conversationZakatEmasniat extends Conversation
{

    public function niatZakatEMas()
    {
        $this->say("<b>Niat Zakat Emas dan Perak</b>", ['parse_mode'=>'html']);
        $this->say("
        نَوَيْتُ أَنْ أُخْرِجَ زَكاَةَ اْللَالِ عَنْ نَفْسِيْ فَرْضًالِلهِ تَعَالَى

Nawaitu an ukhrija zakatadz dzahabi/zakatal fidhdhati/zakatal mali'an nafsi fardan lillahi ta'ala
        
Artinya: Saya berniat mengeluarkan zakat berupa emas/perak/harta dari diri sendiri karena Allah Ta'ala.

        ",['parse_mode'=>'Markdown']);
        // Instansiasi conversationZakatMaal
        $conversation = new conversationZakatMaalEmas();

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
        $this->niatZakatEmas();
    }
}
?>