<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    private $documentTypes = [
        [
            'name' => 'Barangay Clearance',
            'description' => 'Certifies residency & good standing.',
            'fee' => 100,
            'requirements' => 'Valid ID, Cedula (Community Tax Certificate)'
        ],
        [
            'name' => 'Certificate of Indigency',
            'description' => 'Certifies that a person is indigent (low income).',
            'fee' => 0,
            'requirements' => 'Valid ID'
        ],
        [
            'name' => 'Certificate of Residency',
            'description' => 'Proof that a person resides in the barangay.',
            'fee' => 100,
            'requirements' => 'Valid ID, Proof of address'
        ],
        [
            'name' => 'Barangay ID',
            'description' => 'Local ID issued by barangay.',
            'fee' => 150,
            'requirements' => 'Birth Certificate, Valid ID (if available), Photo'
        ],
        [
            'name' => 'Business Permit (Barangay)',
            'description' => 'Requirement for business registration.',
            'fee' => 500,
            'requirements' => 'Business papers, Cedula'
        ],
        [
            'name' => 'Barangay Permit for Events',
            'description' => 'Permit to hold public events (fiesta, concert, etc.).',
            'fee' => 300,
            'requirements' => 'Event details, Sponsor/Organizer ID'
        ],
        [
            'name' => 'Certificate of Good Moral Character',
            'description' => 'Used for school/job applications.',
            'fee' => 100,
            'requirements' => 'Valid ID'
        ],
        [
            'name' => 'Travel / Transfer Permit',
            'description' => 'Certifies movement/transfer of residency.',
            'fee' => 100,
            'requirements' => 'Valid ID, Proof of transfer'
        ],
        [
            'name' => 'Certification for Police Clearance',
            'description' => 'Pre-requisite for police clearance application.',
            'fee' => 100,
            'requirements' => 'Valid ID, Cedula'
        ],
        [
            'name' => 'Barangay Blotter / Incident Report',
            'description' => 'Official record of disputes, complaints, or incidents.',
            'fee' => 0,
            'requirements' => 'Request letter / Valid ID'
        ]
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DocumentType::insert($this->documentTypes);
    }
}
