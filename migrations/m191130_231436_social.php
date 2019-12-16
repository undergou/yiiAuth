<?php

use yii\db\Migration;

/**
 * Class m191130_231436_social
 */
class m191130_231436_social extends Migration
{
    /**
     * @return void
     */
    public function safeUp(): void
    {
        $this->createTable(
            'auth',
            [
                'id'        => $this->primaryKey(11),
                'user_id'   => $this->integer(11)->notNull(),
                'source'    => $this->string(64)->notNull(),
                'source_id' => $this->string(64)->notNull(),
            ]
        );
    }

    /**
     * @return bool
     */
    public function safeDown(): bool
    {
        echo "m191130_231436_social cannot be reverted.\n";

        return false;
    }
}
