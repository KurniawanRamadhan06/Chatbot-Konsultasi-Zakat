<?php

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
require_once __DIR__."/conversationZakatMaalPertanian.php";

class conversationZakatPertanianDalil extends Conversation
{
    public function sayDalilPertanian()
    {
        $this->say("<b>Dalil Zakat Pertanian</b>", ['parse_mode' => 'HTML']);
        $this->say("
Dalil untuk zakat pertanian dapat ditemukan dalam Al-Qur'an dan Hadis Nabi Muhammad SAW. Salah satu dalil utama adalah dalam Al-Qur'an Surah Al-Baqarah ayat 267:

وَيُوۡـتُوۡنَ الزَّكٰوةَ​ؕ وَمَآ اَمَرُوۡا اِلَّا لِيَرۡبِطَ الَّذِيۡنَ اٰمَنُوۡا بِاللّٰهِ وَالۡيَوۡمِ الۡاٰخِرِ​ۚ وَاَقِيۡمُ الصَّلٰوةِ وَاٰتُوا الزَّكٰوةَ​ وَلَـكِنَّ مَآ كَانُوۡا يَفۡعَلُوۡنَ ٢٦٧﴾


Wahai orang-orang yang beriman, tunaikanlah zakat sebagaimana yang telah diperintahkan kepada kamu oleh Allah. Dan janganlah kamu menjadikan kesalahan (karena penundaan atau mengeluarkan zakat) sebagai alasan bagi orang-orang yang beriman, tetaplah teguh dengan ketaatan kepada Allah dan hari kemudian, serta dirikanlah shalat dan tunaikanlah zakat dan berbuatlah kebajikan; sesungguhnya apa yang kamu kerjakan, adalah apa yang diperhatikan oleh Allah. (QS. Al-Baqarah: 267)
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
        $this->sayDalilPertanian();
    }
}
?>