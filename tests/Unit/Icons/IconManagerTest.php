<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Icons;

use Illuminate\Contracts\Support\Htmlable;
use Milenmk\LaravelSimpleDatatablesAndForms\Icons\IconManager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class IconManagerTest extends TestCase
{
    private IconManager $iconManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->iconManager = new IconManager;
    }

    #[Test]
    public function register_icons_as_strings(): void
    {
        $icons = [
            'edit' => '<svg>edit icon</svg>',
            'delete' => '<svg>delete icon</svg>',
        ];

        $this->iconManager->register($icons);

        $this->assertEquals('<svg>edit icon</svg>', $this->iconManager->resolve('edit'));
        $this->assertEquals('<svg>delete icon</svg>', $this->iconManager->resolve('delete'));
    }

    #[Test]
    public function register_icons_as_htmlable(): void
    {
        $htmlableIcon = new class implements Htmlable
        {
            public function to_html(): string
            {
                return '<svg>htmlable icon</svg>';
            }
        };

        $icons = [
            'custom' => $htmlableIcon,
        ];

        $this->iconManager->register($icons);

        $this->assertSame($htmlableIcon, $this->iconManager->resolve('custom'));
    }

    #[Test]
    public function register_mixed_icon_types(): void
    {
        $htmlableIcon = new class implements Htmlable
        {
            public function to_html(): string
            {
                return '<svg>htmlable icon</svg>';
            }
        };

        $icons = [
            'string_icon' => '<svg>string icon</svg>',
            'htmlable_icon' => $htmlableIcon,
        ];

        $this->iconManager->register($icons);

        $this->assertEquals('<svg>string icon</svg>', $this->iconManager->resolve('string_icon'));
        $this->assertSame($htmlableIcon, $this->iconManager->resolve('htmlable_icon'));
    }

    #[Test]
    public function register_multiple_times_merges_icons(): void
    {
        $firstBatch = [
            'edit' => '<svg>edit icon</svg>',
            'delete' => '<svg>delete icon</svg>',
        ];

        $secondBatch = [
            'create' => '<svg>create icon</svg>',
            'view' => '<svg>view icon</svg>',
        ];

        $this->iconManager->register($firstBatch);
        $this->iconManager->register($secondBatch);

        $this->assertEquals('<svg>edit icon</svg>', $this->iconManager->resolve('edit'));
        $this->assertEquals('<svg>delete icon</svg>', $this->iconManager->resolve('delete'));
        $this->assertEquals('<svg>create icon</svg>', $this->iconManager->resolve('create'));
        $this->assertEquals('<svg>view icon</svg>', $this->iconManager->resolve('view'));
    }

    #[Test]
    public function register_overwrites_existing_icons(): void
    {
        $firstBatch = [
            'edit' => '<svg>old edit icon</svg>',
        ];

        $secondBatch = [
            'edit' => '<svg>new edit icon</svg>',
        ];

        $this->iconManager->register($firstBatch);
        $this->iconManager->register($secondBatch);

        $this->assertEquals('<svg>new edit icon</svg>', $this->iconManager->resolve('edit'));
    }

    #[Test]
    public function resolve_with_string_alias(): void
    {
        $icons = [
            'edit' => '<svg>edit icon</svg>',
        ];

        $this->iconManager->register($icons);

        $this->assertEquals('<svg>edit icon</svg>', $this->iconManager->resolve('edit'));
    }

    #[Test]
    public function resolve_with_array_alias_returns_first_match(): void
    {
        $icons = [
            'edit' => '<svg>edit icon</svg>',
            'pencil' => '<svg>pencil icon</svg>',
        ];

        $this->iconManager->register($icons);

        // Should return the first match
        $this->assertEquals('<svg>edit icon</svg>', $this->iconManager->resolve(['edit', 'pencil']));
        $this->assertEquals('<svg>pencil icon</svg>', $this->iconManager->resolve(['missing', 'pencil']));
    }

    #[Test]
    public function resolve_with_array_alias_returns_null_when_no_match(): void
    {
        $icons = [
            'edit' => '<svg>edit icon</svg>',
        ];

        $this->iconManager->register($icons);

        $this->assertNull($this->iconManager->resolve(['missing1', 'missing2']));
    }

    #[Test]
    public function resolve_returns_null_for_non_existent_icon(): void
    {
        $icons = [
            'edit' => '<svg>edit icon</svg>',
        ];

        $this->iconManager->register($icons);

        $this->assertNull($this->iconManager->resolve('non_existent'));
    }

    #[Test]
    public function resolve_returns_null_for_empty_icons(): void
    {
        $this->assertNull($this->iconManager->resolve('any_icon'));
    }

    #[Test]
    public function register_empty_array(): void
    {
        $this->iconManager->register([]);

        $this->assertNull($this->iconManager->resolve('any_icon'));
    }
}
