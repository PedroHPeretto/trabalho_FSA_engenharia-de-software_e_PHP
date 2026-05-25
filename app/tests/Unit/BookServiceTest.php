<?php

namespace Tests\Unit;

use App\Exceptions\OutOfStockException;
use App\Models\Book;
use App\Services\BookService;
use Mockery;

class BookServiceTest extends TestCase
{
    private BookService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BookService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_assert_book_available_throws_for_physical_book_with_no_stock(): void
    {
        $book        = new Book();
        $book->media = 'physical';
        $book->stock = 0;

        $this->expectException(OutOfStockException::class);
        $this->service->assertBookAvailable($book);
    }

    public function test_assert_book_available_passes_for_physical_book_with_stock(): void
    {
        $book        = new Book();
        $book->media = 'physical';
        $book->stock = 5;

        $this->service->assertBookAvailable($book);

        $this->assertTrue(true);
    }

    public function test_assert_book_available_throws_for_reserved_digital_book(): void
    {
        $book           = new Book();
        $book->media    = 'digital';
        $book->reserved = true;

        $this->expectException(OutOfStockException::class);
        $this->service->assertBookAvailable($book);
    }

    public function test_assert_book_available_passes_for_unreserved_digital_book(): void
    {
        $book           = new Book();
        $book->media    = 'digital';
        $book->reserved = false;

        $this->service->assertBookAvailable($book);

        $this->assertTrue(true);
    }

    public function test_decrement_stock_throws_when_stock_is_zero(): void
    {
        $book        = Mockery::mock(Book::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $book->stock = 0;

        $this->expectException(OutOfStockException::class);
        $this->service->decrementStock($book);
    }

    public function test_decrement_stock_calls_decrement_when_stock_is_positive(): void
    {
        $book        = Mockery::mock(Book::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $book->stock = 3;
        $book->shouldReceive('decrement')->with('stock')->once();

        $this->service->decrementStock($book);

        $this->addToAssertionCount(1);
    }

    public function test_increment_stock_calls_increment_on_book(): void
    {
        $book = Mockery::mock(Book::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $book->shouldReceive('increment')->with('stock')->once();

        $this->service->incrementStock($book);

        $this->addToAssertionCount(1);
    }

    public function test_assert_book_available_passes_for_digital_book_with_null_reserved(): void
    {
        $book           = new Book();
        $book->media    = 'digital';
        $book->reserved = null;

        $this->service->assertBookAvailable($book);

        $this->assertTrue(true);
    }
}
