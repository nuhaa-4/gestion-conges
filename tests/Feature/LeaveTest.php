<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Leave;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LeaveTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_leave_request(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/leaves', [
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays(2)->toDateString(),
                'type' => 'Congé Annuel Payé',
                'reason' => 'Vacances d\'été',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        $this->assertDatabaseHas('leaves', [
            'user_id' => $user->id,
            'type' => 'Congé Annuel Payé',
            'status' => 'pending',
        ]);
    }

    public function test_employee_dashboard_displays_leaves_history(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $leave = Leave::create([
            'user_id' => $user->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'type' => 'Congé Annuel Payé',
            'reason' => 'Repos',
            'status' => 'pending',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Historique de mes demandes');
        $response->assertSee('Repos');
    }

    public function test_manager_is_redirected_to_manager_dashboard(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);

        $response = $this
            ->actingAs($manager)
            ->get('/dashboard');

        $response->assertRedirect('/manager/dashboard');
    }

    public function test_non_manager_cannot_access_manager_dashboard(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);

        $response = $this
            ->actingAs($employee)
            ->get('/manager/dashboard');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
    }

    public function test_manager_can_access_manager_dashboard_and_see_pending_leaves(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $employee = User::factory()->create(['role' => 'employee']);
        
        $leave = Leave::create([
            'user_id' => $employee->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'type' => 'Congé Annuel Payé',
            'reason' => 'Congé maladie temporaire',
            'status' => 'pending',
        ]);

        $response = $this
            ->actingAs($manager)
            ->get('/manager/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Congé maladie temporaire');
    }

    public function test_manager_can_approve_leave(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $employee = User::factory()->create(['role' => 'employee']);
        
        $leave = Leave::create([
            'user_id' => $employee->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'type' => 'Congé Annuel Payé',
            'reason' => 'Vacances',
            'status' => 'pending',
        ]);

        $response = $this
            ->actingAs($manager)
            ->post("/leaves/{$leave->id}/approve");

        $response->assertRedirect();
        $this->assertDatabaseHas('leaves', [
            'id' => $leave->id,
            'status' => 'approved',
        ]);
    }

    public function test_manager_can_reject_leave(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $employee = User::factory()->create(['role' => 'employee']);
        
        $leave = Leave::create([
            'user_id' => $employee->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'type' => 'Congé Annuel Payé',
            'reason' => 'Vacances',
            'status' => 'pending',
        ]);

        $response = $this
            ->actingAs($manager)
            ->post("/leaves/{$leave->id}/reject");

        $response->assertRedirect();
        $this->assertDatabaseHas('leaves', [
            'id' => $leave->id,
            'status' => 'rejected',
        ]);
    }

    public function test_employee_cannot_approve_or_reject_leave(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);
        $anotherEmployee = User::factory()->create(['role' => 'employee']);
        
        $leave = Leave::create([
            'user_id' => $anotherEmployee->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'type' => 'Congé Annuel Payé',
            'reason' => 'Vacances',
            'status' => 'pending',
        ]);

        $responseApprove = $this
            ->actingAs($employee)
            ->post("/leaves/{$leave->id}/approve");
        
        $responseApprove->assertStatus(403);

        $responseReject = $this
            ->actingAs($employee)
            ->post("/leaves/{$leave->id}/reject");
        
        $responseReject->assertStatus(403);

        $this->assertDatabaseHas('leaves', [
            'id' => $leave->id,
            'status' => 'pending',
        ]);
    }

    public function test_employee_can_upload_document_when_requesting_leave(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'employee']);
        $file = UploadedFile::fake()->create('certificat.pdf', 500);

        $response = $this
            ->actingAs($user)
            ->post('/leaves', [
                'start_date' => now()->addDay()->toDateString(),
                'end_date' => now()->addDays(2)->toDateString(),
                'type' => 'Congé de Maladie',
                'reason' => 'Grippe',
                'document' => $file,
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        // Vérifie que le fichier a bien été stocké
        Storage::disk('public')->assertExists('documents/' . $file->hashName());

        $this->assertDatabaseHas('leaves', [
            'user_id' => $user->id,
            'type' => 'Congé de Maladie',
            'status' => 'pending',
            'document_path' => 'documents/' . $file->hashName(),
        ]);
    }

    public function test_manager_can_approve_leave_with_comment(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $employee = User::factory()->create(['role' => 'employee']);
        
        $leave = Leave::create([
            'user_id' => $employee->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'type' => 'Congé Annuel Payé',
            'reason' => 'Vacances',
            'status' => 'pending',
        ]);

        $response = $this
            ->actingAs($manager)
            ->post("/leaves/{$leave->id}/approve", [
                'manager_comment' => 'Bonnes vacances !',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('leaves', [
            'id' => $leave->id,
            'status' => 'approved',
            'manager_comment' => 'Bonnes vacances !',
        ]);
    }

    public function test_manager_can_access_employees_list(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);

        $response = $this
            ->actingAs($manager)
            ->get('/manager/employees');

        $response->assertStatus(200);
        $response->assertSee('Liste des collaborateurs');
    }

    public function test_manager_can_update_employee_details(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $employee = User::factory()->create([
            'name' => 'Jean Dupont',
            'email' => 'jean@example.com',
            'role' => 'employee',
        ]);

        $response = $this
            ->actingAs($manager)
            ->put("/manager/employees/{$employee->id}", [
                'name' => 'Jean Dupont Modifié',
                'email' => 'jean.modifie@example.com',
                'role' => 'manager', // promotion
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $employee->id,
            'name' => 'Jean Dupont Modifié',
            'email' => 'jean.modifie@example.com',
            'role' => 'manager',
        ]);
    }

    public function test_employee_submitting_fixed_duration_leave_gets_validated_correctly(): void
    {
        $user = User::factory()->create(['role' => 'employee']);

        // Le congé de paternité impose exactement 3 jours (ex: du 14 au 16 = 14, 15, 16)
        $response = $this
            ->actingAs($user)
            ->post('/leaves', [
                'start_date' => now()->addDay()->toDateString(),
                'end_date' => now()->addDays(3)->toDateString(), // 3 jours de différence inclusive
                'type' => 'Congé de Paternité',
                'reason' => 'Naissance enfant',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        $this->assertDatabaseHas('leaves', [
            'user_id' => $user->id,
            'type' => 'Congé de Paternité',
            'status' => 'pending',
        ]);
    }

    public function test_employee_submitting_invalid_fixed_duration_gets_errors(): void
    {
        $user = User::factory()->create(['role' => 'employee']);

        // Tente de soumettre 5 jours pour un congé de paternité au lieu de 3
        $response = $this
            ->actingAs($user)
            ->post('/leaves', [
                'start_date' => now()->addDay()->toDateString(),
                'end_date' => now()->addDays(5)->toDateString(), // 5 jours
                'type' => 'Congé de Paternité',
                'reason' => 'Fraude durée',
            ]);

        $response->assertSessionHasErrors(['end_date']);
    }

    public function test_manager_can_view_employee_edit_page_with_history(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $employee = User::factory()->create(['role' => 'employee']);
        
        $leave = Leave::create([
            'user_id' => $employee->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'type' => 'Congé Annuel Payé',
            'reason' => 'Vacances Perso',
            'status' => 'approved',
        ]);

        $response = $this
            ->actingAs($manager)
            ->get("/manager/employees/{$employee->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('Historique des demandes de congés de ' . $employee->name);
        $response->assertSee('Vacances Perso');
    }

    public function test_employee_cannot_submit_overlapping_leave_request(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        
        // Crée un congé existant approved du 10 au 15 (septembre 2026)
        Leave::create([
            'user_id' => $user->id,
            'start_date' => '2026-09-10',
            'end_date' => '2026-09-15',
            'type' => 'Congé Annuel Payé',
            'status' => 'approved',
        ]);

        // Tente de soumettre un congé chevauchant (ex: du 12 au 14)
        $response = $this
            ->actingAs($user)
            ->post('/leaves', [
                'start_date' => '2026-09-12',
                'end_date' => '2026-09-14',
                'type' => 'Congé Annuel Payé',
                'reason' => 'Tentative double réservation',
            ]);

        $response->assertSessionHasErrors(['start_date']);
    }

    public function test_manager_cannot_approve_own_leave_request(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        
        $leave = Leave::create([
            'user_id' => $manager->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'type' => 'Congé Annuel Payé',
            'status' => 'pending',
        ]);

        $response = $this
            ->actingAs($manager)
            ->post("/leaves/{$leave->id}/approve");

        $response->assertSessionHas('error', 'Vous ne pouvez pas valider ou refuser votre propre demande de congé.');
        $this->assertDatabaseHas('leaves', [
            'id' => $leave->id,
            'status' => 'pending',
        ]);
    }

    public function test_manager_cannot_approve_non_pending_leave_request(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $employee = User::factory()->create(['role' => 'employee']);
        
        $leave = Leave::create([
            'user_id' => $employee->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'type' => 'Congé Annuel Payé',
            'status' => 'approved', // déjà approuvée
        ]);

        $response = $this
            ->actingAs($manager)
            ->post("/leaves/{$leave->id}/approve");

        $response->assertSessionHas('error', 'Cette demande de congé a déjà été traitée par un autre gestionnaire.');
    }

    public function test_employee_can_submit_sick_leave_with_past_start_date(): void
    {
        $user = User::factory()->create(['role' => 'employee']);

        // Tente de soumettre un congé maladie qui a débuté dans le passé (ex: hier)
        $response = $this
            ->actingAs($user)
            ->post('/leaves', [
                'start_date' => now()->subDay()->toDateString(), // hier
                'end_date' => now()->addDay()->toDateString(),
                'type' => 'Congé de Maladie',
                'reason' => 'Grippe déclarée hier',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        $this->assertDatabaseHas('leaves', [
            'user_id' => $user->id,
            'type' => 'Congé de Maladie',
            'status' => 'pending',
        ]);
    }
}
