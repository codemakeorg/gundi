<?php
namespace Module\News\Database\Migration;

use Core\Library\Database\Migration;

Class Category_1_0_0 extends Migration
{

    /**
     * Create category table.
     * for simplicity we wil not use nested sets
     */
    public function up()
    {
        $this->schema()->dropIfExists('categories');

        $this->schema()->create('categories', function ($table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * drop table
     */
    public function down()
    {
        $this->schema()->drop('categories');
    }

}