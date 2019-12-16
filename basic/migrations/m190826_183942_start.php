<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m190826_183942_start
 */
class m190826_183942_start extends Migration
{
    /**
     * @return void
     */
    public function safeUp(): void
    {
        $this->createTable(
            'users',
            [
                'id'          => $this->primaryKey(11),
                'username'    => $this->string(64)->notNull()->unique(),
                'email'       => $this->string(64)->notNull()->unique(),
                'displayname' => $this->string(64)->notNull(),
                'password'    => $this->string(64)->notNull(),
                'authKey'     => $this->string(64)->notNull(),
                'resetKey'    => $this->string(64)->notNull(),
            ]
        );
    }

    /**
     * @return bool
     */
    public function safeDown(): bool
    {
        echo "m190826_183942_start cannot be reverted.\n";

        return false;
    }
}
