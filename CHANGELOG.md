## 2.1.6

#### Published at: 2025-08-17

- [FIX] Table action modal not rendering
- Improved Confirmation modal customization options

## v2.1.5

#### Published at: 2025-08-17

- [FIX] Table actions had recordId attached permanently. Not is have to be explicitly set.
- [FIX] Table action `action()` cannot accept closure, resulting to an empty `wire:click`
- fixed some code style warnings

## v2.1.4

#### Published at: 2025-008-17

- [FIX] Searchable select in form not setting the selected option value

## v2.1.3

#### Published at: 2025-008-16

- [NEW] `extraAttributes([])` now also can be used for the table feature

## v2.1.2

#### Published at: 2025-008-16

- [FIX] `extraAttributes([])` was implemented only for the form when it has sections. Now it works for form without
  sections also.

## v2.1.1

#### Published at: 2025-008-16

- Form section now also accepts `extraAttributes([])`

## v2.1.0

#### Published at: 2025-008-16

- [NEW] Introduce reactivity to the form feature

## v2.0.1

#### Published at: 2025-008-16

- Fix composer version

- ## v2.0.0

#### Published at: 2025-01-XX

### 🎉 Major Release: Forms Feature Added

This is a major release that introduces comprehensive form functionality alongside the existing table features,
transforming the package from "Laravel Simple Datatables" to "Laravel Simple Datatables And Forms".

### ✨ New Features

#### 📝 Dynamic Forms System

- **[NEW]** Complete form builder with fluent API
- **[NEW]** Multiple field types: Input, Select, Textarea, Checkbox, Toggle, File Upload, Hidden, Radio, Repeater
- **[NEW]** Form sections and responsive layouts
- **[NEW]** Model binding and relationship support
- **[NEW]** Real-time and client-side validation
- **[NEW]** Searchable and relationship select fields
- **[NEW]** Form actions and custom buttons
- **[NEW]** `HasForm` trait for Livewire components
- **[NEW]** `php artisan make:milenmk-form` command with auto-generation support

#### 🛠️ Enhanced Commands & Stubs

- **[NEW]** Form generation commands with `--generate` flag
- **[NEW]** Create/Edit form stubs for different use cases
- **[NEW]** Enhanced table generation with improved field detection

#### 📚 Comprehensive Documentation Overhaul

- **[NEW]** Complete documentation restructure with dedicated sections for tables and forms
- **[NEW]** Step-by-step getting started guides
- **[NEW]** Advanced feature documentation with practical examples
- **[NEW]** Troubleshooting guide with common solutions
- **[NEW]** Documentation hub with organized navigation

### 🔧 Improvements & Enhancements

#### 🎨 UI/UX Improvements

- **[IMPROVED]** Enhanced styling and responsive design
- **[IMPROVED]** Better icon management and display
- **[IMPROVED]** Form field rendering and validation display
- **[IMPROVED]** Enhanced table column visibility controls

#### ⚡ Performance & Architecture

- **[IMPROVED]** Better service organization and dependency injection
- **[IMPROVED]** Enhanced caching mechanisms
- **[IMPROVED]** Optimized asset management
- **[IMPROVED]** Security features and input sanitization

#### 🔧 Configuration & Setup

- **[BREAKING]** Package renamed from `laravel-simple-datatables` to `laravel-simple-datatables-and-forms`
- **[BREAKING]** Configuration file renamed from `simple-datatables.php` to `simple-datatables-and-forms.php`
- **[BREAKING]** Service provider renamed to `LaravelSimpleDatatablesAndFormsServiceProvider`
- **[IMPROVED]** Enhanced asset publishing with `simple-datatables-and-forms:publish-assets` command

### 🐛 Bug Fixes

- **[FIX]** Cannot assign null to property `$visibleColumns` of type object|array
- **[FIX]** Toggle column value now properly uses `$column->getValue($item)` instead of direct property access
- **[FIX]** Improved error handling and validation feedback
- **[FIX]** Better handling of nullable values in various column types
- **[FIX]** Table columns now also accept callable to set `visibility()` value

### 📖 Documentation & Developer Experience

- **[IMPROVED]** Streamlined main README with quick start focus
- **[IMPROVED]** Comprehensive documentation hub with clear navigation
- **[IMPROVED]** Consistent file naming conventions (using dashes instead of underscores)
- **[IMPROVED]** Updated all repository references and contact information
- **[IMPROVED]** Enhanced code examples with proper imports and best practices

### 🔄 Migration Notes

- Update your `composer.json` to use `milenmk/laravel-simple-datatables-and-forms`
- Republish configuration: `php artisan vendor:publish --tag=laravel-simple-datatables-and-forms-config`
- Update asset publishing: `php artisan simple-datatables-and-forms:publish-assets`
- Review and update any custom configurations from the old config file

