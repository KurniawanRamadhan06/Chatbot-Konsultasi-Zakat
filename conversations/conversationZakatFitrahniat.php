<?php

require_once __DIR__."/conversationZakatFitrahDalil.php";

use BotMan\BotMan\BotMan;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class conversationZakatFitrahniat extends Conversation
{

    public function niatZakatFitrah()
    {
        $this->say("<b>Niat Zakat Fitrah</b>", ['parse_mode'=>'html']);
        $this->say("
1. Niat untuk diri Sendiri

        نَوَيْتُ أَن أُخْرِج زكاة الفِطْرِ عَنْ نَفْسِي فَرْضًا لِلَّهِ تَعَالَى

Arab latin: Nawaitu an ukhrija zakaatal fithri an nafsii fardhan lillaahi ta aalaa.

Artinya: Aku niat mengeluarkan zakat fitrah untuk diriku sendiri, fardu karena Allah Taâlâ.

        ",['parse_mode'=>'Markdown']);
        $this->say("
2. Niat untuk Diri Sendiri dan Keluarga

نَوَيْتُ أن أخرج زكاة الفِطْرِ عَنِّي وَعَنْ جَمِيعِ مَا يَلْزَمُنِي نَفَقَاتُهُمْ شَرْعًا فَرْضًا لِلهِ تَعَالَى

Arab latin: Nawaitu an ukhrija zakaatal fithri anni wa an jamii i ma yalzamunii nafaqaatuhum syar an fardhan lillaahi ta aalaa.

Artinya: Aku niat mengeluarkan zakat fitrah untuk diriku dan seluruh orang yang nafkahnya menjadi tanggunganku, fardu karena Allah Ta ala.

        ",['parse_mode'=>'Markdown']);
        $this->say("
3. Niat untuk Istri


نَوَيْتُ أن أخرج زكاة الفطر عَنْ زَوجَتِي فَرْضًا لِلهِ تَعَالَى

Arab latin: Nawaitu an ukhrija zakaatal fithri an zaujatii fardhan lillaahi ta aalaa.

Artinya: Aku niat mengeluarkan zakat fitrah untuk istriku, fardu karena Allah Ta ala.

        ",['parse_mode'=>'Markdown']);
        $this->say("
4. Niat untuk Orang yang Diwakilkan

نَوَيْتُ أن أخرج زكاة الفطر عَنْ (.....) فَرْضًا للهِ تَعَالَى

Arab latin: Nawaitu an ukhrija zakaatal fithri an ... (sebutkan nama) fardhan lillaahi ta aalaa.



        ",['parse_mode'=>'Markdown']);
        
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
        $this->niatZakatFitrah();
    }
}
?>