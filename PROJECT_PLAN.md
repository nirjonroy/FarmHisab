# FarmHisab Project Plan

FarmHisab is being built in planned steps so the foundation stays clean and each module can be tested independently.

## Completed

- Step 1: Laravel 9 foundation with MySQL, Blade, Bootstrap 5, vanilla JavaScript, Vite, Sanctum, planning docs, and base folders.
- Step 2: Web authentication, Spatie roles and permissions, role-based dashboard navigation, and basic admin user management.
- Step 3A: Basic Farm Management with farm listing, search, create, edit, active status, and creator tracking.

## Planned Modules

- Authentication and user roles
- Basic farm management
- Shed management
- Bird types and breeds
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
