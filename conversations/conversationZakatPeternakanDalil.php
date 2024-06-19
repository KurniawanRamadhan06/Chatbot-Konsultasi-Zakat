<?php

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
require_once __DIR__."/conversationZakatMaalPeternakan.php";

class conversationZakatPeternakanDalil extends Conversation
{
    public function sayDalilPeternakan()
    {
        $this->say("<b>Dalil Zakat Peternakan</b>", ['parse_mode' => 'HTML']);
        $this->say('
Diriwayatkan oleh Abu Dzar dari Nabi SAW bahwa beliau bersabda:

“Tidak ada balasan bagi pemilik unta, sapi, atau kambing, kemudian tidak mengeluarkan zakatnya, kecuali datang hewan-hewan itu pada hari kiamat dengan ukuran yang lebih besar, lebih gemuk, sambil menanduk dan menendang.” (H.R. Muttafaq Alaih)
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
        $this->sayDalilPeternakan();
    }
}
?>