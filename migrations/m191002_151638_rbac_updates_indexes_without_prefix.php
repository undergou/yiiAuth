<?php

use yii\base\InvalidConfigException;
use yii\db\Migration;
use yii\rbac\DbManager;
use yii\rbac\ManagerInterface;

/**
 * Updates indexes without a prefix.
 */
class m191002_151638_rbac_updates_indexes_without_prefix extends Migration
{
    /**
     * @return DbManager
     *
     * @throws yii\base\InvalidConfigException
     */
    protected function getAuthManager(): ManagerInterface
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException(
                'You should configure "authManager" component to use database before executing this migration.'
            );
        }

        return $authManager;
    }

    /**
     * @return void
     *
     * @throws InvalidConfigException
     */
    public function up(): void
    {
        $authManager = $this->getAuthManager();

        $this->dropIndex('auth_assignment_user_id_idx', $authManager->assignmentTable);
        $this->createIndex('{{%idx-auth_assignment-user_id}}', $authManager->assignmentTable, 'user_id');

        $this->dropIndex('idx-auth_item-type', $authManager->itemTable);
        $this->createIndex('{{%idx-auth_item-type}}', $authManager->itemTable, 'type');
    }

    /**
     * @return void
     *
     * @throws InvalidConfigException
     */
    public function down(): void
    {
        $authManager = $this->getAuthManager();

        $this->dropIndex('{{%idx-auth_assignment-user_id}}', $authManager->assignmentTable);
        $this->createIndex('auth_assignment_user_id_idx', $authManager->assignmentTable, 'user_id');

        $this->dropIndex('{{%idx-auth_item-type}}', $authManager->itemTable);
        $this->createIndex('idx-auth_item-type', $authManager->itemTable, 'type');
    }
}
