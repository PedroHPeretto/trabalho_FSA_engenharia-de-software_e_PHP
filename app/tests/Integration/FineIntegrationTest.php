<?php

namespace Tests\Integration;

use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;

class FineIntegrationTest extends TestCase
{
    public function test_customer_can_view_own_fines(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get('/fines')
            ->assertOk();
    }

    public function test_librarian_can_pay_fine(): void
    {
        $librarian = User::factory()->librarian()->create();
        $customer  = User::factory()->blocked()->create();
        $loan      = Loan::factory()->create(['user_id' => $customer->id]);
        $fine      = Fine::factory()->create(['loan_id' => $loan->id, 'user_id' => $customer->id]);

        $this->actingAs($librarian)
            ->post("/fines/{$fine->id}/pay")
            ->assertRedirect();

        $this->assertDatabaseHas('fines', ['id' => $fine->id, 'paid' => true]);
    }

    public function test_customer_cannot_pay_fine(): void
    {
        $customer = User::factory()->create();
        $loan     = Loan::factory()->create(['user_id' => $customer->id]);
        $fine     = Fine::factory()->create(['user_id' => $customer->id, 'loan_id' => $loan->id]);

        $this->actingAs($customer)
            ->post("/fines/{$fine->id}/pay")
            ->assertForbidden();
    }

    public function test_user_unblocked_after_last_fine_paid(): void
    {
        $librarian = User::factory()->librarian()->create();
        $customer  = User::factory()->blocked()->create();
        $loan      = Loan::factory()->create(['user_id' => $customer->id]);
        $fine      = Fine::factory()->create(['loan_id' => $loan->id, 'user_id' => $customer->id]);

        $this->actingAs($librarian)->post("/fines/{$fine->id}/pay");

        $this->assertDatabaseHas('users', ['id' => $customer->id, 'blocked' => false]);
    }

    public function test_user_stays_blocked_when_other_fines_remain(): void
    {
        $librarian = User::factory()->librarian()->create();
        $customer  = User::factory()->blocked()->create();

        $loan1 = Loan::factory()->create(['user_id' => $customer->id]);
        $loan2 = Loan::factory()->create(['user_id' => $customer->id]);
        $fine1 = Fine::factory()->create(['loan_id' => $loan1->id, 'user_id' => $customer->id]);
        Fine::factory()->create(['loan_id' => $loan2->id, 'user_id' => $customer->id]);

        $this->actingAs($librarian)->post("/fines/{$fine1->id}/pay");

        $this->assertDatabaseHas('users', ['id' => $customer->id, 'blocked' => true]);
    }
}
