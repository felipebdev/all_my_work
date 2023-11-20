<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePlanCategories extends Migration
{
    const CATEGORIES = [
        'Saúde e Esportes', 'Finanças e Investimentos', 'Relacionamentos',
        'Negócios e Carreira', 'Espiritualidade', 'Entretenimento',
        'Culinária e Gastronomia', 'Idiomas', 'DireitoApps & Software',
        'Literatura', 'Casa e Construção', 'Desenvolvimento Pessoal',
        'Moda e Beleza', 'Animais e Plantas', 'Educacional',
        'Hobbies', 'Design', 'Internet', 'Ecologia e Meio Ambiente',
        'Música e Artes', 'Tecnologia da Informação', 'Empreendedorismo Digital',
        'Outros'
    ];
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });

        $categories = [];
        foreach (self::CATEGORIES as $category) {
            $categories[] = [
                'name' => $category,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        DB::table('plan_categories')->insert($categories);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_categories');
    }
}
