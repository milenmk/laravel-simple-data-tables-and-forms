<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Form\Sections;

use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\InputField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Sections\Section;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;

class SectionTest extends BaseTest
{
    /**
     * @test
     */
    public function section_creation(): void
    {
        $section = new Section('personal_info');

        $this->assertEquals('personal_info', $section->name);
        $this->assertNull($section->label);
        $this->assertNull($section->description);
        $this->assertEquals([], $section->fields);
        $this->assertEquals([], $section->schema);
        $this->assertEquals(2, $section->columns);
        $this->assertFalse($section->collapsible);
        $this->assertFalse($section->collapsed);
        $this->assertFalse($section->persistCollapsed);
        $this->assertNull($section->icon);
        $this->assertEquals([], $section->headerActions);
        $this->assertFalse($section->compact);
        $this->assertFalse($section->aside);
        $this->assertEquals('auto', $section->columnSpan);
        $this->assertTrue($section->visible);
        $this->assertNull($section->view);
        $this->assertNull($section->content);
        $this->assertEquals([], $section->extraAttributes);
        $this->assertNull($section->extraAttributesCallback);
    }

    /**
     * @test
     */
    public function section_make_static_method(): void
    {
        $section = Section::make('contact_info');

        $this->assertEquals('contact_info', $section->name);
    }

    /**
     * @test
     */
    public function section_label_configuration(): void
    {
        $section = new Section('personal_info');
        $result = $section->label('Personal Information');

        $this->assertEquals('Personal Information', $section->label);
        $this->assertSame($section, $result); // Test fluent interface
    }

    /**
     * @test
     */
    public function section_description_configuration(): void
    {
        $section = new Section('personal_info');
        $result = $section->description('Enter your personal details');

        $this->assertEquals('Enter your personal details', $section->description);
        $this->assertSame($section, $result);
    }

    /**
     * @test
     */
    public function section_fields_configuration(): void
    {
        $section = new Section('personal_info');
        $fields = [new InputField('name'), new InputField('email')];

        $result = $section->fields($fields);

        $this->assertEquals($fields, $section->fields);
        $this->assertEquals($fields, $section->schema); // Backward compatibility
        $this->assertSame($section, $result);
    }

    /**
     * @test
     */
    public function section_schema_configuration(): void
    {
        $section = new Section('personal_info');
        $schema = [new InputField('name'), new InputField('email')];

        $result = $section->schema($schema);

        $this->assertEquals($schema, $section->schema);
        $this->assertEquals($schema, $section->fields); // Backward compatibility
        $this->assertSame($section, $result);
    }

    /**
     * @test
     */
    public function section_columns_configuration(): void
    {
        $section = new Section('personal_info');
        $result = $section->columns(3);

        $this->assertEquals(3, $section->columns);
        $this->assertSame($section, $result);
    }

    /**
     * @test
     */
    public function section_collapsible_configuration(): void
    {
        $section = new Section('personal_info');

        // Test enabling collapsible
        $result = $section->collapsible();
        $this->assertTrue($section->collapsible);
        $this->assertSame($section, $result);

        // Test disabling collapsible
        $section->collapsible(false);
        $this->assertFalse($section->collapsible);

        // Test default parameter (true)
        $section->collapsible();
        $this->assertTrue($section->collapsible);
    }

    /**
     * @test
     */
    public function section_collapsed_configuration(): void
    {
        $section = new Section('personal_info');

        // Test enabling collapsed
        $result = $section->collapsed();
        $this->assertTrue($section->collapsed);
        $this->assertSame($section, $result);

        // Test disabling collapsed
        $section->collapsed(false);
        $this->assertFalse($section->collapsed);

        // Test default parameter (true)
        $section->collapsed();
        $this->assertTrue($section->collapsed);
    }

    /**
     * @test
     */
    public function section_icon_configuration(): void
    {
        $section = new Section('personal_info');
        $result = $section->icon('heroicon-o-user');

        $this->assertEquals('heroicon-o-user', $section->icon);
        $this->assertSame($section, $result);
    }

    /**
     * @test
     */
    public function section_fluent_interface(): void
    {
        $fields = [new InputField('name'), new InputField('email')];

        $section = Section::make('contact_info')
            ->label('Contact Information')
            ->description('Your contact details')
            ->fields($fields)
            ->columns(2)
            ->collapsible()
            ->collapsed(false)
            ->icon('heroicon-o-phone');

        $this->assertEquals('contact_info', $section->name);
        $this->assertEquals('Contact Information', $section->label);
        $this->assertEquals('Your contact details', $section->description);
        $this->assertEquals($fields, $section->fields);
        $this->assertEquals(2, $section->columns);
        $this->assertTrue($section->collapsible);
        $this->assertFalse($section->collapsed);
        $this->assertEquals('heroicon-o-phone', $section->icon);
    }

    /**
     * @test
     */
    public function section_backward_compatibility_fields_and_schema(): void
    {
        $section = new Section('test');
        $fields = [new InputField('test_field')];

        // Setting fields should also set schema
        $section->fields($fields);
        $this->assertEquals($fields, $section->fields);
        $this->assertEquals($fields, $section->schema);

        // Setting schema should also set fields
        $newSchema = [new InputField('new_field')];
        $section->schema($newSchema);
        $this->assertEquals($newSchema, $section->schema);
        $this->assertEquals($newSchema, $section->fields);
    }
}
