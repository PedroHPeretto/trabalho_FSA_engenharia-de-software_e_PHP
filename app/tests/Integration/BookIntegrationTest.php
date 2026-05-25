<?php

namespace Tests\Integration;

use App\Models\Book;
use App\Models\User;

class BookIntegrationTest extends TestCase
{
    public function test_authenticated_user_can_view_book_catalog(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/books')
            ->assertOk();
    }

    public function test_librarian_can_create_physical_book(): void
    {
        $librarian = User::factory()->librarian()->create();

        $response = $this->actingAs($librarian)->post('/books', [
            'title'  => 'Clean Code',
            'author' => 'Robert Martin',
            'media'  => 'physical',
            'stock'  => 5,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('books', [
            'title'  => 'Clean Code',
            'author' => 'Robert Martin',
            'media'  => 'physical',
            'stock'  => 5,
        ]);
    }

    public function test_librarian_can_create_digital_book(): void
    {
        $librarian = User::factory()->librarian()->create();

        $response = $this->actingAs($librarian)->post('/books', [
            'title'        => 'PHP Manual',
            'author'       => 'PHP Group',
            'media'        => 'digital',
            'digital_link' => 'https://www.php.net/manual',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('books', ['title' => 'PHP Manual', 'media' => 'digital']);
    }

    public function test_customer_cannot_create_book(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->post('/books', [
                'title'  => 'Forbidden Book',
                'author' => 'Author',
                'media'  => 'physical',
                'stock'  => 1,
            ])
            ->assertForbidden();
    }

    public function test_admin_can_delete_book(): void
    {
        $admin = User::factory()->admin()->create();
        $book  = Book::factory()->create();

        $this->actingAs($admin)
            ->delete("/books/{$book->id}")
            ->assertRedirect();

        $this->assertSoftDeleted('books', ['id' => $book->id]);
    }

    public function test_customer_cannot_delete_book(): void
    {
        $customer = User::factory()->create();
        $book     = Book::factory()->create();

        $this->actingAs($customer)
            ->delete("/books/{$book->id}")
            ->assertForbidden();
    }

    public function test_physical_book_creation_fails_without_stock(): void
    {
        $librarian = User::factory()->librarian()->create();

        $this->actingAs($librarian)
            ->post('/books', [
                'title'  => 'Book Without Stock',
                'author' => 'Author',
                'media'  => 'physical',
            ])
            ->assertSessionHasErrors(['stock']);
    }

    public function test_digital_book_creation_fails_without_link(): void
    {
        $librarian = User::factory()->librarian()->create();

        $this->actingAs($librarian)
            ->post('/books', [
                'title'  => 'Book Without Link',
                'author' => 'Author',
                'media'  => 'digital',
            ])
            ->assertSessionHasErrors(['digital_link']);
    }
}
