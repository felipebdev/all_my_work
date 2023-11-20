<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTemplatesTableAddColumnWelcomeModel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('templates', function (Blueprint $table) {
            /*
            UPDATE `platform_site_configs` SET `welcome_template_id` = xxxx;
            INSERT INTO `templates` (`id`, `name`, `description`, `folder`, `amount_of_fixed_content`, `thumb_id`, `platform`, `created_at`, `updated_at`, `active`, `content`, `content_model`, `has_slide`, `course`, `course_model`, `welcome`) VALUES (NULL, 'Modelo 1', 'Modelo 1', 'welcome-1', '0', 0, NULL, '2020-08-24 16:34:39', '2020-08-24 17:50:32', '1', '0', '0', '0', '0', '0', '1'), (NULL, 'Modelo 2', 'Modelo 2', 'welcome-2', '0', 0, NULL, '2020-08-24 16:35:01', '2020-08-24 17:53:09', '1', '0', '0', '0', '0', '0', '1');
            */
            $table->boolean('welcome')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn('welcome');
        });
    }
}
