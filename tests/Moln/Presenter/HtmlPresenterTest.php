<?php

namespace Moln\Presenter;

class HtmlPresenterTest extends \PHPUnit_Framework_TestCase
{
	/** @test */
	public function it_can_be_instantiated()
	{
		$instance = new HtmlPresenter($this->createObjectMock());
		$this->assertNotNull($instance);
	}

	/** @test */
	public function it_cannot_be_instantiated_without_a_object()
	{
		$this->setExpectedException('\Exception', 'HtmlPresenter expects an object');

		$instance = new HtmlPresenter('foobar');
	}

	/** @test */
	public function it_cannot_be_instantiated_with_another_presenter()
	{
		$this->setExpectedException('\Exception', 'Cannot pass another HtmlPresenter as an object');

		$instance = new HtmlPresenter(new HtmlPresenter($this->createObjectMock()));
	}

	/** @test */
	public function it_escapes_an_object_method_return_string()
	{
		$stub = $this->createObjectMockWithMethods([
			'myMethod' => 'arrivò'
		]);

        $instance = new HtmlPresenter($stub);
		
		$this->assertEquals('arriv&ograve;', $instance->myMethod());
	}

	/** @test */
	public function it_escapes_an_object_method_return_array()
	{
		$stub = $this->createObjectMockWithMethods([
			'myMethod' => ['arrivò', 'arriverà']
		]);

		$instance = new HtmlPresenter($stub);
		
		$this->assertEquals(['arriv&ograve;', 'arriver&agrave;'], $instance->myMethod());
	}

	/** @test */
	public function it_escapes_an_object_method_return_array_keys()
	{
		$stub = $this->createObjectMockWithMethods([
			'myMethod' => ['arrivò' => 'arriverà']
		]);

		$instance = new HtmlPresenter($stub);
		
		$this->assertEquals(['arriv&ograve;' => 'arriver&agrave;'], $instance->myMethod());
	}

	/** @test */
	public function it_escapes_an_object_method_return_object_returning_a_presenter_object()
	{
		$stub = $this->createObjectMockWithMethods([
			'myMethod' => (object)['foo' => 'arriverà']
		]);
		
        $instance = new HtmlPresenter($stub);
		
		$this->assertInstanceOf(HtmlPresenter::class, $instance->myMethod());
		$this->assertEquals('arriver&agrave;', $instance->myMethod()->foo);
	}

	/** @test */
	public function it_escapes_an_object_property()
	{
		$instance = new HtmlPresenter($this->createObjectMock(['foo' => 'arriverò']));

		$this->assertEquals('arriver&ograve;', $instance->foo);
	}

	// ! Utility methods

	private function createObjectMock(array $properties = [])
	{
		return (object)$properties;
	}

	private function createObjectMockWithMethods(array $methods = [])
	{
		$stub = $this->getMockBuilder('SomeClass')->setMethods(array_keys($methods))->getMock();
        
		foreach ($methods as $method => $return) {
			$stub->method($method)->willReturn($return);
		}

        return $stub;
	}
}