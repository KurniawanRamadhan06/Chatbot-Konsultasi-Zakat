<?php

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
require_once __DIR__."/conversationZakatMaalPertanian.php";

class conversationZakatPertanianNiat extends Conversation
{
    public function niatZakatPertanian()
    {
        $this->say("<b>Niat Zakat Pertanian</b>", ['parse_mode' => 'HTML']);
        $this->say("
Arab:
        نَوَيْتُ أَنْ أُخْرِجَ زَكَاةَ الْمَالِ عَنْ هَذَا الْحَرْثِ لِلَّهِ تَعَالَى.
        
Latin:
Nawaitu an ukhrija zakata al-maal 'an haadhal harsa lillahi ta'ala.

Artinya:
Saya niat mengeluarkan zakat maal dari hasil pertanian saya karena Allah Ta'ala.

Niat ini diucapkan dalam hati saat hendak mengeluarkan zakat, dengan tujuan untuk memenuhi kewajiban syariah dan membantu mereka yang membutuhkan sesuai dengan ketentuan zakat masing-masing.
",['parse_mode' => 'Markdown']);
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
        $this->niatZakatPertanian();
    }
}
?>