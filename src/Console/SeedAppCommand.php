<?php

namespace Dcat\Admin\Console;

use Dcat\Admin\Core\Util\StrUtil;
use Dcat\Admin\Models\AdminTablesSeeder;
use Illuminate\Console\Command;

class SeedAppCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'admin:seed-app {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database tables of a new application, And all tables need to be customized in the config file.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->seedDatabase();
        $this->info('Done.');
    }

    public function seedDatabase() {

        $name = StrUtil::uncamelize($this->argument('name'), '-');

        $userTable = config(sprintf('%s.database.users_table', $name));
        if (empty($userTable)) {
            $this->fail('Config name was wrong.');
        }

        $userModel = config(sprintf('%s.database.users_model', $name));
        if ($userModel::count() == 0) {
            AdminTablesSeeder::_setApp($name);
            $this->call('db:seed', ['--class' => AdminTablesSeeder::class]);
        }
    }


    /**
     * Set admin directory.
     *
     * @return void
     */
    protected function setDirectory()
    {
        $this->directory = app_path($this->argument('name'));
    }

}
