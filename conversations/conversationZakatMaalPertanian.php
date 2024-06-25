<?php

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
require_once __DIR__."/conversationZakatPertanianDalil.php";
require_once __DIR__."/conversationZakatPertanianPerhitungan.php";

class conversationZakatMaalPertanian extends Conversation
{
    public function askPilihan()
    {
        $question = Question::create('Apa yang ingin anda tanyakan terkait Zakat Pertanian?')
		->addButtons([
			Button::create('Lihat Penjelasan')->value('0'),
			Button::create('Lihat Dalil')->value('1'),
			Button::create('Lihat Niat')->value('niat'),
			Button::create('Lihat Perhitungan')->value('2'),
			Button::create('Selesai')->value('3'),
		]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->getValue() === '0') {
                $this->pengertianZPertanian();
            }
            elseif ($answer->getValue() === '1') {
                $this->bot->startConversation(new conversationZakatPertanianDalil());

            }
            elseif ($answer->getValue() === '2') {
                $this->bot->startConversation(new conversationZakatPertanianPerhitungan());
                
            }
            elseif ($answer->getValue() === 'niat') {
                $this->bot->startConversation(new conversationZakatPertanianNiat());
                
            }
            else
            {
                $this->finish();
            }
        });
    }
    public function pengertianZPertanian()
    {
        $this->say("<b>Pengertian Zakat Pertanian</b>", ['parse_mode'=>'HTML']);
        $this->say("<b>Zakat Pertanian</b> Adalah kewajiban bagi umat Islam untuk memberikan sebagian dari hasil pertanian mereka kepada yang berhak menerimanya, sesuai dengan ketentuan syariat Islam. Tujuan utama zakat pertanian adalah mengurangi kesenjangan sosial, menghapuskan kemiskinan, dan memperkuat solidaritas sosial dalam masyarakat Muslim. Hukum zakat pertanian adalah wajib, dengan landasan hukum yang terdapat dalam Al-Qur'an dan Sunnah Rasulullah SAW. Penghitungan zakat pertanian berdasarkan nisab yang telah ditetapkan dan persentase tertentu dari hasil panen, yang kemudian didistribusikan kepada mereka yang memenuhi syarat menerima zakat, seperti fakir miskin dan mustahik. Dengan membayar zakat pertanian, umat Islam dapat berkontribusi dalam meningkatkan kesejahteraan umat secara keseluruhan serta memelihara ikatan solidaritas sosial dalam masyarakat.",['parse_mode'=>'HTML']);
        $this->askConfirm();        
    }
    public function finish() 
    {
        $this->say("<b>Selesai</b>", ['parse_mode'=>'HTML']);
        $this->say('Terima kasih sudah berkonsultasi.');
    }

    public function askConfirm()
    {
        $question = Question::create('Apakah ada hal lain yang ingin Anda tanyakan?')
		->addButtons([
			Button::create('Ya')->value('0'),
			Button::create('Tidak')->value('1'),
		]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->getValue() === '0') {
                $this->askPilihan();
            }
            else
            {
                $this->finish();
            }
        });
    }
    public function askConfirmHitung()
    {
        $question = Question::create('Apakah ada hal lain yang ingin Anda tanyakan?')
		->addButtons([
			Button::create('Ya')->value('0'),
			Button::create('Tidak')->value('1'),
			Button::create('Hitung Ulang')->value('2'),
		]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->getValue() === '0') {
                $this->askPilihan();
            }else if ($answer->getValue() === '2') {
                $this->bot->startConversation(new conversationZakatPertanianPerhitungan());
            }
            else
            {
                $this->finish();
            }
        });
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        
    }
}
?>