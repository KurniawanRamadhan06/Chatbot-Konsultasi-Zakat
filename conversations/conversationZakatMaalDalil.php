<?php

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
require_once __DIR__."/conversationZakatMaal.php";

class conversationZakatMaalDalil extends Conversation
{
    public function sayDalilMaal()
    {
        $this->say("<b>Dalil dan Hadis Zakat Maal</b>", ['parse_mode' => 'HTML']);
        $this->say('
Dalil zakat Maal tertuang dalam surah al baqarah ayat 273:

لِلْفُقَرَاءِ الَّذِينَ أُحْصِرُوا فِي سَبِيلِ اللَّهِ لَا يَسْتَطِيعُونَ ضَرْبًا فِي الْأَرْضِ يَحْسَبُهُمُ الْجَاهِلُ أَغْنِيَاءَ مِنَ التَّعَفُّفِ تَعْرِفُهُمْ بِسِيمَاهُمْ لَا يَسْأَلُونَ النَّاسَ إِلْحَافًا ۗ وَمَا تُنْفِقُوا مِنْ خَيْرٍ فَإِنَّ اللَّهَ بِهِ عَلِيمٌ

Artinya: "(Apa yang kamu infakkan) adalah untuk orang-orang fakir yang terhalang (usahanya karena jihad) di jalan Allah, sehingga dia yang tidak dapat berusaha di bumi; (orang lain) yang tidak tahu, menyangka bahwa mereka adalah orang-orang kaya karena mereka menjaga diri (dari meminta-minta).

Hadis dari Abu Hurairah (RA):
Rasulullah SAW bersabda, "Harta yang tidak dizakati (dengan zakat), pasti akan dibakar pada hari kiamat menjadi bara api." (HR. Muslim)
',['parse_mode' => 'Markdown']);
        // Instansiasi conversationZakatMaal
        $conversation = new conversationZakatMaal();

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
        $this->sayDalilMaal();
    }
}
?>