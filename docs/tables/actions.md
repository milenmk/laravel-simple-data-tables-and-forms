## Table Actions

Laravel Simple Datatables And Forms provides a flexible action system that allows you to add interactive buttons to your
tables.
These actions can either navigate to URLs or trigger Livewire methods in your component.

## Basic Usage

Actions are typically added to tables using the `ActionColumn` class:

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\ActionColumn;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions\EditAction;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions\DeleteAction;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions\ViewAction;

public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->schema([
            // Other columns...

            ActionColumn::make('actions')
                ->label('Actions')
                ->actions([
                    EditAction::make('edit')
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square')
                        ->url(fn($row) => route('users.edit', $row)),

                    DeleteAction::make('delete')
                        ->label('Delete')
                        ->icon('heroicon-o-trash')
                        ->action('deleteUser'),

                    ViewAction::make('view')
                        ->label('View')
                        ->icon('heroicon-o-eye')
                        ->url(fn($row) => route('users.show', $row)),
                ]),
        ]);
}
```

## Action Types

The package includes several pre-built action types:

- **EditAction**: For editing records
- **DeleteAction**: For deleting records
- **ViewAction**: For viewing record details
- **CustomAction**: For custom functionality

You can also create your own action types by extending the `BaseAction` class.

## URL-Based Actions

URL-based actions navigate to a specific URL when clicked:

```php
EditAction::make('edit')->label('Edit')->icon('heroicon-o-pencil-square')->url(fn($row) => route('users.edit', $row));
```

The `url` method accepts either a string or a closure. When using a closure, the current record is passed as a
parameter, allowing you to generate dynamic URLs based on the record data.

## Livewire Actions

Livewire actions trigger methods in your Livewire component when clicked:

```php
DeleteAction::make('delete')->label('Delete')->icon('heroicon-o-trash')->action('deleteUser');
```

The `action` method specifies the name of the method to call in your Livewire component. The record ID is automatically
passed to this method:

```php
// In your Livewire component
public function deleteUser($id)
{
    $user = User::find($id);
    if ($user) {
        $user->delete();
        $this->notification()->success('User deleted successfully');
    }
}
```

## Action Styles

Actions can be displayed in different styles:

```php
// Icon style (just the icon)
EditAction::make('edit')->icon('heroicon-o-pencil-square')->actionView('icon');

// Badge style (colored badge with text)
DeleteAction::make('delete')->label('Delete')->actionView('badge');

// Button style (full button with icon and text)
ViewAction::make('view')->label('View')->icon('heroicon-o-eye')->actionView('button');

// Default style (text link with optional icon)
CustomAction::make('custom')->label('Custom Action')->icon('heroicon-o-cog');
```

## Customizing Action Appearance

### Icons

You can set an icon for an action using the `icon` method:

```php
EditAction::make('edit')->icon('heroicon-o-pencil-square');
```

The package uses Blade Icons, so you can use any icon from the Heroicons set or other icon sets that you've installed.

### Colors

You can customize the color of an action using the `color` method:

```php
DeleteAction::make('delete')->color('danger');
```

Available color options include:

- `primary` (default)
- `secondary`
- `success`
- `danger`
- `warning`
- `info`

### Labels

You can set a label for an action using the `label` method:

```php
EditAction::make('edit')->label('Edit User');
```

The label is displayed as text for badge, button, and default styles, and as a tooltip for icon style.

## Grouping Actions

If you have multiple actions, you can group them to save space:

```php
ActionColumn::make('actions')
    ->groupActions()
    ->actions([EditAction::make('edit'), DeleteAction::make('delete'), ViewAction::make('view')]);
```

This will display a dropdown menu with all the actions.

## Conditional Actions

You can conditionally show or hide actions based on the record data:

```php
ActionColumn::make('actions')->actions([
    EditAction::make('edit')->visible(fn($row) => $row->status === 'active'),

    DeleteAction::make('delete')->visible(fn($row) => auth()->user()->can('delete', $row)),
]);
```

## Confirmation Modals

For destructive or important actions, you can add confirmation modals to prevent accidental clicks:

```php
DeleteAction::make('delete')
    ->label('Delete')
    ->icon('heroicon-o-trash')
    ->action('deleteUser')
    ->requiresConfirmation()
    ->modalHeading('Delete User')
    ->modalDescription('Are you sure you want to delete this user?')
    ->modalContent('This action cannot be undone.')
    ->modalConfirmationButtonLabel('Delete User');
