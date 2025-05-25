<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            [
                'title' => 'Initial setup',
                'description' => 'Create a new project and install the necessary dependencies.',
                'status' => 'To Do',
                'deadline' => '2025-05-26 00:00:00',
                'user_id' => 3,
                'project_id' => 1,
            ],
            [
                'title' => 'Setup a new database',
                'description' => 'Create a new database and set up the necessary tables.',
                'status' => 'To Do',
                'deadline' => '2025-05-27 00:00:00',
                'user_id' => 3,
                'project_id' => 1,
            ],
            [
                'title' => 'Landing page design',
                'description' => 'Design the landing page for the project.',
                'status' => 'To Do',
                'deadline' => '2025-05-26 00:00:00',
                'user_id' => 4,
                'project_id' => 1,
            ],
            [
                'title' => 'Login page design',
                'description' => 'Design the login page for the project.',
                'status' => 'To Do',
                'deadline' => '2025-05-27 00:00:00',
                'user_id' => 4,
                'project_id' => 1,
            ],
            [
                'title' => 'Meet the clients',
                'description' => 'Meet the clients and gather requirements for the project.',
                'status' => 'To Do',
                'deadline' => '2025-05-26 00:00:00',
                'user_id' => 5,
                'project_id' => 1,
            ],
            [
                'title' => 'Create a document structure',
                'description' => 'Create a document structure for the project.',
                'status' => 'To Do',
                'deadline' => '2025-05-27 00:00:00',
                'user_id' => 5,
                'project_id' => 1,
            ],
            [
                'title' => 'Fix accounts filter',
                'description' => 'Fix the accounts filter in the search page.',
                'status' => 'To Do',
                'deadline' => '2025-05-28 00:00:00',
                'user_id' => 3,
                'project_id' => 2,
            ],
            [
                'title' => 'Setup a payment gateway',
                'description' => 'Setup a payment gateway for the project using Stripe.',
                'status' => 'To Do',
                'deadline' => '2025-05-29 00:00:00',
                'user_id' => 3,
                'project_id' => 2,
            ],
            [
                'title' => 'Profile page design',
                'description' => 'Design the profile page for the project.',
                'status' => 'To Do',
                'deadline' => '2025-05-28 00:00:00',
                'user_id' => 4,
                'project_id' => 2,
            ],
            [
                'title' => 'Purchase history page design',
                'description' => 'Design the purchase history page for the project.',
                'status' => 'To Do',
                'deadline' => '2025-05-29 00:00:00',
                'user_id' => 4,
                'project_id' => 2,
            ],
            [
                'title' => 'Fix SCRUM board',
                'description' => 'Fix the SCRUM board in the project.',
                'status' => 'To Do',
                'deadline' => '2025-05-28 00:00:00',
                'user_id' => 5,
                'project_id' => 2,
            ],
            [
                'title' => 'Make sure the progress is equal to the tasks',
                'description' => 'Make sure the progress is equal to the tasks in the project.',
                'status' => 'To Do',
                'deadline' => '2025-05-29 00:00:00',
                'user_id' => 5,
                'project_id' => 2,
            ],
            [
                'title' => 'Deploy the project',
                'description' => 'Deploy the project to production.',
                'status' => 'To Do',
                'deadline' => '2025-05-30 00:00:00',
                'user_id' => 3,
                'project_id' => 3,
            ],
            [
                'title' => 'Confifure the payment gateway in production',
                'description' => 'Confifure the payment gateway in production using Stripe.',
                'status' => 'To Do',
                'deadline' => '2025-05-31 00:00:00',
                'user_id' => 3,
                'project_id' => 3,
            ],
            [
                'title' => 'Blackbox testing',
                'description' => 'Perform blackbox testing on the project.',
                'status' => 'To Do',
                'deadline' => '2025-05-30 00:00:00',
                'user_id' => 4,
                'project_id' => 3,
            ],
            [
                'title' => 'Whitebox testing',
                'description' => 'Perform whitebox testing on the project.',
                'status' => 'To Do',
                'deadline' => '2025-05-31 00:00:00',
                'user_id' => 4,
                'project_id' => 3,
            ],
            [
                'title' => 'Meet the clients for UAT',
                'description' => 'Meet the clients for UAT on the project.',
                'status' => 'To Do',
                'deadline' => '2025-05-30 00:00:00',
                'user_id' => 5,
                'project_id' => 3,
            ],
            [
                'title' => 'Fix UAT documents',
                'description' => 'Fix the UAT documents in the project.',
                'status' => 'To Do',
                'deadline' => '2025-05-31 00:00:00',
                'user_id' => 5,
                'project_id' => 3,
            ],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}
