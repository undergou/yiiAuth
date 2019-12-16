<?php

use yii\db\Migration;

/**
 * Class m191201_004903_perms
 */
class m191201_004903_perms extends Migration
{
    /**
     * @return void
     *
     * @throws Exception
     */
    public function safeUp(): void
    {
        $auth = Yii::$app->authManager;

        $admin = $auth->createPermission('admin');
        $admin->description = 'Admin';
        $auth->add($admin);

        $active = $auth->createPermission('active');
        $active->description = 'Active';
        $auth->add($active);
    }

    /**
     * @return bool
     */
    public function safeDown(): bool
    {
        echo "m191201_004903_perms cannot be reverted.\n";

        return false;
    }
}
