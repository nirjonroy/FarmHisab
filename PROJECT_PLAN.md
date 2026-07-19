# FarmHisab Project Plan

FarmHisab is being built in planned steps so the foundation stays clean and each module can be tested independently.

## Completed

- Step 1: Laravel 9 foundation with MySQL, Blade, Bootstrap 5, vanilla JavaScript, Vite, Sanctum, planning docs, and base folders.
- Step 2: Web authentication, Spatie roles and permissions, role-based dashboard navigation, and basic admin user management.
- Step 3A: Basic Farm Management with farm listing, search, create, edit, active status, and creator tracking.
- Step 3B: Basic Shed Management with shed listing, search, farm/status filters, create, edit, active status, capacity, and creator tracking.
- Step 3C: Dynamic Farm Category Management for poultry, livestock, aquaculture, and future farming categories.
- Step 3D-1: Bengali and English localization foundation with language files, web locale middleware, language switching, and user locale preference storage.
- Step 3D-2: Existing CRUD pages localized into Bengali and English for Users, Farms, Sheds, and Farm Categories.
- Step 3D-3: Dynamic bilingual Farm Category fields with locale-aware display accessors.
- Step 3D-4: Coming Soon pages, breadcrumbs, and remaining navigation labels localized through safe module mappings.
- Step 3E-1: Dynamic Farm Variety Management for breeds, species, strains, and varieties under child farm categories.
- Step 3E-2: Default bilingual Farm Variety seed data.

## Planned Modules

- Authentication and user roles
- Basic farm management
- Basic shed management
- Dynamic farm category management
- Localization foundation
- Existing CRUD pages localization
- Dynamic bilingual farm categories
- Coming Soon and navigation localization cleanup
- Dynamic farm variety management
- Default farm variety seed data
- Batch management
- Daily farm records
- Mortality tracking
- Weight tracking
- Feed purchase and usage
- Medicine and vaccination
- Expenses
- Sales
- Suppliers and buyers
- Inventory
- Cashbook and due management
- Profit and loss reports
- Notifications
- Android REST API

## Step 2 Notes

Roles are operational groupings: `admin`, `manager`, and `worker`.

Permissions are feature-access rules such as `users.view` and `feed.manage`. Application code should protect features with permissions, not by checking role names throughout controllers and views.

Only Dashboard and Users are functional in Step 2. Other module links are permission-gated and route to a reusable Coming Soon page.
