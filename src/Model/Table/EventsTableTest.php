<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\EventsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\EventsTable Test Case
 */
class EventsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\EventsTable
     */
    public $EventsTable;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
		'app.events',
		'app.rooms',
		'app.contacts',
		'app.prerequisites',
		'app.honorarias',
		'app.categories',
		'app.tools',
		'app.files',
		'app.registrations'
	];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Events') ? [] : ['className' => EventsTable::class];
        $this->EventsTable = TableRegistry::getTableLocator()->get('Events', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->EventsTable);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Run the table's default validator against a single field, in update
     * mode so the create-only requirePresence rules don't fire. This isolates
     * the no-emoji check from unrelated rules (event_start honorarium check,
     * datetime behaviors, room availability) that would otherwise need a
     * fully-populated payload and additional fixtures.
     */
    private function validateField(string $field, $value): array
    {
        $errors = $this->EventsTable->getValidator()->errors([$field => $value], false);
        return $errors[$field] ?? [];
    }

    /**
     * Production runs utf8mb3, so 4-byte UTF-8 characters (emoji) can't be
     * stored. The validator must reject them up-front so the user sees a
     * field-level error instead of losing their submission to a 500.
     */
    public function testValidationRejectsEmojiInName()
    {
        $errors = $this->validateField('name', 'Soldering 101 🔥');
        $this->assertArrayHasKey('noFourByteChars', $errors);
    }

    public function testValidationRejectsEmojiInShortDescription()
    {
        $errors = $this->validateField('short_description', 'Bring goggles 👀');
        $this->assertArrayHasKey('noFourByteChars', $errors);
    }

    public function testValidationRejectsEmojiInLongDescription()
    {
        $errors = $this->validateField('long_description', "Line 1\nLine 2 with rocket 🚀");
        $this->assertArrayHasKey('noFourByteChars', $errors);
    }

    public function testValidationRejectsEmojiInAdvisories()
    {
        $errors = $this->validateField('advisories', 'Hot surface ♨️🔥');
        $this->assertArrayHasKey('noFourByteChars', $errors);
    }

    /**
     * 3-byte UTF-8 (BMP) characters like accented Latin, CJK, and curly
     * quotes are fine — utf8mb3 can store them. Don't over-reject.
     */
    public function testValidationAcceptsThreeByteUtf8()
    {
        $this->assertArrayNotHasKey(
            'noFourByteChars',
            $this->validateField('name', 'Café — résumé workshop 日本語')
        );
        $this->assertArrayNotHasKey(
            'noFourByteChars',
            $this->validateField('short_description', 'Diacritics and CJK are fine')
        );
        $this->assertArrayNotHasKey(
            'noFourByteChars',
            $this->validateField('long_description', 'Em dash — and curly quotes "" are BMP')
        );
        $this->assertArrayNotHasKey(
            'noFourByteChars',
            $this->validateField('advisories', 'Nothing fancy')
        );
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test hasHonorarium method
     *
     * @return void
     */
    public function testHasHonorarium()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getTotalSpaces method
     *
     * @return void
     */
    public function testGetTotalSpaces()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getFilledSpaces method
     *
     * @return void
     */
    public function testGetFilledSpaces()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test hasFreeSpaces method
     *
     * @return void
     */
    public function testHasFreeSpaces()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test hasPaidSpaces method
     *
     * @return void
     */
    public function testHasPaidSpaces()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test hasOpenSpaces method
     *
     * @return void
     */
    public function testHasOpenSpaces()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test isOwnedBy method
     *
     * @return void
     */
    public function testIsOwnedBy()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
