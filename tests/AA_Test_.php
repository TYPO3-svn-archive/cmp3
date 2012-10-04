<?php




class A {
	public function aa($obj)
	{
		$obj->a = 'a';
	}

	public function bb($obj)
	{
		$obj->a = 'a';

		$obj = new ArrayObject();
		$obj->a = 'b';
	}

	public function cc(&$obj)
	{
		$obj->a = 'a';

		$obj = new ArrayObject();
		$obj->a = 'c';
	}
}



class AA_Test extends \TestCaseBase {



	/**
	 *
	 */
	public function testObject1()
	{

		$obj = new ArrayObject();
		$obj->a = 'x';

		$objA = new A;
		$objA->aa($obj);

		$this->assertEquals('a', $obj->a);
	}


	/**
	 *
	 */
	public function testObject2()
	{

		$obj = new ArrayObject();
		$obj->a = 'x';

		$objA = new A;
		$objA->bb($obj);

		$this->assertNotEquals('b', $obj->a);
	}


	/**
	 *
	 */
	public function testObject3()
	{

		$obj = new ArrayObject();
		$obj->a = 'x';

		$objA = new A;
		$objA->cc($obj);

		$this->assertEquals('c', $obj->a);
	}
}
