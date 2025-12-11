# TODO: Add Timestamps to REM Table

- [x] Edit the migration file `database/migrations/2025_11_14_031003_create_hoa-rem_table.php` to add `$table->timestamps()` to the rem table schema.
- [x] Update the `RemDatabase` model in `app/Models/RemDatabase.php` to enable timestamps by setting `$timestamps = true`.
- [x] Provide the SQL code for adding timestamps to the rem table.
- [ ] Run `php artisan migrate` to apply the changes if the migration hasn't been run yet.
