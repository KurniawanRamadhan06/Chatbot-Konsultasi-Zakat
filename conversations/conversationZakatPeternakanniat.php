<?php

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
require_once __DIR__."/conversationZakatMaalPeternakan.php";

class conversationZakatPeternakanNiat extends Conversation
{
    public function niatZakatPeternakan()
    {
        $this->say("<b>Niat Zakat Peternakan</b>", ['parse_mode' => 'HTML']);
        $this->say('
Arab:
نَوَيْتُ أَنْ أُخْرِجَ زَكَاةَ الْمَالِ عَنْ هَذِهِ الْبَهَائِمِ لِلَّهِ تَعَالَى.

Latin:
Nawaitu an ukhrija zakata al-maal an haadihil bahaa im lillahi ta ala.

Artinya:
Saya niat mengeluarkan zakat maal dari peternakan saya karena Allah Ta ala
        ',['parse_mode' => 'Markdown']);
        // Instansiasi conversationZakatMaal
        $conversation = new conversationZakatMaalPeternakan();

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
        $this->niatZakatPeternakan();
    }
}
?>