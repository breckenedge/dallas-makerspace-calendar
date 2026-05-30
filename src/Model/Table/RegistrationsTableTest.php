<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RegistrationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RegistrationsTable Test Case
 */
class RegistrationsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\RegistrationsTable
     */
    public $RegistrationsTable;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
		'app.registrations',
		'app.events'
	];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Registrations') ? [] : ['className' => RegistrationsTable::class];
        $this->RegistrationsTable = TableRegistry::getTableLocator()->get('Registrations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->RegistrationsTable);

        parent::tearDown();
    }

    /**
     * Build a registration record with sensible defaults.
     */
    private function newRegistration(array $overrides = [])
    {
        $defaults = [
            'event_id' => 1,
            'type' => 'free',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'ad_username' => 'tuser',
            'send_text' => false,
            'edit_key' => 'abc123',
            'status' => 'confirmed',
        ];

        return $this->RegistrationsTable->newEntity($overrides + $defaults);
    }

    public function testInitialize()
    {
        $this->assertTrue($this->RegistrationsTable->hasAssociation('Events'));
    }

    /**
     * The validator rejects a second active registration that reuses an email
     * address already in use for the same event.
     */
    public function testValidationRejectsDuplicateEmailWhenActive()
    {
        $first = $this->newRegistration(['status' => 'confirmed']);
        $this->assertNotFalse($this->RegistrationsTable->save($first));

        $second = $this->newRegistration([
            'ad_username' => 'someoneelse',
            'edit_key' => 'def456',
        ]);

        $this->assertFalse($this->RegistrationsTable->save($second));
        $this->assertArrayHasKey('email', $second->getErrors());
    }

    /**
     * The validator rejects a second active registration that reuses an
     * ad_username already in use for the same event.
     */
    public function testValidationRejectsDuplicateAdUsernameWhenActive()
    {
        $first = $this->newRegistration(['status' => 'confirmed']);
        $this->assertNotFalse($this->RegistrationsTable->save($first));

        $second = $this->newRegistration([
            'email' => 'different@example.com',
            'edit_key' => 'def456',
        ]);

        $this->assertFalse($this->RegistrationsTable->save($second));
        $this->assertArrayHasKey('ad_username', $second->getErrors());
    }

    /**
     * When the prior registration is cancelled, a user is permitted to
     * register again with the same email and ad_username.
     */
    public function testValidationAllowsReregisterAfterCancel()
    {
        $first = $this->newRegistration(['status' => 'cancelled']);
        $this->assertNotFalse($this->RegistrationsTable->save($first));

        $second = $this->newRegistration([
            'status' => 'confirmed',
            'edit_key' => 'def456',
        ]);

        $saved = $this->RegistrationsTable->save($second);
        $this->assertNotFalse($saved, print_r($second->getErrors(), true));
        $this->assertSame([], $second->getErrors());
    }

    /**
     * A registration that was explicitly rejected by an organizer should
     * still block the same user from registering again — otherwise they
     * could just resubmit until they got in. The controller only sets
     * 'rejected' on an update path, so we mirror that by seeding the
     * row with validation disabled.
     */
    public function testValidationStillBlocksWhenPreviousWasRejected()
    {
        $first = $this->RegistrationsTable->newEntity([
            'event_id' => 1,
            'type' => 'free',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'ad_username' => 'tuser',
            'send_text' => false,
            'edit_key' => 'abc123',
            'status' => 'rejected',
        ], ['validate' => false]);
        $this->assertNotFalse($this->RegistrationsTable->save($first, ['validate' => false]));

        $second = $this->newRegistration([
            'status' => 'confirmed',
            'edit_key' => 'def456',
        ]);

        $this->assertFalse($this->RegistrationsTable->save($second));
        $this->assertArrayHasKey('email', $second->getErrors());
    }

    /**
     * Editing an existing registration shouldn't make the unique check
     * collide with itself.
     */
    public function testValidationAllowsEditingExistingRegistration()
    {
        $entity = $this->newRegistration();
        $saved = $this->RegistrationsTable->save($entity);
        $this->assertNotFalse($saved);

        $saved->name = 'Renamed User';
        $this->assertNotFalse($this->RegistrationsTable->save($saved));
    }

    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testRefund()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testIsOwnedBy()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
