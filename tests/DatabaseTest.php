<?php


use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/utils/database.php';

define('Test_DATABASE_FILE', __DIR__ . '/test_database.json');

class DatabaseTest extends TestCase

{

    private $testDatabaseFile; 

    protected function setUp(): void
    {
        $this->testDatabaseFile = Test_DATABASE_FILE;

        if (file_exists($this->testDatabaseFile)) {
            unlink($this->testDatabaseFile);
        }

        // Ensure the file is created and empty for each test
        file_put_contents($this->testDatabaseFile, json_encode([]));
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testDatabaseFile)) {
            unlink($this->testDatabaseFile);
        }
    }

    
    public function testLoadDatabase()
    {
        // Test loading an empty database file
        $data = loadDatabase($this->testDatabaseFile);
        echo "Load Database checking " . "\n";
        $this->assertIsArray($data);
        $this->assertEmpty($data);
        
        
    }


    public function testSaveUserInfo()

    {
        echo "Save data checking " . "\n";
        // Test saving user information
        $userInfo = ['name' => 'John', 'email' => 'john@example.com', 'tickets' => 2, 'id' => '123'];
        saveUserInfo($userInfo, $this->testDatabaseFile);

        // Load the saved data and check if the user information is present
        $loadedData = loadDatabase($this->testDatabaseFile);
        $this->assertCount(count($loadedData), $loadedData);
        $this->assertContains($userInfo, $loadedData);
    }
}

?>
