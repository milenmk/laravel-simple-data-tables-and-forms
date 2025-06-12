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
