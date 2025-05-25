<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'name' => 'Explorenesia',
                'description' => 'Smart tourism dahsboard for analyzing sentiment of DPSP in Indonesia, made for ministry of tourism.',
            ],
            [
                'name' => 'Elegame',
                'description' => 'Efootbal account ecommerce, user can buy and sell their efootbal account.',
            ],
            [
                'name' => 'Situansilat',
                'description' => 'Situansilat is a dashboard for Diskoperindag Bondowoso regency, help them to manage their business.',
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}
