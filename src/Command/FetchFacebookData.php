<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(name: 'app:fetch-facebook-data')]
class FetchFacebookData extends Command
{
    private const ACCESS_TOKEN = "EAAGmslAV690BOxj8hPXz51QAZCIywokmHJQ4XIdI5kWf9sHJkh6BQgNs440KhRA7Hko2Fa3oaI5uf3AdaQTZCj7PIBjdu974LY2Vkg4KSN8W2ZAgGZBZBmgXaEthZC265G31z6R2WgFz79hebkJpK7nHJF9fFlBr08Cv1JexIfMHGZBxE3oZATLMSxQGGZBHjryM85rLIBwlbQtqddIpUrKkXJZCUZD";
    private const DATA_QUERY = "posts?fields=message,properties,permalink_url,created_time,full_picture,reactions&limit=5";
    private const APP_ID = "107881899257304";

    public function __construct(
        private readonly HttpClientInterface $client
    ) {
        parent::__construct();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fetchUrl = sprintf("https://graph.facebook.com/v19.0/%s/%s&access_token=%s",
            self::APP_ID,
            self::DATA_QUERY,
            self::ACCESS_TOKEN
        );

        $data = $this->fetchData($fetchUrl);
        if(!empty($data))
        {
            $this->saveData($data);
            return self::SUCCESS;
        }

        return self::FAILURE;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function fetchData(string $fetchUrl): array
    {
        $response = $this->client->request(
            'GET',
            $fetchUrl
        );

        if($response->getStatusCode() === 200) {
            return json_decode($response->getContent(), true)['data'];
        }

        return [];
    }

    private function saveData(array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents("data/posts.json", $json);
    }
}