```

### Confirmation Modal Options

- **`requiresConfirmation()`**: Enables the confirmation modal
- **`modalHeading()`**: Sets the modal title
- **`modalDescription()`**: Sets the modal description text
- **`modalContent()`**: Sets additional content in the modal body
- **`modalConfirmationButtonLabel()`**: Customizes the confirmation button text

### Dynamic Confirmation Content

All modal methods accept closures for dynamic content based on the record:

```php
DeleteAction::make('delete')
    ->requiresConfirmation()
    ->modalHeading(fn($record) => "Delete {$record->name}")
    ->modalDescription(fn($record) => "Are you sure you want to delete {$record->name}?")
    ->modalConfirmationButtonLabel(fn($record) => "Delete {$record->name}");
```

### HTML Content in Modals

You can use HTML content in modals by returning an `HtmlString`:

```php
use Illuminate\Support\HtmlString;

DeleteAction::make('delete')
    ->requiresConfirmation()
    ->modalConfirmationButtonLabel(fn($record) => new HtmlString("<strong>Delete {$record->name}</strong>"));
```

## Creating Custom Actions

You can create custom actions by extending the `BaseAction` class:

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions\BaseAction;

class ArchiveAction extends BaseAction
{
    public string|Closure|bool|null $icon = 'heroicon-o-archive-box';

    public function label(string|array|null $value): self
    {
        $this->label = $value ?? __('Archive');

        return $this;
    }
}
```

Then use it in your table:

```php
ActionColumn::make('actions')->actions([ArchiveAction::make('archive')->action('archiveRecord')]);
```

## Best Practices

1. **Use Descriptive Labels**: Make sure your action labels clearly describe what the action does.

2. **Add Icons**: Icons help users quickly identify actions without having to read the labels.

3. **Use Appropriate Styles**: Choose the right style for each action based on its importance and frequency of use.

4. **Group Related Actions**: If you have many actions, group them to avoid cluttering the UI.

5. **Add Confirmation for Destructive Actions**: For actions like delete, consider adding a confirmation dialog.

6. **Implement Proper Authorization**: Make sure users can only see and use actions they have permission for.

7. **Handle Errors Gracefully**: In your Livewire action methods, handle errors and provide feedback to the user.

## Example: Complete CRUD Implementation

Here's a complete example of implementing CRUD actions in a table:

```php
// In your Livewire component
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\ActionColumn;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions\EditAction;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions\DeleteAction;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions\ViewAction;

public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->schema([
            // Other columns...

            ActionColumn::make('actions')
                ->label('Actions')
                ->actions([
                    ViewAction::make('view')
                        ->icon('heroicon-o-eye')
                        ->actionView('icon')
                        ->url(fn($row) => route('users.show', $row)),

                    EditAction::make('edit')
                        ->icon('heroicon-o-pencil-square')
                        ->actionView('icon')
                        ->url(fn($row) => route('users.edit', $row)),

                    DeleteAction::make('delete')
                        ->icon('heroicon-o-trash')
                        ->actionView('icon')
                        ->action('deleteUser')
                        ->requiresConfirmation()
                        ->modalHeading('Delete User')
                        ->modalDescription('Are you sure you want to delete this user?')
                        ->modalConfirmationButtonLabel('Delete User'),
                ]),
        ]);
}

// Action handler method
public function deleteUser($id)
{
    $user = User::find($id);

    if (!$user) {
        $this->notification()->error('User not found');
        return;
    }

    try {
        $user->delete();
        $this->notification()->success('User deleted successfully');
    } catch (\Exception $e) {
        $this->notification()->error('Failed to delete user: ' . $e->getMessage());
    }
}
```

This implementation provides a complete set of CRUD actions for managing users, with appropriate icons and styles for
each action.
