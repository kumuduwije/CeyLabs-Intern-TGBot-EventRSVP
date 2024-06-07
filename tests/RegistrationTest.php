<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

require_once __DIR__ . '/../src/utils/registration.php';
require_once __DIR__ . '/../src/utils/database.php';
require_once __DIR__ . '/../src/utils/group_invitation.php';


class RegistrationTest extends TestCase
{
    

    protected function setUp(): void
    {
        // Create a mock for ApiClient
      

        // Set up any required state or mock objects here
        // For instance, create a temporary registration steps file
        if (file_exists('registration_steps.json')) {
            unlink('registration_steps.json');
        }
    }

    protected function tearDown(): void
    {
        // Clean up any resources here
        if (file_exists('registration_steps.json')) {
            unlink('registration_steps.json');
        }
    }

    public function testLoadRegistrationSteps()
    {
        $steps = loadRegistrationSteps();
        $this->assertIsArray($steps);
        $this->assertEmpty($steps);
    }

    public function testSaveRegistrationSteps()
    {
        $steps = ['12345' => ['step' => 1, 'name' => 'John']];
        saveRegistrationSteps($steps);

        $loadedSteps = loadRegistrationSteps();
        $this->assertEquals($steps, $loadedSteps);
    }

    public function testCheckUserState()
    {
        $chat_id = '12345';
        $steps = [$chat_id => ['step' => 1, 'name' => 'John']];
        saveRegistrationSteps($steps);

        $this->assertTrue(checkUserState($chat_id));
        $this->assertFalse(checkUserState('54321'));
    }

    public function testIsValidEmail()
    {
        echo "\n";
        echo "Email Validation checking";
        echo "\n";
        $this->assertTrue(isValidEmail('test@example.com'));
        $this->assertFalse(isValidEmail('invalid-email'));
    }
    
    public function testIsValidName()
    {
        echo "Name Validation checking";
        echo "\n";
        // Valid names
        $this->assertEquals(1, isValidName('John Doe'));
        $this->assertEquals(1, isValidName('Alice Smith'));
    
        // Invalid names containing digits, special characters, or symbols
        $this->assertEquals(0, isValidName('John123'));
        $this->assertEquals(0, isValidName('Mary-Jane'));
        $this->assertEquals(0, isValidName('John&Doe'));
    }

    public function testIsTicketValid(){
        echo "Ticket Validation checking";
        echo "\n";
        //Valid number of tickets
        $validTicket = '5';
        $this->assertTrue(is_numeric($validTicket));

        //Invalid number of tickets
        $invalidTicket = '1 million tickets';
        $this->assertFalse(is_numeric($invalidTicket));
    }
    
}
