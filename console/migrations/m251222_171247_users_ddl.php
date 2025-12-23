<?php

use yii\db\Migration;

class m251222_171247_users_ddl extends Migration
{
    public function up()
    {
        $sql = <<<SQL
            create table users
            (
                id int auto_increment not null
                    primary key,
                email varchar(255) not null,
                auth_key varchar(32) not null,
                password_hash varchar(255) not null,
                password_reset_token varchar(255) null,
                status smallint not null,
                created_at datetime not null,
                updated_at datetime not null,
                constraint `uq-users-email`
                    unique (email),
                constraint `uq-users-password_reset_token`
                    unique (password_reset_token)
            ) character set utf8 collate utf8_unicode_ci ENGINE=InnoDB;

            create index `ix-users-status`
                on users (status);
        SQL;

        Yii::$app->db->createCommand($sql)->execute();
    }

    public function down()
    {
        $this->dropTable('users');
    }
}
