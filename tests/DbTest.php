<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

use pp\Db;

final class DbTest extends TestCase
{

    protected function setUp(): void
    {
        $this->db = new Db('sqlite::memory:');
        $this->db->query("
                CREATE TABLE IF NOT EXISTS mytest (
                    id    INTEGER       PRIMARY KEY AUTOINCREMENT,
                    name  VARCHAR (100),
                    value VARCHAR (100) 
                );
            ");
    }

    public function testIsAnInstanceOfPdo(): void
    {

        $this->assertInstanceOf(\Pdo::class, $this->db);

    }

    
    public function testExec(): void
    {

        $row = [
            'name' => 'my name',
            'value' => 'my value',
        ];
 
        $rowCount = $this->db->exec('INSERT INTO mytest (name, value) VALUES (:name, :value)', $row);

        $this->assertSame(1, $rowCount);

    }

    public function testExecute(): void
    {

        // Insert a row
        $row = [
            'name' => 'my name',
            'value' => 'my value',
        ];
        $this->db->exec('INSERT INTO mytest (name, value) VALUES (:name, :value)', $row);

        // Retrive
        $stmt = $this->db->execute('SELECT name, value FROM mytest WHERE name = :name AND value = :value', $row);

        // Check statement instance
        $this->assertInstanceOf(\pp\Statement::class, $stmt);
        $this->assertInstanceOf(\PdoStatement::class, $stmt);

        // Check output
        $this->assertEquals((object) $row, $stmt->fetch());

    }


    public function testStatementFetchAllColumn(): void
    {

        // Insert rows
        $rows = [
            ['name' => 'my name', 'value' => 'my value'],
            ['name' => 'my name 2', 'value' => 'my value 2'],
        ];
        $stmt = $this->db->prepare('INSERT INTO mytest (name, value) VALUES (:name, :value)');
        array_map([$stmt, 'execute'], $rows);

        // Retrive
        $values = $this->db->execute('SELECT value FROM mytest')->fetchAllColumn();

        // Check output
        $this->assertSame(2, count($values));
        $this->assertEquals(array_map(fn($row) => $row['value'], $rows), $values);

    }

    public function testStatementFetchObjects(): void
    {

        // Insert rows
        $rows = [
            ['name' => 'my name', 'value' => 'my value'],
            ['name' => 'my name 2', 'value' => 'my value 2'],
        ];
        $stmt = $this->db->prepare('INSERT INTO mytest (name, value) VALUES (:name, :value)');
        array_map([$stmt, 'execute'], $rows);

        // Retrive
        $values = $this->db->execute('SELECT name, value FROM mytest')->fetchObjects();

        // Check output
        $this->assertSame(2, count($values));
        $this->assertEquals(array_map(fn($row) => (object)$row, $rows), $values);

    }

}
