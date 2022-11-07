<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

use pp\QueryHelper;
use pp\Db;

final class QueryHelperTest extends TestCase
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
        $this->query = $this->db->getHelper();
    }

    public function testDbIsPdo(): void
    {
        $this->assertInstanceOf(\Pdo::class, $this->query->db);
    }

    public function testPage(): void
    {
        $expected = 'SELECT * FROM mytest LIMIT 20 OFFSET 20';
        $result = $this->query
            ->page(2)
            ->getSql('SELECT * FROM mytest @page')
        ;
        $this->assertSame($expected, $result);

        $expected = 'SELECT * FROM mytest LIMIT 30 OFFSET 30';
        $result = $this->query
            ->page(2, perPage: 30, name: '@myPage')
            ->getSql('SELECT * FROM mytest @myPage')
        ;
        $this->assertSame($expected, $result);
    }

    
    public function testIn(): void
    {
        $expected = "SELECT * FROM mytest WHERE id IN ('1', '2', '3')";
        $result = $this->query
            ->in([1, 2, 3])
            ->getSql('SELECT * FROM mytest WHERE id @in')
        ;
        $this->assertSame($expected, $result);

        $expected = "SELECT * FROM mytest WHERE id IN ('1', '2', '3')";
        $result = $this->query
            ->in([1, 2, 3], '@MyIn')
            ->getSql('SELECT * FROM mytest WHERE id @MyIn')
        ;
        $this->assertSame($expected, $result);

    }

    public function testValues(): void
    {
        $expected = "INSERT INTO mytest (test1, test2) VALUES ('test 1', 'test 2')";
        $result = $this->query
            ->values([
                'test1' => 'test 1',
                'test2' => 'test 2',
            ], name: '@MyValues')
            ->getSql('INSERT INTO mytest @MyValues')
        ;        
        $this->assertSame($expected, $result);

        $expected = "INSERT INTO mytest (test1, test2) VALUES (:test1, :test2)";
        $result = $this->query
            ->values([
                'test1',
                'test2',
            ], name: '@MyValues')
            ->getSql('INSERT INTO mytest @MyValues')
        ;
        $this->assertSame($expected, $result);
    }

    public function testSet(): void
    {
        $expected = "UPDATE mytest SET test1 = '1', test2 = '2'";
        $result = $this->query
            ->set([
                'test1' => 1,
                'test2' => 2,
            ], name: '@MySet')
            ->getSql('UPDATE mytest @MySet')
        ;        
        $this->assertSame($expected, $result);

        $expected = "UPDATE mytest SET test1 = :test1, test2 = :test2";
        $result = $this->query
            ->set([
                'test1',
                'test2',
            ], name: '@MySet')
            ->getSql('UPDATE mytest @MySet')
        ;
        $this->assertSame($expected, $result);
    }

    public function testPrepare(): void
    {

        $row = [
            'name' => 'test name',
            'value' => 'test value',
        ];

        $stmt = $this->query
            ->values(array_keys($row))
            ->prepare('INSERT INTO mytest @values')
        ;

        $stmt->execute($row);

        $this->assertEquals((object) $row, $this->db->query('SELECT name, value FROM mytest LIMIT 1')->fetch());

    }


    public function testExecute(): void
    {

        $row = [
            'name' => 'test name',
            'value' => 'test value',
        ];

        $id = $this->query
            ->values($row)
            ->insertId('INSERT INTO mytest @values')
        ;

        $this->assertEquals(
            (object) $row, 
            $this->query->execute('SELECT name, value FROM mytest WHERE id = ?', [$id])->fetch()
        );

    }

    public function testRowCount(): void
    {

        $row = [
            'name' => 'test name',
            'value' => 'test value',
        ];

        $id = $this->query
            ->values($row)
            ->insertId('INSERT INTO mytest @values')
        ;

        $bind = [
            'newName' => 'new test name',
            'id' => $id,
        ];

        $this->assertSame(
            1, 
            $this->query->rowCount('UPDATE mytest SET name = :newName WHERE id = :id', $bind)
        );

    }

    public function testInsertId(): void
    {

        $row = [
            'name' => 'test name',
            'value' => 'test value',
        ];

        $id = $this->query
            ->values($row)
            ->insertId('INSERT INTO mytest @values')
        ;

        $this->assertSame('1', $id);

    }

}
