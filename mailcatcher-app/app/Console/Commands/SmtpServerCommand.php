<?php

namespace App\Console\Commands;

use App\Models\Email;
use Illuminate\Console\Command;
use PhpMimeMailParser\Parser;

class SmtpServerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:catch {--port=1025} {--host=0.0.0.0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a local SMTP server to catch emails.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $host = $this->option('host');
        $port = $this->option('port');

        $this->info("Starting SMTP server on {$host}:{$port}");

        $socket = stream_socket_server("tcp://{$host}:{$port}", $errno, $errstr);

        if (!$socket) {
            $this->error("Could not create socket: {$errstr} ({$errno})");
            return 1;
        }

        while ($conn = stream_socket_accept($socket, -1)) {
            $this->handleConnection($conn);
        }
    }

    protected function handleConnection($conn)
    {
        fputs($conn, "220 Welcome to Laravel Mail Catcher\r\n");

        $emailData = '';
        $inData = false;

        while ($line = fgets($conn)) {
            if ($inData) {
                if (rtrim($line) === '.') {
                    $this->saveEmail($emailData);
                    $inData = false;
                    fputs($conn, "250 OK: message accepted for delivery\r\n");
                } else {
                    $emailData .= $line;
                }
            } else {
                $command = strtoupper(substr($line, 0, 4));
                switch ($command) {
                    case 'HELO':
                    case 'EHLO':
                        fputs($conn, "250 Hello\r\n");
                        break;
                    case 'MAIL':
                        fputs($conn, "250 OK\r\n");
                        break;
                    case 'RCPT':
                        fputs($conn, "250 OK\r\n");
                        break;
                    case 'DATA':
                        fputs($conn, "354 End data with <CR><LF>.<CR><LF>\r\n");
                        $inData = true;
                        break;
                    case 'QUIT':
                        fputs($conn, "221 Bye\r\n");
                        fclose($conn);
                        return;
                    default:
                        fputs($conn, "500 Command not recognized\r\n");
                        break;
                }
            }
        }
        fclose($conn);
    }

    protected function saveEmail($rawEmail)
    {
        $parser = new Parser();
        $parser->setText($rawEmail);

        $from = $this->formatAddresses($parser->getAddresses('from'));
        $to = $this->formatAddresses($parser->getAddresses('to'));
        $cc = $this->formatAddresses($parser->getAddresses('cc'));
        $bcc = $this->formatAddresses($parser->getAddresses('bcc'));

        Email::create([
            'from' => $from,
            'to' => $to,
            'cc' => $cc ?: null,
            'bcc' => $bcc ?: null,
            'subject' => $parser->getHeader('subject'),
            'html_body' => $parser->getMessageBody('html'),
            'text_body' => $parser->getMessageBody('text'),
            'raw_source' => $rawEmail,
            'attachments' => null, // Attachments handling can be added here
        ]);

        $this->info('Email caught and saved.');
    }

    protected function formatAddresses(array $addresses): array
    {
        $formatted = [];
        foreach ($addresses as $address) {
            $formatted[] = [
                'address' => $address['address'],
                'name' => $address['display'] ?: null,
            ];
        }
        return $formatted;
    }
}