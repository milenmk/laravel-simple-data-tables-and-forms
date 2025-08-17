<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Form\Fields;

use Livewire\Component;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\FileUploadField;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use PHPUnit\Framework\Attributes\Test;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class FileUploadFieldTest extends BaseTest
{
    #[Test]
    public function file_upload_field_creation(): void
    {
        $field = new FileUploadField('document');

        $this->assertEquals('document', $field->name);
        $this->assertEquals('file', $field->type);
        $this->assertFalse($field->multiple);
        $this->assertNull($field->accept);
        $this->assertNull($field->maxSize);
        $this->assertEquals(1, $field->maxFiles);
        $this->assertEquals([], $field->acceptedFileTypes);
        $this->assertEquals('uploads', $field->directory);
        $this->assertEquals('private', $field->visibility);
        $this->assertFalse($field->imagePreview);
    }

    #[Test]
    public function file_upload_field_multiple_configuration(): void
    {
        $field = new FileUploadField('documents');
        $result = $field->multiple();

        $this->assertTrue($field->multiple);
        $this->assertEquals(5, $field->maxFiles); // Default for multiple
        $this->assertSame($field, $result); // Test fluent interface
    }

    #[Test]
    public function file_upload_field_multiple_disabled(): void
    {
        $field = new FileUploadField('document');
        $field->multiple();
        $field->multiple(false);

        $this->assertFalse($field->multiple);
    }

    #[Test]
    public function file_upload_field_accept_configuration(): void
    {
        $field = new FileUploadField('document');
        $result = $field->accept('application/pdf');

        $this->assertEquals('application/pdf', $field->accept);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function file_upload_field_max_size_configuration(): void
    {
        $field = new FileUploadField('document');
        $result = $field->maxSize(2048);

        $this->assertEquals(2048, $field->maxSize);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function file_upload_field_max_files_configuration(): void
    {
        $field = new FileUploadField('documents');
        $result = $field->maxFiles(10);

        $this->assertEquals(10, $field->maxFiles);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function file_upload_field_directory_configuration(): void
    {
        $field = new FileUploadField('document');
        $result = $field->directory('documents');

        $this->assertEquals('documents', $field->directory);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function file_upload_field_visibility_configuration(): void
    {
        $field = new FileUploadField('document');
        $result = $field->visibility('public');

        $this->assertEquals('public', $field->visibility);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function file_upload_field_image_configuration(): void
    {
        $field = new FileUploadField('avatar');
        $result = $field->image();

        $this->assertTrue($field->imagePreview);
        $this->assertEquals(['jpg', 'jpeg', 'png', 'gif'], $field->acceptedFileTypes);
        $this->assertSame($field, $result);
    }

    #[Test]
    public function file_upload_field_accepted_file_types_with_common_types(): void
    {
        $field = new FileUploadField('document');
        $result = $field->acceptedFileTypes(['pdf', 'doc', 'docx']);

        $this->assertEquals(['pdf', 'doc', 'docx'], $field->acceptedFileTypes);
        $this->assertEquals(
            'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            $field->accept,
        );
        $this->assertSame($field, $result);
    }

    #[Test]
    public function file_upload_field_accepted_file_types_with_image_types(): void
    {
        $field = new FileUploadField('image');
        $field->acceptedFileTypes(['jpg', 'png', 'gif']);

        $this->assertEquals(['jpg', 'png', 'gif'], $field->acceptedFileTypes);
        $this->assertEquals('image/jpeg,image/png,image/gif', $field->accept);
    }

    #[Test]
    public function file_upload_field_accepted_file_types_with_unknown_type(): void
    {
        $field = new FileUploadField('file');
        $field->acceptedFileTypes(['xyz']);

        $this->assertEquals(['xyz'], $field->acceptedFileTypes);
        $this->assertEquals('.xyz', $field->accept);
    }

    #[Test]
    public function file_upload_field_validation_rules_single_file(): void
    {
        $field = new FileUploadField('document');
        $field->maxSize(1024)->acceptedFileTypes(['pdf', 'doc']);

        $rules = $field->getValidationRules();

        $this->assertContains('file', $rules);
        $this->assertContains('max:1024', $rules);
        $this->assertContains('mimes:pdf,doc', $rules);
    }

    #[Test]
    public function file_upload_field_validation_rules_multiple_files(): void
    {
        $field = new FileUploadField('documents');
        $field
            ->multiple()
            ->maxFiles(3)
            ->maxSize(2048)
            ->acceptedFileTypes(['pdf', 'docx']);

        $rules = $field->getValidationRules();

        $this->assertContains('array', $rules);
        $this->assertContains('max:3', $rules);
        $this->assertArrayHasKey('documents.*', $rules);

        $fileRules = $rules['documents.*'];
        $this->assertContains('file', $fileRules);
        $this->assertContains('max:2048', $fileRules);
        $this->assertContains('mimes:pdf,docx', $fileRules);
    }

    #[Test]
    public function file_upload_field_validation_rules_without_constraints(): void
    {
        $field = new FileUploadField('document');

        $rules = $field->getValidationRules();

        $this->assertContains('file', $rules);
        $this->assertStringNotContainsString('max:', implode('', $rules));
        $this->assertStringNotContainsString('mimes:', implode('', $rules));
    }

    #[Test]
    public function file_upload_field_fluent_interface(): void
    {
        $field = new FileUploadField('avatar');

        $result = $field
            ->multiple()
            ->maxFiles(3)
            ->maxSize(1024)
            ->directory('avatars')
            ->visibility('public')
            ->image();

        $this->assertTrue($field->multiple);
        $this->assertEquals(3, $field->maxFiles);
        $this->assertEquals(1024, $field->maxSize);
        $this->assertEquals('avatars', $field->directory);
        $this->assertEquals('public', $field->visibility);
        $this->assertTrue($field->imagePreview);
        $this->assertEquals(['jpg', 'jpeg', 'png', 'gif'], $field->acceptedFileTypes);
        $this->assertSame($field, $result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Throwable
     * @throws NotFoundExceptionInterface
     */
    #[Test]
    public function file_upload_field_render(): void
    {
        $field = new FileUploadField('document');
        $field->multiple()->acceptedFileTypes(['pdf', 'doc']);

        view()->share(
            '__livewire',
            new class extends Component
            {
                public function render(): string
                {
                    return '';
                }
            },
        );

        $rendered = $field->render();

        $this->assertStringContainsString('name="document"', $rendered);
        $this->assertStringContainsString('type="file"', $rendered);
    }
}
