<?php

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
require_once __DIR__."/conversationZakatMaalPenghasilan.php";

class conversationZakatPenghasilanNiat extends Conversation
{
    public function niatZakatPenghasilan()
    {
        $this->say("<b>Niat Zakat Penghasilan</b>", ['parse_mode' => 'HTML']);
        $this->say('
Arab:
نَوَيْتُ أَنْ أُخْرِجَ زَكَاةَ الْمَالِ عَنْ كُلِّ مَا اتَّصَلْتُ مِنَ الْأَمْوَالِ لِلَّهِ تَعَالَى.

Latin:
Nawaitu an ukhrija zakata al-maal an kulla maa ittasaltu min al-amwal lillahi ta ala.

Artinya:
Saya niat mengeluarkan zakat maal dari penghasilan saya karena Allah Ta ala.

        ',['parse_mode' => 'Markdown']);


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
        $this->niatZakatPenghasilan();
    }
}
?>