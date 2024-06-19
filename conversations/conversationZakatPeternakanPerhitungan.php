<?php

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Conversations\Conversation;
require_once __DIR__."/conversationZakatMaalPeternakan.php";

class conversationZakatPeternakanPerhitungan extends Conversation
{
    
   public function KalkulatorZPeternakan(){
    $question = Question::create('Jenis Ternak?')
    ->addButtons([
        Button::create('Sapi/Kerbau')->value('0'),
        Button::create('Kambing/Biri-biri/Domba')->value('1'),
        Button::create('Selesai')->value('2'),
    ]);

    $this->ask($question, function (Answer $answer) {
        if ($answer->getValue() === '0') {
            $this->askJumlahSapi();
            
        }
        elseif ($answer->getValue() === '1') {
            $this->askJumlahKambing();
        }
        else
        {
            $this->finish();
        }
    });
   }


   public function convertToNumeric($input)
{
    // Menghapus karakter non-numeric
    $input = preg_replace("/[^0-9]/", "", $input);

    // Mengonversi kata menjadi angka
    $words = ['juta', 'ribu', 'jutaan', 'ribuan', 'miliar', 'milyar', 'miliaran', 'triliun', 'trilyun'];
    $replacements = ['1000000', '1000', '1000000', '1000', '1000000000', '1000000000', '1000000000', '1000000000000', '1000000000000'];

    $input = str_ireplace($words, $replacements, $input);

    return (float) $input;
}

   public function askJumlahSapi()
{
    $this->ask('Berapa jumlah sapi/kerbau yang Anda miliki?', function (Answer $answer) {
        $jumlahSapi = $this->convertToNumeric($answer->getText());
        $nisab = 30; 
        $zakat = 0;
        $jenisZakat = '';

        if ($jumlahSapi < $nisab) {
            $this->say("Anda tidak wajib membayar zakat karena jumlah sapi Anda kurang dari $nisab ekor.");
            // Instansiasi conversationZakatMaal
            $conversation = new conversationZakatMaalPeternakan();

            // Jalankan metode run jika perlu
            $this->bot->startConversation($conversation);

            // Langsung panggil metode finish
            $conversation->askConfirm();
        } else {
            if ($jumlahSapi >= 30 && $jumlahSapi <= 39) {
                $zakat = 1;
                $jenisZakat = "satu ekor sapi jantan/betina tabi (umur 1 tahun masuk ke 2 tahun)";
            } elseif ($jumlahSapi >= 40 && $jumlahSapi <= 59) {
                $zakat = 1;
                $jenisZakat = "satu ekor sapi jantan/betina musinnah (umur 2 tahun masuk ke 3 tahun)";
            } elseif ($jumlahSapi >= 60 && $jumlahSapi <= 69) {
                $zakat = 2;
                $jenisZakat = "dua ekor sapi jantan/betina tabi (umur 1 tahun masuk ke 2 tahun)";
            } elseif ($jumlahSapi >= 70 && $jumlahSapi <= 79) {
                $zakat = 1;
                $jenisZakat = "satu ekor sapi jantan/betina musinnah (umur 2 tahun masuk ke 3 tahun) dan satu ekor sapi jantan/betina tabi (umur 1 tahun masuk ke 2 tahun)";
            } elseif ($jumlahSapi >= 80 && $jumlahSapi <= 89) {
                $zakat = 2;
                $jenisZakat = "dua ekor sapi jantan/betina musinnah (umur 2 tahun masuk ke 3 tahun)";
            } elseif ($jumlahSapi >= 90 && $jumlahSapi <= 99) {
                $zakat = 3;
                $jenisZakat = "tiga ekor sapi jantan/betina tabi (umur 1 tahun masuk ke 2 tahun)";
            } elseif ($jumlahSapi >= 100 && $jumlahSapi <= 109) {
                $zakat = 2;
                $jenisZakat = "dua ekor sapi jantan/betina musinnah (umur 2 tahun masuk ke 3 tahun)";
            } elseif ($jumlahSapi >= 110 && $jumlahSapi <= 119) {
                $zakat = 3;
                $jenisZakat = "dua ekor sapi jantan/betina musinnah (umur 2 tahun masuk ke 3 tahun) dan satu ekor sapi jantan/betina tabi (umur 1 tahun masuk ke 2 tahun)";
            } elseif ($jumlahSapi >= 120 && $jumlahSapi <= 129) {
                $zakat = 3;
                $jenisZakat = "tiga ekor sapi jantan/betina musinnah (umur 2 tahun masuk ke 3 tahun) atau empat ekor sapi jantan/betina tabi (umur 1 tahun masuk ke 2 tahun)";
            } else {
                // Untuk jumlah sapi 130 ke atas
                $zakat = 3; // Zakat dasar untuk 120 ekor sapi
                $sisaSapi = $jumlahSapi - 120;
                $jumlahTabi = (int)($sisaSapi / 30); // Setiap 30 ekor tambahan, zakat bertambah 1 ekor tabi'
                $jumlahMusinnah = (int)($sisaSapi / 40); // Setiap 40 ekor tambahan, zakat bertambah 1 ekor musinnah
                
                // Menentukan jumlah zakat tambahan
                $zakatTabi = $jumlahTabi;
                $zakatMusinnah = $jumlahMusinnah;
                $zakat += $zakatTabi + $zakatMusinnah;
                
                $jenisZakatTabi = $zakatTabi > 0 ? "$zakatTabi ekor sapi jantan/betina tabi (umur 1 tahun masuk ke 2 tahun)" : '';
                $jenisZakatMusinnah = $zakatMusinnah > 0 ? "$zakatMusinnah ekor sapi jantan/betina musinnah (umur 2 tahun masuk ke 3 tahun)" : '';
                
                if ($jenisZakatTabi && $jenisZakatMusinnah) {
                    $jenisZakat = "$jenisZakatTabi dan $jenisZakatMusinnah";
                } elseif ($jenisZakatTabi) {
                    $jenisZakat = $jenisZakatTabi;
                } elseif ($jenisZakatMusinnah) {
                    $jenisZakat = $jenisZakatMusinnah;
                }
            }

            $this->say("Berdasarkan $jumlahSapi ekor sapi, jumlah zakat yang harus Anda bayarkan adalah: $zakat ekor ($jenisZakat).");

            // Instansiasi conversationZakatMaal
            $conversation = new conversationZakatMaalPeternakan();

            // Jalankan metode run jika perlu
            $this->bot->startConversation($conversation);

            // Langsung panggil metode finish
            $conversation->askConfirm();
        }
    });
}

