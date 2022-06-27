*03/07/19 08:37*

INSTRUCTION TO MIGRATE DATABASE FOR NEW WEBPORTAL APP FROM OLD TO NEW:
1. Create database webportal_[version]
2. Export sql files from old webportal db except for the altered table schema
3. Import sql files to newly created database
4. Remove the row of altered table schema from migrations table
5. Execute 'php artisan migrate' to migrate the newly added migration from updated webportal app
6. Execute 'php artisan db:seed' to seed the default roles & permissions
   Note: Make sure to comment out the existing roles & permissions in the seeder
7. Make folder 'migration_[version]'
8. Move a copy of migration with altered table schema to the folder; eg. themes table
9. Execute 'php artisan migrate --path=/database/migrations_[version]/'
   Note: Delete similar row in the old migration table if it exists before execute
10. Execute 'sudo php artisan cache:clear' to clear cache
11. Manually add permissions in the newly added roles
12. Test the webportal before deployment

---------------------------------------------------------------------------------------------

08-20-2019
Migrating from 7.3 to 8.0

1. Create database webportal_[version]
2. Export sql files from old webportal db except for the altered table schema
3. Import sql files to newly created database
4. Don't run division seeder
5. Set customer_id to nullable in files table
6. Set connectivity_ticket_id to nullable in surveys table
7. Set
   ins_date,
   instruction,
   approved_by,
   req_no,
   received_by,
   date_received in maint_requests table
8. Set overtime_id to nullable in approved_logs table
9. Execute 'php artisan migrate --path=/database/migrations_[version]/'
   Note: Delete similar row in the old migration table if it exists before execute
10. Execute 'sudo php artisan cache:clear' to clear cache