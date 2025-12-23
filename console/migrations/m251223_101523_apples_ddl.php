<?php

use yii\db\Migration;

class m251223_101523_apples_ddl extends Migration
{
    public function up()
    {
        $sql = <<<SQL
        create table apples
        (
            id int auto_increment not null
                primary key,
            user_id int not null,
            color varchar(32) not null,
            size_percent double not null,
            status varchar(10) not null,
            appeared_at datetime not null,
            fell_at datetime null,
            created_at datetime not null,
            updated_at datetime not null,
            is_archive bool not null,
            constraint `fk-apples-user_id`
                foreign key (user_id) references users (id)
        ) character set utf8 collate utf8_unicode_ci ENGINE=InnoDB;
        
        create index `ix-apples-is_archive`
            on apples (is_archive);
        
        create index `ix-apples-user_id`
            on apples (user_id);
        SQL;

        Yii::$app->db->createCommand($sql)->execute();
    }

    public function down()
    {
        $this->dropTable('apples');
    }
}
