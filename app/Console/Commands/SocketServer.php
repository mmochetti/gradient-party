<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\SocketHandle;

class SocketServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'socket:start {port}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts the socket server for the application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $port = intval($this->argument('port'));
        $this->info("Starting chat web socket server on port " . $port);

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new SocketHandle()
                )
            ),
            $port,
            '0.0.0.0'
        );

        $server->run();
    }
}
