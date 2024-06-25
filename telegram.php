<?php
//kurniawan ramadhan (Author)
require_once __DIR__."/vendor/autoload.php";


require_once __DIR__."/conversations/conversationZakatFitrah.php";
require_once __DIR__."/conversations/conversationZakatMaal.php";
require_once __DIR__."/conversations/conversationZakatFitrahDalil.php";
require_once __DIR__."/conversations/conversationDalil.php";
require_once __DIR__."/conversations/conversationZakat.php";
require_once __DIR__."/conversations/conversationNiat.php";

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Cache\SymfonyCache;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

$config = [
        'telegram' => [
        'token' => '6910900683:AAHMjNVQm3gHUIJu6oFG2L3d_Ecepl9lZUE'
        ]
    ];

putenv('GOOGLE_CLOUD_PROJECT=baznas-lfcw');
putenv('GOOGLE_APPLICATION_CREDENTIALS=baznas-lfcw-b9deac6d988a.json');

DriverManager::loadDriver(\BotMan\Drivers\Telegram\TelegramDriver::class);

$adapter = new FilesystemAdapter('', 0, __DIR__.'/cache');
$botman = BotManFactory::create($config, new SymfonyCache($adapter));
    

$dialogflow = \BotMan\Middleware\DialogFlow\V2\DialogFlow::create('id');
$botman->middleware->received($dialogflow);
$botman->hears('(.*)', function ($bot) {
    $extras = $bot->getMessage()->getExtras();
    $apiReply = $extras['apiAction'];
    //Pilih Intent
        if($apiReply == 'input.welcome')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
        }
        elseif($apiReply == 'hitung.z.fitrah')
        {
            $conversation = new conversationZakatFitrah();

                // Jalankan metode run jika perlu
                $bot->startConversation($conversation);

                // Langsung panggil metode finish
                $conversation->kalkulatorZakatFitrah();
        } 
        elseif($apiReply == 'input.z.p.pertanian')
        {
            $apiReply = $extras['apiReply'];
            if ($apiReply == 'spesifik') {
                $bot->startConversation(new conversationZakat);
            }
            else{
                $bot->startConversation(new conversationZakatPertanianPerhitungan);
            }
        } 
        elseif($apiReply == 'input.z.p.peternakan')
        {
            $apiReply = $extras['apiReply'];
            if ($apiReply == 'spesifik') {
                $bot->startConversation(new conversationZakat);
            }
            else{
                $bot->startConversation(new conversationZakatPeternakanPerhitungan);
            }
        } 
        elseif($apiReply == 'zakat_niat_fitrah')
        {
            $apiReply = $extras['apiReply'];
            if ($apiReply == 'spesifik') {
                $bot->startConversation(new conversationNiat);
            }
            else{
                $bot->startConversation(new conversationZakatFitrahniat);
            }
        } 
        elseif($apiReply == 'zakat_niat_emas')
        {
            $apiReply = $extras['apiReply'];
            if ($apiReply == 'spesifik') {
                $bot->startConversation(new conversationNiat);
            }
            else{
                $bot->startConversation(new conversationZakatEmasniat);
            }
        } 
        elseif($apiReply == 'zakat_niat_penghasilan')
        {
            $apiReply = $extras['apiReply'];
            if ($apiReply == 'spesifik') {
                $bot->startConversation(new conversationNiat);
            }
            else{
                $bot->startConversation(new conversationZakatPenghasilanNiat);
            }
        } 
        elseif($apiReply == 'zakat_niat_pertanian')
        {
            $apiReply = $extras['apiReply'];
            if ($apiReply == 'spesifik') {
                $bot->startConversation(new conversationNiat);
            }
            else{
                $bot->startConversation(new conversationZakatPertanianNiat);
            }
        } 
        elseif($apiReply == 'zakat_niat_peternakan')
        {
            $apiReply = $extras['apiReply'];
            if ($apiReply == 'spesifik') {
                $bot->startConversation(new conversationNiat);
            }
            else{
                $bot->startConversation(new conversationZakatPeternakanNiat);
            }
        } 
        elseif($apiReply == 'hitung.z.penghasilan')
        {
            $apiReply = $extras['apiReply'];
            if ($apiReply == 'spesifik') {
                $bot->startConversation(new conversationZakat);
            }
            else{
                $bot->startConversation(new conversationZakatPenghasilanPerhitungan);
            }
        } 
        elseif($apiReply == 'input.z.penghasilan')
        {
            $apiReply = $extras['apiReply'];
            if ($apiReply == 'spesifik') {
                $bot->startConversation(new conversationZakat);
            }
            else{
                // Instansiasi conversationZakatMaal
                $conversation = new conversationZakatMaalPenghasilan();

                // Jalankan metode run jika perlu
                $bot->startConversation($conversation);

                // Langsung panggil metode finish
                $conversation->askPilihan();
            }
        } 
        elseif($apiReply == 'ask_golongan')
        {
            $apiReply = $extras['apiReply'];
            
            if ($apiReply == 'spesifik') {
                $bot->reply("Apakah maksud anda orang yang berhak menerima zakat?
1	Orang Fakir: orang yang tidak memiliki harta dan usaha untuk mencukupi kebutuhan sehari-hari.

2	Orang miskin: orang yang tidak memiliki penghidupan yang cukup dan dalam keadaan kekurangan.

3	Amil: orang yang mengurusi segala proses terselenggaranya zakat fitrah.

4	Mualaf: orang yang memiliki kemungkinan untuk masuk Islam atau mereka yang baru masuk Islam dan dimungkinkan mempunyai iman yang masih lemah. Budak

5	Budak: kemungkinan sudah tidak ada di zaman ini.

6	Gharim: orang yang berutang untuk kebutuhan hidup dalam mempertahankan jiwa dan izzahnya.

7	Fisabilillah: orang-orang yang berjuang di jalan Allah.

8	Ibnu Sabil: orang yang sedang dalam perjalanan demi ketaatan kepada Allah dan kehabisan biaya.
                ");
            }
            else{
                $bot->reply($apiReply);
            }
        } 
        elseif($apiReply == 'baznas_ask8')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
        } 
        elseif($apiReply == 'baznas_ask9')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
        } 
        elseif($apiReply == 'baznas_ask10')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
        }
        // FAQ
        elseif($apiReply == 'baznas.faq.1')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
        }
        elseif($apiReply == 'baznas.faq.2')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
        }
        elseif($apiReply == 'baznas.faq.3')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
        }
        elseif($apiReply == 'baznas.faq.4')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
        }
        elseif($apiReply == 'baznas.faq.5')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
        }
        elseif($apiReply == 'baznas.faq.6')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
        }
        elseif($apiReply == 'baznas.faq.7')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
        }
        elseif($apiReply == 'baznas_ask1')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
        } 
        elseif($apiReply == 'baznas_ask2')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
        } 
        elseif($apiReply == 'baznas_ask3')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
        } 
        elseif($apiReply == 'baznas_ask4')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
        } 
        elseif($apiReply == 'baznas_ask5')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
        } 
        elseif($apiReply == 'baznas_ask6')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
        } 
        elseif($apiReply == 'baznas_ask7')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
        } 
        elseif($apiReply == 'input.z.d.penghasilan')
        {
            $apiReply = $extras['apiReply'];
            if ($apiReply == 'spesifik') {
                $bot->startConversation(new conversationDalil);
            }
            else{
                $bot->startConversation(new conversationZakatPenghasilanDalil);
            }
        } 
        elseif($apiReply == 'input.z.fitrah')
        {
            $apiReply = $extras['apiReply'];
            if ($apiReply == 'spesifik') {
                $bot->startConversation(new conversationZakat);
            }
            else{
                $conversation = new conversationZakatFitrah();

                // Jalankan metode run jika perlu
                $bot->startConversation($conversation);

                // Langsung panggil metode finish
                $conversation->askPilihan();
            }
        } 
        elseif($apiReply == 'p_emas')
        {
            $apiReply = $extras['apiReply'];
            $bot->startConversation(new conversationZakatEmasPerhitungan);

        } 
        elseif($apiReply == 'input.z.d.pertanian')
        {
            $apiReply = $extras['apiReply'];
            if ($apiReply == 'spesifik') {
                $bot->startConversation(new conversationDalil);
            }
            else{
                $bot->startConversation(new conversationZakatPertanianDalil);
            }

        } 
        elseif($apiReply == 'input.z.d.peternakan')
        {
            $apiReply = $extras['apiReply'];
            if ($apiReply == 'spesifik') {
                $bot->startConversation(new conversationDalil);
            }
            else{
                $bot->startConversation(new conversationZakatPeternakanDalil);
            }

        } 
        elseif($apiReply == 'dalil.zakat.emas')
        {
            $apiReply = $extras['apiReply'];
            if ($apiReply == 'spesifik') {
                $bot->startConversation(new conversationDalil);
            }
            else{
                $bot->startConversation(new conversationZakatEmasDalil);
            }

        } 
        elseif($apiReply == 'zakat_peternakan')
        {
            
            if ($apiReply == 'spesifik') 
            {
                $bot->startConversation(new conversationDalil);
            }
            else{
                // Instansiasi conversationZakatMaal
                $conversation = new conversationZakatMaalPeternakan();

                // Jalankan metode run jika perlu
                $bot->startConversation($conversation);

                // Langsung panggil metode finish
                $conversation->askPilihan();
            }

        } 
        elseif($apiReply == 'zakat_pertanian')
        {
            $apiReply = $extras['apiReply'];
            if ($apiReply == 'spesifik') 
            {
                $bot->startConversation(new conversationZakat);
            }
            else
            {
                // Instansiasi conversationZakatMaal
                $conversation = new conversationZakatMaalPertanian();

                // Jalankan metode run jika perlu
                $bot->startConversation($conversation);

                // Langsung panggil metode finish
                $conversation->askPilihan();
            }
        } 
        elseif($apiReply == 'zakat_emas')
        {
            $apiReply = $extras['apiReply'];
            if ($apiReply == 'spesifik') 
            {
                $bot->startConversation(new conversationZakat);
            }
            else
            {
                // Instansiasi conversationZakatMaal
                $conversation = new conversationZakatMaalEmas();

                // Jalankan metode run jika perlu
                $bot->startConversation($conversation);

                // Langsung panggil metode finish
                $conversation->askPilihan();
            }

        } 
        elseif($apiReply == 'input.z.maal')
        {
            $apiReply = $extras['apiReply'];
            if ($apiReply == 'spesifik') 
            {
                $bot->startConversation(new conversationZakat);
            }
            else
            {
                // Instansiasi conversationZakatMaal
                $conversation = new conversationZakatMaal();

                // Jalankan metode run jika perlu
                $bot->startConversation($conversation);

                // Langsung panggil metode finish
                $conversation->askPilihan();

            }
        } 
        elseif($apiReply == 'input.z.f.dalil')
        {
            $apiReply = $extras['apiReply'];
            if ($apiReply == 'spesifik') 
            {
                $bot->startConversation(new conversationDalil);
            }
            else
            {
                $bot->startConversation(new conversationZakatFitrahDalil);
            }
        }
        elseif($apiReply == 'input.z.m.dalil')
        {
            $apiReply = $extras['apiReply'];
            if ($apiReply == 'spesifik') 
            {
                $bot->startConversation(new conversationDalil);
            }
            else
            {
                $bot->startConversation(new conversationZakatMaalDalil);
            }
        } 
        elseif($apiReply == 'input.ask.zisedekah')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
            
        } 
        elseif($apiReply == 'input.ask.bayar')
        {
            $apiReply = $extras['apiReply'];
            $bot->reply($apiReply);
            
        } 
        else
        {
            if ($apiReply == 'input.unknown') 
            {
                $apiReply = $extras['apiReply'];
                $bot->reply($apiReply);
            } else 
            {
                $bot->reply('Terima kasih atas pertanyaan Anda mengenai zakat. Saat ini, saya belum memiliki informasi yang cukup untuk memberikan jawaban yang akurat terkait pertanyaan Anda. Anda tetap bisa mendapatkan jawaban terkait pertanyaan tersebut langsung dari Ustad Konsultasi Zakat dengan menghubungi kontak berikut: (+62) 852-6544-6800.');
            }

        }
    //
})->middleware($dialogflow);


$botman->listen();