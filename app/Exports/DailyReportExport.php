<?php

namespace App\Exports;

use App\Models\FeedingAttendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected string $date;
    protected ?string $class;
    protected ?int $schoolId;

    public function __construct(string $date, ?string $class, ?int $schoolId)
    {
        $this->date = $date;
        $this->class = $class;
        $this->schoolId = $schoolId;
    }

    public function collection()
    {
        $query = FeedingAttendance::with(['student', 'markedBy'])
            ->where('date', $this->date)
            ->whereHas('student', function ($q) {
                if ($this->schoolId) {
                    $q->where('school_id', $this->schoolId);
                }
            });

        if ($this->class) {
            $query->whereHas('student', function ($q) {
                $q->where('grade', $this->class);
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Student Name',
            'Class',
            'School',
            'Status',
            'Notes',
            'Marked By',
            'Marked At',
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->date,
            $attendance->student->full_name,
            $attendance->student->grade,
            $attendance->student->school->name,
            ucfirst($attendance->status),
            $attendance->notes ?? '-',
            $attendance->markedBy->name ?? 'System',
            $attendance->created_at->format('H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
