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
        $this->say("<b>Dalil dan Hadis Zakat Peternakan</b>", ['parse_mode' => 'HTML']);
        $this->say('
Surah Al-Baqarah (2:267):

"Wahai orang-orang yang beriman, nafkahkanlah (di jalan Allah) sebagian dari hasil usahamu yang baik-baik dan sebagian dari apa yang Kami keluarkan dari bumi untuk kamu. Dan janganlah kamu memilih yang buruk-buruk lalu kamu menafkahkan daripadanya, padahal kamu sendiri tidak akan mengambilnya melainkan dengan memiringkan kedua mata terhadapnya. Dan ketahuilah, bahwasanya Allah Maha Kaya lagi Maha Terpuji." (QS. Al-Baqarah: 267)

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