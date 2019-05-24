### About database migrations
Few important principles:
* There is only one database structure across all apps and servers.
* Only upwards migrations are supported
* Migrations are always run automatically on production pod init

Because of this, its important to note, that:
**You should generally not drop tables or columns.**

### Database table naming

Database tables that "belong" to an action, should have a suffix:

ae_ext_actionname_

### Example case about dropping

Mitems action has several dependant database tables. These tables are used in different ways between different apps. Hence, dropping even a single column, would result in some other app not working as intended.

### Normal migration
Class under actions Migrations directory is defined like this: 
```
class MigrationsactionMexample extends BootstrapMigrations {
 
     /* set these so that the action will get created automatically */
     public $title = 'Mobile Example';
     public $icon = 'new.png';
     public $description = 'Example module';
 
     public function runModuleMigrations(){
         $this->exampleMigration();
         return true;
     }
 
     /* example on how to run an external sql file */
     private function exampleMigration(){
         if(self::helperTableExists('ae_ext_example_test')){
             $this->runMigrationFromFile('tables.sql');
         }
     }
 }
 
```
### Determine your database state

In order to determine, whether migrations should be run or not, we use little helpers. For example on the above example, the helperTableExists will check whether the local database already have such table, and if not, runs the migration sql.
