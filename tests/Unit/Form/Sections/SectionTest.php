<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Form\Sections;

use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\InputField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Sections\Section;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Models\TestModel;
use PHPUnit\Framework\Attributes\Test;

class SectionTest extends BaseTest
{
    #[Test]
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

    #[Test]
    public function section_make_static_method(): void
    {
        $section = Section::make('contact_info');

        $this->assertEquals('contact_info', $section->name);
    }

    #[Test]
    public function section_label_configuration(): void
    {
        $section = new Section('personal_info');
        $result = $section->label('Personal Information');

        $this->assertEquals('Personal Information', $section->label);
        $this->assertSame($section, $result); // Test fluent interface
    }

    #[Test]
    public function section_description_configuration(): void
    {
        $section = new Section('personal_info');
        $result = $section->description('Enter your personal details');

        $this->assertEquals('Enter your personal details', $section->description);
        $this->assertSame($section, $result);
    }

    #[Test]
    public function section_fields_configuration(): void
    {
        $section = new Section('personal_info');
        $fields = [new InputField('name'), new InputField('email')];

        $result = $section->fields($fields);

        $this->assertEquals($fields, $section->fields);
        $this->assertEquals($fields, $section->schema); // Backward compatibility
        $this->assertSame($section, $result);
    }

    #[Test]
    public function section_schema_configuration(): void
    {
        $section = new Section('personal_info');
        $schema = [new InputField('name'), new InputField('email')];

        $result = $section->schema($schema);

        $this->assertEquals($schema, $section->schema);
        $this->assertEquals($schema, $section->fields); // Backward compatibility
        $this->assertSame($section, $result);
    }

    #[Test]
    public function section_columns_configuration(): void
    {
        $section = new Section('personal_info');
        $result = $section->columns(3);

        $this->assertEquals(3, $section->columns);
        $this->assertSame($section, $result);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function section_icon_configuration(): void
    {
        $section = new Section('personal_info');
        $result = $section->icon('heroicon-o-user');

        $this->assertEquals('heroicon-o-user', $section->icon);
        $this->assertSame($section, $result);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function section_get_fields(): void
    {
        $fields = [new InputField('name'), new InputField('email')];
        $section = new Section('test');
        $section->fields($fields);

        $this->assertEquals($fields, $section->getFields());
    }

    #[Test]
    public function section_set_form_context(): void
    {
        $field = new InputField('name');
        $section = new Section('test');
        $section->fields([$field]);

        $record = TestModel::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'category' => 'Books',
            'is_active' => false,
            'price' => 29.99,
        ]);
        $formData = ['name' => $record->name];

        $result = $section->setFormContext($record, $formData);

        $this->assertSame($section, $result); // Test fluent interface
    }

    #[Test]
    public function section_header_actions_configuration(): void
    {
        $section = new Section('test');
        $actions = ['action1', 'action2'];

        $result = $section->headerActions($actions);

        $this->assertEquals($actions, $section->headerActions);
        $this->assertSame($section, $result);
    }

    #[Test]
    public function section_compact_configuration(): void
    {
        $section = new Section('test');

        // Test enabling compact
        $result = $section->compact();
        $this->assertTrue($section->compact);
        $this->assertSame($section, $result);

        // Test disabling compact
        $section->compact(false);
        $this->assertFalse($section->compact);
    }

    #[Test]
    public function section_aside_configuration(): void
    {
        $section = new Section('test');

        // Test enabling aside
        $result = $section->aside();
        $this->assertTrue($section->aside);
        $this->assertSame($section, $result);

        // Test disabling aside
        $section->aside(false);
        $this->assertFalse($section->aside);
    }

    #[Test]
    public function section_column_span_configuration(): void
    {
        $section = new Section('test');

        // Test with integer
        $result = $section->columnSpan(3);
        $this->assertEquals(3, $section->columnSpan);
        $this->assertSame($section, $result);

        // Test with string
        $section->columnSpan('full');
        $this->assertEquals('full', $section->columnSpan);
    }

    #[Test]
    public function section_persist_collapsed_configuration(): void
    {
        $section = new Section('test');

        // Test enabling persist collapsed
        $result = $section->persistCollapsed();
        $this->assertTrue($section->persistCollapsed);
        $this->assertSame($section, $result);

        // Test disabling persist collapsed
        $section->persistCollapsed(false);
        $this->assertFalse($section->persistCollapsed);
    }

    #[Test]
    public function section_visible_configuration(): void
    {
        $section = new Section('test');

        // Test with boolean
        $result = $section->visible(false);
        $this->assertFalse($section->visible);
        $this->assertSame($section, $result);

        // Test with callable
        $section->visible(fn () => true);
        $this->assertTrue($section->visible);
    }

    #[Test]
    public function section_view_configuration(): void
    {
        $section = new Section('test');
        $result = $section->view('custom.section.view');

        $this->assertEquals('custom.section.view', $section->view);
        $this->assertSame($section, $result);
    }

    #[Test]
    public function section_content_configuration(): void
    {
        $section = new Section('test');
        $content = 'Custom content';

        $result = $section->content($content);

        $this->assertEquals($content, $section->content);
        $this->assertSame($section, $result);
    }

    #[Test]
    public function section_get_label(): void
    {
        // Test with explicit label
        $section = new Section('test_section');
        $section->label('Custom Label');

        $this->assertEquals('Custom Label', $section->getLabel());

        // Test with auto-generated label from name
        $section2 = new Section('user_profile');
        $this->assertEquals('User Profile', $section2->getLabel());
    }

    #[Test]
    public function section_extra_attributes_as_array(): void
    {
        $section = new Section('test');
        $attributes = ['class' => 'custom-class', 'data-test' => 'value'];

        $result = $section->extraAttributes($attributes);

        $this->assertEquals($attributes, $section->extraAttributes);
        $this->assertSame($section, $result);
    }

    #[Test]
    public function section_extra_attributes_as_closure(): void
    {
        $section = new Section('test');
        $closure = fn () => ['class' => 'dynamic-class'];

        $result = $section->extraAttributes($closure);

        $this->assertEquals($closure, $section->extraAttributesCallback);
        $this->assertSame($section, $result);
    }

    #[Test]
    public function section_get_merged_attributes(): void
    {
        $section = new Section('test');
        $section->extraAttributes(['class' => 'extra-class', 'data-test' => 'value']);

        $baseAttributes = ['class' => 'base-class', 'id' => 'test-id'];
        $merged = $section->getMergedAttributes($baseAttributes);

        $this->assertEquals('base-class extra-class', $merged['class']);
        $this->assertEquals('test-id', $merged['id']);
        $this->assertEquals('value', $merged['data-test']);
    }

    #[Test]
    public function section_get_extra_attributes(): void
    {
        $section = new Section('test');
        $attributes = ['class' => 'test-class'];
        $section->extraAttributes($attributes);

        $this->assertEquals($attributes, $section->getExtraAttributes());
    }

    #[Test]
    public function section_get_extra_attributes_with_callback(): void
    {
        $section = new Section('test');
        $section->extraAttributes(fn () => ['class' => 'callback-class']);
        $section->setFormContext(null, []);

        $attributes = $section->getExtraAttributes();

        $this->assertEquals('callback-class', $attributes['class']);
    }
}
