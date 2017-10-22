<?php

namespace Components\Database;

use yii\db\Migration as BaseMigration;

class Migration extends BaseMigration
{
    /**
     * @var string 创建表的选项
     */
    protected $tableOptions;

    /**
     * @var bool 表是否使用事务，默认使用
     */
    protected $useTransaction = true;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if ($this->db->driverName === 'mysql') {
            $this->tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE='.
                ($this->useTransaction ? 'InnoDB' : 'MyISAM');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createTable($table, $columns, $tableOptions = null)
    {
        parent::createTable($table, $columns, $tableOptions ?: $this->tableOptions);
    }

    /**
     * Drop table if exists.
     *
     * @param string $table The table name
     *
     * @throws \yii\db\Exception
     */
    final public function dropTableIfExists($table)
    {
        echo "  > drop table {$table} ...";
        $time = microtime(true);
        $this->db->createCommand("DROP TABLE IF EXISTS {$table}")->execute();
        echo ' done (time: '.sprintf('0.3%f', microtime(true) - $time)."s)\n";
    }
}
