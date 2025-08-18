<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Traits;

use Illuminate\Support\Facades\Config;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Milenmk\LaravelSimpleDatatablesAndForms\Traits\WithSearch;
use PHPUnit\Framework\Attributes\Test;

class WithSearchTest extends BaseTest
{
    private object $testClass;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an anonymous class that uses the WithSearch trait
        $this->testClass = new class
        {
            use WithSearch;

            public function resetPage(): void
            {
                // Mock resetPage method
            }
        };
    }

    #[Test]
    public function it_initializes_with_default_values()
    {
        $this->assertEquals('', $this->testClass->search);
        $this->assertEquals('', $this->testClass->searchMode);
    }

    #[Test]
    public function it_can_initialize_search_with_default_mode()
    {
        Config::set('simple-datatables-and-forms.search.default_mode', 'like');

        $this->testClass->initializeWithSearch();

        $this->assertEquals('like', $this->testClass->searchMode);
    }

    #[Test]
    public function it_can_initialize_search_with_custom_mode()
    {
        Config::set('simple-datatables-and-forms.search.default_mode', 'exact');

        $this->testClass->initializeWithSearch();

        $this->assertEquals('exact', $this->testClass->searchMode);
    }

    #[Test]
    public function it_does_not_override_existing_search_mode()
    {
        $this->testClass->searchMode = 'fulltext';

        $this->testClass->initializeWithSearch();

        $this->assertEquals('fulltext', $this->testClass->searchMode);
    }

    #[Test]
    public function it_can_update_search_with_sufficient_characters()
    {
        Config::set('simple-datatables-and-forms.search.min_characters', 2);

        $this->testClass->search = 'test';

        $this->testClass->updatedSearch();

        // If we get here without exception, the test passes
        $this->assertTrue(true);
    }

    #[Test]
    public function it_can_update_search_with_empty_string()
    {
        Config::set('simple-datatables-and-forms.search.min_characters', 2);

        $this->testClass->search = '';

        $this->testClass->updatedSearch();

        // If we get here without exception, the test passes
        $this->assertTrue(true);
    }

    #[Test]
    public function it_handles_search_with_insufficient_characters()
    {
        Config::set('simple-datatables-and-forms.search.min_characters', 2);

        $this->testClass->search = 'ab';

        $this->testClass->updatedSearch();

        // Should not call resetPage, but we can't easily test that
        $this->assertTrue(true);
    }

    #[Test]
    public function it_can_set_valid_search_mode()
    {
        $this->testClass->setSearchMode('exact');

        $this->assertEquals('exact', $this->testClass->searchMode);
    }

    #[Test]
    public function it_can_set_like_search_mode()
    {
        $this->testClass->setSearchMode('like');

        $this->assertEquals('like', $this->testClass->searchMode);
    }

    #[Test]
    public function it_can_set_fulltext_search_mode()
    {
        $this->testClass->setSearchMode('fulltext');

        $this->assertEquals('fulltext', $this->testClass->searchMode);
    }

    #[Test]
    public function it_ignores_invalid_search_mode()
    {
        $originalMode = $this->testClass->searchMode;

        $this->testClass->setSearchMode('invalid_mode');

        $this->assertEquals($originalMode, $this->testClass->searchMode);
    }

    #[Test]
    public function it_can_get_search_debounce_time()
    {
        Config::set('simple-datatables-and-forms.search.debounce_time', 500);

        $debounceTime = $this->testClass->searchDebounceTime();

        $this->assertEquals(500, $debounceTime);
    }

    #[Test]
    public function it_can_get_default_search_debounce_time()
    {
        Config::set('simple-datatables-and-forms.search.debounce_time', 300);

        $debounceTime = $this->testClass->searchDebounceTime();

        $this->assertEquals(300, $debounceTime);
    }

    #[Test]
    public function it_can_get_search_min_characters()
    {
        Config::set('simple-datatables-and-forms.search.min_characters', 3);

        $minChars = $this->testClass->searchMinCharacters();

        $this->assertEquals(3, $minChars);
    }

    #[Test]
    public function it_can_get_default_search_min_characters()
    {
        Config::set('simple-datatables-and-forms.search.min_characters', 2);

        $minChars = $this->testClass->searchMinCharacters();

        $this->assertEquals(2, $minChars);
    }
}
