<?php

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
require_once __DIR__."/conversationZakatMaalEmas.php";

class conversationZakatEmasDalil extends Conversation
{
    public function sayDalilEmas()
    {
        $this->say("<b>Dalil dan Hadis Zakat Emas</b>", ['parse_mode' => 'HTML']);
        $this->say('
Dalil mengenai kewajiban zakat atas emas ini ada dalam Al-Quran Surat At-Taubah
Ayat 34: 

۞ يٰٓاَيُّهَا الَّذِينَ اٰمَنُوْٓا اِنَّ كَثِيْرًا مِّنَ الْاَحْبَارِ وَالرُّهْبَانِ لَيَأْكُلُوْنَ اَمْوَالَ النَّاسِ بِالْبَاطِلِ وَيَصُدُّوْنَ عَنْ سَبِيْلِ اللّٰهِ ۗوَالَّذِيْنَ يَكْنِزُوْنَ الذَّهَبَ وَالْفِضَّةَ وَلَا يُنْفِقُوْنَهَا فِيْ سَبِيْلِ اللّٰهِ ۙفَبَشِّرْهُمْ بِعَذَابٍ اَلِيْمٍۙ34

yang Artinya: “Dan orang-orang yang menyimpan emas dan perak dan tidak menafkahkannya pada jalan Allah, maka beritahukanlah kepada mereka, (bahwa mereka akan mendapat) siksa yang pedih,”.

Hadis dari Abdullah bin Amr bin Ash (RA):
Rasulullah SAW bersabda, "Tidak ada zakat atas harta yang tidak mencapai lima uqiyah (85 gram emas)." (HR. Bukhari dan Muslim)

        ',['parse_mode' => 'Markdown']);
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
        $this->sayDalilEmas();
    }
}
?>