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
        Button::create('Batalkan')->value('2'),
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
            $this->say('Terima kasih, Proses Dibatalkan.', ['parse_mode'=>'HTML']);

        }
    });
   }


   public function convertToNumeric($input) {
    // Mengganti titik dengan kosong agar "5.000" menjadi "5000"
    $input = str_replace('.', '', $input);

    // Menangani berbagai kombinasi angka dan kata-kata
    $wordsToNumbers = [
        'juta' => 1000000,
        'jutaan' => 1000000,
        'ribu' => 1000,
        'ribuan' => 1000,
        'miliar' => 1000000000,
        'milyar' => 1000000000,
        'miliaran' => 1000000000,
        'triliun' => 1000000000000,
        'trilyun' => 1000000000000,
    ];

    // Proses input untuk menggantikan kata-kata besar dengan angka
    foreach ($wordsToNumbers as $word => $value) {
        if (stripos($input, $word) !== false) {
            if (preg_match('/(\d+)\s*' . $word . '/i', $input, $matches)) {
                $input = str_ireplace($matches[0], $matches[1] * $value, $input);
            }
        }
    }

    // Menghapus semua karakter non-numeric kecuali titik desimal dan angka
    $input = preg_replace("/[^0-9.]/", "", $input);

    return (float) $input;
}

   public function askJumlahSapi()
{
    $this->ask('Berapa jumlah ekor sapi/kerbau yang Anda miliki?', function (Answer $answer) {
        $jumlahSapi = $this->convertToNumeric($answer->getText());
        $nisab = 30; 
        $zakat = 0;
        $jenisZakat = '';

        if ($jumlahSapi < $nisab) {
            $outputHitung = "### Kalkulator Zakat Peternakan ###\n\n";
            $outputHitung .= "Nisab untuk zakat peternakan dengan jenis ternak sapi/kerbau adalah 30 Ekor.\n\n";
            $outputHitung .= "Kadar zakat ini didasarkan pada ketentuan syariat Islam yang mengatur zakat ternak, di mana jumlah zakat tergantung pada jumlah ternak yang dimiliki. \n\n";
            $last = "\n\nAnda tetap bisa menyempurnakan niat baik dengan bersedekah";

            $this->say($outputHitung. "Anda tidak wajib membayar zakat karena jumlah sapi/kerbau Anda yaitu $jumlahSapi ekor kurang dari nisab untuk berzakat minimal $nisab ekor.". $last);
            // Instansiasi conversationZakatMaal
            $conversation = new conversationZakatMaalPeternakan();

            // Jalankan metode run jika perlu
            $this->bot->startConversation($conversation);

            // Langsung panggil metode finish
            $conversation->askConfirmHitung();
        } else {
            if ($jumlahSapi >= 30 && $jumlahSapi <= 39) {
                $zakat = 1;
                $jenisZakat = "satu ekor sapi/kerbau jantan/betina tabi (umur 1 tahun masuk ke 2 tahun)";
            } elseif ($jumlahSapi >= 40 && $jumlahSapi <= 59) {
                $zakat = 1;
                $jenisZakat = "satu ekor sapi/kerbau jantan/betina musinnah (umur 2 tahun masuk ke 3 tahun)";
            } elseif ($jumlahSapi >= 60 && $jumlahSapi <= 69) {
                $zakat = 2;
                $jenisZakat = "dua ekor sapi/kerbau jantan/betina tabi (umur 1 tahun masuk ke 2 tahun)";
            } elseif ($jumlahSapi >= 70 && $jumlahSapi <= 79) {
                $zakat = 1;
                $jenisZakat = "satu ekor sapi/kerbau jantan/betina musinnah (umur 2 tahun masuk ke 3 tahun) dan satu ekor sapi/kerbau jantan/betina tabi (umur 1 tahun masuk ke 2 tahun)";
            } elseif ($jumlahSapi >= 80 && $jumlahSapi <= 89) {
                $zakat = 2;
                $jenisZakat = "dua ekor sapi/kerbau jantan/betina musinnah (umur 2 tahun masuk ke 3 tahun)";
            } elseif ($jumlahSapi >= 90 && $jumlahSapi <= 99) {
                $zakat = 3;
                $jenisZakat = "tiga ekor sapi/kerbau jantan/betina tabi (umur 1 tahun masuk ke 2 tahun)";
            } elseif ($jumlahSapi >= 100 && $jumlahSapi <= 109) {
                $zakat = 2;
                $jenisZakat = "dua ekor sapi/kerbau jantan/betina musinnah (umur 2 tahun masuk ke 3 tahun)";
            } elseif ($jumlahSapi >= 110 && $jumlahSapi <= 119) {
                $zakat = 3;
                $jenisZakat = "dua ekor sapi/kerbau jantan/betina musinnah (umur 2 tahun masuk ke 3 tahun) dan satu ekor sapi/kerbau jantan/betina tabi (umur 1 tahun masuk ke 2 tahun)";
            } elseif ($jumlahSapi >= 120 && $jumlahSapi <= 129) {
                $zakat = 3;
                $jenisZakat = "tiga ekor sapi/kerbau jantan/betina musinnah (umur 2 tahun masuk ke 3 tahun) atau empat ekor sapi/kerbau jantan/betina tabi (umur 1 tahun masuk ke 2 tahun)";
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
                
                $jenisZakatTabi = $zakatTabi > 0 ? "$zakatTabi ekor sapi/kerbau jantan/betina tabi (umur 1 tahun masuk ke 2 tahun)" : '';
                $jenisZakatMusinnah = $zakatMusinnah > 0 ? "$zakatMusinnah ekor sapi/kerbau jantan/betina musinnah (umur 2 tahun masuk ke 3 tahun)" : '';
                
                if ($jenisZakatTabi && $jenisZakatMusinnah) {
                    $jenisZakat = "$jenisZakatTabi dan $jenisZakatMusinnah";
                } elseif ($jenisZakatTabi) {
                    $jenisZakat = $jenisZakatTabi;
                } elseif ($jenisZakatMusinnah) {
                    $jenisZakat = $jenisZakatMusinnah;
                }
            }

            $this->say("
### Kalkulator Zakat Peternakan ###

Nisab untuk zakat peternakan dengan jenis ternak sapi/kerbau adalah 30 Ekor.

Kadar zakat ini didasarkan pada ketentuan syariat Islam yang mengatur zakat ternak, di mana jumlah zakat tergantung pada jumlah ternak yang dimiliki. 

Berdasarkan ". number_format($jumlahSapi, 0, ',', '.') ." ekor sapi,

jumlah zakat yang harus Anda bayarkan adalah
= $zakat ekor ($jenisZakat).");

            // Instansiasi conversationZakatMaal
            $conversation = new conversationZakatMaalPeternakan();

            // Jalankan metode run jika perlu
            $this->bot->startConversation($conversation);

            // Langsung panggil metode finish
            $conversation->askConfirmHitung();
        }
    });
}

    public function askJumlahKambing()
{
    $this->ask('Berapa jumlah ekor kambing/biri-biri/domba yang Anda miliki?', function (Answer $message) {
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
            $outputHitung = "### Kalkulator Zakat Peternakan ###\n\n";
            $outputHitung .= "Nisab untuk zakat peternakan dengan jenis ternak  kambing/biri-biri/domba adalah 40 Ekor.\n\n";
            $outputHitung .= "Kadar zakat ini didasarkan pada ketentuan syariat Islam yang mengatur zakat ternak, di mana jumlah zakat tergantung pada jumlah ternak yang dimiliki.\n\n";
            $last = "\n\nAnda tetap bisa menyempurnakan niat baik dengan bersedekah";

            $this->say($outputHitung. "Anda tidak wajib membayar zakat karena jumlah kambing Anda yaitu $jumlahKambing kurang dari nisab untuk berzakat minimal 40 ekor.".$last);
            // Instansiasi conversationZakatMaal
            $conversation = new conversationZakatMaalPeternakan();

            // Jalankan metode run jika perlu
            $this->bot->startConversation($conversation);

            // Langsung panggil metode finish
            $conversation->askConfirmHitung();
        } else {
            $outputHitung = "### Kalkulator Zakat Peternakan ###\n\n";
            $outputHitung .= "Nisab untuk zakat peternakan dengan jenis ternak  kambing/biri-biri/domba adalah 40 Ekor.\n\n";
            $outputHitung .= "Kadar zakat ini didasarkan pada ketentuan syariat Islam yang mengatur zakat ternak, di mana jumlah zakat tergantung pada jumlah ternak yang dimiliki.\n\n";

            $this->say($outputHitung. "Berdasarkan $jumlahKambing ekor kambing, jumlah zakat yang harus Anda bayarkan adalah: $zakat ekor (umur 1 Tahun untuk kambing/biri-biri/domba).");
            // Instansiasi conversationZakatMaal
            $conversation = new conversationZakatMaalPeternakan();

            // Jalankan metode run jika perlu
            $this->bot->startConversation($conversation);

            // Langsung panggil metode finish
            $conversation->askConfirmHitung();
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