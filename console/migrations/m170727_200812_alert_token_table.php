<?php

use yii\db\Migration;
use api\modules\user\models\Token;
use yii\db\Schema;


class m170727_200812_alert_token_table extends Migration
{
    public function up()
    {
        $this->addColumn(Token::tableName(), 'expire', 'DATETIME NOT NULL');
        $this->addColumn(Token::tableName(), 'status', 'TINYINT(1) NOT NULL');
        $this->addColumn(Token::tableName(), 'ip', Schema::TYPE_STRING . '(32) NOT NULL');
    }

    public function down()
    {
        $this->dropColumn(Token::tableName(), 'ip');
        $this->dropColumn(Token::tableName(), 'status');
        $this->dropColumn(Token::tableName(), 'expire');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