    public function askJumlahKambing()
{
    $this->ask('Berapa jumlah kambing/biri-biri/domba yang Anda miliki?', function (Answer $message) {
        $jumlahKambing = $this->convertToNumeric($message->getText());
        $zakat = 0;

        if ($jumlahKambing >= 40 && $jumlahKambing <= 120) {
            $zakat = 1;
        } elseif ($jumlahKambing >= 121 && $jumlahKambing <= 200) {
            $zakat = 2;
        } elseif ($jumlahKambing >= 201 && $jumlahKambing <= 300) {
            $zakat = 3;
        } elseif ($jumlahKambing >= 301 && $jumlahKambing <= 400) {
            $zakat = 4;
        } elseif ($jumlahKambing >= 401 && $jumlahKambing <= 500) {
            $zakat = 5;
        } elseif ($jumlahKambing > 500) {
            $zakat = 5 + floor(($jumlahKambing - 500) / 100);
        }

        if ($jumlahKambing < 40) {
            $this->say("Anda tidak wajib membayar zakat karena jumlah kambing Anda kurang dari 40 ekor.");
            // Instansiasi conversationZakatMaal
            $conversation = new conversationZakatMaalPeternakan();

            // Jalankan metode run jika perlu
            $this->bot->startConversation($conversation);

            // Langsung panggil metode finish
            $conversation->askConfirm();
        } else {
            $this->say("Berdasarkan $jumlahKambing ekor kambing, jumlah zakat yang harus Anda bayarkan adalah: $zakat ekor (umur 1 Tahun untuk kambing/biri-biri/domba).");
            // Instansiasi conversationZakatMaal
            $conversation = new conversationZakatMaalPeternakan();

            // Jalankan metode run jika perlu
            $this->bot->startConversation($conversation);

            // Langsung panggil metode finish
            $conversation->askConfirm();
        }
    });
}

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->KalkulatorZPeternakan();
    }
}
?>