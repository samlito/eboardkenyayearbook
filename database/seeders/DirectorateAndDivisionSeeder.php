<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Directorate;
use App\Models\Division;

class DirectorateAndDivisionSeeder extends Seeder
{
    public function run()
    {
        // Create Directorates (headed by Directors)
        $directorates = [
            'Infrastructure Planning & Design Directorate',
            'Infrastructure Development Directorate',
            'Bulk Water Services Directorate',
            'Finance & Planning Directorate',
        ];

        $directorateModels = [];
        foreach ($directorates as $name) {
            $directorateModels[$name] = Directorate::firstOrCreate(['name' => $name]);
        }

        // Divisions under Directorates (headed by Deputy Directors)
        $divisionsUnderDirectorates = [
            'Infrastructure Planning & Design Directorate' => [
                'Water Works Planning & Design Division',
                'Sanitation Works Planning & Design Division',
                'Env., Social Safeguards & Climate Resilience Division',
            ],
            'Infrastructure Development Directorate' => [
                'Water Works Development Division',
                'Sanitation Development Division',
            ],
            'Bulk Water Services Directorate' => [
                'Operations & Maintenance Division',
                'Water Quality Division',
                'Commercial Division',
                'Management & Liaison Division',
            ],
            'Finance & Planning Directorate' => [
                'Finance & Accounts Division',
                'Corporate Planning Division',
                'Resource Mobilization Division',
                'Info, Communication & Technology Division',
            ],
        ];

        foreach ($divisionsUnderDirectorates as $directorateName => $divisions) {
            $directorate = $directorateModels[$directorateName];
            foreach ($divisions as $divisionName) {
                Division::firstOrCreate(
                    ['name' => $divisionName],
                    [
                        'directorate_id' => $directorate->id,
                        'requires_ceo_approval' => true,
                    ]
                );
            }
        }

        // Divisions that report directly to the CEO (no directorate)
        $ceoDirectDivisions = [
            'HRA & Administration Division',
            'Legal Services Division',
            'Internal Audit Division',
            'Supply Chain Management Division',
        ];

        foreach ($ceoDirectDivisions as $divisionName) {
            Division::firstOrCreate(
                ['name' => $divisionName],
                [
                    'directorate_id' => null,
                    'requires_ceo_approval' => true,
                ]
            );
        }
    }
}
