<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UnpaidStudentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected ?string $class;
    protected ?int $schoolId;

    public function __construct(?string $class, ?int $schoolId)
    {
        $this->class = $class;
        $this->schoolId = $schoolId;
    }

    public function collection()
    {
        $query = Student::with(['school', 'guardian', 'feedingPlans'])
            ->when($this->schoolId, function ($q) {
                $q->where('school_id', $this->schoolId);
            })
            ->whereDoesntHave('feedingPlans', function ($q) {
                $q->where('student_plan.status', 'active');
            })
            ->orWhereHas('feedingPlans', function ($q) {
                $q->where('student_plan.end_date', '<', now());
            });

        if ($this->class) {
            $query->where('grade', $this->class);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Class',
            'School',
            'Guardian',
            'Guardian Phone',
            'Guardian Email',
            'Last Plan',
            'Expiry Date',
            'Days Unpaid',
        ];
    }

    public function map($student): array
    {
        $lastPlan = $student->feedingPlans->sortByDesc('pivot.end_date')->first();
        $expiryDate = $lastPlan ? $lastPlan->pivot->end_date : null;
        $daysUnpaid = $expiryDate ? now()->diffInDays($expiryDate) : 'Never Paid';

        return [
            $student->full_name,
            $student->grade,
            $student->school->name,
            $student->guardian->name ?? 'N/A',
            $student->guardian->phone ?? 'N/A',
            $student->guardian->email ?? 'N/A',
            $lastPlan ? $lastPlan->name : 'Never',
            $expiryDate ? $expiryDate->format('Y-m-d') : 'N/A',
            $daysUnpaid,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
