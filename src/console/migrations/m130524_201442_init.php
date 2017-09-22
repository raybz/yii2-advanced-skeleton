<?php

use yii\base\InvalidConfigException;
use yii\db\Schema;
use yii\rbac\DbManager;

/**
 * based on @yii/rbac/migrations m130524_201442_init.
 * @property string $authManger
 */

/**
 * Class m130524_201442_init
 * @property integer authManager
 */
class m130524_201442_init extends \yii\db\Migration
{
    /**
     * @throws yii\base\InvalidConfigException
     *
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }

        return $authManager;
    }

    public function up()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $menuTable = \mdm\admin\components\Configs::instance()->menuTable;

        $this->createTable('{{%admin}}', [
            'id' => Schema::TYPE_PK,
            'username' => Schema::TYPE_STRING.' NOT NULL',
            'nick' => Schema::TYPE_STRING.' NOT NULL',
            'auth_key' => Schema::TYPE_STRING.' NOT NULL',
            'password_hash' => Schema::TYPE_STRING.' NOT NULL',
            'password_reset_token' => Schema::TYPE_STRING.' NOT NULL',
            'phone' => Schema::TYPE_STRING." NOT NULL DEFAULT ''",
            'email' => Schema::TYPE_STRING." NOT NULL DEFAULT ''",
            'last_login_ip' => Schema::TYPE_STRING." NOT NULL DEFAULT '0.0.0.0'",
            'last_login_time' => Schema::TYPE_DATETIME." NOT NULL DEFAULT '1970-01-01 00:00:00'",
            'login_times' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_DATETIME.' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME." NOT NULL DEFAULT '1970-01-01 00:00:00'",
            'status' => Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0',
            'CONSTRAINT `UC_ADMIN` UNIQUE (`username`)',
        ], $tableOptions);

        $this->createTable($authManager->ruleTable, [
            'name' => Schema::TYPE_STRING.'(64) NOT NULL',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (name)',
        ], $tableOptions);

        $this->createTable($authManager->itemTable, [
            'name' => Schema::TYPE_STRING.'(64) NOT NULL',
            'type' => Schema::TYPE_INTEGER.' NOT NULL',
            'description' => Schema::TYPE_TEXT,
            'rule_name' => Schema::TYPE_STRING.'(64)',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (name)',
            'FOREIGN KEY (rule_name) REFERENCES '.$authManager->ruleTable.' (name) ON DELETE SET NULL ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createIndex('idx-auth_item-type', $authManager->itemTable, 'type');

        $this->createTable($authManager->itemChildTable, [
            'parent' => Schema::TYPE_STRING.'(64) NOT NULL',
            'child' => Schema::TYPE_STRING.'(64) NOT NULL',
            'PRIMARY KEY (parent, child)',
            'FOREIGN KEY (parent) REFERENCES '.$authManager->itemTable.' (name) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (child) REFERENCES '.$authManager->itemTable.' (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

        $this->createTable($authManager->assignmentTable, [
            'item_name' => Schema::TYPE_STRING.'(64) NOT NULL',
            'user_id' => Schema::TYPE_STRING.'(64) NOT NULL',
            'created_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (item_name, user_id)',
            'FOREIGN KEY (item_name) REFERENCES '.$authManager->itemTable.' (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

        $this->createTable($menuTable, [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
            'parent' => $this->integer()->defaultValue(0),
            'route' => $this->string()->defaultValue(''),
            'order' => $this->integer()->notNull()->defaultValue(1),
            'data' => $this->text()->defaultValue(''),
            "FOREIGN KEY ([[parent]]) REFERENCES {$menuTable}([[id]]) ON DELETE SET NULL ON UPDATE CASCADE",
        ], $tableOptions);

    }

    public function down()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;

        $this->dropTable($authManager->assignmentTable);
        $this->dropTable($authManager->itemChildTable);
        $this->dropTable($authManager->itemTable);
        $this->dropTable($authManager->ruleTable);
        $this->dropTable('{{%admin}}');
    }
}