### 💡 New Capabilities

- Build complex forms with sections, validation, and model binding
- Create searchable select fields with relationship support
- Use repeater fields for dynamic form sections
- Implement real-time validation with Livewire
- Generate forms automatically from model schemas
- Combine tables and forms in comprehensive admin interfaces

## v1.13.1

#### Published at: 2025-08-10

- [FIX] Error `Serialization of 'Closure' is not allowed` when caching table columns
- [NEW] Add filters grouping

## v1.13.0

#### Published at: 2025-08-10

- [FIX] Active filters displays column ID instead of option name
- [NEW] Responsive filters
- [NEW] User can set filters responsiveness to false in the config file and specify custom number of columns
- [NEW] Filters can be grouped

## v1.12.1

#### Published at: 2025-06-13

- Generate a new package.css file to reflect the recent changes

## v1.12.0

#### Published at: 2025-06-13

- [NEW] Implements confirmation modal for actions

## v1.11.0

#### Published at: 2025-06-12

- Refactor actions to support Livewire actions with configurable styles and button types.
- [FIX] `url()` for actions not working properly
- [FIX] `action()` for actions not working properly
- Updates Readme to reflect the current package status and functions
- Updates Security Policy

## v1.10.9

#### Published at: 2025-06-01

- [FIX] Handles null values in date columns
- Add query method for filters

## v1.10.8

#### Published at: 2025-06-01

- [FIX] Tailwind pagination styling

## v1.10.7

#### Published at: 2025-06-01

- [FIX] The package missing modal for the export functionality is shown on a page load

## v1.10.6

#### Published at: 2025-06-01

- Export blade component now includes all available exports
- Modal is shown if the external package required for the export is not installed
- Convert raw strings to translatable strings

## v1.10.5

#### Published at: 2025-06-01

- [FIX] No data exported when using a relation in a column

## v1.10.4

#### Published at: 2025-06-01

- [FIX] Add an option to set the relation search fields like
  `TextColumn::make('user')->searchable(['user.name'])` to avoid SQL query errors when searching a relation field

## v1.10.3

#### Published at: 2025-06-01

- [BUG_FIX] When using a nested value for a relation property like `TextColumn::make('user.name')` no data is displayed

## v1.10.2

#### Published at: 2025-06-01

- [NEW] Ability to set a custom date format for text column when cast to date.

## v1.10.1

#### Published at: 2025-05-27

- Enhance action button styling with specific colors.

## v1.10.0

#### Published at: 2025-05-27

- Removes CreateAction.php to consolidate creation logic elsewhere.

## v1.9.2

#### Published at: 2025-05-27

- Bug fixes

## v1.9.1

#### Published at: 2025-05-27

- Bug fixes
- Adjusting the href tags in the action's blade file

## v1.9.0

#### Published at: 2025-05-27

- Introducing an action column to table
- Added Create, Edit and Delete actions to be used within the ActionColumn

## v1.6.3

#### Published at: 2025-03-24

- Add Tailwind CSS to generate package css file

## v1.6.2

#### Published at: 2025-03-24

- Added custom package css for sorting
- Updated pagination file

## v1.6.1

#### Published at: 2025-03-24

- [FIX] Missing package custom pagination blade file

## v1.6.0

#### Published at: 2025-03-24

- [FIX] Remove obsolete files from publishing
- Add `blade-ui-kit/blade-icons` to the required packages
- Add package own pagination to avoid errors when using default Tailwind pagination
- Add `@SimpleDatatablesStyle` blade directive to apply required styles

## v1.5.0

#### Published at: 2025-03-20

- [NEW] Added IconColumn to Table
- [NEW] Added heroicons support
- [NEW] Command to create Livewire component data table support

## v1.4.2

#### Published at: 2025-03-20

- [FIX] Filters not showing

## v1.4.1

#### Published at: 2025-03-20

- [FIX] Error Property type not supported in Livewire for property: [{}]

## v1.4.0

#### Published at: 2025-03-20

- Add filtering functionality

## v1.3.1

#### Published at: 2025-03-15

- Implement table grouping.
- [BUG_FIX] `Uncaught Snapshot missing on Livewire component with id:` when sorting or show/hide column
- Removed the dedicated Toggle and Checkbox Livewire component. The logic is now inside the HasTable trait.

## v1.3.0

#### Published at: 2025-03-14

- Added README installation and usage instructions

## V1.1.0 - v1.2.4

- BUG Fixes

## v1.0.0

#### Published at: 2024-03-12

- Initial release
