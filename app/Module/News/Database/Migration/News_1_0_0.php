<?php
namespace Module\News\Database\Migration;

use Core\Library\Database\Migration;

Class News_1_0_0 extends Migration
{

    /**
     * Create table.
     */
    public function up()
    {
        $this->schema()->dropIfExists('news');

        $this->schema()->create('news', function ($table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->longText('text');
            $table->boolean('published');
            $table->integer('category_id');
            $table->timestamps();
        });
    }

    /**
     * drop table 
     */
    public function down()
    {
        $this->schema()->drop('news');
    }

}