<?php

use PHPUnit\Framework\TestCase;

class MainTest extends TestCase
{
    protected function setUp(): void
    {
        // Load the configuration
        $config = json_decode(file_get_contents(__DIR__ . '/../src/config.json'), true);

        // Define constants if they are not already defined
        if (!defined('BOT_TOKEN')) {
            define('BOT_TOKEN', $config['bot_token']);
        }
        if (!defined('BOT_USERNAME')) {
            define('BOT_USERNAME', $config['bot_username']);
        }
        if (!defined('API_URL')) {
            define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');
        }
        if (!defined('WEBHOOK_URL')) {
            define('WEBHOOK_URL', $config['webhook_url']);
        }
    }

    public function testSetWebhook()
    {
        $url = API_URL . 'setWebhook?url=' . WEBHOOK_URL;
        $response = file_get_contents($url);
        
        // Print the response to the console
        echo "Set Webhook Response: \n";
        print_r($response);

        $this->assertNotFalse($response, "Webhook response should not be false");

        $responseData = json_decode($response, true);
        $this->assertArrayHasKey('ok', $responseData, "Response should have 'ok' key");
        $this->assertTrue($responseData['ok'], "Webhook should be set successfully");
    }

    public function testGetWebhookInfo()
    {
        $url = API_URL . 'getWebhookInfo';
        $response = file_get_contents($url);
        
        // Print the response to the console
        echo "\n";
        echo "Get Webhook Info Response: \n";
        print_r($response);

        $this->assertNotFalse($response, "Webhook info response should not be false");

        $responseData = json_decode($response, true);
        $this->assertArrayHasKey('ok', $responseData, "Response should have 'ok' key");
        $this->assertTrue($responseData['ok'], "Should retrieve webhook info successfully");
    }
}
