<?php

use yii\base\InvalidConfigException;
use yii\db\Migration;
use yii\rbac\DbManager;
use yii\rbac\ManagerInterface;

/**
 * Adds index on `user_id` column in `auth_assignment` table for performance reasons.
 */
class m191001_052038_rbac_add_index_on_auth_assignment_user_id extends Migration
{
    /**
     * @var string
     */
    public $column = 'user_id';

    /**
     * @var string
     */
    public $index = 'auth_assignment_user_id_idx';

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
        $this->db = $authManager->db;

        $this->createIndex($this->index, $authManager->assignmentTable, $this->column);
    }

    /**
     * @return void
     *
     * @throws InvalidConfigException
     */
    public function down(): void
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;

        $this->dropIndex($this->index, $authManager->assignmentTable);
    }
}
